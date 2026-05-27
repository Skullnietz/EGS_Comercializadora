(function ($) {
  'use strict';

  function initVisitasTabs() {
    var $root = $('.visitas-page');
    if (!$root.length) return;

    function activate(tabId) {
      if (!tabId) return;
      var pane = document.getElementById('visitas-pane-' + tabId);
      if (!pane) return;

      $root.find('.visitas-tab').removeClass('active');
      $root.find('.visitas-tab-pane').removeClass('active');

      $root.find('.visitas-tab[data-visitas-tab="' + tabId + '"]').addClass('active');
      pane.classList.add('active');

      if (history.replaceState) {
        history.replaceState(null, '', '#visitas-' + tabId);
      } else {
        window.location.hash = 'visitas-' + tabId;
      }

      pane.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    $root.on('click', '.visitas-tab', function (e) {
      e.preventDefault();
      activate($(this).attr('data-visitas-tab'));
    });

    $root.on('click', 'a.visitas-tab-link', function (e) {
      var tab = $(this).attr('data-visitas-tab');
      if (!tab) return;
      e.preventDefault();
      activate(tab);
    });

    $(window).on('hashchange.visitasTabs', function () {
      var hash = (window.location.hash || '').replace(/^#/, '');
      if (hash.indexOf('visitas-') === 0) {
        activate(hash.replace('visitas-', ''));
      }
    });

    var hash = (window.location.hash || '').replace(/^#/, '');
    if (hash.indexOf('visitas-') === 0) {
      activate(hash.replace('visitas-', ''));
    } else if (hash === 'tab-config-analytics' || hash === 'tab-config') {
      activate('config');
    }
  }

  function initVisitasCharts() {
    if (typeof Chart === 'undefined' || !window._visitasChartData) return;

    var d = window._visitasChartData;

    var ctxT = document.getElementById('chartVisitasTendencia');
    if (ctxT && d.tendencia.labels.length) {
      new Chart(ctxT.getContext('2d'), {
        type: 'line',
        data: {
          labels: d.tendencia.labels,
          datasets: [{
            label: 'Visitas',
            data: d.tendencia.data,
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,.12)',
            borderWidth: 2,
            fill: true,
            tension: 0.3,
            pointRadius: 3
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: { display: false },
          scales: {
            xAxes: [{ gridLines: { display: false } }],
            yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }]
          }
        }
      });
    }

    var ctxP = document.getElementById('chartVisitasPaises');
    if (ctxP && d.paises.labels.length) {
      new Chart(ctxP.getContext('2d'), {
        type: 'doughnut',
        data: {
          labels: d.paises.labels,
          datasets: [{
            data: d.paises.data,
            backgroundColor: ['#6366f1', '#3b82f6', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899']
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: { position: 'bottom', labels: { boxWidth: 10, fontSize: 10 } }
        }
      });
    }
  }

  function initTablaVisitas() {
    if (!$('.tablaVisitas').length) return;
    if ($.fn.DataTable && $.fn.DataTable.isDataTable('.tablaVisitas')) {
      $('.tablaVisitas').DataTable().destroy();
    }
    $('.tablaVisitas').DataTable({
      ajax: 'ajax/tablaVisitas.ajax.php',
      deferRender: true,
      retrieve: true,
      processing: true,
      order: [[4, 'desc']],
      language: {
        sProcessing: 'Procesando...',
        sLengthMenu: 'Mostrar _MENU_ registros',
        sZeroRecords: 'No se encontraron resultados',
        sEmptyTable: 'Ningún dato disponible en esta tabla',
        sInfo: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_',
        sInfoEmpty: 'Mostrando registros del 0 al 0 de un total de 0',
        sInfoFiltered: '(filtrado de un total de _MAX_ registros)',
        sSearch: 'Buscar:',
        sLoadingRecords: 'Cargando...',
        oPaginate: {
          sFirst: 'Primero',
          sLast: 'Último',
          sNext: 'Siguiente',
          sPrevious: 'Anterior'
        }
      }
    });
  }

  $(function () {
    if (!$('.visitas-page').length) return;
    initVisitasTabs();
    initVisitasCharts();
    initTablaVisitas();
  });
})(jQuery);
