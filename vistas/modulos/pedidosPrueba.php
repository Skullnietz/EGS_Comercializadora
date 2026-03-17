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

      Gestor Pedidos

    </h1>



    <ol class="breadcrumb">



      <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>



      <li class="active">Gestor Pedidos</li>

      

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





          <?php

        if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor") {







             echo '<a href="vistas/modulos/descargar-reporte-pedidos.php?reporte=pedidos">';     



          



            echo'<button class="btn btn-success" style="margin-top:5px">Descargar reporte en Excel</button>



          </a>';

          

          //REPORTE DE PEDIDOS PENDIENTES

       echo '<a href="vistas/modulos/descargar-reporte-pedidos-pendientes.php?reporte=pedidosPendientes">';     





            echo'<button class="btn btn-success" style="margin-top:5px">Descargar reporte pedidos pendientes </button>



          </a>';

          

          //REPORTE DE PEDIDOS ADQUIRIDOS /1

           echo '<a href="vistas/modulos/descargar-reporte-pedidos-adquiridos.php?reporte=pedidosAdquiridos">';     



          

            echo'<button class="btn btn-success" style="margin-top:5px">Descargar reporte pedidos adquiridos</button>



          </a>';

          



          //REPORTE DE PEDIDOS ASESOR /2

           echo '<a href="vistas/modulos/descargar-reporte-pedidos-asesor.php?reporte=pedidosAsesor">';     



          

            echo'<button class="btn btn-success" style="margin-top:5px">Descargar reporte pedidos entregados al asesor</button>



          </a>';

          



          //REPORTE DE PEDIDOS PAGADOS /3

            echo '<a href="vistas/modulos/descargar-reporte-pedidos-pagados.php?reporte=pedidosPagados">';     





            echo'<button class="btn btn-success" style="margin-top:5px">Descargar reporte pedidos pagados</button>



          </a>';

          

          //REPORTE DE PEDIDOS CREDITO /4

            echo '<a href="vistas/modulos/descargar-reporte-pedidos-credito.php?reporte=pedidosCredito">';     



          

            echo'<button class="btn btn-success" style="margin-top:5px">Descargar reporte pedidos crédito</button>



          </a>';

          

        }  

      ?>







          <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarPedido">

          

            Agregar Pedido



          </button>



    

        </div>

  



        <br>

        

        <table class="table table-bordered table-striped dt-responsive tablaPedidos" width="100%">

        

          <thead>

            

            <tr>

              

              <th style="width:10px">#</th>

              <th>Empresa</th>

              <th>No. Pedido</th>

              <th>Cliente</th>

              <th>Estado</th>

              <th>Producto</th>

              <th>Precio</th>

              <th>Cantidad</th>

              <th>Total del pedido</th>

              <th>Método de pago</th>

              <th>Fecha de pedido</th>

              <th>Fecha de entrega</th>

              <th>Acciones</th>

       



            </tr>



          </thead> 



                  <?php

            

             // $administrador = ControladorAdministradores::ctrMostrarAdministradores($item, $valor);

               //foreach ($administrador as $key => $valueA) {

                 echo'



                 <input  type="hidden" id="tipoDePerfil" value="'.$_SESSION["perfil"].'"  placeholder="'.$_SESSION["perfil"].'">

                 <input  type="hidden" id="tipoidperfil" value="'.$_SESSION["id"].'"  placeholder="'.$_SESSION["id"].'">';

                //}

            ?>

        

        </table>





      </div>



    </div>



  </section>



</div>

<!--=====================================

MODAL EDITAR PEDIDO

======================================-->

