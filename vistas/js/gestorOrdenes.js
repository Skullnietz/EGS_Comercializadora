/*=============================================
CARGAR LA TABLA DINÁMICA DE LAS ORDENES
=============================================**/

var tipoDePerfil = $("#tipoDePerfil").val();
$.ajax({

 	url:"ajax/tablaOrdenes.ajax.php?perfil="+$("#tipoDePerfil").val(),
 	success:function(respuesta){
		
 		//console.log("Tabla Ordenes", respuesta);

 	}

})

$(".tablaOrdenes").DataTable({
	 "ajax": "ajax/tablaOrdenes.ajax.php?perfil="+$("#tipoDePerfil").val(),
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
SUMAR TOTAL DE LOS PRECIOS
=============================================*/

$(document).on("change", ".preciodeOrdenUno", function() {
       var sum = 0;
       $(".preciodeOrdenUno").each(function(){
           sum += +$(this).val();
       });
       $(".totalOrden").val(sum);
});

/*=============================================
SUMAR TOTAL DE LOS OREDEN EDITADA
=============================================*/

$(document).on("change", ".precioOrdenEditar", function() {
       var sum = 0;
       $(".precioOrdenEditar").each(function(){
           sum += +$(this).val();
       });
       $(".totalOrdenEditar").val(sum);
});

/*=============================================
REVISAR SI EL TITULO DEL PRODUCTO YA EXISTE
=============================================*/

$(".validarOrden").change(function(){

	$(".alert-titulo-dup").remove();

	var orden = $(this).val();
	if (!orden || orden.trim() === '') return;

	var datos = new FormData();
	datos.append("validarOrden", orden);

	$.ajax({
	    url:"ajax/ordenes.ajax.php",
	    method:"POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
	    success:function(respuesta){

    		if(respuesta.length != 0){

    			// Mostrar advertencia en el preview del título
    			var alertHtml = '<div class="alert alert-warning alert-titulo-dup" style="margin:8px 0;padding:8px 12px;font-size:12px;border-radius:6px"><i class="fa-solid fa-triangle-exclamation" style="margin-right:6px"></i>Ya existe una orden con este título. Se ajustará automáticamente al guardar.</div>';
    			if ($("#egs_tituloPreview").length) {
    				$("#egs_tituloPreview").closest('.egs-title-bar').after(alertHtml);
    			} else {
    				$(".validarOrden").parent().after(alertHtml);
    			}

    		}

	    }

   	})

})

/*=============================================
RUTA ORDEN
=============================================*/

function limpiarUrl(texto){
  var texto = texto.toLowerCase();
  texto = texto.replace(/[á]/g, 'a');
  texto = texto.replace(/[é]/g, 'e');
  texto = texto.replace(/[í]/g, 'i');
  texto = texto.replace(/[ó]/g, 'o');
  texto = texto.replace(/[ú]/g, 'u');
  texto = texto.replace(/[ñ]/g, 'n');
  texto = texto.replace(/[^a-z0-9 -]/g, '');
  texto = texto.replace(/ /g, "-");
  texto = texto.replace(/-+/g, "-");
  return texto;
}

$(".tituloOrden").change(function(){

	$(".rutaOrden").val(limpiarUrl($(".tituloOrden").val()));

})

/*=============================================
AGREGAR MULTIMEDIA CON DROPZONE
=============================================*/

var arrayFiles = [];

$(".multimediaOrden").dropzone({

	url: "/",
	addRemoveLinks: true,
	acceptedFiles: "image/jpeg, image/png",
	maxFilesize: 2,
	maxFiles: 10,
	init: function(){

		this.on("addedfile", function(file){

			arrayFiles.push(file);

			//console.log("arrayFiles", arrayFiles);

		})

		this.on("removedfile", function(file){

			var index = arrayFiles.indexOf(file);

			arrayFiles.splice(index, 1);

			//console.log("arrayFiles", arrayFiles);

		})

	}

})

/*=============================================
SUBIENDO LA FOTO DE PORTADA
=============================================*/

var imagenPortada = null;

$(".fotoPortada").change(function(){

	imagenPortada = this.files[0];
	
	/*=============================================
  	VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
  	=============================================*/

  	if(imagenPortada["type"] != "image/jpeg" && imagenPortada["type"] != "image/png"){

  		$(".fotoPortada").val("");

  		 swal({
		      title: "Error al subir la imagen",
		      text: "¡La imagen debe estar en formato JPG o PNG!",
		      type: "error",
		      confirmButtonText: "¡Cerrar!"
		    });

  	}else if(imagenPortada["size"] > 2000000){

  		$(".fotoPortada").val("");

  		 swal({
		      title: "Error al subir la imagen",
		      text: "¡La imagen no debe pesar más de 2MB!",
		      type: "error",
		      confirmButtonText: "¡Cerrar!"
		    });

  	}else{

  		var datosImagen = new FileReader;
  		datosImagen.readAsDataURL(imagenPortada);

  		$(datosImagen).on("load", function(event){

  			var rutaImagen = event.target.result;

  			$(".previsualizarPortada").attr("src", rutaImagen);

  		})

  	}

})


/*=============================================
SUBIENDO LA FOTO PRINCIPAL
=============================================*/

var imagenFotoPrincipalOrden = null;

$(".fotoPrincipal").change(function(){

	imagenFotoPrincipalOrden = this.files[0];
	
	/*=============================================
  	VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
  	=============================================*/

  	if(imagenFotoPrincipalOrden["type"] != "image/jpeg" && imagenFotoPrincipalOrden["type"] != "image/png"){

  		$(".fotoPrincipal").val("");

  		 swal({
		      title: "Error al subir la imagen",
		      text: "¡La imagen debe estar en formato JPG o PNG!",
		      type: "error",
		      confirmButtonText: "¡Cerrar!"
		    });

  	}else if(imagenFotoPrincipalOrden["size"] > 2000000){

  		$(".fotoPrincipal").val("");

  		 swal({
		      title: "Error al subir la imagen",
		      text: "¡La imagen no debe pesar más de 2MB!",
		      type: "error",
		      confirmButtonText: "¡Cerrar!"
		    });

  	}else{

  		var datosImagen = new FileReader;
  		datosImagen.readAsDataURL(imagenFotoPrincipalOrden);

  		$(datosImagen).on("load", function(event){

  			var rutaImagen = event.target.result;

  			$(".previsualizarPrincipal").attr("src", rutaImagen);

  		})

  	}

})/*=============================================
GUARDAR EL PRODUCTO
=============================================*/

var multimediaOrden = null;

$(".guardarOrden").click(function(){

	/*=============================================
	PREGUNTAMOS SI LOS CAMPOS OBLIGATORIOS ESTÁN LLENOS
	=============================================*/

	if($(".tituloOrden").val() != "" && 
	   $(".descripcionOrden").val() != ""){
	   
	   	/*=============================================
	   	PREGUNTAMOS SI VIENEN IMÁGENES PARA MULTIMEDIA O LINK DE YOUTUBE
	   	=============================================*/

	   		if(arrayFiles.length > 0 && $(".rutaOrden").val() != ""){

	   			var listaMultimedia = [];
	   			var finalFor = 0;

	   			for(var i = 0; i < arrayFiles.length; i++){

	   				var datosMultimedia = new FormData();
	   				datosMultimedia.append("file", arrayFiles[i]);
					datosMultimedia.append("ruta", $(".rutaOrden").val());

					$.ajax({
						url:"ajax/ordenes.ajax.php",
						method: "POST",
						data: datosMultimedia,
						cache: false,
						contentType: false,
						processData: false,
						beforeSend: function(){

							$(".modal-footer .preload").html(`


								<center>

									<img src="vistas/img/plantilla/status.gif" id="status" />
									<br>

								</center>

							`);

						},
						success: function(respuesta){

							$("#status").remove();
							
							listaMultimedia.push({"foto" : respuesta.substr(3)})
							multimediaOrden = JSON.stringify(listaMultimedia);

							if(multimediaOrden == null){

							 	swal({
							      title: "El campo de multimedia no debe estar vacío",
							      type: "error",
							      confirmButtonText: "¡Cerrar!"
							    });

							 	return;

							}
							if ($('.cliente').val().trim() === '') {
							       
							       swal({
							      title: "El campo de cliente no debe ir vacío",
							      type: "error",
							      confirmButtonText: "¡Cerrar!"
							    });

								return;

							}

							if((finalFor + 1) == arrayFiles.length){

								agregarMiOrden(multimediaOrden);
								finalFor = 0;

							}

							finalFor++;

						}

					})

	   			}

	   		}


	}else{

		 swal({
	      title: "Llenar todos los campos obligatorios",
	      type: "error",
	      confirmButtonText: "¡Cerrar!"
	    });

		return;
	}

})

function agregarMiOrden(imagen){

		/*=============================================
		ALMACENAMOS TODOS LOS CAMPOS DE PRODUCTO
		=============================================*/
	   var empresa = $(".empresa").val();
	   var tituloOrden = $(".tituloOrden").val();
	   var rutaOrden = $(".rutaOrden").val();
	   var tecnico = $(".tecnico").val();
	   var asesor = $(".asesor").val();
	   var cliente = $(".cliente").val();
	   var status = $(".status").val();
	   var descripcionOrden = $(".descripcionOrden").val();
	   var partida1 = $(".partida1").val(); 
	   var precio1 = $(".precio1").val();
	   var partida2 = $(".partida2").val(); 
	   var precio2 = $(".precio2").val();	   
	   var partida3 = $(".partida3").val();
	   var precio3 = $(".precio3").val(); 
	   var partida4 = $(".partida4").val(); 
	   var precio4 = $(".precio4").val(); 
	   var partida5 = $(".partida5").val(); 
	   var precio5 = $(".precio5").val(); 
	   var partida6 = $(".partida6").val(); 
	   var precio6 = $(".precio6").val(); 
	   var partida7 = $(".partida7").val(); 
	   var precio7 = $(".precio7").val(); 
	   var partida8 = $(".partida8").val(); 
	   var precio8 = $(".precio8").val(); 
	   var partida9 = $(".partida9").val(); 
	   var precio9 = $(".precio9").val(); 
	   var partida10 = $(".partida10").val(); 
	   var precio10 = $(".precio10").val(); 
	   var totalOrden = $(".totalOrden").val();

	   var marcaDelEquipo = $(".marcaDelEquipo").val();

	   var modeloDelEquipo = $(".modeloDelEquipo").val();

	   var numeroDeSerieDelEquipo = $(".numeroDeSerieDelEquipo").val();
	   
	 	var datosOrden = new FormData();
		datosOrden.append("tituloOrden", tituloOrden);
		datosOrden.append("empresa", empresa);
		datosOrden.append("rutaOrden", rutaOrden);
		datosOrden.append("tecnico", tecnico);
		datosOrden.append("asesor", asesor);
		datosOrden.append("cliente", cliente);
		datosOrden.append("status", status);
		datosOrden.append("descripcionOrden", descripcionOrden);
		datosOrden.append("partida1", partida1);
		datosOrden.append("precio1", precio1);
		datosOrden.append("partida2", partida2);
		datosOrden.append("precio2", precio2);				
		datosOrden.append("partida3", partida3);
		datosOrden.append("precio3", precio3);		
		datosOrden.append("partida4", partida4);
		datosOrden.append("precio4", precio4);		
		datosOrden.append("partida5", partida5);
		datosOrden.append("precio5", precio5);		
		datosOrden.append("partida6", partida6);
		datosOrden.append("precio6", precio6);
		datosOrden.append("partida7", partida7);
		datosOrden.append("precio7", precio7);
		datosOrden.append("partida8", partida8);
		datosOrden.append("precio8", precio8);
		datosOrden.append("partida9", partida9);
		datosOrden.append("precio9", precio9);
		datosOrden.append("partida10", partida10);
		datosOrden.append("precio10", precio10);
		datosOrden.append("totalOrden", totalOrden);
		datosOrden.append("multimedia", imagen);
		datosOrden.append("fotoPortada", imagenPortada);
		datosOrden.append("fotoPrincipal", imagenFotoPrincipalOrden);

		datosOrden.append("marcaDelEquipo", marcaDelEquipo);

		datosOrden.append("modeloDelEquipo", modeloDelEquipo);

		datosOrden.append("numeroDeSerieDelEquipo", numeroDeSerieDelEquipo);



		$.ajax({
				url:"ajax/ordenes.ajax.php",
				method: "POST",
				data: datosOrden,
				cache: false,
				contentType: false,
				processData: false,
				success: function(respuesta){
					
					 //console.log("respuesta", respuesta);

					if(respuesta == "ok"){

						swal({
						  type: "success",
						  title: "La orden ha sido guardada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "index.php?ruta=ordenes";

							}
						})
					}

				}

		})

}
/*=============================================
AGREGAR CAMPOS DINAMICAMENTE
=============================================*/
var nextinput = 0;
var nextinputPrecio = 0;
function AgregarCampos(){
nextinput++;
nextinputPrecio++;
campo = '<div class="form-group row" style="margin-bottom:10px;padding:12px;background:#fafbfc;border-radius:8px;border:1px solid #f1f5f9">' +
  '<div class="col-xs-7 col-md-8">' +
    '<label style="font-size:10px;font-weight:600;color:#64748b;margin-bottom:4px;display:block"><i class="fa-solid fa-file-lines" style="margin-right:4px"></i>Partida ' + nextinput + ' <span style="color:#94a3b8;font-weight:400">(visible en ticket del cliente)</span></label>' +
    '<textarea type="text" maxlength="320" rows="2" class="form-control partida partida' + nextinput + '" placeholder="Descripción del servicio o refacción para el cliente" style="text-transform:uppercase;font-size:13px"></textarea>' +
  '</div>' +
  '<div class="col-xs-5 col-md-4">' +
    '<label style="font-size:10px;font-weight:600;color:#64748b;margin-bottom:4px;display:block"><i class="fa-solid fa-dollar-sign" style="margin-right:4px"></i>Precio</label>' +
    '<div class="input-group">' +
      '<span class="input-group-addon" style="background:#6366f1;color:#fff;border-color:#6366f1;font-weight:700">$</span>' +
      '<input class="form-control precio' + nextinputPrecio + ' preciodeOrdenUno" type="number" value="0" min="0" step="any" placeholder="0.00" style="font-weight:700">' +
    '</div>' +
  '</div>' +
'</div>';
$("#campos").append(campo);
}

/*=============================================
EDITAR ORDEN
=============================================*/
$('.tablaOrdenes tbody').on("click", ".btnEditarOrden", function(){

	$(".previsualizarImgFisico").html("");

	var idOrden = $(this).attr("idOrden");

	var datos = new FormData();
	datos.append("idOrden", idOrden);

	$.ajax({
	    url:"ajax/ordenes.ajax.php",
	    method:"POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
	    success:function(respuesta){

    		//console.log("datos de Ordenes:", respuesta);
    		$("#modalEditarOrden .idOrden").val(respuesta[0]["id"]);
			//$("#EditarTecnico").html(respuesta[0]["id_tecnico"]);
			$("#modalEditarOrden .tituloOrden").val(respuesta[0]["titulo"]);
			$("#modalEditarOrden .rutaOrden").val(respuesta[0]["ruta"]);
			$("#modalEditarOrden .descripcionOrden").val(respuesta[0]["descripcion"]);
			$("#modalEditarOrden .totalOrdenEditar").val(respuesta[0]["total"]);
			
			$("#modalEditarOrden .NumeroDeOrden").html(respuesta[0]["id"]);

			/*=============================================
			TRAEMOS LOS TECNICOS
			=============================================*/

			$("#modalEditarOrden .optionEditarEstatus").html(respuesta[0]["estado"]);

			if (respuesta[0]["estado"] == "Pendiente de revisión (pen)"){

				$(".pen").show();
				$(".sup").show();
				//$(".aut").show();
				//$(".ok").show();
				//$(".ent").show();
			}

			if (respuesta[0]["estado"] == "Supervisión (SUP)"){

				//$(".pen").hide();
				//$(".sup").show();
				$(".aut").show();
			}


			if (respuesta[0]["estado"] == "Pendiente de autorización (AUT)"){

				$(".pen").hide();
				//$(".sup").hide();
				//$(".aut").show();
				$(".ok").show();
			}


			if (respuesta[0]["estado"] == "Aceptado (ok)"){

				$(".pen").hide();
				$(".sup").hide();
				$(".aut").hide();
				$(".ok").show();
				$(".ter").show();
				$(".can").show();

			}


			if (respuesta[0]["estado"] == "Terminada (ter)"){

				$(".pen").hide();
				$(".sup").hide();
				$(".aut").hide();
				$(".ok").hide();
				//$(".ter").show();
				//$(".can").show();
				$(".ent").show();
			}


			if (respuesta[0]["estado"] == "Cancelada (can)"){

				
				$(".pen").hide();
				$(".sup").hide();
				$(".aut").hide();
				$(".ok").hide();
				$(".can").show();
			}
			if (respuesta[0]["estado"] == "Entregado (Ent)"){
				
				$(".pen").hide();
				$(".sup").hide();
				$(".aut").hide();
				$(".ok").hide();
				$(".ent").hide();
			}
			/*=============================================
			TRAEMOS LOS TECNICOS
			=============================================*/
			if (respuesta[0]["id_tecnico"] != 0){

				var datosTecnico = new FormData();
				datosTecnico.append("idTecnico", respuesta[0]["id_tecnico"]);


				$.ajax({

					url:"ajax/Tecnicos.ajax.php",
					method: "POST",
					data: datosTecnico,
					cache: false,
					contentType: false,
					processData: false,
					dataType: "json",
					success: function(respuesta){

						$("#modalEditarOrden .seleccionarTecnico").val(respuesta["id"]);
						$("#modalEditarOrden .optionEditarTecnico").html(respuesta["nombre"]);
		
					}
				})

			}

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

						$("#modalEditarOrden .seleccionarAsesor").val(respuesta["id"]);
						$("#modalEditarOrden .optionEditarAsesor").html(respuesta["nombre"]);
		
					}
				})

			}
						/*=============================================
			TRAEMOS LOS ASESORES
			=============================================*/
			if (respuesta[0]["id_pedido"] != 0){

				//$("#modalEditarOrden .selActivarPedido").hide();
			    $("#modalEditarOrden  .datosPedido").show();
			    $("#modalEditarOrden  .VerEstadoDelPedido").show();

				var datosPedido = new FormData();
				datosPedido.append("idPedido", respuesta[0]["id_pedido"]);


				$.ajax({

					url:"ajax/pedidos.ajax.php",
					method: "POST",
					data: datosPedido,
					cache: false,
					contentType: false,
					processData: false,
					dataType: "json",
					success: function(respuesta){

						//console.log("respuesta de pedido a orden: ", respuesta[0]);
						$("#modalEditarOrden .seleccionarPedido").val(respuesta[0]["id"]);
						$("#modalEditarOrden .optionEditarPedidos").html(respuesta[0]["id"]);

						//$("#modalEditarOrden .EstadoDelPedido").val(respuesta[0]["estado"]);
						$("#modalEditarOrden .verEstadoPedido").html(respuesta[0]["estado"]);
						$("#modalEditarOrden .idPedido").val(respuesta[0]["id"]);
			
					}
				})

			}else{

				//$("#modalEditarOrden .selActivarPedido").show();
			    $("#modalEditarOrden .datosPedido").hide();
			    $("#modalEditarOrden  .VerEstadoDelPedido").hide();


			}
			/*=============================================
			TRAEMOS LOS ASESORES
			=============================================*/
			if (respuesta[0]["id_usuario"] != 0){

				var datosUsuario = new FormData();
				datosUsuario.append("idCliente", respuesta[0]["id_usuario"]);


				$.ajax({

					url:"ajax/clientes.ajax.php",
					method: "POST",
					data: datosUsuario,
					cache: false,
					contentType: false,
					processData: false,
					dataType: "json",
					success: function(respuesta){

						$("#modalEditarOrden .seleccionarCliente").val(respuesta["id"]);
						$("#modalEditarOrden .optionEditarCliente").html(respuesta["nombre"]);
						$("#modalEditarOrden .correoCliente").val(respuesta["correo"]);
						$("#modalEditarOrden .numeroCliente").val(respuesta["telefono"]);
						$("#modalEditarOrden .numeroClienteDos").val(respuesta["telefonoDos"]);

						//$("#modalEditarOrden .telefonoCliente").html(respuesta["nombre"]);
					}
				})

			}

		
				$("#modalEditarOrden .partida1").val(respuesta[0]["partidaUno"]);
				$("#modalEditarOrden .precio1").val(respuesta[0]["precioUno"]);



				$("#modalEditarOrden .partida2").val(respuesta[0]["partidaDos"]);
				$("#modalEditarOrden .precio2").val(respuesta[0]["precioDos"]);

		

				$("#modalEditarOrden .partida3").val(respuesta[0]["partidaTres"]);
				$("#modalEditarOrden .precio3").val(respuesta[0]["precioTres"]);

		
			
				$("#modalEditarOrden .partida4").val(respuesta[0]["partidaCuatro"]);
				$("#modalEditarOrden .precio4").val(respuesta[0]["precioCuatro"]);

		
				$("#modalEditarOrden .partida5").val(respuesta[0]["partidaCinco"]);
				$("#modalEditarOrden .precio5").val(respuesta[0]["precioCinco"]);

		
				$("#modalEditarOrden .partida6").val(respuesta[0]["partidaSeis"]);
				$("#modalEditarOrden .precio6").val(respuesta[0]["precioSeis"]);

		
				$("#modalEditarOrden .partida7").val(respuesta[0]["partidaSiete"]);
				$("#modalEditarOrden .precio7").val(respuesta[0]["precioSiete"]);

		
				$("#modalEditarOrden .partida8").val(respuesta[0]["partidaOcho"]);
				$("#modalEditarOrden .precio8").val(respuesta[0]["precioOcho"]);

				$("#modalEditarOrden .partida9").val(respuesta[0]["partidaNueve"]);
				$("#modalEditarOrden .precio9").val(respuesta[0]["precioNueve"]);

		

				$("#modalEditarOrden .partida10").val(respuesta[0]["partidaDiez"]);
				$("#modalEditarOrden .precio10").val(respuesta[0]["precioDiez"]);

		
				/*=============================================
				CARGAMOS LA IMAGEN DE PORTADA
				=============================================*/

				$("#modalEditarOrden .previsualizarPortada").attr("src", respuesta[0]["portada"]);
				$("#modalEditarOrden .antiguaFotoPortada").val(respuesta[0]["portada"]);
	
			/*=============================================
			CARGAMOS LA IMAGEN PRINCIPAL
			=============================================*/

			$("#modalEditarOrden .previsualizarPrincipal").attr("src", respuesta[0]["portada"]);
			$("#modalEditarOrden .antiguaFotoPrincipal").val(respuesta[0]["portada"]);
			

			/*=============================================
			MULTIMEDIA DROPZONE
			=============================================*/

			if(respuesta[0]["multimedia"] != ""){

					var imagenesMultimedia = JSON.parse(respuesta[0]["multimedia"]);
					
					for(var i = 0; i < imagenesMultimedia.length; i++){

						$(".previsualizarImgFisico").append(

							  '<div class="col-md-3">'+
							    '<div class="thumbnail text-center">'+
							      '<img class="imagenesRestantes" src="'+imagenesMultimedia[i].foto+'" style="width:100%">'+
							      '<div class="removerImagen" style="cursor:pointer">Remove file</div>'+
							    '</div>'+

							  '</div>'

		                );

		                localStorage.setItem("multimediaFisica", JSON.stringify(imagenesMultimedia));

					}		

					/*=============================================
					CUANDO ELIMINAMOS UNA IMAGEN DE LA LISTA
					=============================================*/

				 	$(".removerImagen").click(function(){

						$(this).parent().parent().remove();

						var imagenesRestantes = $(".imagenesRestantes");
						var arrayImgRestantes = [];

						for(var i = 0; i < imagenesRestantes.length; i++){

							arrayImgRestantes.push({"foto":$(imagenesRestantes[i]).attr("src")})
							
						}

						localStorage.setItem("multimediaFisica", JSON.stringify(arrayImgRestantes));
						
					})

				}



	    }

   	})
})

