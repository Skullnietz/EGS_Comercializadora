<?php

	$itemUno = "correo";
    
    $valorUno =  $_SESSION["email"];
	
	$asesorEnsession = Controladorasesores::ctrMostrarAsesoresEleg($itemUno,$valorUno);
    
  	$id_asesor = $asesorEnsession["id"];

  	

  	$estadoUno = "Pendiente de autorización (AUT";

	$item = "id_empresa";


	$valor = $_SESSION["empresa"]; 

	$tecnico = "id_Asesor"; 

	$valorTecnico = $id_asesor;

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
  
	    <h3 class="box-title">Ordenes Pendientes de Autorizacion</h3>

	    <div class="box-tools">
	    	
	    	<?php

	    		$itemUno = "correo";
    
			    $valorUno =  $_SESSION["email"];
				
				$asesorEnsession = Controladorasesores::ctrMostrarAsesoresEmpresas($itemUno,$valorUno);
			    
			  $estadoreporte = "Pendiente de autorización (AUT";

             echo '<a href="vistas/modulos/descargar-reporte-OrdenesPorEstado.php?reporte=ordenesOk&empresa='.$_SESSION["empresa"].'&estado='.$estadoreporte.'&tecnico='.$asesorEnsession["id"].'">
	             <button class="btn btn-success" style="margin-top:5px">Descargar reporte en Excel</button>

	          </a>';

	    	?>

	    </div>

	    

  	</div>
  	<!-- /.box-header -->

  	<!-- box-body -->
  	<div class="box-body no-padding">

	    <!-- users-list -->
	    <ul class="users-list clearfix">

	     <?php

		/*=====================================
		TRAER EL TOTAL DE ORDENES
		=======================================*/
		$TotaloredenesAUT = count($oredenesOk);
		
		/*=====================================
		IGUALAMOS EL TOTAL PARA QUE SE IMPRIMAR TODAS LAS ORDENES
		=======================================*/

	    if($TotaloredenesAUT > count($oredenesOk)){

	     	$totalOrdenes = $TotaloredenesAUT;
	     
	     }else{

	     	$totalOrdenes = count($oredenesOk);

	     }

	     for($i = 0; $i < $totalOrdenes; $i ++){

	     	if($oredenesOk[$i]["portada"] != ""){

		     	if($oredenesOk[$i]["modo"] != "directo"){

		     		$AlbumDeImagenes = json_decode($oredenesOk[$i]["multimedia"], true);
		     		foreach ($AlbumDeImagenes as $key => $valueImagen) {
		     			
		     		}
			     	echo '<li>
					      <a href="index.php?ruta=infoOrden&idOrden='.$oredenesOk[$i]["id"].'&empresa='.$oredenesOk[$i]["id_empresa"].'&asesor='.$oredenesOk[$i]["id_Asesor"].'&cliente='.$oredenesOk[$i]["id_usuario"].'&tecnico='.$oredenesOk[$i]["id_tecnico"].'&tecnicodos='.$oredenesOk[$i]["id_tecnicoDos"].'&pedido='.$oredenesOk[$i]["id_pedido"].'"  target="_blank">   

					      		<img src="'.$valueImagen["foto"].'" alt="User Image" style="width:100px">

					      </a>

					      <a href="index.php?ruta=infoOrden&idOrden='.$oredenesOk[$i]["id"].'&empresa='.$oredenesOk[$i]["id_empresa"].'&asesor='.$oredenesOk[$i]["id_Asesor"].'&cliente='.$oredenesOk[$i]["id_usuario"].'&tecnico='.$oredenesOk[$i]["id_tecnico"].'&tecnicodos='.$oredenesOk[$i]["id_tecnicoDos"].'&pedido='.$oredenesOk[$i]["id_pedido"].'"  target="_blank">

					      		<h4><b>'.$oredenesOk[$i]["id"].'</h4></b>

					      </a>

					      <span class="users-list-date">'.$oredenesOk[$i]["fecha"].'</span>

					    </li>';

				}else{


			     	echo '<li>
					      
					      	<a href="index.php?ruta=infoOrden&idOrden='.$oredenesOk[$i]["id"].'&empresa='.$oredenesOk[$i]["id_empresa"].'&asesor='.$oredenesOk[$i]["id_Asesor"].'&cliente='.$oredenesOk[$i]["id_usuario"].'&tecnico='.$oredenesOk[$i]["id_tecnico"].'&tecnicodos='.$oredenesOk[$i]["id_tecnicoDos"].'&pedido='.$oredenesOk[$i]["id_pedido"].'"  target="_blank">

					      		<img src="'.$valueImagen["foto"].'" alt="User Image" style="width:100px">
					      	</a>

					      <a href="index.php?ruta=infoOrden&idOrden='.$oredenesOk[$i]["id"].'&empresa='.$oredenesOk[$i]["id_empresa"].'&asesor='.$oredenesOk[$i]["id_Asesor"].'&cliente='.$oredenesOk[$i]["id_usuario"].'&tecnico='.$oredenesOk[$i]["id_tecnico"].'&tecnicodos='.$oredenesOk[$i]["id_tecnicoDos"].'&pedido='.$oredenesOk[$i]["id_pedido"].'"  target="_blank">

					      	<h4><b>'.$oredenesOk[$i]["id"].'</h4></b>

					      </a>

					      <span class="users-list-date">'.$oredenesOk[$i]["fecha"].'</span>
					    </li>';

				}

			}else{

				 echo ' <li>

                  <a href="index.php?ruta=infoOrden&idOrden='.$oredenesOk[$i]["id"].'&empresa='.$oredenesOk[$i]["id_empresa"].'&asesor='.$oredenesOk[$i]["id_Asesor"].'&cliente='.$oredenesOk[$i]["id_usuario"].'&tecnico='.$oredenesOk[$i]["id_tecnico"].'&tecnicodos='.$oredenesOk[$i]["id_tecnicoDos"].'&pedido='.$oredenesOk[$i]["id_pedido"].'"  target="_blank">


                  	<img src="'.$valueImagen["foto"].'" alt="User Image" style="width:100px;">
                  </a>

                 <a href="index.php?ruta=infoOrden&idOrden='.$oredenesOk[$i]["id"].'&empresa='.$oredenesOk[$i]["id_empresa"].'&asesor='.$oredenesOk[$i]["id_Asesor"].'&cliente='.$oredenesOk[$i]["id_usuario"].'&tecnico='.$oredenesOk[$i]["id_tecnico"].'&tecnicodos='.$oredenesOk[$i]["id_tecnicoDos"].'&pedido='.$oredenesOk[$i]["id_pedido"].'"  target="_blank">

                 	<h4><b>'.$oredenesOk[$i]["id"].'</h4></b>

                 </a>
                 
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