<?php
class ControladorComisiones{
	
	public function ctrlMostrarComicion(){
		
		if (isset($_GET["idempleados"])){
				
			$tabla = "ordenes";
			$idEmpleado = $_GET["idempleados"];
			$fechaInicial = $_GET["fechaInicial"];
			$fechaFinal = $_GET["fechaFinal"];

			$reporte = ModeloReportes::mdlDescargarReporteComisiones($tabla,$idEmpleado,$fechaInicial,$fechaFinal);


			//var_dump($reporte);

			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/
			$nombre = $idEmpleado.'.xls';

			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate"); 
			header('Content-Description: File Transfer');
			header('Last-Modified: '.date('D, d M Y H:i:s'));
			header("Pragma: public"); 
			header('Content-Disposition:; filename="'.$nombre.'"');
			header("Content-Transfer-Encoding: binary");

			echo utf8_decode("

				<table border='0'> 

					<tr> 
						
						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Monto</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Inversión</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Comision</td>

					</tr>");

			foreach($reporte as $key => $value) {

				

				$GananciaNeta = $value["total"] - $value["totalInversion"];

				$comiciones = $_GET["PorcentajeDeComision"] / 100 * $GananciaNeta;

					
			}
				 echo utf8_decode("

				 	<tr>
							<td style='border:1px solid #eee;'>".$value["id"]."</td>
							<td style='border:1px solid #eee;'>$ ".number_format($value["total"],2)."</td>
							<td style='border:1px solid #eee;'>$ ".number_format($value["totalInversion"],2)."</td>
							<td style='border:1px solid #eee;'>".$value["id"]."</td>

					 </tr>

				</table>");

			


		}
		
	}
	/*===========================================
	GENERAR EL REPORTE DE COMISIONES DE TECNICO
	============================================*/
	public function ctrlMostrarComicionTecnico(){
		
		if (isset($_GET["reporte"])) {
				
			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"]) && isset($_GET["tecnico"])) {
				
				$tabla="ordenes";
				$OrdenesFechaTecnico =ModeloOrdenes::mdlRangoFechasOrdenesENTTecnico($tabla,$_GET["tecnico"],$_GET["fechaInicial"], $_GET["fechaFinal"]);
			

			}else{

				$tabla="ordenes";

				$estado = "Entregado (Ent)";

				$OrdenesFechaTecnico = ModeloOrdenes::mdlRangoFechasOrdenesENTTecnico($tabla, $estado, $_GET["tecnico"]);
			}
			
			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = $_GET["reporte"].'.xls';

			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate"); 
			header('Content-Description: File Transfer');
			header('Last-Modified: '.date('D, d M Y H:i:s'));
			header("Pragma: public"); 
			header('Content-Disposition:; filename="'.$Name.'"');
			header("Content-Transfer-Encoding: binary");

			echo utf8_decode("<table border='0'> 

					<tr> 
						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Tecnico</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Ingreso Bruto</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Inversión</td>
						<td style='font-weight:bold; border:1px solid #eee;'>comisión</td>
						<td style='font-weight:bold; border:1px solid #eee;'>fecha salida</td>
					</tr>");
			foreach ($OrdenesFechaTecnico as $key => $valueOrdenesTec) {
					
				$item = "id";
              	$valor = $valueOrdenesTec["id_empresa"];

             	 $NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

              	$NombreEmpresa = $NameEmpresa["empresa"];

              	//CALCULO DEL TOTAL DESCONTANDO INVERCION
              	if ($valueOrdenesTec["totalInversion"] == 0){


              		$invercion = 0;
              		$totalNetoOrden =  $valueOrdenesTec["total"];
              		$utilidadAntesIva = $valueOrdenesTec["total"] / 1.16;

              	}else{

              		$invercion = $valueOrdenesTec["totalInversion"];
              		$totalNetoOrden =  $valueOrdenesTec["total"] - $invercion;
      				$utilidadAntesIva =  $valueOrdenesTec["total"] / 1.16;

              	}
      			

      			//TRAER TECNICO
      			$item = "id";
      			$valor = $valueOrdenesTec["id_tecnico"];

      			$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);

      			$NombreTecnico = $tecnico["nombre"];
      			$departamento = $tecnico["departamento"];

      			if ($departamento == "sistemas"){
      				
      				$comision= $totalNetoOrden / 1.16 * 0.04;
 
      			}elseif($departamento == "electronica") {

      				$comision= $totalNetoOrden / 1.16 * 0.2;

      			}elseif ($departamento == "impresoras") {

      				if ($valueOrdenesTec["recarga-de-cartucho"] != "") {
      					
      					$comision= $totalNetoOrden / 1.16 * 0.1;

      				}else if ($valueOrdenesTec["recargaCartucho"] != "") {
      					
      					$comisionRestoPartidas = ($totalNetoOrden - $valueOrdenesTec["totalRecargaDeCartucho"]) / 1.16 * 0.2;
      					var_dump($totalNetoOrden, "-" , $valueOrdenesTec["totalRecargaDeCartucho"] , "/", "1.16","*","0.2", $comisionRestoPartidas);
      					$comisionRecarga = $valueOrdenesTec["totalRecargaDeCartucho"] / 1.16 * 0.1;

      					$comision = $comisionRestoPartidas + $comisionRecarga;

      				}else{

      					$comision= $totalNetoOrden / 1.16 * 0.2;

      				}
      				
      			}

      			/*=============================================
				TRAER EMAIL DATOS DE COMPRA
				=============================================*/
				$sumaComisiones += $comision;
				$sumaGanancia += $utilidadAntesIva;
				$sumaCobrado += $valueOrdenesTec["total"];
				$sumainv += $invercion;
					echo utf8_decode("<tr>
									 <td style='border:1px solid #eee;'>".$valueOrdenesTec["id"]."</td>
									 <td style='border:1px solid #eee;'>".$NombreTecnico."</td>
			 					  	 <td style='border:1px solid #eee;'>".$valueOrdenesTec["estado"]."</td>
			 					  	 <td style='border:1px solid #eee;'>$ ".$valueOrdenesTec["total"]."</td>
			 					  	 <td style='border:1px solid #eee;'>$ ".$invercion."</td>
			 					  	 <td style='border:1px solid #eee;'>$ ".$comision."</td>
			 					  	 <td style='border:1px solid #eee;'>".$valueOrdenesTec["fecha_Salida"]."</td>
			 					  	 </tr>"); 		

			}

			echo utf8_decode("<tr>
				<td style='font-weight:bold; border:1px solid #eee;'>Total Ingreso Bruto</td>
				<td style='font-weight:bold; border:1px solid #eee;'>Total Inversión</td>
				<td style='font-weight:bold; border:1px solid #eee;'>total comision</td>
				</tr>
				
				<tr>
				<td style='border:1px solid #eee;'>$ ".$sumaCobrado."</td>
				<td style='border:1px solid #eee;'>$ ".$sumainv."</td>
				<td style='border:1px solid #eee;'>$ ".$sumaComisiones."</td>
				</tr>");

			echo utf8_decode("</table>

					");
		
		}
		
	}	

	/*===========================================
	GENERAR EL REPORTE DE COMISIONES DE ASESOR
	============================================*/
	public function ctrlMostrarComicionAsesor(){
		
		if (isset($_GET["reporte"])) {
				
			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"]) && isset($_GET["asesor"])) {
				
				$tabla="ordenes";
				$OrdenesFechaAsesor =ModeloOrdenes::mdlRangoFechasOrdenesENTasesor($tabla,$_GET["asesor"],$_GET["fechaInicial"], $_GET["fechaFinal"]);
			

			}else{

				$tabla="ordenes";

				$estado = "Entregado (Ent)";

				$OrdenesFechaAsesor = ModeloOrdenes::mdlRangoFechasOrdenesENTasesor($tabla, $estado, $_GET["tecnico"]);
			}
			
			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = $_GET["reporte"].'.xls';

			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate"); 
			header('Content-Description: File Transfer');
			header('Last-Modified: '.date('D, d M Y H:i:s'));
			header("Pragma: public"); 
			header('Content-Disposition:; filename="'.$Name.'"');
			header("Content-Transfer-Encoding: binary");

			echo utf8_decode("<table border='0'> 

					<tr> 
						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Ingreso Bruto</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Inversión</td>
						<td style='font-weight:bold; border:1px solid #eee;'>comisión</td>
						<td style='font-weight:bold; border:1px solid #eee;'>fecha salida</td>
					</tr>");
			foreach ($OrdenesFechaAsesor as $key => $valueOrdenesAsesor) {
					
				$item = "id";
              	$valor = $valueOrdenesAsesor["id_empresa"];

             	 $NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

              	$NombreEmpresa = $NameEmpresa["empresa"];

              	//NOMBRE ASESOR
      			$itemA = "id";
      			$valorA = $valueOrdenesAsesor["id_Asesor"];

      			$Asesor = ctrMostrarAsesores::ctrMostrarAsesoresEleg($itemA,$valorA);
      			$nombreAsesor = $Asesor["nombre"];
      			$NombreTecnico = $tecnico["nombre"];
              	//CALCULO DEL TOTAL DESCONTANDO INVERCION
              	if ($valueOrdenesAsesor["totalInversion"] == 0){


              		$invercion = 0;
              		$totalNetoOrden =  $valueOrdenesAsesor["total"];
              		$utilidadAntesIva = $valueOrdenesAsesor["total"] / 1.16;

              	}else{

              		$invercion = $valueOrdenesAsesor["totalInversion"];
              		$totalNetoOrden =  $valueOrdenesAsesor["total"] - $invercion;
      				$utilidadAntesIva =  $valueOrdenesAsesor["total"] / 1.16;

              	}
      	

      			$comision= $totalNetoOrden / 1.16 * 0.3;

      			/*=============================================
				TRAER EMAIL DATOS DE COMPRA
				=============================================*/
				$sumaComisiones += $comision;
				$sumaGanancia += $utilidadAntesIva;
				$sumaCobrado += $valueOrdenesAsesor["total"];
				$sumainv += $invercion;
					echo utf8_decode("<tr>
									 <td style='border:1px solid #eee;'>".$valueOrdenesAsesor["id"]."</td>
									 <td style='border:1px solid #eee;'>".$nombreAsesor."</td>
			 					  	 <td style='border:1px solid #eee;'>".$valueOrdenesAsesor["estado"]."</td>
			 					  	 <td style='border:1px solid #eee;'>$ ".$valueOrdenesAsesor["total"]."</td>
			 					  	 <td style='border:1px solid #eee;'>$ ".$invercion."</td>
			 					  	 <td style='border:1px solid #eee;'>$ ".$comision."</td>
			 					  	 <td style='border:1px solid #eee;'>".$valueOrdenesAsesor["fecha_Salida"]."</td>
			 					  	 </tr>"); 		

			}

			echo utf8_decode("<tr>
				<td style='font-weight:bold; border:1px solid #eee;'>Total Ingreso Bruto</td>
				<td style='font-weight:bold; border:1px solid #eee;'>Total Inversión</td>
				<td style='font-weight:bold; border:1px solid #eee;'>total comision</td>
				</tr>
				
				<tr>
				<td style='border:1px solid #eee;'>$ ".$sumaCobrado."</td>
				<td style='border:1px solid #eee;'>$ ".$sumainv."</td>
				<td style='border:1px solid #eee;'>$ ".$sumaComisiones."</td>
				</tr>");

			echo utf8_decode("</table>

					");
		
		}
		
	}	
}