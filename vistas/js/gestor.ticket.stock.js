/*=============================================
CARGAR LA TABLA DINÁMICA DE LOS PRODUCTOS A VENDER
=============================================*/
//var Perfil = $("#Perfil").val();
$.ajax({
	//url:"ajax/tablapedidos.ajax.php?perfil="+$("#Perfil").val(),
 	url:"ajax/tablaProductosTciket.ajax.php",
 	success:function(respuesta){
		
 		//console.log("respuesta", respuesta);

 	}
 })

$(".tablaProductosTicket").DataTable({
	"ajax":"ajax/tablaProductosTciket.ajax.php",
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





$(".tablaProductosTicket").on("click", "button.agregarProductoTicket", function(){


	var idProducto = $(this).attr("idProducto");
	//console.log(idProducto);

	$(this).removeClass("btn-primary agregarProductoTicket");
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

        	//DATOS DEL PRODUCTO

        	var tituloProductoParaTicket = respuesta[0]["titulo"];
 			var stockProductoParaTicket = respuesta[0]["disponibilidad"];
 			var precioProductoParaTicket = 0;
 			var medidaProductoTicket = respuesta[0]["medida"];

 			var StockInicialMasUno = Number(1) + Number(stockProductoParaTicket);

        	$(".nuevoProductoTicket").append(

	        	'<div class="row" style="padding:5px 15px">'+

	 				   '<!-- Descripción del producto-->'+
	                  
	                '<div class="col-xs-6" style="padding-right:0px">'+
	                  
	                    '<div class="input-group">'+

	                    '<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs quitarProductoDeTicket" idProducto="'+idProducto+'"><i class="fa fa-times"></i></button></span>'+

	                    '<input type="text" class="form-control nuevaDescripcionProductoTicket" name="agregarProducto" idProducto="'+idProducto+'" value="'+tituloProductoParaTicket+'" readonly required>'+

	                '</div>'+

                '</div> '+

                '<!-- Cantidad del producto -->'+

                '<div class="col-xs-3">'+

                	'<input type="number" class="form-control nuevaCantidadProductoTicket" name="nuevaCantidadProductoTicket" min="1" value="1" stockTicket="'+stockProductoParaTicket+'" nuevoStockTicket="'+Number(StockInicialMasUno)+'"  required>'+

                	

                      '<select class="form-control">'+
                      	'<option>'+medidaProductoTicket+'</option>'+
                      	'<option>PZAS</option>'+
                        '<option>GRS</option>'+
                        '<option>KG</option>'+
                        '<option>cuartillo</option>'+
                        '<option>Tapa</option>'+
                        '<option>Caja</option>'+
                        '<option>lister</option>'+

                     '</select>'+

                '</div> '+


                '<!-- Precio del producto -->'+

                  '<div class="col-xs-3 ingresoPrecioProductoTicket" style="padding-left:0px">'+

                    '<div class="input-group">'+

                      '<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>'+
                         
                      '<input type="text" class="form-control nuevoPrecioProductoTicket"  precioReal="'+precioProductoParaTicket+'" name="nuevoPrecioProductoTicket" value="'+precioProductoParaTicket+'" required>'+
         			  
                      
                    '</div>'+
                     
                  '</div>'+
                  '</div>')

        	sumarTotalPreciosProductos()
        	listarProductosTicket()
                      

        }

	})     
});

/*=============================================
MODIFICAR LA CANTIDAD 
=============================================*/
$(".formularioCrearTicket").on("change", "input.nuevaCantidadProductoTicket", function(){


	var precioProductoParaTicket = $(this).parent().parent().children(".ingresoPrecioProductoTicket").children().children(".nuevoPrecioProductoTicket");

	var presioFInalProductoTicket = $(this).val() * precioProductoParaTicket.attr("precioReal");

	precioProductoParaTicket.val(presioFInalProductoTicket);

	//console.log(presioFInalProductoTicket);


	sumarTotalPreciosProductos()
	listarProductosTicket()

	var nuevoStockTicket =  Number($(this).attr("stockTicket")) + Number( $(this).val());

	$(this).attr("nuevoStockTicket",nuevoStockTicket);


})

/*=============================================
CUANDO CARGUE LA TABLA CADA VEZ QUE NAVEGUE EN ELLA
=============================================*/

