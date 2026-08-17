<?php
/*=====================================================================
 LIMPIEZA DE FOTOS POR ESPACIO (mantenimiento)
 - Solo administradores.
 - Cubre dos módulos: Observaciones y Reporte de estado del equipo.
 - Borra los archivos físicos y sus registros anteriores a una fecha de
   corte, y elimina carpetas AAAA-MM vacías.
=====================================================================*/

session_start();

if (!isset($_SESSION["validarSesionBackend"]) || $_SESSION["validarSesionBackend"] !== "ok"
	|| !isset($_SESSION["perfil"])
	|| !in_array($_SESSION["perfil"], array("administrador", "Super-Administrador"), true)) {
	http_response_code(403);
	echo "Acceso restringido. Inicia sesión como administrador.";
	exit;
}

require_once __DIR__ . "/../modelos/conexion.php";
require_once __DIR__ . "/../modelos/observacionOrdenes.modelo.php";
require_once __DIR__ . "/../modelos/reporteEquipo.modelo.php";

// Módulos soportados: clave => configuración
$modulos = array(
	"observaciones" => array(
		"etiqueta" => "Observaciones",
		"baseDir"  => __DIR__ . "/../vistas/img/observaciones",
		"tabla"    => "observacionesFotos",
		"modelo"   => "ModeloObservaciones"
	),
	"reporte" => array(
		"etiqueta" => "Reporte de estado del equipo",
		"baseDir"  => __DIR__ . "/../vistas/img/reporte-equipo",
		"tabla"    => "reporteEstadoEquipoFotos",
		"modelo"   => "ModeloReporteEquipo"
	)
);

/*---------------------------------------------------------------------
 Utilidades
---------------------------------------------------------------------*/
function fmtBytes($b) {
	$u = array('B','KB','MB','GB','TB');
	$i = 0;
	$b = max(0, (float)$b);
	while ($b >= 1024 && $i < count($u) - 1) { $b /= 1024; $i++; }
	return round($b, 2) . ' ' . $u[$i];
}

function dirSize($dir) {
	$total = 0;
	if (!is_dir($dir)) return 0;
	$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
	foreach ($it as $f) { if ($f->isFile()) $total += $f->getSize(); }
	return $total;
}

function eliminarDirsVacios($dir) {
	if (!is_dir($dir)) return;
	foreach (scandir($dir) as $entry) {
		if ($entry === '.' || $entry === '..') continue;
		$ruta = $dir . '/' . $entry;
		if (is_dir($ruta)) {
			eliminarDirsVacios($ruta);
			$restantes = array_diff(scandir($ruta), array('.', '..'));
			if (empty($restantes)) { @rmdir($ruta); }
		}
	}
}

/*---------------------------------------------------------------------
 Procesar borrado
---------------------------------------------------------------------*/
$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["fechaCorte"]) && isset($_POST["modulo"])) {

	$claveModulo = $_POST["modulo"];

	if (isset($modulos[$claveModulo])) {
		$cfg        = $modulos[$claveModulo];
		$modelo     = $cfg["modelo"];
		$tabla      = $cfg["tabla"];
		$fechaCorte = date("Y-m-d 00:00:00", strtotime($_POST["fechaCorte"]));
		$fotos      = $modelo::mdlFotosAnterioresA($tabla, $fechaCorte);

		$borrados = 0;
		$liberado = 0;
		$idsBorrar = array();

		if (is_array($fotos)) {
			foreach ($fotos as $foto) {
				$rutaFisica = __DIR__ . "/../" . ltrim($foto["ruta"], "/");
				if (!empty($foto["ruta"]) && file_exists($rutaFisica)) {
					$liberado += @filesize($rutaFisica);
					if (@unlink($rutaFisica)) { $borrados++; }
				}
				$idsBorrar[] = $foto["id"];
			}
			$modelo::mdlEliminarFotosPorIds($tabla, $idsBorrar);
		}

		eliminarDirsVacios($cfg["baseDir"]);

		$mensaje = "[" . htmlspecialchars($cfg["etiqueta"]) . "] Se eliminaron <strong>$borrados</strong> archivo(s), liberando <strong>"
				 . fmtBytes($liberado) . "</strong>. Registros en BD purgados: <strong>" . count($idsBorrar) . "</strong>.";
	}
}

