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

        var datos = new FormData();
        datos.append("tituloCita", titulo);
        datos.append("fechaCita", fecha);
        datos.append("colorCita", color);

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
