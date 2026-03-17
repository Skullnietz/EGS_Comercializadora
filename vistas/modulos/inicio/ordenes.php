<?php

//if($_SESSION["perfil"] != "administrador"){

  //echo '<script>

  //window.location = "inicio";

  //</script>';

 // return;

//}

?>
<div class="content-wrapper">
	
	<section class="content-header">
		
		<h1>
			Gestor Órdenes De Servicio
		</h1>

		<ol class="breadcrumb">

	      <li><a href="index.php?ruta=inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>

	      <li class="active">Gestor Órdenes De Servicio</li>
      
    	</ol>

	</section>

	<section class="content">

	    <div class="box"> 

	      <div class="box-header with-border">
	        
	        <?php

	        //include "inicio/grafico-ventas.php";

	        ?>

	      </div>

	      <div class="box-body">

	        <div class="box-tools">

	
	        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarOrden">
	          
	          Agregar Orden

	        </button>

         <!-- <button class='btn btn-info' data-toggle='modal' data-target='#modalAsignarPedido'><i class='fa fa-sort'></i></button>-->

         </div>

          <label>Buscador</label>

            <input class="form-control col-md-3 light-table-filter" data-table="order-table" type="text" placeholder="Buscar..">
          
	        <br>
       
         <!-- <div class="form-1-2">

            <label for="buscadroOrdenesLbael">Buscar:</label>
            <input type="text"  class="caja_Busqueda">

          </div>-->
          	        
	        <table class="table table-bordered table-striped dt-responsive ordenes order-table" width="100%" style="table-layout: fixed;">
	        
	          <thead>
	            
	            <tr>
	              
	              <th style="width:10px">#</th>
                	<th>Empresa</th>
	              <th>No. Orden</th>
	              <th>Técnico</th>
	              <th>Asesor</th>
	              <th>Cliente</th>
	              <th>TOTAL</th>
	              <th>Estado</th>
	              <th>Fecha Entrada</th>
                <th>Ultima modificación</th>
                <th>Fecha de Salida</th>
                 <th>Editar</th>
                <?php
                  if ($_SESSION["perfil"] == "administrador") {
                    
                    echo'<th>Eliminar</th>';
                  
                  }

                ?>
                <th>Imprimir Ticket</th>
                
	            </tr>

	          </thead> 
            
            <tbody>

<?php
//INICIA LA PAGINACION//

//TRAEMOS LA BASE CORRESPONDIENTE A CADA PAGINA
$base = (1-1)*150;
$tope = 12;

//$ordenesConTope=controladorOrdenes::ctrlTraerOrdenesConTope($base,$tope);

//var_dump($ordenesConTope);

$listarOrdenes = controladorOrdenes::ctrListarOrdenes();

foreach ($listarOrdenes as $key => $valueTotalOrdenes){


  $pagOrdenes = ceil($valueTotalOrdenes[0]/150);
  
  if ($pagOrdenes >10){

    /*===============================
    PRIEMROS BOTONES Y LA UTLIMA PAGINA
    ===============================*/

    if ($_GET["ruta"]== "ordenes"){
      
      echo '<ul class="pagination">';

      for ($i=1; $i < $pagOrdenes ; $i++) { 
        
        echo '<li><a href="'.$_GET["ruta"].'/'.$i.'">'.$i.'</a></li>';
      }

      echo'<li class="disabled"><a>...</a></li>

      <li><a href="'.$_GET["ruta"].'/'.$pagOrdenes.'">'.$pagOrdenes.'</a></li>

      <li><a href="'.$_GET["ruta"].'/2"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>

      </ul>';
    }
     

  }else{

     echo '<ul class="pagination">';

    for ($i=1; $i < $pagOrdenes ; $i++) { 
        
        echo '<li><a href="#">'.$i.'</a></li>';
    }

     echo'</ul>';
  }


}

