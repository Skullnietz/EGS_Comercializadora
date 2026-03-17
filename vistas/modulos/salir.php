<?php

session_destroy();

echo '<script>
	
	window.location= "index.php?ruta=ingreso";

</script>';

$tabla="administradores";
$sesionActiva=0;
$activarsesion = ModeloAdministradores::mdlActivarSesion($tabla,$_SESSION["id"],$sesionActiva);