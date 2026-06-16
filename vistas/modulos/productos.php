<?php

if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"] != "vendedor"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}

require_once __DIR__ . '/../../config/InventarioHelper.php';
InventarioHelper::ensureConfigTable();
$resumenInventario = ControladorProductos::ctrResumenInventario($_SESSION["empresa"]);
$tipoCambioUsd = InventarioHelper::getTipoCambioUsd();
$esAdminInventario = ($_SESSION["perfil"] === "administrador" || $_SESSION["perfil"] === "Super-Administrador");
$codigoDeepLink = isset($_GET['codigo']) ? htmlspecialchars($_GET['codigo'], ENT_QUOTES, 'UTF-8') : '';
?>
<style>
  .alta-helper {
    border: 1px solid #dce7f3;
    border-radius: 12px;
    background: linear-gradient(145deg, #f4fbff 0%, #f6fff7 100%);
    padding: 12px;
    margin-bottom: 14px;
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
  }
  .alta-helper h5 {
    margin: 0 0 6px;
    font-weight: 700;
    color: #1d3550;
  }
  .alta-helper p {
    margin: 0 0 10px;
    color: #51657d;
    font-size: 12px;
  }
  .alta-templates {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 10px;
  }
  .alta-template-btn {
    border-radius: 999px;
    border: 1px solid #c8d9ea;
    background: #fff;
    color: #22415f;
    font-weight: 600;
    font-size: 12px;
    padding: 6px 12px;
  }
  .alta-template-btn:hover,
  .alta-template-btn.active {
    background: #1f8d61;
    border-color: #1f8d61;
    color: #fff;
  }
  .alta-progress {
    height: 8px;
    border-radius: 999px;
    background: #e5edf6;
    overflow: hidden;
    margin-bottom: 6px;
  }
  .alta-progress-bar {
    width: 0;
    height: 100%;
    background: linear-gradient(90deg, #1f8d61 0%, #3aaf73 100%);
    transition: width .25s ease;
  }
  .alta-progress-text {
    font-size: 12px;
    color: #40556d;
    margin-bottom: 10px;
    display: inline-block;
  }
  .alta-resumen {
    background: #fff;
    border: 1px solid #e3eaf3;
    border-radius: 10px;
    padding: 8px 10px;
    font-size: 12px;
    color: #1d2b3a;
  }
  .alta-resumen .row {
    margin: 0;
  }
  .alta-resumen .row + .row {
    margin-top: 3px;
  }
  .alta-checklist {
    margin-top: 10px;
    border-top: 1px solid #dbe6f1;
    padding-top: 10px;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
  }
  .alta-check-group {
    background: #fff;
    border: 1px solid #e1eaf3;
    border-radius: 8px;
    padding: 8px;
  }
  .alta-check-group h6 {
    margin: 0 0 6px;
    font-size: 11px;
    color: #334e68;
    font-weight: 700;
    text-transform: uppercase;
  }
  .alta-check-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #5c6f82;
    margin: 4px 0;
  }
  .alta-check-item .check-icon {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 1px solid #c7d6e6;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 700;
    background: #fff;
  }
  .alta-check-item.done {
    color: #226745;
  }
  .alta-check-item.done .check-icon {
    background: #1f8d61;
    border-color: #1f8d61;
    color: #fff;
  }
  .alta-kbd {
    border: 1px solid #cfd9e6;
    border-bottom-width: 2px;
    border-radius: 4px;
    background: #fff;
    padding: 1px 5px;
    font-size: 11px;
  }
  .alta-wizard-nav {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 8px;
    margin-bottom: 12px;
  }
  .alta-step-chip {
    border: 1px solid #d6e0ec;
    border-radius: 10px;
    background: #fff;
    color: #56708a;
    text-align: center;
    font-size: 12px;
    font-weight: 600;
    padding: 8px;
  }
  .alta-step-chip.active {
    background: #1f8d61;
    border-color: #1f8d61;
    color: #fff;
    box-shadow: 0 5px 12px rgba(31, 141, 97, .3);
  }
  .alta-step {
    display: none;
  }
  .alta-step.active {
    display: block;
    animation: altaStepIn .22s ease;
  }
  .alta-step-chip.done {
    border-color: #bfe3cf;
    background: #eff9f3;
    color: #1f8d61;
  }
  @keyframes altaStepIn {
    from {
      opacity: .15;
      transform: translateY(6px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  #modalAgregarProducto .modal-header,
  #modalEditarProducto .modal-header {
    background: linear-gradient(135deg, #6366f1, #818cf8) !important;
    border-radius: 4px 4px 0 0;
  }
  #modalAgregarProducto .alta-helper-compact,
  #modalEditarProducto .alta-helper-compact {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #f8fafc;
    padding: 12px 14px;
    margin-bottom: 14px;
  }
  #modalAgregarProducto .alta-helper-compact p,
  #modalEditarProducto .alta-helper-compact p {
    margin: 0 0 8px; font-size: 12px; color: #64748b;
  }
  #modalAgregarProducto .alta-guia-panel,
  #modalEditarProducto .alta-guia-panel {
    display: none;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px dashed #dbe3ef;
  }
  #modalAgregarProducto .alta-guia-panel.open,
  #modalEditarProducto .alta-guia-panel.open { display: block; }
  #modalAgregarProducto .alta-field-label,
  #modalEditarProducto .alta-field-label {
    display: block; font-size: 12px; font-weight: 700; color: #334155;
    margin: 0 0 6px;
  }
  #modalAgregarProducto .alta-field-hint,
  #modalEditarProducto .alta-field-hint {
    display: block; font-size: 11px; color: #94a3b8; margin: -2px 0 8px;
  }
  #modalAgregarProducto .alta-step-title,
  #modalEditarProducto .alta-step-title {
    font-size: 14px; font-weight: 800; color: #0f172a; margin: 0 0 12px;
  }
  #modalAgregarProducto .alta-collapsible,
  #modalEditarProducto .alta-collapsible { display: none; margin-top: 8px; }
  #modalAgregarProducto .alta-collapsible.open,
  #modalEditarProducto .alta-collapsible.open { display: block; }
  #modalAgregarProducto .btn-alta-toggle,
  #modalEditarProducto .btn-alta-toggle {
    border: 0; background: transparent; color: #6366f1;
    font-size: 12px; font-weight: 700; padding: 0; margin-top: 6px;
  }
  #modalAgregarProducto .rutaProducto-wrap,
  #modalEditarProducto .rutaProducto-wrap { display: none; }
  #modalAgregarProducto .form-control,
  #modalEditarProducto .form-control { border-radius: 8px; }
  #modalAgregarProducto .form-group:has(.rutaProducto),
  #modalEditarProducto .form-group:has(.rutaProducto),
  #modalEditarProducto .form-group:has(.empresa) { display: none !important; }
  #modalAgregarProducto .EntradaCodigo .alta-barcode-tools,
  #modalEditarProducto .EntradaCodigoEdit .alta-barcode-tools,
  #modalEditarProducto .EntradaCodigoEdit #qrEditWrap { display: none; }
  #modalAgregarProducto .EntradaCodigo.alta-barcode-open .alta-barcode-tools,
  #modalEditarProducto .EntradaCodigoEdit.alta-barcode-open .alta-barcode-tools,
  #modalEditarProducto .EntradaCodigoEdit.alta-barcode-open #qrEditWrap { display: block; margin-top: 10px; }
  #modalAgregarProducto .EntradaCodigo.alta-barcode-open .alta-barcode-tools .btn,
  #modalEditarProducto .EntradaCodigoEdit.alta-barcode-open .alta-barcode-tools .btn { margin-right: 6px; }
  #modalAgregarProducto .EntradaCodigo.alta-barcode-open #print,
  #modalEditarProducto .EntradaCodigoEdit.alta-barcode-open #printEdit { display: block; margin-top: 8px; }
  #modalAgregarProducto .alta-catalogo-opcional,
  #modalEditarProducto .alta-catalogo-opcional { display: none; margin-top: 8px; padding-top: 10px; border-top: 1px dashed #e2e8f0; }
  #modalAgregarProducto .alta-catalogo-opcional.open,
  #modalEditarProducto .alta-catalogo-opcional.open { display: block; }
