/*=============================================
CARGAR LA TABLA DINÁMICA DE CLIENTES
=============================================*/
$(document).ready(function(){

var Empresa_del_perfil = $("#Empresa_del_perfil").val();

/* Solo inicializar si estamos en la página de clientes */
if(!Empresa_del_perfil || !$(".tablaClientesOrden").length) return;

var tablaClientes = $(".tablaClientesOrden").DataTable({
	"ajax": "ajax/tablaClientes.ajax.php?empresa=" + Empresa_del_perfil,
	"deferRender": true,
	"destroy": true,
	"processing": true,

	/* Columnas ocultas para sorting (7 = órdenes, 8 = fecha raw, 9 = calificación) */
	"columnDefs": [
		{ "targets": [7, 8, 9], "visible": false, "searchable": false },
		{
			"targets": [0],
			"type": "num",
			"render": function(data, type, row){
				if(type === 'sort' || type === 'type'){
					return parseInt(data) || 0;
				}
				return data;
			}
		}
	],

	/* Default: orden por ID descendente (más recientes primero) */
	"order": [[0, "desc"]],

	"language": {
		"sProcessing":     "Procesando...",
		"sLengthMenu":     "Mostrar _MENU_ registros",
		"sZeroRecords":    "No se encontraron resultados",
		"sEmptyTable":     "Ningún dato disponible en esta tabla",
		"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
		"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
		"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
		"sInfoPostFix":    "",
		"sSearch":         "Buscar:",
		"sUrl":            "",
		"sInfoThousands":  ",",
		"sLoadingRecords": "Cargando...",
		"oPaginate": {
			"sFirst":    "Primero",
			"sLast":     "Último",
			"sNext":     "Siguiente",
			"sPrevious": "Anterior"
		},
		"oAria": {
			"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
			"sSortDescending": ": Activar para ordenar la columna de manera descendente"
		}
	}
});

/* Sincronizar botones con el orden real de la tabla */
function syncToggleButtons(order){
	$(".btn-toggle-view").removeClass("active");
	if(order.length === 1 && order[0][0] === 0){
		$("#btnOrdenID").addClass("active");
	} else if(order.length >= 1 && order[0][0] === 9 && order[0][1] === "desc"){
		$("#btnMejoresClientes").addClass("active");
	} else if(order.length >= 1 && order[0][0] === 9 && order[0][1] === "asc"){
		$("#btnMalosClientes").addClass("active");
	} else {
		$("#btnOrdenID").addClass("active");
	}
}

/* Sincronizar al hacer clic en encabezados de columna */
tablaClientes.on("order.dt", function(){
	syncToggleButtons(tablaClientes.order());
});

/*=============================================
TOGGLE: Por ID / Mejores Clientes / Malos Clientes
=============================================*/
$("#btnOrdenID").on("click", function(){
	$(".btn-toggle-view").removeClass("active");
	$(this).addClass("active");
	tablaClientes.order([[0, "desc"]]).draw();
});

$("#btnMejoresClientes").on("click", function(){
	$(".btn-toggle-view").removeClass("active");
	$(this).addClass("active");
	tablaClientes.order([[9, "desc"], [7, "desc"]]).draw();
});

$("#btnMalosClientes").on("click", function(){
	$(".btn-toggle-view").removeClass("active");
	$(this).addClass("active");
	/* Calificación ascendente (peores primero, -1 sin calificar al inicio), luego más órdenes */
	tablaClientes.order([[9, "asc"], [7, "desc"]]).draw();
});

/*=============================================
EDITAR CLIENTE
=============================================*/
$(".tablaClientesOrden").on("click", ".btnEditarCliente", function(){

	var idCliente = $(this).attr("idCliente");

	var datos = new FormData();
	datos.append("idCliente", idCliente);

	$.ajax({
		url:"ajax/clientes.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){
			$("#idCliente").val(respuesta["id"]);
			$("#editarNombreDelCliente").val(respuesta["nombre"]);
			$("#EditarCorreoCliente").val(respuesta["correo"]);
			$("#EditarNumeroDelCliente").val(respuesta["telefono"]);
			$("#EditarSegundoNumeroDeTel").val(respuesta["telefonoDos"]);
			$("#EditarAsesorDelCliente").val(respuesta["id_Asesor"]);
		}
	})
})

/*=============================================
VALIDAR NO REPETIR CLIENTES (por nombre)
=============================================*/
$(".nombreCliente").change(function(){

	$(".alert-duplicado-nombre").remove();

	var nombre = $(this).val();
	if(!nombre || nombre.trim() === "") return;

	var datos = new FormData();
	datos.append("validarDuplicadoCliente", true);
	datos.append("validarNombre", nombre);
	datos.append("validarWhatsapp", "");

	$.ajax({
	    url:"ajax/clientes.ajax.php",
	    method:"POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
	    success:function(respuesta){

    		if(respuesta.duplicado){

    			$(".nombreCliente").parent().after('<div class="alert alert-warning alert-duplicado-nombre" style="margin-top:5px"><i class="fa fa-exclamation-triangle"></i> El cliente "<strong>' + respuesta.cliente + '</strong>" ya está registrado</div>');

	    		$(".nombreCliente").val("");

    		}

	    }

   	})

})

/*=============================================
VALIDAR NO REPETIR NÚMERO DE WHATSAPP (agregar)
=============================================*/
$("#whatsapp, .telwhatsapp").on("change blur", function(){

	$(".alert-duplicado-whatsapp").remove();

	var whatsapp = $(this).val();
	if(!whatsapp || whatsapp.trim() === "") return;

	var whatsappLimpio = whatsapp.replace(/\D/g, "");
	if(whatsappLimpio.length < 10) return;

	var campo = $(this);

	var datos = new FormData();
	datos.append("validarDuplicadoCliente", true);
	datos.append("validarNombre", "");
	datos.append("validarWhatsapp", whatsappLimpio);

	$.ajax({
	    url:"ajax/clientes.ajax.php",
	    method:"POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
	    success:function(respuesta){

    		if(respuesta.duplicado){

    			campo.parent().after('<div class="alert alert-warning alert-duplicado-whatsapp" style="margin-top:5px"><i class="fa fa-exclamation-triangle"></i> Este número ya está registrado con el cliente "<strong>' + respuesta.cliente + '</strong>"</div>');

	    		campo.val("");

    		}

	    }

   	})

})

/*=============================================
VALIDAR NO REPETIR NÚMERO DE TELÉFONO (agregar)
=============================================*/
$("#telefono").on("change blur", function(){

	$(".alert-duplicado-telefono").remove();

	var telefono = $(this).val();
	if(!telefono || telefono.trim() === "") return;

	var telefonoLimpio = telefono.replace(/\D/g, "");
	if(telefonoLimpio.length < 10) return;

	var campo = $(this);

	var datos = new FormData();
	datos.append("validarDuplicadoCliente", true);
	datos.append("validarNombre", "");
	datos.append("validarWhatsapp", telefonoLimpio);

	$.ajax({
	    url:"ajax/clientes.ajax.php",
	    method:"POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
	    success:function(respuesta){

    		if(respuesta.duplicado){

    			campo.parent().after('<div class="alert alert-warning alert-duplicado-telefono" style="margin-top:5px"><i class="fa fa-exclamation-triangle"></i> Este número ya está registrado con el cliente "<strong>' + respuesta.cliente + '</strong>"</div>');

	    		campo.val("");

    		}

	    }

   	})

})

/*=============================================
VALIDAR NO REPETIR WHATSAPP AL EDITAR
=============================================*/
$("#EditarSegundoNumeroDeTel").on("change blur", function(){

	$(".alert-duplicado-whatsapp-edit").remove();

	var whatsapp = $(this).val();
	if(!whatsapp || whatsapp.trim() === "") return;

	var whatsappLimpio = whatsapp.replace(/\D/g, "");
	if(whatsappLimpio.length < 10) return;

	var idClienteActual = $("#idCliente").val();
	var campo = $(this);

	var datos = new FormData();
	datos.append("validarDuplicadoCliente", true);
	datos.append("validarNombre", "");
	datos.append("validarWhatsapp", whatsappLimpio);
	if(idClienteActual) datos.append("validarExcluirId", idClienteActual);

	$.ajax({
	    url:"ajax/clientes.ajax.php",
	    method:"POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
	    success:function(respuesta){

    		if(respuesta.duplicado){

    			campo.parent().after('<div class="alert alert-warning alert-duplicado-whatsapp-edit" style="margin-top:5px"><i class="fa fa-exclamation-triangle"></i> Este número ya está registrado con el cliente "<strong>' + respuesta.cliente + '</strong>"</div>');

	    		campo.val("");

    		}

	    }

   	})

})

/*=============================================
VALIDAR NO REPETIR TELÉFONO AL EDITAR
=============================================*/
$("#EditarNumeroDelCliente").on("change blur", function(){

	$(".alert-duplicado-telefono-edit").remove();

	var telefono = $(this).val();
	if(!telefono || telefono.trim() === "") return;

	var telefonoLimpio = telefono.replace(/\D/g, "");
	if(telefonoLimpio.length < 10) return;

	var idClienteActual = $("#idCliente").val();
	var campo = $(this);

	var datos = new FormData();
	datos.append("validarDuplicadoCliente", true);
	datos.append("validarNombre", "");
	datos.append("validarWhatsapp", telefonoLimpio);
	if(idClienteActual) datos.append("validarExcluirId", idClienteActual);

	$.ajax({
	    url:"ajax/clientes.ajax.php",
	    method:"POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
	    success:function(respuesta){

    		if(respuesta.duplicado){

    			campo.parent().after('<div class="alert alert-warning alert-duplicado-telefono-edit" style="margin-top:5px"><i class="fa fa-exclamation-triangle"></i> Este número ya está registrado con el cliente "<strong>' + respuesta.cliente + '</strong>"</div>');

	    		campo.val("");

    		}

	    }

   	})

})

/*=============================================
LIMPIAR ALERTAS AL ABRIR MODALES
=============================================*/
$("#modalAgregarUsuario").on("show.bs.modal", function(){
	$(".alert-duplicado-nombre, .alert-duplicado-whatsapp, .alert-duplicado-telefono").remove();
})
$("#modalEditarUsuario").on("show.bs.modal", function(){
	$(".alert-duplicado-whatsapp-edit, .alert-duplicado-telefono-edit").remove();
})

}); /* end document.ready */
