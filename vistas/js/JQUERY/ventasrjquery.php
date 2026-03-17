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
    const patron2 = /[0-9a-zA-ZñÑ ÉÁÍÓ()]+/;
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