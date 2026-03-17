<?php
if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "Super-Administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}
?>
<script>
 $(document).ready(function(){
     $("#guardar").hide();
     //Validaciones segun el input
    const $input1 = document.querySelector('#nombrecliente');
    const patron1 = /[a-zA-ZñÑ éáíóÉÁÍÓ]+/;
    $input1.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron1.test(event.key)){
                    $("#nombrecliente").css({ "border": "1px solid #0C0"});
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
    const $input2 = document.querySelector('#whatsapp');
    const patron2 = /[0-9-]+/;
    $input2.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron2.test(event.key)){
                    
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
    const $input3 = document.querySelector('#telefono');
    const patron3 = /[0-9-]+/;
    $input3.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron3.test(event.key)){
                    $("#telefono").css({ "border": "1px solid #0C0"});
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
        //Nombre en Mayus
    
    $("#nombrecliente").keyup(function() {
        $('#nombrecliente').val($(this).val().toUpperCase());
    });
   
    /*$(".namecliente").change(function() {
        var textoboton = "<a style='color: white;' href='https://api.whatsapp.com/send/?phone=52"+whatsapp+"&text=BIENVENIDO+*"+nombre+"*.%0A%0A+Somos+*COMERCIALIZADORA%20EGS*,+Gracias+por+venir+y+permitirnos+apoyarte+en+tu+proyecto+de+reparación,+recuerda+que+es+importante+seguir+en+comunicación+por+este+medio.+%0A%0A+*HORARIO*:+%0A+*LUNES+A+VIERNES+DE+10:00+AM+A+2:00+PM+Y+DE+4:00+PM+A+6:30+PM*+%0A+*SABADOS+DE+9:00+AM+A+2:00+PM*+%0A%0A*Teléfonos:*+%207222144416%20/%207221671684%20/%207222831159%20+solo+para+dudas+y+aclaraciones+%0A'target='_blank'><button class='btn btn-success btn-lg'><i class='fab fa-whatsapp'></i> VERIFICAR NUMERO DE WHATSAPP</a>";
        $("#spanbuttonw").html(textoboton);
    });*/
    $(".telwhatsapp").keyup(function() {
        var nombre = $(".namecliente").val();
        var whatsapp = $(".telwhatsapp").val();
        var textoboton = "<a style='color: white;' href='https://api.whatsapp.com/send/?phone=52"+whatsapp+"&text=BIENVENIDO+*"+nombre+"*.%0A%0A+Somos+*COMERCIALIZADORA%20EGS*,+Gracias+por+venir+y+permitirnos+apoyarte+en+tu+proyecto+de+reparación,+recuerda+que+es+importante+seguir+en+comunicación+por+este+medio.+%0A%0A+*HORARIO*:+%0A+*LUNES+A+VIERNES+DE+10:00+AM+A+2:00+PM+Y+DE+4:00+PM+A+6:30+PM*+%0A+*SABADOS+DE+9:00+AM+A+2:00+PM*+%0A%0A*Teléfonos:*+%207222144416%20/%207221671684%20/%207222831159%20+solo+para+dudas+y+aclaraciones+%0A'target='_blank'><button class='btn btn-success btn-lg spanbuttonwa'><i class='fab fa-whatsapp'></i> VERIFICAR NUMERO DE WHATSAPP</a></button>";
        $("#spanbuttonw").html(textoboton);
        $(".spanbuttonwa").click(function(event){
     $("#guardar").show();
        });
    });
    
    $(".emailverif").keyup(function() {
        var nombre = $(".namecliente").val();
        var email = $(".emailverif").val();
        var textoboton = "<a style='color: white;' href='mailto:"+email+"?subject=BIENVENIDO%20A%20EGS&body=BIENVENIDO%20"+nombre+".%0A%0A%20Somos%20COMERCIALIZADORA%20EGS,%20Gracias%20por%20venir%20y%20permitirnos%20apoyarte%20en%20tu%20proyecto%20de%20reparación,%20recuerda%20que%20es%20importante%20seguir%20en%20comunicación%20por%20este%20medio.%20%0A%0A%20HORARIO:%20%0A%20LUNES%20A%20VIERNES%20DE%2010:00%20AM%20A%202:00%20PM%20Y%20DE%204:00%20PM%20A%206:30%20PM%20%0A%20SABADOS%20DE%209:00%20AM%20A%202:00%20PM%20%0A%0ATeléfonos:%20%207222144416%20/%207221671684%20/%207222831159%20%20solo%20para%20dudas%20y%20aclaraciones%20%0A'target='_blank'><button class='btn btn-primary btn-lg spanbuttonem'><i class='fas fa-envelope'></i> VERIFICAR EMAIL </a></button>";
        $("#spanbuttone").html(textoboton);
        $(".spanbuttonem").click(function(event){
     $("#guardar").show();
        });
    });
    
    $(".numerocliente1").keyup(function(){
               var txtnumero = $(".numerocliente1").val();
               var valnumero = /^(\(\+?\d{2,3}\)[\*|\s|\-|\.]?(([\d][\*|\s|\-|\.]?){6})(([\d][\s|\-|\.]?){2})?|(\+?[\d][\s|\-|\.]?){8}(([\d][\s|\-|\.]?){2}(([\d][\s|\-|\.]?){2})?)?)$/;
               if(valnumero.test(txtnumero)){
                                $("#spaninputnum1").text("Correcto").css("color", "green");
                            $(".numerocliente1").css({ "border":"1px solid #0C0"}).fadeIn(2000);
                            $("#spanbuttonw").show();
                            
                            
                          }
                            else{$("#spaninputnum1").text("Registre un numero valido").css("color", "red");
                            $(".numerocliente1").css({ "border":"1px solid #C00"}).fadeIn(2000);
                            $("#spanbuttonw").hide();
                            
                            
                          }
            });
    $(".mailv").keyup(function(){
                            var txtmail = $(".mailv").val();
                            var valmail = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
                            if(valmail.test(txtmail)){
                                $("#spanmail").text("Valido").css("color", "green");
                            $(".mailv").css({ "border":"1px solid #0F0"}).fadeIn(2000);
                            $("#spanbuttone").show();}
                            else{$("#spanmail").text("Correo Incorrecto").css("color", "red");
                            $(".mailv").css({ "border":"1px solid #F00"}).fadeIn(2000);
                            $("#spanbuttone").hide();
                          }
                            });
   
   
   
 });
