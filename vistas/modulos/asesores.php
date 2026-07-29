<?php

if ($_SESSION["perfil"] !== "administrador" && $_SESSION["perfil"] !== "Super-Administrador") {
  echo '<script>window.location = "inicio";</script>';
  return;
}

$esSuperAdminAsesores = $_SESSION["perfil"] === "Super-Administrador";
$empresaSesionAsesores = isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0;
$empresasAsesores = ControladorEmpresas::ctrMostrarEmpresasParaEditar(null, null);
$empresasAsesores = is_array($empresasAsesores) ? $empresasAsesores : array();
$empresasAsesorPorId = array();

foreach ($empresasAsesores as $empresaAsesor) {
  if (isset($empresaAsesor["id"])) {
    $empresasAsesorPorId[intval($empresaAsesor["id"])] = isset($empresaAsesor["empresa"])
      ? $empresaAsesor["empresa"]
      : "Empresa #" . intval($empresaAsesor["id"]);
  }
}

$asesores = $esSuperAdminAsesores
  ? Controladorasesores::ctrMostrarAsesoresEleg(null, null)
  : Controladorasesores::ctrMostrarAsesoresEmpresas("id_empresa", $empresaSesionAsesores, false);
$asesores = is_array($asesores) ? $asesores : array();

$totalAsesores = count($asesores);
$asesoresActivos = 0;
$asesoresInactivos = 0;
$sumaComisiones = 0;

foreach ($asesores as $asesorResumen) {
  if (isset($asesorResumen["estado"]) && $asesorResumen["estado"] === "Activo") {
    $asesoresActivos++;
  } else {
    $asesoresInactivos++;
  }

  $sumaComisiones += isset($asesorResumen["porcentajeComision"])
    ? floatval($asesorResumen["porcentajeComision"])
    : 0;
}

$promedioComision = $totalAsesores > 0 ? round($sumaComisiones / $totalAsesores, 1) : 0;
$escAsesor = function ($valor) {
  return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
};
?>

