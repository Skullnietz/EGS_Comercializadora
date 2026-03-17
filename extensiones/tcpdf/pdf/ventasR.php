<?php
if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "vendedor"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}

?>

<div class="content-wrapper">
  
   <section class="content-header">
      
    <h1>
      Gestor Ventas Rápidas
    </h1>

    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>

      <li class="active">Gestor Ventas Rápidas</li>
      
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

          <a href="vistas/modulos/reporte.ventasR.php?reporte=compras">
            
              <button class="btn btn-success">Descargar Reporte En Excel</button>

          </a>


        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarVenta">
          
          Agregar Venta

        </button>
        
        <div class="box-header with-border">
        
              <a href="creararventa">

                <button class="btn btn-primary">
                  
                  Agregar venta Prueba

                </button>

              </a>

            </div>
    
        </div>
  

        <br>
        
        <table class="table table-bordered table-striped dt-responsive tablaVentasR" width="100%">
        
          <thead>
            
            <tr>
              
              <th style="width:10px">#</th>
              <th>Empresa</th>
              <th>No. Venta</th>
              <th>Correo Cliente</th>
              <th>Nombre Cliente</th>
              <th>Producto Uno</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Producto Dos</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Producto Tres</th>
              <th>Cantidad</th>  
              <th>Precio</th>         
              <th>Total</th>
              <th>Método</th>
              <th>Asesor</th>
              <th>Fecha</th>
              <th>Imprimir ticket</th>
              <th>Eliminar Venta</th>

            </tr>

          </thead> 

                  <?php
            
             // $administrador = ControladorAdministradores::ctrMostrarAdministradores($item, $valor);
               //foreach ($administrador as $key => $valueA) {
                 echo'

                 <input  type="hidden" id="tipoDePerfil" value="'.$_SESSION["perfil"].'"  placeholder="'.$_SESSION["perfil"].'">';
                //}
            ?>
        
        </table>


      </div>

    </div>

  </section>

</div>