<div id="modalEditarPedido" class="modal fade" role="dialog">

  

    <div class="modal-dialog">

      

      <div class="modal-content">

        

        <!--=====================================

        CABEZA DEL MODAL

        ======================================-->

        <div class="modal-header" style="background:#138a1e; color:white">

          

          <button type="button" class="close" data-dismiss>&times;</button>



          <h4 class="modal-title">Editar Pedido</h4>



        </div>

        <!--=====================================

        CUERPO DEL MODAL

        ======================================--> 

        <input type="hidden" class="idPedido">







        <!--=====================================

        ENTRADA PARA EL ASESOR

        ======================================-->

        <div class="form-group">

          

          <div class="input-group">

            

            <span class="input-group-addon"><i class="fas fa-user"></i></span>



            <input type="tex" class="form-control input-lg asesorDePedido" readonly>



          </div>



        </div>



        <!--=====================================

        ENTRADA PARA INFORMACION DEL PEDIDO UNO

        ======================================-->

        <div class="form-group row productoUnoEdicionMostrar" style="display: none;">

          

          <div class="col-xs-6">

            <span><h5><center>Producto</center></h5></span>

            <div class="input-group">

              

              <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span>

              <input type="text" class="form-control input-lg edicionProductoUnoPedido">



            </div>



          </div>

          <div class="col-xs-6">

            <span><h5><center>Precio</center></h5></span>

            <div class="input-group">



              <input type="number" class="form-control input-lg precioProductoPedidoEdicion" readonly>





              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

              

            </div>



          </div>



        </div>  





        <!--=====================================

        ENTRADA PARA CANTIDAD DE CANTIDA DE PRODUCTOS PEDIDOS UNO

        ======================================-->

        <div class="form-group cantidadProductosUnoPedidoEditados" style="display: none;">

          <span><h5><center>Cantidad</center></h5></span>

          <div class="input-group">

            

            <span class="input-group-addon"><i class="fas fa-arrow-circle-up"></i></span>

            <input type="number" class="form-control input-lg cantidadDeProductoPedidoEditado" readonly>  



          </div>



        </div>

        <!--=====================================

        ENTRADA PARA INFORMACION DEL PEDIDO DOS 

        ======================================-->

        <div class="form-group row productoDosEdicionMostrar" style="display: none;">

          

          <div class="col-xs-6">

            <span><h5><center>Producto</center></h5></span>

            <div class="input-group">

              

              <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span>

              <input type="text" class="form-control input-lg edicionProductoUnoPedidoDos">



            </div>



          </div>

          <div class="col-xs-6">

            <span><h5><center>Precio</center></h5></span>

            <div class="input-group">



              <input type="number" class="form-control input-lg precioProductoPedidoEdicionDos" readonly>





              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

              

            </div>



          </div>



        </div>  



        <!--=====================================

        ENTRADA PARA CANTIDAD DE CANTIDA DE PRODUCTOS PEDIDOS DOS

        ======================================-->

        <div class="form-group cantidadProductosDosPedidoEditados" style="display: none;">

          <span><h5><center>Cantidad</center></h5></span>

          <div class="input-group">

            

            <span class="input-group-addon"><i class="fas fa-arrow-circle-up"></i></span>

            <input type="number" class="form-control input-lg cantidadDeProductoPedidoEditadoDos" readonly>  



          </div>



        </div>

        <!--=====================================

        ENTRADA PARA INFORMACION DEL PEDIDO TRES

        ======================================-->

        <div class="form-group row productoTresEdicionMostrar" style="display: none;">

          

          <div class="col-xs-6">

            <span><h5><center>Producto</center></h5></span>

            <div class="input-group">

              

              <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span>

              <input type="text" class="form-control input-lg edicionProductoUnoPedidoTres">



            </div>



          </div>

          <div class="col-xs-6">

            <span><h5><center>Precio</center></h5></span>

            <div class="input-group">



              <input type="number" class="form-control input-lg precioProductoPedidoEdicionTres" readonly>





              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

              

            </div>



          </div>



        </div>  



        <!--=====================================

        ENTRADA PARA CANTIDAD DE CANTIDA DE PRODUCTOS PEDIDOS TRES

        ======================================-->

        <div class="form-group cantidadProductosTresPedidoEditados" style="display: none;">

          <span><h5><center>Cantidad</center></h5></span>

          <div class="input-group">

            

            <span class="input-group-addon"><i class="fas fa-arrow-circle-up"></i></span>

            <input type="number" class="form-control input-lg cantidadDeProductoPedidoEditadoTres" readonly>  



          </div>



        </div>

        <!--=====================================

        ENTRADA PARA INFORMACION DEL PEDIDO CUATRO

        ======================================-->

        <div class="form-group row productoCuatroEdicionMostrar" style="display: none;">

          

          <div class="col-xs-6">

            <span><h5><center>Producto</center></h5></span>

            <div class="input-group">

              

              <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span>

              <input type="text" class="form-control input-lg edicionProductoUnoPedidoCuatro">



            </div>



          </div>

          <div class="col-xs-6">

            <span><h5><center>Precio</center></h5></span>

            <div class="input-group">



              <input type="number" class="form-control input-lg precioProductoPedidoEdicionCuatro" readonly>





              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

              

            </div>



          </div>



        </div>  



        <!--=====================================

        ENTRADA PARA CANTIDAD DE CANTIDA DE PRODUCTOS PEDIDOS CUATRO

        ======================================-->

        <div class="form-group cantidadProductosCuatroPedidoEditados" style="display: none;">

          <span><h5><center>Cantidad</center></h5></span>

          <div class="input-group">

            

            <span class="input-group-addon"><i class="fas fa-arrow-circle-up"></i></span>

            <input type="number" class="form-control input-lg cantidadDeProductoPedidoEditadoCuatro" readonly>  



          </div>



        </div>

        <!--=====================================

        ENTRADA PARA INFORMACION DEL PEDIDO CINCO

        ======================================-->

        <div class="form-group row productoCincoEdicionMostrar" style="display: none;">

          

          <div class="col-xs-6">

            <span><h5><center>Producto</center></h5></span>

            <div class="input-group">

              

              <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span>

              <input type="text" class="form-control input-lg edicionProductoUnoPedidoCinco">



            </div>



          </div>

          <div class="col-xs-6">

            <span><h5><center>Precio</center></h5></span>

            <div class="input-group">



              <input type="number" class="form-control input-lg precioProductoPedidoEdicionCinco" readonly>





              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

              

            </div>



          </div>



        </div>  



        <!--=====================================

        ENTRADA PARA CANTIDAD DE CANTIDAD DE PRODUCTOS PEDIDOS CINCO

        ======================================-->

        <div class="form-group cantidadProductosCincoPedidoEditados" style="display: none;">

          <span><h5><center>Cantidad</center></h5></span>

          <div class="input-group">

            

            <span class="input-group-addon"><i class="fas fa-arrow-circle-up"></i></span>

            <input type="number" class="form-control input-lg cantidadDeProductoPedidoEditadoCinco" readonly>  



          </div>



        </div>

        <!--=====================================

        ENTRADA PARA PAGOS

        ======================================-->

        <div class="form-group row">

          

          <div class="col-xs-6">

            

            <span><h5><center>Pago del cliente</center></h5></span>



            <div class="input-group"> 

              

              <span class="input-group-addon"><i class="fas fa-money"></i></span>

              <input type="number" class="form-control input-lg pagoClientePedido" value="0" min="0" step="any" readonly>



            </div>



          </div>

          <div class="col-xs-6">

            

            <span><h5><center>TOTAL</center></h5></span>



            <div class="input-group">

              

                <span class="input-group-addon"><i class="fas fa-money"></i></span>

                <input type="number" class="form-control input-lg pagoPedidoEdidato" readonly>



            </div>



          </div>



        </div>







        <!--=====================================

        Adeudo

        ======================================-->

        <div class="form-group row">

          

          <div class="col-xs-6">

            

            <span><h5><center>Adeudo</center></h5></span>



            <div class="input-group">

              

              <span class="input-group-addon"><i class="fas fa-exchange"></i></span>

              

              <input type="number" class="form-control input-lg adeudoPedidoEditado" min="0" value="0" step="any" readonly>  



            </div>  



          </div>





        </div>

            <!--=====================================

            ENTRADA PARA LOS ESTATUS

            ======================================-->  



              <div class="form-group">

                

                <div class="input-group">



                  <span class="input-group-addon"><i class="fas fa-toggle-on"></i></i></span>

                  

                  <select class="form-control input-lg EstadoDelPedido">



                    <option class="optionEstadoPedido">

                      



                    </option>

                    

                    <option value="Pedido Pendiente">



                      Pedido Pendiente



                    </option>



                      <option value="Pedido Adquirido">



                        Pedido Adquirido        



                      </option> 



                      <option value="Producto en Almacen">



                        Producto en Almacén        



                      </option> 

  

                      <option value="Entregado al asesor">



                        Entregado al Asesor



                      </option>



                      <option value="Entregado/Pagado">



                        Entregado/Pagado



                      </option>



                      <option value="Entregado/Credito">



                        Entregado/Crédito



                      </option>





                  </select>



                </div>



              </div>



            <!--=====================================

            BOTON PARA PODER AGREGAR ABONOS DE MANERA AUTOMATICA

            ======================================-->  

            

             <div class="panel">AGREGAR NUEVO ABONO</div>



                 <a href="#" onclick="AgregarCampoDeaAbonoEditado();">

                  

                  <div id="camposAbono">

                

                    <input type="button" class="btn btn-primary " value="Agregar Abono"/></br></br>



                </a>



          </div>

        <!--=====================================

        CAMPOS DE ABONOS

        ======================================-->

        <div class="form-group row">

          

          <div class="col-xs-6">

            

            <span><h5><center>Abono</center></h5></span>



            <div class="input-group">

              

              <span class="input-group-addon"><i class="fas fa-money"></i></span>

              <input class="form-control input-lg abono1Lectura" type="text" readonly>



            </div>



          </div>

          <div class="col-xs-6">

            

            <span><h5><center>Fecha</center></h5></span>



            <div class="input-group">



              <span class="input-group-addon"><i class="fas fa-clock"></i></span>

              <input type="date" class="form-control input-lg fechaAbono1Lectura" min="0" value="0" step="any" readonly>



            </div>



          </div>



        </div>



        <!--=====================================

        Fecha de entrega

        ======================================-->

        <div class="form-group">



          <span><h5><center>Fecha de Entrega</center></h5></span>

          

          <div class="input-group">

            

            <span class="input-group-addon"><i class="fas fa-dollar"></i></span>



            <input type="date" class="form-control input-lg fechaEntregaPedidoEditado" readonly>



          </div>



        </div>



        <!--=====================================

        PIE DEL MODAL

        ======================================-->

        <div class="modal-footer">

          

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary guardarPedidoEditado">Guardar Pedido</button>



        </div>





      </div>



    </div>