if ($_SESSION["perfil"] == "administrador") {
  

  $item=null;
    $valor=null; 

    $ordenes = controladorOrdenes::ctrMostrarOrdenes($item, $valor);

    foreach ($ordenes as $key => $valueOrdenes) {
      //TRAER EMPRESA

                  $item = "id";
                  $valor = $valueOrdenes["id_empresa"];

                  $respuesta = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

                  $NombreEmpresa = $respuesta["empresa"];

                  //TRAER TECNICO
                  $item = "id";
                  $valor = $valueOrdenes["id_tecnico"];

                  $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);

                  $NombreTecnico = $tecnico["nombre"];

                  //TRAER ASESOR
            
                  $item = "id";
                  $valor = $valueOrdenes["id_Asesor"];

                  $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);

                  $NombreAsesor = $asesor["nombre"];

                  //TRAER CLIENTE (USUARIO)

                  $item = "id";
                  $valor = $valueOrdenes["id_usuario"];

                  $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                  $NombreUsuario = $usuario["nombre"];


                  $InfoOrdenes = "<button class='btn btn-warning btnVerInfoOrden' idOrden='".$valueOrdenes["id"]."' cliente='".$valueOrdenes["id_usuario"]."'  tecnico='".$valueOrdenes["id_tecnico"]."' asesor='".$valueOrdenes["id_Asesor"]."' empresa='".$valueOrdenes["id_empresa"]."' pedido='".$valueOrdenes["id_pedido"]."' data-toggle='modal'><i class='fa fa-edit'></button>";

                  $eliminarOrden = "<button class='btn btn-danger btnEliminarorden' idOrden='".$valueOrdenes["id"]."'><i class='fa fa-times'></i></button>";

                  $ticket = "<button class='btn btn-warning btnImprimirorden' idOrden='".$valueOrdenes["id"]."' cliente='".$valueOrdenes["id_usuario"]."'  tecnico='".$valueOrdenes["id_tecnico"]."' asesor='".$valueOrdenes["id_Asesor"]."' empresa='".$valueOrdenes["id_empresa"]."' data-toggle='modal'><i class='fa fa-ticket'></i></button>";

                  $pedido = "<button class='btn btn-info' data-toggle='modal' data-target='#modalAsignarPedido'><i class='fa fa-sort'></i></button>";

                  date_default_timezone_set("America/Mexico_City");

                  $fechaDeIngreso = $valueOrdenes["fecha_ingreso"];

                  $fecha = date("Y-m-d H:i:s",strtotime($fechaDeIngreso."+ 5 days"));

                if ($valueOrdenes["fecha_ingreso"]  >= $fecha) {
                  
                  echo '<tr  style="background:#EB9A93;">
                      
                      <td>'.($key+1).'</td>
                      <td>'.$NombreEmpresa.'</td>
                      <td><strong><h4>ORDEN: '.$valueOrdenes["id"].'</h4></strong></td>
                      <td>'.$NombreTecnico.'</td>
                      <td>'.$NombreAsesor.'</td>
                      <td>'.$NombreUsuario.'</td>
                      <td>$ '.number_format($valueOrdenes["total"],2).'</td>
                      <td>'.$valueOrdenes["estado"].'</td>
                      <td>'.$valueOrdenes["fecha_ingreso"].'</td>
                      <td>'.$valueOrdenes["fecha"].'</td>
                      <td>'.$valueOrdenes["fecha_Salida"].'</td>
                      <td>'.$InfoOrdenes.'</td>';                   

                      if ($_SESSION["perfil"] == "administrador") {
                       
                        echo' <td>'.$eliminarOrden.'</td>';
                      }
                     
                      echo'<td>'.$ticket.'</td>

                  </tr>';
                }else{

                   echo '<tr>
                      
                      <td>'.($key+1).'</td>
                      <td>'.$NombreEmpresa.'</td>
                      <td><strong><h4>ORDEN: '.$valueOrdenes["id"].'</h4></strong></td>
                      <td>'.$NombreTecnico.'</td>
                      <td>'.$NombreAsesor.'</td>
                      <td>'.$NombreUsuario.'</td>
                      <td>$ '.number_format($valueOrdenes["total"],2).'</td>
                      <td>'.$valueOrdenes["estado"].'</td>
                      <td>'.$valueOrdenes["fecha_ingreso"].'</td>
                      <td>'.$valueOrdenes["fecha"].'</td>
                      <td>'.$valueOrdenes["fecha_Salida"].'</td>
                      <td>'.$InfoOrdenes.'</td>';                   

                      if ($_SESSION["perfil"] == "administrador") {
                       
                        echo' <td>'.$eliminarOrden.'</td>';
                      }
                     
                      echo'<td>'.$ticket.'</td>

                  </tr>';
                }


    }

}

