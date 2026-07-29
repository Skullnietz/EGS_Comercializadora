<?php

if ($_SESSION["perfil"] != "administrador" && $_SESSION["perfil"] != "Super-Administrador") {

  echo '<script>window.location = "inicio";</script>';

  return;
}

$esSuperAdministrador = $_SESSION["perfil"] === "Super-Administrador";
$empresaSesion = isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0;
$controladorEmpresasTecnicos = new ControladorEmpresas();

if ($esSuperAdministrador) {
  $empresasTecnicos = $controladorEmpresasTecnicos->ctrMostrarEmpresas(null, null);
  $tecnicos = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas(null, null, false);
} else {
  $empresaActualTecnicos = $controladorEmpresasTecnicos->ctrMostrarEmpresas("id", $empresaSesion);
  $empresasTecnicos = $empresaActualTecnicos ? array($empresaActualTecnicos) : array();
  $tecnicos = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas("id_empresa", $empresaSesion, false);
}

if (!is_array($empresasTecnicos)) {
  $empresasTecnicos = array();
}

if (!is_array($tecnicos)) {
  $tecnicos = array();
}

$empresasPorId = array();

foreach ($empresasTecnicos as $empresaTecnicos) {
  if (isset($empresaTecnicos["id"])) {
    $empresasPorId[intval($empresaTecnicos["id"])] = isset($empresaTecnicos["empresa"])
      ? $empresaTecnicos["empresa"]
      : "Empresa #" . intval($empresaTecnicos["id"]);
  }
}

$totalTecnicos = count($tecnicos);
$tecnicosActivos = 0;

foreach ($tecnicos as $tecnicoConteo) {
  if (isset($tecnicoConteo["estado"]) && strcasecmp($tecnicoConteo["estado"], "Activo") === 0) {
    $tecnicosActivos++;
  }
}

$tecnicosInactivos = $totalTecnicos - $tecnicosActivos;
$escTecnicos = function ($valor) {
  return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
};

$areasTecnicos = array(
  "electronica" => "Electrónica",
  "impresoras" => "Impresoras",
  "sistemas" => "Sistemas"
);

?>

