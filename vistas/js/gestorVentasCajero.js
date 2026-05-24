(function ($) {
  "use strict";

  if (!$("#posCajeroRoot").length) {
    return;
  }

  var carrito = [];
  var dtCatalogo = null;
  var clienteChoices = null;
  var egsSaldoMonederoCliente = 0;
  var egsCanjeAplicadoActual = 0;
  var totalSinCanje = 0;
  var ultimaVentaTicketUrl = "";

  var $scanner = $("#posScannerInput");
  var $cartBody = $("#posCartBody");
  var $cartEmpty = $("#posCartEmpty");
  var $cartWrap = $("#posCartWrap");
  var $cartCount = $("#posCartCount");
  var $btnCobrar = $("#posBtnCobrar");
  var $clienteSelect = document.getElementById("egs_clienteVentaPOS");

  function formatMoney(n) {
    return "$" + (parseFloat(n) || 0).toFixed(2);
  }

  function normalizarCodigoEscaneado(raw) {
    var s = (raw || "").trim();
    var m = s.match(/[?&]codigo=([^&]+)/i);
    return m ? decodeURIComponent(m[1]) : s;
  }

  var toastIcons = {
    success: "fa-circle-check",
    error: "fa-circle-xmark",
    warning: "fa-triangle-exclamation",
    info: "fa-circle-info"
  };

  function toast(msg, type) {
    type = type || "info";
    var $host = $("#posToastHost");
    if (!$host.length) return;

    var $el = $(
      '<div class="pos-toast pos-toast-' + type + '">' +
        '<i class="fa-solid pos-toast-icon ' + (toastIcons[type] || toastIcons.info) + '"></i>' +
        '<span class="pos-toast-msg">' + escapeHtml(msg) + '</span>' +
      '</div>'
    );
    $host.append($el);

    setTimeout(function () {
      $el.fadeOut(200, function () { $(this).remove(); });
    }, type === "error" ? 4200 : 2800);
  }

  function enfocarScanner() {
    $scanner.val("").focus();
  }

  function buscarProductoPorCodigo(codigo) {
    var datos = new FormData();
    datos.append("codigoProducto", codigo);
    return $.ajax({
      url: "ajax/productos.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json"
    });
  }

  function buscarProductoPorId(idProducto) {
    var datos = new FormData();
    datos.append("idProducto", idProducto);
    return $.ajax({
      url: "ajax/productos.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json"
    });
  }

  function productoDesdeRespuesta(respuesta) {
    if (!respuesta || !respuesta.length) return null;
    var p = respuesta[0];
    return {
      id: parseInt(p.id, 10),
      titulo: p.titulo || "",
      codigo: p.codigo || "",
      precio: parseFloat(p.precio) || 0,
      stock: parseInt(p.disponibilidad, 10) || 0,
      medida: p.medida || "PZAS"
    };
  }

  function agregarAlCarrito(producto, cantidadAgregar) {
    cantidadAgregar = cantidadAgregar || 1;
    if (!producto || !producto.id) return false;

    if (producto.stock <= 0) {
      toast("Sin stock: " + producto.titulo, "error");
      return false;
    }

    var existente = null;
    for (var i = 0; i < carrito.length; i++) {
      if (carrito[i].id === producto.id) {
        existente = carrito[i];
        break;
      }
    }

    if (existente) {
      var nuevaCant = existente.cantidad + cantidadAgregar;
      if (nuevaCant > producto.stock) {
        toast("Stock insuficiente: solo " + producto.stock + " " + producto.medida, "warning");
        return false;
      }
      existente.cantidad = nuevaCant;
      existente.stock = producto.stock;
    } else {
      if (cantidadAgregar > producto.stock) {
        toast("Stock insuficiente: solo " + producto.stock + " " + producto.medida, "warning");
        return false;
      }
      carrito.push({
        id: producto.id,
        titulo: producto.titulo,
        codigo: producto.codigo,
        cantidad: cantidadAgregar,
        precio: producto.precio,
        stock: producto.stock,
        medida: producto.medida
      });
    }

    renderCarrito();
    toast("Agregado: " + producto.titulo, "success");
    return true;
  }

  function renderCarrito() {
    $cartBody.empty();
    if (!carrito.length) {
      $cartEmpty.show();
      $cartWrap.hide();
      $cartCount.text("0");
      $btnCobrar.prop("disabled", true);
      serializarCarrito();
      recalcularTotales();
      return;
    }

    $cartEmpty.hide();
    $cartWrap.show();
    var totalItems = 0;

    carrito.forEach(function (item, idx) {
      totalItems += item.cantidad;
      var subtotal = item.precio * item.cantidad;
      var row = $(
        "<tr data-idx=\"" + idx + "\">" +
          "<td>" +
            "<strong>" + escapeHtml(item.titulo) + "</strong><br>" +
            "<span class=\"pos-line-code\">" + escapeHtml(item.codigo) + "</span> · " +
            "<span class=\"pos-line-sub\">" + escapeHtml(item.medida) + "</span>" +
          "</td>" +
          "<td>" +
            "<div class=\"pos-qty-controls\">" +
              "<button type=\"button\" class=\"pos-btn-qty-minus\" data-idx=\"" + idx + "\">−</button>" +
              "<input type=\"number\" class=\"pos-qty-input\" data-idx=\"" + idx + "\" min=\"1\" max=\"" + item.stock + "\" value=\"" + item.cantidad + "\">" +
              "<button type=\"button\" class=\"pos-btn-qty-plus\" data-idx=\"" + idx + "\">+</button>" +
            "</div>" +
          "</td>" +
          "<td>" + formatMoney(item.precio) + "</td>" +
          "<td><strong>" + formatMoney(subtotal) + "</strong></td>" +
          "<td><button type=\"button\" class=\"btn btn-xs btn-danger pos-btn-quitar\" data-idx=\"" + idx + "\"><i class=\"fa fa-times\"></i></button></td>" +
        "</tr>"
      );
      $cartBody.append(row);
    });

    $cartCount.text(totalItems);
    $btnCobrar.prop("disabled", false);
    serializarCarrito();
    recalcularTotales();
  }

  function escapeHtml(str) {
    return String(str || "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function serializarCarrito() {
    var lista = carrito.map(function (item) {
      return {
        id: item.id,
        titulo: item.titulo,
        codigo: item.codigo,
        cantidad: item.cantidad,
        precio: item.precio,
        stock: Math.max(0, item.stock - item.cantidad),
        medida: item.medida,
        total: (item.precio * item.cantidad).toFixed(2)
      };
    });
    $("#listaProductos").val(JSON.stringify(lista));
  }

  function recalcularTotales() {
    var subtotal = 0;
    carrito.forEach(function (item) {
      subtotal += item.precio * item.cantidad;
    });

    var pct = parseFloat($("#posDescuentoPct").val()) || 0;
    if (pct < 0) pct = 0;
    if (pct > 100) pct = 100;
    var descuento = subtotal * (pct / 100);
    totalSinCanje = Math.max(0, subtotal - descuento);

    $("#posSubtotalLabel").text(formatMoney(subtotal));
    $("#posDescuentoLabel").text("-" + formatMoney(descuento));

    aplicarCanjeMonedero();
    actualizarCambioEfectivo();
    validarCobro();
  }

  function aplicarCanjeMonedero() {
    var canje = parseFloat($("#egsMontoCanjeVenta").val()) || 0;
    if (canje < 0) canje = 0;
    if (canje > egsSaldoMonederoCliente) canje = egsSaldoMonederoCliente;
    if (canje > totalSinCanje) canje = totalSinCanje;

    var totalFinal = Math.max(0, totalSinCanje - canje);
    $("#egsMontoCanjeVenta").val(canje.toFixed(2));
    $("#totalVenta").val(totalSinCanje.toFixed(2));
    $("#nuevoTotalVenta").val(totalFinal.toFixed(2));
    $("#posTotalDisplay").text(formatMoney(totalFinal));
    egsCanjeAplicadoActual = canje;
    actualizarCambioEfectivo();
  }

  function actualizarCambioEfectivo() {
    if ($("#nuevoMetodoPago").val() !== "efectivo") return;
    var recibido = parseFloat($("#posEfectivoRecibido").val()) || 0;
    var total = parseFloat($("#nuevoTotalVenta").val()) || 0;
    var cambio = Math.max(0, recibido - total);
    $("#posEfectivoCambio").val(cambio.toFixed(2));
  }

  function listarMetodoPago() {
    var metodo = $("#nuevoMetodoPago").val();
    if (metodo === "efectivo") {
      $("#listaMetodoPago").val("efectivo");
    } else if (metodo) {
      var ref = ($("#nuevoCodigoTransaccion").val() || "").trim();
      $("#listaMetodoPago").val(metodo + (ref ? "-" + ref : ""));
    } else {
      $("#listaMetodoPago").val("");
    }
  }

  function validarCobro() {
    var ok = carrito.length > 0;
    var clienteId = parseInt($("#id_cliente").val(), 10) || 0;
    var asesorId = parseInt($("#posAsesor").val(), 10) || 0;
    var metodo = $("#nuevoMetodoPago").val();
    ok = ok && clienteId > 0 && asesorId > 0 && metodo !== "";
    $btnCobrar.prop("disabled", !ok);
  }

  function vaciarCarrito() {
    carrito = [];
    renderCarrito();
  }

  function procesarEscaneo() {
    var codigo = normalizarCodigoEscaneado($scanner.val());
    if (!codigo) return;

    buscarProductoPorCodigo(codigo).done(function (respuesta) {
      var producto = productoDesdeRespuesta(respuesta);
      if (!producto) {
        toast("Producto no encontrado: " + codigo, "error");
        enfocarScanner();
        return;
      }
      if (agregarAlCarrito(producto, 1)) {
        enfocarScanner();
      }
    }).fail(function () {
      toast("Error al buscar producto", "error");
      enfocarScanner();
    });
  }

  function initClienteChoices() {
    if (!window.Choices || !$clienteSelect) return;
    if (clienteChoices) {
      clienteChoices.destroy();
    }
    clienteChoices = new Choices($clienteSelect, {
      searchEnabled: true,
      shouldSort: false,
      itemSelectText: "",
      searchPlaceholderValue: "Escribe para buscar...",
      noResultsText: "Sin resultados",
      noChoicesText: "Sin opciones"
    });
  }

  function syncClienteHidden(val) {
    var idCliente = parseInt(val, 10) || 0;
    var nombre = "";
    if (idCliente > 0 && $clienteSelect) {
      var opt = $clienteSelect.querySelector('option[value="' + idCliente + '"]');
      nombre = opt ? (opt.getAttribute("data-nombre") || opt.textContent) : "";
    }
    $("#id_cliente").val(idCliente > 0 ? idCliente : 0);
    $("#seleccionarCliente").val(idCliente > 0 ? idCliente : "");
    $("#nombreCliente").val(nombre);
    validarCobro();
  }

  function cargarMonedero(idCliente) {
    $("#egsMontoCanjeVenta").val(0);
    $("#egsMonederoMsg").text("");
    egsSaldoMonederoCliente = 0;
    egsCanjeAplicadoActual = 0;

    if (!idCliente || idCliente <= 0) {
      $("#egsMonederoVentaRapida").hide();
      recalcularTotales();
      return;
    }

    $.ajax({
      url: "ajax/recompensas.ajax.php",
      method: "POST",
      data: { idClienteRecompensas: idCliente },
      dataType: "json",
      success: function (resp) {
        var saldo = resp && typeof resp.saldo !== "undefined" ? parseFloat(resp.saldo) || 0 : 0;
        egsSaldoMonederoCliente = saldo;
        $("#egsSaldoMonederoLabel").text(formatMoney(saldo));
        $("#egsMontoCanjeVenta").attr("max", saldo);
        if (saldo > 0) {
          $("#egsMonederoVentaRapida").show();
          $("#egsMonederoMsg").text("Puedes aplicar hasta " + formatMoney(saldo) + ".").css("color", "#16a34a");
          $("#egsMontoCanjeVenta").prop("disabled", false);
        } else {
          $("#egsMonederoVentaRapida").show();
          $("#egsMonederoMsg").text("Este cliente no tiene saldo disponible.").css("color", "#64748b");
          $("#egsMontoCanjeVenta").prop("disabled", true);
        }
        recalcularTotales();
      },
      error: function () {
        $("#egsMonederoVentaRapida").hide();
        recalcularTotales();
      }
    });
  }

  function initCatalogo() {
    var $tabla = $("#tablaProductosPos");
    if (!$tabla.length) return null;

    if ($.fn.DataTable.isDataTable($tabla)) {
      $tabla.DataTable().destroy();
      dtCatalogo = null;
    }

    try {
      dtCatalogo = $tabla.DataTable({
        ajax: "ajax/tablaVentasDinamicas.ajax.php?perfil=" + encodeURIComponent($("#tipoDePerfil").val()) + "&empresa=" + encodeURIComponent($("#id_empresa").val()),
        deferRender: true,
        processing: true,
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
        pagingType: "simple_numbers",
        dom: '<"pos-cat-dt-top"lf>rt<"pos-cat-dt-bottom"ip>',
        autoWidth: false,
        columnDefs: [
          { orderable: false, targets: [1, 5] },
          { className: "text-center", targets: [0, 1, 4, 5] }
        ],
        language: {
          sProcessing: "Procesando...",
          sLengthMenu: "Mostrar _MENU_",
          sZeroRecords: "Sin productos",
          sEmptyTable: "Sin datos",
          sInfo: "_START_–_END_ de _TOTAL_",
          sSearch: "Buscar:",
          oPaginate: { sNext: "Sig.", sPrevious: "Ant." }
        },
        initComplete: function () {
          $("#tablaProductosPos_wrapper").addClass("pos-catalogo-dt-wrapper");
        }
      });
    } catch (err) {
      console.error("POS catálogo DataTable:", err);
      toast("No se pudo cargar el catálogo", "error");
      return null;
    }
    return dtCatalogo;
  }

  function enfocarBusquedaCatalogo() {
    var $filter = $("#modalPosCatalogo").find(".dataTables_filter input");
    if ($filter.length) {
      setTimeout(function () { $filter.focus().select(); }, 200);
    }
  }

  function abrirCatalogo() {
    var $modal = $("#modalPosCatalogo");
    if (!$modal.length) {
      toast("Modal de catálogo no disponible", "error");
      return;
    }
    $modal.modal("show");
  }

  function ocultarBannerExito() {
    $("#posVentaExitosa").removeClass("show");
  }

  function mostrarBannerExito(mensaje, ticketUrl) {
    ultimaVentaTicketUrl = ticketUrl || "";
    $("#posVentaExitosaMsg").text(mensaje || "Venta registrada correctamente");
    $("#posVentaExitosa").addClass("show");
    $("html, body").animate({ scrollTop: $("#posVentaExitosa").offset().top - 80 }, 250);
  }

  function resetPosNuevaVenta() {
    ocultarBannerExito();
    vaciarCarrito();
    if (clienteChoices) {
      clienteChoices.setChoiceByValue("");
    } else if ($clienteSelect) {
      $clienteSelect.value = "";
    }
    $("#posNuevoClienteSection").hide();
    $("#posNuevoClienteNombre, #posNuevoClienteWhatsapp").val("");
    syncClienteHidden(0);
    cargarMonedero(0);
    $("#posDescuentoPct").val(0);
    $("#nuevoMetodoPago").val("").trigger("change");
    $("#posEfectivoRecibido, #posEfectivoCambio, #nuevoCodigoTransaccion").val("");
    enfocarScanner();
  }

  window.egsPosVentaCompletada = function (tipo, mensaje, idVenta, idEmpresa, idAsesor) {
    $btnCobrar.prop("disabled", false).html('<i class="fa-solid fa-check"></i> Cobrar');
    validarCobro();

    if (tipo !== "success") {
      toast(mensaje, "error");
      return;
    }

    var ticketUrl = "extensiones/tcpdf/pdf/ticketVentasD.php?idventa=" + idVenta + "&empresa=" + idEmpresa + "&asesor=" + idAsesor;
    toast("Venta registrada correctamente", "success");
    mostrarBannerExito(mensaje, ticketUrl);
  };

  $("#posBtnNuevaVenta").on("click", resetPosNuevaVenta);

  $("#posBtnImprimirTicket").on("click", function () {
    if (ultimaVentaTicketUrl) {
      window.open(ultimaVentaTicketUrl, "_blank");
    }
  });

  $("#modalPosCatalogo").appendTo("body");

  $("#modalPosCatalogo").on("shown.bs.modal", function () {
    if (!dtCatalogo) {
      initCatalogo();
    } else if (dtCatalogo.columns) {
      dtCatalogo.columns.adjust();
      if (dtCatalogo.ajax) {
        dtCatalogo.ajax.reload(null, false);
      }
    }
    enfocarBusquedaCatalogo();
  });

  // ── Eventos scanner ──
  $scanner.on("keydown", function (e) {
    if (e.key === "Enter" || e.keyCode === 13) {
      e.preventDefault();
      procesarEscaneo();
    }
  });

  $("#posBtnEnfocarScanner").on("click", enfocarScanner);

  // ── Carrito qty / quitar ──
  $cartBody.on("click", ".pos-btn-qty-minus", function () {
    var idx = parseInt($(this).data("idx"), 10);
    if (carrito[idx] && carrito[idx].cantidad > 1) {
      carrito[idx].cantidad--;
      renderCarrito();
    }
  });

  $cartBody.on("click", ".pos-btn-qty-plus", function () {
    var idx = parseInt($(this).data("idx"), 10);
    var item = carrito[idx];
    if (!item) return;
    if (item.cantidad >= item.stock) {
      toast("Stock máximo alcanzado", "warning");
      return;
    }
    item.cantidad++;
    renderCarrito();
  });

  $cartBody.on("change", ".pos-qty-input", function () {
    var idx = parseInt($(this).data("idx"), 10);
    var item = carrito[idx];
    if (!item) return;
    var qty = parseInt($(this).val(), 10) || 1;
    if (qty < 1) qty = 1;
    if (qty > item.stock) {
      qty = item.stock;
      toast("Cantidad ajustada al stock disponible", "warning");
    }
    item.cantidad = qty;
    renderCarrito();
  });

  $cartBody.on("click", ".pos-btn-quitar", function () {
    var idx = parseInt($(this).data("idx"), 10);
    carrito.splice(idx, 1);
    renderCarrito();
  });

  $("#posBtnVaciarCarrito").on("click", function () {
    if (!carrito.length) return;
    if (window.confirm("¿Vaciar el carrito?")) {
      vaciarCarrito();
      toast("Carrito vaciado", "info");
    }
  });

  // ── Catálogo (modal) ──
  $("#posBtnAbrirCatalogo").on("click", function (e) {
    e.preventDefault();
    abrirCatalogo();
  });

  $("#modalPosCatalogo").on("click", "button.agregarProducto", function () {
    var idProducto = $(this).attr("idProducto");
    var $btn = $(this);
    $btn.prop("disabled", true);
    buscarProductoPorId(idProducto).done(function (respuesta) {
      var producto = productoDesdeRespuesta(respuesta);
      if (producto) agregarAlCarrito(producto, 1);
      $btn.prop("disabled", false);
    }).fail(function () {
      toast("Error al cargar producto", "error");
      $btn.prop("disabled", false);
    });
  });

  // ── Cliente ──
  initClienteChoices();

  if ($clienteSelect) {
    $clienteSelect.addEventListener("change", function () {
      var val = this.value;
      if (val === "nuevo") {
        $("#posNuevoClienteSection").slideDown(200);
        $("#egsMonederoVentaRapida").hide();
        syncClienteHidden(0);
        return;
      }
      $("#posNuevoClienteSection").slideUp(200);
      var idCliente = parseInt(val, 10) || 0;
      syncClienteHidden(idCliente);
      cargarMonedero(idCliente);
    });
  }

  // ── Monedero ──
  $("#egsAplicarMaxMonedero").on("click", function () {
    var maxAplicable = Math.min(egsSaldoMonederoCliente, totalSinCanje);
    $("#egsMontoCanjeVenta").val(maxAplicable.toFixed(2));
    aplicarCanjeMonedero();
  });

  $("#egsMontoCanjeVenta").on("input change", aplicarCanjeMonedero);
  $("#posDescuentoPct").on("input change", recalcularTotales);

  // ── Pago ──
  $("#nuevoMetodoPago").on("change", function () {
    var metodo = $(this).val();
    if (metodo === "efectivo") {
      $("#posEfectivoRow").addClass("active");
      $("#posTransaccionWrap").hide();
    } else if (metodo) {
      $("#posEfectivoRow").removeClass("active");
      $("#posTransaccionWrap").show();
    } else {
      $("#posEfectivoRow").removeClass("active");
      $("#posTransaccionWrap").hide();
    }
    listarMetodoPago();
    actualizarCambioEfectivo();
    validarCobro();
  });

  $("#posEfectivoRecibido, #nuevoCodigoTransaccion").on("input change", function () {
    listarMetodoPago();
    actualizarCambioEfectivo();
  });

  $("#posAsesor").on("change", validarCobro);

  // ── Submit ──
  $("#posFormVenta").on("submit", function (e) {
    if (!carrito.length) {
      e.preventDefault();
      toast("Agrega al menos un producto al carrito", "warning");
      return false;
    }

    var val = $clienteSelect ? $clienteSelect.value : "";

    if (val === "nuevo") {
      e.preventDefault();
      var nombre = $.trim($("#posNuevoClienteNombre").val());
      var whatsapp = $.trim($("#posNuevoClienteWhatsapp").val());
      if (!nombre || !whatsapp) {
        $("#posNuevoClienteError").text("Nombre y WhatsApp son obligatorios.").show();
        return false;
      }
      $("#posNuevoClienteError").hide();
      var $form = $(this);
      var $btn = $btnCobrar;
      $btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

      $.ajax({
        url: "ajax/clientes.ajax.php",
        method: "POST",
        data: {
          crearClienteRapido: 1,
          nombreClienteRapido: nombre,
          whatsappClienteRapido: whatsapp,
          empresaClienteRapido: $("#id_empresa").val() || 0
        },
        dataType: "json",
        success: function (resp) {
          if (resp.status === "ok" && resp.id > 0) {
            if (clienteChoices) {
              clienteChoices.setChoices([{ value: String(resp.id), label: resp.nombre, selected: true }], "value", "label", false);
              clienteChoices.setChoiceByValue(String(resp.id));
            } else {
              var opt = document.createElement("option");
              opt.value = resp.id;
              opt.textContent = resp.nombre;
              opt.setAttribute("data-nombre", resp.nombre);
              $clienteSelect.appendChild(opt);
              $clienteSelect.value = resp.id;
            }
            syncClienteHidden(resp.id);
            cargarMonedero(resp.id);
            $("#posNuevoClienteSection").hide();
            listarMetodoPago();
            serializarCarrito();
            $form.off("submit").submit();
          } else {
            $("#posNuevoClienteError").text(resp.mensaje || resp.message || "No se pudo crear el cliente.").show();
            $btn.prop("disabled", false).html('<i class="fa-solid fa-check"></i> Cobrar');
          }
        },
        error: function () {
          $("#posNuevoClienteError").text("Error de conexión al crear cliente.").show();
          $btn.prop("disabled", false).html('<i class="fa-solid fa-check"></i> Cobrar');
        }
      });
      return false;
    }

    if (!val || parseInt(val, 10) <= 0) {
      e.preventDefault();
      toast("Selecciona o agrega un cliente", "warning");
      return false;
    }

    listarMetodoPago();
    serializarCarrito();

    if ($("#nuevoMetodoPago").val() !== "efectivo" && !($("#nuevoCodigoTransaccion").val() || "").trim()) {
      e.preventDefault();
      toast("Ingresa la referencia de la transacción", "warning");
      return false;
    }

    $btnCobrar.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Procesando...');
    return true;
  });

  // ── Atajos ──
  $(document).on("keydown", function (e) {
    if (!$("#posCajeroRoot").length) return;
    if (e.key === "F2") {
      e.preventDefault();
      enfocarScanner();
    }
    if (e.key === "Escape") {
      if ($("#modalPosCatalogo").is(":visible")) {
        $("#modalPosCatalogo").modal("hide");
      } else {
        $scanner.val("");
      }
    }
    if (e.ctrlKey && e.key === "b") {
      e.preventDefault();
      abrirCatalogo();
    }
    if (e.ctrlKey && e.key === "Enter") {
      e.preventDefault();
      if (!$btnCobrar.prop("disabled")) {
        $("#posFormVenta").submit();
      }
    }
  });

  // ── Init ──
  renderCarrito();
  enfocarScanner();

})(jQuery);
