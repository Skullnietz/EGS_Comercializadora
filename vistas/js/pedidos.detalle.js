/*=============================================
DETALLE DEL PEDIDO (infopedido)
Único script de la pantalla. Guarda por AJAX y solo confirma éxito con
respuesta del servidor. Abonos y observaciones solo se agregan: lo ya
guardado nunca se reenvía, así los datos antiguos no pasan por inputs
que los alteren.
=============================================*/
(function ($) {
  'use strict';
  $(function () {
    var $form = $('#pedidoDetalleForm');
    if (!$form.length) return;

    var idPedido = $form.find('[name=idPedido]').val();
    var finanzas = $form.attr('data-finanzas') === '1';
    var pagadoGuardado = Number($form.attr('data-pagado')) || 0;
    var $total = $form.find('[name=total]');
    var nuevosPagos = [];
    var obsRef = null;
    var busy = 0;
    var saving = false;
    var dirty = false;

    function money(n) { return Math.round((Number(n) + Number.EPSILON) * 100) / 100; }
    function currency(n) { return '$' + money(n).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }
    function ref() { return (Date.now().toString(36) + Math.random().toString(36).slice(2)).slice(0, 24); }
    function badInput(input) { return !!(input && input.validity && input.validity.badInput); }
    function error(message, title) {
      return swal({type: 'error', title: title || 'No se guardaron los cambios', text: message, confirmButtonText: 'Cerrar'});
    }
    function request(data) {
      data.idPedido = idPedido;
      busy++;
      return new Promise(function (resolve, reject) {
        $.ajax({url: 'ajax/pedidos.ajax.php', method: 'POST', data: data, dataType: 'json', cache: false})
          .done(function (r) {
            if (r && r.ok) resolve(r);
            else reject(new Error((r && r.mensaje) || 'El servidor no confirmó el guardado.'));
          })
          .fail(function (xhr) {
            var r = xhr.responseJSON;
            if (r && r.mensaje) reject(new Error(r.mensaje));
            else if (!xhr.status) reject(new Error('No hubo respuesta del servidor. Revisa tu conexión y vuelve a intentar; lo capturado sigue en la página.'));
            else reject(new Error('El servidor respondió con un error (HTTP ' + xhr.status + ') y no confirmó el guardado. Vuelve a intentar; si se repite, avisa a soporte con este código.'));
          })
          .always(function () { busy--; });
      });
    }

    /* ---------- Totales ---------- */
    function summary() {
      var paid = pagadoGuardado;
      nuevosPagos.forEach(function (pago) { paid += pago.pago; });
      $form.find('.ped-pagado').text(currency(paid));
      $form.find('.ped-adeudo').text(currency(Math.max(0, (Number($total.val()) || 0) - paid)));
    }

    /* ---------- Productos: cada cambio ajusta el total con la diferencia de su línea ---------- */
    $form.find('.ped-dynamic-product').each(function () { $(this).attr('data-actual', $(this).attr('data-subtotal')); });
    $form.on('input change', '.ped-dynamic-product input', function () {
      if (!finanzas) return;
      var $row = $(this).closest('.ped-dynamic-product');
      var quantity = Number($row.find('.ped-prod-cantidad').val()) || 0;
      var unit = Number($row.find('.ped-prod-precio').val()) || 0;
      var subtotal = money(Math.max(0, quantity) * Math.max(0, unit));
      var delta = subtotal - Number($row.attr('data-actual'));
      $row.attr({'data-editado': '1', 'data-actual': subtotal});
      $row.find('.ped-line-subtotal').text(currency(subtotal));
      if (delta) $total.val(money((Number($total.val()) || 0) + delta));
      dirty = true;
      summary();
    });
    $total.on('input change', function () { dirty = true; summary(); });
    $form.on('change', '[name=estado]', function () { dirty = true; });

    /* ---------- Abonos nuevos ---------- */
    function addPayment() {
      var $amount = $('#pedNuevoAbonoMonto');
      var $date = $('#pedNuevoAbonoFecha');
      if (!$amount.length) return false;
      if (badInput($amount[0])) throw new Error('El monto del abono no es un número válido. Escríbelo sin comas ni signos.');
      if ($amount.val() === '') return false;
      var amount = money($amount.val());
      if (!(amount > 0)) throw new Error('El monto del abono debe ser mayor a cero.');
      if (!$date.val()) throw new Error('Indica la fecha del abono.');
      var pago = {pago: amount, fecha: $date.val(), ref: ref()};
      var $row = $('<div class="ped-payment-item ped-payment-new"><div class="ped-payment-icon"><i class="fa-solid fa-money-bill-wave"></i></div>' +
        '<div style="flex:1;"><div class="ped-info-label"></div><div class="ped-payment-amount"></div></div>' +
        '<div class="ped-payment-date"></div>' +
        '<button type="button" class="ped-btn-danger ped-remove-payment" title="Quitar"><i class="fa-solid fa-times"></i></button></div>');
      $row.find('.ped-info-label').text('Nuevo abono ').append('<span style="color:#f59e0b;font-weight:600;">• sin guardar</span>');
      $row.find('.ped-payment-amount').text(currency(amount));
      $row.find('.ped-payment-date').text(pago.fecha);
      $row.data('pago', pago);
      $form.find('.ped-payments').append($row);
      nuevosPagos.push(pago);
      $amount.val('');
      dirty = true;
      summary();
      return true;
    }
    $form.on('click', '.btnToggleNewPayment', function () { $('#pedNewPaymentForm').toggleClass('active'); });
    $form.on('click', '.btnAgregarAbono', function () {
      try { addPayment(); } catch (e) { error(e.message, 'Revisa el abono'); }
    });
    $form.on('keydown', '#pedNewPaymentForm input', function (e) {
      if (e.key === 'Enter') { e.preventDefault(); $form.find('.btnAgregarAbono').trigger('click'); }
    });
    $form.on('click', '.ped-remove-payment', function () {
      var $row = $(this).closest('.ped-payment-new');
      var index = nuevosPagos.indexOf($row.data('pago'));
      if (index >= 0) nuevosPagos.splice(index, 1);
      $row.remove();
      summary();
    });

    /* ---------- Observaciones: se guardan al agregarlas ---------- */
    function status(text) { $form.find('.ped-observation-status').text(text); }
    function updateCount() {
      var count = $form.find('.ped-obs-item').length;
      $form.find('.ped-obs-count').text(count + (count === 1 ? ' comentario' : ' comentarios'));
    }
    function renderObservation(obs) {
      var $item = $('<div class="ped-obs-item ped-obs-new" style="animation:pedSlideIn .25s var(--crm-ease);">' +
        '<div class="ped-obs-body"><div class="ped-obs-header"><span class="ped-obs-name"></span><span class="ped-obs-date"></span></div>' +
        '<div class="ped-obs-content"></div></div>' +
        '<button type="button" class="ped-btn-danger ped-remove-observation" style="align-self:flex-start;margin-top:2px;" title="Quitar"><i class="fa-solid fa-times"></i></button></div>');
      $item.prepend($('#pedObsCompose .ped-obs-avatar').clone());
      $item.find('.ped-obs-name').text(obs.creador);
      $item.find('.ped-obs-date').text(obs.fecha);
      $item.find('.ped-obs-content').text(obs.observacion);
      $item.data('observacion', obs);
      $form.find('.ped-obs-list').prepend($item);
      $('#pedObsEmpty').remove();
      updateCount();
    }
    function addObservation() {
      var $input = $('#pedNewObsText');
      var text = $.trim($input.val() || '');
      if (!text) return Promise.resolve(false);
      var $button = $form.find('.btnAgregarObservacionInfoPedido');
      obsRef = obsRef || ref();
      $input.prop('readonly', true);
      $button.prop('disabled', true);
      status('Guardando observación…');
      return request({accionPedido: 'agregarObservacion', observacion: text, ref: obsRef}).then(function (r) {
        renderObservation(r.observacion);
        $input.val('');
        obsRef = null;
        status('Observación guardada');
        return true;
      }, function (e) {
        status('La observación no se guardó');
        throw e;
      }).then(function (added) {
        $input.prop('readonly', false);
        $button.prop('disabled', false);
        return added;
      }, function (e) {
        $input.prop('readonly', false);
        $button.prop('disabled', false);
        throw e;
      });
    }
    $('#pedNewObsText').on('input', function () { obsRef = null; });
    $form.on('click', '.btnAgregarObservacionInfoPedido', function () {
      if (!$.trim($('#pedNewObsText').val() || '')) return;
      addObservation().catch(function (e) { error(e.message, 'No se guardó la observación'); });
    });
    $form.on('click', '.ped-remove-observation', function () {
      var $button = $(this).prop('disabled', true);
      var $item = $button.closest('.ped-obs-item');
      request({accionPedido: 'quitarObservacion', observacion: JSON.stringify($item.data('observacion'))}).then(function () {
        $item.remove();
        updateCount();
        status('Observación quitada');
      }, function (e) {
        $button.prop('disabled', false);
        error(e.message, 'No se quitó la observación');
      });
    });

    /* ---------- Guardar ---------- */
    function collect() {
      var productos = [];
      $form.find('.ped-dynamic-product[data-editado]').each(function () {
        var $row = $(this);
        var $quantity = $row.find('.ped-prod-cantidad');
        var $unit = $row.find('.ped-prod-precio');
        var name = $.trim($row.find('.ped-prod-descripcion').val()) || 'sin descripción';
        if (badInput($quantity[0]) || !(Number($quantity.val()) > 0)) throw new Error('La cantidad de «' + name + '» debe ser un número mayor a cero.');
        if (badInput($unit[0]) || $unit.val() === '' || !(Number($unit.val()) >= 0)) throw new Error('El precio de «' + name + '» debe ser un número, sin comas ni signos.');
        productos.push({indice: Number($row.attr('data-indice')), Descripcion: $row.find('.ped-prod-descripcion').val(), cantidad: $quantity.val(), precioUnitario: $unit.val()});
      });
      if (badInput($total[0]) || $total.val() === '' || !(Number($total.val()) >= 0)) throw new Error('El total del pedido debe ser un número, sin comas ni signos.');
      return {
        accionPedido: 'guardar',
        versionPedido: $form.find('[name=versionPedido]').val(),
        estado: $form.find('[name=estado]').val() || '',
        total: $total.val(),
        productos: JSON.stringify(productos),
        pagos: JSON.stringify(nuevosPagos)
      };
    }
    $form.on('submit', function (event) {
      event.preventDefault();
      if (saving) return;
      var data = null;
      try {
        if (finanzas) {
          addPayment();
          data = collect();
        }
      } catch (e) { error(e.message); return; }
      saving = true;
      var $buttons = $form.find('button').prop('disabled', true);
      addObservation().then(function (added) {
        if (!data) {
          return swal(added
            ? {type: 'success', title: '¡Observación guardada!', confirmButtonText: 'Cerrar'}
            : {type: 'info', title: 'No hay observaciones pendientes', text: 'Escribe una observación; se guarda al presionar «Agregar».', confirmButtonText: 'Cerrar'});
        }
        return request(data).then(function () {
          dirty = false;
          nuevosPagos = [];
          return swal({type: 'success', title: '¡El pedido se ha guardado correctamente!', confirmButtonText: 'Cerrar'})
            .then(function () { window.location.reload(); });
        });
      }).catch(function (e) { error(e.message); }).then(function () {
        saving = false;
        $buttons.prop('disabled', false);
      });
    });
    window.addEventListener('beforeunload', function (e) {
      if (dirty || busy > 0 || nuevosPagos.length || $('#pedNuevoAbonoMonto').val() || $.trim($('#pedNewObsText').val() || '')) {
        e.preventDefault();
        e.returnValue = '';
      }
    });

    /* ---------- Inicio ---------- */
    summary();

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
