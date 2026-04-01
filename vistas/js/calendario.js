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
            swal({
                title: info.event.title,
                text: 'Fecha: ' + info.event.start.toLocaleString(),
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ok',
                cancelButtonText: 'Eliminar'
            }).then((result) => {
                if (result.dismiss === 'cancel') {

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
                                swal(
                                    'Eliminado!',
                                    'La cita ha sido eliminada.',
                                    'success'
                                )
                            } else {
                                swal('Error', 'No se pudo eliminar la cita', 'error');
                            }
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

        var datos = new FormData();
        datos.append("tituloCita", titulo);
        datos.append("fechaCita", fecha);
        datos.append("colorCita", color);
        datos.append("idOrden", idOrden);

        $.ajax({
            url: "ajax/citas.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            success: function (respuesta) {
                if (respuesta == "ok") {
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
