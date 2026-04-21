<style>
    .marginr{
        margin-right:10px;
        
    }
    .marginl{
        margin-left:20px;
    }
    .fa-minus-circle{
        color: red;
    }
    .fa-plus-circle{
        color: green;
    }
</style>
<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Crear venta
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="#"><i class="fas fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Crear venta</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="row">

      <!--=====================================
      EL FORMULARIO
      ======================================-->
      
      <div class="col-lg-5 col-xs-12">
        
        <div class="box box-success">
          
          <div class="box-header with-border"></div>
          <form role="form" method="post" class="formularioVenta" enctype="multipart/form-data">

            <div class="box-body">
  
              <div class="box">
                <!--=====================================
                ENTRADA PARA LA EMPRESA
                ======================================-->        
             <!-- ENTRADA PARA EL NOMBRE DE LA EMPRESA QUE VENDE-->

            <div class="form-group">
              
              <div class="input-group">
                
                 <span style="border-radius: 0px 0px 0px 5px ;" class="input-group-addon"><i class="fas fa-building"></i></span> 

                 <select style="border-radius: 0px 0px 5px 0px ;" class="form-control" id="empresa" name="empresa" required>
                  
                  <option value="">Seleccionar Empresa</option>


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
                ENTRADA PARA LA EMPRESA
                ======================================-->
              <!-- ENTRADA PARA EL NOMBRE DEL ASESOR-->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon" style="border-radius: 5px 0 0 5px ;"><i class="fas fa-user"></i></span> 

                <!--<input type="text" class="form-control input-lg" name="asesor" placeholder="Ingresar nombre del Asesor" required>-->

                <select class="form-control input" style="border-radius: 0px 5px 5px 0px;" name="asesor" required>
                  
                  <option>Seleccionar Asesor</option>

                  <?php
                      
                      $item = null;
                      $valor = null;

                      $Asesores= Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);

                      foreach ($Asesores as $key => $value) {
                        
                        echo '

                        <option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                      }
                  ?>

                </select>

              </div>

            </div>
                
                <!--=====================================
                ENTRADA DEL CLIENTE
                ======================================--> 

                <div class="form-group">
                  
                  <div class="input-group" >
                    
                    <span class="input-group-addon" style="border-radius: 5px 0 0 5px ;"><i class="fas fa-users"></i></span>
                    
                   <select class="form-control" style=" margin-top: 1.5px;" id="seleccionarCliente" name="seleccionarCliente" required>
                  
                  <option  value="">Seleccionar cliente</option>

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

                    
                    <span style="border-radius: 0px 5px 5px 0px;" class="input-group-addon"><button type="button"  class="btn btn-default btn-xs" data-toggle="modal" data-target="#modalAgregarUsuario" data-dismiss="modal">Agregar cliente</button></span>

                  </div>

                </div>

                <!--=====================================
                MONEDERO ELECTRÓNICO DEL CLIENTE (Canje en venta rápida)
                ======================================-->
                <div id="egsMonederoVentaRapida" class="form-group" style="display:none;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px;margin-bottom:10px;">
                  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <span style="font-weight:600;color:#b45309;font-size:13px;"><i class="fa-solid fa-wallet"></i> Dinero electrónico</span>
                    <span style="font-size:12px;color:#64748b;">Saldo disponible: <b id="egsSaldoMonederoLabel" style="color:#16a34a;">$0.00</b></span>
                  </div>
                  <div class="input-group input-group-sm">
                    <span class="input-group-addon"><i class="fa-solid fa-coins"></i></span>
                    <input type="number" id="egsMontoCanjeVenta" name="montoCanjeElectronicoVenta" class="form-control" min="0" step="0.01" value="0" placeholder="Monto a aplicar">
                    <span class="input-group-btn">
                      <button type="button" id="egsAplicarMaxMonedero" class="btn btn-warning btn-sm">Usar todo</button>
                    </span>
                  </div>
                  <small id="egsMonederoMsg" style="color:#64748b;display:block;margin-top:4px;"></small>
                </div>

                <!-- Hidden inputs para compatibilidad con backend (id_cliente + nombreCliente + correo) -->
                <input type="hidden" id="id_cliente" name="id_cliente" value="0">
                <input type="hidden" id="nombreCliente" name="nombreCliente" value="">
                <input type="hidden" id="correoClienteVenta" name="correo" value="">

                <!--=====================================
                ENTRADA PARA AGREGAR PRODUCTO
                ======================================--> 

                <div class="form-group row nuevoProducto">

               
                </div>


                <div class="form-fomr row nuevoCampoCajas">
                  
                </div>

                <!--=====================================
                RECIBIR DATOS EN JSON DESDE JS 
                ======================================-->
                <input type="hidden" id="listaProductos" name="listaProductos">  
              


                <!--=====================================
                BOTÓN PARA AGREGAR PRODUCTO
                ======================================-->

                <button type="button" class="btn btn-default hidden-lg btnAgregarProductoMovil">Agregar producto</button>

                <hr>

                <div class="row">

                  <!--=====================================
                  ENTRADA IMPUESTOS Y TOTAL
                  ======================================-->
                  
                  <div class="form-group row">

                  <!--  <div class="col-xs-5 marginl">
                    
                     <span><b><center style="border:#d2d6de 1px solid; border-radius: 5px 5px 0 0;">Aumentar (+%)</center></b></span>

                              <div class="input-group">

                                <span class="input-group-addon" style="border-radius: 0 0 0px 5px;"><i class="fas fa-plus-circle fa-xs"></i><i class="fas fa-percent"></i></span>
                             
                               <input type="number" style="border-radius: 0 0 5px 0;" class="form-control input-lg" min="0" id="nuevoImpuestoVenta" name="nuevoImpuestoVenta" placeholder="0">

                                <input type="hidden" name="nuevoPrecioImpuesto" id="nuevoPrecioImpuesto" required>

                                <input type="hidden" name="precioNeto" id="precioNeto" required>
                        
                              </div>-->
                    </div>  
                    
                  <div class="col-xs-5" style="margin-left: 30px;">

                            <span><b><center style="border:#d2d6de 1px solid; border-radius: 5px 5px 0 0px;">Aumentar (+)</center></b></span>      

                            <div class="input-group" >
                                <span class="input-group-addon" style="border-radius: 0 0 0px 5px;"><i class="fas fa-plus-circle fa-xs"></i><i class="fas fa-dollar-sign"></i></span>

                              <input type="number" style="border-radius: 0 0 5px 0px;" class="form-control input-lg" min="0" id="Inversion" name="nversion" placeholder="0">

                              
                        
                            </div>
                    </div>

                  </div>
                  <div class="form-group row">
    
                  <div class="col-xs-5" style="margin-left: 30px; margin-top: 15px">
                    
                    <span><b><center style="border:#d2d6de 1px solid; border-radius: 5px 5px 0 0;" >Descuento (-%)</center></b></span>

                    <div class="input-group">
                      
                      <span class="input-group-addon" style="border-radius: 0 0 0px 5px;"><i class="fas fa-minus-circle fa-xs"></i><i class="fas fa-percent"></i></span>

                      <input type="number" style="border-radius: 0 0 5px 0;" class="form-control input-lg" min="0" id="nuevodescuentoPorcentaje" name="nuevodescuentoPorcentaje" placeholder="0">

                    </div>
                      <div class="col-xs-5" style="margin-left: 30px;">
                  </div>
                  <!-- <div class="col-xs-5" style="margin-left: 30px;">
                    
                      <span><b><center style="border:#d2d6de 1px solid; border-radius: 5px 5px 0 0;">Descuento (-)</center></b></span>

                    <div class="input-group">
                      
                     <span class="input-group-addon" style="border-radius: 0 0 0px 5px;"><i class="fas fa-minus-circle fa-xs"></i><i class="fas fa-dollar-sign"></i></span>

                      <input type="number" style="border-radius: 0 0 5px 0;" class="form-control input-lg" min="0" id="nuevodescuentoventaEntero" name="nuevodescuentoventaEntero" placeholder="0">

                    </div>

                  </div>-->

                </div>
                  
                   <div class="form-group" style="margin-left:30px;margin-right:70px;">

                                                     
                            <div class="input-group">
                           
                              <span class="input-group-addon" style="border-radius: 0 0 5px 0px ;"><i class="ion ion-social-usd"></i></span>

                              <input type="text" style="border-radius: 0px 0px 5px 0px;" class="form-control input-lg" id="nuevoTotalVenta" name="nuevoTotalVenta" total="" placeholder="00000" readonly required>
                              
                              <input type="hidden" name="totalVenta" id="totalVenta">
                        
                            </div>
                    </div>

                </div>

                <hr>

                <!--=====================================
                ENTRADA MÉTODO DE PAGO
                ======================================-->

                <div class="form-group row">
                  
                  <div class="col-xs-6" style="padding-right:0px">
                    
                     <div class="input-group">
                  
                      <select style="border-radius: 5px ;"class="form-control" id="nuevoMetodoPago" name="nuevoMetodoPago" required>
                        <option value="">Seleccione método de pago</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjetaCredito">Tarjeta Crédito</option>
                        <option value="tarjetaDebito">Tarjeta Débito</option>     
                        <option value="tarjetaDebito">Pago Electrónico</option>   
                        <option value="tarjetaDebito">Mercado pago</option>     
                        <option value="tarjetaDebito">PayPal</option>  
                        <option value="tarjetaDebito">Cheque</option>         
                      </select>    

                    </div>

                  </div>

                  <div class="cajasMetodoDePago"></div>

                  <input type="hidden" id="listaMetodoPago" name="listaMetodoPago">

                </div>

                <br>
      
              </div>

          </div>

          <div class="box-footer">

            <button type="submit" class="btn btn-primary pull-right">Guardar venta</button>

          </div>


          <?php

            $ventaDinamica = new ControladorVentas();
            $ventaDinamica -> ctrCrearVentaDinamica();

          ?>

        </form>

        </div>
            
      </div>

      <!--=====================================
      LA TABLA DE PRODUCTOS
      ======================================-->

      <div class="col-lg-7 hidden-md hidden-sm hidden-xs">
        
        <div class="box box-warning">

          <div class="box-header with-border"></div>

          <div class="box-body">
            
            <table class="table table-bordered table-striped dt-responsive tablaProductosDinamicas">
              
               <thead>

                 <tr>
                  <th style="width: 10px">#</th>
                  <th>Imagen</th>
                  <th>Código</th>
                  <th>Producto</th>
                  <th>Stock</th>
                  <th>Acciones</th>
                </tr>

              </thead>
              <?php

                echo'<input  type="hidden" id="tipoDePerfil" value="'.$_SESSION["perfil"].'"  placeholder="'.$_SESSION["perfil"].'">
              
                <input  type="hidden" id="id_empresa" value="'.$_SESSION["empresa"].'">';

              ?>

            </table>

          </div>

        </div>


      </div>

    </div>
   
  </section>

