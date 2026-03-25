<?php
/**
 * Configuración de integración con MercadoLibre API
 *
 * Para obtener estas credenciales:
 *  1. Crea una aplicación en: https://developers.mercadolibre.com.mx/apps
 *  2. Genera tu access_token desde el panel de desarrolladores de ML
 *  3. Tu seller_id lo encuentras llamando:
 *     https://api.mercadolibre.com/users/me?access_token={tu_access_token}
 *
 * Nota: El access_token expira cada 6 horas.
 * Si configuras client_id, client_secret y refresh_token se renovará
 * automáticamente sin intervención manual.
 *
 * Este archivo se actualiza automáticamente cuando el sistema
 * renueva el access_token via refresh_token.
 */
return [
    'access_token'  => '',   // Requerido — Bearer token de ML
    'refresh_token' => '',   // Opcional — para renovación automática
    'seller_id'     => '',   // Requerido — ID numérico del vendedor
    'client_id'     => '',   // Opcional — ID de tu aplicación ML
    'client_secret' => '',   // Opcional — Secret de tu aplicación ML
    'site_id'       => 'MLM', // MLM = México
];
