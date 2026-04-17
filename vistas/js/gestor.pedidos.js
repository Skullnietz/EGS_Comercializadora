/*=============================================
CARGAR LA TABLA DINÁMICA DE VENTAS
=============================================*/
//var Perfil = $("#Perfil").val();
$.ajax({
	//url:"ajax/tablapedidos.ajax.php?perfil="+$("#Perfil").val(),
  url:"ajax/tablapedidos.ajax.php?perfil="+$("#tipoDePerfil").val()+"&empresa="+$("#id_empresa").val(),
 	success:function(respuesta){
		
 		//console.log("respuesta", respuesta);

 	}
 })

$(".tablaPedidos").DataTable({
  "ajax": "ajax/tablapedidos.ajax.php?perfil="+$("#tipoDePerfil").val()+"&empresa="+$("#id_empresa").val(),
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
AGREGAR CAMPOS DINAMICAMENTE OBSERVACION
=============================================*/
var nextinput = 0;
var nextinputPrecio = 0;
var nextCantidad = 0;
function AgregarCamposPedidos(){
nextinput++;
nextinputPrecio++;
nextCantidad++;
campo = '<div class="form-group row"><div class="col-xs-6"><div class="input-group"><span class="input-group-addon"><i class="fa fa-product-hunt"></i></span><input class="form-control input-lg Producto'+nextinput+'" type="text" placeholder="Nombre Del Producto"></div></div><div class="col-xs-6"><div class="input-group"><input class="form-control input-lg precioProducto precioProducto'+nextinputPrecio+'" type="number" value="0" min="0" step="any" placeholder="Precio" id="precioUno"><span class="input-group-addon"><i class="fa fa-dollar"></i></span></div></div></div><div class="form-group"><div class="input-group"><span class="input-group-addon"><i class="fa fa-arrow-circle-up"></i></span><input type="number" class="form-control input-lg cantidadProducto cantidadProducto'+nextCantidad+'" placeholder="cantidad"></div></div>';
$("#camposProductos").append(campo);
}

/*=============================================
REALIZAR OPERACIONES PRODUCTO UNO
=============================================*/
$(document).on("change", function() {


        $(".precioProducto1").each(function() {
            var $cantidadPedidoUno = $(".cantidadProducto1").val();
            var $precioPedidoUno = $(".precioProducto1").val();
            
            var $totalUno = $cantidadPedidoUno * $precioPedidoUno;


            
            $(".pagoPedido").val($totalUno);
            $(".totalPedidoUno").val($totalUno);

        });
});
/*=============================================
REALIZAR OPERACIONES PRODUCTO DOS
=============================================*/
$(document).on("change", function() {


        $(".precioProducto2").each(function() {
            var $cantidadPedidoUno = $(".cantidadProducto1").val();
            var $precioPedidoUno = $(".precioProducto1").val();
            var $cantidadPedidoDos = $(".cantidadProducto2").val();
            var $precioPedidoDos = $(".precioProducto2").val();

            var $totalUno = $cantidadPedidoUno * $precioPedidoUno;
            var $calculoTotalDos = $cantidadPedidoDos * $precioPedidoDos;
            var $totalDos = $totalUno + $calculoTotalDos;
            
            $(".pagoPedido").val($totalDos);
            $(".totalPedidoDos").val($calculoTotalDos);
        });
});
/*=============================================
REALIZAR OPERACIONES PRODUCTO TRES
=============================================*/
$(document).on("change", function() {


        $(".precioProducto3").each(function() {
            var $cantidadPedidoUno = $(".cantidadProducto1").val();
            var $precioPedidoUno = $(".precioProducto1").val();
            var $cantidadPedidoDos = $(".cantidadProducto2").val();
            var $precioPedidoDos = $(".precioProducto2").val();
            var $cantidadPedidoTres = $(".cantidadProducto3").val();
            var $precioPedidoTres = $(".precioProducto3").val();

            var $totalUno = $cantidadPedidoUno * $precioPedidoUno;
            var $calculoTotalDos = $cantidadPedidoDos * $precioPedidoDos;
            var $calculoTotalTres = $cantidadPedidoTres * $precioPedidoTres;
            var $TotalTres = $totalUno + $calculoTotalDos + $calculoTotalTres;
            
            $(".pagoPedido").val($TotalTres);
           $(".totalPedidoTres").val($calculoTotalTres);
        });
});
/*=============================================
REALIZAR OPERACIONES PRODUCTO CUATRO
=============================================*/
$(document).on("change", function() {


        $(".precioProducto3").each(function() {
            var $cantidadPedidoUno = $(".cantidadProducto1").val();
            var $precioPedidoUno = $(".precioProducto1").val();
            var $cantidadPedidoDos = $(".cantidadProducto2").val();
            var $precioPedidoDos = $(".precioProducto2").val();
            var $cantidadPedidoTres = $(".cantidadProducto3").val();
            var $precioPedidoTres = $(".precioProducto3").val();
            var $cantidadPedidoCuatro = $(".cantidadProducto4").val();
            var $precioPedidoCuatro = $(".precioProducto4").val();

            var $totalUno = $cantidadPedidoUno * $precioPedidoUno;
            var $calculoTotalDos = $cantidadPedidoDos * $precioPedidoDos;
            var $calculoTotalTres = $cantidadPedidoTres * $precioPedidoTres;
            var $calculoTotalCuatro = $cantidadPedidoCuatro * $precioPedidoCuatro;
            var $TotalCuatro = $totalUno + $calculoTotalDos + $calculoTotalTres + $calculoTotalCuatro;

            $(".pagoPedido").val($TotalCuatro);
            $(".totalPedidoCuatro").val($calculoTotalCuatro);
        });
});
/*=============================================
REALIZAR OPERACIONES PRODUCTO CINCO
=============================================*/
$(document).on("keyup", function() {


        $(".precioProducto3").each(function() {
            var $cantidadPedidoUno = $(".cantidadProducto1").val();
            var $precioPedidoUno = $(".precioProducto1").val();
            var $cantidadPedidoDos = $(".cantidadProducto2").val();
            var $precioPedidoDos = $(".precioProducto2").val();
            var $cantidadPedidoTres = $(".cantidadProducto3").val();
            var $precioPedidoTres = $(".precioProducto3").val();
			var $cantidadPedidoCuatro = $(".cantidadProducto4").val();
            var $precioPedidoCuatro = $(".precioProducto4").val();
            var $cantidadPedidoCinco = $(".cantidadProducto5").val();
            var $precioPedidoCinco = $(".precioProducto5").val();

            var $totalUno = $cantidadPedidoUno * $precioPedidoUno;
            var $calculoTotalDos = $cantidadPedidoDos * $precioPedidoDos;
            var $calculoTotalTres = $cantidadPedidoTres * $precioPedidoTres;
            var $calculoTotalCuatro = $cantidadPedidoCuatro * $precioPedidoCuatro;
            var $calculoTotalCinco = $cantidadPedidoCinco * $precioPedidoCinco;
            var $TotalCinco = $totalUno + $calculoTotalDos + $calculoTotalTres + $calculoTotalCuatro + $calculoTotalCinco;
            
            $(".pagoPedido").val($TotalCinco);
            $(".totalPedidoCinco").val($TotalCinco);
        });
});
/*=============================================
CALCULO DE ADEUDO
=============================================*/
$(document).on("change", "#pagoClientePedido", function() {


        $("#pagoClientePedido").each(function() {

            var $pagoClientePedido = $("#pagoClientePedido").val();
            var $TotalDelPedido = $("#ResultadoPedido").val();


            var $TotalAdeudo = parseFloat($TotalDelPedido) - parseFloat($pagoClientePedido);
            
            $("#adeudo").val($TotalAdeudo);
        });
});
/*=============================================
GUARDAR EL PEDIDO
=============================================*/


$(".guardarPedido").click(function(){

   
    agregarMiPedido();          

})

function agregarMiPedido(){

        /*=============================================
        ALMACENAMOS TODOS LOS CAMPOS DE PEDIDO
        =============================================*/
       var empresaPedido = $(".empresaPedido").val();
       var AsesorPedido = $(".AsesorPedido").val();
       var clientePeido = $(".clientePeido").val();
       var Producto1 = $(".Producto1").val();
       var precioProducto1 = $(".precioProducto1").val();
       var cantidadProducto1 = $(".cantidadProducto1").val();
       var totalPedidoUno = $(".totalPedidoUno").val();
       var Producto2 = $(".Producto2").val();
       var precioProducto2 = $(".precioProducto2").val();
       var cantidadProducto2 = $(".cantidadProducto2").val();
       var totalPedidoDos = $(".totalPedidoDos").val();
       var Producto3 = $(".Producto3").val();
       var precioProducto3 = $(".precioProducto3").val();
       var cantidadProducto3 = $(".cantidadProducto3").val();
       var totalPedidoTres = $(".totalPedidoTres").val();
       var Producto4 = $(".Producto4").val();
       var precioProducto4 = $(".precioProducto4").val();
       var cantidadProducto4 = $(".cantidadProducto4").val();
       var totalPedidoCuatro = $(".totalPedidoCuatro").val();
       var Producto5 = $(".Producto5").val();
       var precioProducto5 = $(".precioProducto5").val();
       var cantidadProducto5 = $(".cantidadProducto5").val();
       var totalPedidoCinco = $(".totalPedidoCinco").val();
       var metodo = $(".metodo").val();
       var IngresarEstadoDelPedido = $(".IngresarEstadoDelPedido").val();

       var pagoClientePedido = $(".pagoClientePedido").val();
       var pagoPedido = $(".pagoPedido").val();
       var adeudo = $(".adeudo").val();
       var fechaEntrega = $(".fechaEntrega").val();
         
        var datospedido = new FormData();
        datospedido.append("empresaPedido", empresaPedido);
        datospedido.append("AsesorPedido", AsesorPedido);
        datospedido.append("clientePeido", clientePeido);
        datospedido.append("Producto1", Producto1);
        datospedido.append("precioProducto1", precioProducto1);
        datospedido.append("cantidadProducto1", cantidadProducto1);
        datospedido.append("totalPedidoUno", totalPedidoUno);
        datospedido.append("Producto2", Producto2);
        datospedido.append("precioProducto2", precioProducto2);
        datospedido.append("cantidadProducto2", cantidadProducto2);
        datospedido.append("totalPedidoDos", totalPedidoDos);
        datospedido.append("Producto3", Producto3);
        datospedido.append("precioProducto3", precioProducto3);
        datospedido.append("cantidadProducto3", cantidadProducto3);              
        datospedido.append("totalPedidoTres", totalPedidoTres);
        datospedido.append("Producto4", Producto4);
        datospedido.append("precioProducto4", precioProducto4);      
        datospedido.append("cantidadProducto4", cantidadProducto4);
        datospedido.append("totalPedidoCuatro", totalPedidoCuatro);      
        datospedido.append("Producto5", Producto5);
        datospedido.append("precioProducto5", precioProducto5);      
        datospedido.append("cantidadProducto5", cantidadProducto5);
        datospedido.append("totalPedidoCinco", totalPedidoCinco);
        datospedido.append("metodo", metodo);
        datospedido.append("pagoClientePedido", pagoClientePedido);
        datospedido.append("pagoPedido", pagoPedido);
        datospedido.append("adeudo", adeudo);
        datospedido.append("fechaEntrega", fechaEntrega);
        datospedido.append("IngresarEstadoDelPedido", IngresarEstadoDelPedido);

        $.ajax({
                url:"ajax/pedidos.ajax.php",
                method: "POST",
                data: datospedido,
                cache: false,
                contentType: false,
                processData: false,
                success: function(respuesta){
                    
                     //console.log("respuesta", respuesta);

                    if(respuesta == "ok"){

                        swal({
                          type: "success",
                          title: "El pedido ha sido guardado correctamente",
                          showConfirmButton: true,
                          confirmButtonText: "Cerrar"
                          }).then(function(result){
                            if (result.value) {

                            window.location = "pedidos";

                            }
                        })
                    }

                }

        })

}

/*=============================================
IMPRIMIR TICKET DE ORDEN
=============================================*/
$(".tablaPedidos").on("click", ".btnImprimirTicketPedido", function(){
    //console.log("Editar");
  var idPedido = $(this).attr("idPedido");
  //console.log(idPedido);
  var empresa = $(this).attr("empresa");
  var asesor = $(this).attr("asesor");
  var cliente = $(this).attr("cliente");
  var datos = new FormData();
  datos.append("idPedido", idPedido);
  datos.append("empresa", empresa);
  datos.append("asesor", asesor);
  datos.append("cliente", cliente);
  $.ajax({


    url:"ajax/pedidos.ajax.php",
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
         $("#idPedido").val(respuesta["id"]);
         $("#empresa").val(respuesta["id_empresa"]);
         $("#asesor").val(respuesta["id_Asesor"]);
         $("#cliente").val(respuesta["id_usuario"]);
    
        //console.log("Datos usuario:", respuesta);

    }

  })
  window.open("https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/ticketpedido.php/?idPedido="+idPedido+"&empresa="+empresa+"&asesor="+asesor+"&cliente="+cliente+"", "_blank");


})

/*=============================================
EDITAR PEDIDO
=============================================*/
$('.tablaPedidos tbody').on("click", ".btnEditarPedido", function(){

  var idPedido = $(this).attr("idPedido");
  var datos = new FormData();
  datos.append("idPedido", idPedido);

  $.ajax({

    url:"ajax/pedidos.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function(respuesta){ 
        
         $(".idPedido").val(respuesta[0]["id"]); 
         $(".edicionProductoUnoPedido").val(respuesta[0]["productoUno"]);
         $(".precioProductoPedidoEdicion").val(respuesta[0]["precioProductoUno"]);
         $(".cantidadDeProductoPedidoEditado").val(respuesta[0]["cantidaProductoUno"]);
         $(".pagoClientePedido").val(respuesta[0]["pagoPedido"]);
         $(".pagoPedidoEdidato").val(respuesta[0]["total"]);
         $(".adeudoPedidoEditado").val(respuesta[0]["adeudo"]);
         $(".fechaEntregaPedidoEditado").val(respuesta[0]["fechaEntrega"]);
         $(".optionEstadoPedido").html(respuesta[0]["estado"]);
         $(".optionEstadoPedido").val(respuesta[0]["estado"]);
         $(".NumeroDePedido").html(respuesta[0]["id"]);
         /*=============================================
         DATOS PEDIDO DOS
         =============================================*/
         $(".edicionProductoUnoPedidoDos").val(respuesta[0]["ProductoDos"]);
         $(".precioProductoPedidoEdicionDos").val(respuesta[0]["precioProductoDos"]);
         $(".cantidadDeProductoPedidoEditadoDos").val(respuesta[0]["cantidadProductoDos"]);
         //$(".pagoClientePedido").val(respuesta[0]["pagoPedido"]);


         /*=============================================
         DATOS PEDIDO TRES
         =============================================*/
         $(".edicionProductoUnoPedidoTres").val(respuesta[0]["ProductoTres"]);
         $(".precioProductoPedidoEdicionTres").val(respuesta[0]["precioProductoTres"]);
         $(".cantidadDeProductoPedidoEditadoTres").val(respuesta[0]["cantidadProductoTres"]);
         //$(".pagoClientePedido").val(respuesta[0]["pagoPedido"]);

         /*=============================================
         DATOS PEDIDO CUATRO
         =============================================*/
         $(".edicionProductoUnoPedidoCuatro").val(respuesta[0]["ProductoCuatro"]);
         $(".precioProductoPedidoEdicionCuatro").val(respuesta[0]["precioProductoCuatro"]);
         $(".cantidadDeProductoPedidoEditadoCuatro").val(respuesta[0]["cantidadProductoCuatro"]);
         //$(".pagoClientePedido").val(respuesta[0]["pagoPedido"]);

         /*=============================================
         DATOS PEDIDO CINCO
         =============================================*/
         $(".edicionProductoUnoPedidoCinco").val(respuesta[0]["ProductoCinco"]);
         $(".precioProductoPedidoEdicionCinco").val(respuesta[0]["precioProductoCinco"]);
         $(".cantidadDeProductoPedidoEditadoCinco").val(respuesta[0]["cantidadProductoCinco"]);
         //$(".pagoClientePedido").val(respuesta[0]["pagoPedido"]);


        //console.log("Datos pedidos:", respuesta[0]);


        if (respuesta[0]["productoUno"]!= "undefined"){

          $(".productoUnoEdicionMostrar").show();
          $(".cantidadProductosUnoPedidoEditados").show();
          
          //$(".multimediaFisica").hide();
        }

        if (respuesta[0]["ProductoDos"] != "undefined"){

          $(".productoDosEdicionMostrar").show();
          $(".cantidadProductosDosPedidoEditados").show();
          

        }else{


           $(".productoDosEdicionMostrar").hide();
           $(".cantidadProductosDosPedidoEditados").hide();
        }

        if (respuesta[0]["ProductoTres"] != "undefined"){

          $(".productoTresEdicionMostrar").show();
          $(".cantidadProductosTresPedidoEditados").show();
          

        }else{


          $(".productoTresEdicionMostrar").hide();
          $(".cantidadProductosTresPedidoEditados").hide();
        }

        if (respuesta[0]["ProductoCuatro"] != "undefined"){

          $(".productoCuatroEdicionMostrar").show();
          $(".cantidadProductosCuatroPedidoEditados").show();

        }else{

          $(".productoCuatroEdicionMostrar").hide();
          $(".cantidadProductosCuatroPedidoEditados").hide();
        }

        if (respuesta[0]["ProductoCinco"] != "undefined"){

          $(".productoCincoEdicionMostrar").show();
          $(".cantidadProductosCincoPedidoEditados").show();
          
        }else{

          $(".productoCincoEdicionMostrar").hide();
          $(".cantidadProductosCincoPedidoEditados").hide();


        }


         /*=============================================
         DATOS DE ABONO UNO
         =============================================*/
         $(".abono1Lectura").val(respuesta[0]["abonoUno"]);
         $(".fechaAbono1Lectura").val(respuesta[0]["fechaAbonoUno"]);
         
       /*=============================================
      TRAEMOS LOS ASESORES
      =============================================*/
      if (respuesta[0]["id_Asesor"] != 0){

        var datosAsesores = new FormData();
        datosAsesores.append("idAsesor", respuesta[0]["id_Asesor"]);


        $.ajax({

          url:"ajax/Asesores.ajax.php",
          method: "POST",
          data: datosAsesores,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(respuesta){

            $(".asesorDePedido").val(respuesta["nombre"]);
    
          }
        })

      }
 /*=============================================
      TRAEMOS LOS DATOS DE CLIENTES
      =============================================*/
      if (respuesta[0]["id_cliente"] != 0){

        var datosAsesores = new FormData();
        datosAsesores.append("idCliente", respuesta[0]["id_cliente"]);


        $.ajax({

          url:"ajax/clientes.ajax.php",
          method: "POST",
          data: datosAsesores,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(respuesta){
            //console.log("resp1", respuesta);
          $(".clienteNombre").val(respuesta["nombre"]);
          $(".clienteNumero").val(respuesta["telefono"]);
    
          }
        })

      }
      

    }

  })

})
/*=============================================
AGREGAR CAMPOS DINAMICAMENTE DE ABONO
=============================================*/
var nextinput = 0;
var nextinputPrecio = 0;
var nextFecha = 0;
function AgregarCampoDeaAbonoEditado(){
nextinput++;
nextinputPrecio++;
nextFecha++;
campo = '<div class="form-group row"><div class="col-xs-6"><span><h5><center>Abono</center></h5></span><div class="input-group"><span class="input-group-addon"><i class="fa fa-money"></i></span><input class="form-control input-lg abono'+nextinput+'" type="text" placeholder="Agregar Abono"></div></div><div class="col-xs-6"><span><h5><center>Fecha</center></h5></span><div class="input-group"><span class="input-group-addon"><i class="fa fa-date"></i></span><input type="date" class="form-control input-lg fechaAbono'+nextFecha+'" min="0" value="0" step="any">  </div>  </div></div>';
$("#camposAbono").append(campo);
}

