<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<?php



if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"] != "vendedor"){



  echo '<script>



  window.location = "inicio";

  

  



  </script>';

  



  return;



}?>
<div class="content-wrapper">

	

<section class="content-header">

  

  <h1>

    Gestor Citas

  </h1>



  <ol class="breadcrumb">



      <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>



      <li class="active">Crear Cita</li>

    

    </ol>



</section>

  <section class="content">



    <div class="box"> 



       <div class="box-body">



          

         <br>


<div class="container">
    <h3>

Crear cita

</h3>
    <div class="row">
        <div class="col-lg-6">
            <form method="POST">
      
   <div class="form-group">
    <label for="exampleFormControlSelect1">Seleccionar orden</label>
    <select class="form-control" id="exampleFormControlSelect1" name="citaorden">
      <option>Selecciona tu orden (Ordenes en Terminado)</option>
      <?php
      $orden_ter = "Terminada (ter)";
  $orden_ter = ControladorCitas::ctrMostrarOrdenesTER($tabla, $orden_ter);
  foreach ($orden_ter as $key => $valu){
      echo '<option value="'.$valu["id"].'">'.$valu["id"].' </option>';
  }
      ?>
    </select>
  </div>
  <div class="form-group">
    <label for="exampleFormControlSelect1">Prioridad de la orden</label>
    <select class="form-control" id="exampleFormControlSelect1" name="prioridad">
        <option>Selecciona la proridad de la orden</option>
      <option>4.Baja</option>
      <option>3.Normal</option>
      <option>2.Alta</option>
      <option>1.Urgente</option>
      </select>
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
         </div><?php
    $valorcreador = $_SESSION["id"];
      echo'<input type="hidden" name="creadapor" value=" '.$valorcreador.'">';?>
      
        <center><button class="btn btn-primary"  type="submit">Crear cita</button></center>

  </form>
    
  </div>


<div class="col-lg-6">
            <h3>Citas creadas recientemente</h3>
            <table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Orden</th>
      <th scope="col">Prioridad</th>
      <th scope="col">Fecha</th>
      <th scope="col">Hora</th>
      <th scope="col">Creada por</th>
    </tr>
  </thead>
  <tbody><?php
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
    </tr>';
}
?>
  </tbody>
</table>
        </div>
        </div>
    </div>

</div>
</div>
<?php
$objeto = new ControladorCitas();
$objeto -> ctrlSubirCita();
?>   

</section>


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