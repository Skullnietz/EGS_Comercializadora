<div class="content-wrapper">

    <section class="content-header">
        <h1>
            Calendario de Citas
            <small>Panel de Control</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
            <li class="active">Calendario de Citas</li>
        </ol>
    </section>

    <section class="content">

        <div class="egs-section" style="border-radius:14px;overflow:hidden;border:1px solid #e2e8f0;box-shadow:0 4px 24px rgba(15,23,42,.08);">

            <!-- Título con gradiente -->
            <div class="egs-title-bar" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <i class="fa-solid fa-calendar-check" style="font-size:18px;opacity:.9;"></i>
                    <span style="font-size:16px;font-weight:700;">Agenda de Citas</span>
                </div>
                <button type="button" class="btn" data-toggle="modal" data-target="#modalAgregarCita"
                    style="background:rgba(255,255,255,.18);color:#fff;border:1.5px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-size:12px;font-weight:600;transition:all .15s;">
                    <i class="fa-solid fa-plus" style="margin-right:5px;"></i>Nueva Cita
                </button>
            </div>

            <div class="egs-body" style="padding:20px;background:#fff;border-radius:0 0 14px 14px;">
                <!-- Contenedor del Calendario -->
                <div id="calendar"></div>
            </div>

        </div>

    </section>

</div>

<!-- Modal Agregar Cita -->
<div class="modal fade" id="modalAgregarCita" tabindex="-1" role="dialog" aria-labelledby="modalAgregarCitaLabel">
    <div class="modal-dialog" role="document" style="max-width:520px;margin:60px auto;">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(15,23,42,.22);">

            <form role="form" id="formularioCita">

                <!-- Header gradiente -->
                <div style="background:linear-gradient(135deg,#6366f1,#8b5cf6);padding:20px 24px;color:#fff;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="color:#fff;opacity:.8;text-shadow:none;font-size:22px;">&times;</button>
                    <h4 style="margin:0;font-size:17px;font-weight:700;">
                        <i class="fa-regular fa-calendar-plus" style="margin-right:8px;"></i>Agendar Cita
                    </h4>
                    <p style="margin:6px 0 0;font-size:12px;opacity:.8;">Completa los detalles para registrar una nueva cita</p>
                </div>

                <!-- Body -->
                <div style="padding:22px 24px;">

                    <!-- Título -->
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="font-size:12px;font-weight:600;color:#475569;margin-bottom:6px;display:block;">
                            <i class="fa-solid fa-tag" style="margin-right:4px;color:#6366f1;"></i>T&iacute;tulo de la Cita
                        </label>
                        <input type="text" class="form-control" id="tituloCita" name="tituloCita"
                            placeholder="Ej: Revisión equipo cliente" required
                            style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 12px;font-size:13px;transition:border-color .2s;">
                    </div>

                    <!-- Fecha y Hora -->
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="font-size:12px;font-weight:600;color:#475569;margin-bottom:6px;display:block;">
                            <i class="fa-regular fa-clock" style="margin-right:4px;color:#6366f1;"></i>Fecha y Hora de Inicio
                        </label>
                        <input type="datetime-local" class="form-control" id="fechaCita" name="fechaCita" required
                            style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 12px;font-size:13px;transition:border-color .2s;">
                    </div>

                    <!-- No. de Orden -->
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="font-size:12px;font-weight:600;color:#475569;margin-bottom:6px;display:block;">
                            <i class="fa-solid fa-file-lines" style="margin-right:4px;color:#6366f1;"></i>No. de Orden o Pedido <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="number" class="form-control" id="idOrden" name="idOrden"
                            placeholder="Ingresa el ID de la orden o pedido" required min="1"
                            style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 12px;font-size:13px;transition:border-color .2s;">
                    </div>

                    <!-- Comentarios / Observaciones -->
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="font-size:12px;font-weight:600;color:#475569;margin-bottom:6px;display:block;">
                            <i class="fa-solid fa-comment-dots" style="margin-right:4px;color:#6366f1;"></i>Comentarios / Observaciones
                        </label>
                        <textarea class="form-control" id="descripcionCita" name="descripcionCita" rows="3"
                            placeholder="¿De qué trata la cita? Ej: Revisión de pantalla, entrega de equipo, seguimiento de presupuesto..."
                            style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 12px;font-size:13px;resize:vertical;min-height:60px;transition:border-color .2s;"></textarea>
                        <small style="font-size:10px;color:#94a3b8;margin-top:4px;display:block;">Opcional — Esta información aparecerá al consultar la cita</small>
                    </div>

                    <!-- Color automático -->
                    <div class="form-group" style="margin-bottom:6px;">
                        <label style="font-size:12px;font-weight:600;color:#475569;margin-bottom:8px;display:block;">
                            <i class="fa-solid fa-palette" style="margin-right:4px;color:#6366f1;"></i>Etiqueta de color
                        </label>
                        <input type="hidden" id="colorCita" name="colorCita" value="#3a87ad">
                        <div id="pcAutoColorPreview" style="padding:10px 14px;border-radius:8px;background:#f1f5f9;border:1.5px solid #e2e8f0;font-size:12px;color:#64748b;display:flex;align-items:center;gap:8px;">
                            <span id="pcAutoColorDot" style="width:14px;height:14px;border-radius:50%;background:#3a87ad;flex-shrink:0;"></span>
                            <span id="pcAutoColorText">Ingresa un No. de Orden para asignar color automático</span>
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div style="padding:14px 24px 20px;display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #f1f5f9;">
                    <button type="button" class="btn btn-default" data-dismiss="modal"
                        style="border-radius:8px;padding:8px 18px;font-size:13px;">Cancelar</button>
                    <button type="submit" class="btn"
                        style="border-radius:8px;padding:8px 22px;font-size:13px;font-weight:600;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border:none;transition:opacity .15s;">
                        <i class="fa-solid fa-check" style="margin-right:5px;"></i>Guardar Cita
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- Estilos FullCalendar -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

