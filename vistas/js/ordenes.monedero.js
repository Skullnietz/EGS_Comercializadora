/* El servidor confirma el consumo junto con la entrega. Aquí solo se muestra el desglose. */
$(function () {
    var $monto = $('#egsMontoMonederoOrden');
    if (!$monto.length) return;
    var saldo = Math.max(0, Math.round(Number($monto.attr('data-saldo')) * 100) || 0);
    function formato(centavos) { return '$' + (centavos / 100).toFixed(2); }
    function bruto() { return Math.max(0, Math.round(Number($('#costoTotalDeOrden').val()) * 100) || 0); }
    function solicitado() {
        var valor = $monto.val();
        if (valor === '') return 0;
        return /^\d{1,8}(?:\.\d{1,2})?$/.test(valor) ? Math.round(Number(valor) * 100) : NaN;
    }
    function actualizar() {
        var total = bruto(), maximo = Math.min(saldo, total), importe = solicitado();
        var entregando = $('select[name="estado"]').val() === 'Entregado (Ent)';
        $('#egsMonederoPanel').toggleClass('visible', entregando);
        $monto.attr('max', (maximo / 100).toFixed(2));
        $('#egsMonederoUsarTodo').prop('disabled', maximo <= 0);
        $('#egsMonederoMaxLabel').text('Máximo aplicable: ' + formato(maximo));
        var valido = Number.isFinite(importe) && importe >= 0 && importe <= maximo;
        $('#egsMonederoHint').text(valido
            ? 'Aplica el importe únicamente cuando el cliente solicite usar su monedero.'
            : 'Revisa el importe: puedes aplicar hasta ' + formato(maximo) + ' con dos decimales.');
        // Nunca cambiar silenciosamente el importe que solicitó el cliente.
        $('#egsMontoMonederoOrdenHidden').val(entregando && valido ? (importe / 100).toFixed(2) : '0');
        $('#egsTotalBrutoMonederoOrden').val((total / 100).toFixed(2));
        $('#egsTotalPagadoMonederoOrden').val(((total - (valido && entregando ? importe : 0)) / 100).toFixed(2));
        $('#egsMondBruto').text(formato(total));
        $('#egsMondDescuento').text('-' + formato(valido ? importe : 0));
        $('#egsMondTotal').text(formato(total - (valido ? importe : 0)));
        $('#egsMonederoDesglose').toggle(entregando && valido && importe > 0);
        return !entregando || valido;
    }
    $(document).on('input change', '#egsMontoMonederoOrden, #costoTotalDeOrden, .precioPartidaGuardada', actualizar);
    $(document).on('change', 'select[name="estado"]', function () {
        if ($(this).val() !== 'Entregado (Ent)') $monto.val('');
        actualizar();
    });
    $(document).on('click', '#egsMonederoUsarTodo', function () {
        $monto.val((Math.min(saldo, bruto()) / 100).toFixed(2));
        actualizar();
    });
    $(document).on('submit', '#formObservaciones', function (event) {
        if (!actualizar()) {
            event.preventDefault();
            if (typeof swal === 'function') swal({type: 'error', title: 'Revisa el importe de monedero', text: $('#egsMonederoHint').text()});
            $monto.focus();
        }
    });
    actualizar();
});
