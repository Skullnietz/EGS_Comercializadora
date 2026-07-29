<?php

if ($_SESSION["perfil"] !== "administrador" && $_SESSION["perfil"] !== "Super-Administrador") {
  echo '<script>window.location = "inicio";</script>';
  return;
}

$empresas = ControladorEmpresas::ctrMostrarEmpresas(null, null);
$empresas = is_array($empresas) ? $empresas : array();
$totalEmpresas = count($empresas);
$empresasConSitio = 0;
$empresasConFacebook = 0;
$empresasContactoCompleto = 0;

foreach ($empresas as $empresaResumen) {
  if (!empty($empresaResumen["Sitio"])) {
    $empresasConSitio++;
  }
  if (!empty($empresaResumen["Facebook"])) {
    $empresasConFacebook++;
  }
  if (!empty($empresaResumen["correo"]) && !empty($empresaResumen["telefono"]) && !empty($empresaResumen["direccion"])) {
    $empresasContactoCompleto++;
  }
}

$escEmpresa = function ($valor) {
  return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
};

$urlEmpresaSegura = function ($valor) {
  $valor = trim((string)$valor);

  if (!filter_var($valor, FILTER_VALIDATE_URL)) {
    return "";
  }

  $esquema = strtolower((string)parse_url($valor, PHP_URL_SCHEME));
  return in_array($esquema, array("http", "https"), true) ? $valor : "";
};
?>

