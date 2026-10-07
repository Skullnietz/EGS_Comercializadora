const assert = require('node:assert/strict');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const {spawnSync} = require('node:child_process');
const base = fs.mkdtempSync(path.join(os.tmpdir(), 'egs-pedidos-regresion-'));
const dbFile = path.join(base, 'pedidos.sqlite');
let checks = 0;
function run(input) {
  const result = spawnSync('php', ['-d', 'display_startup_errors=0', path.join(__dirname, 'pedidos.ajax.fixture.php')], {
    input: JSON.stringify(input), encoding: 'utf8', windowsHide: true,
    env: {...process.env, EGS_PEDIDOS_TEST_DB: dbFile}
  });
  if (result.error) throw result.error;
  assert.equal(result.status, 0, result.stderr);
  const status = /EGS_HTTP:(\d+)/.exec(result.stderr);
  return {status: status ? Number(status[1]) : 200, body: JSON.parse(result.stdout)};
}
function check(value, message) { assert.ok(value, message); checks++; }
try {
  const create = {accionPedido: 'crear', solicitud: 'ajaxcrear001', empresa: 1, cliente: 2, asesor: 3, productos: '[{"Descripcion":"Equipo 💻","cantidad":2,"precioUnitario":50.25}]', pago: '[{"pago":20.5,"fecha":"2026-10-02"}]', estado: 'Pedido Pendiente', id_orden: 0};
  for (const [tabla, comando, vinculada] of [['pedidos_guardados','INSERT',false], ['pedidos_guardados','UPDATE',false], ['ordenes','UPDATE',true], ['pedidos','INSERT',false]]) {
    const permiso = run({rechazarTabla: tabla, rechazarComando: comando, post: {...create, id_orden: vinculada ? 101 : 0}});
    check(permiso.status === 500 && permiso.body.ok === false && permiso.body.codigo === 1142 && permiso.body.reintentar === false, '1142 se clasifica como rechazo definitivo del servidor');
    check(permiso.body.tipo === 'permisos_servidor' && !JSON.stringify(permiso.body).includes('cuenta_prueba'), 'Respuesta de permisos explica el fallo sin exponer la cuenta SQL');
    const integridad = run({integridad: true}).body;
    check(integridad.pedidos === 0 && integridad.confirmaciones === 0 && integridad.vinculos === 0, 'Permiso denegado revierte pedido, recibo y vínculo, incluso al confirmar o vincular');
    if (vinculada) check(permiso.body.mensaje.includes('vincular la orden'), 'Diagnóstico de vínculo identifica el permiso de órdenes');
  }
  const initial = run({post: create});
  check(initial.status === 200 && initial.body.ok === true && initial.body.id > 0 && initial.body.version.length === 64, 'Alta AJAX entrega ID y versión confirmados');
  assert.deepEqual(run({post: create}).body, initial.body); checks++;
  check(run({snapshot: true}).body.length === 1, 'Repetir el alta AJAX no duplica el pedido');
  const save = {accionPedido: 'guardar', solicitud: 'ajaxguardar1', idPedido: String(initial.body.id), versionPedido: initial.body.version, estado: 'Pedido Adquirido', total: '120.25', productos: '[]', pagos: '[{"pago":10.25,"fecha":"2026-10-02","ref":"ajaxabono01"}]', observacion: 'Pedido comprobado ✅'};
  const saved = run({post: save});
  check(saved.status === 200 && saved.body.ok === true && saved.body.version !== initial.body.version, 'Guardar AJAX confirma una versión nueva');
  assert.deepEqual(run({post: save}).body, saved.body); checks++;
  const row = run({snapshot: true}).body[0];
  check(Number(row.adeudo) === 89.5 && JSON.parse(row.pagos).length === 2 && JSON.parse(row.observaciones).length === 1, 'Pedido completo persiste sin duplicar pagos ni observaciones');
  const conflict = run({post: {...save, solicitud: 'ajaxguardar2'}});
  check(conflict.status === 409 && conflict.body.ok === false, 'Guardado nuevo con versión vieja devuelve conflicto');
  const missing = run({post: {...save, solicitud: ''}});
  check(missing.status === 409 && missing.body.ok === false, 'Falta de identificador no devuelve éxito');
  const expired = run({perfil: 'sinSesion', post: save});
  check(expired.status === 401 && expired.body.ok === false, 'Sesión expirada se comunica con JSON y HTTP 401');
  const restricted = run({perfil: 'vendedor', post: save});
  check(restricted.status === 409 && restricted.body.ok === false, 'Un vendedor no puede reutilizar el recibo de una edición financiera');
  const obs = {accionPedido: 'agregarObservacion', idPedido: String(initial.body.id), observacion: 'Observación de vendedor', ref: 'ajaxobserv01'};
  const observation = run({perfil: 'vendedor', post: obs});
  check(observation.body.ok === true && observation.body.observacion.observacion === obs.observacion, 'Un vendedor sí puede agregar observaciones');
  assert.deepEqual(run({perfil: 'vendedor', post: obs}).body, observation.body); checks++;
  const old = run({post: {idPedidoDinamicoAjax: initial.body.id, pagos: '[]'}});
  check(old.status === 409 && old.body.ok === false, 'Pestaña antigua no reescribe listas sin control de versión');
  console.log(`OK: ${checks} comprobaciones del endpoint AJAX`);
} finally {
  // Solo archivos propios de este directorio temporal; no hay borrado recursivo.
  for (const name of ['pedidos.sqlite', 'sess_pedidosregresion']) {
    const file = path.join(base, name);
    if (fs.existsSync(file)) fs.unlinkSync(file);
  }
  fs.rmdirSync(base);
}
