/*=============================================
CARGAR LA TABLA DINÁMICA DE LOS PRODUCTOS A VENDER
=============================================*/
//var Perfil = $("#Perfil").val();
$.ajax({
	//url:"ajax/tablapedidos.ajax.php?perfil="+$("#Perfil").val(),
 	url:"ajax/tablaVentasDinamicas.ajax.php",
 	success:function(respuesta){
		
 		//console.log("productos venta", respuesta);

 	}
 })

$(".tablaProductosDinamicas").DataTable({
	"ajax":"ajax/tablaVentasDinamicas.ajax.php?perfil="+$("#tipoDePerfil").val()+"&empresa="+$("#id_empresa").val(),
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
CARGAR LA TABLA DINÁMICA DE VENTAS REALIZADAS
=============================================*/
//var Perfil = $("#Perfil").val();
$.ajax({
	//url:"ajax/tablapedidos.ajax.php?perfil="+$("#Perfil").val(),
 	url:"ajax/tablaVentasRelizadas.ajax.php",
 	success:function(respuesta){
		
 		//console.log("respuesta", respuesta);

 	}
 })

$(".tablaVentasDinamicasRealizadas").DataTable({
	"ajax":"ajax/tablaVentasRelizadas.ajax.php",
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
/*=================================
CLICK EVENT ENTER PARA PASAR PRODUCTOS
EN TABLA
==================================*/
$('input[type="search"]').change(function(){
     
   

		var codigoProducto = $('input[type="search"]').val();

		console.log(codigoProducto);

		var datos = new FormData();
		datos.append("codigoProducto", codigoProducto);

		$.ajax({

			url:"ajax/productos.ajax.php",
			method: "POST",
	        data: datos,
	        cache: false,
	        contentType: false,
	        processData: false,
	        dataType:"json",
	        success: function(respuesta){

	        	console.log("respuesta",respuesta);

	        	var titulo = respuesta[0]["titulo"];
 				var stock = respuesta[0]["disponibilidad"];
 				var precio = respuesta[0]["precio"];
 				var medida = respuesta[0]["medida"];
 				var idProducto = respuesta[0]["id"];
	 			/*=============================================
	          	EVITAR AGREGAR PRODUTO CUANDO EL STOCK ESTÁ EN CERO
	          	=============================================*/
	          	if (respuesta[0]["disponibilidad"] == 0){

	          		swal({
				      title: "No hay stock disponible",
				      type: "error",
				      confirmButtonText: "¡Cerrar!"
				    });

				    return;

	          	}

	          	$(".nuevoProducto").append(

 					'<div class="row" style="padding:5px 15px">'+

 				   '<!-- Descripción del producto-->'+
                  
                  '<div class="col-xs-6" style="padding-right:0px">'+
                  
                    '<div class="input-group">'+
                      
                      '<span class="input-group-addon" style="border-radius: 5px 0 0 5px ;"><button type="button" class="btn btn-danger btn-xs quitarProducto" idProducto="'+idProducto+'"><i class="fa fa-times fa-sm"></i></button></span>'+

                      '<input type="text" style="border-radius: 0px 5px 5px 0px;" class="form-control nuevaDescripcionProducto" name="agregarProducto" idProducto="'+idProducto+'" value="'+titulo+'" readonly required>'+

                    '</div>'+	

                  '</div> '+

                  '<!-- Cantidad del producto -->'+

                  '<div class="col-xs-3">'+
                    
                     '<input style="border-radius: 5px;" type="number" class="form-control nuevaCantidadProducto" name="nuevaCantidadProducto" min="1" value="1" stock="'+stock+'" nuevoStock="'+Number(stock-1)+'"  required>'+
                     '<span class="input-group-addon">'+
                     '<select class="form-control">'+
                      	'<option>'+medida+'</option>'+
                      	'<option>PZAS</option>'+
                        '<option>GRS</option>'+
                        '<option>KG</option>'+
                        '<option>cuartillo</option>'+
                        '<option>Tapa</option>'+
                        '<option class="OptionVentaPorCaja">Caja</option>'+
                        '<option>lister</option>'+
                        '</span>'+

                     '</select>'+
                  '</div> '+

                  '<!-- Precio del producto -->'+

                  '<div class="col-xs-3 ingresoPrecio" style="padding-left:0px">'+

                    '<div class="input-group">'+

                      '<span class="input-group-addon" style="border-radius: 5px 0 0 5px ;"><i class="ion ion-social-usd"></i></span>'+
                         
                      '<input type="text" style="border-radius: 0px 5px 5px 0px;" class="form-control nuevoPrecioProducto"  precioReal="'+precio+'" name="nuevoPrecioProducto" value="'+precio+'"  disabled>'+
         
                    '</div>'+
                     
                  '</div>'+
                  '</div>'	)
 
 			sumarTotalPrecios()

 			agregarImpuesto()

 			agregarDescuento()

 			agregarDescuentoPorcentaje()

 			agregarInversion()

 			listarProductos()

 			// PONER FORMATO AL PRECIO DE LOS PRODUCTOS

	        $(".nuevoPrecioProducto").number(true, 2);

        	}
    	})
	

});


$(".tablaProductosDinamicas tbody").on("click", "button.agregarProducto", function(){


	var idProducto = $(this).attr("idProducto");
	//console.log(idProducto);

	$(this).removeClass("btn-primary agregarProducto");
	$(this).addClass("btn-default");

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
        success: function(respuesta){
 			
 		    //console.log("datos de los productos:", respuesta);

 			var titulo = respuesta[0]["titulo"];
 			var titulo = respuesta[0]["titulo"];
 			var stock = respuesta[0]["disponibilidad"];
 			var precio = respuesta[0]["precio"];
 			var medida = respuesta[0]["medida"];

 			/*=============================================
          	EVITAR AGREGAR PRODUTO CUANDO EL STOCK ESTÁ EN CERO
          	=============================================*/
          	if (respuesta[0]["disponibilidad"] == 0){

          		swal({
			      title: "No hay stock disponible",
			      type: "error",
			      confirmButtonText: "¡Cerrar!"
			    });

          		$("button[idProducto='"+idProducto+"']").addClass("btn-primary agregarProducto");

			    return;

          	}


 			$(".nuevoProducto").append(

 					'<div class="row" style="padding:5px 15px">'+

 				   '<!-- Descripción del producto-->'+
                  
                  '<div class="col-xs-6" style="padding-right:0px">'+
                  
                    '<div class="input-group">'+
                      
                      '<span class="input-group-addon" style="border-radius: 5px 0 0 5px ;"><button type="button" class="btn btn-danger btn-xs quitarProducto" idProducto="'+idProducto+'"><i class="fa fa-times"></i></button></span>'+

                      '<input type="text" style="border-radius: 0px 5px 5px 0px;" class="form-control nuevaDescripcionProducto" name="agregarProducto" idProducto="'+idProducto+'" value="'+titulo+'" disabled>'+

                    '</div>'+	

                  '</div> '+

                  '<!-- Cantidad del producto -->'+

                  '<div class="col-xs-3">'+
                    
                     '<input type="number" style="border-radius: 5px;" class="form-control nuevaCantidadProducto" name="nuevaCantidadProducto" min="1" value="1" stock="'+stock+'" nuevoStock="'+Number(stock-1)+'"  required>'+
                     '<span class="input-group-addon">'+
                     '<select class="form-control">'+
                      	'<option>'+medida+'</option>'+
                      	'<option>PZAS</option>'+
                        '<option>GRS</option>'+
                        '<option>KG</option>'+
                        '<option>cuartillo</option>'+
                        '<option>Tapa</option>'+
                        '<option class="OptionVentaPorCaja">Caja</option>'+
                        '<option>lister</option>'+
                        '</span>'+

                     '</select>'+
                  '</div> '+

                  '<!-- Precio del producto -->'+

                  '<div class="col-xs-3 ingresoPrecio" style="padding-left:0px">'+

                    '<div class="input-group">'+

                      '<span class="input-group-addon" style="border-radius: 5px 0 0 5px ;"><i class="ion ion-social-usd"></i></span>'+
                         
                      '<input type="text" style="border-radius: 0px 5px 5px 0px;" class="form-control nuevoPrecioProducto"  precioReal="'+precio+'" name="nuevoPrecioProducto" value="'+precio+'" disabled>'+
         
                    '</div>'+
                     
                  '</div>'+
                  '</div>'	)
 
 			sumarTotalPrecios()

 			agregarImpuesto()

 			agregarDescuento()

 			agregarDescuentoPorcentaje()

 			agregarInversion()

 			listarProductos()

 			// PONER FORMATO AL PRECIO DE LOS PRODUCTOS

	        $(".nuevoPrecioProducto").number(true, 2);

         }


	})

});


/*=============================================
CUANDO CARGUE LA TABLA CADA VEZ QUE NAVEGUE EN ELLA
=============================================*/

$(".tablaProductosDinamicas").on("draw.dt", function(){

	if(localStorage.getItem("quitarProducto") != null){

		var listaIdProductos = JSON.parse(localStorage.getItem("quitarProducto"));

		for(var i = 0; i < listaIdProductos.length; i++){

			$("button.recuperarBoton[idProducto='"+listaIdProductos[i]["idProducto"]+"']").removeClass('btn-default');
			$("button.recuperarBoton[idProducto='"+listaIdProductos[i]["idProducto"]+"']").addClass('btn-primary agregarProducto');

		}


	}

})


/*=============================================
QUITAR PRODUCTOS DE LA VENTA Y RECUPERAR BOTÓN
=============================================*/
var idQuitarProducto = [];
localStorage.removeItem("quitarProducto");
$(".formularioVenta").on("click", "button.quitarProducto", function(){


	$(this).parent().parent().parent().parent().remove();

	var idProducto = $(this).attr("idProducto");

	/*=============================================
	ALMACENAR EN EL LOCALSTORAGE EL ID DEL PRODUCTO A QUITAR
	=============================================*/

	if (localStorage.getItem("quitarProducto") == null){

		idQuitarProducto = [];

	}else{

		idQuitarProducto.concat(localStorage.getItem("quitarProducto"))
	}

	idQuitarProducto.push({"idProducto":idProducto});
	localStorage.setItem("quitarProducto", JSON.stringify(idQuitarProducto));





	$("button.recuperarBoton[idProducto='"+idProducto+"']").removeClass('btn-default');

	$("button.recuperarBoton[idProducto='"+idProducto+"']").addClass('btn-primary agregarProducto');


	//if ($(".nuevoProducto").children().length == 0){

		//$("#nuevoImpuestoVenta").val(0);
		//$("#nuevoTotalVenta").val(0);
		//$("#totalVenta").val(0);
		//$("#nuevoTotalVenta").attr("total",0);

	//}else{

	sumarTotalPrecios()

	agregarImpuesto()

	agregarDescuento()

	agregarDescuentoPorcentaje()

	agregarInversion()

	listarProductos()

	//}


})
/*=============================================
AGREGANDO PRODUCTOS DESDE EL BOTÓN PARA DISPOSITIVOS
=============================================*/
var numProducto = 0;
$(".btnAgregarProductoMovil").click(function(){

	numProducto ++;

	var datos = new FormData();
	datos.append("traerProductosDispositivo", "ok");

	$.ajax({

		url:"ajax/productos.ajax.php",
      	method: "POST",
      	data: datos,
      	cache: false,
      	contentType: false,
      	processData: false,
      	dataType:"json",
      	success:function(respuesta){
      	    
      		$(".nuevoProducto").append(

 					'<div class="row" style="padding:5px 15px">'+

 				   '<!-- Descripción del producto-->'+
                  
                  '<div class="col-xs-6" style="padding-right:0px">'+
                  
                    '<div class="input-group">'+
                      
                      '<span class="input-group-addon" style="border-radius: 5px 0 0 5px ;"><button type="button" class="btn btn-danger btn-xs quitarProducto" idProducto><i class="fa fa-times"></i></button></span>'+

                      '<select class="form-control style="border-radius: 0px 5px 5px 0px;" nuevaDescripcionProducto" id="producto'+numProducto+'"  idProducto name="nuevaDescripcionProducto" required>'+

                      	'<option>Seleccionar el producto</option>'+


                      '</select>'+


                    '</div>'+

                  '</div> '+

                  '<!-- Cantidad del producto -->'+

                  '<div class="col-xs-3 ingresoCantidad">'+
                    
                     '<input type="number" style="border-radius: 5px;" class="form-control nuevaCantidadProducto" name="nuevaCantidadProducto" min="1" value="1" stock nuevoStock  required>'+

                  '</div> '+

                  '<!-- Precio del producto -->'+

                  '<div class="col-xs-3 ingresoPrecio" style="padding-left:0px">'+

                    '<div class="input-group">'+

                      '<span class="input-group-addon" style="border-radius: 5px 0 0 5px ;"><i class="ion ion-social-usd"></i></span>'+
                         
                      '<input type="text" style="border-radius: 0px 5px 5px 0px;"  class="form-control nuevoPrecioProducto" precioReal= name="nuevoPrecioProducto"    value="" disabled>'+
         
                    '</div>'+
                     
                  '</div>'+
                  '</div>'	);



      			//AGREGAR LOS PRODUCTOS AL SELECT 
      			respuesta.forEach(funcionForEach);

      			function funcionForEach(item, index) {
      				if (item.disponibilidad != 0){

	      				$("#producto"+numProducto).append(

	      					'<option idProducto="'+item.id+'" value="'+item.titulo+'">'+item.titulo+'</option>'
	      				)
		      		}

		      		sumarTotalPrecios()

		      		agregarImpuesto()

		      		agregarDescuento()

		      		agregarDescuentoPorcentaje()

		      		agregarInversion()


		      		// PONER FORMATO AL PRECIO DE LOS PRODUCTOS

	        		$(".nuevoPrecioProducto").number(true, 2);
      			}

      	}
    }) 	
})

$(".formularioVenta").on("change","input.nuevoPrecioProducto", function(){
	
	sumarTotalPrecios()

	agregarImpuesto()

	agregarDescuento()

	agregarDescuentoPorcentaje()

	agregarInversion()
})

/*=============================================
SELECCIOANR PRODUCTO MOVILES
=============================================*/
$(".formularioVenta").on("change", "select.nuevaDescripcionProducto", function(){

	var nombreProdcuto = $(this).val();
	//console.log(nombreProdcuto);
	var nuevoPrecioProducto = $(this).parent().parent().parent().children(".ingresoPrecio").children().children(".nuevoPrecioProducto");

	var nuevaCantidadProducto = $(this).parent().parent().parent().children(".ingresoCantidad").children(".nuevaCantidadProducto");
	
	var datos = new FormData();
	datos.append("nombreProdcuto",nombreProdcuto);

	$.ajax({

		url:"ajax/productos.ajax.php",
	    method: "POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType:"json",
	    success:function(respuesta){

	    	//console.log("respuesta titulos: ", respuesta);
	    	$(nuevaCantidadProducto).attr("stock", respuesta[0]["disponibilidad"]);
	    	$(nuevaCantidadProducto).attr("nuevoStock", Number(respuesta[0]["disponibilidad"])-1);
	    	$(nuevoPrecioProducto).val(respuesta[0]["precio"]);
	    	$(nuevoPrecioProducto).attr("precioReal", respuesta[0]["precio"]);

	    	listarProductos()

 		}
    }) 	

})
/*=============================================
MODIFICAR LA CANTIDAD
=============================================*/
$(".formularioVenta").on("change", "input.nuevaCantidadProducto", function(){


	var precio = $(this).parent().parent().children(".ingresoPrecio").children().children(".nuevoPrecioProducto")

	

	var precioFinal = $(this).val() * precio.attr("precioReal");

	precio.val(precioFinal);

	//console.log(precioFinal);

	sumarTotalPrecios()

	var nuevoStock =  Number($(this).attr("stock")) - Number( $(this).val());

	$(this).attr("nuevoStock",nuevoStock);


	if (Number($(this).val()) > Number($(this).attr("stock"))){

		/*=============================================
		SI LA CANTIDAD ES MAYOR A STOCK REGRESAR LOS VALORES INICIALES
		=============================================*/

		$(this).val(1);

		var precioFinal = $(this).val() * precio.attr("precioReal");

		precio.val(precioFinal);

		sumarTotalPrecios();


        swal({
			title: "La cantidad supera el stock disponible",
			text: "¡Solo hay " +$(this).attr("stock")+" unidades!",
			type: "error",
			confirmButtonText: "¡Cerrar!"
		});

		return;
	}

	sumarTotalPrecios()

	agregarImpuesto()

	agregarDescuento()

	agregarDescuentoPorcentaje()

	agregarInversion()

	listarProductos()
})
/*=============================================
SUMAR TODOS LOS PRECIOS
=============================================*/
function sumarTotalPrecios(){

	var precioItem = $(".nuevoPrecioProducto");

	var arraySumarPrecio = [];

	for (var i = 0; i < precioItem.length; i++) {
		
		arraySumarPrecio.push(Number($(precioItem[i]).val()));
	}

	//console.log("arraySumarPrecio:", arraySumarPrecio);
	function sumaArrayPrecios(total, numero){

		return total + numero;

	}

	var sumaTotalPrecio = arraySumarPrecio.reduce(sumaArrayPrecios);
	//console.log("SumaTotalDelPrecio:", sumaTotalPrecio);

	$("#nuevoTotalVenta").val(sumaTotalPrecio);
	$("#totalVenta").val(sumaTotalPrecio);
	$("#nuevoTotalVenta").attr("total",sumaTotalPrecio);

	// Al cambiar el total base, re-aplicar canje del monedero si el cliente tiene saldo.
	if (typeof egsCanjeAplicadoActual !== "undefined") {
		egsCanjeAplicadoActual = 0;
		if (typeof aplicarCanjeMonedero === "function") aplicarCanjeMonedero();
	}

}

/*=============================================
FUNCION AGREGAR DESCUENTO - 
=============================================*/
$("#nuevodescuentoventaEntero").change(function(){

	egsCanjeAplicadoActual = 0;
	agregarDescuento()

	agregarDescuentoPorcentaje()
	aplicarCanjeMonedero();
})
function agregarDescuento(){

	var descuentoEntero = $("#nuevodescuentoventaEntero").val();
	
	var precioTotal = $("#nuevoTotalVenta").attr("total");

	var precioDesceuntoEntero = Number(precioTotal) - Number(descuentoEntero);

	$("#nuevoTotalVenta").val(precioDesceuntoEntero);

	$("#totalVenta").val(precioDesceuntoEntero);

	$("#precioNeto").val(precioTotal);


}
/*=============================================
FUNCION AGREGAR DESCUENTO - 
=============================================*/
$("#nuevodescuentoPorcentaje").change(function(){

	egsCanjeAplicadoActual = 0;
	agregarDescuentoPorcentaje()
	aplicarCanjeMonedero();

})
function agregarDescuentoPorcentaje(){

	var descuentoPocentaje = $("#nuevodescuentoPorcentaje").val();
	
	var precioTotal = $("#nuevoTotalVenta").attr("total");

	var precioDescuento = Number(precioTotal * descuentoPocentaje/100);

	var totalconDescuento =  Number(precioTotal) - Number(precioDescuento);

	$("#nuevoTotalVenta").val(totalconDescuento);

	$("#totalVenta").val(totalconDescuento);

	$("#nuevoPrecioImpuesto").val(precioDescuento);

	$("#precioNeto").val(precioTotal);

	console.log("hola soy una consola xd");

}

/*=============================================
FUNCION AGREGAR DESCUENTO DE ACUERDO AL CLIENTE 
=============================================*/




/*=============================================
FUNCION AGREGAR IMPUESTO
=============================================*/
function agregarImpuesto(){

	var impuesto = $("#nuevoImpuestoVenta").val();
	
	var precioTotal = $("#nuevoTotalVenta").attr("total");

	var precioImpuesto = Number(precioTotal * impuesto/100);

	var totalconImpuesto = Number(precioImpuesto) + Number(precioTotal);

	$("#nuevoTotalVenta").val(totalconImpuesto);

	$("#totalVenta").val(totalconImpuesto);

	$("#nuevoPrecioImpuesto").val(precioImpuesto);

	$("#precioNeto").val(precioTotal);

}
/*=============================================
CUANDO CAMBIA EL IMPUESTO
=============================================*/
$("#nuevoImpuestoVenta").change(function(){

	agregarImpuesto()

	agregarDescuento()

	agregarDescuentoPorcentaje()
});
/*=============================================
FUNCION AGREGAR inversion
=============================================*/
function agregarInversion(){

	var inversion = $("#Inversion").val();
	
	var precioTotal = $("#nuevoTotalVenta").attr("total");

	var precioInversion = Number(precioTotal) + Number(inversion);

	$("#nuevoTotalVenta").val(precioInversion);

	$("#totalVenta").val(precioInversion);

	$("#precioNeto").val(precioTotal);

}
/*=============================================
CUANDO CAMBIA EL IMPUESTO
=============================================*/
$("#Inversion").change(function(){

	egsCanjeAplicadoActual = 0;
	agregarInversion();
	aplicarCanjeMonedero();
});

/*=============================================
MONEDERO ELECTRÓNICO EN VENTA RÁPIDA
Al seleccionar cliente se consulta el saldo.
El monto ingresado se descuenta del total final de la venta.
=============================================*/
var egsSaldoMonederoCliente = 0;

$(document).on("change", "#seleccionarCliente", function(){
	var idCliente = parseInt($(this).val(), 10);
	var nombreCliente = $(this).find("option:selected").text();

	// Sincroniza los hidden que espera el backend.
	$("#id_cliente").val(idCliente > 0 ? idCliente : 0);
	$("#nombreCliente").val(idCliente > 0 ? nombreCliente : "");

	// Resetear UI del monedero
	$("#egsMontoCanjeVenta").val(0);
	$("#egsMonederoMsg").text("");
	egsSaldoMonederoCliente = 0;

	if (!idCliente || idCliente <= 0) {
		$("#egsMonederoVentaRapida").hide();
		$("#egsSaldoMonederoLabel").text("$0.00");
		aplicarCanjeMonedero();
		return;
	}

	$.ajax({
		url: "ajax/recompensas.ajax.php",
		method: "POST",
		data: { idClienteRecompensas: idCliente },
		dataType: "json",
		success: function(resp){
			var saldo = 0;
			if (resp && typeof resp.saldo !== "undefined") saldo = parseFloat(resp.saldo) || 0;
			egsSaldoMonederoCliente = saldo;
			$("#egsSaldoMonederoLabel").text("$" + saldo.toFixed(2));
			$("#egsMontoCanjeVenta").attr("max", saldo);
			$("#egsMonederoVentaRapida").show();
			if (saldo <= 0) {
				$("#egsMonederoMsg").text("Este cliente no tiene saldo disponible.").css("color","#64748b");
				$("#egsMontoCanjeVenta").prop("disabled", true);
			} else {
				$("#egsMonederoMsg").text("Puedes aplicar hasta $" + saldo.toFixed(2) + " a esta venta.").css("color","#16a34a");
				$("#egsMontoCanjeVenta").prop("disabled", false);
			}
			aplicarCanjeMonedero();
		},
		error: function(){
			$("#egsMonederoVentaRapida").hide();
			egsSaldoMonederoCliente = 0;
			aplicarCanjeMonedero();
		}
	});
});

$(document).on("click", "#egsAplicarMaxMonedero", function(){
	var precioTotalBase = Number($("#nuevoTotalVenta").attr("total")) || 0;
	var maxAplicable = Math.min(egsSaldoMonederoCliente, precioTotalBase);
	$("#egsMontoCanjeVenta").val(maxAplicable.toFixed(2));
	aplicarCanjeMonedero();
});

$(document).on("input change", "#egsMontoCanjeVenta", function(){
	aplicarCanjeMonedero();
});

var egsCanjeAplicadoActual = 0;

function aplicarCanjeMonedero(){
	var canje = parseFloat($("#egsMontoCanjeVenta").val()) || 0;
	if (canje < 0) canje = 0;
	if (canje > egsSaldoMonederoCliente) canje = egsSaldoMonederoCliente;

	// Restaurar total "sin canje" (los mutadores previos ya aplicaron descuentos/inversión sobre #nuevoTotalVenta)
	var totalVisible = parseFloat($("#nuevoTotalVenta").val()) || 0;
	var totalSinCanje = totalVisible + egsCanjeAplicadoActual;

	if (canje > totalSinCanje) canje = totalSinCanje > 0 ? totalSinCanje : 0;

	var totalFinal = Math.max(0, totalSinCanje - canje);
	$("#egsMontoCanjeVenta").val(canje.toFixed(2));
	$("#nuevoTotalVenta").val(totalFinal.toFixed(2));
	$("#totalVenta").val(totalFinal.toFixed(2));
	egsCanjeAplicadoActual = canje;
}

/*=============================================
FORMATO AL PRECIO FINAL
=============================================*/

$("#nuevoTotalVenta").number(true, 2);

/*=============================================
SELECCIONAR METODO DE PAGO
=============================================*/
$("#nuevoMetodoPago").change(function(){


	var metodo = $(this).val();

	if(metodo == "efectivo"){

		$(this).parent().parent().removeClass("col-xs-6");
		$(this).parent().parent().addClass("col-xs-4");
		$(this).parent().parent().parent().children(".cajasMetodoDePago").html(

			'<div class="col-xs-4">'+
				
				'<div class="input-group">'+

					'<span class="input-group-addon" style="border-radius: 5px 0 0 5px ;"><i class="ion ion-social-usd"></i></span>'+

					'<input type="text" class="form-control nuevoValorEfectivo"  style="border-radius: 0px 5px 5px 0px;" name="nuevoValorEfectivo" placeholder="00000">'+
			
				'</div>'+

			'</div>'+

			'<div class="col-xs-4 capturaCambioEfectivo" style="padding-left:0px">'+

				'<div class="input-group">'+

					'<span class="input-group-addon" style="border-radius: 5px 0 0 5px ;"><i class="ion ion-social-usd"></i></span>'+

					'<input type="text" style="border-radius: 0px 5px 5px 0px;" class="form-control nuevoCambioEfectivo" naem="nuevoCambioEfectivo" placeholder="000000" readonly>'+

				'</div>'+

			'</div>'

		)

		// PONER FORMATO AL PRECIO AL PRECIO

	    $(".nuevoValorEfectivo").number(true, 2);
	    $(".nuevoCambioEfectivo").number(true, 2);

	    listarMetodosDePago()

	}else{

		$(this).parent().parent().removeClass('col-xs-4');

		$(this).parent().parent().addClass('col-xs-6');
		$(this).parent().parent().parent().children('.cajasMetodoDePago').html(

			'<div class="col-xs-6" style="padding-left:0px">'+
                        
                    '<div class="input-group">'+
                         
                      '<input stype="text" style="border-radius: 5px 0 0 5px ;" class="form-control" id="nuevoCodigoTransaccion" name="nuevoCodigoTransaccion" placeholder="Código transacción"  required>'+
                           
                      '<span class="input-group-addon" style="border-radius: 0px 5px 5px 0px;"><i class="fa fa-lock"></i></span>'+
                      
                    '</div>'+

                  '</div>')

	}

})

/*=============================================
CAMBIO DEL EFCTIVO
=============================================*/
$(".formularioVenta").on("change", "input.nuevoValorEfectivo", function(){

	var efectivo = $(this).val();

	var cambio = Number(efectivo) - Number($('#nuevoTotalVenta').val());
	var nuevoCambioEfectivo = $(this).parent().parent().parent().children('.capturaCambioEfectivo').children().children('.nuevoCambioEfectivo');

	nuevoCambioEfectivo.val(cambio);

	//console.log(cambio);

})
/*=============================================
CAMBIO DEL TRANSACCION
=============================================*/
$(".formularioVenta").on("change", "input#nuevoCodigoTransaccion", function(){

	listarMetodosDePago();
})

/*=============================================
LISTAR PRODCUTOS
=============================================*/
function listarProductos(){


	var listaProductos = [];

	var titulo = $(".nuevaDescripcionProducto");

	var cantidad = $(".nuevaCantidadProducto");

	var precio = $(".nuevoPrecioProducto");


	for (var i =0; i < titulo.length; i++) {
		
		listaProductos.push({ "id" : $(titulo[i]).attr("idProducto"),
							   "titulo" : $(titulo[i]).val(), 
							   "cantidad" : $(cantidad[i]).val(),
							   "stock" : $(cantidad[i]).attr("nuevoStock"),
							   "precio" : $(precio[i]).attr("precioReal"),
							   "total" : $(precio[i]).val()})
	}

	//console.log("listaProductos", JSON.stringify(listaProductos));

	$("#listaProductos").val(JSON.stringify(listaProductos));
}

/*=============================================
LISTAR METODO DE PAGO
=============================================*/
function listarMetodosDePago(){

	var listarMetodos = "";

	if ($("#nuevoMetodoPago").val() == "efectivo"){

		$("#listaMetodoPago").val("efectivo");
	
	}else{

		$("#listaMetodoPago").val($("#nuevoMetodoPago").val()+"-"+$("#nuevoCodigoTransaccion").val());

	}

}
/*=============================================
IMPRIMIR COMPRVANTE DE VENTA EN PDF
=============================================*/
$(".tablaVentasDinamicasRealizadas").on("click", ".imprimirTicketVentaDinamica", function(){
		//console.log("Editar");
	var idventa = $(this).attr("idventa");
	var empresa = $(this).attr("empresa");
	var asesor = $(this).attr("asesor");
	
	var datos = new FormData();
	datos.append("idventa", idventa);
	datos.append("empresa", empresa);
	datos.append("asesor", asesor);
	//console.log(empresa);
	
	$.ajax({


		url:"ajax/ventas.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){ 

		 $("#printDetalles").val(respuesta["detalles"]);
		 $("#cantidad").val(respuesta["cantidad"]);
      	 $("#cantidadPagada").val(respuesta["pago"]);
      	 $("#idventa").val(respuesta["id"]);
      	 $("#asesor").val(respuesta["id_Asesor"]);
				
				//console.log("Datos usuario:", respuesta);

		}

	})
	window.open("https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/ticketVentasD.php/?idventa="+idventa+"&empresa="+empresa+"&asesor="+asesor+"", "_blank");


})

/*=============================================
IMPRIMIR COMPRVANTE DE VENTA EN PDF
=============================================*/
$(".OptionVentaPorCaja").click(function(){

		agregarCantidadCajas()
})

function agregarCantidadCajas(){

	$(".nuevoCampoCajas").append(

		'<h1>Este Producto Contiene 10 Piezas por caja</h1>'

	);
}