/*=============================================
AGREGAR ID DE ORDEN OBSERVACION
=============================================*/
$('.tablaOrdenes tbody').on("click", ".btnAgregarDetalleInterno", function(){


	var idOrden = $(this).attr("idOrden");

	var datos = new FormData();
	datos.append("idOrden", idOrden);


	$.ajax({
	    url:"ajax/ordenes.ajax.php",
	    method:"POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
	    success:function(respuesta){

    		$(".idOrdenObservacion").val(respuesta[0]["id"]);
    
		   	var datosObservaciones = new FormData();
			datosObservaciones.append("idOrdenOv", respuesta[0]["id"]);



			$.ajax({

				url:"ajax/observaciones.ajax.php",
				method: "POST",
				data: datosObservaciones,
				cache: false,
				contentType: false,
				processData: false,
				dataType: "json",
				success: function(respuesta){

					//console.log("OBSERVACIONES:", respuesta);
					//$("#modalAgregarDetalle .observacionUno").val(respuesta["id"]);
					$("#modalAgregarDetalle .observacionUno").html(respuesta["observacionUno"]);
					$("#modalAgregarDetalle .fecha").val(respuesta["fecha"]);

					//$("#modalEditarOrden .telefonoCliente").html(respuesta["nombre"]);



				}
			})
	  
    	  }

   	})

})
/*=============================================
GUARDAR CAMBIOS DEL PRODUCTO
=============================================*/	
var multimediaFisica = null;
$(".guardarCambiosOrden").click(function(){

	/*=============================================
	PREGUNTAMOS SI LOS CAMPOS OBLIGATORIOS ESTÁN LLENOS
	=============================================*/
	if ($("#modalEditarOrden .tituloOrden").val() != "" && 
	    $("#modalEditarOrden .descripcionOrden").val() != ""){

	/*=============================================
	PREGUNTAMOS SI VIENEN IMÁGENES PARA MULTIMEDIA 
	=============================================*/
	if (arrayFiles.length > 0 && $("#modalEditarOrden .rutaOrden").val() != ""){
		
		var listaMultimedia = [];
		var finalFor = 0;

		for(var i = 0; i < arrayFiles.length; i++){
			var datosMultimedia = new FormData();
			datosMultimedia.append("file", arrayFiles[i]);
			datosMultimedia.append("ruta", $("#modalEditarOrden .rutaOrden").val());

			$.ajax({
				url:"ajax/productos.ajax.php",
				method: "POST",
				data: datosMultimedia,
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function(){
					$(".modal-footer .preload").html(`


						<center>

							<img src="vistas/img/plantilla/status.gif" id="status" />
							<br>

						</center>

					`);
			},

			success: function(respuesta){

				$("#status").remove();

				listaMultimedia.push({"foto" : respuesta.substr(3)});
				multimediaFisica = JSON.stringify(listaMultimedia);
				if(localStorage.getItem("multimediaFisica") != null){
					var jsonLocalStorage = JSON.parse(localStorage.getItem("multimediaFisica"));
					var jsonMultimediaFisica = listaMultimedia.concat(jsonLocalStorage);
					multimediaFisica = JSON.stringify(jsonMultimediaFisica);												
				}
				if(multimediaFisica == null){

					swal({
						title: "El campo de multimedia no debe estar vacío",
						type: "error",
						confirmButtonText: "¡Cerrar!"
					});
						return;
				}
				if((finalFor + 1) == arrayFiles.length){

					editarMiOrden(multimediaFisica);
					finalFor = 0;

				}

				finalFor++;		
			}

			})
		}

	}else{


		var jsonLocalStorage = JSON.parse(localStorage.getItem("multimediaFisica"));

		multimediaFisica = JSON.stringify(jsonLocalStorage);

		editarMiOrden(multimediaFisica);	

	}

	editarMiOrden(multimediaVirtual);	

	}else{


	 swal({
		title: "Llenar todos los campos obligatorios",
	 	type: "error",
	 	confirmButtonText: "¡Cerrar!"
	});

		return;
	}

})