/*=============================================
GUARDAR CAMBIOS DEL PEDIDO
=============================================*/ 
$(".guardarPedidoEditado").click(function(){

 btnEditarMiPedido(); 

})


function btnEditarMiPedido(){

  var idPedido = $("#modalEditarPedido .idPedido").val(); 
  var edicionProductoUnoPedido = $("#modalEditarPedido .edicionProductoUnoPedido").val();
  var abono1 = $("#modalEditarPedido .abono1").val();
  var fechaAbono1 = $("#modalEditarPedido .fechaAbono1").val();
  var edicionProductoUnoPedidoDos = $("#modalEditarPedido .edicionProductoUnoPedidoDos").val()
  var abono2 = $("#modalEditarPedido .abono2").val();
  var fechaAbono2 = $("#modalEditarPedido .fechaAbono2").val();
  var abono3 = $("#modalEditarPedido .abono3").val();
  var fechaAbono3 = $("#modalEditarPedido .fechaAbono3").val();
  var abono4 = $("#modalEditarPedido .abono4").val();
  var fechaAbono4 = $("#modalEditarPedido .fechaAbono4").val();
  var abono5 = $("#modalEditarPedido .abono5").val();
  var fechaAbono5 = $("#modalEditarPedido .fechaAbono5").val();
  var adeudoPedidoEditado = $("#modalEditarPedido .adeudoPedidoEditado").val();
  var EstadoDelPedido = $("#modalEditarPedido .EstadoDelPedido").val();


  var datosPedido = new FormData();
  datosPedido.append("id", idPedido);
  datosPedido.append("abono1", abono1);
  datosPedido.append("edicionProductoUnoPedido", edicionProductoUnoPedido);
  datosPedido.append("fechaAbono1", fechaAbono1);
  datosPedido.append("edicionProductoUnoPedidoDos", edicionProductoUnoPedidoDos);
  datosPedido.append("abono2", abono2);
  datosPedido.append("fechaAbono2", fechaAbono2);
  datosPedido.append("abono3", abono3);
  datosPedido.append("fechaAbono3", fechaAbono3);
  datosPedido.append("abono4", abono4);
  datosPedido.append("fechaAbono4", fechaAbono4);
  datosPedido.append("abono5", abono5);
  datosPedido.append("fechaAbono5", fechaAbono5);
  datosPedido.append("adeudoPedidoEditado", adeudoPedidoEditado);        
  datosPedido.append("EstadoDelPedido", EstadoDelPedido); 

  $.ajax({
      url:"ajax/pedidos.ajax.php",
      method: "POST",
      data: datosPedido,
      cache: false,
      contentType: false,
      processData: false,
      success: function(respuesta){
                  
                     
        if(respuesta == "ok"){

          swal({
            type: "success",
            title: "El abono ha sido agregado correctamente",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
            }).then(function(result){
            if (result.value) {

            window.location = "pedidos";

            }
          })
        }

      }

  })
  
}

