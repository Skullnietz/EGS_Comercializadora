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
   REGISTRO GLOBAL DE ERRORES AJAX
   No interrumpe la interfaz con alertas globales; cada
   módulo conserva su propio manejo visual cuando aplica.
   ===================================================== */
$(document).ajaxError(function (event, xhr, settings, error) {
  $('#ajax-loading-bar').removeClass('active');
  /* Ignorar peticiones abortadas (ej. al navegar) */
  if (xhr.status === 0 || xhr.statusText === 'abort') return;
  if (window.console && console.warn) {
    console.warn('[AJAX]', xhr.status, settings && settings.url, error || xhr.statusText);
  }
});
