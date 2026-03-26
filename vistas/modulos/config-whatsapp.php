<?php
if ($_SESSION["perfil"] != "administrador" && $_SESSION["perfil"] != "Super-Administrador") {
  echo '<script>window.location = "inicio";</script>';
  return;
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Configuración WhatsApp <small>Meta Cloud API</small></h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fas fa-home"></i> Inicio</a></li>
      <li><a href="inicio"><i class="fas fa-cog"></i> Configuración</a></li>
      <li class="active">WhatsApp</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fab fa-whatsapp" style="color:#25d366;"></i> Credenciales de Meta Cloud API</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Contraer">
                <i class="fa fa-minus"></i>
              </button>
            </div>
          </div>
      <div class="box-body">
        <div class="alert alert-info alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          <h4><i class="icon fa fa-info"></i> Información</h4>
          Configura tus credenciales de Meta Cloud API para enviar mensajes automáticos a clientes cuando cambien los estados de sus órdenes.
        </div>

        <div class="form-group">
          <div class="checkbox">
            <label>
              <input type="checkbox" id="waEnabled"> <strong>Habilitar integración WhatsApp</strong>
            </label>
          </div>
        </div>

        <hr style="margin:15px 0;">

        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label><i class="fas fa-code"></i> Versión API</label>
              <input type="text" class="form-control" id="waMetaApiVersion" placeholder="v20.0">
              <small class="text-muted">Ej: v20.0, v19.0</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><i class="fas fa-phone"></i> Phone Number ID</label>
              <input type="text" class="form-control" id="waMetaPhoneNumberId" placeholder="123456789012345">
              <small class="text-muted">ID de tu número WhatsApp Business</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><i class="fas fa-globe"></i> Prefijo País</label>
              <input type="text" class="form-control" id="waMetaCountryCode" placeholder="52">
              <small class="text-muted">Prefijo país (ej: 52 = México)</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><i class="fas fa-hourglass"></i> Timeout (segundos)</label>
              <input type="number" min="1" max="60" class="form-control" id="waTimeout" value="5">
              <small class="text-muted">Tiempo máximo de respuesta</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label><i class="fas fa-key"></i> Access Token (Recomendado: Token Permanente)</label>
              <input type="password" class="form-control input-lg" id="waMetaAccessToken" placeholder="EAAG...">
              <small class="text-muted">Token de acceso permanente de Meta. <strong>Mantén esto confidencial.</strong></small>
            </div>
          </div>
        </div>
      </div>

      <div class="box-footer">
        <button type="button" class="btn btn-default" id="waReloadBtn"><i class="fas fa-sync"></i> Recargar</button>
        <button type="button" class="btn btn-primary pull-right" id="waSaveMetaBtn"><i class="fas fa-save"></i> Guardar Credenciales</button>
      </div>
    </div>
  </div>

  <div class="col-md-12">
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fas fa-envelope"></i> Plantillas de Mensajes por Estado</h3>
        <div class="box-tools pull-right">
          <small class="text-muted" style="margin-right:10px;"><i class="fas fa-code"></i> Variables: {orden}, {estado_anterior}, {estado_nuevo}, {cliente}, {empresa}</small>
          <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Contraer">
            <i class="fa fa-minus"></i>
          </button>
        </div>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover" id="waTemplatesTable">
            <thead>
              <tr style="background-color:#f4f4f4;">
                <th style="width:180px; vertical-align:middle;"><i class="fas fa-list"></i> Estado</th>
                <th style="vertical-align:middle;"><i class="fas fa-comment"></i> Mensaje</th>
                <th style="width:130px; vertical-align:middle;"><i class="fas fa-image"></i> Multimedia</th>
                <th style="width:260px; vertical-align:middle;"><i class="fas fa-link"></i> URL</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>

      <div class="box-footer">
        <button type="button" class="btn btn-default" id="waReloadTemplatesBtn"><i class="fas fa-sync"></i> Recargar</button>
        <button type="button" class="btn btn-success pull-right" id="waSaveTemplatesBtn"><i class="fas fa-save"></i> Guardar Plantillas</button>
      </div>
    </div>
  </div>
</div>

<style>
.box-header.with-border {
  border-bottom: 2px solid #ddd;
}
#waTemplatesTable tbody tr:hover {
  background-color: #f9f9f9;
}
#waTemplatesTable textarea.form-control {
  font-family: 'Courier New', monospace;
  font-size: 12px;
  resize: vertical;
}
.wa-media-type, .wa-message, .wa-media-url {
  border-radius: 3px;
}
</style>

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
      ['image','📷 Imagen'],
      ['video','🎥 Video'],
      ['audio','🔊 Audio'],
      ['document','📄 Documento']
    ];
    var html = '<select class="form-control input-sm wa-media-type" style="border-radius:3px;">';
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
      var label = (st === '_default') ? '⭐ Default (fallback)' : st;
      var bgColor = (st === '_default') ? 'background-color:#efefef;' : '';
      var row = ''+
        '<tr data-estado="'+$('<div>').text(st).html()+'" style="'+bgColor+'">'+
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

  // Eventos mejorados
  $('#waReloadBtn').on('click', function(){
    loadConfig();
  });
  
  $('#waReloadTemplatesBtn').on('click', function(){
    loadConfig();
  });

  $('#waSaveMetaBtn').on('click', function(){
    if(!$('#waEnabled').is(':checked')){
      Swal.fire('Aviso','Habilita la integración primero','info');
      return;
    }
    if($('#waMetaPhoneNumberId').val() === '' || $('#waMetaAccessToken').val() === ''){
      Swal.fire('Error','Completa los campos requeridos (Phone Number ID, Access Token)','error');
      return;
    }
    saveConfig();
  });

  $('#waSaveTemplatesBtn').on('click', function(){
    saveConfig();
  });

  loadConfig();
})();
</script>