function editarMiOrden(imagen){

	var idOrden = $("#modalEditarOrden .idOrden").val();
	var tituloOrden = $("#modalEditarOrden .tituloOrden").val();
	var rutaOrden = $("#modalEditarOrden .rutaOrden").val();
	var seleccionarTecnico = $("#modalEditarOrden .seleccionarTecnico").val();
	var seleccionarAsesor = $("#modalEditarOrden .seleccionarAsesor").val();
	var seleccionarCliente = $("#modalEditarOrden .seleccionarCliente").val();
	var seleccionarEstatus = $("#modalEditarOrden .seleccionarEstatus").val();
	var descripcionOrden = $("#modalEditarOrden .descripcionOrden").val();
	var partida1 = $("#modalEditarOrden .partida1").val();
	var precio1 = $("#modalEditarOrden .precio1").val();
	var partida2 = $("#modalEditarOrden .partida2").val();
	var precio2 = $("#modalEditarOrden .precio2").val();
	var partida3 = $("#modalEditarOrden .partida3").val();
	var precio3 = $("#modalEditarOrden .precio3").val();
	var partida4 = $("#modalEditarOrden .partida4").val();
	var precio4 = $("#modalEditarOrden .precio4").val();
	var partida5 = $("#modalEditarOrden .partida5").val();
	var precio5 = $("#modalEditarOrden .precio5").val();
	var partida6 = $("#modalEditarOrden .partida6").val();
	var precio6 = $("#modalEditarOrden .precio6").val();
	var partida7 = $("#modalEditarOrden .partida7").val();
	var precio7 = $("#modalEditarOrden .precio7").val();
	var partida8 = $("#modalEditarOrden .partida8").val();
	var precio8 = $("#modalEditarOrden .precio8").val();
	var partida9 = $("#modalEditarOrden .partida9").val();
	var precio9 = $("#modalEditarOrden .precio9").val();
	var partida10 = $("#modalEditarOrden .partida10").val();
	var precio10 = $("#modalEditarOrden .precio10").val();
	var totalOrdenEditar = $("#modalEditarOrden .totalOrdenEditar").val();
	var EstadoDelPedido = $("#modalEditarOrden .EstadoDelPedido").val();

	var antiguaFotoPortada = $("#modalEditarOrden .antiguaFotoPortada").val();
	var antiguaFotoPrincipal = $("#modalEditarOrden .antiguaFotoPrincipal").val();

	var seleccionarPedido = $("#modalEditarOrden .seleccionarPedido").val();
	var idPedido = $("#modalEditarOrden .idPedido").val();

	var datosOrden = new FormData();
	datosOrden.append("id", idOrden);
	datosOrden.append("editarOrden", tituloOrden);
	datosOrden.append("rutaOrden", rutaOrden);
	datosOrden.append("seleccionarTecnico", seleccionarTecnico);
	datosOrden.append("seleccionarAsesor", seleccionarAsesor);
	datosOrden.append("seleccionarCliente", seleccionarCliente);
	datosOrden.append("seleccionarEstatus", seleccionarEstatus);
	datosOrden.append("descripcionOrden", descripcionOrden);
	datosOrden.append("partida1", partida1);
	datosOrden.append("precio1", precio1);
	datosOrden.append("partida2", partida2);
	datosOrden.append("precio2", precio2);				
	datosOrden.append("partida3", partida3);
	datosOrden.append("precio3", precio3);		
	datosOrden.append("partida4", partida4);
	datosOrden.append("precio4", precio4);		
	datosOrden.append("partida5", partida5);
	datosOrden.append("precio5", precio5);		
	datosOrden.append("partida6", partida6);
	datosOrden.append("precio6", precio6);
	datosOrden.append("partida7", partida7);
	datosOrden.append("precio7", precio7);
	datosOrden.append("partida8", partida8);
	datosOrden.append("precio8", precio8);
	datosOrden.append("partida9", partida9);
	datosOrden.append("precio9", precio9);
	datosOrden.append("partida10", partida10);
	datosOrden.append("precio10", precio10);
	datosOrden.append("totalOrdenEditar", totalOrdenEditar);
	datosOrden.append("idPedido", idPedido);
	datosOrden.append("EstadoDelPedido", EstadoDelPedido);

	datosOrden.append("seleccionarPedido", seleccionarPedido);

	if(imagen == null){

		multimediaFisica = localStorage.getItem("multimediaFisica");
		datosOrden.append("multimedia", multimediaFisica);

	}else{

		datosOrden.append("multimedia", imagen);
	}	

	datosOrden.append("fotoPortada", imagenPortada);
	datosOrden.append("fotoPrincipal", imagenFotoPrincipal);
	datosOrden.append("antiguaFotoPortada", antiguaFotoPortada);
	datosOrden.append("antiguaFotoPrincipal", antiguaFotoPrincipal);

	$.ajax({
			url:"ajax/ordenes.ajax.php",
			method: "POST",
			data: datosOrden,
			cache: false,
			contentType: false,
			processData: false,
			success: function(respuesta){
									
				
				if(respuesta == "ok"){

					swal({
					  type: "success",
					  title: "La orden ha sido cambiada correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar"
					  }).then(function(result){
						if (result.value) {

						localStorage.removeItem("multimediaFisica");
						localStorage.clear();
						window.location = "index.php?ruta=ordenes";

						}
					})
				}

			}

	})
	
}

