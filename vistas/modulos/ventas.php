  <?php

//if($_SESSION["perfil"] != "administrador"){

  //echo '<script>

  //window.location = "inicio";

  //</script>';

 // return;

//}

?>
<script>
    $(document).ready(function(){
        $(".agregarProducto").click(function() {
        $(".nuevoPrecioProducto").prop('disabled', true);
        }); 
    });   
</script>

<div class="content-wrapper">
  
   <section class="content-header">
      
    <h1>
      Gestor ventas
    </h1>

    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>

      <li class="active">Gestor Ventas</li>
      
    </ol>

  </section>


  <section class="content">

    <div class="box"> 

      <div class="box-header with-border">
        
        <?php

        include "inicio/grafico-ventas.php";

        ?>

      </div>

      <div class="box-body">

        <div class="box-tools">

          <a href="vistas/modulos/reportes.php?reporte=compras">
            
              <button class="btn btn-success">Descargar Reporte En Excel</button>

          </a>


        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarVenta">
          
          Agregar Venta

        </button>

        </div>


        <br>
        
        <table class="table table-bordered table-striped dt-responsive tablaVentas" width="100%">
        
          <thead>
            
            <tr>
              
              <th style="width:10px">#</th>
              <th>Producto</th>
              <th>Imagen Producto</th>
              <th>Detalles</th>
              <th>Cantidad</th>
              <th>Cliente</th>
              <th>Foto Cliente</th>
              <th>Venta</th>
              <th>Tipo</th>  
              <th>Proceso de envío</th>         
              <th>Metodo</th>
              <th>Email</th>
              <th>Dirección</th>
              <th>País</th>
              <th>Fecha</th>
              <th>Imprimir pdf</th>
              <th>Imprimir ticket</th>

            </tr>

          </thead> 


        </table>


      </div>

    </div>

  </section>

</div>

<!--=====================================
MODAL AGREGAR VENTA
======================================-->
<div  id="modalAgregarVenta" class="modal fade" role="dialog">
  
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
                
                <!-- ENTRADA PARA EL TÍTULO -->

                <div class="form-group">
                  
                  <div  class="input-group">
                    
                    <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span>

                     <input type="text" class="form-control input-lg producto" name="producto" placeholder="Ingresar título producto">

                  </div>

                </div>

                <!-- AGREGAR DESCRIPCIÓN -->

                <div class="form-group">
                  
                  <div class="input-group">

                    <span class="input-group-addon"><i class="fas fa-pencil"></i></span> 
                        <textarea type="text" maxlength="320" rows="3" class="form-control input-lg" placeholder="Ingresar descripción producto" name="desccripcion"></textarea>
                    
                  </div>

                </div>

                <!-- ENTRADA PARA EL NOMBRE -->

                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fas fa-user"></i></span>

                          <input type="text"  class="form-control input-lg nombre"   placeholder="Ingresa Nombre Del Cliente" name="nombreCliente" required>

                  </div>

                </div>

                <!-- ENTRADA PARA El  NUMERO TELEFONICO -->

                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                          <input type="tel" class="form-control input-lg" name="" placeholder="Ingresa Número Telefónico 1" name="numeroClienteUno" required>


                  </div>

                </div>

                <!-- ENTRADA PARA El  NUMERO TELEFONICO DOS-->

                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fa fa-headphones"></i></span> 

                        <input type="tel" class="form-control input-lg numeroClienteDos" name="numeroClienteDos" placeholder="Ingresa Número Telefónico 2" required>

                  </div>

                </div>

                <!-- ENTRADA PARA EL EMAIL -->

                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fas fa-envelope"></i></span> 

                        <input type="email" class="form-control input-lg correo" name="correo" placeholder="Ingresar Email deL Cliente" id="email del client" required>

                  </div>

                </div>

                <!-- ENTRADA PARA LA DIRECCION -->

                <div class="form-group">
                  
                  <div class="input-group">

                    <span class="input-group-addon"><i class="fas fa-address-card"></i></span> 

                        <input type="direccion" class="form-control input-lg direccion" name="direccion" placeholder="Ingresar Dirección deL Cliente" id="direccion del client" required>
                    
                  </div>

                </div>

                <!-- AGREGAR CANTIDAD VENDIDA -->

                <div class="form-group">
                
                  <div class="input-group">
                    
                     <span class="input-group-addon"><i class="fas fa-industry"></i></span> 

                          <input type="number" class="form-control input-lg cantidadProductos" name="cantidadProductos" min="0" value="0">

                  </div>  

                </div>

                <!-- AGREGAR TOTAL DE LA VENTA -->

                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fas fa-dollar"></i></span> 

                          <input type="number" class="form-control input-lg pagoTotal" name="pagoTotal" min="0" value="0">

                  </div>

                </div>

                <!-- AGREGAR METODO DE PAGO -->

                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fas fa-shopping-cart"></i></span> 

                        <input type="text" class="form-control input-lg metodoDePago" name="metodoDePago">

                  </div>

                </div>

                <!--=====================================
                PIE DEL MODAL
                ======================================-->

                <div class="modal-footer">
                  
                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

                  <button type="submit" class="btn btn-primary">
                    
                    Guardar Venta

                  </button>

                </div>

                <?php

                  $crearVenta = new ControladorVentas();
                  $crearVenta -> ctrCrearventa();

                ?>

              </div>

            </div>


      </form>
      
    </div>

  </div>

</div>