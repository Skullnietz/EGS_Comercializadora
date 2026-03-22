
$('.AgregarCamposDePartida').click(function() {
	$(".NuevaPartida").append(
		'<div class="form-group row egs-partida-row">'+
			'<div class="col-xs-7 col-md-8" style="padding-right:6px">'+
				'<label style="font-size:10px;font-weight:600;color:#64748b;margin-bottom:3px;display:block"><i class="fa-solid fa-plus-circle" style="margin-right:3px"></i>Nueva partida</label>'+
				'<div class="input-group">'+
					'<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs quitarPartida"><i class="fa fa-times"></i></button></span>'+
					'<textarea type="text" maxlength="320" rows="2" class="form-control text-uppercase NuevaPartidaAgregada" placeholder="Ingresar detalles para cliente" style="font-size:13px;resize:vertical"></textarea>'+
				'</div>'+
			'</div>'+
			'<div class="col-xs-5 col-md-4" style="padding-left:6px">'+
				'<label style="font-size:10px;font-weight:600;color:#64748b;margin-bottom:3px;display:block"><i class="fa-solid fa-dollar-sign" style="margin-right:3px"></i>Precio</label>'+
				'<div class="input-group">'+
					'<span class="input-group-addon egs-dollar">$</span>'+
					'<input class="form-control nuevoPrecioOrden precioPartidaGuardada precioPartidaListada" type="number" value="0" min="0" step="any" placeholder="0.00" style="font-weight:700">'+
				'</div>'+
			'</div>'+
		'</div>');

	listaPartidas();
	listaPartidasParaSumarlasAlasYaExistentes();
	listarObservaciones();
	listarinversion();
	listaPartidasTecncioDos();
});

/*=============================================
SUMAR TOTAL EN DOCUMENTO READY
=============================================*/
$(document).ready(function(){
	var sum = 0;
	$(".precioPartidaGuardada").each(function(){
		sum += +$(this).val();
	});
	$("#costoTotalDeOrden").val(sum);

	listarObservaciones();
	listaPartidasTecncioDos();
	listaPartidas();
	listaPartidasParaSumarlasAlasYaExistentes();
	listarinversion();
});

/*=============================================
SUMAR TOTAL DE LOS ORDEN EDITADA
=============================================*/
$(document).on("change", ".precioPartidaGuardada", function() {
	var sum = 0;
	$(".precioPartidaGuardada").each(function(){
		sum += +$(this).val();
	});
	$("#costoTotalDeOrden").val(sum);

	listaPartidas();
	listaPartidasParaSumarlasAlasYaExistentes();
	listarObservaciones();
	listarinversion();
	listaPartidasTecncioDos();
});

$(document).on("change", ".NuevaPartidaAgregada", function() {
	listaPartidas();
	listaPartidasParaSumarlasAlasYaExistentes();
	listarObservaciones();
	listarinversion();
	listaPartidasTecncioDos();
});

$(".selector").change(function(){
	listaPartidas();
	listaPartidasParaSumarlasAlasYaExistentes();
	listarObservaciones();
	listaPartidasTecncioDos();
	listarinversion();
});

/*=============================================
LISTAR TODAS LAS PARTIDAS DE LAS ORDENES
=============================================*/
function listaPartidas(){
	var listarPartidas = [];
	var descripcion = $(".NuevaPartidaAgregada");
	var precio = $(".precioPartidaListada");
	for (var i = 0; i < descripcion.length; i++) {
		listarPartidas.push({"precioPartida" : $(precio[i]).val(),
							 "descripcion" : $(descripcion[i]).val()});
	}
	$("#listatOrdenes").val(JSON.stringify(listarPartidas));
}

/*=============================================
LISTAR NUEVAS PARTIDAS CUANDO YA HAY EXISTENTES
=============================================*/
function listaPartidasParaSumarlasAlasYaExistentes(){
	var listarPartidasNuevas = [];
	var descripcionDos = $(".NuevaPartidaAgregada");
	var precioDos = $(".precioPartidaListada");
	for (var i = 0; i < descripcionDos.length; i++) {
		listarPartidasNuevas.push({"precioPartida" : $(precioDos[i]).val(),
							 "descripcion" : $(descripcionDos[i]).val()});
	}
	$("#listatOrdenesNuevas").val(JSON.stringify(listarPartidasNuevas));
}

var today = new Date();
var dd = today.getDate();
var mm = today.getMonth() + 1;
var yyyy = today.getFullYear();
var fecha = mm + '/' + dd + '/' + yyyy;
var valorsesion = $('.usuarioQueCaptura').val();
$("#fechaVista").attr("fecha", fecha);

