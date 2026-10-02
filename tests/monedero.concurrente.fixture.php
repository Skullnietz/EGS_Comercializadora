<?php
// SQLite temporal: comprueba coordinación y rollback entre procesos, sin conectar con producción.
date_default_timezone_set('America/Mexico_City');
require_once __DIR__ . '/../modelos/monedero.persistencia.php';
$db=new PDO('sqlite:'.getenv('EGS_MONEDERO_TEST_DB'), null, null, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$db->exec('PRAGMA busy_timeout=10000');
$pool=new ReflectionProperty('Database','instances'); $pool->setAccessible(true); $pool->setValue(null,['wordpress'=>$db,'ecommerce'=>$db]);
if ($argv[1]==='init') {
    $db->exec('CREATE TABLE dinero_electronico(id INTEGER PRIMARY KEY, id_cliente INTEGER UNIQUE, saldo NUMERIC, token TEXT UNIQUE)');
    $db->exec('CREATE TABLE dinero_electronico_movimientos(id INTEGER PRIMARY KEY, id_cliente INTEGER,id_orden INTEGER,tipo TEXT,monto NUMERIC,saldo_anterior NUMERIC,saldo_nuevo NUMERIC,descripcion TEXT,referencia_tipo TEXT,referencia_id INTEGER,id_empresa INTEGER,id_usuario_aplico INTEGER,origen_total_bruto NUMERIC,origen_total_neto NUMERIC,monto_aplicado NUMERIC,fecha TEXT DEFAULT CURRENT_TIMESTAMP, UNIQUE(referencia_tipo,referencia_id,tipo))');
    $db->exec('CREATE TABLE ordenes(id INTEGER PRIMARY KEY,id_usuario INTEGER,id_empresa INTEGER,id_pedido INTEGER DEFAULT 0,estado TEXT,total NUMERIC,fecha_Salida TEXT,total_bruto_monedero NUMERIC,monto_monedero_aplicado NUMERIC DEFAULT 0,total_pagado_cliente NUMERIC,fecha_canje_monedero TEXT)');
    $db->exec('CREATE TABLE compras(id INTEGER PRIMARY KEY,id_cliente INTEGER,pago NUMERIC,fecha TEXT)');
    $db->prepare("INSERT INTO ordenes(id,id_usuario,id_empresa,estado,total,fecha_Salida) VALUES(1,2,1,'Entregado (Ent)',10000,?)")->execute([date('Y-m-d')]);
    $db->exec("INSERT INTO ordenes(id,id_usuario,id_empresa,estado,total) VALUES(101,2,1,'Terminada (ter)',200),(102,2,1,'Terminada (ter)',200)");
    echo '{}'; exit;
}
if ($argv[1]==='snapshot') {
    echo json_encode(['movimientos'=>$db->query('SELECT * FROM dinero_electronico_movimientos')->fetchAll(PDO::FETCH_ASSOC),'ordenes'=>$db->query('SELECT * FROM ordenes WHERE id>1')->fetchAll(PDO::FETCH_ASSOC),'saldo'=>ModeloRecompensas::mdlCalcularSaldoDinamico(2,1,1)]); exit;
}
$_SESSION=['id'=>7,'empresa'=>1,'perfil'=>'administrador','tokenMonederoOrden'=>'prueba']; $_POST=['tokenMonederoOrden'=>'prueba'];
$id=(int)$argv[1];
try {
    $r=MonederoPersistencia::guardarOrden($id,'Entregado (Ent)','200','80',function()use($db,$id){
        $db->prepare("UPDATE ordenes SET estado='Entregado (Ent)' WHERE id=?")->execute([$id]); return 'ok';
    },2);
    echo json_encode(['ok'=>true,'reintento'=>$r['reintento']]);
} catch (Throwable $e) { echo json_encode(['ok'=>false,'mensaje'=>$e->getMessage()]); }
