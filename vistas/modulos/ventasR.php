<?php
if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "secretaria"  AND $_SESSION["perfil"]!= "Super-Administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}

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
 const $input1 = document.querySelector('#nombrecliente');
    const patron1 = /[a-zA-ZñÑ ÉÁÍÓ()]+/;
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
    .productosp{ margin-left:-100px; }
    .circulo{ margin-top:8px; }
    .negativo{ border-color: #dc143c; }
    .positivo{ border-color: #7cfc00; }
    #pagoCliente, #Resultado, #cambio{ font-size: 40px; }
    .plus{ margin-left:20px; }
    .btn-circle {
      width: 30px;
      height: 30px;
      padding: 6px 0px;
      border-radius: 15px;
      text-align: center;
      font-size: 12px;
      line-height: 1.42857;
    }

    .vr-dashboard-shell {
      background: linear-gradient(125deg, #f5f8ff 0%, #f2f8f4 45%, #fff5ea 100%);
      border-radius: 14px;
      padding: 16px;
      box-shadow: 0 10px 25px rgba(16, 24, 40, 0.08);
    }
    .vr-hero {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
      margin-bottom: 12px;
    }
    .vr-hero h3 {
      margin: 0;
      font-weight: 700;
      color: #1a2f44;
      letter-spacing: .2px;
    }
    .vr-hero p {
      margin: 2px 0 0;
      color: #506177;
      font-size: 13px;
    }
    .vr-kpi-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 10px;
      margin-bottom: 14px;
    }
    .vr-kpi-card {
      background: #fff;
      border: 1px solid #e6ebf2;
      border-left: 4px solid #1f8d61;
      border-radius: 10px;
      padding: 10px 12px;
      box-shadow: 0 6px 14px rgba(30, 41, 59, 0.07);
    }
    .vr-kpi-card .vr-kpi-title {
      display: block;
      font-size: 11px;
      text-transform: uppercase;
      color: #5f7085;
      letter-spacing: .8px;
      margin-bottom: 2px;
      font-weight: 700;
    }
    .vr-kpi-card .vr-kpi-value {
      display: block;
      font-size: 25px;
      font-weight: 700;
      color: #0f172a;
      line-height: 1.1;
    }
    .vr-toolbar {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: flex-start;
      gap: 12px;
      margin-bottom: 12px;
    }
    .vr-primary-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
    }
    .vr-primary-actions .btn,
    .vr-filtro-fechas .btn {
      border-radius: 999px;
      font-weight: 600;
      box-shadow: 0 4px 10px rgba(15, 23, 42, 0.12);
    }
    .vr-btn-main {
      min-height: 40px;
      padding: 8px 14px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .vr-filtro-fechas {
      background: #fff;
      border: 1px solid #dde7f2;
      border-radius: 12px;
      padding: 10px;
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
      justify-content: flex-end;
      max-width: 700px;
      width: 100%;
    }
    .vr-filtro-fechas .form-control {
      min-width: 145px;
      border-radius: 999px;
      border: 1px solid #d4dde8;
      box-shadow: none;
    }
    .vr-date-label {
      font-size: 11px;
      font-weight: 700;
      color: #5b6f84;
      text-transform: uppercase;
      letter-spacing: .6px;
      margin-right: 2px;
    }
    .vr-filtro-presets {
      display: inline-flex;
      gap: 6px;
      flex-wrap: wrap;
      margin-left: 2px;
    }
    .vr-filtro-presets .btn {
      border-radius: 999px;
      padding: 6px 10px;
      font-size: 12px;
      box-shadow: none;
    }
    @media (max-width: 991px) {
      .vr-toolbar {
        flex-direction: column;
      }
      .vr-filtro-fechas {
        justify-content: flex-start;
      }
    }
    .vr-table-wrap {
      background: #fff;
      border-radius: 12px;
      padding: 12px;
      border: 1px solid #e7edf4;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
    }
    .vr-table-wrap .dataTables_filter input {
      border-radius: 20px;
      border: 1px solid #d4dde8;
      padding: 4px 10px;
    }
    #tablaVentasRapidasUI thead th {
      position: sticky;
      top: 0;
      background: #f8fafc;
      z-index: 2;
      box-shadow: 0 1px 0 rgba(15, 23, 42, .08);
      white-space: nowrap;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: .4px;
      color: #475569;
      border-bottom-color: #e8eef5;
    }
    #tablaVentasRapidasUI.dataTable thead .sorting,
    #tablaVentasRapidasUI.dataTable thead .sorting_asc,
    #tablaVentasRapidasUI.dataTable thead .sorting_desc,
    #tablaVentasRapidasUI.dataTable thead .sorting_asc_disabled,
    #tablaVentasRapidasUI.dataTable thead .sorting_desc_disabled,
    #tablaVentasRapidasUI.dataTable thead .sorting_disabled {
      background-image: none !important;
      padding-right: 8px !important;
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
      display: none !important;
      content: none !important;
    }
    #tablaVentasRapidasUI tbody tr td {
      vertical-align: middle;
    }
    #tablaVentasRapidasUI.dataTable.stripe tbody tr.odd,
    #tablaVentasRapidasUI.dataTable.display tbody tr.odd,
    #tablaVentasRapidasUI.table-striped > tbody > tr:nth-of-type(odd) {
      background-color: #fbfdff;
    }
    #tablaVentasRapidasUI tbody tr:hover {
      background-color: #f4f7ff !important;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_length,
    #tablaVentasRapidasUI_wrapper .dataTables_filter {
      margin-bottom: 12px;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_length label,
    #tablaVentasRapidasUI_wrapper .dataTables_filter label {
      color: #475569;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: .2px;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_length select {
      border: 1px solid #dbe3ef !important;
      border-radius: 8px;
      background: #fff;
      color: #334155;
      height: 34px;
      padding: 4px 26px 4px 10px;
      margin: 0 6px;
      font-size: 12px;
      font-weight: 600;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_filter input {
      border: 1px solid #dbe3ef !important;
      border-radius: 10px;
      background: #fff;
      color: #334155;
      height: 36px;
      min-width: 220px;
      padding: 6px 12px;
      font-size: 12px;
      font-weight: 600;
      transition: all .15s ease;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_length select:focus,
    #tablaVentasRapidasUI_wrapper .dataTables_filter input:focus {
      outline: none;
      border-color: #a5b4fc !important;
      box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
    }
    #tablaVentasRapidasUI_wrapper .dataTables_paginate {
      margin-top: 14px;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button > a {
      border-radius: 8px !important;
      border: 1px solid #dbe3ef !important;
      background: #fff !important;
      color: #334155 !important;
      margin-left: 6px;
      padding: 6px 12px !important;
      font-weight: 600;
      transition: all .15s ease;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button > a:hover {
      background: #eef2ff !important;
      border-color: #a5b4fc !important;
      color: #3730a3 !important;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button.active > a,
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button.active > a:hover,
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button.active > a:focus {
      background: #1a3152 !important;
      border-color: #1a3152 !important;
      color: #fff !important;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button.disabled > a,
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button.disabled > a:hover,
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button.disabled > a:focus {
      background: #f8fafc !important;
      border-color: #e2e8f0 !important;
      color: #94a3b8 !important;
      cursor: not-allowed;
    }
    #tablaVentasRapidasUI_wrapper .dataTables_paginate ul.pagination > li.paginate_button {
      background: transparent !important;
      border: 0 !important;
      box-shadow: none !important;
    }

    #modalAgregarVenta .modal-content {
      border-radius: 14px;
      border: 1px solid #dae5f1;
      box-shadow: 0 18px 45px rgba(15, 23, 42, .24);
      overflow: hidden;
    }
    #modalAgregarVenta .modal-header {
      background: linear-gradient(90deg, #1f8d61 0%, #2eaf78 100%) !important;
      border-bottom: 0;
      padding: 14px 18px;
    }
    #modalAgregarVenta .modal-title {
      font-weight: 700;
      letter-spacing: .2px;
    }
    #modalAgregarVenta .modal-body {
      background: #f7fbff;
      padding: 16px;
    }
    #modalAgregarVenta .box-body {
      background: #fff;
      border: 1px solid #e2eaf3;
      border-radius: 10px;
      padding: 14px;
    }
    #modalAgregarVenta .input-group-addon {
      background: #f3f7fc;
      color: #35506d;
      border-color: #d7e2ef;
      font-weight: 600;
    }
    #modalAgregarVenta .form-control {
      border-color: #d7e2ef;
      border-radius: 8px;
      box-shadow: none;
    }
    #modalAgregarVenta .form-control:focus {
      border-color: #41a776;
      box-shadow: 0 0 0 2px rgba(31, 141, 97, .12);
    }
    #modalAgregarVenta #Caltotal {
      background: linear-gradient(90deg, #f3fbf6 0%, #f2f8ff 100%);
      border: 1px dashed #c8ddcf;
      border-radius: 12px;
      padding: 10px 0;
    }
    #modalAgregarVenta #Resultado,
    #modalAgregarVenta #pagoCliente,
    #modalAgregarVenta #cambio {
      font-size: 34px;
      font-weight: 700;
      color: #10243a;
      text-align: center;
    }
    #modalAgregarVenta .modal-footer {
      border-top: 1px solid #dde7f2;
      background: #f8fbff;
    }
    #modalAgregarVenta .modal-footer .btn {
      border-radius: 999px;
      font-weight: 600;
      min-width: 120px;
    }
