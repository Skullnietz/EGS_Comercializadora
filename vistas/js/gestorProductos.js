/*=============================================

CARGAR LA TABLA DINÁMICA DE PRODUCTOS

=============================================*/

 $.ajax({



	url:"ajax/tablaProductos.ajax.php",

	success:function(respuesta){

		

 		//console.log("respuesta", respuesta);



	}



 })

$(".tablaProductos").DataTable({
	 "ajax": "ajax/tablaProductos.ajax.php?perfil="+$("#tipoDePerfil").val()+"&empresa="+$("#id_empresa").val(),
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
AGREGAR DATOS A BOTON
=============================================*/
$(document).on("click", ".btnEditarProducto", function(){		        

	var hola = "hola";
	$(".btnEditarProducto").attr("idProducto");

	$(".previsualizarImgFisico").html("");
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

			//console.log("hola",respuesta);
		}
	})

});

/*=============================================

ACTIVAR PRODUCTO

=============================================*/

$('.tablaProductos tbody').on("click", ".btnActivar", function(){



	var idProducto = $(this).attr("idProducto");

	var estadoProducto = $(this).attr("estadoProducto");



	var datos = new FormData();

 	datos.append("activarId", idProducto);

  	datos.append("activarProducto", estadoProducto);



  	$.ajax({



	  url:"ajax/productos.ajax.php",

	  method: "POST",

	  data: datos,

	  cache: false,

      contentType: false,

      processData: false,

      success: function(respuesta){    

          

           //console.log("respuesta", respuesta);



      }



  	})



	if(estadoProducto == 0){



  		$(this).removeClass('btn-success');

  		$(this).addClass('btn-danger');

  		$(this).html('Desactivado');

  		$(this).attr('estadoProducto',1);



  	}else{



  		$(this).addClass('btn-success');

  		$(this).removeClass('btn-danger');

  		$(this).html('Activado');

  		$(this).attr('estadoProducto',0);



  	}



})



/*=============================================

REVISAR SI EL TITULO DEL PRODUCTO YA EXISTE

=============================================*/



$(".validarProducto").change(function(){



	$(".alert").remove();



	var producto = $(this).val();



	var datos = new FormData();

	datos.append("validarProducto", producto);



	 $.ajax({

	    url:"ajax/productos.ajax.php",

	    method:"POST",

	    data: datos,

	    cache: false,

	    contentType: false,

	    processData: false,

	    dataType: "json",

	    success:function(respuesta){



    		if(respuesta.length != 0){



    			$(".validarProducto").parent().after('<div class="alert alert-warning">Este título de producto ya existe en la base de datos</div>');



	    		$(".validarProducto").val("");



    		}



	    }



   	})

		//generarbarcode();

		//imprimir();

})



/*=============================================

RUTA PRODUCTO

=============================================*/



function limpiarUrl(texto){

  var texto = texto.toLowerCase(); 

  texto = texto.replace(/[á]/, 'a');

  texto = texto.replace(/[é]/, 'e');

  texto = texto.replace(/[í]/, 'i');

  texto = texto.replace(/[ó]/, 'o');

  texto = texto.replace(/[ú]/, 'u');

  texto = texto.replace(/[ñ]/, 'n');

  texto = texto.replace(/[-]/, ' ');

  texto = texto.replace(/ /g, "-")

  return texto;

}



$(".tituloProducto").change(function(){



	$(".rutaProducto").val(limpiarUrl($(".tituloProducto").val()));



})

$(".tituloProducto").on("keyup", function(){
	var modalAgregar = $("#modalAgregarProducto");
	if(modalAgregar.is(":visible")){
		modalAgregar.find(".rutaProducto").val(limpiarUrl($(this).val()));
		actualizarAsistenteAltaProducto();
	}
})

function actualizarAsistenteAltaProducto(){
	var modal = $("#modalAgregarProducto");
	if(!modal.length){
		return;
	}

	var titulo = $.trim(modal.find(".tituloProducto").val());
	var ruta = $.trim(modal.find(".rutaProducto").val());
	var tipoProducto = $.trim(modal.find(".seleccionarTipo").val());
	var categoria = $.trim(modal.find(".seleccionarCategoria").val());
	var subCategoria = $.trim(modal.find(".seleccionarSubCategoria").val());
	var descripcion = $.trim(modal.find(".descripcionProducto").val());
	var palabras = $.trim(modal.find(".pClavesProducto").val());
	var precio = $.trim(modal.find(".precio").val());
	var disponibilidad = $.trim(modal.find(".disponibilidad").val());
	var proveedor = $.trim(modal.find(".Proveedor").val());

	var camposClave = [titulo, tipoProducto, categoria, subCategoria, descripcion, palabras, precio];
	var completados = 0;

	for(var i = 0; i < camposClave.length; i++){
		if(camposClave[i] !== ""){
			completados++;
		}
	}

	var porcentaje = Math.round((completados / camposClave.length) * 100);

	$("#altaProgressBar").css("width", porcentaje + "%");
	$("#altaProgressText").text("Completitud de campos clave: " + porcentaje + "%");
	$("#resumenTituloProducto").text(titulo || "Sin definir");
	$("#resumenRutaProducto").text(ruta || "Sin generar");
	$("#resumenPrecioProducto").text(precio !== "" ? Number(precio).toFixed(2) : "0.00");
	$("#resumenDisponibilidadProducto").text(disponibilidad || "0");
	$("#resumenProveedorProducto").text(proveedor || "No definido");
}

var altaWizardStep = 1;

