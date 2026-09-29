/*=============================================
DETALLE DEL PEDIDO (infopedido)
Único script de la pantalla: calcula totales, guarda observaciones y el
pedido completo por AJAX, y solo confirma éxito con respuesta del servidor.
=============================================*/
(function ($) {
  'use strict';
  $(function () {
    var $form = $('#pedidoDetalleForm');
    if (!$form.length) return;

    var pending = Promise.resolve();
    var obsSaving = 0;
    var obsFailed = false;
    var saving = false;
    var dirty = false;
    var GRADS = [
      'linear-gradient(135deg,#6366f1,#818cf8)', 'linear-gradient(135deg,#3b82f6,#60a5fa)',
      'linear-gradient(135deg,#06b6d4,#22d3ee)', 'linear-gradient(135deg,#22c55e,#4ade80)',
      'linear-gradient(135deg,#f59e0b,#fbbf24)', 'linear-gradient(135deg,#ef4444,#f87171)',
      'linear-gradient(135deg,#8b5cf6,#a78bfa)', 'linear-gradient(135deg,#ec4899,#f472b6)'
    ];

    function money(n) { return Math.round((Number(n) + Number.EPSILON) * 100) / 100; }
    function error(message) { return swal({type: 'error', title: 'No se guardaron los cambios', text: message, confirmButtonText: 'Cerrar'}); }
    function request(data) {
      return new Promise(function (resolve, reject) {
        $.ajax({url: 'ajax/pedidos.ajax.php', method: 'POST', data: data, dataType: 'json'})
          .done(function (r) { if (r && r.ok) resolve(r); else reject(new Error((r && r.mensaje) || 'No se pudo guardar.')); })
          .fail(function (xhr) { reject(new Error((xhr.responseJSON || {}).mensaje || 'No se pudo confirmar el guardado. Comprueba la conexión y vuelve a intentar.')); });
      });
    }

    /* ---------- Observaciones ---------- */
    function observations() {
      var values = [];
      $form.find('textarea.nuevaObservacion').each(function () {
        values.push({observacion: $(this).val(), creador: $(this).attr('data-creador') || 'Usuario', fecha: $(this).attr('fecha') || ''});
      });
      var json = JSON.stringify(values);
      $form.find('[name=listarObservacionesPedidos]').val(json);
      return json;
    }
    function status(text) { $form.find('.ped-observation-status').text(text); }
    function saveObservations() {
      var json = observations();
      obsSaving++;
      status('Guardando observaciones…');
      pending = pending.catch(function () {}).then(function () {
        return request({idPedidoDinamicoAjax: $form.find('[name=idPedido]').val(), observacionesDinamicoAjax: json, versionObservaciones: $form.find('[name=versionObservaciones]').val()});
      }).then(function (r) {
        $form.find('[name=versionObservaciones]').val(r.version);
        obsFailed = false;
        if (observations() === json) status('Observaciones guardadas');
      }, function (e) {
        obsFailed = true;
        status('Observaciones pendientes de guardar');
        error(e.message);
        throw e;
      }).then(function () { obsSaving--; }, function (e) { obsSaving--; throw e; });
      return pending;
    }
    function today() { var d = new Date(); return (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear(); }
    function initials(name) {
      var parts = name.trim().split(/\s+/);
      return (parts.length >= 2 ? parts[0][0] + parts[1][0] : name.substring(0, 2)).toUpperCase();
    }
    function gradient(name) {
      var hash = 0;
      for (var i = 0; i < name.length; i++) hash = ((hash << 5) - hash + name.charCodeAt(i)) | 0;
      return GRADS[Math.abs(hash) % GRADS.length];
    }
    function updateCount() {
      var count = $form.find('.ped-obs-item').length;
      $form.find('.ped-card-head span').filter(function () { return /comentario/.test($(this).text()); })
        .text(count + (count === 1 ? ' comentario' : ' comentarios'));
    }
    function addObservation() {
      var $input = $('#pedNewObsText');
      var text = ($input.val() || '').trim();
      if (!text) return false;
      var name = $('.usuarioActualPedido').val() || 'Usuario';
      var date = today();
      if (!$form.find('.ped-obs-list').length) $('#pedObsCompose').after('<div class="ped-obs-list"></div>');
      var $item = $('<div class="ped-obs-item" style="animation:pedSlideIn .25s var(--crm-ease);">' +
        '<div class="ped-obs-avatar"></div><div class="ped-obs-body"><div class="ped-obs-header">' +
        '<span class="ped-obs-name"></span><span class="ped-obs-date"></span></div><div class="ped-obs-content"></div></div>' +
        '<button type="button" class="ped-btn-danger ped-remove-observation" style="align-self:flex-start;margin-top:2px;" title="Quitar"><i class="fa-solid fa-times"></i></button></div>');
      $item.find('.ped-obs-avatar').css('background', gradient(name)).text(initials(name));
      $item.find('.ped-obs-name').text(name);
      $item.find('.ped-obs-date').text(date + ' ').append('<span style="color:#22c55e;font-weight:600;">• Nueva</span>');
      $item.find('.ped-obs-content').text(text)
        .append($('<textarea class="nuevaObservacion" style="display:none;">').val(text).attr({'data-creador': name, fecha: date}));
      $form.find('.ped-obs-list').prepend($item);
      $('#pedObsEmpty').remove();
      $input.val('');
      updateCount();
      return true;
    }
    $form.on('click', '.btnAgregarObservacionInfoPedido', function () { if (addObservation()) saveObservations().catch(function () {}); });
    $form.on('click', '.ped-remove-observation', function () {
      $(this).closest('.ped-obs-item').remove();
      updateCount();
      saveObservations().catch(function () {});
    });

    /* ---------- Productos y pagos ---------- */
    function confirmPayment() {
      var $new = $('#pedNewPaymentForm');
      if (!$new.length) return;
      var amount = $new.find('.pagoAbonado').val();
      var date = $new.find('.fechaAbono').val();
      if (!amount && !date) return;
      if (!(Number(amount) > 0) || !date) throw new Error('Completa el monto y la fecha del nuevo abono.');
      var number = $('.agregarCamposPago .ped-payment-item').length + 1;
      var $row = $('<div class="ped-payment-item"><div class="ped-payment-icon"><i class="fa-solid fa-money-bill-wave"></i></div>' +
        '<div style="flex:1;"><div class="ped-info-label"></div></div><div class="ped-payment-date"></div></div>');
      $row.find('.ped-info-label').text('Abono #' + number).after(
        $('<input type="number" step="any" class="form-control pagoAbonado" readonly style="border:none;background:transparent;font-size:14px;font-weight:700;padding:0;height:auto;color:var(--crm-text);box-shadow:none;">').val(amount));
      $row.find('.ped-payment-date').append(
        $('<input type="date" class="form-control fechaAbono" readonly style="border:none;background:transparent;font-size:12px;color:var(--crm-muted);box-shadow:none;text-align:right;">').val(date));
      $('.agregarCamposPago').append($row);
      $new.find('input').val('');
      $new.removeClass('active');
    }
    function calculate() {
      var products = [], payments = [];
      var total = Number($form.attr('data-total-anterior')) || 0;
      $form.find('.ped-dynamic-product').each(function () {
        var $row = $(this);
        var quantity = Number($row.find('.cantidadProductoParaListar').val());
        var unit = Number($row.find('.precioProductoParaListar').val());
        var subtotal = money(quantity * unit);
        products.push({Descripcion: $row.find('.descripcioParaListar').val(), cantidad: quantity, precioUnitario: unit, precio: subtotal});
        total += subtotal;
        $row.find('.ped-line-subtotal').text('$' + subtotal.toFixed(2));
      });
      var paid = Number($form.attr('data-pagado-anterior')) || 0;
      $form.find('.agregarCamposPago .ped-payment-item').each(function () {
        var amount = $(this).find('.pagoAbonado').val();
        payments.push({pago: amount, fecha: $(this).find('.fechaAbono').val()});
        paid += Number(amount) || 0;
      });
      $form.find('[name=ListarPreciosActualizados]').val(JSON.stringify(products));
      $form.find('[name=PagosListados]').val(JSON.stringify(payments));
      $form.find('.totalPagarPedidoDinamico').val(money(total));
      $form.find('.totalPagosPeiddoDinamico').val(money(paid));
      $form.find('.adeudoPedidoDinamico').val(money(Math.max(0, total - paid)));
    }
    $form.on('input change', '.ped-dynamic-product input', function () { dirty = true; calculate(); });
    $form.on('click', '.btnToggleNewPayment', function () { $('#pedNewPaymentForm').toggleClass('active'); });
    $form.on('change', '#pedNewPaymentForm input', function () {
      dirty = true;
      if ($('#pedNewPaymentForm .pagoAbonado').val() && $('#pedNewPaymentForm .fechaAbono').val()) {
        try { confirmPayment(); calculate(); } catch (e) { error(e.message); }
      }
    });
    $form.on('change', '[name=EstadoPedidoDinamico]', function () { dirty = true; });

    /* ---------- Guardar ---------- */
    $form.on('submit', function (event) {
      event.preventDefault();
      if (saving) return;
      try {
        confirmPayment();
        calculate();
        if (addObservation()) saveObservations().catch(function () {});
      } catch (e) { error(e.message); return; }
      saving = true;
      var $buttons = $form.find('button');
      $buttons.prop('disabled', true);
      pending.catch(function () {}).then(function () {
        observations();
        var data = $form.serializeArray();
        data.push({name: 'guardarPedidoDetalle', value: '1'});
        return request(data);
      }).then(function () {
        dirty = false;
        obsFailed = false;
        return swal({type: 'success', title: '¡El pedido se ha guardado correctamente!', confirmButtonText: 'Cerrar'})
          .then(function () { window.location.reload(); });
      }).catch(function (e) { error(e.message); }).then(function () {
        saving = false;
        $buttons.prop('disabled', false);
      });
    });
    window.addEventListener('beforeunload', function (e) {
      if (dirty || obsSaving > 0 || obsFailed || ($('#pedNewObsText').val() || '').trim()) { e.preventDefault(); e.returnValue = ''; }
    });

    /* ---------- Inicio ---------- */
    $form.find('.ped-obs-content textarea.nuevaObservacion[readonly]').each(function () {
      this.style.height = 'auto';
      this.style.height = this.scrollHeight + 'px';
    });
    calculate();
    observations();

    var ordenChoices = null;
    $('#modalAsignarPedido').on('shown.bs.modal', function () {
      if (ordenChoices || typeof Choices === 'undefined') return;
      ordenChoices = new Choices('#selectorOrdenChoices', {
        searchEnabled: true, searchPlaceholderValue: 'Escribe para buscar...', placeholderValue: 'Buscar orden...',
        itemSelectText: 'Seleccionar', noResultsText: 'Sin resultados', noChoicesText: 'No hay opciones',
        shouldSort: false, searchResultLimit: 20
      });
    });
  });
})(jQuery);
