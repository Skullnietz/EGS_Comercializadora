$(function () {
  var $tablaEmpresas = $(".tablaEmpresas");

  if ($tablaEmpresas.length && $.fn.DataTable && !$.fn.DataTable.isDataTable($tablaEmpresas[0])) {
    $tablaEmpresas.DataTable({
      responsive: true,
      pageLength: 25,
      order: [[1, "asc"]],
      columnDefs: [
        { targets: [0, 6], orderable: false, searchable: false }
      ],
      language: {
        emptyTable: "No hay empresas registradas",
        info: "Mostrando _START_ a _END_ de _TOTAL_ empresas",
        infoEmpty: "Mostrando 0 empresas",
        infoFiltered: "(filtrado de _MAX_ empresas)",
        lengthMenu: "Mostrar _MENU_",
        loadingRecords: "Cargando...",
        processing: "Procesando...",
        search: "Buscar:",
        zeroRecords: "No se encontraron empresas",
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

function mostrarErrorEmpresa(titulo, mensaje) {
  swal({
    title: titulo,
    text: mensaje,
    type: "error",
    confirmButtonText: "Cerrar"
  });
}

$(".tablaEmpresas").on("click", ".btnEditarEmpresa", function () {
  var $boton = $(this);
  var contenidoOriginal = $boton.html();
  var datos = new FormData();

  datos.append("idEmpresa", $boton.attr("idEmpresa"));
  $boton.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i>');

  $.ajax({
    url: "ajax/empresas.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json"
  })
    .done(function (respuesta) {
      if (!respuesta || respuesta.error) {
        $("#modalAgregarEmpresaEditada").modal("hide");
        mostrarErrorEmpresa("No se pudo cargar la empresa", respuesta && respuesta.error ? respuesta.error : "El registro ya no está disponible.");
        return;
      }

      $("#idEmpresa").val(respuesta.id || "");
      $("#editarNombreEmpresa").val(respuesta.empresa || "");
      $("#editarCorreoEmpresa").val(respuesta.correo || "");
      $("#editarNumeroUnoDeEmpresa").val(respuesta.telefono || "");
      $("#telefonoDosDeEmpresaEditado").val(respuesta.telefonoDos || "");
      $("#EditarDireccion").val(respuesta.direccion || "");
      $("#HoraEditada").val(respuesta.Horario || "");
      $("#FacebookEditado").val(respuesta.Facebook || "");
      $("#SitioEditado").val(respuesta.Sitio || "");
    })
    .fail(function (xhr) {
      $("#modalAgregarEmpresaEditada").modal("hide");
      var mensaje = xhr.status === 403
        ? "No tienes permisos para editar empresas."
        : "Verifica tu conexión e inténtalo nuevamente.";
      mostrarErrorEmpresa("No se pudo cargar la empresa", mensaje);
    })
    .always(function () {
      $boton.prop("disabled", false).html(contenidoOriginal);
    });
});

$(".tablaEmpresas").on("click", ".btnEliminarEmpresa", function () {
  var idEmpresa = $(this).attr("idEmpresa");
  var nombreEmpresa = $(this).attr("nombreEmpresa") || "esta empresa";

  swal({
    title: "¿Eliminar " + nombreEmpresa + "?",
    text: "Solo podrá eliminarse si no existen perfiles, técnicos o asesores relacionados.",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#dc2626",
    cancelButtonColor: "#64748b",
    cancelButtonText: "Cancelar",
    confirmButtonText: "Sí, eliminar"
  }).then(function (resultado) {
    if (resultado.value) {
      window.location = "index.php?ruta=empresas&idEmpresa=" + encodeURIComponent(idEmpresa);
    }
  });
});
