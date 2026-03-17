/*=============================================
CARGAR LA TABLA DINÁMICA DE VENTAS
=============================================*/
var tipoDePerfil = $("#tipoDePerfil").val();

$(".tablaVentasRapidas").DataTable({
    "ajax": "ajax/tablaVentasR.ajax.php?perfil=" + $("#tipoDePerfil").val() + "&empresa=" + $("#id_empresa").val(),
    "deferRender": true,
    "retrieve": true,
    "processing": true,
    "language": {
        "sProcessing": "Procesando...",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sZeroRecords": "No se encontraron resultados",
        "sEmptyTable": "Ningún dato disponible en esta tabla",
        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0",
        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix": "",
        "sSearch": "Buscar:",
        "sUrl": "",
        "sInfoThousands": ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate": {
            "sFirst": "Primero",
            "sLast": "Último",
            "sNext": "Siguiente",
            "sPrevious": "Anterior"
        },
        "oAria": {
            "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
    }
});
/*=============================================
MANDAR EL TIPO DE USUARIO AL AJAX
=============================================

var tipoDePerfil = $("#tipoDePerfil").val();
$.ajax({
"ajax":"ajax/tablaVentasR.ajax.php.ajax.php?perfil="+$("#tipoDePerfil").val(),
})*/
/*=============================================
IMPRIMIR COMPRVANTE DE VENTA EN PDF
=============================================*/
$(".tablaVentasRapidas").on("click", ".btnImprimirComprovanteDeVentaR", function(){
		//console.log("Editar");
	var idventa = $(this).attr("idventa");
	var empresa = $(this).attr("empresa");
	
	var datos = new FormData();
	datos.append("idventa", idventa);
	datos.append("empresa", empresa);
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
				
				//console.log("Datos usuario:", respuesta);

		}

	})
	window.open("https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/ticketR.php/?idventa="+idventa+"&empresa="+empresa+"", "_blank");

	sumarTotalPrecios()

})

/*=============================================
AGREGAR DATOS CLIENTE
=============================================*/

var tipo = null;

$(".seleccionarDatos").change(function(){

	tipo = $(this).val();

	if(tipo == "venta"){

		$(".cantidadProductos").show();
		$(".pagoTotal").show();
		$(".Agregar").show();
		$(".Resultado").show();
		$(".nombreCliente").hide();
		$(".numeroClienteUno").hide();
		$(".numeroClienteDos").hide();
		$(".correo").hide();
		$(".direccion").hide();
	
	}else{

		$(".cantidadProductos").show();
		$(".pagoTotal").show();
		$(".Agregar").show();
		$(".Resultado").show();
		$(".nombreCliente").show();
		$(".numeroClienteUno").show();
		$(".numeroClienteDos").show();
		$(".correo").show();
		$(".direccion").show();

	}
})

/*=============================================
ELIMINAR VENTA
=============================================*/
$(".tablaVentasRapidas").on("click", ".btnEliminarVenta", function(){

  var idventa = $(this).attr("idventa");


  swal({
    title: '¿Está seguro de borrar la venta?',
    text: "¡Si no lo está puede cancelar la accíón!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Si, borrar venta!'
  }).then(function(result){

    if(result.value){

      window.location = "index.php?ruta=ventasR&idventa="+idventa;

    }

  })

})


/*=============================================
NO DEJAR ELIMINAR VENTA
=============================================*/
$(".tablaVentasRapidas").on("click", ".btnNoEliminarVenta", function(){


  swal({
    title: 'Solicita Permiso Al Administrador Para Eliminar una venta!',
    text: "¡Por Favor Preciones OK o Cancelar!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
	  }).then(function(result){

    if(result.value){

      window.location = "index.php?ruta=ventasR";

    }

  })

})

