/* Transporte compartido: reintenta el mismo contenido; conserva borradores por usuario y pestaña. */
(function (window, $) {
  'use strict';
  function ref() {
    if (window.crypto && window.crypto.getRandomValues) {
      var bytes = new Uint8Array(16);
      window.crypto.getRandomValues(bytes);
      return Array.prototype.map.call(bytes, function (b) { return ('0' + b.toString(16)).slice(-2); }).join('');
    }
    return (Date.now().toString(36) + Math.random().toString(36).slice(2) + Math.random().toString(36).slice(2)).slice(0, 32);
  }
  function request(data) {
    // La copia evita que otro evento modifique lo que se reintentará.
    var payload = JSON.parse(JSON.stringify(data));
    return new Promise(function (resolve, reject) {
      function attempt(number) {
        $.ajax({url: 'ajax/pedidos.ajax.php', method: 'POST', data: payload, dataType: 'json', cache: false, timeout: 20000})
          .done(function (r) {
            if (r && r.ok === true) { resolve(r); return; }
            var error = new Error((r && r.mensaje) || 'El servidor no confirmó el guardado. Reintenta para comprobarlo.');
            error.uncertain = !r || r.ok !== false;
            reject(error);
          })
          .fail(function (xhr) {
            var uncertain = !xhr.status || xhr.status === 408 || xhr.status === 429 || xhr.status >= 500 || (xhr.status >= 200 && xhr.status < 300);
            if (uncertain && number < 2) { window.setTimeout(function () { attempt(number + 1); }, 700 * (number + 1)); return; }
            var message = xhr.responseJSON && xhr.responseJSON.mensaje;
            var error = new Error(message || (uncertain
              ? 'No se pudo confirmar el guardado. Lo capturado sigue aquí. Presiona Guardar para recuperar el resultado del mismo intento.'
              : 'No se guardó el cambio (HTTP ' + xhr.status + '). Lo capturado sigue aquí.'));
            error.uncertain = uncertain;
            reject(error);
          });
      }
      attempt(0);
    });
  }
  function drafts(key) {
    key = 'egs:pedidos:' + key;
    return {
      read: function () { try { return JSON.parse(window.sessionStorage.getItem(key)); } catch (e) { return null; } },
      write: function (value) { try { window.sessionStorage.setItem(key, JSON.stringify(value)); return true; } catch (e) { return false; } },
      clear: function () { try { window.sessionStorage.removeItem(key); } catch (e) {} }
    };
  }
  function recovery($container, snapshot, canDiscard, discard) {
    var $panel = $('<div style="margin:12px 0;"><button type="button" class="btn btn-default btn-sm">Descargar captura</button> <button type="button" class="btn btn-default btn-sm">Descartar borrador</button></div>').appendTo($container).hide();
    $panel.find('button').first().on('click', function () {
      var value = snapshot();
      if (!value) return;
      var url = window.URL.createObjectURL(new Blob([JSON.stringify(value, null, 2)], {type: 'application/json'}));
      var link = window.document.createElement('a');
      link.href = url; link.download = 'captura-pedido.json'; link.click();
      window.setTimeout(function () { window.URL.revokeObjectURL(url); }, 1000);
    });
    $panel.find('button').last().on('click', function () {
      if (!canDiscard()) return;
      swal({type: 'warning', title: '¿Descartar los cambios pendientes?', text: 'Puedes descargar la captura antes de descartarla.', showCancelButton: true, confirmButtonText: 'Descartar', cancelButtonText: 'Conservar'})
        .then(function (r) { if (r.value) discard(); });
    });
    return function (visible) { $panel.toggle(visible); $panel.find('button').first().prop('disabled', false); $panel.find('button').last().prop('disabled', !canDiscard()); };
  }
  window.PedidosGuardado = {ref: ref, request: request, drafts: drafts, recovery: recovery};
})(window, jQuery);
