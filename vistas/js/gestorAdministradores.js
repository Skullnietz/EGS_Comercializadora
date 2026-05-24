/*=============================================
ACTIVAR PERFIL
=============================================*/
$(".tablaPerfiles").on("click", ".btnActivar", function(){

  var idPerfil = $(this).attr("idPerfil");
  var estadoPerfil = $(this).attr("estadoPerfil");

  var datos = new FormData();
  datos.append("activarId", idPerfil);
    datos.append("activarPerfil", estadoPerfil);

    $.ajax({

    url:"ajax/administradores.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
      contentType: false,
      processData: false,
      success: function(respuesta){
        //console.log("respuesta", respuesta);
      }

    })

    if(estadoPerfil == 0){

      $(this).removeClass('btn-success');
      $(this).addClass('btn-danger');
      $(this).html('Desactivado');
      $(this).attr('estadoPerfil',1);

    }else{

      $(this).addClass('btn-success');
      $(this).removeClass('btn-danger');
      $(this).html('Activado');
      $(this).attr('estadoPerfil',0);

    }

})

/*=============================================
SUBIENDO LA FOTO DEL PERFIL
=============================================*/
$(".nuevaFoto").change(function(){

  var imagen = this.files[0];
  
  /*=============================================
    VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
    =============================================*/

    if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){

      $(".nuevaFoto").val("");

       swal({
          title: "Error al subir la imagen",
          text: "¡La imagen debe estar en formato JPG o PNG!",
          type: "error",
          confirmButtonText: "¡Cerrar!"
        });

    }else if(imagen["size"] > 2000000){

      $(".nuevaFoto").val("");

       swal({
          title: "Error al subir la imagen",
          text: "¡La imagen no debe pesar más de 2MB!",
          type: "error",
          confirmButtonText: "¡Cerrar!"
        });

    }else{

      var datosImagen = new FileReader;
      datosImagen.readAsDataURL(imagen);

      $(datosImagen).on("load", function(event){

        var rutaImagen = event.target.result;

        $(".previsualizar").attr("src", rutaImagen);

      })

    }
})

/*=============================================
EDITAR PERFIL
=============================================*/
$(".tablaPerfiles").on("click", ".btnEditarPerfil", function(){

  var idPerfil = $(this).attr("idPerfil");
  
  var datos = new FormData();
  datos.append("idPerfil", idPerfil);

  $.ajax({ 

    url:"ajax/administradores.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function(respuesta){ 

      $("#editarNombre").val(respuesta["nombre"]);
      $("#idPerfil").val(respuesta["id"]);
      $("#editarEmail").val(respuesta["email"]);
      $("#editarPerfilOpcion").html(respuesta["perfil"]);
      $("#editarPerfilOpcion").val(respuesta["perfil"]);
      $("#editarPerfilSelect").val(respuesta["perfil"]);

      // Auto-fill technician data if available
      if(respuesta["perfil"] == "tecnico"){
        $("#editarNumeroUnoTecnico").val(respuesta["telefono_tec"] || "");
        $("#editarTelefonoDosTecnico").val(respuesta["telefonoDos_tec"] || "");
        $("#HoraDeComidaEditada").val(respuesta["HoraDeComida_tec"] || "");
        $("#editarAreratecnico").val(respuesta["areratecnico_tec"] || "");
      }
      
      // Auto-fill advisor data if available
      if(respuesta["perfil"] == "vendedor"){
        $("#editarNumeroUnoAsesor").val(respuesta["numeroTelefono_ase"] || "");
        $("#editarTelefonoDosAsesor").val(respuesta["numerodeCelular_ase"] || "");
      }
      
      // Set department
      $("#editarDepartamento").val(respuesta["Departamento"] || "");

      $("#fotoActual").val(respuesta["foto"]);
      $("#passwordActual").val(respuesta["password"]);

      if(respuesta["foto"] != ""){
        $(".previsualizar").attr("src", respuesta["foto"]);
      }
      
      // Trigger change to display dynamic divs
      $("#editarPerfilSelect").trigger("change");

    }

  })

})

