const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const {JSDOM} = require('jsdom');
const jquery = require('jquery');
const source = name => fs.readFileSync(path.join(__dirname, '../vistas/js', name), 'utf8');
const tick = () => new Promise(resolve => setTimeout(resolve, 15));
let checks = 0;
function check(value, message) { assert.ok(value, message); checks++; }
const detail = `<form id="pedidoDetalleForm" data-usuario="7:1" data-finanzas="1" data-pagado="20.5">
  <input name="idPedido" value="1"><input name="versionPedido" value="versionInicial">
  <select name="estado"><option>Pedido Pendiente</option><option>Pedido Adquirido</option></select>
  <input name="total" type="number" value="100.5"><span class="ped-pagado"></span><span class="ped-adeudo"></span>
  <div class="ped-dynamic-product" data-indice="0" data-subtotal="100.5"><input class="ped-prod-descripcion" value="Equipo"><input class="ped-prod-cantidad" type="number" value="2"><input class="ped-prod-precio" type="number" value="50.25"><span class="ped-line-subtotal"></span></div>
  <div id="pedNewPaymentForm"><input id="pedNuevoAbonoMonto" type="number"><input id="pedNuevoAbonoFecha" type="date"></div>
  <button type="button" class="btnAgregarAbono">Agregar abono</button><div class="ped-payments"></div>
  <div id="pedObsCompose"><span class="ped-obs-avatar">C</span><textarea id="pedNewObsText"></textarea></div>
  <button type="button" class="btnAgregarObservacionInfoPedido">Agregar</button><span class="ped-observation-status"></span><span class="ped-obs-count"></span><div class="ped-obs-list"></div>
  <button type="submit">Guardar</button></form>`;
const nuevo = `<div id="modalAgregarPedido"><form id="pedidoNuevoForm" data-usuario="7:1"><div class="modal-body">
  <input name="empresaPedioDinamico" value="1"><select name="asesorPedidoDinamico"><option value=""></option><option value="3">Asesor</option></select>
  <select name="clientePedidoDinamico"><option value=""></option><option value="2">Cliente</option></select>
  <select name="EstadoPedidoDinamico"><option>Pedido Pendiente</option><option>Pedido Adquirido</option></select>
  <select name="seleccionarOrdenPedidoDinamico"><option value="0">Sin orden</option><option value="101">101</option></select>
  <button type="button" class="AgregarProductos">Agregar</button><div class="NuevoProductoPedido"></div>
  <input class="PagoClientePedidoDinamico" type="number"><input class="fechaPagoVentaModal" type="date"><input class="TotalPedidoEnOrden">
  </div><button type="submit">Guardar</button></form></div>`;
