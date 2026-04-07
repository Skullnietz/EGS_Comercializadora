<!-- ═══ Calendario Navbar Dropdown ═══ -->
<li class="dropdown egs-calendar-menu">
  <a href="#" class="dropdown-toggle egs-cal-trigger" data-toggle="dropdown" id="egsCalendarToggle" aria-expanded="false">
    <i class="fa-regular fa-calendar-days"></i>
    <span class="egs-cal-trigger-label">Agenda</span>
    <span class="egs-cal-badge" id="egsCalBadge" style="display:none;"></span>
  </a>

  <ul class="dropdown-menu egs-cal-dropdown">

    <!-- Header con tabs -->
    <li class="egs-cal-header">
      <div class="egs-cal-title">
        <i class="fa-solid fa-calendar-check"></i>
        <span>Mi Agenda</span>
      </div>
      <div class="egs-cal-tabs" role="tablist">
        <button class="egs-cal-tab active" data-rango="hoy">Hoy</button>
        <button class="egs-cal-tab" data-rango="semana">Semana</button>
        <button class="egs-cal-tab" data-rango="mes">Mes</button>
      </div>
    </li>

    <!-- Contenido de eventos -->
    <li>
      <div class="egs-cal-body" id="egsCalBody">
        <div class="egs-cal-loading">
          <i class="fa-solid fa-spinner fa-spin"></i> Cargando...
        </div>
      </div>
    </li>

    <!-- Footer -->
    <li class="egs-cal-footer">
      <a href="index.php?ruta=pantallacitas">
        <i class="fa-solid fa-expand"></i> Ver calendario completo
      </a>
      <a href="#" id="egsCalQuickAdd" class="egs-cal-add-btn">
        <i class="fa-solid fa-plus"></i> Nueva cita
      </a>
    </li>

  </ul>
</li>

