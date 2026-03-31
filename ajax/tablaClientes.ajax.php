<?php

require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";
require_once "../controladores/controlador.asesore.php";
require_once "../modelos/modelo.asesores.php";

class TablaClientes{

	/*=============================================
  	MOSTRAR LA TABLA DE CLIENTES
  	=============================================*/

	public function mostrarTablaClientes(){

		ob_start(); // capturar cualquier error/warning para que no rompa el JSON

		$empresaDelPerfil = $_GET["empresa"];
		$item  = "id_empresa";
		$valor = $empresaDelPerfil;
		$clientes = ControladorClientes::ctrMostrarClientesTabla($item, $valor);

		if(!is_array($clientes)) $clientes = [];

		$data = [];

		for($i = 0; $i < count($clientes); $i++){

			$item1  = "id";
			$valor1 = $clientes[$i]["id_Asesor"];
			$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item1, $valor1);
			$nombre_asesor = isset($asesor["nombre"]) ? $asesor["nombre"] : "Sin asignar";

			// Conteo de órdenes del cliente
			try {
				$totalOrdenes = ControladorClientes::ctrContarOrdenesCliente($clientes[$i]["id"]);
			} catch(Exception $e) {
				$totalOrdenes = 0;
			}

			$tel    = trim($clientes[$i]["telefono"]);
			$wa     = trim($clientes[$i]["telefonoDos"]);
			$correo = trim($clientes[$i]["correo"]);

			$hayTel   = ($tel    && $tel    !== "sin Telefono");
			$hayWa    = ($wa     && $wa     !== "sin whatsapp");
			$hayEmail = ($correo && $correo !== "");

			/* ══════════════════════════════════════════
			   COLUMNA CONTACTO
			   Teléfono + WhatsApp + Correo en píldoras
			══════════════════════════════════════════ */
			$contacto = "<div style='display:flex;flex-direction:column;gap:5px;min-width:170px;'>";

			if($hayTel){
				$contacto .= "<span style='display:inline-flex;align-items:center;gap:6px;padding:3px 10px;"
				           . "background:#eaf4fb;border-radius:20px;font-size:12px;color:#1a5276;white-space:nowrap;'>"
				           . "<i class='fas fa-phone-alt' style='color:#2980b9;'></i>"
				           . "<strong>Tel:</strong>&nbsp;" . $tel . "</span>";
			}

			if($hayWa){
				$contacto .= "<a href='https://api.whatsapp.com/send/?phone=521" . $wa . "' target='_blank' style='text-decoration:none;'>"
				           . "<span style='display:inline-flex;align-items:center;gap:6px;padding:3px 10px;"
				           . "background:#e9f7ef;border-radius:20px;font-size:12px;color:#196f3d;white-space:nowrap;'>"
				           . "<i class='fab fa-whatsapp' style='color:#25d366;'></i>"
				           . "<strong>WA:</strong>&nbsp;" . $wa . "</span></a>";
			}

			if($hayEmail){
				$contacto .= "<a href='mailto:" . $correo . "' style='text-decoration:none;'>"
				           . "<span style='display:inline-flex;align-items:center;gap:6px;padding:3px 10px;"
				           . "background:#fef9e7;border-radius:20px;font-size:12px;color:#784212;"
				           . "max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>"
				           . "<i class='fas fa-envelope' style='color:#e67e22;'></i>&nbsp;" . $correo . "</span></a>";
			}

			if(!$hayTel && !$hayWa && !$hayEmail){
				$contacto .= "<span style='color:#aaa;font-size:12px;font-style:italic;'>Sin datos de contacto</span>";
			}

			$contacto .= "</div>";

			/* ══════════════════════════════════════════
			   COLUMNA CLASIFICACIÓN
			   Etiqueta + Órdenes + Antigüedad
			══════════════════════════════════════════ */
			$etiqueta = isset($clientes[$i]["etiqueta"]) ? $clientes[$i]["etiqueta"] : "Nuevo";
			$fecha    = $clientes[$i]["fecha"];

			// Colores de etiqueta
			$etiqColor = "#1a5276"; $etiqBg = "#d6eaf8"; // Nuevo
			if($etiqueta === "Frecuente")    { $etiqColor = "#1e8449"; $etiqBg = "#d5f5e3"; }
			if($etiqueta === "Problematico") { $etiqColor = "#c0392b"; $etiqBg = "#fadbd8"; }

			// Asegurar que sea entero
			$totalOrdenes = intval($totalOrdenes);

			// Colores según cantidad de órdenes
			if($totalOrdenes == 0)       { $ordBg = "#e8e8e8"; $ordColor = "#777"; }
			elseif($totalOrdenes <= 2)   { $ordBg = "#d6eaf8"; $ordColor = "#1a5276"; }
			elseif($totalOrdenes <= 5)   { $ordBg = "#d5f5e3"; $ordColor = "#1e8449"; }
			else                         { $ordBg = "#fdebd0"; $ordColor = "#784212"; }

			$ordLabel = $totalOrdenes == 1 ? "orden" : "órdenes";

			// Badge de antigüedad por fecha de registro
			$regBadge = "";
			if($fecha){
				try {
					$fechaReg = new DateTime($fecha);
					$ahora    = new DateTime();
					$dias     = $ahora->diff($fechaReg)->days;
					if($dias <= 30){
						$regBadge = "<span style='display:inline-block;padding:2px 8px;border-radius:20px;"
						          . "font-size:11px;background:#d5f5e3;color:#1e8449;font-weight:600;'>Reciente</span>";
					} elseif($dias > 365){
						$regBadge = "<span style='display:inline-block;padding:2px 8px;border-radius:20px;"
						          . "font-size:11px;background:#fdebd0;color:#784212;font-weight:600;'>Antiguo</span>";
					}
				} catch(Exception $e){}
			}

			$clasificacion  = "<div style='display:flex;flex-direction:column;gap:5px;align-items:flex-start;'>";
			$clasificacion .= "<span style='display:inline-block;padding:3px 10px;border-radius:20px;"
			                . "font-size:12px;background:{$etiqBg};color:{$etiqColor};font-weight:700;'>{$etiqueta}</span>";
			$clasificacion .= "<span style='display:inline-flex;align-items:center;gap:4px;padding:2px 9px;"
			                . "border-radius:20px;font-size:11px;background:{$ordBg};color:{$ordColor};'>"
			                . "<i class='fas fa-clipboard-list'></i>&nbsp;{$totalOrdenes}&nbsp;{$ordLabel}</span>";
			if($regBadge) $clasificacion .= $regBadge;
			$clasificacion .= "</div>";

			/* ══════════════════════════════════════════
			   COLUMNA ACCIONES
			   Botones correctamente separados
			══════════════════════════════════════════ */
			$btnW = "display:block;width:115px;text-align:center;border-radius:6px;";

			$acciones  = "<div style='display:flex;flex-direction:column;gap:5px;align-items:center;padding:4px 0;'>";
			$acciones .= "<button class='btn btn-warning btn-sm btnEditarCliente' "
			           . "idCliente='" . $clientes[$i]["id"] . "' "
			           . "data-toggle='modal' data-target='#btnEditarCliente' "
			           . "style='{$btnW}'><i class='fas fa-user-edit'></i>&nbsp;Editar</button>";

			$acciones .= "<a href='index.php?ruta=Historialdecliente&idCliente=" . $clientes[$i]["id"]
			           . "&nombreCliente=" . urlencode($clientes[$i]["nombre"]) . "' target='_blank'>"
			           . "<button class='btn btn-primary btn-sm' style='{$btnW}'>"
			           . "<i class='fas fa-clipboard-list'></i>&nbsp;Historial</button></a>";

			if($hayWa){
				$acciones .= "<a href='https://api.whatsapp.com/send/?phone=521" . $wa . "' target='_blank'>"
				           . "<button class='btn btn-success btn-sm' style='{$btnW}'>"
				           . "<i class='fab fa-whatsapp'></i>&nbsp;WhatsApp</button></a>";
			}

			$acciones .= "</div>";

			/* ══════════════════════════════════════════
			   FECHA
			══════════════════════════════════════════ */
			$fechaDisplay = ($fecha) ? date("d/m/Y", strtotime($fecha)) : "—";

			$data[] = [
				($i + 1),
				$clientes[$i]["nombre"],
				$nombre_asesor,
				$contacto,
				$clasificacion,
				$acciones,
				$fechaDisplay,
				$totalOrdenes,
				$fecha ? $fecha : "2000-01-01"
			];
		}

		ob_end_clean(); // descartar cualquier output previo (warnings, notices)
		header('Content-Type: application/json; charset=utf-8');
		$json = json_encode(["data" => $data], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

		if($json === false){
			// Fallback: limpiar strings no-UTF8 e intentar de nuevo
			array_walk_recursive($data, function(&$val){
				if(is_string($val)) $val = mb_convert_encoding($val, 'UTF-8', 'UTF-8');
			});
			$json = json_encode(["data" => $data], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
		}

		echo $json ? $json : '{"data":[]}';
	}

}

/*=============================================
ACTIVAR TABLA DE CLIENTES
=============================================*/
$activar = new TablaClientes();
$activar->mostrarTablaClientes();
