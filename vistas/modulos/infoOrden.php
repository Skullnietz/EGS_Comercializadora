<?php
if ($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"] != "vendedor" AND $_SESSION["perfil"] != "tecnico" AND $_SESSION["perfil"] != "secretaria") {

	echo '<script>

  window.location = "index.php?ruta=ordenes";

  </script>';

	return;
}

?>
<style>
	.garantia {
		background: yellow !important;
	}

	#btn-mas {
		display: none;

	}

	.avatar {
		vertical-align: middle;
		width: 50px;
		height: 50px;
		border-radius: 50%;
	}

	.container {
		position: fixed;
		z-index: 2;
		bottom: 550px;
		right: -1000px;
		float: right;
		opacity: 0.5;
	}

	.redes a,
	.btn-mas label {
		display: block;
		text-decoration: none;
		background: #2b59cc;
		color: #fff;
		width: 55px;
		height: 55px;
		line-height: 55px;
		text-align: center;
		border-radius: 10%;
		box-shadow: 0px 1px 10px rgba(0, 0, 0, 0.4);
		transition: all 500ms ease;
	}

	.redes a:hover {
		background: #fff;
		color: #2b59cc;
	}

	.redes a {
		margin-bottom: -15px;
		opacity: 0;
		visibility: hidden;
	}

	#btn-mas:checked~.redes a {
		margin-bottom: 10px;
		opacity: 1;
		visibility: visible;
	}

	.btn-mas label {
		cursor: pointer;
		background: #f44141;
		font-size: 23px;
	}

	#btn-mas:checked~.btn-mas label {
		transform: rotate(135deg);
		font-size: 25px;
	}

	.img-orden-standard {
		width: 100%;
		height: 400px;
		object-fit: cover;
		background-color: #f4f4f4;
		cursor: pointer;
	}

	/* Force strict height on the carousel container */
	.orden-carousel .carousel-inner {
		height: 400px !important;
	}

    .imagepreview {
        transform-origin: center center;
		cursor: grab;
    }
    .imagepreview:active {
        cursor: grabbing;
    }

	.modal-body {
		overflow: hidden;
		/* Contain the zoomed image */
	}
</style>
<script>
	$(document).ready(function () {
		//Cada 10 segundos (10000 milisegundos) se ejecutará la función refrescar
		setTimeout(refrescar, 300000);
	});
	function refrescar() {
		//Actualiza la página
		location.reload();
	}
	var timer2 = "5:01";
	var interval = setInterval(function () {


		var timer = timer2.split(':');
		//by parsing integer, I avoid all extra string processing
		var minutes = parseInt(timer[0], 10);
		var seconds = parseInt(timer[1], 10);
		--seconds;
		minutes = (seconds < 0) ? --minutes : minutes;
		if (minutes < 0) clearInterval(interval);
		seconds = (seconds < 0) ? 59 : seconds;
		seconds = (seconds < 10) ? '0' + seconds : seconds;
		//minutes = (minutes < 10) ?  minutes : minutes;
		$('.countdown').html(minutes + ':' + seconds);
		timer2 = minutes + ':' + seconds;
	}, 1000);
