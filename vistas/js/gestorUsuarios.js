/*=============================================
CARGAR LA TABLA DINÁMICA DE USUARIOS
=============================================*/
var Empresa_del_perfil = $("#Empresa_del_perfil").val();
 $.ajax({

 	url:"ajax/tablaUsuarios.ajax.php?empresa="+$("#Empresa_del_perfil").val(),
 	success:function(respuesta){
		var url = "ajax/tablaUsuarios.ajax.php?empresa="+$("#Empresa_del_perfil").val();
 		//console.log("respuesta", respuesta);
 		//console.log("url", url);
 	}

 })

$(".tablaUsuarios").DataTable({
	 "ajax": "ajax/tablaUsuarios.ajax.php?empresa="+$("#Empresa_del_perfil").val(),
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
ACTIVAR USUARIO
=============================================*/

$(".tablaUsuarios tbody").on("click", ".btnActivar", function(){

	var idUsuario = $(this).attr("idUsuario");
	var estadoUsuario = $(this).attr("estadoUsuario");

	var datos = new FormData();
 	datos.append("activarId", idUsuario);
  	datos.append("activarUsuario", estadoUsuario);

  	$.ajax({

  		 url:"ajax/usuarios.ajax.php",
  		 method: "POST",
	  	data: datos,
	  	cache: false,
      	contentType: false,
      	processData: false,
      	success: function(respuesta){ 
      	    
      	    // console.log("respuesta", respuesta);

      	} 	 

  	});

  	if(estadoUsuario == 1){

  		$(this).removeClass('btn-success');
  		$(this).addClass('btn-danger');
  		$(this).html('Desactivado');
  		$(this).attr('estadoUsuario',0);
  	
  	}else{

  		$(this).addClass('btn-success');
  		$(this).removeClass('btn-danger');
  		$(this).html('Activado');
  		$(this).attr('estadoUsuario',1);

  	}

})

/*=============================================
EDITAR ASESOR
=============================================*/
$(".tablaUsuarios").on("click", ".btnEditarUsuario", function(){

	//console.log("Editar");
	var idUsuario = $(this).attr("idUsuario");
	
	var datos = new FormData();
	datos.append("idUsuario", idUsuario);
	//console.log(idUsuario);
	
	$.ajax({


		url:"ajax/usuarios.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){ 

		 $("#editarNombre").val(respuesta["nombre"]);
		 $("#editarEmail").val(respuesta["correo"]);
		 $("#editarAsesor").html(respuesta["asesor"]);
      		 $("#editarAsesor").val(respuesta["asesor"]);
      	 	 $("#idUsuario").val(respuesta["id"]);
      	 	 $("#Modo").val(respuesta["modo"]);
      	 	 $("#Modo").val(respuesta["modo"]);
				
				//console.log("Datos usuario:", respuesta);

		}

	})


})