/*=============================================
REALIZAR OPERACIONES DE ABONO UNO 
=============================================*/
$(document).on("change", function() {


        $(".abono1").each(function() {
            var $adeudoDelPedido = $(".adeudoPedidoEditado").val();
            var $primerAbono = $(".abono1").val();

            var $totalNuevoAdeudo = $adeudoDelPedido - $primerAbono;

            $(".adeudoPedidoEditado").val($totalNuevoAdeudo);
        });
});
/*=============================================
REALIZAR OPERACIONES DE ABONO DOS 
=============================================*/
$(document).on("change", function() {


        $(".abono2").each(function() {
            var $pagoTotal = $(".pagoPedidoEdidato").val();
            var $pagoInicial = $(".pagoClientePedido").val();
            var $primerAbono = $(".abono1").val();
            var $segudnoAbono = $(".abono2").val();
            var $totalAbonos = parseFloat($primerAbono) + parseFloat($segudnoAbono);
            var $totalNuevoAdeudoDos =  parseFloat($pagoTotal) - parseFloat($totalAbonos) - parseFloat($pagoInicial);

            $(".adeudoPedidoEditado").val($totalNuevoAdeudoDos);
        });
});
/*=============================================
REALIZAR OPERACIONES DE ABONO TRES 
=============================================*/
$(document).on("change", function() {


        $(".abono3").each(function() {
            var $pagoTotal = $(".pagoPedidoEdidato").val();
            var $pagoInicial = $(".pagoClientePedido").val();
            var $primerAbono = $(".abono1").val();
            var $segudnoAbono = $(".abono2").val();
            var $TercerAbono = $(".abono3").val();
            var $totalAbonos = parseFloat($primerAbono) + parseFloat($segudnoAbono) + parseFloat($TercerAbono);
            var $totalNuevoAdeudoTres =  parseFloat($pagoTotal) - parseFloat($totalAbonos) - parseFloat($pagoInicial);

            $(".adeudoPedidoEditado").val($totalNuevoAdeudoTres);
        });
});
/*=============================================
REALIZAR OPERACIONES DE ABONO CUATRO 
=============================================*/
$(document).on("change", function() {


        $(".abono4").each(function() {
            var $adeudoDelPedidoCuatro = $(".adeudoPedidoEditado").val();
            var $CuartoAbono = $(".abono4").val();

            var $totalNuevoAdeudoCuatro =  $adeudoDelPedidoCuatro - $CuartoAbono;

            $(".adeudoPedidoEditado").val($totalNuevoAdeudoCuatro);
        });
});
/*=============================================
REALIZAR OPERACIONES DE ABONO CINCO 
=============================================*/
$(document).on("change", function() {


        $(".abono5").each(function() {
            var $adeudoDelPedidoCinco = $(".adeudoPedidoEditado").val();
            var $QuintoAbono = $(".abono5").val();

            var $totalNuevoAdeudoCinco =  $adeudoDelPedidoCinco - $QuintoAbono;

            $(".adeudoPedidoEditado").val($totalNuevoAdeudoCinco);
        });
});