</script>
<div class="content-wrapper">
    
  <section class="content-header">
      
    <h1>
      Gestor usuarios
    </h1>
 
    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>

      <li class="active">Gestor usuarios</li>
      
    </ol>

  </section>

  <section class="content">

    <div class="box">  

      <div class="box-header with-border">

      </div>

      <div class="box-body">

        <div class="box-tools">

          <?php

            if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == 
              "Super-Administrador" || $_SESSION["perfil"] == "vendedor") {
              
              echo'<a href="vistas/modulos/descargar-reporte-Usuarios.php?reporte=usuariosTienda&empresa='.$_SESSION["empresa"].'">

                <button class="btn btn-success" style="margin-top:5px">Descargar reporte en Excel</button>

              </a>

               <a href="vistas/modulos/descargar-reporte-Usuarios.php?reportes=usuariosTiendaENT&empresa='.$_SESSION["empresa"].'">

               <button class="btn btn-success" style="margin-top:5px">Descargar reporte usuarios ENT</button>

              </a>';

            }

          ?>

     

        </div> 

         <div class="box-header with-border">
         
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarUsuario">

          
          Agregar Cliente

        </button>

      </div>


        <br>
         
        <table class="table table-bordered table-striped dt-responsive tablaClientesOrden" width="100%">

          <thead>
            
            <tr>
              
              <th style="width:10px">
              <th>Nombre</th>
              <th>Asesor</th>
              <th>Email</th>
              <th>Telefono*</th>
              <th>Whatsapp</th>
              <th>Acciones</th>
              <th>Fecha</th>

            </tr>

          </thead>

          <?php

           echo'

                 <input  type="hidden" id="tipoDePerfil" value="'.$_SESSION["perfil"].'"  placeholder="'.$_SESSION["perfil"].'">
                 <input  type="hidden" id="Empresa_del_perfil" value="'.$_SESSION["empresa"].'"  placeholder="'.$_SESSION["empresa"].'">';
          ?>

        </table> 

      </div>
        
    </div>

  </section>

