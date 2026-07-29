(function ($) {
  "use strict";

  if (
    $.fn.DataTable &&
    $(".tablaTecnicos").length &&
    !$.fn.DataTable.isDataTable(".tablaTecnicos")
  ) {
    $(".tablaTecnicos").DataTable({
      responsive: true,
      pageLength: 25,
      order: [[7, "desc"]],
      columnDefs: [
        { orderable: false, targets: [8] }
      ],
      language: {
        sProcessing: "Procesando...",
        sLengthMenu: "Mostrar _MENU_ registros",
        sZeroRecords: "No se encontraron técnicos",
        sEmptyTable: "Aún no hay técnicos registrados",
        sInfo: "Mostrando _START_ a _END_ de _TOTAL_ técnicos",
        sInfoEmpty: "Mostrando 0 técnicos",
        sInfoFiltered: "(filtrado de _MAX_ registros)",
        sSearch: "Buscar:",
        oPaginate: {
          sFirst: "Primero",
          sLast: "Último",
          sNext: "Siguiente",
          sPrevious: "Anterior"
        }
      }
    });
  }

  $(".tablaTecnicos").on("click", ".btnEditarDatosTecnico", function () {
    var idTecnico = $(this).attr("idTecnico");
    var $modal = $("#modalAgregarTecnicoEditado");
    var $botonGuardar = $modal.find('button[type="submit"]');
    var datos = new FormData();

    datos.append("idTecnico", idTecnico);
    $botonGuardar.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');

    $.ajax({
      url: "ajax/Tecnicos.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {
        if (!respuesta || respuesta.error) {
          mostrarErrorTecnico(respuesta && respuesta.error ? respuesta.error : "No fue posible cargar el técnico.");
          $modal.modal("hide");
          return;
        }

        $("#idTecnico").val(respuesta.id);
        $("#editarNombreTecnico").val(respuesta.nombre || "");
        $("#editarEmailTecnico").val(respuesta.correo || "");
        $("#editarNumeroUnoTecnico").val(respuesta.telefono || "");
        $("#editarTelefonoDosTecnico").val(respuesta.telefonoDos || "");
        $("#HoraDeComidaEditada").val(respuesta.HoraDeComida || "");
        $("#editarEstadoTecnico").val(respuesta.estado || "Activo");

        asignarOpcionDisponible(
          $("#editarAreaTecnico"),
          respuesta.departamento || "",
          respuesta.departamento || "Departamento actual"
        );

        var idEmpresa = respuesta.id_empresa ? String(respuesta.id_empresa) : "";
        var $empresaEditable = $("#editarEmpresaTecnico");
        var $empresaVisible = $("#editarEmpresaTecnicoVista");

        if ($empresaEditable.is("select")) {
          asignarOpcionDisponible(
            $empresaEditable,
            idEmpresa,
            idEmpresa ? "Empresa #" + idEmpresa : "Empresa actual"
          );
        } else {
          $empresaEditable.val(idEmpresa);
        }

        if ($empresaVisible.length) {
          $empresaVisible.val(idEmpresa);
        }
      },
      error: function (xhr) {
        var mensaje = "No fue posible cargar los datos del técnico.";

        if (xhr.responseJSON && xhr.responseJSON.error) {
          mensaje = xhr.responseJSON.error;
        }

        $modal.modal("hide");
        mostrarErrorTecnico(mensaje);
      },
      complete: function () {
        $botonGuardar
          .prop("disabled", false)
          .html('<i class="fas fa-save"></i> Guardar cambios');
      }
    });
  });

  $(".tablaTecnicos").on("click", ".btnEliminarTecnico", function () {
    var idTecnico = $(this).attr("idTecnico");
    var nombreTecnico = $(this).attr("nombreTecnico") || "este técnico";

    swal({
      title: "¿Eliminar a " + nombreTecnico + "?",
      text: "El registro se eliminará del catálogo de técnicos.",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#be123c",
      cancelButtonColor: "#64748b",
      cancelButtonText: "Cancelar",
      confirmButtonText: "Sí, eliminar"
    }).then(function (result) {
      if (result.value) {
        window.location = "index.php?ruta=tecnicos&idtecnico=" + encodeURIComponent(idTecnico);
      }
    });
  });

  $("#modalAgregarTecnicoEditado").on("hidden.bs.modal", function () {
    var formulario = $(this).find("form").get(0);

    if (formulario) {
      formulario.reset();
    }

    $(this).find('option[data-temporal="1"]').remove();
    $("#idTecnico").val("");
  });

  function asignarOpcionDisponible($select, valor, etiqueta) {
    if (!$select.length) {
      return;
    }

    valor = valor === null || typeof valor === "undefined" ? "" : String(valor);

    if (valor && !$select.find("option").filter(function () {
      return String($(this).val()) === valor;
    }).length) {
      $("<option>", {
        value: valor,
        text: etiqueta,
        "data-temporal": "1"
      }).appendTo($select);
    }

    $select.val(valor);
  }

  function mostrarErrorTecnico(mensaje) {
    swal({
      type: "error",
      title: mensaje,
      showConfirmButton: true,
      confirmButtonText: "Cerrar"
    });
  }
})(jQuery);
