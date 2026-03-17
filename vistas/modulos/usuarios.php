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
      Gestor usuarios
    </h1>
 
    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>

      <li class="active">Gestor Usuarios</li>
      
    </ol>

  </section>

  <section class="content">

    <div class="box">  

      <div class="box-header with-border">

      </div>

      <div class="box-body">

        <div class="box-tools">

          <a href="vistas/modulos/reportes.php?reporte=usuarios">

            <button class="btn btn-success" style="margin-top:5px">Descargar Reporte En Excel</button>

          </a>

        </div> 

         <div class="box-header with-border">
         
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarUsuario">

          
          Agregar Usuario

        </button>

      </div>


        <br>
         
        <table class="table table-bordered table-striped dt-responsive tablaUsuarios" width="100%">

          <thead>
            
            <tr>
              
              <th style="width:10px">
              <th>Nombre</th>
              <th>Asesor</th>
              <th>Email</th>
              <th>Modo</th>
              <th>Foto</th>
              <th>Estado</th>
              <th>Acciones</th>
              <th>Fecha</th>

            </tr>

          </thead>

        </table> 

      </div>
        
    </div>

  </section>

</div>

<!--=====================================
MODAL EDITAR USUARIO
======================================-->

<div id="modalEditarUsuario" class="modal fade" role="dialog">

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
              
              Editar Asesor   

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

                <input type="text"  class="form-control input-lg"  id="editarNombre" readonly>

              </div>

            </div>            

            <!-- ENTRADA PARA EL CORREO -->

            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fas fa-at"></i></span>

                <input type="text"  class="form-control input-lg"  id="editarEmail" readonly>

              </div>

            </div>  

            <!-- ENTRADA PARA EL MODO DE REGISTRO -->

            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fas fa-sign-in"></i></span>

                <input type="text"  class="form-control input-lg"  id="Modo" readonly>

              </div>

            </div>                

            <!-- ENTRADA PARA EL NOMBRE DEL ASESOR -->

            <div class="input-group">
              
              <span class="input-group-addon">
                
                <i class="fas fa-headphones"></i>

              </span>

              <select class="form-control input-lg" name="editarAsesor">
                
                <option value="" id="editarAsesor">
                  
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

              <input type="hidden" id="idUsuario" name="idUsuario">

            </div>

          </div>

          <!--=====================================
          PIE DEL MODAL
          ======================================-->
    
          <div class="modal-footer">

            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

            <button type="submit" class="btn btn-primary">Modificar Asesor</button>

          </div>

        </div>
      <?php
          
          $editarAsesor = new ControladorUsuarios();
          $editarAsesor -> ctrEditarAsesorU();

        ?>
      </form>

    </div>

  </div>

</div>
<!--=====================================
MODAL EDITAR USUARIO
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

            <!-- ENTRADA PARA EL MODO DE REGISTRO -->

            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fas fa-sign-in"></i></span>

                <input type="text"  class="form-control input-lg directo" placeholder="Escribe directo">

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
          
          $AgreagrUsuarioDirectoO = new ControladorUsuarios();
          $AgreagrUsuarioDirectoO -> ctrAgregarUsuarioDirecto();

        ?>
      </form>

    </div>

  </div>

</div>