</style>

<div class="content-wrapper">
  
   <section class="content-header">
      
    <h1>
      Gestor Ventas Rápidas
    </h1>

    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>

      <li class="active">Gestor Ventas Rápidas</li>
      
    </ol>

  </section>


  <section class="content">

    <div class="box vr-dashboard-shell"> 

      <div class="box-header with-border">
        
        <?php

        //include "inicio/grafico-ventas.php";

        ?>

      </div>

      <div class="box-body">

        <div class="vr-hero">
          <div>
            <h3>Centro de Ventas R</h3>
            <p>Panel operativo con indicadores en vivo y acceso rapido a captura y reportes.</p>
          </div>
        </div>

        <div class="vr-kpi-grid">
          <div class="vr-kpi-card">
            <span class="vr-kpi-title">Ventas visibles</span>
            <span class="vr-kpi-value" id="kpiVentasTotal">0</span>
          </div>
          <div class="vr-kpi-card">
            <span class="vr-kpi-title">Ingreso visible</span>
            <span class="vr-kpi-value" id="kpiIngresoTotal">$0.00</span>
          </div>
          <div class="vr-kpi-card">
            <span class="vr-kpi-title">Ticket promedio</span>
            <span class="vr-kpi-value" id="kpiTicketPromedio">$0.00</span>
          </div>
          <div class="vr-kpi-card">
            <span class="vr-kpi-title">Productos vendidos</span>
            <span class="vr-kpi-value" id="kpiProductosTotal">0</span>
          </div>
        </div>

        <div class="vr-toolbar">

          <div class="vr-primary-actions">

            <a id="btnDescargarExcelVentasR" href="vistas/modulos/descargar-reporte-ventasR.php?reporte=ventasR&empresa=<?echo $_SESSION["empresa"]?>">
            
              <button class="btn btn-success vr-btn-main"><i class="fas fa-file-excel"></i> Descargar Reporte En Excel</button>

            </a>


            <button class="btn btn-primary vr-btn-main" data-toggle="modal" data-target="#modalAgregarVenta">
          
              <i class="fas fa-plus-circle"></i> Agregar Venta

            </button>
          </div>

          <div class="vr-filtro-fechas">
          <span class="vr-date-label">Desde</span>
          <input type="date" id="filtroFechaInicialR" class="form-control" placeholder="Fecha inicial">
          <span class="vr-date-label">Hasta</span>
          <input type="date" id="filtroFechaFinalR" class="form-control" placeholder="Fecha final">
          <div class="vr-filtro-presets">
            <button class="btn btn-default" type="button" id="btnPresetHoyR">Hoy</button>
            <button class="btn btn-default" type="button" id="btnPresetSemanaR">Semana</button>
            <button class="btn btn-default" type="button" id="btnPresetMesR">Mes</button>
          </div>
          <button class="btn btn-default" type="button" id="btnAplicarFiltroFechaR"><i class="fas fa-filter"></i> Filtrar</button>
          <button class="btn btn-default" type="button" id="btnLimpiarFiltroFechaR"><i class="fas fa-eraser"></i> Limpiar</button>
          </div>
        
        <!--<div class="box-header with-border">
        
              <a href="creararventa">

                <button class="btn btn-primary">
                  
                  Agregar venta Prueba

                </button>

              </a>

            </div>
    
        </div>-->
  

        </div>
        
        <div class="vr-table-wrap">
        
        <table id="tablaVentasRapidasUI" class="table table-bordered table-striped dt-responsive tablaVentasRapidas" width="100%">
        
          <thead>
            
            <tr>
              
              <th style="width:10px">#</th>
              <th>Numero de venta</th>
              <th>Cliente</th>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Ticket</th>
              <th>Eliminar Venta</th>

            </tr>

          </thead> 

                  <?php
            
             // $administrador = ControladorAdministradores::ctrMostrarAdministradores($item, $valor);
               //foreach ($administrador as $key => $valueA) {
                 echo'

                 <input  type="hidden" id="tipoDePerfil" value="'.$_SESSION["perfil"].'"  placeholder="'.$_SESSION["perfil"].'">
              
                <input  type="hidden" id="id_empresa" value="'.$_SESSION["empresa"].'">

                ';
                //}
            ?>
        
        </table>
        </div>


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

        <div class="modal-header" style="background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Venta</h4>

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
            NOMBRE CLIENTE
            ======================================-->
            <div class="form-group">
              	
             	<div class="input-group">
              		
              		<span class="input-group-addon"><i class="fas fa-user"></i></span>
                   	<input id="nombrecliente" type="text" class="form-control input-lg" name="nombreCliente" placeholder="Nombre del cliente">    

              	</div>

            </div>
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
            <div class="form-group row">
                <center><h3>PRODUCTOS A AGREGAR</h3></center>
                <div class="formgroup col-md-1"></div>
                <div class="formgroup col-md-8">
            <select id="selprod" class="form-control">
                  <option value="0">Numero de productos</option>
                      <?php
                      for ($i=1; $i <= 10; $i++) {
                       echo '<option value="'.$i.'">'.$i.'</option>';
                      }
                       ?>
              </select>
              </div>
                <div class="col">
                 
                 

                 <a href="#" onclick="AgregarCamposPedidos();">
                  
                  <!--<div id="camposProductos">-->
                
                    <input type="button" class="btn btn-primary plus" value="Agregar producto"/></br></br>
                </a>
                </div>
               
            
            
                </div>
                
              
            