/*=============================================
ELIMINAR PEDIDO
=============================================*/

$('.tablaPedidos tbody').on("click", ".btnEliminarPedido", function(){

  var idpedido = $(this).attr("idpedido");


  swal({
    title: '¿Está seguro de borrar el pedido?',
    text: "¡Si no lo está puede cancelar la accíón!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Si, borrar Pedido!'
  }).then(function(result){

    if(result.value){

      window.location = "index.php?ruta=pedidos&idpedido="+idpedido+"";

    }

  })

})

/*=============================================
SELECTOR DE CLIENTE
=============================================*/
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Choices para el select con la clase 'clientePeido'
    document.querySelectorAll('.clientePeido').forEach(function(element) {
        new Choices(element, {
            removeItemButton: false, // Puedes habilitar si necesitas quitar elementos seleccionados
            searchEnabled: true,      // Habilita la búsqueda
            shouldSort: false         // Deshabilita la ordenación automática si lo requieres
            // Agrega aquí otras opciones de configuración según tus necesidades
        });
    });
});

/*=============================================
VER INFORMACION DEL PEDIDO
=============================================*/
$(".tablaPedidos").on("click", ".btnVerInfoPedido", function(){
    //console.log("Editar");
  var idPedido = $(this).attr("idPedido");
 //console.log(idPedido);
  var empresa = $(this).attr("empresa");
  var asesor = $(this).attr("asesor");
  var cliente = $(this).attr("cliente");
  var datos = new FormData();
  datos.append("idPedido", idPedido);
  datos.append("empresa", empresa);
  datos.append("asesor", asesor);
  datos.append("cliente", cliente);
  
  $.ajax({


    url:"ajax/pedidos.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function(respuesta){ 

     
    
        //console.log("Datos Orden:", respuesta);

    }

  })
  window.open("index.php?ruta=infopedido&idPedido="+idPedido+"&empresa="+empresa+"&asesor="+asesor+"&cliente="+cliente+"","_self");


})