<!-- ═══ Modal: Cita Rápida — se mueve a body via JS para evitar conflictos con dropdown ═══ -->
<div class="modal fade egs-modal-cita-rapida" id="modalCitaRapida" tabindex="-1" role="dialog" style="display:none;">
  <div class="modal-dialog" role="document" style="max-width:480px;margin:80px auto;">
    <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(15,23,42,.22);">

      <form id="formCitaRapida">
        <!-- Header -->
        <div style="background:linear-gradient(135deg,#6366f1,#8b5cf6);padding:20px 24px;color:#fff;">
          <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.8;text-shadow:none;font-size:22px;">&times;</button>
          <h4 style="margin:0;font-size:17px;font-weight:700;">
            <i class="fa-regular fa-calendar-plus" style="margin-right:8px;"></i>Agendar Cita Rápida
          </h4>
          <p style="margin:6px 0 0;font-size:12px;opacity:.8;">Selecciona cuándo y agrega los detalles</p>
        </div>

        <!-- Body -->
        <div style="padding:20px 24px;">

          <!-- Título -->
          <div class="form-group" style="margin-bottom:16px;">
            <label style="font-size:12px;font-weight:600;color:#475569;margin-bottom:6px;display:block;">Título de la cita</label>
            <input type="text" class="form-control" id="crTitulo" placeholder="Ej: Revisión equipo cliente" required
              style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 12px;font-size:13px;transition:border-color .2s;">
          </div>

          <!-- No. de Orden o Pedido (opcional) -->
          <div class="form-group" style="margin-bottom:16px;">
            <label style="font-size:12px;font-weight:600;color:#475569;margin-bottom:6px;display:block;">
              <i class="fa-solid fa-file-lines" style="margin-right:4px;color:#6366f1;"></i>No. de Orden o Pedido
            </label>
            <input type="number" class="form-control" id="crOrdenId" placeholder="Ingresa el ID de la orden o pedido (opcional)" min="1"
              style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 12px;font-size:13px;transition:border-color .2s;">
            <div id="crOrdenPreview" style="display:none;margin-top:6px;padding:6px 10px;border-radius:8px;font-size:11px;"></div>
          </div>

          <!-- Comentarios / Observaciones -->
          <div class="form-group" style="margin-bottom:16px;">
            <label style="font-size:12px;font-weight:600;color:#475569;margin-bottom:6px;display:block;">
              <i class="fa-solid fa-comment-dots" style="margin-right:4px;color:#6366f1;"></i>Comentarios / Observaciones
            </label>
            <textarea class="form-control" id="crDescripcion" rows="2"
              placeholder="¿De qué trata la cita? Ej: Revisión de pantalla, entrega de equipo..."
              style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 12px;font-size:13px;resize:vertical;min-height:50px;transition:border-color .2s;"></textarea>
            <small style="font-size:10px;color:#94a3b8;margin-top:4px;display:block;">Opcional — Aparecerá al consultar la cita</small>
          </div>

          <!-- Accesos rápidos de fecha -->
          <div class="form-group" style="margin-bottom:16px;">
            <label style="font-size:12px;font-weight:600;color:#475569;margin-bottom:8px;display:block;">
              <i class="fa-regular fa-clock" style="margin-right:4px;color:#6366f1;"></i>¿Cuándo?
            </label>
            <div class="egs-quick-dates" id="egsQuickDates">
              <button type="button" class="egs-qd-chip active" data-fecha="hoy">
                <i class="fa-solid fa-sun"></i> Hoy
              </button>
              <button type="button" class="egs-qd-chip" data-fecha="manana">
                <i class="fa-solid fa-forward"></i> Mañana
              </button>
              <button type="button" class="egs-qd-chip" data-fecha="lunes">
                <i class="fa-regular fa-calendar"></i> Lunes
              </button>
              <button type="button" class="egs-qd-chip" data-fecha="martes">
                <i class="fa-regular fa-calendar"></i> Martes
              </button>
              <button type="button" class="egs-qd-chip" data-fecha="miercoles">
                <i class="fa-regular fa-calendar"></i> Miércoles
              </button>
              <button type="button" class="egs-qd-chip" data-fecha="jueves">
                <i class="fa-regular fa-calendar"></i> Jueves
              </button>
              <button type="button" class="egs-qd-chip" data-fecha="viernes">
                <i class="fa-regular fa-calendar"></i> Viernes
              </button>
              <button type="button" class="egs-qd-chip" data-fecha="sabado">
                <i class="fa-regular fa-calendar"></i> Sábado
              </button>
              <button type="button" class="egs-qd-chip egs-qd-custom" data-fecha="personalizado">
                <i class="fa-solid fa-pen"></i> Elegir fecha
              </button>
            </div>

            <!-- Input de fecha personalizada (oculto por defecto) -->
            <div id="egsCustomDateWrap" style="display:none;margin-top:10px;">
              <input type="date" class="form-control" id="crFechaCustom"
                style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 12px;font-size:13px;">
            </div>

            <!-- Preview de fecha seleccionada -->
            <div id="egsDatePreview" style="margin-top:8px;padding:8px 12px;background:#f1f5f9;border-radius:8px;font-size:12px;color:#475569;display:flex;align-items:center;gap:6px;">
              <i class="fa-regular fa-calendar-check" style="color:#6366f1;"></i>
              <span id="egsDatePreviewText">Hoy — <?php echo date('l j \d\e F', strtotime('today')); ?></span>
            </div>
          </div>

          <!-- Hora (se genera dinámicamente según el día) -->
          <div class="form-group" style="margin-bottom:16px;">
            <label style="font-size:12px;font-weight:600;color:#475569;margin-bottom:8px;display:block;">
              <i class="fa-regular fa-clock" style="margin-right:4px;color:#6366f1;"></i>Hora
            </label>
            <div id="egsHorarioInfo" style="margin-bottom:8px;padding:6px 10px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;font-size:11px;color:#92400e;display:flex;align-items:center;gap:6px;">
              <i class="fa-solid fa-info-circle"></i>
              <span id="egsHorarioTexto">L-V: 10:00–14:00 y 16:00–18:30</span>
            </div>
            <div class="egs-time-grid" id="egsTimeGrid">
              <!-- Se genera por JS -->
            </div>
          </div>

          <!-- Color automático basado en calificación del cliente -->
          <div class="form-group" style="margin-bottom:6px;">
            <label style="font-size:12px;font-weight:600;color:#475569;margin-bottom:8px;display:block;">
              <i class="fa-solid fa-palette" style="margin-right:4px;color:#6366f1;"></i>Etiqueta de color
            </label>
            <div id="egsAutoColorPreview" style="padding:10px 14px;border-radius:8px;background:#f1f5f9;border:1.5px solid #e2e8f0;font-size:12px;color:#64748b;display:flex;align-items:center;gap:8px;">
              <span id="egsAutoColorDot" style="width:14px;height:14px;border-radius:50%;background:#3a87ad;flex-shrink:0;"></span>
              <span id="egsAutoColorText">Ingresa un No. de Orden para asignar color automático</span>
            </div>
          </div>

        </div>

        <!-- Footer -->
        <div style="padding:14px 24px 20px;display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #f1f5f9;">
          <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius:8px;padding:8px 18px;font-size:13px;">Cancelar</button>
          <button type="submit" class="btn" id="btnGuardarCitaRapida"
            style="border-radius:8px;padding:8px 22px;font-size:13px;font-weight:600;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border:none;transition:opacity .15s;">
            <i class="fa-solid fa-check" style="margin-right:5px;"></i>Agendar
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<style>
/* ═══ Calendar Trigger — Pill destacado en navbar ═══ */
.egs-calendar-menu > a.egs-cal-trigger {
  display: flex !important;
  align-items: center !important;
  gap: 7px !important;
  padding: 7px 14px !important;
  margin: 7px 6px !important;
  border-radius: 10px !important;
  background: linear-gradient(135deg, rgba(99,102,241,.15), rgba(139,92,246,.12)) !important;
  border: 1.5px solid rgba(99,102,241,.3) !important;
  color: #a5b4fc !important;
  font-size: 15px !important;
  transition: all .2s !important;
  position: relative !important;
  line-height: 1.3 !important;
}

