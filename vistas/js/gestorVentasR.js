/*=============================================
CARGAR LA TABLA DINÁMICA DE VENTAS
=============================================*/
var tipoDePerfil = $("#tipoDePerfil").val();

function getVentasRDateFilters(){
  return {
    fechaInicial: $.trim($("#filtroFechaInicialR").val()),
    fechaFinal: $.trim($("#filtroFechaFinalR").val())
  };
}

function buildVentasRAjaxUrl(){
  var perfil = $("#tipoDePerfil").val();
  var empresa = $("#id_empresa").val();
  var filtros = getVentasRDateFilters();

  var url = "ajax/tablaVentasR.ajax.php?perfil=" + encodeURIComponent(perfil) + "&empresa=" + encodeURIComponent(empresa);

  if(filtros.fechaInicial !== ""){
    url += "&fechaInicial=" + encodeURIComponent(filtros.fechaInicial);
  }

  if(filtros.fechaFinal !== ""){
    url += "&fechaFinal=" + encodeURIComponent(filtros.fechaFinal);
  }

  return url;
}

function updateExcelVentasRLink(){
  var empresa = $("#id_empresa").val();
  var filtros = getVentasRDateFilters();
  var href = "vistas/modulos/reporte.ventasR.php?reporte=compras&empresa=" + encodeURIComponent(empresa);

  if(filtros.fechaInicial !== ""){
    href += "&fechaInicial=" + encodeURIComponent(filtros.fechaInicial);
  }

  if(filtros.fechaFinal !== ""){
    href += "&fechaFinal=" + encodeURIComponent(filtros.fechaFinal);
  }

  $("#btnDescargarExcelVentasR").attr("href", href);
}

function parseCurrencyToFloat(value){
  if(value === null || value === undefined){
    return 0;
  }

  var clean = String(value).replace(/[^0-9.-]/g, "");
  var parsed = parseFloat(clean);

  return isNaN(parsed) ? 0 : parsed;
}

function formatCompactNumber(value){
  var absValue = Math.abs(value);

  if(absValue >= 1000000){
    return (value / 1000000).toFixed(2).replace(/\.00$/, "") + " M";
  }

  if(absValue >= 1000){
    return (value / 1000).toFixed(2).replace(/\.00$/, "") + " K";
  }

  return Number(value).toLocaleString("es-MX", { maximumFractionDigits: 2 });
}

