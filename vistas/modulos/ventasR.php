<?php
if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "secretaria"  AND $_SESSION["perfil"]!= "Super-Administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}

$esVendedorVentasR = ($_SESSION["perfil"] === "vendedor");

?>

<script>
   $(document).ready(function(){ 
 //Ocultar todos los productos
    $("#productouno").hide();
    $("#productodos").hide();
    $("#productotres").hide();
    $("#productocuatro").hide();
    $("#productocinco").hide();
    $("#productoseis").hide();
    $("#productosiete").hide();
    $("#productoocho").hide();
    $("#productonueve").hide();
    $("#productodiez").hide();
    $(".METODOPAGO").hide();
    $("#CAMBIORE").hide();
    $("#Caltotal").hide();
    $("#completarventa").hide();
//Ocultar o mostrar productos segun el select de productos
    $("#selprod").change(function(){
            var asignar= $(this).val();
            parseInt(asignar)
            if(asignar== 1){
                $("#productouno").show();
                $("#productodos").hide();
                $("#productotres").hide();
                $("#productocuatro").hide();
                $("#productocinco").hide();
                $("#productoseis").hide();
                $("#productosiete").hide();
                $("#productoocho").hide();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if(asignar== 2){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").hide();
                $("#productocuatro").hide();
                $("#productocinco").hide();
                $("#productoseis").hide();
                $("#productosiete").hide();
                $("#productoocho").hide();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if(asignar== 3){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").hide();
                $("#productocinco").hide();
                $("#productoseis").hide();
                $("#productosiete").hide();
                $("#productoocho").hide();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if(asignar== 4){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").show();
                $("#productocinco").hide();
                $("#productoseis").hide();
                $("#productosiete").hide();
                $("#productoocho").hide();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if(asignar== 5){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").show();
                $("#productocinco").show();
                $("#productoseis").hide();
                $("#productosiete").hide();
                $("#productoocho").hide();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if(asignar== 6){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").show();
                $("#productocinco").show();
                $("#productoseis").show();
                $("#productosiete").hide();
                $("#productoocho").hide();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if(asignar== 7){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").show();
                $("#productocinco").show();
                $("#productoseis").show();
                $("#productosiete").show();
                $("#productoocho").hide();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if(asignar== 8){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").show();
                $("#productocinco").show();
                $("#productoseis").show();
                $("#productosiete").show();
                $("#productoocho").show();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if(asignar== 9){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").show();
                $("#productocinco").show();
                $("#productoseis").show();
                $("#productosiete").show();
                $("#productoocho").show();
                $("#productonueve").show();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if(asignar== 10){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").show();
                $("#productocinco").show();
                $("#productoseis").show();
                $("#productosiete").show();
                $("#productoocho").show();
                $("#productonueve").show();
                $("#productodiez").show();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }
    })
// Cambiar el value del select con botones (+)
$('.plus').click(function(){
        //Aumentamos el valor del campo
        $('#selprod').val(parseInt($('#selprod').val()) + 1);
        if($("#selprod").val()==1){
                $("#productouno").show();
                $("#productodos").hide();
                $("#productotres").hide();
                 $("#productocuatro").hide();
                $("#productocinco").hide();
                $("#productoseis").hide();
                $("#productosiete").hide();
                $("#productoocho").hide();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if($("#selprod").val()==2){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").hide();
                $("#productocuatro").hide();
                $("#productocinco").hide();
                $("#productoseis").hide();
                $("#productosiete").hide();
                $("#productoocho").hide();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if($("#selprod").val()==3){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").hide();
                $("#productocinco").hide();
                $("#productoseis").hide();
                $("#productosiete").hide();
                $("#productoocho").hide();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if($("#selprod").val()==4){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").show();
                $("#productocinco").hide();
                $("#productoseis").hide();
                $("#productosiete").hide();
                $("#productoocho").hide();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if($("#selprod").val()==5){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").show();
                $("#productocinco").show();
                $("#productoseis").hide();
                $("#productosiete").hide();
                $("#productoocho").hide();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if($("#selprod").val()==6){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").show();
                $("#productocinco").show();
                $("#productoseis").show();
                $("#productosiete").hide();
                $("#productoocho").hide();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if($("#selprod").val()==7){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").show();
                $("#productocinco").show();
                $("#productoseis").show();
                $("#productosiete").show();
                $("#productoocho").hide();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if($("#selprod").val()==8){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").show();
                $("#productocinco").show();
                $("#productoseis").show();
                $("#productosiete").show();
                $("#productoocho").show();
                $("#productonueve").hide();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if($("#selprod").val()==9){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").show();
                $("#productocinco").show();
                $("#productoseis").show();
                $("#productosiete").show();
                $("#productoocho").show();
                $("#productonueve").show();
                $("#productodiez").hide();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }if($("#selprod").val()==10){
                $("#productouno").show();
                $("#productodos").show();
                $("#productotres").show();
                $("#productocuatro").show();
                $("#productocinco").show();
                $("#productoseis").show();
                $("#productosiete").show();
                $("#productoocho").show();
                $("#productonueve").show();
                $("#productodiez").show();
                $(".METODOPAGO").show();
                $("#Caltotal").show();
                $("#completarventa").show();
            }
    })
// Quitar producto por id con boton y sus valores
$('#minus1').click(function(){
 $("#productouno").hide();
  $("#cantidadUno").val(0);
  $("#multiplo1").val(0);
  $("#precioUno").val(0);
  $("#productoUno").val("");
    })
$('#minus2').click(function(){
 $("#productodos").hide();
 $("#cantidadDos").val(0);
 $("#multiplo2").val(0);
 $("#precioDos").val(0);
 $("#productoDos").val("");
    })
$('#minus3').click(function(){
 $("#productotres").hide();
 $("#cantidadTres").val(0);
 $("#multiplo3").val(0);
 $("#precioTres").val(0);
 $("#productoTres").val("");
    })
$('#minus4').click(function(){
 $("#productocuatro").hide();
 $("#cantidadCuatro").val(0);
 $("#multiplo4").val(0);
 $("#precioCuatro").val(0);
 $("#productoCuatro").val("");
    })
$('#minus5').click(function(){
 $("#productocinco").hide();
 $("#cantidadCinco").val(0);
 $("#multiplo5").val(0);
 $("#precioCinco").val(0);
 $("#productoCinco").val("");
    })
$('#minus6').click(function(){
 $("#productoseis").hide();
 $("#cantidadSeis").val(0);
 $("#multiplo6").val(0);
 $("#precioSeis").val(0);
 $("#productoSeis").val("");
    })
$('#minus7').click(function(){
 $("#productosiete").hide();
 $("#cantidadSiete").val(0);
 $("#multiplo7").val(0);
 $("#precioSiete").val(0);
 $("#productoSiete").val("");
    })
$('#minus8').click(function(){
 $("#productoocho").hide();
 $("#cantidadOcho").val(0);
 $("#multiplo8").val(0);
 $("#precioOcho").val(0);
 $("#productoOcho").val("");
    })
$('#minus9').click(function(){
 $("#productonueve").hide();
 $("#cantidadNueve").val(0);
 $("#multiplo9").val(0);
 $("#precioNueve").val(0);
 $("#productoNueve").val("");
    })
$('#minus10').click(function(){
 $("#productodiez").hide();
 $("#cantidadDiez").val(0);
 $("#multiplo10").val(0);
 $("#precioDiez").val(0);
 $("#productoDiez").val("");
    })


    
// Multiplicar precio segun cantidad
 $('#cantidadUno, #multiplo1').change(function() {
     var cantidadUno = $("#cantidadUno").val();
     var precioUno = $("#precioUno").val();  
     var multiplo1 = Number(cantidadUno) * Number(precioUno);
     $("#multiplo1").val(multiplo1);
 })
 $('#cantidadDos, #multiplo2').change(function() {
     var cantidadDos = $("#cantidadDos").val();
     var precioDos = $("#precioDos").val();  
     var multiplo2 = Number(cantidadDos) * Number(precioDos);
     $("#multiplo2").val(multiplo2);
 })
 $('#cantidadTres, #multiplo3').change(function() {
     var cantidadTres = $("#cantidadTres").val();
     var precioTres = $("#precioTres").val();  
     var multiplo3 = Number(cantidadTres) * Number(precioTres);
     $("#multiplo3").val(multiplo3);
 })
  $('#cantidadCuatro, #multiplo4').change(function() {
     var cantidadCuatro = $("#cantidadCuatro").val();
     var precioCuatro = $("#precioCuatro").val();  
     var multiplo4 = Number(cantidadCuatro) * Number(precioCuatro);
     $("#multiplo4").val(multiplo4);
 })
 
 $('#cantidadCinco, #multiplo5').change(function() {
     var cantidadCinco = $("#cantidadCinco").val();
     var precioCinco = $("#precioCinco").val();  
     var multiplo5 = Number(cantidadCinco) * Number(precioCinco);
     $("#multiplo5").val(multiplo5);
 })
 
  $('#cantidadSeis, #multiplo6').change(function() {
     var cantidadSeis = $("#cantidadSeis").val();
     var precioSeis = $("#precioSeis").val();  
     var multiplo6 = Number(cantidadSeis) * Number(precioSeis);
     $("#multiplo6").val(multiplo6);
 })
  $('#cantidadSiete, #multiplo7').change(function() {
     var cantidadSiete = $("#cantidadSiete").val();
     var precioSiete = $("#precioSiete").val();  
     var multiplo7 = Number(cantidadSiete) * Number(precioSiete);
     $("#multiplo7").val(multiplo7);
 })
  $('#cantidadOcho, #multiplo8').change(function() {
     var cantidadOcho = $("#cantidadOcho").val();
     var precioOcho = $("#precioOcho").val();  
     var multiplo8 = Number(cantidadOcho) * Number(precioOcho);
     $("#multiplo8").val(multiplo8);
 })
  $('#cantidadNueve, #multiplo9').change(function() {
     var cantidadNueve = $("#cantidadNueve").val();
     var precioNueve = $("#precioNueve").val();  
     var multiplo9 = Number(cantidadNueve) * Number(precioNueve);
     $("#multiplo9").val(multiplo9);
 })
  $('#cantidadDiez, #multiplo10').change(function() {
     var cantidadDiez = $("#cantidadDiez").val();
     var precioDiez = $("#precioDiez").val();  
     var multiplo10 = Number(cantidadDiez) * Number(precioDiez);
     $("#multiplo10").val(multiplo10);
 })
 
// Sumar segun los cambios en los precios
              $("#cantidadUno,#cantidadDos,#cantidadTres,#cantidadCuatro,#cantidadCinco,#cantidadSeis,#cantidadSiete,#cantidadOcho,#cantidadNueve,#cantidadDiez").change(function() {
             var multiplo1= $("#multiplo1").val();
             var multiplo2= $("#multiplo2").val();
             var multiplo3= $("#multiplo3").val();
             var multiplo4= $("#multiplo4").val();
             var multiplo5= $("#multiplo5").val();
             var multiplo6= $("#multiplo6").val();
             var multiplo7= $("#multiplo7").val();
             var multiplo8= $("#multiplo8").val();
             var multiplo9= $("#multiplo9").val();
             var multiplo10= $("#multiplo10").val();
             var resultado= Number(multiplo1) + Number(multiplo2) + Number(multiplo3)+ Number(multiplo4)+ Number(multiplo5)+ Number(multiplo6)+ Number(multiplo7)+ Number(multiplo8)+ Number(multiplo9)+ Number(multiplo10);
              $("#Resultado").val(resultado);
              $("#Resultado").addClass('positivo');
        setTimeout(function () {
            $("#Resultado").removeClass('positivo');
        }, 2000);
              
               // Sumar cantidades
             var Uno= $("#cantidadUno").val();
             var Dos= $("#cantidadDos").val();
             var Tres= $("#cantidadTres").val();
             var Cuatro= $("#cantidadCuatro").val();
             var Cinco= $("#cantidadCinco").val();
             var Seis= $("#cantidadSeis").val();
             var Siete= $("#cantidadSiete").val();
             var Ocho= $("#cantidadOcho").val();
             var Nueve= $("#cantidadNueve").val();
             var Diez= $("#cantidadDiez").val();
             var resultado2= Number(Uno) + Number(Dos) + Number(Tres)+ Number(Cuatro)+ Number(Cinco)+ Number(Seis)+ Number(Siete)+ Number(Ocho)+ Number(Nueve)+ Number(Diez);
              $("#cantidadProductos").val(resultado2);
})
 $(".minus").click(function() {
             var multiplo1= $("#multiplo1").val();
             var multiplo2= $("#multiplo2").val();
             var multiplo3= $("#multiplo3").val();
             var multiplo4= $("#multiplo4").val();
             var multiplo5= $("#multiplo5").val();
             var multiplo6= $("#multiplo6").val();
             var multiplo7= $("#multiplo7").val();
             var multiplo8= $("#multiplo8").val();
             var multiplo9= $("#multiplo9").val();
             var multiplo10= $("#multiplo10").val();
             var resultado= Number(multiplo1) + Number(multiplo2) + Number(multiplo3)+ Number(multiplo4)+ Number(multiplo5)+ Number(multiplo6)+ Number(multiplo7)+ Number(multiplo8)+ Number(multiplo9)+ Number(multiplo10);
              $("#Resultado").val(resultado);
              $("#Resultado").addClass('negativo');
        setTimeout(function () {
            $("#Resultado").removeClass('negativo');
        }, 2000);
              
               // Sumar cantidades
             var Uno= $("#cantidadUno").val();
             var Dos= $("#cantidadDos").val();
             var Tres= $("#cantidadTres").val();
             var Cuatro= $("#cantidadCuatro").val();
             var Cinco= $("#cantidadCinco").val();
             var Seis= $("#cantidadSeis").val();
             var Siete= $("#cantidadSiete").val();
             var Ocho= $("#cantidadOcho").val();
             var Nueve= $("#cantidadNueve").val();
             var Diez= $("#cantidadDiez").val();
             var resultado2= Number(Uno) + Number(Dos) + Number(Tres)+ Number(Cuatro)+ Number(Cinco)+ Number(Seis)+ Number(Siete)+ Number(Ocho)+ Number(Nueve)+ Number(Diez);
              $("#cantidadProductos").val(resultado2);
              
              
})
// Cambio a dar al cliente
 $('#pagoCliente').keyup(function() {
     var dinerocliente = $("#pagoCliente").val();
     var clientepaga = $("#Resultado").val();  
     var cambio = Number(dinerocliente) - Number(clientepaga);
     $("#cambio").val(cambio);
 })
// Metodo pago en efectivo
$('#CAMBIORE').hide();
  $('#metodopago').change(function() {
     if($('#metodopago').val()=="Efectivo"){
         $('#CAMBIORE').show();
         
     }else{
         $('#CAMBIORE').hide();
     }
     
     
 });
 // EVITAR USO DE CARACTERES ESPECIALES
const $input2 = document.querySelector('#productoUno');
    const patron2 = /[0-9a-zA-ZñÑ ÉÁÍÓ().-]+/;
    $input2.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron2.test(event.key)){
                    
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();
                    }
                }
            });
const $input3 = document.querySelector(' #productoDos');
    $input3.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron2.test(event.key)){
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
const $input4 = document.querySelector(' #productoTres');
    $input4.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron2.test(event.key)){
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
const $input5 = document.querySelector(' #productoCuatro');
    $input5.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron2.test(event.key)){
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
const $input6 = document.querySelector(' #productoCinco');
    $input6.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron2.test(event.key)){
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
const $input7 = document.querySelector(' #productoSeis');
    $input7.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron2.test(event.key)){
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
const $input8 = document.querySelector(' #productoSiete');
    $input8.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron2.test(event.key)){
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
const $input9 = document.querySelector(' #productoOcho');
    $input9.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron2.test(event.key)){
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
const $input10 = document.querySelector(' #productoNueve');
    $input10.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron2.test(event.key)){
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
const $input11 = document.querySelector(' #productoDiez');
    $input11.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron2.test(event.key)){
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
 
            

   });
 </script> 
<style>
    /* ─── Base helpers ─── */
    .productosp{ margin-left:-100px; }
    .circulo{ margin-top:8px; }
    .negativo{ border-color: #ef4444 !important; }
    .positivo{ border-color: #22c55e !important; }
    #pagoCliente, #Resultado, #cambio{ font-size: 40px; }
    .plus{ margin-left:20px; }
    .btn-circle {
      width: 30px; height: 30px; padding: 6px 0px;
      border-radius: 15px; text-align: center;
      font-size: 12px; line-height: 1.42857;
    }

    /* ─── CRM Design Tokens (match inicio.php) ─── */
    :root {
      --crm-bg:       #f8fafc;
      --crm-surface:  #ffffff;
      --crm-border:   #e2e8f0;
      --crm-text:     #0f172a;
      --crm-text2:    #475569;
      --crm-muted:    #94a3b8;
      --crm-accent:   #6366f1;
      --crm-accent2:  #818cf8;
      --crm-radius:   14px;
      --crm-radius-sm:10px;
      --crm-shadow:   0 1px 3px rgba(15,23,42,.06), 0 4px 14px rgba(15,23,42,.04);
      --crm-shadow-lg:0 4px 24px rgba(15,23,42,.10);
      --crm-ease:     cubic-bezier(.4,0,.2,1);
    }

    /* ─── KPI Cards ─── */
    .vr-kpi-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 14px;
      margin-bottom: 20px;
    }
    .vr-kpi {
      border-radius: var(--crm-radius);
      padding: 22px 20px 18px;
      position: relative; overflow: hidden;
      color: #fff;
      transition: transform .2s var(--crm-ease), box-shadow .2s var(--crm-ease);
    }
    .vr-kpi:hover { transform: translateY(-3px); box-shadow: var(--crm-shadow-lg); }
    .vr-kpi-icon {
      position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
      font-size: 48px; opacity: .12;
    }
    .vr-kpi-label {
      font-size: 11px; font-weight: 600; text-transform: uppercase;
      letter-spacing: .5px; opacity: .85; margin-bottom: 6px;
    }
    .vr-kpi-value {
      font-size: 28px; font-weight: 800; line-height: 1.1; margin-bottom: 0;
      letter-spacing: -.02em;
    }

    /* ─── Toolbar actions ─── */
    .vr-toolbar {
      display: flex; flex-wrap: wrap; justify-content: space-between;
      align-items: center; gap: 14px; margin-bottom: 20px;
    }
    .vr-primary-actions {
      display: flex; flex-wrap: wrap; gap: 10px; align-items: center;
    }
    .vr-primary-actions .btn {
      border-radius: var(--crm-radius-sm); font-weight: 600;
      box-shadow: var(--crm-shadow);
      transition: all .18s var(--crm-ease);
    }
    .vr-primary-actions .btn:hover {
      transform: translateY(-1px); box-shadow: var(--crm-shadow-lg);
    }
    .vr-btn-main {
      min-height: 42px; padding: 8px 18px;
      display: inline-flex; align-items: center; gap: 8px;
      font-size: 13px; letter-spacing: .2px;
    }

    /* ─── Date filters ─── */
    .vr-filtro-fechas {
      background: var(--crm-surface);
      border: 1px solid var(--crm-border);
      border-radius: var(--crm-radius);
      padding: 12px 16px;
      display: flex; flex-wrap: wrap; gap: 10px;
      align-items: center; justify-content: flex-end;
      max-width: 720px; width: 100%;
      box-shadow: var(--crm-shadow);
    }
    .vr-filtro-fechas .form-control {
      min-width: 148px; border-radius: var(--crm-radius-sm);
      border: 1px solid var(--crm-border); box-shadow: none;
      font-size: 12px; height: 36px; font-weight: 500;
      transition: border-color .15s, box-shadow .15s;
    }
    .vr-filtro-fechas .form-control:focus {
      border-color: var(--crm-accent); box-shadow: 0 0 0 3px rgba(99,102,241,.12);
    }
    .vr-date-label {
      font-size: 11px; font-weight: 700; color: var(--crm-text2);
      text-transform: uppercase; letter-spacing: .6px;
    }
    .vr-filtro-presets {
      display: inline-flex; gap: 6px; flex-wrap: wrap;
    }
    .vr-filtro-presets .btn,
    .vr-filtro-fechas > .btn {
      border-radius: var(--crm-radius-sm); padding: 6px 12px;
      font-size: 12px; font-weight: 600; box-shadow: none;
      border: 1px solid var(--crm-border);
      transition: all .15s var(--crm-ease);
    }
    .vr-filtro-presets .btn:hover,
    .vr-filtro-fechas > .btn:hover {
      border-color: var(--crm-accent); background: #eef2ff; color: #3730a3;
    }
    @media (max-width: 991px) {
      .vr-toolbar { flex-direction: column; align-items: stretch; }
      .vr-filtro-fechas { justify-content: flex-start; max-width: 100%; }
    }

    /* ─── Table card ─── */
    .vr-table-card {
      background: var(--crm-surface);
      border: 1px solid var(--crm-border);
      border-radius: var(--crm-radius);
      box-shadow: var(--crm-shadow);
      overflow: hidden;
      transition: box-shadow .2s var(--crm-ease);
    }
    .vr-table-card:hover { box-shadow: var(--crm-shadow-lg); }
    .vr-table-card-head {
      display: flex; align-items: center; justify-content: space-between;
      padding: 16px 20px 12px;
      border-bottom: 1px solid #f1f5f9;
    }
    .vr-table-card-title {
      display: flex; align-items: center; gap: 10px;
      font-size: 14px; font-weight: 700; color: var(--crm-text);
      margin: 0; line-height: 1.3;
    }
    .vr-table-card-title i {
      font-size: 15px; color: var(--crm-accent); opacity: .85;
    }
    .vr-table-card-body { padding: 16px 20px; }

    /* ─── DataTable overrides ─── */
    #tablaVentasRapidasUI thead th {
      position: sticky; top: 0; z-index: 2;
      background: #f8fafc;
      padding: 12px 16px; font-size: 10px; font-weight: 700;
      text-transform: uppercase; letter-spacing: .6px;
      color: var(--crm-muted);
      border-bottom: 1px solid var(--crm-border);
      box-shadow: 0 1px 0 rgba(15,23,42,.04);
      white-space: nowrap;
    }
    #tablaVentasRapidasUI.dataTable thead .sorting,
    #tablaVentasRapidasUI.dataTable thead .sorting_asc,
    #tablaVentasRapidasUI.dataTable thead .sorting_desc,
    #tablaVentasRapidasUI.dataTable thead .sorting_asc_disabled,
    #tablaVentasRapidasUI.dataTable thead .sorting_desc_disabled,
    #tablaVentasRapidasUI.dataTable thead .sorting_disabled {
      background-image: none !important; padding-right: 8px !important;
    }
    #tablaVentasRapidasUI.dataTable thead .sorting::before,
    #tablaVentasRapidasUI.dataTable thead .sorting::after,
    #tablaVentasRapidasUI.dataTable thead .sorting_asc::before,
    #tablaVentasRapidasUI.dataTable thead .sorting_asc::after,
    #tablaVentasRapidasUI.dataTable thead .sorting_desc::before,
    #tablaVentasRapidasUI.dataTable thead .sorting_desc::after,
    #tablaVentasRapidasUI.dataTable thead .sorting_asc_disabled::before,
    #tablaVentasRapidasUI.dataTable thead .sorting_asc_disabled::after,
    #tablaVentasRapidasUI.dataTable thead .sorting_desc_disabled::before,
    #tablaVentasRapidasUI.dataTable thead .sorting_desc_disabled::after,
    #tablaVentasRapidasUI.dataTable thead .sorting_disabled::before,
    #tablaVentasRapidasUI.dataTable thead .sorting_disabled::after {
      display: none !important; content: none !important;
    }
    #tablaVentasRapidasUI tbody tr {
      transition: background .12s;
    }
    #tablaVentasRapidasUI tbody tr:hover { background: #f4f7ff !important; }
    #tablaVentasRapidasUI tbody td {
      padding: 12px 16px; font-size: 13px; color: var(--crm-text);
      border-bottom: 1px solid #f1f5f9; vertical-align: middle;
    }
    #tablaVentasRapidasUI.dataTable.stripe tbody tr.odd,
    #tablaVentasRapidasUI.dataTable.display tbody tr.odd,
    #tablaVentasRapidasUI.table-striped > tbody > tr:nth-of-type(odd) {
      background-color: #fbfdff;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_length,
    #tablaVentasRapidasUI_wrapper .dataTables_filter {
      margin-bottom: 12px;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_length label,
    #tablaVentasRapidasUI_wrapper .dataTables_filter label {
      color: var(--crm-text2); font-size: 12px; font-weight: 700; letter-spacing: .2px;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_length select {
      border: 1px solid var(--crm-border) !important;
      border-radius: 8px; background: #fff; color: #334155;
      height: 34px; padding: 4px 26px 4px 10px; margin: 0 6px;
      font-size: 12px; font-weight: 600;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_filter input {
      border: 1px solid var(--crm-border) !important;
      border-radius: var(--crm-radius-sm); background: #fff; color: #334155;
      height: 36px; min-width: 220px; padding: 6px 12px;
      font-size: 12px; font-weight: 600; transition: all .15s ease;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_length select:focus,
    #tablaVentasRapidasUI_wrapper .dataTables_filter input:focus {
      outline: none;
      border-color: var(--crm-accent) !important;
      box-shadow: 0 0 0 3px rgba(99,102,241,.12);
    }
    #tablaVentasRapidasUI_wrapper .dataTables_paginate { margin-top: 14px; }
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button > a {
      border-radius: 8px !important;
      border: 1px solid var(--crm-border) !important;
      background: #fff !important; color: #334155 !important;
      margin-left: 6px; padding: 6px 12px !important;
      font-weight: 600; transition: all .15s ease;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button > a:hover {
      background: #eef2ff !important;
      border-color: var(--crm-accent) !important;
      color: #3730a3 !important;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button.active > a,
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button.active > a:hover,
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button.active > a:focus {
      background: var(--crm-accent) !important;
      border-color: var(--crm-accent) !important;
      color: #fff !important;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button.disabled > a,
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button.disabled > a:hover,
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button.disabled > a:focus {
      background: #f8fafc !important; border-color: #e2e8f0 !important;
      color: #94a3b8 !important; cursor: not-allowed;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button {
      background: transparent !important; border: 0 !important; box-shadow: none !important;
    }

    /* ─── Modal ─── */
    #modalAgregarVenta .modal-content {
      border-radius: var(--crm-radius); border: 1px solid var(--crm-border);
      box-shadow: 0 20px 60px rgba(15,23,42,.28); overflow: hidden;
    }
    #modalAgregarVenta .modal-header {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 40%, #334155 100%) !important;
      border-bottom: 0; padding: 18px 22px;
    }
    #modalAgregarVenta .modal-title {
      font-weight: 800; letter-spacing: -.01em; font-size: 16px;
    }
    #modalAgregarVenta .modal-body {
      background: var(--crm-bg); padding: 20px;
    }
    #modalAgregarVenta .box-body {
      background: var(--crm-surface);
      border: 1px solid var(--crm-border);
      border-radius: var(--crm-radius); padding: 20px;
    }

    /* ─── Inputs & Selects refinados ─── */
    #modalAgregarVenta .form-group { margin-bottom: 12px; }

    #modalAgregarVenta .input-group-addon {
      background: #f8fafc; color: var(--crm-text2);
      border-color: var(--crm-border); font-weight: 600;
      font-size: 12px; padding: 6px 12px;
      min-width: 42px; text-align: center;
    }
    #modalAgregarVenta .form-control {
      border-color: var(--crm-border); border-radius: 8px; box-shadow: none;
      transition: border-color .15s, box-shadow .15s;
      font-size: 13px; height: 38px; padding: 6px 12px;
      color: var(--crm-text);
    }
    #modalAgregarVenta .form-control:focus {
      border-color: var(--crm-accent);
      box-shadow: 0 0 0 3px rgba(99,102,241,.12);
    }
    #modalAgregarVenta select.form-control {
      appearance: none; -webkit-appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23475569' viewBox='0 0 16 16'%3E%3Cpath d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 32px;
    }
    #modalAgregarVenta .form-control::placeholder {
      color: var(--crm-muted); font-weight: 400;
    }
    /* Quitar input-lg overrides dentro del modal */
    #modalAgregarVenta .input-lg,
    #modalAgregarVenta .form-control.input-lg {
      height: 38px; font-size: 13px; padding: 6px 12px;
      border-radius: 8px; line-height: 1.5;
    }

    /* ─── Input groups: bordes limpios ─── */
    #modalAgregarVenta .input-group .input-group-addon:first-child {
      border-radius: 8px 0 0 8px; border-right: 0;
    }
    #modalAgregarVenta .input-group .form-control:last-child,
    #modalAgregarVenta .input-group .form-control:not(:first-child) {
      border-radius: 0 8px 8px 0;
    }
    #modalAgregarVenta .input-group .input-group-addon + .form-control {
      border-left: 1px solid var(--crm-border);
    }

    /* ─── Producto rows compactas ─── */
    #modalAgregarVenta .form-group.row { margin-left: -6px; margin-right: -6px; }
    #modalAgregarVenta .form-group.row > [class*="col-"] { padding-left: 6px; padding-right: 6px; }

    /* ─── Section divider de productos ─── */
    #modalAgregarVenta .vr-products-header {
      display: flex; align-items: center; gap: 10px;
      margin: 16px 0 12px; padding: 0;
    }
    #modalAgregarVenta .vr-products-header h5 {
      margin: 0; font-size: 13px; font-weight: 700;
      color: var(--crm-text); text-transform: uppercase;
      letter-spacing: .5px;
    }
    #modalAgregarVenta .vr-products-header::after {
      content: ''; flex: 1; height: 1px; background: var(--crm-border);
    }

    /* ─── Total section ─── */
    #modalAgregarVenta #Caltotal {
      background: linear-gradient(90deg, #eef2ff 0%, #f0fdf4 100%);
      border: 1px dashed #c7d2fe; border-radius: var(--crm-radius);
      padding: 16px 0; margin-top: 8px;
    }
    #modalAgregarVenta #Resultado,
    #modalAgregarVenta #pagoCliente,
    #modalAgregarVenta #cambio {
      font-size: 32px !important; font-weight: 800; color: var(--crm-text);
      text-align: center; letter-spacing: -.02em;
      height: auto !important;
    }

    /* ─── Payment method ─── */
    #modalAgregarVenta .METODOPAGO { margin-top: 12px; }

    /* ─── Footer ─── */
    #modalAgregarVenta .modal-footer {
      border-top: 1px solid #f1f5f9; background: var(--crm-bg);
      padding: 14px 22px;
    }
    #modalAgregarVenta .modal-footer .btn {
      border-radius: var(--crm-radius-sm); font-weight: 600; min-width: 130px;
      transition: all .18s var(--crm-ease);
    }
    #modalAgregarVenta .modal-footer .btn:hover {
      transform: translateY(-1px); box-shadow: var(--crm-shadow-lg);
    }
    #modalAgregarVenta .modal-footer .btn-primary {
      background: linear-gradient(135deg, var(--crm-accent), var(--crm-accent2));
      border: none;
    }
    #modalAgregarVenta .modal-footer .btn-primary:hover {
      background: linear-gradient(135deg, #4f46e5, #6366f1);
    }

    /* ─── Nuevo cliente section ─── */
    #nuevoClienteSection {
      border-radius: var(--crm-radius-sm) !important;
      border-color: #a5b4fc !important;
      background: #eef2ff !important;
    }
    #nuevoClienteSection .input-group-addon {
      background: #e0e7ff; border-color: #a5b4fc; color: #3730a3;
    }

    /* ─── Dinero electrónico section ─── */
    #egs_deVentaR_section {
      border-radius: var(--crm-radius-sm) !important;
    }

    /* ─── Choices.js dentro del modal ─── */
    #modalAgregarVenta .egs-cliente-choices-wrap .choices {
      width: 100%;
    }
    #modalAgregarVenta .egs-cliente-choices-wrap .choices__inner {
      background: var(--crm-surface) !important;
      border: 1px solid var(--crm-border) !important;
      border-radius: 8px !important;
      min-height: 38px !important;
      padding: 4px 8px !important;
      font-size: 13px !important;
      box-shadow: none !important;
      transition: border-color .15s, box-shadow .15s;
    }
    #modalAgregarVenta .egs-cliente-choices-wrap .choices.is-open .choices__inner,
    #modalAgregarVenta .egs-cliente-choices-wrap .choices.is-focused .choices__inner {
      border-color: var(--crm-accent) !important;
      box-shadow: 0 0 0 3px rgba(99,102,241,.12) !important;
    }
    #modalAgregarVenta .egs-cliente-choices-wrap .choices__list--single .choices__item {
      color: var(--crm-text) !important;
      font-size: 13px !important;
    }
    #modalAgregarVenta .egs-cliente-choices-wrap .choices__placeholder {
      color: var(--crm-muted) !important;
      opacity: 1 !important;
    }
    #modalAgregarVenta .egs-cliente-choices-wrap .choices__input {
      background: transparent !important;
      color: var(--crm-text) !important;
      font-size: 13px !important;
      padding: 0 !important;
      margin-bottom: 0 !important;
    }
    #modalAgregarVenta .egs-cliente-choices-wrap .choices__list--dropdown,
    #modalAgregarVenta .egs-cliente-choices-wrap .choices__list[aria-expanded] {
      background: var(--crm-surface) !important;
      border: 1px solid var(--crm-border) !important;
      border-radius: 0 0 8px 8px !important;
      box-shadow: 0 8px 24px rgba(15,23,42,.12) !important;
      z-index: 10000 !important;
    }
    #modalAgregarVenta .egs-cliente-choices-wrap .choices__list--dropdown .choices__item {
      color: var(--crm-text) !important;
      font-size: 13px !important;
      padding: 8px 12px !important;
    }
    #modalAgregarVenta .egs-cliente-choices-wrap .choices__list--dropdown .choices__item--selectable.is-highlighted {
      background: #eef2ff !important;
      color: var(--crm-accent) !important;
    }
    #modalAgregarVenta .egs-cliente-choices-wrap .choices__list--dropdown .choices__item[data-value="nuevo"] {
      color: #1e40af !important;
      font-weight: 700 !important;
      border-top: 1px solid var(--crm-border);
    }