if ($_SESSION["perfil"] == "vendedor") {
  

  //TRAER ORDENES CON ATRASO 
  //$item = "correo";
  //$valor =  $_SESSION["email"];

  //$Asesores = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);

  //$id_Asesor = $Asesores["id"];

  //echo'<pre>'.$id_Asesor.'</pre>';

  $ordenes = controladorOrdenes::ctrMostrarOrdenes($item, $valor);

    foreach ($ordenes as $key => $valueOrdenes) {
      //TRAER EMPRESA

                  $item = "id";
                  $valor = $valueOrdenes["id_empresa"];

                  $respuesta = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

                  $NombreEmpresa = $respuesta["empresa"];

                  //TRAER TECNICO
                  $item = "id";
                  $valor = $valueOrdenes["id_tecnico"];

                  $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);

                  $NombreTecnico = $tecnico["nombre"];

                  //TRAER ASESOR
            
                  $item = "id";
                  $valor = $valueOrdenes["id_Asesor"];

                  $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);

                  $NombreAsesor = $asesor["nombre"];

                  //TRAER CLIENTE (USUARIO)

                  $item = "id";
                  $valor = $valueOrdenes["id_usuario"];

                  $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                  $NombreUsuario = $usuario["nombre"];


                  $InfoOrdenes = "<button class='btn btn-warning btnVerInfoOrden' idOrden='".$valueOrdenes["id"]."' cliente='".$valueOrdenes["id_usuario"]."'  tecnico='".$valueOrdenes["id_tecnico"]."' asesor='".$valueOrdenes["id_Asesor"]."' empresa='".$valueOrdenes["id_empresa"]."' pedido='".$valueOrdenes["id_pedido"]."' data-toggle='modal'><i class='fa fa-edit'></button>";

                  $eliminarOrden = "<button class='btn btn-danger btnEliminarorden' idOrden='".$valueOrdenes["id"]."'><i class='fa fa-times'></i></button>";

                  $ticket = "<button class='btn btn-warning btnImprimirorden' idOrden='".$valueOrdenes["id"]."' cliente='".$valueOrdenes["id_usuario"]."'  tecnico='".$valueOrdenes["id_tecnico"]."' asesor='".$valueOrdenes["id_Asesor"]."' empresa='".$valueOrdenes["id_empresa"]."' data-toggle='modal'><i class='fa fa-ticket'></i></button>";

                  $pedido = "<button class='btn btn-info' data-toggle='modal' data-target='#modalAsignarPedido'><i class='fa fa-sort'></i></button>";

                  date_default_timezone_set("America/Mexico_City");

                  $fechaDeIngreso = $valueOrdenes["fecha_ingreso"];

                  $fecha = date("Y-m-d H:i:s",strtotime($fechaDeIngreso."+ 5 days"));

                if ($valueOrdenes["fecha_ingreso"]  >= $fecha) {
                  
                  echo '<tr  style="background:#EB9A93;">
                      
                      <td>'.($key+1).'</td>
                      <td>'.$NombreEmpresa.'</td>
                      <td><strong><h4>ORDEN: '.$valueOrdenes["id"].'</h4></strong></td>
                      <td>'.$NombreTecnico.'</td>
                      <td>'.$NombreAsesor.'</td>
                      <td>'.$NombreUsuario.'</td>
                      <td>$ '.number_format($valueOrdenes["total"],2).'</td>
                      <td>'.$valueOrdenes["estado"].'</td>
                      <td>'.$valueOrdenes["fecha_ingreso"].'</td>
                      <td>'.$valueOrdenes["fecha"].'</td>
                      <td>'.$valueOrdenes["fecha_Salida"].'</td>
                      <td>'.$InfoOrdenes.'</td>';                   

                      if ($_SESSION["perfil"] == "administrador") {
                       
                        echo' <td>'.$eliminarOrden.'</td>';
                      }
                     
                      echo'<td>'.$ticket.'</td>

                  </tr>';
                }else{

                   echo '<tr>
                      
                      <td>'.($key+1).'</td>
                      <td>'.$NombreEmpresa.'</td>
                      <td><strong><h4>ORDEN: '.$valueOrdenes["id"].'</h4></strong></td>
                      <td>'.$NombreTecnico.'</td>
                      <td>'.$NombreAsesor.'</td>
                      <td>'.$NombreUsuario.'</td>
                      <td>$ '.number_format($valueOrdenes["total"],2).'</td>
                      <td>'.$valueOrdenes["estado"].'</td>
                      <td>'.$valueOrdenes["fecha_ingreso"].'</td>
                      <td>'.$valueOrdenes["fecha"].'</td>
                      <td>'.$valueOrdenes["fecha_Salida"].'</td>
                      <td>'.$InfoOrdenes.'</td>';                   

                      if ($_SESSION["perfil"] == "administrador") {
                       
                        echo' <td>'.$eliminarOrden.'</td>';
                      }
                     
                      echo'<td>'.$ticket.'</td>

                  </tr>';
                }


    }

}