function renderAltaWizardStep(step){
	altaWizardStep = step;

	var modal = $("#modalAgregarProducto");
	modal.find(".alta-step").removeClass("active");
	modal.find('.alta-step[data-step="' + step + '"]').addClass("active");

	modal.find(".alta-step-chip").removeClass("active");
	modal.find('.alta-step-chip[data-wizard-chip="' + step + '"]').addClass("active");

	if(step === 1){
		modal.find(".wizardPrev").hide();
		modal.find(".wizardNext").show();
		modal.find(".wizardFinishBtn").hide();
	}else if(step === 2){
		modal.find(".wizardPrev").show();
		modal.find(".wizardNext").show();
		modal.find(".wizardFinishBtn").hide();
	}else{
		modal.find(".wizardPrev").show();
		modal.find(".wizardNext").hide();
		modal.find(".wizardFinishBtn").show();
	}
}

function validarPasoWizard(step){
	var modal = $("#modalAgregarProducto");

	if(step === 1){
		if($.trim(modal.find(".tituloProducto").val()) === "" || $.trim(modal.find(".seleccionarTipo").val()) === ""){
			swal({
				title: "Completa los datos base",
				text: "Ingresa titulo y tipo de producto para continuar.",
				type: "warning",
				confirmButtonText: "Cerrar"
			});
			return false;
		}
	}

	if(step === 2){
		if($.trim(modal.find(".seleccionarCategoria").val()) === "" ||
		   $.trim(modal.find(".seleccionarSubCategoria").val()) === "" ||
		   $.trim(modal.find(".descripcionProducto").val()) === "" ||
		   $.trim(modal.find(".pClavesProducto").val()) === ""){
			swal({
				title: "Faltan datos de contenido",
				text: "Selecciona categoria/subcategoria y completa descripcion y palabras clave.",
				type: "warning",
				confirmButtonText: "Cerrar"
			});
			return false;
		}
	}

	return true;
}

$("#modalAgregarProducto").on("shown.bs.modal", function(){
	renderAltaWizardStep(1);
	actualizarAsistenteAltaProducto();
})

$("#modalAgregarProducto").on("click", ".wizardNext", function(){
	if(!validarPasoWizard(altaWizardStep)){
		return;
	}
	renderAltaWizardStep(Math.min(3, altaWizardStep + 1));
})

$("#modalAgregarProducto").on("click", ".wizardPrev", function(){
	renderAltaWizardStep(Math.max(1, altaWizardStep - 1));
})

$("#modalAgregarProducto").on("click", ".alta-step-chip", function(){
	var targetStep = parseInt($(this).attr("data-wizard-chip"), 10);
	if(targetStep <= altaWizardStep){
		renderAltaWizardStep(targetStep);
		return;
	}

	if(validarPasoWizard(altaWizardStep)){
		renderAltaWizardStep(targetStep);
	}
})

$("#modalAgregarProducto").on("keyup change", ".tituloProducto, .rutaProducto, .seleccionarTipo, .seleccionarCategoria, .seleccionarSubCategoria, .descripcionProducto, .pClavesProducto, .precio, .disponibilidad, .Proveedor", function(){
	actualizarAsistenteAltaProducto();
})

$("#modalAgregarProducto").on("click", ".alta-template-btn", function(){
	var modal = $("#modalAgregarProducto");
	var template = $(this).attr("data-template");

	$("#modalAgregarProducto .alta-template-btn").removeClass("active");
	$(this).addClass("active");

	if(template == "fisico"){
		modal.find(".seleccionarTipo").val("fisico").trigger("change");
		modal.find(".entrega").val(2);
		modal.find(".peso").val(0.5);
		modal.find(".disponibilidad").val(10);
		modal.find(".seleccionarMedida").val("PZAS");
	}

	if(template == "servicio"){
		modal.find(".seleccionarTipo").val("virtual").trigger("change");
		modal.find(".entrega").val(0);
		modal.find(".peso").val(0);
		modal.find(".disponibilidad").val(1);
		modal.find(".seleccionarMedida").val("PZAS");
	}

	if(template == "express"){
		if(modal.find(".SubircodigoProducto").val() == ""){
			modal.find(".SubircodigoProducto").val("PRD-" + Date.now());
		}
		if(modal.find(".Proveedor").val() == ""){
			modal.find(".Proveedor").val("Proveedor local");
		}
		if(modal.find(".precio").val() == ""){
			modal.find(".precio").val(0);
		}
	}

	actualizarAsistenteAltaProducto();
})

$(document).on("keydown", function(e){
	if(e.ctrlKey && e.keyCode === 13 && $("#modalAgregarProducto").is(":visible")){
		e.preventDefault();
		$("#modalAgregarProducto .guardarProducto").trigger("click");
	}
})

/*=============================================

AGREGAR MULTIMEDIA

=============================================*/



var tipo = null;



$(".seleccionarTipo").change(function(){



	tipo = $(this).val();



	if(tipo == "virtual"){



		$(".multimediaVirtual").show();

		$(".multimediaFisica").hide();



		$(".detallesVirtual").show();

		$(".detallesFisicos").hide();

	

	}else{



		$(".multimediaFisica").show();

		$(".multimediaVirtual").hide();

		

		$(".detallesFisicos").show();

		$(".detallesVirtual").hide();	



	}

})



/*=============================================

AGREGAR MULTIMEDIA CON DROPZONE

=============================================*/



var arrayFiles = [];