<hr>
            <div class="form-group row" id="productouno">

              <!--=====================================
              PRODUCTO UNO
              ======================================-->
              <div class="col-sm-1">
               <button type="button" class="btn btn-danger btn-circle circulo minus" id="minus1"><i class="fa fa-minus"></i></button></div>
              
                        
              <div class="col-lg-4 md-0 productosp1">

                <div class="input-group">
                        
                  <span class="input-group-addon"><i class="fab fa-product-hunt"></i> | Producto</span> 
                          
                  <input id="productoUno" class="form-control input-lg " type="text"  name="productoUno" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-money-bill-alt"></i> | Precio</span> 
                    
                             
                  <input class="form-control input-lg"   name="precioUno" id="precioUno" value=""   step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo1" class="amt" value="0">
                  

                </div>

              </div>


            

           

              <!--=====================================
              PCANTIDAD PRODUCTO UNO
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fas fa-sort-amount-up-alt"></i> | Cantidad</span> 
                           
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
                        
                  <span class="input-group-addon"><i class="fab fa-product-hunt"></i> | Producto</span> 
                          
                  <input id="productoDos" class="form-control input-lg " type="text"  name="productoDos" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-money-bill-alt"></i> | Precio</span> 
                    
                             
                  <input class="form-control input-lg"   name="precioDos" id="precioDos" value=""   step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo2" class="amt" value="0">
                  

                </div>

              </div>


            

           

              <!--=====================================
              PCANTIDAD PRODUCTO DOS
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fas fa-sort-amount-up-alt"></i> | Cantidad</span> 
                           
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
                        
                  <span class="input-group-addon"><i class="fab fa-product-hunt"></i> | Producto</span> 
                          
                  <input id="productoTres" class="form-control input-lg" type="text"  name="productoTres" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-money-bill-alt"></i> | Precio</span> 
                    
                             
                  <input class="form-control input-lg"   name="precioTres" id="precioTres" value=""  step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo3" class="amt" value="0">
                  

                </div>

              </div>


            

           

              <!--=====================================
              PCANTIDAD PRODUCTO TRES
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fas fa-sort-amount-up-alt"></i> | Cantidad</span> 
                           
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
                        
                  <span class="input-group-addon"><i class="fab fa-product-hunt"></i> | Producto</span> 
                          
                  <input id="productoCuatro" class="form-control input-lg " type="text"  name="productoCuatro" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-money-bill-alt"></i> | Precio</span> 
                    
                             
                  <input class="form-control input-lg"   name="precioCuatro" id="precioCuatro" value="" step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo4" class="amt" value="0">
                  

                </div>

              </div>

              <!--=====================================
              PCANTIDAD PRODUCTO CUATRO
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fas fa-sort-amount-up-alt"></i> | Cantidad</span> 
                           
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
                        
                  <span class="input-group-addon"><i class="fab fa-product-hunt"></i> | Producto</span> 
                          
                  <input id="productoCinco" class="form-control input-lg " type="text"  name="productoCinco" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-money-bill-alt"></i> | Precio</span> 
                    
                             
                  <input class="form-control input-lg"   name="precioCinco" id="precioCinco" value=""   step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo5" class="amt" value="0">
                  

                </div>

              </div>

              <!--=====================================
              PCANTIDAD PRODUCTO CINCO
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fas fa-sort-amount-up-alt"></i> | Cantidad</span> 
                           
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
                        
                  <span class="input-group-addon"><i class="fab fa-product-hunt"></i> | Producto</span> 
                          
                  <input id="productoSeis" class="form-control input-lg " type="text"  name="productoSeis" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-money-bill-alt"></i> | Precio</span> 
                    
                             
                  <input class="form-control input-lg"   name="precioSeis" id="precioSeis" value=""  step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo6" class="amt" value="0">
                  

                </div>

              </div>

              <!--=====================================
              PCANTIDAD PRODUCTO SEIS
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fas fa-sort-amount-up-alt"></i> | Cantidad</span> 
                           
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
                        
                  <span class="input-group-addon"><i class="fab fa-product-hunt"></i> | Producto</span> 
                          
                  <input id="productoSiete" class="form-control input-lg " type="text"  name="productoSiete" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-money-bill-alt"></i> | Precio</span> 
                    
                             
                  <input class="form-control input-lg"   name="precioSiete" id="precioSiete" value=""   step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo7" class="amt" value="0">
                  

                </div>

              </div>

              <!--=====================================
              PCANTIDAD PRODUCTO SIETE
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fas fa-sort-amount-up-alt"></i> | Cantidad</span> 
                           
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
                        
                  <span class="input-group-addon"><i class="fab fa-product-hunt"></i> | Producto</span> 
                          
                  <input id="productoOcho" class="form-control input-lg " type="text"  name="productoOcho" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-money-bill-alt"></i> | Precio</span> 
                    
                             
                  <input class="form-control input-lg"   name="precioOcho" id="precioOcho" value=""   step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo8" class="amt" value="0">
                  

                </div>

              </div>

              <!--=====================================
              PCANTIDAD PRODUCTO OCHO
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fas fa-sort-amount-up-alt"></i> | Cantidad</span> 
                           
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
                        
                  <span class="input-group-addon"><i class="fab fa-product-hunt"></i> | Producto</span> 
                          
                  <input id="productoNueve" class="form-control input-lg " type="text"  name="productoNueve" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-money-bill-alt"></i> | Precio</span> 
                    
                             
                  <input class="form-control input-lg"   name="precioNueve" id="precioNueve" value=""  step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo9" class="amt" value="0">
                  

                </div>

              </div>

              <!--=====================================
              PCANTIDAD PRODUCTO NUEVE
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fas fa-sort-amount-up-alt"></i> | Cantidad</span> 
                           
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
                        
                  <span class="input-group-addon"><i class="fab fa-product-hunt"></i> | Producto</span> 
                          
                  <input id="productoDiez" class="form-control input-lg " type="text"  name="productoDiez" placeholder="Nombre del producto">
                  

                </div>

              </div>

              <div class="col-lg-3 productosp2">
                           
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-money-bill-alt"></i> | Precio</span> 
                    
                             
                  <input  class="form-control input-lg"   name="precioDiez" id="precioDiez" value=""   step="any" placeholder="Precio">
                  <input type="hidden" id="multiplo10" class="amt" value="0">
                  

                </div>

              </div>

              <!--=====================================
              PCANTIDAD PRODUCTO DIEZ
              ======================================-->
                  

              <div class="col-lg-3 productosp1">
                         
                <div class="input-group">
                     <span class="input-group-addon"><i class="fas fa-sort-amount-up-alt"></i> | Cantidad</span> 
                           
                  <input class="form-control input-lg"  type="number" name="cantidadDiez"  id="cantidadDiez" value="0"  min="0" step="any" placeholder="Cantidad">
                  

                </div>

              </div>
              <div class="col-sm-1">  <button type="button" class="btn btn-primary btn-circle circulo plus"><i class="fa fa-plus"></i></button>
               </div>

            </div>
            
            
            
              

              <div class="form-group row" id="Caltotal">