if ($_SESSION["perfil"] == "tecnico") {
  
    //TRAER ORDENES CON ATRASO 
      $itemUno = "correo";
      $valorUno =  $_SESSION["email"];

      $tecnicoEnSession = ControladorTecnicos::ctrMostrarTecnicos($itemUno,$valorUno);

      $id_tecnico = $tecnicoEnSession["id"];

        //echo'<pre>'.$id_tecnico.'</pre>';

      $ordenesDelTecnico = controladorOrdenes::ctrMostrarOrdenesDelTecncio($id_tecnico);

      foreach ($ordenesDelTecnico as $key => $valueOrdeneDelTecnico) {
      
         //TRAER EMPRESA

                  $item = "id";
                  $valor = $valueOrdeneDelTecnico["id_empresa"];

                  $respuesta = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

                  $NombreEmpresa = $respuesta["empresa"];

                  //TRAER TECNICO
                  $item = "id";
                  $valor = $valueOrdeneDelTecnico["id_tecnico"];

                  $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);

                  $NombreTecnico = $tecnico["nombre"];

                  //TRAER ASESOR
            
                  $item = "id";
                  $valor = $valueOrdeneDelTecnico["id_Asesor"];

                  $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);

                  $NombreAsesor = $asesor["nombre"];

                  //TRAER CLIENTE (USUARIO)

                  $item = "id";
                  $valor = $valueOrdeneDelTecnico["id_usuario"];

                  $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                  $NombreUsuario = $usuario["nombre"];


                  $InfoOrdenes = "<button class='btn btn-warning btnVerInfoOrden' idOrden='".$valueOrdeneDelTecnico["id"]."' cliente='".$valueOrdeneDelTecnico["id_usuario"]."'  tecnico='".$valueOrdeneDelTecnico["id_tecnico"]."' asesor='".$valueOrdeneDelTecnico["id_Asesor"]."' empresa='".$valueOrdeneDelTecnico["id_empresa"]."' pedido='".$valueOrdeneDelTecnico["id_pedido"]."' data-toggle='modal'><i class='fa fa-edit'></button>";

                  $eliminarOrden = "<button class='btn btn-danger btnEliminarorden' idOrden='".$valueOrdeneDelTecnico["id"]."'><i class='fa fa-times'></i></button>";

                  $ticket = "<button class='btn btn-warning btnImprimirorden' idOrden='".$valueOrdeneDelTecnico["id"]."' cliente='".$valueOrdeneDelTecnico["id_usuario"]."'  tecnico='".$valueOrdeneDelTecnico["id_tecnico"]."' asesor='".$valueOrdeneDelTecnico["id_Asesor"]."' empresa='".$valueOrdeneDelTecnico["id_empresa"]."' data-toggle='modal'><i class='fa fa-ticket'></i></button>";

                  $pedido = "<button class='btn btn-info' data-toggle='modal' data-target='#modalAsignarPedido'><i class='fa fa-sort'></i></button>";

                  date_default_timezone_set("America/Mexico_City");

                  $fechaDeIngreso = $valueOrdeneDelTecnico["fecha_ingreso"];

                  $fecha = date("Y-m-d H:i:s",strtotime($fechaDeIngreso."+ 5 days"));

                  if ($valueOrdeneDelTecnico["fecha_ingreso"]  >= $fecha) {


                  echo '<tr  style="background:#EB9A93;">
                      
                      <td>'.($key+1).'</td>
                      <td>'.$NombreEmpresa.'</td>
                      <td><strong><h4>ORDEN: '.$valueOrdeneDelTecnico["id"].'</h4></strong></td>
                      <td>'.$NombreTecnico.'</td>
                      <td>'.$NombreAsesor.'</td>
                      <td>'.$NombreUsuario.'</td>
                      <td>$ '.number_format($valueOrdeneDelTecnico["total"],2).'</td>
                      <td>'.$valueOrdeneDelTecnico["estado"].'</td>
                      <td>'.$valueOrdeneDelTecnico["fecha_ingreso"].'</td>
                      <td>'.$valueOrdeneDelTecnico["fecha"].'</td>
                      <td>'.$valueOrdeneDelTecnico["fecha_Salida"].'</td>
                      <td>'.$InfoOrdenes.'</td>';                   

                      if ($_SESSION["perfil"] == "administrador") {
                       
                        echo' <td>'.$eliminarOrden.'</td>';
                      }
                     
                      echo'<td>'.$ticket.'</td>

                    </tr>';

                  }else{


                  echo '<tr>
                      
                      <td>'.($key+1).'</td>
                      <td>'.$NombreEmpresa.'</td>
                      <td><strong><h4>ORDEN: '.$valueOrdeneDelTecnico["id"].'</h4></strong></td>
                      <td>'.$NombreTecnico.'</td>
                      <td>'.$NombreAsesor.'</td>
                      <td>'.$NombreUsuario.'</td>
                      <td>$ '.number_format($valueOrdeneDelTecnico["total"],2).'</td>
                      <td>'.$valueOrdeneDelTecnico["estado"].'</td>
                      <td>'.$valueOrdeneDelTecnico["fecha_ingreso"].'</td>
                      <td>'.$valueOrdeneDelTecnico["fecha"].'</td>
                      <td>'.$valueOrdeneDelTecnico["fecha_Salida"].'</td>
                      <td>'.$InfoOrdenes.'</td>';                   

                      if ($_SESSION["perfil"] == "administrador") {
                       
                        echo' <td>'.$eliminarOrden.'</td>';
                      }
                     
                      echo'<td>'.$ticket.'</td>

                  </tr>';

                  }

      }
}
              ?>


            </tbody>


	        </table>


	      </div>

	    </div>

	  </section>

</div>

