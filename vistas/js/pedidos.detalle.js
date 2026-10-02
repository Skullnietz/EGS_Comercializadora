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
    var pending = null;
    var observationPending = false;
    var restoring = false;
    var guardado = window.PedidosGuardado;
    var draft = guardado.drafts($form.attr('data-usuario') + ':detalle:' + idPedido);
    var lastDraft = null;
    var draftTools = guardado.recovery($form.find('.ped-observation-status').parent(), function () { return lastDraft; },
      function () { return !saving && !busy && !pending && !observationPending; }, function () { dirty = false; nuevosPagos = []; $('#pedNuevoAbonoMonto, #pedNewObsText').val(''); draft.clear(); window.location.reload(); });

    function money(n) { return Math.round((Number(n) + Number.EPSILON) * 100) / 100; }
    function currency(n) { return '$' + money(n).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }
    function ref() { return guardado.ref(); }
    function badInput(input) { return !!(input && input.validity && input.validity.badInput); }
    function error(message, title) {
      return swal({type: 'error', title: title || 'No se guardaron los cambios', text: message, confirmButtonText: 'Cerrar'});
    }
    function request(data) {
      data.idPedido = idPedido;
      busy++;
      return guardado.request(data).then(function (r) { busy--; return r; }, function (e) { busy--; throw e; });
    }

    function saveDraft() {
      if (restoring) return;
      var products = [];
      $form.find('.ped-dynamic-product[data-editado]').each(function () {
        var $row = $(this);
        products.push({indice: Number($row.attr('data-indice')), Descripcion: $row.find('.ped-prod-descripcion').val(), cantidad: $row.find('.ped-prod-cantidad').val(), precioUnitario: $row.find('.ped-prod-precio').val()});
      });
      if (!dirty && !pending && !observationPending && !nuevosPagos.length && !$('#pedNuevoAbonoMonto').val() && !$('#pedNewObsText').val()) { draft.clear(); lastDraft = null; draftTools(false); return; }
      lastDraft = {version: $form.find('[name=versionPedido]').val(), total: $total.val(), estado: $form.find('[name=estado]').val(), productos: products,
        pagos: nuevosPagos, monto: $('#pedNuevoAbonoMonto').val(), fecha: $('#pedNuevoAbonoFecha').val(), observacion: $('#pedNewObsText').val(), obsRef: obsRef,
        observationPending: observationPending, pending: pending, dirty: dirty};
      draftTools(true);
      if (!draft.write(lastDraft)) {
        status('El navegador no permitió guardar una copia de recuperación. Mantén esta página abierta hasta confirmar el guardado.');
      }
    }
    function lockFields(lock) {
      $form.find('.ped-dynamic-product input, [name=total], [name=estado], #pedNewPaymentForm input, #pedNewObsText').prop('disabled', lock);
      $form.find('button').prop('disabled', lock);
      // Ante una respuesta incierta solo se permite reintentar el contenido original.
      if (lock && !saving) $form.find('button[type=submit]').prop('disabled', false);
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
      saveDraft();
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
      dirty = true;
      summary();
      saveDraft();
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
      $item.attr('data-ref', obs.ref || '');
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
      observationPending = true;
      saveDraft();
      $input.prop('readonly', true);
      $button.prop('disabled', true);
      status('Guardando observación…');
      return request({accionPedido: 'agregarObservacion', observacion: text, ref: obsRef}).then(function (r) {
        var exists = false;
        $form.find('.ped-obs-item').each(function () { if ($(this).attr('data-ref') === r.observacion.ref) exists = true; });
        if (!exists) renderObservation(r.observacion);
        $input.val('');
        obsRef = null;
        observationPending = false;
        saveDraft();
        status('Observación guardada');
        return true;
      }, function (e) {
        observationPending = !!e.uncertain;
        saveDraft();
        status(e.uncertain ? 'Guardado sin confirmar. Presiona Agregar para recuperar el resultado.' : 'La observación no se guardó');
        throw e;
      }).then(function (added) {
        $input.prop('readonly', observationPending);
        $button.prop('disabled', false);
        return added;
      }, function (e) {
        $input.prop('readonly', observationPending);
        $button.prop('disabled', false);
        throw e;
      });
    }
    $('#pedNewObsText').on('input', function () { if (!observationPending) obsRef = null; saveDraft(); });
    $form.on('click', '.btnAgregarObservacionInfoPedido', function () {
      if (busy || saving || pending || !$.trim($('#pedNewObsText').val() || '')) return;
      addObservation().catch(function (e) { error(e.message, 'No se guardó la observación'); });
    });
    $form.on('click', '.ped-remove-observation', function () {
      if (busy || saving || pending) return;
      var $button = $(this).prop('disabled', true);
      var $item = $button.closest('.ped-obs-item');
      var removeRef = $item.data('removeRef') || ref();
      $item.data('removeRef', removeRef);
      request({accionPedido: 'quitarObservacion', observacion: JSON.stringify($item.data('observacion')), ref: removeRef}).then(function () {
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
        pagos: JSON.stringify(nuevosPagos),
        observacion: $.trim($('#pedNewObsText').val() || ''),
        solicitud: ref()
      };
    }
    $form.on('submit', function (event) {
      event.preventDefault();
      if (saving || busy) return;
      var data = null;
      try {
        if (finanzas) {
          if (observationPending) { error('Primero presiona Agregar para confirmar la observación pendiente.'); return; }
          if (!pending) { addPayment(); pending = collect(); }
          data = pending;
        }
      } catch (e) { error(e.message); return; }
      saving = true;
      saveDraft();
      lockFields(true);
      var operation = data ? request(data).then(function () {
        dirty = false;
        nuevosPagos = [];
        pending = null;
        $('#pedNewObsText').val('');
        draft.clear();
        return swal({type: 'success', title: '¡El pedido se ha guardado correctamente!', confirmButtonText: 'Volver a pedidos'})
          .then(function () { window.location.href = 'index.php?ruta=pedidos'; });
      }) : addObservation().then(function (added) {
          return swal(added
            ? {type: 'success', title: '¡Observación guardada!', confirmButtonText: 'Cerrar'}
            : {type: 'info', title: 'No hay observaciones pendientes', text: 'Escribe una observación; se guarda al presionar «Agregar».', confirmButtonText: 'Cerrar'});
      });
      operation.catch(function (e) {
        if (!e.uncertain) pending = null;
        saveDraft();
        error(e.message);
      }).then(function () {
        saving = false;
        lockFields(!!pending);
        $('#pedNewObsText').prop('readonly', observationPending);
        draftTools(!!lastDraft);
      });
    });
    $form.on('input change', 'input, select, textarea', saveDraft);
    window.addEventListener('beforeunload', function (e) {
      saveDraft();
      if (dirty || busy > 0 || nuevosPagos.length || $('#pedNuevoAbonoMonto').val() || $.trim($('#pedNewObsText').val() || '')) {
        e.preventDefault();
        e.returnValue = '';
      }
    });

    /* ---------- Inicio ---------- */
    var recovered = draft.read();
    if (recovered) {
      restoring = true;
      var changedVersion = $form.find('[name=versionPedido]').val() !== recovered.version;
      pending = recovered.pending || null;
      observationPending = !!recovered.observationPending;
      dirty = !!recovered.dirty;
      $form.find('[name=versionPedido]').val(recovered.version);
      $total.val(recovered.total);
      $form.find('[name=estado]').val(recovered.estado);
      (recovered.productos || []).forEach(function (p) {
        var $row = $form.find('.ped-dynamic-product[data-indice="' + Number(p.indice) + '"]');
        $row.find('.ped-prod-descripcion').val(p.Descripcion);
        $row.find('.ped-prod-cantidad').val(p.cantidad);
        $row.find('.ped-prod-precio').val(p.precioUnitario);
        var subtotal = money(Number(p.cantidad) * Number(p.precioUnitario));
        $row.attr({'data-editado': '1', 'data-actual': subtotal});
        $row.find('.ped-line-subtotal').text(currency(subtotal));
      });
      (recovered.pagos || []).forEach(function (p) {
        $('#pedNuevoAbonoMonto').val(p.pago);
        $('#pedNuevoAbonoFecha').val(p.fecha);
        addPayment();
        nuevosPagos[nuevosPagos.length - 1].ref = p.ref;
      });
      $('#pedNuevoAbonoMonto').val(recovered.monto);
      $('#pedNuevoAbonoFecha').val(recovered.fecha);
      $('#pedNewObsText').val(recovered.observacion).prop('readonly', observationPending);
      obsRef = recovered.obsRef;
      lockFields(!!pending);
      status(pending ? 'Se recuperó un guardado sin confirmar. Presiona Guardar para obtener su resultado.' : changedVersion
        ? 'El pedido cambió desde tu captura. Descarga el borrador para conservarlo; después descártalo y vuelve a capturar sobre el pedido actual.'
        : 'Se recuperaron cambios pendientes de esta pestaña.');
      restoring = false;
      saveDraft();
    }
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
