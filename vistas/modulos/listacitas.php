<?php
if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "Super-Administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}
?>
<script>
 $(document).ready(function(){
     //Validaciones segun el input
    const $input1 = document.querySelector('#nombrecliente');
    const patron1 = /[a-zA-ZñÑ ÉÁÍÓ()]+/;
    $input1.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron1.test(event.key)){
                    $("#nombrecliente").css({ "border": "1px solid #0C0"});
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
    const $input2 = document.querySelector('#whatsapp');
    const patron2 = /[0-9-]+/;
    $input2.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron2.test(event.key)){
                    $("#whatsapp").css({ "border": "1px solid #0C0"});
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
    const $input3 = document.querySelector('#telefono');
    const patron3 = /[0-9-]+/;
    $input3.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron3.test(event.key)){
                    $("#telefono").css({ "border": "1px solid #0C0"});
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
        //Nombre en Mayus
    
    $("#nombrecliente").keyup(function() {
        $('#nombrecliente').val($(this).val().toUpperCase());
    });
   
    
 });
</script>
<div class="content-wrapper">
    
  <section class="content-header">
      
    <h1>
      Gestor usuarios
    </h1>
 
    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>

      <li class="active">Gestor usuarios</li>
      
    </ol>

  </section>

  <section class="content">

    <div class="box">  

      <div class="box-header with-border">

      </div>

      <div class="box-body">

        <div class="box-tools">

          <?php

            if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == 
              "Super-Administrador" || $_SESSION["perfil"] == "vendedor") {
              
              echo'<a href="vistas/modulos/descargar-reporte-Usuarios.php?reporte=usuariosTienda&empresa='.$_SESSION["empresa"].'">

                <button class="btn btn-success" style="margin-top:5px">Descargar Reporte de Citas</button>

              </a>

               <a href="vistas/modulos/descargar-reporte-Usuarios.php?reportes=usuariosTiendaENT&empresa='.$_SESSION["empresa"].'">

               <button class="btn btn-success" style="margin-top:5px">Descargar Reporte de Atrasadas</button>

              </a>';

            }

          ?>

     

        </div> 

         <div class="box-header with-border">
         
        <a class="btn btn-primary" href="index.php?ruta=crearcitas">

          
          Agregar Cita

        </a>

      </div>


        <br>
         
        <table class="table table-bordered table-striped dt-responsive" width="100%">

          <thead>
            
            <tr>
              
              <th style="width:10px">#</th>
              <th scope="col">Orden</th>
              <th scope="col">Prioridad</th>
              <th scope="col">Fecha</th>
              <th scope="col">Hora | Tiempo</th>
              <th scope="col">Creada por</th>
              <th scope="col">Fecha Creación</th>
              <th scope="col">Reagendar</th>
              <th scope="col">Acciones</th>

            </tr>

          </thead>

          <?php
      $citas = ControladorCitas::ctrMostrarUltimasCitas($item, $valor);
foreach ($citas as $key => $value){
$idperfil = $value["creadapor"]; 
$nombre1 = ControladorCitas::ctrMostrarCreadorCita($tabla, $idperfil);
$nombre= $nombre1;
    echo'<tr>
    <th scope="row"> '.$value["cita_id"].'</th>
    <td> '.$value["cita_orden"].'</td>';
     if($value["prioridad"] == "4.Baja"){ echo '<td style="background-color:#98FB98;"> '.$value["prioridad"].'</td>';}
  if($value["prioridad"] == "3.Normal"){ echo '<td style="background-color:#87CEFA;"> '.$value["prioridad"].'</td>';}
  if($value["prioridad"] == "2.Alta"){ echo '<td style="background-color:yellow;"> '.$value["prioridad"].'</td>';}
  if($value["prioridad"] == "1.Urgente"){ echo '<td style="background-color:red; color:white"> '.$value["prioridad"].'</td>';}
    echo '
    <td> '.$value["fecha"].'</td>
    <td> '.$value["hora"].'</td>';
    foreach ($nombre as $val){ 
    echo '<td> '.$val["nombre"].'</td>';
    }
    
    echo '<td> '.$value["fechacreacion"].'</td>
    <td><button class="btn btn-sm btn-info"><i class="fas fa-calendar-day"></i> Reagendar cita</button></td>
    <td>
    <a href="index.php?ruta=infoOrden&idOrden='.$value["cita_orden"].'" class="btn btn-sm btn-primary" title="Ver Orden"><i class="fas fa-eye"></i></a> ';

    // Google Calendar link - obtener datos de orden y cliente
    $gcalTitle = urlencode('Cita - Orden #'.$value["cita_orden"]);
    $fechaRaw = $value["fecha"].' '.$value["hora"];
    $gcalStart = date('Ymd\THis', strtotime($fechaRaw));
    $gcalEnd = date('Ymd\THis', strtotime($fechaRaw.' +1 hour'));
    $gcalDetalles = array('Orden #'.$value["cita_orden"]);
    // Obtener nombre del cliente y descripción del equipo
    $_gcOrd = ModeloCitas::mdlMostrarCitas("citas", "id_orden", $value["cita_orden"]);
    if (!empty($_gcOrd)) {
        // Query directo para datos de orden y cliente
        try {
            $_gcStmt = Conexion::conectar()->prepare("SELECT o.descripcion, cl.nombre FROM ordenes o LEFT JOIN clientes cl ON o.id_usuario = cl.id WHERE o.id = :id LIMIT 1");
            $_gcStmt->bindParam(":id", $value["cita_orden"], PDO::PARAM_INT);
            $_gcStmt->execute();
            $_gcData = $_gcStmt->fetch(PDO::FETCH_ASSOC);
            if ($_gcData) {
                if (!empty($_gcData["nombre"])) $gcalDetalles[] = 'Cliente: '.$_gcData["nombre"];
                if (!empty($_gcData["descripcion"])) $gcalDetalles[] = 'Equipo: '.$_gcData["descripcion"];
            }
            $_gcStmt = null;
        } catch(Exception $e) {}
    }
    $gcalDetails = urlencode(implode("\n", $gcalDetalles));
    $gcalUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text='.$gcalTitle.'&dates='.$gcalStart.'/'.$gcalEnd.'&details='.$gcalDetails;

    echo '<a href="'.$gcalUrl.'" target="_blank" class="btn btn-sm" style="background:#4285f4;color:#fff" title="Agendar en Google Calendar"><i class="fab fa-google"></i></a> ';

    echo '<button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEditarCita"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-danger"><i class="fas fa-times"></i></button>
    </td>
    </tr>';
    
}
?>

        </table> 

      </div>
        
    </div>

  </section>

