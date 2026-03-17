/*=============================================
CARGAR LA TABLA DINÁMICA DE LAS ORDENES
=============================================*/
$.ajax({

 	url:"ajax/tablaStockAlerta.ajax.php",
 	success:function(respuesta){
		
 		//console.log("respuesta", respuesta);

 	}

})

$(".tablaStockAlerta").DataTable({
	 "ajax": "ajax/tablaStockAlerta.ajax.php",
	 "deferRender": true,
	 "retrieve": true,
	 "processing": true,
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

/*=============================================
editar stock con alerta
=============================================*/
$('.tablaStockAlerta').on("click", ".btnEditarStockAlerta", function(){

	var idProducto = $(this).attr("idProducto");

	var datos = new FormData();
	datos.append("idProducto", idProducto);

	$.ajax({

		url:"ajax/productos.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){

			//console.log("ver stock",respuesta);
		
			$("#ModalAgregarStockAgotandose .VerStock").val(respuesta[0]["disponibilidad"]);
			
			$("#ModalAgregarStockAgotandose .verInversion").val(respuesta[0]["inversion"]);

			$("#ModalAgregarStockAgotandose .idProducto").val(respuesta[0]["id"]);
	
		}
	})

})