/*=============================================
AGREGAR CAMPOS PAGO PEDIDO DINAMICO
=============================================*/
$('.agregarCamposPagoPedido').click(function() {

  $(".agregarCamposPago").append(

      '<div class="input-group">'+
        '<span class="input-group-addon"><i class="fa fa-money"></i></span>'+
        '<input type="number" class="form-control input-sm pagoAbonado">'+
      '</div>'+
      '</br></br>'
  
  )
  listarPrimerPago()
  listarObservacionesPedidos()
  listarNuevosPreciosDePedido()
  

});

$('.agregarCamposPagoPedido').click(function() {

  $(".nuevoCampoPagoPedido").append(

    '<div class="input-group">'+
      '<span class="input-group-addon"><i class="fa fa-table"></i></span>'+
      '<input type="date" class="form-control input-sm fechaAbono">'+
    '</div>'+
    '</br></br>'                   
  )
  listarProductosPedidoDinamico()
  listarNuevosPreciosDePedido()
});


$(document).on("change", "input.pagoAbonado", function(){

  listarProductosPedidoDinamico()
  listarNuevosPreciosDePedido()
})
$(document).on("change", "input.fechaAbono", function(){

  listarProductosPedidoDinamico()
  listarNuevosPreciosDePedido()
})

$(document).ready(function(){

  listarProductosPedidoDinamico()
  listarPrimerPago()
  listarNuevosPreciosDePedido()

});  
/*=============================================
LISTAR PRODUCTOS DEL PEDIDO
=============================================*/
function listarProductosPedidoDinamico(){

  var listarProductosPedidodianmico = [];

  var pago = $(".pagoAbonado");
  var fecha = $(".fechaAbono");

  for (var i =0; i < pago.length; i++) {

    listarProductosPedidodianmico.push({"pago" : $(pago[i]).val(),
                                        "fecha" : $(fecha[i]).val()})

  }

  $(".PagosListados").val(JSON.stringify(listarProductosPedidodianmico));
} 