/*---------------------------------------------------------------------
 Uso por carpeta AAAA-MM (por módulo)
---------------------------------------------------------------------*/
function usoPorMes($baseDir) {
	$carpetas = array();
	if (is_dir($baseDir)) {
		foreach (scandir($baseDir) as $entry) {
			if ($entry === '.' || $entry === '..') continue;
			$ruta = $baseDir . '/' . $entry;
			if (is_dir($ruta) && preg_match('/^\d{4}-\d{2}$/', $entry)) {
				$carpetas[$entry] = dirSize($ruta);
			}
		}
		krsort($carpetas);
	}
	return $carpetas;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Limpieza de fotos por espacio</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<style>
		body { font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif; background:#f1f5f9; color:#0f172a; margin:0; padding:24px; }
		.card { max-width:760px; margin:0 auto 20px; background:#fff; border-radius:14px; box-shadow:0 4px 20px rgba(0,0,0,.07); overflow:hidden; }
		.head { background:linear-gradient(135deg,#6366f1,#818cf8); color:#fff; padding:20px 24px; }
		.head h1 { font-size:18px; margin:0; font-weight:600; }
		.head p { margin:6px 0 0; font-size:13px; opacity:.9; }
		.body { padding:24px; }
		.alert { background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:12px 16px; border-radius:10px; font-size:14px; margin-bottom:18px; }
		.modtitle { font-size:15px; margin:0 0 8px; display:flex; align-items:center; gap:8px; }
		table { width:100%; border-collapse:collapse; margin:8px 0 16px; font-size:14px; }
		th, td { text-align:left; padding:10px 12px; border-bottom:1px solid #f1f5f9; }
		th { color:#64748b; font-size:12px; text-transform:uppercase; letter-spacing:.4px; }
		.total { font-weight:700; color:#6366f1; }
		label { font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.4px; display:block; margin-bottom:6px; }
		input[type=date] { padding:10px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; }
		.btn { background:#6366f1; color:#fff; border:none; padding:11px 20px; border-radius:8px; font-weight:600; font-size:14px; cursor:pointer; }
		.btn:hover { background:#4f46e5; }
		.empty { color:#94a3b8; text-align:center; padding:20px; }
		a.back { display:inline-block; margin:0 auto; color:#6366f1; text-decoration:none; font-size:13px; }
	</style>
</head>
<body>
	<div class="card">
		<div class="head">
			<h1><i class="fa-solid fa-broom"></i> Limpieza de fotos por espacio</h1>
			<p>Libera espacio borrando fotos antiguas. El texto de observaciones y reportes no se modifica.</p>
		</div>
		<div class="body">
			<?php if ($mensaje): ?>
				<div class="alert"><i class="fa-solid fa-circle-check"></i> <?php echo $mensaje; ?></div>
			<?php endif; ?>
		</div>
	</div>

	<?php foreach ($modulos as $clave => $cfg):
		$carpetas = usoPorMes($cfg["baseDir"]);
		$totalDisco = array_sum($carpetas);
	?>
	<div class="card">
		<div class="body">
			<h3 class="modtitle"><i class="fa-solid fa-folder-tree" style="color:#6366f1"></i> <?php echo htmlspecialchars($cfg["etiqueta"]); ?></h3>
			<?php if (!empty($carpetas)): ?>
				<table>
					<thead><tr><th>Carpeta (año-mes)</th><th>Tamaño</th></tr></thead>
					<tbody>
						<?php foreach ($carpetas as $mes => $size): ?>
							<tr><td><?php echo htmlspecialchars($mes); ?></td><td><?php echo fmtBytes($size); ?></td></tr>
						<?php endforeach; ?>
						<tr><td class="total">TOTAL</td><td class="total"><?php echo fmtBytes($totalDisco); ?></td></tr>
					</tbody>
				</table>
			<?php else: ?>
				<div class="empty"><i class="fa-regular fa-folder-open" style="font-size:24px;display:block;margin-bottom:6px;opacity:.5"></i>No hay fotos almacenadas todavía.</div>
			<?php endif; ?>

			<form method="post" onsubmit="return confirm('¿Borrar de forma permanente todas las fotos de «<?php echo htmlspecialchars($cfg["etiqueta"]); ?>» anteriores a la fecha seleccionada? Esta acción no se puede deshacer.');">
				<input type="hidden" name="modulo" value="<?php echo htmlspecialchars($clave); ?>">
				<label>Borrar fotos anteriores a</label>
				<input type="date" name="fechaCorte" required value="<?php echo date('Y-m-d', strtotime('-6 months')); ?>">
				<button type="submit" class="btn"><i class="fa-solid fa-trash-can"></i> Borrar antiguas</button>
			</form>
		</div>
	</div>
	<?php endforeach; ?>

	<div style="text-align:center">
		<a class="back" href="../index.php?ruta=ordenes"><i class="fa-solid fa-arrow-left"></i> Volver al panel</a>
	</div>
</body>
</html>