</style>
<?php include __DIR__ . '/partials/productos-inventario-vista.php'; ?>
<!--=====================================
MODAL AGREGAR PRODUCTO
======================================-->
<div id="modalAgregarProducto" class="modal fade producto-wizard-modal" role="dialog">

  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <!-- <form role="form" method="post" enctype="multipart/form-data"> -->

      <!--=====================================
      CABEZA DEL MODAL
      ======================================-->
      <div class="modal-header" style="background:linear-gradient(135deg,#6366f1,#818cf8); color:white">

        <button type="button" class="close" data-dismiss="modal">&times;</button>

        <h4 class="modal-title"><i class="fa-solid fa-box"></i> Nuevo producto</h4>
        <p style="margin:4px 0 0;font-size:12px;opacity:.92">3 pasos simples · enfoque inventario</p>

      </div>

      <!--=====================================
      CUERPO DEL MODAL
      ======================================-->
      <div class="modal-body">

        <div class="box-body">

          <div class="alta-helper alta-helper-compact" id="altaHelperProducto">
            <p><strong>Inicio rápido:</strong> elija plantilla o complete el paso actual. Use <span class="alta-kbd">Ctrl+Enter</span> para guardar al final.</p>

            <div class="alta-templates">
              <button type="button" class="alta-template-btn" data-template="express">Express</button>
              <button type="button" class="alta-template-btn" data-template="laptop">Laptop</button>
              <button type="button" class="alta-template-btn" data-template="componente">Componente</button>
              <button type="button" class="alta-template-btn" data-template="servicio">Servicio</button>
            </div>

            <div class="alta-progress">
              <div class="alta-progress-bar" id="altaProgressBar"></div>
            </div>
            <span class="alta-progress-text" id="altaProgressText">Progreso: 0%</span>
            <button type="button" class="btn-alta-toggle btn-toggle-guia">Ver resumen</button>

            <div class="alta-guia-panel" id="altaGuiaPanel">
            <div class="alta-resumen" id="altaResumenProducto">
              <div class="row"><strong>Título:</strong> <span class="resumen-titulo-producto">Sin definir</span></div>
              <span class="resumen-ruta-producto" style="display:none"></span>
              <div class="row"><strong>Precio:</strong> <span class="resumen-precio-producto">0.00</span> MXN</div>
              <div class="row"><strong>Stock:</strong> <span class="resumen-disponibilidad-producto">0</span></div>
              <span class="resumen-proveedor-producto" style="display:none"></span>
            </div>
            </div>

            <div class="alta-checklist" id="altaChecklist" style="display:none">
              <div class="alta-check-group">
                <h6>Paso 1 Base</h6>
                <div class="alta-check-item" id="chkPaso1Titulo"><span class="check-icon">...</span><span>Titulo capturado</span></div>
                <div class="alta-check-item" id="chkPaso1Tipo"><span class="check-icon">...</span><span>Tipo definido</span></div>
                <div class="alta-check-item" id="chkPaso1Codigo"><span class="check-icon">...</span><span>Código asignado</span></div>
              </div>
              <div class="alta-check-group">
                <h6>Paso 2 Contenido</h6>
                <div class="alta-check-item" id="chkPaso2Categoria"><span class="check-icon">...</span><span>Categoria elegida</span></div>
                <div class="alta-check-item" id="chkPaso2Subcategoria"><span class="check-icon">...</span><span>Subcategoria elegida</span></div>
                <div class="alta-check-item" id="chkPaso2Contenido"><span class="check-icon">...</span><span>Descripcion y keywords</span></div>
              </div>
              <div class="alta-check-group">
                <h6>Paso 3 Precio</h6>
                <div class="alta-check-item" id="chkPaso3Precio"><span class="check-icon">...</span><span>Precio configurado</span></div>
                <div class="alta-check-item" id="chkPaso3Stock"><span class="check-icon">...</span><span>Disponibilidad configurada</span></div>
                <div class="alta-check-item" id="chkPaso3Proveedor"><span class="check-icon">...</span><span>Proveedor definido</span></div>
              </div>
            </div>
          </div>

          <div class="alta-wizard-nav" id="altaWizardNav">
            <div class="alta-step-chip active" data-wizard-chip="1">1. Identificación</div>
            <div class="alta-step-chip" data-wizard-chip="2">2. Catálogo</div>
            <div class="alta-step-chip" data-wizard-chip="3">3. Precio e inventario</div>
          </div>

          <div class="alta-step active" data-step="1">
          <h5 class="alta-step-title">1. Identificación del producto</h5>
          <p class="alta-field-hint" style="margin-bottom:12px">Tipo, nombre y código. El código puede generarse automáticamente.</p>
          <input type="hidden" class="id_empresa" value="<?php echo $_SESSION['empresa']?>">
          <input type="hidden" class="id_almacen" value="0">
          <!-- Tipo de producto -->
          <div class="form-group">
            <label class="alta-field-label">Tipo de producto <span style="color:#ef4444">*</span></label>
            <div class="input-group">
              <span class="input-group-addon"><i class="fas fa-bookmark"></i></span>
              <select class="form-control input-lg seleccionarTipo">
                <option value="">Seleccionar tipo</option>
                <option value="virtual">Servicio</option>
                <option value="fisico">Físico</option>
              </select>
            </div>
          </div>
          <!-- Nombre -->
          <div class="form-group">
            <label class="alta-field-label">Nombre del producto <span style="color:#ef4444">*</span></label>
            <div class="input-group">
              <span class="input-group-addon"><i class="fab fa-product-hunt"></i></span>
              <input type="text" class="form-control input-lg validarProducto tituloProducto" placeholder="Ej. Laptop Dell Latitude 5420">
            </div>
          </div>
          <!-- Ruta (oculta por CSS) -->
          <div class="form-group rutaProducto-wrap">
            <div class="input-group">
              <span class="input-group-addon"><i class="fas fa-link"></i></span>
              <input type="text" class="form-control input-lg rutaProducto" placeholder="Ruta url del producto" readonly>
            </div>
          </div>
          <!-- Código -->
          <div class="form-group EntradaCodigo">
            <label class="alta-field-label">Código / SKU <span style="color:#ef4444">*</span></label>
            <div class="input-group">
              <span class="input-group-addon"><i class="fas fa-barcode"></i></span>
              <input type="text" class="form-control input-lg SubircodigoProducto" id="codigoProducto" placeholder="Código de barras o SKU interno">
            </div>
            <div style="margin-top:8px;display:flex;flex-wrap:wrap;gap:8px;align-items:center">
              <button type="button" class="btn btn-default btn-sm" onclick="generarCodigoAuto()">Generar código</button>
              <button type="button" class="btn-alta-toggle btn-toggle-barcode">Ver código de barras / imprimir</button>
            </div>
            <div class="alta-barcode-tools">
            <button class="btn btn-success btn-sm botonGenerarCodigo" type="button" onclick="generarbarcode()">Generar</button>
            <button class="btn btn-info btn-sm botonImprimirCodigo" type="button" onclick="imprimir()">Imprimir</button>
            <div id="print"><svg id="barcode"></svg></div>
            </div>
          </div>

          </div>

          <div class="alta-step" data-step="2">
          <h5 class="alta-step-title">2. Catálogo <small style="font-weight:500;color:#94a3b8">— descripción opcional</small></h5>
          <p class="alta-field-hint" style="margin-bottom:10px">Solo categoría y subcategoría son obligatorias. Si omite descripción, se copia el nombre del producto.</p>
          <!--=====================================
          ENTRADA PARA AGREGAR MULTIMEDIA
          ======================================-->
          <div class="form-group agregarMultimedia">
            <!--=====================================
            SUBIR MULTIMEDIA DE PRODUCTO VIRTUAL
            =====================================
            <div class="input-group multimediaVirtual" style="display:none">

              <span class="input-group-addon"><i class="fas fa-youtube-play"></i></span>
              <input type="text" class="form-control input-lg multimedia" placeholder="Ingresar código video youtube">
            </div>
            </br>=-->
            <!--=====================================
            Campos para datos clientes
            ======================================-->
            <!-- ENTRADA PARA EL NOMBRE -->
            <div class="form-group agregarnombre" style="display:none">
              
              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-user"></i></span>

                <input type="text"  class="form-control input-lg nombre"  id="Nombre"  placeholder="Ingresa Nombre Del Cliente" required>

              </div>

            </div>
            <!-- ENTRADA PARA El  NUMERO TELEFONICO -->
            <div class="form-group entradatelefono"  style="display:none">

              <div class="input-group">
                
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span>
                <input type="tel" class="form-control input-lg numerotelcliente" name="numerotelcliente" placeholder="Ingresa Numero Telefonico 1" required>

              </div>

            </div>
            <!-- ENTRADA PARA El  NUMERO TELEFONICO DOS-->
            <div class="form-group entradatelefonoDos" style="display:none">

              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-headphones"></i></span>
                <input type="tel" class="form-control input-lg nuevoNumeroDosc" name="nuevoNumeroDosc" placeholder="Ingresa Numero Telefonico 2" required>

              </div>

            </div>
            <!-- ENTRADA PARA EL EMAIL -->
            <div class="form-group entradacorreo" style="display:none">

              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control input-lg entradaCorreoc" name="entradaCorreoc" placeholder="Ingresar Email deL Cliente" id="email del client" required>

              </div>

            </div>
            <!-- ENTRADA PARA DETALLES VENTAS -->
            <div class="form-group entradadetallesventas" style="display:none">

              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                <textarea type="text" maxlength="320" rows="3" class="form-control input-lg descripcionventas" placeholder="Ingresar detalles ventas"></textarea>

              </div>

            </div>

            <!-- ENTRADA PARA DETALLES VENTAS -->
            <div class="form-group entradadetallestecnicos" style="display:none">

              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                <textarea type="text" maxlength="320" rows="3" class="form-control input-lg descripciondetalles" placeholder="Ingresar detalles tecnicos"></textarea>

              </div>

            </div>
            <!--=====================================
            SUBIR MULTIMEDIA DE PRODUCTO FÍSICO
            ======================================-->
            <div class="multimediaFisica needsclick dz-clickable" style="display:none">

              <div class="dz-message needsclick">

                Arrastrar o dar click para subir imagenes.

              </div>

            </div>

          </div>
          <!--=====================================
          AGREGAR DETALLES VIRTUALES
          ======================================-->
          <div class="detallesVirtual" style="display:none">

            <div class="panel">DETALLES</div>

              <!-- Tipo de servicio -->
              <div class="form-group row">

                <div class="col-xs-3">

                  <input class="form-control input-lg" type="text" value="Servicio" readonly>

                </div>
                <div class="col-xs-9">
                  <!--<input class="form-control input-lg tagsInput detalleColor" data-role="tagsinput" type="text" placeholder="Descripción">-->

                </div>

              </div>

            </div>
            <!--=====================================
            AGREGAR DETALLES FÍSICOS
            ======================================-->
            <div class="detallesFisicos" style="display:none">

              <div class="panel">DETALLES</div>

              <!-- COLOR -->
              <div class="form-group row">

                <div class="col-xs-3">

                  <input class="form-control input-lg" type="text" value="Detalles" readonly>

                </div>
                <div class="col-xs-9">

                  <input class="form-control input-lg tagsInput detalleColor" data-role="tagsinput" type="text" placeholder="Separe valores con coma">

                </div>

              </div>

            </div>
            <!--=====================================
            AGREGAR CATEGORÍA
            ======================================--> 
            <label class="alta-field-label">Categoría <span style="color:#ef4444">*</span></label>
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-th"></i></span>
                <select class="form-control input-lg seleccionarCategoria">

                  <option value="">Selecionar categoría</option>
                  <?php

                    $item = null;
                    $valor = null;

                    $categorias = ControladorCategorias::ctrMostrarCategorias($item, $valor);

                    foreach ($categorias as $key => $value) {

                      echo '<option value="'.$value["id"].'">'.$value["categoria"].'</option>';
                    }

                  ?>
                </select>

              </div>
            
            </div>
            <!--=====================================
            AGREGAR SUBCATEGORÍA
            ======================================-->
            <label class="alta-field-label entradaSubcategoria-label" style="display:none">Subcategoría <span style="color:#ef4444">*</span></label>
            <div class="form-group  entradaSubcategoria" style="display:none">

              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-th"></i></span>
                <select class="form-control input-lg seleccionarSubCategoria">

                </select>

              </div>

            </div>
            <!--=====================================
            AGREGAR DESCRIPCIÓN
            ======================================-->
            <button type="button" class="btn-alta-toggle btn-toggle-catalogo-opcional">+ Descripción, palabras clave e imágenes (opcional)</button>
            <div class="alta-catalogo-opcional" id="altaCatalogoOpcional">
            <div class="form-group">

              <div class="input-group">

               <span class="input-group-addon"><i class="fas fa-pencil-alt"></i></span>
               <textarea type="text" maxlength="320" rows="3" class="form-control input-lg descripcionProducto" placeholder="Ingresar descripción producto"></textarea>

              </div>

            </div>
            <!--=====================================
            AGREGAR PALABRAS CLAVES
            ======================================-->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-key"></i></span>
                <input type="text" class="form-control input-lg tagsInput pClavesProducto" data-role="tagsinput"  placeholder="Ingresar palabras claves">

              </div>

            </div>
            <!--=====================================
            AGREGAR FOTO DE PORTADA
            ======================================-->
            <div class="form-group">

              <div class="panel">SUBIR FOTO PORTADA</div>

              <input type="file" class="fotoPortada">

              <p class="help-block">Tamaño recomendado 1280px * 720px <br> Peso máximo de la foto 2MB</p>

              <img loading="lazy" src="vistas/img/default/default.png" class="img-thumbnail previsualizarPortada" width="100%">

            </div>
            <!--=====================================
            AGREGAR FOTO DE MULTIMEDIA
            ======================================-->
            <div class="form-group">

              <div class="panel">SUBIR FOTO PRINCIPAL DEL PRODUCTO</div>

                <input type="file" class="fotoPrincipal">

                  <p class="help-block">Tamaño recomendado 400px * 450px <br> Peso máximo de la foto 2MB</p>
                  <img loading="lazy" src="vistas/img/default/default.png" class="img-thumbnail previsualizarPrincipal" width="200px">

              </div>
            </div>

                  </div>

                  <div class="alta-step" data-step="3">
              <h5 class="alta-step-title">3. Precio e inventario</h5>
              <!--=====================================
              AGREGAR PRECIO, PESO Y ENTREGA
              ======================================-->
              <div class="form-group row">

                <!-- PRECIO -->
                <div class="col-md-4 col-xs-12">

                  <div class="panel">PRECIO (MXN)</div>
                  <small class="text-muted" style="display:block;margin-bottom:6px">Referencia USD: <span id="refUsdAlta">—</span></small>

                  <div class="input-group">

                    <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>
                    <input type="number" class="form-control input-lg precio" min="0" step="any">

                  </div>

                </div>
                <!-- PESO -->
                <div class="col-md-4 col-xs-12">

                  <div class="panel">PESO</div>

                    <div class="input-group">

                      <span class="input-group-addon"><i class="fas fa-balance-scale"></i></span>
                      <input type="number" class="form-control input-lg peso" min="0" step="any" value="0">

                    </div>

                  </div>
                  <!-- ENTREGA -->
                  <div class="col-md-4 col-xs-12">

                    <div class="panel">DÍAS DE ENTREGA</div>

                    <div class="input-group">

                      <span class="input-group-addon"><i class="fas fa-truck"></i></span>
                      <input type="number" class="form-control input-lg entrega" min="0" value="0">

                    </div>

                  </div>

                </div>
                <div class="form-group row">
                  <!--=====================================
                  AGREGAR CANTIDAD DISPONIBLE              
                  ======================================-->
                  <div class="col-md-5 col-xs-12">
                    <br>
                    <div class="panel">CANTIDAD DISPONIBLE</div>

                      <div class="input-group">

                        <span class="input-group-addon"><i class="fas fa-industry"></i></span>
                        <input type="number" class="form-control input-lg disponibilidad" min="0" value="0">
                        <span class="input-group-addon">
                          <select class="seleccionarMedida">
                            <option>PZAS</option>
                                            <option>GRS</option>
                                            <option>KG</option>
                                            <option>cuartillo</option>
                                            <option>Tapa</option>
                                            <option>Caja</option>
                                            <option>lister</option>
                          </select>
                        </span>

                      </div>

                    </div>
                    <!--=====================================
                    AGREGAR PROVEEDOR
                    ======================================-->
                    <div class="col-md-4 col-xs-12">

                      <br>

                      <div class="panel">PROVEEDOR</div>

                        <div class="input-group">

                          <span class="input-group-addon"><i class="fas fa-building"></i></span>
                          <input type="text" class="form-control input-lg Proveedor"  placeholder="Proveedor"> 

                        </div>

                      </div>
                      <div class="col-md-4 col-xs12">

                        <br>

                        <div class="panel">Inversión</div>

                          <div class="input-group">

                            <span class="input-group-addon"><i class="fas fa-money-check-alt"></i></span>
                            <input type="number" class="form-control input-lg EntradInversion">

                          </div>

                        </div>

                      </div>
                      <!--=====================================
                      AGREGAR TIPO
                      ======================================-->
                      <div class="form-group">
                        
                        <select class="form-control input-lg selActivarTipo">
                          
                          <option>Escoger tipo</option>
                          <option value="caja">Caja</option>
                          <option>Lister</option>

                        </select>

                      </div>
                      <div class="datosTipo" style="display:none">

                        <div class="form-group row">

                          <div class="col-xs-6"> 
                            
                            <span class="input-group-addon"><i class="fas fa-cubes"></i></span>
                              <input class="form-control input-lg cantidadTipo" type="number" value="0"   min="0" placeholder="Cantidad"> 

                          </div>
                          
                        </div>

                      </div>

                        <!--=====================================
                        VALOR OFERTAS
                        ======================================-->

                      <!--=====================================
                      AGREGAR OFERTAS
                      ======================================-->
                      <div class="form-group">

                        <select class="form-control input-lg selActivarOferta">
                          
                          <option value="">No tiene oferta</option>
                          <option value="oferta">Activar oferta</option>

                        </select>
                      </div>
                      <div class="datosOferta" style="display:none">

                        <!--=====================================
                        VALOR OFERTAS
                        ======================================-->
                        <div class="form-group row"> 

                          <div class="col-xs-6"> 

                            <div class="input-group">

                              <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>
                              <input class="form-control input-lg valorOferta precioOferta" tipo="oferta" type="number" value="0"   min="0" placeholder="Precio"> 

                            </div>

                          </div>
                          <div class="col-xs-6">

                            <div class="input-group">

                              <input class="form-control input-lg valorOferta descuentoOferta" tipo="descuento" type="number" value="0"  min="0" placeholder="Descuento">

                               <span class="input-group-addon"><i class="fas fa-percent"></i></span>

                            </div>

                          </div>

                        </div>
                        <!--===================================== 
                        FECHA FINALIZACIÓN OFERTA
                        ======================================-->
                        <div class="form-group">

                          <div class="input-group date">

                            <input type='text' class="form-control datepicker input-lg valorOferta finOferta">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                            </span>

                          </div>

                        </div>
                        <!--=====================================
                        FOTO OFERTA
                        ======================================--> 
                        <div class="form-group">

                          <div class="panel">SUBIR FOTO OFERTA</div>

                            <input type="file" class="fotoOferta valorOferta">
                            <p class="help-block">Tamaño recomendado 640px * 430px <br> Peso máximo de la foto 2MB</p>

                            <img loading="lazy" src="vistas/img/ofertas/default/default.jpg" class="img-thumbnail previsualizarOferta" width="100px">

                          </div>

                          <div class="col-md-4 col-xs12">
                            
                            <br>
                            <div class="panel">Inversión</div> 

                              <div class="input-group">

                                <span class="input-group-addon"><i class="fas fa-money"></i></span>
                                <input type="number" class="form-control input-lg inversionEditada">

                              </div>

                            </div>

                          </div>

                        </div>

                      </div> 
                      </div>
                      <!--=====================================
                      PIE DEL MODAL
                      ======================================-->
                      <div class="modal-footer">

                        <div class="preload"></div>

                          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                          <button type="button" class="btn btn-info pull-left wizardPrev" style="display:none; margin-left:8px;">Anterior</button>
                          <button type="button" class="btn btn-primary wizardNext">Siguiente</button>
                          <button type="button" class="btn btn-primary guardarProducto wizardFinishBtn" style="display:none;">Guardar producto</button>

                        </div>

                      </form>

                    </div>

                  </div>

                </div>


                <!--=====================================
                MODAL EDITAR PRODUCTO
                ======================================-->
                <div id="modalEditarProducto" class="modal fade producto-wizard-modal" role="dialog">

                  <div class="modal-dialog modal-lg">

                    <div class="modal-content">

                      <div class="modal-header" style="background:linear-gradient(135deg,#6366f1,#818cf8); color:white">

                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                          <h4 class="modal-title"><i class="fa-solid fa-pen-to-square"></i> Editar producto</h4>
                          <p style="margin:4px 0 0;font-size:12px;opacity:.92">3 pasos simples · enfoque inventario</p>
                      </div>        
                      <!--=====================================
                      CUERPO DEL MODAL
                      ======================================-->
                      <div class="modal-body"> 

                        <div class="box-body">

                          <div class="alta-helper alta-helper-compact">
                            <p><strong>Edición guiada:</strong> revise identificación, catálogo y precio/stock.</p>
                            <div class="alta-progress"><div class="alta-progress-bar"></div></div>
                            <span class="alta-progress-text">Progreso: 0%</span>
                            <button type="button" class="btn-alta-toggle btn-toggle-guia">Ver resumen</button>
                            <div class="alta-guia-panel">
                              <div class="alta-resumen">
                                <div class="row"><strong>Título:</strong> <span class="resumen-titulo-producto">Sin definir</span></div>
                                <div class="row"><strong>Precio:</strong> <span class="resumen-precio-producto">0.00</span> MXN</div>
                                <div class="row"><strong>Stock:</strong> <span class="resumen-disponibilidad-producto">0</span></div>
                              </div>
                            </div>
                          </div>

                          <div class="alta-wizard-nav">
                            <div class="alta-step-chip active" data-wizard-chip="1">1. Identificación</div>
                            <div class="alta-step-chip" data-wizard-chip="2">2. Catálogo</div>
                            <div class="alta-step-chip" data-wizard-chip="3">3. Precio e inventario</div>
                          </div>

                          <div class="alta-step active" data-step="1">
                          <h5 class="alta-step-title">1. Identificación del producto</h5>
                          <p class="alta-field-hint" style="margin-bottom:12px">Nombre y tipo no se modifican. Puede actualizar el código.</p>

                          <!-- Empresa (oculta) -->
                         <div class="form-group"> 

                            <div class="input-group">

                              <span class="input-group-addon"><i class="fas fa-building"></i></span>
                                <?php

                                  $item = "id";
                                  $valor = $_SESSION["empresa"];

                                  $respuesta = ControladorEmpresas::ctrMostrarEmpresasParaEditar($item, $valor);

                                  //foreach ($respuesta as $key => $value){

                                    //echo '<option>'.$value["empresa"].'</option>'; 
                              

                                  //} 

                                 echo '<input type="hidden" value="'.$respuesta["empresa"].'" class="empresa">';
                                ?> 

                            </div>

                          </div>
                        <!--=====================================
                        ENTRADA PARA EL TÍTULO
                        ======================================-->
                        <label class="alta-field-label">Tipo</label>
                        <div class="form-group">
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-bookmark"></i></span>
                            <input type="text" class="form-control input-lg seleccionarTipo" readonly>
                          </div>
                        </div>
                        <label class="alta-field-label">Nombre del producto</label>
                        <div class="form-group">
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fab fa-product-hunt"></i></span>
                            <input type="text" class="form-control input-lg validarProducto tituloProducto" readonly>
                            <input type="hidden" class="idProducto">
                            <input type="hidden" class="idCabecera">
                          </div>
                        </div>
                        <div class="form-group rutaProducto-wrap">
                          <input type="text" class="form-control input-lg rutaProducto" readonly>
                        </div>
                        <!--=====================================
                        ENTRADA PARA LA EDICION DEL CODIGO DEL PRODUCTO
                        ======================================-->
                        <div class="form-group EntradaCodigoEdit">
                          <label class="alta-field-label">Código / SKU <span style="color:#ef4444">*</span></label>
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-barcode"></i></span>
                            <input type="text" class="form-control input-lg codigoEditado campoCodigoProducto" id="codigoProductoEditado" required>
                          </div>
                          <div style="margin-top:8px">
                            <button type="button" class="btn-alta-toggle btn-toggle-barcode">Ver código de barras / QR</button>
                          </div>
                          <div class="alta-barcode-tools">
                            <button class="btn btn-success btn-sm" type="button" onclick="generarbarcodeEditado()">Generar</button>
                            <button class="btn btn-info btn-sm" type="button" onclick="imprimirCodigoEditado()">Imprimir</button>
                            <div id="printEdit"><svg id="barcodeEdit"></svg></div>
                            <div id="qrEditWrap"><small class="text-muted">QR del producto:</small><div id="qrEditProducto" style="margin-top:8px"></div></div>
                          </div>
                        </div>

                          </div>

                          <div class="alta-step" data-step="2">
                          <h5 class="alta-step-title">2. Catálogo <small style="font-weight:500;color:#94a3b8">— descripción opcional</small></h5>
                          <p class="alta-field-hint" style="margin-bottom:10px">Solo categoría y subcategoría son obligatorias.</p>
                        <!--=====================================
                        ENTRADA PARA AGREGAR MULTIMEDIA
                        ======================================-->
                        <div class="form-group agregarMultimedia">

                          <!--=====================================
                          SUBIR MULTIMEDIA DE PRODUCTO VIRTUAL
                          ======================================-->
                          <div class="input-group multimediaVirtual" style="display:none">

                            <span class="input-group-addon"><i class="fas fa-youtube-play"></i></span>
                            <input type="text" class="form-control input-lg multimedia">

                          </div>
                          <!--===================================== 
                          SUBIR MULTIMEDIA DE PRODUCTO FÍSICO
                          ======================================-->
                          <div class="row previsualizarImgFisico"></div>

                            <div class="multimediaFisica needsclick dz-clickable" style="display:none">

                              <div class="dz-message needsclick">
                              Arrastrar o dar click para subir imagenes.
                              </div>

                            </div> 

                          </div>
                          <!--=====================================
                          AGREGAR DETALLES VIRTUALES
                          ======================================-->
                          <div class="detallesVirtual" style="display:none">

                            <div class="panel">DETALLES</div>
                            <!-- CLASES -->
                            <div class="form-group row">

                              <div class="col-xs-3"> 

                                <input class="form-control input-lg" type="text" value="Reparacion" readonly>

                              </div>
                              <div class="col-xs-9">

                                <input type="text" class="form-control input-lg detalleReparacion" placeholder="Descripción">

                              </div>

                            </div>

                          </div>
                          <!--=====================================
                          AGREGAR DETALLES FÍSICOS
                          ======================================-->
                          <div class="detallesFisicos" style="display:none">

                            <!-- COLOR -->
                            <div class="form-group row">

                              <div class="col-xs-3">

                                <input class="form-control input-lg" type="text" value="Detalles" readonly>

                              </div>

                              <div class="col-xs-9 editarColor">

                                <!--   <input class="form-control input-lg tagsInput detalleColor" data-role="tagsinput" type="text" placeholder="Separe valores con coma"> -->
                              </div>

                            </div> 
                          
                           </div>
                           <!--=====================================
                           AGREGAR CATEGORÍA 
                          ======================================--> 
                          <label class="alta-field-label">Categoría <span style="color:#ef4444">*</span></label>
                          <div class="form-group">

                            <div class="input-group"> 

                              <span class="input-group-addon"><i class="fas fa-th"></i></span>
                              <select class="form-control input-lg seleccionarCategoria">

                                <option class="optionEditarCategoria"></option>
                                <?php

                                  $item = null;
                                  $valor = null;

                                  $categorias = ControladorCategorias::ctrMostrarCategorias($item, $valor);

                                     foreach ($categorias as $key => $value) {  

                                      echo '<option value="'.$value["id"].'">'.$value["categoria"].'</option>';

                                    } 
                                ?>
                              </select>

                            </div>

                          </div>
                          <!--=====================================
                          AGREGAR SUBCATEGORÍA 
                          ======================================-->
                          <label class="alta-field-label">Subcategoría <span style="color:#ef4444">*</span></label>
                          <div class="form-group entradaSubcategoria">

                            <div class="input-group">

                              <span class="input-group-addon"><i class="fas fa-th"></i></span>
                              <select class="form-control input-lg seleccionarSubCategoria">

                                <option class="optionEditarSubCategoria"></option>

                              </select>

                            </div> 

                          </div>
                          <!--=====================================
                          AGREGAR DESCRIPCIÓN
                          ======================================-->
                          <button type="button" class="btn-alta-toggle btn-toggle-catalogo-opcional">+ Descripción, palabras clave e imágenes (opcional)</button>
                          <div class="alta-catalogo-opcional">
                          <div class="form-group">

                            <div class="input-group"> 

                              <span class="input-group-addon"><i class="fas fa-pencil"></i></span>
                              <textarea type="text" maxlength="320" rows="3" class="form-control input-lg descripcionProducto"></textarea>

                            </div> 

                          </div>
                          <!--=====================================
                          AGREGAR PALABRAS CLAVES
                          ======================================-->
                          <div class="form-group editarPalabrasClaves">

                            <!--   <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-key"></i></span>
                            <input type="text" class="form-control input-lg tagsInput pClavesProducto" data-role="tagsinput"  placeholder="Ingresar palabras claves">
                            </div> -->

                          </div> 
                          <!--=====================================
                          AGREGAR FOTO DE PORTADA
                          ======================================-->
                          <div class="form-group">

                            <div class="panel">SUBIR FOTO PORTADA</div>

                              <input type="file" class="fotoPortada"> 
                              <input type="hidden" class="antiguaFotoPortada">

                              <p class="help-block">Tamaño recomendado 1280px * 720px <br> Peso máximo de la foto 2MB</p>

                              <img loading="lazy" src="vistas/img/default/default.png" class="img-thumbnail previsualizarPortada" width="100%">

                            </div>
                            <!--=====================================
                            AGREGAR FOTO DE MULTIMEDIA 
                            ======================================-->
                            <div class="form-group">

                              <div class="panel">SUBIR FOTO PRINCIPAL DEL PRODUCTO</div>

                                <input type="file" class="fotoPrincipal">
                                <input type="hidden" class="antiguaFotoPrincipal">
                                <p class="help-block">Tamaño recomendado 400px * 450px <br> Peso máximo de la foto 2MB</p>

                                <img loading="lazy" src="vistas/img/default/default.png" class="img-thumbnail previsualizarPrincipal" width="200px">

                              </div>
                            </div>

                          </div>

                          <div class="alta-step" data-step="3">
                          <h5 class="alta-step-title">3. Precio e inventario</h5>
                              <!--=====================================
                              AGREGAR PRECIO, PESO Y ENTREGA
                              ======================================-->
                              <div class="form-group row">
                                <!-- PRECIO -->
                                <div class="col-md-4 col-xs-12">

                                  <div class="panel">PRECIO (MXN)</div>
                                  <small class="text-muted" style="display:block;margin-bottom:6px">Referencia USD: <span id="refUsdEdit">—</span></small>

                                    <div class="input-group">

                                      <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>
                                      <input type="number" class="form-control input-lg precio" min="0" step="any">

                                    </div>

                                  </div>
                                  <!-- PESO -->
                                  <div class="col-md-4 col-xs-12">

                                    <div class="panel">PESO</div>

                                      <div class="input-group"> 

                                        <span class="input-group-addon"><i class="fas fa-balance-scale"></i></span>

                                          <input type="number" class="form-control input-lg peso" min="0" step="any" value="0"> 

                                      </div>

                                    </div>
                                    <!-- ENTREGA -->
                                    <div class="col-md-4 col-xs-12">

                                      <div class="panel">DÍAS DE ENTREGA</div> 

                                        <div class="input-group">

                                          <span class="input-group-addon"><i class="fas fa-truck"></i></span>
                                          <input type="number" class="form-control input-lg entrega" min="0" value="0">

                                        </div>

                                      </div>

                                    </div>
                                    <!-- Disponibilidad -->
                                    <div class="col-md-5 col-xs-12">

                                      <br>

                                      <div class="panel">CANTIDAD DISPONIBLE</div>

                                      <div class="input-group">

                                        <span class="input-group-addon"><i class="fas fa-industry"></i></span>
                                        <input type="number" class="form-control input-lg disponibilidad" min="0" value="0">
                                        <span class="input-group-addon">
                                          <select class="medida">

                                            <option>PZAS</option>
                                            <option>GRS</option>
                                            <option>KG</option>
                                            <option>cuartillo</option>
                                            <option>Tapa</option>
                                            <option>Caja</option>
                                            <option>lister</option>
                                          
                                          </select>

                                        </span>

                                      </div>

                                    </div>
                                    <!--=====================================
                                    AGREGAR PROVEEDOR 
                                    ======================================-->
                                    <div class="col-md-4 col-xs-12">

                                      <br>
                                      <div class="panel">PROVEEDOR</div>

                                        <div class="input-group">

                                          <span class="input-group-addon"><i class="fas fa-building"></i></span>
                                          <input type="text" class="form-control input-lg Proveedor" id="Proveedor" placeholder="Proveedor">

                                        </div>
                                        </br></br>
                                      </div>
                                      <div class="col-md-4 col-xs12">
                                        
                                        <br>

                                        <div class="panel">Inversión</div>

                                          <div class="input-group">

                                            <span class="input-group-addon"><i class="fas fa-money"></i></span>
                                            <input type="number" class="form-control input-lg inversionEditada">

                                          </div>

                                        </div>
                                        <!--=====================================
                                        AGREGAR OFERTAS
                                        ======================================-->
                                        <div class="form-group">

                                          <select class="form-control input-lg selActivarOferta">

                                            <option value="">No tiene oferta</option> 
                                            <option value="oferta">Activar oferta</option>

                                          </select>

                                        </div>
                                        <div class="datosOferta" style="display:none">
                                        <!--=====================================
                                        VALOR OFERTAS
                                        ======================================-->
                                        <div class="form-group row">

                                          <div class="col-xs-6">

                                            <div class="input-group">

                                              <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>
                                              <input class="form-control input-lg valorOferta precioOferta" tipo="oferta" type="number" value="0" min="0" placeholder="Precio">

                                            </div> 

                                          </div>

                                        <div class="col-xs-6">

                                          <div class="input-group"> 

                                            <input class="form-control input-lg valorOferta descuentoOferta" tipo="descuento" type="number" value="0"  min="0" placeholder="Descuento">
                                            <span class="input-group-addon"><i class="fas fa-percent"></i></span>

                                          </div>

                                        </div> 

                                      </div> 
                                      <!--=====================================
                                      FECHA FINALIZACIÓN OFERTA
                                      ======================================-->
                                      <div class="form-group">

                                        <div class="input-group date">

                                          <input type='text' class="form-control datepicker input-lg valorOferta finOferta">
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                          </span>

                                        </div> 

                                      </div>
                                      <!--===================================== 
                                      FOTO OFERTA
                                      ======================================--> 
                                      <div class="form-group">

                                        <div class="panel">SUBIR FOTO OFERTA</div>

                                          <input type="file" class="fotoOferta valorOferta">
                                          <input type="hidden" class="antiguaFotoOferta">

                                          <p class="help-block">Tamaño recomendado 640px * 430px <br> Peso máximo de la foto 2MB</p>

                                          <img loading="lazy" src="vistas/img/ofertas/default/default.jpg" class="img-thumbnail previsualizarOferta" width="100px">

                                        </div> 

                                      </div>

                                    </div>

                                  </div>
                                  <!--=====================================
                                  PIE DEL MODAL
                                  ======================================-->
                                  <div class="modal-footer">

                                    <div class="preload"></div>

                                      <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                                      <button type="button" class="btn btn-info pull-left wizardPrev" style="display:none; margin-left:8px;">Anterior</button>
                                      <button type="button" class="btn btn-primary wizardNext">Siguiente</button>
                                      <button type="button" class="btn btn-primary guardarCambiosProducto wizardFinishBtn" style="display:none;">Guardar cambios</button>
                                  
                                    </div>

                                  </div> 

                                </div>

                              </div>

                              <?php  

                              $eliminarProducto = new ControladorProductos();  
                              $eliminarProducto -> ctrEliminarProducto();