</style>

<div class="content-wrapper">
  
  <section class="content-header">
    <h1>Ventas Rápidas <small>Gestión de ventas</small></h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa-solid fa-gauge"></i> Inicio</a></li>
      <li class="active">Ventas Rápidas</li>
    </ol>
  </section>

  <section class="content">

    <!-- ══ WELCOME BANNER ══ -->
    <?php
      $diasEs  = array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
      $mesesEs = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
      $_vrFechaHoy = $diasEs[date('w')].' '.date('j').' de '.$mesesEs[intval(date('n'))].', '.date('Y');
    ?>
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 40%,#334155 100%);border-radius:var(--crm-radius,14px);padding:28px 30px;margin-bottom:24px;position:relative;overflow:hidden">
      <div style="position:absolute;right:-20px;top:-20px;width:180px;height:180px;border-radius:50%;background:rgba(99,102,241,.12)"></div>
      <div style="position:absolute;right:60px;bottom:-40px;width:120px;height:120px;border-radius:50%;background:rgba(99,102,241,.08)"></div>
      <div style="position:relative;z-index:1">
        <h2 style="margin:0 0 4px;color:#fff;font-size:22px;font-weight:800;letter-spacing:-.02em">
          <i class="fa-solid fa-bolt" style="margin-right:8px;opacity:.7"></i>
          Centro de Ventas Rápidas
        </h2>
        <p style="margin:0;color:rgba(255,255,255,.55);font-size:13px;font-weight:400">
          <?php echo $_vrFechaHoy; ?> &mdash; Panel operativo con indicadores en vivo y acceso rápido a captura y reportes
        </p>
      </div>
    </div>

    <!-- ══ KPI CARDS ══ -->
    <?php if(!$esVendedorVentasR): ?>
    <div class="vr-kpi-grid">
      <div class="vr-kpi" style="background:linear-gradient(135deg,#6366f1,#818cf8)">
        <i class="fa-solid fa-receipt vr-kpi-icon"></i>
        <div class="vr-kpi-label">Ventas visibles</div>
        <div class="vr-kpi-value" id="kpiVentasTotal">0</div>
      </div>
      <div class="vr-kpi" style="background:linear-gradient(135deg,#22c55e,#4ade80)">
        <i class="fa-solid fa-dollar-sign vr-kpi-icon"></i>
        <div class="vr-kpi-label">Ingreso visible</div>
        <div class="vr-kpi-value" id="kpiIngresoTotal">$0.00</div>
      </div>
      <div class="vr-kpi" style="background:linear-gradient(135deg,#f59e0b,#fbbf24)">
        <i class="fa-solid fa-ticket vr-kpi-icon"></i>
        <div class="vr-kpi-label">Ticket promedio</div>
        <div class="vr-kpi-value" id="kpiTicketPromedio">$0.00</div>
      </div>
      <div class="vr-kpi" style="background:linear-gradient(135deg,#3b82f6,#60a5fa)">
        <i class="fa-solid fa-boxes-stacked vr-kpi-icon"></i>
        <div class="vr-kpi-label">Productos vendidos</div>
        <div class="vr-kpi-value" id="kpiProductosTotal">0</div>
      </div>
    </div>
    <?php endif; ?>

    <!-- ══ TOOLBAR ══ -->
    <div class="vr-toolbar">
      <div class="vr-primary-actions">
        <?php if(!$esVendedorVentasR): ?>
        <a id="btnDescargarExcelVentasR" href="vistas/modulos/descargar-reporte-ventasR.php?reporte=ventasR&empresa=<?php echo $_SESSION["empresa"]; ?>">
          <button class="btn btn-success vr-btn-main"><i class="fas fa-file-excel"></i> Descargar Excel</button>
        </a>
        <?php endif; ?>
        <button class="btn btn-primary vr-btn-main" data-toggle="modal" data-target="#modalAgregarVenta" style="background:linear-gradient(135deg,var(--crm-accent,#6366f1),var(--crm-accent2,#818cf8));border:none">
          <i class="fas fa-plus-circle"></i> Agregar Venta
        </button>
      </div>

      <?php if(!$esVendedorVentasR): ?>
      <div class="vr-filtro-fechas">
        <span class="vr-date-label">Desde</span>
        <input type="date" id="filtroFechaInicialR" class="form-control">
        <span class="vr-date-label">Hasta</span>
        <input type="date" id="filtroFechaFinalR" class="form-control">
        <div class="vr-filtro-presets">
          <button class="btn btn-default" type="button" id="btnPresetHoyR">Hoy</button>
          <button class="btn btn-default" type="button" id="btnPresetSemanaR">Semana</button>
          <button class="btn btn-default" type="button" id="btnPresetMesR">Mes</button>
        </div>
        <button class="btn btn-default" type="button" id="btnAplicarFiltroFechaR"><i class="fas fa-filter"></i> Filtrar</button>
        <button class="btn btn-default" type="button" id="btnLimpiarFiltroFechaR"><i class="fas fa-eraser"></i> Limpiar</button>
      </div>
      <?php endif; ?>
    </div>

    <!-- ══ TABLE CARD ══ -->
    <div class="vr-table-card">
      <div class="vr-table-card-head">
        <h4 class="vr-table-card-title">
          <i class="fa-solid fa-table-list"></i> Registro de Ventas
        </h4>
      </div>
      <div class="vr-table-card-body">
        <table id="tablaVentasRapidasUI" class="table table-bordered table-striped dt-responsive tablaVentasRapidas" width="100%">
          <thead>
            <tr>
              <th style="width:10px">#</th>
              <th>Numero de venta</th>
              <th>Cliente</th>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Historial</th>
              <th>Ticket</th>
              <th>Eliminar Venta</th>
            </tr>
          </thead>
          <?php
            echo '
              <input type="hidden" id="tipoDePerfil" value="'.$_SESSION["perfil"].'" placeholder="'.$_SESSION["perfil"].'">
              <input type="hidden" id="id_empresa" value="'.$_SESSION["empresa"].'">
            ';
          ?>
        </table>
      </div>
    </div>

  </section>

