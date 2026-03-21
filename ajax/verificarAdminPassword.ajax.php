<?php
/*=============================================
  VERIFICAR CONTRASEÑA DE ADMINISTRADOR
  — Usado para confirmar eliminación de órdenes —

  POST esperado:
    egsAdminPass  : contraseña ingresada
    egsOrdenId    : id de la orden a eliminar
    egsImgPrincipal : ruta imagen principal
    egsImgPortada   : ruta imagen portada

  Respuesta JSON:
    { "ok": true,  "redirect": "..." }  → contraseña correcta
    { "ok": false, "msg": "..."      }  → error
=============================================*/

session_start();

header('Content-Type: application/json');

// ── 1. Solo administradores ─────────────────────────────────────────────────
if (!isset($_SESSION['validarSesionBackend'])
    || $_SESSION['validarSesionBackend'] !== 'ok'
    || !isset($_SESSION['perfil'])
    || $_SESSION['perfil'] !== 'administrador') {

    echo json_encode(['ok' => false, 'msg' => 'Solo los administradores pueden eliminar órdenes.']);
    exit;
}

// ── 2. Validar datos ─────────────────────────────────────────────────────────
if (!isset($_POST['egsAdminPass']) || !isset($_POST['egsOrdenId'])) {
    echo json_encode(['ok' => false, 'msg' => 'Datos incompletos.']);
    exit;
}

$passIngresada = $_POST['egsAdminPass'];
$idOrden       = intval($_POST['egsOrdenId']);
$imgPrincipal  = isset($_POST['egsImgPrincipal']) ? $_POST['egsImgPrincipal'] : '';
$imgPortada    = isset($_POST['egsImgPortada'])   ? $_POST['egsImgPortada']   : '';

// Solo alfanumérico para la contraseña (igual que el login)
if (!preg_match('/^[a-zA-Z0-9]+$/', $passIngresada)) {
    echo json_encode(['ok' => false, 'msg' => 'Contraseña inválida.']);
    exit;
}

if ($idOrden <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'ID de orden no válido.']);
    exit;
}

// ── 3. Verificar contraseña —– mismo hash que usa el login ──────────────────
$hashIngresado  = crypt($passIngresada, '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
$hashAlmacenado = isset($_SESSION['password']) ? $_SESSION['password'] : '';

if ($hashIngresado !== $hashAlmacenado || empty($hashAlmacenado)) {
    echo json_encode(['ok' => false, 'msg' => 'Contraseña incorrecta.']);
    exit;
}

// ── 4. Contraseña correcta → generar token de un solo uso ───────────────────
// Guardamos en sesión el token + id de orden autorizados (expira en 60s)
$_SESSION['egs_del_token']   = hash('sha256', $idOrden . $hashAlmacenado . time());
$_SESSION['egs_del_id']      = $idOrden;
$_SESSION['egs_del_expires'] = time() + 60;

// Construir URL de eliminación con token
$redirect = 'index.php?ruta=ordenesnew'
          . '&idOrden='       . $idOrden
          . '&imgPrincipal='  . urlencode($imgPrincipal)
          . '&imgPortada='    . urlencode($imgPortada)
          . '&egsDelToken='   . $_SESSION['egs_del_token'];

echo json_encode(['ok' => true, 'redirect' => $redirect]);
exit;
