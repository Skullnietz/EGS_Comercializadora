document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    // Initialize Select2
    $('.select2').select2();

    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        initialView: 'dayGridMonth',
        locale: 'es',
        navLinks: true,
        businessHours: true,
        editable: true,
        selectable: true,

        // Cargar eventos desde BD via AJAX
        events: {
            url: 'ajax/citas.ajax.php',
            method: 'POST',
            extraParams: {
                accion: 'mostrar'
            },
            failure: function () {
                console.log('Error al cargar eventos');
            }
        },

        // Al hacer click en una fecha
        dateClick: function (info) {
            document.getElementById('formularioCita').reset();
            var dateStr = info.dateStr;
            if (dateStr.length === 10) {
                dateStr += 'T09:00';
            }
            document.getElementById('fechaCita').value = dateStr;
            $('#modalAgregarCita').modal('show');
        },

        // Al hacer click en un evento
        eventClick: function (info) {
            var idOrden = info.event.extendedProps.id_orden;
            var descripcion = info.event.extendedProps.description || '';
            var clienteNombre = info.event.extendedProps.cliente_nombre || '';
            var equipo = info.event.extendedProps.equipo || '';

            var textoInfo = 'Fecha: ' + info.event.start.toLocaleString();
            if (idOrden) textoInfo += '\nOrden #' + idOrden;
            if (clienteNombre) textoInfo += '\nCliente: ' + clienteNombre;
            if (equipo) textoInfo += '\nEquipo: ' + equipo;
            if (descripcion) textoInfo += '\n\n📝 ' + descripcion;

            swal({
                title: info.event.title,
                text: textoInfo,
                icon: 'info',
                buttons: {
                    cancel: { text: 'Cerrar', visible: true },
                    verOrden: { text: 'Ver Orden', value: 'verOrden', className: 'swal-btn-info' },
                    googleCal: { text: 'Google Calendar', value: 'googleCal', className: 'swal-btn-gcal' },
                    eliminar: { text: 'Eliminar', value: 'eliminar', className: 'swal-btn-danger' }
                }
            }).then(function (value) {
                if (value === 'verOrden') {
                    if (idOrden) {
                        window.location = 'index.php?ruta=infoOrden&idOrden=' + idOrden;
                    } else {
                        swal('Sin orden', 'Esta cita no tiene una orden vinculada.', 'warning');
                    }
                } else if (value === 'googleCal') {
                    // Generar URL de Google Calendar
                    var startDate = info.event.start;
                    var endDate = info.event.end || new Date(startDate.getTime() + 60 * 60 * 1000); // +1 hora si no hay fin

                    function formatGCalDate(d) {
                        return d.getFullYear().toString() +
                            ('0' + (d.getMonth() + 1)).slice(-2) +
                            ('0' + d.getDate()).slice(-2) + 'T' +
                            ('0' + d.getHours()).slice(-2) +
                            ('0' + d.getMinutes()).slice(-2) +
                            ('0' + d.getSeconds()).slice(-2);
                    }

                    var detalles = [];
                    if (idOrden) detalles.push('Orden #' + idOrden);
                    var clienteNombre = info.event.extendedProps.cliente_nombre;
                    if (clienteNombre) detalles.push('Cliente: ' + clienteNombre);
                    var equipo = info.event.extendedProps.equipo;
                    if (equipo) detalles.push('Equipo: ' + equipo);
                    if (descripcion) detalles.push('\nNotas: ' + descripcion);

                    var gcalUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE' +
                        '&text=' + encodeURIComponent(info.event.title) +
                        '&dates=' + formatGCalDate(startDate) + '/' + formatGCalDate(endDate) +
                        (detalles.length ? '&details=' + encodeURIComponent(detalles.join('\n')) : '');

                    window.open(gcalUrl, '_blank');
                } else if (value === 'eliminar') {
                    swal({
                        title: '¿Estás seguro?',
                        text: 'Se eliminará la cita "' + info.event.title + '"',
                        icon: 'warning',
                        buttons: ['Cancelar', 'Sí, eliminar'],
                        dangerMode: true
                    }).then(function (confirmar) {
                        if (confirmar) {
                            var datos = new FormData();
                            datos.append("idCita", info.event.id);

                            $.ajax({
                                url: "ajax/citas.ajax.php",
                                method: "POST",
                                data: datos,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function (respuesta) {
                                    if (respuesta == "ok") {
                                        info.event.remove();
                                        swal('Eliminado!', 'La cita ha sido eliminada.', 'success');
                                    } else {
                                        swal('Error', 'No se pudo eliminar la cita', 'error');
                                    }
                                }
                            });
                        }
                    });
                }
            });
        }
    });

    calendar.render();

    // Auto-color: buscar calificación del cliente al cambiar ID de orden
    var pcColorReq = null;
    $(document).on('input change', '#idOrden', function(){
        var idOrden = $(this).val();
        if (pcColorReq && pcColorReq.readyState !== 4) pcColorReq.abort();

        if (!idOrden || idOrden < 1) {
            $('#colorCita').val('#3a87ad');
            $('#pcAutoColorDot').css('background', '#3a87ad');
            $('#pcAutoColorText').text('Ingresa un No. de Orden para asignar color').css('color','#64748b');
            return;
        }

        $('#pcAutoColorText').html('<i class="fa-solid fa-spinner fa-spin"></i> Consultando...');

        pcColorReq = $.ajax({
            url: 'ajax/citas.ajax.php',
            type: 'POST',
            data: { accion: 'colorPorOrden', idOrden: idOrden },
            dataType: 'json',
            global: false,
            success: function(resp) {
                if (resp && resp.ok) {
                    $('#colorCita').val(resp.color);
                    $('#pcAutoColorDot').css('background', resp.color);
                    var labels = {'#16a34a':'Excelente','#2563eb':'Bueno','#d97706':'Regular','#dc2626':'Bajo','#8b5cf6':'Cliente nuevo'};
                    var label = labels[resp.color] || 'General';
                    var detail = '';
                    if (resp.es_nuevo) { detail = 'Cliente nuevo (' + resp.total_ordenes + ' órdenes)'; }
                    else if (resp.calif !== null) { detail = label + ' — Calif: ' + resp.calif + '%'; }
                    else { detail = 'Sin historial suficiente'; }
                    $('#pcAutoColorText').text(detail).css('color','#334155');
                } else {
                    $('#colorCita').val('#3a87ad');
                    $('#pcAutoColorDot').css('background', '#3a87ad');
                    $('#pcAutoColorText').text(resp.error || 'Orden no encontrada').css('color','#ef4444');
                }
            },
            error: function(xhr) {
                if (xhr.statusText === 'abort') return;
                $('#colorCita').val('#3a87ad');
                $('#pcAutoColorDot').css('background', '#3a87ad');
                $('#pcAutoColorText').text('Error al consultar').css('color','#ef4444');
            }
        });
    });

    // Flag to prevent double submission
    var isSaving = false;

    // Manejar el envío del formulario
    $(document).off('submit', '#formularioCita').on('submit', '#formularioCita', function (e) {
        e.preventDefault();
        e.stopPropagation();

        if (isSaving) {
            return; // Exit if already saving
        }

        isSaving = true;
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);

        var titulo = $('#tituloCita').val();
        var fecha = $('#fechaCita').val();
        var color = $('#colorCita').val();
        var idOrden = $('#idOrden').val();

        if (!idOrden || idOrden < 1) {
            swal({
                icon: 'warning',
                title: 'Orden requerida',
                text: 'Debes vincular un No. de Orden o Pedido para crear la cita.'
            });
            isSaving = false;
            submitBtn.prop('disabled', false);
            $('#idOrden').focus();
            return;
        }

        var descripcion = $('#descripcionCita').val() || '';

        var datos = new FormData();
        datos.append("tituloCita", titulo);
        datos.append("fechaCita", fecha);
        datos.append("colorCita", color);
        datos.append("idOrden", idOrden);
        datos.append("descripcionCita", descripcion);

        $.ajax({
            url: "ajax/citas.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            success: function (respuesta) {
                if (respuesta.indexOf('duplicado_hora') === 0) {
                    var parts = respuesta.split('|');
                    swal({ icon: 'warning', title: 'Horario ocupado', text: 'Ya existe una cita a esa hora: "' + parts[1] + '" (' + parts[2] + '). Elige otra hora.' });
                } else if (respuesta.indexOf('duplicado_orden') === 0) {
                    var parts = respuesta.split('|');
                    swal({ icon: 'warning', title: 'Cita duplicada', text: 'Ya existe una cita para esta orden en ese día: "' + parts[1] + '" (' + parts[2] + ').' });
                } else if (respuesta == "ok") {
                    $('#modalAgregarCita').modal('hide');
                    document.getElementById('formularioCita').reset();
                    calendar.refetchEvents();
                    swal({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Cita agendada correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Hubo un error al guardar: ' + respuesta
                    });
                }
            },
            error: function (request, status, error) {
                swal({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo guardar la cita.'
                });
            },
            complete: function () {
                isSaving = false;
                submitBtn.prop('disabled', false);
            }
        });
    });
});