</div>

<!--=====================================
MODAL AGREGAR CLIENTE
======================================-->

<div id="modalAgregarUsuario" class="modal fade" role="dialog">

  <div class="modal-dialog">
    
    <div class="modal-content" style="border-radius: 10px;">
      
      <form role="form" method="POST" enctype="multipart/form-data">
        
        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="border-radius: 10px 10px 0% 0%; background:#138a1e; color:white">
          
          <button type="button" class="close" data-dismiss="modal">

            &times;

          </button>

            <h4 class="modal-title">
              
              Agregar Usuario   

            </h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">
          
          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE -->

            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon" style="border-radius: 3px;"><i class="fas fa-user"></i></span>

                <input type="text"  class="form-control input-lg nombreCliente namecliente"  name="AgregarNombreCliente" placeholder="Nombre del Cliente" id="nombrecliente" style="border-radius: 3px;">

                <?php
                
                  echo'<input  type="hidden" value="'.$_SESSION["empresa"].'" name="id_empresa">';

                ?>
              </div>

            </div>            

            <!-- ENTRADA PARA EL CORREO -->

            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon" style="border-radius: 3px;"><i class="fas fa-at"></i></span>

                <input type="text"  style="border-radius: 3px;" class="form-control input-lg emailverif mailv" name="AgregarCorreoCliente" placeholder="Correo Del Cliente">
                <span id="spanmail"></span>

              </div>

            </div>  

            <!-- ENTRADA PARA EL TELEFONO DOS -->

             <div class="form-group">
              
              <div class="input-group">
              
                
                <span class="input-group-addon" style="border-radius: 3px;"><i class="fab fa-whatsapp"></i></span> 

                <input type="tel"  style="border-radius: 3px;" class="form-control input-lg telwhatsapp numerocliente1" id="whatsapp" name="telefonoDosCliente" placeholder="Ingresa Whatsapp" required>
                <span id="spaninputnum1"></span>

              </div>

            </div>     

            <!-- ENTRADA PARA EL TELEFONO DOS -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span style="border-radius: 3px;" class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                <input style="border-radius: 3px;" type="tel" class="form-control input-lg"  id="telefono" name="telefonoUnoCliente" placeholder="Ingresa Numero Telefonico" required>
                
              </div>

            </div>       
            <!--=========================
            AGREGAR ETIQUETA AL CLIENTE
            ============================-->
            <div class="form-group">
                <div class="input-group">
                    
                    <span class="input-group-addon">
                    
                        <i class="fas fa-tag"></i>

                    </span>

                    <select style="border-radius: 3px;" class="form-control input-lg" name="EtiquetaCliente">
                    
                        <option value="Nuevo">
                             <span class="label label-primary">Nuevo</span>
                        </option>
                        <option value="Frecuente">
                             <span class="label label-success">Frecuente</span>
                        </option>
                        <option value="Problematico">
                             <span class="label label-danger">Problematico</span>
                        </option>
                    </select>
                    
                </div>
            </div>

            <!-- ENTRADA PARA EL NOMBRE DEL ASESOR -->

            <div class="input-group">
              
              <span class="input-group-addon">
                
                <i class="fas fa-headphones"></i>

              </span>

              <select style="border-radius: 3px;" class="form-control input-lg" name="AgreagrAsesorAlCliente">
                
                <option value="" id="AgreagrAsesorAlCliente">
                  
                  Seleccionar Asesor

                </option>
                 <?php
                      
                      $item = null;
                      $valor = null;

                      $Asesores= Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);

                      foreach ($Asesores as $key => $value) {
                        
                        echo '

                        <option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                      }
               ?>


              </select>
              

            </div>
            <hr>
              <br>
              <center><h2>VERIFICAR CLIENTE</h2>
              
              <span id="spanbuttonw"></span>
              <br><br>
              <span id="spanbuttone"></span>
              </center>

          </div>

          <!--=====================================
          PIE DEL MODAL
          ======================================-->
    
          <div class="modal-footer">

            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

            <button type="submit"  class="btn btn-primary" id="guardar">Agregar Cliente</button>

          </div>

        </div>
      <?php
          
         $AgregarCliente = new ControladorClientes();
         $AgregarCliente -> ctrMostrarAgregarCliente();

        ?>
      </form>

    </div>

  </div>

