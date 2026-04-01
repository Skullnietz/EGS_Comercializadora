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

        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Agenda de Citas</h3>
            </div>

            <div class="box-body">
                <!-- Contenedor del Calendario -->
                <div id="calendar"></div>
            </div>

        </div>

    </section>

</div>

<!-- Modal Agregar Cita -->
<div class="modal fade" id="modalAgregarCita" tabindex="-1" role="dialog" aria-labelledby="modalAgregarCitaLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <form role="form" id="formularioCita">

                <div class="modal-header" style="background:#3c8dbc; color:white">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modalAgregarCitaLabel">Agendar Cita</h4>
                </div>

                <div class="modal-body">
                    <div class="box-body">

                        <!-- Entrada para el T�tulo -->
                        <div class="form-group">
                            <label for="tituloCita">T&iacute;tulo de la Cita</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                                <input type="text" class="form-control input-lg" id="tituloCita" name="tituloCita"
                                    placeholder="Ingresar t&iacute;tulo" required>
                            </div>
                        </div>

                        <!-- Entrada para la Fecha y Hora -->
                        <div class="form-group">
                            <label for="fechaCita">Fecha y Hora de Inicio</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="datetime-local" class="form-control input-lg" id="fechaCita"
                                    name="fechaCita" required>
                            </div>
                        </div>

                        <!-- Entrada para la ID Orden -->
                        <div class="form-group">
                            <label for="idOrden">No. de Orden o Pedido <span style="color:#ef4444;">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-file-text-o"></i></span>
                                <input type="number" class="form-control input-lg" id="idOrden" name="idOrden"
                                    placeholder="ID de la Orden o Pedido" required min="1">
                            </div>
                        </div>

                        <!-- Comentarios / Observaciones -->
                        <div class="form-group">
                            <label for="descripcionCita"><i class="fa fa-comment-dots" style="margin-right:4px;color:#6366f1"></i> Comentarios / Observaciones</label>
                            <textarea class="form-control" id="descripcionCita" name="descripcionCita" rows="3"
                                placeholder="¿De qué trata la cita? Ej: Revisión de pantalla, entrega de equipo, seguimiento de presupuesto..."
                                style="resize:vertical;min-height:60px;border-radius:6px;"></textarea>
                            <small class="text-muted" style="font-size:11px;">Opcional — Esta información aparecerá al consultar la cita</small>
                        </div>

                        <!-- Color automático basado en calificación del cliente -->
                        <div class="form-group">
                            <label>Color de Etiqueta (automático)</label>
                            <input type="hidden" id="colorCita" name="colorCita" value="#3a87ad">
                            <div id="pcAutoColorPreview" style="padding:10px 14px;border-radius:8px;background:#f8fafc;border:1px solid #e2e8f0;font-size:13px;color:#64748b;display:flex;align-items:center;gap:8px;">
                                <span id="pcAutoColorDot" style="width:14px;height:14px;border-radius:50%;background:#3a87ad;flex-shrink:0;"></span>
                                <span id="pcAutoColorText">Ingresa un No. de Orden para asignar color</span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button type="submit" class="btn btn-primary">Guardar Cita</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- Estilos FullCalendar -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

<!-- Estilos botones swal para citas -->
<style>
    .swal-btn-info { background-color: #3c8dbc !important; color: #fff !important; }
    .swal-btn-info:hover { background-color: #367fa9 !important; }
    .swal-btn-gcal { background-color: #4285f4 !important; color: #fff !important; }
    .swal-btn-gcal:hover { background-color: #3367d6 !important; }
    .swal-btn-danger { background-color: #dd4b39 !important; color: #fff !important; }
    .swal-btn-danger:hover { background-color: #d73925 !important; }
</style>
<!-- Scripts FullCalendar -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js'></script>

<!-- Script Personalizado -->
<script src="vistas/js/calendario.js?v=<?php echo time(); ?>"></script>