/*=============================================
SELECTOR DE CLIENTE
=============================================*/
document.addEventListener('DOMContentLoaded', function() {

    // Inicializar Choices para 'seleccionarOrdenPedidoDinamico'
    document.querySelectorAll('.seleccionarOrdenPedidoDinamico').forEach(function(element) {
        new Choices(element, {
            removeItemButton: false, // Opcional: para permitir eliminar elementos seleccionados
            searchEnabled: true,      // Habilita la búsqueda
            shouldSort: false         // Deshabilita el ordenamiento automático
        });
    });

    // Inicializar Choices para 'clientePedidoDinamico'
    document.querySelectorAll('.clientePedidoDinamico').forEach(function(element) {
        new Choices(element, {
            removeItemButton: false, // Opcional: para permitir eliminar elementos seleccionados
            searchEnabled: true,      // Habilita la búsqueda
            shouldSort: false         // Deshabilita el ordenamiento automático
        });
    });
});

/*=============================================
SUMAR TOTAL DE LOS PRECIOS
=============================================*/
$(document).ready(function(){
       var sum = 0;
       $(".pagoAbonado").each(function(){
           sum += +$(this).val();
       });
       $(".totalPagosPeiddoDinamico").val(sum);

       //console.log("suma de los abonos: ", sum);
});
/*=============================================
RESTAR TOTAL DE LOS PRECIOS
=============================================*/
$(document).ready(function(){
      
      var totalPagosPeiddoDinamico = $(".totalPagosPeiddoDinamico").val();

      //console.log(totalPagosPeiddoDinamico);

      var totalPagarPedidoDinamico = $(".totalPagarPedidoDinamico").val();

       //console.log(totalPagarPedidoDinamico);

      var operacion = parseFloat(totalPagarPedidoDinamico) - parseFloat(totalPagosPeiddoDinamico);

      $(".adeudoPedidoDinamico").val(operacion);

       //console.log("adeudo: ", operacion);
});
/*=============================================
RESTAR TOTAL DE LOS PRECIOS
=============================================*/
$(document).on("change", "input.pagoAbonado", function(){

    var sumaDos = 0;
      $(".pagoAbonado").each(function(){
        
        sumaDos += +$(this).val();
      
      });

       //console.log("sma de nuevo abono",sumaDos);

    var totalPagarPedidoDinamico = $(".totalPagarPedidoDinamico").val();

    var restarNuevoPago = parseFloat(totalPagarPedidoDinamico) - parseFloat(sumaDos);

    //console.log("total Nuevo Adeudo", restarNuevoPago);

    $(".adeudoPedidoDinamico").val(restarNuevoPago);
});
/*=============================================
RESTAR CAMBIO A REGRESAR NE VENTANA MODAL
=============================================*/
$(document).on("change", "input.PagoClientePedidoDinamico", function(){

var pagoDelCliente = $(".PagoClientePedidoDinamico").val();

var TotalPedidoEnModal = $(".TotalPedidoEnOrden").val();

var calcularCambio =  parseFloat(TotalPedidoEnModal) - parseFloat(pagoDelCliente);

  $(".cambioClientePedidoDinamico").val(calcularCambio);

  listarPrimerPago()
  listarObservacionesPedidos()
  listarNuevosPreciosDePedido()


});

