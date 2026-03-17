<?php

if($_SESSION["perfil"] != "Super-Administrador" AND $_SESSION["perfil"] != "administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}

?>

<div class="content-wrapper">

  <section class="content-header">

   <h1>
      Administrador de perfiles
    </h1>

    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>

      <li class="active">Administrar perfiles</li>

    </ol>

  </section>

  <section class="content">

    <div class="box">
       
      <div class="box-header with-border">
         
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarPerfil">

          
          Agregar Perfil

        </button>

      </div>

      <div class="box-body">

        <table class="table table-bordered table-striped dt-responsive tablaPerfiles" width="100%">
        
          <thead>
         
            <tr>
             
               <th style="width:10px">#</th>
               <th>Nombre</th>
               <th>Correo</th>
               <th>Foto</th>
               <th>Departamento</th>
               <th>Estado</th>
               <th>Acciones</th>


            </tr> 

          </thead>  

          <tbody>
            
            <?php

            if ($_SESSION["perfil"] == "Super-Administrador") {
              
              $item = null;
              $valor = null;

              $perfiles = ControladorAdministradores::ctrMostrarAdministradores($item, $valor);

            }else{

              $item = "id_empresa";
              $valor = $_SESSION["empresa"];

              $perfiles = ControladorAdministradores::ctrlMostrarAdministradoresPorEmpresa($item, $valor);
            }
              
              foreach ($perfiles as $key => $value){


                echo ' <tr>
                          <td>'.($key+1).'</td>
                          <td>'.$value["nombre"].'</td>
                          <td>'.$value["email"].'</td>';

               if($value["foto"] != ""){

                          echo '<td><img loading="lazy" src="'.$value["foto"].'" class="img-thumbnail" width="40px"></td>';

                         }else{

                            echo '<td><img loading="lazy" src="vistas/img/perfiles/default/anonymous.png" class="img-thumbnail" width="40px"></td>';

                        }

                        echo '<td>'.$value["Departamento"].'</td>';

                         if($value["estado"] != 0){

                          echo '<td><button class="btn btn-success btn-xs btnActivar" idPerfil="'.$value["id"].'" estadoPerfil="0">Activado</button></td>';

                        }else{

                          echo '<td><button class="btn btn-danger btn-xs btnActivar" idPerfil="'.$value["id"].'" estadoPerfil="1">Desactivado</button></td>';

                        } 

                         echo '<td>

                          <div class="btn-group">
                              
                            <button class="btn btn-warning btnEditarPerfil" idPerfil="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarPerfil"><i class="fas fa-edit"></i></button>

                            <button class="btn btn-danger btnEliminarPerfil" idPerfil="'.$value["id"].'" fotoPerfil="'.$value["foto"].'"><i class="fas fa-times"></i></button>

                          </div>  

                        </td>

                      </tr>';            
             }


            ?>
      

          </tbody> 
     
        </table>
          
      </div>

    </div>

  </section>

</div>

<!--=====================================
MODAL AGREGAR PERFIL
======================================-->

<div id="modalAgregarPerfil" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Perfil</h4>

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

                <input type="text" class="form-control input-lg" name="nuevoNombre" placeholder="Ingresar nombre" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL EMAIL -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-envelope"></i></span> 

                <input type="email" class="form-control input-lg" name="nuevoEmail" placeholder="Ingresar Email" id="nuevoEmail" required>

              </div>

            </div>

            <!-- ENTRADA PARA LA CONTRASEÑA -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-lock"></i></span> 

                <input type="password" class="form-control input-lg" name="nuevoPassword" placeholder="Ingresar contraseña" required>

              </div>

            </div>

            <!-- ENTRADA PARA SELECCIONAR SU PERFIL -->

            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-users"></i></span> 

                <select class="form-control input-lg" name="nuevoPerfil">
                  
                  <option value="">Selecionar perfil</option>

                  <?php
                  if ($_SESSION["perfil"] == "Super-Administrador") {
                    
                    echo'<option value="Super-Administrador">Super Administrador</option>';

                  }
                  ?>

                  <option value="administrador">Administrador</option>
                  
                  <option value="vendedor">Vendedor</option>

                  <option value="tecnico">Técnico</option>

                  <option value="secretaria">Secretaria</option>

                </select>
                

              </div>
            </div>
            <!-- ENTRADA PARA INGRESAR EL DEPARTAMENTO-->
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-user-tag"></i></span> 

                
                
                <select class="form-control input-lg" name="Departamento">
                  
                  <option value="">Selecionar Departamento</option>
                  <option value="Ventas">Ventas</option>
                  <option value="Administracion">Administracion</option>
                  <option value="Ventas Externas">Ventas Externas</option>
                  <option value="Sistemas">Sistemas</option>
                  <option value="Electronica">Electronica</option>
                  <option value="Impresoras">Impresoras</option>
                  <option value="Desarrollo">Desarrollo</option>
                  <option value="">Sin departamento</option>
                  

                </select>

              </div>

            </div>
        <!-- ENTRADA PARA EL NOMBRE DE LA EMPRESA QUE VENDE-->

            <div class="form-group">
              
              <div class="input-group">
                <?php
               
                 if ($_SESSION["perfil"] == "Super-Administrador") {
                  
                  $item = null;
                  $valor = null;
                   
                   $empresa = ControladorEmpresas::ctrMostrarEmpresasParaEditar($item, $valor);

                  echo'
                    <div class="form-group">
              
                      <div class="input-group">
                        
                        <span class="input-group-addon"><i class="fas fa-building"></i></span>
                          <select class="form-control input-lg" name="empresa">';
                        
                          foreach ($empresa as $key => $valueEmpresa) {
                            
                              echo '<option value='.$valueEmpresa["id"].'>'.$valueEmpresa["empresa"].'</option>';
                          }
                        
                          echo'</select>

                        </div>

                      </div>';


                 }else{

                  echo'<input type="hidden" value="'.$_SESSION["empresa"].'" name="empresa">';

                  }

                ?>
              

                        
              </div>

            </div>
            <!-- ENTRADA PARA SUBIR FOTO -->

             <div class="form-group">
              
              <div class="panel">SUBIR FOTO</div>

              <input type="file" class="nuevaFoto" name="nuevaFoto">

              <p class="help-block">Peso máximo de la foto 2MB</p>

              <img loading="lazy" src="vistas/img/perfiles/default/anonymous.png" class="img-thumbnail previsualizar" width="100px">

            </div>

          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Guardar Perfil</button>

        </div>

        <?php

          $crearPerfil = new ControladorAdministradores();
          $crearPerfil -> ctrCrearPerfil();

        ?>

      </form>

    </div>

  </div>

</div>

<!--=====================================
MODAL EDITAR PERFIL
======================================-->

<div id="modalEditarPerfil" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Editar Perfil</h4>

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

                <input type="text" class="form-control input-lg" id="editarNombre" name="editarNombre" value="" required>

                <input type="hidden" id="idPerfil" name="idPerfil">

              </div>

            </div>

            <!-- ENTRADA PARA EL EMAIL -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-envelope"></i></span> 

                <input type="email" class="form-control input-lg" id="editarEmail" name="editarEmail" value="" required>

              </div>

            </div>

            <!-- ENTRADA PARA LA CONTRASEÑA -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-lock"></i></span> 

                <input type="password" class="form-control input-lg" name="editarPassword" placeholder="Escriba la nueva contraseña">

                <input type="hidden" id="passwordActual" name="passwordActual">

              </div>

            </div>

            <!-- ENTRADA PARA SELECCIONAR SU PERFIL -->

            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-users"></i></span> 

                <select class="form-control input-lg" name="editarPerfil">
                  
                  <option value="" id="editarPerfil"></option>
                  <?php
                  if ($_SESSION["perfil"] == "Super-Administrador") {
                    
                    echo'<option value="Super-Administrador">Super Administrador</option>';

                  }
                  ?>
                  
                  <option value="administrador">Administrador</option>
                  
                  <option value="vendedor">Vendedor</option>

                  <option value="tecnico">Tecnico</option>

                  <option value="secretaria">Secretaria</option>

                </select>

              </div>

            </div>
            <!-- ENTRADA PARA EDITAR EL DEPARTAMENTO-->
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-user-tag"></i></span> 

                
                
                <select class="form-control input-lg" name="Departamento">
                  
                  <option value="">Selecionar Departamento</option>
                  <option value="Ventas">Ventas</option>
                  <option value="Administracion">Administracion</option>
                  <option value="Ventas Externas">Ventas Externas</option>
                  <option value="Sistemas">Sistemas</option>
                  <option value="Electronica">Electronica</option>
                  <option value="Impresoras">Impresoras</option>
                  <option value="Desarrollo">Desarrollo</option>
                  <option value="">Sin departamento</option>

                </select>

              </div>

            </div>
            
            <!-- ENTRADA PARA EDITAR LA EMPRESA-->

            <div class="form-group">
              
              <div class="input-group">
                <?php
               
                 if ($_SESSION["perfil"] == "Super-Administrador") {
                  
                  $item = "id";
                  $valor = $_SESSION["empresa"];
                   
                   $empresa = ControladorEmpresas::ctrMostrarEmpresasParaReportes($item, $valor);

                  echo'
                    <div class="form-group">
              
                      <div class="input-group">
                        
                        <span class="input-group-addon"><i class="fas fa-building"></i></span>
                          <select class="form-control input-lg" name="empresa">';
                        
                          foreach ($empresa as $key => $valueEmpresa) {
                            
                              echo '<option value='.$valueEmpresa["id"].'>'.$valueEmpresa["empresa"].'</option>';
                          }
                        
                          echo'</select>

                        </div>

                      </div>';


                 }else{

                  echo'<input type="hidden" value="'.$_SESSION["empresa"].'" name="empresa">';

                  }

                ?>
              

                        
              </div>

            </div>

            <!-- ENTRADA PARA SUBIR FOTO -->

             <div class="form-group">
              
              <div class="panel">SUBIR FOTO</div>

              <input type="file" class="nuevaFoto" name="editarFoto">

              <p class="help-block">Peso máximo de la foto 2MB</p>

              <img loading="lazy" src="vistas/img/perfiles/default/anonymous.png" class="img-thumbnail previsualizar" width="100px">

              <input type="hidden" name="fotoActual" id="fotoActual">

            </div>

          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->
  
        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Modificar Perfil</button>

        </div>

     <?php

          $editarPerfil = new ControladorAdministradores();
          $editarPerfil -> ctrEditarPerfil();

        ?> 

      </form>

    </div>

  </div>

</div>

<?php

  $eliminarPerfil = new ControladorAdministradores();
  $eliminarPerfil -> ctrEliminarPerfil();

?> 