/*=============================================
VER INFORMACION DE LA ORDEN
=============================================
$(".tablaOrdenes").on("click", ".btnVerInfoOrden", function(){
		//console.log("Editar");
	var idOrden = $(this).attr("idOrden");
	console.log(idOrden);
	var empresa = $(this).attr("empresa");
	var asesor = $(this).attr("asesor");
	var cliente = $(this).attr("cliente");
	var tecnico = $(this).attr("tecnico");
	var pedido = $(this).attr("pedido");
	var datos = new FormData();
	datos.append("idOrden", idOrden);
	datos.append("empresa", empresa);
	datos.append("asesor", asesor);
	datos.append("cliente", cliente);
	datos.append("tecnico", tecnico);
	datos.append("pedido", pedido);
	
	$.ajax({


		url:"ajax/ordenes.ajax.php",
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
      	 $("#idOrden").val(respuesta["id"]);
      	 $("#empresa").val(respuesta["id_empresa"]);
      	 $("#asesor").val(respuesta["id_Asesor"]);
      	 $("#cliente").val(respuesta["id_usuario"]);
      	 $("#tecnico").val(respuesta["id_tecnico"]);
      	 $("#pedido").val(respuesta["id_pedido"]);
		
				//console.log("Datos Orden:", respuesta);

		}

	})
	window.open("index.php?ruta=infoOrden&idOrden="+idOrden+"&empresa="+empresa+"&asesor="+asesor+"&cliente="+cliente+"&tecnico="+tecnico+"&pedido="+pedido+"","_self");


})*/
/*=============================================
IMPRIMIR TICKET DE ORDEN
=============================================*/
$(document).on("click", ".btnImprimirorden", function(){
		//console.log("Editar");
	var idOrden = $(this).attr("idOrden");
	//console.log(idOrden);
	var empresa = $(this).attr("empresa");
	var asesor = $(this).attr("asesor");
	var cliente = $(this).attr("cliente");
	var tecnico = $(this).attr("tecnico");
	var datos = new FormData();
	datos.append("idOrden", idOrden);
	datos.append("empresa", empresa);
	datos.append("asesor", asesor);
	datos.append("cliente", cliente);
	datos.append("tecnico", tecnico);
	
	$.ajax({


		url:"ajax/ordenes.ajax.php",
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
      	 $("#idOrden").val(respuesta["id"]);
      	 $("#empresa").val(respuesta["id_empresa"]);
      	 $("#asesor").val(respuesta["id_Asesor"]);
      	 $("#cliente").val(respuesta["id_usuario"]);
      	 $("#tecnico").val(respuesta["id_tecnico"]);
		
				//console.log("Datos usuario:", respuesta);

		}

	})
	window.open("https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/ticketOrden.php/?idOrden="+idOrden+"&empresa="+empresa+"&asesor="+asesor+"&cliente="+cliente+"&tecnico="+tecnico+"", "_blank");


})

