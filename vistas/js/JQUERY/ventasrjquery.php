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


    
function recalcularTotalVR() {
    var granTotal = 0;
    var totalProductos = 0;
    var sufijos = ['Uno','Dos','Tres','Cuatro','Cinco','Seis','Siete','Ocho','Nueve','Diez'];
    for(var i=0; i<10; i++){
        var s = sufijos[i];
        var c = parseFloat($('#cantidad'+s).val()) || 0;
        var p = parseFloat($('#precio'+s).val()) || 0;
        var mult = c * p;
        $('#multiplo'+(i+1)).val(mult);
        granTotal += mult;
        totalProductos += c;
    }
    
    $('#Resultado').val(granTotal).trigger('change');
    $('#Resultado').addClass('positivo');
    setTimeout(function () { $('#Resultado').removeClass('positivo'); }, 2000);
    $('#cantidadProductos').val(totalProductos);
}

var selectoresArray = [];
['Uno','Dos','Tres','Cuatro','Cinco','Seis','Siete','Ocho','Nueve','Diez'].forEach(function(s){
    selectoresArray.push('#cantidad'+s);
    selectoresArray.push('#precio'+s);
});
$(document).on('change input', selectoresArray.join(','), recalcularTotalVR);

$(".minus").click(function() {
    setTimeout(recalcularTotalVR, 50);
});
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