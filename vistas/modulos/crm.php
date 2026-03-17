<?php
if($_SESSION["perfil"] !== "administrador" AND $_SESSION["perfil"]!== "vendedor" AND $_SESSION["perfil"] !== "secretaria"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}
?>

<!--=============================
CABECERAN DE PAGINA
===============================-->

<div  class="content-wrapper">

    <section class="content-header">

        <h1>Administracion de relaciones con el cliente</h1>

        <ol class="breadcrumb">
            <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>
            <li class="active">CRM</li>
        
        </ol>
    </section>
    
    <!--=============================
    ESTRUCTURA DE TABLA
    ===============================-->
    <section class="content">
        <div class="box">

            <div class="box-body">

                <div class="box-header with-border">
                    
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarCliente">
                        Agregar Cliente
                    </button>
                    <button class="btn btn-secundary">
                        <a href="#">
                        Calendario
                        </a>
                    </button>
                </div>
            </div>
            <table class="table table-bordered table-striped dt-responsive tablaClientesCRM" width="100%">
        
        <thead>
        
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Telefono #1</th>
                <th>Telefono #2</th>
                <th>Asesor</th>
                <th>Fecha de registro</th>
                <th>Ultimo Servicio</th>
                <th>Descripción</th>
                <th>Etiqueta</th>
                <th>Correo</th>
                <th>Acciones</th>
            </tr>
        
        </thead>
    
    </table>
        </div>
        
    </section>
</div>
<!--=========================================
Ventana Modal Para Registrar Nuevo Usuario
==============================================-->
<div id="modalAgregarCliente" class="modal fade" role="dialog">

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

                <input type="text"  class="form-control input-lg"  name="AgregarNombre" placeholder="Nombre del usuario">

              </div>

            </div>            

            <!-- ENTRADA PARA EL CORREO -->

            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fas fa-at"></i></span>

                <input type="text"  class="form-control input-lg" name="AgregarCorreo" placeholder="Correo Del Usuario">

              </div>

            </div>  

            <!-- ENTRADA PARA ETIQUETA DEL SUSUARIO -->

              
            <div class="form-group">
                <div class="input-group">
                    
                    <span class="input-group-addon">
                    
                        <i class="fas fa-tag"></i>

                    </span>

                    <select class="form-control input-lg" name="AgreagrAsesor">
                    
                        <option value="">
                             <span class="label label-primary">Nuevo</span>
                        </option>
                        <option value="">
                             <span class="label label-success">Frecuente</span>
                        </option>
                        <option value="">
                             <span class="label label-danger">Problematico</span>
                        </option>
                    </select>
                    
                </div>
            </div>


            <!-- ENTRADA PARA EL NOMBRE DEL ASESOR -->

            <div class="input-group">
              
              <span class="input-group-addon">
                
                <i class="fas fa-headphones"></i>

              </span>

              <select class="form-control input-lg" name="AgreagrAsesor">
                
                <option value="" id="AgreagrAsesor">
                  
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
          PIE DEL MODAL
          ======================================-->
    
          <div class="modal-footer">

            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

            <button type="submit" class="btn btn-primary">Agregar Usuario</button>

          </div>

        </div>
      <?php
          
          //$AgreagrUsuarioDirectoO = new ControladorUsuarios();
          //$AgreagrUsuarioDirectoO -> ctrAgregarUsuarioDirecto();

        ?>
      </form>

    </div>

  </div>

</div>