$(document).on("change", "input.fechaPagoVentaModal", function(){

  listarPrimerPago()
  listarObservacionesPedidos()
  listarNuevosPreciosDePedido()
  

});
/*=============================================
LISTAR PRIMER PAGO
=============================================*/
function listarPrimerPago(){

  var listarPrimerPagoPedido = [];

  var pago = $(".PagoClientePedidoDinamico");
  var fecha = $(".fechaPagoVentaModal");

  for (var i =0; i < pago.length; i++) {

    listarPrimerPagoPedido.push({"pago" : $(pago[i]).val(),
                                  "fecha" : $(fecha[i]).val()})

  }

  $(".PrimerPagolistado").val(JSON.stringify(listarPrimerPagoPedido));

  listarObservacionesPedidos()
  listarNuevosPreciosDePedido()

} 

$(document).on("change", "input.PagoClientePedidoDinamico", function(){

  var pagoParaDeudo = $(".PagoClientePedidoDinamico").val();

  var TotalPedidoEnOrden = $(".TotalPedidoEnOrden").val();


  var operacionPrimerAdeudo = parseFloat(TotalPedidoEnOrden) - parseFloat(pagoParaDeudo);

   $(".PrimerAdeudo").val(operacionPrimerAdeudo);

   //console.log("primer adeudo: " , operacionPrimerAdeudo);

   listarObservacionesPedidos()
   listarNuevosPreciosDePedido()
   

});

  var today = new Date();

  var dd = today.getDate();

  var mm = today.getMonth() + 1; //January is 0!

  var yyyy = today.getFullYear();



  var fecha = mm + '/' + dd + '/' + yyyy;