/*=============================================
MOSTRAR/OCULTAR CAMPOS DINÁMICOS POR ROL
=============================================*/
$("#nuevoPerfil").change(function(){
  var perfil = $(this).val();
  if(perfil == "tecnico"){
    $("#divAdicionalTecnico").slideDown();
    $("#divAdicionalAsesor").slideUp();
  } else if(perfil == "vendedor"){
    $("#divAdicionalAsesor").slideDown();
    $("#divAdicionalTecnico").slideUp();
  } else {
    $("#divAdicionalTecnico").slideUp();
    $("#divAdicionalAsesor").slideUp();
  }
});

$("#editarPerfilSelect").change(function(){
  var perfil = $(this).val();
  if(perfil == "tecnico"){
    $("#divAdicionalTecnicoEdit").slideDown();
    $("#divAdicionalAsesorEdit").slideUp();
  } else if(perfil == "vendedor"){
    $("#divAdicionalAsesorEdit").slideDown();
    $("#divAdicionalTecnicoEdit").slideUp();
  } else {
    $("#divAdicionalTecnicoEdit").slideUp();
    $("#divAdicionalAsesorEdit").slideUp();
  }
});

/*=============================================
ELIMINAR USUARIO
=============================================*/
$(".tablaPerfiles").on("click", ".btnEliminarPerfil", function(){

  var idPerfil = $(this).attr("idPerfil");
  var fotoPerfil = $(this).attr("fotoPerfil");


  swal({
    title: '¿Está seguro de borrar el perfil?',
    text: "¡Si no lo está puede cancelar la accíón!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Si, borrar perfil!'
  }).then(function(result){

    if(result.value){

      window.location = "index.php?ruta=perfiles&idPerfil="+idPerfil+"&fotoPerfil="+fotoPerfil;

    }

  })

})

/*=============================================
CERRAR SESION DESPUES DE 20 MINUTOS DE INACTIVIDAD
=============================================
$(document).ready(function(){
incrementarContador()
});

var ContadorTiempoEnSesion = 0;
$(document).ready(function () {
    //Increment the contador un minuto.
    var idleInterval = setInterval(incrementarContador, 60000); // 1 minute

    //Colocar el contador en cero cuadno exita un evento de mouse o de tecla.
    $(this).mousemove(function (e) {

        ContadorTiempoEnSesion = 0;
    });
    
    $(this).keypress(function (e) {

        ContadorTiempoEnSesion = 0;
    });

});
function incrementarContador(){

    ContadorTiempoEnSesion = ContadorTiempoEnSesion + 1;

    if (ContadorTiempoEnSesion > 19) { // 20 minutos

        window.location="salir";
    }
}
*/

/*=============================================
VALIDAR CONTRASEÑAS AL CREAR Y EDITAR
=============================================*/
$("form").on("submit", function(e){
  var nuevoPass = $(this).find("#nuevoPassword");
  var nuevoPassConf = $(this).find("#nuevoPasswordConfirmar");

  if(nuevoPass.length > 0 && nuevoPassConf.length > 0){
    if(nuevoPass.val() !== nuevoPassConf.val()){
      e.preventDefault();
      swal({
        title: "Error de contraseña",
        text: "¡Las contraseñas no coinciden!",
        type: "error",
        confirmButtonText: "¡Cerrar!"
      });
      return false;
    }
  }

  var editPass = $(this).find("#editarPassword");
  var editPassConf = $(this).find("#editarPasswordConfirmar");

  if(editPass.length > 0 && editPassConf.length > 0){
    if(editPass.val() !== "" && editPass.val() !== editPassConf.val()){
      e.preventDefault();
      swal({
        title: "Error de contraseña",
        text: "¡Las contraseñas no coinciden!",
        type: "error",
        confirmButtonText: "¡Cerrar!"
      });
      return false;
    }
  }
});

/* Efecto hover zona de imagen */
$(".nuevaFoto").hover(
  function() { $(this).parent().css("border-color", "var(--crm-accent)"); },
  function() { $(this).parent().css("border-color", "var(--crm-border)"); }
);