</div>



<?php



  $eliminarPedido = new ControladorPedidos();

  $eliminarPedido -> ctrEliminarPedido();

?>





<!--=====================================

MODAL AGREGAR PEDIDO

======================================-->

<div id="modalAgregarPedido" class="modal fade" role="dialog">

  

  <div class="modal-dialog">



    <div class="modal-content">





      <!--<form role="form" method="post" enctype="multipart/form-data">-->



        <!--=====================================

        CABEZA DEL MODAL

        ======================================-->



        <div class="modal-header" style="background:#138a1e; color:white">



          <button type="button" class="close" data-dismiss="modal">&times;</button>



          <h4 class="modal-title">Agregar Pedido</h4>



        </div>



        <!--=====================================

        CUERPO DEL MODAL

        ======================================-->



        <div class="modal-body">



          <div class="box-body">



            <!-- ENTRADA PARA EL NOMBRE DE LA EMPRESA QUE VENDE-->



            <div class="form-group">

              

              <div class="input-group">

                

                 <span class="input-group-addon"><i class="fas fa-building"></i></span> 



                 <select class="form-control input-lg empresaPedido" required>

                   

                   <option value="" id="empresaPedido">

                     

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

                ENTRADA PARA EL ASESOR

                ======================================-->      

                <div class="form-group">

                  

                  <div class="input-group">

                    

                    <span class="input-group-addon"><i class="fas fa-user"></i></span>

                      

                    <select class="form-control input-lg AsesorPedido">

                      

                      <option>

                        

                        Seleccionar asesor



                      </option>

                      

                      <?php



                        $item= null;

                        $valor=null;



                        $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);

                            foreach ($asesor as $key => $value) {

                              

                              echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';



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

                    

                    <span class="input-group-addon"><i class="fas fa-user"></i></i></span>



                    <select class="form-control input-lg clientePeido">

                      

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

            ENTRADA PARA LOS ESTATUS

            ======================================-->  



              <div class="form-group">

                

                <div class="input-group">



                  <span class="input-group-addon"><i class="fas fa-toggle-on"></i></i></span>

                  

                  <select class="form-control input-lg IngresarEstadoDelPedido">

                    

                    <option value="Pedido Pendiente">

                      Pedido Pendiente

                    </option>



                      <option value="Pedido Adquirido">

                        Pedido Adquirido        

                      </option> 

                      <option value="Entregado al asesor">



                        Entregado al Asesor



                      </option>



                      <option value="Entregado/Pagado">

                        Entregado/Pagado

                      </option>



                      <option value="Entregado/Credito">

                        Entregado/Crédito

                      </option>



                  </select>



                </div>



              </div>







            <!--=====================================

            ENTRADA PARA LOS PRODUCTOS

            ======================================-->   

            <div class="form-group">

                

              <div class="panel">AGREGAR NUEVA ORDEN</div>



                 <a href="#" onclick="AgregarCamposPedidos();">

                  

                  <div id="camposProductos">

                

                    <input type="button" class="btn btn-primary " value="Agregar producto"/></br></br>

                </a>

                    <input type="hidden" class="totalPedidoUno">

                    <input type="hidden" class="totalPedidoDos">

                    <input type="hidden" class="totalPedidoTres">

                    <input type="hidden" class="totalPedidoCuatro">

                    <input type="hidden" class="totalPedidoCinco">

                



            </div>



              <!--=====================================

              METODO DE METODO DE PAGO

              ======================================-->

              <div class="form-group">

              	

              		

              		<div class="input-group">

              			

              			<span class="input-group-addon"><i class="fas fa-dollar"></i></span>

                   	

                    <input type="text" class="form-control input-lg metodo" placeholder="Metodo de pago">    



              	</div>



              </div>







              <div class="form-group row">



                <!--=====================================

                PRODUCTO CALCULAR TOTALES

                ======================================-->

                        

                <div class="col-xs-6">

                    

                    <span><h5><center>Pago del Cliente</center></h5></span>

                  

                  <div class="input-group">

                                  

                                  

                  <span class="input-group-addon"><i class="fas fa-money"></i></span>

                         

                    <input class="form-control input-lg pagoClientePedido"  type="number" id="pagoClientePedido" value="0"  min="0" step="any" placeholder="pago Cliente">

                  </div>



                </div>



                <div class="col-xs-6">

                        <span><h5><center>TOTAL</center></h5></span>

                  <div class="input-group">



                    <span class="input-group-addon"><i class="fas fa-money"></i></span> 

                    <input type="number" class="form-control input-lg pagoPedido"  id="ResultadoPedido" min="0" value="0" step="any" readonly>

                    

                    <input type="hidden" class="form-control input-lg" name="cantidadProductosPedido" id="cantidadProductosPedido" min="0" value="0" readonly>



                  </div>



                </div>



              </div>





              <div class="form-group row">



                <!--=====================================

                TOTAL ADEUDO

                ======================================-->

          



                <div class="col-xs-6">

                        <span><h5><center>Adeudo</center></h5></span>

                  <div class="input-group"> 



                    <span class="input-group-addon"><i class="fas fa-exchange"></i></span>



                    <input type="number" class="form-control input-lg adeudo"  id="adeudo" min="0" value="0"  step="any" readonly>



                  </div>



                </div>



              </div>



              <!--=====================================

              FECHA DE ENTREGA

              ======================================-->

              <div class="form-group">

              	

              		

              		<div class="input-group">

              			

              			<span class="input-group-addon"><i class="fas fa-dollar"></i></span>

                    <center>Fecha de Entrega</center></br>

                   		<input type="date" class="form-control input-lg fechaEntrega" placeholder="Fecha de entrega">    



              	</div>



              </div>

        <!--=====================================

        PIE DEL MODAL

        ======================================-->



        <div class="modal-footer">



          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>



          <button type="submit" class="btn btn-primary guardarPedido">Guardar Pedido</button>



        </div>





      <!--</form>-->



              <!--=====================================

              FINAL DEL CUERPO DEL MODAL

              ======================================-->



            </div>



        </div>



    </div>



  </div>



</div>



