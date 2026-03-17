<?php



if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"] != "Super-Administrador"){



  echo '<script>



  window.location = "inicio";

  

  



  </script>';

  



  return;



}



?>
<!-- "COMBOBOX SIMULACION JQUERY" Muestra las opciones segun lo seleccionado TECNICOS,ASESORES-->
<script> 
    $(document).ready(function(){
        $("#combobox1").hide();
        $("#combobox2").hide();
        $(".select1").hide();
        $(".select2").hide();
        $(".select3").hide();
        $(".select4").hide();
        $(".select5").hide();
        $("#selopt").change(function(){
            var asignar= $(this).val();
            if(asignar== "Departamento"){
                $("#combobox1").hide();
                $("#combobox2").show();
            }if(asignar== "Individual"){
                $("#combobox1").show();
                $("#combobox2").hide();
            }
            
        });
        $("#seldepartamento").change(function(){
            var depa = $(this).val();
            if(depa == "Asesores"){
                $(".select1").show();
                $(".select2").hide();
                $(".select3").hide();
                $(".select4").hide();
                $(".select5").hide();
            }if(depa == "Sistemas"){
                $(".select1").hide();
                $(".select2").show();
                $(".select3").hide();
                $(".select4").hide();
                $(".select5").hide();
            }if(depa == "Electronica"){
                $(".select1").hide();
                $(".select2").hide();
                $(".select3").show();
                $(".select4").hide();
                $(".select5").hide();
            }if(depa == "Impresoras"){
                $(".select1").hide();
                $(".select2").hide();
                $(".select3").hide();
                $(".select4").show();
                $(".select5").hide();
            }if(depa == "Ventas Externas"){
                $(".select1").hide();
                $(".select2").hide();
                $(".select3").hide();
                $(".select4").hide();
                $(".select5").show();
            }
        });
    });
</script>


<div class="content-wrapper">

	

	<section class="content-header">

		

		<h1>

			Gestor Objetivos

		</h1>



		<ol class="breadcrumb">



	      <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>



	      <li class="active">Crear Objetivo</li>

      

    	</ol>



	</section>

    <section class="content">



	    <div class="box"> 



	       <div class="box-body">



	          

           <br>

<h3>

  Creación de Meta

</h3>

<br>
<p class=" text-lg-center font-weight-bold col-form-label ">

    Asignar meta por 

  </p>
<select class="form-control" id="selopt"> 
<option>Departamento | Individual</option>
<option value="Departamento">Por departamento</option>
<option value="Individual">Individual</option>
</select>
<br>
<div id="combobox1">
<form method="POST">
  <p class=" text-lg-center font-weight-bold col-form-label ">

    Área

  </p>

  <select class="form-control" name="area" id="seldepartamento">

  <option>Área (Departamento)</option>
  <option value="Asesores">Asesores</option>
  <option value="Ventas Externas">Ventas Externas</option>
  <option value="Sistemas">Sistemas</option>
  <option value="Electronica">Electronica</option>
  <option value="Impresoras">Impresoras</option>
  