async function app(html, script, stored = {}) {
  const dom = new JSDOM(html, {url: 'https://egs.test/index.php?ruta=pedidos', runScripts: 'outside-only'});
  const w = dom.window, $ = jquery(w), calls = [], alerts = [];
  w.jQuery = $; w.$ = $;
  w.setTimeout = fn => setTimeout(fn, 0);
  w.swal = options => { alerts.push(options); return options.type === 'success' ? new Promise(() => {}) : Promise.resolve({value: true}); };
  $.fn.modal = function () { return this; };
  $.ajax = options => { const deferred = $.Deferred(); calls.push({data: JSON.parse(JSON.stringify(options.data)), options, deferred}); return deferred.promise(); };
  Object.entries(stored).forEach(([key, value]) => w.sessionStorage.setItem(key, value));
  if (script === 'pedidos.nuevo.js') {
    const orders = source('gestorOrdenes.js');
    const start = orders.indexOf("$('.AgregarProductos').click(function()");
    const end = orders.indexOf('$(document).ready(function(){', start);
    check(start >= 0 && end > start, 'Carga el manejador real de filas de producto');
    w.eval(orders.slice(start, end));
    w.sumarTotalPreciosPedido = () => {};
  }
  w.eval(source('pedidos.guardado.js'));
  w.eval(source(script));
  w.document.dispatchEvent(new w.Event('DOMContentLoaded'));
  await tick();
  return {w, $, calls, alerts, close: () => dom.window.close(), storage: () => Object.fromEntries(Array.from({length: w.sessionStorage.length}, (_, i) => {const key = w.sessionStorage.key(i); return [key, w.sessionStorage.getItem(key)];}))};
}
function fail(call, status = 0, mensaje) { call.deferred.reject({status, responseJSON: mensaje ? {ok: false, mensaje} : undefined}); }
async function exhaust(a, offset = 0) { for (let i = 0; i < 3; i++) { fail(a.calls[offset + i]); await tick(); } }
function fillNew(a) {
  const $ = a.$;
  $('[name=asesorPedidoDinamico]').val('3').trigger('change'); $('[name=clientePedidoDinamico]').val('2').trigger('change');
  $('.AgregarProductos').trigger('click'); $('.descripcioProductoPedido').val('Equipo 💻').trigger('input');
  $('.nuevoPrecioProductoPedido').val('50.25').trigger('input'); $('.nuevaCantidadProductoPedido').val('2').trigger('input');
  $('.PagoClientePedidoDinamico').val('20.5').trigger('input'); $('.fechaPagoVentaModal').val('2026-10-02').trigger('change');
}
(async function () {
  let a = await app(detail, 'pedidos.detalle.js');
  a.$('.ped-prod-cantidad').val('3').trigger('input');
  check(a.$('[name=total]').val() === '150.75', 'La cantidad ajusta el total una sola vez');
  a.$('.ped-prod-cantidad').val('4').trigger('input');
  check(a.$('[name=total]').val() === '201', 'Los cambios repetidos no acumulan multiplicaciones');
  a.$('#pedNuevoAbonoMonto').val('10.25').trigger('input'); a.$('#pedNuevoAbonoFecha').val('2026-10-02').trigger('change');
  a.$('#pedNewObsText').val('Observación ✅').trigger('input');
  a.$('#pedidoDetalleForm').trigger('submit');
  check(a.calls.length === 1 && a.calls[0].data.accionPedido === 'guardar' && a.calls[0].data.observacion === 'Observación ✅', 'Guardar envía campos y observación juntos');
  const payload = a.calls[0].data;
  check(JSON.parse(payload.pagos).length === 1, 'Incluye abono escrito aunque no se presione Agregar');
  await exhaust(a);
  check(a.calls.every(c => JSON.stringify(c.data) === JSON.stringify(payload)), 'Los reintentos conservan solicitud y contenido exactos');
  check(!a.alerts.some(x => x.type === 'success'), 'No muestra éxito sin confirmación');
  check(a.$('[name=total]').prop('disabled') && !a.$('button[type=submit]').prop('disabled'), 'Respuesta incierta permite recuperar sin editar el intento');
  const backup = a.storage(); a.close();
  a = await app(detail, 'pedidos.detalle.js', backup);
  check(a.$('[name=total]').val() === '201' && a.$('#pedNewObsText').val() === 'Observación ✅', 'Recupera todos los campos al recargar');
  a.$('#pedidoDetalleForm').trigger('submit');
  assert.deepEqual(a.calls[0].data, payload); checks++;
  a.calls[0].deferred.resolve({ok: true, version: 'versionNueva'}); await tick();
  check(a.alerts.some(x => x.type === 'success') && a.w.sessionStorage.length === 0, 'Solo borra el borrador al confirmar éxito'); a.close();

  a = await app(detail, 'pedidos.detalle.js');
  a.$('#pedNewObsText').val('Texto que no debe cambiar').trigger('input'); a.$('.btnAgregarObservacionInfoPedido').trigger('click');
  const observation = a.calls[0].data;
  await exhaust(a);
  check(a.$('#pedNewObsText').prop('readonly'), 'Observación sin confirmar conserva texto y referencia');
  const obsBackup = a.storage(); a.close();
  a = await app(detail, 'pedidos.detalle.js', obsBackup);
  a.$('.btnAgregarObservacionInfoPedido').trigger('click');
  assert.deepEqual(a.calls[0].data, observation); checks++;
  a.calls[0].deferred.resolve({ok: true, observacion: {observacion: observation.observacion, creador: 'Carlos', fecha: '10/2/2026', ref: observation.ref}}); await tick();
  check(a.$('.ped-obs-item').length === 1 && a.$('#pedNewObsText').val() === '', 'Muestra observación únicamente al confirmar'); a.close();

  a = await app(detail, 'pedidos.detalle.js');
  a.$('[name=total]').val('120').trigger('input'); a.$('#pedidoDetalleForm').trigger('submit');
  fail(a.calls[0], 409, 'Otra persona modificó el pedido'); await tick();
  check(a.calls.length === 1 && a.$('[name=total]').val() === '120' && !a.$('[name=total]').prop('disabled'), 'Conflicto conserva captura y no se reintenta automáticamente'); a.close();

  a = await app(nuevo, 'pedidos.nuevo.js'); fillNew(a); a.$('#pedidoNuevoForm').trigger('submit');
  const creation = a.calls[0].data;
  check(creation.accionPedido === 'crear' && JSON.parse(creation.productos)[0].precioUnitario === '50.25', 'Alta serializa precio unitario y centavos');
  a.$('#pedidoNuevoForm').trigger('submit'); check(a.calls.length === 1, 'Doble clic no envía dos altas');
  await exhaust(a);
  check(a.calls.every(c => JSON.stringify(c.data) === JSON.stringify(creation)), 'Alta reintentada conserva su identificador');
  const newBackup = a.storage(); a.close();
  a = await app(nuevo, 'pedidos.nuevo.js', newBackup);
  check(a.$('.descripcioProductoPedido').val() === 'Equipo 💻' && a.$('.fechaPagoVentaModal').val() === '2026-10-02', 'Recupera filas del alta, selecciones, pago y fecha');
  a.$('#pedidoNuevoForm').trigger('submit'); assert.deepEqual(a.calls[0].data, creation); checks++;
  a.calls[0].deferred.resolve({ok: true, id: 42}); await tick();
  check(a.w.sessionStorage.length === 0 && a.alerts.some(x => x.title.includes('#42')), 'Alta confirmada identifica pedido y limpia borrador'); a.close();

  a = await app(nuevo, 'pedidos.nuevo.js'); fillNew(a); a.$('#pedidoNuevoForm').trigger('submit');
  fail(a.calls[0], 401, 'Tu sesión expiró'); await tick();
  check(a.calls.length === 1 && a.$('.descripcioProductoPedido').val() === 'Equipo 💻' && !a.$('.descripcioProductoPedido').prop('disabled'), 'Sesión expirada conserva el pedido y habilita reenvío'); a.close();
  a = await app(nuevo, 'pedidos.nuevo.js'); fillNew(a);
  a.$('.PagoClientePedidoDinamico').val('').trigger('input'); a.$('#pedidoNuevoForm').trigger('submit');
  check(a.calls.length === 0 && a.alerts.some(x => x.text.includes('monto')), 'No omite una fecha de pago capturada sin monto'); a.close();
  a = await app(nuevo, 'pedidos.nuevo.js'); fillNew(a);
  Object.defineProperty(a.$('.PagoClientePedidoDinamico')[0], 'validity', {value: {badInput: true}});
  a.$('#pedidoNuevoForm').trigger('submit');
  check(a.calls.length === 0, 'No ignora un importe que el navegador marca como inválido'); a.close();
  console.log(`OK: ${checks} comprobaciones de frontend`);
})().catch(error => { console.error(error); process.exitCode = 1; });
