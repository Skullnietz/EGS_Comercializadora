$(function () {
  var $tablaAsesores = $(".tablaAsesores");

  if ($tablaAsesores.length && $.fn.DataTable && !$.fn.DataTable.isDataTable($tablaAsesores[0])) {
    $tablaAsesores.DataTable({
      responsive: true,
      pageLength: 25,
      order: [[1, "asc"]],
      columnDefs: [
        { targets: [0, 6], orderable: false, searchable: false }
      ],
      language: {
        emptyTable: "No hay asesores registrados",
        info: "Mostrando _START_ a _END_ de _TOTAL_ asesores",
        infoEmpty: "Mostrando 0 asesores",
        infoFiltered: "(filtrado de _MAX_ asesores)",
        lengthMenu: "Mostrar _MENU_",
        loadingRecords: "Cargando...",
        processing: "Procesando...",
        search: "Buscar:",
        zeroRecords: "No se encontraron asesores",
        paginate: {
          first: "Primero",
          last: "Último",
          next: "Siguiente",
          previous: "Anterior"
        }
      }
    });
  }
});

function mostrarErrorAsesor(titulo, mensaje) {
  swal({
    title: titulo,
    text: mensaje,
    type: "error",
    confirmButtonText: "Cerrar"
  });
}

function asegurarEmpresaAsesor($select, idEmpresa) {
  if (!$select.length || !idEmpresa) {
    return;
  }

  if (!$select.find('option[value="' + idEmpresa + '"]').length) {
    $("<option>", {
      value: idEmpresa,
      text: "Empresa asignada"
    }).appendTo($select);
  }

  $select.val(String(idEmpresa));
}

$(document).on("click", ".btnEditarDatosAsesor", function (evento) {
  evento.preventDefault();

  var $boton = $(this);
  var contenidoOriginal = $boton.html();
  var idAsesor = $boton.attr("data-id-asesor") || $boton.attr("idAsesor");
  var datos = new FormData();

  if (!idAsesor) {
    mostrarErrorAsesor("No se pudo editar el asesor", "El registro no tiene un identificador válido.");
    return;
  }

  datos.append("idAsesor", idAsesor);
  $boton.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i>');

  $.ajax({
    url: "ajax/Asesores.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json"
  })
    .done(function (respuesta) {
      if (!respuesta || respuesta.error) {
        mostrarErrorAsesor("No se pudo cargar el asesor", respuesta && respuesta.error ? respuesta.error : "El registro ya no está disponible.");
        return;
      }

      $("#idAsesor").val(respuesta.id || "");
      $("#editarNombreAsesor").val(respuesta.nombre || "");
      $("#editarEmailAsesor").val(respuesta.correo || "");
      $("#editarNumeroUno").val(respuesta.numerodeCelular || "");
      $("#editarTelefonoDos").val(respuesta.numeroTelefono || "");
      $("#editarPorcentajeComision").val(respuesta.porcentajeComision || 0);
      $("#editarEstadoAsesor").val(respuesta.estado === "Inactivo" ? "Inactivo" : "Activo");

      asegurarEmpresaAsesor($("#editarEmpresaAsesor").filter("select"), respuesta.id_empresa);
      $("#editarEmpresaAsesor").filter("input").val(respuesta.id_empresa || "");
      $("#editarEmpresaAsesorVista").val(String(respuesta.id_empresa || ""));

      $("#modalAgregarAsesorEditado").modal("show");
    })
    .fail(function (xhr) {
      var mensaje = xhr.status === 403
        ? "No tienes permisos para editar este asesor."
        : "Verifica tu conexión e inténtalo nuevamente.";
      mostrarErrorAsesor("No se pudo cargar el asesor", mensaje);
    })
    .always(function () {
      $boton.prop("disabled", false).html(contenidoOriginal);
    });
});

$(".tablaAsesores").on("click", ".btnEliminarAsesor", function () {
  var idAsesor = $(this).attr("idAsesor");
  var nombreAsesor = $(this).attr("nombreAsesor") || "este asesor";

  swal({
    title: "¿Eliminar " + nombreAsesor + "?",
    text: "El asesor dejará de estar disponible en el directorio comercial.",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#dc2626",
    cancelButtonColor: "#64748b",
    cancelButtonText: "Cancelar",
    confirmButtonText: "Sí, eliminar"
  }).then(function (resultado) {
    if (resultado.value) {
      window.location = "index.php?ruta=asesores&idAsesor=" + encodeURIComponent(idAsesor);
    }
  });
});

$(document).on("click", ".btnAbrirAgendarCitaAsesor", function () {
  $("#crTitulo").val("");
  $("#crOrdenId").val("");
  $("#modalCitaRapida").modal("show");
});

$(document).on("click", ".btnAgendarCitaAsesor", function () {
  $("#crTitulo").val("Cita con " + ($(this).data("nombre") || ""));
  $("#crOrdenId").val("");
  $("#modalCitaRapida").modal("show");
});
