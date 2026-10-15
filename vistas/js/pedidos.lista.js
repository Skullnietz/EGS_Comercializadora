(function ($) {
  'use strict';
  $(function () {
    var $table = $('#tablepedidos');
    if (!$table.length || $.fn.dataTable.isDataTable($table[0])) return;
    var key = 'egs:pedidos:lista:' + $('#tipoidperfil').val() + ':' + $('#id_empresa').val() + ':' + $('#tipoDePerfil').val();
    var typedQuery = '';
    var timer = null;
    var loaded = false;
    var loadNumber = 0;
    var lastRows = [];
    var $input;
    var $notice = $('<div role="status" style="margin:0 0 12px;"></div>').insertBefore($table.closest('.table-responsive-wrap'));
    var $message = $('<span></span>').appendTo($notice);
    var $retry = $('<button type="button" class="btn btn-default btn-sm" style="margin-left:8px;">Reintentar carga</button>').appendTo($notice).hide();

    function normalize(value) {
      var text = String(value == null ? '' : value);
      return typeof text.normalize === 'function' ? text.normalize('NFD').replace(/[\u0300-\u036f]/g, '') : text;
    }
    function text(value) {
      var div = document.createElement('div');
      div.innerHTML = String(value == null ? '' : value);
      return div.textContent || '';
    }
    function applySearch(api, draw) {
      var query = normalize(typedQuery).trim();
      var pedido = /^(?:(?:pedido|folio)\s*[:#]?\s*|#\s*)?(\d+)$/i.exec(query);
      var orden = /^orden\s*[:#]?\s*(\d+)$/i.exec(query);
      var folio = pedido ? pedido[1].replace(/^0+(?=\d)/, '') : '';
      var link = orden ? orden[1].replace(/^0+(?=\d)/, '') : '';
      var general = pedido || orden ? '' : query;
      if (api.search() !== general) api.search(general, false, true);
      if (api.column(2).search() !== folio) api.column(2).search(folio, false, false);
      if (api.column(6).search() !== link) api.column(6).search(link, false, false);
      if (draw) api.draw();
    }
    function dateOrder(value) {
      var parts = /^(\d{2})\/(\d{2})\/(\d{4})(?:\s+(\d{2}):(\d{2}))?/.exec(text(value));
      return parts ? Date.UTC(+parts[3], +parts[2] - 1, +parts[1], +(parts[4] || 0), +(parts[5] || 0)) : 0;
    }
    function remember(settings, state) {
      state.pedidosQuery = typedQuery;
      try { window.sessionStorage.setItem(key, JSON.stringify(state)); } catch (error) { /* La búsqueda sigue funcionando sin almacenamiento. */ }
    }

    // Se enlaza antes del AJAX inicial: escribir mientras carga también se conserva.
    $table.on('preInit.dt.pedidosLista', function (event, settings) {
      var api = new $.fn.dataTable.Api(settings);
      $input = $(settings.nTableWrapper).find('.dataTables_filter input');
      // La versión incluida de DataTables usa un throttle; este módulo espera la última tecla.
      $input.off('.DT');
      $table.off('search.dt.DT');
      $input.val(typedQuery).attr('placeholder', 'Folio, cliente, estado u orden 123');
      function searchNow() {
        if (timer !== null) window.clearTimeout(timer);
        timer = null;
        applySearch(api, true);
      }
      $input.on('input.pedidosLista search.pedidosLista', function () {
        typedQuery = this.value;
        if (timer !== null) window.clearTimeout(timer);
        api.state.save(); // Incluye el texto aunque aún no haya terminado de escribir.
        if (!typedQuery) searchNow();
        else timer = window.setTimeout(searchNow, 300);
      }).on('keydown.pedidosLista', function (event) {
        if (event.key === 'Enter' || event.which === 13) { event.preventDefault(); searchNow(); }
      });
      applySearch(api, false);
    });

    var table = $table.DataTable({
      ajax: function (request, callback, settings) {
        var currentLoad = ++loadNumber;
        $notice.removeClass('text-danger').addClass('text-muted').show();
        $message.text(loaded ? 'Actualizando pedidos…' : 'Cargando pedidos… Puedes escribir la búsqueda.');
        $retry.hide();
        var api = new $.fn.dataTable.Api(settings);
        function failed(xhr) {
          if (currentLoad !== loadNumber) return;
          $notice.removeClass('text-muted').addClass('text-danger').show();
          $message.text(xhr && xhr.status === 401
            ? 'Tu sesión venció. Inicia sesión de nuevo; la búsqueda se conserva en esta pestaña.'
            : 'No se pudo actualizar la lista. Se conservan la búsqueda y los pedidos que ya estaban cargados.');
          $retry.show();
          settings.oLanguage.sZeroRecords = loaded ? 'No se encontraron pedidos con esta búsqueda.' : 'No se pudo cargar la lista. Usa Reintentar carga.';
          settings.oLanguage.sEmptyTable = loaded ? 'No hay pedidos disponibles.' : 'No se pudo cargar la lista. Usa Reintentar carga.';
          applySearch(api, false);
          callback({data: lastRows});
        }
        return $.ajax({
          url: 'ajax/tablapedidos.ajax.php',
          data: {perfil: $('#tipoDePerfil').val(), empresa: $('#id_empresa').val()},
          dataType: 'json', cache: false,
          success: function (response) {
            if (currentLoad !== loadNumber) return;
            if (!response || response.error || !Array.isArray(response.data) || !response.data.every(function (row) { return Array.isArray(row) && row.length === 11; })) { failed(); return; }
            lastRows = response.data;
            loaded = true;
            settings.oLanguage.sZeroRecords = 'No se encontraron pedidos con esta búsqueda.';
            settings.oLanguage.sEmptyTable = 'No hay pedidos disponibles.';
            applySearch(api, false);
            callback({data: lastRows});
            var page = api.page.info();
            if (page.pages && page.page >= page.pages) api.page('last').draw('page');
            $notice.hide();
          }, error: failed
        });
      },
      deferRender: true, processing: true, autoWidth: false,
      pageLength: 25, lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
      order: [[2, 'desc']], stateSave: true, stateDuration: -1,
      stateSaveCallback: remember,
      stateLoadCallback: function () {
        try {
          var state = JSON.parse(window.sessionStorage.getItem(key) || 'null');
          if (state && Array.isArray(state.columns) && state.columns.length === 11) { typedQuery = String(state.pedidosQuery || ''); return state; }
        } catch (error) { /* Un estado inválido no impide cargar pedidos. */ }
        return null;
      },
      columnDefs: [
        {targets: [9, 10], orderable: false, searchable: false},
        {targets: [0, 1, 2, 3, 4, 5, 6], render: function (value, type) { return type === 'filter' ? normalize(text(value)) : value; }},
        {targets: [7, 8], type: 'num', render: function (value, type) { return type === 'sort' || type === 'type' ? dateOrder(value) : (type === 'filter' ? text(value) : value); }}
      ],
      language: {
        sProcessing: 'Procesando…', sLengthMenu: 'Mostrar _MENU_ registros', sSearch: 'Buscar:',
        sLoadingRecords: 'Cargando pedidos…', sZeroRecords: 'Cargando pedidos…', sEmptyTable: 'Cargando pedidos…',
        sInfo: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_',
        sInfoEmpty: 'Mostrando registros del 0 al 0 de un total de 0', sInfoFiltered: ' (filtrado de un total de _MAX_ registros)', sThousands: ',',
        oPaginate: {sFirst: 'Primero', sLast: 'Último', sNext: 'Siguiente', sPrevious: 'Anterior'},
        oAria: {sSortAscending: ': Activar para ordenar la columna de manera ascendente', sSortDescending: ': Activar para ordenar la columna de manera descendente'}
      }
    });
    $retry.on('click', function () { table.ajax.reload(null, false); });
    window.addEventListener('pagehide', function () { table.state.save(); });

    function parameters(button) {
      var values = [];
      ['idPedido', 'empresa', 'asesor', 'cliente'].forEach(function (name) { values.push(encodeURIComponent(name) + '=' + encodeURIComponent($(button).attr(name) || '')); });
      return values.join('&');
    }
    $table.on('click.pedidosLista', '.btnVerInfoPedido', function (event) {
      event.preventDefault(); table.state.save();
      window.open('index.php?ruta=infopedido&' + parameters(this), '_self');
    }).on('click.pedidosLista', '.btnImprimirTicketPedido', function (event) {
      event.preventDefault(); table.state.save();
      window.open('extensiones/tcpdf/pdf/ticketpedido.php?' + parameters(this), '_blank', 'noopener');
    }).on('click.pedidosLista', '.btnEliminarPedido', function (event) {
      event.preventDefault();
      var $button = $(this);
      if ($button.prop('disabled')) return;
      $button.prop('disabled', true);
      swal({title: '¿Está seguro de borrar el pedido?', text: 'Puedes cancelar para conservarlo.', type: 'warning', showCancelButton: true,
        confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', cancelButtonText: 'Cancelar', confirmButtonText: 'Sí, borrar pedido'
      }).then(function (result) {
        $button.prop('disabled', false);
        if (result.value) { table.state.save(); window.open('index.php?ruta=pedidos&idpedido=' + encodeURIComponent($button.attr('idPedido')), '_self'); }
      });
    });
  });
})(jQuery);
