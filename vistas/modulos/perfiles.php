<?php

if ($_SESSION["perfil"] != "Super-Administrador" && $_SESSION["perfil"] != "administrador") {
  echo '<script>window.location = "inicio";</script>';
  return;
}

$esSuperAdminPerfiles = $_SESSION["perfil"] === "Super-Administrador";
$empresaSesionPerfiles = isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0;

if ($esSuperAdminPerfiles) {
  $perfiles = ControladorAdministradores::ctrMostrarAdministradores(null, null);
  $empresasPerfiles = ControladorEmpresas::ctrMostrarEmpresasParaEditar(null, null);
} else {
  $perfiles = ControladorAdministradores::ctrlMostrarAdministradoresPorEmpresa("id_empresa", $empresaSesionPerfiles);
  $empresaActualPerfiles = ControladorEmpresas::ctrMostrarEmpresasParaEditar("id", $empresaSesionPerfiles);
  $empresasPerfiles = $empresaActualPerfiles ? array($empresaActualPerfiles) : array();
}

if (!is_array($perfiles)) {
  $perfiles = array();
}

if (!is_array($empresasPerfiles)) {
  $empresasPerfiles = array();
}

$empresasPerfilPorId = array();

foreach ($empresasPerfiles as $empresaPerfil) {
  if (isset($empresaPerfil["id"])) {
    $empresasPerfilPorId[intval($empresaPerfil["id"])] = isset($empresaPerfil["empresa"])
      ? $empresaPerfil["empresa"]
      : "Empresa #" . intval($empresaPerfil["id"]);
  }
}

$rolesPerfil = array(
  "Super-Administrador" => "Superadministración",
  "administrador" => "Administración",
  "vendedor" => "Asesor de ventas",
  "tecnico" => "Técnico",
  "secretaria" => "Secretaría"
);

$totalPerfiles = count($perfiles);
$perfilesActivos = 0;
$rolesPresentes = array();

foreach ($perfiles as $perfilConteo) {
  if (!empty($perfilConteo["estado"])) {
    $perfilesActivos++;
  }

  if (!empty($perfilConteo["perfil"])) {
    $rolesPresentes[$perfilConteo["perfil"]] = true;
  }
}

$perfilesInactivos = $totalPerfiles - $perfilesActivos;
$escPerfil = function ($valor) {
  return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
};

?>