</script>
<?php
if ($_SESSION["perfil"] == "tecnico") {
	echo '<script>
$(document).ready(function(){
     //No se mostrara el boton de completar si los tecnicos no llenan la ficha tecnica
    $("#btncompletarorden").hide();
    $("#marca").keyup(function() {
        $("#marca").val($(this).val().toUpperCase());
    });
    $("#modelo").keyup(function() {
        $("#modelo").val($(this).val().toUpperCase());
    });
    $("#numeroserial").keyup(function() {
        $("#numeroserial").val($(this).val().toUpperCase());
    });
    $("#spanboton").html("Complete su ficha tecnica");
    $( "#marca,#modelo,#numeroserial").keyup(function() {
        if ($("#marca").val().length >= 2){
        $("#spanmarca").html("");
        $("#spanboton").html("");
        if ($("#modelo").val().length >= 4){
        $("#spanmodelo").html("");
        $("#spanboton").html("");
        if ($("#numeroserial").val().length == 6){
        $("#spannumeroserie").html("");
        $("#spanboton").html("");
        $("#btncompletarorden").show();
        }else{
        $("#btncompletarorden").hide();
        $("#spannumeroserie").html("Debe contener los ultimos <b>6</b> digitos");
        $("#spanboton").html("Complete el campo de numero de serie");
        }
        }else{
        $("#btncompletarorden").hide();
        $("#spanboton").html("Complete el campo de modelo");
        $("#spanmodelo").html("Debe contener al menos <b>4</b> digitos");   
        }
        }else{
        $("#btncompletarorden").hide();
        $("#spanboton").html("Complete el campo de marca");
        $("#spanmarca").html("Debe contener al menos <b>2</b> digitos");
        }
    });
    
    //funcion para los campos que ya estan llenos
    $( document ).ready(function() {
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
        var currentScale = 1;
        var isDragging = false;
        var startX, startY;
        var translateX = 0, translateY = 0;

        function updateTransform(enableTransition) {
            if (enableTransition) {
                $(".imagepreview").css("transition", "transform 0.25s ease");
            } else {
                $(".imagepreview").css("transition", "none");
            }
            $(".imagepreview").css("transform", `translate(${translateX}px, ${translateY}px) scale(${currentScale})`);
        }

		$(".img-orden-standard").on("click", function () {
			var src = $(this).attr("src");
            // Reset state
            currentScale = 1;
            translateX = 0;
            translateY = 0;
			$(".imagepreview").attr("src", src);
            updateTransform(false);
			$("#imagemodal").modal("show");
		});

        $("#zoomIn").on("click", function() {
            currentScale += 0.2;
            updateTransform(true);
        });

        $("#zoomOut").on("click", function() {
            if (currentScale > 0.4) {
                currentScale -= 0.2;
                updateTransform(true);
            }
        });

        // Drag functionality
        $(".imagepreview").on("mousedown", function(e) {
             if (currentScale > 1) { // Only allow drag if zoomed in slightly
                isDragging = true;
                startX = e.clientX - translateX;
                startY = e.clientY - translateY;
                e.preventDefault(); // Prevent default image drag behavior
            }
        });

        $(document).on("mousemove", function(e) {
            if (isDragging) {
                translateX = e.clientX - startX;
                translateY = e.clientY - startY;
                updateTransform(false); // No transition during drag
            }
        });

        $(document).on("mouseup", function() {
            isDragging = false;
        });
	});
</script>

<div class="content-wrapper">

	<section class="content-header">

		<?php

		$item = "id";

		$valor = $_GET["idOrden"];

		$ordenes = controladorOrdenes::ctrMostrarordenesParaValidar($item, $valor);

		?>


		<h2>
			<center>Orden Numero: <? echo $_GET["idOrden"] ?></center>
		</h2>

		<ol class="breadcrumb">


			<li><a href="#"><i class="fas fa-dashboard"></i> Inicio</a></li>

			<li class="active">Orden <? echo $_GET["idOrden"] ?></li>



		</ol>





	</section>



	<section class="content">

		<div id="recargable">

			<div class="row">



				<div class="col-lg-5 col-xs-12">



					<div class="box box-success">



						<div class="box-header with-border"></div>



						<div class="box-body">



							<div class="box">



								<!--=====================================

								ENTRADA DATOS DEL CLIENTE

								======================================-->

								<?php



								//TRAER CLIENTE (USUARIO)
								
								$item = "id";

								$valor = $_GET["cliente"];



								$usuario = ControladorClientes::ctrMostrarClientesOrdenes($item, $valor);



								?>



								<div class="form-group">



									<div class="input-group">



										<span class="input-group-addon"><i class="fas fa-user"></i></span>



										<input type="text" class="form-control" value="<?php echo $usuario["nombre"] ?>"
											name="nombreCliente" readonly>


									</div>



								</div>



								<div class="form-group">



									<div class="input-group">



										<span class="input-group-addon"><i class="fas fa-envelope"></i></span>

										<?php

										if ($_SESSION["perfil"] != "tecnico") {

											echo '<input type="text" class="form-control" name="correoCliente" value="' . $usuario["correo"] . '" readonly>';
										}

										?>





									</div>



								</div>



								<div class="form-group">



									<div class="input-group">



										<span class="input-group-addon"><i class="fab fa-whatsapp"></i></span>

										<?php

										if ($_SESSION["perfil"] != "tecnico") {


											echo '<input type="text" class="form-control" value="' . $usuario["telefonoDos"] . '" id="botonwhats" readonly>';
										}
										?>




									</div>



								</div>



								<div class="form-group">



									<div class="input-group">



										<span class="input-group-addon"><i class="fas fa-phone-alt"></i></span>

										<?php

										if ($_SESSION["perfil"] != "tecnico") {


											echo '<input type="text" class="form-control" value="' . $usuario["telefono"] . '" readonly>';

										}

										?>

									</div>



								</div>

								<!--====================
								INFORMACION DE FECHAS DE INGRESO
								====================-->
								<div class="form-group">

									<div class="input-group">

										<span class="input-group-addon">Entrada</span>
										<?php


										$item = "id";

										$valor = $_GET["idOrden"];



										$ordenes = controladorOrdenes::ctrMostrarordenesParaValidar($item, $valor);
										foreach ($ordenes as $key => $valueOrdenesFecha) {
											# code...
										}
										echo '<input type="text" class="form-control" value="' . $valueOrdenesFecha["fecha_ingreso"] . '" readonly>';


										?>

									</div>

								</div>

							</div>

							<div class="form-group">

								<div class="input-group">
									<?
									echo '<span class="input-group-addon">Ultima modificacion</span>
								      		<input type="text" class="form-control" value="' . $valueOrdenesFecha["fecha"] . '" readonly>';
									?>
								</div>

							</div>

							<?php

							if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor") {

								echo '<hr><a style="float:left" class="btn btn-primary" style="color: white;" href="mailto:' . $usuario["correo"] . '?subject=INFORME%20DEL%20ESTADO%20DE%20SU%20ORDEN%20' . $_GET["idOrden"] . '&body=SALUDOS%20' . $usuario["nombre"] . '"> <i class="fas fa-envelope"></i> Enviar correo</a>';
							} else {

							}
							if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor") {
								echo '<a style="float:right"  href="//api.whatsapp.com/send?phone=521' . $usuario["telefonoDos"] . '" target="_blank" id="linkwhats"><button class="btn btn-success"><i class="fab fa-whatsapp"></i> Enviar mensaje de Whatsapp</button></a>';
							} else {

							}

							?>


						</div>



					</div>



				</div>



				<!--=====================================

	  LA TABLA DE PRODUCTOS

	  ======================================-->



				<div class="col-lg-7">



					<div class="box box-warning">



						<div class="box-header with-border"></div>



						<div class="box-body">



							<?php



							$item = "id";

							$valor = $_GET["idOrden"];



							$ordenes = controladorOrdenes::ctrMostrarordenesParaValidar($item, $valor);



							foreach ($ordenes as $key => $value) {



								$portada = $value["portada"];

								$AlbumDeImagenes = json_decode($value["multimedia"], true);

								$estado = $value["estado"];

								$partidaUno = $value["partidaUno"];

								$precioUno = $value["precioUno"];

								$partidaDos = $value["partidaDos"];

								$precioDos = $value["precioDos"];

								$partidaTres = $value["partidaTres"];

								$precioTres = $value["precioTres"];

								$partidaCuatro = $value["partidaCuatro"];

								$precioCuatro = $value["precioCuatro"];

								$partidaCinco = $value["partidaCinco"];

								$PrecioCinco = $value["precioCinco"];

								$partidaSeis = $value["partidaSeis"];

								$precioSeis = $value["precioSeis"];

								$partidaSiete = $value["partidaSiete"];

								$precioSiete = $value["precioSiete"];

								$partidaOcho = $value["partidaOcho"];

								$precioOcho = $value["precioOcho"];

								$partidaNueve = $value["partidaNueve"];

								$precioNueve = $value["precioNueve"];

								$partidaDiez = $value["partidaDiez"];

								$precioDiez = $value["precioDiez"];

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



							?>











							<?php

							date_default_timezone_set("America/Mexico_City");

							$fecha = date("Y-m-d H:i:s", strtotime($fecha_ingreso . "+ 5 days"));


							if ($fecha >= $fecha_ingreso) {



							}

							?>




							<!-- ===================== IMÁGENES DE LA ORDEN (CARRUSEL RESPONSIVE) ===================== -->

							<div style="margin-top: -30px">
								<center>

									<h2>Imágenes de la orden</h2>

									<?php if (!empty($portada) || !empty($AlbumDeImagenes)) { ?>

										<div id="myCarousel" class="carousel slide orden-carousel" data-ride="carousel">
											<!-- Indicators -->
											<ol class="carousel-indicators">
												<!-- Primer indicador (portada) -->
												<li data-target="#myCarousel" data-slide-to="0" class="active"></li>

												<?php
												$i = 0;
												if (!empty($AlbumDeImagenes)) {
													foreach ($AlbumDeImagenes as $key => $valueImagenes) {
														$i++;
														?>
														<li data-target="#myCarousel" data-slide-to="<?php echo $i; ?>"></li>
														<?php
													}
												}
												?>
											</ol>

											<!-- Wrapper for slides -->
											<div class="carousel-inner">
												<!-- Slide principal (portada) -->
												<div class="item active">
													<?php if (!empty($portada)) { ?>
														<img loading="lazy" src="<?php echo $portada; ?>"
															class="img-orden-standard">
													<?php } ?>
												</div>

												<!-- Resto de imágenes del álbum -->
												<?php
												if (!empty($AlbumDeImagenes)) {
													foreach ($AlbumDeImagenes as $key => $valueImagenes) {
														?>
														<div class="item">
															<img loading="lazy" src="<?php echo $valueImagenes["foto"]; ?>"
																class="img-orden-standard">
														</div>
														<?php
													}
												}
												?>
											</div>

											<!-- Left and right controls -->
											<a class="left carousel-control" href="#myCarousel" style="color:black"
												data-slide="prev">
												<span class="glyphicon glyphicon-chevron-left"></span>
												<span class="sr-only">Previous</span>
											</a>
											<a class="right carousel-control" href="#myCarousel" style="color:black"
												data-slide="next">
												<span class="glyphicon glyphicon-chevron-right"></span>
												<span class="sr-only">Next</span>
											</a>
										</div>

									<?php } else { ?>

										<img src="vistas/img/productos/default/default.jpg" class="img-orden-standard"
											alt="Sin imágenes">

									<?php } ?>

									<!-- Modal Lightbox -->
									<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog"
										aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal"><span
															aria-hidden="true">&times;</span><span
															class="sr-only">Cerrar</span></button>
													<h4 class="modal-title" id="myModalLabel"
														style="display: inline-block;">Vista previa</h4>
													<div class="pull-right" style="margin-right: 20px;">
														<button type="button" class="btn btn-default btn-sm"
															id="zoomOut"><i class="fa fa-minus"></i></button>
														<button type="button" class="btn btn-default btn-sm"
															id="zoomIn"><i class="fa fa-plus"></i></button>
													</div>
												</div>
												<div class="modal-body">
													<img src="" class="imagepreview" style="width: 100%;">
												</div>
											</div>
										</div>
									</div>

								</center>
							</div>

							<!-- ===================== FIN IMÁGENES DE LA ORDEN ===================== -->







							<form role="form" method="post" class="formularioFichaTecnica">

								<?php
								if ($value["marcaDelEquipo"] == "") {

									echo '<div style="background-color:#eee">
            	<h2><center>Ficha técnica</center></h2>

            	<div class="form-group">

            		<div class="input-group">';

									echo '<span class="input-group-addon"><i class="far fa-copyright"></i>&nbsp;  | Marca del equipo&nbsp;&nbsp;&nbsp;&nbsp;</span><input type="text" id="marca" class="form-control" name="marcaDelEquipo"> <span id="spanmarca" style="color:red;"></span>';

									echo '</div>

            	</div>

            	<div class="form-group">

            		<div class="input-group">

            
						<span class="input-group-addon"><i class="fas fa-kaaba"></i>&nbsp;  | Modelo del equipo</span><input type="text" id="modelo" class="form-control" name="modeloDelEquipo"><span id="spanmodelo" style="color:red;"></span>

            		</div>

            	</div>

            	<div class="form-group">

            		<div class="input-group">

            			<span style="width=100px;"class="input-group-addon"><i class="fas fa-barcode"></i>&nbsp;  | Numero de serie &nbsp;&nbsp;&nbsp;&nbsp;</span><input type="text" class="form-control" id="numeroserial" name="numeroDeSerieDelEquipo"><span id="spannumeroserie" style="color:red;"></span>
            		
            		</div>

            	</div></div>';


								} else {

									echo '<div style="background-color:#eee; border-radius: 10px; border: 2px solid #d2d6de;">
            	<h2><center>Ficha técnica</center></h2>

            	<div class="form-group">

            		<div class="input-group">';

									echo '<span class="input-group-addon"><i class="far fa-copyright"></i>&nbsp;  | Marca del equipo&nbsp;&nbsp;&nbsp;&nbsp;</span><input type="text" id="marca2" class="form-control" value="' . $value["marcaDelEquipo"] . '" readonly>';

									echo '</div>

            	</div>

            	<div class="form-group">

            		<div class="input-group">

            
						<span class="input-group-addon"><i class="fas fa-kaaba"></i>&nbsp;  | Modelo del equipo</span><input type="text" id="modelo2" class="form-control" value="' . $value["modeloDelEquipo"] . '" readonly>

            		</div>

            	</div>

            	<div class="form-group">

            		<div class="input-group">

            			<span class="input-group-addon"><i class="fas fa-barcode"></i>&nbsp;  | Numero de serie &nbsp;&nbsp;&nbsp;&nbsp;</span><input type="text" id="numeroserial2" class="form-control" value="' . $value["numeroDeSerieDelEquipo"] . '" readonly>
            			

            		</div>

            	</div></div>';


								}

								echo '<input type="hidden" value="' . $_GET["idOrden"] . '" name="idOrden">';

								?>

						</div>



					</div>





				</div>



				<!--=====================================

		ENTRADA DATOS TECNICO Y ASESOR

		======================================-->

				<div class="col-lg-12">



					<div class="box box-danger">



						<div class="box-header with-border"></div>



						<form role="form" method="post" class="formularioPartidas">



							<div class="box-body">



								<div class="box">



									<!--=====================================

								ASESOR

								======================================-->

									<?php

									$item = "id";

									$valor = $_GET["asesor"];


									$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



									?>

									<div class="form-group">



										<span><b>
												<h3>Asesor</h3>
											</b></span>



										<div class="input-group">



											<span class="input-group-addon"><i class="fas fa-user"></i></span>

											<?php
											if ($_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "secretaria" || $_SESSION["perfil"] == "vendedor") {

												echo '<input type="text" class=" form-control input-lg" value="' . $asesor["nombre"] . '" readonly>

											<input type="hidden" value="' . $asesor["id"] . '" name="asesorEditadoEnOrdenDianmica">';



											} else {

												echo '<select class="form-control input-lg selector" name="asesorEditadoEnOrdenDianmica" required>

											<option value="' . $asesor["id"] . '">' . $asesor["nombre"] . '</option>';

												$item = "id";

												$valor = $_GET["asesor"];



												$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



												$itemUno = "id_empresa";

												$valorDos = $_SESSION["empresa"];

												$asesorParaSelect = Controladorasesores::ctrMostrarAsesoresEmpresas($itemUno, $valorDos);

												foreach ($asesorParaSelect as $key => $valueAasesores) {

													echo '<option value="' . $valueAasesores["id"] . '" class="text-uppercase">' . $valueAasesores["nombre"] . '</option>';
												}

												echo '</select>';
											}
											?>


										</div>



									</div>



									<!--=====================================

								ENTRADA PARA EL TECNICO

								======================================-->






									<div class="form-group">



										<span><b>
												<h3>Técnico (En posesion)</h3>
											</b></span>



										<div class="input-group">



											<span class="input-group-addon"><i class="fas fa-cogs"></i></span>

											<?php


											$item = "id";

											$valor = $_GET["tecnico"];



											$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);

											if ($_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "secretaria") {

												echo '<input type="text" class=" form-control input-lg" value="' . $tecnico["nombre"] . '" readonly>
											<input type="hidden" value="' . $tecnico["id"] . '" name="tecnicoEditadoEnOrdenDianmica">';

											} else {

												echo '<select class="form-control input-lg selector" name="tecnicoEditadoEnOrdenDianmica" required>

											<option value="' . $tecnico["id"] . '">' . $tecnico["nombre"] . '</option>';

												$item = "id_empresa";

												$valor = $_SESSION["empresa"];



												$tecnico = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas($item, $valor);



												foreach ($tecnico as $key => $valuetecnico) {

													echo '<option value="' . $valuetecnico["id"] . '" class="text-uppercase">' . $valuetecnico["nombre"] . '</option>';
												}

												echo '</select>';
											}

											?>





										</div>



									</div>


									<!--===================================
									TRAER PARTICIPACION DEL SEGUNDO TECNICO
								======================================-->
									<div class="form-group">



										<span><b>
												<h3>Técnico (Participación)</h3>
											</b></span>



										<div class="input-group">



											<span class="input-group-addon"><i class="fas fa-cogs"></i></span>

											<?php


											$item = "id";

											$valor = $_GET["tecnicodos"];



											$tecnico2 = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);

											if ($_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "secretaria") {

												echo '<input type="text" class=" form-control input-lg" value="' . $tecnico2["nombre"] . '" readonly>
											<input type="hidden" value="' . $tecnico2["id"] . '" name="tecnicodosEditadoEnOrdenDianmica">';

											} else {

												echo '<select class="form-control input-lg selector" name="tecnicodosEditadoEnOrdenDianmica" >

											<option value="' . $tecnico2["id"] . '">' . $tecnico2["nombre"] . '</option>';

												$item = "id_empresa";

												$valor = $_SESSION["empresa"];



												$tecnico2 = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas($item, $valor);



												foreach ($tecnico2 as $key => $valuetecnico2) {

													echo '<option value="' . $valuetecnico2["id"] . '" class="text-uppercase">' . $valuetecnico2["nombre"] . '</option>';
												}

												echo '</select>';
											}

											?>





										</div>



									</div>


									<!--=====================================
								ENTRADA PARA EL ESTADO DE ORDEN 
								======================================-->
									<span><b>
											<h3>Estado de la orden</h3>
										</b></span>
									<div class="form-group">

										<div class="input-group">
											<?php

											utf8_encode(utf8_decode($EstadoRevision = "En revisión (REV)"));

											utf8_encode(utf8_decode($EstadoAUT = "Pendiente de autorización (AUT"));

											utf8_encode(utf8_decode($EstadoSUP = "Supervisión (SUP)"));


											if ($estado !== "Entregado (Ent)") {

												if ($_SESSION["perfil"] == "tecnico") {

													echo '<span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>
                                    
                                    	        <select class="form-control input-lg selector" name="estado">
                                    														
                                    			<option>' . $estado . '</option>';

													if ($estado == $EstadoRevision) {

														echo '<option class="aut" value="En revisión (REV)">En revisión (REV)</option><option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option>';

													} else if ($estado == "Aceptado (ok)") {

														echo '</option><option class="ok" value="Aceptado (ok)">Aceptado (ok)</option><option class="ter" value="Terminada (ter)">Terminada (ter)</option>';


													}

													echo '</select>';
												}

												if ($_SESSION["perfil"] == "editor") {

													echo '<span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>
                                    
                                    	        <select class="form-control input-lg selector" name="estado">
                                    														
                                    			<option>' . $estado . '</option>';

													if ($estado == $EstadoRevision) {

														echo '<option class="aut" value="En revisión (REV)">En revisión (REV)</option><option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option>';

													} else if ($estado == $EstadoSUP) {

														echo '<option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option><option class="aut" value="Pendiente de autorizaci&oacute;n (AUT">Pendiente de autorizaci&oacute;n (AUT</option>';

													} else if ($estado == $EstadoAUT) {

														echo 'Pendiente de autorizaci&oacute;n (AUT">Pendiente de autorizaci&oacute;n (AUT</option>
                                    			<option class="ok" value="Aceptado (ok)">Aceptado (ok)</option>';

													} else if ($estado == "Aceptado (ok)") {

														echo '<option class="ter" value="Terminada (ter)">Terminada (ter)</option>';

													} else if ($estado == "Cancelada (can)") {

														echo '<option class="can" value="Cancelada (can)">Cancelada (can)</option>';
													}

													echo '</select>';
												}

												if ($_SESSION["perfil"] == "vendedor") {

													echo '<span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>
                                    
                                    	        <select class="form-control input-lg selector" name="estado">
                                    														
                                    			<option>' . $estado . '</option>';

													if ($estado == $EstadoRevision) {

														echo '<option class="aut" value="En revisión (REV)">En revisión (REV)</option><option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option>';

													} else if ($estado == $EstadoSUP) {

														echo '</option><option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option><option class="aut" value="Pendiente de autorizaci&oacute;n (AUT">Pendiente de autorizaci&oacute;n (AUT</option>';

													} else if ($estado == $EstadoAUT) {

														echo '<option class="aut" value="Pendiente de autorizaci&oacute;n (AUT">Pendiente de autorizaci&oacute;n (AUT</option><option class="ok" value="Aceptado (ok)">Aceptado (ok)</option>';
													} else if ($estado == $EstadoAUT2) {

														echo 'Pendiente de autorizaci&oacute;n (AUT">Pendiente de autorizaci&oacute;n (AUT</option>
                                    			<option class="ok" value="Aceptado (ok)">Aceptado (ok)</option>';

													} else if ($estado == "Aceptado (ok)") {

														echo '</option><option class="ok" value="Aceptado (ok)">Aceptado (ok)</option><option class="ter" value="Terminada (ter)">Terminada (ter)</option>';

													} else if ($estado == "Terminada (ter)") {
														echo '<option class="ter" value="Terminada (ter)">Terminada (ter)</option>';
													} else if ($estado == "Cancelada (can)") {

														echo '<option class="can" value="Cancelada (can)">Cancelada (can)</option>';
													}

													echo '</select>';
												}

												if ($_SESSION["perfil"] == "administrador") {

													echo '<span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>
                                    
                                    	        <select class="form-control input-lg selector" name="estado">';

													if ($estado == "Supervisión (SUP)") {

														echo '<option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option><option class="aut" value="En revisión (REV)">En revisión (REV)</option>
                                    	        	<option class="aut" value="Pendiente de autorizaci&oacute;n (AUT">Pendiente de autorizaci&oacute;n (AUT</option><option class="ok" value="Aceptado (ok)">Aceptado (ok)</option><option class="ter" value="Terminada (ter)">Terminada (ter)</option><option class="can" value="Cancelada (can)">Cancelada (can)</option><option value="Sin reparación (SR)">Sin reparación (SR)</option><option class="ent" value="Entregado (Ent)">Entregado (Ent)</option><option class="ent" value="Producto para venta">Producto para venta</option>
                                    	        	 <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía </option>
                                                     <option class="garantia" value="Garantía aceptada (GA)">Garantía aceptada (GA)</option>';

													} else if ($estado == "En revisión (REV)") {

														echo '<option class="aut" value="En revisión (REV)">En revisión (REV)</option>
                                    	        	<option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option><option class="aut" value="Pendiente de autorizaci&oacute;n (AUT">Pendiente de autorizaci&oacute;n (AUT</option><option class="ok" value="Aceptado (ok)">Aceptado (ok)</option><option class="ter" value="Terminada (ter)">Terminada (ter)</option><option class="can" value="Cancelada (can)">Cancelada (can)</option><option value="Sin reparación (SR)">Sin reparación (SR)</option><option class="ent" value="Entregado (Ent)">Entregado (Ent)</option><option class="ent" value="Producto para venta">Producto para venta</option>
                                    	        	  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía </option>
                                                     <option class="garantia" value="Garantía aceptada (GA)">Garantía aceptada (GA)</option>';


													} else if ($estado == "En revisión probable garantía ") {

														echo '<option class="aut garantia" value="En revisión probable garantía ">En revisión probable garantía </option>
                                    	        	<option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option><option class="aut" value="Pendiente de autorizaci&oacute;n (AUT">Pendiente de autorizaci&oacute;n (AUT</option><option class="ok" value="Aceptado (ok)">Aceptado (ok)</option><option class="ter" value="Terminada (ter)">Terminada (ter)</option><option class="can" value="Cancelada (can)">Cancelada (can)</option><option value="Sin reparación (SR)">Sin reparación (SR)</option><option class="ent" value="Entregado (Ent)">Entregado (Ent)</option><option class="ent" value="Producto para venta">Producto para venta</option>
                                    	        	  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía </option>
                                                     <option class="garantia" value="Garantía aceptada (GA)">Garantía aceptada (GA)</option>';


													} else if ($estado == "Pendiente de autorización (AUT") {

														echo '<option class="aut" value="Pendiente de autorizaci&oacute;n (AUT">Pendiente de autorizaci&oacute;n (AUT</option>
												      <option class="aut" value="En revisión (REV)">En revisión (REV)</option>
                                    	        	<option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option><option class="ok" value="Aceptado (ok)">Aceptado (ok)</option><option class="ter" value="Terminada (ter)">Terminada (ter)</option><option class="can" value="Cancelada (can)">Cancelada (can)</option><option value="Sin reparación (SR)">Sin reparación (SR)</option><option class="ent" value="Entregado (Ent)">Entregado (Ent)</option><option class="ent" value="Producto para venta">Producto para venta</option>';

													} else if ($estado == "Garantía aceptada (GA)") {

														echo '<option class="ok garantia" value="Garantía aceptada (GA)">Garantía aceptada (GA)</option><option class="aut" value="En revisión (REV)">En revisión (REV)</option>
                                    	        	<option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option><option class="aut" value="Pendiente de autorizaci&oacute;n (AUT">Pendiente de autorizaci&oacute;n (AUT</option><option class="ter" value="Terminada (ter)">Terminada (ter)</option><option class="can" value="Cancelada (can)">Cancelada (can)</option><option value="Sin reparación (SR)">Sin reparación (SR)</option><option class="ent" value="Entregado (Ent)">Entregado (Ent)</option><option class="ent" value="Producto para venta">Producto para venta</option>';

													} else if ($estado == "Aceptado (ok)") {

														echo '<option class="ok" value="Aceptado (ok)">Aceptado (ok)</option><option class="aut" value="En revisión (REV)">En revisión (REV)</option>
                                    	        	<option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option><option class="aut" value="Pendiente de autorizaci&oacute;n (AUT">Pendiente de autorizaci&oacute;n (AUT</option><option class="ter" value="Terminada (ter)">Terminada (ter)</option><option class="can" value="Cancelada (can)">Cancelada (can)</option><option value="Sin reparación (SR)">Sin reparación (SR)</option><option class="ent" value="Entregado (Ent)">Entregado (Ent)</option><option class="ent" value="Producto para venta">Producto para venta</option>';

													} else if ($estado == "Terminada (ter)") {

														echo '<option class="ter" value="Terminada (ter)">Terminada (ter)</option><option class="aut" value="En revisión (REV)">En revisión (REV)</option>
                                    	        	<option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option><option class="aut" value="Pendiente de autorizaci&oacute;n (AUT">Pendiente de autorizaci&oacute;n (AUT</option><option class="ok" value="Aceptado (ok)">Aceptado (ok)</option><option class="can" value="Cancelada (can)">Cancelada (can)</option><option value="Sin reparación (SR)">Sin reparación (SR)</option><option class="ent" value="Entregado (Ent)">Entregado (Ent)</option><option class="ent" value="Producto para venta">Producto para venta</option>';
													} else if ($estado == "Cancelada (can)") {

														echo '<option class="can" value="Cancelada (can)">Cancelada (can)</option><option class="aut" value="En revisión (REV)">En revisión (REV)</option>
                                    	        	<option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option><option class="aut" value="Pendiente de autorizaci&oacute;n (AUT">Pendiente de autorizaci&oacute;n (AUT</option><option class="ok" value="Aceptado (ok)">Aceptado (ok)</option><option class="ter" value="Terminada (ter)">Terminada (ter)</option><option value="Sin reparación (SR)">Sin reparación (SR)</option><option class="ent" value="Entregado (Ent)">Entregado (Ent)</option><option class="ent" value="Producto para venta">Producto para venta</option>';
													} else if ($estado == "Sin reparación (SR)") {

														echo '<option value="Sin reparación (SR)">Sin reparación (SR)</option><option class="aut" value="En revisión (REV)">En revisión (REV)</option>
                                    	        	<option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option><option class="aut" value="Pendiente de autorizaci&oacute;n (AUT">Pendiente de autorizaci&oacute;n (AUT</option><option class="ok" value="Aceptado (ok)">Aceptado (ok)</option><option class="ter" value="Terminada (ter)">Terminada (ter)</option><option class="can" value="Cancelada (can)">Cancelada (can)</option><option class="ent" value="Entregado (Ent)">Entregado (Ent)</option><option class="ent" value="Producto para venta">Producto para venta</option>';

													} else {

														echo '<option class="ent" value="Producto para venta">Producto para venta</option><option class="aut" value="En revisión (REV)">En revisión (REV)</option>
                                    	        	<option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option><option class="aut" value="Pendiente de autorizaci&oacute;n (AUT">Pendiente de autorizaci&oacute;n (AUT</option><option class="ok" value="Aceptado (ok)">Aceptado (ok)</option><option class="ter" value="Terminada (ter)">Terminada (ter)</option><option class="can" value="Cancelada (can)">Cancelada (can)</option><option value="Sin reparación (SR)">Sin reparación (SR)</option><option class="ent" value="Entregado (Ent)">Entregado (Ent)</option><option class="garantia" value="En revisión probable garantía "> En revisión probable garantía </option>
                                                     <option class="garantia" value="Garantía aceptada (GA)">Garantía aceptada (GA)</option>';
													}

													echo '</select>';

												}

											} else {


												echo '<input type="hidden" name="estado" value="Entregado (Ent)">
                                    	<center><span><b><h1>ENTREGADO EL: ' . $fecha_Salida . '</h1></b></span></center>';
											}
											?>
										</div>



									</div>

									<!--=====================================

								ENTRADA PARA VISUALIZAR PRIMERA PARTIDA

								======================================-->

									<?php



									if ($partidaUno != null) {



										if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "secretaria") {



											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaUno" readonly>' . $partidaUno . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>





					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioUno" type="number" value="' . $precioUno . '" readonly>



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';



										} else {



											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaUno">' . $partidaUno . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>

										

															                	

					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioUno" type="number" value="' . $precioUno . '">



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					              	</br></br></br>



					              							            	

					            </div>';





										}



									}





									?>

									<!--=====================================

								ENTRADA PARA VISUALIZAR SEGUNDA PARTIDA

								======================================-->

									<?php



									if ($partidaDos != null) {



										if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "secretaria") {



											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaDos" readonly>' . $partidaDos . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioDos" type="number" value="' . $precioDos . '" readonly>



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';



										} else {



											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                 <span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaDos">' . $partidaDos . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioDos" type="number" value="' . $precioDos . '">



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';



										}



									}



									?>

									<!--=====================================

								ENTRADA PARA VISUALIZAR TERCERA PARTIDA

								======================================-->

									<?php



									if ($partidaTres != null) {



										if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "secretaria") {





											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaTres" readonly>' . $partidaTres . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioTres" type="number" value="' . $precioTres . '" readonly>



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';

										} else {



											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaTres">' . $partidaTres . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioTres" type="number" value="' . $precioTres . '">



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';

										}

									}

									?>

									<!--=====================================

								ENTRADA PARA VISUALIZAR CUARTA PARTIDA

								======================================-->

									<?php



									if ($partidaCuatro != null) {



										if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "secretaria") {





											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaCuatro" readonly>' . $partidaCuatro . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioCuatro" type="number" value="' . $precioCuatro . '" readonly>



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';

										} else {

											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaCuatro">' . $partidaCuatro . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioCuatro" type="number" value="' . $precioCuatro . '">



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';

										}

									}

									?>

									<!--=====================================

								ENTRADA PARA VISUALIZAR QUINTA PARTIDA

								======================================-->

									<?php



									if ($partidaCinco != null) {



										if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "secretaria") {





											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaCinco" readonly>' . $partidaCinco . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioCinco" type="number" value="' . $PrecioCinco . '" readonly>



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';

										} else {



											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaCinco">' . $partidaCinco . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioCinco" type="number" value="' . $PrecioCinco . '">



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';

										}

									}

									?>

									<!--=====================================

								ENTRADA PARA SEXTA SEPTIMA PARTIDA

								======================================-->

									<?php



									if ($partidaSeis != null) {



										if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "secretaria") {



											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaSeis" readonly>' . $partidaSeis . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioSeis" type="number" value="' . $precioSeis . '" readonly>



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';



										} else {



											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaSeis">' . $partidaSeis . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioSeis" type="number" value="' . $precioSeis . '">



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';

										}



									}

									?>

									<!--=====================================

								ENTRADA PARA VISUALIZAR SEPTIMA PARTIDA

								======================================-->

									<?php



									if ($partidaSiete != null) {



										if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "secretaria") {



											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaSiete" readonly>' . $partidaSiete . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioSiete" type="number" value="' . $precioSiete . '" readonly>



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';



										} else {



											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaSiete">' . $partidaSiete . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioSiete" type="number" value="' . $precioSiete . '">



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';



										}



									}

									?>

									<!--=====================================

								ENTRADA PARA VISUALIZAR OCHO PARTIDA

								======================================-->







									<?php



									if ($partidaOcho != null) {



										if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "secretaria") {





											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg precioPartidaGuardada" name="partidaOcho" readonly>' . $partidaOcho . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioOcho" type="number" value="' . $precioOcho . '" readonly>



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';



										} else {



											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercaseprecioPartidaGuardada" name="partidaOcho">' . $partidaOcho . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioOcho" type="number" value="' . $precioOcho . '">



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';



										}



									}

									?>

									<!--=====================================

								ENTRADA PARA VISUALIZAR NUEVE PARTIDA

								======================================-->

									<?php



									if ($partidaNueve != null) {



										if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "secretaria") {





											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaNueve" readonly>' . $partidaNueve . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioNueve" type="number" value="' . $precioNueve . '" readonly>



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';



										} else {



											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaNueve">' . $partidaNueve . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioNueve" type="number" value="' . $precioNueve . '">



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';

										}



									}

									?>

									<!--=====================================

								ENTRADA PARA VISUALIZAR DIEZ PARTIDA

								======================================-->

									<?php



									if ($partidaDiez != null) {



										if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "secretaria") {





											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaDiez" readonly>' . $partidaDiez . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioDiez" type="number" value="' . $precioDiez . '" readonly>



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';

										} else {



											echo '<div class="form-group row">



					              				<div class="col-xs-6">



					                				<div class="input-group"> 



									                  <span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>



									                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaDiez">' . $partidaDiez . '</textarea> 



					                				</div> 



					              				</div>



					              		<div>



					                	<div class="col-xs-6">



					                  		<div class="input-group">



					                    		<input class="form-control input-lg precioPartidaGuardada" name="precioDiez" type="number" value="' . $precioDiez . '">



					                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



					                  		</div>



					                	</div>



					              	</div>



					            </div>';

										}

									}

									?>



									<!--=====================================

								ENTRADA PARA MOSTRAR PARTIDAS JSON

								======================================-->

									<?php

									if (is_array($partidas) || is_object($partidas)) {

										foreach ($partidas as $key => $itemDetallesPartidas) {






											$itemPartida = "descripcion";

											$descripcioPartida = $itemDetallesPartidas["descripcion"];



											$itemPartida = "precioPartida";

											$valorProducto = $itemDetallesPartidas["precioPartida"];



											if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "secretaria") {



												echo '<div class="form-group row">



							              				<div class="col-xs-6">



							                				<div class="input-group"> 



											                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>



																<textarea type="text" maxlength="320" rows="3" class="form-control  input-lg text-uppercase NuevaPartidaAgregada" readonly>' . $itemDetallesPartidas["descripcion"] . '</textarea> 



							                				</div> 



							              				</div>



							              		<div>



							                	<div class="col-xs-6">



							                  		<div class="input-group">



							                    		<input class="form-control input-lg precioPartidaGuardada precioPartidaListada" type="number" value="' . $itemDetallesPartidas["precioPartida"] . '" readonly>

														

														<input type="hidden"  name="partidaYaListada">



							                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



							                  		</div>



							                	</div>



							              	</div>



							            </div>';



											} else if ($_SESSION["perfil"] == "administrador") {





												echo '<div class="form-group row">



						              				<div class="col-xs-6">



						                				<div class="input-group"> 



										                  <span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>



															<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase NuevaPartidaAgregada text-uppercase">' . $itemDetallesPartidas["descripcion"] . '</textarea> 



						                				</div> 



						              				</div>



						              		<div>



						                	<div class="col-xs-6">



						                  		<div class="input-group">



						                    		<input class="form-control input-lg precioPartidaGuardada precioPartidaListada" type="number" value="' . $itemDetallesPartidas["precioPartida"] . '">



													

						                    		<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>



						                  		</div>



						                	</div>



						              	</div>



						            </div>';

											}



										}
									}

									if ($_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "secretaria") {

										echo '</br></br>

									<div class="nuevaRecarga">

									</div>

									<div class="nuevaPartidaTecnicoDos">

										<input type="hidden" id="listarPartidasTecnicoDos" name="partidasTecnicoDos">

										<input type="hidden" id="TotalPartidasTecnicoDos" name="TotalPartidasTecnicoDos">

									</div>';

										echo '<!--=============================
									MOSTRAR RECARGA DE CARTUCHO
									=================================-->';
										if ($recarga != "") {

											echo '<div class="form-group row">
	    										
	    										<div class="col-xs-6">
													
													<div class="input-group">
														
														<span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>
															
															<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase  NuevaRecargaAgregada">' . $recarga . '</textarea>
													</div>

												</div>

												<div class="col-xs-6">

													<div class="input-group">

														<input class="form-control input-lg nuevoPrecioOrden precioPartidaGuardada preciodeRecarganueva"  type="number" value="' . $precioRecarga . '">

														<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

													</div>

												</div>

											</div>

										</div>';
										}


										echo '<!--=============================
									MOSTRAR PARTIDAS TECNICO DOS
									=================================-->';
										if ($partidasTecnicoDos != "") {

											foreach ($partidasTecnicoDos as $key => $valuePartidasTecnivosDos) {

												echo '<div class="form-group row">
		    										
		    										<div class="col-xs-6">
														
														<div class="input-group">
															
															<span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>
																
																<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase  NuevaRecargaAgregada">' . $valuePartidasTecnivosDos["descripcion"] . '</textarea>
														</div>

													</div>

													<div class="col-xs-6">

														<div class="input-group">

															<input class="form-control input-lg nuevoPrecioOrden precioPartidaGuardada preciodeRecarganueva"  type="number" value="' . $valuePartidasTecnivosDos["precioPartida"] . '">

															<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

														</div>

													</div>

												</div>

											</div>';
											}


										}

									}
									echo '<!--=====================================

                				ENTRADA PARA AGREGAR PARTIDA

                				======================================--> 

                				<div class="NuevaPartida">

								</div>';
									if ($_SESSION["perfil"] == "administrador") {



										echo '<div class="nuevaInversion">

								</div>

								<!--=====================================
                					   ENTRADA PARA INVERSIONES EN JSON
                					======================================-->';

										if (is_array($inversiones) || is_object($inversiones)) {



											foreach ($inversiones as $key => $valueinversiones) {

												if ($_SESSION["perfil"] == "administrador") {

													echo '<div class="form-group row">

													<div class="col-xs-6">

														<div class="input-group">

														<span class="input-group-addon"><button type="button" class="btn btn-danger quitarInversion" btn-lg><i class="fas fa-times"></i></button></span>

															<span class="input-group-addon">Detalle</span>

														
															<input type="text" class="form-control input-lg detalleInversion" value="' . $valueinversiones["observacion"] . '">

														</div>

													</div>

													<div>

														<div class="col-xs-6">

															<div class="input-group">

																<span class="input-group-addon">Inversion</span>

																<span class="input-group-addon"><i class="fas fa-dollar"></i></span>

																<input type="number" class="form-control input-lg precioNuevainversion" value="' . $valueinversiones["invsersion"] . '">

															</div>

														</div>

													</div>

											</div>';


												} else {

													echo '<div class="form-group row" style="display:none">

													<div class="col-xs-6">

														<div class="input-group">

														<span class="input-group-addon"><button type="button" class="btn btn-danger quitarInversion" btn-lg><i class="fas fa-times"></i></button></span>

															<span class="input-group-addon">Detalle</span>

														
															<input type="text" class="form-control input-lg detalleInversion" value="' . $valueinversiones["observacion"] . '">

														</div>

													</div>

													<div>

														<div class="col-xs-6">

															<div class="input-group">

																<span class="input-group-addon">Inversion</span>

																<span class="input-group-addon"><i class="fas fa-dollar"></i></span>

																<input type="number" class="form-control input-lg precioNuevainversion" value="' . $valueinversiones["invsersion"] . '">

															</div>

														</div>

													</div>

											</div>';

												}

											}
										}

									}
									if ($_SESSION["perfil"] == "tecnico") {

										echo '<section>

									<button type="button" class="btn pull-right btn-primary btn-md agregartipReparacion">Agregar tipo de reparación</button>
									</section>

									</br></br>

									<div class="Tipo-de-reparacion" style="display:none;">

									<div class="form-group">

										<div class="input-group">

											<span class="class="input-group-addon"><i class="class="fas fa-toggle-on"></i></span>

											<select class="form-control input-lg Tipo-repearacion-selector" name="Tipo-repearacion">

												<option id="escoger-reparacion" valu="sin tipo de reaparacion">Escoge el tipo de reparación</option>

												<option value="recarga-de-cartucho">Recarga de cartucho</option>
												<option value="servicio-externo">Servicio externo</option>

											</select>

										</div>

									</div>

									</div>';

									}
									if ($_SESSION["perfil"] == "vendedor") {

										echo '<section>

									<button type="button" class="btn pull-right btn-primary btn-md agregartipReparacion">Agregar tipo de orden</button>
									</section>

									</br></br>

									<div class="Tipo-de-orden">

									<div class="form-group">

										<div class="input-group">

											<span class="class="input-group-addon"><i class="class="fas fa-toggle-on"></i></span>

											<select class="form-control input-lg Tipo-orden">

												<option id="escoger-tipoOrden" valu="sin tipo de orden">Escoge el tipo de orden</option>

												<option value="Seguimiento-de-venta">Seguimiento de venta</option>

											</select>

										</div>

									</div>

									</div>';

									}
									?>




									<input type="hidden" id="listatOrdenesNuevas" name="listatOrdenesNuevas">




									<input type="hidden" id="listatOrdenes" name="listatOrdenes">


									<input type="hidden" class="form-control" value="<?php echo $usuario["nombre"] ?>"
										name="nombreCliente" readonly>

									<input type="hidden" class="form-control" value="<?php echo $usuario["correo"] ?>"
										name="correoCliente" readonly>

									<input type="hidden" value="<?php echo $fecha_ingreso ?>" name="fecha_ingreso">


									<input type="hidden" value="<?php echo $valor = $_GET["idOrden"]; ?>"
										name="idOrden">



									<!--=====================================

								ENTRADA PARA MOSTRAR TOTAL

								======================================-->

									<div class="form-group">



										<h4><b>TOTAL:</b></h4>



										<div class="input-group">



											<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>



											<input type="number" class="form-control input-lg" id="costoTotalDeOrden"
												name="costoTotalDeOrden" readonly>



										</div>



									</div>





									<!--=====================================

								ENTRADA PARA MOSTRAR TOTAL DE TODAS LAS 

								INVERSIONES EN LA ORDEN

								======================================-->

									<div class="form-group">



										<h4><b>Inversion</b></h4>



										<div class="input-group">





											<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>

											<?php

											if ($_SESSION["perfil"] == "administrador") {
												echo '<input type="number" name="totalInversiones" class="form-control input-lg" id="costoTotalInversiones" readonly>';
											}

											?>


										</div>



									</div>



									<button type="button" class="btn btn-primary AgregarCamposDePartida">Agregar Nueva
										Partida</button>


									<button type="button" class="btn pull-right btn-secondary btn-md agregarRecarga"
										style="margin-left: 10px"><i class="fas fa-tint"></i> Agregar Recarga</button>

									<button type="button"
										class="btn pull-right btn-info btn-md agregarPartidaSegundoTecnico"><i
											class="fas fa-cogs"></i> Agregar Partida Segundo Técnico</button>

									<button type="button" class="btn pull-right btn-success btn-md agregarInvercion"
										style="margin-right: 10px"><i class="fas fa-money"></i> Agregar
										Inversión</button>

								</div>



							</div>

							<!--<span><b>Imprimir ticket</b></span>

						<button class="btn btn-warning" data-toggle="modal"><i class="fas fa-ticket"></i></button>-->











							<?php



							$objeto = new controladorOrdenes();

							$objeto->ctrEditarOrdenDinamica();



							?>







					</div>



				</div>



			</div>



	</section>



	<?php

	$item = "id";
	$valor = $_GET["pedido"];
	$tarerPedido = ControladorPedidos::ctrMostrarPedido($item, $valor);

	foreach ($tarerPedido as $key => $valuePedidos) {


	}


	if ($valuePedidos["productoUno"] != null and $valuePedidos["productoUno"]) {

		echo '

		<section class="content">

		<div class="box">

			

			<div class="box-header with-border">

				

				<div class="box-header with-border">

					

					<ul class="todo-list">';


		foreach ($tarerPedido as $key => $valuePedidos) {

			echo '<li id="' . $valuePedidos["id"] . '">

										<div class="box-group" id="accordion">

											<div class="panel box box-info">

												<div class="box-header with-border">

													<span class="handle">

	                  									<i class="fas fa-ellipsis-v"></i>

	                  									<i class="fas fa-ellipsis-v"></i>

	                								</span>

	                								<h4 class="box-title">


	                									<a data-toggle="collapse" data-parent="#accordion" href="#collapse' . $valuePedidos["id"] . '">

	                										<p class="text-uppercase">PEDIDO ' . $valuePedidos["id"] . '</p>

	                									</a>

	                								</h4>

		                							<!--============================
		             								 MÃ“DULOS COLAPSABLES
		             								=============================-->  
		             								<div id="collapse' . $valuePedidos["id"] . '" class="panel-collapse collapse collapseSlide">


		             									<center><h3>' . $valuePedidos["productoUno"] . '</h3></center>';

			if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor") {

				echo '<select class="form-control input-lg" name="EstadoPedidoDinamico">

		             										<option>' . $valuePedidos["estado"] . '</option>
		             										<option value="Pedido Pendiente">

												                      Pedido Pendiente

												                    </option>

												                      <option value="Pedido Adquirido">

												                        Pedido Adquirido        

												                      </option> 

												                      <option value="Producto en Almacen">

												                        Producto en Almacén        

												                      </option> 
												  
												                      <option value="Entregado al asesor">

												                        Entregado al Asesor

												                      </option>

												                      <option value="Entregado/Pagado">

												                        Entregado/Pagado

												                      </option>

												                      <option value="Entregado/Credito">

												                        Entregado/Crédito

												                      </option>

												                      <option value="cancelado">

												                        cancelado

												                      </option>

		             									</select>';

			} else {

				echo "<input type='text' class='form-control input-lg' value=" . $valuePedidos["estado"] . " readonly>";
			}

			echo '</div>    

												</div>

											</div>

										</div>

								</li>';


		}







		echo '</ul>

					<!--<div class="btn-group pull-right">

						<button class="btn btn-primary guardarPedidoDinamico"><i class="fas fa-floppy-o"></i></button>

						<button class="btn btn-danger eliminarPedidoDinamico"><i class="fas fa-times"></i></button>

					</div>-->

				</div>

	



			</div>



		</div>



	</section>';



	}

	$productosPedidoDinamico = json_decode($valuePedidos["productos"], true);

	if (is_array($productosPedidoDinamico) || is_object($productosPedidoDinamico)) {

		foreach ($productosPedidoDinamico as $key => $valueProductosPedido) {



		}

	}



	if ($productosPedidoDinamico != null and $productosPedidoDinamico != "") {

		echo '<section class="content">

			<div class="box">

				

				<div class="box-header with-border">

					

					<div class="box-header with-border">

						

						<ul class="todo-list">';

		foreach ($tarerPedido as $key => $valuePedidos) {

			echo '<li id="' . $valuePedidos["id"] . '">

										<div class="box-group" id="accordion">

											<div class="panel box box-info">

												<div class="box-header with-border">

													<span class="handle">

	                  									<i class="fas fa-ellipsis-v"></i>

	                  									<i class="fas fa-ellipsis-v"></i>

	                								</span>

	                								<h4 class="box-title">


	                									<a data-toggle="collapse" data-parent="#accordion" href="#collapse' . $valuePedidos["id"] . '">

	                										

	                										<p class="text-uppercase">PEDIDO ' . $valuePedidos["id"] . '</p>

	                										<input type="hidden" value="' . $valuePedidos["id"] . '" name="idPeido">

	                									</a>

	                								</h4>

		                							<!--=====================================
		             								 MÃ“DULOS COLAPSABLES
		             								======================================-->  
		             								<div id="collapse' . $valuePedidos["id"] . '" class="panel-collapse collapse collapseSlide">';

			$productosPedidoDinamico = json_decode($valuePedidos["productos"], true);

			foreach ($productosPedidoDinamico as $key => $valueProductosPedido) {

				echo '
		             										<div class="form-group row">

		             											<div class="col-xs-6">

		             												<div class="input-group">


		             														<span class="input-group-addon"><i class="fas fa-product-hunt"></i></span>

		             														<input type=text class="form-control input-lg"value="' . $valueProductosPedido["Descripcion"] . '" readonly>


		             												</div>

		             											</div>

		             											<div class="col-xs-2">

		             												<div class="input-group">


		             														<span class="input-group-addon"><i class="fas fa-cubes"></i></span>

		             														<input type=text class="form-control input-lg"value="' . $valueProductosPedido["cantidad"] . '" readonly>


		             												</div>

		             											</div>
		             											<div class="col-xs-4">

		             												<div class="input-group">


		             														<span class="input-group-addon"><i class="fas fa-dollar"></i></span>

		             														<input type=text class="form-control input-lg"value="' . $valueProductosPedido["precio"] . '" readonly>


		             												</div>

		             											</div>

		             										</div>';
			}


			if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor") {


				echo '

		             									<div class="form-group">

		             										<div class="input-group">

		             											<span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>

		             											<select class="form-control input-lg" class="EdicionUnicaDeEstadoDePedidoEnOrden" name="EdicionUnicaDeEstadoDePedidoEnOrden">

			             										<option>' . $valuePedidos["estado"] . '</option>
			             										<option value="Pedido Pendiente">

													                      Pedido Pendiente

													                    </option>

													                      <option value="Pedido Adquirido">

													                        Pedido Adquirido        

													                      </option> 

													                      <option value="Producto en Almacen">

													                        Producto en Almacén        

													                      </option> 
													  
													                      <option value="Entregado al asesor">

													                        Entregado al Asesor

													                      </option>

													                      <option value="Entregado/Pagado">

													                        Entregado/Pagado

													                      </option>

													                      <option value="Entregado/Credito">

													                        Entregado/Crédito

													                      </option>

													                      <option value="cancelado">

													                        cancelado

													                      </option>

			             									</select>';
			} else {

				echo "<input type='text' class='form-control input-lg' value=" . $valuePedidos["estado"] . " readonly>";

			}
			echo '</div>

			             								</div>

		             								</div>    

												</div>

											</div>

										</div>

								</li>';


		}



		echo '</ul>

					<!--<div class="btn-group pull-right">

						<button class="btn btn-primary guardarPedidoDinamico"><i class="fas fa-floppy-o"></i></button>

						<button class="btn btn-danger eliminarPedidoDinamico"><i class="fas fa-times"></i></button>

					</div>-->

				</div>


			';



		echo '</div>



		</div>



	</section>

	';


	}

	?>

	<div class="col-lg-12">

		<div class="box box-danger">

			<div class="box-header with-border"></div>

			<form role="form" method="post" class="formularioObervaciones">


				<div class="box-body">

					<div class="box">

						<div class="form-group">

							<div class="input-group">

								<span class="input-group-addon"><i class="fas fa-edit"></i></span>

								<?php

								echo '<textarea type="text"  class="form-control input-lg" style="text-alinging:right; font-weight: bold;" name="observaciones">' . $descripcion . '</textarea>



											<input type="hidden" value="' . $_GET["idOrden"] . '" name="idOrden">';
								//OBBSERVACIONES DE LA TABLA observacionesOrdenes
								
								?>

							</div>

						</div>

						<!--==================================
							OBSERVACIONES EN JSON
							=================================-->
						<?php

						if (is_array($observaciones) || is_object($observaciones)) {

							foreach ($observaciones as $key => $valueObservaciones) {

								if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "secretaria") {


									echo '<div class="form-group">

												<span class="input-group-addon">' . $valueObservaciones["creador"] . '<b> </span>

												<div class="input-group">
														
													<span class="input-group-addon"><i class="fas fa-edit"></i></span>

													<textarea type="text"  class="form-control input-lg nuevaObservacion text-uppercase"  style="text-alinging:right; font-weight: bold;"readonly>' . $valueObservaciones["observacion"] . '</textarea>

												</div>

											</div>';

								} else {

									echo '<div class="form-group">

												<span class="input-group-addon"><b>' . $valueObservaciones["creador"] . '<b> </span>

												<div class="input-group">

													<span class="input-group-addon"><button type="button" class="btn btn-danger quitarObservacion" btn-lg><i class="fas fa-times"></i></button></span>

													<textarea type="text"  class="form-control input-lg nuevaObservacion text-uppercase"  style="text-alinging:right; font-weight: bold;">' . $valueObservaciones["observacion"] . '</textarea>

												</div>

											</div>';

								}
							}
						}
						?>

						<div class="NuevaObserva">

							<?php

							echo '<input type="hidden" class="usuarioQueCaptura" value="' . $_SESSION["nombre"] . '" name="usuarioQueCaptura">';

							?>

							<input type="hidden" class="form-control input-lg" id="fechaVista"></textarea>

							<input type="hidden" id="listarObservaciones" name="listarObservaciones">

							<input type="hidden" name="listarinversiones" id="listarinversiones">



						</div>

					</div>


				</div>


				<hr>
				<?php
				if ($_SESSION["perfil"] != "administrador") {
					$itemobs = $_GET["idOrden"];
					$observacionesnew = controladorObservaciones::ctrMostrarobservaciones($itemobs);
					$date = "";
					foreach ($observacionesnew as $key => $valueobs) {
						$idadmin = $valueobs["id_creador"];
						$infouser = controladorObservaciones::ctrMostrarInfoUser($idadmin);
						$infoiduser = $infouser[0];
						setlocale(LC_ALL, 'es_ES.UTF-8');
						$date = strtotime($valueobs["fecha"]);
						$fecha = strftime("%A, %d de %B de %Y | Hora: %l:%M", $date);
						echo '<div class="form-group">

												<span style="border-color:#CCC;background-color:#EEE;" class="input-group-addon"><b> ' . $infoiduser["nombre"] . ' <b> </span>

												<div class="input-group">

													<span style="background-color:#eee" class="input-group-addon"><img src="' . $infoiduser["foto"] . '" alt="Avatar" class="avatar"></span>

													<textarea type="text"  class="form-control input-lg text-uppercase"  style="text-alinging:right; font-weight: bold;" readonly>' . $valueobs["observacion"] . '</textarea>

												</div>
												<span style="float:right;color:gray"><b> Fecha:<b> ' . $fecha . ' </span>

											</div>';
					}
				} else {
					$itemobs = $_GET["idOrden"];
					$observacionesnew = controladorObservaciones::ctrMostrarobservaciones($itemobs);
					$date = "";
					foreach ($observacionesnew as $key => $valueobs) {
						$idadmin = $valueobs["id_creador"];
						$infouser = controladorObservaciones::ctrMostrarInfoUser($idadmin);
						$infoiduser = $infouser[0];
						setlocale(LC_ALL, 'es_ES.UTF-8');
						$date = strtotime($valueobs["fecha"]);
						$fecha = strftime("%A, %d de %B de %Y | Hora: %l:%M", $date);
						echo '<div class="form-group">

												<span style="border-color:#CCC;background-color:#EEE;" class="input-group-addon"><b> ' . $infoiduser["nombre"] . ' <b> <button type="button" class="btn btn-xs eliminarObservacion" idObs="' . $valueobs["id"] . '" style="float:right;color:red">Eliminar</button></span>

												<div class="input-group">

													<span style="background-color:#eee" class="input-group-addon"><img src="' . $infoiduser["foto"] . '" alt="Avatar" class="avatar"></span>

													<textarea type="text"  class="form-control input-lg text-uppercase"  style="text-alinging:right; font-weight: bold;" readonly>' . $valueobs["observacion"] . '</textarea>

												</div>
												<span style="float:right;color:gray"><b> Fecha:<b> ' . $fecha . ' </span>

											</div>';

					}
				}
				?>



				<!-- Button trigger modal -->
				<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
					Agregar observación
				</button>


				<div class="box-footer">

					<button type="submit" class="btn btn-primary pull-right" id="btncompletarorden">Guardar</button>
					<span style="color:red; float:right;" id="spanboton"></span>
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

				//$ingresarobservaciones = new controladorObservaciones();
				//$ingresarobservaciones -> ctrlCrearObservacion();
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
	<!-- Modal -->
	<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="Observacion"
		aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<center>
						<h3 class="modal-title">Observacion</h3>
					</center>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span style="color:red">&times;</span>
					</button>
				</div>
				<form method="post" class="observacion">
					<div class="modal-body">
						<textarea name="observacion" type="text" class="form-control input-lg text-uppercase" value=""
							style="text-alinging:right; font-weight: bold;" placeholder="Escribe tu Observación"
							required></textarea>
						<input name="id_orden" type="hidden" value="<?php echo $valor = $_GET["idOrden"]; ?>">
						<input name="id_creador" type="hidden" value="<?php echo $_SESSION["id"]; ?>">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-primary">Guardar observación</button>
				</form>
			</div>
		</div>
	</div>
</div>
</div>
<div class="container">
	<input type="checkbox" id="btn-mas">
	<div class="btn-mas">
		<label for="btn-mas">
			<div class="countdown"></div>
		</label>
	</div>
</div>
<script>
	$(document).ready(function () {
		if ($("#botonwhats").val().length < 10) {
			$('#linkwhats').hide();
		}
	});
	//Cuando la página esté cargada completamente

</script>

<?php

$insertarobservacion = new controladorObservaciones();
$insertarobservacion->ctrlCrearObservacion();







