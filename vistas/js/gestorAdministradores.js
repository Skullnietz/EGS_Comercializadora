$(function () {
  var $tablaPerfiles = $(".tablaPerfiles");

  if ($tablaPerfiles.length && $.fn.DataTable && !$.fn.DataTable.isDataTable($tablaPerfiles[0])) {
    $tablaPerfiles.DataTable({
      responsive: true,
      pageLength: 25,
      order: [[1, "asc"]],
      columnDefs: [
        { targets: [0, 7], orderable: false, searchable: false }
      ],
      language: {
        decimal: "",
        emptyTable: "No hay perfiles registrados",
        info: "Mostrando _START_ a _END_ de _TOTAL_ perfiles",
        infoEmpty: "Mostrando 0 perfiles",
        infoFiltered: "(filtrado de _MAX_ perfiles)",
        lengthMenu: "Mostrar _MENU_",
        loadingRecords: "Cargando...",
        processing: "Procesando...",
        search: "Buscar:",
        zeroRecords: "No se encontraron perfiles",
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

function mostrarErrorPerfil(titulo, mensaje) {
  swal({
    title: titulo,
    text: mensaje,
    type: "error",
    confirmButtonText: "Cerrar"
  });
}

function actualizarEstadoVisualPerfil($boton, estadoDestino) {
  var activo = String(estadoDestino) === "1";

  $boton
    .toggleClass("is-active", activo)
    .toggleClass("is-inactive", !activo)
    .attr("estadoPerfil", activo ? "0" : "1")
    .html(
      '<i class="fa ' +
        (activo ? "fa-check-circle" : "fa-pause-circle") +
        '"></i><span>' +
        (activo ? "Activo" : "Inactivo") +
        "</span>"
    );
}

/* Activar o desactivar un perfil */
$(".tablaPerfiles").on("click", ".btnActivar", function () {
  var $boton = $(this);
  var estadoAnterior = $boton.hasClass("is-active") ? "1" : "0";
  var estadoDestino = String($boton.attr("estadoPerfil"));
  var datos = new FormData();

  datos.append("activarId", $boton.attr("idPerfil"));
  datos.append("activarPerfil", estadoDestino);
  $boton.prop("disabled", true);

  $.ajax({
    url: "ajax/administradores.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false
  })
    .done(function (respuesta) {
      if ($.trim(String(respuesta)) !== "ok") {
        mostrarErrorPerfil("No se pudo cambiar el estado", "Actualiza la página e inténtalo nuevamente.");
        actualizarEstadoVisualPerfil($boton, estadoAnterior);
        return;
      }

      actualizarEstadoVisualPerfil($boton, estadoDestino);
    })
    .fail(function () {
      mostrarErrorPerfil("No se pudo cambiar el estado", "Verifica tu conexión e inténtalo nuevamente.");
      actualizarEstadoVisualPerfil($boton, estadoAnterior);
    })
    .always(function () {
      $boton.prop("disabled", false);
    });
});

/* Previsualización de fotografía */
$(".nuevaFoto").on("change", function () {
  var input = this;
  var imagen = input.files && input.files[0];

  if (!imagen) {
    return;
  }

  if (["image/jpeg", "image/png"].indexOf(imagen.type) === -1) {
    $(input).val("");
    mostrarErrorPerfil("Error al subir la imagen", "La imagen debe estar en formato JPG o PNG.");
    return;
  }

  if (imagen.size > 2000000) {
    $(input).val("");
    mostrarErrorPerfil("Error al subir la imagen", "La imagen no debe pesar más de 2 MB.");
    return;
  }

  var lector = new FileReader();
  lector.onload = function (evento) {
    $(input).closest("form").find(".previsualizar").attr("src", evento.target.result);
  };
  lector.readAsDataURL(imagen);
});

function asegurarOpcionEmpresa($select, idEmpresa, nombreEmpresa) {
  if (!$select.length || !idEmpresa) {
    return;
  }

  if (!$select.find('option[value="' + idEmpresa + '"]').length) {
    $("<option>", {
      value: idEmpresa,
      text: nombreEmpresa || "Empresa asignada"
    }).appendTo($select);
  }

  $select.val(String(idEmpresa));
}

/* Cargar información para edición */
$(".tablaPerfiles").on("click", ".btnEditarPerfil", function () {
  var $boton = $(this);
  var textoOriginal = $boton.html();
  var datos = new FormData();

  datos.append("idPerfil", $boton.attr("idPerfil"));
  $boton.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i>');

  $.ajax({
    url: "ajax/administradores.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json"
  })
    .done(function (respuesta) {
      if (!respuesta || respuesta.error) {
        $("#modalEditarPerfil").modal("hide");
        mostrarErrorPerfil("No se pudo cargar el perfil", respuesta && respuesta.error ? respuesta.error : "El perfil ya no está disponible.");
        return;
      }

      $("#editarNombre").val(respuesta.nombre || "");
      $("#idPerfil").val(respuesta.id || "");
      $("#editarEmail").val(respuesta.email || "");
      $("#editarPerfilSelect").val(respuesta.perfil || "");
      $("#editarDepartamento").val(respuesta.Departamento || "");
      $("#fotoActual").val(respuesta.foto || "");
      $("#passwordActual").val(respuesta.password || "");

      asegurarOpcionEmpresa(
        $("#editarEmpresaPerfil").filter("select"),
        respuesta.id_empresa,
        respuesta.nombre_empresa
      );
      $("#editarEmpresaPerfil").filter("input").val(respuesta.id_empresa || "");
      $("#editarEmpresaPerfilVista").val(String(respuesta.id_empresa || ""));

      $("#editarNumeroUnoTecnico").val(respuesta.telefono_tec || "");
      $("#editarTelefonoDosTecnico").val(respuesta.telefonoDos_tec || "");
      $("#HoraDeComidaEditada").val(respuesta.HoraDeComida_tec || "");
      $("#editarAreratecnico").val(respuesta.areratecnico_tec || "");
      $("#editarNumeroUnoAsesor").val(respuesta.numeroTelefono_ase || "");
      $("#editarTelefonoDosAsesor").val(respuesta.numerodeCelular_ase || "");

      var foto = respuesta.foto || "vistas/img/perfiles/default/anonymous.png";
      $("#modalEditarPerfil .previsualizar").attr("src", foto);

      $("#editarPerfilSelect").trigger("change");
      $("#editarDepartamento").val(respuesta.Departamento || "");
    })
    .fail(function () {
      $("#modalEditarPerfil").modal("hide");
      mostrarErrorPerfil("No se pudo cargar el perfil", "Verifica tu conexión e inténtalo nuevamente.");
    })
    .always(function () {
      $boton.prop("disabled", false).html(textoOriginal);
    });
});

function filtrarDepartamentos(perfil, $select) {
  var grupos = {
    vendedor: ["Ventas", "Ventas Externas"],
    tecnico: ["Sistemas", "Electronica", "Impresoras", "Desarrollo"],
    administrador: ["Administracion", "Sistemas", "Desarrollo"],
    "Super-Administrador": ["Administracion", "Sistemas", "Desarrollo"],
    secretaria: ["Administracion", "Ventas"]
  };
  var permitidos = grupos[perfil] || [];
  var valorActual = $select.val();

  $select.find("option").each(function () {
    var valor = $(this).val();
    var mostrar = !valor || !perfil || valor === valorActual || permitidos.indexOf(valor) !== -1;
    $(this).prop("disabled", !mostrar).toggle(mostrar);
  });

  if (valorActual && !$select.find('option[value="' + valorActual + '"]').prop("disabled")) {
    $select.val(valorActual);
  } else if ($select.find("option:selected").prop("disabled")) {
    $select.val("");
  }
}

$("#nuevoPerfil").on("change", function () {
  var perfil = $(this).val();
  filtrarDepartamentos(perfil, $("#nuevoDepartamento"));

  $("#divAdicionalTecnico").stop(true, true).slideToggle(perfil === "tecnico");
  $("#divAdicionalAsesor").stop(true, true).slideToggle(perfil === "vendedor");
});

$("#editarPerfilSelect").on("change", function () {
  var perfil = $(this).val();
  filtrarDepartamentos(perfil, $("#editarDepartamento"));

  $("#divAdicionalTecnicoEdit").stop(true, true).slideToggle(perfil === "tecnico");
  $("#divAdicionalAsesorEdit").stop(true, true).slideToggle(perfil === "vendedor");
});

/* Eliminar perfil */
$(".tablaPerfiles").on("click", ".btnEliminarPerfil", function () {
  var idPerfil = $(this).attr("idPerfil");
  var fotoPerfil = $(this).attr("fotoPerfil") || "";
  var nombrePerfil = $(this).attr("nombrePerfil") || "este perfil";

  swal({
    title: "¿Eliminar " + nombrePerfil + "?",
    text: "El perfil y su registro relacionado dejarán de estar disponibles.",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#dc2626",
    cancelButtonColor: "#64748b",
    cancelButtonText: "Cancelar",
    confirmButtonText: "Sí, eliminar"
  }).then(function (resultado) {
    if (resultado.value) {
      window.location =
        "index.php?ruta=perfiles&idPerfil=" +
        encodeURIComponent(idPerfil) +
        "&fotoPerfil=" +
        encodeURIComponent(fotoPerfil);
    }
  });
});

/* Validar confirmación de contraseña solo en los formularios de perfiles */
$("#modalAgregarPerfil form, #modalEditarPerfil form").on("submit", function (evento) {
  var $formulario = $(this);
  var $password = $formulario.find("#nuevoPassword, #editarPassword");
  var $confirmacion = $formulario.find("#nuevoPasswordConfirmar, #editarPasswordConfirmar");

  if ($password.length && $confirmacion.length && $password.val() !== $confirmacion.val()) {
    evento.preventDefault();
    mostrarErrorPerfil("Las contraseñas no coinciden", "Revisa ambos campos antes de guardar.");
    return false;
  }
});
