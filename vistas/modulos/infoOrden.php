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
	.egs-dollar { background: #6366f1; color: #fff; border-color: #6366f1; font-weight: 700; }
	.egs-estado-badge { display: inline-block; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; background: #e0e7ff; color: #4338ca; }

	/* Carousel */
	.img-orden-standard { width: 100%; height: 400px; object-fit: cover; background-color: #f4f4f4; cursor: pointer; border-radius: 8px; }
	.orden-carousel .carousel-inner { height: 400px !important; }
	.imagepreview { transform-origin: center center; cursor: grab; }
	.imagepreview:active { cursor: grabbing; }
	.modal-body { overflow: hidden; }
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

<script>
$(document).ready(function () {
	var currentScale = 1, isDragging = false, startX, startY, translateX = 0, translateY = 0;
	function updateTransform(t) {
		$(".imagepreview").css("transition", t ? "transform 0.25s ease" : "none")
			.css("transform", "translate("+translateX+"px, "+translateY+"px) scale("+currentScale+")");
	}
	$(".img-orden-standard").on("click", function () {
		currentScale = 1; translateX = 0; translateY = 0;
		$(".imagepreview").attr("src", $(this).attr("src"));
		updateTransform(false);
		$("#imagemodal").modal("show");
	});
	$("#zoomIn").on("click", function() { currentScale += 0.2; updateTransform(true); });
	$("#zoomOut").on("click", function() { if (currentScale > 0.4) { currentScale -= 0.2; updateTransform(true); } });
	$(".imagepreview").on("mousedown", function(e) {
		if (currentScale > 1) { isDragging = true; startX = e.clientX - translateX; startY = e.clientY - translateY; e.preventDefault(); }
	});
	$(document).on("mousemove", function(e) { if (isDragging) { translateX = e.clientX - startX; translateY = e.clientY - startY; updateTransform(false); } });
	$(document).on("mouseup", function() { isDragging = false; });
});
</script>

<?php
// ==================== DATOS PRINCIPALES ====================
$item = "id";
$valor = $_GET["idOrden"];
$ordenes = controladorOrdenes::ctrMostrarordenesParaValidar($item, $valor);

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
?>

<div class="content-wrapper">

	<section class="content-header">
		<h2 style="margin:0 0 10px"><i class="fa-solid fa-file-invoice" style="color:#6366f1;margin-right:8px"></i>Orden #<?php echo htmlspecialchars($_GET["idOrden"]); ?></h2>
		<ol class="breadcrumb">
			<li><a href="index.php?ruta=inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>
			<li><a href="index.php?ruta=ordenes">Órdenes</a></li>
			<li class="active">Orden <?php echo htmlspecialchars($_GET["idOrden"]); ?></li>
		</ol>
	</section>

	<section class="content">

		<!-- ==================== FILA 1: CLIENTE (izq) + IMÁGENES (der) ==================== -->
		<div class="row">

			<div class="col-lg-5 col-xs-12">
				<!-- DATOS DEL CLIENTE -->
				<div class="egs-section">
					<div class="egs-title-bar"><i class="fa-solid fa-user"></i> Datos del cliente</div>
					<div class="egs-body">
						<div class="egs-field-row">
							<label class="egs-lbl"><i class="fa-solid fa-user" style="margin-right:4px"></i>Nombre</label>
							<input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario["nombre"]); ?>" readonly>
						</div>

						<?php if (!$isTecnico): ?>
						<div class="egs-field-row">
							<label class="egs-lbl"><i class="fa-solid fa-envelope" style="margin-right:4px"></i>Correo</label>
							<input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario["correo"]); ?>" readonly>
						</div>
						<div class="egs-field-row">
							<label class="egs-lbl"><i class="fa-brands fa-whatsapp" style="margin-right:4px"></i>WhatsApp</label>
							<input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario["telefonoDos"]); ?>" id="botonwhats" readonly>
						</div>
						<div class="egs-field-row">
							<label class="egs-lbl"><i class="fa-solid fa-phone" style="margin-right:4px"></i>Teléfono</label>
							<input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario["telefono"]); ?>" readonly>
						</div>
						<?php endif; ?>

						<div class="egs-field-row">
							<label class="egs-lbl"><i class="fa-solid fa-calendar-check" style="margin-right:4px"></i>Fecha de entrada</label>
							<input type="text" class="form-control" value="<?php echo htmlspecialchars($fecha_ingreso); ?>" readonly>
						</div>
						<div class="egs-field-row">
							<label class="egs-lbl"><i class="fa-solid fa-clock" style="margin-right:4px"></i>Última modificación</label>
							<input type="text" class="form-control" value="<?php echo htmlspecialchars($value["fecha"]); ?>" readonly>
						</div>

						<?php if ($isAdmin || $isVendedor): ?>
						<hr style="margin:12px 0">
						<div style="display:flex;gap:8px;flex-wrap:wrap">
							<a class="btn egs-btn-accent btn-sm" href="mailto:<?php echo htmlspecialchars($usuario["correo"]); ?>?subject=INFORME%20DEL%20ESTADO%20DE%20SU%20ORDEN%20<?php echo $_GET["idOrden"]; ?>&body=SALUDOS%20<?php echo htmlspecialchars($usuario["nombre"]); ?>">
								<i class="fas fa-envelope"></i> Enviar correo
							</a>
							<a class="btn btn-success btn-sm" href="//api.whatsapp.com/send?phone=521<?php echo htmlspecialchars($usuario["telefonoDos"]); ?>" target="_blank" id="linkwhats">
								<i class="fab fa-whatsapp"></i> WhatsApp
							</a>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="col-lg-7 col-xs-12">
				<!-- IMÁGENES DE LA ORDEN -->
				<div class="egs-section">
					<div class="egs-title-bar"><i class="fa-solid fa-images"></i> Imágenes de la orden</div>
					<div class="egs-body" style="padding:12px">
						<?php if (!empty($portada) || !empty($AlbumDeImagenes)) { ?>
							<div id="myCarousel" class="carousel slide orden-carousel" data-ride="carousel">
								<ol class="carousel-indicators">
									<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
									<?php
									$i = 0;
									if (!empty($AlbumDeImagenes)) {
										foreach ($AlbumDeImagenes as $key => $valueImagenes) {
											$i++;
											echo '<li data-target="#myCarousel" data-slide-to="'.$i.'"></li>';
										}
									}
									?>
								</ol>
								<div class="carousel-inner">
									<div class="item active">
										<?php if (!empty($portada)) { ?>
											<img loading="lazy" src="<?php echo $portada; ?>" class="img-orden-standard">
										<?php } ?>
									</div>
									<?php
									if (!empty($AlbumDeImagenes)) {
										foreach ($AlbumDeImagenes as $key => $valueImagenes) {
											echo '<div class="item"><img loading="lazy" src="'.$valueImagenes["foto"].'" class="img-orden-standard"></div>';
										}
									}
									?>
								</div>
								<a class="left carousel-control" href="#myCarousel" style="color:black" data-slide="prev">
									<span class="glyphicon glyphicon-chevron-left"></span>
								</a>
								<a class="right carousel-control" href="#myCarousel" style="color:black" data-slide="next">
									<span class="glyphicon glyphicon-chevron-right"></span>
								</a>
							</div>
						<?php } else { ?>
							<img src="vistas/img/productos/default/default.jpg" class="img-orden-standard" alt="Sin imágenes">
						<?php } ?>
					</div>
				</div>
			</div>

		</div><!-- /row 1 -->

		<!-- Modal Lightbox -->
		<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" style="display:inline-block">Vista previa</h4>
						<div class="pull-right" style="margin-right:20px">
							<button type="button" class="btn btn-default btn-sm" id="zoomOut"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-default btn-sm" id="zoomIn"><i class="fa fa-plus"></i></button>
						</div>
					</div>
					<div class="modal-body">
						<img src="" class="imagepreview" style="width:100%">
					</div>
				</div>
			</div>
		</div>

		<!-- ==================== FILA 2: PARTIDAS (izq) + FICHA TÉCNICA + ASIGNACIÓN (der) ==================== -->
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

								<!-- TOTAL INVERSIONES (admin only) -->
								<?php if ($isAdmin): ?>
								<div class="egs-inv-bar">
									<span class="egs-inv-label"><i class="fa-solid fa-coins" style="margin-right:6px"></i>Inversión total</span>
									<div class="input-group">
										<span class="input-group-addon egs-dollar">$</span>
										<input type="number" name="totalInversiones" class="form-control" id="costoTotalInversiones" form="formObservaciones" readonly style="font-weight:700;font-size:18px">
									</div>
								</div>
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

			<!-- COLUMNA DERECHA: FICHA TÉCNICA + ASIGNACIÓN -->
			<div class="col-lg-4 col-xs-12">

				<!-- FICHA TÉCNICA -->
				<div class="egs-section">
					<div class="egs-title-bar"><i class="fa-solid fa-microchip"></i> Ficha técnica</div>
					<div class="egs-body">
						<form role="form" method="post" class="formularioFichaTecnica">
							<?php if ($value["marcaDelEquipo"] == ""): ?>
								<div class="egs-field-row">
									<label class="egs-lbl"><i class="far fa-copyright" style="margin-right:4px"></i>Marca del equipo</label>
									<input type="text" id="marca" class="form-control" name="marcaDelEquipo" placeholder="Ej: HP, EPSON, BROTHER">
									<span id="spanmarca" style="color:red;font-size:12px"></span>
								</div>
								<div class="egs-field-row">
									<label class="egs-lbl"><i class="fas fa-kaaba" style="margin-right:4px"></i>Modelo del equipo</label>
									<input type="text" id="modelo" class="form-control" name="modeloDelEquipo" placeholder="Ej: LaserJet Pro M404">
									<span id="spanmodelo" style="color:red;font-size:12px"></span>
								</div>
								<div class="egs-field-row">
									<label class="egs-lbl"><i class="fas fa-barcode" style="margin-right:4px"></i>Número de serie</label>
									<input type="text" id="numeroserial" class="form-control" name="numeroDeSerieDelEquipo" placeholder="Últimos 6 dígitos">
									<span id="spannumeroserie" style="color:red;font-size:12px"></span>
								</div>
							<?php else: ?>
								<div class="egs-field-row">
									<label class="egs-lbl"><i class="far fa-copyright" style="margin-right:4px"></i>Marca</label>
									<input type="text" id="marca2" class="form-control" value="<?php echo htmlspecialchars($value["marcaDelEquipo"]); ?>" readonly>
								</div>
								<div class="egs-field-row">
									<label class="egs-lbl"><i class="fas fa-kaaba" style="margin-right:4px"></i>Modelo</label>
									<input type="text" id="modelo2" class="form-control" value="<?php echo htmlspecialchars($value["modeloDelEquipo"]); ?>" readonly>
								</div>
								<div class="egs-field-row">
									<label class="egs-lbl"><i class="fas fa-barcode" style="margin-right:4px"></i>Número de serie</label>
									<input type="text" id="numeroserial2" class="form-control" value="<?php echo htmlspecialchars($value["numeroDeSerieDelEquipo"]); ?>" readonly>
								</div>
							<?php endif; ?>
							<input type="hidden" value="<?php echo $_GET["idOrden"]; ?>" name="idOrden">
						</form>
					</div>
				</div>

				<!-- ASIGNACIÓN -->
				<div class="egs-section">
					<div class="egs-title-bar"><i class="fa-solid fa-users-gear"></i> Asignación</div>
					<div class="egs-body">

						<!-- ASESOR -->
						<div class="egs-field-row">
							<label class="egs-lbl"><i class="fa-solid fa-user-tie" style="margin-right:4px"></i>Asesor</label>
							<?php
							$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $_GET["asesor"]);
							if ($isReadonly) {
								echo '<input type="text" class="form-control" value="'.htmlspecialchars($asesor["nombre"]).'" readonly>';
								echo '<input type="hidden" value="'.$asesor["id"].'" name="asesorEditadoEnOrdenDianmica" form="formObservaciones">';
							} else {
								echo '<select class="form-control selector" name="asesorEditadoEnOrdenDianmica" form="formObservaciones" required>';
								echo '<option value="'.$asesor["id"].'">'.htmlspecialchars($asesor["nombre"]).'</option>';
								$asesorParaSelect = Controladorasesores::ctrMostrarAsesoresEmpresas("id_empresa", $_SESSION["empresa"]);
								foreach ($asesorParaSelect as $va) {
									echo '<option value="'.$va["id"].'" class="text-uppercase">'.htmlspecialchars($va["nombre"]).'</option>';
								}
								echo '</select>';
							}
							?>
						</div>

						<!-- TÉCNICO (En posesión) -->
						<div class="egs-field-row">
							<label class="egs-lbl"><i class="fa-solid fa-screwdriver-wrench" style="margin-right:4px"></i>Técnico (En posesión)</label>
							<?php
							$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $_GET["tecnico"]);
							if ($isTecnico || $isSecretaria) {
								echo '<input type="text" class="form-control" value="'.htmlspecialchars($tecnico["nombre"]).'" readonly>';
								echo '<input type="hidden" value="'.$tecnico["id"].'" name="tecnicoEditadoEnOrdenDianmica" form="formObservaciones">';
							} else {
								echo '<select class="form-control selector" name="tecnicoEditadoEnOrdenDianmica" form="formObservaciones" required>';
								echo '<option value="'.$tecnico["id"].'">'.htmlspecialchars($tecnico["nombre"]).'</option>';
								$tecnicoList = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas("id_empresa", $_SESSION["empresa"]);
								foreach ($tecnicoList as $vt) {
									echo '<option value="'.$vt["id"].'" class="text-uppercase">'.htmlspecialchars($vt["nombre"]).'</option>';
								}
								echo '</select>';
							}
							?>
						</div>

						<!-- TÉCNICO (Participación) -->
						<div class="egs-field-row">
							<label class="egs-lbl"><i class="fa-solid fa-user-plus" style="margin-right:4px"></i>Técnico (Participación)</label>
							<?php
							$tecnico2 = ControladorTecnicos::ctrMostrarTecnicos("id", $_GET["tecnicodos"]);
							if ($isTecnico || $isSecretaria) {
								echo '<input type="text" class="form-control" value="'.htmlspecialchars($tecnico2["nombre"]).'" readonly>';
								echo '<input type="hidden" value="'.$tecnico2["id"].'" name="tecnicodosEditadoEnOrdenDianmica" form="formObservaciones">';
							} else {
								echo '<select class="form-control selector" name="tecnicodosEditadoEnOrdenDianmica" form="formObservaciones">';
								echo '<option value="'.$tecnico2["id"].'">'.htmlspecialchars($tecnico2["nombre"]).'</option>';
								$tecnico2List = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas("id_empresa", $_SESSION["empresa"]);
								foreach ($tecnico2List as $vt2) {
									echo '<option value="'.$vt2["id"].'" class="text-uppercase">'.htmlspecialchars($vt2["nombre"]).'</option>';
								}
								echo '</select>';
							}
							?>
						</div>

						<!-- ESTADO DE LA ORDEN -->
						<div class="egs-field-row">
							<label class="egs-lbl"><i class="fa-solid fa-toggle-on" style="margin-right:4px"></i>Estado</label>
							<span class="egs-estado-badge" style="margin-bottom:8px"><?php echo htmlspecialchars($estado); ?></span>
							<?php
							if ($estado !== "Entregado (Ent)") {
								$allStates = array(
									'En revisión (REV)', 'Supervisión (SUP)', 'Pendiente de autorización (AUT',
									'Aceptado (ok)', 'Terminada (ter)', 'Cancelada (can)',
									'Sin reparación (SR)', 'Entregado (Ent)', 'Producto para venta',
									'En revisión probable garantía ', 'Garantía aceptada (GA)'
								);

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
							} else {
								echo '<input type="hidden" name="estado" value="Entregado (Ent)" form="formObservaciones">';
								echo '<div style="text-align:center;padding:8px"><h4 style="color:#16a34a;margin:0"><i class="fa-solid fa-circle-check"></i> ENTREGADO EL: '.htmlspecialchars($fecha_Salida).'</h4></div>';
							}
							?>
						</div>

					</div>
				</div>

			</div><!-- /col-lg-4 ficha+asignacion -->

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
								<input type="hidden" name="listarinversiones" id="listarinversiones">
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
										<p style="margin:0;color:#334155;text-transform:uppercase;font-weight:500;font-size:13px;line-height:1.5"><?php echo htmlspecialchars($valueobs["observacion"]); ?></p>
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

							<!-- Botón agregar observación -->
							<div style="margin-top:16px;display:flex;gap:8px;align-items:center;flex-wrap:wrap">
								<button type="button" class="btn egs-btn-accent" data-toggle="modal" data-target="#exampleModal">
									<i class="fa-solid fa-plus" style="margin-right:4px"></i>Agregar observación
								</button>
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
					if(badge.length){ badge.text(res.estado).css("animation","pulse .6s"); }
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
							var html = '<div class="egs-obs-item" style="display:flex;gap:12px;padding:14px;border:1px solid #f1f5f9;border-radius:10px;margin-bottom:8px">';
							if(obs.foto){
								html += '<img src="'+obs.foto+'" style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #e2e8f0;flex-shrink:0" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\'">';
								html += '<div style="display:none;width:40px;height:40px;border-radius:50%;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;background:'+grad+'">'+ini+'</div>';
							} else {
								html += '<div style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;background:'+grad+'">'+ini+'</div>';
							}
							html += '<div style="flex:1;min-width:0">';
							html += '<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap">';
							html += '<span style="font-size:13px;font-weight:700;color:#0f172a">'+nombre+'</span>';
							html += '<span style="font-size:11px;color:#94a3b8;margin-left:auto"><i class="fa-regular fa-clock" style="font-size:9px"></i> '+fechaStr+'</span>';
							html += '</div>';
							html += '<p style="margin:0;color:#334155;text-transform:uppercase;font-weight:500;font-size:13px;line-height:1.5">' + (obs.observacion || '') + '</p>';
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

<?php
$insertarobservacion = new controladorObservaciones();
$insertarobservacion->ctrlCrearObservacion();
?>
