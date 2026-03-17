(function(c){"function"===typeof define&&define.amd?define(["jquery","datatables.net-bs","datatables.net-responsive"],function(a){return c(a,window,document)}):"object"===typeof exports?module.exports=function(a,b){a||(a=window);if(!b||!b.fn.dataTable)b=require("datatables.net-bs")(a,b).$;b.fn.dataTable.Responsive||require("datatables.net-responsive")(a,b);return c(b,a,a.document)}:c(jQuery,window,document)})(function(c){var a=c.fn.dataTable,b=a.Responsive.display,g=b.modal,d=c('<div class="modal fade dtr-bs-modal" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"/></div></div></div>');b.modal=function(a){return function(b,e,f){c.fn.modal?e||(a&&a.header&&d.find("div.modal-header").empty().append('<h4 class="modal-title">'+a.header(b)+"</h4>"),d.find("div.modal-body").empty().append(f()),d.appendTo("body").modal()):g(b,e,f)}};return a.Responsive});

$("form :input").attr("autocomplete", "off");

/* =====================================================
   LOADING INDICATOR GLOBAL — barra superior AJAX
   ===================================================== */
$(document).ajaxStart(function () {
  $('#ajax-loading-bar').addClass('active');
});

$(document).ajaxStop(function () {
  $('#ajax-loading-bar').removeClass('active');
});

/* =====================================================
   MANEJO GLOBAL DE ERRORES AJAX
   Muestra alerta SweetAlert en errores de red/servidor.
   Las llamadas con error handlers propios los siguen
   ejecutando sin interferencia.
   ===================================================== */
$(document).ajaxError(function (event, xhr) {
  $('#ajax-loading-bar').removeClass('active');
  /* Ignorar peticiones abortadas (ej. al navegar) */
  if (xhr.status === 0 || xhr.statusText === 'abort') return;
  swal({
    type: 'error',
    title: 'Error de conexión',
    text: 'No se pudo completar la solicitud (HTTP ' + xhr.status + '). Verifica tu conexión e intenta de nuevo.',
    confirmButtonText: 'Cerrar'
  });
});