<!--=====================================
MODAL AGREGAR OORDENES
======================================-->
<div id="modalAgregarOrden" class="modal fade" role="dialog">
  
   <div class="modal-dialog">
     
     <div class="modal-content">
       
       <!-- <form role="form" method="post" enctype="multipart/form-data"> -->
         
         <!--=====================================
        CABEZA DEL MODAL
        ======================================-->
        <div class="modal-header" style="background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Orden</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">
            
            <!-- ENTRADA PARA EL NOMBRE DE LA EMPRESA QUE VENDE-->

            <div class="form-group">
              
              <div class="input-group">
                
                 <span class="input-group-addon"><i class="fa fa-building"></i></span> 

                 <select class="form-control input-lg empresa" required>
                   
                   <option>
                     
                     Seleccionar Empresa

                   </option>

                    <?php
                      
                      $item = null;
                      $valor = null;

                      $respuesta = ControladorEmpresas::ctrMostrarEmpresasParaEditar($item, $valor);

                      foreach ($respuesta as $key => $value) {
                        
                        echo '

                        <option value="'.$value["id"].'">'.$value["empresa"].'</option>';

                      }
                  ?>
                        
                 </select>
                        
              </div>

            </div>
            <!--=====================================
            ENTRADA PARA EL TÍTULO
            ======================================-->

            <div class="form-group">
              
                <div class="input-group">
              
                  <span class="input-group-addon"><i class="fa fa-product-hunt"></i></span> 

                  <input type="text" class="form-control input-lg validarOrden tituloOrden"  placeholder="Ingresar título orden">

                </div>

            </div>

            <!--=====================================
            ENTRADA PARA LA RUTA DEL PRODUCTO
            ======================================-->

            <div class="form-group">
              
                <div class="input-group">
              
                  <span class="input-group-addon"><i class="fa fa-link"></i></span> 

                  <input type="text" class="form-control input-lg rutaOrden" placeholder="Ruta url de la orden" readonly>

                </div>

            </div>



            <!--=====================================
            ENTRADA PARA AGREGAR MULTIMEDIA
            ======================================-->

            <div class="form-group agregarMultimedia"> 



                <!--=====================================
                ENTRADA PARA EL TECNICO
                ======================================-->             
                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fa fa-cogs"></i></span>

                    <select class="form-control input-lg tecnico">
                      
                      <option value="">
                        
                  Seleccionar Tecnico 

                      </option>

                      <?php

                        //$tecnico = ControladorAdministradores::ctrMostrarTecnicosActivos();

                      
                                  $item = null;

                                  $valor = null;



                                  $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);


                        foreach ($tecnico as $key => $valueTecnicoActivo) {
                          
                          echo'<option value="'.$valueTecnicoActivo["id"].'">'.$valueTecnicoActivo["nombre"].'</option>';
                        }


                      ?>

                    </select>

                  </div>

                </div>

                  <!--=====================================
                ENTRADA PARA EL ASESOR
                ======================================-->      
                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                      
                    <select class="form-control input-lg asesor">
                      
                      <option>
                        
                        Seleccionar asesor

                      </option>
                      
                      <?php


                        //$asesorActivo = ControladorAdministradores::ctrMostrarAdministradoresActvisoEnVentas();

                                  $itemUno = null;

                                  $valorDos = null;

                                  $asesorParaSelect = Controladorasesores::ctrMostrarAsesoresEleg($itemUno,$valorDos);

                            foreach($asesorParaSelect as $key => $valueAsesoresActivos){
                            
                             echo '<option value="'.$valueAsesoresActivos["id"].'">'.$valueAsesoresActivos["nombre"].'</option>';

                            }                         

                      ?>

                    </select>

                  </div>

                </div>
                
                <!--=====================================
                ENTRADA PARA EL CLIENTE
                ======================================-->   
                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fa fa-user"></i></i></span>

                    <select class="form-control input-lg cliente" style="width: 100%; height: 100%">
                      
                      <option value="">
                        
                        Seleccionar Cliente

                      </option>

                       <?php

                            $item = null;
                            $valor = null;

                            $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                            foreach ($usuario as $key => $value) {
                              
                                echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';

                              }

                          ?>

                    </select>

                  </div>

                </div>
                <!--=====================================
                ENTRADA PARA EL STATUS
                ======================================-->
                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fa fa-toggle-on"></i></span> 

                    <select class="form-control input-lg status">

                      <option value="">Selecionar Estado</option>

                      <?php
                      
                        if ($_SESSION["perfil"] == "tecnico") {
                          
                            echo '<option value="En revisión (REV)">En revisión (REV)</option>

                                  <option value="Supervisión (SUP)">Supervisión (SUP)</option>

                                  <option value="Terminada (ter)">Terminada (ter)</option>
                                  <option value="Sin reparación (SR)">Sin reparación (SR)</option>"';
                        }

                        if ($_SESSION["perfil"] == "editor") {
                          
                            echo '<option value="En revisión (REV)">En revisión (REV)</option>
                                  <option value="Aceptado (ok)">Aceptado (ok)</option>
                                  <option value="Terminada (ter)">Terminada (ter)</option>';
                        }
                        
                        if ($_SESSION["perfil"] == "vendedor") {
                              echo '<option value="En revisión (REV)">En revisión (REV)</option>
                                    <option value="Terminada (ter)">Terminada (ter)</option>';
                        }
                        if ($_SESSION["perfil"] == "administrador") {
                          
                            echo '<option value="En revisión (REV)">En revisión (REV)</option>
                                  <option value="Supervisión (SUP)">Supervisión (SUP)</option>
                                  <option value="Pendiente de autorización (AUT)">Pendiente de autorización (AUT)</option>
                                  <option value="Aceptado (ok)">Aceptado (ok)</option>
                                  <option value="Terminada (ter)">Terminada (ter)</option>
                                  <option value="Cancelada (can)">Cancelada (can)</option>
                                  <option value="Entregado (Ent)">Entregado (Ent)</option>
                                  <option value="Sin reparación (SR)">Sin reparación (SR)</option>';
                        }

                      ?>
                    
                      
                       
                       

                       

                       
                       
                       

                    </select>

                  </div>

                </div>
              <!--=====================================
              SUBIR MULTIMEDIA DE PRODUCTO FÍSICO
              ======================================-->
              
			     <div class="multimediaOrden needsclick dz-clickable">

                <div class="dz-message needsclick cuadro multimedia">
                  
                  Arrastrar o dar click para subir imagenes.

                </div>

              </div>

            </div>
           
         

           <!--=====================================
            AGREGAR DESCRIPCIÓN
            ======================================-->

            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                <textarea type="text" rows="3" class="form-control input-lg descripcionOrden" id="textareaDetallesInternos" placeholder="Ingresar detalles internos"></textarea>

              </div>

            </div>

                <div class="form-group row">

                

            <!--=====================================
            ENTRADA PARA AGREGAR NUEVA PARTIDA
            ======================================-->

            <div class="form-group">
              
              <div class="panel">SUBIR FOTO PORTADA</div>

              <input type="file" class="fotoPortada">

              <p class="help-block">Tamaño recomendado 1280px * 720px <br> Peso máximo de la foto 2MB</p>

              <img loading="lazy" src="vistas/img/cabeceras/default/default.jpg" class="img-thumbnail previsualizarPortada" width="100%">

            </div>

            <!--=====================================
            AGREGAR FOTO DE MULTIMEDIA
            ======================================-->

            <div class="form-group">
                
              <div class="panel">SUBIR FOTO PRINCIPAL DEL PRODUCTO</div>

              <input type="file" class="fotoPrincipal">

              <p class="help-block">Tamaño recomendado 400px * 450px <br> Peso máximo de la foto 2MB</p>

              <img loading="lazy" src="vistas/img/productos/default/default.jpg" class="img-thumbnail previsualizarPrincipal" width="200px">

            </div>
              
        
            <!--=====================================
            PARTIDA UNO
            ======================================
            <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida1 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio1 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fa fa-dollar-sign"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA DOS
            ======================================
            <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida2 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio2 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fa fa-dollar-sign"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA TRES
            ======================================

          <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida3 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio3 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fa fa-dollar-sign"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA CUATRO
            ======================================

          <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida4 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio4 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fa fa-dollar-sign"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA CINCO
            ======================================

                      <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida5 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio5 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fa fa-dollar-sign"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA SEIS
            ======================================
                      <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida6 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio6 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fa fa-dollar-sign"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA SIETE
            ======================================
                      <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida7 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio7 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fa fa-dollar-sign"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA OCHO
            ======================================
                                  <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida8 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio8 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fa fa-dollar-sign"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA NUEVE
            ======================================
            <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida9 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio9 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fa fa-dollar-sign"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA DIEZ
            ======================================
            <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida10 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio10 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fa fa-dollar-sign"></i></span>

            </div>
          </div>
        </div>-->
        </div>


                    </div>

                     <div class="form-group">
                
              <div class="panel">AGREGAR NUEVA ORDEN</div>

                 <a href="#" onclick="AgregarCampos();">
                          <div id="campos">
                         <input type="button" class="btn btn-primary " value="Agregar Partida"/></br></br>
                          </a>
            </div>
              

                    <!--=====================================
                PRODUCTO CALCULAR TOTALES
                ======================================-->
                 <div class="form-group row">

                <!--=====================================
                CAMBIO A REGRESAR
                ======================================-->
                        
                <div class="col-xs-6">

                 

                </div>

                <div class="col-xs-6">
                        <span><h5><center>TOTAL</center></h5></span>
                  <div class="input-group"> 

                    <span class="input-group-addon"><i class="fa fa-dollar-sign"></i></span>

                    <input type="number" class="form-control input-lg totalOrden"  min="0" value="0"  step="any" readonly>

                  </div>

                </div>

              </div>
              </div>           

          </div>


        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <div class="preload"></div>
  
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="button" class="btn btn-primary guardarOrden">Guardar Orden</button>

        </div>

       </form> 

     </div>

   </div>

