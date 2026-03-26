<?php

class ModeloWhatsapp
{
    private static function rutaConfig()
    {
        return __DIR__ . '/../config/whatsapp_config.json';
    }

    static public function mdlConfigDefault()
    {
        return array(
            'enabled' => false,
            'timeout' => 5,
            'provider' => 'meta',
            'meta' => array(
                'api_version' => 'v20.0',
                'phone_number_id' => '',
                'access_token' => '',
                'default_country_code' => '52'
            ),
            'endpoint' => '',
            'token' => '',
            'templates' => array(
                '_default' => array(
                    'message' => 'Hola {cliente}, tu orden #{orden} ahora esta en estado: {estado_nuevo}.',
                    'media_type' => 'none',
                    'media_url' => ''
                )
            )
        );
    }

    static public function mdlLeerConfig()
    {
        $ruta = self::rutaConfig();
        $base = self::mdlConfigDefault();

        if (!file_exists($ruta)) {
            return $base;
        }

        $json = @file_get_contents($ruta);
        if ($json === false || trim($json) === '') {
            return $base;
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            return $base;
        }

        return array_replace_recursive($base, $data);
    }

    static public function mdlGuardarConfig($config)
    {
        $ruta = self::rutaConfig();
        $dir = dirname($ruta);

        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            return 'error';
        }

        $tmp = $ruta . '.tmp';
        $ok = @file_put_contents($tmp, $json, LOCK_EX);
        if ($ok === false) {
            return 'error';
        }

        if (!@rename($tmp, $ruta)) {
            @unlink($tmp);
            return 'error';
        }

        return 'ok';
    }
}