/*=============================================
AGREGAR CAMPO DE OBSERVACIÓN
=============================================*/
$('.AgregarCampoDeObservacion').click(function() {
	$(".NuevaObserva").append(
		'<div class="form-group">'+
			'<div class="input-group">'+
				'<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs quitarObservacion"><i class="fa fa-times"></i></button></span>'+
				'<textarea type="text" class="form-control text-uppercase nuevaObservacion" style="font-weight:bold"></textarea>'+
				'<input type="hidden" class="usuarioQueCaptura" value="'+valorsesion+'" name="usuarioQueCaptura">'+
			'</div>'+
		'</div>');

	listarObservaciones();
	listaPartidasTecncioDos();
	listaPartidas();
	listaPartidasParaSumarlasAlasYaExistentes();
	listarinversion();
});

$(document).on("change", ".nuevaObservacion", function() {
	listarObservaciones();
	listaPartidasTecncioDos();
	listaPartidas();
	listaPartidasParaSumarlasAlasYaExistentes();
	listarinversion();
});

/*=============================================
LISTAR OBSERVACIONES
=============================================*/
function listarObservaciones(){
	var listarnuevasObservaciones = [];
	var descripcion = $(".nuevaObservacion");
	var creador = $(".usuarioQueCaptura");
	for (var i = 0; i < descripcion.length; i++) {
		listarnuevasObservaciones.push({"observacion" : $(descripcion[i]).val(),
							 		   "creador" : $(creador).val()});
	}
	$("#listarObservaciones").val(JSON.stringify(listarnuevasObservaciones));
}

/*=============================================
BUSQUEDA EN TABLA DE ORDENES
=============================================*/
(function(document) {
	'use strict';
	var LightTableFilter = (function(Arr) {
		var _input;
		function _onInputEvent(e) {
			_input = e.target;
			var tables = document.getElementsByClassName(_input.getAttribute('data-table'));
			Arr.forEach.call(tables, function(table) {
				Arr.forEach.call(table.tBodies, function(tbody) {
					Arr.forEach.call(tbody.rows, _filter);
				});
			});
		}
		function _filter(row) {
			var text = row.textContent.toLowerCase(), val = _input.value.toLowerCase();
			row.style.display = text.indexOf(val) === -1 ? 'none' : 'table-row';
		}
		return {
			init: function() {
				var inputs = document.getElementsByClassName('light-table-filter');
				Arr.forEach.call(inputs, function(input) {
					input.oninput = _onInputEvent;
				});
			}
		};
	})(Array.prototype);
	document.addEventListener('readystatechange', function() {
		if (document.readyState === 'complete') {
			LightTableFilter.init();
		}
	});
})(document);

/*=============================================
QUITAR PARTIDAS
=============================================*/
$(document).on("click", "button.quitarPartida", function(){
	$(this).parent().parent().parent().parent().remove();

	var sum = 0;
	$(".precioPartidaGuardada").each(function(){
		sum += +$(this).val();
	});
	$("#costoTotalDeOrden").val(sum);

	listaPartidas();
	listaPartidasParaSumarlasAlasYaExistentes();
	listarObservaciones();
	listaPartidasTecncioDos();
	listarinversion();
});

/*=============================================
AGREGAR INVERSIÓN
=============================================*/
$('.agregarInvercion').click(function() {
	$(".nuevaInversion").append(
		'<div class="form-group row egs-partida-row" style="border-color:#fde68a;background:#fffbeb">'+
			'<div class="col-xs-6" style="padding-right:6px">'+
				'<label style="font-size:10px;font-weight:600;color:#ca8a04;margin-bottom:3px;display:block"><i class="fa-solid fa-coins" style="margin-right:3px"></i>Detalle inversión</label>'+
				'<div class="input-group">'+
					'<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs quitarInversion"><i class="fa fa-times"></i></button></span>'+
					'<input type="text" class="form-control detalleInversion" placeholder="Descripción">'+
				'</div>'+
			'</div>'+
			'<div class="col-xs-6" style="padding-left:6px">'+
				'<label style="font-size:10px;font-weight:600;color:#ca8a04;margin-bottom:3px;display:block">Inversión</label>'+
				'<div class="input-group">'+
					'<span class="input-group-addon egs-dollar">$</span>'+
					'<input type="number" class="form-control precioNuevainversion" min="0" step="any" placeholder="0.00" style="font-weight:700">'+
				'</div>'+
			'</div>'+
		'</div>');
});

/*=============================================
SUMAR INVERSIONES
=============================================*/
$(document).ready(function(){
	var sum = 0;
	$(".precioNuevainversion").each(function(){
		sum += +$(this).val();
	});
	$("#costoTotalInversiones").val(sum);

	listarObservaciones();
	listaPartidasTecncioDos();
	listaPartidas();
	listaPartidasParaSumarlasAlasYaExistentes();
	listarinversion();
});

