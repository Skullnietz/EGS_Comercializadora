/*=============================================
SUBIR LOGOTIPO
=============================================*/

$("#subirLogo").change(function(){

	var imagenLogo = this.files[0];

	/*=============================================
  	VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
  	=============================================*/

  	if(imagenLogo["type"] != "image/jpeg" && imagenLogo["type"] != "image/png"){

  		$("#subirLogo").val("");

  		swal({
		      title: "Error al subir la imagen",
		      text: "¡La imagen debe estar en formato JPG o PNG!",
		      type: "error",
		      confirmButtonText: "¡Cerrar!"
		    });

  	/*=============================================
  	VALIDAMOS EL TAMAÑO DE LA IMAGEN
  	=============================================*/

  	}else if(imagenLogo["size"] > 2000000){

  		$("#subirLogo").val("");

  		 swal({
		      title: "Error al subir la imagen",
		      text: "¡La imagen no debe pesar más de 2MB!",
		      type: "error",
		      confirmButtonText: "¡Cerrar!"
		    });

  	/*=============================================
  	PREVISUALIZAMOS LA IMAGEN
  	=============================================*/

  	}else{

  		var datosImagen = new FileReader;
  		datosImagen.readAsDataURL(imagenLogo);

  		$(datosImagen).on("load", function(event){

  			var rutaImagen = event.target.result;

  			$(".previsualizarLogo").attr("src", rutaImagen);

  		})

  	}

  	/*=============================================
  	GUARDAR EL LOGOTIPO
  	=============================================*/

  	$("#guardarLogo").click(function(){

  		var datos = new FormData();
  		datos.append("imagenLogo", imagenLogo);

  		$.ajax({

			url:"ajax/comercio.ajax.php",
			method: "POST",
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			success: function(respuesta){

				if(respuesta == "ok"){

					swal({
				      title: "Cambios guardados",
				      text: "¡El logo a sido actualizado correctamente!",
				      type: "success",
				      confirmButtonText: "¡Cerrar!"
				    });
			
				}

				
			}

		})


  	})

})

/*=============================================
SUBIR ICONO
=============================================*/

$("#subirIcono").change(function(){

	var imagenIcono = this.files[0];
	
	/*=============================================
  	VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
  	=============================================*/

  	if(imagenIcono["type"] != "image/jpeg" && imagenIcono["type"] != "image/png"){

  		$("#subirIcono").val("");

  		 swal({
		      title: "Error al subir la imagen",
		      text: "¡La imagen debe estar en formato JPG o PNG!",
		      type: "error",
		      confirmButtonText: "¡Cerrar!"
		    });

  	/*=============================================
  	VALIDAMOS EL TAMAÑO DE LA IMAGEN
  	=============================================*/

  	}else if(imagenIcono["size"] > 2000000){

  		$("#subirIcono").val("");

  		 swal({
		      title: "Error al subir la imagen",
		      text: "¡La imagen no debe pesar más de 2MB!",
		      type: "error",
		      confirmButtonText: "¡Cerrar!"
		    });

	/*=============================================
  	PREVISUALIZAMOS LA IMAGEN
  	=============================================*/

  	}else{

  		var datosImagen = new FileReader;
  		datosImagen.readAsDataURL(imagenIcono);

  		$(datosImagen).on("load", function(event){

  			var rutaImagen = event.target.result;

  			$(".previsualizarIcono").attr("src", rutaImagen);

  		})

  	}

  	/*=============================================
  	GUARDAR EL ICONO
  	=============================================*/

  	$("#guardarIcono").click(function(){

		var datos = new FormData();
		datos.append("imagenIcono", imagenIcono);

		$.ajax({

			url:"ajax/comercio.ajax.php",
			method: "POST",
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			success: function(respuesta){
				
				if(respuesta == "ok"){

					swal({
				      title: "Cambios guardados",
				      text: "¡El icono se a actualizado correctamente correctamente!",
				      type: "success",
				      confirmButtonText: "¡Cerrar!"
				    });
			
				}
		
			}

		});

	})

})


/*=============================================
CAMBIAR COLOR
=============================================*/

