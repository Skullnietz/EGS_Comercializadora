<?php
if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "Super-Administrador"){

  echo '<script>window.location = "inicio";</script>';

  return;

}
?>

<style>
/* ── Encabezado de página ────────────────────────────────── */
.clientes-page-header {
    background: linear-gradient(135deg, #138a1e 0%, #1abc2e 100%);
    border-radius: 10px;
    padding: 22px 28px;
    margin-bottom: 22px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    box-shadow: 0 4px 15px rgba(19,138,30,0.25);
}
.clientes-page-header .header-left h2 {
    margin: 0 0 4px 0;
    font-size: 22px;
    font-weight: 700;
    letter-spacing: 0.3px;
}
.clientes-page-header .header-left p {
    margin: 0;
    font-size: 13px;
    opacity: 0.88;
}
.clientes-page-header .header-right {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
}

/* ── Card principal ──────────────────────────────────────── */
.clientes-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    overflow: hidden;
}
.clientes-card .card-toolbar {
    padding: 16px 20px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    background: #fafafa;
}
.clientes-card .card-toolbar .toolbar-left {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.clientes-card .card-body-inner {
    padding: 18px 20px;
}

/* ── Botones de la toolbar ───────────────────────────────── */
.btn-add-client {
    background: #138a1e;
    color: white;
    border: none;
    border-radius: 7px;
    padding: 8px 18px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.btn-add-client:hover { background: #0f6e17; color: white; }

.btn-excel {
    background: #fff;
    color: #138a1e;
    border: 1.5px solid #138a1e;
    border-radius: 7px;
    padding: 7px 14px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}
.btn-excel:hover { background: #138a1e; color: white; text-decoration: none; }

/* ── Tabla ───────────────────────────────────────────────── */
.tablaClientesOrden thead tr th {
    background: #f7f9fc;
    color: #2c3e50;
    font-weight: 700;
    font-size: 13px;
    border-bottom: 2px solid #e0e6ed !important;
    padding: 12px 10px;
    white-space: nowrap;
}
.tablaClientesOrden tbody tr td {
    padding: 10px 10px;
    vertical-align: middle;
    border-color: #f0f3f7 !important;
}
.tablaClientesOrden tbody tr:hover { background: #f8fffe; }

/* ── Nombre en tabla ─────────────────────────────────────── */
.cliente-nombre {
    font-weight: 700;
    color: #1a252f;
    font-size: 13.5px;
}
.cliente-asesor {
    font-size: 12.5px;
    color: #566573;
}

/* ── Toggle buttons ──────────────────────────────────────── */
.btn-toggle-view {
    background: #fff;
    color: #555;
    border: 1.5px solid #ddd;
    border-radius: 7px;
    padding: 7px 14px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-toggle-view:hover {
    border-color: #138a1e;
    color: #138a1e;
}
.btn-toggle-view:focus {
    outline: none;
    box-shadow: none;
}
.btn-toggle-view:active:not(.active) {
    background: #e8f5e9;
}
.btn-toggle-view.active {
    background: #138a1e;
    color: #fff;
    border-color: #138a1e;
    box-shadow: 0 2px 8px rgba(19,138,30,0.2);
}
</style>

<script>
$(document).ready(function(){

    $("#guardar").hide();

    /* ── Validación nombre ─────────────────────────── */
    const $input1 = document.querySelector('#nombrecliente');
    const patron1 = /[a-zA-ZñÑ éáíóÉÁÍÓ]+/;
    $input1.addEventListener("keydown", event => {
        if(patron1.test(event.key)){
            $("#nombrecliente").css({ "border": "1px solid #0C0"});
        } else {
            if(event.keyCode == 8){ } else { event.preventDefault(); }
        }
    });

    /* ── Validación WhatsApp ───────────────────────── */
    const $input2 = document.querySelector('#whatsapp');
    const patron2 = /[0-9-]+/;
    $input2.addEventListener("keydown", event => {
        if(!patron2.test(event.key)){
            if(event.keyCode == 8){ } else { event.preventDefault(); }
        }
    });

    /* ── Validación teléfono ───────────────────────── */
    const $input3 = document.querySelector('#telefono');
    const patron3 = /[0-9-]+/;
    $input3.addEventListener("keydown", event => {
        if(patron3.test(event.key)){
            $("#telefono").css({ "border": "1px solid #0C0"});
        } else {
            if(event.keyCode == 8){ } else { event.preventDefault(); }
        }
    });

    /* ── Nombre en mayúsculas ──────────────────────── */
    $("#nombrecliente").keyup(function() {
        $('#nombrecliente').val($(this).val().toUpperCase());
    });

    /* ── Botón verificar WhatsApp (sin mensaje predefinido) ── */
    $(".telwhatsapp").keyup(function() {
        var whatsapp = $(".telwhatsapp").val();
        var textoboton = "<a style='color:white;' href='https://api.whatsapp.com/send/?phone=52" + whatsapp + "' target='_blank'>"
                       + "<button class='btn btn-success btn-lg spanbuttonwa'>"
                       + "<i class='fab fa-whatsapp'></i> VERIFICAR NÚMERO DE WHATSAPP"
                       + "</button></a>";
        $("#spanbuttonw").html(textoboton);
        $(".spanbuttonwa").click(function(){
            $("#guardar").show();
        });
    });

    /* ── Botón verificar Email ─────────────────────── */
    $(".emailverif").keyup(function() {
        var nombre = $(".namecliente").val();
        var email  = $(".emailverif").val();
        var textoboton = "<a style='color:white;' href='mailto:" + email + "?subject=BIENVENIDO%20A%20EGS' target='_blank'>"
                       + "<button class='btn btn-primary btn-lg spanbuttonem'>"
                       + "<i class='fas fa-envelope'></i> VERIFICAR EMAIL"
                       + "</button></a>";
        $("#spanbuttone").html(textoboton);
        $(".spanbuttonem").click(function(){
            $("#guardar").show();
        });
    });

    /* ── Validación número ─────────────────────────── */
    $(".numerocliente1").keyup(function(){
        var txtnumero = $(".numerocliente1").val();
        var valnumero = /^(\(\+?\d{2,3}\)[\*|\s|\-|\.]?(([\d][\*|\s|\-|\.]?){6})(([\d][\s|\-|\.]?){2})?|(\+?[\d][\s|\-|\.]?){8}(([\d][\s|\-|\.]?){2}(([\d][\s|\-|\.]?){2})?)?)$/;
        if(valnumero.test(txtnumero)){
            $("#spaninputnum1").text("Correcto").css("color", "green");
            $(".numerocliente1").css({ "border":"1px solid #0C0"}).fadeIn(2000);
            $("#spanbuttonw").show();
        } else {
            $("#spaninputnum1").text("Registre un número válido").css("color", "red");
            $(".numerocliente1").css({ "border":"1px solid #C00"}).fadeIn(2000);
            $("#spanbuttonw").hide();
        }
    });

    /* ── Validación email ──────────────────────────── */
    $(".mailv").keyup(function(){
        var txtmail = $(".mailv").val();
        var valmail = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
        if(valmail.test(txtmail)){
            $("#spanmail").text("Válido").css("color", "green");
            $(".mailv").css({ "border":"1px solid #0F0"}).fadeIn(2000);
            $("#spanbuttone").show();
        } else {
            $("#spanmail").text("Correo incorrecto").css("color", "red");
            $(".mailv").css({ "border":"1px solid #F00"}).fadeIn(2000);
            $("#spanbuttone").hide();
        }
    });

});
</script>

<div class="content-wrapper">

  <section class="content-header">
    <h1><i class="fas fa-users" style="color:#138a1e;"></i> Gestor de Clientes</h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fas fa-home"></i> Inicio</a></li>
      <li class="active">Clientes</li>
    </ol>
  </section>

  <section class="content">

    <!-- ── Encabezado visual ──────────────────────────── -->
    <div class="clientes-page-header">

      <div class="header-left">
        <h2><i class="fas fa-users"></i>&nbsp; Clientes Registrados</h2>
        <p>Consulta, registra y clasifica a tus clientes. Ve su historial y contacto en un solo lugar.</p>
      </div>

      <div class="header-right">
        <?php if($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "Super-Administrador" || $_SESSION["perfil"] == "vendedor"): ?>
          <a class="btn-excel" href="vistas/modulos/descargar-reporte-Usuarios.php?reporte=usuariosTienda&empresa=<?php echo $_SESSION["empresa"]; ?>">
            <i class="fas fa-file-excel"></i> Reporte General
          </a>
          <a class="btn-excel" href="vistas/modulos/descargar-reporte-Usuarios.php?reportes=usuariosTiendaENT&empresa=<?php echo $_SESSION["empresa"]; ?>">
            <i class="fas fa-file-excel"></i> Reporte ENT
          </a>
        <?php endif; ?>
      </div>

    </div>

    <!-- ── Card principal ────────────────────────────── -->
    <div class="clientes-card">

      <div class="card-toolbar">
        <div class="toolbar-left">
          <button class="btn-add-client" data-toggle="modal" data-target="#modalAgregarUsuario">
            <i class="fas fa-user-plus"></i>&nbsp; Agregar Cliente
          </button>

          <div class="toggle-view-group" style="display:inline-flex;gap:4px;margin-left:12px;">
            <button id="btnOrdenID" class="btn-toggle-view active" title="Ordenados por ID (más recientes primero)">
              <i class="fas fa-sort-numeric-down"></i>&nbsp; Por ID
            </button>
            <button id="btnMejoresClientes" class="btn-toggle-view" title="Mejores clientes: más órdenes y mejor calificación">
              <i class="fas fa-trophy"></i>&nbsp; Mejores Clientes
            </button>
            <button id="btnMalosClientes" class="btn-toggle-view" title="Clientes con baja calificación y más órdenes">
              <i class="fas fa-exclamation-triangle"></i>&nbsp; Malos Clientes
            </button>
          </div>
        </div>
        <div style="font-size:13px;color:#888;">
          <i class="fas fa-info-circle"></i>&nbsp;
          Los badges de <strong>Contacto</strong> son clicables para llamar, abrir WhatsApp o correo.
        </div>
      </div>

      <div class="card-body-inner">

        <table class="table table-bordered table-striped dt-responsive tablaClientesOrden" width="100%">
          <thead>
            <tr>
              <th style="width:36px;">#</th>
              <th>Nombre</th>
              <th>Asesor</th>
              <th>Contacto</th>
              <th>Clasificación</th>
              <th>Acciones</th>
              <th>Registro</th>
              <th>_ordenes</th>
              <th>_fecha</th>
              <th>_calificacion</th>
            </tr>
          </thead>

          <?php
            echo '<input type="hidden" id="tipoDePerfil"      value="'.$_SESSION["perfil"].'">'
               . '<input type="hidden" id="Empresa_del_perfil" value="'.$_SESSION["empresa"].'">';
          ?>

        </table>

      </div>

    </div>

  </section>

</div>


<!--═══════════════════════════════════════════════
  MODAL — AGREGAR CLIENTE
══════════════════════════════════════════════════-->

<div id="modalAgregarUsuario" class="modal fade" role="dialog">
  <div class="modal-dialog" style="max-width:520px">
    <div class="modal-content" style="border-radius:14px;border:none;box-shadow:0 8px 40px rgba(15,23,42,.18);overflow:hidden">
      <form role="form" method="POST" enctype="multipart/form-data">

        <div class="modal-header" style="background:linear-gradient(135deg,#0f1c2e,#1e3d72);border-bottom:none;padding:20px 24px">
          <button type="button" class="close" data-dismiss="modal" style="color:rgba(255,255,255,.8);opacity:1;text-shadow:none;font-size:20px;font-weight:700">&times;</button>
          <h4 class="modal-title" style="font-weight:800;font-size:16px;color:#fff;margin:0;display:flex;align-items:center;gap:8px">
            <i class="fas fa-user-plus" style="font-size:15px;opacity:.85"></i> Agregar Cliente
          </h4>
          <div style="font-size:11px;color:rgba(255,255,255,.5);margin-top:4px">Registrar nuevo cliente en el sistema</div>
        </div>

        <div class="modal-body" style="padding:24px">

          <!-- Nombre -->
          <div class="form-group" style="margin-bottom:16px">
            <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;display:block">
              <i class="fas fa-user" style="margin-right:4px;color:#94a3b8"></i> Nombre
            </label>
            <input type="text" class="form-control nombreCliente namecliente"
                   name="AgregarNombreCliente" placeholder="Nombre del cliente"
                   id="nombrecliente"
                   style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 14px;font-size:14px;transition:border-color .15s,box-shadow .15s">
            <?php echo '<input type="hidden" value="'.$_SESSION["empresa"].'" name="id_empresa">'; ?>
            <input type="hidden" name="EtiquetaCliente" value="Nuevo">
          </div>

          <!-- Correo -->
          <div class="form-group" style="margin-bottom:16px">
            <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;display:block">
              <i class="fas fa-at" style="margin-right:4px;color:#94a3b8"></i> Correo electrónico
            </label>
            <div style="position:relative">
              <input type="text" class="form-control emailverif mailv"
                     name="AgregarCorreoCliente" placeholder="correo@ejemplo.com"
                     style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 14px;font-size:14px;transition:border-color .15s,box-shadow .15s">
              <span id="spanmail" style="position:absolute;right:12px;top:50%;transform:translateY(-50%)"></span>
            </div>
          </div>

          <div style="display:flex;gap:12px">
            <!-- WhatsApp -->
            <div class="form-group" style="margin-bottom:16px;flex:1">
              <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;display:block">
                <i class="fab fa-whatsapp" style="margin-right:4px;color:#25d366"></i> WhatsApp
              </label>
              <div style="position:relative">
                <input type="tel" class="form-control telwhatsapp numerocliente1"
                       id="whatsapp" name="telefonoDosCliente" placeholder="10 dígitos" required
                       style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 14px;font-size:14px;transition:border-color .15s,box-shadow .15s">
                <span id="spaninputnum1" style="position:absolute;right:12px;top:50%;transform:translateY(-50%)"></span>
              </div>
            </div>

            <!-- Teléfono -->
            <div class="form-group" style="margin-bottom:16px;flex:1">
              <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;display:block">
                <i class="fas fa-phone-alt" style="margin-right:4px;color:#94a3b8"></i> Teléfono
              </label>
              <input type="tel" class="form-control"
                     id="telefono" name="telefonoUnoCliente" placeholder="10 dígitos" required
                     style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 14px;font-size:14px;transition:border-color .15s,box-shadow .15s">
            </div>
          </div>

          <!-- Asesor -->
          <div class="form-group" style="margin-bottom:16px">
            <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;display:block">
              <i class="fas fa-headphones" style="margin-right:4px;color:#94a3b8"></i> Asesor
            </label>
            <select class="form-control" name="AgreagrAsesorAlCliente"
                    style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 14px;font-size:14px;height:auto">
              <option value="">Seleccionar Asesor</option>
              <?php
                $item  = null;
                $valor = null;
                $Asesores = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);
                foreach($Asesores as $key => $value){
                  echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                }
              ?>
            </select>
          </div>

          <!-- Verificar contacto -->
          <div style="text-align:center;padding:14px 0 4px;border-top:1px solid #f1f5f9;margin-top:8px">
            <p style="font-weight:700;font-size:12px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px">Verificar contacto</p>
            <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap">
              <span id="spanbuttonw"></span>
              <span id="spanbuttone"></span>
            </div>
          </div>

        </div>

        <div class="modal-footer" style="border-top:1px solid #f1f5f9;padding:14px 24px;display:flex;justify-content:space-between;align-items:center">
          <button type="button" class="btn" data-dismiss="modal"
                  style="background:#f1f5f9;color:#64748b;border:none;border-radius:8px;padding:8px 18px;font-weight:600;font-size:13px">
            Cancelar
          </button>
          <button type="submit" class="btn" id="guardar"
                  style="background:linear-gradient(135deg,#6366f1,#818cf8);color:#fff;border:none;border-radius:8px;padding:8px 22px;font-weight:700;font-size:13px;box-shadow:0 2px 8px rgba(99,102,241,.3);transition:box-shadow .15s">
            <i class="fas fa-save" style="margin-right:4px"></i> Agregar Cliente
          </button>
        </div>

        <?php
          $AgregarCliente = new ControladorClientes();
          $AgregarCliente->ctrMostrarAgregarCliente();
        ?>

      </form>
    </div>
  </div>
</div>


<!--═══════════════════════════════════════════════
  MODAL — EDITAR CLIENTE
══════════════════════════════════════════════════-->

<div id="btnEditarCliente" class="modal fade" role="dialog">
  <div class="modal-dialog" style="max-width:520px">
    <div class="modal-content" style="border-radius:14px;border:none;box-shadow:0 8px 40px rgba(15,23,42,.18);overflow:hidden">
      <form role="form" method="post" enctype="multipart/form-data">

        <div class="modal-header" style="background:linear-gradient(135deg,#0f1c2e,#1e3d72);border-bottom:none;padding:20px 24px">
          <button type="button" class="close" data-dismiss="modal" style="color:rgba(255,255,255,.8);opacity:1;text-shadow:none;font-size:20px;font-weight:700">&times;</button>
          <h4 class="modal-title" style="font-weight:800;font-size:16px;color:#fff;margin:0;display:flex;align-items:center;gap:8px">
            <i class="fas fa-user-edit" style="font-size:15px;opacity:.85"></i> Editar Cliente
          </h4>
          <div style="font-size:11px;color:rgba(255,255,255,.5);margin-top:4px">Modificar datos del cliente</div>
        </div>

        <div class="modal-body" style="padding:24px">

          <!-- Nombre -->
          <div class="form-group" style="margin-bottom:16px">
            <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;display:block">
              <i class="fas fa-user" style="margin-right:4px;color:#94a3b8"></i> Nombre
            </label>
            <input type="text" class="form-control"
                   name="editarNombreDelCliente" id="editarNombreDelCliente"
                   placeholder="Nombre del cliente"
                   style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 14px;font-size:14px;transition:border-color .15s,box-shadow .15s">
            <input type="hidden" id="idCliente" name="idCliente">
          </div>

          <!-- Email -->
          <div class="form-group" style="margin-bottom:16px">
            <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;display:block">
              <i class="fas fa-envelope" style="margin-right:4px;color:#94a3b8"></i> Correo electrónico
            </label>
            <input type="email" class="form-control"
                   name="EditarCorreoCliente" id="EditarCorreoCliente"
                   placeholder="correo@ejemplo.com" required
                   style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 14px;font-size:14px;transition:border-color .15s,box-shadow .15s">
          </div>

          <div style="display:flex;gap:12px">
            <!-- Teléfono -->
            <div class="form-group" style="margin-bottom:16px;flex:1">
              <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;display:block">
                <i class="fas fa-phone-alt" style="margin-right:4px;color:#94a3b8"></i> Teléfono
              </label>
              <input type="tel" class="form-control"
                     name="EditarNumeroDelCliente" id="EditarNumeroDelCliente"
                     placeholder="10 dígitos" required
                     style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 14px;font-size:14px;transition:border-color .15s,box-shadow .15s">
            </div>

            <!-- WhatsApp -->
            <div class="form-group" style="margin-bottom:16px;flex:1">
              <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;display:block">
                <i class="fab fa-whatsapp" style="margin-right:4px;color:#25d366"></i> WhatsApp
              </label>
              <input type="tel" class="form-control"
                     name="EditarSegundoNumeroDeTel" id="EditarSegundoNumeroDeTel"
                     placeholder="10 dígitos" required
                     style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 14px;font-size:14px;transition:border-color .15s,box-shadow .15s">
            </div>
          </div>

          <!-- Asesor -->
          <div class="form-group" style="margin-bottom:0">
            <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;display:block">
              <i class="fas fa-headphones" style="margin-right:4px;color:#94a3b8"></i> Asesor
            </label>
            <select class="form-control"
                    name="EditarAsesorDelCliente"
                    style="border-radius:8px;border:1.5px solid #e2e8f0;padding:10px 14px;font-size:14px;height:auto"
                    <?php if($_SESSION["perfil"] == "vendedor"){ echo 'disabled'; } ?>>
              <option value="" id="EditarAsesorDelCliente">Seleccionar Asesor</option>
              <?php
                $item  = null;
                $valor = null;
                $Asesores = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);
                foreach($Asesores as $key => $value){
                  echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                }
              ?>
            </select>
          </div>

        </div>

        <div class="modal-footer" style="border-top:1px solid #f1f5f9;padding:14px 24px;display:flex;justify-content:space-between;align-items:center">
          <button type="button" class="btn" data-dismiss="modal"
                  style="background:#f1f5f9;color:#64748b;border:none;border-radius:8px;padding:8px 18px;font-weight:600;font-size:13px">
            Cancelar
          </button>
          <button type="submit" class="btn"
                  style="background:linear-gradient(135deg,#6366f1,#818cf8);color:#fff;border:none;border-radius:8px;padding:8px 22px;font-weight:700;font-size:13px;box-shadow:0 2px 8px rgba(99,102,241,.3);transition:box-shadow .15s">
            <i class="fas fa-save" style="margin-right:4px"></i> Guardar Cambios
          </button>
        </div>

        <?php
          $editarPerfil = new ControladorClientes();
          $editarPerfil->ctrEditarCliente();
        ?>

      </form>
    </div>
  </div>
</div>
