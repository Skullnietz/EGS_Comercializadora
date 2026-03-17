/*=============================================
CARGAR LA TABLA DINÁMICA DE USUARIOS
=============================================*/
var Empresa_del_perfil = $("#Empresa_del_perfil").val();

 $.ajax({

 	url:"ajax/tablaClientes.ajax.php?empresa="+$("#Empresa_del_perfil").val(),
 	success:function(respuesta){
		
 		//console.log("respuesta", respuesta);

 	}

 })

$(".tablaClientesOrden").DataTable({
	 "ajax": "ajax/tablaClientes.ajax.php?empresa="+$("#Empresa_del_perfil").val(),
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

$(".tablaClientesOrden").on("click", ".btnEditarCliente", function(){

	//console.log("Editar");
	var idCliente = $(this).attr("idCliente");
	
	var datos = new FormData();
	datos.append("idCliente", idCliente);
	//console.log(idUsuario);
	
	$.ajax({


		url:"ajax/clientes.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){ 

			$("#idCliente").val(respuesta["id"]);
			$("#editarNombreDelCliente").val(respuesta["nombre"]);
      		$("#EditarCorreoCliente").val(respuesta["correo"]);
      		$("#EditarNumeroDelCliente").val(respuesta["telefono"]);
      		$("#EditarSegundoNumeroDeTel").val(respuesta["telefonoDos"]);
      		$("#EditarAsesorDelCliente").val(respuesta["id_Asesor"]);
			$("#modalAgregarUsuario .EditarEtiquetaDelCLiente").val(respuesta["etiqueta"]);
			$("#modalAgregarUsuario .MostrarEtiquetaDelCliente").html(respuesta["etiqueta"]);
		
			console.log("cliente:", respuesta);

		}

	})


})

/*VALIDAR NO REPETIR CLIENTES*/

$(".nombreCliente").change(function(){

	$(".alert").remove();

	var nombre = $(this).val();

	var datos = new FormData();
	datos.append("nombreCliente", nombre);

	$.ajax({
	    url:"ajax/clientes.ajax.php",
	    method:"POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
	    success:function(respuesta){

    		if(respuesta.length != 0){

    			$(".nombreCliente").parent().after('<div class="alert alert-warning">El clienten ya existe en la base de datos</div>');

	    		$(".nombreCliente").val("");

    		}

	    }

   	})

})
