<script src="vistas/bower_components/jquery/dist/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script
  src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<!DOCTYPE html>
<style>
  .choices__list--dropdown {
    z-index: 9999 !important;
  }
</style>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>EGS EQUIPO DE COMPUTO | Panel de Control</title>

  <link rel="icon" href="vistas/img/plantilla/icono.png">

  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, minimum-scale=1.0" />
  <!--<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">-->
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="vistas/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="vistas/bower_components/font-awesome/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="vistas/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="vistas/dist/css/AdminLTE.css">
  <link rel="stylesheet" href="vistas/dist/css/skins/skin-blue.min.css">
  <link rel="stylesheet" href="vistas/css/custom.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="vistas/plugins/iCheck/square/blue.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="vistas/bower_components/morris.js/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="vistas/bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="vistas/bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">

  <!-- En el <head> CHOICES.js -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

  <!-- bootstrap slider -->
  <link rel="stylesheet" href="vistas/plugins/bootstrap-slider/slider.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <!-- DataTables -->
  <link rel="stylesheet" href="vistas/bower_components/datatables.net-bs/css/dataTables.bootstrap.css">
  <link rel="stylesheet" href="vistas/bower_components/datatables.net-bs/css/responsive.bootstrap.min.css">



  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="vistas/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

  <!-- Dropzone -->
  <link rel="stylesheet" href="vistas/plugins/dropzone/dropzone.css">

  <!-- Dropzone -->
  <link rel="stylesheet" href="vistas/plugins/dropzone/dropzone.css">

  <!-- Daterange picker -->
  <link rel="stylesheet" href="vistas/bower_components/bootstrap-daterangepicker/daterangepicker.css">



  <!-- REQUIRED JS SCRIPTS -->

  <!-- jQuery UI 1.11.4 
  <script src="vistas/bower_components/jquery-ui/jquery-ui.min.js"></script>-->

  <!-- Justo antes de </body> -->
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

  <!-- Bootstrap 3.3.7 -->
  <script src="vistas/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <!-- AdminLTE App -->
  <script src="vistas/dist/js/adminlte.min.js"></script>
  <!-- iCheck -->
  <script src="vistas/plugins/iCheck/icheck.min.js"></script>
  <!-- Morris.js charts -->
  <script src="vistas/bower_components/raphael/raphael.min.js"></script>
  <script src="vistas/bower_components/morris.js/morris.min.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="vistas/bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
  <!-- jvectormap -->
  <script src="vistas/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
  <script src="vistas/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
  <!-- ChartJS -->
  <script src="vistas/bower_components/chart.js/Chart.js"></script>

  <!-- SweetAlert 2 https://sweetalert2.github.io/-->
  <script src="vistas/plugins/sweetalert2/sweetalert2.all.js"></script>

  <!-- bootstrap color picker https://farbelous.github.io/bootstrap-colorpicker/v2/-->
  <script src="vistas/bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>

  <!--Number JS-->
  <script src="vistas/plugins/jqueryNumber/jquerynumber.min.js"></script>

  <!-- Bootstrap slider http://seiyria.com/bootstrap-slider/-->
  <script src="vistas/plugins/bootstrap-slider/bootstrap-slider.js"></script>

  <!-- DataTables https://datatables.net/-->
  <!--<script src="vistas/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>-->
  <script src="vistas/bower_components/datatables.net/js/jquery.dataTables.js"></script>
  <script src="vistas/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
  <script src="vistas/bower_components/datatables.net-bs/js/dataTables.responsive.min.js"></script>
  <script src="vistas/bower_components/datatables.net-bs/js/responsive.bootstrap.min.js"></script>



  <!-- bootstrap tags input https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/-->
  <script src="vistas/plugins/tags/bootstrap-tagsinput.min.js"></script>

  <!-- bootstrap datetimepicker http://bootstrap-datepicker.readthedocs.io-->
  <script src="vistas/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

  <!-- Dropzone http://www.dropzonejs.com/-->
  <script src="vistas/plugins/dropzone/dropzone.js"></script>

  <!-- daterangepicker http://www.daterangepicker.com/-->
  <script src="vistas/bower_components/moment/min/moment.min.js"></script>
  <script src="vistas/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>

  <script src="vistas/plugins/bardcode/codigo.js"></script>
  <script src="vistas/plugins/printArea/jquery.PrintArea.js"></script>
  <script src="vistas/plugins/push/push.min.js"></script>



</head>

