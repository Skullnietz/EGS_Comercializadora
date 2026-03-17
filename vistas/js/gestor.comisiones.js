
/*=============================================
VARIABLE LOCAL STORAGE
=============================================*/
if(localStorage.getItem("btnComisiones") != null){

	$("#daterange-btnComisiones span").html(localStorage.getItem("btnComisiones"));


}else{

	$("#daterange-btnComisiones span").html('<i class="fa fa-calendar"></i> Rango de fecha')

}
/*=============================================
RANGO DE FECHAS
=============================================*/
$('#daterange-btnComisiones').daterangepicker(
  {
    ranges   : {
      'Hoy'       : [moment(), moment()],
      'Ayer'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Últimos 7 días' : [moment().subtract(6, 'days'), moment()],
      'Este mes'  : [moment().startOf('month'), moment().endOf('month')],
      'Último mes'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    startDate: moment(),
    endDate  : moment()
  },
  function (start, end) {
    $('#daterange-btnComisiones span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

    var fechaInicial = start.format('YYYY-MM-DD');

    var fechaFinal = end.format('YYYY-MM-DD');

    var btnComisiones = $("#daterange-btnComisiones span").html();
   
   	localStorage.setItem("btnComisiones", btnComisiones);

   	window.location = "index.php?ruta=comisiones&fechaInicial="+fechaInicial+"&fechaFinal="+fechaFinal;

  }

)

/*=============================================
CANCELAR RANGO DE FECHAS
=============================================*/

$(".daterangepicker.opensright.range_inputs.cancelBtn").on("click", function(){

	localStorage.removeItem("btnComisiones");
	localStorage.clear();
	window.location = "comisiones";
})

/*=============================================
CAPTURAR HOY
=============================================*/

$(".daterangepicker.opensright .ranges li").on("click", function(){

	var textoHoy = $(this).attr("data-range-key");

	if(textoHoy == "Hoy"){

    var d = new Date();
    
    var dia = d.getDate();
    var mes = d.getMonth()+1;
    var año = d.getFullYear();

    if(mes < 10){

      var fechaInicial = año+"-0"+mes+"-"+dia;
      var fechaFinal = año+"-0"+mes+"-"+dia;

    }else if(dia < 10){

      var fechaInicial = año+"-"+mes+"-0"+dia;
      var fechaFinal = año+"-"+mes+"-0"+dia;

    }else if(mes < 10 && dia < 10){

      var fechaInicial = año+"-0"+mes+"-0"+dia;
      var fechaFinal = año+"-0"+mes+"-0"+dia;

    }else{

      var fechaInicial = año+"-"+mes+"-"+dia;
        var fechaFinal = año+"-"+mes+"-"+dia;

    } 

    	localStorage.setItem("btnComisiones", "Hoy");

    	window.location = "index.php?ruta=comisiones&fechaInicial="+fechaInicial+"&fechaFinal="+fechaFinal;

	}

})