$(".multimediaFisica").dropzone({



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

AGREGAR MULTIMEDIA CON DROPZONE

=============================================*/



var arrayFiles = [];



$(".multimediaVirtual").dropzone({



	url: "/",

	addRemoveLinks: true,

	acceptedFiles: "image/jpeg, image/png",

	maxFilesize: 2,

	maxFiles: 10,

	init: function(){



		this.on("addedfile", function(file){



			arrayFiles.push(file);



			// console.log("arrayFiles", arrayFiles);



		})



		this.on("removedfile", function(file){



			var index = arrayFiles.indexOf(file);



			arrayFiles.splice(index, 1);



			// console.log("arrayFiles", arrayFiles);



		})



	}



})





/*=============================================

SELECCIONAR SUBCATEGORÍA

=============================================*/



$(".seleccionarCategoria").change(function(){



	var categoria = $(this).val();



	$(".seleccionarSubCategoria").html("");



	$("#modalEditarProducto .seleccionarSubCategoria").html("");



	var datos = new FormData();

	datos.append("idCategoria", categoria);



	 $.ajax({

	    url:"ajax/subCategorias.ajax.php",

	    method:"POST",

	    data: datos,

	    cache: false,

	    contentType: false,

	    processData: false,

	    dataType: "json",

	    success:function(respuesta){

	    	

	    	// console.log("respuesta", respuesta);



	    	$(".entradaSubcategoria").show();



	    	respuesta.forEach(funcionForEach);



	        function funcionForEach(item, index){



	        	$(".seleccionarSubCategoria").append(



    				'<option value="'+item["id"]+'">'+item["subcategoria"]+'</option>'



    			)



	        }



	    }



	})



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



var imagenFotoPrincipal = null;



$(".fotoPrincipal").change(function(){



	imagenFotoPrincipal = this.files[0];

	

	/*=============================================

  	VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG

  	=============================================*/



  	if(imagenFotoPrincipal["type"] != "image/jpeg" && imagenFotoPrincipal["type"] != "image/png"){



  		$(".fotoPrincipal").val("");



  		 swal({

		      title: "Error al subir la imagen",

		      text: "¡La imagen debe estar en formato JPG o PNG!",

		      type: "error",

		      confirmButtonText: "¡Cerrar!"

		    });



  	}else if(imagenFotoPrincipal["size"] > 2000000){



  		$(".fotoPrincipal").val("");



  		 swal({

		      title: "Error al subir la imagen",

		      text: "¡La imagen no debe pesar más de 2MB!",

		      type: "error",

		      confirmButtonText: "¡Cerrar!"

		    });



  	}else{



  		var datosImagen = new FileReader;

  		datosImagen.readAsDataURL(imagenFotoPrincipal);



  		$(datosImagen).on("load", function(event){



  			var rutaImagen = event.target.result;



  			$(".previsualizarPrincipal").attr("src", rutaImagen);



  		})



  	}



})



/*=============================================

ACTIVAR OFERTA

=============================================*/



function activarOferta(event){



	if(event == "oferta"){



		$(".datosOferta").show();

		$(".valorOferta").prop("required",true);

		$(".valorOferta").val("");





	}else{



		$(".datosOferta").hide();

		$(".valorOferta").prop("required",false);

		$(".valorOferta").val("");



	}

}





$(".selActivarOferta").change(function(){



	activarOferta($(this).val())



})



/*=============================================

VALOR OFERTA POR PRECIO

=============================================*/

$("#modalAgregarProducto .precioOferta").change(function() {

	var precio = $(".precio").val();

	var nvoPrecioOferta = $(this).val();

	if (precio != 0) {

		var descuento = 100 - (Number($(this).val())*100/Number($(".precio").val()));

 

		$(".precioOferta").prop("readonly",true);

		$(".descuentoOferta").prop("readonly",false);

		$(".descuentoOferta").val(Math.ceil(descuento));

	} else {

		swal({

			title: "Error al agregar la oferta",

			text: "¡Primero agregue un precio al producto!",

			type: "error",

			confirmButtonText: "¡Cerrar!"

		});

		$(".precioOferta").val("");

		$(".descuentoOferta").val("");

		return;

	}

})

 

/*=============================================

VALOR OFERTA POR DESCUENTO

=============================================*/

$("#modalAgregarProducto  .descuentoOferta").change(function() {

	var precio = $(".precio").val();

	var nvoDescuento = $(this).val();

	if (precio != 0) {

		var oferta = Number($(".precio").val())-(Number($(this).val())*Number($(".precio").val())/100);

			

		$(".descuentoOferta").prop("readonly",true);

		$(".precioOferta").prop("readonly",false);

		$(".precioOferta").val(oferta);

	} else {

		swal({

			title: "Error al agregar la oferta",

			text: "¡Primero agregue un precio al producto!",

			type: "error",

			confirmButtonText: "¡Cerrar!"

		});

		$(".precioOferta").val("");

		$(".descuentoOferta").val("");

		return;

	}

})



/*=============================================

SUBIENDO LA FOTO DE LA OFERTA

=============================================*/



var imagenOferta = null;



$(".fotoOferta").change(function(){



	imagenOferta = this.files[0];

	

	/*=============================================

  	VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG

  	=============================================*/



  	if(imagenOferta["type"] != "image/jpeg" && imagenOferta["type"] != "image/png"){



  		$(".fotoOferta").val("");



  		 swal({

		      title: "Error al subir la imagen",

		      text: "¡La imagen debe estar en formato JPG o PNG!",

		      type: "error",

		      confirmButtonText: "¡Cerrar!"

		    });



  	}else if(imagenOferta["size"] > 2000000){



  		$(".fotoOferta").val("");



  		 swal({

		      title: "Error al subir la imagen",

		      text: "¡La imagen no debe pesar más de 2MB!",

		      type: "error",

		      confirmButtonText: "¡Cerrar!"

		    });



  	}else{



  		var datosImagen = new FileReader;

  		datosImagen.readAsDataURL(imagenOferta);



  		$(datosImagen).on("load", function(event){



  			var rutaImagen = event.target.result;



  			$(".previsualizarOferta").attr("src", rutaImagen);



  		})



  	}

})

/*=============================================

CAMBIAR EL PRECIO

=============================================*/



$(".precio").change(function(){



	$(".precioOferta").val(0);

	$(".descuentoOferta").val(0);



})



/*=============================================

GUARDAR EL PRODUCTO

=============================================*/



var multimediaFisica = null;

var multimediaVirtual = null;	



$(".guardarProducto").click(function(){



	/*=============================================

	PREGUNTAMOS SI LOS CAMPOS OBLIGATORIOS ESTÁN LLENOS

	=============================================*/



	if($(".tituloProducto").val() != "" && 

	   $(".seleccionarTipo").val() != "" && 

	   $(".seleccionarCategoria").val() != "" &&

	   $(".seleccionarSubCategoria").val() != "" &&

	   $(".descripcionProducto").val() != "" &&

	   $(".pClavesProducto").val() != ""){

	   

	   	/*=============================================

	   	PREGUNTAMOS SI VIENEN IMÁGENES PARA MULTIMEDIA O LINK DE YOUTUBE

	   	=============================================*/



	   	if(tipo != "virtual"){



	   		if(arrayFiles.length > 0 && $(".rutaProducto").val() != ""){



	   			var listaMultimedia = [];

	   			var finalFor = 0;



	   			for(var i = 0; i < arrayFiles.length; i++){



	   				var datosMultimedia = new FormData();

	   				datosMultimedia.append("file", arrayFiles[i]);

					datosMultimedia.append("ruta", $(".rutaProducto").val());



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

							

							listaMultimedia.push({"foto" : respuesta.substr(3)})

							multimediaFisica = JSON.stringify(listaMultimedia);

							multimediaVirtual = null;



							if(multimediaFisica == null){



							 	swal({

							      title: "El campo de multimedia no debe estar vacío",

							      type: "error",

							      confirmButtonText: "¡Cerrar!"

							    });



							 	return;



							}



							if((finalFor + 1) == arrayFiles.length){



								agregarMiProducto(multimediaFisica);

								finalFor = 0;



							}



							finalFor++;



						}



					})



	   			}



	   		}



	   	}else{



	   		multimediaVirtual = $(".multimedia").val();

	   		multimediaFisica = null;



	   		if(multimediaVirtual == null){	



 			 swal({

			      title: "El campo de multimedia no debe estar vacío",

			      type: "error",

			      confirmButtonText: "¡Cerrar!"

			    });



 			  return;

			

			}



			agregarMiProducto(multimediaVirtual);		



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



function agregarMiProducto(imagen){



		/*=============================================
		ALMACENAMOS TODOS LOS CAMPOS DE PRODUCTO
		=============================================*/



	   var tituloProducto = $(".tituloProducto").val();

	   var rutaProducto = $(".rutaProducto").val();

	   var seleccionarTipo = $(".seleccionarTipo").val();

	   var seleccionarCategoria = $(".seleccionarCategoria").val();

	   var seleccionarSubCategoria = $(".seleccionarSubCategoria").val();

	   var empresa = $(".empresa").val();

	   var descripcionProducto = $(".descripcionProducto").val();

	   var pClavesProducto = $(".pClavesProducto").val();

	   var precio = $(".precio").val();

	   var peso = $(".peso").val();

	   var entrega = $(".entrega").val();

	   var disponibilidad = $(".disponibilidad").val();

	   var Proveedor = $(".Proveedor").val();

	   var selActivarOferta = $(".selActivarOferta").val();

	   var precioOferta = $(".precioOferta").val();

	   var descuentoOferta = $(".descuentoOferta").val();

	   var finOferta = $(".finOferta").val();

	   var SubircodigoProducto = $(".SubircodigoProducto").val();

	   var EntradInversion = $(".EntradInversion").val();

	   var medida = $(".seleccionarMedida").val();

	   var cantidadTipo = $(".cantidadTipo").val()

	   var id_almacen = $(".id_almacen").val()

	    var id_empresa = $(".id_empresa").val()

	    if(seleccionarTipo == "virtual"){



			var detalles = {"Reparacion": $(".detalleVirtual").tagsinput('items')};

		}else{



			var detalles = {"Color": $(".detalleColor").tagsinput('items')};



		}



		var detallesString = JSON.stringify(detalles);



	 	var datosProducto = new FormData();

		datosProducto.append("tituloProducto", tituloProducto);

		datosProducto.append("rutaProducto", rutaProducto);

		datosProducto.append("seleccionarTipo", seleccionarTipo);	

		datosProducto.append("detalles", detallesString);	

		datosProducto.append("seleccionarCategoria", seleccionarCategoria);

		datosProducto.append("seleccionarSubCategoria", seleccionarSubCategoria);

		datosProducto.append("empresa", empresa);

		datosProducto.append("descripcionProducto", descripcionProducto);

		datosProducto.append("pClavesProducto", pClavesProducto);

		datosProducto.append("precio", precio);

		datosProducto.append("peso", peso);

		datosProducto.append("entrega", entrega);

		datosProducto.append("disponibilidad", disponibilidad);

		datosProducto.append("Proveedor", Proveedor);		



		datosProducto.append("multimedia", imagen);

		

		datosProducto.append("fotoPortada", imagenPortada);

		datosProducto.append("fotoPrincipal", imagenFotoPrincipal);

		datosProducto.append("selActivarOferta", selActivarOferta);

		datosProducto.append("precioOferta", precioOferta);

		datosProducto.append("descuentoOferta", descuentoOferta);

		datosProducto.append("finOferta", finOferta);

		datosProducto.append("fotoOferta", imagenOferta);

		datosProducto.append("SubircodigoProducto", SubircodigoProducto);

		datosProducto.append("EntradInversion", EntradInversion);

		datosProducto.append("medida", medida);

		datosProducto.append("cantidadTipo", cantidadTipo);


		datosProducto.append("id_almacen", id_almacen);

		datosProducto.append("id_empresa",id_empresa);

		$.ajax({

				url:"ajax/productos.ajax.php",

				method: "POST",

				data: datosProducto,

				cache: false,

				contentType: false,

				processData: false,

				success: function(respuesta){

					

					// console.log("respuesta", respuesta);



					if(respuesta == "ok"){



						swal({

						  type: "success",

						  title: "El producto ha sido guardado correctamente",

						  showConfirmButton: true,

						  confirmButtonText: "Cerrar"

						  }).then(function(result){

							if (result.value) {



							window.location = "productos";



							}

						})

					}



				}



		})



}





/*=============================================

EDITAR PRODUCTO

=============================================*/



$('.tablaProductos').on("click", ".btnEditarProducto", function(){

	

	$(".previsualizarImgFisico").html("");


		
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



			//console.log("datos de productos:", respuesta);

			

			$("#modalEditarProducto .idProducto").val(respuesta[0]["id"]);

			$("#Editarempresa").html(respuesta[0]["empresa"]);

			$("#modalEditarProducto .tituloProducto").val(respuesta[0]["titulo"]);

			$("#modalEditarProducto .rutaProducto").val(respuesta[0]["ruta"]);

			$("#modalEditarProducto .codigoEditado").val(respuesta[0]["codigo"]);

			$("#modalEditarProducto .inversionEditada").val(respuesta[0]["inversion"]);

			/*=============================================

			TRAER EL TIPO DE PRODUCTO

			=============================================*/



			$("#modalEditarProducto .seleccionarTipo").val(respuesta[0]["tipo"]);

			$("#modalEditarProducto .entradaEditadaMedicion").val(respuesta[0]["medida"]);



			/*=============================================

			CUANDO EL PRODUCTO ES VIRTUAL

			=============================================*/



			if(respuesta[0]["tipo"] == "virtual"){

		

				$(".multimediaVirtual").show();

				$(".multimediaFisica").hide();



				$("#modalEditarProducto .multimedia").val(respuesta[0]["multimedia"]);



				$(".detallesVirtual").show();

				$(".detallesFisicos").hide();



				var detalles = JSON.parse(respuesta[0]["detalles"]);

				

				$("#modalEditarProducto .detalleReparacion").val(detalles.Reparacion);





			/*=============================================

			CUANDO EL PRODUCTO ES FÍSICO

			=============================================*/

			

			}else{



				$(".multimediaVirtual").hide();

				$(".multimediaFisica").show();



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



				$(".detallesVirtual").hide();

				$(".detallesFisicos").show();



				var detalles = JSON.parse(respuesta[0]["detalles"]);





				$(".editarColor").html(



					'<input class="form-control input-lg tagsInput detalleColor" value="'+detalles.Color+'" data-role="tagsinput" type="text" style="padding:20px">'



				)



				$("#modalEditarProducto .detalleColor").tagsinput('items');



				

				$(".bootstrap-tagsinput").css({"padding":"12px", "width":"110%"})

			

			}



			/*=============================================

			TRAEMOS LA CATEGORIA

			=============================================*/



			if(respuesta[0]["id_categoria"] != 0){

			

				var datosCategoria = new FormData();

				datosCategoria.append("idCategoria", respuesta[0]["id_categoria"]);

				



				$.ajax({



						url:"ajax/categorias.ajax.php",

						method: "POST",

						data: datosCategoria,

						cache: false,

						contentType: false,

						processData: false,

						dataType: "json",

						success: function(respuesta){



							$("#modalEditarProducto .seleccionarCategoria").val(respuesta["id"]);

							$("#modalEditarProducto .optionEditarCategoria").html(respuesta["categoria"]);



							

						}



					})



			}else{



				

				$("#modalEditarProducto .optionEditarCategoria").html("SIN CATEGORÍA");



			}



			/*=============================================

			TRAEMOS LA SUBCATEGORIA

			=============================================*/



			if(respuesta[0]["id_subcategoria"] != 0){

					

				var datosSubCategoria = new FormData();

				datosSubCategoria.append("idSubCategoria", respuesta[0]["id_subcategoria"]);



				$.ajax({

								  

						url:"ajax/subCategorias.ajax.php",

						method: "POST",

						data: datosSubCategoria,

						cache: false,

						contentType: false,

						processData: false,

						dataType: "json",

						success: function(respuesta){	





							$("#modalEditarProducto .optionEditarSubCategoria").val(respuesta[0]["id"]);

							$("#modalEditarProducto .optionEditarSubCategoria").html(respuesta[0]["subcategoria"]);



							var datosCategoria = new FormData();

							datosCategoria.append("idCategoria", respuesta[0]["id_categoria"]);	



							$.ajax({



								url:"ajax/subCategorias.ajax.php",

								method: "POST",

								data: datosCategoria,

								cache: false,

								contentType: false,

								processData: false,

								dataType: "json",

								success: function(respuesta){



									respuesta.forEach(funcionForEach);



							        function funcionForEach(item, index){



						    			$("#modalEditarProducto .seleccionarSubCategoria").append(



						    				'<option value="'+item["id"]+'">'+item["subcategoria"]+'</option>'



						    			)



						    		}



								}



							})												



						}



					})



			}else{

				

				$("#modalEditarProducto  .optionEditarSubCategoria").html("SIN CATEGORÍA");



			}



			/*=============================================

			TRAEMOS DATOS DE CABECERA

			=============================================*/



			var datosCabecera = new FormData();

			datosCabecera.append("ruta", respuesta[0]["ruta"]);



			$.ajax({



					url:"ajax/cabeceras.ajax.php",

					method: "POST",

					data: datosCabecera,

					cache: false,

					contentType: false,

					processData: false,

					dataType: "json",

					success: function(respuesta){



						/*=============================================

						CARGAMOS EL ID DE LA CABECERA

						=============================================*/



						$("#modalEditarProducto .idCabecera").val(respuesta["id"]);



						/*=============================================

						CARGAMOS LA DESCRIPCION

						=============================================*/



						$("#modalEditarProducto .descripcionProducto").val(respuesta["descripcion"]);



						/*=============================================

						CARGAMOS LAS PALABRAS CLAVES

						=============================================*/	

						

						if(respuesta["palabrasClaves"] != null){



							$("#modalEditarProducto .editarPalabrasClaves").html('<div class="input-group">'+

	              

	                		'<span class="input-group-addon"><i class="fa fa-key"></i></span>'+ 



							'<input type="text" class="form-control input-lg tagsInput pClavesProducto" value="'+respuesta["palabrasClaves"]+'" data-role="tagsinput">'+

							



							'</div>');



							$("#modalEditarProducto .pClavesProducto").tagsinput('items');



						}else{



							$("#modalEditarProducto .editarPalabrasClaves").html('<div class="input-group">'+

	              

	                		'<span class="input-group-addon"><i class="fa fa-key"></i></span>'+ 



							'<input type="text" class="form-control input-lg tagsInput pClavesProducto" value="" data-role="tagsinput">'+



							'</div>');



							$("#modalEditarProducto .pClavesProducto").tagsinput('items');



						}



						/*=============================================

						CARGAMOS LA IMAGEN DE PORTADA

						=============================================*/



						$("#modalEditarProducto .previsualizarPortada").attr("src", respuesta["portada"]);

						$("#modalEditarProducto .antiguaFotoPortada").val(respuesta["portada"]);

					

					}

					

			});



			/*=============================================

			CARGAMOS LA IMAGEN PRINCIPAL

			=============================================*/



			$("#modalEditarProducto .previsualizarPrincipal").attr("src", respuesta[0]["portada"]);

			$("#modalEditarProducto .antiguaFotoPrincipal").val(respuesta[0]["portada"]);



			/*=============================================

			CARGAMOS EL PRECIO, PESO Y DIAS DE ENTREGA

			=============================================*/

			$("#modalEditarProducto .precio").val(respuesta[0]["precio"]);

			$("#modalEditarProducto .peso").val(respuesta[0]["peso"]);

			$("#modalEditarProducto .entrega").val(respuesta[0]["entrega"]);

			$("#modalEditarProducto .disponibilidad").val(respuesta[0]["disponibilidad"]);

			$("#modalEditarProducto .Proveedor").val(respuesta[0]["Proveedor"]);



			/*=============================================

			PREGUNTAMOS SI EXITE OFERTA

			=============================================*/



			if(respuesta[0]["oferta"] != 0){



				$("#modalEditarProducto .selActivarOferta").val("oferta");



				$("#modalEditarProducto .datosOferta").show();

				$("#modalEditarProducto .valorOferta").prop("required",true);



				$("#modalEditarProducto .precioOferta").val(respuesta[0]["precioOferta"]);

				$("#modalEditarProducto .descuentoOferta").val(respuesta[0]["descuentoOferta"]);



				if(respuesta[0]["precioOferta"] != 0){



					$("#modalEditarProducto .precioOferta").prop("readonly",true);

					$("#modalEditarProducto .descuentoOferta").prop("readonly",false);



				}



				if(respuesta[0]["descuentoOferta"] != 0){



					$("#modalEditarProducto .descuentoOferta").prop("readonly",true);

					$("#modalEditarProducto .precioOferta").prop("readonly",false);



				}

	

				$("#modalEditarProducto .previsualizarOferta").attr("src", respuesta[0]["imgOferta"]);



				$("#modalEditarProducto .antiguaFotoOferta").val(respuesta[0]["imgOferta"]);

				

				$("#modalEditarProducto .finOferta").val(respuesta[0]["finOferta"]);						



			}else{



				$("#modalEditarProducto .selActivarOferta").val("");

				$("#modalEditarProducto .datosOferta").hide();

				$("#modalEditarProducto .valorOferta").prop("required",false);

				$("#modalEditarProducto .previsualizarOferta").attr("src", "vistas/img/ofertas/default/default.jpg");

				$("#modalEditarProducto .antiguaFotoOferta").val(respuesta[0]["imgOferta"]);



			}



			/*=============================================

			CREAR NUEVA OFERTA AL EDITAR

			=============================================*/



			$("#modalEditarProducto .selActivarOferta").change(function(){



				activarOferta($(this).val())



			})



			$("#modalEditarProducto .valorOferta").change(function(){



				if($(this).attr("tipo") == "oferta"){



					var descuento = 100-(Number($(this).val())*100/Number($("#modalEditarProducto .precio").val()));



					$("#modalEditarProducto .precioOferta").prop("readonly",true);

					$("#modalEditarProducto .descuentoOferta").prop("readonly",false);

					$("#modalEditarProducto .descuentoOferta").val(Math.ceil(descuento));



				}



				if($(this).attr("tipo") == "descuento"){



					var oferta = Number($("#modalEditarProducto .precio").val())-(Number($(this).val())*Number($("#modalEditarProducto .precio").val())/100);	



					$("#modalEditarProducto .descuentoOferta").prop("readonly",true);

					$("#modalEditarProducto .precioOferta").prop("readonly",false);

					$("#modalEditarProducto .precioOferta").val(oferta);



				}



			})



			/*=============================================

			GUARDAR CAMBIOS DEL PRODUCTO

			=============================================*/	



			var multimediaFisica = null;

			var multimediaVirtual = null;	



			$(".guardarCambiosProducto").click(function(){



					/*=============================================

					PREGUNTAMOS SI LOS CAMPOS OBLIGATORIOS ESTÁN LLENOS

					=============================================*/



					if($("#modalEditarProducto .tituloProducto").val() != "" && 

					   $("#modalEditarProducto .seleccionarTipo").val() != "" && 

					   $("#modalEditarProducto .seleccionarCategoria").val() != "" &&

					   $("#modalEditarProducto .seleccionarSubCategoria").val() != "" &&

					   $("#modalEditarProducto .descripcionProducto").val() != "" &&

					   $("#modalEditarProducto .pClavesProducto").val() != ""){



						/*=============================================

					   	PREGUNTAMOS SI VIENEN IMÁGENES PARA MULTIMEDIA O LINK DE YOUTUBE

					   	=============================================*/



					   	if($("#modalEditarProducto .seleccionarTipo").val() != "virtual"){	



						   	if(arrayFiles.length > 0 && $("#modalEditarProducto .rutaProducto").val() != ""){



						   		var listaMultimedia = [];

						   		var finalFor = 0;



								for(var i = 0; i < arrayFiles.length; i++){

									

									var datosMultimedia = new FormData();

									datosMultimedia.append("file", arrayFiles[i]);

									datosMultimedia.append("ruta", $("#modalEditarProducto .rutaProducto").val());



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

																			

											multimediaVirtual = null;



											if(multimediaFisica == null){



												 swal({

												      title: "El campo de multimedia no debe estar vacío",

												      type: "error",

												      confirmButtonText: "¡Cerrar!"

												    });



												 return;

											}





											if((finalFor + 1) == arrayFiles.length){



												editarMiProducto(multimediaFisica);

												finalFor = 0;



											}



											finalFor++;							

								

										}



									})



								}



							}else{

					

								var jsonLocalStorage = JSON.parse(localStorage.getItem("multimediaFisica"));



								multimediaFisica = JSON.stringify(jsonLocalStorage);



								editarMiProducto(multimediaFisica);												

								

							}



						}else{



							multimediaVirtual = $("#modalEditarProducto .multimedia").val();

							multimediaFisica = null;



							if(multimediaVirtual == null){



					 			 swal({

								      title: "El campo de multimedia no debe estar vacío",

								      type: "error",

								      confirmButtonText: "¡Cerrar!"

								    });



					 			  return;

							}	



							editarMiProducto(multimediaVirtual);	

							

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

					

		}



	})



})



function editarMiProducto(imagen){



	var idProducto = $("#modalEditarProducto .idProducto").val();

	var tituloProducto = $("#modalEditarProducto .tituloProducto").val();

	var rutaProducto = $("#modalEditarProducto .rutaProducto").val();

	var seleccionarTipo = $("#modalEditarProducto .seleccionarTipo").val();

	var seleccionarCategoria = $("#modalEditarProducto .seleccionarCategoria").val();

	var seleccionarSubCategoria = $("#modalEditarProducto .seleccionarSubCategoria").val();

	var empresa = $("#modalEditarProducto .empresa").val();

	var descripcionProducto = $("#modalEditarProducto .descripcionProducto").val();

	var pClavesProducto = $("#modalEditarProducto .pClavesProducto").val();

	var precio = $("#modalEditarProducto .precio").val();

	var peso = $("#modalEditarProducto .peso").val();

	var entrega = $("#modalEditarProducto .entrega").val();

	var disponibilidad = $("#modalEditarProducto .disponibilidad").val();

	var Proveedor = $("#modalEditarProducto .Proveedor").val();

	var selActivarOferta = $("#modalEditarProducto .selActivarOferta").val();

	var precioOferta = $("#modalEditarProducto .precioOferta").val();

	var descuentoOferta = $("#modalEditarProducto .descuentoOferta").val();

	var finOferta = $("#modalEditarProducto .finOferta").val();

	var inversionEditada =$("#modalEditarProducto .inversionEditada").val();

	var medida = $("#modalEditarProducto .medida").val();

  	if(seleccionarTipo == "virtual"){



		var detalles = {"Reparacion": $("#modalEditarProducto .detalleReparacion").val()};

	}else{



		var detalles = {"Color": $("#modalEditarProducto .detalleColor").tagsinput('items')};



	}



	var detallesString = JSON.stringify(detalles);



	

	var antiguaFotoPortada = $("#modalEditarProducto .antiguaFotoPortada").val();

	var antiguaFotoPrincipal = $("#modalEditarProducto .antiguaFotoPrincipal").val();

	var antiguaFotoOferta = $("#modalEditarProducto .antiguaFotoOferta").val();

	var idCabecera = $("#modalEditarProducto .idCabecera").val();

	var codigoEditado = $("#modalEditarProducto .codigoEditado").val();



	var datosProducto = new FormData();

	datosProducto.append("id", idProducto);

	datosProducto.append("editarProducto", tituloProducto);

	datosProducto.append("rutaProducto", rutaProducto);

	datosProducto.append("seleccionarTipo", seleccionarTipo);	

	datosProducto.append("detalles", detallesString);	

	datosProducto.append("seleccionarCategoria", seleccionarCategoria);

	datosProducto.append("seleccionarSubCategoria", seleccionarSubCategoria);

	datosProducto.append("empresa", empresa);

	datosProducto.append("descripcionProducto", descripcionProducto);

	datosProducto.append("pClavesProducto", pClavesProducto);

	datosProducto.append("precio", precio);

	datosProducto.append("peso", peso);

	datosProducto.append("entrega", entrega);

	datosProducto.append("disponibilidad", disponibilidad);

	datosProducto.append("Proveedor", Proveedor);

	datosProducto.append("codigoEditado",codigoEditado);



	if(imagen == null){



		multimediaFisica = localStorage.getItem("multimediaFisica");

		datosProducto.append("multimedia", multimediaFisica);



	}else{



		datosProducto.append("multimedia", imagen);

	}	



	datosProducto.append("fotoPortada", imagenPortada);

	datosProducto.append("fotoPrincipal", imagenFotoPrincipal);

	datosProducto.append("selActivarOferta", selActivarOferta);

	datosProducto.append("precioOferta", precioOferta);

	datosProducto.append("descuentoOferta", descuentoOferta);

	datosProducto.append("finOferta", finOferta);

	datosProducto.append("fotoOferta", imagenOferta);

	datosProducto.append("antiguaFotoPortada", antiguaFotoPortada);

	datosProducto.append("antiguaFotoPrincipal", antiguaFotoPrincipal);

	datosProducto.append("antiguaFotoOferta", antiguaFotoOferta);

	datosProducto.append("idCabecera", idCabecera);

	datosProducto.append("inversionEditada", 	inversionEditada);

	datosProducto.append("medida", medida);


	$.ajax({

			url:"ajax/productos.ajax.php",

			method: "POST",

			data: datosProducto,

			cache: false,

			contentType: false,

			processData: false,

			success: function(respuesta){

									

				

				if(respuesta == "ok"){



					swal({

					  type: "success",

					  title: "El producto ha sido cambiado correctamente",

					  showConfirmButton: true,

					  confirmButtonText: "Cerrar"

					  }).then(function(result){

						if (result.value) {



						localStorage.removeItem("multimediaFisica");

						localStorage.clear();

						window.location = "productos";



						}

					})

				}



			}



	})

	

}





/*=============================================

ELIMINAR PRODUCTO

=============================================*/



$('.tablaProductos tbody').on("click", ".btnEliminarProducto", function(){





  var idProducto = $(this).attr("idProducto");

  var imgOferta = $(this).attr("imgOferta");

  var rutaCabecera = $(this).attr("rutaCabecera");

  var imgPortada = $(this).attr("imgPortada");

  var imgPrincipal = $(this).attr("imgPrincipal");



  swal({

    title: '¿Está seguro de borrar el producto?',

    text: "¡Si no lo está puede cancelar la accíón!",

    type: 'warning',

    showCancelButton: true,

    confirmButtonColor: '#3085d6',

      cancelButtonColor: '#d33',

      cancelButtonText: 'Cancelar',

      confirmButtonText: 'Si, borrar producto!'

  }).then(function(result){



    if(result.value){



      window.location = "index.php?ruta=productos&idProducto="+idProducto+"&imgOferta="+imgOferta+"&rutaCabecera="+rutaCabecera+"&imgPortada="+imgPortada+"&imgPrincipal="+imgPrincipal;



    }



  })



})

/*=============================================

GENERAR CODIGO DE BARRAS

=============================================*/





//función para generar el código de barras

function generarbarcode()

{

	codigo=$("#codigoProducto").val();

	JsBarcode("#barcode", codigo);

	$("#print").show();

}



//Función para imprimir el Código de barras

function imprimir()

{

	$("#print").printArea();

}



/*=============================================

GENERAR CODIGO DE BARRAS PRODUCTOS EDITADOS

=============================================*/

//función para generar el código de barras

function generarbarcodeEditado()

{

	codigoEditado=$("#codigoProductoEditado").val();

	JsBarcode("#barcode", codigoEditado);

	$("#print").show();

}



//Función para imprimir el Código de barras

function imprimirCodigoEditado()

{

	$("#print").printArea();

}




function activarTipo(event){



	if(event == "caja"){



		$(".datosTipo").show();

		$(".cantidadTipo").prop("required",true);




	}else{



		$(".datosTipo").hide();

		$(".cantidadTipo").prop("required",false);


	}

}


$(".selActivarTipo").change(function(){



	activarTipo($(this).val())



})