</div>


<!--=====================================
MODAL AGREGAR PRODUCTO
======================================-->
<div id="modalAgregarVenta" class="modal fade" role="dialog">
  
  <div class="modal-dialog modal-lg">

    <div class="modal-content">


        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 40%,#334155 100%); color:white">

          <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.7">&times;</button>

          <h4 class="modal-title"><i class="fa-solid fa-bolt" style="margin-right:8px;opacity:.7"></i> Agregar Venta</h4>

        </div>
        

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE DE LA EMPRESA QUE VENDE-->
            <form method="POST">

            <div class="form-group">
              
              <div class="input-group">
                
                  <?php
                
                  echo'<input  type="hidden" value="'.$_SESSION["empresa"].'" name="empresa">';

                  ?>

              </div>

            </div>

            <!-- ENTRADA PARA EL NOMBRE DEL ASESOR-->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-user"></i></span> 

                <!--<input type="text" class="form-control input-lg" name="asesor" placeholder="Ingresar nombre del Asesor" required>-->

                <select class="form-control input-lg" name="asesor" required>
                  
                  <option value="" id="asesor">
                    
                    Seleccionar Asesor

                  </option>

                  <?php
                      
                      $item = "id_empresa";
                      $valor = $_SESSION["empresa"];

                      $Asesores= Controladorasesores::ctrMostrarAsesoresEmpresas($item, $valor);

                      foreach ($Asesores as $key => $value) {
                        
                        echo '

                        <option>'.$value["nombre"].'</option>';
                      }
                  ?>

                </select>

              </div>

            </div>
            
            <!--=====================================
            CLIENTE (obligatorio) – Choices.js buscador
            ======================================-->
            <div class="form-group egs-cliente-choices-wrap">
              <label style="font-size:12px;font-weight:600;color:var(--crm-text2);margin-bottom:6px;display:block"><i class="fas fa-user" style="margin-right:4px;opacity:.6"></i> Cliente <span style="color:#dc2626">*</span></label>
              <select id="egs_clienteVentaR" name="id_cliente" required>
                <option value="">Buscar cliente...</option>
                <option value="nuevo">+ Agregar nuevo cliente</option>
                <?php
                  $clientesVR = ControladorClientes::ctrMostrarClientes(null, null);
                  if (is_array($clientesVR)) {
                    foreach ($clientesVR as $clVR) {
                      echo '<option value="'.intval($clVR["id"]).'" data-nombre="'.htmlspecialchars($clVR["nombre"]).'">'.htmlspecialchars($clVR["nombre"]).'</option>';
                    }
                  }
                ?>
              </select>
            </div>

            <!-- Sección para agregar nuevo cliente (oculta por defecto) -->
            <div id="nuevoClienteSection" style="display:none;background:#eff6ff;border:1px solid #93c5fd;border-radius:8px;padding:12px;margin-bottom:12px">
              <div style="font-weight:700;color:#1e40af;margin-bottom:8px"><i class="fas fa-user-plus"></i> Nuevo Cliente</div>
              <div class="form-group" style="margin-bottom:8px">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fas fa-user"></i></span>
                  <input type="text" class="form-control input-lg" id="nuevoClienteNombre" placeholder="Nombre del cliente *">
                </div>
              </div>
              <div class="form-group" style="margin-bottom:0">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fab fa-whatsapp" style="color:#25d366"></i></span>
                  <input type="text" class="form-control input-lg" id="nuevoClienteWhatsapp" placeholder="WhatsApp del cliente *">
                </div>
              </div>
              <div id="nuevoClienteError" style="display:none;color:#dc2626;font-size:12px;margin-top:6px"></div>
            </div>

            <!-- Campo oculto para nombre del cliente (se auto-rellena) -->
            <input type="hidden" id="nombrecliente" name="nombreCliente" value="">

            <!-- Sección dinero electrónico (oculta hasta seleccionar cliente) -->
            <div id="egs_deVentaR_section" style="display:none;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px;margin-bottom:12px">
              <div style="font-weight:700;color:#166534;margin-bottom:6px"><i class="fas fa-wallet"></i> Monedero EGS</div>
              <div id="egs_deVentaR_loading" style="text-align:center;padding:8px"><i class="fa fa-spinner fa-spin"></i> Cargando...</div>
              <div id="egs_deVentaR_content" style="display:none">
                <div>Saldo disponible: <span id="egs_deVentaR_saldo" style="font-weight:900;color:#166534">$0.00</span>
                  <span id="egs_deVentaR_nivel" style="font-size:11px;color:#64748b;margin-left:6px"></span>
                </div>
                <div id="egs_deVentaR_sinSaldo" style="display:none;margin-top:6px;font-size:12px;color:#64748b">
                  Esta venta generará <span id="egs_deVentaR_montoGenerar" style="font-weight:700;color:#166534"></span> en dinero electrónico.
                </div>
                <div id="egs_deVentaR_conSaldo" style="display:none;margin-top:8px">
                  <label style="cursor:pointer;font-weight:600;color:#166534"><input type="checkbox" id="egs_deVentaR_usarSaldo"> Usar dinero electrónico en esta venta</label>
                  <div id="egs_deVentaR_montoSection" style="display:none;margin-top:6px">
                    <div class="input-group" style="max-width:280px">
                      <span class="input-group-addon">$</span>
                      <input type="number" class="form-control" id="egs_deVentaR_montoInput" min="0" step="0.01" value="0" placeholder="Monto a usar">
                      <span class="input-group-btn"><button type="button" class="btn btn-success" id="egs_deVentaR_usarTodo">Usar todo</button></span>
                    </div>
                    <div style="font-size:11px;color:#64748b;margin-top:3px">Máximo: <span id="egs_deVentaR_maxMonto">$0.00</span></div>
                  </div>
                </div>
              </div>
            </div>
            <input type="hidden" id="egs_montoCanjeElectronicoVenta" name="montoCanjeElectronicoVenta" value="0">
            <!--=====================================
            CORREO CLIENTE
            ======================================-->
            <div class="form-group">
              	
             	<div class="input-group">
              		
              		<span class="input-group-addon"><i class="fas fa-at"></i></span>
                   	<input type="email" class="form-control input-lg" name="correo" placeholder="Correo del cliente">    

              	</div>

            </div>

            <!--=====================================
            ENTRADA PARA LOS PRODUCTOS
            ======================================-->   
            <div class="vr-products-header"><h5><i class="fa-solid fa-boxes-stacked"></i> Productos</h5></div>
            <div class="form-group" style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
              <select id="selprod" class="form-control" style="max-width:200px">
                <option value="0">Nº de productos</option>
                <?php for ($i=1; $i <= 10; $i++) { echo '<option value="'.$i.'">'.$i.'</option>'; } ?>
              </select>
              <a href="#" onclick="AgregarCamposPedidos();" style="text-decoration:none">
                <button type="button" class="btn btn-primary" style="border-radius:8px;font-size:13px;font-weight:600;padding:6px 16px"><i class="fa fa-plus" style="margin-right:4px"></i> Agregar</button>
              </a>
            </div>
            <div class="form-group row" id="productouno">

              <!--=====================================
              PRODUCTO UNO
              ======================================-->
              <div class="col-sm-1">
               <button type="button" class="btn btn-danger btn-circle circulo minus" id="minus1"><i class="fa fa-minus"></i></button></div>
              
                        
              <div class="col-lg-4 md-0 productosp1">

                <div class="input-group">
                        
                  <span class="input-group-addon"><i class="fa-solid fa-tag"></i></span> 
                          
                  <input id="productoUno" class="form-control input-lg " type="text"  name="productoUno" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa-solid fa-dollar-sign"></i></span> 
                    
                             
                  <input class="form-control input-lg"   name="precioUno" id="precioUno" value=""   step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo1" class="amt" value="0">
                  

                </div>

              </div>


            

           

              <!--=====================================
              PCANTIDAD PRODUCTO UNO
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fa-solid fa-hashtag"></i></span> 
                           
                  <input class="form-control input-lg"  type="number" name="cantidadUno"  id="cantidadUno" value="0"  min="0" step="any" placeholder="Cantidad">
                  

                </div>

              </div>
              <div class="col-sm-1">  <button type="button" class="btn btn-primary btn-circle circulo plus"><i class="fa fa-plus"></i></button>
               </div>

            </div>
            
            <div class="form-group row" id="productodos">
                <br>

              <!--=====================================
              PRODUCTO DOS
              ======================================-->
              <div class="col-sm-1">  <button type="button" class="btn btn-danger btn-circle circulo minus" id="minus2"><i class="fa fa-minus"></i></button> </div>
                        
              <div class="col-lg-4 md-0 productosp1">

                <div class="input-group">
                        
                  <span class="input-group-addon"><i class="fa-solid fa-tag"></i></span> 
                          
                  <input id="productoDos" class="form-control input-lg " type="text"  name="productoDos" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa-solid fa-dollar-sign"></i></span> 
                    
                             
                  <input class="form-control input-lg"   name="precioDos" id="precioDos" value=""   step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo2" class="amt" value="0">
                  

                </div>

              </div>


            

           

              <!--=====================================
              PCANTIDAD PRODUCTO DOS
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fa-solid fa-hashtag"></i></span> 
                           
                  <input class="form-control input-lg"  type="number" name="cantidadDos"  id="cantidadDos" value="0"  min="0" step="any" placeholder="Cantidad">
                  

                </div>

              </div>
              <div class="col-sm-1">  <button type="button" class="btn btn-primary btn-circle circulo plus"><i class="fa fa-plus"></i></button>
               </div>

            </div>
            
            
            <div class="form-group row" id="productotres">
                <br>

              <!--=====================================
              PRODUCTO TRES
              ======================================-->
              <div class="col-sm-1">  <button type="button" class="btn btn-danger btn-circle circulo minus" id="minus3"><i class="fa fa-minus"></i></button> </div>
                        
              <div class="col-lg-4 md-0 productosp1">

                <div class="input-group">
                        
                  <span class="input-group-addon"><i class="fa-solid fa-tag"></i></span> 
                          
                  <input id="productoTres" class="form-control input-lg" type="text"  name="productoTres" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa-solid fa-dollar-sign"></i></span> 
                    
                             
                  <input class="form-control input-lg"   name="precioTres" id="precioTres" value=""  step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo3" class="amt" value="0">
                  

                </div>

              </div>


            

           

              <!--=====================================
              PCANTIDAD PRODUCTO TRES
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fa-solid fa-hashtag"></i></span> 
                           
                  <input class="form-control input-lg"  type="number" name="cantidadTres"  id="cantidadTres" value="0"  min="0" step="any" placeholder="Cantidad">
                  

                </div>

              </div>
              <div class="col-sm-1">  <button type="button" class="btn btn-primary btn-circle circulo plus"><i class="fa fa-plus"></i></button>
               </div>

            </div>
            
            <div class="form-group row" id="productocuatro">
                <br>

              <!--=====================================
              PRODUCTO CUATRO
              ======================================-->
              <div class="col-sm-1">  <button type="button" class="btn btn-danger btn-circle circulo minus" id="minus4"><i class="fa fa-minus"></i></button> </div>
                        
              <div class="col-lg-4 md-0 productosp1">

                <div class="input-group">
                        
                  <span class="input-group-addon"><i class="fa-solid fa-tag"></i></span> 
                          
                  <input id="productoCuatro" class="form-control input-lg " type="text"  name="productoCuatro" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa-solid fa-dollar-sign"></i></span> 
                    
                             
                  <input class="form-control input-lg"   name="precioCuatro" id="precioCuatro" value="" step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo4" class="amt" value="0">
                  

                </div>

              </div>

              <!--=====================================
              PCANTIDAD PRODUCTO CUATRO
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fa-solid fa-hashtag"></i></span> 
                           
                  <input class="form-control input-lg"  type="number" name="cantidadCuatro"  id="cantidadCuatro" value="0"  min="0" step="any" placeholder="Cantidad">
                  

                </div>

              </div>
              <div class="col-sm-1">  <button type="button" class="btn btn-primary btn-circle circulo plus"><i class="fa fa-plus"></i></button>
               </div>

            </div>
            
            <div class="form-group row" id="productocinco">
                <br>

              <!--=====================================
              PRODUCTO CINCO
              ======================================-->
              <div class="col-sm-1">  <button type="button" class="btn btn-danger btn-circle circulo minus" id="minus5"><i class="fa fa-minus"></i></button> </div>
                        
              <div class="col-lg-4 md-0 productosp1">

                <div class="input-group">
                        
                  <span class="input-group-addon"><i class="fa-solid fa-tag"></i></span> 
                          
                  <input id="productoCinco" class="form-control input-lg " type="text"  name="productoCinco" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa-solid fa-dollar-sign"></i></span> 
                    
                             
                  <input class="form-control input-lg"   name="precioCinco" id="precioCinco" value=""   step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo5" class="amt" value="0">
                  

                </div>

              </div>

              <!--=====================================
              PCANTIDAD PRODUCTO CINCO
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fa-solid fa-hashtag"></i></span> 
                           
                  <input class="form-control input-lg"  type="number" name="cantidadCinco"  id="cantidadCinco" value="0"  min="0" step="any" placeholder="Cantidad">
                  

                </div>

              </div>
              <div class="col-sm-1">  <button type="button" class="btn btn-primary btn-circle circulo plus"><i class="fa fa-plus"></i></button>
               </div>

            </div>
            
            <div class="form-group row" id="productoseis">
                <br>

              <!--=====================================
              PRODUCTO SEIS
              ======================================-->
              <div class="col-sm-1">  <button type="button" class="btn btn-danger btn-circle circulo minus" id="minus6"><i class="fa fa-minus"></i></button> </div>
                        
              <div class="col-lg-4 md-0 productosp1">

                <div class="input-group">
                        
                  <span class="input-group-addon"><i class="fa-solid fa-tag"></i></span> 
                          
                  <input id="productoSeis" class="form-control input-lg " type="text"  name="productoSeis" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa-solid fa-dollar-sign"></i></span> 
                    
                             
                  <input class="form-control input-lg"   name="precioSeis" id="precioSeis" value=""  step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo6" class="amt" value="0">
                  

                </div>

              </div>

              <!--=====================================
              PCANTIDAD PRODUCTO SEIS
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fa-solid fa-hashtag"></i></span> 
                           
                  <input class="form-control input-lg"  type="number" name="cantidadSeis"  id="cantidadSeis" value="0"  min="0" step="any" placeholder="Cantidad">
                  

                </div>

              </div>
              <div class="col-sm-1">  <button type="button" class="btn btn-primary btn-circle circulo plus"><i class="fa fa-plus"></i></button>
               </div>

            </div>
            
            <div class="form-group row" id="productosiete">
                <br>

              <!--=====================================
              PRODUCTO SIETE
              ======================================-->
              <div class="col-sm-1">  <button type="button" class="btn btn-danger btn-circle circulo minus" id="minus7"><i class="fa fa-minus"></i></button> </div>
                        
              <div class="col-lg-4 md-0 productosp1">

                <div class="input-group">
                        
                  <span class="input-group-addon"><i class="fa-solid fa-tag"></i></span> 
                          
                  <input id="productoSiete" class="form-control input-lg " type="text"  name="productoSiete" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa-solid fa-dollar-sign"></i></span> 
                    
                             
                  <input class="form-control input-lg"   name="precioSiete" id="precioSiete" value=""   step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo7" class="amt" value="0">
                  

                </div>

              </div>

              <!--=====================================
              PCANTIDAD PRODUCTO SIETE
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fa-solid fa-hashtag"></i></span> 
                           
                  <input class="form-control input-lg"  type="number" name="cantidadSiete"  id="cantidadSiete" value="0"  min="0" step="any" placeholder="Cantidad">
                  

                </div>

              </div>
              <div class="col-sm-1">  <button type="button" class="btn btn-primary btn-circle circulo plus"><i class="fa fa-plus"></i></button>
               </div>

            </div>
            
            
            <div class="form-group row" id="productoocho">
                <br>

              <!--=====================================
              PRODUCTO OCHO
              ======================================-->
              <div class="col-sm-1">  <button type="button" class="btn btn-danger btn-circle circulo minus" id="minus8"><i class="fa fa-minus"></i></button> </div>
                        
              <div class="col-lg-4 md-0 productosp1">

                <div class="input-group">
                        
                  <span class="input-group-addon"><i class="fa-solid fa-tag"></i></span> 
                          
                  <input id="productoOcho" class="form-control input-lg " type="text"  name="productoOcho" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa-solid fa-dollar-sign"></i></span> 
                    
                             
                  <input class="form-control input-lg"   name="precioOcho" id="precioOcho" value=""   step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo8" class="amt" value="0">
                  

                </div>

              </div>

              <!--=====================================
              PCANTIDAD PRODUCTO OCHO
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fa-solid fa-hashtag"></i></span> 
                           
                  <input class="form-control input-lg"  type="number" name="cantidadOcho"  id="cantidadOcho" value="0"  min="0" step="any" placeholder="Cantidad">
                  

                </div>

              </div>
              <div class="col-sm-1">  <button type="button" class="btn btn-primary btn-circle circulo plus"><i class="fa fa-plus"></i></button>
               </div>

            </div>
            
            <div class="form-group row" id="productonueve">
                <br>

              <!--=====================================
              PRODUCTO NUEVE
              ======================================-->
              <div class="col-sm-1">  <button type="button" class="btn btn-danger btn-circle circulo minus" id="minus9"><i class="fa fa-minus"></i></button> </div>
                        
              <div class="col-lg-4 md-0 productosp1">

                <div class="input-group">
                        
                  <span class="input-group-addon"><i class="fa-solid fa-tag"></i></span> 
                          
                  <input id="productoNueve" class="form-control input-lg " type="text"  name="productoNueve" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa-solid fa-dollar-sign"></i></span> 
                    
                             
                  <input class="form-control input-lg"   name="precioNueve" id="precioNueve" value=""  step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo9" class="amt" value="0">
                  

                </div>

              </div>

              <!--=====================================
              PCANTIDAD PRODUCTO NUEVE
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fa-solid fa-hashtag"></i></span> 
                           
                  <input class="form-control input-lg"  type="number" name="cantidadNueve"  id="cantidadNueve" value="0"  min="0" step="any" placeholder="Cantidad">
                  

                </div>

              </div>
              <div class="col-sm-1">  <button type="button" class="btn btn-primary btn-circle circulo plus"><i class="fa fa-plus"></i></button>
               </div>

            </div>
            
            <div class="form-group row" id="productodiez">
                <br>

              <!--=====================================
              PRODUCTO DIEZ
              ======================================-->
              <div class="col-sm-1">  <button type="button" class="btn btn-danger btn-circle circulo minus" id="minus10"><i class="fa fa-minus"></i></button> </div>
                        
              <div class="col-lg-4 md-0 productosp1">

                <div class="input-group">
                        
                  <span class="input-group-addon"><i class="fa-solid fa-tag"></i></span> 
                          
                  <input id="productoDiez" class="form-control input-lg " type="text"  name="productoDiez" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa-solid fa-dollar-sign"></i></span> 
                    
                             
                  <input  class="form-control input-lg"   name="precioDiez" id="precioDiez" value=""   step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo10" class="amt" value="0">
                  

                </div>

              </div>

              <!--=====================================
              PCANTIDAD PRODUCTO DIEZ
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fa-solid fa-hashtag"></i></span> 
                           
                  <input class="form-control input-lg"  type="number" name="cantidadDiez"  id="cantidadDiez" value="0"  min="0" step="any" placeholder="Cantidad">
                  

                </div>

              </div>
              <div class="col-sm-1">  <button type="button" class="btn btn-primary btn-circle circulo plus"><i class="fa fa-plus"></i></button>
               </div>

            </div>
            
            
            
              

              <div class="form-group row" id="Caltotal">
                <!--=====================================
                PRODUCTO CALCULAR TOTALES
                ======================================-->
                        

                <div class="col-xs-3"></div>
                <div class="col-xs-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--crm-muted);text-align:center;margin-bottom:6px">Total</div>
                  <div class="input-group">

                    <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span> 
                    <input type="number" class="form-control" name="pago" id="Resultado" value="0" readonly style="font-size:28px!important;font-weight:800;height:auto!important;text-align:center">
                    
                    
                    <input type="hidden" class="form-control" name="cantidadProductos" id="cantidadProductos" min="0" value="0" readonly>

                  </div>

                </div>
                <div class="col-xs-3"></div>
                

              </div>
              <!--=====================================
              METODO DE PAGO
              ======================================-->
              <div class="form-group METODOPAGO">
              	
              		
              		<div class="input-group">
              			
              			<span class="input-group-addon"><i class="fas fa-hand-holding-usd"></i></span>
                   		<select class="form-control" id="metodopago" name="metodo"> 
                   		<option>Seleccione método de pago</option>
                   		<option value="Efectivo">Efectivo</option>
                   		<option value="Tarjeta de Credito">Tarjeta de Crédito</option>
                   		<option value="Tarjeta de Debito">Tarjeta de Débito</option>
                   		<option value="Transferencia bancaria">Transferencia bancaria</option>
                   		<option value="MercadoPago">MercadoPago</option>
                   		<option value="Paypal">Paypal</option>
                   		<option value="Credito">Crédito</option>
                   		<option value="Puntos">Puntos</option>
                   		</select>

              	</div>

              </div>

              <div class="form-group row" id="CAMBIORE">

                <!--=====================================
                CAMBIO A REGRESAR
                ======================================-->
                <div class="col-xs-6">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--crm-muted);text-align:center;margin:14px 0 6px">Pago del Cliente</div>
                  
                  <div class="input-group">
                                  
                                  
                  <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>
                         
                    <input class="form-control" type="number" name="pagoCliente" id="pagoCliente" value="0" min="0" step="any" placeholder="Pago cliente" style="font-size:22px!important;font-weight:700;height:auto!important;text-align:center">
                  </div>

                </div>

                <div class="col-xs-6">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--crm-muted);text-align:center;margin:14px 0 6px">Cambio a Regresar</div>
                  <div class="input-group"> 

                    <span class="input-group-addon"><i class="fas fa-cash-register"></i></span>

                    <input type="number" class="form-control" name="cambio" id="cambio" min="0" value="0" step="any" readonly style="font-size:22px!important;font-weight:700;height:auto!important;text-align:center">

                  </div>

                </div>

              </div>




        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer" id="completarventa">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Guardar Venta</button>

        </div>

        <?php

          $crearVenta = new ControladorVentas();
          $crearVenta -> ctrCrearventa();

        ?>

      </form>

              <!--=====================================
              FINAL DEL CUERPO DEL MODAL
              ======================================-->

            </div>

        </div>

    </div>

  </div>