<hr>
                <!--=====================================
                PRODUCTO CALCULAR TOTALES
                ======================================-->
                        

                <div class="col-xs-3"></div>
                <div class="col-xs-6">
                        <span><h5><center>TOTAL</center></h5></span>
                  <div class="input-group">

                    <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span> 
                    <input type="number" class="form-control input-lg" name="pago" id="Resultado" value="0"  readonly>
                    
                    
                    <input type="hidden" class="form-control input-lg" name="cantidadProductos" id="cantidadProductos" min="0" value="0" readonly>

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
                   		<select  class="form-control input-lg" id="metodopago" name="metodo"> 
                   		<option >  Seleccione metodo de pago </option>
                   		<option value="Efectivo">  Efectivo </option>
                   		<option value="Tarjeta de Credito"> Tarjeta de Credito </option>
                   		<option value="Tarjeta de Debito"> Tarjeta de Debito </option>
                   		<option value="Transferencia bancaria">Transferencia bancaria </option>
                   		<option value="MercadoPago">MercadoPago </option>
                   		<option value="Paypal">Paypal </option>
                   		<option value="Credito">Credito </option>
                   		<option value="Puntos"> Puntos </option>
                   		</select>

              	</div>

              </div>

              <div class="form-group row" id="CAMBIORE">

                <!--=====================================
                CAMBIO A REGRESAR
                ======================================-->
                <div class="col-xs-6">
                    </br></br>
                    
                    <span><h5><center>Pago del Cliente</center></h5></span>
                  
                  <div class="input-group">
                                  
                                  
                  <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>
                         
                    <input class="form-control input-lg"  type="number" name="pagoCliente" id="pagoCliente" value="0"  min="0" step="any" placeholder="pago Cliente">
                  </div>

                </div>
                        
                <div class="col-xs-6">
                   </br></br>
                  

                </div>

                <div class="col-xs-6">
                        <span><h5><center>Cambio a Regresar</center></h5></span>
                  <div class="input-group"> 

                    <span class="input-group-addon"><i class="fas fa-cash-register"></i></span>

                    <input type="number" class="form-control input-lg" name="cambio" id="cambio" min="0" value="0"  step="any" readonly>

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
