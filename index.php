<?php



require_once "controladores/plantilla.controlador.php";

require_once "controladores/administradores.controlador.php";



///////////////////////////////////////////////////////////////

require_once "controladores/banner.controlador.php";

require_once "controladores/categorias.controlador.php";

require_once "controladores/comercio.controlador.php";

require_once "controladores/mensajes.controlador.php";

require_once "controladores/perfiles.controlador.php";

require_once "controladores/productos.controlador.php";

require_once "controladores/slide.controlador.php";

require_once "controladores/usuarios.controlador.php";

require_once "controladores/ventas.controlador.php";

require_once "controladores/visitas.controlador.php";

require_once "controladores/subcategorias.controlador.php";

require_once "controladores/cabeceras.controlador.php";

require_once "controladores/notificaciones.controlador.php";

require_once "controladores/controlador.asesore.php";

require_once "controladores/empresas.controlador.php";

require_once "controladores/ordenes.controlador.php";

require_once "controladores/clientes.controlador.php";

require_once "controladores/tecnicos.controlador.php";

require_once "controladores/observacionOrdenes.controlador.php";

require_once "controladores/pedidos.controlador.php";

require_once "controladores/tickets.controlador.php";

require_once "controladores/comisiones.controlador.php";

//require_once "controladores/crm.controlador.php";

require_once "controladores/categorias.orden.controlador.php";

//

require_once "controladores/metas.controlador.php";
require_once "controladores/citas.controlador.php";


require_once "controladores/almacenes.controlador.php";
require_once "controladores/pedidos.controlador.php";
require_once "controladores/peticionmaterial.controlador.php";
require_once "controladores/cotizaciones.controlador.php";
/////////////////////////////////////////////////////////////// 

require_once "modelos/administradores.modelo.php";

require_once "modelos/metas.modelo.php";

require_once "modelos/citas.modelo.php";

require_once "modelos/banner.modelo.php";

require_once "modelos/categorias.modelo.php";

require_once "modelos/comercio.modelo.php";

require_once "modelos/mensajes.modelo.php";

require_once "modelos/perfiles.modelo.php";

require_once "modelos/productos.modelo.php";

require_once "modelos/slide.modelo.php";

require_once "modelos/usuarios.modelo.php";

require_once "modelos/ventas.modelo.php";

require_once "modelos/visitas.modelo.php";

require_once "modelos/subcategorias.modelo.php";

require_once "modelos/cabeceras.modelo.php";

require_once "modelos/notificaciones.modelo.php";

require_once "modelos/modelo.asesores.php";

require_once "modelos/empresas.modelo.php";

require_once "modelos/ordenes.modelo.php";

require_once "modelos/clientes.modelo.php";

require_once "modelos/tecnicos.modelo.php";

require_once "modelos/observacionOrdenes.modelo.php";

require_once "modelos/pedidos.modelo.php";

require_once "modelos/tickets.modelo.php";


require_once "modelos/reportes.modelo.php";


require_once "modelos/rutas.php";

//require_once "modelos/crm.modelo.php";

require_once "modelos/categorias.orden.modelo.php";

require_once "modelos/almacenes.modelo.php";
require_once "modelos/peticionmaterial.modelo.php";
require_once "modelos/cotizacion.modelo.php";
/////////////////////////modelos siste ventas//////////////////////////////////////



require_once "modelos/Articulo.php";

//require_once "modelos/Categoria.php";

//require_once "modelos/Ingreso.php";

//require_once "modelos/Ingreso.php";





/////////////////////////Librerias tikets//////////////////////////////////////

require_once "extensiones/vendor/autoload.php";

require_once "extensiones/PHPMailer/PHPMailerAutoload.php";

$plantilla = new ControladorPlantilla();

$plantilla->plantilla();