<!--===================
DEBERAS COLOCAR UN BOTON CON LAS CLASE btn btn-success
EL BOTOND DEBERA TENER LA LEYENDA DESCARGAR PRODUCTOS
Y DEBERA CONTAR CON LA ETIQUETA DE ENLACE <a href="#"></a>
ESTE BOTON DEBERA ESTAR ENLASADO A LA RUTA 
vistas/modulos/descargar-reporte-productos.php?reporte=productos
pasando por variable GET el nobre del reporte a crear
====================-->
<a href=""></a>
<button class="#"></button>

<!--==================
PARA PODER VOLCAR LOS DATOS Y DESCARGAR EL REPORTE EN FORMATO EXCEL 
DEBERAS INSTANCIAR DENTRO DEL ARCHIVO descargar-reporte-productos.php
DEBERS INSTANCIAR UN METODO EL CUAL CREARAS DENTRO DE LA CLASE DEL CONTROLADOR PRODUCTOS
=====================-->
<?php 

$objeto = new clase();
$objeto -> metodo();

?>
<!--===================
RECUERDA QUE APRA PODER HACER QUE FUNCIONE TU INSTANCIA AEL METODO QUE CREARAS 
DEBES COLOCAR require_once ENRUTANDO EL CONTROLADOR QUE ESTAS LLAMANDO EN ESTE CASO EL DE PRODUCTOS
=======================-->
<?php
require_once "../../modelos/reportes.modelo.php";
?>

<!--==================
POR ULTIMO DENTRO DEL METODO DEBERAN 
SEGUIR LOS SIGUIENTES PASOS
===================-->
<?php
/*=============================================
VALIDAMOS QUE VENGA LA VARIABLE GET QUE MANDAMOS
PREVIAMENTE EN EL BOTON 
=============================================*/
if (isset($_GET[])) {
	# code...
}
/*=============================================
CREAMOS EL ARCHIVO DE EXCEL
=============================================*/

$nombre = $_GET["reporte"].'.xls';

header('Expires: 0');
header('Cache-control: private');
header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
header("Cache-Control: cache, must-revalidate"); 
header('Content-Description: File Transfer');
header('Last-Modified: '.date('D, d M Y H:i:s'));
header("Pragma: public"); 
header('Content-Disposition:; filename="'.$nombre.'"');
header("Content-Transfer-Encoding: binary");
/*=============================================
CREAMOS LAS COLUMNAS QUE TENDRA NUESTRO ARCHIVO
=============================================*/

echo utf8_decode("

	<table border='0'> 

		<tr> 
						
			<td style='font-weight:bold; border:1px solid #eee;'>PRODUCTO</td>
			<td style='font-weight:bold; border:1px solid #eee;'>CLIENTE</td>
			<td style='font-weight:bold; border:1px solid #eee;'>VENTA</td>
			<td style='font-weight:bold; border:1px solid #eee;'>TIPO</td>
			<td style='font-weight:bold; border:1px solid #eee;'>PROCESO DE ENVÍO</td>
			<td style='font-weight:bold; border:1px solid #eee;'>MÉTODO</td>
			<td style='font-weight:bold; border:1px solid #eee;'>EMAIL</td>		
			<td style='font-weight:bold; border:1px solid #eee;'>DIRECCIÓN</td>		
			<td style='font-weight:bold; border:1px solid #eee;'>PAÍS</td	
			<td style='font-weight:bold; border:1px solid #eee;'>FECHA</td>		

</tr>");

/*=============================================
HACIENDO USO NUEVAMIENTE DE LA INSTANCIA
MANDAREMOS A LLAMAR EL METODO QUE NOS MUESTRA TODA LA 
INFORMACION DE LOS PRODUCTOS PARA DE ESTA MANERA PODER VOLCAR LOS DATOS
MEDIANTE EL CICLO FOREACH(){}
=============================================*/
$traerCliente = ControladorUsuarios::ctrMostrarUsuarios($item2, $valor2);

echo utf8_decode("

	<tr>
	<td style='border:1px solid #eee;'>".$traerProducto[0]["titulo"]."</td>
	<td style='border:1px solid #eee;'>".$traerCliente["nombre"]."</td>
	<td style='border:1px solid #eee;'>$ ".number_format($value["pago"],2)."</td>
	<td style='border:1px solid #eee;'>".$traerProducto[0]["tipo"]."</td>
	<td style='border:1px solid #eee;'></td>
	</tr>
	</table>
");