.egs-calendar-menu > a.egs-cal-trigger:hover,
.egs-calendar-menu.open > a.egs-cal-trigger {
  background: linear-gradient(135deg, rgba(99,102,241,.28), rgba(139,92,246,.22)) !important;
  border-color: rgba(99,102,241,.5) !important;
  color: #c7d2fe !important;
  box-shadow: 0 0 16px rgba(99,102,241,.2) !important;
}

.egs-calendar-menu > a.egs-cal-trigger > i {
  font-size: 17px !important;
}

.egs-cal-trigger-label {
  font-size: 12px !important;
  font-weight: 600 !important;
  letter-spacing: .3px;
}

@media (max-width: 767px) {
  .egs-cal-trigger-label { display: none; }
}

/* ═══ Calendar Dropdown Styles ═══ */
.egs-calendar-menu > a {
  position: relative;
}

.egs-cal-dropdown {
  width: 360px !important;
  padding: 0 !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 14px !important;
  box-shadow: 0 16px 48px rgba(15,23,42,.18) !important;
  overflow: hidden !important;
  right: 0 !important;
  left: auto !important;
  margin-top: 8px !important;
}

.egs-cal-header {
  background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
  padding: 16px 18px 12px !important;
  border: none !important;
}

.egs-cal-title {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #fff;
  font-size: 15px;
  font-weight: 700;
  margin-bottom: 12px;
}

.egs-cal-title i {
  font-size: 16px;
  opacity: .9;
}

.egs-cal-tabs {
  display: flex;
  gap: 4px;
  background: rgba(255,255,255,.15);
  border-radius: 8px;
  padding: 3px;
}

.egs-cal-tab {
  flex: 1;
  padding: 6px 0;
  border: none;
  background: transparent;
  color: rgba(255,255,255,.7);
  font-size: 12px;
  font-weight: 600;
  border-radius: 6px;
  cursor: pointer;
  transition: all .15s;
}

.egs-cal-tab:hover {
  color: #fff;
  background: rgba(255,255,255,.1);
}

.egs-cal-tab.active {
  background: #fff;
  color: #6366f1;
  box-shadow: 0 1px 3px rgba(0,0,0,.12);
}

.egs-cal-body {
  max-height: 320px;
  overflow-y: auto;
  padding: 8px 0;
  background: #fff;
}

.egs-cal-loading {
  text-align: center;
  padding: 24px;
  color: #94a3b8;
  font-size: 13px;
}

.egs-cal-event {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 16px;
  transition: background .12s;
  cursor: pointer;
  text-decoration: none !important;
  color: #334155 !important;
}

.egs-cal-event:hover {
  background: #f8fafc;
}

.egs-cal-event-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  flex-shrink: 0;
  margin-top: 4px;
}

.egs-cal-event-info {
  flex: 1;
  min-width: 0;
}

.egs-cal-event-title {
  font-size: 13px;
  font-weight: 600;
  color: #1e293b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.egs-cal-event-time {
  font-size: 11px;
  color: #94a3b8;
  margin-top: 2px;
  display: flex;
  align-items: center;
  gap: 4px;
}

.egs-cal-event-time i {
  font-size: 10px;
}

.egs-cal-event-desc {
  font-size: 11px;
  color: #64748b;
  margin-top: 3px;
  display: flex;
  align-items: flex-start;
  gap: 4px;
  line-height: 1.4;
  white-space: normal;
  word-break: break-word;
}
.egs-cal-event-desc i {
  font-size: 10px;
  margin-top: 2px;
  color: #6366f1;
  flex-shrink: 0;
}

.egs-cal-event-actions {
  display: flex;
  flex-direction: column;
  gap: 4px;
  flex-shrink: 0;
  margin-left: auto;
}
.egs-cal-event-actions a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  padding: 4px 9px;
  border-radius: 6px;
  font-size: 10px;
  font-weight: 600;
  text-decoration: none !important;
  transition: all .15s;
  line-height: 1.3;
  white-space: nowrap;
}
.egs-cal-event-actions a:hover {
  opacity: .85;
}
.egs-cal-btn-orden {
  background: #eef2ff;
  color: #4f46e5 !important;
  border: 1px solid #c7d2fe;
}
.egs-cal-btn-gcal {
  background: #eff6ff;
  color: #2563eb !important;
  border: 1px solid #bfdbfe;
}

.egs-cal-empty {
  text-align: center;
  padding: 32px 16px;
  color: #94a3b8;
}

.egs-cal-empty i {
  font-size: 28px;
  color: #cbd5e1;
  margin-bottom: 8px;
  display: block;
}

.egs-cal-empty span {
  font-size: 13px;
  display: block;
}

