<?php

if($_SESSION["perfil"] != "Super-Administrador" AND $_SESSION["perfil"] != "administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}

?>
<style>
/* ─── CRM Design System Tokens ─── */
:root {
  --crm-bg:       #f8fafc;
  --crm-surface:  #ffffff;
  --crm-border:   #e2e8f0;
  --crm-text:     #0f172a;
  --crm-text2:    #475569;
  --crm-muted:    #94a3b8;
  --crm-accent:   #6366f1;
  --crm-radius:   14px;
  --crm-radius-sm:10px;
  --crm-shadow:   0 1px 3px rgba(15,23,42,.06), 0 4px 14px rgba(15,23,42,.04);
  --crm-shadow-lg:0 4px 24px rgba(15,23,42,.10);
  --crm-ease:     cubic-bezier(.4,0,.2,1);
}

.crm-card {
  background: var(--crm-surface);
  border: 1px solid var(--crm-border);
  border-radius: var(--crm-radius);
  box-shadow: var(--crm-shadow);
  overflow: hidden;
  transition: box-shadow .2s var(--crm-ease), transform .2s var(--crm-ease);
}
.crm-card:hover { box-shadow: var(--crm-shadow-lg); }
.crm-btn { border-radius: var(--crm-radius-sm); font-weight: 600; transition: all .15s var(--crm-ease); }
.modal-content.crm-modal { border-radius: var(--crm-radius); border: none; box-shadow: var(--crm-shadow-lg); }
.modal-header.crm-modal-header { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; border-top-left-radius: var(--crm-radius); border-top-right-radius: var(--crm-radius); }
.modal-header.crm-modal-header .close { color: white; opacity: 0.8; }
.form-group .input-group-addon { border-top-left-radius: 8px; border-bottom-left-radius: 8px; background: #f8fafc; color: var(--crm-muted); border-color: var(--crm-border); }
.form-group .form-control { border-top-right-radius: 8px; border-bottom-right-radius: 8px; border-color: var(--crm-border); box-shadow: none; }
.form-group .form-control:focus { border-color: var(--crm-accent); box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
</style>

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

    <div class="box crm-card">
       
      <div class="box-header with-border" style="padding: 20px;">
         
        <button class="btn btn-primary crm-btn" data-toggle="modal" data-target="#modalAgregarPerfil" style="background:#6366f1; border:none;">
          <i class="fas fa-user-plus"></i> Agregar Perfil
        </button>

      </div>

      <div class="box-body" style="padding: 20px;">

        <table class="table table-striped dt-responsive tablaPerfiles" width="100%">
        
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
                          <td style="font-weight:600;">'.$value["nombre"].'<br><small style="color:var(--crm-muted);font-weight:400;text-transform:uppercase;">'.$value["perfil"].'</small></td>
                          <td>'.$value["email"].'</td>';

               if($value["foto"] != ""){
                          echo '<td><img loading="lazy" src="'.$value["foto"].'" class="img-thumbnail" width="40px" style="border-radius:50%"></td>';
                         }else{
                            echo '<td><img loading="lazy" src="vistas/img/perfiles/default/anonymous.png" class="img-thumbnail" width="40px" style="border-radius:50%"></td>';
                        }

                        echo '<td><span class="label label-default" style="border-radius:10px;padding:4px 10px;background:#e2e8f0;color:#475569;">'.$value["Departamento"].'</span></td>';

                         if($value["estado"] != 0){
                          echo '<td><button class="btn btn-success btn-xs btnActivar crm-btn" idPerfil="'.$value["id"].'" estadoPerfil="0" style="border-radius:20px;padding:3px 12px;">Activado</button></td>';
                        }else{
                          echo '<td><button class="btn btn-danger btn-xs btnActivar crm-btn" idPerfil="'.$value["id"].'" estadoPerfil="1" style="border-radius:20px;padding:3px 12px;">Desactivado</button></td>';
                        } 

                         echo '<td>
                          <div class="btn-group">
                            <button class="btn btn-warning btnEditarPerfil crm-btn" idPerfil="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarPerfil" style="margin-right:5px;"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btnEliminarPerfil crm-btn" idPerfil="'.$value["id"].'" fotoPerfil="'.$value["foto"].'"><i class="fas fa-times"></i></button>
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

    <div class="modal-content crm-modal">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header crm-modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fas fa-user-plus"></i> Agregar Perfil</h4>
        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body" style="padding: 24px;">

          <div class="box-body">

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fas fa-user"></i></span> 
                <input type="text" class="form-control input-lg" name="nuevoNombre" placeholder="Ingresar nombre" required>
              </div>
            </div>

             <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fas fa-envelope"></i></span> 
                <input type="email" class="form-control input-lg" name="nuevoEmail" placeholder="Ingresar Email" id="nuevoEmail" required>
              </div>
            </div>

             <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fas fa-lock"></i></span> 
                <input type="password" class="form-control input-lg" name="nuevoPassword" placeholder="Ingresar contraseña" required>
              </div>
            </div>

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fas fa-users"></i></span> 
                <select class="form-control input-lg" name="nuevoPerfil" id="nuevoPerfil">
                  <option value="">Seleccionar perfil</option>
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

            <!-- CONTENEDORES ADICIONALES (Se muestran por JS) -->
            <div id="divAdicionalTecnico" style="display:none; padding:15px; margin-bottom:15px; border-left:4px solid #0ea5e9; background:#f0f9ff; border-radius:4px;">
                <h5 style="color:#0284c7; font-weight:700; margin-top:0;"><i class="fas fa-wrench"></i> Datos Adicionales de Técnico</h5>
                
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-phone"></i></span> 
                    <input type="tel" class="form-control" name="numeroTelTecnico" id="numeroTelTecnico" placeholder="Teléfono Principal">
                  </div>
                </div>

                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-mobile-alt"></i></span> 
                    <input type="tel" class="form-control" name="numeroTelDosTecnico" id="numeroTelDosTecnico" placeholder="Teléfono Secundario">
                  </div>
                </div>

                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-clock"></i></span> 
                    <input type="text" class="form-control" name="HoraDeComida" id="HoraDeComida" placeholder="Horario de Comida">
                  </div>
                </div>

                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-cogs"></i></span> 
                    <select class="form-control" name="areratecnico" id="areratecnico">
                      <option value="">Seleccionar área de técnico</option>
                      <option value="electronica">Electrónica</option>
                      <option value="impresoras">Impresoras</option>
                      <option value="sistemas">Sistemas</option>
                    </select>
                  </div>
                </div>
            </div>

            <div id="divAdicionalAsesor" style="display:none; padding:15px; margin-bottom:15px; border-left:4px solid #8b5cf6; background:#f5f3ff; border-radius:4px;">
                <h5 style="color:#6d28d9; font-weight:700; margin-top:0;"><i class="fas fa-handshake"></i> Datos Adicionales de Asesor</h5>
                
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-phone"></i></span> 
                    <input type="tel" class="form-control" name="nuevoNumeroUno" id="nuevoNumeroUno" placeholder="Teléfono Asesor">
                  </div>
                </div>

                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-mobile-alt"></i></span> 
                    <input type="tel" class="form-control" name="nuevoNumeroDos" id="nuevoNumeroDos" placeholder="Celular Asesor">
                  </div>
                </div>
            </div>

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fas fa-user-tag"></i></span> 
                <select class="form-control input-lg" name="Departamento">
                  <option value="">Seleccionar Departamento</option>
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

             <div class="form-group">
              <div class="panel">SUBIR FOTO</div>
              <input type="file" class="nuevaFoto" name="nuevaFoto">
              <p class="help-block">Peso máximo de la foto 2MB</p>
              <img loading="lazy" src="vistas/img/perfiles/default/anonymous.png" class="img-thumbnail previsualizar" width="100px" style="border-radius:12px;">
            </div>

          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer" style="border-top: 1px solid var(--crm-border);">
          <button type="button" class="btn btn-default pull-left crm-btn" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary crm-btn" style="background:#6366f1; border:none;">Guardar Perfil</button>
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

    <div class="modal-content crm-modal">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header crm-modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fas fa-user-edit"></i> Editar Perfil</h4>
        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body" style="padding: 24px;">

          <div class="box-body">
            
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fas fa-user"></i></span> 
                <input type="text" class="form-control input-lg" id="editarNombre" name="editarNombre" value="" required>
                <input type="hidden" id="idPerfil" name="idPerfil">
              </div>
            </div>

             <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fas fa-envelope"></i></span> 
                <input type="email" class="form-control input-lg" id="editarEmail" name="editarEmail" value="" required>
              </div>
            </div>

             <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fas fa-lock"></i></span> 
                <input type="password" class="form-control input-lg" name="editarPassword" placeholder="Escriba la nueva contraseña">
                <input type="hidden" id="passwordActual" name="passwordActual">
              </div>
            </div>

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fas fa-users"></i></span> 
                <select class="form-control input-lg" name="editarPerfil" id="editarPerfilSelect">
                  <option value="" id="editarPerfilOpcion"></option>
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

            <!-- CONTENEDORES ADICIONALES (Se muestran por JS al editar) -->
            <div id="divAdicionalTecnicoEdit" style="display:none; padding:15px; margin-bottom:15px; border-left:4px solid #0ea5e9; background:#f0f9ff; border-radius:4px;">
                <h5 style="color:#0284c7; font-weight:700; margin-top:0;"><i class="fas fa-wrench"></i> Datos Adicionales de Técnico</h5>
                
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-phone"></i></span> 
                    <input type="tel" class="form-control" name="editarNumeroUnoTecnico" id="editarNumeroUnoTecnico" placeholder="Teléfono Principal">
                  </div>
                </div>

                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-mobile-alt"></i></span> 
                    <input type="tel" class="form-control" name="editarTelefonoDosTecnico" id="editarTelefonoDosTecnico" placeholder="Teléfono Secundario">
                  </div>
                </div>

                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-clock"></i></span> 
                    <input type="text" class="form-control" name="HoraDeComidaEditada" id="HoraDeComidaEditada" placeholder="Horario de Comida">
                  </div>
                </div>

                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-cogs"></i></span> 
                    <select class="form-control" name="editarAreratecnico" id="editarAreratecnico">
                      <option value="">Seleccionar área de técnico</option>
                      <option value="electronica">Electrónica</option>
                      <option value="impresoras">Impresoras</option>
                      <option value="sistemas">Sistemas</option>
                    </select>
                  </div>
                </div>
            </div>

            <div id="divAdicionalAsesorEdit" style="display:none; padding:15px; margin-bottom:15px; border-left:4px solid #8b5cf6; background:#f5f3ff; border-radius:4px;">
                <h5 style="color:#6d28d9; font-weight:700; margin-top:0;"><i class="fas fa-handshake"></i> Datos Adicionales de Asesor</h5>
                
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-phone"></i></span> 
                    <input type="tel" class="form-control" name="editarNumeroUnoAsesor" id="editarNumeroUnoAsesor" placeholder="Teléfono Asesor">
                  </div>
                </div>

                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-mobile-alt"></i></span> 
                    <input type="tel" class="form-control" name="editarTelefonoDosAsesor" id="editarTelefonoDosAsesor" placeholder="Celular Asesor">
                  </div>
                </div>
            </div>

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fas fa-user-tag"></i></span> 
                <select class="form-control input-lg" name="Departamento" id="editarDepartamento">
                  <option value="">Seleccionar Departamento</option>
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

             <div class="form-group">
              <div class="panel">SUBIR FOTO</div>
              <input type="file" class="nuevaFoto" name="editarFoto">
              <p class="help-block">Peso máximo de la foto 2MB</p>
              <img loading="lazy" src="vistas/img/perfiles/default/anonymous.png" class="img-thumbnail previsualizar" width="100px" style="border-radius:12px;">
              <input type="hidden" name="fotoActual" id="fotoActual">
            </div>

          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->
  
        <div class="modal-footer" style="border-top: 1px solid var(--crm-border);">
          <button type="button" class="btn btn-default pull-left crm-btn" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary crm-btn" style="background:#6366f1; border:none;">Modificar Perfil</button>
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
