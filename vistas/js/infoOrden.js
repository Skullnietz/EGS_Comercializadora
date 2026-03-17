
$('.AgregarCamposDePartida').click(function() {



	$(".NuevaPartida").append(

		'<div class="form-group row">'+

		    '<div class="col-xs-6">'+

				'<div class="input-group"> '+

					'<span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fa fa-times"></i></button></span>'+

					'<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase  NuevaPartidaAgregada" placeholder="Ingresar detalles para cliente" ></textarea>'+ 

				'</div> '+

			'</div>'+
			'<div class="col-xs-6">'+
				
				'<div class="input-group">'+

					'<input class="form-control input-lg nuevoPrecioOrden precioPartidaGuardada precioPartidaListada"  type="number" value="0"  min="0" step="any" placeholder="Precio" >'+
					'<span class="input-group-addon" ><i class="fa fa-dollar"></i></span>'+

				'</div>'+

			'</div>'+

		'</div>')

	listaPartidas()

	listaPartidasParaSumarlasAlasYaExistentes()

	listarObservaciones()

	//listarNuevasObservaciones()

	listarinversion()

	listaPartidasTecncioDos()


	

});



$(document).ready(function(){



	 var sum = 0;

       $(".precioPartidaGuardada").each(function(){

           sum += +$(this).val();

       });

       $("#costoTotalDeOrden").val(sum);



       listarObservaciones()

       //listarNuevasObservaciones()

       listaPartidasTecncioDos()


       listaPartidas()

	listaPartidasParaSumarlasAlasYaExistentes()

	listarinversion()
       

});	



/*=============================================

SUMAR TOTAL DE LOS OREDEN EDITADA

=============================================*/



$(document).on("change", ".precioPartidaGuardada", function() {

       var sum = 0;

       $(".precioPartidaGuardada").each(function(){

           sum += +$(this).val();

       });

       $("#costoTotalDeOrden").val(sum);



       listaPartidas()

       listaPartidasParaSumarlasAlasYaExistentes()



       listarObservaciones()

       //listarNuevasObservaciones()

       listarinversion()

       listaPartidasTecncioDos()


});	
$(document).on("change", ".NuevaPartidaSegundoTecnico", function() {


       listaPartidas()

       listaPartidasParaSumarlasAlasYaExistentes()



       listarObservaciones()

       //listarNuevasObservaciones()

       listarinversion()

       listaPartidasTecncioDos()


});	

$(document).on("change", ".NuevaRecargaAgregada", function() {


       listaPartidas()

       listaPartidasParaSumarlasAlasYaExistentes()



       listarObservaciones()

       //listarNuevasObservaciones()

       listarinversion()

       listaPartidasTecncioDos()


});	


$(document).on("change", ".NuevaPartidaAgregada", function() {

    

    listaPartidas()

    listaPartidasParaSumarlasAlasYaExistentes()

    listarObservaciones()

    //listarNuevasObservaciones()

    listarinversion()

    listaPartidasTecncioDos()



});

$(".selector").change(function(){

         

	listaPartidas()

    listaPartidasParaSumarlasAlasYaExistentes()

    listarObservaciones()

    //listarNuevasObservaciones()

    listaPartidasTecncioDos()

    listarinversion()

});

