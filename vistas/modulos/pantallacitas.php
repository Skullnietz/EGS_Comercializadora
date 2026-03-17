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

                        <!-- Entrada para el Título -->
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
                            <label for="idOrden">No. de Orden (Opcional)</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-file-text-o"></i></span>
                                <input type="number" class="form-control input-lg" id="idOrden" name="idOrden"
                                    placeholder="ID de la Orden">
                            </div>
                        </div>

                        <!-- Entrada para el Color -->
                        <div class="form-group">
                            <label for="colorCita">Color de la Etiqueta</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-paint-brush"></i></span>
                                <select class="form-control input-lg" id="colorCita" name="colorCita"
                                    style="color: #333 !important; background-color: #fff !important; -webkit-text-fill-color: #333 !important; opacity: 1 !important;">
                                    <option value="#3a87ad">Azul (Por defecto)</option>
                                    <option value="#28a745">Verde (Confirmado)</option>
                                    <option value="#ffc107">Amarillo (Pendiente)</option>
                                    <option value="#dc3545">Rojo (Urgente)</option>
                                    <option value="#6c757d">Gris (Normal)</option>
                                </select>
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
<!-- Scripts FullCalendar -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js'></script>

<!-- Script Personalizado -->
<script src="vistas/js/calendario.js?v=<?php echo time(); ?>"></script>