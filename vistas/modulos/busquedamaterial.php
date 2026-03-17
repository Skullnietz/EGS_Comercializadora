<?php
if ($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"] != "vendedor") {
  echo '<script>window.location = "index.php?ruta=ordenes";</script>';
  return;
}
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css" />

<style>
  .content-header .breadcrumb {
    margin-bottom: 0;
  }

  .box-header h3 {
    margin: 0;
    font-weight: 700;
  }

  .table-responsive-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  #tablematerial thead th {
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 2;
    box-shadow: 0 1px 0 rgba(0, 0, 0, .06);
    white-space: nowrap;
  }

  #tablematerial.dataTable tbody tr td {
    vertical-align: middle;
  }

  .td-info {
    max-width: 360px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .td-orden {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Courier New", monospace;
  }

  .thumb {
    width: 92px;
    height: 64px;
    object-fit: cover;
    border-radius: .5rem;
    border: 1px solid rgba(0, 0, 0, .08);
    transition: transform .15s ease, box-shadow .15s ease;
    cursor: zoom-in;
  }

  .thumb:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, .12);
  }

  .badge {
    display: inline-block;
    padding: .35em .6em;
    border-radius: 999px;
    font-size: 0.95rem;
    font-weight: 600;
    border: 1px solid transparent;
    white-space: nowrap;
  }

  .badge-pendiente {
    color: #92400e;
    background: #fef3c7;
    border-color: #fde68a;
  }

  .badge-proceso {
    color: #1e3a8a;
    background: #dbeafe;
    border-color: #bfdbfe;
  }

  .badge-completada {
    color: #065f46;
    background: #d1fae5;
    border-color: #a7f3d0;
  }

  .badge-cancelada {
    color: #991b1b;
    background: #fee2e2;
    border-color: #fecaca;
  }

  .badge-otro {
    color: #374151;
    background: #f3f4f6;
    border-color: #e5e7eb;
  }

  /* Nuevos estados con colores específicos */
  .badge-aceptado {
    color: #155724;
    background: #d4edda;
    border-color: #c3e6cb;
  }

  /* Verde */
  .badge-pendiente-aut {
    color: #856404;
    background: #fff3cd;
    border-color: #ffeeba;
  }

  /* Amarillo/Naranja */
  .badge-producto-venta {
    color: #0c5460;
    background: #d1ecf1;
    border-color: #bee5eb;
  }

  /* Azul */
  .badge-terminado {
    color: #1b1e21;
    background: #d6d8d9;
    border-color: #c6c8ca;
  }

  /* Gris oscuro */
  .td-actions {
    white-space: nowrap;
    width: 1%;
  }

  table.dataTable.stripe tbody tr.odd,
  table.dataTable.display tbody tr.odd {
    background-color: #fafafa;
  }

  /* Fila de filtros */
  #tablematerial thead tr.filters th {
    background: #fafafa;
    position: sticky;
    top: 42px;
    z-index: 2;
  }

  #tablematerial thead tr.filters input,
  #tablematerial thead tr.filters select {
    width: 100%;
    box-sizing: border-box;
    padding: .35rem .5rem;
    font-size: 1.1rem;
  }

  /* Tooltips */
  [data-tooltip] {
    position: relative;
  }

  [data-tooltip]:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    left: 0;
    top: 100%;
    transform: translateY(6px);
    background: rgba(17, 24, 39, .95);
    color: #fff;
    font-size: .75rem;
    line-height: 1.2;
    padding: .4rem .55rem;
    border-radius: .375rem;
    max-width: 520px;
    white-space: normal;
    z-index: 99;
    box-shadow: 0 8px 16px rgba(0, 0, 0, .2);
  }

  /* Modal img */
  .img-modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
  }

  .img-modal.open {
    display: flex;
  }

  .img-modal img {
    max-width: 92vw;
    max-height: 86vh;
    border-radius: .75rem;
    box-shadow: 0 16px 48px rgba(0, 0, 0, .5);
  }

  .img-modal .close {
    position: absolute;
    top: 12px;
    right: 16px;
    font-size: 28px;
    color: #fff;
    cursor: pointer;
    padding: .25rem .5rem;
    line-height: 1;
    opacity: .9;
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <ol class="breadcrumb">
      <li><a href="#"><i class="fas fa-dashboard"></i></a></li>
      <li class="active">Búsqueda de Producto</li>
    </ol>
  </section>

  <div class="content">
    <div class="row" id="ORDEN">
      <div class="col-12">
        <div class="box box-success">
          <div class="box-header whith-border">
            <h3>Búsqueda de Producto</h3>
          </div>
          <div class="box-body">
            <div class="box table-responsive-wrap">

              <table id="tablematerial"
                class="table stripe ordenes order-table display compact cell-border hover row-border"
                style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Información</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Orden</th>
                    <th>Técnico</th>
                    <th>Imagen</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>Ingreso</th>
                    <th></th>
                  </tr>
                  <!-- Filtros por columna -->
                  <tr class="filters">
                    <th></th>
                    <th><input type="text" placeholder="Buscar información"></th>
                    <th><input type="text" placeholder="Buscar marca"></th>
                    <th><input type="text" placeholder="Buscar modelo"></th>
                    <th><input type="text" placeholder="Buscar orden"></th>
                    <th>
                      <select id="tecnicoFilter">
                        <option value="">Todos</option>
                        <!-- Se llena dinámicamente -->
                      </select>
                    </th>
                    <th></th>
                    <th>
                      <select id="estadoFilter">
                        <option value="">Todos</option>
                        <!-- Se llena dinámicamente -->
                      </select>
                    </th>
                    <th>
                      <div style="display:flex; gap:.25rem;">
                        <input type="number" step="0.01" id="minTotal" placeholder="Mín" style="width:50%;">
                        <input type="number" step="0.01" id="maxTotal" placeholder="Máx" style="width:50%;">
                      </div>
                    </th>
                    <th>
                      <div style="display:flex; gap:.25rem;">
                        <input type="date" id="minFecha" style="width:50%;">
                        <input type="date" id="maxFecha" style="width:50%;">
                      </div>
                    </th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $ordenes = ControladorOrdenes::ctrMostrarOrdenesMaterial();
                  foreach ($ordenes as $key => $value) {

                    // Técnico
                    $item = "id";
                    $valor = $value["id_tecnico"];
                    $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);
                    $NombreTecnico = $tecnico["nombre"];

                    // Enlace Info Orden
                    $InfoOrdenes = "<a href='index.php?ruta=infoOrden&idOrden=" . $value["id"] . "&empresa=" . $value["id_empresa"] . "&asesor=" . $value["id_Asesor"] . "&cliente=" . $value["id_usuario"] . "&tecnico=" . $value["id_tecnico"] . "&pedido=" . $value["id_pedido"] . "' class='btn btn-warning btnVerInfoOrden' idOrden='" . $value["id"] . "' cliente='" . $value["id_usuario"] . "'  tecnico='" . $value["id_tecnico"] . "' asesor='" . $value["id_Asesor"] . "' empresa='" . $value["id_empresa"] . "' pedido='" . $value["id_pedido"] . "' data-toggle='modal' target='_blank' title='Ver detalles de la orden'><i class='fas fa-eye'></i></a>";

                    // Marca / Modelo
                    $marca = isset($value["marcaDelEquipo"]) ? htmlspecialchars($value["marcaDelEquipo"]) : "";
                    $modelo = isset($value["modeloDelEquipo"]) ? htmlspecialchars($value["modeloDelEquipo"]) : "";

                    // Estado -> badge
                    $estadoText = (string) $value["estado"];
                    $estadoRaw = trim(mb_strtolower($estadoText, 'UTF-8'));
                    $estadoClass = 'badge-otro';

                    if (in_array($estadoRaw, ['pendiente', 'por asignar']))
                      $estadoClass = 'badge-pendiente';
                    elseif (in_array($estadoRaw, ['en proceso', 'proceso', 'atendiendo']))
                      $estadoClass = 'badge-proceso';
                    elseif (in_array($estadoRaw, ['completada', 'finalizada', 'cerrada']))
                      $estadoClass = 'badge-completada';
                    elseif (in_array($estadoRaw, ['cancelada', 'rechazada']))
                      $estadoClass = 'badge-cancelada';

                    // Mapeo específico solicitado
                    elseif ($estadoRaw == 'aceptado')
                      $estadoClass = 'badge-aceptado';
                    elseif (strpos($estadoRaw, 'pendiente de autorizacion') !== false || strpos($estadoRaw, 'pendiente de autorización') !== false)
                      $estadoClass = 'badge-pendiente-aut';
                    elseif ($estadoRaw == 'producto para venta')
                      $estadoClass = 'badge-producto-venta';
                    elseif ($estadoRaw == 'terminado')
                      $estadoClass = 'badge-terminado';

                    // Total + Fecha
                    $totalNum = (float) $value["total"];
                    $totalFmt = number_format($totalNum, 2, '.', ',');
                    $fechaIngreso = htmlspecialchars($value["fecha_ingreso"]); // ideal: YYYY-MM-DD
                  
                    // Primer thumbnail
                    $thumbHtml = '';
                    $AlbumDeImagenes = json_decode($value["multimedia"], true);
                    if (is_array($AlbumDeImagenes)) {
                      foreach ($AlbumDeImagenes as $k => $valueImagenes) {
                        if (!empty($valueImagenes["foto"])) {
                          $src = htmlspecialchars($valueImagenes["foto"]);
                          $thumbHtml = '<img class="thumb" src="' . $src . '" alt="Foto" data-full="' . $src . '" onclick="openImageModal(this)">';
                          break;
                        }
                      }
                    }

                    echo '<tr>';
                    echo '  <td>' . ($key + 1) . '</td>';
                    $info = htmlspecialchars($value["partidaUno"]);
                    echo '  <td class="td-info" data-tooltip="' . htmlspecialchars($value["partidaUno"]) . '">' . $info . '</td>';
                    echo '  <td>' . $marca . '</td>';
                    echo '  <td>' . $modelo . '</td>';
                    echo '  <td class="td-orden" data-orden="' . htmlspecialchars($value["id"]) . '">' . htmlspecialchars($value["id"]) . '</td>';
                    echo '  <td>' . htmlspecialchars($NombreTecnico) . '</td>';
                    echo '  <td>' . $thumbHtml . '</td>';
                    echo '  <td class="td-estado" data-estado="' . htmlspecialchars($estadoText) . '"><span class="badge ' . $estadoClass . '">' . htmlspecialchars($estadoText) . '</span></td>';
                    echo '  <td data-total="' . $totalNum . '">$ ' . $totalFmt . '</td>';
                    echo '  <td data-fecha="' . $fechaIngreso . '">' . $fechaIngreso . '</td>';
                    echo '  <td class="td-actions">' . $InfoOrdenes . '</td>';
                    echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>

              <!-- Modal imagen -->
              <div class="img-modal" id="imgModal" aria-hidden="true"
                onclick="if(event.target === this) closeImageModal()">
                <span class="close" id="imgModalClose" aria-label="Cerrar" onclick="closeImageModal()">&times;</span>
                <img id="imgModalImg" alt="Imagen">
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- jQuery (carga condicional) -->
<script>
  if (!window.jQuery) {
    var s = document.createElement('script');
    s.src = "https://code.jquery.com/jquery-3.7.1.min.js";
    document.head.appendChild(s);
  }
</script>

<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>

<script>
  // ---- Modal de imagen (Plain JS) ----
  function openImageModal(imgElement) {
    const modal = document.getElementById('imgModal');
    const modalImg = document.getElementById('imgModalImg');
    if (!modal || !modalImg) return;

    const fullSrc = imgElement.getAttribute('data-full') || imgElement.src;

    modalImg.src = fullSrc;
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
  }

  function closeImageModal() {
    const modal = document.getElementById('imgModal');
    const modalImg = document.getElementById('imgModalImg');
    if (!modal || !modalImg) return;

    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
    modalImg.src = '';
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      const modal = document.getElementById('imgModal');
      if (modal && modal.classList.contains('open')) {
        closeImageModal();
      }
    }
  });

  const fmtMXN = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 2 });

  // Filtro global extra: Total (rango) + Fecha (rango) + Estado (select)
  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    const $row = $('#tablematerial tbody tr').eq(dataIndex);

    // ----- Total (col 8) -----
    const minTotal = parseFloat($('#minTotal').val());
    const maxTotal = parseFloat($('#maxTotal').val());
    const totalCell = $row.find('td:eq(8)');
    const totalVal = parseFloat(totalCell.attr('data-total'));
    if (!isNaN(minTotal) && (isNaN(totalVal) || totalVal < minTotal)) return false;
    if (!isNaN(maxTotal) && (isNaN(totalVal) || totalVal > maxTotal)) return false;

    // ----- Fecha (col 9) -----
    const minFechaStr = $('#minFecha').val(); // yyyy-mm-dd
    const maxFechaStr = $('#maxFecha').val();
    const fechaCell = $row.find('td:eq(9)');
    const fechaStr = fechaCell.attr('data-fecha'); // ideal ISO
    if (fechaStr) {
      const f = new Date(fechaStr);
      if (minFechaStr) {
        const fMin = new Date(minFechaStr);
        if (isFinite(f) && isFinite(fMin) && f < fMin) return false;
      }
      if (maxFechaStr) {
        const fMax = new Date(maxFechaStr);
        if (isFinite(f) && isFinite(fMax)) {
          fMax.setHours(23, 59, 59, 999);
          if (f > fMax) return false;
        }
      }
    }

    // ----- Estado (col 7) -----
    const estadoSel = ($('#estadoFilter').val() || '').trim().toLowerCase();
    if (estadoSel) {
      const estadoCell = $row.find('td:eq(7)');
      const estadoTxt = (estadoCell.attr('data-estado') || '').trim().toLowerCase();
      if (estadoTxt !== estadoSel) return false;
    }

    return true;
  });

  $(document).ready(function () {

    var table = $('#tablematerial').DataTable({
      language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
      orderCellsTop: true,
      pageLength: 25,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
      autoWidth: false,
      columnDefs: [
        { targets: [0, 6, 10], orderable: false },  // #, Imagen, Acción
        { targets: [6, 10], searchable: false }    // Imagen, Acción
      ]
    });

    // ---- Búsquedas por columna (Información=1, Marca=2, Modelo=3, Orden=4) ----
    $('#tablematerial thead tr.filters th').each(function (i) {
      const $input = $(this).find('input[type="text"]');
      if ($input.length) {
        $input.on('keyup change', function () {
          if (table.column(i).search() !== this.value) {
            table.column(i).search(this.value).draw();
          }
        });
      }
    });

    // ---- Poblar el select de Estado (col 7) ----
    const estadosSet = new Set();
    table.column(7).data().each(function (val) {
      const div = document.createElement('div'); div.innerHTML = val;
      const txt = (div.textContent || div.innerText || '').trim();
      if (txt) estadosSet.add(txt);
    });
    const estados = Array.from(estadosSet).sort((a, b) => a.localeCompare(b, 'es'));
    const $estado = $('#estadoFilter');
    estados.forEach(e => {
      const opt = document.createElement('option');
      opt.value = e.toLowerCase();
      opt.textContent = e;
      $estado.append(opt);
    });

    // ---- Poblar el select de Técnico (col 5) ----
    const tecnicosSet = new Set();
    table.column(5).data().each(function (val) {
      const txt = (val || '').toString().trim();
      if (txt) tecnicosSet.add(txt);
    });
    const tecnicos = Array.from(tecnicosSet).sort((a, b) => a.localeCompare(b, 'es'));
    const $tecnico = $('#tecnicoFilter');
    tecnicos.forEach(t => {
      const opt = document.createElement('option');
      opt.value = t;
      opt.textContent = t;
      $tecnico.append(opt);
    });

    // ---- Listener técnico: filtro exacto por columna 5 ----
    $('#tecnicoFilter').on('change', function () {
      const val = this.value;
      if (val) {
        // Coincidencia exacta (regex) escapando caracteres especiales
        const esc = $.fn.dataTable.util.escapeRegex(val);
        table.column(5).search('^' + esc + '$', true, false).draw();
      } else {
        table.column(5).search('', true, false).draw();
      }
    });

    // ---- Listeners para filtros avanzados ----
    $('#estadoFilter, #minTotal, #maxTotal, #minFecha, #maxFecha').on('change keyup', function () {
      table.draw();
    });

    // ---- Re-formatea "Total" con Intl al dibujar ----
    const fmtMXN = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 2 });
    table.on('draw', function () {
      $('#tablematerial tbody tr td[data-total]').each(function () {
        var val = parseFloat($(this).attr('data-total'));
        if (!isNaN(val)) $(this).text(fmtMXN.format(val));
      });
    }).trigger('draw');

    // ---- Modal de imagen (ahora en Plain JS global) ----
  });
</script>