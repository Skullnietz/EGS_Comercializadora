(function ($) {
  'use strict';
  $(function () {
    var $form = $('#pedidoNuevoForm');
    if (!$form.length) return;
    var guardado = window.PedidosGuardado;
    var draft = guardado.drafts($form.attr('data-usuario') + ':nuevo');
    var pending = null;
    var saving = false;
    var restoring = false;
    var dirty = false;
    var $status = $('<p role="status" style="margin:12px 0;color:#475569;"></p>').prependTo($form.find('.modal-body'));
    var lastDraft = null;
    var draftTools = guardado.recovery($form.find('.modal-body'), function () { return lastDraft; }, function () { return !saving && !pending; },
      function () { dirty = false; draft.clear(); window.location.reload(); });
    function field(name) { return $form.find('[name="' + name + '"]'); }
    function lock(value) {
      $form.find('input, select, button').prop('disabled', value);
      if (value && !saving) $form.find('button[type=submit]').prop('disabled', false);
    }
    function capture() {
      var products = [];
      $form.find('.NuevoProductoPedido > .row').each(function () {
        var $row = $(this);
        products.push({Descripcion: $row.find('.descripcioProductoPedido').val(), cantidad: $row.find('.nuevaCantidadProductoPedido').val(), precioUnitario: $row.find('.nuevoPrecioProductoPedido').val()});
      });
      return {empresa: field('empresaPedioDinamico').val(), asesor: field('asesorPedidoDinamico').val(), cliente: field('clientePedidoDinamico').val(), estado: field('EstadoPedidoDinamico').val(), id_orden: field('seleccionarOrdenPedidoDinamico').val(),
        productos: products, monto: $form.find('.PagoClientePedidoDinamico').val(), fecha: $form.find('.fechaPagoVentaModal').val()};
    }
    function persist() {
      if (restoring) return;
      if (!dirty && !pending) { draft.clear(); lastDraft = null; draftTools(false); return; }
      lastDraft = {captura: capture(), pending: pending};
      draftTools(true);
      if (!draft.write(lastDraft)) $status.text('No fue posible guardar una copia de recuperación en este navegador. Mantén la página abierta.');
    }
    function collect() {
      var invalidNumber = false;
      $form.find('input').each(function () { if (this.validity && this.validity.badInput) invalidNumber = true; });
      if (invalidNumber) throw new Error('Uno de los importes o cantidades no es un número válido. Escríbelo sin comas ni signos.');
      var data = capture();
      if (!data.asesor || !data.cliente) throw new Error('Selecciona cliente y asesor.');
      if (!data.productos.length) throw new Error('Agrega al menos un producto.');
      data.productos.forEach(function (p) {
        if (!$.trim(p.Descripcion) || p.cantidad === '' || !isFinite(Number(p.cantidad)) || Number(p.cantidad) <= 0 || p.precioUnitario === '' || !isFinite(Number(p.precioUnitario)) || Number(p.precioUnitario) < 0) throw new Error('Revisa la descripción, cantidad y precio de cada producto.');
      });
      if (data.monto !== '' && (!isFinite(Number(data.monto)) || Number(data.monto) < 0)) throw new Error('El pago debe ser un número mayor o igual a cero.');
      if (Number(data.monto) > 0 && !data.fecha) throw new Error('Indica la fecha del pago.');
      if (data.fecha && !(Number(data.monto) > 0)) throw new Error('La fecha de pago necesita un monto mayor a cero.');
      return {accionPedido: 'crear', solicitud: guardado.ref(), empresa: data.empresa, asesor: data.asesor, cliente: data.cliente, estado: data.estado, id_orden: data.id_orden,
        productos: JSON.stringify(data.productos), pago: JSON.stringify(Number(data.monto) > 0 ? [{pago: data.monto, fecha: data.fecha}] : [])};
    }
    $form.on('input change', 'input, select', function () { dirty = true; persist(); });
    $form.on('click', '.AgregarProductos, .quitarProducto', function () { dirty = true; window.setTimeout(persist, 0); });
    $form.on('submit', function (e) {
      e.preventDefault();
      if (saving) return;
      try { pending = pending || collect(); } catch (error) { swal({type: 'error', title: 'Revisa el pedido', text: error.message}); return; }
      saving = true;
      persist();
      lock(true);
      $status.text('Guardando pedido…');
      guardado.request(pending).then(function (r) {
        pending = null;
        dirty = false;
        draft.clear();
        $status.text('Pedido #' + r.id + ' guardado');
        return swal({type: 'success', title: '¡Pedido #' + r.id + ' guardado!', confirmButtonText: 'Ver pedido'})
          .then(function () { window.location.href = 'index.php?ruta=infopedido&idPedido=' + Number(r.id); });
      }).catch(function (error) {
        if (!error.uncertain) pending = null;
        persist();
        $status.text(error.uncertain ? 'Guardado sin confirmar. Presiona Guardar para recuperar el mismo intento.' : 'El pedido no se guardó. Lo capturado sigue aquí.');
        swal({type: 'error', title: 'No se confirmó el guardado', text: error.message});
      }).then(function () { saving = false; lock(!!pending); draftTools(!!lastDraft); });
    });
    window.addEventListener('beforeunload', function (e) {
      persist();
      if (dirty || saving || pending) { e.preventDefault(); e.returnValue = ''; }
    });
    var recovered = draft.read();
    if (recovered && recovered.captura) {
      restoring = true;
      var data = recovered.captura;
      field('asesorPedidoDinamico').val(data.asesor);
      field('clientePedidoDinamico').val(data.cliente);
      field('EstadoPedidoDinamico').val(data.estado);
      field('seleccionarOrdenPedidoDinamico').val(data.id_orden);
      $form.find('.NuevoProductoPedido').empty();
      (data.productos || []).forEach(function (p) {
        $form.find('.AgregarProductos').trigger('click');
        var $row = $form.find('.NuevoProductoPedido > .row').last();
        $row.find('.descripcioProductoPedido').val(p.Descripcion);
        $row.find('.nuevaCantidadProductoPedido').val(p.cantidad);
        $row.find('.nuevoPrecioProductoPedido').val(p.precioUnitario);
      });
      $form.find('.PagoClientePedidoDinamico').val(data.monto);
      $form.find('.fechaPagoVentaModal').val(data.fecha);
      if (typeof sumarTotalPreciosPedido === 'function') sumarTotalPreciosPedido();
      pending = recovered.pending || null;
      dirty = true;
      restoring = false;
      lock(!!pending);
      $status.text(pending ? 'Se recuperó un guardado sin confirmar. Presiona Guardar para consultar su resultado.' : 'Se recuperó el pedido pendiente de esta pestaña.');
      persist();
      $('#modalAgregarPedido').modal('show');
    }
    if (/(?:[?&])nuevo=1(?:&|$)/.test(window.location.search)) $('#modalAgregarPedido').modal('show');
  });
})(jQuery);
