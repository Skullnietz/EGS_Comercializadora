$(".tablaEmpresas").on("click", ".btnEditarEmpresa", function(){

	//console.log("Editar");
	var idEmpresa = $(this).attr("idEmpresa");
	
	var datos = new FormData();
	datos.append("idEmpresa", idEmpresa);
	//console.log(idEmpresa);
	
	$.ajax({


		url:"ajax/empresas.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){ 

		 $("#idEmpresa").val(respuesta["id"]);
		 $("#editarNombreEmpresa").val(respuesta["empresa"]);
      	 $("#editarCorreoEmpresa").val(respuesta["correo"]);
      	 $("#editarNumeroUnoDeEmpresa").val(respuesta["telefono"]);
      	 $("#telefonoDosDeEmpresaEditado").val(respuesta["telefonoDos"]);
      	 $("#EditarDireccion").val(respuesta["direccion"]);
      	 $("#HoraEditada").val(respuesta["Horario"]);
      	 $("#Facebook").val(respuesta["Facebook"]);
      	 $("#Sitio").val(respuesta["Sitio"]);
				
				//console.log("Datos usuario:", respuesta);

		}

	})


})

/*=============================================
ELIMINAR USUARIO
=============================================*/
$(".tablaEmpresas").on("click", ".btnEliminarEmpresa", function(){

  var idEmpresa = $(this).attr("idEmpresa");


  swal({
    title: '¿Está seguro de borrar la empresa?',
    text: "¡Si no lo está puede cancelar la accíón!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Si, borrar Empresa!'
  }).then(function(result){

    if(result.value){

      window.location = "index.php?ruta=empresas&idEmpresa="+idEmpresa;

    }

  })

})

/*=============================================
VALIDAR INPUTS
=============================================*/
function validarEmpresa() {
	
	var nombre= $("#empresa").val();
	var correo= $("#correo").val();
	var telefonoDeEmpresa= $("#telefonoDeEmpresa").val();
	var telefonoDosDeEmpresa= $("#telefonoDosDeEmpresa").val();
	var direccion= $("#direccion").val();
	var Horario= $("#Horario").val();
	var Facebook= $("#Facebook").val();
	var Sitio= $("#Sitio").val();

	/*======================================	
	validacion nombre
	======================================*/
	if(nombre == ""){

		$("#empresa").before('<h6 class="alert alert-danger">Escriba porfavor el nombre de la empresa</h6>');

		return false;
	}else{

		var expresion =  /^[a-zA-ZñÑáéíóúÁÉÍÓÚ ]*$/;

		if(!expresion.test(nombre)){

			$("#empresa").before('<h6 class="alert alert-danger">Escriba por favor solo letras sin caracteres especiales</h6>');

			return false;
		}
	}

	/*======================================
	validacion correo
	======================================*/
	if(correo == ""){
		
		$("#correo").before('<h6 class="alert alert-danger">Escriba por favor el correo electronico</h6>');

		return false;

	}else{

		var expresion =  /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;

		if(!expresion.test(email)){

			$("#correo").before('<h6 class="alert alert-danger">Escriba por favor correctamente el correo electronico</h6>');

			return false;
		}
	}

	/*======================================
	validacion telefonos
	======================================*/
	if(telefonoDeEmpresa == ""){
		
		$("#telefonoDeEmpresa").before('<h6 class="alert alert-danger">Escriba por favor el telefono</h6>');

		return false;

	}else{

		var expresion =  /[0-9]{3}-[0-9]{3}-[0-9]{4}/;

		if(!expresion.test(email)){

			$("#telefonoDeEmpresa").before('<h6 class="alert alert-danger">Escriba por favor correctamente el correo telefono</h6>');

			return false;
		}
	}
	if(telefonoDosDeEmpresa == ""){
		
		$("#telefonoDosDeEmpresa").before('<h6 class="alert alert-danger">Escriba por favor el telefono</h6>');

		return false;

	}else{

		var expresion =  /[0-9]{3}-[0-9]{3}-[0-9]{4}/;

		if(!expresion.test(email)){

			$("#telefonoDosDeEmpresa").before('<h6 class="alert alert-danger">Escriba por favor correctamente el telefono telefono</h6>');

			return false;
		}
	}

	/*======================================
	validacion de direccion
	======================================*/
	if(direccion == ""){

		$("#direccion").before('<h6 class="alert alert-danger">Escriba porfavor la direccion</h6>');

		return false;
	}else{

		var expresion =  /^([a-z]+[0-9]+)|([0-9]+[a-z]+)/i;

		if(!expresion.test(direccion)){

			$("#direccion").before('<h6 class="alert alert-danger">Escriba por favor solo letras sin caracteres especiales</h6>');

			return false;
		}
	}
	/*======================================
	validacion de horario
	======================================*/
	if(Horario == ""){

		$("#Horario").before('<h6 class="alert alert-danger">Escriba porfavor el horario de comida</h6>');

		return false;
	}else{

		var expresion =  /^([a-z]+[0-9]+)|([0-9]+[a-z]+)/i;

		if(!expresion.test(Horario)){

			$("#Horario").before('<h6 class="alert alert-danger">Escriba por favor solo letras sin caracteres especiales</h6>');

			return false;
		}
	}
	/*======================================
	validacion de url facebok
	======================================*/
	if(Facebook == ""){

		$("#Facebook").before('<h6 class="alert alert-danger">Escriba porfavor el url nombre</h6>');

		return false;
	}else{

		var expresion = /^(ht|f)tps?:\/\/\w+([\.\-\w]+)?\.([a-z]{2,6})?([\.\-\w\/_]+)$/i;

		if(!expresion.test(Facebook)){

			$("#Facebook").before('<h6 class="alert alert-danger">Escriba por favorun url sin caracteres especiales</h6>');

			return false;
		}
	}
	/*======================================
	validacion de url sitio web
	======================================*/
	if(Sitio == ""){

		$("#Sitio").before('<h6 class="alert alert-danger">Escriba porfavor el url del sitio web</h6>');

		return false;
	}else{

		var expresion = /^(ht|f)tps?:\/\/\w+([\.\-\w]+)?\.([a-z]{2,6})?([\.\-\w\/_]+)$/i;

		if(!expresion.test(Sitio)){

			$("#Sitio").before('<h6 class="alert alert-danger">Escriba por favorun url sin caracteres especiales</h6>');

			return false;
		}
	}
	return true;
}