/*=============================================
ELIMINAR ORDEN
=============================================*/

$('.ordenes').on("click", ".btnEliminarorden", function(){


  var idOrden = $(this).attr("idOrden");
  var imgPortada = $(this).attr("imgPortada");
  var imgPrincipal = $(this).attr("imgPrincipal");

  swal({
    title: '¿Está seguro de borrar la orden?',
    text: "¡Si no lo está puede cancelar la accíón!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Si, borrar orden!'
  }).then(function(result){

    if(result.value){

      window.location = "index.php?ruta=ordenes&idOrden="+idOrden+"&imgPortada="+imgPortada+"&imgPrincipal="+imgPrincipal;

    }

  })

})
/*=============================================
AGREGAR CAMPOS DINAMICAMENTE OBSERVACION
=============================================*/
var nextinput = 0;
var nextinputPrecio = 0;
function AgregarObservacion(){
nextinput++;
nextinputPrecio++;
campo = '<div class="form-group row"><div class="input-group"> <span class="input-group-addon"><i class="fa fa-edit"></i></span><textarea type="text" maxlength="320" rows="3" class="form-control input-lg observacion' + nextinput + '"  placeholder="Ingresar observación"></textarea> </div> </div><div></div>';
$("#camposObservacion").append(campo);
}

$(".agregarObservacion").click(function(){

	 agregarMiObservacion();
})

