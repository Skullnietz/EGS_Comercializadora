<?php
if ($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"] != "vendedor" AND $_SESSION["perfil"] != "tecnico" AND $_SESSION["perfil"] != "secretaria") {
	echo '<script>
  window.location = "index.php?ruta=ordenes";
  </script>';
	return;
}

$isAdmin = ($_SESSION["perfil"] == "administrador");
$isTecnico = ($_SESSION["perfil"] == "tecnico");
$isVendedor = ($_SESSION["perfil"] == "vendedor");
$isSecretaria = ($_SESSION["perfil"] == "secretaria");
$isReadonly = ($isTecnico || $isVendedor || $isSecretaria);
?>
<style>
	.garantia { background: yellow !important; }
	.avatar { vertical-align: middle; width: 42px; height: 42px; border-radius: 50%; object-fit: cover; }

	/* CRM Design System */
	.egs-section { background: #fff; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.06); margin-bottom: 20px; overflow: hidden; }
	.egs-title-bar { background: linear-gradient(135deg,#6366f1 0%,#818cf8 100%); color: #fff; padding: 14px 20px; font-size: 15px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
	.egs-title-bar i { font-size: 16px; opacity: .85; }
	.egs-body { padding: 20px; }
	.egs-lbl { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 4px; display: block; }
	.egs-req::after { content: " *"; color: #ef4444; }
	.egs-field-row { margin-bottom: 12px; }
	.egs-partida-row { margin-bottom: 8px; padding: 10px 12px; background: #fafbfc; border-radius: 8px; border: 1px solid #f1f5f9; }
	.egs-total-bar { background: linear-gradient(135deg,#f0fdf4 0%,#dcfce7 100%); border: 2px solid #22c55e; border-radius: 10px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; margin: 16px 0; }
	.egs-total-bar .egs-total-label { font-size: 14px; font-weight: 700; color: #16a34a; text-transform: uppercase; }
	.egs-total-bar .input-group { max-width: 220px; }
	.egs-inv-bar { background: linear-gradient(135deg,#fefce8 0%,#fef9c3 100%); border: 2px solid #eab308; border-radius: 10px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; margin: 8px 0 16px; }
	.egs-inv-bar .egs-inv-label { font-size: 14px; font-weight: 700; color: #ca8a04; text-transform: uppercase; }
	.egs-inv-bar .input-group { max-width: 220px; }
	.egs-obs-item { padding: 12px 16px; background: #f8fafc; border-radius: 8px; margin-bottom: 10px; border-left: 3px solid #6366f1; }
	.egs-btn-accent { background: #6366f1; border-color: #6366f1; color: #fff; border-radius: 8px; font-weight: 600; }
	.egs-btn-accent:hover { background: #4f46e5; border-color: #4f46e5; color: #fff; }
	.egs-dollar { background: #6366f1; color: #fff; border-color: #6366f1; font-weight: 700; border-radius: 8px 0 0 8px !important; }
	.egs-body .input-group { border-radius: 8px; overflow: hidden; }
	.egs-body .input-group .form-control { border-radius: 0 8px 8px 0 !important; border-left: none; }
	.egs-body .input-group-addon { background: linear-gradient(135deg,#6366f1 0%,#818cf8 100%); color: #fff; border-color: #6366f1; min-width: 42px; border-radius: 8px 0 0 8px !important; }
	.egs-body .input-group-addon i { font-size: 14px; }
	.egs-body .input-group-addon .btn { background: transparent; border: none; color: #fff; padding: 0; }
	.egs-body .input-group-addon .btn-danger { color: #fca5a5; }
	.egs-body .input-group-addon .btn-danger:hover { color: #fff; }
	.egs-estado-badge { display: inline-block; padding: 5px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; border: 1px solid transparent; }

	/* Estados estandarizados */
	.egs-estado-revision       { color: #b91c1c; background: #fef2f2; border-color: #fca5a5; }
	.egs-estado-supervision    { color: #6d28d9; background: #f5f3ff; border-color: #ddd6fe; }
	.egs-estado-pendiente      { color: #92400e; background: #fffbeb; border-color: #fde68a; }
	.egs-estado-aceptado       { color: #1e40af; background: #eff6ff; border-color: #bfdbfe; }
	.egs-estado-terminada      { color: #0e7490; background: #ecfeff; border-color: #a5f3fc; }
	.egs-estado-cancelada      { color: #475569; background: #f1f5f9; border-color: #e2e8f0; }
	.egs-estado-sin-reparacion { color: #64748b; background: #f8fafc; border-color: #e2e8f0; }
	.egs-estado-entregado      { color: #166534; background: #f0fdf4; border-color: #bbf7d0; }
	.egs-estado-garantia       { color: #991b1b; background: #fef2f2; border-color: #fecaca; }
	.egs-estado-producto-venta { color: #c2410c; background: #fff7ed; border-color: #fed7aa; }
	.egs-estado-default        { color: #475569; background: #f1f5f9; border-color: #e2e8f0; }

	/* ═══════════════════════════════════════
	   ANALYTICS PANEL — Cliente stats
	   ═══════════════════════════════════════ */
	.egs-analytics-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; }
	@media(max-width:576px){ .egs-analytics-grid { grid-template-columns: 1fr; } }
	.egs-stat-card { background: #f8fafc; border-radius: 10px; padding: 16px; border: 1px solid #e2e8f0; text-align: center; }
	.egs-stat-card .egs-stat-value { font-size: 28px; font-weight: 800; line-height: 1.1; }
	.egs-stat-card .egs-stat-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .4px; margin-top: 4px; }
	.egs-stat-card .egs-stat-sub { font-size: 12px; color: #94a3b8; margin-top: 2px; }
	.egs-progress-bar { width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden; margin-top: 8px; }
	.egs-progress-fill { height: 100%; border-radius: 4px; transition: width .6s ease; }
	.egs-rec-box { background: #f8fafc; border-radius: 10px; padding: 14px 16px; border-left: 4px solid #6366f1; }
	.egs-rec-box .egs-rec-title { font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
	.egs-rec-box .egs-rec-text { font-size: 13px; color: #475569; line-height: 1.5; margin: 0 0 6px; }
	.egs-rec-box .egs-rec-text:last-child { margin-bottom: 0; }
	.egs-calc-summary { font-size: 12px; color: #94a3b8; margin-top: 12px; padding-top: 12px; border-top: 1px solid #e2e8f0; line-height: 1.6; }
	.egs-calc-summary i { margin-right: 4px; color: #cbd5e1; }

	/* ═══════════════════════════════════════
	   GALLERY — Modern Carousel + Lightbox
	   ═══════════════════════════════════════ */

	/* ── Main image ── */
	.egs-gallery { position: relative; border-radius: 12px; overflow: hidden; background: #0f172a; }
	.egs-gallery-main {
		position: relative; width: 100%; height: 420px;
		display: flex; align-items: center; justify-content: center;
		cursor: pointer; overflow: hidden;
	}
	.egs-gallery-main img {
		width: 100%; height: 100%; object-fit: contain;
		transition: transform .5s cubic-bezier(.4,0,.2,1), opacity .4s ease;
		user-select: none; -webkit-user-drag: none;
	}
	.egs-gallery-main:hover img { transform: scale(1.03); }

	/* ── Zoom hint overlay ── */
	.egs-gallery-main::after {
		content: '\f00e'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
		position: absolute; bottom: 14px; right: 14px;
		width: 38px; height: 38px; border-radius: 50%;
		background: rgba(255,255,255,.9); color: #334155;
		display: flex; align-items: center; justify-content: center;
		font-size: 14px; opacity: 0; transition: opacity .25s ease;
		pointer-events: none; box-shadow: 0 2px 8px rgba(0,0,0,.15);
	}
	.egs-gallery-main:hover::after { opacity: 1; }

	/* ── Counter badge ── */
	.egs-gallery-counter {
		position: absolute; top: 12px; left: 12px; z-index: 5;
		background: rgba(0,0,0,.55); backdrop-filter: blur(6px);
		color: #fff; font-size: 11px; font-weight: 600;
		padding: 4px 10px; border-radius: 20px;
		letter-spacing: .03em;
	}

	/* ── Navigation arrows ── */
	.egs-gallery-nav {
		position: absolute; top: 50%; transform: translateY(-50%); z-index: 5;
		width: 40px; height: 40px; border-radius: 50%;
		background: rgba(255,255,255,.85); backdrop-filter: blur(4px);
		border: none; color: #334155; font-size: 15px;
		display: flex; align-items: center; justify-content: center;
		cursor: pointer; opacity: 0; transition: all .25s ease;
		box-shadow: 0 2px 10px rgba(0,0,0,.15);
	}
	.egs-gallery:hover .egs-gallery-nav { opacity: 1; }
	.egs-gallery-nav:hover { background: #fff; color: #0f172a; transform: translateY(-50%) scale(1.1); }
	.egs-gallery-nav.prev { left: 12px; }
	.egs-gallery-nav.next { right: 12px; }

	/* ── Thumbnails strip ── */
	.egs-gallery-thumbs {
		display: flex; gap: 6px; padding: 10px 12px;
		background: rgba(15,23,42,.6);
		overflow-x: auto; scroll-behavior: smooth;
		-ms-overflow-style: none; scrollbar-width: none;
	}
	.egs-gallery-thumbs::-webkit-scrollbar { display: none; }
	.egs-gallery-thumb {
		flex-shrink: 0; width: 62px; height: 48px;
		border-radius: 8px; overflow: hidden; cursor: pointer;
		border: 2px solid transparent; opacity: .55;
		transition: all .25s ease; position: relative;
	}
	.egs-gallery-thumb.active,
	.egs-gallery-thumb:hover { opacity: 1; border-color: #6366f1; }
	.egs-gallery-thumb img {
		width: 100%; height: 100%; object-fit: cover;
		pointer-events: none;
	}

	/* ── No-image placeholder ── */
	.egs-gallery-empty {
		width: 100%; height: 300px; display: flex; flex-direction: column;
		align-items: center; justify-content: center; gap: 12px;
		background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
		border-radius: 12px; color: #94a3b8;
	}
	.egs-gallery-empty i { font-size: 48px; opacity: .5; }
	.egs-gallery-empty span { font-size: 13px; font-weight: 500; }

	/* ═══════════════════════════════════════
	   LIGHTBOX — Fullscreen modal
	   ═══════════════════════════════════════ */
	#egsLightbox .modal-dialog { width: 96vw; max-width: 1200px; margin: 2vh auto; }
	#egsLightbox .modal-content {
		background: #0f172a; border: none; border-radius: 16px;
		overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,.6);
	}
	#egsLightbox .modal-body {
		position: relative; padding: 0; overflow: hidden;
		display: flex; align-items: center; justify-content: center;
		min-height: 70vh; max-height: 85vh; background: #0f172a;
	}
	#egsLightbox .lb-image {
		max-width: 100%; max-height: 82vh; object-fit: contain;
		transform-origin: center center; cursor: grab;
		transition: transform .3s cubic-bezier(.4,0,.2,1);
		user-select: none; -webkit-user-drag: none;
	}
	#egsLightbox .lb-image:active { cursor: grabbing; }

	/* ── Lightbox toolbar ── */
	.lb-toolbar {
		position: absolute; bottom: 0; left: 0; right: 0; z-index: 10;
		display: flex; align-items: center; justify-content: center; gap: 4px;
		padding: 12px 16px;
		background: linear-gradient(to top, rgba(0,0,0,.7) 0%, transparent 100%);
	}
	.lb-toolbar button {
		width: 40px; height: 40px; border-radius: 50%;
		background: rgba(255,255,255,.12); border: none;
		color: #e2e8f0; font-size: 15px; cursor: pointer;
		display: flex; align-items: center; justify-content: center;
		transition: all .2s ease; backdrop-filter: blur(4px);
	}
	.lb-toolbar button:hover { background: rgba(255,255,255,.25); color: #fff; transform: scale(1.1); }
	.lb-toolbar .lb-separator { width: 1px; height: 24px; background: rgba(255,255,255,.15); margin: 0 8px; }
	.lb-counter { color: rgba(255,255,255,.7); font-size: 12px; font-weight: 600; margin: 0 12px; }

	/* ── Lightbox close ── */
	.lb-close {
		position: absolute; top: 14px; right: 14px; z-index: 15;
		width: 42px; height: 42px; border-radius: 50%;
		background: rgba(0,0,0,.4); backdrop-filter: blur(6px);
		border: 1px solid rgba(255,255,255,.1);
		color: #e2e8f0; font-size: 18px; cursor: pointer;
		display: flex; align-items: center; justify-content: center;
		transition: all .2s ease;
	}
	.lb-close:hover { background: rgba(239,68,68,.8); color: #fff; transform: scale(1.1); }

	/* ── Lightbox nav arrows ── */
	.lb-nav {
		position: absolute; top: 50%; transform: translateY(-50%); z-index: 10;
		width: 48px; height: 48px; border-radius: 50%;
		background: rgba(255,255,255,.1); backdrop-filter: blur(4px);
		border: 1px solid rgba(255,255,255,.08);
		color: #e2e8f0; font-size: 18px; cursor: pointer;
		display: flex; align-items: center; justify-content: center;
		transition: all .25s ease; opacity: 0;
	}
	#egsLightbox .modal-body:hover .lb-nav { opacity: 1; }
	.lb-nav:hover { background: rgba(255,255,255,.25); color: #fff; transform: translateY(-50%) scale(1.1); }
	.lb-nav.prev { left: 16px; }
	.lb-nav.next { right: 16px; }

	@media (max-width: 768px) {
		.egs-gallery-main { height: 280px; }
		.egs-gallery-thumb { width: 50px; height: 38px; }
		#egsLightbox .modal-dialog { width: 100vw; margin: 0; }
		#egsLightbox .modal-content { border-radius: 0; min-height: 100vh; }
		.lb-nav { width: 38px; height: 38px; }
	}
</style>

<?php
if ($isTecnico) {
	echo '<script>
$(document).ready(function(){
    $("#btncompletarorden").hide();
    $("#marca").keyup(function() { $("#marca").val($(this).val().toUpperCase()); });
    $("#modelo").keyup(function() { $("#modelo").val($(this).val().toUpperCase()); });
    $("#numeroserial").keyup(function() { $("#numeroserial").val($(this).val().toUpperCase()); });
    $("#spanboton").html("Complete su ficha tecnica");
    $("#marca,#modelo,#numeroserial").keyup(function() {
        if ($("#marca").val().length >= 2){
        $("#spanmarca").html(""); $("#spanboton").html("");
        if ($("#modelo").val().length >= 4){
        $("#spanmodelo").html(""); $("#spanboton").html("");
        if ($("#numeroserial").val().length == 6){
        $("#spannumeroserie").html(""); $("#spanboton").html("");
        $("#btncompletarorden").show();
        }else{ $("#btncompletarorden").hide(); $("#spannumeroserie").html("Debe contener los ultimos <b>6</b> digitos"); $("#spanboton").html("Complete el campo de numero de serie"); }
        }else{ $("#btncompletarorden").hide(); $("#spanboton").html("Complete el campo de modelo"); $("#spanmodelo").html("Debe contener al menos <b>4</b> digitos"); }
        }else{ $("#btncompletarorden").hide(); $("#spanboton").html("Complete el campo de marca"); $("#spanmarca").html("Debe contener al menos <b>2</b> digitos"); }
    });
    $(document).ready(function() {
        if ($("#marca2").val().length >= 2){
        $("#spanboton").html("");
        if ($("#modelo2").val().length >= 4){
        $("#spanboton").html("");
        if ($("#numeroserial2").val().length >= 6){
        $("#spanboton").html("");
        $("#btncompletarorden").show();
        }else{$("#btncompletarorden").hide();}
        }else{$("#btncompletarorden").hide();}
        }else{$("#btncompletarorden").hide();}
    });
});
</script>';
}
?>

<!-- Gallery JS se inyecta después del HTML de la galería -->

<?php
// ==================== DATOS PRINCIPALES ====================
$item = "id";
$valor = $_GET["idOrden"];
$ordenes = controladorOrdenes::ctrMostrarordenesParaValidar($item, $valor);

// Fallback: si faltan parámetros GET, obtenerlos de la orden misma
if (is_array($ordenes) && !empty($ordenes)) {
	$_ord0 = $ordenes[0];
	if (!isset($_GET["cliente"]))  $_GET["cliente"]  = isset($_ord0["id_usuario"])   ? $_ord0["id_usuario"]   : 0;
	if (!isset($_GET["empresa"]))  $_GET["empresa"]  = isset($_ord0["id_empresa"])   ? $_ord0["id_empresa"]   : 0;
	if (!isset($_GET["asesor"]))   $_GET["asesor"]   = isset($_ord0["id_Asesor"])    ? $_ord0["id_Asesor"]    : 0;
	if (!isset($_GET["tecnico"]))  $_GET["tecnico"]  = isset($_ord0["id_tecnico"])   ? $_ord0["id_tecnico"]   : 0;
	if (!isset($_GET["pedido"]))   $_GET["pedido"]   = isset($_ord0["id_pedido"])    ? $_ord0["id_pedido"]    : 0;
	if (!isset($_GET["tecnicodos"])) $_GET["tecnicodos"] = isset($_ord0["id_tecnicoDos"]) ? $_ord0["id_tecnicoDos"] : 0;
}

// Cliente
$usuario = ControladorClientes::ctrMostrarClientesOrdenes("id", $_GET["cliente"]);

// Datos de la orden
foreach ($ordenes as $key => $value) {
	$portada = $value["portada"];
	$AlbumDeImagenes = json_decode($value["multimedia"], true);
	$estado = $value["estado"];
	$partidas = json_decode($value["partidas"], true);
	$observaciones = json_decode($value["observaciones"], true);
	$inversiones = json_decode($value["inversiones"], true);
	$descripcion = $value["descripcion"];
	$fecha_Salida = $value["fecha_Salida"];
	$fecha_ingreso = $value["fecha_ingreso"];
	$recarga = $value["recargaCartucho"];
	$precioRecarga = $value["totalRecargaDeCartucho"];
	$partidasTecnicoDos = json_decode($value["partidasTecnicoDos"], true);
	$totalTecnicosDos = $value["TotalTecnicoDos"];
	$tecnicoDos = $value["id_tecnicoDos"];
}

// ── Validación centralizada de contacto ─────────────────────────
$_wa_tel1      = preg_replace('/\D/', '', (string)($usuario["telefonoDos"] ?? ""));
$_wa_tel2      = preg_replace('/\D/', '', (string)($usuario["telefono"]   ?? ""));
$_tel1_valido  = (strlen($_wa_tel1) === 10);
$_tel2_valido  = (strlen($_wa_tel2) === 10);

// Número que se usará para WhatsApp (prioridad: telefonoDos)
$_wa_phone        = "";
$_wa_display      = ""; // número que se muestra en el campo WhatsApp
if ($_tel1_valido)       { $_wa_phone = "52" . $_wa_tel1; $_wa_display = $_wa_tel1; }
elseif ($_tel2_valido)   { $_wa_phone = "52" . $_wa_tel2; $_wa_display = $_wa_tel2; }

// Teléfono secundario: solo mostrar si es válido Y diferente al de WhatsApp
$_tel_display = ($_tel2_valido && $_wa_tel2 !== $_wa_tel1) ? $_wa_tel2 : "";

$_wa_orden = (string)($value["id"] ?? "");

// Asesor y tecnico para el mensaje de Terminados
$_wa_asesor  = Controladorasesores::ctrMostrarAsesoresEleg("id", $_GET["asesor"]);
$_wa_tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $_GET["tecnico"]);
$_wa_asesorNombre  = isset($_wa_asesor["nombre"])  ? $_wa_asesor["nombre"]  : "";
$_wa_tecnicoNombre = isset($_wa_tecnico["nombre"]) ? $_wa_tecnico["nombre"] : "";

$_wa_total = number_format(floatval($value["total"] ?? 0), 2);
$_wa_btns = array(); // cada elemento: ['label'=>'...','url'=>'...']

if ($_wa_phone !== "") {
    if ($estado === 'Pendiente de autorización (AUT') {
        $msg = 'NOS DA GUSTO INFORMARTE QUE YA TENEMOS TU PRESUPUESTO PODRÁS COMUNICARTE POR FAVOR PARA EXPLICARTE MEJOR A LOS TELÉFONOS 7222831159/7221671684/7222144416/720-3321271 EN UN HORARIO DE LUNES A VIERNES DE 10 A 2 Y DE 4 A 6:30 SÁBADOS DE 9 A 2 GRACIAS. ORDEN ' . $_wa_orden . ' ASESOR *' . $_wa_asesorNombre . '* ESTE NUMERO ES SOLO PARA MENSAJES';
        $_wa_btns[] = array('label' => 'WhatsApp — Presupuesto', 'url' => 'https://api.whatsapp.com/send?phone=' . $_wa_phone . '&text=' . rawurlencode($msg));
    } elseif ($estado === 'Aceptado (ok)') {
        $msg = "Buenos días soy su asesor " . $_wa_asesorNombre . " le comparto el  monto apagar  que acepto para la reparación de su equipo \xE2\x80\x8E\n\nOrden: " . $_wa_orden . "\n\nAceptada.\n\n$ " . $_wa_total . "\n\nGracias.";
        $_wa_btns[] = array('label' => 'WhatsApp — Aceptado', 'url' => 'https://api.whatsapp.com/send?phone=' . $_wa_phone . '&text=' . rawurlencode($msg));
    } elseif ($estado === 'Terminada (ter)') {
        $msg1 = 'BUEN DIA LE INFORMAMOS QUE SU EQUIPO YA ESTA TERMINADO OJALÁ PODAMOS CONTAR CON SU RECOLECCIÓN PARA MAYOR INFORMACIÓN 7222831159/7221671684/7222144416 EN UN HORARIO DE LUNES A VIERNES DE 10 A 2 Y DE 4 A 6:30 SÁBADOS DE 9 A 2 GRACIAS. ORDEN *' . $_wa_orden . '* ASESOR *' . $_wa_asesorNombre . '* TÉCNICO *' . $_wa_tecnicoNombre . '* https://comercializadoraegs.com';
        $msg2 = 'BUEN DIA PARA BRINDARLE UN MEJOR SERVICIO LE AGRADECERÍAMOS NOS PUEDA CONFIRMAR SU CITA HOY O MAÑANA * PARA LA RECOLECCIÓN DE SU EQUIPO GRACIAS* https://comercializadoraegs.com SI USTED YA PASO POR EL EQUIPO O TIENE CITA PROGRAMADA POR FAVOR INFORMENOS POR ESTE MEDIO GRACIAS';
        $_wa_btns[] = array('label' => 'WhatsApp — Equipo listo',   'url' => 'https://api.whatsapp.com/send?phone=' . $_wa_phone . '&text=' . rawurlencode($msg1));
        $_wa_btns[] = array('label' => 'WhatsApp — Confirmar cita', 'url' => 'https://api.whatsapp.com/send?phone=' . $_wa_phone . '&text=' . rawurlencode($msg2));
    } elseif ($estado === 'Entregado (Ent)') {
        $msg = 'BUEN DIA PARA BRINDARLE UN MEJOR SERVICIO LE AGRADECERÍAMOS NOS PUEDA *COMENTAR COMO ESTA TRABAJANDO EL EQUIPO QUE NOS TRAJO A REPARACION, PARA NOSOTROS ES MUY IMPORTANTE SU SATISFACCION GRACIAS https://comercializadoraegs.com ORDEN *' . $_wa_orden . '* ASESOR *' . $_wa_asesorNombre . '*';
        $_wa_btns[] = array('label' => 'WhatsApp — Seguimiento', 'url' => 'https://api.whatsapp.com/send?phone=' . $_wa_phone . '&text=' . rawurlencode($msg));
    } elseif ($estado === 'En revisión (REV)') {
        $msg = 'Somos COMERCIALIZADORA EGS * *https://comercializadoraegs.com gracias por venir y permitirnos apoyarte en tu proyecto de REPARACION DE EQUIPOS DE COMPUTO recuerda que es importante seguir en comunicación por este medio en un horario de LUNES A VIERNES DE 10 A 2 Y DE 4 A 6:30 SÁBADOS DE 9 A 2 o a los teléfonos 7222831159/7221671684/7222144416. ORDEN *' . $_wa_orden . '* ASESOR *' . $_wa_asesorNombre . '*';
        $_wa_btns[] = array('label' => 'WhatsApp — Bienvenida', 'url' => 'https://api.whatsapp.com/send?phone=' . $_wa_phone . '&text=' . rawurlencode($msg));
    } else {
        $_wa_btns[] = array('label' => 'WhatsApp', 'url' => '//api.whatsapp.com/send?phone=521' . $_wa_tel1);
    }
}
// ── Email contextual por estado ──────────────────────────────────
$_em_correo      = trim($usuario["correo"] ?? "");
$_em_tieneCorreo = (filter_var($_em_correo, FILTER_VALIDATE_EMAIL) !== false);
$_em_subject     = "";
$_em_body        = "";
$_em_nombreCliente = $usuario["nombre"] ?? "";

if ($_em_tieneCorreo) {
    if ($estado === 'Pendiente de autorización (AUT') {
        $_em_subject = "Presupuesto de tu equipo - Orden #" . $_wa_orden;
        $_em_body    = "Buenos días " . $_em_nombreCliente . ",\n\nNos da gusto informarte que ya tenemos tu presupuesto listo. Por favor comunícate con nosotros para explicarte mejor a los teléfonos 7222831159 / 7221671684 / 7222144416, en un horario de Lunes a Viernes de 10 a 2 y de 4 a 6:30, Sábados de 9 a 2.\n\nOrden: " . $_wa_orden . "\nAsesor: " . $_wa_asesorNombre . "\n\nEste número es solo para mensajes.\n\nGracias.\nComercializadora EGS\nhttps://comercializadoraegs.com";
    } elseif ($estado === 'Aceptado (ok)') {
        $_em_subject = "Orden aceptada - #" . $_wa_orden;
        $_em_body    = "Buenos días " . $_em_nombreCliente . ",\n\nSoy su asesor " . $_wa_asesorNombre . ", le comparto el monto a pagar que aceptó para la reparación de su equipo.\n\nOrden: " . $_wa_orden . "\n\nAceptada.\n\n$ " . $_wa_total . "\n\nGracias.\nComercializadora EGS\nhttps://comercializadoraegs.com";
    } elseif ($estado === 'Terminada (ter)') {
        $_em_subject = "¡Tu equipo está listo! - Orden #" . $_wa_orden;
        $_em_body    = "Buenos días " . $_em_nombreCliente . ",\n\nLe informamos que su equipo ya está terminado. ¡Esperamos poder contar con su recolección!\n\nPara mayor información comuníquese a los teléfonos 7222831159 / 7221671684 / 7222144416, en un horario de Lunes a Viernes de 10 a 2 y de 4 a 6:30, Sábados de 9 a 2.\n\nOrden: " . $_wa_orden . "\nAsesor: " . $_wa_asesorNombre . "\nTécnico: " . $_wa_tecnicoNombre . "\n\nGracias.\nComercializadora EGS\nhttps://comercializadoraegs.com";
    } elseif ($estado === 'Entregado (Ent)') {
        $_em_subject = "¿Cómo está trabajando tu equipo? - Orden #" . $_wa_orden;
        $_em_body    = "Buenos días " . $_em_nombreCliente . ",\n\nPara brindarle un mejor servicio, le agradeceríamos nos pudiera comentar cómo está trabajando el equipo que nos trajo a reparación. Para nosotros es muy importante su satisfacción.\n\nOrden: " . $_wa_orden . "\nAsesor: " . $_wa_asesorNombre . "\n\nGracias.\nComercializadora EGS\nhttps://comercializadoraegs.com";
    } elseif ($estado === 'En revisión (REV)') {
        $_em_subject = "Gracias por tu visita - Orden #" . $_wa_orden;
        $_em_body    = "Buenos días " . $_em_nombreCliente . ",\n\nSomos Comercializadora EGS. Gracias por venir y permitirnos apoyarte en tu proyecto de reparación de equipos de cómputo. Recuerda que es importante seguir en comunicación en un horario de Lunes a Viernes de 10 a 2 y de 4 a 6:30, Sábados de 9 a 2, o a los teléfonos 7222831159 / 7221671684 / 7222144416.\n\nOrden: " . $_wa_orden . "\nAsesor: " . $_wa_asesorNombre . "\n\nGracias.\nhttps://comercializadoraegs.com";
    } else {
        $_em_subject = "Estado de tu orden #" . $_wa_orden;
        $_em_body    = "Buenos días " . $_em_nombreCliente . ",\n\nSaludos de Comercializadora EGS.\n\nhttps://comercializadoraegs.com";
    }
}
// ─────────────────────────────────────────────────────────────────

// Nombres de partidas para el loop
$partidaNames = array('Uno','Dos','Tres','Cuatro','Cinco','Seis','Siete','Ocho','Nueve','Diez');
$partidaLabels = array(
	1 => 'Diagnóstico / Servicio principal',
	2 => 'Refacción o pieza',
	3 => 'Servicio adicional',
	4 => 'Mano de obra',
	5 => 'Componente o accesorio',
	6 => 'Limpieza / Mantenimiento',
	7 => 'Configuración / Software',
	8 => 'Transporte o logística',
	9 => 'Garantía extendida',
	10 => 'Otro concepto'
);

date_default_timezone_set("America/Mexico_City");

// Mapeo estandarizado de estado → clase CSS
function _egsEstadoClass($estado) {
	$e = strtolower(trim($estado));
	if (strpos($e, 'autorización') !== false || strpos($e, 'autorizacion') !== false || strpos($e, 'pendiente') !== false) return 'egs-estado-pendiente';
	if (strpos($e, 'supervisión') !== false || strpos($e, 'supervision') !== false) return 'egs-estado-supervision';
	if (strpos($e, 'garantía') !== false || strpos($e, 'garantia') !== false) return 'egs-estado-garantia';
	if (strpos($e, 'revisión') !== false || strpos($e, 'revision') !== false) return 'egs-estado-revision';
	if (strpos($e, 'terminada') !== false) return 'egs-estado-terminada';
	if (strpos($e, 'entregado') !== false || strpos($e, 'entregada') !== false) return 'egs-estado-entregado';
	if (strpos($e, 'aceptado') !== false || strpos($e, 'aceptada') !== false) return 'egs-estado-aceptado';
	if (strpos($e, 'cancel') !== false) return 'egs-estado-cancelada';
	if (strpos($e, 'sin reparación') !== false || strpos($e, 'sin reparacion') !== false) return 'egs-estado-sin-reparacion';
	if (strpos($e, 'producto para venta') !== false) return 'egs-estado-producto-venta';
	return 'egs-estado-default';
}
?>

<div class="content-wrapper">

	<section class="content-header">
		<h2 style="margin:0 0 10px">
			<i class="fa-solid fa-file-invoice" style="color:#6366f1;margin-right:8px"></i>Orden #<?php echo htmlspecialchars($_GET["idOrden"]); ?>
			<a href="index.php?ruta=seguimiento&idOrden=<?php echo urlencode($_GET["idOrden"]); ?>"
			   class="btn btn-sm"
			   style="margin-left:12px;background:#6366f1;color:#fff;border-radius:6px;font-size:13px;vertical-align:middle"
			   title="Ver seguimiento de la orden">
				<i class="fas fa-route" style="margin-right:4px"></i> Seguimiento
			</a>
		</h2>
		<ol class="breadcrumb">
			<li><a href="index.php?ruta=inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>
			<li><a href="index.php?ruta=ordenes">Órdenes</a></li>
			<li class="active">Orden <?php echo htmlspecialchars($_GET["idOrden"]); ?></li>
		</ol>
	</section>

	<section class="content">

		<!-- ==================== FILA 1: CLIENTE + ANÁLISIS (izq) + IMÁGENES + FICHA TÉCNICA (der) ==================== -->
		<div class="row">

			<div class="col-lg-5 col-xs-12">
				<!-- DATOS DEL CLIENTE -->
				<div class="egs-section">
					<div class="egs-title-bar"><i class="fa-solid fa-user"></i> Datos del cliente</div>
					<div class="egs-body">
						<div class="egs-field-row">
							<label class="egs-lbl">Nombre</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fas fa-user"></i></span>
								<input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario["nombre"]); ?>" readonly>
								<?php if (intval($_GET["cliente"]) > 0): ?>
								<span class="input-group-btn">
									<a class="btn btn-info" href="index.php?ruta=Historialdecliente&idCliente=<?php echo intval($_GET["cliente"]); ?>&nombreCliente=<?php echo rawurlencode($usuario["nombre"]); ?>" target="_blank" title="Ver historial del cliente">
										<i class="fa-solid fa-clock-rotate-left"></i>
									</a>
								</span>
								<?php endif; ?>
							</div>
						</div>

						<?php if (!$isTecnico): ?>
						<?php if ($_em_tieneCorreo): ?>
						<div class="egs-field-row">
							<label class="egs-lbl">Correo</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fas fa-envelope"></i></span>
								<input type="text" class="form-control" value="<?php echo htmlspecialchars($_em_correo); ?>" readonly>
							</div>
						</div>
						<?php endif; ?>
						<?php if ($_wa_phone !== ""): ?>
						<div class="egs-field-row">
							<label class="egs-lbl">WhatsApp</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fab fa-whatsapp"></i></span>
								<input type="text" class="form-control" value="<?php echo htmlspecialchars($_wa_display); ?>" id="botonwhats" readonly>
								<span class="input-group-btn">
									<button class="btn btn-default" type="button" title="Copiar número"
									        onclick="_egsCopiarTel(this, '<?php echo htmlspecialchars($_wa_display); ?>')">
									<i class="fas fa-copy"></i>
									</button>
								</span>
							</div>
						</div>
						<?php endif; ?>
						<?php if ($_tel_display !== ""): ?>
						<div class="egs-field-row">
							<label class="egs-lbl">Teléfono</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fas fa-phone-alt"></i></span>
								<input type="text" class="form-control" value="<?php echo htmlspecialchars($_tel_display); ?>" readonly>
								<span class="input-group-btn">
									<button class="btn btn-default" type="button" title="Copiar número"
									        onclick="_egsCopiarTel(this, '<?php echo htmlspecialchars($_tel_display); ?>')">
									<i class="fas fa-copy"></i>
									</button>
								</span>
							</div>
						</div>
						<?php endif; ?>
						<?php endif; ?>

						<div class="egs-field-row">
							<label class="egs-lbl">Fecha de entrada</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fas fa-calendar-check"></i></span>
								<input type="text" class="form-control" value="<?php echo htmlspecialchars($fecha_ingreso); ?>" readonly>
							</div>
						</div>
						<div class="egs-field-row">
							<label class="egs-lbl">Última modificación</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fas fa-clock"></i></span>
								<input type="text" class="form-control" value="<?php echo htmlspecialchars($value["fecha"]); ?>" readonly>
							</div>
						</div>

						<?php if (($isAdmin || $isVendedor) && ($_em_tieneCorreo || !empty($_wa_btns))): ?>
						<hr style="margin:12px 0">
						<div style="display:flex;gap:8px;flex-wrap:wrap">
							<?php if ($_em_tieneCorreo): ?>
							<a class="btn egs-btn-accent btn-sm" href="mailto:<?php echo htmlspecialchars($_em_correo); ?>?subject=<?php echo rawurlencode($_em_subject); ?>&body=<?php echo rawurlencode($_em_body); ?>">
								<i class="fas fa-envelope"></i> Enviar correo
							</a>
							<?php endif; ?>
							<?php foreach ($_wa_btns as $_wb): ?>
							<a class="btn btn-success btn-sm" href="<?php echo htmlspecialchars($_wb['url']); ?>" target="_blank">
								<i class="fab fa-whatsapp"></i> <?php echo htmlspecialchars($_wb['label']); ?>
							</a>
							<?php endforeach; ?>
							<button type="button" class="btn btn-sm btnAgendarCitaDesdeOrden" data-orden-id="<?php echo intval($valor); ?>" style="background:#6366f1;color:#fff;border-color:#6366f1;font-weight:600;border-radius:6px;">
								<i class="fa-solid fa-calendar-plus"></i> Nueva Cita
							</button>
						</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- ANÁLISIS DEL CLIENTE -->
				<?php
				require_once "config/clienteBadges.helper.php";
				$_bh = ClienteBadgesHelper::getInstance();
				$_cs = $_bh->getDetailedStats(intval($_GET["cliente"]));
				$_csBadges = $_bh->render(intval($_GET["cliente"]));

				$_pcVal = $_cs['prob_cancelacion'];
				$_ceVal = $_cs['calif_entrega'];
				$_rdVal = $_cs['avg_recogida'];

				if ($_pcVal !== null) {
					if ($_pcVal <= 15)      $_pcColor = '#16a34a';
					elseif ($_pcVal <= 35)  $_pcColor = '#2563eb';
					elseif ($_pcVal <= 55)  $_pcColor = '#d97706';
					else                    $_pcColor = '#dc2626';
				}
				if ($_ceVal !== null) {
					if ($_ceVal >= 90)      { $_ceColor='#16a34a'; $_ceIcon='fa-star'; $_ceLabel='Excelente'; }
					elseif ($_ceVal >= 70)  { $_ceColor='#2563eb'; $_ceIcon='fa-thumbs-up'; $_ceLabel='Buena'; }
					elseif ($_ceVal >= 50)  { $_ceColor='#d97706'; $_ceIcon='fa-minus-circle'; $_ceLabel='Regular'; }
					else                    { $_ceColor='#dc2626'; $_ceIcon='fa-thumbs-down'; $_ceLabel='Baja'; }
				}
				if ($_rdVal !== null) {
					if ($_rdVal <= 7)       { $_rdColor='#16a34a'; $_rdIcon='fa-bolt'; $_rdTag='Recolecta rápido'; }
					elseif ($_rdVal <= 14)  { $_rdColor='#2563eb'; $_rdIcon='fa-clock'; $_rdTag='Tiempo normal'; }
					elseif ($_rdVal <= 30)  { $_rdColor='#d97706'; $_rdIcon='fa-hourglass-half'; $_rdTag='Tarda en recoger'; }
					else                    { $_rdColor='#dc2626'; $_rdIcon='fa-hourglass-end'; $_rdTag='Muy lento para recoger'; }
				}

				// ── Recomendaciones por perfil con contexto de negocio ──
				$_perfil = $_SESSION["perfil"];
				$_recs = [];

				// ─── ADMINISTRADOR ───
				if ($isAdmin) {
					if ($_pcVal !== null && $_pcVal > 50) {
						$_recs[] = '<i class="fas fa-exclamation-triangle" style="color:#dc2626"></i> <strong>Alerta — Alta cancelación (' . $_pcVal . '%):</strong> De ' . $_cs['resueltas'] . ' órdenes resueltas, ' . $_cs['canceladas'] . ' fueron canceladas. Revise si el presupuesto de reparación está siendo competitivo frente a equipos nuevos, o si los tiempos de diagnóstico están causando que el cliente busque otra opción. Considere reunirse con el asesor asignado para identificar el patrón.';
					} elseif ($_pcVal !== null && $_pcVal > 30) {
						$_recs[] = '<i class="fas fa-eye" style="color:#d97706"></i> <strong>Monitorear — Cancelación moderada (' . $_pcVal . '%):</strong> ' . $_cs['canceladas'] . ' de ' . $_cs['resueltas'] . ' órdenes canceladas. Valide que los presupuestos estén siendo claros desde el primer contacto y que el asesor explique bien las partidas de mano de obra y refacciones.';
					}
					if ($_rdVal !== null && $_rdVal > 30) {
						$_recs[] = '<i class="fas fa-warehouse" style="color:#dc2626"></i> <strong>Almacén — Equipo estancado (~' . $_rdVal . ' días):</strong> Este cliente históricamente tarda más de un mes en recoger equipos terminados. Esto ocupa espacio en el taller y genera riesgo de daño o pérdida. Considere aplicar cargo por almacenaje después de 15 días o establecer contacto directo desde gerencia.';
					} elseif ($_rdVal !== null && $_rdVal > 14) {
						$_recs[] = '<i class="fas fa-clock" style="color:#d97706"></i> <strong>Seguimiento — Recolección lenta (~' . $_rdVal . ' días):</strong> El cliente no recoge de inmediato. Indique al asesor que programe un WhatsApp de recordatorio desde el día que el equipo pasa a "Terminada" y un segundo aviso a los 7 días.';
					}
					if ($_ceVal !== null && $_ceVal >= 90 && $_rdVal !== null && $_rdVal <= 7) {
						$_recs[] = '<i class="fas fa-trophy" style="color:#16a34a"></i> <strong>Cliente VIP — Excelente historial:</strong> ' . $_cs['entregadas'] . ' de ' . $_cs['resueltas'] . ' entregas exitosas y recoge en menos de una semana. Este tipo de cliente genera flujo constante. Considere ofrecerle un descuento preferencial del 5-10% en su próximo servicio o prioridad de atención para fidelizarlo.';
					}
					if ($_cs['es_nuevo']) {
						$_recs[] = '<i class="fas fa-seedling" style="color:#8b5cf6"></i> <strong>Cliente nuevo (' . $_cs['total_ordenes'] . ' órdenes):</strong> La primera experiencia define si regresa o no. Asegúrese de que el diagnóstico se entregue rápido, la comunicación sea clara y el presupuesto no tenga sorpresas. Un cliente nuevo bien atendido puede representar 5-10 órdenes futuras.';
					}
				}

				// ─── VENDEDOR / ASESOR ───
				if ($isVendedor) {
					if ($_pcVal !== null && $_pcVal > 50) {
						$_recs[] = '<i class="fas fa-phone-alt" style="color:#dc2626"></i> <strong>Acción urgente — Alta cancelación (' . $_pcVal . '%):</strong> Este cliente cancela más de la mitad de sus órdenes. Antes de enviar el presupuesto por WhatsApp, llámele directamente para explicar cada partida. Enfatice el valor de la reparación vs. comprar equipo nuevo y ofrezca opciones si el costo es alto (reparación parcial, priorizar lo esencial).';
					} elseif ($_pcVal !== null && $_pcVal > 30) {
						$_recs[] = '<i class="fab fa-whatsapp" style="color:#d97706"></i> <strong>Seguimiento activo — Cancelación moderada (' . $_pcVal . '%):</strong> Envíe un WhatsApp de seguimiento al día siguiente de entregar el presupuesto. Pregunte si tiene dudas y recuérdele los beneficios de reparar (garantía de servicio, tiempo de entrega). No espere a que el cliente se comunique.';
					} elseif ($_pcVal !== null && $_pcVal <= 15 && $_ceVal !== null && $_ceVal >= 85) {
						$_recs[] = '<i class="fas fa-handshake" style="color:#16a34a"></i> <strong>Cliente leal — Baja cancelación:</strong> Este cliente casi siempre acepta los presupuestos. Aproveche la confianza para ofrecerle servicios adicionales: mantenimiento preventivo, limpieza profunda, respaldo de información o actualización de componentes (RAM, SSD).';
					}
					if ($_rdVal !== null && $_rdVal > 21) {
						$_recs[] = '<i class="fas fa-bell" style="color:#d97706"></i> <strong>Recordatorio de recolección (~' . $_rdVal . ' días promedio):</strong> Cuando esta orden pase a "Terminada", envíe el WhatsApp de equipo listo ese mismo día. Si a los 5 días no confirma, envíe un segundo mensaje. Este cliente necesita reminders constantes para que recoja.';
					} elseif ($_rdVal !== null && $_rdVal <= 7) {
						$_recs[] = '<i class="fas fa-bolt" style="color:#16a34a"></i> <strong>Recolección rápida (~' . $_rdVal . ' días):</strong> Este cliente recoge pronto. Cuando el equipo esté listo, basta con un solo aviso por WhatsApp. No necesita seguimiento adicional.';
					}
					if ($_cs['es_nuevo']) {
						$_recs[] = '<i class="fas fa-user-plus" style="color:#8b5cf6"></i> <strong>Cliente nuevo — Primera impresión:</strong> Sea especialmente detallado en la explicación del presupuesto. Describa cada partida (diagnóstico, refacción, mano de obra) y dé un rango de tiempo realista. Si el cliente siente transparencia, es muy probable que regrese para futuras reparaciones.';
					}
					if ($_cs['resueltas'] == 0 && !$_cs['es_nuevo']) {
						$_recs[] = '<i class="fas fa-info-circle" style="color:#64748b"></i> Este cliente tiene órdenes registradas pero ninguna ha llegado a entrega o cancelación. Verifique si hay órdenes pendientes de autorización que requieran seguimiento.';
					}
				}

				// ─── TÉCNICO ───
				if ($isTecnico) {
					if ($_pcVal !== null && $_pcVal > 50) {
						$_recs[] = '<i class="fas fa-exclamation-circle" style="color:#dc2626"></i> <strong>No priorizar — Alta cancelación (' . $_pcVal . '%):</strong> Este cliente cancela frecuentemente. Realice el diagnóstico estándar pero no invierta tiempo en reparaciones complejas hasta que el asesor confirme que el cliente autorizó el presupuesto. Si hay otros equipos en espera, atienda primero los de clientes con mejor historial.';
					} elseif ($_pcVal !== null && $_pcVal <= 20 && $_ceVal !== null && $_ceVal >= 80) {
						$_recs[] = '<i class="fas fa-check-circle" style="color:#16a34a"></i> <strong>Cliente confiable — Proceda con confianza:</strong> Historial sólido de ' . $_cs['entregadas'] . ' entregas exitosas. Este cliente casi siempre autoriza y recoge. Si durante la reparación identifica algo adicional que mejoraría el equipo (ej: cambio de pasta térmica, limpieza interna), repórtelo al asesor como oportunidad — probablemente lo acepte.';
					}
					if ($_rdVal !== null && $_rdVal > 30) {
						$_recs[] = '<i class="fas fa-hourglass-end" style="color:#d97706"></i> <strong>Equipo ocupará espacio — Recolección lenta (~' . $_rdVal . ' días):</strong> Una vez terminado, este equipo probablemente estará en el taller al menos un mes. Colóquelo en zona de almacenaje, no en mesa de trabajo activa. Priorice espacio para equipos de clientes que recogen más rápido.';
					} elseif ($_rdVal !== null && $_rdVal <= 7) {
						$_recs[] = '<i class="fas fa-bolt" style="color:#16a34a"></i> <strong>Recolección rápida (~' . $_rdVal . ' días):</strong> Este cliente recoge en cuanto se le avisa. Si puede adelantar la terminación de este equipo, libera espacio de trabajo rápidamente.';
					}
					if ($_cs['es_nuevo']) {
						$_recs[] = '<i class="fas fa-seedling" style="color:#8b5cf6"></i> <strong>Cliente nuevo — Diagnóstico detallado:</strong> Documente bien cada hallazgo en las observaciones (fotos del estado del equipo, componentes revisados, fallas encontradas). El asesor necesitará esta información para explicar el presupuesto de forma clara y convincente al cliente.';
					}
				}

				// ─── SECRETARIA ───
				if ($isSecretaria) {
					if ($_rdVal !== null && $_rdVal > 21) {
						$_recs[] = '<i class="fas fa-calendar-alt" style="color:#d97706"></i> <strong>Programar recordatorios — Recolección lenta (~' . $_rdVal . ' días):</strong> Al pasar esta orden a "Terminada", agende un recordatorio a los 3, 7 y 14 días para llamar al cliente. Este perfil de cliente necesita contacto repetido. Tenga a la mano el teléfono y WhatsApp para no perder el seguimiento.';
					} elseif ($_rdVal !== null && $_rdVal <= 7) {
						$_recs[] = '<i class="fas fa-bolt" style="color:#16a34a"></i> <strong>Recolección rápida (~' . $_rdVal . ' días):</strong> Normalmente este cliente pasa por el equipo en menos de una semana. Basta con un aviso. Tenga lista la documentación de entrega.';
					}
					if ($_pcVal !== null && $_pcVal > 40) {
						$_recs[] = '<i class="fas fa-clipboard-list" style="color:#dc2626"></i> <strong>Preparar documentación — Alta cancelación (' . $_pcVal . '%):</strong> Si el cliente decide cancelar, tenga lista la orden de devolución y verifique que todas las observaciones del técnico estén registradas. Asegúrese de que el equipo se devuelva en las mismas condiciones que ingresó.';
					}
					if ($_ceVal !== null && $_ceVal >= 90) {
						$_recs[] = '<i class="fas fa-star" style="color:#16a34a"></i> <strong>Buen cliente — Entrega ágil:</strong> ' . $_cs['entregadas'] . ' entregas exitosas. Cuando venga a recoger, agilice la documentación y el cobro. Un buen servicio en la entrega refuerza la fidelización.';
					}
					if ($_cs['es_nuevo']) {
						$_recs[] = '<i class="fas fa-id-card" style="color:#8b5cf6"></i> <strong>Cliente nuevo — Datos completos:</strong> Verifique que el teléfono, WhatsApp y correo estén registrados correctamente. Para clientes nuevos es especialmente importante tener múltiples canales de contacto por si no responde al primero.';
					}
				}
				?>

				<!-- ANÁLISIS DEL CLIENTE -->
				<div class="egs-section">
					<div class="egs-title-bar"><i class="fa-solid fa-chart-line"></i> Análisis del cliente <?php echo $_csBadges; ?></div>
					<div class="egs-body">
						<?php if ($_cs['es_nuevo']): ?>
						<div class="egs-rec-box" style="border-left-color:#8b5cf6;margin-bottom:14px">
							<div class="egs-rec-title"><i class="fas fa-seedling" style="color:#8b5cf6"></i> Cliente nuevo</div>
							<p class="egs-rec-text">Este cliente tiene pocas órdenes registradas (<?php echo $_cs['total_ordenes']; ?>). Aún no hay suficientes datos para calcular probabilidades confiables.</p>
						</div>
						<?php endif; ?>

						<div class="egs-analytics-grid">
							<!-- Probabilidad de cancelación -->
							<div class="egs-stat-card">
								<?php if ($_pcVal !== null): ?>
									<div class="egs-stat-value" style="color:<?php echo $_pcColor; ?>"><?php echo $_pcVal; ?>%</div>
									<div class="egs-stat-label">Probabilidad de cancelación</div>
									<div class="egs-progress-bar"><div class="egs-progress-fill" style="width:<?php echo $_pcVal; ?>%;background:<?php echo $_pcColor; ?>"></div></div>
									<div class="egs-stat-sub"><?php echo $_cs['canceladas']; ?> canceladas de <?php echo $_cs['resueltas']; ?> resueltas</div>
								<?php else: ?>
									<div class="egs-stat-value" style="color:#94a3b8">—</div>
									<div class="egs-stat-label">Probabilidad de cancelación</div>
									<div class="egs-stat-sub">Sin datos suficientes</div>
								<?php endif; ?>
							</div>

							<!-- Tiempo estimado de recolección -->
							<div class="egs-stat-card">
								<?php if ($_rdVal !== null): ?>
									<div class="egs-stat-value" style="color:<?php echo $_rdColor; ?>"><i class="fas <?php echo $_rdIcon; ?>" style="font-size:18px;vertical-align:middle;margin-right:4px"></i>~<?php echo $_rdVal; ?> días</div>
									<div class="egs-stat-label">Tiempo estimado de recolección</div>
									<div class="egs-progress-bar"><div class="egs-progress-fill" style="width:<?php echo min($_rdVal / 45 * 100, 100); ?>%;background:<?php echo $_rdColor; ?>"></div></div>
									<div class="egs-stat-sub"><?php echo $_rdTag; ?></div>
								<?php else: ?>
									<div class="egs-stat-value" style="color:#94a3b8">—</div>
									<div class="egs-stat-label">Tiempo estimado de recolección</div>
									<div class="egs-stat-sub">Sin datos suficientes</div>
								<?php endif; ?>
							</div>
						</div>

						<!-- Calificación de entregas -->
						<?php if ($_ceVal !== null): ?>
						<div class="egs-analytics-grid" style="grid-template-columns:1fr">
							<div class="egs-stat-card" style="text-align:left;display:flex;align-items:center;gap:16px">
								<div style="min-width:50px;text-align:center">
									<i class="fas <?php echo $_ceIcon; ?>" style="font-size:28px;color:<?php echo $_ceColor; ?>"></i>
								</div>
								<div style="flex:1">
									<div style="font-size:18px;font-weight:800;color:<?php echo $_ceColor; ?>"><?php echo $_ceVal; ?>% — <?php echo $_ceLabel; ?></div>
									<div class="egs-stat-label" style="margin-top:2px">Calificación de entregas</div>
									<div class="egs-progress-bar" style="margin-top:6px"><div class="egs-progress-fill" style="width:<?php echo $_ceVal; ?>%;background:<?php echo $_ceColor; ?>"></div></div>
									<div class="egs-stat-sub"><?php echo $_cs['entregadas']; ?> entregadas de <?php echo $_cs['resueltas']; ?> resueltas (<?php echo $_cs['total_ordenes']; ?> totales)</div>
								</div>
							</div>
						</div>
						<?php endif; ?>

						<!-- Recomendaciones por perfil -->
						<?php if (!empty($_recs)): ?>
						<div class="egs-rec-box">
							<div class="egs-rec-title"><i class="fas fa-lightbulb" style="color:#6366f1"></i> Recomendaciones para <?php echo htmlspecialchars(ucfirst($_perfil)); ?></div>
							<?php foreach ($_recs as $_r): ?>
							<p class="egs-rec-text"><?php echo $_r; ?></p>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>

						<!-- Cómo se calcula -->
						<div class="egs-calc-summary">
							<i class="fas fa-calculator"></i> <strong>¿Cómo se calcula?</strong><br>
							<i class="fas fa-angle-right"></i> <strong>Prob. de cancelación:</strong> canceladas ÷ (entregadas + canceladas) × 100<br>
							<i class="fas fa-angle-right"></i> <strong>Tiempo de recolección:</strong> promedio de días entre "Terminada" y "Entregado" en órdenes anteriores<br>
							<i class="fas fa-angle-right"></i> <strong>Calificación:</strong> entregadas ÷ (entregadas + canceladas) × 100
						</div>
					</div>
				</div>

			</div>

			<div class="col-lg-7 col-xs-12">
				<!-- IMÁGENES DE LA ORDEN -->
				<div class="egs-section">
					<div class="egs-title-bar"><i class="fa-solid fa-images"></i> Imágenes de la orden</div>
					<div class="egs-body" style="padding:0">
						<?php
						// Construir array de todas las imágenes
						$_gal_imgs = array();
						$_default_img = 'vistas/img/default/default.png';
						if (!empty($AlbumDeImagenes)) {
							foreach ($AlbumDeImagenes as $_gi) {
								if (isset($_gi["foto"]) && !empty($_gi["foto"]) && $_gi["foto"] !== $_default_img) $_gal_imgs[] = $_gi["foto"];
							}
						}
						// Solo agregar portada si no hay imágenes en el álbum
						if (empty($_gal_imgs) && !empty($portada) && $portada !== $_default_img) {
							$_gal_imgs[] = $portada;
						}
						$_gal_total = count($_gal_imgs);

						if ($_gal_total > 0):
						?>
						<div class="egs-gallery">
							<!-- Counter badge -->
							<span class="egs-gallery-counter">1 / <?php echo $_gal_total; ?></span>

							<!-- Main image -->
							<div class="egs-gallery-main">
								<img src="<?php echo htmlspecialchars($_gal_imgs[0]); ?>" alt="Imagen de la orden" loading="lazy">
							</div>

							<?php if ($_gal_total > 1): ?>
							<!-- Navigation arrows -->
							<button class="egs-gallery-nav prev" title="Anterior"><i class="fa-solid fa-chevron-left"></i></button>
							<button class="egs-gallery-nav next" title="Siguiente"><i class="fa-solid fa-chevron-right"></i></button>

							<!-- Thumbnails strip -->
							<div class="egs-gallery-thumbs">
								<?php foreach ($_gal_imgs as $_gi_idx => $_gi_src): ?>
								<div class="egs-gallery-thumb <?php echo $_gi_idx === 0 ? 'active' : ''; ?>" data-src="<?php echo htmlspecialchars($_gi_src); ?>">
									<img src="<?php echo htmlspecialchars($_gi_src); ?>" alt="Miniatura" loading="lazy">
								</div>
								<?php endforeach; ?>
							</div>
							<?php endif; ?>
						</div>
						<?php else: ?>
						<div style="width:100%;border-radius:12px;overflow:hidden;background:#f1f5f9">
							<img src="vistas/img/default/default.png" alt="Sin imagen" style="width:100%;height:300px;object-fit:contain;display:block" loading="lazy">
						</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- FICHA TÉCNICA -->
				<div class="egs-section">
					<div class="egs-title-bar"><i class="fa-solid fa-microchip"></i> Ficha técnica</div>
					<div class="egs-body">
						<form role="form" method="post" class="formularioFichaTecnica">
							<?php if ($value["marcaDelEquipo"] == ""): ?>
								<div class="egs-field-row">
									<label class="egs-lbl">Marca del equipo</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="far fa-copyright"></i></span>
										<input type="text" id="marca" class="form-control" name="marcaDelEquipo" placeholder="Ej: HP, EPSON, BROTHER">
									</div>
									<span id="spanmarca" style="color:red;font-size:12px"></span>
								</div>
								<div class="egs-field-row">
									<label class="egs-lbl">Modelo del equipo</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-kaaba"></i></span>
										<input type="text" id="modelo" class="form-control" name="modeloDelEquipo" placeholder="Ej: LaserJet Pro M404">
									</div>
									<span id="spanmodelo" style="color:red;font-size:12px"></span>
								</div>
								<div class="egs-field-row">
									<label class="egs-lbl">Número de serie</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-barcode"></i></span>
										<input type="text" id="numeroserial" class="form-control" name="numeroDeSerieDelEquipo" placeholder="Últimos 6 dígitos">
									</div>
									<span id="spannumeroserie" style="color:red;font-size:12px"></span>
								</div>
							<?php else: ?>
								<div class="egs-field-row">
									<label class="egs-lbl">Marca</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="far fa-copyright"></i></span>
										<input type="text" id="marca2" class="form-control" value="<?php echo htmlspecialchars($value["marcaDelEquipo"]); ?>" readonly>
									</div>
								</div>
								<div class="egs-field-row">
									<label class="egs-lbl">Modelo</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-kaaba"></i></span>
										<input type="text" id="modelo2" class="form-control" value="<?php echo htmlspecialchars($value["modeloDelEquipo"]); ?>" readonly>
									</div>
								</div>
								<div class="egs-field-row">
									<label class="egs-lbl">Número de serie</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-barcode"></i></span>
										<input type="text" id="numeroserial2" class="form-control" value="<?php echo htmlspecialchars($value["numeroDeSerieDelEquipo"]); ?>" readonly>
									</div>
								</div>
							<?php endif; ?>
							<input type="hidden" value="<?php echo $_GET["idOrden"]; ?>" name="idOrden">
						</form>
					</div>
				</div>
			</div>

		</div><!-- /row 1 -->

		<!-- ═══ Lightbox Modal ═══ -->
		<div class="modal fade" id="egsLightbox" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body">
						<!-- Close button -->
						<button class="lb-close" data-dismiss="modal" title="Cerrar (Esc)">
							<i class="fa-solid fa-xmark"></i>
						</button>

						<!-- Nav arrows -->
						<?php if ($_gal_total > 1): ?>
						<button class="lb-nav prev" title="Anterior (←)"><i class="fa-solid fa-chevron-left"></i></button>
						<button class="lb-nav next" title="Siguiente (→)"><i class="fa-solid fa-chevron-right"></i></button>
						<?php endif; ?>

						<!-- Image -->
						<img src="" class="lb-image" alt="Vista previa">

						<!-- Toolbar -->
						<div class="lb-toolbar">
							<button id="lbZoomOut" title="Alejar (-)"><i class="fa-solid fa-minus"></i></button>
							<button id="lbZoomReset" title="Restablecer"><i class="fa-solid fa-expand"></i></button>
							<button id="lbZoomIn" title="Acercar (+)"><i class="fa-solid fa-plus"></i></button>
							<?php if ($_gal_total > 1): ?>
							<div class="lb-separator"></div>
							<span class="lb-counter">1 / <?php echo $_gal_total; ?></span>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ═══ Gallery JS (después del HTML para que el DOM exista) ═══ -->
		<script>
		(function(){
			var _gal = {
				imgs: <?php echo json_encode(array_values($_gal_imgs)); ?>,
				idx: 0, scale: 1, dragging: false,
				startX: 0, startY: 0, tx: 0, ty: 0
			};

			if (!_gal.imgs || _gal.imgs.length === 0) return;

			function galUpdate(animate) {
				var $main = $('.egs-gallery-main img');
				if (animate) {
					$main.css({opacity: 0, transform: 'scale(.96)'});
					setTimeout(function(){
						$main.attr('src', _gal.imgs[_gal.idx]);
						$main.css({opacity: 1, transform: 'scale(1)'});
					}, 200);
				} else {
					$main.attr('src', _gal.imgs[_gal.idx]);
				}
				$('.egs-gallery-counter').text((_gal.idx + 1) + ' / ' + _gal.imgs.length);
				$('.egs-gallery-thumb').removeClass('active').eq(_gal.idx).addClass('active');
				var $strip = $('.egs-gallery-thumbs'), $active = $('.egs-gallery-thumb.active');
				if ($active.length && $strip.length) {
					var sl = $active[0].offsetLeft - $strip.width()/2 + $active.outerWidth()/2;
					$strip.animate({scrollLeft: sl}, 200);
				}
			}

			function galGo(dir) {
				_gal.idx = (_gal.idx + dir + _gal.imgs.length) % _gal.imgs.length;
				galUpdate(true);
			}

			// Carousel nav
			$(document).on('click', '.egs-gallery-nav.prev', function(e){ e.preventDefault(); e.stopPropagation(); galGo(-1); });
			$(document).on('click', '.egs-gallery-nav.next', function(e){ e.preventDefault(); e.stopPropagation(); galGo(1); });
			$(document).on('click', '.egs-gallery-thumb', function(e){
				e.preventDefault();
				_gal.idx = $(this).index();
				galUpdate(true);
			});

			// Keyboard
			$(document).on('keydown', function(e){
				if ($('#egsLightbox').hasClass('in')) {
					if (e.keyCode === 37) { galGo(-1); lbUpdate(true); }
					else if (e.keyCode === 39) { galGo(1); lbUpdate(true); }
					else if (e.keyCode === 27) { $('#egsLightbox').modal('hide'); }
					else if (e.keyCode === 187 || e.keyCode === 107) { _gal.scale = Math.min(_gal.scale + .25, 5); lbTransform(true); }
					else if (e.keyCode === 189 || e.keyCode === 109) { _gal.scale = Math.max(_gal.scale - .25, .3); lbTransform(true); }
				}
			});

			/* ── Lightbox ── */
			function lbTransform(anim) {
				$('.lb-image').css({
					transition: anim ? 'transform .3s cubic-bezier(.4,0,.2,1)' : 'none',
					transform: 'translate('+_gal.tx+'px,'+_gal.ty+'px) scale('+_gal.scale+')'
				});
			}
			function lbReset() { _gal.scale = 1; _gal.tx = 0; _gal.ty = 0; }
			function lbUpdate(animate) {
				var $img = $('.lb-image');
				if (animate) {
					$img.css({opacity: 0, transform: 'scale(.92)'});
					setTimeout(function(){
						lbReset(); $img.attr('src', _gal.imgs[_gal.idx]);
						$img.css({opacity: 1}); lbTransform(true);
					}, 200);
				} else {
					lbReset(); $img.attr('src', _gal.imgs[_gal.idx]); lbTransform(false);
				}
				$('.lb-counter').text((_gal.idx + 1) + ' / ' + _gal.imgs.length);
			}

			// Open lightbox
			$(document).on('click', '.egs-gallery-main', function(){
				lbReset();
				$('.lb-image').attr('src', _gal.imgs[_gal.idx]);
				lbTransform(false);
				$('.lb-counter').text((_gal.idx + 1) + ' / ' + _gal.imgs.length);
				$('#egsLightbox').modal('show');
			});

			// Lightbox nav
			$(document).on('click', '.lb-nav.prev', function(){ galGo(-1); lbUpdate(true); });
			$(document).on('click', '.lb-nav.next', function(){ galGo(1); lbUpdate(true); });

			// Zoom
			$(document).on('click', '#lbZoomIn', function(){ _gal.scale = Math.min(_gal.scale + .25, 5); lbTransform(true); });
			$(document).on('click', '#lbZoomOut', function(){ _gal.scale = Math.max(_gal.scale - .25, .3); lbTransform(true); });
			$(document).on('click', '#lbZoomReset', function(){ lbReset(); lbTransform(true); });

			// Drag
			$(document).on('mousedown touchstart', '.lb-image', function(e){
				if (_gal.scale > 1) {
					_gal.dragging = true;
					var ev = e.type === 'touchstart' ? e.originalEvent.touches[0] : e;
					_gal.startX = ev.clientX - _gal.tx; _gal.startY = ev.clientY - _gal.ty;
					e.preventDefault();
				}
			});
			$(document).on('mousemove touchmove', function(e){
				if (_gal.dragging) {
					var ev = e.type === 'touchmove' ? e.originalEvent.touches[0] : e;
					_gal.tx = ev.clientX - _gal.startX; _gal.ty = ev.clientY - _gal.startY;
					lbTransform(false);
				}
			});
			$(document).on('mouseup touchend', function(){ _gal.dragging = false; });

			// Double-click zoom toggle
			$(document).on('dblclick', '.lb-image', function(){
				if (_gal.scale > 1) { lbReset(); } else { _gal.scale = 2.5; }
				lbTransform(true);
			});

			// Mouse wheel zoom
			$(document).on('wheel', '#egsLightbox .modal-body', function(e){
				e.preventDefault();
				var delta = e.originalEvent.deltaY > 0 ? -.15 : .15;
				_gal.scale = Math.min(Math.max(_gal.scale + delta, .3), 5);
				lbTransform(true);
			});

			// Swipe on mobile
			var _swipeX = 0;
			$(document).on('touchstart', '.egs-gallery-main', function(e){ _swipeX = e.originalEvent.touches[0].clientX; });
			$(document).on('touchend', '.egs-gallery-main', function(e){
				var diff = e.originalEvent.changedTouches[0].clientX - _swipeX;
				if (Math.abs(diff) > 50) { galGo(diff < 0 ? 1 : -1); }
			});
		})();
		</script>

		<!-- ==================== FILA 2: PARTIDAS (izq) + ASIGNACIÓN (der) ==================== -->
		<div class="row">

			<!-- COLUMNA IZQUIERDA: PARTIDAS Y COSTOS -->
			<div class="col-lg-8 col-xs-12">
				<div class="egs-section">
					<div class="egs-title-bar"><i class="fa-solid fa-list-check"></i> Partidas y costos</div>
					<div class="egs-body">
						<div class="formularioPartidas" id="formPartidas">
							<div class="box" style="border:none;box-shadow:none;margin:0">

								<?php
								// 10 partidas hardcodeadas en un loop
								for ($p = 0; $p < 10; $p++):
									$pName = $partidaNames[$p];
									$pNum = $p + 1;
									$partidaField = "partida".$pName;
									$precioField = "precio".$pName;
									$partidaVal = $value[$partidaField];
									$precioVal = $value[$precioField];

									if ($partidaVal == null) continue;
								?>
									<div class="form-group row egs-partida-row">
										<div class="col-xs-7 col-md-8" style="padding-right:6px">
											<label style="font-size:10px;font-weight:600;color:#64748b;margin-bottom:3px;display:block">
												<i class="fa-solid fa-file-lines" style="margin-right:3px"></i>Partida <?php echo $pNum; ?>
											</label>
											<?php if ($isReadonly): ?>
												<textarea maxlength="320" rows="2" class="form-control text-uppercase" name="<?php echo $partidaField; ?>" form="formObservaciones" placeholder="<?php echo $partidaLabels[$pNum]; ?>" style="font-size:13px;resize:vertical" readonly><?php echo htmlspecialchars($partidaVal); ?></textarea>
											<?php else: ?>
												<div class="input-group">
													<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs quitarPartida"><i class="fas fa-times"></i></button></span>
													<textarea maxlength="320" rows="2" class="form-control text-uppercase" name="<?php echo $partidaField; ?>" form="formObservaciones" placeholder="<?php echo $partidaLabels[$pNum]; ?>" style="font-size:13px;resize:vertical"><?php echo htmlspecialchars($partidaVal); ?></textarea>
												</div>
											<?php endif; ?>
										</div>
										<div class="col-xs-5 col-md-4" style="padding-left:6px">
											<label style="font-size:10px;font-weight:600;color:#64748b;margin-bottom:3px;display:block">
												<i class="fa-solid fa-dollar-sign" style="margin-right:3px"></i>Precio
											</label>
											<div class="input-group">
												<span class="input-group-addon egs-dollar">$</span>
												<input class="form-control precioPartidaGuardada" name="<?php echo $precioField; ?>" form="formObservaciones" type="number" value="<?php echo htmlspecialchars($precioVal); ?>" min="0" step="any" placeholder="0.00" style="font-weight:700"<?php echo $isReadonly ? ' readonly' : ''; ?>>
											</div>
										</div>
									</div>
								<?php endfor; ?>

								<!-- PARTIDAS JSON DINÁMICAS -->
								<?php
								if (is_array($partidas) || is_object($partidas)) {
									foreach ($partidas as $key => $itemDetallesPartidas) {
								?>
									<div class="form-group row egs-partida-row">
										<div class="col-xs-7 col-md-8" style="padding-right:6px">
											<label style="font-size:10px;font-weight:600;color:#64748b;margin-bottom:3px;display:block">
												<i class="fa-solid fa-plus-circle" style="margin-right:3px"></i>Partida adicional
											</label>
											<?php if ($isReadonly): ?>
												<textarea maxlength="320" rows="2" class="form-control text-uppercase NuevaPartidaAgregada" style="font-size:13px;resize:vertical" readonly><?php echo htmlspecialchars($itemDetallesPartidas["descripcion"]); ?></textarea>
											<?php else: ?>
												<div class="input-group">
													<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs quitarPartida"><i class="fas fa-times"></i></button></span>
													<textarea maxlength="320" rows="2" class="form-control text-uppercase NuevaPartidaAgregada" style="font-size:13px;resize:vertical"><?php echo htmlspecialchars($itemDetallesPartidas["descripcion"]); ?></textarea>
												</div>
											<?php endif; ?>
										</div>
										<div class="col-xs-5 col-md-4" style="padding-left:6px">
											<label style="font-size:10px;font-weight:600;color:#64748b;margin-bottom:3px;display:block">
												<i class="fa-solid fa-dollar-sign" style="margin-right:3px"></i>Precio
											</label>
											<div class="input-group">
												<span class="input-group-addon egs-dollar">$</span>
												<input class="form-control precioPartidaGuardada precioPartidaListada" type="number" value="<?php echo htmlspecialchars($itemDetallesPartidas["precioPartida"]); ?>" min="0" step="any" style="font-weight:700"<?php echo $isReadonly ? ' readonly' : ''; ?>>
												<input type="hidden" name="partidaYaListada">
											</div>
										</div>
									</div>
								<?php
									}
								}
								?>

								<!-- DISPLAY RECARGA EXISTENTE (readonly) -->
								<?php if ($recarga != ""): ?>
								<div class="form-group row egs-partida-row" style="border-color:#d4d4d8;background:#f5f5f4">
									<div class="col-xs-7 col-md-8" style="padding-right:6px">
										<label style="font-size:10px;font-weight:600;color:#78716c;margin-bottom:3px;display:block">
											<i class="fa-solid fa-tint" style="margin-right:3px"></i>Recarga de cartucho
										</label>
										<textarea maxlength="320" rows="2" class="form-control text-uppercase NuevaRecargaAgregada" style="font-size:13px;resize:vertical" readonly><?php echo htmlspecialchars($recarga); ?></textarea>
									</div>
									<div class="col-xs-5 col-md-4" style="padding-left:6px">
										<label style="font-size:10px;font-weight:600;color:#78716c;margin-bottom:3px;display:block">Precio</label>
										<div class="input-group">
											<span class="input-group-addon egs-dollar">$</span>
											<input class="form-control precioPartidaGuardada preciodeRecarganueva" type="number" value="<?php echo htmlspecialchars($precioRecarga); ?>" readonly style="font-weight:700">
										</div>
									</div>
								</div>
								<?php endif; ?>

								<!-- DISPLAY PARTIDAS TÉCNICO DOS (readonly) -->
								<?php
								if (!empty($partidasTecnicoDos) && (is_array($partidasTecnicoDos) || is_object($partidasTecnicoDos))) {
									foreach ($partidasTecnicoDos as $key => $valuePartidasTecnivosDos) {
								?>
								<div class="form-group row egs-partida-row" style="border-color:#bfdbfe;background:#eff6ff">
									<div class="col-xs-7 col-md-8" style="padding-right:6px">
										<label style="font-size:10px;font-weight:600;color:#3b82f6;margin-bottom:3px;display:block">
											<i class="fa-solid fa-user-plus" style="margin-right:3px"></i>Partida 2do técnico
										</label>
										<textarea maxlength="320" rows="2" class="form-control text-uppercase" style="font-size:13px;resize:vertical" readonly><?php echo htmlspecialchars($valuePartidasTecnivosDos["descripcion"]); ?></textarea>
									</div>
									<div class="col-xs-5 col-md-4" style="padding-left:6px">
										<label style="font-size:10px;font-weight:600;color:#3b82f6;margin-bottom:3px;display:block">Precio</label>
										<div class="input-group">
											<span class="input-group-addon egs-dollar">$</span>
											<input class="form-control precioPartidaGuardada" type="number" value="<?php echo htmlspecialchars($valuePartidasTecnivosDos["precioPartida"]); ?>" readonly style="font-weight:700">
										</div>
									</div>
								</div>
								<?php
									}
								}
								?>

								<!-- Containers dinámicos -->
								<div class="nuevaRecarga"></div>
								<div class="nuevaPartidaTecnicoDos">
									<input type="hidden" id="listarPartidasTecnicoDos" name="partidasTecnicoDos" form="formObservaciones">
									<input type="hidden" id="TotalPartidasTecnicoDos" name="TotalPartidasTecnicoDos" form="formObservaciones">
								</div>
								<div class="NuevaPartida"></div>

								<!-- INVERSIONES (admin only) -->
								<?php if ($isAdmin): ?>
								<div class="nuevaInversion"></div>
								<?php
								if (is_array($inversiones) || is_object($inversiones)) {
									foreach ($inversiones as $key => $valueinversiones) {
								?>
									<div class="form-group row egs-partida-row" style="border-color:#fde68a;background:#fffbeb">
										<div class="col-xs-6" style="padding-right:6px">
											<label style="font-size:10px;font-weight:600;color:#ca8a04;margin-bottom:3px;display:block">
												<i class="fa-solid fa-coins" style="margin-right:3px"></i>Detalle inversión
											</label>
											<div class="input-group">
												<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs quitarInversion"><i class="fas fa-times"></i></button></span>
												<input type="text" class="form-control detalleInversion" value="<?php echo htmlspecialchars($valueinversiones["observacion"]); ?>">
											</div>
										</div>
										<div class="col-xs-6" style="padding-left:6px">
											<label style="font-size:10px;font-weight:600;color:#ca8a04;margin-bottom:3px;display:block">Inversión</label>
											<div class="input-group">
												<span class="input-group-addon egs-dollar">$</span>
												<input type="number" class="form-control precioNuevainversion" value="<?php echo htmlspecialchars($valueinversiones["invsersion"]); ?>" min="0" step="any" style="font-weight:700">
											</div>
										</div>
									</div>
								<?php
									}
								}
								endif;
								?>

								<!-- TIPO DE REPARACIÓN (técnico) -->
								<?php if ($isTecnico): ?>
								<div style="margin:12px 0">
									<button type="button" class="btn btn-sm egs-btn-accent agregartipReparacion">Agregar tipo de reparación</button>
								</div>
								<div class="Tipo-de-reparacion" style="display:none;margin-bottom:12px">
									<select class="form-control Tipo-repearacion-selector" name="Tipo-repearacion" form="formObservaciones">
										<option value="sin tipo de reaparacion">Escoge el tipo de reparación</option>
										<option value="recarga-de-cartucho">Recarga de cartucho</option>
										<option value="servicio-externo">Servicio externo</option>
									</select>
								</div>
								<?php endif; ?>

								<!-- TIPO DE ORDEN (vendedor) -->
								<?php if ($isVendedor): ?>
								<div style="margin:12px 0">
									<button type="button" class="btn btn-sm egs-btn-accent agregartipReparacion">Agregar tipo de orden</button>
								</div>
								<div class="Tipo-de-orden" style="margin-bottom:12px">
									<select class="form-control Tipo-orden">
										<option value="sin tipo de orden">Escoge el tipo de orden</option>
										<option value="Seguimiento-de-venta">Seguimiento de venta</option>
									</select>
								</div>
								<?php endif; ?>

								<!-- HIDDEN FIELDS -->
								<input type="hidden" id="listatOrdenesNuevas" name="listatOrdenesNuevas" form="formObservaciones">
								<input type="hidden" id="listatOrdenes" name="listatOrdenes" form="formObservaciones">
								<input type="hidden" class="form-control" value="<?php echo htmlspecialchars($usuario["nombre"]); ?>" name="nombreCliente" form="formObservaciones" readonly>
								<input type="hidden" class="form-control" value="<?php echo htmlspecialchars($usuario["correo"]); ?>" name="correoCliente" form="formObservaciones" readonly>
								<input type="hidden" value="<?php echo htmlspecialchars($fecha_ingreso); ?>" name="fecha_ingreso" form="formObservaciones">
								<input type="hidden" value="<?php echo htmlspecialchars($_GET["idOrden"]); ?>" name="idOrden" form="formObservaciones">

								<!-- TOTAL -->
								<div class="egs-total-bar">
									<span class="egs-total-label"><i class="fa-solid fa-calculator" style="margin-right:6px"></i>Total</span>
									<div class="input-group">
										<span class="input-group-addon egs-dollar">$</span>
										<input type="number" class="form-control" id="costoTotalDeOrden" name="costoTotalDeOrden" form="formObservaciones" readonly style="font-weight:700;font-size:18px">
									</div>
								</div>

								<!-- TOTAL INVERSIONES -->
								<?php if ($isAdmin): ?>
								<div class="egs-inv-bar">
									<span class="egs-inv-label"><i class="fa-solid fa-coins" style="margin-right:6px"></i>Inversión total</span>
									<div class="input-group">
										<span class="input-group-addon egs-dollar">$</span>
										<input type="number" name="totalInversiones" class="form-control" id="costoTotalInversiones" form="formObservaciones" readonly style="font-weight:700;font-size:18px">
									</div>
								</div>
								<?php else: ?>
								<!-- Preservar inversiones cuando no es admin -->
								<input type="hidden" name="totalInversiones" value="<?php echo htmlspecialchars($value["totalInversion"]); ?>" form="formObservaciones">
								<?php endif; ?>

								<!-- BOTONES DE ACCIÓN -->
								<div style="margin-top:16px;display:flex;gap:8px;flex-wrap:wrap">
									<button type="button" class="btn egs-btn-accent AgregarCamposDePartida">
										<i class="fa-solid fa-plus" style="margin-right:4px"></i>Agregar Nueva Partida
									</button>
									<?php if ($isAdmin): ?>
									<button type="button" class="btn btn-success agregarInvercion">
										<i class="fas fa-money" style="margin-right:4px"></i>Agregar Inversión
									</button>
									<?php endif; ?>
								</div>

							</div><!-- /box -->

						</div><!-- /formularioPartidas -->
					</div>
				</div>
			</div><!-- /col-lg-8 partidas -->

			<!-- COLUMNA DERECHA: ASIGNACIÓN -->
			<div class="col-lg-4 col-xs-12">

				<!-- ASIGNACIÓN -->
				<div class="egs-section">
					<div class="egs-title-bar"><i class="fa-solid fa-users-gear"></i> Asignación</div>
					<div class="egs-body">

						<!-- ASESOR -->
						<div class="egs-field-row">
							<label class="egs-lbl">Asesor</label>
							<?php
							$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $_GET["asesor"]);
							if ($isReadonly) {
								echo '<div class="input-group"><span class="input-group-addon"><i class="fas fa-user-tie"></i></span>';
								echo '<input type="text" class="form-control" value="'.htmlspecialchars($asesor["nombre"]).'" readonly></div>';
								echo '<input type="hidden" value="'.$asesor["id"].'" name="asesorEditadoEnOrdenDianmica" form="formObservaciones">';
							} else {
								echo '<div class="input-group"><span class="input-group-addon"><i class="fas fa-user-tie"></i></span>';
								echo '<select class="form-control selector" name="asesorEditadoEnOrdenDianmica" form="formObservaciones" required>';
								echo '<option value="'.$asesor["id"].'">'.htmlspecialchars($asesor["nombre"]).'</option>';
								$asesorParaSelect = Controladorasesores::ctrMostrarAsesoresEmpresas("id_empresa", $_SESSION["empresa"]);
								foreach ($asesorParaSelect as $va) {
									echo '<option value="'.$va["id"].'" class="text-uppercase">'.htmlspecialchars($va["nombre"]).'</option>';
								}
								echo '</select></div>';
							}
							?>
						</div>

						<!-- TÉCNICO (En posesión) -->
						<div class="egs-field-row">
							<label class="egs-lbl">Técnico (En posesión)</label>
							<?php
							$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $_GET["tecnico"]);
							if ($isTecnico || $isSecretaria) {
								echo '<div class="input-group"><span class="input-group-addon"><i class="fas fa-screwdriver-wrench"></i></span>';
								echo '<input type="text" class="form-control" value="'.htmlspecialchars($tecnico["nombre"]).'" readonly></div>';
								echo '<input type="hidden" value="'.$tecnico["id"].'" name="tecnicoEditadoEnOrdenDianmica" form="formObservaciones">';
							} else {
								echo '<div class="input-group"><span class="input-group-addon"><i class="fas fa-screwdriver-wrench"></i></span>';
								echo '<select class="form-control selector" name="tecnicoEditadoEnOrdenDianmica" form="formObservaciones" required>';
								echo '<option value="'.$tecnico["id"].'">'.htmlspecialchars($tecnico["nombre"]).'</option>';
								$tecnicoList = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas("id_empresa", $_SESSION["empresa"]);
								foreach ($tecnicoList as $vt) {
									echo '<option value="'.$vt["id"].'" class="text-uppercase">'.htmlspecialchars($vt["nombre"]).'</option>';
								}
								echo '</select></div>';
							}
							?>
						</div>

						<!-- TÉCNICO (Participación) -->
						<div class="egs-field-row">
							<label class="egs-lbl">Técnico (Participación)</label>
							<?php
							$_tec2Id = isset($_GET["tecnicodos"]) ? $_GET["tecnicodos"] : '';
							$tecnico2 = null;
							if ($_tec2Id !== '' && $_tec2Id !== '0') {
								$tecnico2 = ControladorTecnicos::ctrMostrarTecnicos("id", $_tec2Id);
							}
							$_tec2Nombre = (is_array($tecnico2) && isset($tecnico2["nombre"])) ? $tecnico2["nombre"] : 'Sin asignar';
							$_tec2IdVal  = (is_array($tecnico2) && isset($tecnico2["id"])) ? $tecnico2["id"] : '';

							if ($isTecnico || $isSecretaria) {
								echo '<div class="input-group"><span class="input-group-addon"><i class="fas fa-user-plus"></i></span>';
								echo '<input type="text" class="form-control" value="'.htmlspecialchars($_tec2Nombre).'" readonly></div>';
								echo '<input type="hidden" value="'.htmlspecialchars($_tec2IdVal).'" name="tecnicodosEditadoEnOrdenDianmica" form="formObservaciones">';
							} else {
								echo '<div class="input-group"><span class="input-group-addon"><i class="fas fa-user-plus"></i></span>';
								echo '<select class="form-control selector" name="tecnicodosEditadoEnOrdenDianmica" form="formObservaciones">';
								if ($_tec2IdVal !== '') {
									echo '<option value="'.htmlspecialchars($_tec2IdVal).'">'.htmlspecialchars($_tec2Nombre).'</option>';
								} else {
									echo '<option value="">Sin asignar</option>';
								}
								$tecnico2List = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas("id_empresa", $_SESSION["empresa"]);
								foreach ($tecnico2List as $vt2) {
									if ($vt2["id"] != $_tec2IdVal) {
										echo '<option value="'.$vt2["id"].'" class="text-uppercase">'.htmlspecialchars($vt2["nombre"]).'</option>';
									}
								}
								echo '</select></div>';
							}
							?>
						</div>

						<!-- ESTADO DE LA ORDEN -->
						<div class="egs-field-row">
							<label class="egs-lbl">Estado</label>
							<span class="egs-estado-badge <?php echo _egsEstadoClass($estado); ?>" style="margin-bottom:8px"><?php echo htmlspecialchars($estado); ?></span>
							<?php
							if ($estado !== "Entregado (Ent)") {
								$allStates = array(
									'En revisión (REV)', 'Supervisión (SUP)', 'Pendiente de autorización (AUT',
									'Aceptado (ok)', 'Terminada (ter)', 'Cancelada (can)',
									'Sin reparación (SR)', 'Entregado (Ent)', 'Producto para venta',
									'En revisión probable garantía ', 'Garantía aceptada (GA)'
								);

								echo '<div class="input-group"><span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>';
								if ($isTecnico) {
									echo '<select class="form-control selector" name="estado" form="formObservaciones">';
									echo '<option>'.htmlspecialchars($estado).'</option>';
									if ($estado == 'En revisión (REV)') {
										echo '<option value="En revisión (REV)">En revisión (REV)</option>';
										echo '<option value="Supervisión (SUP)">Supervisión (SUP)</option>';
									} elseif ($estado == 'Aceptado (ok)') {
										echo '<option value="Aceptado (ok)">Aceptado (ok)</option>';
										echo '<option value="Terminada (ter)">Terminada (ter)</option>';
									}
									echo '</select>';
								} elseif ($isVendedor) {
									echo '<select class="form-control selector" name="estado" form="formObservaciones">';
									echo '<option>'.htmlspecialchars($estado).'</option>';
									if ($estado == 'En revisión (REV)') {
										echo '<option value="En revisión (REV)">En revisión (REV)</option><option value="Supervisión (SUP)">Supervisión (SUP)</option>';
									} elseif ($estado == 'Supervisión (SUP)') {
										echo '<option value="Supervisión (SUP)">Supervisión (SUP)</option><option value="Pendiente de autorización (AUT">Pendiente de autorización (AUT</option>';
									} elseif ($estado == 'Pendiente de autorización (AUT') {
										echo '<option value="Pendiente de autorización (AUT">Pendiente de autorización (AUT</option><option value="Aceptado (ok)">Aceptado (ok)</option>';
									} elseif ($estado == 'Aceptado (ok)') {
										echo '<option value="Aceptado (ok)">Aceptado (ok)</option><option value="Terminada (ter)">Terminada (ter)</option>';
									} elseif ($estado == 'Terminada (ter)') {
										echo '<option value="Terminada (ter)">Terminada (ter)</option>';
									} elseif ($estado == 'Cancelada (can)') {
										echo '<option value="Cancelada (can)">Cancelada (can)</option>';
									}
									echo '</select>';
								} elseif ($isAdmin) {
									echo '<select class="form-control selector" name="estado" form="formObservaciones">';
									echo '<option value="'.htmlspecialchars($estado).'">'.htmlspecialchars($estado).'</option>';
									foreach ($allStates as $st) {
										if ($st !== $estado) {
											echo '<option value="'.htmlspecialchars($st).'">'.htmlspecialchars($st).'</option>';
										}
									}
									echo '</select>';
								} else {
									echo '<select class="form-control selector" name="estado" form="formObservaciones">';
									echo '<option>'.htmlspecialchars($estado).'</option>';
									echo '</select>';
								}
								echo '</div>';
							} else {
								echo '<input type="hidden" name="estado" value="Entregado (Ent)" form="formObservaciones">';
								echo '<div style="text-align:center;padding:8px"><h4 style="color:#16a34a;margin:0"><i class="fa-solid fa-circle-check"></i> ENTREGADO EL: '.htmlspecialchars($fecha_Salida).'</h4></div>';
							}
							?>
						</div>

					</div>
				</div>

			</div><!-- /col-lg-4 asignacion -->

		</div><!-- /row 2 -->

		<!-- ==================== PEDIDOS ==================== -->
		<?php
		$item = "id";
		$valor = $_GET["pedido"];
		$tarerPedido = ControladorPedidos::ctrMostrarPedido($item, $valor);
		foreach ($tarerPedido as $key => $valuePedidos) {}

		if ($valuePedidos["productoUno"] != null and $valuePedidos["productoUno"]) {
			echo '<div class="row"><div class="col-lg-12"><div class="egs-section"><div class="egs-title-bar"><i class="fa-solid fa-box"></i> Pedido</div><div class="egs-body"><ul class="todo-list" style="list-style:none;padding:0">';
			foreach ($tarerPedido as $key => $valuePedidos) {
				echo '<li id="'.$valuePedidos["id"].'">
					<div class="box-group" id="accordion">
						<div class="panel box box-info" style="margin-bottom:8px">
							<div class="box-header with-border">
								<h4 class="box-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$valuePedidos["id"].'"><p class="text-uppercase">PEDIDO '.$valuePedidos["id"].'</p></a></h4>
								<div id="collapse'.$valuePedidos["id"].'" class="panel-collapse collapse">
									<center><h3>'.$valuePedidos["productoUno"].'</h3></center>';
				if ($isAdmin || $isVendedor || $_SESSION["perfil"] == "editor") {
					echo '<select class="form-control" name="EstadoPedidoDinamico">
						<option>'.$valuePedidos["estado"].'</option>
						<option value="Pedido Pendiente">Pedido Pendiente</option>
						<option value="Pedido Adquirido">Pedido Adquirido</option>
						<option value="Producto en Almacen">Producto en Almacén</option>
						<option value="Entregado al asesor">Entregado al Asesor</option>
						<option value="Entregado/Pagado">Entregado/Pagado</option>
						<option value="Entregado/Credito">Entregado/Crédito</option>
						<option value="cancelado">cancelado</option>
					</select>';
				} else {
					echo "<input type='text' class='form-control' value='".$valuePedidos["estado"]."' readonly>";
				}
				echo '</div></div></div></div></li>';
			}
			echo '</ul></div></div></div></div>';
		}

		$productosPedidoDinamico = json_decode($valuePedidos["productos"], true);
		if (is_array($productosPedidoDinamico) || is_object($productosPedidoDinamico)) {
			foreach ($productosPedidoDinamico as $key => $valueProductosPedido) {}
		}

		if ($productosPedidoDinamico != null and $productosPedidoDinamico != "") {
			echo '<div class="row"><div class="col-lg-12"><div class="egs-section"><div class="egs-title-bar"><i class="fa-solid fa-boxes-stacked"></i> Pedido (productos)</div><div class="egs-body"><ul class="todo-list" style="list-style:none;padding:0">';
			foreach ($tarerPedido as $key => $valuePedidos) {
				echo '<li id="'.$valuePedidos["id"].'">
					<div class="box-group" id="accordion">
						<div class="panel box box-info" style="margin-bottom:8px">
							<div class="box-header with-border">
								<h4 class="box-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$valuePedidos["id"].'"><p class="text-uppercase">PEDIDO '.$valuePedidos["id"].'</p></a>
								<input type="hidden" value="'.$valuePedidos["id"].'" name="idPeido"></h4>
								<div id="collapse'.$valuePedidos["id"].'" class="panel-collapse collapse">';
				$productosPedidoDinamico = json_decode($valuePedidos["productos"], true);
				foreach ($productosPedidoDinamico as $key => $valueProductosPedido) {
					echo '<div class="form-group row egs-partida-row">
						<div class="col-xs-6"><div class="input-group"><span class="input-group-addon"><i class="fas fa-product-hunt"></i></span><input type="text" class="form-control" value="'.$valueProductosPedido["Descripcion"].'" readonly></div></div>
						<div class="col-xs-2"><div class="input-group"><span class="input-group-addon"><i class="fas fa-cubes"></i></span><input type="text" class="form-control" value="'.$valueProductosPedido["cantidad"].'" readonly></div></div>
						<div class="col-xs-4"><div class="input-group"><span class="input-group-addon egs-dollar">$</span><input type="text" class="form-control" value="'.$valueProductosPedido["precio"].'" readonly></div></div>
					</div>';
				}
				if ($isAdmin || $isVendedor || $_SESSION["perfil"] == "editor") {
					echo '<div class="form-group"><select class="form-control" name="EdicionUnicaDeEstadoDePedidoEnOrden">
						<option>'.$valuePedidos["estado"].'</option>
						<option value="Pedido Pendiente">Pedido Pendiente</option>
						<option value="Pedido Adquirido">Pedido Adquirido</option>
						<option value="Producto en Almacen">Producto en Almacén</option>
						<option value="Entregado al asesor">Entregado al Asesor</option>
						<option value="Entregado/Pagado">Entregado/Pagado</option>
						<option value="Entregado/Credito">Entregado/Crédito</option>
						<option value="cancelado">cancelado</option>
					</select></div>';
				} else {
					echo "<input type='text' class='form-control' value='".$valuePedidos["estado"]."' readonly>";
				}
				echo '</div></div></div></div></li>';
			}
			echo '</ul></div></div></div></div>';
		}
		?>

		<!-- ==================== FILA 3: OBSERVACIONES (ancho completo) ==================== -->
		<div class="row">
			<div class="col-lg-12">
				<div class="egs-section">
					<div class="egs-title-bar"><i class="fa-solid fa-comments"></i> Observaciones y detalles internos</div>
					<div class="egs-body">
						<form role="form" method="post" class="formularioObervaciones" id="formObservaciones">

							<!-- Detalles internos -->
							<div class="egs-field-row">
								<label class="egs-lbl"><i class="fa-solid fa-edit" style="margin-right:4px"></i>Detalles internos</label>
								<?php
								echo '<textarea class="form-control text-uppercase" style="font-weight:bold;min-height:80px" name="observaciones">'.htmlspecialchars($descripcion).'</textarea>';
								?>
							</div>

							<!-- OBSERVACIONES JSON EXISTENTES -->
							<?php
							if (is_array($observaciones) || is_object($observaciones)) {
								foreach ($observaciones as $key => $valueObservaciones) {
									if ($isReadonly) {
										echo '<div class="egs-obs-item">
											<strong style="color:#6366f1">'.htmlspecialchars($valueObservaciones["creador"]).'</strong>
											<textarea class="form-control text-uppercase nuevaObservacion" style="font-weight:bold;margin-top:6px" readonly>'.htmlspecialchars($valueObservaciones["observacion"]).'</textarea>
										</div>';
									} else {
										echo '<div class="egs-obs-item" style="position:relative">
											<strong style="color:#6366f1">'.htmlspecialchars($valueObservaciones["creador"]).'</strong>
											<button type="button" class="btn btn-danger btn-xs quitarObservacion" style="float:right"><i class="fas fa-times"></i></button>
											<textarea class="form-control text-uppercase nuevaObservacion" style="font-weight:bold;margin-top:6px">'.htmlspecialchars($valueObservaciones["observacion"]).'</textarea>
										</div>';
									}
								}
							}
							?>

							<div class="NuevaObserva">
								<?php echo '<input type="hidden" class="usuarioQueCaptura" value="'.$_SESSION["nombre"].'" name="usuarioQueCaptura">'; ?>
								<input type="hidden" class="form-control" id="fechaVista">
								<input type="hidden" id="listarObservaciones" name="listarObservaciones">
								<?php if ($isAdmin): ?>
								<input type="hidden" name="listarinversiones" id="listarinversiones">
								<?php else: ?>
								<!-- Sin id= para que el JS no lo encuentre y sobreescriba -->
								<input type="hidden" name="listarinversiones" value="<?php echo htmlspecialchars($value["inversiones"]); ?>">
								<?php endif; ?>
							</div>

							<!-- OBSERVACIONES DE LA ORDEN (tabla observacionesOrdenes) -->
							<?php
							$_obs_grads = array(
								'linear-gradient(135deg,#6366f1,#818cf8)',
								'linear-gradient(135deg,#3b82f6,#60a5fa)',
								'linear-gradient(135deg,#8b5cf6,#a78bfa)',
								'linear-gradient(135deg,#06b6d4,#22d3ee)',
								'linear-gradient(135deg,#22c55e,#4ade80)',
								'linear-gradient(135deg,#f59e0b,#fbbf24)',
							);
							if (!function_exists('_obsColorPerfil')) { function _obsColorPerfil($perfil) {
								$p = strtolower($perfil);
								if (strpos($p, 'admin') !== false)    return array('#6366f1', '#eef2ff', 'fa-shield-halved');
								if (strpos($p, 'vendedor') !== false || strpos($p, 'asesor') !== false) return array('#8b5cf6', '#f5f3ff', 'fa-headset');
								if (strpos($p, 'tecnico') !== false || strpos($p, 'técnico') !== false) return array('#06b6d4', '#ecfeff', 'fa-wrench');
								if (strpos($p, 'secretaria') !== false) return array('#f59e0b', '#fffbeb', 'fa-clipboard');
								return array('#64748b', '#f1f5f9', 'fa-user');
							} }

							$itemobs = $_GET["idOrden"];
							$observacionesnew = controladorObservaciones::ctrMostrarobservaciones($itemobs);
							$_obs_count = is_array($observacionesnew) ? count($observacionesnew) : 0;
							?>

							<div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;margin-bottom:10px">
								<span style="font-size:13px;font-weight:700;color:#0f172a">
									<i class="fa-solid fa-clock-rotate-left" style="color:#6366f1;margin-right:6px"></i>Historial de observaciones
								</span>
								<span style="font-size:11px;font-weight:600;color:#6366f1;background:#eef2ff;padding:3px 10px;border-radius:20px">
									<?php echo $_obs_count; ?> registrada<?php echo $_obs_count !== 1 ? 's' : ''; ?>
								</span>
							</div>

							<div class="egs-observaciones-list">
							<?php
							if (!empty($observacionesnew)) {
								foreach ($observacionesnew as $_oi => $valueobs) {
									$idadmin = $valueobs["id_creador"];
									$infouser = controladorObservaciones::ctrMostrarInfoUser($idadmin);
									$infoiduser = $infouser[0];
									$date = strtotime($valueobs["fecha"]);
									$fecha = date("d/m/Y H:i", $date);
									$_obs_nombre = htmlspecialchars($infoiduser["nombre"]);
									$_obs_foto = $infoiduser["foto"];
									$_obs_perfil = isset($infoiduser["perfil"]) ? $infoiduser["perfil"] : '';
									$_obs_initial = mb_strtoupper(mb_substr($_obs_nombre, 0, 1));
									$_obs_grad = $_obs_grads[$_oi % count($_obs_grads)];
									$_obs_col = _obsColorPerfil($_obs_perfil);
									$obsTexto = trim($valueobs["observacion"]);
									$isDigitalForm = (strpos($obsTexto, 'FORMULARIO_TABLET_JSON:') === 0);
									if($isDigitalForm) {
										$_obs_nombre = 'CLIENTE ORIGINAL';
										$_obs_perfil = 'Titular de la orden';
										$_obs_col = ['#16a34a', '#dcfce7', 'fa-user-check'];
									}
							?>
								<div class="egs-obs-item" style="display:flex;gap:12px;padding:14px;border:1px solid #f1f5f9;border-radius:10px;margin-bottom:8px;transition:background .12s"
									 onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
									<?php if (!empty($_obs_foto)): ?>
										<img src="<?php echo $_obs_foto; ?>" alt=""
											 onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex'"
											 style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #e2e8f0;flex-shrink:0">
										<div style="display:none;width:40px;height:40px;border-radius:50%;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;background:<?php echo $_obs_grad; ?>">
											<?php echo $_obs_initial; ?>
										</div>
									<?php else: ?>
										<div style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;background:<?php echo $_obs_grad; ?>">
											<?php echo $_obs_initial; ?>
										</div>
									<?php endif; ?>
									<div style="flex:1;min-width:0">
										<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap">
											<span style="font-size:13px;font-weight:700;color:#0f172a"><?php echo $_obs_nombre; ?></span>
											<?php if (!empty($_obs_perfil)): ?>
											<span style="display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:600;color:<?php echo $_obs_col[0]; ?>;background:<?php echo $_obs_col[1]; ?>;padding:2px 8px;border-radius:8px">
												<i class="fa-solid <?php echo $_obs_col[2]; ?>" style="font-size:8px"></i>
												<?php echo htmlspecialchars(ucfirst($_obs_perfil)); ?>
											</span>
											<?php endif; ?>
											<span style="font-size:11px;color:#94a3b8;margin-left:auto;flex-shrink:0">
												<i class="fa-regular fa-clock" style="font-size:9px"></i> <?php echo $fecha; ?>
											</span>
										</div>
										<?php
										$obsTexto = trim($valueobs["observacion"]);
										if (strpos($obsTexto, 'FORMULARIO_TABLET_JSON:') === 0) {
											$jsonStr = substr($obsTexto, 24); // 23 chars for prefix + 1 space
											$formData = json_decode($jsonStr, true);
											if(is_array($formData)) {
												echo '<div style="background:#fff;border:2px dashed #cbd5e1;border-radius:10px;padding:16px;margin-top:10px;box-shadow:inset 0 0 10px rgba(0,0,0,0.02)">';
												echo '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">';
												echo '<h4 style="margin:0;color:#4f46e5;font-weight:800;font-size:15px;display:flex;align-items:center;gap:6px"><i class="fa-solid fa-tablet-screen-button"></i> ' . htmlspecialchars($formData["tipo_formulario"]) . '</h4>';
												echo '<a href="extensiones/tcpdf/pdf/imprimirFormulario.php?idOrden=' . urlencode($_GET["idOrden"]) . '&idObs=' . urlencode($valueobs["id"]) . '" target="_blank" class="btn btn-sm btn-default" style="border:1px solid #cbd5e1;color:#475569;font-weight:600;font-size:11px;border-radius:6px" title="Imprimir Formato en Papel"><i class="fa-solid fa-print"></i> Imprimir</a>';
												echo '</div>';
												echo '<p style="font-size:13px;color:#475569;margin-bottom:12px;font-weight:600">Equipo: <span style="color:#0f172a">' . htmlspecialchars($formData["marcaModelo"]) . '</span></p>';
												
												echo '<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:10px;margin-bottom:16px">';
												foreach($formData["respuestas"] as $pregunta => $respuesta) {
													$preguntaFormateada = str_replace("_", " ", $pregunta);
													echo '<div style="background:#f8fafc;padding:10px;border-radius:6px;border:1px solid #e2e8f0">';
													echo '<label style="display:block;font-size:10px;color:#64748b;text-transform:uppercase;margin-bottom:4px;font-weight:700">' . htmlspecialchars($preguntaFormateada) . '</label>';
													echo '<span style="font-size:13px;color:#1e293b;font-weight:600">' . htmlspecialchars($respuesta) . '</span>';
													echo '</div>';
												}
												echo '</div>';
												
												if(isset($formData["firma"]) && !empty($formData["firma"])) {
													echo '<div style="border-top:1px solid #e2e8f0;padding-top:12px;text-align:center;width:max-content;margin:0 auto">';
													echo '<img src="' . htmlspecialchars($formData["firma"]) . '" style="max-width:250px;height:auto;display:block;margin:0 auto;filter:contrast(1.2)">';
													echo '<div style="border-top:2px solid #000;padding-top:4px;font-size:12px;font-weight:700;color:#000;margin-top:-10px;position:relative;z-index:2;letter-spacing:1px">FIRMA DIGITAL DEL CLIENTE</div>';
													echo '</div>';
												}
												echo '</div>';
											} else {
												echo '<p style="margin:0;color:#334155;font-weight:500;font-size:13px;line-height:1.5">' . htmlspecialchars($obsTexto) . '</p>';
											}
										} else {
											echo '<p style="margin:0;color:#334155;text-transform:uppercase;font-weight:500;font-size:13px;line-height:1.5">' . htmlspecialchars($obsTexto) . '</p>';
										}
										?>
										<?php if ($isAdmin): ?>
										<button type="button" class="btn btn-xs eliminarObservacion" idObs="<?php echo $valueobs["id"]; ?>"
												style="margin-top:6px;color:#ef4444;font-size:11px;padding:2px 8px;border:1px solid #fecaca;border-radius:6px;background:#fff"
												onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='#fff'">
											<i class="fas fa-trash" style="margin-right:3px"></i>Eliminar
										</button>
										<?php endif; ?>
									</div>
								</div>
							<?php
								}
							} else {
							?>
								<div style="text-align:center;padding:30px 16px;color:#94a3b8">
									<i class="fa-solid fa-comment-slash" style="font-size:28px;display:block;margin-bottom:10px;opacity:.4"></i>
									<span style="font-size:13px">Sin observaciones registradas para esta orden</span>
								</div>
							<?php } ?>
							</div>

							<!-- Botón agregar observación + agendar cita -->
							<div style="margin-top:16px;display:flex;gap:8px;align-items:center;flex-wrap:wrap">
								<button type="button" class="btn egs-btn-accent" data-toggle="modal" data-target="#exampleModal">
									<i class="fa-solid fa-plus" style="margin-right:4px"></i>Agregar observación
								</button>
								<?php if (!$isTecnico): ?>
								<button type="button" class="btn btnAgendarCitaDesdeOrden" data-orden-id="<?php echo intval($_GET["idOrden"]); ?>" style="background:#6366f1;color:#fff;border-color:#6366f1;font-weight:600;border-radius:8px;">
									<i class="fa-solid fa-calendar-plus" style="margin-right:4px"></i>Nueva Cita
								</button>
								<?php endif; ?>
							</div>

							<hr style="margin:20px 0 12px">
							<div style="display:flex;align-items:center;justify-content:space-between">
								<span style="color:red;font-weight:600" id="spanboton"></span>
								<button type="submit" class="btn egs-btn-accent btn-lg" id="btncompletarorden" style="min-width:160px">
									<i class="fa-solid fa-floppy-disk" style="margin-right:6px"></i>Guardar
								</button>
							</div>

							<?php
							$observacionesDePartidas = new controladorOrdenes();
							$observacionesDePartidas->ctrEditarObservacionesYaExistentes();

							$objetoUno = new controladorOrdenes();
							$objetoUno->ctrEditarOrdenDinamica();

							$objetoDos = new controladorOrdenes();
							$objetoDos->ctrEditarInversiones();

							$editarEstadoOrden = new ControladorPedidos();
							$editarEstadoOrden->ctrEditarPedidoEnEstado();

							$ingresarTipoDeOrden = new controladorOrdenes();
							$ingresarTipoDeOrden->ctrlAgregarTipoDeOrden();

							$ingresarRecarga = new controladorOrdenes();
							$ingresarRecarga->ctrlAgregarRecargaDeCartucho();

							$ingresarPartidasTecnicoDos = new controladorOrdenes();
							$ingresarPartidasTecnicoDos->ctrlAgregarNuevaPartidaTecnicoDos();

							$ingresarTecnicoDos = new controladorOrdenes();
							$ingresarTecnicoDos->ctrlAgregarTecnicoDos();

							$marca = new controladorOrdenes();
							$marca->ctrlEditarMarca();
							?>

						</form>
					</div>
				</div>
			</div>
		</div><!-- /row 3 observaciones -->

		<!-- Modal Observación -->
		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="Observacion" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content" style="border-radius:12px;overflow:hidden">
					<div class="modal-header" style="background:linear-gradient(135deg,#6366f1 0%,#818cf8 100%);color:#fff;border:none">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:.8">
							<span>&times;</span>
						</button>
						<h4 class="modal-title" style="font-weight:600"><i class="fa-solid fa-comment-dots" style="margin-right:8px"></i>Nueva observación</h4>
					</div>
					<form method="post" class="observacion" id="formObservacion">
						<div class="modal-body" style="padding:20px">
							<label class="egs-lbl egs-req">Observación</label>
							<textarea name="observacion" class="form-control text-uppercase" style="font-weight:bold;min-height:100px" placeholder="Escribe tu observación" required></textarea>
							<input name="id_orden" type="hidden" value="<?php echo $_GET["idOrden"]; ?>">
							<input name="id_creador" type="hidden" value="<?php echo $_SESSION["id"]; ?>">
							<input name="_obs_token" type="hidden" value="<?php echo bin2hex(random_bytes(16)); ?>">
						</div>
						<div class="modal-footer" style="border-top:1px solid #f1f5f9">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
							<button type="submit" class="btn egs-btn-accent" id="btnGuardarObs">
								<i class="fa-solid fa-paper-plane" style="margin-right:4px"></i>Guardar observación
							</button>
						</div>
					</form>
					<script>
					(function(){
						var form = document.getElementById('formObservacion');
						if (form) {
							form.addEventListener('submit', function(){
								var btn = document.getElementById('btnGuardarObs');
								if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...'; }
							});
						}
					})();
					</script>
				</div>
			</div>
		</div>

	</section>

</div><!-- /content-wrapper -->

<script>
var _egsIsAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;
$(document).ready(function () {
	if ($("#botonwhats").val() && $("#botonwhats").val().length < 10) {
		$('#linkwhats').hide();
	}
});
</script>

<!-- AJAX Polling: actualización en tiempo real cada 45s -->
<script>
$(document).ready(function(){
	var ordenId = <?php echo json_encode($_GET["idOrden"]); ?>;
	var lastEstado = null;
	var lastObsCount = <?php echo isset($_obs_count) ? intval($_obs_count) : 0; ?>;

	function pollInfoOrden(){
		$.ajax({
			url: "ajax/infoOrden.ajax.php",
			method: "POST",
			data: { pollInfoOrden: 1, idOrden: ordenId },
			dataType: "json",
			success: function(res){
				if(res.error) return;
				if(lastEstado !== null && res.estado !== lastEstado){
					var badge = $(".egs-estado-badge");
					if(badge.length){
						badge.text(res.estado).css("animation","pulse .6s");
						// Actualizar clase de color
						badge.removeClass("egs-estado-revision egs-estado-supervision egs-estado-pendiente egs-estado-aceptado egs-estado-terminada egs-estado-cancelada egs-estado-sin-reparacion egs-estado-entregado egs-estado-garantia egs-estado-producto-venta egs-estado-default");
						var e = res.estado.toLowerCase();
						var cls = "egs-estado-default";
						if(e.indexOf("autorización")>-1||e.indexOf("autorizacion")>-1||e.indexOf("pendiente")>-1) cls="egs-estado-pendiente";
						else if(e.indexOf("supervisión")>-1||e.indexOf("supervision")>-1) cls="egs-estado-supervision";
						else if(e.indexOf("garantía")>-1||e.indexOf("garantia")>-1) cls="egs-estado-garantia";
						else if(e.indexOf("revisión")>-1||e.indexOf("revision")>-1) cls="egs-estado-revision";
						else if(e.indexOf("terminada")>-1) cls="egs-estado-terminada";
						else if(e.indexOf("entregado")>-1||e.indexOf("entregada")>-1) cls="egs-estado-entregado";
						else if(e.indexOf("aceptado")>-1||e.indexOf("aceptada")>-1) cls="egs-estado-aceptado";
						else if(e.indexOf("cancel")>-1) cls="egs-estado-cancelada";
						else if(e.indexOf("sin reparación")>-1||e.indexOf("sin reparacion")>-1) cls="egs-estado-sin-reparacion";
						else if(e.indexOf("producto para venta")>-1) cls="egs-estado-producto-venta";
						badge.addClass(cls);
					}
				}
				lastEstado = res.estado;
				if(res.observaciones && res.observaciones.length > lastObsCount){
					var container = $(".egs-observaciones-list");
					if(container.length){
						var grads = ['linear-gradient(135deg,#6366f1,#818cf8)','linear-gradient(135deg,#3b82f6,#60a5fa)','linear-gradient(135deg,#8b5cf6,#a78bfa)','linear-gradient(135deg,#06b6d4,#22d3ee)','linear-gradient(135deg,#22c55e,#4ade80)','linear-gradient(135deg,#f59e0b,#fbbf24)'];
						// Limpiar estado vacío si existe
						container.find('.egs-obs-empty').remove();
						for(var i = lastObsCount; i < res.observaciones.length; i++){
							var obs = res.observaciones[i];
							var nombre = obs.nombre || 'Usuario';
							var ini = nombre.charAt(0).toUpperCase();
							var grad = grads[i % grads.length];
							var fecha = obs.fecha ? new Date(obs.fecha) : null;
							var fechaStr = fecha ? (fecha.getDate()+'/'+(fecha.getMonth()+1)+'/'+fecha.getFullYear()+' '+fecha.getHours()+':'+String(fecha.getMinutes()).padStart(2,'0')) : '';
							// Determinar color/icono según perfil
							var perfil = obs.perfil || '';
							var pLow = perfil.toLowerCase();
							var pColor = '#64748b', pBg = '#f1f5f9', pIcon = 'fa-user';
							if(pLow.indexOf('admin')>-1){ pColor='#6366f1'; pBg='#eef2ff'; pIcon='fa-shield-halved'; }
							else if(pLow.indexOf('vendedor')>-1||pLow.indexOf('asesor')>-1){ pColor='#8b5cf6'; pBg='#f5f3ff'; pIcon='fa-headset'; }
							else if(pLow.indexOf('tecnico')>-1||pLow.indexOf('técnico')>-1){ pColor='#06b6d4'; pBg='#ecfeff'; pIcon='fa-wrench'; }
							else if(pLow.indexOf('secretaria')>-1){ pColor='#f59e0b'; pBg='#fffbeb'; pIcon='fa-clipboard'; }

							var html = '<div class="egs-obs-item" style="display:flex;gap:12px;padding:14px;border:1px solid #f1f5f9;border-radius:10px;margin-bottom:8px;transition:background .12s" onmouseover="this.style.background=\'#f8fafc\'" onmouseout="this.style.background=\'#fff\'">';
							if(obs.foto){
								html += '<img src="'+obs.foto+'" style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #e2e8f0;flex-shrink:0" onerror="this.onerror=null;this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\'">';
								html += '<div style="display:none;width:40px;height:40px;border-radius:50%;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;background:'+grad+'">'+ini+'</div>';
							} else {
								html += '<div style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;background:'+grad+'">'+ini+'</div>';
							}
							let tx = obs.observacion || '';
							let isDigForm = tx.startsWith("FORMULARIO_TABLET_JSON:");
							if (isDigForm) {
								nombre = 'CLIENTE ORIGINAL';
								perfil = 'Titular de la orden';
								pColor = '#16a34a';
								pBg = '#dcfce7';
								pIcon = 'fa-user-check';
							}
							html += '<div style="flex:1;min-width:0">';
							html += '<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap">';
							html += '<span style="font-size:13px;font-weight:700;color:#0f172a">'+nombre+'</span>';
							if(perfil){
								html += '<span style="display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:600;color:'+pColor+';background:'+pBg+';padding:2px 8px;border-radius:8px"><i class="fa-solid '+pIcon+'" style="font-size:8px"></i>'+perfil.charAt(0).toUpperCase()+perfil.slice(1)+'</span>';
							}
							html += '<span style="font-size:11px;color:#94a3b8;margin-left:auto;flex-shrink:0"><i class="fa-regular fa-clock" style="font-size:9px"></i> '+fechaStr+'</span>';
							html += '</div>';
							let tx = obs.observacion || '';
							if(tx.startsWith("FORMULARIO_TABLET_JSON:")) {
								try {
									let jsData = JSON.parse(tx.substring(24));
									let cardHtml = '<div style="background:#fff;border:2px dashed #cbd5e1;border-radius:10px;padding:16px;margin-top:10px;box-shadow:inset 0 0 10px rgba(0,0,0,0.02)">';
									cardHtml += '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">';
									cardHtml += '<h4 style="margin:0;color:#4f46e5;font-weight:800;font-size:15px;display:flex;align-items:center;gap:6px"><i class="fa-solid fa-tablet-screen-button"></i> ' + jsData.tipo_formulario + '</h4>';
									cardHtml += '<a href="extensiones/tcpdf/pdf/imprimirFormulario.php?idOrden=' + encodeURIComponent(obs.id_orden) + '&idObs=' + encodeURIComponent(obs.id) + '" target="_blank" class="btn btn-sm btn-default" style="border:1px solid #cbd5e1;color:#475569;font-weight:600;font-size:11px;border-radius:6px" title="Imprimir Formato en Papel"><i class="fa-solid fa-print"></i> Imprimir</a>';
									cardHtml += '</div>';
									cardHtml += '<p style="font-size:13px;color:#475569;margin-bottom:12px;font-weight:600">Equipo: <span style="color:#0f172a">' + (jsData.marcaModelo || '') + '</span></p>';
									cardHtml += '<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:10px;margin-bottom:16px">';
									for(let key in jsData.respuestas) {
										let labelForm = key.replace(/_/g, " ");
										cardHtml += '<div style="background:#f8fafc;padding:10px;border-radius:6px;border:1px solid #e2e8f0">';
										cardHtml += '<label style="display:block;font-size:10px;color:#64748b;text-transform:uppercase;margin-bottom:4px;font-weight:700">' + labelForm + '</label>';
										cardHtml += '<span style="font-size:13px;color:#1e293b;font-weight:600">' + jsData.respuestas[key] + '</span>';
										cardHtml += '</div>';
									}
									cardHtml += '</div>';
									if(jsData.firma) {
										cardHtml += '<div style="border-top:1px solid #e2e8f0;padding-top:12px;text-align:center;width:max-content;margin:0 auto">';
										cardHtml += '<img src="' + jsData.firma + '" style="max-width:250px;height:auto;display:block;margin:0 auto;filter:contrast(1.2)">';
										cardHtml += '<div style="border-top:2px solid #000;padding-top:4px;font-size:12px;font-weight:700;color:#000;margin-top:-10px;position:relative;z-index:2;letter-spacing:1px">FIRMA DIGITAL DEL CLIENTE</div>';
										cardHtml += '</div>';
									}
									cardHtml += '</div>';
									html += cardHtml;
								}catch(e){
									html += '<p style="margin:0;color:#334155;text-transform:uppercase;font-weight:500;font-size:13px;line-height:1.5">' + tx + '</p>';
								}
							} else {
								html += '<p style="margin:0;color:#334155;text-transform:uppercase;font-weight:500;font-size:13px;line-height:1.5">' + tx + '</p>';
							}
							html += '</div></div>';
							container.append(html);
						}
					}
					lastObsCount = res.observaciones.length;
				}
			}
		});
	}
	setInterval(pollInfoOrden, 45000);
	pollInfoOrden();
});
</script>

<script>
function _egsCopiarTel(btn, numero) {
	var icon = btn.querySelector('i');
	var texto = numero.trim();
	if (!texto) return;

	function _feedback() {
		icon.className = 'fas fa-check';
		btn.style.color = '#22c55e';
		setTimeout(function() {
			icon.className = 'fas fa-copy';
			btn.style.color = '';
		}, 1800);
	}

	if (navigator.clipboard && navigator.clipboard.writeText) {
		navigator.clipboard.writeText(texto).then(_feedback).catch(function() {
			_egsCopiarFallback(texto, _feedback);
		});
	} else {
		_egsCopiarFallback(texto, _feedback);
	}
}

function _egsCopiarFallback(texto, cb) {
	var ta = document.createElement('textarea');
	ta.value = texto;
	ta.style.cssText = 'position:fixed;top:-999px;left:-999px;opacity:0';
	document.body.appendChild(ta);
	ta.focus();
	ta.select();
	try { document.execCommand('copy'); cb(); } catch(e) {}
	document.body.removeChild(ta);
}
</script>

<!-- Handler: Agendar cita desde orden (abre el modal de cita rápida del navbar) -->
<script>
$(document).on('click', '.btnAgendarCitaDesdeOrden', function(){
	var ordenId = $(this).data('orden-id');
	var $modal = $('#modalCitaRapida');
	if (!$modal.length) return;
	// Prellenar el ID de orden, disparar auto-color y bloquear edición
	var $campo = $('#crOrdenId');
	$campo.val(ordenId).trigger('change');
	// Deshabilitar visualmente pero guardar valor para el submit
	$campo.prop('disabled', true).data('orden-forzada', ordenId);
	$modal.modal('show');
});
// Al cerrar el modal, restaurar el campo por si lo abren desde otro lugar
$(document).on('hidden.bs.modal', '#modalCitaRapida', function(){
	var $campo = $('#crOrdenId');
	$campo.prop('disabled', false).removeData('orden-forzada');
});
</script>

<?php
$insertarobservacion = new controladorObservaciones();
$insertarobservacion->ctrlCrearObservacion();
?>