</div>
<!--=====================================
MODAL AGREGAR OBSERVACION
======================================

<div id="modalAgregarDetalle" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">-->

        <!--=====================================
        CABEZA DEL MODAL
        ======================================

        <div class="modal-header" style="background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Observacion</h4>

        </div>-->

        <!--=====================================
        CUERPO DEL MODAL
        ======================================

        <div class="modal-body">

          	<div class="box-body">

          		<div class="form-group">
                
              		<div class="panel">AGREGAR NUEVA OBSERVACION</div>

                 		<a href="#" onclick="AgregarObservacion();">
                          <div id="camposObservacion">
                         	
                         	<input type="button" class="btn btn-primary " value="Agregar observación"/></br></br>
                          <?php

                         // echo'<input type="hidden" class="creador" value="'.$_SESSION["id"].'">
                            //  <input type="hidden" class="idOrdenObservacion">';

                          ?>
                          </a>e
                    </div>
            </div>-->

             <!--=====================================
            OBSERVACION UNO
            ======================================
             <div class="form-group row">
              
               <div class="col-xs-6">

                <div class="input-group">
                  <input class="form-control input-lg fecha" type="text"  readonly>
                  </div>
              </div>

              <div class="col-xs-6">
              
                <div class="input-group">

                <textarea type="text"  rows="3" class="form-control input-lg observacionUno" placeholder="Ingresar detalles internos" readonly></textarea>

                <span class="input-group-addon"><i class="fa fa-edit"></i></span> 

              </div>
            </div>

          </div>
  
          </div>

        </div>-->

        <!--=====================================
        PIE DEL MODAL
        ======================================
  
        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary agregarObservacion">Agregar observación</button>

        </div>


    </div>

  </div>

</div>-->