$(".cambioColor").change(function(){
 
	var barraSuperior = $("#barraSuperior").val();

	var textoSuperior = $("#textoSuperior").val();

	var colorFondo = $("#colorFondo").val();

	var colorTexto = $("#colorTexto").val();

	$("#guardarColores").click(function(){

		var datos = new FormData();
		datos.append("barraSuperior", barraSuperior);
		datos.append("textoSuperior", textoSuperior);
		datos.append("colorFondo", colorFondo);
		datos.append("colorTexto", colorTexto);


		$.ajax({

			url:"ajax/comercio.ajax.php",
			method: "POST",
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			success: function(respuesta){

				//console.log("respuesta",respuesta);
				
				if(respuesta == "ok"){

					swal({
				      title: "Cambios guardados",
				      text: "¡La plantilla ha sido actualizada correctamente!",
				      type: "success",
				      confirmButtonText: "¡Cerrar!"
				    });
			
				}

			}

		})

	})

})

/*=============================================
CAMBIAR COLOR REDES SOCIALES
=============================================*/
var checkBox = $(".seleccionarRed");

$("input[name='colorRedSocial']").on("ifChecked", function(){

	var color = $(this).val();
	var colorRed = null;

	var iconos = $(".redSocial");
	var redes = ["facebook", "twitter","google-plus","youtube"];
	
	if(color == "color"){

		colorRed = "Color";

	}else if(color == "blanco"){

		colorRed = "Blanco";

	}else{

		colorRed = "Negro";

	}

	for(var i = 0; i < iconos.length; i++){

		$(iconos[i]).attr("class","fa fa-"+redes[i]+" "+redes[i]+colorRed+" redSocial");
		$(checkBox[i]).attr("estilo", redes[i]+colorRed);

	}

	crearDatosJsonRedes();
})

/*=============================================
CAMBIAR URL REDES SOCIALES
=============================================*/
$(".cambiarUrlRed").change(function(){

	var cambiarUrlRed = $(".cambiarUrlRed");

	for(var i = 0; i < cambiarUrlRed.length; i++){

		$(checkBox[i]).attr("ruta", $(cambiarUrlRed[i]).val());

	}

	crearDatosJsonRedes();

})


/*=============================================
QUITAR RED SOCIAL
=============================================*/
$(".seleccionarRed").on("ifUnchecked",function(){

	$(this).attr("validarRed","");

	crearDatosJsonRedes();

})

/*=============================================
AGREGAR RED SOCIAL
=============================================*/

$(".seleccionarRed").on("ifChecked",function(){

	$(this).attr("validarRed", $(this).attr("red"));

	crearDatosJsonRedes();

})

/*=============================================
CREAR DATOS JSON PARA ALMACENAR EN BD
=============================================*/ 
function crearDatosJsonRedes(){

	var redesSociales = [];

	for(var i = 0; i < checkBox.length; i++){

		if($(checkBox[i]).attr("validarRed") != ""){

			redesSociales.push({"red": $(checkBox[i]).attr("red"),
								"estilo": $(checkBox[i]).attr("estilo"),
								"url": $(checkBox[i]).attr("ruta"),
								"activo": 1})


		}else{

			redesSociales.push({"red": $(checkBox[i]).attr("red"),
								"estilo": $(checkBox[i]).attr("estilo"),
								"url": $(checkBox[i]).attr("ruta"),
								"activo": 0})

		}

		$("#valorRedesSociales").val(JSON.stringify(redesSociales));

	}

}
/*=============================================
GUARDAR REDES SOCIALES
=============================================*/

$("#guardarRedesSociales").click(function(){

	var valorRedesSociales = $("#valorRedesSociales").val();

	var datos = new FormData();
	datos.append("redesSociales", valorRedesSociales);

	$.ajax({

		url:"ajax/comercio.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		success: function(respuesta){

			if(respuesta == "ok"){

				swal({
			      title: "Cambios guardados",
			      text: "¡Las redes sociales se an actualizado correctamente!",
			      type: "success",
			      confirmButtonText: "¡Cerrar!"
			    });
		
			}

		}

	})

})
/*=============================================
CAMBIAR CÓDIGOS
=============================================*/

$(".cambioScript").change(function(){

	var apiFacebook = $("#apiFacebook").val();

	var pixelFacebook = $("#pixelFacebook").val();

	var googleAnalytics = $("#googleAnalytics").val();


	$("#guardarScript").click(function(){


		var datos = new FormData();
		datos.append("apiFacebook", apiFacebook);
		datos.append("pixelFacebook", pixelFacebook);
		datos.append("googleAnalytics", googleAnalytics);

		$.ajax({

			url:"ajax/comercio.ajax.php",
			method: "POST",
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			success: function(respuesta){
				
				if(respuesta == "ok"){

					swal({
				      title: "Cambios guardados",
				      text: "¡Los codigos se han actualizado correctamente correctamente!",
				      type: "success",
				      confirmButtonText: "¡Cerrar!"
				    });
			
				}
				
			}

		})

	})

})


