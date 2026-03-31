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
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:10px;">
      <form role="form" method="POST" enctype="multipart/form-data">

        <div class="modal-header" style="border-radius:10px 10px 0 0;background:#138a1e;color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fas fa-user-plus"></i>&nbsp; Agregar Cliente</h4>
        </div>

        <div class="modal-body">
          <div class="box-body">

            <!-- Nombre -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon" style="border-radius:3px;"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control input-lg nombreCliente namecliente"
                       name="AgregarNombreCliente" placeholder="Nombre del cliente"
                       id="nombrecliente" style="border-radius:3px;">
                <?php echo '<input type="hidden" value="'.$_SESSION["empresa"].'" name="id_empresa">'; ?>
              </div>
            </div>

            <!-- Correo -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon" style="border-radius:3px;"><i class="fas fa-at"></i></span>
                <input type="text" style="border-radius:3px;"
                       class="form-control input-lg emailverif mailv"
                       name="AgregarCorreoCliente" placeholder="Correo del cliente">
                <span id="spanmail"></span>
              </div>
            </div>

            <!-- WhatsApp -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon" style="border-radius:3px;"><i class="fab fa-whatsapp"></i></span>
                <input type="tel" style="border-radius:3px;"
                       class="form-control input-lg telwhatsapp numerocliente1"
                       id="whatsapp" name="telefonoDosCliente" placeholder="Número de WhatsApp" required>
                <span id="spaninputnum1"></span>
              </div>
            </div>

            <!-- Teléfono -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon" style="border-radius:3px;"><i class="fas fa-phone-alt"></i></span>
                <input type="tel" style="border-radius:3px;"
                       class="form-control input-lg"
                       id="telefono" name="telefonoUnoCliente" placeholder="Número telefónico" required>
              </div>
            </div>

            <!-- Etiqueta -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fas fa-tag"></i></span>
                <select style="border-radius:3px;" class="form-control input-lg" name="EtiquetaCliente">
                  <option value="Nuevo">Nuevo</option>
                  <option value="Frecuente">Frecuente</option>
                  <option value="Problematico">Problemático</option>
                </select>
              </div>
            </div>

            <!-- Asesor -->
            <div class="input-group">
              <span class="input-group-addon"><i class="fas fa-headphones"></i></span>
              <select style="border-radius:3px;" class="form-control input-lg" name="AgreagrAsesorAlCliente">
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

            <hr>
            <div style="text-align:center;padding:10px 0;">
              <p style="font-weight:600;font-size:15px;color:#555;margin-bottom:12px;">Verificar contacto</p>
              <span id="spanbuttonw"></span>
              <br><br>
              <span id="spanbuttone"></span>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-success" id="guardar">
            <i class="fas fa-save"></i> Agregar Cliente
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
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:10px;">
      <form role="form" method="post" enctype="multipart/form-data">

        <div class="modal-header" style="border-radius:10px 10px 0 0;background:#138a1e;color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fas fa-user-edit"></i>&nbsp; Editar Cliente</h4>
        </div>

        <div class="modal-body">
          <div class="box-body">

            <!-- Nombre -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon" style="border-radius:3px;"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control input-lg"
                       name="editarNombreDelCliente" id="editarNombreDelCliente"
                       placeholder="Nombre del cliente" style="border-radius:3px;">
                <input type="hidden" id="idCliente" name="idCliente">
              </div>
            </div>

            <!-- Email -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon" style="border-radius:3px;"><i class="fas fa-envelope"></i></span>
                <input type="email" style="border-radius:3px;" class="form-control input-lg"
                       name="EditarCorreoCliente" id="EditarCorreoCliente"
                       placeholder="Correo del cliente" required>
              </div>
            </div>

            <!-- Teléfono -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon" style="border-radius:3px;"><i class="fas fa-phone-alt"></i></span>
                <input type="tel" style="border-radius:3px;" class="form-control input-lg"
                       name="EditarNumeroDelCliente" id="EditarNumeroDelCliente"
                       placeholder="Número telefónico" required>
              </div>
            </div>

            <!-- WhatsApp -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon" style="border-radius:3px;"><i class="fab fa-whatsapp"></i></span>
                <input type="tel" style="border-radius:3px;" class="form-control input-lg"
                       name="EditarSegundoNumeroDeTel" id="EditarSegundoNumeroDeTel"
                       placeholder="Número de WhatsApp" required>
              </div>
            </div>

            <!-- Etiqueta -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fas fa-tag"></i></span>
                <select style="border-radius:3px;" class="form-control input-lg EditarEtiquetaDelCLiente"
                        name="EditarEtiquetaDelCLiente">
                  <option class="MostrarEtiquetaDelCliente"></option>
                  <option value="Nuevo">Nuevo</option>
                  <option value="Frecuente">Frecuente</option>
                  <option value="Problematico">Problemático</option>
                </select>
              </div>
            </div>

            <!-- Asesor -->
            <div class="input-group">
              <span class="input-group-addon"><i class="fas fa-headphones"></i></span>
              <select style="border-radius:3px;" class="form-control input-lg"
                      name="EditarAsesorDelCliente"
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
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Guardar Cambios
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
