<?php

require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";
require_once "../controladores/controlador.asesore.php";
require_once "../modelos/modelo.asesores.php";
require_once "../config/clienteBadges.helper.php";

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
		$ultimoClienteId = !empty($clientes) ? intval($clientes[0]["id"]) : 0;

		// Una sola query para contar órdenes de TODOS los clientes
		try {
			$ordenesMap = ControladorClientes::ctrContarOrdenesClientesBulk();
		} catch(Exception $e) {
			$ordenesMap = [];
		}

		// Una sola query para contar entregadas/canceladas de TODOS los clientes
		try {
			$estadoMap = ControladorClientes::ctrContarOrdenesEstadoBulk();
		} catch(Exception $e) {
			$estadoMap = [];
		}

		// Una sola query para promedio de días de recogida
		try {
			$recogidaMap = ControladorClientes::ctrPromedioRecogidaBulk();
		} catch(Exception $e) {
			$recogidaMap = [];
		}

		// Cache de asesores para evitar queries repetidas
		$asesoresCache = [];

		$data = [];

		for($i = 0; $i < count($clientes); $i++){

			$idAsesor = $clientes[$i]["id_Asesor"];
			if(!isset($asesoresCache[$idAsesor])){
				$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $idAsesor);
				$asesoresCache[$idAsesor] = isset($asesor["nombre"]) ? $asesor["nombre"] : "Sin asignar";
			}
			$nombre_asesor = $asesoresCache[$idAsesor];

			$idCliente = intval($clientes[$i]["id"]);
			$totalOrdenes = isset($ordenesMap[$idCliente]) ? $ordenesMap[$idCliente] : 0;

			$tel    = trim($clientes[$i]["telefono"]);
			$wa     = trim($clientes[$i]["telefonoDos"]);
			$correo = trim($clientes[$i]["correo"]);

			// Validar formato de teléfono (exactamente 10 dígitos numéricos)
			$telClean = preg_replace('/\D/', '', $tel);
			$waClean  = preg_replace('/\D/', '', $wa);
			$telValido  = (strlen($telClean) === 10);
			$waValido   = (strlen($waClean) === 10);

			$hayTel   = ($tel && $tel !== "sin Telefono");
			$hayWa    = ($wa  && $wa  !== "sin whatsapp");
			$hayEmail = ($correo && $correo !== "");

			// Validar formato de correo
			$emailValido = ($hayEmail && filter_var($correo, FILTER_VALIDATE_EMAIL) !== false);

			/* ══════════════════════════════════════════
			   COLUMNA CONTACTO
			   Teléfono + WhatsApp + Correo en píldoras
			══════════════════════════════════════════ */
			$contacto = "<div style='display:flex;flex-direction:column;gap:5px;min-width:170px;'>";

			if($hayTel){
				if($telValido){
					$contacto .= "<span style='display:inline-flex;align-items:center;gap:6px;padding:3px 10px;"
					           . "background:#eaf4fb;border-radius:20px;font-size:12px;color:#1a5276;white-space:nowrap;'>"
					           . "<i class='fas fa-phone-alt' style='color:#2980b9;'></i>"
					           . "<strong>Tel:</strong>&nbsp;" . htmlspecialchars($tel) . "</span>";
				} else {
					$contacto .= "<span style='display:inline-flex;align-items:center;gap:6px;padding:3px 10px;"
					           . "background:#fef2f2;border-radius:20px;font-size:12px;color:#991b1b;white-space:nowrap;'>"
					           . "<i class='fas fa-phone-alt' style='color:#dc2626;'></i>"
					           . "<strong>Tel:</strong>&nbsp;" . htmlspecialchars($tel)
					           . "&nbsp;<i class='fas fa-exclamation-triangle' style='color:#dc2626;font-size:10px;' title='Formato inválido'></i></span>";
				}
			}

			if($hayWa){
				if($waValido){
					$contacto .= "<a href='https://api.whatsapp.com/send/?phone=521" . $waClean . "' target='_blank' style='text-decoration:none;'>"
					           . "<span style='display:inline-flex;align-items:center;gap:6px;padding:3px 10px;"
					           . "background:#e9f7ef;border-radius:20px;font-size:12px;color:#196f3d;white-space:nowrap;'>"
					           . "<i class='fab fa-whatsapp' style='color:#25d366;'></i>"
					           . "<strong>WA:</strong>&nbsp;" . htmlspecialchars($wa) . "</span></a>";
				} else {
					$contacto .= "<span style='display:inline-flex;align-items:center;gap:6px;padding:3px 10px;"
					           . "background:#fef2f2;border-radius:20px;font-size:12px;color:#991b1b;white-space:nowrap;'>"
					           . "<i class='fab fa-whatsapp' style='color:#dc2626;'></i>"
					           . "<strong>WA:</strong>&nbsp;" . htmlspecialchars($wa)
					           . "&nbsp;<i class='fas fa-exclamation-triangle' style='color:#dc2626;font-size:10px;' title='Formato inválido'></i></span>";
				}
			}

			if($hayEmail){
				if($emailValido){
					$contacto .= "<a href='mailto:" . htmlspecialchars($correo) . "' style='text-decoration:none;'>"
					           . "<span style='display:inline-flex;align-items:center;gap:6px;padding:3px 10px;"
					           . "background:#fef9e7;border-radius:20px;font-size:12px;color:#784212;"
					           . "max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>"
					           . "<i class='fas fa-envelope' style='color:#e67e22;'></i>&nbsp;" . htmlspecialchars($correo) . "</span></a>";
				} else {
					$contacto .= "<span style='display:inline-flex;align-items:center;gap:6px;padding:3px 10px;"
					           . "background:#fef2f2;border-radius:20px;font-size:12px;color:#991b1b;"
					           . "max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>"
					           . "<i class='fas fa-envelope' style='color:#dc2626;'></i>&nbsp;" . htmlspecialchars($correo)
					           . "&nbsp;<i class='fas fa-exclamation-triangle' style='color:#dc2626;font-size:10px;' title='Correo inválido'></i></span>";
				}
			}

			if(!$hayTel && !$hayWa && !$hayEmail){
				$contacto .= "<span style='color:#aaa;font-size:12px;font-style:italic;'>Sin datos de contacto</span>";
			}

			$contacto .= "</div>";

			/* ══════════════════════════════════════════
			   COLUMNA CLASIFICACIÓN
			   Órdenes + Calificación + Antigüedad
			══════════════════════════════════════════ */
			$fecha    = $clientes[$i]["fecha"];

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
			$clasificacion .= "<span style='display:inline-flex;align-items:center;gap:4px;padding:2px 9px;"
			                . "border-radius:20px;font-size:11px;background:{$ordBg};color:{$ordColor};'>"
			                . "<i class='fas fa-clipboard-list'></i>&nbsp;{$totalOrdenes}&nbsp;{$ordLabel}</span>";

			// ── Calificación del cliente (entregadas vs canceladas, mínimo 3 órdenes) ──
			$cliEntregadas = isset($estadoMap[$idCliente]) ? $estadoMap[$idCliente]["entregadas"] : 0;
			$cliCanceladas = isset($estadoMap[$idCliente]) ? $estadoMap[$idCliente]["canceladas"] : 0;
			$califLabel = "Sin calificar"; $califColor = "#64748b"; $califBg = "#f1f5f9"; $califIcon = "fa-circle-question";
			$califPct = "";
			$califRatio = -1; // para sorting: -1 = sin datos suficientes
			if($totalOrdenes >= 3 && ($cliEntregadas + $cliCanceladas) > 0){
				$ratio = $cliEntregadas / ($cliEntregadas + $cliCanceladas) * 100;
				$califRatio = round($ratio, 2);
				$califPct = round($ratio) . "%";
				if($ratio >= 90)      { $califLabel = "Excelente"; $califColor = "#16a34a"; $califBg = "#f0fdf4"; $califIcon = "fa-star"; }
				elseif($ratio >= 70)  { $califLabel = "Bueno";     $califColor = "#2563eb"; $califBg = "#eff6ff"; $califIcon = "fa-thumbs-up"; }
				elseif($ratio >= 50)  { $califLabel = "Regular";   $califColor = "#d97706"; $califBg = "#fffbeb"; $califIcon = "fa-minus-circle"; }
				else                  { $califLabel = "Malo";      $califColor = "#dc2626"; $califBg = "#fef2f2"; $califIcon = "fa-thumbs-down"; }
			}
			$clasificacion .= "<span style='display:inline-flex;align-items:center;gap:4px;padding:2px 9px;"
			                . "border-radius:20px;font-size:11px;background:{$califBg};color:{$califColor};font-weight:600;'>"
			                . "<i class='fas {$califIcon}'></i>&nbsp;{$califLabel}";
			if($califPct) $clasificacion .= "&nbsp;<span style='opacity:.7;font-size:10px;'>({$califPct})</span>";
			$clasificacion .= "</span>";

			// ── Tiempo promedio de recogida (solo clientes con 3+ entregadas) ──
			$recogidaBadge = "";
			if(isset($recogidaMap[$idCliente])){
				$promDias = $recogidaMap[$idCliente];
				if($promDias <= 7)       { $recBg = "#f0fdf4"; $recColor = "#16a34a"; $recIcon = "fa-bolt"; }
				elseif($promDias <= 14)   { $recBg = "#eff6ff"; $recColor = "#2563eb"; $recIcon = "fa-clock"; }
				elseif($promDias <= 30)  { $recBg = "#fffbeb"; $recColor = "#d97706"; $recIcon = "fa-hourglass-half"; }
				else                     { $recBg = "#fef2f2"; $recColor = "#dc2626"; $recIcon = "fa-hourglass-end"; }
				$diasTxt = ($promDias == 1) ? "día" : "días";
				$recogidaBadge = "<span style='display:inline-flex;align-items:center;gap:4px;padding:2px 9px;"
				               . "border-radius:20px;font-size:11px;background:{$recBg};color:{$recColor};font-weight:600;'>"
				               . "<i class='fas {$recIcon}'></i>&nbsp;Recoge:&nbsp;~{$promDias}&nbsp;{$diasTxt}</span>";
			}
			if($recogidaBadge) $clasificacion .= $recogidaBadge;

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

			if($hayWa && $waValido){
				$acciones .= "<a href='https://api.whatsapp.com/send/?phone=521" . $waClean . "' target='_blank'>"
				           . "<button class='btn btn-success btn-sm' style='{$btnW}'>"
				           . "<i class='fab fa-whatsapp'></i>&nbsp;WhatsApp</button></a>";
			}

			$acciones .= "</div>";

			/* ══════════════════════════════════════════
			   FECHA
			══════════════════════════════════════════ */
			$fechaDisplay = ($fecha) ? date("d/m/Y", strtotime($fecha)) : "—";

			$bh = ClienteBadgesHelper::getInstance();

			$nombreClienteBadges = $bh->render($idCliente);
			$nombreClienteHtml = "<div style='display:flex;flex-direction:column;gap:6px;max-width:260px;'>"
			                  . "<span style='display:block;font-weight:700;color:#1a252f;white-space:normal;word-break:break-word;line-height:1.32;'>"
			                  . htmlspecialchars($clientes[$i]["nombre"])
			                  . "</span>";
			if(!empty($nombreClienteBadges)){
				$nombreClienteHtml .= "<div style='display:flex;align-items:center;flex-wrap:wrap;margin-left:-4px;'>"
				                   . $nombreClienteBadges
				                   . "</div>";
			}
			$nombreClienteHtml .= "</div>";
			if($idCliente === $ultimoClienteId){
				$nombreClienteHtml = "<div class='cli-ultimo-registro' style='display:flex;align-items:flex-start;gap:8px;padding:8px 10px;border-radius:12px;background:linear-gradient(135deg,#ecfeff,#f0fdf4);border:1px solid #a5f3fc;box-shadow:0 8px 18px rgba(34,211,238,.12);'>"
				                  . "<span style='display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:999px;background:#0f172a;color:#fff;font-size:10px;font-weight:800;letter-spacing:.04em;text-transform:uppercase;white-space:nowrap;'>"
				                  . "<i class='fas fa-sparkles' style='font-size:10px;color:#facc15;'></i> Último registro</span>"
				                  . "<div style='min-width:0;flex:1;'>".$nombreClienteHtml."</div>"
				                  . "</div>";
			}

			$data[] = [
				$idCliente,
				$nombreClienteHtml,
				$nombre_asesor,
				$contacto,
				$clasificacion,
				$acciones,
				$fechaDisplay,
				$totalOrdenes,
				$fecha ? $fecha : "2000-01-01",
				$califRatio
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
