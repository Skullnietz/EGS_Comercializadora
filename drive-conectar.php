<?php
require_once __DIR__.'/config/DriveStorage.php';
ini_set('session.cookie_secure','1');
ini_set('session.cookie_httponly','1');
session_start();
header('Cache-Control: no-store');header('Referrer-Policy: no-referrer');header('X-Content-Type-Options: nosniff');header('X-Frame-Options: DENY');
if(empty($_SESSION['drive_csrf']))$_SESSION['drive_csrf']=bin2hex(random_bytes(32));
// A cPanel owner can authorize only this configuration screen without changing
// application roles. GET shows a confirmation form; it never consumes the link.
if(isset($_POST['owner_setup'])){
    if(!isset($_POST['csrf'])||!hash_equals($_SESSION['drive_csrf'],(string)$_POST['csrf'])){http_response_code(403);exit('No se pudo validar la sesión de configuración.');}
    try{EgsDriveStorage::claimOwnerToken($_POST['owner_setup']);session_regenerate_id(true);$_SESSION['drive_owner_setup']=array('expires_at'=>time()+3600);header('Location: '.EgsDriveStorage::REDIRECT_URI,true,303);exit;}
    catch(Throwable $e){http_response_code(403);exit('Este acceso del propietario venció o ya fue utilizado.');}
}
if(isset($_GET['owner_setup'])&&EgsDriveStorage::ownerTokenValid($_GET['owner_setup'])){
    $csrf=htmlspecialchars($_SESSION['drive_csrf'],ENT_QUOTES,'UTF-8');$token=htmlspecialchars($_GET['owner_setup'],ENT_QUOTES,'UTF-8');
    echo '<!doctype html><html lang="es"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Configurar Drive — propietario EGS</title><body style="font:18px system-ui;max-width:650px;margin:60px auto;padding:24px"><h1>Configurar Google Drive de EGS</h1><p>Este acceso temporal permite configurar el almacenamiento del sitio. Al continuar se abrirá una sesión de configuración de una hora.</p><form method="post"><input type="hidden" name="csrf" value="'.$csrf.'"><input type="hidden" name="owner_setup" value="'.$token.'"><button style="font:inherit;padding:14px">Continuar como propietario del servidor</button></form></body></html>';exit;
}
$isSuper=isset($_SESSION['validarSesionBackend'],$_SESSION['perfil'])&&$_SESSION['validarSesionBackend']==='ok'&&$_SESSION['perfil']==='Super-Administrador';
$isOwner=isset($_SESSION['drive_owner_setup']['expires_at'])&&$_SESSION['drive_owner_setup']['expires_at']>=time();
if(!$isSuper&&!$isOwner){http_response_code(403);echo 'Se requiere el acceso temporal del propietario del servidor o una sesión de Super-Administrador para configurar Google Drive.';exit;}
function drive_h($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
$message='';$error='';
try {
    if(isset($_GET['code']) || isset($_GET['error'])) {
        $flow=isset($_SESSION['drive_oauth'])?$_SESSION['drive_oauth']:array();unset($_SESSION['drive_oauth']);
        if(empty($flow['state']) || !isset($_GET['state'],$flow['at']) || time()-$flow['at']>600 || !hash_equals($flow['state'],(string)$_GET['state']))throw new RuntimeException('La autorización venció o no corresponde a esta sesión.');
        if(isset($_GET['error']))throw new RuntimeException('Google no autorizó la conexión.');
        $previous=EgsDriveStorage::readJson('tokens');
        try{EgsDriveStorage::exchangeCode((string)$_GET['code']);EgsDriveStorage::prepareLibrary();}
        catch(Throwable $e){EgsDriveStorage::saveJson('tokens',$previous);throw $e;}
        $_SESSION['drive_flash']='Google Drive quedó conectado. La carga automática aún no está activada.';
        header('Location: '.EgsDriveStorage::REDIRECT_URI,true,303);exit;
    }
    if($_SERVER['REQUEST_METHOD']==='POST') {
        if(!isset($_POST['csrf']) || !hash_equals($_SESSION['drive_csrf'],(string)$_POST['csrf']))throw new RuntimeException('La sesión del formulario venció.');
        $action=isset($_POST['action'])?$_POST['action']:'';
        if($action==='client'){
            if(isset($_FILES['client_file'])&&$_FILES['client_file']['error']!==UPLOAD_ERR_NO_FILE){
                $f=$_FILES['client_file'];if($f['error']!==UPLOAD_ERR_OK||!is_uploaded_file($f['tmp_name'])||filesize($f['tmp_name'])>20000)throw new RuntimeException('No se recibió un archivo JSON válido de menos de 20 KB.');
                $clientJson=file_get_contents($f['tmp_name']);
            }else $clientJson=isset($_POST['client_json'])?$_POST['client_json']:'';
            if(!is_string($clientJson)||strlen($clientJson)>20000)throw new RuntimeException('Configuración demasiado grande.');
            EgsDriveStorage::saveClient($clientJson);$message='Cliente guardado. Ya puedes autorizar tu cuenta de Google.';
        }elseif($action==='connect'){
            if(!empty($_POST['production_confirmed']))EgsDriveStorage::saveJson('oauth_production',array('confirmed'=>true,'confirmed_at'=>time(),'actor'=>'configuration_owner'));
            $state=bin2hex(random_bytes(32));$_SESSION['drive_oauth']=array('state'=>$state,'at'=>time());
            header('Location: '.EgsDriveStorage::authorizationUrl($state),true,303);exit;
        }elseif($action==='probe'){
            $info=EgsDriveStorage::prepareLibrary();
            $png=base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aG1sAAAAASUVORK5CYII=');
            $row=EgsDriveStorage::put('backend/vistas/img/drive-pruebas/conexion.png',$png,'image/png',array('drive_folder'=>'Pruebas','tipo'=>'prueba_conexion'));
            if(EgsDriveStorage::bytes($row)!==$png)throw new RuntimeException('La lectura de prueba no coincide.');
            EgsDriveStorage::saveJson('probe',array('verified_at'=>time(),'file_id'=>$row['file_id']));
            $message='Carga, lectura y comprobación de integridad verificadas en tu Drive.';
        }elseif($action==='enable'){
            $ready=EgsDriveStorage::readJson('integration_ready');$probe=EgsDriveStorage::readJson('probe');
            if(empty($ready['backend_ready'])||empty($ready['wordpress_ready'])||empty($probe['verified_at']))throw new RuntimeException('Primero deben verificarse ambos sitios y la carga de prueba.');
            EgsDriveStorage::token();EgsDriveStorage::saveJson('activation',array('at'=>time(),'actor'=>isset($_SESSION['id'])?$_SESSION['id']:'server_owner'));
            if(file_put_contents(EgsDriveStorage::root().'/.drive-media-enabled','drive')===false)throw new RuntimeException('No se pudo activar la lectura de imágenes.');chmod(EgsDriveStorage::root().'/.drive-media-enabled',0644);
            if(file_put_contents(EgsDriveStorage::stateDir().'/enabled','drive')===false)throw new RuntimeException('No se pudo activar Drive.');chmod(EgsDriveStorage::stateDir().'/enabled',0600);
            $message='Las nuevas cargas utilizarán Google Drive. Los archivos anteriores siguen disponibles durante la migración.';
        }elseif($action==='pause'){
            throw new RuntimeException('Para detener las nuevas cargas se debe conservar el servicio de lectura de las imágenes migradas. Contacta al responsable técnico.');
        }else throw new RuntimeException('Acción inválida.');
    }
}catch(Throwable $e){$error=$e->getMessage();}
if(isset($_SESSION['drive_flash'])){$message=$_SESSION['drive_flash'];unset($_SESSION['drive_flash']);}
$library=EgsDriveStorage::readJson('library');$ready=EgsDriveStorage::readJson('integration_ready');
?>
<!doctype html><html lang="es"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Conectar Google Drive — EGS</title>
<style>body{font:16px system-ui;max-width:850px;margin:40px auto;padding:20px;color:#163045;background:#f5f8fa}section{background:white;border:1px solid #d9e2e8;border-radius:10px;padding:24px;margin:20px 0}textarea{width:100%;box-sizing:border-box;min-height:150px}button{padding:12px 18px;background:#165f91;color:white;border:0;border-radius:6px;font:inherit;cursor:pointer}code{overflow-wrap:anywhere}.success{background:#dff3e8;padding:15px}.error{background:#ffebe6;padding:15px}a{color:#165f91}</style>
<h1>Almacenamiento de imágenes en Google Drive</h1><p>Conecta la cuenta propietaria de las imágenes del backend y del sitio público. Los archivos de Drive permanecen privados; el sitio seguirá mostrando sus imágenes mediante sus rutas actuales.</p>
<?php if($message):?><p class="success"><?=drive_h($message)?></p><?php endif;?><?php if($error):?><p class="error"><?=drive_h($error)?></p><?php endif;?>
<section><h2>1. Configurar la conexión</h2><p>En <a href="https://console.cloud.google.com/apis/library/drive.googleapis.com" target="_blank" rel="noopener">Google Cloud</a>, habilita Google Drive API y crea un cliente OAuth de tipo Aplicación web. Registra esta URL de retorno exacta:</p><p><code><?=drive_h(EgsDriveStorage::REDIRECT_URI)?></code></p><p>Selecciona el archivo JSON descargado del cliente. Las credenciales se guardan fuera del directorio público y no se muestran después de guardarlas.</p>
<form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=drive_h($_SESSION['drive_csrf'])?>"><input type="hidden" name="action" value="client"><input type="file" name="client_file" accept=".json,application/json" required><p><button>Guardar configuración</button></p></form></section>
<section><h2>2. Autorizar tu cuenta</h2><p>Se solicitará acceso a los archivos creados por esta integración, usando el permiso drive.file. La aplicación debe estar en modo Producción para que la conexión permanente no expire a los siete días.</p>
<p>EGS Multimedia utiliza la cuenta que autorices para guardar, organizar y recuperar las imágenes y documentos de las órdenes y del sitio. Guarda el correo de la cuenta, identificadores y metadatos de los archivos, y la autorización OAuth necesaria para operar. El hosting conserva las relaciones con los registros y sirve los archivos; las URLs que ya eran públicas mantienen esa visibilidad. Los clientes no tienen que conectar su Drive personal.</p>
<p>Antes de autorizar, consulta el <a href="https://comercializadoraegs.com/politica-de-privacidad/" target="_blank" rel="noopener">Aviso de privacidad</a>, las <a href="https://comercializadoraegs.com/condiciones-de-servicio/" target="_blank" rel="noopener">Condiciones de servicio</a> y la <a href="https://comercializadoraegs.com/integracion-google-drive/" target="_blank" rel="noopener">información de EGS Multimedia</a>. Puedes revocar el acceso desde las conexiones de tu cuenta de Google; la revocación no borra los archivos existentes y puede interrumpir las cargas y lecturas. Para gestionar la eliminación de datos, contacta a <a href="mailto:politicasdeprivacidad@comercializadoraegs.com">politicasdeprivacidad@comercializadoraegs.com</a>.</p>
<p>Para terminar el traslado y retirar los originales del hosting, abre <a href="https://console.cloud.google.com/auth/audience" target="_blank" rel="noopener">Google Cloud → Público</a>, selecciona este proyecto y pulsa <strong>Publicar aplicación</strong>. Luego marca la confirmación y vuelve a conectar para renovar la autorización después de publicar.</p>
<form method="post"><input type="hidden" name="csrf" value="<?=drive_h($_SESSION['drive_csrf'])?>"><input type="hidden" name="action" value="connect"><p><label><input type="checkbox" name="production_confirmed" value="1"> Confirmo que esta aplicación ya está publicada en Producción en Google Cloud.</label></p><button>Conectar con Google</button></form>
<p><?=EgsDriveStorage::productionReady()?'Autorización renovada después de confirmar Producción. Los lotes pueden retirar las copias locales verificadas.':'Las copias de archivos anteriores conservan sus originales hasta confirmar Producción y renovar la autorización.'?></p>
<?php if(!empty($library['root_id'])):?><p>Cuenta: <?=drive_h($library['owner_email'])?>. <a href="https://drive.google.com/drive/folders/<?=drive_h($library['root_id'])?>" target="_blank" rel="noopener">Abrir biblioteca de imágenes</a>.</p><?php endif;?></section>
<section><h2>3. Verificar antes de activar</h2><p>Esta prueba crea y vuelve a leer una imagen de un píxel en tu Drive. No modifica órdenes ni archivos existentes.</p><form method="post"><input type="hidden" name="csrf" value="<?=drive_h($_SESSION['drive_csrf'])?>"><input type="hidden" name="action" value="probe"><button>Probar carga y lectura</button></form><p>Estado: <?=EgsDriveStorage::enabled()?'Drive activo':'Preparación; las cargas actuales siguen en el servidor'?>.</p>
<?php if(!empty($ready['backend_ready'])&&!empty($ready['wordpress_ready'])):?><form method="post"><input type="hidden" name="csrf" value="<?=drive_h($_SESSION['drive_csrf'])?>"><input type="hidden" name="action" value="enable"><button>Activar nuevas cargas a Drive</button></form><?php else:?><p>La activación se habilitará cuando las adaptaciones del backend y de WordPress estén verificadas.</p><?php endif;?></section></html>
