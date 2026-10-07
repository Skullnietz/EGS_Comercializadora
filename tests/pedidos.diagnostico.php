<?php
require_once __DIR__ . '/../modelos/pedidos.diagnostico.php';
require_once __DIR__ . '/../modelos/pedidos.errores.php';
$checks = 0;
function check($valor, $mensaje) { global $checks; if (!$valor) throw new RuntimeException($mensaje); $checks++; }
class DiagnosticoResultado {
    private $fila;
    public function __construct($fila=[]) { $this->fila=$fila; }
    public function fetch($modo) { return $this->fila; }
    public function closeCursor() { return true; }
}
class ConexionDiagnosticoPrueba {
    public $consultas = [];
    public $identidad;
    public $denegar = [];
    public function __construct($base) { $this->identidad=['esquema'=>$base,'cuenta'=>"usuario'prueba@localhost",'servidor'=>'servidor','puerto'=>3306,'id_servidor'=>1]; }
    public function quote($texto) { return "'" . str_replace("'", "''", $texto) . "'"; }
    public function query($sql) {
        $this->consultas[]=$sql;
        if (strpos($sql,'SELECT DATABASE()')===0) return new DiagnosticoResultado($this->identidad);
        if (strpos($sql,'EXPLAIN ')!==0 || stripos($sql,'ANALYZE')!==false) throw new RuntimeException('El diagnóstico intentó ejecutar una escritura');
        foreach ($this->denegar as $fragmento=>$codigo) if (strpos($sql,$fragmento)!==false) {
            $e=new PDOException('Error simulado');$e->errorInfo=['42000',$codigo,'Error simulado'];throw $e;
        }
        return new DiagnosticoResultado();
    }
}
$ec=new ConexionDiagnosticoPrueba('ecommerce');$wp=new ConexionDiagnosticoPrueba('principal');
$r=PedidosDiagnostico::ejecutar($ec,$wp);
check($r['correcto'] && count($r['pruebas'])===9 && !$r['sql_para_administrador_bd'],'Si los permisos pasan no propone cambios innecesarios');
check(count($ec->consultas)===10 && count($wp->consultas)===1,'Diagnostica las escrituras desde la cuenta que realmente guarda pedidos');
check(count(array_filter($ec->consultas,function($sql){return strpos($sql,'EXPLAIN ')===0;}))===9,'Los planes de escrituras usan EXPLAIN sin ejecutar DML');
$ec->denegar=['UPDATE `principal`.`ordenes`'=>1142];
$r=PedidosDiagnostico::ejecutar($ec,$wp);
check(!$r['correcto'] && count($r['sql_para_administrador_bd'])===1,'Identifica permiso faltante de vínculo');
check($r['sql_para_administrador_bd'][0]==="GRANT SELECT, UPDATE ON `principal`.`ordenes` TO 'usuario''prueba'@'localhost';",'GRANT afecta solo la tabla de órdenes y usa la cuenta efectiva escapada');
$ec->denegar=['`ecommerce`.`pedidos_guardados`'=>1142];
$r=PedidosDiagnostico::ejecutar($ec,$wp);
check(count($r['sql_para_administrador_bd'])===2 && strpos(implode(' ',$r['sql_para_administrador_bd']),'pedidos_guardados')!==false,'Permisos de confirmación se diagnostican por separado');
check(strpos(implode(' ',$r['sql_para_administrador_bd']),'*.*')===false && strpos(implode(' ',$r['sql_para_administrador_bd']),'ALL PRIVILEGES')===false,'No propone permisos globales');
$ec->denegar=['`ecommerce`.`pedidos_guardados`'=>1146];
$r=PedidosDiagnostico::ejecutar($ec,$wp);
check(!$r['correcto'] && !$r['sql_para_administrador_bd'],'Una tabla faltante necesita migración, no GRANT');
$wp->identidad['servidor']='otro_servidor';$ec->denegar=[];
$r=PedidosDiagnostico::ejecutar($ec,$wp);
check(!$r['mismo_servidor'] && !$r['correcto'] && count($r['pruebas'])===7 && !$r['sql_para_administrador_bd'],'Servidores separados no se arreglan otorgando permisos cruzados');
foreach ([1044,1045,1142,1143,1227] as $codigo) {
    $e=new PDOException("UPDATE command denied to user 'cuenta_interna'@'localhost' for table 'ordenes'");$e->errorInfo=['42000',$codigo,$e->getMessage()];
    $r=PedidosErrores::respuesta($e);
    check($r['body']['reintentar']===false && $r['body']['tipo']==='permisos_servidor' && strpos($r['body']['mensaje'],'cuenta_interna')===false,'Permisos faltantes no son un fallo temporal ni exponen la cuenta');
}
$e=new PDOException('Conexión perdida');$e->errorInfo=['HY000',2013,'Conexión perdida'];
check(PedidosErrores::respuesta($e)['body']['reintentar']===true,'Una conexión perdida conserva recuperación del mismo intento');
$e=new PDOException('Dato demasiado largo');$e->errorInfo=['22001',1406,'Dato demasiado largo'];
check(PedidosErrores::respuesta($e)['body']['reintentar']===false,'Un dato rechazado no se repite automáticamente');
echo "OK: $checks comprobaciones de diagnóstico y clasificación de errores\n";