<!--=====================================
MODAL EDITAR ORDEN
======================================-->
<div id="modalEditarOrden" class="modal fade" role="dialog">
  
  <div class="modal-dialog">
    
    <div class="modal-content">
      
        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->
        <div class="modal-header" style="background: #138a1e; color:white;">
          
          <button type="button" class="close" data-dissmiss="modal">&times;</button>

          <center><h2><b>ORDEN:</b></h2><h2 class="modal-title NumeroDeOrden"></h2></center>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->
        <div class="modal-body">
          
          <div class="box-body">
            <!--=====================================
            ENTRADA PARA EL TÍTULO
            ======================================-->
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-product-hunt"></i></span> 

                <input type="text" class="form-control input-lg validarOrden tituloOrden" readonly>

                <input type="hidden" class="idOrden">

              </div>

            </div>

            <!--=====================================
            ENTRADA PARA LA RUTA DE LA ORDEN
            ======================================-->
            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fa fa-link"></i></span>

                <input type="text" class="form-control input-lg rutaOrden" readonly>

              </div>

            </div>
            <!--=====================================
            ENTRADA PARA EL TECNICO
            ======================================-->
            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fa fa-cogs"></i></span>

                <select class="form-control input-lg seleccionarTecnico">
                  
                  <option class="optionEditarTecnico"></option>

                  <?php

                     $item = null;
                      $valor = null;

                      $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);

                      foreach ($tecnico as $key => $value) {
                        
                        echo '

                        <option value="'.$value["id"].'">'.$value["nombre"].'</option>';

                      }

                  ?>

                </select>

              </div>

            </div>
            <!--=====================================
            ENTRADA PARA EL ASESOR
            ======================================-->
            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fa fa-user"></i></span>

                <select class="form-control input-lg seleccionarAsesor">
                  
                  <option class="optionEditarAsesor"></option>

                  <?php

                     $item = null;
                      $valor = null;

                      $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);

                      foreach ($asesor as $key => $value) {
                        
                        echo '

                        <option value="'.$value["id"].'">'.$value["nombre"].'</option>';

                      }

                  ?>

                </select>

              </div>

            </div>
            <!--=====================================
            ENTRADA PARA EL CLIENTE
            ======================================-->
            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fa fa-user"></i></span>

                <select class="form-control input-lg seleccionarCliente" readonly>
                  
                  <option class="optionEditarCliente" ></option readonly>

                  <?php

                     $item = null;
                      $valor = null;

                      $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                      foreach ($usuario as $key => $value) {
                        
                        echo '

                        <option value="'.$value["id"].'">'.$value["nombre"].'</option>';

                      }

                  ?>

                </select>

              </div>

            </div>

            <?php

            if ($_SESSION["perfil"] != "tecnico") {
             
              echo'<div class="form-group">

                    <div class="input-group">

                      <span class="input-group-addon"><i class="fa fa-envelope"></i></span>

                      <input type="email" class="form-control input-lg correoCliente" readonly>

                    </div>


              </div>
              <div class="form-group">

                    <div class="input-group">

                      <span class="input-group-addon"><i class="fa fa-headphones"></i></span>

                      <input type="tel" class="form-control input-lg numeroCliente" readonly>

                    </div>


              </div>
               <div class="form-group">

                    <div class="input-group">

                      <span class="input-group-addon"><i class="fa fa-headphones"></i></span>

                      <input type="tel" class="form-control input-lg numeroClienteDos" readonly>

                    </div>


              </div>';

            }

            ?>
            <!--=====================================
            ENTRADA PARA EL ESTADO
            ======================================-->
            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fa fa-toggle-on"></i></span>

                <select class="form-control input-lg seleccionarEstatus">
                  
                  <option class="optionEditarEstatus"></option>

                  <?php
                      
                        if ($_SESSION["perfil"] == "tecnico") {
                          
                            echo '<option class="pen" value="En revisión (REV)" style="display:none">En revisión (REV)</option>

                                  <option class="sup" value="Supervisión (SUP)" style="display:none">Supervisión (SUP)</option>

                                  <option class="ter" value="Terminada (ter)" style="display:none">Terminada (ter)</option>';
                        }

                        if ($_SESSION["perfil"] == "editor") {
                          
                            echo '<option class="pen" value="En revisión (REV)" style="display:none">En revisión (REV)</option>
                                 <option class="aut" value="Pendiente de autorización (AUT)" style="display:none">Pendiente de autorización (AUT)</option>
                                  <option class="ok" value="Aceptado (ok)" style="display:none">Aceptado (ok)</option>
                                  <option class="ter" value="Terminada (ter)" style="display:none">Terminada (ter)</option>
                                  <option class="ent" value="Entregado (Ent)" style="display:none">Entregado (Ent)</option>';
                        }
                         
                        if ($_SESSION["perfil"] == "vendedor") {
                              echo '<option value="En revisión (REV)">En revisión (REV)</option>
                                  <option value="Aceptado (ok)">Aceptado (ok)</option>
                                  <option value="Terminada (ter)">Terminada (ter)</option>
                                  <option value="Entregado (Ent)">Entregado (Ent)</option>';
                        }
                        if ($_SESSION["perfil"] == "administrador") {
                          
                            echo '<option class="pen" value="En revisión (REV)">En revisión (REV)</option>
                                  <option class="sup" value="Supervisión (SUP)">Supervisión (SUP)</option>
                                  <option class="aut" value="Pendiente de autorización (AUT)">Pendiente de autorización (AUT)</option>
                                  <option class="ok" value="Aceptado (ok)">Aceptado (ok)</option>
                                  <option class="ter" value="Terminada (ter)">Terminada (ter)</option>
                                  <option class="can" value="Cancelada (can)">Cancelada (can)</option>
                                  <option class="ent" value="Entregado (Ent)">Entregado (Ent)</option>
                                  ';
                        }

                      ?>

                </select>

              </div>

            </div>
            <!--=====================================
            AGREGAR MULTIMEDIA
            ======================================-->
            <div class="form-group agregarMultimedia">
              
              <div class="row previsualizarImgFisico"></div>

              <div class="multimediaFisica needsclick dz-clickable">
                
                <div class="dz-message needsclick">
                  
                  Arrastrar o dar click para subir imagenes.

                </div>

              </div>

            </div>

            <!--=====================================
            DETALLES INTERNOS
            ======================================-->
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-edit"></i></span> 

                <textarea type="text"  rows="3" class="form-control input-lg descripcionOrden" placeholder="Ingresar detalles internos"></textarea>

            </div>

            <!--=====================================
            AGREGAR FOTO DE PORTADA
            ======================================-->
            <div class="form-group">
              
              <div class="panel">SUBIR FOTO PORTADA</div>

              <input type="file" class="fotoPortada">
              <input type="hidden" class="antiguaFotoPortada">

              <p class="help-block">Tamaño recomendado 1280px * 720px <br> Peso máximo de la foto 2MB</p>

              <img loading="lazy" src="vistas/img/cabeceras/default/default.jpg" class="img-thumbnail previsualizarPortada" width="100%">

            </div>

            <!--=====================================
            AGREGAR FOTO DE MULTIMEDIA
            ======================================-->

            <div class="form-group">
                
              <div class="panel">SUBIR FOTO PRINCIPAL DEL PRODUCTO</div>

              <input type="file" class="fotoPrincipal">
              <input type="hidden" class="antiguaFotoPrincipal">

              <p class="help-block">Tamaño recomendado 400px * 450px <br> Peso máximo de la foto 2MB</p>

              <img loading="lazy" src="vistas/img/productos/default/default.jpg" class="img-thumbnail previsualizarPrincipal" width="200px">

            </div>
            <!--=====================================
            PARTIDA UNO
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida1 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)" readonly></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio1 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio" readonly>

                    <span class="input-group-addon" ><i class="fa fa-dollar-sign"></i></span>

                  </div>

                </div>

              </div>

            </div>

            <!--=====================================
            PARTIDA DOS
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida2 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)" ></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio2 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio" >

                    <span class="input-group-addon" ><i class="fa fa-dollar-sign"></i></span>

                  </div>

                </div>

              </div>

            </div>
            <!--=====================================
            PARTIDA TRES
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida3 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)" ></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio3 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio" >

                    <span class="input-group-addon" ><i class="fa fa-dollar-sign"></i></span>

                  </div>

                </div>

              </div>

            </div>
            <!--=====================================
            PARTIDA CUATRO
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida4 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio4 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">

                    <span class="input-group-addon" ><i class="fa fa-dollar-sign"></i></span>

                  </div>

                </div>

              </div>

            </div>
            <!--=====================================
            PARTIDA CINCO
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida5 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio5 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">

                    <span class="input-group-addon" ><i class="fa fa-dollar-sign"></i></span>

                  </div>

                </div>

              </div>

            </div>

            <!--=====================================
            PARTIDA SEIS
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida6 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio6 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">

                    <span class="input-group-addon" ><i class="fa fa-dollar-sign"></i></span>

                  </div>

                </div>

              </div>

            </div>

            <!--=====================================
            PARTIDA SIETE
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida7 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio7 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">

                    <span class="input-group-addon" ><i class="fa fa-dollar-sign"></i></span>

                  </div>

                </div>

              </div>

            </div>
            <!--=====================================
            PARTIDA OCHO
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida8 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio8 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">

                    <span class="input-group-addon" ><i class="fa fa-dollar-sign"></i></span>

                  </div>

                </div>

              </div>

            </div>
            <!--=====================================
            PARTIDA NUEVE
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida9 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio9 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">

                    <span class="input-group-addon" ><i class="fa fa-dollar-sign"></i></span>

                  </div>

                </div>

              </div>

            </div>

            <!--=====================================
            PARTIDA DIEZ
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida10 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio10 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">

                    <span class="input-group-addon" ><i class="fa fa-dollar-sign"></i></span>

                  </div>

                </div>

              </div>

            </div>
            <!--=====================================
            TOTAL ORDEN
            ======================================-->
            <div class="form-group row">
            <!--=====================================
            CAMBIO A REGRESAR
            ======================================-->
            <div class="col-xs-6">
                

            </div>

            <div class="col-xs-6">
              <span><h5><center>TOTAL</center></h5></span>
              <div class="input-group"> 

                <span class="input-group-addon"><i class="fa fa-dollar-sign"></i></span>

                <input type="number" class="form-control input-lg totalOrdenEditar"  min="0" value="0"  step="any" readonly>

               </div>                 
                
              </div>

            </div>

              <!--=====================================
                SELECCIONAR PEDIDO
                ======================================-->
                <div class="form-group">
                  
                  <select class="form-control input-lg selActivarPedido">
                    
                    <option value="">No tiene pedido</option>

                    <option value="pedido">Activar Pedido</option>
                    
                  </select>                  

                </div> 
              <!--=====================================
              DATOS DEL PEDIDO
              ======================================-->
              <div class="datosPedido" style="display:none">
                
                  <select class="form-control input-lg seleccionarPedido">
                  
                    <option class="optionEditarPedidos"></option>

                    <?php

                       $item = null;
                        $valor = null;

                        $pedido = ControladorPedidos::ctrMostrarorpedidosParaValidar($item,$valor);

                        foreach ($pedido as $key => $value) {
                          
                          echo '

                          <option value="'.$value["id"].'">'.$value["id"].'</option>';

                        }

                    ?>

                  </select>


              </div>                        

          </div>

        </div>


        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <div class="preload"></div>
  
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="button" class="btn btn-primary guardarCambiosOrden">Guardar cambios</button>

        </div>

    </div>

  </div>

</div>
</div>
<?php

  $eliminarOrden = new controladorOrdenes();
  $eliminarOrden -> ctrEliminarOrden();
?>