$(".tablaProductosTicket").on("draw.dt", function(){

	if(localStorage.getItem("quitarProductoDeTicket") != null){

		var listaIdProductosTicket = JSON.parse(localStorage.getItem("quitarProductoDeTicket"));

		for(var i = 0; i < listaIdProductosTicket.length; i++){

			$("button.recuperarBoton[idProducto='"+listaIdProductosTicket[i]["idProducto"]+"']").removeClass('btn-default'); 
			$("button.recuperarBoton[idProducto='"+listaIdProductosTicket[i]["idProducto"]+"']").addClass('btn-primary agregarProductoTicket');

		}


	}

})


/*=============================================
QUITAR PRODUCTOS DE LA VENTA Y RECUPERAR BOTÓN
=============================================*/
var idQuitarProducto = [];
localStorage.removeItem("quitarProductoDeTicket");
$(".formularioCrearTicket").on("click", "button.quitarProductoDeTicket", function(){


	$(this).parent().parent().parent().parent().remove();

	var idProducto = $(this).attr("idProducto");

	/*=============================================
	ALMACENAR EN EL LOCALSTORAGE EL ID DEL PRODUCTO A QUITAR
	=============================================*/

	if (localStorage.getItem("quitarProductoDeTicket") == null){

		idQuitarProducto = [];

	}else{

		idQuitarProducto.concat(localStorage.getItem("quitarProductoDeTicket"))
	}

	idQuitarProducto.push({"idProducto":idProducto});
	localStorage.setItem("quitarProductoDeTicket", JSON.stringify(idQuitarProducto));





	$("button.recuperarBoton[idProducto='"+idProducto+"']").removeClass('btn-default');

	$("button.recuperarBoton[idProducto='"+idProducto+"']").addClass('btn-primary agregarProductoTicket');


	if ($(".nuevoProductoTicket").children().length == 0){

		$("#TotalProductosTicket").val(0);

	}else{

	sumarTotalPreciosProductos()
	listarProductosTicket()


	}


})
/*=============================================
SUMAR TODOS LOS PRECIOS
=============================================*/
$(".formularioCrearTicket").on("change", "input.nuevoPrecioProductoTicket", function(){

	sumarTotalPreciosProductos() 
	listarProductosTicket()	
})
function sumarTotalPreciosProductos() {
	
	var precioCadaProducto = $(".nuevoPrecioProductoTicket");

	var arraySumarPrecioProductoTicket = [];

	for (var i = 0; i < precioCadaProducto.length; i++) {
		
		arraySumarPrecioProductoTicket.push(Number($(precioCadaProducto[i]).val()));
	}

	//console.log("arraySumarPrecioProductoTicket:", arraySumarPrecioProductoTicket);
	function sumaArrayPreciosPorducto(total, numero){

		return total + numero;

	}

	var sumaTotalPrecio = arraySumarPrecioProductoTicket.reduce(sumaArrayPreciosPorducto);
	//console.log("SumaTotalDelPrecio:", sumaTotalPrecio);

	$("#TotalProductosTicket").val(sumaTotalPrecio);
	$("#TotalProductosTicket").attr("total",sumaTotalPrecio);
}
/*=============================================
LISTAR PRODCUTOS
=============================================*/
function listarProductosTicket(){

	var listaProductosDelTicket = [];

	var titulo = $(".nuevaDescripcionProductoTicket");

	var cantidad = $(".nuevaCantidadProductoTicket");

	var precio = $(".nuevoPrecioProductoTicket");

	for (var i =0; i < titulo.length; i++) {


		listaProductosDelTicket.push({ "id" : $(titulo[i]).attr("idProducto"),
							   "titulo" : $(titulo[i]).val(), 
							   "cantidad" : $(cantidad[i]).val(),
							   "stock" : $(cantidad[i]).attr("nuevoStockTicket"),
							   "precio" : $(precio[i]).attr("precioReal"),
							   "total" : $(precio[i]).val()})
	}

	//console.log("listaProductosDelTicket", JSON.stringify(listaProductosDelTicket));

	$("#listaProductosDelTicket").val(JSON.stringify(listaProductosDelTicket));
}
/*=============================================
VER INFORMACION DEL TICKET
=============================================*/
$(document).on("click", ".btnVerInfoTikcet", function(){

	var idTicket = $(this).attr("idTicket");
	//console.log(idTicket);

	window.open("index.php?ruta=infoTicket&idTicket="+idTicket+"","_self");


})