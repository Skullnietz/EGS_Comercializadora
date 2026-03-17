$(".tablaAsesores").on("click",".btnEditarDatosAsesor",function(){var idAsesor=$(this).attr("idAsesor");var datos=new FormData();datos.append("idAsesor",idAsesor);$.ajax({url:"ajax/Asesores.ajax.php",method:"POST",data:datos,cache:false,contentType:false,processData:false,dataType:"json",success:function(respuesta){$("#idAsesor").val(respuesta["id"]);$("#editarNombreAsesor").val(respuesta["nombre"]);$("#editarEmailAsesor").val(respuesta["correo"]);

	$("#editarNumeroUno").val(respuesta["numerodeCelular"]);
	$("#editarPorcentajeComision").val(respuesta["porcentajeComision"]);
	$("#editarTelefonoDos").val(respuesta["numeroTelefono"]);
//console.log("Datos usuario:",respuesta);
}})})
$(".tablaAsesores").on("click",".btnEliminarAsesor",function(){var idAsesor=$(this).attr("idAsesor");swal({title:'¿Está seguro de borrar el Asesor?',text:"¡Si no lo está puede cancelar la accíón!",type:'warning',showCancelButton:true,confirmButtonColor:'#3085d6',cancelButtonColor:'#d33',cancelButtonText:'Cancelar',confirmButtonText:'Si, borrar Asesor!'}).then(function(result){if(result.value){window.location="index.php?ruta=asesores&idAsesor="+idAsesor;}})})

/*=========================
VALIDAR ENTRADA DE DATOS ASESORES
==========================*/
function validarAsesores() {
		
	var nombre= $("#nuevoNombreAsesor").val();
	var correo= $("#nuevoEmailAsesor").val();
	var telefonoUno= $("#nuevoNumeroUno").val();
	var telefonoDos= $("#nuevoNumeroDos").val();

	/*======================================
	validacion nombre
	======================================*/
	if(nombre == ""){

		$("#nuevoNombreAsesor").before('<h6 class="alert alert-danger">Escriba porfavor el nombre</h6>');

		return false;
	}else{

		var expresion =  /^[a-zA-ZñÑáéíóúÁÉÍÓÚ ]*$/;

		if(!expresion.test(nombre)){

			$("#nuevoNombreAsesor").before('<h6 class="alert alert-danger">Escriba por favor solo letras sin caracteres especiales</h6>');

			return false;
		}
	}

	/*======================================
	validacion correo
	======================================*/
	if(correo == ""){
		
		$("#nuevoEmailAsesor").before('<h6 class="alert alert-danger">Escriba por favor el email</h6>');

		return false;

	}else{

		var expresion =  /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;

		if(!expresion.test(email)){

			$("#nuevoEmailAsesor").before('<h6 class="alert alert-danger">Escriba por favor correctamente el correo electronico</h6>');

			return false;
		}
	}

	/*======================================
	validacion telefonos
	======================================*/
	if(telefonoUno == ""){
		
		$("#nuevoNumeroUno").before('<h6 class="alert alert-danger">Escriba por favor el telefono</h6>');

		return false;

	}else{

		var expresion =  /[0-9]{3}-[0-9]{3}-[0-9]{4}/;

		if(!expresion.test(email)){

			$("#nuevoNumeroUno").before('<h6 class="alert alert-danger">Escriba por favor correctamente el correo telefono</h6>');

			return false;
		}
	}

	return true;
}