/*=============================================
SELECCIONAR PAIS
=============================================*/

$.ajax({
	url:"vistas/js/countries.json",
	type: "GET",
	cache: false,
	contentType: false,
	processData:false,
	dataType:"json",
	success: function(respuesta){

		respuesta.forEach(seleccionarPais);

		function seleccionarPais(item, index){

			var pais = item.name;
			var codPais = item.code;

			if($("#codigoPais").val() == codPais){

				$("#paisSeleccionado").attr("value",codPais);
				$("#paisSeleccionado").html(pais);

			}

			$("#seleccionarPais").append('<option value="'+codPais+'">'+pais+'</option>');

		}

	}

})


/*=============================================
CAMBIAR INFORMACIÓN
=============================================*/

var impuesto = $("#impuesto").val();
var envioNacional = $("#envioNacional").val();
var envioInternacional = $("#envioInternacional").val();
var tasaMininaNa = $("#tasaMininaNa").val();
var tasaMininaInter = $("#tasaMininaInter").val();
var seleccionarPais = $("#codigoPais").val();
var clienteidPaypal = $("#clienteidPaypal").val();
var llaveSecretPaypal = $("#llaveSecretPaypal").val();
var LlavePublicaMercadoPago = $("#LlavePublicaMercadoPago").val();
var TokenDeAcceso = $("#TokenDeAcceso").val();

/*=============================================
CAMBIAR MODO PAYPAL
=============================================*/
$("input[name='modoPaypal']").on("ifChecked",function(){

	var modoPaypal = $(this).val();
	var modoPayu = $("input[name='modoPayu']:checked").val();

	$("#guardarInformacion").click(function(){

		cambiarInformacion(modoPaypal, modoPayu);

	});

})


/*=============================================
CAMBIAR MODO PAYU
=============================================*/

$("input[name='modoPayu']").on("ifChecked",function(){

	var modoPayu = $(this).val();
	var modoPaypal = $("input[name='modoPaypal']:checked").val();

	$("#guardarInformacion").click(function(){

		cambiarInformacion(modoPaypal, modoPayu);

	});

})


/*=============================================
GUARDAR LA INFORMACION
=============================================*/

$(".cambioInformacion").change(function(){

	impuesto = $("#impuesto").val();

	envioNacional = $("#envioNacional").val();

	envioInternacional = $("#envioInternacional").val();

	tasaMininaNa = $("#tasaMininaNa").val();

	tasaMininaInter = $("#tasaMininaInter").val();

	seleccionarPais = $("#seleccionarPais").val();

	modoPaypal = $("input[name='modoPaypal']:checked").val();

	clienteidPaypal = $("#clienteidPaypal").val();

	llaveSecretPaypal = $("#llaveSecretPaypal").val();

	modoPayu = $("input[name='modoPayu']:checked").val();

	LlavePublicaMercadoPago = $("#LlavePublicaMercadoPago").val();

	TokenDeAcceso = $("#TokenDeAcceso").val();

	$("#guardarInformacion").click(function(){

		cambiarInformacion(modoPaypal, modoPayu);
	
	})	

})

/*=============================================
// FUNCIÓN PARA CAMBIAR LA INFORMACIÓN
=============================================*/

function cambiarInformacion(modoPaypal, modoPayu){

	var datos = new FormData();
	datos.append("impuesto", impuesto);
	datos.append("envioNacional", envioNacional);
	datos.append("envioInternacional", envioInternacional);
	datos.append("tasaMininaNa", tasaMininaNa);
	datos.append("tasaMininaInter", tasaMininaInter);
	datos.append("seleccionarPais", seleccionarPais);
	datos.append("modoPaypal", modoPaypal);	
	datos.append("clienteidPaypal", clienteidPaypal);
	datos.append("llaveSecretPaypal", llaveSecretPaypal);
	datos.append("modoPayu", modoPayu);	
	datos.append("LlavePublicaMercadoPago", LlavePublicaMercadoPago);
	datos.append("TokenDeAcceso", TokenDeAcceso);

	$.ajax({

		url:"ajax/comercio.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		success: function(respuesta){
			
			if(respuesta == "error"){

				swal({
			      title: "Cambios guardados",
			      text: "¡El comercio ha sido actualizado correctamente!",
			      type: "success",
			      confirmButtonText: "¡Cerrar!"
			    });
			
			}else{

				alert("fallo alcambiar informacion");

			}
							
		}

	})
}