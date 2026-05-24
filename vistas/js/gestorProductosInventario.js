/* Gestor Productos — Vista Inventario IT */
(function ($) {
  "use strict";

  if (!$(".tablaInventarioProductos").length) {
    return;
  }

  var $tabla = $(".tablaInventarioProductos");
  var filtroActual = "todos";
  var productoSeleccionadoId = null;
  var dtInventario = null;

  function getEmpresa() {
    return $("#id_empresa").val() || "";
  }

  function getTipoCambio() {
    return parseFloat($("#invTipoCambio").val()) || 17.5;
  }

  function urlTabla() {
    return "ajax/tablaProductosInventario.ajax.php?empresa=" + getEmpresa()
      + "&filtro=" + filtroActual
      + "&tipoCambio=" + getTipoCambio();
  }

  function toast(msg, tipo) {
    if (typeof swal === "function") {
      swal({ title: msg, type: tipo || "info", timer: 1800, showConfirmButton: false });
      return;
    }
    alert(msg);
  }

  function recargarTabla() {
    if (dtInventario) {
      dtInventario.ajax.url(urlTabla()).load(recargarKpis);
    }
  }

  function recargarKpis() {
    var datos = new FormData();
    datos.append("accionInventario", "resumenInventario");
    datos.append("idEmpresa", getEmpresa());
    $.ajax({
      url: "ajax/productos.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (r) {
        $("#kpiTotalActivos").text(r.total_activos || 0);
        $("#kpiStockBajo").text(r.stock_bajo || 0);
        $("#kpiSinStock").text(r.sin_stock || 0);
        $("#kpiValorInventario").text("$" + Number(r.valor_inventario || 0).toLocaleString("es-MX", { minimumFractionDigits: 0, maximumFractionDigits: 0 }));
      }
    });
  }

  function resaltarFilaPorCodigo(codigo) {
    var encontrado = false;
    $tabla.find("tbody tr").each(function () {
      var $row = $(this);
      var codigoFila = $row.find(".inv-codigo").data("codigo") || $row.find(".inv-codigo").text();
      if ($.trim(String(codigoFila)) === $.trim(String(codigo))) {
        $row.addClass("inv-row-highlight");
        productoSeleccionadoId = $row.find(".btnEditarProducto").attr("idProducto");
        $("html, body").animate({ scrollTop: $row.offset().top - 120 }, 300);
        setTimeout(function () { $row.removeClass("inv-row-highlight"); }, 2500);
        encontrado = true;
        return false;
      }
    });
    return encontrado;
  }

  function buscarPorCodigo(codigo, abrirStock) {
    if (!codigo) return;
    var datos = new FormData();
    datos.append("codigoProducto", codigo);
    $.ajax({
      url: "ajax/productos.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {
        if (respuesta && respuesta.length) {
          var p = respuesta[0];
          if (!resaltarFilaPorCodigo(codigo) && dtInventario) {
            dtInventario.search(p.codigo || codigo).draw();
          }
          if (abrirStock) {
            abrirModalStock(p.id, p.disponibilidad, p.titulo);
          }
        } else {
          swal({
            title: "Producto no encontrado",
            text: "¿Desea crear un producto con código " + codigo + "?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Crear producto",
            cancelButtonText: "Cancelar"
          }).then(function (result) {
            if (result.value || result.isConfirmed) {
              $("#modalAgregarProducto").modal("show");
              $(".SubircodigoProducto").val(codigo);
            }
          });
        }
      }
    });
  }

  function abrirModalStock(id, stock, titulo) {
    $("#stockProductoId").val(id);
    $("#stockActualLabel").text(stock);
    $("#stockAjusteInput").val(0);
    $("#stockMotivoInput").val("");
    $("#modalAjustarStock .modal-title").text("Ajustar stock — " + (titulo || ""));
    $("#modalAjustarStock").modal("show");
  }

  function guardarAjusteStock() {
    var id = $("#stockProductoId").val();
    var ajuste = parseInt($("#stockAjusteInput").val(), 10);
    if (!id || isNaN(ajuste) || ajuste === 0) {
      toast("Ingrese un ajuste válido (+/-)", "warning");
      return;
    }
    var datos = new FormData();
    datos.append("accionInventario", "ajustarStock");
    datos.append("idProducto", id);
    datos.append("ajusteStock", ajuste);
    $.ajax({
      url: "ajax/productos.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      success: function (r) {
        if (r === "ok") {
          $("#modalAjustarStock").modal("hide");
          recargarTabla();
          toast("Stock actualizado", "success");
        } else {
          toast("Error al actualizar stock", "error");
        }
      }
    });
  }

  function generarQrEnContenedor($container, texto) {
    $container.empty();
    if (typeof QRCode === "undefined") {
      $container.html("<p class=\"text-danger\">Librería QR no disponible</p>");
      return;
    }
    new QRCode($container[0], {
      text: texto,
      width: 180,
      height: 180,
      colorDark: "#0f172a",
      colorLight: "#ffffff",
      correctLevel: QRCode.CorrectLevel.M
    });
  }

  function urlProductoDeepLink(codigo) {
    var base = window.location.pathname + "?ruta=productos&codigo=" + encodeURIComponent(codigo);
    return window.location.origin + window.location.pathname.replace(/[^/]*$/, "") + base.replace(/^\//, "");
  }

  function abrirModalQr(codigo, titulo, precio) {
    var link = "index.php?ruta=productos&codigo=" + encodeURIComponent(codigo);
    $("#qrProductoTitulo").text(titulo || codigo);
    $("#qrProductoCodigo").text(codigo);
    $("#qrProductoPrecio").text(precio > 0 ? ("$" + Number(precio).toFixed(2) + " MXN") : "Gratis");
    generarQrEnContenedor($("#qrCodeContainer"), link);
    $("#modalVerQr").modal("show");
  }

  function imprimirEtiqueta(codigo, titulo, precio) {
    var precioTxt = precio > 0 ? ("$" + Number(precio).toFixed(2) + " MXN") : "Gratis";
    var html = "<div id=\"invPrintLabel\" style=\"text-align:center;padding:12px;font-family:sans-serif\">"
      + "<strong style=\"font-size:13px\">" + titulo + "</strong><br>"
      + "<span style=\"font-size:11px\">" + codigo + "</span><br>"
      + "<svg id=\"invBarcodePrint\"></svg><br>"
      + "<span style=\"font-size:12px;font-weight:bold\">" + precioTxt + "</span>"
      + "</div>";
    $("body").append(html);
    if (typeof JsBarcode !== "undefined") {
      JsBarcode("#invBarcodePrint", codigo, { format: "CODE128", width: 1.5, height: 50, displayValue: true });
    }
    if ($.fn.printArea) {
      $("#invPrintLabel").printArea();
    } else {
      window.print();
    }
    setTimeout(function () { $("#invPrintLabel").remove(); }, 1000);
  }

  dtInventario = $tabla.DataTable({
    ajax: urlTabla(),
    deferRender: true,
    retrieve: true,
    processing: true,
    order: [[3, "asc"]],
    columnDefs: [
      { orderable: false, targets: [0, 1, 7] },
      { className: "text-center", targets: [0, 1, 4, 7] }
    ],
    language: {
      sProcessing: "Procesando...",
      sLengthMenu: "Mostrar _MENU_ registros",
      sZeroRecords: "No se encontraron resultados",
      sEmptyTable: "Ningún dato disponible",
      sInfo: "Mostrando _START_ a _END_ de _TOTAL_",
      sInfoEmpty: "0 registros",
      sInfoFiltered: "(filtrado de _MAX_)",
      sSearch: "Buscar:",
      oPaginate: { sFirst: "Primero", sLast: "Último", sNext: "Siguiente", sPrevious: "Anterior" }
    }
  });

  recargarKpis();

  $(".inv-filtro-chip").on("click", function () {
    $(".inv-filtro-chip").removeClass("active");
    $(this).addClass("active");
    filtroActual = $(this).data("filtro") || "todos";
    recargarTabla();
  });

  $("#invScannerInput").on("keydown", function (e) {
    if (e.key === "Enter" || e.keyCode === 13) {
      e.preventDefault();
      var codigo = $.trim($(this).val());
      $(this).val("");
      if (codigo) buscarPorCodigo(codigo, true);
    }
  });

  $("#btnEnfocarScanner").on("click", function () {
    $("#invScannerInput").focus();
  });

  $(document).on("click", ".inv-codigo", function () {
    var codigo = $(this).data("codigo") || $.trim($(this).text());
    if (navigator.clipboard) {
      navigator.clipboard.writeText(codigo);
      toast("Código copiado: " + codigo, "success");
    }
  });

  $tabla.on("click", ".btnAjustarStock", function () {
    abrirModalStock(
      $(this).attr("idProducto"),
      $(this).attr("stockActual"),
      $(this).attr("tituloProducto")
    );
  });

  $tabla.on("click", ".btnVerQr", function () {
    abrirModalQr(
      $(this).attr("codigoProducto"),
      $(this).attr("tituloProducto"),
      parseFloat($(this).attr("precioProducto")) || 0
    );
  });

  $tabla.on("click", ".btnImprimirEtiqueta", function () {
    imprimirEtiqueta(
      $(this).attr("codigoProducto"),
      $(this).attr("tituloProducto"),
      parseFloat($(this).attr("precioProducto")) || 0
    );
  });

  $tabla.on("click", ".btnEditarProducto", function () {
    productoSeleccionadoId = $(this).attr("idProducto");
  });

  $("#btnGuardarAjusteStock").on("click", guardarAjusteStock);

  $("#btnGuardarTipoCambio").on("click", function () {
    var tc = parseFloat($("#inputTipoCambio").val());
    if (!tc || tc <= 0) {
      toast("Tipo de cambio inválido", "warning");
      return;
    }
    var datos = new FormData();
    datos.append("accionInventario", "setTipoCambio");
    datos.append("tipoCambio", tc);
    $.ajax({
      url: "ajax/productos.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (r) {
        if (r.ok) {
          $("#invTipoCambio").val(r.tipoCambio);
          $("#labelTipoCambio").text(Number(r.tipoCambio).toFixed(2));
          $("#modalTipoCambio").modal("hide");
          recargarTabla();
          toast("Tipo de cambio actualizado", "success");
        }
      }
    });
  });

  $("#btnImprimirQr").on("click", function () {
    if ($.fn.printArea) {
      $("#qrPrintArea").printArea();
    } else {
      window.print();
    }
  });

  $(document).on("keydown", function (e) {
    if (e.key === "F2") {
      e.preventDefault();
      $("#invScannerInput").focus();
    }
    if (e.key === "Escape") {
      $("#invScannerInput").val("");
    }
    if (e.ctrlKey && e.key === "n") {
      e.preventDefault();
      $("#modalAgregarProducto").modal("show");
    }
    if (e.ctrlKey && e.key === "e" && productoSeleccionadoId) {
      e.preventDefault();
      $tabla.find('.btnEditarProducto[idProducto="' + productoSeleccionadoId + '"]').first().click();
    }
  });

  var deepCodigo = $("#invDeepLinkCodigo").val();
  if (deepCodigo) {
    setTimeout(function () {
      buscarPorCodigo(deepCodigo, false);
      $("#invScannerInput").val(deepCodigo);
    }, 800);
  }

  window.recargarInventarioProductos = recargarTabla;

})(jQuery);