function agregarMiObservacion(){

  	   var creador = $(".creador").val();
  	   var idOrdenObservacion = $(".idOrdenObservacion").val();
	   var observacion1 = $(".observacion1").val();
	   var observacion2 = $(".observacion2").val(); 
	   var observacion3 = $(".observacion3").val();	   
	   var observacion4 = $(".observacion4").val();
	   var observacion5 = $(".observacion5").val(); 
	   var observacion6 = $(".observacion6").val(); 
	   var observacion7 = $(".observacion7").val(); 
	   var observacion8 = $(".observacion8").val(); 
	   var observacion9 = $(".observacion9").val(); 
	   var observacion10 = $(".observacion10").val(); 

	   
	 	var datosOrden = new FormData();
	 	datosOrden.append("creador", creador);
	 	datosOrden.append("idOrdenObservacion", idOrdenObservacion);
		datosOrden.append("observacion1", observacion1);
		datosOrden.append("observacion2", observacion2);
		datosOrden.append("observacion3", observacion3);				
		datosOrden.append("observacion4", observacion4);
		datosOrden.append("observacion5", observacion5);		
		datosOrden.append("observacion6", observacion6);
		datosOrden.append("observacion7", observacion7);
		datosOrden.append("observacion8", observacion8);		
		datosOrden.append("observacion9", observacion9);
		datosOrden.append("observacion10", observacion10);

		$.ajax({
				url:"ajax/ordenes.ajax.php",
				method: "POST",
				data: datosOrden,
				cache: false,
				contentType: false,
				processData: false,
				success: function(respuesta){
					
					 //console.log("respuesta", respuesta);

					if(respuesta == "ok"){

						swal({
						  type: "success",
						  title: "La observacion guardado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "index.php?ruta=ordenes";

							}
						})
					}

				}

		})
}

// Si pulsamos tecla en un Input
 $("input").keydown(function (e){
   // Capturamos qué telca ha sido
   var keyCode= e.which;
   // Si la tecla es el Intro/Enter
   if (keyCode == 13){
     // Evitamos que se ejecute eventos
     event.preventDefault();
     // Devolvemos falso
     return false;
   }
 });
 // Si pulsamos tecla en un textarea
 $("textarea").keydown(function (e){
   // Capturamos qué telca ha sido
   var keyCode= e.which;
   // Si la tecla es el Intro/Enter
   if (keyCode == 13){
     // Evitamos que se ejecute eventos
     event.preventDefault();
     // Devolvemos falso
     return false;
   }
});