<div
  class="content-wrapper admin-catalog-page"
  style="--catalog-accent:#0284c7;--catalog-accent-dark:#0369a1;--catalog-accent-soft:#e0f2fe;--catalog-accent-rgb:2,132,199;">

  <section class="content-header">
    <h1>Gestión de asesores</h1>
    <p class="catalog-subtitle">Administra su información de contacto, comisión, estado y empresa asignada.</p>

    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>
      <li class="active">Asesores</li>
    </ol>
  </section>

  <section class="content">
    <div class="catalog-summary">
      <div class="catalog-summary-card">
        <span class="catalog-summary-icon"><i class="fas fa-user-tie"></i></span>
        <span>
          <strong class="catalog-summary-value"><?php echo $totalAsesores; ?></strong>
          <span class="catalog-summary-label">Asesores registrados</span>
        </span>
      </div>

      <div class="catalog-summary-card is-success">
        <span class="catalog-summary-icon"><i class="fas fa-user-check"></i></span>
        <span>
          <strong class="catalog-summary-value"><?php echo $asesoresActivos; ?></strong>
          <span class="catalog-summary-label">Activos</span>
        </span>
      </div>

      <div class="catalog-summary-card is-danger">
        <span class="catalog-summary-icon"><i class="fas fa-user-clock"></i></span>
        <span>
          <strong class="catalog-summary-value"><?php echo $asesoresInactivos; ?></strong>
          <span class="catalog-summary-label">Inactivos</span>
        </span>
      </div>

      <div class="catalog-summary-card is-info">
        <span class="catalog-summary-icon"><i class="fas fa-percentage"></i></span>
        <span>
          <strong class="catalog-summary-value"><?php echo $promedioComision; ?>%</strong>
          <span class="catalog-summary-label">Comisión promedio</span>
        </span>
      </div>
    </div>

    <div class="box catalog-box">
      <div class="box-header catalog-box-header">
        <div class="catalog-box-title">
          <h3>Directorio comercial</h3>
          <p>Consulta y actualiza la asignación de cada asesor.</p>
        </div>

        <div class="catalog-header-actions">
          <button type="button" class="btn catalog-secondary-button btnAbrirAgendarCitaAsesor">
            <i class="far fa-calendar-plus"></i> Agendar cita
          </button>
          <a href="index.php?ruta=pantallacitas" class="btn catalog-secondary-button">
            <i class="far fa-calendar-alt"></i> Ver calendario
          </a>
          <button type="button" class="btn catalog-primary-button" data-toggle="modal" data-target="#modalAgregarAsesor">
            <i class="fas fa-user-plus"></i> Nuevo asesor
          </button>
        </div>
      </div>

      <div class="box-body">
        <table class="table table-hover dt-responsive tablaAsesores catalog-table" width="100%">
          <thead>
            <tr>
              <th style="width:40px">#</th>
              <th>Asesor</th>
              <th>Contacto</th>
              <th>Empresa</th>
              <th>Comisión</th>
              <th>Estado</th>
              <th style="width:125px">Acciones</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($asesores as $key => $value) {
              $idAsesorVista = isset($value["id"]) ? intval($value["id"]) : 0;
              $nombreAsesor = isset($value["nombre"]) ? $value["nombre"] : "Sin nombre";
              $correoAsesor = isset($value["correo"]) ? $value["correo"] : "";
              $telefonoUnoAsesor = isset($value["numerodeCelular"]) ? $value["numerodeCelular"] : "";
              $telefonoDosAsesor = isset($value["numeroTelefono"]) ? $value["numeroTelefono"] : "";
              $idEmpresaAsesor = isset($value["id_empresa"]) ? intval($value["id_empresa"]) : 0;
              $empresaAsesorNombre = isset($empresasAsesorPorId[$idEmpresaAsesor])
                ? $empresasAsesorPorId[$idEmpresaAsesor]
                : ($idEmpresaAsesor > 0 ? "Empresa #" . $idEmpresaAsesor : "Sin empresa");
              $estadoAsesor = isset($value["estado"]) && $value["estado"] === "Activo" ? "Activo" : "Inactivo";
              $comisionAsesor = isset($value["porcentajeComision"]) ? floatval($value["porcentajeComision"]) : 0;
              $inicialesAsesor = strtoupper(substr(trim($nombreAsesor), 0, 2));
            ?>
              <tr>
                <td><?php echo $key + 1; ?></td>
                <td>
                  <div class="catalog-entity">
                    <span class="catalog-avatar"><?php echo $escAsesor($inicialesAsesor ?: "AS"); ?></span>
                    <span>
                      <span class="catalog-entity-name"><?php echo $escAsesor($nombreAsesor); ?></span>
                      <span class="catalog-entity-meta">ID <?php echo $idAsesorVista; ?></span>
                    </span>
                  </div>
                </td>
                <td>
                  <span class="catalog-contact-line"><i class="fas fa-envelope"></i><?php echo $escAsesor($correoAsesor ?: "Sin correo"); ?></span>
                  <span class="catalog-contact-line"><i class="fas fa-phone"></i><?php echo $escAsesor($telefonoUnoAsesor ?: "Sin teléfono"); ?></span>
                  <?php if ($telefonoDosAsesor !== "") { ?>
                    <span class="catalog-contact-line"><i class="fas fa-mobile-alt"></i><?php echo $escAsesor($telefonoDosAsesor); ?></span>
                  <?php } ?>
                </td>
                <td><span class="catalog-chip is-company"><i class="fas fa-building"></i> <?php echo $escAsesor($empresaAsesorNombre); ?></span></td>
                <td><span class="catalog-chip is-role"><?php echo $escAsesor($comisionAsesor); ?>%</span></td>
                <td>
                  <span class="catalog-status <?php echo $estadoAsesor === "Activo" ? "is-active" : "is-inactive"; ?>">
                    <i class="fas <?php echo $estadoAsesor === "Activo" ? "fa-check-circle" : "fa-pause-circle"; ?>"></i>
                    <?php echo $estadoAsesor; ?>
                  </span>
                </td>
                <td>
                  <div class="btn-group">
                    <button
                      type="button"
                      class="btn catalog-action is-calendar btnAgendarCitaAsesor"
                      data-nombre="<?php echo $escAsesor($nombreAsesor); ?>"
                      title="Agendar cita"
                      aria-label="Agendar cita con <?php echo $escAsesor($nombreAsesor); ?>">
                      <i class="far fa-calendar-plus"></i>
                    </button>
                    <button
                      type="button"
                      class="btn catalog-action is-edit btnEditarDatosAsesor"
                      idAsesor="<?php echo $idAsesorVista; ?>"
                      data-toggle="modal"
                      data-target="#modalAgregarAsesorEditado"
                      title="Editar asesor"
                      aria-label="Editar a <?php echo $escAsesor($nombreAsesor); ?>">
                      <i class="fas fa-pen"></i>
                    </button>
                    <button
                      type="button"
                      class="btn catalog-action is-delete btnEliminarAsesor"
                      idAsesor="<?php echo $idAsesorVista; ?>"
                      nombreAsesor="<?php echo $escAsesor($nombreAsesor); ?>"
                      title="Eliminar asesor"
                      aria-label="Eliminar a <?php echo $escAsesor($nombreAsesor); ?>">
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

<!-- Modal: crear asesor -->
<div
  id="modalAgregarAsesor"
  class="modal fade admin-catalog-page"
  style="--catalog-accent:#0284c7;--catalog-accent-dark:#0369a1;--catalog-accent-soft:#e0f2fe;--catalog-accent-rgb:2,132,199;"
  role="dialog"
  aria-labelledby="tituloAgregarAsesor">
  <div class="modal-dialog modal-lg">
    <div class="modal-content catalog-modal">
      <form role="form" method="post">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
          <h4 class="modal-title" id="tituloAgregarAsesor"><i class="fas fa-user-plus"></i> Nuevo asesor</h4>
        </div>

        <div class="modal-body">
          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-address-card"></i> Información de contacto</h5>
            <div class="row">
              <div class="form-group col-md-6">
                <label class="control-label" for="nuevoNombreAsesor">Nombre completo <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-user"></i></span>
                  <input type="text" class="form-control" id="nuevoNombreAsesor" name="nuevoNombreAsesor" maxlength="120" autocomplete="name" required>
                </div>
              </div>

              <div class="form-group col-md-6">
                <label class="control-label" for="nuevoEmailAsesor">Correo electrónico <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                  <input type="email" class="form-control" id="nuevoEmailAsesor" name="nuevoEmailAsesor" maxlength="160" autocomplete="email" required>
                </div>
              </div>

              <div class="form-group col-md-6">
                <label class="control-label" for="nuevoNumeroUno">Teléfono principal <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-phone"></i></span>
                  <input type="tel" class="form-control" id="nuevoNumeroUno" name="nuevoNumeroUno" maxlength="25" autocomplete="tel" required>
                </div>
              </div>

              <div class="form-group col-md-6">
                <label class="control-label" for="nuevoNumeroDos">Teléfono secundario</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-mobile-alt"></i></span>
                  <input type="tel" class="form-control" id="nuevoNumeroDos" name="nuevoNumeroDos" maxlength="25">
                </div>
              </div>
            </div>
          </div>

          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-briefcase"></i> Asignación comercial</h5>
            <div class="row">
              <div class="form-group col-md-4">
                <label class="control-label" for="<?php echo $esSuperAdminAsesores ? "empresaAsesor" : "empresaAsesorVista"; ?>">Empresa <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-building"></i></span>
                  <?php if ($esSuperAdminAsesores) { ?>
                    <select class="form-control" id="empresaAsesor" name="empresa" required>
                      <option value="">Seleccionar empresa</option>
                      <?php foreach ($empresasAsesores as $empresaAsesor) { ?>
                        <option value="<?php echo intval($empresaAsesor["id"]); ?>"><?php echo $escAsesor($empresaAsesor["empresa"]); ?></option>
                      <?php } ?>
                    </select>
                  <?php } else { ?>
                    <select class="form-control" id="empresaAsesorVista" disabled>
                      <option><?php echo $escAsesor(isset($empresasAsesorPorId[$empresaSesionAsesores]) ? $empresasAsesorPorId[$empresaSesionAsesores] : "Empresa asignada"); ?></option>
                    </select>
                    <input type="hidden" name="empresa" value="<?php echo $empresaSesionAsesores; ?>">
                  <?php } ?>
                </div>
              </div>

              <div class="form-group col-md-4">
                <label class="control-label" for="nuevoPorcentajeComision">Comisión (%) <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-percentage"></i></span>
                  <input type="number" class="form-control" id="nuevoPorcentajeComision" name="nuevoPorcentajeComision" min="0" max="100" value="0" required>
                </div>
              </div>

              <div class="form-group col-md-4">
                <label class="control-label" for="nuevoEstadoAsesor">Estado inicial <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>
                  <select class="form-control" id="nuevoEstadoAsesor" name="nuevoEstadoAsesor" required>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary catalog-save-button"><i class="fas fa-save"></i> Guardar asesor</button>
        </div>

        <?php
          $crearPerfilAsesor = new Controladorasesores();
          $crearPerfilAsesor->ctrCrearPerfil();
        ?>
      </form>
    </div>
  </div>
</div>

<!-- Modal: editar asesor -->
<div
  id="modalAgregarAsesorEditado"
  class="modal fade admin-catalog-page"
  style="--catalog-accent:#0284c7;--catalog-accent-dark:#0369a1;--catalog-accent-soft:#e0f2fe;--catalog-accent-rgb:2,132,199;"
  role="dialog"
  aria-labelledby="tituloEditarAsesor">
  <div class="modal-dialog modal-lg">
    <div class="modal-content catalog-modal">
      <form role="form" method="post">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
          <h4 class="modal-title" id="tituloEditarAsesor"><i class="fas fa-user-edit"></i> Editar asesor</h4>
        </div>

        <div class="modal-body">
          <input type="hidden" id="idAsesor" name="idAsesor">

          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-address-card"></i> Información de contacto</h5>
            <div class="row">
              <div class="form-group col-md-6">
                <label class="control-label" for="editarNombreAsesor">Nombre completo <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-user"></i></span>
                  <input type="text" class="form-control" id="editarNombreAsesor" name="editarNombreAsesor" maxlength="120" required>
                </div>
              </div>

              <div class="form-group col-md-6">
                <label class="control-label" for="editarEmailAsesor">Correo electrónico <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                  <input type="email" class="form-control" id="editarEmailAsesor" name="editarEmailAsesor" maxlength="160" required>
                </div>
              </div>

              <div class="form-group col-md-6">
                <label class="control-label" for="editarNumeroUno">Teléfono principal <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-phone"></i></span>
                  <input type="tel" class="form-control" id="editarNumeroUno" name="editarNumeroUno" maxlength="25" required>
                </div>
              </div>

              <div class="form-group col-md-6">
                <label class="control-label" for="editarTelefonoDos">Teléfono secundario</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-mobile-alt"></i></span>
                  <input type="tel" class="form-control" id="editarTelefonoDos" name="editarTelefonoDos" maxlength="25">
                </div>
              </div>
            </div>
          </div>

          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-briefcase"></i> Asignación comercial</h5>
            <div class="row">
              <div class="form-group col-md-4">
                <label class="control-label" for="<?php echo $esSuperAdminAsesores ? "editarEmpresaAsesor" : "editarEmpresaAsesorVista"; ?>">Empresa <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-building"></i></span>
                  <?php if ($esSuperAdminAsesores) { ?>
                    <select class="form-control" id="editarEmpresaAsesor" name="editarEmpresaAsesor" required>
                      <option value="">Seleccionar empresa</option>
                      <?php foreach ($empresasAsesores as $empresaAsesor) { ?>
                        <option value="<?php echo intval($empresaAsesor["id"]); ?>"><?php echo $escAsesor($empresaAsesor["empresa"]); ?></option>
                      <?php } ?>
                    </select>
                  <?php } else { ?>
                    <select class="form-control" id="editarEmpresaAsesorVista" disabled>
                      <option value="<?php echo $empresaSesionAsesores; ?>"><?php echo $escAsesor(isset($empresasAsesorPorId[$empresaSesionAsesores]) ? $empresasAsesorPorId[$empresaSesionAsesores] : "Empresa asignada"); ?></option>
                    </select>
                    <input type="hidden" id="editarEmpresaAsesor" name="editarEmpresaAsesor" value="<?php echo $empresaSesionAsesores; ?>">
                  <?php } ?>
                </div>
              </div>

              <div class="form-group col-md-4">
                <label class="control-label" for="editarPorcentajeComision">Comisión (%) <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-percentage"></i></span>
                  <input type="number" class="form-control" id="editarPorcentajeComision" name="editarPorcentajeComision" min="0" max="100" required>
                </div>
              </div>

              <div class="form-group col-md-4">
                <label class="control-label" for="editarEstadoAsesor">Estado <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>
                  <select class="form-control" id="editarEstadoAsesor" name="estado" required>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary catalog-save-button"><i class="fas fa-save"></i> Guardar cambios</button>
        </div>

        <?php
          $editarPerfilAsesor = new Controladorasesores();
          $editarPerfilAsesor->ctrEditarAsesor();
        ?>
      </form>
    </div>
  </div>
</div>

<?php
  $eliminarAsesor = new Controladorasesores();
  $eliminarAsesor->ctrEliminarAsesor();
?>
