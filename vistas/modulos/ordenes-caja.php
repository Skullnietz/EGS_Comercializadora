<?php

	$itemUno = "correo";
    
    $valorUno =  $_SESSION["email"];
	
	$tecnicoEnSession = ControladorTecnicos::ctrMostrarTecnicos($itemUno,$valorUno);
    
  $id_tecnico = $tecnicoEnSession["id"];



  $estadoUno = "Aceptado (ok)";

	$item = "id_empresa";


	$valor = $_SESSION["empresa"]; 

	$tecnico = "id_tecnico"; 

	$valorTecnico = $id_tecnico;

	$oredenesOk = controladorOrdenes::ctrlMostrarOrdenesPorEstadoEmpresayTecnico($estadoUno, $item, $valor, $tecnico, $valorTecnico);

		
$url = Ruta::ctrRuta();

?>


<!--=====================================
ÚLTIMOS USUARIOS
======================================-->

<!-- USERS LIST -->
<div class="box box-danger">

	<!-- box-header -->
  	<div class="box-header with-border">
  
	    <h3 class="box-title">Ordenes Aceptadas</h3>

	    <div class="box-tools">
	    	
	    	<?php


             echo '<a href="vistas/modulos/descargar-reporte-OrdenesPorEstado.php?reporte=ordenesOk&empresa='.$_SESSION["empresa"].'">';

	    	?>

	    </div>

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

	     if(count($oredenesOk) > 8){

	     	$totalOrdenes = 8;
	     
	     }else{

	     	$totalOrdenes = count($oredenesOk);

	     }

	     for($i = 0; $i < $totalOrdenes; $i ++){

	     	if($oredenesOk[$i]["portada"] != ""){

		     	if($oredenesOk[$i]["modo"] != "directo"){

			     	echo '<li>
					      <a href="index.php?ruta=infoOrden&idOrden='.$oredenesOk[$i]["id"].'&empresa='.$oredenesOk[$i]["id_empresa"].'&asesor='.$oredenesOk[$i]["id_Asesor"].'&cliente='.$oredenesOk[$i]["id_usuario"].'&tecnico='.$oredenesOk[$i]["id_tecnico"].'&tecnicodos='.$oredenesOk[$i]["id_tecnicoDos"].'&pedido='.$oredenesOk[$i]["id_pedido"].'" class="btn btn-warning" target="_blank">   

					      		<img src="'.$oredenesOk[$i]["portada"].'" alt="User Image" style="width:100px">

					      </a>

					      <a href="index.php?ruta=infoOrden&idOrden='.$oredenesOk[$i]["id"].'&empresa='.$oredenesOk[$i]["id_empresa"].'&asesor='.$oredenesOk[$i]["id_Asesor"].'&cliente='.$oredenesOk[$i]["id_usuario"].'&tecnico='.$oredenesOk[$i]["id_tecnico"].'&tecnicodos='.$oredenesOk[$i]["id_tecnicoDos"].'&pedido='.$oredenesOk[$i]["id_pedido"].'" class="btn btn-warning" target="_blank">

					      		'.$oredenesOk[$i]["id"].'

					      </a>

					      <span class="users-list-date">'.$oredenesOk[$i]["fecha"].'</span>

					    </li>';

				}else{


			     	echo '<li>
					      
					      	<a href="index.php?ruta=infoOrden&idOrden='.$oredenesOk[$i]["id"].'&empresa='.$oredenesOk[$i]["id_empresa"].'&asesor='.$oredenesOk[$i]["id_Asesor"].'&cliente='.$oredenesOk[$i]["id_usuario"].'&tecnico='.$oredenesOk[$i]["id_tecnico"].'&tecnicodos='.$oredenesOk[$i]["id_tecnicoDos"].'&pedido='.$oredenesOk[$i]["id_pedido"].'" class="btn btn-warning" target="_blank">

					      <a href="index.php?ruta=infoOrden&idOrden='.$oredenesOk[$i]["id"].'&empresa='.$oredenesOk[$i]["id_empresa"].'&asesor='.$oredenesOk[$i]["id_Asesor"].'&cliente='.$oredenesOk[$i]["id_usuario"].'&tecnico='.$oredenesOk[$i]["id_tecnico"].'&tecnicodos='.$oredenesOk[$i]["id_tecnicoDos"].'&pedido='.$oredenesOk[$i]["id_pedido"].'" class="btn btn-warning" target="_blank">

					      	'.$oredenesOk[$i]["id"].'

					      </a>

					      <span class="users-list-date">'.$oredenesOk[$i]["fecha"].'</span>
					    </li>';

				}

			}else{

				 echo ' <li>

                  <a href="index.php?ruta=infoOrden&idOrden='.$oredenesOk[$i]["id"].'&empresa='.$oredenesOk[$i]["id_empresa"].'&asesor='.$oredenesOk[$i]["id_Asesor"].'&cliente='.$oredenesOk[$i]["id_usuario"].'&tecnico='.$oredenesOk[$i]["id_tecnico"].'&tecnicodos='.$oredenesOk[$i]["id_tecnicoDos"].'&pedido='.$oredenesOk[$i]["id_pedido"].'" class="btn btn-warning" target="_blank">


                  	<img src="'.$oredenesOk[$i][$i]["portada"].'" alt="User Image" style="width:100px;">
                  </a>

                 <a href="index.php?ruta=infoOrden&idOrden='.$oredenesOk[$i]["id"].'&empresa='.$oredenesOk[$i]["id_empresa"].'&asesor='.$oredenesOk[$i]["id_Asesor"].'&cliente='.$oredenesOk[$i]["id_usuario"].'&tecnico='.$oredenesOk[$i]["id_tecnico"].'&tecnicodos='.$oredenesOk[$i]["id_tecnicoDos"].'&pedido='.$oredenesOk[$i]["id_pedido"].'" class="btn btn-warning" target="_blank">
                 
                  <span class="users-list-date">'.$oredenesOk[$i]["fecha"].'</span>
                
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
    
    	<a href="ordenes" class="uppercase">Ver todas los ordenes</a>
  
  	</div>
  	<!-- /.box-footer -->

</div>
<!-- USERS LIST -->