/*=============================================
VARIABLE LOCAL STORAGE
=============================================*/
if($("#daterange-btnOrdenes").length){

	if(localStorage.getItem("btnOrdenes") != null){
		$("#daterange-btnOrdenes span").html(localStorage.getItem("btnOrdenes"));
	}else{
		$("#daterange-btnOrdenes span").text("Mes actual");
	}

	var presetRanges = {
		mes_actual: {
			label: "Mes actual",
			start: moment().startOf('month'),
			end: moment().endOf('month')
		},
		mes_anterior: {
			label: "Mes anterior",
			start: moment().subtract(1, 'month').startOf('month'),
			end: moment().subtract(1, 'month').endOf('month')
		},
		tres_meses: {
			label: "Ultimos 3 meses",
			start: moment().subtract(2, 'months').startOf('month'),
			end: moment().endOf('month')
		},
		seis_meses: {
			label: "Ultimos 6 meses",
			start: moment().subtract(5, 'months').startOf('month'),
			end: moment().endOf('month')
		},
		doce_meses: {
			label: "Ultimos 12 meses",
			start: moment().subtract(11, 'months').startOf('month'),
			end: moment().endOf('month')
		}
	};

	function irAReporteConRango(startMoment, endMoment, label){
		localStorage.setItem("btnOrdenes", label);
		var fechaInicial = startMoment.format('YYYY-MM-DD');
		var fechaFinal = endMoment.format('YYYY-MM-DD');
		window.location = "index.php?ruta=reportePorFecheOrdenes&fechaInicial=" + fechaInicial + "&fechaFinal=" + fechaFinal;
	}

	var $hiddenRangeInput = $('<input type="text" id="flatpickrRangoOrdenes" style="position:fixed;left:-9999px;top:-9999px;opacity:0;" />');
	$('body').append($hiddenRangeInput);

	var flatpickrRangoOrdenes = flatpickr("#flatpickrRangoOrdenes", {
		mode: "range",
		dateFormat: "Y-m-d",
		allowInput: false,
		onClose: function(selectedDates){
			if(selectedDates.length === 2){
				var start = moment(selectedDates[0]);
				var end = moment(selectedDates[1]);
				irAReporteConRango(start, end, "Rango personalizado");
			}
		}
	});

	$("#daterange-btnOrdenes").on("click", function(){
		Swal.fire({
			title: "Selecciona un rango",
			input: "select",
			inputOptions: {
				mes_actual: "Mes actual",
				mes_anterior: "Mes anterior",
				tres_meses: "Ultimos 3 meses",
				seis_meses: "Ultimos 6 meses",
				doce_meses: "Ultimos 12 meses",
				personalizado: "Rango personalizado"
			},
			inputPlaceholder: "Selecciona una opcion",
			showCancelButton: true,
			confirmButtonText: "Aplicar",
			cancelButtonText: "Cancelar"
		}).then(function(result){
			if(!result.isConfirmed || !result.value){
				return;
			}

			if(result.value === "personalizado"){
				flatpickrRangoOrdenes.clear();
				flatpickrRangoOrdenes.open();
				return;
			}

			if(presetRanges[result.value]){
				var rango = presetRanges[result.value];
				irAReporteConRango(rango.start, rango.end, rango.label);
			}
		});
	});
}

/*=============================================
TRAEMOS LOS ASESORES
=============================================
var value=$.trim($(".partidaUno").val());

if ($('.partidaUno').val().length !== " ") {

    $(".partidaUno").prop("readonly",true);
    
}else{
	
}*/

/*=============================================
ACTIVAR PEDIDO
=============================================*/
function activarPedido(event){
	
	if (event=="pedido"){

		$(".datosPedido").show();
		$(".EstadoDelPedido").show();
	
	}else{

		$(".datosPedido").hide();
		$(".EstadoDelPedido").hide();
		
	}
}

$(".selActivarPedido").change(function(){

	activarPedido($(this).val())

})


/*=============================================
VER INFORMACION DE LA ORDEN
=============================================*/
$(document).on("click", ".btnVerInfoOrden", function(){
		//console.log("Editar");
	var idOrden = $(this).attr("idOrden");
	//console.log(idOrden);
	var empresa = $(this).attr("empresa");
	var asesor = $(this).attr("asesor");
	var cliente = $(this).attr("cliente");
	var tecnico = $(this).attr("tecnico");
	var pedido = $(this).attr("pedido");
	//console.log("peido",pedido);
	var datos = new FormData();
	datos.append("idOrden", idOrden);
	datos.append("empresa", empresa);
	datos.append("asesor", asesor);
	datos.append("cliente", cliente);
	datos.append("tecnico", tecnico);
	datos.append("pedido", pedido);
	
	$.ajax({


		url:"ajax/ordenes.ajax.php",
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
      	 $("#idOrden").val(respuesta["id"]);
      	 $("#empresa").val(respuesta["id_empresa"]);
      	 $("#asesor").val(respuesta["id_Asesor"]);
      	 $("#cliente").val(respuesta["id_usuario"]);
      	 $("#tecnico").val(respuesta["id_tecnico"]);
		 $("#pedido").val(respuesta["id_pedido"]);
			
			//console.log("Datos usuario:", respuesta);
				

		}

	})
	window.open("index.php?ruta=infoOrden&idOrden="+idOrden+"&empresa="+empresa+"&asesor="+asesor+"&cliente="+cliente+"&tecnico="+tecnico+"&pedido="+pedido+"","_self");


})

//BUSCADOR CON AJAX
//$(document).ready(function(){
//	$(document).on('keyup','.caja_Busqueda',function(){
//
//		var consulta = $(".caja_Busqueda").val();
//
//		var datos = new FormData();
//		datos.append("consulta", consulta);
//
//		console.log(consulta);
//
//		$.ajax({
//
//
//			url:"ajax/ordenes.ajax.php",
//			method: "POST",
//			data: datos,
//			cache: false,
//			contentType: false,
//			processData: false,
//			dataType: "json",
//			success: function(respuesta){ 
//				
//				console.log("DATOS BUSQUEDA:", respuesta[0]);
//
//			}

//		})
		
//	})

//});
/*=============================================
SELECTOR DE CLIENTE
=============================================*/
document.addEventListener('DOMContentLoaded', function() {
  // Inicializar Choices para todos los select con la clase 'cliente'
  document.querySelectorAll('.cliente').forEach(function(element) {
    new Choices(element, {
      removeItemButton: false,  // Puedes habilitar si necesitas quitar elementos seleccionados
      searchEnabled: true,      // Habilita la búsqueda
      shouldSort: false         // Deshabilita la ordenación automática si lo requieres
      // Agrega aquí otras opciones de configuración según tus necesidades
    });
  });

  // Inicializar Choices para los select de búsqueda
  document.querySelectorAll('.caja_Busqueda').forEach(function(element) {
    new Choices(element, {
      removeItemButton: false,
      searchEnabled: true,
      shouldSort: false
    });
  });
});




/*=============================================
SUMAR PRECIOS PRODUCTOS DE PEDIDO
=============================================*/
$(document).on("change", ".input", function() {

	//multiplica()
	//validarselect()
});