<body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">

  <?php

  session_start();

  if (isset($_SESSION["validarSesionBackend"]) && $_SESSION["validarSesionBackend"] === "ok" || (isset($_GET["ruta"]) && $_GET["ruta"] == "validar-cotizacion")) {

    echo '<div class="wrapper">';



    /*=============================================
     CABEZOTE
     =============================================*/

    include "modulos/cabezote.php";

    /*=============================================
     LATERAL
     =============================================*/

    include "modulos/lateral.php";

    /*=============================================
    CONTENIDO
    =============================================*/

    if (isset($_GET["ruta"])) {

      if (
        $_GET["ruta"] == "inicio" ||
        //$_GET["ruta"]== "comercio" ||
        //$_GET["ruta"]== "slide" ||
        $_GET["ruta"] == "categorias" ||
        $_GET["ruta"] == "subcategorias" ||
        $_GET["ruta"] == "productos" ||
        $_GET["ruta"] == "banner" ||
        $_GET["ruta"] == "ventas" ||
        $_GET["ruta"] == "visitas" ||
        $_GET["ruta"] == "usuarios" ||
        $_GET["ruta"] == "perfiles" ||
        $_GET["ruta"] == "perfil" ||
        $_GET["ruta"] == "almacen" ||
        $_GET["ruta"] == "categoriasp" ||
        $_GET["ruta"] == "ingreso" ||
        $_GET["ruta"] == "proveedor" ||
        $_GET["ruta"] == "venta" ||
        $_GET["ruta"] == "cliente" ||
        $_GET["ruta"] == "consultacompras" ||
        $_GET["ruta"] == "historial-cotizaciones" ||
        $_GET["ruta"] == "crm" ||
        $_GET["ruta"] == "asesores" ||
        $_GET["ruta"] == "ventasR" ||
        $_GET["ruta"] == "ticketR" ||
        $_GET["ruta"] == "empresas" ||
        $_GET["ruta"] == "reportes" ||
        $_GET["ruta"] == "reportePorFecheOrdenes" ||
        $_GET["ruta"] == "tecnicos" ||
        $_GET["ruta"] == "CorteTotal" ||
        $_GET["ruta"] == "corteStatus" ||
        $_GET["ruta"] == "clientes" ||
        $_GET["ruta"] == "pedidos" ||
        $_GET["ruta"] == "creararventa" ||
        $_GET["ruta"] == "stock" ||
        $_GET["ruta"] == "ventasD" ||
        $_GET["ruta"] == "pedidosPrueba" ||
        $_GET["ruta"] == "EstadoOrden" ||
        $_GET["ruta"] == "infoOrden" ||
        $_GET["ruta"] == "AgregarPedido" ||
        $_GET["ruta"] == "infopedido" ||
        $_GET["ruta"] == "infoCliente" ||
        $_GET["ruta"] == "crearTicket" ||
        $_GET["ruta"] == "tickets" ||
        $_GET["ruta"] == "infoTicket" ||
        $_GET["ruta"] == "ordenes" ||
        $_GET["ruta"] == "ordenes/2" ||
        $_GET["ruta"] == "comisiones" ||
        $_GET["ruta"] == "comisionesDos" ||
        $_GET["ruta"] == "lasordenes" ||
        $_GET["ruta"] == "objetivos" ||
        $_GET["ruta"] == "createobjetivo" ||
        $_GET["ruta"] == "listaobjetivos" ||
        $_GET["ruta"] == "objetivosventas" ||
        $_GET["ruta"] == "objetivoselectronica" ||
        $_GET["ruta"] == "objetivosimpresoras" ||
        $_GET["ruta"] == "objetivossistemas" ||
        $_GET["ruta"] == "metas" ||
        $_GET["ruta"] == "preguntas" ||
        $_GET["ruta"] == "productoswoocommerce" ||
        $_GET["ruta"] == "categoriasOrden" ||
        $_GET["ruta"] == "pantallacitas" ||
        $_GET["ruta"] == "objetivosventasext" ||
        $_GET["ruta"] == "almacenes" ||
        $_GET["ruta"] == "crearcita" ||
        $_GET["ruta"] == "ordenesnew" ||
        $_GET["ruta"] == "listacitas" ||
        $_GET["ruta"] == "ordenesentornoprueba" ||
        $_GET["ruta"] == "pedidosentornodeprueba" ||
        $_GET["ruta"] == "cotizacion" ||
        $_GET["ruta"] == "peticionmaterial" ||
        $_GET["ruta"] == "Historialdecliente" ||
        $_GET["ruta"] == "peticionorden" ||
        $_GET["ruta"] == "busquedamaterial" ||
        $_GET["ruta"] == "listaPeticionesM" ||
        $_GET["ruta"] == "salir" ||
        $_GET["ruta"] == "validar-cotizacion"
      ) {

        include_once "modulos/" . $_GET["ruta"] . ".php";


      } else {

        include_once "modulos/error404.php";
        include_once "extensiones/tcpdf/pdf/pdf.php";

      }

    }

    /*=============================================
    FOOTER
    =============================================*/

    include_once "modulos/footer.php";


    echo '</div>';

  } else {

    include_once "modulos/login.php";

  }


  ?>

  <script src="vistas/js/plantilla.js"></script>
  <script src="vistas/js/gestorComercio.js"></script>
  <script src="vistas/js/gestorCategorias.js"></script>
  <script src="vistas/js/gestorSubCategorias.js"></script>
  <script src="vistas/js/gestorProductos.js"></script>
  <script src="vistas/js/gestorVentas.js"></script>
  <script src="vistas/js/gestorVentasR.js"></script>
  <script src="vistas/js/gestorVisitas.js"></script>
  <script src="vistas/js/gestorUsuarios.js"></script>
  <script src="vistas/js/gestorAdministradores.js"></script>
  <script src="vistas/js/gestorNotificaciones.js"></script>
  <script src="vistas/js/gestorAsesores.js"></script>
  <script src="vistas/js/gestorEmpresas.js"></script>
  <script src="vistas/js/gestorOrdenes.js?v=<?= time() ?>"></script>
  <script src="vistas/js/gestorTecnicos.js"></script>
  <script src="vistas/js/reporteGrafica.js"></script>
  <script src="vistas/js/CorteTotal.js"></script>
  <script src="vistas/js/gestorClientes.js"></script>
  <script src="vistas/js/ventasDinamicas.js"></script>
  <script src="vistas/js/gestorStockApuntoDeAgotarse.js"></script>
  <script src="vistas/js/gestorStockAlerta.js"></script>
  <script src="vistas/js/infoOrden.js"></script>
  <script src="vistas/js/gestorBanner.js"></script>
  <script src="vistas/js/gestor.ticket.stock.js"></script>
  <script src="vistas/js/gestor.pedidos.js"></script>
  <script src="vistas/js/gestor.comisiones.js"></script>
  <script src="vistas/js/gestor.crm.js"></script>
  <script src="vistas/js/almacenes.js"></script>


</body>

</html>