</select>

  <br>
  <!-- SELECT DE ASESORES -->

  <p class=" text-lg-center font-weight-bold col-form-label ">

    Usuario

  </p>

  <select class="form-control" name="usuario" >

  <option>Usuario (Técnico | Asesor)</option>

  <?php
  

    $item = "Departamento";

    $valor = "Ventas";

    $itemDos = "Departamento";

    $valorDos = "Sistemas";

    $empleados = ControladorAdministradores::ctrlMostrarAdministradoresPorEmpresaRol($itemDos, $valorDos, $item, $valor );

    foreach ($empleados as $key => $value) {

      echo'<option class="select1" value="'.$value["id"].'">'.$value["nombre"].'</option>';

    }
   
  ?>
  <!-- SELECT DE SISTEMAS -->
  <?php
  

    $item = "Departamento";

    $valor = "Sistemas";

    $itemDos = "Departamento";

    $valorDos = "Ventas";

    $empleados = ControladorAdministradores::ctrlMostrarAdministradoresPorEmpresaRol($itemDos, $valorDos, $item, $valor );

    foreach ($empleados as $key => $value) {

      echo'<option class="select2" value="'.$value["id"].'">'.$value["nombre"].'</option>';

    }
   
  ?>
  <!-- SELECT DE ELECTRONICA -->
  <?php
  

    $item = "Departamento";

    $valor = "Electronica";

    $itemDos = "Departamento";

    $valorDos = "Ventas";

    $empleados = ControladorAdministradores::ctrlMostrarAdministradoresPorEmpresaRol($itemDos, $valorDos, $item, $valor );

    foreach ($empleados as $key => $value) {

      echo'<option class="select3" value="'.$value["id"].'">'.$value["nombre"].'</option>';

    }
   
  ?>
  <!-- SELECT DE IMPRESORAS -->
  <?php
  

    $item = "Departamento";

    $valor = "Impresoras";

    $itemDos = "Departamento";

    $valorDos = "Ventas";

    $empleados = ControladorAdministradores::ctrlMostrarAdministradoresPorEmpresaRol($itemDos, $valorDos, $item, $valor );

    foreach ($empleados as $key => $value) {

      echo'<option class="select4" value="'.$value["id"].'">'.$value["nombre"].'</option>';

    }
   
  ?>
  <!-- SELECT DE VENTAS EXTERNAS -->
  <?php
  

    $item = "Departamento";

    $valor = "Ventas Externas";

    $itemDos = "Departamento";

    $valorDos = "Ventas";

    $empleados = ControladorAdministradores::ctrlMostrarAdministradoresPorEmpresaRol($itemDos, $valorDos, $item, $valor );

    foreach ($empleados as $key => $value) {

      echo'<option class="select5" value="'.$value["id"].'">'.$value["nombre"].'</option>';

    }
   
  ?>

</select>





 <br>

<hr>

  <div class="form-group row" name="tipo">

    <label for="colFormLabelLg" class="col-sm-2 col-form-label col-form-label-lg">Tipo de Meta</label>

    <div class="col-sm-10">

      <input type="text" class="form-control " id="colFormLabelLg" placeholder="* Ejemplo: 10 Llamadas" name="tipo">

    </div>

  </div>

  <br>

  

  <div class="form-group row" name="descripcion">

    <label for="colFormLabelLg" class="col-sm-2 col-form-label col-form-label-lg">Descripción</label>

    <div class="col-sm-10">

      <input type="text" class="form-control " id="colFormLabelLg" placeholder="* Breve descripción de la meta" name="descripcion">

    </div>

  </div>

  <br>

  <select class="form-control input-lg" name="actividades">

  <option>Numero de actividades</option>

    <?php

    for ($i=0; $i <= 100; $i++) { 
      
      echo '<option>'.$i.'</option>';
    }

    ?>

  </select>

  <br>
  <button class="btn btn-primary" style="float:right;" type="submit">Subir meta</button>

  </form>
</div>

<!-- Select por departamento-->
<div id="combobox2">
<p class=" text-lg-center font-weight-bold col-form-label ">

    Área

  </p>

  <select class="form-control" name="area" id="seldepartamento">

  <option>Área (Departamento)</option>
  <option value="Asesores">Asesores</option>
  <option value="Ventas Externas">Ventas Externas</option>
  <option value="Sistemas">Sistemas</option>
  <option value="Electronica">Electronica</option>
  <option value="Impresoras">Impresoras</option>
  </select>
  <br>

<hr>

  <div class="form-group row" name="tipo">

    <label for="colFormLabelLg" class="col-sm-2 col-form-label col-form-label-lg">Tipo de Meta</label>

    <div class="col-sm-10">

      <input type="text" class="form-control " id="colFormLabelLg" placeholder="* Ejemplo: 10 Llamadas" name="tipo">

    </div>

  </div>

  <br>

  

  <div class="form-group row" name="descripcion">

    <label for="colFormLabelLg" class="col-sm-2 col-form-label col-form-label-lg">Descripción</label>

    <div class="col-sm-10">

      <input type="text" class="form-control " id="colFormLabelLg" placeholder="* Breve descripción de la meta" name="descripcion">

    </div>

  </div>

  <br>

  <select class="form-control input-lg" name="actividades">

  <option>Numero de actividades</option>

    <?php

    for ($i=0; $i <= 100; $i++) { 
      
      echo '<option>'.$i.'</option>';
    }

    ?>
    </select>
    
</div></div>
  <?php

  $objeto = new ControladorMetas();
  $objeto -> ctrlSUbirMeta();

  ?>

  </div>

   </section>

</div>