<div
  class="content-wrapper admin-catalog-page"
  style="--catalog-accent:#6366f1;--catalog-accent-dark:#4f46e5;--catalog-accent-soft:#eef2ff;--catalog-accent-rgb:99,102,241;">

  <section class="content-header">
    <h1>Perfiles y accesos</h1>
    <p class="catalog-subtitle">Administra cuentas, roles, departamentos y la empresa asignada a cada integrante.</p>

    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>
      <li class="active">Perfiles</li>
    </ol>
  </section>

  <section class="content">
    <div class="catalog-summary">
      <div class="catalog-summary-card">
        <span class="catalog-summary-icon"><i class="fas fa-id-badge"></i></span>
        <span>
          <strong class="catalog-summary-value"><?php echo $totalPerfiles; ?></strong>
          <span class="catalog-summary-label">Perfiles registrados</span>
        </span>
      </div>

      <div class="catalog-summary-card is-success">
        <span class="catalog-summary-icon"><i class="fas fa-user-check"></i></span>
        <span>
          <strong class="catalog-summary-value"><?php echo $perfilesActivos; ?></strong>
          <span class="catalog-summary-label">Accesos activos</span>
        </span>
      </div>

      <div class="catalog-summary-card is-danger">
        <span class="catalog-summary-icon"><i class="fas fa-user-lock"></i></span>
        <span>
          <strong class="catalog-summary-value"><?php echo $perfilesInactivos; ?></strong>
          <span class="catalog-summary-label">Accesos inactivos</span>
        </span>
      </div>

      <div class="catalog-summary-card is-info">
        <span class="catalog-summary-icon"><i class="fas fa-users-cog"></i></span>
        <span>
          <strong class="catalog-summary-value"><?php echo count($rolesPresentes); ?></strong>
          <span class="catalog-summary-label">Roles en uso</span>
        </span>
      </div>
    </div>

    <div class="box catalog-box">
      <div class="box-header with-border catalog-box-header">
        <div class="catalog-box-title">
          <h3>Directorio de accesos</h3>
          <p>Edita la cuenta, el rol y los datos operativos asociados.</p>
        </div>

        <div class="catalog-header-actions">
          <button class="btn catalog-primary-button" data-toggle="modal" data-target="#modalAgregarPerfil">
            <i class="fas fa-user-plus"></i> Nuevo perfil
          </button>
        </div>
      </div>

      <div class="box-body">
        <table class="table table-hover dt-responsive tablaPerfiles catalog-table" width="100%">
          <thead>
            <tr>
              <th style="width:40px">#</th>
              <th>Integrante</th>
              <th>Contacto</th>
              <th>Rol</th>
              <th>Empresa</th>
              <th>Departamento</th>
              <th>Estado</th>
              <th style="width:90px">Acciones</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($perfiles as $key => $value) {
              $idPerfilVista = isset($value["id"]) ? intval($value["id"]) : 0;
              $nombrePerfil = isset($value["nombre"]) ? $value["nombre"] : "Sin nombre";
              $correoPerfil = isset($value["email"]) ? $value["email"] : "";
              $rolPerfil = isset($value["perfil"]) ? $value["perfil"] : "";
              $rolEtiqueta = isset($rolesPerfil[$rolPerfil]) ? $rolesPerfil[$rolPerfil] : ($rolPerfil ?: "Sin rol");
              $departamentoPerfil = !empty($value["Departamento"]) ? $value["Departamento"] : "Sin departamento";
              $idEmpresaPerfil = isset($value["id_empresa"]) ? intval($value["id_empresa"]) : 0;
              $nombreEmpresaPerfil = isset($empresasPerfilPorId[$idEmpresaPerfil])
                ? $empresasPerfilPorId[$idEmpresaPerfil]
                : ($idEmpresaPerfil > 0 ? "Empresa #" . $idEmpresaPerfil : "Sin empresa");
              $perfilActivo = !empty($value["estado"]);
              $fotoPerfil = !empty($value["foto"]) ? $value["foto"] : "vistas/img/perfiles/default/anonymous.png";
            ?>
              <tr>
                <td><?php echo $key + 1; ?></td>
                <td>
                  <div class="catalog-entity">
                    <img loading="lazy" src="<?php echo $escPerfil($fotoPerfil); ?>" class="catalog-avatar" alt="">
                    <span>
                      <span class="catalog-entity-name"><?php echo $escPerfil($nombrePerfil); ?></span>
                      <span class="catalog-entity-meta">ID <?php echo $idPerfilVista; ?></span>
                    </span>
                  </div>
                </td>
                <td>
                  <span class="catalog-contact-line"><i class="fas fa-envelope"></i><?php echo $escPerfil($correoPerfil ?: "Sin correo"); ?></span>
                </td>
                <td><span class="catalog-chip is-role"><?php echo $escPerfil($rolEtiqueta); ?></span></td>
                <td><span class="catalog-chip is-company"><i class="fas fa-building"></i> <?php echo $escPerfil($nombreEmpresaPerfil); ?></span></td>
                <td><span class="catalog-chip"><?php echo $escPerfil($departamentoPerfil); ?></span></td>
                <td>
                  <button
                    type="button"
                    class="catalog-status <?php echo $perfilActivo ? "is-active" : "is-inactive"; ?> btnActivar"
                    idPerfil="<?php echo $idPerfilVista; ?>"
                    estadoPerfil="<?php echo $perfilActivo ? "0" : "1"; ?>">
                    <i class="fas <?php echo $perfilActivo ? "fa-check-circle" : "fa-pause-circle"; ?>"></i>
                    <?php echo $perfilActivo ? "Activo" : "Inactivo"; ?>
                  </button>
                </td>
                <td>
                  <div class="btn-group">
                    <button
                      type="button"
                      class="btn catalog-action is-edit btnEditarPerfil"
                      idPerfil="<?php echo $idPerfilVista; ?>"
                      data-toggle="modal"
                      data-target="#modalEditarPerfil"
                      title="Editar perfil"
                      aria-label="Editar a <?php echo $escPerfil($nombrePerfil); ?>">
                      <i class="fas fa-pen"></i>
                    </button>
                    <button
                      type="button"
                      class="btn catalog-action is-delete btnEliminarPerfil"
                      idPerfil="<?php echo $idPerfilVista; ?>"
                      nombrePerfil="<?php echo $escPerfil($nombrePerfil); ?>"
                      fotoPerfil="<?php echo $escPerfil(isset($value["foto"]) ? $value["foto"] : ""); ?>"
                      title="Eliminar perfil"
                      aria-label="Eliminar a <?php echo $escPerfil($nombrePerfil); ?>">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- Modal: crear perfil -->
<div
  id="modalAgregarPerfil"
  class="modal fade admin-catalog-page"
  role="dialog"
  aria-labelledby="tituloAgregarPerfil"
  style="--catalog-accent:#6366f1;--catalog-accent-dark:#4f46e5;--catalog-accent-soft:#eef2ff;--catalog-accent-rgb:99,102,241;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content catalog-modal">
      <form role="form" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
          <h4 class="modal-title" id="tituloAgregarPerfil"><i class="fas fa-user-plus"></i> Crear perfil</h4>
        </div>

        <div class="modal-body">
          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-address-card"></i> Cuenta de acceso</h5>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="nuevoNombre">Nombre completo <span class="catalog-required">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="nuevoNombre" name="nuevoNombre" maxlength="120" placeholder="Ej. Daniela López" required>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="nuevoEmail">Correo electrónico <span class="catalog-required">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="nuevoEmail" name="nuevoEmail" maxlength="150" placeholder="persona@empresa.com" required>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="nuevoPassword">Contraseña <span class="catalog-required">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="nuevoPassword" name="nuevoPassword" placeholder="Ingresar contraseña" required>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="nuevoPasswordConfirmar">Confirmar contraseña <span class="catalog-required">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-shield-alt"></i></span>
                    <input type="password" class="form-control" id="nuevoPasswordConfirmar" name="nuevoPasswordConfirmar" placeholder="Repetir contraseña" required>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-sitemap"></i> Rol y asignación</h5>

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="nuevoPerfil">Rol <span class="catalog-required">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-users-cog"></i></span>
                    <select class="form-control" name="nuevoPerfil" id="nuevoPerfil" required>
                      <option value="">Seleccionar rol</option>
                      <?php if ($esSuperAdminPerfiles) { ?>
                        <option value="Super-Administrador">Superadministración</option>
                      <?php } ?>
                      <option value="administrador">Administración</option>
                      <option value="vendedor">Asesor de ventas</option>
                      <option value="tecnico">Técnico</option>
                      <option value="secretaria">Secretaría</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="nuevoDepartamento">Departamento</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-user-tag"></i></span>
                    <select class="form-control" name="Departamento" id="nuevoDepartamento">
                      <option value="">Sin departamento</option>
                      <option value="Ventas">Ventas</option>
                      <option value="Administracion">Administración</option>
                      <option value="Ventas Externas">Ventas externas</option>
                      <option value="Sistemas">Sistemas</option>
                      <option value="Electronica">Electrónica</option>
                      <option value="Impresoras">Impresoras</option>
                      <option value="Desarrollo">Desarrollo</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="empresaPerfil">Empresa <span class="catalog-required">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-building"></i></span>
                    <?php if ($esSuperAdminPerfiles) { ?>
                      <select class="form-control" id="empresaPerfil" name="empresa" required>
                        <option value="">Seleccionar empresa</option>
                        <?php foreach ($empresasPerfiles as $empresaOpcion) { ?>
                          <option value="<?php echo intval($empresaOpcion["id"]); ?>"><?php echo $escPerfil($empresaOpcion["empresa"]); ?></option>
                        <?php } ?>
                      </select>
                    <?php } else { ?>
                      <select class="form-control" id="empresaPerfilVista" disabled>
                        <option value="<?php echo $empresaSesionPerfiles; ?>"><?php echo $escPerfil(isset($empresasPerfilPorId[$empresaSesionPerfiles]) ? $empresasPerfilPorId[$empresaSesionPerfiles] : "Empresa actual"); ?></option>
                      </select>
                      <input type="hidden" name="empresa" value="<?php echo $empresaSesionPerfiles; ?>">
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>

            <div id="divAdicionalTecnico" class="catalog-dynamic-panel" style="display:none;">
              <h5 class="catalog-form-title"><i class="fas fa-tools"></i> Datos del técnico</h5>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="numeroTelTecnico">Teléfono principal</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fas fa-phone"></i></span>
                      <input type="tel" class="form-control" name="numeroTelTecnico" id="numeroTelTecnico" maxlength="25">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="numeroTelDosTecnico">Teléfono alterno</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fas fa-mobile-alt"></i></span>
                      <input type="tel" class="form-control" name="numeroTelDosTecnico" id="numeroTelDosTecnico" maxlength="25">
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="HoraDeComida">Horario de comida</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fas fa-utensils"></i></span>
                      <input type="text" class="form-control" name="HoraDeComida" id="HoraDeComida" maxlength="50" placeholder="Ej. 14:00 - 15:00">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="areratecnico">Área técnica</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fas fa-tools"></i></span>
                      <select class="form-control" name="areratecnico" id="areratecnico">
                        <option value="">Seleccionar área</option>
                        <option value="electronica">Electrónica</option>
                        <option value="impresoras">Impresoras</option>
                        <option value="sistemas">Sistemas</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div id="divAdicionalAsesor" class="catalog-dynamic-panel" style="display:none;">
              <h5 class="catalog-form-title"><i class="fas fa-handshake"></i> Datos del asesor</h5>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="nuevoNumeroUno">Teléfono principal</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fas fa-phone"></i></span>
                      <input type="tel" class="form-control" name="nuevoNumeroUno" id="nuevoNumeroUno" maxlength="25">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="nuevoNumeroDos">Teléfono alterno</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fas fa-mobile-alt"></i></span>
                      <input type="tel" class="form-control" name="nuevoNumeroDos" id="nuevoNumeroDos" maxlength="25">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-camera"></i> Fotografía</h5>
            <div class="catalog-upload">
              <input type="file" class="nuevaFoto" name="nuevaFoto" accept="image/jpeg,image/png">
              <i class="fas fa-cloud-upload-alt catalog-upload-icon"></i>
              <strong>Seleccionar foto de perfil</strong>
              <p class="catalog-field-help">JPG o PNG, máximo 2 MB.</p>
              <img loading="lazy" src="vistas/img/perfiles/default/anonymous.png" class="img-thumbnail previsualizar catalog-upload-preview" alt="">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary catalog-save-button"><i class="fas fa-save"></i> Guardar perfil</button>
        </div>

        <?php
        $crearPerfil = new ControladorAdministradores();
        $crearPerfil->ctrCrearPerfil();
        ?>
      </form>
    </div>
  </div>
</div>

<!-- Modal: editar perfil -->
<div
  id="modalEditarPerfil"
  class="modal fade admin-catalog-page"
  role="dialog"
  aria-labelledby="tituloEditarPerfil"
  style="--catalog-accent:#6366f1;--catalog-accent-dark:#4f46e5;--catalog-accent-soft:#eef2ff;--catalog-accent-rgb:99,102,241;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content catalog-modal">
      <form role="form" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
          <h4 class="modal-title" id="tituloEditarPerfil"><i class="fas fa-user-edit"></i> Editar perfil</h4>
        </div>

        <div class="modal-body">
          <input type="hidden" id="idPerfil" name="idPerfil">
          <input type="hidden" id="passwordActual" name="passwordActual">

          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-address-card"></i> Cuenta de acceso</h5>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="editarNombre">Nombre completo <span class="catalog-required">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="editarNombre" name="editarNombre" maxlength="120" required>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="editarEmail">Correo electrónico <span class="catalog-required">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="editarEmail" name="editarEmail" maxlength="150" required>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="editarPassword">Nueva contraseña</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="editarPassword" name="editarPassword" placeholder="Dejar vacío para conservarla">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="editarPasswordConfirmar">Confirmar nueva contraseña</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-shield-alt"></i></span>
                    <input type="password" class="form-control" id="editarPasswordConfirmar" name="editarPasswordConfirmar">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-sitemap"></i> Rol y asignación</h5>

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="editarPerfilSelect">Rol <span class="catalog-required">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-users-cog"></i></span>
                    <select class="form-control" name="editarPerfil" id="editarPerfilSelect" required>
                      <option value="">Seleccionar rol</option>
                      <?php if ($esSuperAdminPerfiles) { ?>
                        <option value="Super-Administrador">Superadministración</option>
                      <?php } ?>
                      <option value="administrador">Administración</option>
                      <option value="vendedor">Asesor de ventas</option>
                      <option value="tecnico">Técnico</option>
                      <option value="secretaria">Secretaría</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="editarDepartamento">Departamento</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-user-tag"></i></span>
                    <select class="form-control" name="Departamento" id="editarDepartamento">
                      <option value="">Sin departamento</option>
                      <option value="Ventas">Ventas</option>
                      <option value="Administracion">Administración</option>
                      <option value="Ventas Externas">Ventas externas</option>
                      <option value="Sistemas">Sistemas</option>
                      <option value="Electronica">Electrónica</option>
                      <option value="Impresoras">Impresoras</option>
                      <option value="Desarrollo">Desarrollo</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="<?php echo $esSuperAdminPerfiles ? "editarEmpresaPerfil" : "editarEmpresaPerfilVista"; ?>">Empresa <span class="catalog-required">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-building"></i></span>
                    <?php if ($esSuperAdminPerfiles) { ?>
                      <select class="form-control" id="editarEmpresaPerfil" name="empresa" required>
                        <option value="">Seleccionar empresa</option>
                        <?php foreach ($empresasPerfiles as $empresaOpcion) { ?>
                          <option value="<?php echo intval($empresaOpcion["id"]); ?>"><?php echo $escPerfil($empresaOpcion["empresa"]); ?></option>
                        <?php } ?>
                      </select>
                    <?php } else { ?>
                      <select class="form-control" id="editarEmpresaPerfilVista" disabled>
                        <option value="<?php echo $empresaSesionPerfiles; ?>"><?php echo $escPerfil(isset($empresasPerfilPorId[$empresaSesionPerfiles]) ? $empresasPerfilPorId[$empresaSesionPerfiles] : "Empresa actual"); ?></option>
                      </select>
                      <input type="hidden" id="editarEmpresaPerfil" name="empresa" value="<?php echo $empresaSesionPerfiles; ?>">
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>

            <div id="divAdicionalTecnicoEdit" class="catalog-dynamic-panel" style="display:none;">
              <h5 class="catalog-form-title"><i class="fas fa-tools"></i> Datos del técnico</h5>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="editarNumeroUnoTecnico">Teléfono principal</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fas fa-phone"></i></span>
                      <input type="tel" class="form-control" name="editarNumeroUnoTecnico" id="editarNumeroUnoTecnico" maxlength="25">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="editarTelefonoDosTecnico">Teléfono alterno</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fas fa-mobile-alt"></i></span>
                      <input type="tel" class="form-control" name="editarTelefonoDosTecnico" id="editarTelefonoDosTecnico" maxlength="25">
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="HoraDeComidaEditada">Horario de comida</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fas fa-utensils"></i></span>
                      <input type="text" class="form-control" name="HoraDeComidaEditada" id="HoraDeComidaEditada" maxlength="50">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="editarAreratecnico">Área técnica</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fas fa-tools"></i></span>
                      <select class="form-control" name="editarAreratecnico" id="editarAreratecnico">
                        <option value="">Seleccionar área</option>
                        <option value="electronica">Electrónica</option>
                        <option value="impresoras">Impresoras</option>
                        <option value="sistemas">Sistemas</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div id="divAdicionalAsesorEdit" class="catalog-dynamic-panel" style="display:none;">
              <h5 class="catalog-form-title"><i class="fas fa-handshake"></i> Datos del asesor</h5>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="editarNumeroUnoAsesor">Teléfono principal</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fas fa-phone"></i></span>
                      <input type="tel" class="form-control" name="editarNumeroUnoAsesor" id="editarNumeroUnoAsesor" maxlength="25">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="editarTelefonoDosAsesor">Teléfono alterno</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fas fa-mobile-alt"></i></span>
                      <input type="tel" class="form-control" name="editarTelefonoDosAsesor" id="editarTelefonoDosAsesor" maxlength="25">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-camera"></i> Fotografía</h5>
            <div class="catalog-upload">
              <input type="file" class="nuevaFoto" name="editarFoto" accept="image/jpeg,image/png">
              <i class="fas fa-cloud-upload-alt catalog-upload-icon"></i>
              <strong>Cambiar foto de perfil</strong>
              <p class="catalog-field-help">JPG o PNG, máximo 2 MB.</p>
              <img loading="lazy" src="vistas/img/perfiles/default/anonymous.png" class="img-thumbnail previsualizar catalog-upload-preview" alt="">
              <input type="hidden" name="fotoActual" id="fotoActual">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary catalog-save-button"><i class="fas fa-save"></i> Guardar cambios</button>
        </div>

        <?php
        $editarPerfil = new ControladorAdministradores();
        $editarPerfil->ctrEditarPerfil();
        ?>
      </form>
    </div>
  </div>
</div>

<?php
$eliminarPerfil = new ControladorAdministradores();
$eliminarPerfil->ctrEliminarPerfil();
?>