.egs-cal-empty small {
  font-size: 11px;
  color: #cbd5e1;
  display: block;
  margin-top: 4px;
}

.egs-cal-footer {
  display: flex !important;
  justify-content: space-between;
  align-items: center;
  padding: 10px 16px !important;
  background: #f8fafc !important;
  border-top: 1px solid #e2e8f0 !important;
}

.egs-cal-footer a {
  font-size: 12px !important;
  font-weight: 600 !important;
  color: #6366f1 !important;
  text-decoration: none !important;
  display: flex;
  align-items: center;
  gap: 5px;
  transition: color .12s;
}

.egs-cal-footer a:hover {
  color: #4f46e5 !important;
}

.egs-cal-footer a.egs-cal-add-btn {
  background: #6366f1 !important;
  color: #fff !important;
  padding: 5px 12px !important;
  border-radius: 6px !important;
  font-size: 11px !important;
  transition: background .12s !important;
}

.egs-cal-footer a.egs-cal-add-btn:hover {
  background: #4f46e5 !important;
  color: #fff !important;
}

/* Fecha counter badge en el icono del calendar */
.egs-cal-badge {
  position: absolute;
  top: -2px;
  right: -4px;
  background: #ef4444;
  color: #fff;
  font-size: 9px;
  font-weight: 700;
  min-width: 17px;
  height: 17px;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 4px;
  box-shadow: 0 0 0 2px #1e293b;
  line-height: 1;
  animation: egs-badge-pulse 2s ease-in-out infinite;
}

@keyframes egs-badge-pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.12); }
}

/* ═══ Quick Date Chips ═══ */
.egs-quick-dates {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.egs-qd-chip {
  padding: 6px 12px;
  border-radius: 20px;
  border: 1.5px solid #e2e8f0;
  background: #fff;
  color: #475569;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all .15s;
  display: flex;
  align-items: center;
  gap: 5px;
}

.egs-qd-chip:hover {
  border-color: #6366f1;
  color: #6366f1;
  background: #f5f3ff;
}

.egs-qd-chip.active {
  background: #6366f1;
  color: #fff;
  border-color: #6366f1;
}

.egs-qd-chip i {
  font-size: 11px;
}

.egs-qd-custom {
  border-style: dashed;
}

/* ═══ Time Grid ═══ */
.egs-time-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 5px;
}

.egs-time-chip {
  padding: 7px 4px;
  border-radius: 8px;
  border: 1.5px solid #e2e8f0;
  background: #fff;
  color: #475569;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  text-align: center;
  transition: all .15s;
}

.egs-time-chip:hover {
  border-color: #6366f1;
  color: #6366f1;
}

.egs-time-chip.active {
  background: #6366f1;
  color: #fff;
  border-color: #6366f1;
}

/* ═══ (Color pills removed — auto-color from client badge) ═══ */

/* ═══ Day labels in calendar dropdown ═══ */
.egs-cal-day-label {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .05em;
  color: #94a3b8;
  padding: 8px 16px 4px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.egs-cal-day-label::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #f1f5f9;
}
</style>

