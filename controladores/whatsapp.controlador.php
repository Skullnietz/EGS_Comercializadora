<?php

class ControladorWhatsapp
{
    static private function estadosSistema()
    {
        return array(
            'En revision (REV)',
            'Pendiente de autorizacion (AUT)',
            'Aceptado (ok)',
            'Supervision (SUP)',
            'Terminada (ter)',
            'Entregado (Ent)',
            'Cancelada (can)',
            'Sin reparacion (SR)',
            'Producto para venta',
            'En revision probable garantia'
        );
    }

    static private function normalizarMediaType($type)
    {
        $permitidos = array('none', 'image', 'video', 'audio', 'document');
        $type = strtolower(trim((string) $type));
        return in_array($type, $permitidos, true) ? $type : 'none';
    }

    static private function normalizarNumero($raw, $defaultCountryCode = '52')
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $raw);
        if ($digits === '') {
            return '';
        }

        if (strpos($digits, '00') === 0) {
            $digits = substr($digits, 2);
        }

        if (strlen($digits) === 10) {
            $cc = preg_replace('/[^0-9]/', '', (string) $defaultCountryCode);
            if ($cc !== '') {
                $digits = $cc . $digits;
            }
        }

        return $digits;
    }

    static public function ctrObtenerConfig()
    {
        $cfg = ModeloWhatsapp::mdlLeerConfig();
        if (!isset($cfg['templates']) || !is_array($cfg['templates'])) {
            $cfg['templates'] = array();
        }

        foreach (self::estadosSistema() as $estado) {
            if (!isset($cfg['templates'][$estado])) {
                $cfg['templates'][$estado] = array(
                    'message' => '',
                    'media_type' => 'none',
                    'media_url' => ''
                );
            }
        }

        return $cfg;
    }

    static public function ctrGuardarConfig($input)
    {
        $actual = self::ctrObtenerConfig();

        $out = array(
            'enabled' => !empty($input['enabled']),
            'provider' => 'meta',
            'meta' => array(
                'api_version' => trim((string) ($input['meta']['api_version'] ?? 'v20.0')),
                'phone_number_id' => trim((string) ($input['meta']['phone_number_id'] ?? '')),
                'access_token' => trim((string) ($input['meta']['access_token'] ?? '')),
                'default_country_code' => trim((string) ($input['meta']['default_country_code'] ?? '52')),
            ),
            'endpoint' => trim((string) ($input['endpoint'] ?? '')),
            'token' => trim((string) ($input['token'] ?? '')),
            'timeout' => intval($input['timeout'] ?? 5),
            'templates' => array()
        );

        if ($out['timeout'] <= 0) {
            $out['timeout'] = 5;
        }

        if ($out['meta']['api_version'] === '') {
            $out['meta']['api_version'] = 'v20.0';
        }
        if ($out['meta']['default_country_code'] === '') {
            $out['meta']['default_country_code'] = '52';
        }

        $templatesIn = isset($input['templates']) && is_array($input['templates']) ? $input['templates'] : array();
        foreach ($templatesIn as $estado => $tpl) {
            if (!is_array($tpl)) {
                continue;
            }
            $out['templates'][(string) $estado] = array(
                'message' => trim((string) ($tpl['message'] ?? '')),
                'media_type' => self::normalizarMediaType($tpl['media_type'] ?? 'none'),
                'media_url' => trim((string) ($tpl['media_url'] ?? ''))
            );
        }

        if (!isset($out['templates']['_default'])) {
            $out['templates']['_default'] = $actual['templates']['_default'];
        }

        return ModeloWhatsapp::mdlGuardarConfig($out);
    }

    static private function renderTemplate($template, $ctx)
    {
        $repl = array(
            '{orden}' => (string) ($ctx['id_orden'] ?? ''),
            '{estado_anterior}' => (string) ($ctx['estado_anterior'] ?? ''),
            '{estado_nuevo}' => (string) ($ctx['estado_nuevo'] ?? ''),
            '{cliente}' => (string) ($ctx['cliente_nombre'] ?? ''),
            '{empresa}' => (string) ($ctx['empresa_nombre'] ?? '')
        );

        return strtr((string) $template, $repl);
    }

    static public function ctrPrepararNotificacionEstado($ctx)
    {
        $cfg = self::ctrObtenerConfig();

        $enabled = !empty($cfg['enabled']);

        if (!$enabled) {
            return null;
        }

        $estado = (string) ($ctx['estado_nuevo'] ?? '');
        $tpl = $cfg['templates'][$estado] ?? ($cfg['templates']['_default'] ?? array());

        $mensaje = self::renderTemplate($tpl['message'] ?? '', $ctx);
        if ($mensaje === '') {
            $mensaje = self::renderTemplate('Hola {cliente}, tu orden #{orden} ahora esta en estado: {estado_nuevo}.', $ctx);
        }

        $mediaType = self::normalizarMediaType($tpl['media_type'] ?? 'none');
        $mediaUrl = trim((string) ($tpl['media_url'] ?? ''));

        $meta = isset($cfg['meta']) && is_array($cfg['meta']) ? $cfg['meta'] : array();
        $apiVersion = trim((string) ($meta['api_version'] ?? 'v20.0'));
        $phoneNumberId = trim((string) ($meta['phone_number_id'] ?? ''));
        $accessToken = trim((string) ($meta['access_token'] ?? ''));
        $countryCode = trim((string) ($meta['default_country_code'] ?? '52'));

        $to = self::normalizarNumero((string) ($ctx['cliente_whatsapp'] ?? ''), $countryCode);

        if ($apiVersion === '') {
            $apiVersion = 'v20.0';
        }

        if ($phoneNumberId !== '' && $accessToken !== '' && $to !== '') {
            $endpoint = 'https://graph.facebook.com/' . rawurlencode($apiVersion) . '/' . rawurlencode($phoneNumberId) . '/messages';

            $base = array(
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
            );

            $payload = $base;
            if ($mediaType === 'none' || $mediaUrl === '') {
                $payload['type'] = 'text';
                $payload['text'] = array(
                    'preview_url' => false,
                    'body' => $mensaje
                );
            } elseif ($mediaType === 'image') {
                $payload['type'] = 'image';
                $payload['image'] = array('link' => $mediaUrl, 'caption' => $mensaje);
            } elseif ($mediaType === 'video') {
                $payload['type'] = 'video';
                $payload['video'] = array('link' => $mediaUrl, 'caption' => $mensaje);
            } elseif ($mediaType === 'document') {
                $payload['type'] = 'document';
                $payload['document'] = array('link' => $mediaUrl, 'caption' => $mensaje);
            } elseif ($mediaType === 'audio') {
                $payload['type'] = 'audio';
                $payload['audio'] = array('link' => $mediaUrl);
            } else {
                $payload['type'] = 'text';
                $payload['text'] = array(
                    'preview_url' => false,
                    'body' => $mensaje
                );
            }

            return array(
                'endpoint' => $endpoint,
                'token' => $accessToken,
                'timeout' => intval($cfg['timeout'] ?? 5),
                'payload' => $payload
            );
        }

        $endpoint = trim((string) ($cfg['endpoint'] ?? ''));
        if ($endpoint === '') {
            return null;
        }

        $payload = $ctx;
        $payload['event'] = 'orden.estado.cambiado';
        $payload['message'] = $mensaje;
        $payload['media'] = array(
            'type' => $mediaType,
            'url' => $mediaUrl
        );

        return array(
            'endpoint' => $endpoint,
            'token' => trim((string) ($cfg['token'] ?? '')),
            'timeout' => intval($cfg['timeout'] ?? 5),
            'payload' => $payload
        );
    }
}
