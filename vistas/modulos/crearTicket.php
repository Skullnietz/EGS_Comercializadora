<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Crear ticket
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="#"><i class="fas fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Crear ticket</li>
    
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
          <form role="form" method="post" class="formularioCrearTicket" enctype="multipart/form-data">

            <div class="box-body">
  
              <div class="box">
                <!--=====================================
                ENTRADA PARA LA EMPRESA
                ======================================-->        
             <!-- ENTRADA PARA EL NOMBRE DE LA EMPRESA QUE VENDE-->

            <div class="form-group">
              
              <div class="input-group">
                
                 <span class="input-group-addon"><i class="fas fa-building"></i></span> 

                 <select class="form-control" id="empresa" name="empresa" required>
                  
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
            ENTRADA PARA NUMERO DE TICKET O FACTURA
            ======================================-->
            

              <div class="form-group">

                <div class="input-group">
                  
                  <span class="input-group-addon"><i class="fas fa-ticket"></i></span>
                  <input type="number" class="form-control input-xs" name="codigoTicket">

                </div>
                
              </div>




                <!--=====================================
                ENTRADA PARA AGREGAR PRODUCTO
                ======================================--> 

                <div class="form-group row nuevoProductoTicket">

               
                </div>

                <!--=====================================
                RECIBIR DATOS EN JSON DESDE JS 
                ======================================-->
                <input type="hidden" id="listaProductosDelTicket" name="listaProductosDelTicket"> 

                <div class="form-group">
                  
                  <div class="input-group">

                    <span class="input-group-addon"><i class="fas fa-dollar"></i></span>
                    
                     <input type="number" class="form-control input-lg" id="TotalProductosTicket" name="TotalProductosTicket" readonly required>


                  </div>

                </div> 
              

               

                <!--=====================================
                BOTÓN PARA AGREGAR PRODUCTO
                ======================================-->

                <button type="button" class="btn btn-default hidden-lg btnAgregarProductoMovil">Agregar producto</button>

              <hr>

               <hr>

                <br>
      
              </div>

          </div>

          <div class="box-footer">

            <button type="submit" class="btn btn-primary pull-right">Guardar venta</button>

          </div>


          <?php

            $crearTicket = new ControladorTickets();
            $crearTicket -> ctrCrearTicket();

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
            
            <table class="table table-bordered table-striped dt-responsive tablaProductosTicket">
              
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