<script>
(function(){
  var currentRango = 'hoy';
  var calendarBadgeLoaded = false;

  // Días en español
  var diasEs = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
  var mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

  function formatFechaCorta(dateStr) {
    var d = new Date(dateStr.replace(/-/g,'/').replace('T',' '));
    if (isNaN(d)) return dateStr;
    var hoy = new Date(); hoy.setHours(0,0,0,0);
    var fecha = new Date(d); fecha.setHours(0,0,0,0);
    var diffDays = Math.round((fecha - hoy) / 86400000);

    var hora = d.getHours().toString().padStart(2,'0') + ':' + d.getMinutes().toString().padStart(2,'0');
    if (diffDays === 0) return 'Hoy, ' + hora;
    if (diffDays === 1) return 'Mañana, ' + hora;
    return diasEs[d.getDay()] + ' ' + d.getDate() + ', ' + hora;
  }

  function groupByDay(events) {
    var groups = {};
    events.forEach(function(ev){
      var d = ev.start.substring(0,10);
      if (!groups[d]) groups[d] = [];
      groups[d].push(ev);
    });
    return groups;
  }

  function renderEvents(events) {
    var $body = $('#egsCalBody');
    if (!events || !events.length) {
      var msgs = {
        'hoy': 'No tienes citas para hoy',
        'semana': 'Sin citas esta semana',
        'mes': 'Sin citas este mes'
      };
      $body.html(
        '<div class="egs-cal-empty">' +
          '<i class="fa-regular fa-calendar-xmark"></i>' +
          '<span>' + (msgs[currentRango] || 'Sin citas') + '</span>' +
          '<small>Agenda una cita nueva con el botón +</small>' +
        '</div>'
      );
      return;
    }

    var grouped = groupByDay(events);
    var html = '';
    var hoy = new Date(); hoy.setHours(0,0,0,0);

    Object.keys(grouped).sort().forEach(function(dateKey){
      var d = new Date(dateKey.replace(/-/g,'/'));
      var diffDays = Math.round((d - hoy) / 86400000);
      var label = '';
      if (diffDays === 0) label = 'Hoy';
      else if (diffDays === 1) label = 'Mañana';
      else label = diasEs[d.getDay()] + ' ' + d.getDate() + ' de ' + mesesEs[d.getMonth()];

      if (currentRango !== 'hoy') {
        html += '<div class="egs-cal-day-label">' + label + '</div>';
      }

      grouped[dateKey].forEach(function(ev){
        var hora = '--:--';
        if (ev.start && ev.start.length > 10) {
          var dt = new Date(ev.start.replace(/-/g,'/').replace('T',' '));
          hora = dt.getHours().toString().padStart(2,'0') + ':' + dt.getMinutes().toString().padStart(2,'0');
        }
        // Generar URL de Google Calendar
        var gcalUrl = '';
        if (ev.start) {
          var dtStart = new Date(ev.start.replace(/-/g,'/').replace('T',' '));
          var dtEnd = new Date(dtStart.getTime() + 60*60*1000);
          function fmtGCal(d){ return d.getFullYear().toString()+('0'+(d.getMonth()+1)).slice(-2)+('0'+d.getDate()).slice(-2)+'T'+('0'+d.getHours()).slice(-2)+('0'+d.getMinutes()).slice(-2)+('0'+d.getSeconds()).slice(-2); }
          var detalles = [];
          if (ev.id_orden) detalles.push('Orden #' + ev.id_orden);
          if (ev.cliente_nombre) detalles.push('Cliente: ' + ev.cliente_nombre);
          if (ev.equipo) detalles.push('Equipo: ' + ev.equipo);
          if (ev.description) detalles.push('\nNotas: ' + ev.description);
          gcalUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE' +
            '&text=' + encodeURIComponent(ev.title || 'Cita') +
            '&dates=' + fmtGCal(dtStart) + '/' + fmtGCal(dtEnd) +
            (detalles.length ? '&details=' + encodeURIComponent(detalles.join('\n')) : '');
        }

        // Botones de acción
        var actionsHtml = '<div class="egs-cal-event-actions">';
        if (ev.id_orden) {
          actionsHtml += '<a href="index.php?ruta=infoOrden&idOrden=' + ev.id_orden + '" class="egs-cal-btn-orden"><i class="fa-solid fa-eye"></i> Orden</a>';
        }
        if (gcalUrl) {
          actionsHtml += '<a href="' + gcalUrl + '" target="_blank" class="egs-cal-btn-gcal"><i class="fa-brands fa-google"></i> Calendar</a>';
        }
        actionsHtml += '</div>';

        html += '<div class="egs-cal-event" data-cita-id="' + ev.id + '" style="cursor:pointer;">' +
          '<span class="egs-cal-event-dot" style="background:' + (ev.color || '#3a87ad') + '"></span>' +
          '<div class="egs-cal-event-info">' +
            '<div class="egs-cal-event-title">' + $('<span>').text(ev.title || 'Sin título').html() + '</div>' +
            '<div class="egs-cal-event-time"><i class="fa-regular fa-clock"></i> ' + hora + '</div>' +
            (ev.description ? '<div class="egs-cal-event-desc"><i class="fa-regular fa-comment-dots"></i> ' + $('<span>').text(ev.description).html() + '</div>' : '') +
          '</div>' +
          actionsHtml +
        '</div>';
      });
    });

    $body.html(html);
  }

  function loadEvents(rango) {
    currentRango = rango;
    var $body = $('#egsCalBody');
    $body.html('<div class="egs-cal-loading"><i class="fa-solid fa-spinner fa-spin"></i> Cargando...</div>');

    $.ajax({
      url: 'ajax/citas.ajax.php',
      type: 'POST',
      data: { accion: 'citasPorRango', rango: rango },
      dataType: 'json',
      global: false,
      success: function(resp) {
        renderEvents(resp);
        // Update badge
        var $badge = $('#egsCalBadge');
        if (rango === 'hoy') {
          if (resp && resp.length) {
            $badge.text(resp.length).show();
          } else {
            $badge.hide();
          }
        }
      },
      error: function() {
        $body.html('<div class="egs-cal-empty"><i class="fa-solid fa-exclamation-triangle"></i><span>Error al cargar</span></div>');
      }
    });
  }

  // Tab clicks
  $(document).on('click', '.egs-cal-tab', function(e){
    e.preventDefault();
    e.stopPropagation();
    $('.egs-cal-tab').removeClass('active');
    $(this).addClass('active');
    loadEvents($(this).data('rango'));
  });

  // Load on dropdown open
  $(document).on('click', '#egsCalendarToggle', function(){
    if (!calendarBadgeLoaded) {
      loadEvents('hoy');
      calendarBadgeLoaded = true;
    }
  });

  // Prevent dropdown close on tab click
  $(document).on('click', '.egs-cal-dropdown', function(e){
    if ($(e.target).hasClass('egs-cal-tab')) {
      e.stopPropagation();
    }
  });

  // Click en evento de agenda → ir al calendario y abrir modal de la cita
  $(document).on('click', '.egs-cal-event', function(e){
    // No interceptar clicks en los botones de acción (Orden, Google Calendar)
    if ($(e.target).closest('.egs-cal-event-actions').length) return;
    var citaId = $(this).data('cita-id');
    if (!citaId) return;
    e.preventDefault();
    e.stopPropagation();
    window.location.href = 'index.php?ruta=pantallacitas&citaId=' + citaId;
  });

  // Quick add button — cerrar dropdown primero, luego abrir modal
  // Si estamos en infoOrden, pre-llenar con datos de la orden
  $(document).on('click', '#egsCalQuickAdd', function(e){
    e.preventDefault();
    e.stopPropagation();
    $('#egsCalendarToggle').dropdown('toggle');

    var urlParams = new URLSearchParams(window.location.search);
    var ruta = urlParams.get('ruta');
    var ordenId = urlParams.get('idOrden');

    setTimeout(function(){
      if (ruta === 'infoOrden' && ordenId && parseInt(ordenId) > 0) {
        $('#crOrdenId').val(ordenId).prop('readonly', true).trigger('input');
        $('#crTitulo').val('Cita #' + ordenId);
      }
      $('#modalCitaRapida').modal('show');
    }, 200);
  });

  // Load badge on page load + mover modal al body
  $(document).ready(function(){
    // Mover el modal fuera del navbar al body para evitar conflictos de z-index
    $('#modalCitaRapida').appendTo('body');

    $.ajax({
      url: 'ajax/citas.ajax.php',
      type: 'POST',
      data: { accion: 'citasPorRango', rango: 'hoy' },
      dataType: 'json',
      global: false,
      success: function(resp) {
        if (resp && resp.length) {
          $('#egsCalBadge').text(resp.length).show();
        }
      }
    });
  });

  // ═══ Quick Date Logic ═══
  var selectedFecha = 'hoy';
  var selectedHora = '10:00';
  var selectedColor = '#3a87ad';

  // Horarios por tipo de día
  // L-V: 10:00-14:00 y 16:00-18:30
  var horasLV = ['10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','16:00','16:30','17:00','17:30','18:00','18:30'];
  // Sábado: 9:00-14:30
  var horasSab = ['09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30'];

  function getNextDayOfWeek(dayIndex) {
    var d = new Date();
    var diff = dayIndex - d.getDay();
    if (diff <= 0) diff += 7;
    d.setDate(d.getDate() + diff);
    return d;
  }

  function calcFecha(tipo) {
    var d = new Date();
    switch(tipo) {
      case 'hoy': return d;
      case 'manana': d.setDate(d.getDate()+1); return d;
      case 'lunes': return getNextDayOfWeek(1);
      case 'martes': return getNextDayOfWeek(2);
      case 'miercoles': return getNextDayOfWeek(3);
      case 'jueves': return getNextDayOfWeek(4);
      case 'viernes': return getNextDayOfWeek(5);
      case 'sabado': return getNextDayOfWeek(6);
      default: return d;
    }
  }

  // Determina si un día (0=Dom..6=Sáb) es sábado
  function esSabado(dayOfWeek) { return dayOfWeek === 6; }
  function esDomingo(dayOfWeek) { return dayOfWeek === 0; }

  function getHorasParaDia(dayOfWeek) {
    if (esSabado(dayOfWeek)) return horasSab;
    if (esDomingo(dayOfWeek)) return []; // Domingo cerrado
    return horasLV;
  }

  function renderTimeGrid() {
    var d = selectedFecha === 'personalizado'
      ? (function(){ var v=$('#crFechaCustom').val(); return v ? new Date(v.replace(/-/g,'/')) : new Date(); })()
      : calcFecha(selectedFecha);

    var dow = d.getDay();
    var horas = getHorasParaDia(dow);
    var $grid = $('#egsTimeGrid');
    var $info = $('#egsHorarioTexto');

    if (esDomingo(dow)) {
      $grid.html('<div style="text-align:center;padding:12px;color:#ef4444;font-size:12px;"><i class="fa-solid fa-ban" style="margin-right:5px;"></i>Domingo no disponible — elige otro día</div>');
      $info.text('Domingos no se atiende');
      $('#egsHorarioInfo').css({'background':'#fef2f2','border-color':'#fecaca','color':'#991b1b'});
      selectedHora = '';
      return;
    }

    if (esSabado(dow)) {
      $info.text('Sábado: 9:00 – 14:30');
    } else {
      $info.text('L-V: 10:00–14:00 y 16:00–18:30');
    }
    $('#egsHorarioInfo').css({'background':'#fffbeb','border-color':'#fde68a','color':'#92400e'});

    // Filtrar horas pasadas si es hoy
    var esHoy = false;
    var ahora = new Date();
    var hoyDate = new Date(); hoyDate.setHours(0,0,0,0);
    var fechaSel = new Date(d); fechaSel.setHours(0,0,0,0);
    if (fechaSel.getTime() === hoyDate.getTime()) esHoy = true;

    var horaActual = ahora.getHours().toString().padStart(2,'0') + ':' + ahora.getMinutes().toString().padStart(2,'0');

    var horasFiltradas = esHoy ? horas.filter(function(h){ return h > horaActual; }) : horas;

    if (esHoy && horasFiltradas.length === 0) {
      $grid.html('<div style="text-align:center;padding:12px;color:#d97706;font-size:12px;"><i class="fa-solid fa-clock" style="margin-right:5px;"></i>Ya no hay horarios disponibles hoy — elige otro día</div>');
      selectedHora = '';
      return;
    }

    var html = '';
    var found = false;
    horasFiltradas.forEach(function(h){
      var isActive = (h === selectedHora);
      if (isActive) found = true;
      html += '<button type="button" class="egs-time-chip' + (isActive ? ' active' : '') + '" data-hora="' + h + '">' + h + '</button>';
    });

    $grid.html(html);

    // Si la hora seleccionada no está en las opciones, seleccionar la primera
    if (!found && horasFiltradas.length) {
      selectedHora = horasFiltradas[0];
      $grid.find('.egs-time-chip').first().addClass('active');
    }
  }

  function updateDatePreview() {
    var d;
    if (selectedFecha === 'personalizado') {
      var val = $('#crFechaCustom').val();
      d = val ? new Date(val.replace(/-/g,'/')) : new Date();
    } else {
      d = calcFecha(selectedFecha);
    }
    var txt = diasEs[d.getDay()] + ' ' + d.getDate() + ' de ' + mesesEs[d.getMonth()];
    if (selectedHora) txt += ' — ' + selectedHora;
    $('#egsDatePreviewText').text(txt);
  }

  // Render time grid al inicio
  renderTimeGrid();
  updateDatePreview();

  // Date chip selection
  $(document).on('click', '.egs-qd-chip', function(){
    var fecha = $(this).data('fecha');
    if (!fecha) return;
    $('.egs-qd-chip').removeClass('active');
    $(this).addClass('active');
    selectedFecha = fecha;

    if (fecha === 'personalizado') {
      $('#egsCustomDateWrap').slideDown(150);
    } else {
      $('#egsCustomDateWrap').slideUp(150);
    }
    renderTimeGrid();
    updateDatePreview();
  });

  $('#crFechaCustom').on('change', function(){
    renderTimeGrid();
    updateDatePreview();
  });

  // Time chip selection
  $(document).on('click', '.egs-time-chip', function(){
    var hora = $(this).data('hora');
    if (!hora) return;
    $('.egs-time-chip').removeClass('active');
    $(this).addClass('active');
    selectedHora = hora;
    updateDatePreview();
  });

  // Auto-color: buscar calificación del cliente al ingresar ID de orden
  var colorAjaxReq = null;
  $(document).on('input change', '#crOrdenId', function(){
    var idOrden = $(this).val();
    if (colorAjaxReq && colorAjaxReq.readyState !== 4) colorAjaxReq.abort();

    if (!idOrden || idOrden < 1) {
      selectedColor = '#3a87ad';
      $('#egsAutoColorDot').css('background', '#3a87ad');
      $('#egsAutoColorText').text('Ingresa un No. de Orden para asignar color automático').css('color','#64748b');
      return;
    }

    $('#egsAutoColorText').html('<i class="fa-solid fa-spinner fa-spin"></i> Consultando...');

    colorAjaxReq = $.ajax({
      url: 'ajax/citas.ajax.php',
      type: 'POST',
      data: { accion: 'colorPorOrden', idOrden: idOrden },
      dataType: 'json',
      global: false,
      success: function(resp) {
        if (resp && resp.ok) {
          selectedColor = resp.color;
          $('#egsAutoColorDot').css('background', resp.color);

          var labels = {
            '#16a34a': 'Excelente',
            '#2563eb': 'Bueno',
            '#d97706': 'Regular',
            '#dc2626': 'Bajo',
            '#8b5cf6': 'Cliente nuevo'
          };
          var label = labels[resp.color] || 'General';
          var detail = '';
          if (resp.es_nuevo) {
            detail = 'Cliente nuevo (' + resp.total_ordenes + ' órdenes)';
          } else if (resp.calif !== null) {
            detail = label + ' — Calif. entrega: ' + resp.calif + '%';
            if (resp.avg_recogida) detail += ' · Recoge: ~' + resp.avg_recogida + ' días';
          } else {
            detail = 'Sin historial suficiente';
          }
          $('#egsAutoColorText').text(detail).css('color','#334155');
        } else {
          selectedColor = '#3a87ad';
          $('#egsAutoColorDot').css('background', '#3a87ad');
          $('#egsAutoColorText').text(resp.error || 'Orden no encontrada').css('color','#ef4444');
        }
      },
      error: function(xhr) {
        if (xhr.statusText === 'abort') return;
        selectedColor = '#3a87ad';
        $('#egsAutoColorDot').css('background', '#3a87ad');
        $('#egsAutoColorText').text('Error al consultar').css('color','#ef4444');
      }
    });
  });

  // Submit form
  $(document).on('submit', '#formCitaRapida', function(e){
    e.preventDefault();
    var titulo = $('#crTitulo').val().trim();
    if (!titulo) return;

    // Validar orden/pedido (opcional)
    var ordenId = $('#crOrdenId').val();

    // Validar que no sea domingo y que haya hora seleccionada
    if (!selectedHora) {
      swal({ icon:'warning', title:'Hora requerida', text:'Selecciona una hora disponible.' });
      return;
    }

    var d;
    if (selectedFecha === 'personalizado') {
      var val = $('#crFechaCustom').val();
      if (!val) { swal({ icon:'warning', title:'Fecha requerida', text:'Selecciona una fecha.' }); return; }
      d = new Date(val.replace(/-/g,'/'));
    } else {
      d = calcFecha(selectedFecha);
    }

    if (esDomingo(d.getDay())) {
      swal({ icon:'warning', title:'Día no disponible', text:'No se pueden agendar citas en domingo.' });
      return;
    }

    var yyyy = d.getFullYear();
    var mm = (d.getMonth()+1).toString().padStart(2,'0');
    var dd = d.getDate().toString().padStart(2,'0');
    var fechaFinal = yyyy + '-' + mm + '-' + dd + 'T' + selectedHora;

    var descripcion = $('#crDescripcion').val() || '';

    var datos = new FormData();
    datos.append("tituloCita", titulo);
    datos.append("fechaCita", fechaFinal);
    datos.append("colorCita", selectedColor);
    datos.append("idOrden", ordenId);
    datos.append("descripcionCita", descripcion);

    var $btn = $('#btnGuardarCitaRapida');
    $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Guardando...');

    $.ajax({
      url: 'ajax/citas.ajax.php',
      method: 'POST',
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      success: function(resp) {
        // Verificar duplicados
        if (resp.indexOf('duplicado_hora') === 0) {
          var parts = resp.split('|');
          swal({ icon:'warning', title:'Horario ocupado', text:'Ya existe una cita a esa hora: "' + parts[1] + '" (' + parts[2] + '). Elige otra hora.' });
          return;
        }
        if (resp.indexOf('duplicado_orden') === 0) {
          var parts = resp.split('|');
          swal({ icon:'warning', title:'Cita duplicada', text:'Ya existe una cita para esta orden en ese día: "' + parts[1] + '" (' + parts[2] + ').' });
          return;
        }
        if (resp == 'ok') {
          $('#modalCitaRapida').modal('hide');
          $('#formCitaRapida')[0].reset();
          $('#crOrdenId').val('');
          $('#crOrdenPreview').hide();
          // Reset auto-color
          $('#egsAutoColorDot').css('background', '#3a87ad');
          $('#egsAutoColorText').text('Ingresa un No. de Orden para asignar color automático').css('color','#64748b');
          // Reset chips
          $('.egs-qd-chip').removeClass('active').first().addClass('active');
          selectedFecha = 'hoy'; selectedHora = '10:00'; selectedColor = '#3a87ad';
          $('#egsCustomDateWrap').hide();
          renderTimeGrid();
          updateDatePreview();

          swal({ icon:'success', title:'Cita agendada', text: titulo, showConfirmButton:false, timer:1800 });

          // Refresh badge
          calendarBadgeLoaded = false;
          loadEvents('hoy');

          // Refresh fullcalendar if on that page
          if (typeof calendar !== 'undefined' && calendar.refetchEvents) {
            calendar.refetchEvents();
          }
        } else {
          swal({ icon:'error', title:'Error', text:'No se pudo guardar la cita: ' + resp });
        }
      },
      error: function() {
        swal({ icon:'error', title:'Error de conexión', text:'No se pudo guardar la cita.' });
      },
      complete: function() {
        $btn.prop('disabled', false).html('<i class="fa-solid fa-check" style="margin-right:5px;"></i>Agendar');
      }
    });
  });

  // Agendar cita desde infoOrden — pre-llena el modal rápido con el ID de orden
  $(document).on('click', '.btnAgendarCitaDesdeOrden', function(){
    var ordenId = $(this).data('orden-id');
    if (!ordenId || parseInt(ordenId) < 1) return;
    $('#crOrdenId').val(ordenId).prop('readonly', true).trigger('input');
    $('#crTitulo').val('Cita #' + ordenId);
    $('#modalCitaRapida').modal('show');
  });

  // Restaurar campo de orden al cerrar el modal
  $('#modalCitaRapida').on('hidden.bs.modal', function(){
    $('#crOrdenId').prop('readonly', false);
  });

})();
</script>
