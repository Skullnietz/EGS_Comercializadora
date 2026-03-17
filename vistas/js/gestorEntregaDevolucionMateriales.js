/*=============================================
ENTREGA Y DEVOLUCION DE MATERIAL
=============================================*/
$(".tablapeticionesmaterial").on("click", ".btnActivar", function(){

  var id_peticionM = $(this).attr("id_peticionM");
  var estadoPeticion = $(this).attr("estadoPeticion");

  var datos = new FormData();
  datos.append("activarid_peticionM", id_peticionM);
    datos.append("activarPeticion", estadoPeticion);

    $.ajax({

    url:"ajax/PeticionMaterial.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
      contentType: false,
      processData: false,
      success: function(respuesta){
        //console.log("respuesta", respuesta);
      }

    })

    if(estadoPeticion == 0){

      $(this).removeClass('btn-success');
      $(this).addClass('btn-danger');
      $(this).html('No entregado');
      $(this).attr('estadoPeticion',1);

    }else{

      $(this).addClass('btn-success');
      $(this).removeClass('btn-danger');
      $(this).html('Entregado');
      $(this).attr('estadoPeticion',0);

    }

})
