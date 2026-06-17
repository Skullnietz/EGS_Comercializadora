<?php
/**
 * estado-orden-cliente.php
 *
 * Redirector de compatibilidad para QRs ya impresos.
 *
 * Los tickets viejos tienen un QR que apunta a esta ruta con un token
 * que identifica una ORDEN específica. Hoy el flujo correcto es el
 * portal del cliente (portal-cliente) que muestra todas sus órdenes,
 * monedero, ayuda y privacidad.
 *
 * Este script:
 *   1) Resuelve el token a una orden
 *   2) Obtiene el id_cliente de esa orden
 *   3) Obtiene el token persistente del cliente (dinero_electronico.token)
 *   4) Hace 302 → portal-cliente con la orden preseleccionada
 *
 * Si no se puede resolver, muestra un mensaje claro.
 */

$token = isset($_GET["token"]) ? trim((string) $_GET["token"]) : "";

$orden = null;
if ($token !== "" && preg_match('/^[a-f0-9]{64}$/', $token)) {
    $orden = controladorOrdenes::ctrMostrarOrdenPorToken($token);
}

if (is_array($orden) && !empty($orden["id_usuario"])) {
    $idCliente = intval($orden["id_usuario"]);

    $tokenCliente = "";
    try {
        $info = ControladorRecompensas::ctrObtenerInfoRecompensas($idCliente);
        if (is_array($info) && !empty($info["token"])) {
            $tokenCliente = $info["token"];
        }
    } catch (Exception $e) {
        $tokenCliente = "";
    }

    if ($tokenCliente !== "") {
        $idOrden = intval($orden["id"]);
        $url = "?ruta=portal-cliente&token=" . urlencode($tokenCliente) . "&orden=" . $idOrden;
        header("Location: " . $url, true, 302);
        exit;
    }
}
?>
<div style="max-width:480px;margin:60px auto;padding:24px;font-family:'Source Sans Pro',Arial,sans-serif;background:#fff;border-radius:14px;box-shadow:0 6px 24px rgba(0,0,0,.08);border:1px solid #e2e8f0;text-align:center">
  <img src="vistas/img/plantilla/Captura3.PNG" alt="Logo" style="max-height:60px;margin-bottom:14px">
  <h2 style="margin:0 0 10px;font-size:18px;color:#0f172a;font-weight:800">Enlace no v&aacute;lido</h2>
  <p style="margin:0 0 14px;font-size:13px;color:#64748b;line-height:1.6">
    No pudimos encontrar tu orden con el c&oacute;digo proporcionado. Si tienes dudas comun&iacute;cate con nosotros.
  </p>
  <a href="https://comercializadoraegs.com" style="display:inline-block;background:#0f172a;color:#fff;padding:10px 22px;border-radius:10px;text-decoration:none;font-weight:700;font-size:13px">Ir al sitio</a>
</div>
