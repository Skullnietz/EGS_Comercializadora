<?php
require_once "../../controladores/visitas.controlador.php";
require_once "../../modelos/visitas.modelo.php";

$visitas = ControladorVisitas::ctrMostrarVisitas();
if (!is_array($visitas)) {
	$visitas = array();
}

$nombre = 'visitasPersonas.xls';
header('Expires: 0');
header('Cache-control: private');
header("Content-type: application/vnd.ms-excel");
header("Cache-Control: cache, must-revalidate");
header('Content-Description: File Transfer');
header('Last-Modified: ' . date('D, d M Y H:i:s'));
header("Pragma: public");
header('Content-Disposition:; filename="' . $nombre . '"');
header("Content-Transfer-Encoding: binary");

echo "<table border='0'><tr>
<td style='font-weight:bold;border:1px solid #eee;'>IP</td>
<td style='font-weight:bold;border:1px solid #eee;'>PAIS</td>
<td style='font-weight:bold;border:1px solid #eee;'>VISITAS</td>
<td style='font-weight:bold;border:1px solid #eee;'>FECHA</td>
</tr>";

foreach ($visitas as $value) {
	echo "<tr>
<td style='border:1px solid #eee;'>" . htmlspecialchars($value["ip"]) . "</td>
<td style='border:1px solid #eee;'>" . htmlspecialchars($value["pais"]) . "</td>
<td style='border:1px solid #eee;'>" . intval($value["visitas"]) . "</td>
<td style='border:1px solid #eee;'>" . htmlspecialchars($value["fecha"]) . "</td>
</tr>";
}
echo "</table>";