<!-- Estilos modernos calendario + swal -->
<style>
    /* ═══ FullCalendar — Overrides modernos EGS ═══ */

    /* Toolbar superior */
    .fc .fc-toolbar {
        margin-bottom: 16px !important;
        flex-wrap: wrap;
        gap: 10px;
    }
    .fc .fc-toolbar-title {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        text-transform: capitalize;
    }

    /* Botones del toolbar */
    .fc .fc-button {
        background: #fff !important;
        color: #475569 !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 6px 14px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        text-transform: capitalize !important;
        box-shadow: none !important;
        transition: all .15s !important;
    }
    .fc .fc-button:hover {
        background: #f8fafc !important;
        border-color: #6366f1 !important;
        color: #6366f1 !important;
    }
    .fc .fc-button-active,
    .fc .fc-button:active {
        background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
        color: #fff !important;
        border-color: #6366f1 !important;
    }
    .fc .fc-button-group > .fc-button {
        border-radius: 0 !important;
    }
    .fc .fc-button-group > .fc-button:first-child {
        border-radius: 8px 0 0 8px !important;
    }
    .fc .fc-button-group > .fc-button:last-child {
        border-radius: 0 8px 8px 0 !important;
    }
    .fc .fc-today-button {
        border-radius: 8px !important;
    }
    .fc .fc-button:disabled {
        opacity: .5 !important;
    }

    /* Encabezado de días */
    .fc .fc-col-header-cell {
        background: #f8fafc !important;
        border-color: #e2e8f0 !important;
        padding: 10px 0 !important;
    }
    .fc .fc-col-header-cell-cushion {
        font-size: 12px !important;
        font-weight: 700 !important;
        color: #475569 !important;
        text-transform: uppercase !important;
        letter-spacing: .04em !important;
        text-decoration: none !important;
    }

    /* Celdas del calendario */
    .fc .fc-daygrid-day {
        border-color: #f1f5f9 !important;
        transition: background .12s;
    }
    .fc .fc-daygrid-day:hover {
        background: #faf5ff !important;
    }
    .fc .fc-daygrid-day-number {
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #64748b !important;
        padding: 8px 10px !important;
        text-decoration: none !important;
    }
    .fc .fc-day-today {
        background: rgba(99, 102, 241, .06) !important;
    }
    .fc .fc-day-today .fc-daygrid-day-number {
        background: #6366f1 !important;
        color: #fff !important;
        border-radius: 50% !important;
        width: 28px !important;
        height: 28px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        margin: 4px 6px !important;
    }

    /* Eventos en el calendario */
    .fc .fc-event,
    .fc .fc-daygrid-event {
        border-radius: 6px !important;
        border: none !important;
        padding: 3px 8px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: transform .12s, box-shadow .12s !important;
        box-shadow: 0 1px 3px rgba(0,0,0,.1) !important;
    }
    .fc .fc-event:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,.15) !important;
    }
    .fc .fc-daygrid-event-dot {
        border-color: currentColor !important;
    }
    .fc .fc-event-title {
        font-weight: 600 !important;
    }
    .fc .fc-event-time {
        font-weight: 500 !important;
        opacity: .85;
    }

    /* Vista lista */
    .fc .fc-list {
        border-radius: 10px !important;
        overflow: hidden !important;
        border-color: #e2e8f0 !important;
    }
    .fc .fc-list-day-cushion {
        background: #f8fafc !important;
        padding: 10px 16px !important;
    }
    .fc .fc-list-day-text,
    .fc .fc-list-day-side-text {
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        text-decoration: none !important;
    }
    .fc .fc-list-event:hover td {
        background: #faf5ff !important;
    }
    .fc .fc-list-event-dot {
        border-color: currentColor !important;
    }

    /* Fondo general */
    .fc {
        --fc-border-color: #e2e8f0;
        --fc-today-bg-color: rgba(99, 102, 241, .06);
    }

    /* ═══ Botones swal para citas ═══ */
    .swal-btn-info {
        background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
        color: #fff !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 8px 18px !important;
    }
    .swal-btn-info:hover { opacity: .9 !important; }

    .swal-btn-gcal {
        background: #4285f4 !important;
        color: #fff !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 8px 18px !important;
    }
    .swal-btn-gcal:hover { background: #3367d6 !important; }

    .swal-btn-danger {
        background: #fff !important;
        color: #ef4444 !important;
        border: 1.5px solid #fecaca !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 8px 18px !important;
    }
    .swal-btn-danger:hover { background: #fef2f2 !important; }

    /* ═══ Focus states para inputs del modal ═══ */
    #modalAgregarCita .form-control:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .12) !important;
        outline: none !important;
    }

    /* ═══ Responsive ═══ */
    @media (max-width: 767px) {
        .fc .fc-toolbar { flex-direction: column; align-items: stretch; }
        .fc .fc-toolbar-chunk { display: flex; justify-content: center; }
        .fc .fc-toolbar-title { text-align: center; font-size: 16px !important; }
    }
</style>
<!-- Scripts FullCalendar -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js'></script>

<!-- Script Personalizado -->
<script src="vistas/js/calendario.js?v=<?php echo time(); ?>"></script>