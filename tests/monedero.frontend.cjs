const assert = require('node:assert/strict');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const {spawn, spawnSync} = require('node:child_process');
const {JSDOM} = require('jsdom');
const jquery = require('jquery');
let checks=0;
function check(value,message) { assert.ok(value,message); checks++; }
const html=`<form id="formObservaciones"><select name="estado"><option>Terminada (ter)</option><option>Entregado (Ent)</option></select><input id="costoTotalDeOrden" value="50.25"></form>
<div id="egsMonederoPanel"><input id="egsMontoMonederoOrden" type="number" data-saldo="100.75"><button id="egsMonederoUsarTodo"></button><span id="egsMonederoMaxLabel"></span><span id="egsMonederoHint"></span><input id="egsMontoMonederoOrdenHidden"><input id="egsTotalBrutoMonederoOrden"><input id="egsTotalPagadoMonederoOrden"><div id="egsMonederoDesglose"><span id="egsMondBruto"></span><span id="egsMondDescuento"></span><span id="egsMondTotal"></span></div></div>`;
const base=fs.mkdtempSync(path.join(os.tmpdir(),'egs-monedero-regresion-'));
const fixture=path.join(__dirname,'monedero.concurrente.fixture.php');
const env={...process.env, EGS_MONEDERO_TEST_DIR:base, EGS_MONEDERO_TEST_DB:path.join(base,'monedero.sqlite')};
function php(args,input) {
    const r=spawnSync('php',args,{input:input===undefined?undefined:JSON.stringify(input),encoding:'utf8',windowsHide:true,env});
    if(r.error)throw r.error; assert.equal(r.status,0,r.stderr);
    return {body:JSON.parse(r.stdout),status:Number(/EGS_HTTP:(\d+)/.exec(r.stderr)?.[1]||200)};
}
function canjear(id) { return new Promise((resolve,reject)=>{
    const p=spawn('php',[fixture,String(id)],{windowsHide:true,env}); let out='',err='';
    p.stdout.on('data',s=>out+=s);p.stderr.on('data',s=>err+=s);p.on('error',reject);
    p.on('close',code=>{try{assert.equal(code,0,err);resolve(JSON.parse(out));}catch(e){reject(e);}});
}); }
(async()=>{
    const dom=new JSDOM(html,{runScripts:'outside-only',url:'https://egs.test/'});
    const w=dom.window,$=jquery(w),alerts=[];w.$=$;w.swal=o=>alerts.push(o);
    w.eval(fs.readFileSync(path.join(__dirname,'../vistas/js/ordenes.monedero.js'),'utf8'));
    w.document.dispatchEvent(new w.Event('DOMContentLoaded'));
    await new Promise(r=>setTimeout(r,20));
    check(!$('#egsMonederoPanel').hasClass('visible'),'No ofrece canje antes de elegir entrega');
    $('select[name=estado]').val('Entregado (Ent)').trigger('change');
    check($('#egsMonederoPanel').hasClass('visible'),'Ofrece canje durante entrega');
    $('#egsMonederoUsarTodo').trigger('click');
    check($('#egsMontoMonederoOrden').val()==='50.25' && $('#egsMontoMonederoOrdenHidden').val()==='50.25','Todo limita el canje al menor de saldo y total');
    check($('#egsTotalPagadoMonederoOrden').val()==='0.00','Canje completo muestra cobro cero');
    $('#egsMontoMonederoOrden').val('10.05').trigger('input');
    check($('#egsTotalPagadoMonederoOrden').val()==='40.20' && $('#egsMontoMonederoOrdenHidden').val()==='10.05','Desglose parcial conserva centavos');
    $('#egsMontoMonederoOrden').val('60').trigger('input');
    const permitido=w.document.querySelector('#formObservaciones').dispatchEvent(new w.Event('submit',{bubbles:true,cancelable:true}));
    check(!permitido && alerts.length===1 && $('#egsMontoMonederoOrden').val()==='60','Impide importe excesivo sin cambiar lo solicitado');
    $('#egsMontoMonederoOrden').val('1.001').trigger('input');
    check($('#egsMontoMonederoOrdenHidden').val()==='0','No redondea solicitud con más de dos decimales');
    $('#egsMontoMonederoOrden').val('10.05');
    w.document.querySelector('#formObservaciones').dispatchEvent(new w.Event('submit',{bubbles:true,cancelable:true}));
    check($('#egsMontoMonederoOrdenHidden').val()==='10.05','Sincroniza importe al guardar aunque no haya change');
    $('select[name=estado]').val('Terminada (ter)').trigger('change');
    check($('#egsMontoMonederoOrdenHidden').val()==='0' && $('#egsMontoMonederoOrden').val()==='','Salir de entrega limpia solicitud');
    $('#costoTotalDeOrden').val('0').trigger('change');$('select[name=estado]').val('Entregado (Ent)').trigger('change');$('#egsMonederoUsarTodo').trigger('click');
    check($('#egsMontoMonederoOrdenHidden').val()==='0.00','Total cero no usa un bruto oculto desactualizado');
    dom.window.close();
    const ajax=path.join(__dirname,'monedero.ajax.fixture.php');
    let r=php([ajax],{perfil:'sinSesion',post:{idClienteRecompensas:2}});
    check(r.status===401 && r.body.status==='error','Endpoint sin sesión no consulta ni consume');
    for(const campo of ['idClienteCanje','idClienteCanjeVenta','idClienteCanjePedido']) {
        r=php([ajax],{perfil:'administrador',post:{[campo]:2}});
        check(r.status===409 && r.body.status==='error','Endpoint antiguo no hace consumos separados');
    }
    r=php([ajax],{perfil:'vendedor',post:{toggleRecompensasVentas:1}});
    check(r.status===403,'Vendedor no cambia configuración de monedero');
    php([fixture,'init']);
    const resultados=await Promise.all([canjear(101),canjear(102)]);
    check(resultados.filter(r=>r.ok).length===1,'Dos entregas simultáneas no pueden gastar el mismo saldo');
    const snapshot=php([fixture,'snapshot']).body;
    check(snapshot.movimientos.length===1 && Number(snapshot.movimientos[0].monto)===-80,'Solo persiste un consumo durante concurrencia');
    check(snapshot.ordenes.filter(o=>o.estado==='Entregado (Ent)').length===1 && Number(snapshot.saldo)===21.2,'La entrega rechazada queda pendiente y el saldo conserva consistencia');
    const id=snapshot.ordenes.find(o=>o.estado==='Entregado (Ent)').id;
    const reintentos=await Promise.all([canjear(id),canjear(id)]);
    check(reintentos.every(r=>r.ok&&r.reintento) && php([fixture,'snapshot']).body.movimientos.length===1,'Reintentos simultáneos recuperan el consumo único');
    console.log(`OK: ${checks} comprobaciones de interfaz, endpoint y concurrencia del monedero`);
})().catch(e=>{console.error(e);process.exitCode=1;}).finally(()=>{
    for(const name of ['monedero.sqlite','sess_monederoregresion']) {const f=path.join(base,name);if(fs.existsSync(f))fs.unlinkSync(f);}
    fs.rmdirSync(base);
});