/*=============================================
AGREGAR CAMPOS PARA PEDIDO
=============================================*/
$('.AgregarProductos').click(function() {

	$(".NuevoProductoPedido").append(
	
		'<div class="row" style="padding:5px 15px">'+

 				   '<!-- Descripción del producto-->'+
                  
                  '<div class="col-xs-6" style="padding-right:0px">'+
                  
                    '<div class="input-group">'+
                      
                      '<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs quitarProducto"><i class="fa fa-times"></i></button></span>'+

                      '<input type="text" class="form-control descripcioProductoPedido"  required>'+

                    '</div>'+

                  '</div> '+

                  '<!-- Cantidad del producto -->'+

                  '<div class="col-xs-3">'+
                    
                     '<input type="number" class="form-control nuevaCantidadProductoPedido"  min="1" value="1"  required>'+

                  '</div> '+

                  '<!-- Precio del producto -->'+

                  '<div class="col-xs-3 ingresoPrecio" style="padding-left:0px">'+

                    '<div class="input-group">'+

                      '<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>'+
                         
                      '<input type="text" class="form-control nuevoPrecioProductoPedido" required>'+
         
                    '</div>'+
                     
                  '</div>'+
                  '</div>'	
	)

	//validarselect()

});

$(document).ready(function(){
			
	//multiplica()
	//validarselect()
	
});

/*=============================================
VALIDAR SELECTS DE FORMULARIO 
=============================================*/

/*=============================================
MODIFICAR LA CANTIDAD
=============================================*/
$(document).on("change", "input.nuevaCantidadProductoPedido", function(){


	var precio = $(this).parent().parent().children(".ingresoPrecio").children().children(".nuevoPrecioProductoPedido")

	
	var precioFinal = $(this).val() * precio.val();

	precio.val(precioFinal);

	//console.log(precioFinal);
	sumarTotalPreciosPedido()


})
$(document).on("change", "input.nuevoPrecioProductoPedido", function(){

	sumarTotalPreciosPedido()
	listarProductosPedidos()
})
/*=============================================
SUMAR TODOS LOS PRECIOS
=============================================*/
function sumarTotalPreciosPedido(){

	var precioItemPedido = $(".nuevoPrecioProductoPedido");

	var arraySumarPrecioPedido = [];

	for (var i = 0; i < precioItemPedido.length; i++) {
		
		arraySumarPrecioPedido.push(Number($(precioItemPedido[i]).val()));
	}

	//console.log("arraySumarPrecioPedido:", arraySumarPrecioPedido);
	function sumaArrayPreciosPedido(total, numero){

		return total + numero;

	}

	var sumaTotalPrecioPedido = arraySumarPrecioPedido.reduce(sumaArrayPreciosPedido);
	//console.log("SumaTotalDelPrecio:", sumaTotalPrecioPedido);

	//$("#nuevoTotalVenta").val(sumaTotalPrecioPedido);
	$(".TotalPedidoEnOrden").val(sumaTotalPrecioPedido);
	//$("#nuevoTotalVenta").attr("total",sumaTotalPrecioPedido);
	
	listarProductosPedidos()
}


/*=============================================
LISTAR PRODUCTOS
=============================================*/
function listarProductosPedidos(){

	var listarProductosPedido = [];

	var descripcion = $(".descripcioProductoPedido");
	var cantidad = $(".nuevaCantidadProductoPedido");
	var precio = $(".nuevoPrecioProductoPedido");

	for (var i =0; i < descripcion.length; i++) {

		listarProductosPedido.push({"Descripcion" : $(descripcion[i]).val(),
								    "cantidad" : $(cantidad[i]).val(),
									"precio" : $(precio[i]).val()})

	}

	$("#ProductosPedidoListados").val(JSON.stringify(listarProductosPedido));
}

/*=============================================

QUITAR CAMPOS PEDIDOS

=============================================*/

$(".formularioPedidosDinamicos").on("click", "button.quitarProducto", function(){



	$(this).parent().parent().parent().parent().remove();


});

/* Mensaje de inactividad removido */


/*=============================================
VER INFORMACION DE LA ORDEN
=============================================*/
$("#btnVerInfoOrdenPrueba").change(function(){
		//console.log("Editar");
	var idOrden = $(this).attr("idOrden");
	//console.log(idOrden);
	var empresa = $(this).attr("empresa");
	var asesor = $(this).attr("asesor");
	var cliente = $(this).attr("cliente");
	var tecnico = $(this).attr("tecnico");
	var pedido = $(this).attr("pedido");
	//console.log("peido",pedido);
	var datos = new FormData();
	datos.append("idOrden", idOrden);
	datos.append("empresa", empresa);
	datos.append("asesor", asesor);
	datos.append("cliente", cliente);
	datos.append("tecnico", tecnico);
	datos.append("pedido", pedido);
	
	$.ajax({


		url:"ajax/ordenes.ajax.php",
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
      	 $("#idOrden").val(respuesta["id"]);
      	 $("#empresa").val(respuesta["id_empresa"]);
      	 $("#asesor").val(respuesta["id_Asesor"]);
      	 $("#cliente").val(respuesta["id_usuario"]);
      	 $("#tecnico").val(respuesta["id_tecnico"]);
		 $("#pedido").val(respuesta["id_pedido"]);
			
			//console.log("Datos usuario:", respuesta);
				

		}

	})
	window.open("index.php?ruta=infoOrden&idOrden="+idOrden+"&empresa="+empresa+"&asesor="+asesor+"&cliente="+cliente+"&tecnico="+tecnico+"&pedido="+pedido+"","_self");


})

$(document).on("change", ".descripcionOrden", function() {
       
       verificarTA()
     
});


/*==============
VALIDAR CANTIDAD DE CARACTERES EN TEXTAREA
=================*/
function verificarTA(){

	if (document.getElementById('textareaDetallesInternos').value.length > 50){

		$(".textareaDetallesInternos").before('<div class="alert alert-warning">Los detalles internos deben ser escritos en menos de 50 palabras</div>');
	}
}


/*====================================
COLOCAR CLIENTE AL SELECCIONAR ASESOR
=====================================*/
$("select.seleccionarElAsesor").on("click", function(){


	var idAsesor = $(this).attr("idAsesor");
	
	var datos = new FormData();
	datos.append("idAsesor", idAsesor);

	$.ajax({

		url:"ajax/Asesores.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){

			console.log("el asesor", respuesta);

		}
			
	});

})


/*=============================================
AGREGAR CAMPOS PARA CARACTERISTICAS DEL EQUIPO
===============================================*/
$('#agregarCaracteristicas').click(function() {

	$(".campocaracteristicas").append(
	       
		'<div class="col-xs-4">'+
		
		'<div class="input-group">'+
			
			'<input type="text" class="form-control input-lg precio1 nombreEquipo" placeholder="Nombre del Equipo">'+
			'<span class="input-group-addon"><i class="fas fa-cogs"></i></span>'+

		'</div>'+
		'</br>'+

		'<div class="col-lg-6">'+
		
		'<div class="input-group">'+
			
			'<input type="text" class="form-control input-lg precio1 SerieEquipo" placeholder="No Serie">'+
			'<span class="input-group-addon"><i class="fas fa-cogs"></i></span>'+

		'</div>'+


	'</div>'

	)

	//validarselect()

});