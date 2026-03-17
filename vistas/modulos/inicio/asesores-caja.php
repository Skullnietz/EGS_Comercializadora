<?php
$item= "id_empresa";
$valor = $_SESSION["empresa"];
$itemDos = "perfil";
$valorDos = "vendedor";
$asesores = ControladorAdministradores::ctrlMostrarAdministradoresPorEmpresaRol($item, $valor, $itemDos, $valorDos);

$url = Ruta::ctrRuta();

?>


<!--=====================================
ÚLTIMOS USUARIOS
======================================-->

<!-- USERS LIST -->
<div class="box box-danger">

	<!-- box-header -->
  	<div class="box-header with-border">
  
	    <h3 class="box-title">Asesores registrados</h3>

	    <div class="box-tools pull-right">
	      
	      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
	      </button>
	  
	    </div>

  	</div>
  	<!-- /.box-header -->

  	<!-- box-body -->
  	<div class="box-body no-padding">

	    <!-- users-list -->
	    <ul class="users-list clearfix">

	     <?php

	     if(count($asesores) > 8){

	     	$totalAsesores = 8;
	     
	     }else{

	     	$totalAsesores = count($asesores);

	     }

	     for($i = 0; $i < $totalAsesores; $i ++){

	     	if($asesores[$i]["foto"] != ""){

		     	if($asesores[$i]["modo"] != "directo"){

			     	echo '<li>
					      <img src="'.$asesores[$i]["foto"].'" alt="User Image" style="width:100px">
					      <a class="users-list-name" href="">'.$asesores[$i]["nombre"].'</a>
					      <span class="users-list-date">'.$asesores[$i]["fecha"].'</span>
					      </li>';

				}else{


			     	echo '<li>
					      <img src="'.$asesores[$i]["foto"].'" alt="User Image" style="width:100px">
					      <a class="users-list-name" href="">'.$asesores[$i]["nombre"].'</a>
					      <span class="users-list-date">'.$asesores[$i]["fecha"].'</span>
					      </li>';

				}

			}else{

				 echo ' <li>
                  <img src="'.$asesores[$i]["foto"].'" alt="User Image" style="width:100px;">
                  <a class="users-list-name" href="#">'.$asesores[$i]["nombre"].'</a>
                  <span class="users-list-date">'.$asesores[$i]["fecha"].'</span>
                </li>';


			}


	     }

	     ?> 

	    </ul>
	  	<!-- /.users-list -->

  	</div>
  	<!-- /.box-body -->

  	<!-- box-footer -->
  	<div class="box-footer text-center">
    
    	<a href="index.php?ruta=asesores" class="uppercase">Ver todos los asesores</a>
  
  	</div>
  	<!-- /.box-footer -->

</div>
<!-- USERS LIST -->