/*=============================================
LISTAR TODAS LAS PARTIDAS DE LAS ORDENES
=============================================*/
function listaPartidas(){

	var listarPartidas = [];

	var descripcion = $(".NuevaPartidaAgregada");

	var precio = $(".precioPartidaListada");

	for (var i =0; i < descripcion.length; i++) {

		listarPartidas.push({"precioPartida" : $(precio[i]).val(), 

							 "descripcion" : $(descripcion[i]).val()})

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



	for (var i =0; i < descripcionDos.length; i++) {



		listarPartidasNuevas.push({"precioPartida" : $(precioDos[i]).val(), 

							 "descripcion" : $(descripcionDos[i]).val()})

	}





	$("#listatOrdenesNuevas").val(JSON.stringify(listarPartidasNuevas));

}





	var today = new Date();

	var dd = today.getDate();

	var mm = today.getMonth() + 1; //January is 0!

	var yyyy = today.getFullYear();



	var fecha = mm + '/' + dd + '/' + yyyy;



var valorsesion = $('.usuarioQueCaptura').val();



$("#fechaVista").attr("fecha", fecha);





$('.AgregarCampoDeObservacion').click(function() {



	$(".NuevaObserva").append(



		'<div class="form-group">'+



			'<div class="input-group">'+



				'<span class="input-group-addon"><button type="button" class="btn btn-danger quitarObservacion" btn-lg><i class="fa fa-times"></i></button></span>'+

					

					'<textarea type="text"  class="form-control input-lg nuevaObservacion  text-uppercase"  style="text-alinging:right; font-weight: bold;"></textarea>'+

					

					'<input type="hidden" class="usuarioQueCaptura" value="'+valorsesion+'" name="usuarioQueCaptura">'+

								

			'</div>'+



			'</div>')



			listarObservaciones()

			//listarNuevasObservaciones()

			listaPartidasTecncioDos()

			listaPartidas()

	listaPartidasParaSumarlasAlasYaExistentes()	

	listarinversion()

});





$(document).on("change", ".nuevaObservacion", function() {

    

    listarObservaciones()

    //listarNuevasObservaciones()

    listaPartidasTecncioDos()

    listaPartidas()

	listaPartidasParaSumarlasAlasYaExistentes()

	listarinversion()

});











/*=============================================
LISTAR NUEVAS PARTIDAS CUANDO YA HAY EXISTENTES
=============================================*/

function listarObservaciones(){

	var listarnuevasObservaciones = [];

	var descripcion = $(".nuevaObservacion");

	var creador = $(".usuarioQueCaptura");

	for (var i =0; i < descripcion.length; i++) {



		listarnuevasObservaciones.push({"observacion" : $(descripcion[i]).val(), 

							 		   "creador" : $(creador).val()})

	}



	
	$("#listarObservaciones").val(JSON.stringify(listarnuevasObservaciones));

}


/*=============================================
LISTAR NUEVAS OBSERVACIONES
=============================================
function listarNuevasObservaciones(){


	var listarnuvasObservaciones = [];

	var descripcion = $(".nuevaObservacion");

	var creador = $(".usuarioQueCaptura");

	for (var i =0; i < descripcion.length; i++) {



		listarnuvasObservaciones.push({"observacion" : $(descripcion[i]).val(), 

							 		   "creador" : $(creador).val(),

							 		   "fecha" : $(descripcion[i]).attr("fecha")})

	}



	var listarNuevasObservacionesAgregadas = [];

	var descripcionNueva = $(".nuevaobservacionagregada");

	var creador = $(".usuarioQueCaptura"); 

	for (var i =0; i < descripcionNueva.length; i++) {

	listarNuevasObservacionesAgregadas.push({"observacion" : $(descripcionNueva[i]).val(), 

							 		   "creador" : $(creador).val(),

							 		   "fecha" : $(descripcionNueva[i]).attr("fecha")})

	}

	var observacionesCompletas = JSON.stringify(listarnuvasObservaciones)+JSON.stringify(listarNuevasObservacionesAgregadas);
	console.log(observacionesCompletas);
	//$("#listarObservaciones").val(JSON.stringify(observacionesCompletas));

}*/










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



	listaPartidas()

    listaPartidasParaSumarlasAlasYaExistentes()



    listarObservaciones()
    //listarNuevasObservaciones()
    listaPartidasTecncioDos()
    listarinversion()


});

/*=============================================

AGREGAR INVERCION

=============================================*/

$('.agregarInvercion').click(function() {



	$(".nuevaInversion").append(



		'<div class="form-group row">'+

			'<div class="col-xs-6">'+

				'<div class="input-group">'+

					
					'<span class="input-group-addon"><button type="button" class="btn btn-danger quitarInversion" btn-lg><i class="fa fa-times"></i></button></span>'+
					
					'<span class="input-group-addon">Detalle</span>'+

					'<input type="text" class="form-control input-lg detalleInversion">'+

				'</div>'+

			'</div>'+

			'<div>'+

				'<div class="col-xs-6">'+

					'<div class="input-group">'+

						'<span class="input-group-addon">Inversion</span>'+

						'<span class="input-group-addon"><i class="fa fa-dollar"></i></span>'+

						'<input type="number" class="form-control input-lg precioNuevainversion">'+


					'</div>'+

				'</div>'+

			'</div>'+

		'</div>'



	)

	

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



       listarObservaciones()
       //listarNuevasObservaciones()

       listaPartidasTecncioDos()

       listaPartidas()

	listaPartidasParaSumarlasAlasYaExistentes()

       listarinversion()

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



       listaPartidas()

       listaPartidasParaSumarlasAlasYaExistentes()



       listarObservaciones()

       //listarNuevasObservaciones()

       listaPartidasTecncioDos()


       listarinversion()

});	

/*=============================================
LISTAR INVERSIONES
=============================================*/
function listarinversion(){

	var listarinversiones = [];

	var invsersion = $(".precioNuevainversion");
	var detalle = $(".detalleInversion");

	for (var i =0; i < invsersion.length; i++) {

		listarinversiones.push({"invsersion" : $(invsersion[i]).val(),
								"observacion" : $(detalle[i]).val()})

	}

	$("#listarinversiones").val(JSON.stringify(listarinversiones));
}
/*=============================================
QUITAR OBSERVACIONES
=============================================*/

$(document).on("click", "button.quitarObservacion", function(){



	$(this).parent().parent().parent().remove();


	listaPartidas()

    listaPartidasParaSumarlasAlasYaExistentes()

    listarObservaciones()

    //listarNuevasObservaciones()

    listaPartidasTecncioDos()

    listarinversion()


});
/*=============================================
QUITAR INVERSIONES
=============================================*/

$(document).on("click", "button.quitarInversion", function(){



	$(this).parent().parent().parent().remove();


	listaPartidas()

    listaPartidasParaSumarlasAlasYaExistentes()

    listarObservaciones()

    //listarNuevasObservaciones()

    listarinversion()

    listaPartidasTecncioDos()



});

