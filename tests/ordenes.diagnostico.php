<?php
require_once __DIR__ . '/../modelos/ordenes.diagnostico.php';
$checks=0;
function check($valor,$mensaje) { global $checks; if (!$valor) throw new RuntimeException($mensaje); $checks++; }
class ResultadoDiagnosticoOrdenes {
    private $datos;
    public function __construct($datos=[]) { $this->datos=$datos; }
    public function fetch($modo) { return $this->datos; }
    public function fetchAll($modo) { return $this->datos; }
    public function closeCursor() { return true; }
}
class ConexionDiagnosticoOrdenes {
    public $consultas=[];
    public $denegar=[];
    public $motores=[['TABLE_NAME'=>'ordenes','ENGINE'=>'InnoDB'],['TABLE_NAME'=>'dinero_electronico','ENGINE'=>'InnoDB'],['TABLE_NAME'=>'dinero_electronico_movimientos','ENGINE'=>'InnoDB']];
    public $columnas=[];
    public function __construct() {
        $campos=[
            'ordenes'=>'id,id_usuario,id_empresa,estado,total,fecha_Salida,total_bruto_monedero,monto_monedero_aplicado,total_pagado_cliente,fecha_canje_monedero',
            'dinero_electronico'=>'id_cliente,saldo,token',
            'dinero_electronico_movimientos'=>'id,id_cliente,id_orden,tipo,monto,saldo_anterior,saldo_nuevo,descripcion,referencia_tipo,referencia_id,id_empresa,id_usuario_aplico,origen_total_bruto,origen_total_neto,monto_aplicado'
        ];
        foreach ($campos as $tabla=>$lista) foreach (explode(',',$lista) as $campo) $this->columnas[]=['TABLE_NAME'=>$tabla,'COLUMN_NAME'=>$campo,'DATA_TYPE'=>'int','COLUMN_TYPE'=>$campo==='fecha_Salida'?'date':'int'];
    }
    public function quote($s) { return "'" . str_replace("'","''",$s) . "'"; }
    public function query($sql) {
        $this->consultas[]=$sql;
        if (strpos($sql,'SELECT DATABASE()')===0) return new ResultadoDiagnosticoOrdenes(['esquema'=>'principal','cuenta'=>"cuenta'ordenes@localhost"]);
        if (strpos($sql,'FROM information_schema.TABLES')!==false) return new ResultadoDiagnosticoOrdenes($this->motores);
        if (strpos($sql,'FROM information_schema.COLUMNS')!==false) return new ResultadoDiagnosticoOrdenes($this->columnas);
        if (strpos($sql,'EXPLAIN ')!==0 || stripos($sql,'ANALYZE')!==false) throw new RuntimeException('El diagnóstico intentó una escritura');
        foreach ($this->denegar as $fragmento=>$codigo) if (strpos($sql,$fragmento)!==false) {
            $e=new PDOException('Error simulado'); $e->errorInfo=['42000',$codigo,'Error simulado']; throw $e;
        }
        return new ResultadoDiagnosticoOrdenes();
    }
}
$db=new ConexionDiagnosticoOrdenes();
$r=OrdenesDiagnostico::ejecutar($db);
check($r['correcto'] && count($r['pruebas'])===6 && !$r['sql_para_administrador_bd'],'Esquema y permisos completos no solicitan cambios');
check(count($db->consultas)===9 && count(array_filter($db->consultas,function($s){return strpos($s,'EXPLAIN ')===0;}))===6,'Los planes de entrega y consumo no ejecutan escrituras');
check($r['cuenta_ordenes']==="cuenta'ordenes@localhost" && $r['fecha_salida']==='date','Diagnostica la cuenta de órdenes y el tipo real de fecha');
$db->denegar=['INSERT INTO dinero_electronico_movimientos'=>1142];
$r=OrdenesDiagnostico::ejecutar($db);
check(!$r['correcto'] && $r['pruebas'][5]['codigo']===1142,'Identifica permiso de consumo denegado');
check($r['sql_para_administrador_bd']===["GRANT INSERT ON `principal`.`dinero_electronico_movimientos` TO 'cuenta''ordenes'@'localhost';"],'GRANT se dirige a la cuenta real de órdenes y solo a la tabla afectada');
$db->denegar=['dinero_electronico'=>1142];
$r=OrdenesDiagnostico::ejecutar($db);
check(strpos(implode(' ',$r['sql_para_administrador_bd']),'*.*')===false && strpos(implode(' ',$r['sql_para_administrador_bd']),'ALL PRIVILEGES')===false,'No propone permisos globales');
$db->denegar=[]; $db->motores[0]['ENGINE']='MyISAM';
$r=OrdenesDiagnostico::ejecutar($db);
check(!$r['correcto'] && isset($r['indicacion']) && !$r['sql_para_administrador_bd'],'Un motor sin rollback necesita migración, no GRANT');
$db=new ConexionDiagnosticoOrdenes();
$db->columnas=array_values(array_filter($db->columnas,function($c){return $c['COLUMN_NAME']!=='monto_monedero_aplicado';}));
$db->denegar=['UPDATE ordenes'=>1054];
$r=OrdenesDiagnostico::ejecutar($db);
check(!$r['correcto'] && $r['columnas_faltantes']['ordenes']===['monto_monedero_aplicado'],'Identifica exactamente las columnas sin migrar');
check(!$r['sql_para_administrador_bd'] && strpos($r['indicacion'],'20261002_entrega_monedero.sql')!==false,'Una columna faltante no se trata como permiso denegado');
$db=new ConexionDiagnosticoOrdenes(); array_pop($db->motores);
$r=OrdenesDiagnostico::ejecutar($db);
check(!$r['correcto'] && array_key_exists('dinero_electronico_movimientos',$r['motores']) && $r['motores']['dinero_electronico_movimientos']===null,'Detecta tabla faltante o inaccesible para la cuenta real');
echo "OK: $checks comprobaciones de diagnóstico de entrega\n";
