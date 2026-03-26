<?php
if ($_SESSION["perfil"] != "administrador" && $_SESSION["perfil"] != "Super-Administrador") {
  echo '<script>window.location = "inicio";</script>';
  return;
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Configuracion WhatsApp</h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>
      <li class="active">Config WhatsApp</li>
    </ol>
  </section>

  <section class="content">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Meta Cloud API</h3>
      </div>
      <div class="box-body">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label>
                <input type="checkbox" id="waEnabled"> Habilitar integracion
              </label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Version API</label>
              <input type="text" class="form-control" id="waMetaApiVersion" placeholder="v20.0">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Phone Number ID</label>
              <input type="text" class="form-control" id="waMetaPhoneNumberId" placeholder="123456789012345">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Prefijo Pais</label>
              <input type="text" class="form-control" id="waMetaCountryCode" placeholder="52">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-9">
            <div class="form-group">
              <label>Access Token (Permanent Token recomendado)</label>
              <input type="password" class="form-control" id="waMetaAccessToken" placeholder="EAAG...">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Timeout (seg)</label>
              <input type="number" min="1" class="form-control" id="waTimeout" value="5">
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title">Plantillas por Estado</h3>
        <small class="text-muted" style="margin-left:10px;">Variables: {orden}, {estado_anterior}, {estado_nuevo}, {cliente}, {empresa}</small>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="waTemplatesTable">
            <thead>
              <tr>
                <th style="width:190px;">Estado</th>
                <th>Mensaje</th>
                <th style="width:140px;">Multimedia</th>
                <th style="width:280px;">URL Multimedia</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>

      <div class="box-footer">
        <button type="button" class="btn btn-default" id="waReloadBtn"><i class="fas fa-sync"></i> Recargar</button>
        <button type="button" class="btn btn-primary pull-right" id="waSaveBtn"><i class="fas fa-save"></i> Guardar configuracion</button>
      </div>
    </div>
  </section>
</div>

<script>
(function(){
  var knownStates = [
    'En revision (REV)',
    'Pendiente de autorizacion (AUT)',
    'Aceptado (ok)',
    'Supervision (SUP)',
    'Terminada (ter)',
    'Entregado (Ent)',
    'Cancelada (can)',
    'Sin reparacion (SR)',
    'Producto para venta',
    'En revision probable garantia',
    '_default'
  ];

  function mediaSelect(value){
    var v = value || 'none';
    var opts = [
      ['none','Sin multimedia'],
      ['image','Imagen'],
      ['video','Video'],
      ['audio','Audio'],
      ['document','Documento']
    ];
    var html = '<select class="form-control input-sm wa-media-type">';
    for(var i=0;i<opts.length;i++){
      var sel = (opts[i][0]===v)?' selected':'';
      html += '<option value="'+opts[i][0]+'"'+sel+'>'+opts[i][1]+'</option>';
    }
    html += '</select>';
    return html;
  }

  function renderRows(templates){
    var $tb = $('#waTemplatesTable tbody');
    $tb.html('');

    knownStates.forEach(function(st){
      var tpl = templates[st] || { message:'', media_type:'none', media_url:'' };
      var label = (st === '_default') ? 'Default (fallback)' : st;
      var row = ''+
        '<tr data-estado="'+$('<div>').text(st).html()+'">'+
          '<td><strong>'+ $('<div>').text(label).html() +'</strong></td>'+
          '<td><textarea class="form-control input-sm wa-message" rows="2" placeholder="Mensaje para este estado">'+ $('<div>').text(tpl.message||'').html() +'</textarea></td>'+
          '<td>'+ mediaSelect(tpl.media_type) +'</td>'+
          '<td><input type="text" class="form-control input-sm wa-media-url" placeholder="https://..." value="'+ $('<div>').text(tpl.media_url||'').html() +'"></td>'+
        '</tr>';
      $tb.append(row);
    });
  }

  function loadConfig(){
    $.post('ajax/whatsapp.ajax.php', { accion:'obtenerConfig' }, function(resp){
      if(!resp || !resp.ok){
        Swal.fire('Error','No se pudo cargar configuracion','error');
        return;
      }

      var cfg = resp.config || {};
      var meta = cfg.meta || {};
      $('#waEnabled').prop('checked', !!cfg.enabled);
      $('#waMetaApiVersion').val(meta.api_version || 'v20.0');
      $('#waMetaPhoneNumberId').val(meta.phone_number_id || '');
      $('#waMetaAccessToken').val(meta.access_token || '');
      $('#waMetaCountryCode').val(meta.default_country_code || '52');
      $('#waTimeout').val(cfg.timeout || 5);
      renderRows(cfg.templates || {});
    }, 'json').fail(function(){
      Swal.fire('Error','Fallo de comunicacion al cargar','error');
    });
  }

  function collectConfig(){
    var templates = {};
    $('#waTemplatesTable tbody tr').each(function(){
      var $tr = $(this);
      var estado = $tr.attr('data-estado');
      templates[estado] = {
        message: $tr.find('.wa-message').val() || '',
        media_type: $tr.find('.wa-media-type').val() || 'none',
        media_url: $tr.find('.wa-media-url').val() || ''
      };
    });

    return {
      enabled: $('#waEnabled').is(':checked'),
      provider: 'meta',
      meta: {
        api_version: $('#waMetaApiVersion').val() || 'v20.0',
        phone_number_id: $('#waMetaPhoneNumberId').val() || '',
        access_token: $('#waMetaAccessToken').val() || '',
        default_country_code: $('#waMetaCountryCode').val() || '52'
      },
      timeout: parseInt($('#waTimeout').val() || '5', 10),
      templates: templates
    };
  }

  function saveConfig(){
    var cfg = collectConfig();

    $.post('ajax/whatsapp.ajax.php', {
      accion:'guardarConfig',
      config: JSON.stringify(cfg)
    }, function(resp){
      if(resp && resp.ok){
        Swal.fire('Guardado','Configuracion actualizada','success');
        return;
      }
      Swal.fire('Error', (resp && resp.error) ? resp.error : 'No se pudo guardar', 'error');
    }, 'json').fail(function(){
      Swal.fire('Error','Fallo de comunicacion al guardar','error');
    });
  }

  $('#waReloadBtn').on('click', loadConfig);
  $('#waSaveBtn').on('click', saveConfig);
  loadConfig();
})();
</script>
