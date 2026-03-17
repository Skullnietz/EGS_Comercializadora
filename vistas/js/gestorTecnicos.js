$(".tablaTecnicos").on("click", ".btnEditarDatosTecnico", function(){

	//console.log("Editar");
	var idTecnico = $(this).attr("idTecnico");
	
	var datos = new FormData();
	datos.append("idTecnico", idTecnico);
	//console.log(idUsuario);
	
	$.ajax({


		url:"ajax/Tecnicos.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){ 

		 $("#idTecnico").val(respuesta["id"]);
		 $("#editarNombreTecnico").val(respuesta["nombre"]);
      	 $("#editarEmailTecnico").val(respuesta["correo"]);
      	 $("#editarNumeroUnoTecnico").val(respuesta["telefono"]);
      	 $("#editarTelefonoDosTecnico").val(respuesta["telefonoDos"]);
      	 $("#HoraDeComidaEditada").val(respuesta["HoraDeComida"]);
				
        $(".estado").val(respuesta["estado"]);
        $(".estadoDelTecnico").html(respuesta["estado"]);
    
				console.log("Datos usuario:", respuesta);

		}

	})


})

/*=============================================
ELIMINAR USUARIO
=============================================*/
$(".tablaTecnicos").on("click", ".btnEliminarTecnico", function(){

  var idtecnico = $(this).attr("idtecnico");


  swal({
    title: '¿Está seguro de borrar el Técnico?',
    text: "¡Si no lo está puede cancelar la accíón!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Si, borrar Técnico!'
  }).then(function(result){

    if(result.value){

      window.location = "index.php?ruta=tecnicos&idtecnico="+idtecnico;

    }

  })

})
/*=============================================
ACTIVAR TECNICO
=============================================*/

$(".tablaTecnicos tbody").on("click", ".btnActivarTecnico", function(){

	var idTecnico = $(this).attr("idTecnico");
	var estadoPerfilTecnico = $(this).attr("estadoPerfilTecnico");

	var datos = new FormData();
 	datos.append("activarId", idTecnico);
  	datos.append("activarTecnico", estadoPerfilTecnico);

  	$.ajax({

  		 url:"ajax/tecnico.ajax.php",
  		 method: "POST",
	  	data: datos,
	  	cache: false,
      	contentType: false,
      	processData: false,
      	success: function(respuesta){ 
      	    
      	    console.log("respuesta", respuesta);

      	} 	 

  	});

  	if(estadoPerfilTecnico == 1){

  		$(this).removeClass('btn-success');
  		$(this).addClass('btn-danger');
  		$(this).html('Desactivado');
  		$(this).attr('estadoPerfilTecnico',0);
  	
  	}else{

  		$(this).addClass('btn-success');
  		$(this).removeClass('btn-danger');
  		$(this).html('Activado');
  		$(this).attr('estadoPerfilTecnico',1);

  	}

})