<style>
  .tecnicos-page {
    --tec-primary: #0f766e;
    --tec-primary-dark: #115e59;
    --tec-ink: #172033;
    --tec-muted: #64748b;
    --tec-border: #e2e8f0;
    --tec-surface: #ffffff;
    --tec-bg: #f5f7fb;
  }

  .tecnicos-page .content-header {
    padding-bottom: 18px;
  }

  .tecnicos-page .content-header h1 {
    color: var(--tec-ink);
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -.3px;
    margin-bottom: 5px;
  }

  .tecnicos-page .page-subtitle {
    color: var(--tec-muted);
    font-size: 14px;
    margin: 0;
  }

  .tecnicos-page .tech-summary {
    display: grid;
    gap: 16px;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    margin-bottom: 18px;
  }

  .tecnicos-page .summary-card {
    align-items: center;
    background: var(--tec-surface);
    border: 1px solid var(--tec-border);
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(15, 23, 42, .04);
    display: flex;
    min-height: 92px;
    padding: 18px;
  }

  .tecnicos-page .summary-icon {
    align-items: center;
    background: #e6fffb;
    border-radius: 12px;
    color: var(--tec-primary);
    display: flex;
    font-size: 20px;
    height: 48px;
    justify-content: center;
    margin-right: 14px;
    width: 48px;
  }

  .tecnicos-page .summary-card.is-active .summary-icon {
    background: #ecfdf5;
    color: #059669;
  }

  .tecnicos-page .summary-card.is-inactive .summary-icon {
    background: #fff1f2;
    color: #e11d48;
  }

  .tecnicos-page .summary-card.is-company .summary-icon {
    background: #eef2ff;
    color: #4f46e5;
  }

  .tecnicos-page .summary-value {
    color: var(--tec-ink);
    display: block;
    font-size: 25px;
    font-weight: 750;
    line-height: 1;
  }

  .tecnicos-page .summary-label {
    color: var(--tec-muted);
    display: block;
    font-size: 12px;
    font-weight: 600;
    margin-top: 7px;
    text-transform: uppercase;
  }

  .tecnicos-page .tech-box {
    border: 0;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
    overflow: hidden;
  }

  .tecnicos-page .tech-box .box-header {
    align-items: center;
    border-bottom: 1px solid var(--tec-border);
    display: flex;
    justify-content: space-between;
    padding: 20px 22px;
  }

  .tecnicos-page .tech-box .box-header:before,
  .tecnicos-page .tech-box .box-header:after {
    content: none;
    display: none;
  }

  .tecnicos-page .box-title-wrap h3 {
    color: var(--tec-ink);
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 4px;
  }

  .tecnicos-page .box-title-wrap p {
    color: var(--tec-muted);
    font-size: 13px;
    margin: 0;
  }

  .tecnicos-page .btn-new-technician {
    background: var(--tec-primary);
    border: 0;
    border-radius: 9px;
    box-shadow: 0 5px 12px rgba(15, 118, 110, .2);
    color: #fff;
    font-weight: 650;
    padding: 10px 16px;
  }

  .tecnicos-page .btn-new-technician:hover,
  .tecnicos-page .btn-new-technician:focus {
    background: var(--tec-primary-dark);
    color: #fff;
  }

  .tecnicos-page .tech-box .box-body {
    padding: 20px 22px 24px;
  }

  .tecnicos-page .tablaTecnicos thead th {
    background: #f8fafc;
    border-bottom: 1px solid var(--tec-border);
    color: #475569;
    font-size: 11px;
    font-weight: 750;
    padding: 12px 10px;
    text-transform: uppercase;
    vertical-align: middle;
  }

  .tecnicos-page .tablaTecnicos tbody td {
    border-color: #edf2f7;
    color: #334155;
    padding: 13px 10px;
    vertical-align: middle;
  }

  .tecnicos-page .technician-cell {
    align-items: center;
    display: flex;
    min-width: 180px;
  }

  .tecnicos-page .technician-avatar {
    align-items: center;
    background: linear-gradient(135deg, #ccfbf1, #99f6e4);
    border-radius: 50%;
    color: var(--tec-primary-dark);
    display: flex;
    flex: 0 0 38px;
    font-size: 14px;
    font-weight: 800;
    height: 38px;
    justify-content: center;
    margin-right: 10px;
  }

  .tecnicos-page .technician-name {
    color: var(--tec-ink);
    display: block;
    font-weight: 700;
  }

  .tecnicos-page .technician-id {
    color: #94a3b8;
    display: block;
    font-size: 11px;
    margin-top: 2px;
  }

  .tecnicos-page .contact-line {
    display: block;
    font-size: 12px;
    margin-bottom: 3px;
    white-space: nowrap;
  }

  .tecnicos-page .contact-line i {
    color: #94a3b8;
    margin-right: 5px;
    text-align: center;
    width: 13px;
  }

  .tecnicos-page .company-chip,
  .tecnicos-page .area-chip,
  .tecnicos-page .status-chip {
    border-radius: 999px;
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    padding: 5px 10px;
    white-space: nowrap;
  }

  .tecnicos-page .company-chip {
    background: #eef2ff;
    color: #4338ca;
  }

  .tecnicos-page .company-chip.is-empty {
    background: #fff7ed;
    color: #c2410c;
  }

  .tecnicos-page .area-chip {
    background: #f1f5f9;
    color: #475569;
  }

  .tecnicos-page .status-chip.is-active {
    background: #dcfce7;
    color: #15803d;
  }

  .tecnicos-page .status-chip.is-inactive {
    background: #ffe4e6;
    color: #be123c;
  }

  .tecnicos-page .table-action {
    align-items: center;
    border: 0;
    border-radius: 8px !important;
    display: inline-flex;
    height: 34px;
    justify-content: center;
    margin: 0 2px;
    padding: 0;
    width: 34px;
  }

  .tecnicos-page .btn-edit-tech {
    background: #e0f2fe;
    color: #0369a1;
  }

  .tecnicos-page .btn-edit-tech:hover {
    background: #bae6fd;
    color: #075985;
  }

  .tecnicos-page .btn-delete-tech {
    background: #fff1f2;
    color: #be123c;
  }

  .tecnicos-page .btn-delete-tech:hover {
    background: #ffe4e6;
    color: #9f1239;
  }

  .tecnico-modal {
    border: 0;
    border-radius: 15px;
    box-shadow: 0 22px 55px rgba(15, 23, 42, .2);
    overflow: hidden;
  }

  .tecnico-modal .modal-header {
    background: linear-gradient(135deg, #0f766e, #0d9488);
    border: 0;
    color: #fff;
    padding: 20px 24px;
  }

  .tecnico-modal .modal-header .close {
    color: #fff;
    font-size: 28px;
    opacity: .85;
    text-shadow: none;
  }

  .tecnico-modal .modal-title {
    font-size: 20px;
    font-weight: 700;
  }

  .tecnico-modal .modal-title i {
    margin-right: 8px;
  }

  .tecnico-modal .modal-body {
    background: #f8fafc;
    padding: 22px 24px 8px;
  }

  .tecnico-modal .form-section {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin-bottom: 16px;
    padding: 18px 18px 4px;
  }

  .tecnico-modal .form-section-title {
    color: #172033;
    font-size: 14px;
    font-weight: 750;
    margin: 0 0 16px;
  }

  .tecnico-modal .form-section-title i {
    color: #0f766e;
    margin-right: 7px;
  }

  .tecnico-modal .control-label {
    color: #475569;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 7px;
  }

  .tecnico-modal .required-mark {
    color: #e11d48;
  }

  .tecnico-modal .input-group-addon {
    background: #f8fafc;
    border-color: #dbe3ec;
    color: #64748b;
    min-width: 42px;
  }

  .tecnico-modal .form-control {
    border-color: #dbe3ec;
    box-shadow: none;
    height: 42px;
  }

  .tecnico-modal .form-control:focus {
    border-color: #14b8a6;
    box-shadow: 0 0 0 3px rgba(20, 184, 166, .12);
  }

  .tecnico-modal .field-help {
    color: #94a3b8;
    font-size: 11px;
    margin: 6px 0 0;
  }

  .tecnico-modal .modal-footer {
    background: #fff;
    border-top: 1px solid #e2e8f0;
    padding: 16px 24px;
  }

  .tecnico-modal .modal-footer .btn {
    border-radius: 8px;
    font-weight: 650;
    padding: 9px 17px;
  }

  .tecnico-modal .btn-save-tech {
    background: #0f766e;
    border-color: #0f766e;
  }

  .tecnico-modal .btn-save-tech:hover,
  .tecnico-modal .btn-save-tech:focus {
    background: #115e59;
    border-color: #115e59;
  }

  @media (max-width: 991px) {
    .tecnicos-page .tech-summary {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 767px) {
    .tecnicos-page .tech-summary {
      grid-template-columns: 1fr;
    }

    .tecnicos-page .tech-box .box-header {
      align-items: stretch;
      flex-direction: column;
    }

    .tecnicos-page .btn-new-technician {
      margin-top: 14px;
      width: 100%;
    }
  }
</style>

<div class="content-wrapper tecnicos-page">

  <section class="content-header">
    <h1>Equipo técnico</h1>
    <p class="page-subtitle">Administra los datos, la disponibilidad y la empresa a la que pertenece cada técnico.</p>

    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>
      <li class="active">Técnicos</li>
    </ol>
  </section>

  <section class="content">

    <div class="tech-summary">
      <div class="summary-card">
        <span class="summary-icon"><i class="fas fa-users-cog"></i></span>
        <span>
          <strong class="summary-value"><?php echo $totalTecnicos; ?></strong>
          <span class="summary-label">Técnicos registrados</span>
        </span>
      </div>

      <div class="summary-card is-active">
        <span class="summary-icon"><i class="fas fa-user-check"></i></span>
        <span>
          <strong class="summary-value"><?php echo $tecnicosActivos; ?></strong>
          <span class="summary-label">Activos</span>
        </span>
      </div>

      <div class="summary-card is-inactive">
        <span class="summary-icon"><i class="fas fa-user-clock"></i></span>
        <span>
          <strong class="summary-value"><?php echo $tecnicosInactivos; ?></strong>
          <span class="summary-label">Inactivos</span>
        </span>
      </div>

      <div class="summary-card is-company">
        <span class="summary-icon"><i class="fas fa-building"></i></span>
        <span>
          <strong class="summary-value"><?php echo count($empresasTecnicos); ?></strong>
          <span class="summary-label"><?php echo $esSuperAdministrador ? "Empresas disponibles" : "Empresa asignada"; ?></span>
        </span>
      </div>
    </div>

    <div class="box tech-box">
      <div class="box-header with-border">
        <div class="box-title-wrap">
          <h3>Directorio de técnicos</h3>
          <p>Edita cualquier registro y controla dónde puede ser asignado.</p>
        </div>

        <button class="btn btn-new-technician" data-toggle="modal" data-target="#modalAgregarTecnico">
          <i class="fas fa-user-plus"></i> Nuevo técnico
        </button>
      </div>

      <div class="box-body">
        <table class="table table-hover dt-responsive tablaTecnicos" width="100%">
          <thead>
            <tr>
              <th style="width:40px">#</th>
              <th>Técnico</th>
              <th>Contacto</th>
              <th>Empresa</th>
              <th>Departamento</th>
              <th>Horario de comida</th>
              <th>Estado</th>
              <th>Alta</th>
              <th style="width:90px">Acciones</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($tecnicos as $key => $value) {
              $idTecnicoVista = isset($value["id"]) ? intval($value["id"]) : 0;
              $nombreTecnico = isset($value["nombre"]) ? $value["nombre"] : "Sin nombre";
              $correoTecnico = isset($value["correo"]) ? $value["correo"] : "";
              $telefonoTecnico = isset($value["telefono"]) ? $value["telefono"] : "";
              $telefonoDosTecnico = isset($value["telefonoDos"]) ? $value["telefonoDos"] : "";
              $idEmpresaTecnico = isset($value["id_empresa"]) ? intval($value["id_empresa"]) : 0;
              $nombreEmpresaTecnico = isset($empresasPorId[$idEmpresaTecnico])
                ? $empresasPorId[$idEmpresaTecnico]
                : ($idEmpresaTecnico > 0 ? "Empresa #" . $idEmpresaTecnico : "Sin empresa");
              $departamentoTecnico = isset($value["departamento"]) ? $value["departamento"] : "";
              $departamentoEtiqueta = isset($areasTecnicos[$departamentoTecnico])
                ? $areasTecnicos[$departamentoTecnico]
                : ($departamentoTecnico !== "" ? ucfirst($departamentoTecnico) : "Sin asignar");
              $horaComidaTecnico = !empty($value["HoraDeComida"]) ? $value["HoraDeComida"] : "Sin horario";
              $estadoTecnico = isset($value["estado"]) ? $value["estado"] : "Inactivo";
              $estaActivo = strcasecmp($estadoTecnico, "Activo") === 0;
              $fechaTecnico = isset($value["fecha"]) ? $value["fecha"] : "";
              $fechaOrden = $fechaTecnico;
              $fechaMostrar = $fechaTecnico !== "" ? $fechaTecnico : "—";
              $fechaTimestamp = $fechaTecnico !== "" ? strtotime($fechaTecnico) : false;

              if ($fechaTimestamp !== false) {
                $fechaMostrar = date("d/m/Y", $fechaTimestamp);
                $fechaOrden = date("Y-m-d H:i:s", $fechaTimestamp);
              }

              if (function_exists("mb_substr")) {
                $inicialTecnico = mb_strtoupper(mb_substr(trim($nombreTecnico), 0, 1, "UTF-8"), "UTF-8");
              } else {
                $inicialTecnico = strtoupper(substr(trim($nombreTecnico), 0, 1));
              }
            ?>
              <tr>
                <td><?php echo $key + 1; ?></td>
                <td>
                  <div class="technician-cell">
                    <span class="technician-avatar"><?php echo $escTecnicos($inicialTecnico); ?></span>
                    <span>
                      <span class="technician-name"><?php echo $escTecnicos($nombreTecnico); ?></span>
                      <span class="technician-id">ID <?php echo $idTecnicoVista; ?></span>
                    </span>
                  </div>
                </td>
                <td>
                  <span class="contact-line"><i class="fas fa-envelope"></i><?php echo $escTecnicos($correoTecnico ?: "Sin correo"); ?></span>
                  <span class="contact-line"><i class="fas fa-phone"></i><?php echo $escTecnicos($telefonoTecnico ?: "Sin teléfono"); ?></span>
                  <?php if ($telefonoDosTecnico !== "") { ?>
                    <span class="contact-line"><i class="fas fa-mobile-alt"></i><?php echo $escTecnicos($telefonoDosTecnico); ?></span>
                  <?php } ?>
                </td>
                <td>
                  <span class="company-chip<?php echo $idEmpresaTecnico > 0 ? "" : " is-empty"; ?>">
                    <i class="fas fa-building"></i> <?php echo $escTecnicos($nombreEmpresaTecnico); ?>
                  </span>
                </td>
                <td><span class="area-chip"><?php echo $escTecnicos($departamentoEtiqueta); ?></span></td>
                <td><?php echo $escTecnicos($horaComidaTecnico); ?></td>
                <td>
                  <span class="status-chip <?php echo $estaActivo ? "is-active" : "is-inactive"; ?>">
                    <i class="fas <?php echo $estaActivo ? "fa-check-circle" : "fa-pause-circle"; ?>"></i>
                    <?php echo $escTecnicos($estaActivo ? "Activo" : "Inactivo"); ?>
                  </span>
                </td>
                <td data-order="<?php echo $escTecnicos($fechaOrden); ?>"><?php echo $escTecnicos($fechaMostrar); ?></td>
                <td>
                  <div class="btn-group">
                    <button
                      type="button"
                      class="btn table-action btn-edit-tech btnEditarDatosTecnico"
                      idTecnico="<?php echo $idTecnicoVista; ?>"
                      data-toggle="modal"
                      data-target="#modalAgregarTecnicoEditado"
                      title="Editar técnico"
                      aria-label="Editar a <?php echo $escTecnicos($nombreTecnico); ?>">
                      <i class="fas fa-pen"></i>
                    </button>
                    <button
                      type="button"
                      class="btn table-action btn-delete-tech btnEliminarTecnico"
                      idTecnico="<?php echo $idTecnicoVista; ?>"
                      nombreTecnico="<?php echo $escTecnicos($nombreTecnico); ?>"
                      title="Eliminar técnico"
                      aria-label="Eliminar a <?php echo $escTecnicos($nombreTecnico); ?>">
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

<!-- Modal: agregar técnico -->
<div id="modalAgregarTecnico" class="modal fade" role="dialog" aria-labelledby="tituloAgregarTecnico">
  <div class="modal-dialog modal-lg">
    <div class="modal-content tecnico-modal">
      <form role="form" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
          <h4 class="modal-title" id="tituloAgregarTecnico"><i class="fas fa-user-plus"></i> Crear técnico</h4>
        </div>

        <div class="modal-body">
          <div class="form-section">
            <h5 class="form-section-title"><i class="fas fa-address-card"></i> Información de contacto</h5>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="NombreDelTecnico">Nombre completo <span class="required-mark">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="NombreDelTecnico" name="NombreDelTecnico" maxlength="120" placeholder="Ej. Carlos Hernández" autocomplete="name" required>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="Emailtecnico">Correo electrónico <span class="required-mark">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="Emailtecnico" name="Emailtecnico" maxlength="150" placeholder="tecnico@empresa.com" autocomplete="email" required>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="numeroTelTecnico">Teléfono principal <span class="required-mark">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-phone"></i></span>
                    <input type="tel" class="form-control" id="numeroTelTecnico" name="numeroTelTecnico" maxlength="25" inputmode="tel" placeholder="Ej. 55 1234 5678" autocomplete="tel" required>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="numeroTelDosTecnico">Teléfono alterno</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-mobile-alt"></i></span>
                    <input type="tel" class="form-control" id="numeroTelDosTecnico" name="numeroTelDosTecnico" maxlength="25" inputmode="tel" placeholder="Opcional" autocomplete="tel-national">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h5 class="form-section-title"><i class="fas fa-sitemap"></i> Asignación y disponibilidad</h5>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="empresaTecnico">Empresa a la que pertenece <span class="required-mark">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-building"></i></span>
                    <?php if ($esSuperAdministrador) { ?>
                      <select class="form-control" id="empresaTecnico" name="empresa" required>
                        <option value="">Seleccionar empresa</option>
                        <?php foreach ($empresasTecnicos as $empresaOpcion) { ?>
                          <option value="<?php echo intval($empresaOpcion["id"]); ?>"><?php echo $escTecnicos($empresaOpcion["empresa"]); ?></option>
                        <?php } ?>
                      </select>
                    <?php } else { ?>
                      <select class="form-control" id="empresaTecnicoVista" disabled>
                        <option value="<?php echo $empresaSesion; ?>"><?php echo $escTecnicos(isset($empresasPorId[$empresaSesion]) ? $empresasPorId[$empresaSesion] : "Empresa actual"); ?></option>
                      </select>
                      <input type="hidden" name="empresa" value="<?php echo $empresaSesion; ?>">
                    <?php } ?>
                  </div>
                  <p class="field-help">Define en qué empresa y órdenes estará disponible el técnico.</p>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="areratecnico">Departamento <span class="required-mark">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-tools"></i></span>
                    <select class="form-control" id="areratecnico" name="areratecnico" required>
                      <option value="">Seleccionar departamento</option>
                      <?php foreach ($areasTecnicos as $valorArea => $etiquetaArea) { ?>
                        <option value="<?php echo $escTecnicos($valorArea); ?>"><?php echo $escTecnicos($etiquetaArea); ?></option>
                      <?php } ?>
                    </select>
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
                    <input type="text" class="form-control" id="HoraDeComida" name="HoraDeComida" maxlength="50" placeholder="Ej. 14:00 - 15:00">
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="estadoTecnico">Estado inicial <span class="required-mark">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>
                    <select class="form-control" id="estadoTecnico" name="estadoTecnico" required>
                      <option value="Activo" selected>Activo</option>
                      <option value="Inactivo">Inactivo</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary btn-save-tech"><i class="fas fa-save"></i> Guardar técnico</button>
        </div>

        <?php
        $crearPerfilTecnico = new ControladorTecnicos();
        $crearPerfilTecnico->ctrCrearTecnico();
        ?>
      </form>
    </div>
  </div>
</div>

<!-- Modal: editar técnico -->
<div id="modalAgregarTecnicoEditado" class="modal fade" role="dialog" aria-labelledby="tituloEditarTecnico">
  <div class="modal-dialog modal-lg">
    <div class="modal-content tecnico-modal">
      <form role="form" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
          <h4 class="modal-title" id="tituloEditarTecnico"><i class="fas fa-user-edit"></i> Editar técnico</h4>
        </div>

        <div class="modal-body">
          <input type="hidden" id="idTecnico" name="idTecnico">

          <div class="form-section">
            <h5 class="form-section-title"><i class="fas fa-address-card"></i> Información de contacto</h5>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="editarNombreTecnico">Nombre completo <span class="required-mark">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="editarNombreTecnico" name="editarNombreTecnico" maxlength="120" autocomplete="name" required>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="editarEmailTecnico">Correo electrónico <span class="required-mark">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="editarEmailTecnico" name="editarEmailTecnico" maxlength="150" autocomplete="email" required>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="editarNumeroUnoTecnico">Teléfono principal <span class="required-mark">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-phone"></i></span>
                    <input type="tel" class="form-control" id="editarNumeroUnoTecnico" name="editarNumeroUnoTecnico" maxlength="25" inputmode="tel" autocomplete="tel" required>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="editarTelefonoDosTecnico">Teléfono alterno</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-mobile-alt"></i></span>
                    <input type="tel" class="form-control" id="editarTelefonoDosTecnico" name="editarTelefonoDosTecnico" maxlength="25" inputmode="tel" autocomplete="tel-national">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h5 class="form-section-title"><i class="fas fa-sitemap"></i> Asignación y disponibilidad</h5>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="<?php echo $esSuperAdministrador ? "editarEmpresaTecnico" : "editarEmpresaTecnicoVista"; ?>">Empresa a la que pertenece <span class="required-mark">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-building"></i></span>
                    <?php if ($esSuperAdministrador) { ?>
                      <select class="form-control" id="editarEmpresaTecnico" name="editarEmpresaTecnico" required>
                        <option value="">Seleccionar empresa</option>
                        <?php foreach ($empresasTecnicos as $empresaOpcion) { ?>
                          <option value="<?php echo intval($empresaOpcion["id"]); ?>"><?php echo $escTecnicos($empresaOpcion["empresa"]); ?></option>
                        <?php } ?>
                      </select>
                    <?php } else { ?>
                      <select class="form-control" id="editarEmpresaTecnicoVista" disabled>
                        <option value="<?php echo $empresaSesion; ?>"><?php echo $escTecnicos(isset($empresasPorId[$empresaSesion]) ? $empresasPorId[$empresaSesion] : "Empresa actual"); ?></option>
                      </select>
                      <input type="hidden" id="editarEmpresaTecnico" name="editarEmpresaTecnico" value="<?php echo $empresaSesion; ?>">
                    <?php } ?>
                  </div>
                  <p class="field-help">Al cambiarla, el técnico quedará disponible para la empresa seleccionada.</p>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="editarAreaTecnico">Departamento <span class="required-mark">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-tools"></i></span>
                    <select class="form-control" id="editarAreaTecnico" name="editarAreaTecnico" required>
                      <option value="">Seleccionar departamento</option>
                      <?php foreach ($areasTecnicos as $valorArea => $etiquetaArea) { ?>
                        <option value="<?php echo $escTecnicos($valorArea); ?>"><?php echo $escTecnicos($etiquetaArea); ?></option>
                      <?php } ?>
                    </select>
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
                    <input type="text" class="form-control" id="HoraDeComidaEditada" name="HoraDeComidaEditada" maxlength="50" placeholder="Ej. 14:00 - 15:00">
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="editarEstadoTecnico">Estado <span class="required-mark">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>
                    <select class="form-control" id="editarEstadoTecnico" name="estado" required>
                      <option value="Activo">Activo</option>
                      <option value="Inactivo">Inactivo</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary btn-save-tech"><i class="fas fa-save"></i> Guardar cambios</button>
        </div>

        <?php
        $editarTecnico = new ControladorTecnicos();
        $editarTecnico->ctrEditarTecnico();
        ?>
      </form>
    </div>
  </div>
</div>

<?php
$eliminarTecnico = new ControladorTecnicos();
$eliminarTecnico->ctrEliminarTecnico();
?>