<!--=====================================
MODAL AGREGAR PRODUCTO
======================================-->
<div id="modalAgregarVenta" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Venta</h4>

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

                 <select class="form-control input-lg" name="empresa" required>
                   
                   <option value="" id="empresa">
                     
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

            <!-- ENTRADA PARA EL NOMBRE DEL ASESOR-->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-user"></i></span> 

                <!--<input type="text" class="form-control input-lg" name="asesor" placeholder="Ingresar nombre del Asesor" required>-->

                <select class="form-control input-lg" name="asesor" required>
                  
                  <option value="" id="asesor">
                    
                    Seleccionar Asesor

                  </option>

                  <?php
                      
                      $item = null;
                      $valor = null;

                      $Asesores= Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);

                      foreach ($Asesores as $key => $value) {
                        
                        echo '

                        <option>'.$value["nombre"].'</option>';
                      }
                  ?>

                </select>

              </div>

            </div>
            <!--=====================================
            NOMBRE CLIENTE
            ======================================-->
            <div class="form-group">
              	
             	<div class="input-group">
              		
              		<span class="input-group-addon"><i class="fa fa-user"></i></span>
                   	<input type="text" class="form-control input-lg" name="nombreCliente" placeholder="Nombre del cliente">    

              	</div>

            </div>
            <!--=====================================
            CORREO CLIENTE
            ======================================-->
            <div class="form-group">
              	
             	<div class="input-group">
              		
              		<span class="input-group-addon"><i class="fa fa-at"></i></span>
                   	<input type="email" class="form-control input-lg" name="correo" placeholder="Correo del cliente">    

              	</div>

            </div>

            <!--=====================================
            ENTRADA PARA LOS PRODUCTOS
            ======================================-->   
            <div class="form-group">
                
              <div class="panel">AGREGAR NUEVO PRODUCTO</div>

                 <a href="#" onclick="AgregarCamposPedidos();">
                  
                  <div id="camposProductos">
                
                    <input type="button" class="btn btn-primary " value="Agregar producto"/></br></br>
                </a>
                
            </div>


            <div class="form-group row">

              <!--=====================================
              PRODUCTO UNO
              ======================================-->
                        
              <div class="col-xs-6">

                <div class="input-group">
                        
                  <span class="input-group-addon"><i class="fa fa-product-hunt"></i></span> 
                          
                  <input class="form-control input-lg" type="text"  name="productoUno" placeholder="Nombre Del Producto">

                </div>

              </div>

              <div class="col-xs-6">
                           
                <div class="input-group">
                             
                  <input class="form-control input-lg"  type="number" name="precioUno" id="precioUno" value="0"  min="0" step="any" placeholder="Precio">
                  <span class="input-group-addon"><i class="fa fa-dollar"></i></span>

                </div>

              </div>

            </div>

            <div class="form-group row">

              <!--=====================================
              PCANTIDAD PRODUCTO UNO
              ======================================-->
                  
              <div class="col-xs-6">

                  <div class="input-group"> 
                        
                    <input class="form-control input-lg" type="text" placeholder="Cantidad de productos" readonly>

                  </div>

              </div>

              <div class="col-xs-6">
                         
                <div class="input-group">
                           
                  <input class="form-control input-lg"  type="number" name="cantidadUno"  id="cantidadUno" value="0"  min="0" step="any" placeholder="Cantidad">
                  <span class="input-group-addon"><i class="fa fa-arrow-circle-up"></i></span>

                </div>

              </div>

            </div>

            <div class="form-group row">

              <!--=====================================
              PRODUCTO DOS
              ======================================-->
                  
              <div class="col-xs-6">

                <div class="input-group">
                        
                  <span class="input-group-addon"><i class="fa fa-product-hunt"></i></span> 
                          
                  <input class="form-control input-lg" type="text"  name="productoDos" placeholder="Nombre Del Producto">

                </div>

              </div>

              <div class="col-xs-6">
                           
                <div class="input-group">
                             
                  <input class="form-control input-lg"  type="number" name="precioDos" id="precioDos" value="0"  min="0" step="any" placeholder="Precio">
                  <span class="input-group-addon"><i class="fa fa-dollar"></i></span>

                </div>

              </div>

            </div>

            <div class="form-group row">

              <!--=====================================
              CANTIDAD PRODUCTO DOS
              ======================================-->
                        
              <div class="col-xs-6">

                <div class="input-group"> 
                          
                  <input class="form-control input-lg" type="text" placeholder="Cantidad de Productos" readonly>

                </div>

              </div>

              <div class="col-xs-6">
                           
                <div class="input-group">
                             
                  <input class="form-control input-lg"  type="number" name="cantidadDos" id="cantidadDos" value="0"  min="0" placeholder="Cantidad">
                  <span class="input-group-addon"><i class="fa fa-arrow-circle-up"></i></span>

                </div>

              </div>

            </div>

            <div class="form-group row">

              <!--=====================================
              PRODUCTO DOS
              ======================================-->
                        
              <div class="col-xs-6">

                <div class="input-group">
                        
                  <span class="input-group-addon"><i class="fa fa-product-hunt"></i></span> 
                          
                  <input class="form-control input-lg" type="text"  name="productoTres" placeholder="Nombre Del Producto">

                </div>

              </div>

              <div class="col-xs-6">
                           
                <div class="input-group">
                             
                  <input class="form-control input-lg"  type="number" name="precioTres" id="precioTres" value="0"  min="0" step="any" placeholder="Precio">
                  <span class="input-group-addon"><i class="fa fa-dollar"></i></span>

                </div>

              </div>

            </div>

            <div class="form-group row">

              <!--=====================================
              PCANTIDAD PRODUCTO TRES
              ======================================-->
                        
                <div class="col-xs-6">

                  <div class="input-group"> 
                          
                    <input class="form-control input-lg" type="text" placeholder="Cantidad de productos" readonly>

                  </div>

                </div>

                <div class="col-xs-6">
                           
                  <div class="input-group">
                             
                    <input class="form-control input-lg"  type="number" name="cantidadTres" id="cantidadTres" value="0"  min="0"  placeholder="Cantidad">
                    <span class="input-group-addon"><i class="fa fa-arrow-circle-up"></i></span>

                  </div>

                </div>

              </div>

              <div class="form-group row">

                <!--=====================================
                PRODUCTO CALCULAR TOTALES
                ======================================-->
                        
                <div class="col-xs-6">
                    
                    <span><h5><center>Pago del Cliente</center></h5></span>
                  
                  <div class="input-group">
                                  
                                  
                  <span class="input-group-addon"><i class="fa fa-money"></i></span>
                         
                    <input class="form-control input-lg"  type="number" name="pagoCliente" id="pagoCliente" value="0"  min="0" step="any" placeholder="pago Cliente">
                  </div>

                </div>

                <div class="col-xs-6">
                        <span><h5><center>TOTAL</center></h5></span>
                  <div class="input-group">

                    <span class="input-group-addon"><i class="fa fa-money"></i></span> 
                    <input type="number" class="form-control input-lg" name="pago" id="Resultado" min="0" value="0" step="any" readonly>
                    
                    <input type="hidden" class="form-control input-lg" name="cantidadProductos" id="cantidadProductos" min="0" value="0" readonly>

                  </div>

                </div>

              </div>
              <!--=====================================
              METODO DE PAGO
              ======================================-->
              <div class="form-group">
              	
              		
              		<div class="input-group">
              			
              			<span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                   		<input type="text" class="form-control input-lg" name="metodo" placeholder="Metodo de pago">    

              	</div>

              </div>

              <div class="form-group row">

                <!--=====================================
                CAMBIO A REGRESAR
                ======================================-->
                        
                <div class="col-xs-6">
                   </br></br>
                  <div class="input-group">
                     
                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                   <input type="button" class="btn btn-primary " value="Calcular Total De La Venta" id="calcular"/>
                          
                  </div>

                </div>

                <div class="col-xs-6">
                        <span><h5><center>Cambio a Regresar</center></h5></span>
                  <div class="input-group"> 

                    <span class="input-group-addon"><i class="fa fa-exchange"></i></span>

                    <input type="number" class="form-control input-lg" name="cambio" id="cambio" min="0" value="0"  step="any" readonly>

                  </div>

                </div>

              </div>




        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Guardar Venta</button>

        </div>

        <?php

          $crearVenta = new ControladorVentas();
          $crearVenta -> ctrCrearventa();

        ?>

      </form>

              <!--=====================================
              FINAL DEL CUERPO DEL MODAL
              ======================================-->

            </div>

        </div>

    </div>

  </div>

</div>

<?php

  $eliminarVenta = new ControladorVentas();
  $eliminarVenta -> ctrEliminarVenta();