function formatCurrencyCompact(value){
  var absValue = Math.abs(value);

  if(absValue >= 1000000){
    return "$" + (value / 1000000).toFixed(2).replace(/\.00$/, "") + " M";
  }

  if(absValue >= 1000){
    return "$" + (value / 1000).toFixed(2).replace(/\.00$/, "") + " K";
  }

  return "$" + Number(value).toLocaleString("es-MX", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatCurrencyFull(value){
  return "$" + Number(value).toLocaleString("es-MX", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function renderVentasRKpis(tableApi){
  if(!tableApi || !$("#kpiVentasTotal").length){
    return;
  }

  var rows = tableApi.rows({ search: "applied" }).data();
  var totalVentas = rows.length;
  var totalIngreso = 0;
  var totalProductos = 0;

  for(var i = 0; i < rows.length; i++){
    totalProductos += parseCurrencyToFloat(rows[i][4]);
    totalIngreso += parseCurrencyToFloat(rows[i][5]);
  }

  var ticketPromedio = totalVentas > 0 ? (totalIngreso / totalVentas) : 0;

  $("#kpiVentasTotal").text(formatCompactNumber(totalVentas)).attr("title", Number(totalVentas).toLocaleString("es-MX"));
  $("#kpiIngresoTotal").text(formatCurrencyCompact(totalIngreso)).attr("title", formatCurrencyFull(totalIngreso));
  $("#kpiTicketPromedio").text(formatCurrencyCompact(ticketPromedio)).attr("title", formatCurrencyFull(ticketPromedio));
  $("#kpiProductosTotal").text(formatCompactNumber(totalProductos)).attr("title", Number(totalProductos).toLocaleString("es-MX", { maximumFractionDigits: 2 }));
}

var tablaVentasRapidas = $(".tablaVentasRapidas").DataTable({
  "ajax": buildVentasRAjaxUrl(),
    "deferRender": true,
    "retrieve": true,
    "processing": true,
  "drawCallback": function(){
    renderVentasRKpis(this.api());
  },
    "language": {
        "sProcessing": "Procesando...",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sZeroRecords": "No se encontraron resultados",
        "sEmptyTable": "Ningún dato disponible en esta tabla",
        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0",
        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix": "",
        "sSearch": "Buscar:",
        "sUrl": "",
        "sInfoThousands": ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate": {
            "sFirst": "Primero",
            "sLast": "Último",
            "sNext": "Siguiente",
            "sPrevious": "Anterior"
        },
        "oAria": {
            "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
    }
});

    renderVentasRKpis(tablaVentasRapidas);
  updateExcelVentasRLink();

$("#btnAplicarFiltroFechaR").on("click", function(){
  tablaVentasRapidas.ajax.url(buildVentasRAjaxUrl()).load();
  updateExcelVentasRLink();
});

$("#btnLimpiarFiltroFechaR").on("click", function(){
  $("#filtroFechaInicialR").val("");
  $("#filtroFechaFinalR").val("");
  tablaVentasRapidas.ajax.url(buildVentasRAjaxUrl()).load();
  updateExcelVentasRLink();
});
/*=============================================
MANDAR EL TIPO DE USUARIO AL AJAX
=============================================

var tipoDePerfil = $("#tipoDePerfil").val();
$.ajax({
"ajax":"ajax/tablaVentasR.ajax.php.ajax.php?perfil="+$("#tipoDePerfil").val(),
})*/
/*=============================================
IMPRIMIR COMPRVANTE DE VENTA EN PDF
=============================================*/
$(".tablaVentasRapidas").on("click", ".btnImprimirComprovanteDeVentaR", function(){
		//console.log("Editar");
	var idventa = $(this).attr("idventa");
	var empresa = $(this).attr("empresa");
	
	var datos = new FormData();
	datos.append("idventa", idventa);
	datos.append("empresa", empresa);
	//console.log(empresa);
	
	$.ajax({


		url:"ajax/ventas.ajax.php",
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
      	 $("#idventa").val(respuesta["id"]);
				
				//console.log("Datos usuario:", respuesta);

		}

	})
	window.open("https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/ticketR.php/?idventa="+idventa+"&empresa="+empresa+"", "_blank");

	sumarTotalPrecios()

})

/*=============================================
AGREGAR DATOS CLIENTE
=============================================*/

var tipo = null;

$(".seleccionarDatos").change(function(){

	tipo = $(this).val();

	if(tipo == "venta"){

		$(".cantidadProductos").show();
		$(".pagoTotal").show();
		$(".Agregar").show();
		$(".Resultado").show();
		$(".nombreCliente").hide();
		$(".numeroClienteUno").hide();
		$(".numeroClienteDos").hide();
		$(".correo").hide();
		$(".direccion").hide();
	
	}else{

		$(".cantidadProductos").show();
		$(".pagoTotal").show();
		$(".Agregar").show();
		$(".Resultado").show();
		$(".nombreCliente").show();
		$(".numeroClienteUno").show();
		$(".numeroClienteDos").show();
		$(".correo").show();
		$(".direccion").show();

	}
})

/*=============================================
ELIMINAR VENTA
=============================================*/
$(".tablaVentasRapidas").on("click", ".btnEliminarVenta", function(){

  var idventa = $(this).attr("idventa");


  swal({
    title: '¿Está seguro de borrar la venta?',
    text: "¡Si no lo está puede cancelar la accíón!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Si, borrar venta!'
  }).then(function(result){

    if(result.value){

      window.location = "index.php?ruta=ventasR&idventa="+idventa;

    }

  })

})


/*=============================================
NO DEJAR ELIMINAR VENTA
=============================================*/
$(".tablaVentasRapidas").on("click", ".btnNoEliminarVenta", function(){


  swal({
    title: 'Solicita Permiso Al Administrador Para Eliminar una venta!',
    text: "¡Por Favor Preciones OK o Cancelar!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
	  }).then(function(result){

    if(result.value){

      window.location = "index.php?ruta=ventasR";

    }

  })

})