</div>

<?php

  $eliminarVenta = new ControladorVentas();
  $eliminarVenta -> ctrEliminarVenta();

?>

<script>
// ═══════════════════════════════════════════════
// CLIENTE OBLIGATORIO + NUEVO CLIENTE + DINERO ELECTRÓNICO
// ═══════════════════════════════════════════════
(function(){
  var _deVR_saldo = 0;
  var _clienteChoices = null;
  var _clienteSelect = document.getElementById('egs_clienteVentaR');

  // ── Inicializar Choices.js en el select de cliente ──
  function initClienteChoices() {
    if (_clienteChoices) { _clienteChoices.destroy(); }
    _clienteChoices = new Choices(_clienteSelect, {
      searchEnabled: true,
      shouldSort: false,
      removeItemButton: false,
      placeholderValue: 'Buscar cliente...',
      searchPlaceholderValue: 'Escribe para buscar...',
      itemSelectText: '',
      noResultsText: 'Sin resultados',
      noChoicesText: 'Sin opciones'
    });
  }

  // Inicializar cuando se abre el modal (para que el DOM esté visible)
  $('#modalAgregarVenta').on('shown.bs.modal', function(){
    if (!_clienteChoices) { initClienteChoices(); }
  });

  // ── Mostrar/ocultar sección nuevo cliente y dinero electrónico ──
  _clienteSelect.addEventListener('change', function(){
    var val = this.value;

    // Si eligió "nuevo cliente"
    if (val === 'nuevo') {
      $('#nuevoClienteSection').slideDown(200);
      $('#egs_deVentaR_section').hide();
      $('#egs_montoCanjeElectronicoVenta').val(0);
      $('#nombrecliente').val('');
      return;
    }

    // Si eligió un cliente existente
    $('#nuevoClienteSection').slideUp(200);
    $('#nuevoClienteNombre').val('');
    $('#nuevoClienteWhatsapp').val('');
    $('#nuevoClienteError').hide();

    var idCliente = parseInt(val) || 0;
    
    // Auto-rellenar nombre del cliente
    if (idCliente > 0) {
      var $sel = $(_clienteSelect);
      var nombreSel = $sel.find('option:selected').data('nombre') || $sel.find('option:selected').text();
      $('#nombrecliente').val(nombreSel);
    } else {
      $('#nombrecliente').val('');
    }

    if (idCliente <= 0) {
      $('#egs_deVentaR_section').hide();
      $('#egs_montoCanjeElectronicoVenta').val(0);
      return;
    }
    $('#egs_deVentaR_section').show();
    $('#egs_deVentaR_loading').show();
    $('#egs_deVentaR_content').hide();

    $.ajax({
      url: 'ajax/recompensas.ajax.php',
      method: 'POST',
      data: { idClienteRecompensas: idCliente },
      dataType: 'json',
      success: function(data) {
        $('#egs_deVentaR_loading').hide();
        $('#egs_deVentaR_content').show();

        _deVR_saldo = parseFloat(data.saldo) || 0;
        $('#egs_deVentaR_saldo').text('$' + _deVR_saldo.toFixed(2));
        $('#egs_deVentaR_nivel').text('Nivel: ' + data.porcentaje + '% | ' + data.entregadas + ' transacciones');

        var totalVenta = parseFloat($('#Resultado').val()) || 0;

        if (_deVR_saldo > 0) {
          $('#egs_deVentaR_sinSaldo').hide();
          $('#egs_deVentaR_conSaldo').show();
          var maxUsar = Math.min(_deVR_saldo, totalVenta);
          $('#egs_deVentaR_maxMonto').text('$' + maxUsar.toFixed(2));
          $('#egs_deVentaR_montoInput').attr('max', maxUsar).val('');
        } else {
          $('#egs_deVentaR_sinSaldo').show();
          $('#egs_deVentaR_conSaldo').hide();
          var montoGenerar = (totalVenta * (data.porcentaje / 100)).toFixed(2);
          $('#egs_deVentaR_montoGenerar').text('$' + montoGenerar);
        }
      },
      error: function() {
        $('#egs_deVentaR_loading').hide();
        $('#egs_deVentaR_content').show();
        $('#egs_deVentaR_saldo').text('$0.00');
        $('#egs_deVentaR_sinSaldo').show();
        $('#egs_deVentaR_conSaldo').hide();
      }
    });
  });

  $('#egs_deVentaR_usarSaldo').on('change', function(){
    $('#egs_deVentaR_montoSection').toggle(this.checked);
    if (!this.checked) {
      $('#egs_deVentaR_montoInput').val('0');
      $('#egs_montoCanjeElectronicoVenta').val(0);
    }
  });

  $('#egs_deVentaR_usarTodo').on('click', function(){
    var totalVenta = parseFloat($('#Resultado').val()) || 0;
    var maxUsar = Math.min(_deVR_saldo, totalVenta);
    $('#egs_deVentaR_montoInput').val(maxUsar.toFixed(2));
    $('#egs_montoCanjeElectronicoVenta').val(maxUsar.toFixed(2));
  });

  $('#egs_deVentaR_montoInput').on('input change', function(){
    var val = parseFloat($(this).val()) || 0;
    var totalVenta = parseFloat($('#Resultado').val()) || 0;
    var maxUsar = Math.min(_deVR_saldo, totalVenta);
    if (val > maxUsar) val = maxUsar;
    if (val < 0) val = 0;
    $('#egs_montoCanjeElectronicoVenta').val(val.toFixed(2));
  });

  // ── Interceptar envío del formulario para validar/crear cliente ──
  $('#modalAgregarVenta form').on('submit', function(e){
    var $select = $('#egs_clienteVentaR');
    var val = $select.val();

    // Validar que se seleccionó un cliente
    if (!val || val === '') {
      e.preventDefault();
      swal({
        type: 'warning',
        title: 'Cliente obligatorio',
        text: 'Debes seleccionar o agregar un cliente para registrar la venta.',
        confirmButtonText: 'Entendido'
      });
      return false;
    }

    // Si es cliente existente, dejar pasar
    if (val !== 'nuevo') {
      return true;
    }

    // Si es nuevo cliente, validar campos y crear vía AJAX
    e.preventDefault();
    var nombre = $.trim($('#nuevoClienteNombre').val());
    var whatsapp = $.trim($('#nuevoClienteWhatsapp').val());

    if (nombre === '' || whatsapp === '') {
      $('#nuevoClienteError').text('El nombre y WhatsApp del nuevo cliente son obligatorios.').show();
      return false;
    }

    $('#nuevoClienteError').hide();
    var $form = $(this);
    var $btnSubmit = $form.find('button[type="submit"]');
    $btnSubmit.prop('disabled', true).text('Guardando cliente...');

    $.ajax({
      url: 'ajax/clientes.ajax.php',
      method: 'POST',
      data: {
        crearClienteRapido: 1,
        nombreClienteRapido: nombre,
        whatsappClienteRapido: whatsapp,
        empresaClienteRapido: $('#id_empresa').val() || 0
      },
      dataType: 'json',
      success: function(resp) {
        if (resp.status === 'ok' && resp.id > 0) {
          // Agregar la nueva opción via Choices.js y seleccionarla
          if (_clienteChoices) {
            _clienteChoices.setChoices([{
              value: String(resp.id),
              label: resp.nombre,
              selected: true,
              customProperties: { nombre: resp.nombre }
            }], 'value', 'label', false);
            _clienteChoices.setChoiceByValue(String(resp.id));
          } else {
            var $newOpt = $('<option>', { value: resp.id, text: resp.nombre, 'data-nombre': resp.nombre });
            $select.append($newOpt);
            $select.val(resp.id);
          }
          $('#nombrecliente').val(resp.nombre);
          $('#nuevoClienteSection').hide();

          // Ahora sí enviar el formulario
          $btnSubmit.prop('disabled', false).text('Guardar Venta');
          $form[0].submit();
        } else {
          $btnSubmit.prop('disabled', false).text('Guardar Venta');
          $('#nuevoClienteError').text(resp.mensaje || 'Error al crear el cliente. Intenta de nuevo.').show();
        }
      },
      error: function() {
        $btnSubmit.prop('disabled', false).text('Guardar Venta');
        $('#nuevoClienteError').text('Error de conexión. Intenta de nuevo.').show();
      }
    });

    return false;
  });

})();
</script>
