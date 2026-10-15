<?php
/**
 * Plugin Name: EGS - Información legal pública
 * Description: Enlaces al aviso de privacidad, condiciones e integración de Google Drive.
 */
if (!defined('ABSPATH')) { exit; }

add_action('wp_footer', function () {
    if (is_admin()) { return; }
    echo '<section aria-label="Información legal de Comercializadora EGS" style="padding:24px;background:#f4f7fa;color:#183047;border-top:1px solid #d7e2eb;text-align:center;font:15px/1.6 system-ui,sans-serif">';
    echo '<p style="margin:0 auto 12px;max-width:900px;padding:0 16px">EGS Multimedia es la integración de Comercializadora EGS que organiza y recupera en Google Drive los archivos de sus órdenes y sitios. La conexión la autoriza el propietario de la biblioteca.</p>';
    echo '<nav aria-label="Privacidad y condiciones" style="display:flex;justify-content:center;flex-wrap:wrap;gap:12px 24px;padding:0 16px">';
    foreach (array('politica-de-privacidad'=>'Aviso de privacidad','condiciones-de-servicio'=>'Condiciones de servicio','integracion-google-drive'=>'EGS Multimedia y Google Drive') as $slug=>$label) {
        echo '<a style="color:#075ea3;text-decoration:underline" href="'.esc_url(home_url('/'.$slug.'/')).'">'.esc_html($label).'</a>';
    }
    echo '</nav></section>';
}, 30);