/*=============================================
LISTAR INVERCIONES CUADNO CAMBIE EL ESTADO DE LA ORDEN
=============================================*/

$(document).on("click", "input.EdicionUnicaDeEstadoDePedidoEnOrden", function(){


	listaPartidas()

    listaPartidasParaSumarlasAlasYaExistentes()

    listarObservaciones()

    //listarNuevasObservaciones()

    listarinversion()

    listaPartidasTecncioDos()


});


$('.agregartipReparacion').click(function() {

	$(".Tipo-de-reparacion").show();

	//console.log("no funciona");
});

//$('.AgregarCamposDePartida').click(function() {



	//$(".NuevaPartida").append()



	

//});

/*=============================================

AGREGAR RECARGA DE CARTUCHO

=============================================*/

$('.agregarRecarga').click(function() {



	$(".nuevaRecarga").append(



			'<div class="form-group row">'+

	    

	    '<div class="col-xs-6">'+



			'<div class="input-group"> '+



			'<span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fa fa-times"></i></button></span>'+



				'<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase  NuevaRecargaAgregada" name="nuevaRecarga" placeholder="Ingresar detalles para de la recarga" ></textarea>'+ 



			'</div> '+



		'</div>'+

	



			'<div class="col-xs-6">'+





				'<div class="input-group">'+





					'<input class="form-control input-lg nuevoPrecioOrden precioPartidaGuardada preciodeRecarganueva" name="precioRecarga"  type="number" value="0"  min="0" step="any" placeholder="Precio" >'+



					'<span class="input-group-addon" ><i class="fa fa-dollar"></i></span>'+



				'</div>'+





			'</div>'+



		'</div>'+



		'</div>'


	)

	

});

/*=============================================
SUMAR PARTIDAS DEL SEGUNDOTECNICO
=============================================*/
$(document).ready(function(){



	 var sum = 0;

       $(".precioPartidaTecnicoDos").each(function(){

           sum += +$(this).val();

       });

       $("#TotalPartidasTecnicoDos").val(sum);



       listarObservaciones()

       //listarNuevasObservaciones()

       listaPartidasTecncioDos()

       listaPartidas()

	listaPartidasParaSumarlasAlasYaExistentes()

       listarinversion()

});	


/*=============================================
SUMAR PARTIDAS DEL SEGUNDOTECNICO
=============================================*/
$(document).on("change", ".precioPartidaTecnicoDos", function() {

       var SumaPartidasTecDos = 0;

       $(".precioPartidaTecnicoDos").each(function(){

           SumaPartidasTecDos += +$(this).val();

       });

       $("#TotalPartidasTecnicoDos").val(SumaPartidasTecDos);



       listaPartidas()

       listaPartidasParaSumarlasAlasYaExistentes()

       listarObservaciones()

       //listarNuevasObservaciones()

       listarinversion()

       listaPartidasTecncioDos()


});	

/*=============================================

AGREGAR RECARGA PARTIDAS SEGUNDO TECNICO

=============================================*/

$('.agregarPartidaSegundoTecnico').click(function() {



	$(".nuevaPartidaTecnicoDos").append(



			'<div class="form-group row">'+

	    

	    '<div class="col-xs-6">'+



			'<div class="input-group"> '+



			'<span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fa fa-times"></i></button></span>'+



				'<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase  NuevaPartidaSegundoTecnico" name="NuevasPartidasSegundoTecnico" placeholder="Ingresar detalles de la partida" ></textarea>'+ 



			'</div> '+



		'</div>'+

	



			'<div class="col-xs-6">'+





				'<div class="input-group">'+



					'<input class="form-control input-lg nuevoPrecioOrden precioPartidaGuardada precioPartidaTecnicoDos" name="precioPartidaTecncioDos"  type="number" value="0"  min="0" step="any" placeholder="Precio" >'+



					'<span class="input-group-addon" ><i class="fa fa-dollar"></i></span>'+



				'</div>'+





			'</div>'+



		'</div>'+



		'</div>'


	)

	

});

/*=============================================
LISTAR TODAS LAS PARTIDAS DEL TECNICO DOS
=============================================*/
function listaPartidasTecncioDos(){

	var listarPartidasTecnicoDos = [];

	var descripcionPartidaTecnicodos = $(".NuevaPartidaSegundoTecnico");

	var precioPartidaSegundoTecnico = $(".precioPartidaTecnicoDos");

	for (var i =0; i < descripcionPartidaTecnicodos.length; i++) {

		listarPartidasTecnicoDos.push({"precioPartida" : $(precioPartidaSegundoTecnico[i]).val(), 

							 "descripcion" : $(descripcionPartidaTecnicodos[i]).val()})

	}

	$("#listarPartidasTecnicoDos").val(JSON.stringify(listarPartidasTecnicoDos));
}
/*=============================================
AGREGAR SLECTOR NUEVO TECNICO
=============================================*/
$('.agregarNuevoTecnicoAorden').click(function() {

	$(".selecnuevoTec").show();

	//console.log("no funciona");
});