</div>
<!--=====================================
MODAL AGREGAR CLIENTE
======================================-->

<div id="modalAgregarUsuario" class="modal fade" role="dialog">

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
              
              Agregar Usuario   

            </h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">
          
          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE -->

            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fas fa-user"></i></span>

                <input type="text"  class="form-control input-lg"  name="AgregarNombreCliente" placeholder="Nombre del Cliente">

              </div>

            </div>            

            <!-- ENTRADA PARA EL CORREO -->

            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fas fa-at"></i></span>

                <input type="text"  class="form-control input-lg" name="AgregarCorreoCliente" placeholder="Correo Del Cliente">

              </div>

            </div>  

            <!-- ENTRADA PARA EL TELEFONO DOS -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                <input type="tel" class="form-control input-lg" name="telefonoUnoCliente" placeholder="Ingresa Numero Telefonico" required>

              </div>

            </div>     

            <!-- ENTRADA PARA EL TELEFONO DOS -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                <input type="tel" class="form-control input-lg" name="telefonoDosCliente" placeholder="Ingresa Numero Telefonico Dos" required>

              </div>

            </div>       

            <!-- ENTRADA PARA EL NOMBRE DEL ASESOR -->

            <div class="input-group">
              
              <span class="input-group-addon">
                
                <i class="fas fa-headphones"></i>

              </span>

              <select class="form-control input-lg" name="AgreagrAsesorAlCliente">
                
                <option value="" id="AgreagrAsesorAlCliente">
                  
                  Seleccionar Asesor

                </option>
                 <?php
                      
                      $item = null;
                      $valor = null;

                      $Asesores= Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);

                      foreach ($Asesores as $key => $value) {
                        
                        echo '

                        <option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                      }
               ?>


              </select>

            </div>

          </div>

          <!--=====================================
          PIE DEL MODAL
          ======================================-->
    
          <div class="modal-footer">

            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

            <button type="submit" class="btn btn-primary">Agregar Cliente</button>

          </div>

        </div>

        <?php
          
         $AgregarCliente = new ControladorClientes();
         $AgregarCliente -> ctrMostrarAgregarClienteDnetroDeVenta();

        ?>
      </form>

    </div>

  </div>

</div>