</div>

<!--=====================================
MODAL AGREGAR CLIENTE
======================================-->

<div id="modalEditarCita" class="modal fade" role="dialog">

  <div class="modal-dialog">
    
    <div class="modal-content">
      
      <form role="form" method="POST" enctype="multipart/form-data">
        
        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#138a1e; color:white">
          
          <button type="button" class="close" data-dismiss="modal">

            &times;

          </button>

            <h4 class="modal-title">
              
              Editar Cita 

            </h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">
          
          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE -->

            <div class="form-group">
              <label>Numero de orden</label>
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fas fa-clipboard-list"></i></span>

                <input type="text"  class="form-control input"  name="AgregarNombreCliente" placeholder="NUMERO DE ORDEN" id="numorden">
              </div>

            </div>            

            <!-- ENTRADA PARA EL CORREO -->

            <div class="form-group">
              <label>Prioridad</label>
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fas fa-stream"></i></span>

                <input type="text"  class="form-control " name="AgregarCorreoCliente" placeholder="PRIORIDAD">

              </div>

            </div>  
            
            <div class="form-group">
                <label for="exampleInputEmail1">Fecha de la cita</label>
                 <div class='input-group date'>
                <input class="form-control" id="datepicker" placeholder="Elige una fecha" name="fecha">
                <span class="input-group-addon">
                <span ><i class="far fa-calendar"></i></span>
              </div></div>

            <div class="form-group">
            <div class='input-group date' id='datetimepicker3'>
               <input id='datetimepicker3' type='text' class="form-control" name="hora" title="Para facilitar la tarea, da un click en el. ➡️🕐 "  placeholder="Elige una hora"/>
               <span class="input-group-addon">
               <span class="glyphicon glyphicon-time"></span>
               </span>
            </div>
         </div>
            <!--=========================
            AGREGAR ETIQUETA AL CLIENTE
            ============================-->
            <div class="form-group">
                <label>Creador</label>
                <div class="input-group">
                    <span class="input-group-addon">
                    
                        <i class="fas fa-tag"></i>

                    </span>

                   <input type="tel" class="form-control"  name="telefonoUnoCliente" placeholder="CREADA POR" required>
                    
                </div>
            </div>

           

            </div>

          </div>

          <!--=====================================
          PIE DEL MODAL
          ======================================-->
    
          <div class="modal-footer">

            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

            <button type="submit" class="btn btn-primary">Editar cita</button>

          </div>

        </div>
      <?php
          
         $AgregarCliente = new ControladorClientes();
         $AgregarCliente -> ctrMostrarAgregarCliente();

        ?>
      </form>

    </div>

  </div>

</div>
<script>

    $(document).ready(function(){
        
  

 // String

$(function() {
       $( "#datepicker" ).datepicker({
   format: 'yyyy-mm-dd'
});
    
});


});

</script>
<script type="text/javascript">
         $(function () {
             $('#datetimepicker3').datetimepicker({
                 format: 'LT'
                  
             });
         });
          $( function() {
    $( document ).tooltip();
  } );
</script>
<?php

 // $eliminarAsesor = new Controladorasesores();
  //$eliminarAsesor -> ctrEliminarAsesor();