var valor_sesion = $('.usuarioQueCaptura').val();



$("#fechaVista").attr("fecha", fecha);


$('.AgregarCampoDeObservacionPedidos').click(function() {


  $( ".cajaObervacionesPedidos" ).show();

  $(".agregarcampoobervacionesPedidos").append(



    '<div class="form-group">'+



      '<div class="input-group">'+



        '<span class="input-group-addon"><button type="button" class="btn btn-danger quitarObservacion" btn-lg><i class="fa fa-times"></i></button></span>'+

          

          '<textarea type="text"  class="form-control input-lg nuevaObservacion" fecha="'+fecha+'" style="text-alinging:right; font-weight: bold;"></textarea>'+

          

          '<input type="hidden" class="usuarioQueCaptura" value="'+valor_sesion+'" name="usuarioQueCaptura">'+

                

      '</div>'+



      '</div>')

  listarObservacionesPedidos()
  listarNuevosPreciosDePedido()
  


});



$(document).on("change", ".nuevaObservacion", function() {

    listarObservacionesPedidos()
    listarNuevosPreciosDePedido()
    

});
$(document).on("change", ".descripcioParaListar", function() {

    listarObservacionesPedidos()
    listarNuevosPreciosDePedido()
    

});
$(document).on("change", ".cantidadProductoParaListar", function() {

    listarObservacionesPedidos()
    listarNuevosPreciosDePedido()
    

});
$(document).on("change", ".precioProductoParaListar", function() {

    listarObservacionesPedidos()
    listarNuevosPreciosDePedido()
    

});
/*=============================================
SUMAR TOTAL DE LOS PRECIOS DEL PEDIDO
=============================================*/

$(document).on("change", ".precioProductoParaListar", function() {
       var sum = 0;
       $(".precioProductoParaListar").each(function(){
           sum += +$(this).val();
       });
       $(".totalPagarPedidoDinamico").val(sum);
});
$(document).on("change", ".cantidadProductoParaListar", function() {
       var mult = $(".totalPagarPedidoDinamico").val();
       $(".cantidadProductoParaListar").each(function(){
           mult *= +$(this).val();
       });
       $(".totalPagarPedidoDinamico").val(mult);
});
/*=============================================
LISTAR OBERVACIONES
=============================================*/
function listarObservacionesPedidos(){

  var listarnuvasObservacionesPedidos = [];

  var descripcion = $(".nuevaObservacion");

  var creador = $(".usuarioQueCaptura");

  for (var i =0; i < descripcion.length; i++){

    listarnuvasObservacionesPedidos.push({"observacion" : $(descripcion[i]).val(), 

                     "creador" : $(creador[i]).val(),

                     "fecha" : $(descripcion[i]).attr("fecha")})

  }

  $("#listarObservacionesPedidos").val(JSON.stringify(listarnuvasObservacionesPedidos));

}

/*=============================================
LISTAR OBERVACIONES
=============================================*/
function listarNuevosPreciosDePedido(){

  var listarNuevosPrecios = [];

  var Descripcion = $(".descripcioParaListar");

  var cantidad = $(".cantidadProductoParaListar");

  var precio = $(".precioProductoParaListar");

  for (var i =0; i < Descripcion.length; i++){

    listarNuevosPrecios.push({"Descripcion" : $(Descripcion[i]).val(), 

                     "cantidad" : $(cantidad[i]).val(),

                     "precio" : $(precio[i]).val()})

  }

  $("#ListarPreciosActualizados").val(JSON.stringify(listarNuevosPrecios));

}