/*=============================================
SUMAR TOTAL DE LAS INVERSIONES
=============================================*/
$(document).on("change", ".precioNuevainversion", function() {
	var sum = 0;
	$(".precioNuevainversion").each(function(){
		sum += +$(this).val();
	});
	$("#costoTotalInversiones").val(sum);

	listaPartidas();
	listaPartidasParaSumarlasAlasYaExistentes();
	listarObservaciones();
	listaPartidasTecncioDos();
	listarinversion();
});

/*=============================================
LISTAR INVERSIONES
=============================================*/
function listarinversion(){
	var listarinversiones = [];
	var invsersion = $(".precioNuevainversion");
	var detalle = $(".detalleInversion");
	for (var i = 0; i < invsersion.length; i++) {
		listarinversiones.push({"invsersion" : $(invsersion[i]).val(),
								"observacion" : $(detalle[i]).val()});
	}
	$("#listarinversiones").val(JSON.stringify(listarinversiones));
}

/*=============================================
QUITAR OBSERVACIONES
=============================================*/
$(document).on("click", "button.quitarObservacion", function(){
	$(this).parent().parent().parent().remove();
	listaPartidas();
	listaPartidasParaSumarlasAlasYaExistentes();
	listarObservaciones();
	listaPartidasTecncioDos();
	listarinversion();
});

/*=============================================
QUITAR INVERSIONES
=============================================*/
$(document).on("click", "button.quitarInversion", function(){
	$(this).parent().parent().parent().remove();

	var sum = 0;
	$(".precioNuevainversion").each(function(){
		sum += +$(this).val();
	});
	$("#costoTotalInversiones").val(sum);

	listaPartidas();
	listaPartidasParaSumarlasAlasYaExistentes();
	listarObservaciones();
	listarinversion();
	listaPartidasTecncioDos();
});

/*=============================================
LISTAR INVERSIONES CUANDO CAMBIE EL ESTADO
=============================================*/
$(document).on("click", "input.EdicionUnicaDeEstadoDePedidoEnOrden", function(){
	listaPartidas();
	listaPartidasParaSumarlasAlasYaExistentes();
	listarObservaciones();
	listarinversion();
	listaPartidasTecncioDos();
});

$('.agregartipReparacion').click(function() {
	$(".Tipo-de-reparacion").show();
});

/*=============================================
SUMAR PARTIDAS DEL SEGUNDO TECNICO
=============================================*/
$(document).ready(function(){
	var sum = 0;
	$(".precioPartidaTecnicoDos").each(function(){
		sum += +$(this).val();
	});
	$("#TotalPartidasTecnicoDos").val(sum);

	listarObservaciones();
	listaPartidasTecncioDos();
	listaPartidas();
	listaPartidasParaSumarlasAlasYaExistentes();
	listarinversion();
});

$(document).on("change", ".precioPartidaTecnicoDos", function() {
	var SumaPartidasTecDos = 0;
	$(".precioPartidaTecnicoDos").each(function(){
		SumaPartidasTecDos += +$(this).val();
	});
	$("#TotalPartidasTecnicoDos").val(SumaPartidasTecDos);

	listaPartidas();
	listaPartidasParaSumarlasAlasYaExistentes();
	listarObservaciones();
	listarinversion();
	listaPartidasTecncioDos();
});

/*=============================================
LISTAR TODAS LAS PARTIDAS DEL TECNICO DOS
=============================================*/
function listaPartidasTecncioDos(){
	var listarPartidasTecnicoDos = [];
	var descripcionPartidaTecnicodos = $(".NuevaPartidaSegundoTecnico");
	var precioPartidaSegundoTecnico = $(".precioPartidaTecnicoDos");
	for (var i = 0; i < descripcionPartidaTecnicodos.length; i++) {
		listarPartidasTecnicoDos.push({"precioPartida" : $(precioPartidaSegundoTecnico[i]).val(),
							 "descripcion" : $(descripcionPartidaTecnicodos[i]).val()});
	}
	$("#listarPartidasTecnicoDos").val(JSON.stringify(listarPartidasTecnicoDos));
}

/*=============================================
AGREGAR SELECTOR NUEVO TECNICO
=============================================*/
$('.agregarNuevoTecnicoAorden').click(function() {
	$(".selecnuevoTec").show();
});

/*=============================================
ELIMINAR OBSERVACIÓN (tabla observacionesOrdenes)
=============================================*/
$(document).on("click", ".eliminarObservacion", function(){
	var idObs = $(this).attr("idObs");
	var item = $(this).closest(".egs-obs-item");
	swal({
		title: "¿Eliminar esta observación?",
		text: "Esta acción no se puede deshacer",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#ef4444",
		confirmButtonText: "Sí, eliminar",
		cancelButtonText: "Cancelar",
		closeOnConfirm: false
	}, function(isConfirm){
		if(isConfirm){
			window.location = window.location.pathname + window.location.search + "&idobs=" + idObs;
		}
	});
});

