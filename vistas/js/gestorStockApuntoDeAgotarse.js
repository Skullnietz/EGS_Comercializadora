/*=============================================
CARGAR LA TABLA DINÁMICA DE LAS ORDENES
=============================================*/
$.ajax({

 	url:"ajax/tablaStockApuntoDeAgotarse.ajax.php",
 	success:function(respuesta){
		
 		//console.log("respuesta", respuesta);

 	}

})

$(".tablaStockApuntoAgotarse").DataTable({
	 "ajax": "ajax/tablaStockApuntoDeAgotarse.ajax.php",
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
EDITAR STOCK
=============================================*/
$('.tablaStockApuntoAgotarse').on("click", ".btnEditarStockApuntoDeAgotarse", function(){

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
			
			$("#ModalAgregarStockAgotandose .idProducto").val(respuesta[0]["id"]);
		
		}
	})

})
/*=============================================
AGREGAR CAMPOS DINAMICOS PARA ENTRADA DE ESTOCK
=============================================*/


$(document).on("click", "button.AgregarCamposProductosTicket", function(){

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
      	dataType:"json",
      	success:function(respuesta){

	$(".nuevoProductoTicket").append(

			
		'</br>'+
		'<div class="col-xs-4">'+'</br>'+
								
			'<div class="input-group">'+
									
				'<span class="input-group-addon"><i class="fa fa-product-hunt"></i></span>'+

				'<select class="form-control input-lg nuevaDescripcionProducto" id="producto'+numProducto+'"  idProducto name="nuevaDescripcionProducto" required>'+

                      	'<option>Seleccionar el producto</option>'+


                      '</select>'+

				
			'</div>'+

		'</div>'+
		'</br>'+
		'<div class="col-xs-5">'+
								
			'<div class="input-group">'+
									
				'<span class="input-group-addon"><i class="fa fa-cubes"></i></span>'+

				'<input type="number" class="form-control input-lg">'+
				'<span class="input-group-addon"><select>'+
					'<option>PZAS</option>'+
					'<option>KG</option>'+
					'<option>GRS</option>'+
				'</select></span>'+
									

			'</div>'+

		'</div>'+
		
		'<div class="col-xs-3">'+
								
			'<div class="input-group">'+
									
				'<span class="input-group-addon"><i class="fa fa-dollar"></i></span>'+

				'<input type="number" class="form-control input-lg">'+

			'</div>'+

		'</div>'+
		'</br>'
	);


					//AGREGAR LOS PRODUCTOS AL SELECT 
      			respuesta.forEach(funcionForEach);

      			function funcionForEach(item, index) {
      				if (item.disponibilidad != 0){

	      				$("#producto"+numProducto).append(

	      					'<option idProducto="'+item.id+'" value="'+item.titulo+'">'+item.titulo+'</option>'
	      				)
		      		}

		      	}

	}
	}) 

});