</div>


<!--=====================================
MODAL EDITAR CLIENTE
======================================-->

<div id="btnEditarCliente" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content" style="border-radius: 10px;">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="border-radius: 10px 10px 0% 0%; background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Editar Cliente</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span style="border-radius: 3px;" class="input-group-addon"><i class="fas fa-user"></i></span> 

                <input type="text" class="form-control input-lg" name="editarNombreDelCliente" id="editarNombreDelCliente" placeholder="Ingresar nombre del Cliente" >

                <input type="hidden" id="idCliente" name="idCliente">

              </div>

            </div>

             <!-- ENTRADA PARA EL EMAIL -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span style="border-radius: 3px;" class="input-group-addon "><i class="fas fa-envelope"></i></span> 

                <input style="border-radius: 3px;" type="email" class="form-control input-lg" name="EditarCorreoCliente" placeholder="Ingresar Email deL Asesor" id="EditarCorreoCliente" required>

              </div>

            </div>
           <!-- ENTRADA PARA El  NUMERO TELEFONICO UNO -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span style="border-radius: 3px;" class="input-group-addon"><i class="fas fa-phone-square-alt"></i></span> 

                <input style="border-radius: 3px;" type="tel" class="form-control input-lg" name="EditarNumeroDelCliente" id="EditarNumeroDelCliente" placeholder="Ingresa Numero Telefonico" required>

              </div>

            </div>
            <!-- ENTRADA PARA EL TELEFONO DOS -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span style="border-radius: 3px;" class="input-group-addon"><i class="fab fa-whatsapp-square"></i></span> 

                <input style="border-radius: 3px;" type="tel" class="form-control input-lg" id="EditarSegundoNumeroDeTel" name="EditarSegundoNumeroDeTel" placeholder="Ingresa Numero Telefonico 2" required>

              </div>

            </div>
              <!-- ENTRADA PARA EL NOMBRE DEL ASESOR -->
            <div class="form-group">
              <div class="input-group">
                
                <span class="input-group-addon">
                  
                  <i class="fas fa-tag"></i>

                </span>

                <select style="border-radius: 3px;" class="form-control input-lg EditarEtiquetaDelCLiente" name="EditarEtiquetaDelCLiente">
                  
                  <option class="MostrarEtiquetaDelCliente"></option>

                  <option value="Nuevo">
                    <span class="label label-primary">Nuevo</span>
                  </option>

                  <option value="Frecuente">
                    <span class="label label-success">Frecuente</span>
                  </option>

                  <option value="Problematico">
                    <span class="label label-danger">Problematico</span>
                  </option>

                </select>

              </div>
            </div>
              <!-- ENTRADA PARA EL NOMBRE DEL ASESOR -->

            <div class="input-group">
              
              <span class="input-group-addon">
                
                <i class="fas fa-headphones"></i>

              </span>

              <select style="border-radius: 3px;" class="form-control input-lg" name="EditarAsesorDelCliente"<?php if($_SESSION["perfil"] == "vendedor"){ echo'disabled';}?>>
                
                <option value="" id="EditarAsesorDelCliente" >
                  
                  Seleccionar Asesor

                </option>
                 <?php
                      
                      $item = null;
                      $valor = null;

                      $Asesores= Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);

                      foreach ($Asesores as $key => $value) {
                        
                        echo '

                        <option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                      }
               ?>


              </select>

            </div>
  
          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->
  
        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Modificar Cliente</button>

        </div>

     <?php

          $editarPerfil = new ControladorClientes();
          $editarPerfil -> ctrEditarCliente();

        ?> 

      </form>

    </div>

  </div>

</div>

<?php

 // $eliminarAsesor = new Controladorasesores();
  //$eliminarAsesor -> ctrEliminarAsesor();