<div
  class="content-wrapper admin-catalog-page"
  style="--catalog-accent:#059669;--catalog-accent-dark:#047857;--catalog-accent-soft:#d1fae5;--catalog-accent-rgb:5,150,105;">

  <section class="content-header">
    <h1>Gestión de empresas</h1>
    <p class="catalog-subtitle">Mantén actualizados los datos de contacto, ubicación, horarios y canales digitales.</p>

    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>
      <li class="active">Empresas</li>
    </ol>
  </section>

  <section class="content">
    <div class="catalog-summary">
      <div class="catalog-summary-card">
        <span class="catalog-summary-icon"><i class="fas fa-building"></i></span>
        <span>
          <strong class="catalog-summary-value"><?php echo $totalEmpresas; ?></strong>
          <span class="catalog-summary-label">Empresas registradas</span>
        </span>
      </div>

      <div class="catalog-summary-card is-success">
        <span class="catalog-summary-icon"><i class="fas fa-address-book"></i></span>
        <span>
          <strong class="catalog-summary-value"><?php echo $empresasContactoCompleto; ?></strong>
          <span class="catalog-summary-label">Contacto completo</span>
        </span>
      </div>

      <div class="catalog-summary-card is-info">
        <span class="catalog-summary-icon"><i class="fas fa-globe"></i></span>
        <span>
          <strong class="catalog-summary-value"><?php echo $empresasConSitio; ?></strong>
          <span class="catalog-summary-label">Con sitio web</span>
        </span>
      </div>

      <div class="catalog-summary-card">
        <span class="catalog-summary-icon"><i class="fab fa-facebook-f"></i></span>
        <span>
          <strong class="catalog-summary-value"><?php echo $empresasConFacebook; ?></strong>
          <span class="catalog-summary-label">Con Facebook</span>
        </span>
      </div>
    </div>

    <div class="box catalog-box">
      <div class="box-header catalog-box-header">
        <div class="catalog-box-title">
          <h3>Directorio de empresas</h3>
          <p>La información de esta tabla se usa en perfiles, técnicos y asesores.</p>
        </div>

        <div class="catalog-header-actions">
          <button type="button" class="btn catalog-primary-button" data-toggle="modal" data-target="#modalAgregarEmpresa">
            <i class="fas fa-plus"></i> Nueva empresa
          </button>
        </div>
      </div>

      <div class="box-body">
        <table class="table table-hover dt-responsive tablaEmpresas catalog-table" width="100%">
          <thead>
            <tr>
              <th style="width:40px">#</th>
              <th>Empresa</th>
              <th>Contacto</th>
              <th>Dirección</th>
              <th>Horario</th>
              <th>Canales digitales</th>
              <th style="width:90px">Acciones</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($empresas as $key => $value) {
              $idEmpresaVista = isset($value["id"]) ? intval($value["id"]) : 0;
              $nombreEmpresa = isset($value["empresa"]) ? $value["empresa"] : "Sin nombre";
              $correoEmpresa = isset($value["correo"]) ? $value["correo"] : "";
              $telefonoEmpresa = isset($value["telefono"]) ? $value["telefono"] : "";
              $telefonoDosEmpresa = isset($value["telefonoDos"]) ? $value["telefonoDos"] : "";
              $direccionEmpresa = isset($value["direccion"]) ? $value["direccion"] : "";
              $horarioEmpresa = isset($value["Horario"]) ? $value["Horario"] : "";
              $facebookEmpresa = isset($value["Facebook"]) ? $value["Facebook"] : "";
              $sitioEmpresa = isset($value["Sitio"]) ? $value["Sitio"] : "";
              $facebookSeguro = $urlEmpresaSegura($facebookEmpresa);
              $sitioSeguro = $urlEmpresaSegura($sitioEmpresa);
              $inicialesEmpresa = strtoupper(substr(trim($nombreEmpresa), 0, 2));
            ?>
              <tr>
                <td><?php echo $key + 1; ?></td>
                <td>
                  <div class="catalog-entity">
                    <span class="catalog-avatar"><?php echo $escEmpresa($inicialesEmpresa ?: "EM"); ?></span>
                    <span>
                      <span class="catalog-entity-name"><?php echo $escEmpresa($nombreEmpresa); ?></span>
                      <span class="catalog-entity-meta">ID <?php echo $idEmpresaVista; ?></span>
                    </span>
                  </div>
                </td>
                <td>
                  <span class="catalog-contact-line"><i class="fas fa-envelope"></i><?php echo $escEmpresa($correoEmpresa ?: "Sin correo"); ?></span>
                  <span class="catalog-contact-line"><i class="fas fa-phone"></i><?php echo $escEmpresa($telefonoEmpresa ?: "Sin teléfono"); ?></span>
                  <?php if ($telefonoDosEmpresa !== "") { ?>
                    <span class="catalog-contact-line"><i class="fas fa-mobile-alt"></i><?php echo $escEmpresa($telefonoDosEmpresa); ?></span>
                  <?php } ?>
                </td>
                <td>
                  <span class="catalog-contact-line"><i class="fas fa-map-marker-alt"></i><?php echo $escEmpresa($direccionEmpresa ?: "Sin dirección"); ?></span>
                </td>
                <td><span class="catalog-chip"><?php echo $escEmpresa($horarioEmpresa ?: "Sin horario"); ?></span></td>
                <td>
                  <?php if ($sitioSeguro !== "") { ?>
                    <a class="catalog-chip is-role" href="<?php echo $escEmpresa($sitioSeguro); ?>" target="_blank" rel="noopener noreferrer" title="Abrir sitio web">
                      <i class="fas fa-globe"></i> Sitio
                    </a>
                  <?php } else { ?>
                    <span class="catalog-chip"><i class="fas fa-globe"></i> Sin sitio</span>
                  <?php } ?>

                  <?php if ($facebookSeguro !== "") { ?>
                    <a class="catalog-chip is-company" href="<?php echo $escEmpresa($facebookSeguro); ?>" target="_blank" rel="noopener noreferrer" title="Abrir Facebook">
                      <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                  <?php } else { ?>
                    <span class="catalog-chip"><i class="fab fa-facebook-f"></i> Sin Facebook</span>
                  <?php } ?>
                </td>
                <td>
                  <div class="btn-group">
                    <button
                      type="button"
                      class="btn catalog-action is-edit btnEditarEmpresa"
                      idEmpresa="<?php echo $idEmpresaVista; ?>"
                      data-toggle="modal"
                      data-target="#modalAgregarEmpresaEditada"
                      title="Editar empresa"
                      aria-label="Editar <?php echo $escEmpresa($nombreEmpresa); ?>">
                      <i class="fas fa-pen"></i>
                    </button>
                    <button
                      type="button"
                      class="btn catalog-action is-delete btnEliminarEmpresa"
                      idEmpresa="<?php echo $idEmpresaVista; ?>"
                      nombreEmpresa="<?php echo $escEmpresa($nombreEmpresa); ?>"
                      title="Eliminar empresa"
                      aria-label="Eliminar <?php echo $escEmpresa($nombreEmpresa); ?>">
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

<!-- Modal: crear empresa -->
<div
  id="modalAgregarEmpresa"
  class="modal fade admin-catalog-page"
  style="--catalog-accent:#059669;--catalog-accent-dark:#047857;--catalog-accent-soft:#d1fae5;--catalog-accent-rgb:5,150,105;"
  role="dialog"
  aria-labelledby="tituloAgregarEmpresa">
  <div class="modal-dialog modal-lg">
    <div class="modal-content catalog-modal">
      <form role="form" method="post">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
          <h4 class="modal-title" id="tituloAgregarEmpresa"><i class="fas fa-building"></i> Nueva empresa</h4>
        </div>

        <div class="modal-body">
          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-id-card"></i> Información general</h5>
            <div class="row">
              <div class="form-group col-md-6">
                <label class="control-label" for="empresa">Nombre comercial <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-building"></i></span>
                  <input type="text" class="form-control" id="empresa" name="empresa" maxlength="150" required>
                </div>
              </div>

              <div class="form-group col-md-6">
                <label class="control-label" for="correo">Correo electrónico <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                  <input type="email" class="form-control" id="correo" name="correo" maxlength="160" required>
                </div>
              </div>

              <div class="form-group col-md-6">
                <label class="control-label" for="telefonoDeEmpresa">Teléfono principal <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-phone"></i></span>
                  <input type="tel" class="form-control" id="telefonoDeEmpresa" name="telefonoDeEmpresa" maxlength="25" required>
                </div>
              </div>

              <div class="form-group col-md-6">
                <label class="control-label" for="telefonoDosDeEmpresa">Teléfono secundario</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-mobile-alt"></i></span>
                  <input type="tel" class="form-control" id="telefonoDosDeEmpresa" name="telefonoDosDeEmpresa" maxlength="25">
                </div>
              </div>
            </div>
          </div>

          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-map-marked-alt"></i> Ubicación y atención</h5>
            <div class="row">
              <div class="form-group col-md-8">
                <label class="control-label" for="direccion">Dirección <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-map-marker-alt"></i></span>
                  <input type="text" class="form-control" id="direccion" name="direccion" maxlength="255" required>
                </div>
              </div>

              <div class="form-group col-md-4">
                <label class="control-label" for="Horario">Horario <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="far fa-clock"></i></span>
                  <input type="text" class="form-control" id="Horario" name="Horario" maxlength="120" placeholder="Lun–Vie 9:00–18:00" required>
                </div>
              </div>
            </div>
          </div>

          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-share-alt"></i> Canales digitales</h5>
            <div class="row">
              <div class="form-group col-md-6">
                <label class="control-label" for="Facebook">Página de Facebook</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fab fa-facebook-f"></i></span>
                  <input type="text" inputmode="url" class="form-control" id="Facebook" name="Facebook" maxlength="255" placeholder="https://facebook.com/...">
                </div>
              </div>

              <div class="form-group col-md-6">
                <label class="control-label" for="Sitio">Sitio web</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-globe"></i></span>
                  <input type="text" inputmode="url" class="form-control" id="Sitio" name="Sitio" maxlength="255" placeholder="https://...">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary catalog-save-button"><i class="fas fa-save"></i> Guardar empresa</button>
        </div>

        <?php
          $crearEmpresa = new ControladorEmpresas();
          $crearEmpresa->ctrCrearEmpresa();
        ?>
      </form>
    </div>
  </div>
</div>

<!-- Modal: editar empresa -->
<div
  id="modalAgregarEmpresaEditada"
  class="modal fade admin-catalog-page"
  style="--catalog-accent:#059669;--catalog-accent-dark:#047857;--catalog-accent-soft:#d1fae5;--catalog-accent-rgb:5,150,105;"
  role="dialog"
  aria-labelledby="tituloEditarEmpresa">
  <div class="modal-dialog modal-lg">
    <div class="modal-content catalog-modal">
      <form role="form" method="post">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
          <h4 class="modal-title" id="tituloEditarEmpresa"><i class="fas fa-edit"></i> Editar empresa</h4>
        </div>

        <div class="modal-body">
          <input type="hidden" id="idEmpresa" name="idEmpresa">

          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-id-card"></i> Información general</h5>
            <div class="row">
              <div class="form-group col-md-6">
                <label class="control-label" for="editarNombreEmpresa">Nombre comercial <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-building"></i></span>
                  <input type="text" class="form-control" id="editarNombreEmpresa" name="editarNombreEmpresa" maxlength="150" required>
                </div>
              </div>

              <div class="form-group col-md-6">
                <label class="control-label" for="editarCorreoEmpresa">Correo electrónico <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                  <input type="email" class="form-control" id="editarCorreoEmpresa" name="editarCorreoEmpresa" maxlength="160" required>
                </div>
              </div>

              <div class="form-group col-md-6">
                <label class="control-label" for="editarNumeroUnoDeEmpresa">Teléfono principal <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-phone"></i></span>
                  <input type="tel" class="form-control" id="editarNumeroUnoDeEmpresa" name="editarNumeroUnoDeEmpresa" maxlength="25" required>
                </div>
              </div>

              <div class="form-group col-md-6">
                <label class="control-label" for="telefonoDosDeEmpresaEditado">Teléfono secundario</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-mobile-alt"></i></span>
                  <input type="tel" class="form-control" id="telefonoDosDeEmpresaEditado" name="telefonoDosDeEmpresaEditado" maxlength="25">
                </div>
              </div>
            </div>
          </div>

          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-map-marked-alt"></i> Ubicación y atención</h5>
            <div class="row">
              <div class="form-group col-md-8">
                <label class="control-label" for="EditarDireccion">Dirección <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-map-marker-alt"></i></span>
                  <input type="text" class="form-control" id="EditarDireccion" name="EditarDireccion" maxlength="255" required>
                </div>
              </div>

              <div class="form-group col-md-4">
                <label class="control-label" for="HoraEditada">Horario <span class="catalog-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="far fa-clock"></i></span>
                  <input type="text" class="form-control" id="HoraEditada" name="HoraEditada" maxlength="120" required>
                </div>
              </div>
            </div>
          </div>

          <div class="catalog-form-section">
            <h5 class="catalog-form-title"><i class="fas fa-share-alt"></i> Canales digitales</h5>
            <div class="row">
              <div class="form-group col-md-6">
                <label class="control-label" for="FacebookEditado">Página de Facebook</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fab fa-facebook-f"></i></span>
                  <input type="text" inputmode="url" class="form-control" id="FacebookEditado" name="FacebookEditado" maxlength="255" placeholder="https://facebook.com/...">
                </div>
              </div>

              <div class="form-group col-md-6">
                <label class="control-label" for="SitioEditado">Sitio web</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-globe"></i></span>
                  <input type="text" inputmode="url" class="form-control" id="SitioEditado" name="SitioEditado" maxlength="255" placeholder="https://...">
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
          $editarEmpresa = new ControladorEmpresas();
          $editarEmpresa->ctrEditarEmpresa();
        ?>
      </form>
    </div>
  </div>
</div>

<?php
  $eliminarEmpresa = new ControladorEmpresas();
  $eliminarEmpresa->ctrEliminarEmpresa();
?>
