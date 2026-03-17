<?php

if($_SESSION["perfil"] != "administrador"  AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "tecnico" AND $_SESSION["perfil"]!= "secretaria" AND $_SESSION["perfil"]!= "Super-Administrador"){



  echo '<script>



  window.location = "inicio";



  </script>';



  return;

}



?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>

<style>
    .garantia{
  background: yellow !important;
    }
    #ultimaenentrega {
    border-radius: 15px;
    background:#CCC;
    border:#000;
}

</style>
<script>
 $(document).ready(function(){
     // Ocultar imagenes de la orden
     $(".ocultarimagen").hide();
     //No se admitira numeros ni signos en el titulo
    const $input1 = document.querySelector('.nce');
    const patron = /[a-zA-Z]+/;
    $input1.addEventListener("keydown", event => {
                console.log(event.key);
                if(patron.test(event.key)){
                    $("#nombre").css({ "border": "1px solid #0C0"});
                }
                else{
                    if(event.keyCode==8){ console.log("backspace"); }
                    else{ event.preventDefault();}
                }
            });
        //Todo en MAYUS
    $("#btncompletarorden").hide();
    $("#spanboton").html("Complete su ficha tecnica");
    $("#marca").keyup(function() {
        $('#marca').val($(this).val().toUpperCase());
    });
    $("#modelo").keyup(function() {
        $('#modelo').val($(this).val().toUpperCase());
    });
    $("#numeroserial").keyup(function() {
        $('#numeroserial').val($(this).val().toUpperCase());
    });
    $("#textareaDetallesInternos").keyup(function() {
        $('#textareaDetallesInternos').val($(this).val().toUpperCase());
    });
    //No se mostrara el boton de completar si los tecnicos no llenan la ficha tecnica
    $( "#marca,#modelo,#numeroserial").keyup(function() {
        if ($("#marca").val().length >= 2){
        $("#spanmarca").html("");
        $("#spanboton").html("");
        if ($("#modelo").val().length >= 4){
        $("#spanmodelo").html("");
        $("#spanboton").html("");
        if ($("#numeroserial").val().length == 6){
        $("#spannumeroserie").html("");
        $("#spanboton").html("");
        $("#btncompletarorden").show();
        }else{
        $("#btncompletarorden").hide();
        $("#spannumeroserie").html("Debe contener los ultimos <b>6</b> digitos");
        $("#spanboton").html("Complete el campo de numero de serie");
        }
        }else{
        $("#btncompletarorden").hide();
        $("#spanboton").html("Complete el campo de modelo");
        $("#spanmodelo").html("Debe contener al menos <b>4</b> digitos");   
        }
        }else{
        $("#btncompletarorden").hide();
        $("#spanboton").html("Complete el campo de marca");
        $("#spanmarca").html("Debe contener al menos <b>2</b> digitos");
        }
    });
    $('#datatableordenes').DataTable( {
    "order": [[ 1, "desc" ]],
    "ajax":{
        "url": "../ServerSide/consultaordenes.php",
        "dataSrc":""
    },
    
    "columns":[
        {"render": function ( data, type, full, meta ) {
    return  meta.row + 1;
} },
        {data: null,
            "render": function (data, type, row, meta ) {
            return 'Comercializadora EGS'}},
        {data: null,
            "render": function (data, type, row, meta ) {
                return '<b>ORDEN: </b>'+data.id;
            }},
        {data: null,
            "render": function (data, type, row, meta ) {
            return '<b>$</b>'+data.total;}},
        {"data":"estado"},
        {"data":"fecha_ingreso"},
        {"data":"fecha"},
        {"data":"fecha_Salida"},
        {data: null,
            "render": function (data, type, row, meta ) {
            return '<a type="button" href="index.php?ruta=infoOrden&idOrden='+ data.id +'&empresa='+ data.id_empresa +'&asesor='+ data.id_Asesor +'&cliente='+ data.id_usuario +'&tecnico='+ data.id_tecnico +'&pedido='+ data.id_pedido +'" id="ButtonEditar" class="editar btn btn-warning botonEditar" target="_blank"><span class="fa fa-edit"></span><span class="hidden-xs"> Editar</span></a>';
        }
        },
        {data: null,
        "render": function (data, type, row, meta) {
            return '<a type="button" href="/extensiones/tcpdf/pdf/ticketOrden.php/?idOrden='+ data.id +'&empresa='+ data.id_empresa +'&asesor='+ data.id_Asesor +'&cliente='+ data.id_usuario +'&tecnico='+ data.id_tecnico +'" id="ButtonImprimir" class="editar btn btn-primary botonImprimir" target="_blank"><span class="fa fa-print"></span><span class="hidden-xs"> Imprimir</span></a>';
        
        }
        },
        
        ],});
            dataTable.on( 'order.dt search.dt', function () {
                dataTable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
            });

    

</script>
<div class="content-wrapper">

	

	<section class="content-header">

		

		<h1>

			Gestor Órdenes De Servicio

		</h1>



		<ol class="breadcrumb">



	      <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>



	      <li class="active">Gestor Órdenes De Servicio</li>

      

    	</ol>



	</section>



	<section class="content">



	    <div class="box"> 



	      <div class="box-header with-border">

	        

	        <?php



	        //include "inicio/grafico-ventas.php";



	        ?>



	      </div>



	      <div class="box-body">



	        <div class="box-tools">



	<div class="container" >
        <div class="row" id="ultimaenentrega">
            <div class="col-md-7">
                 <button class="btn btn-primary" style="border-radius:15px" data-toggle="modal" data-target="#modalAgregarOrden">

	          

	          Agregar Orden



	        </button>
            </div>
            <div class="col">
                <div class="row" >
	              <div >
	                <?php //https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/ticketOrden.php/?idOrden=5283&empresa=1&asesor=9&cliente=2540&tecnico=4
	                
	                $UltimaEntregada= controladorOrdenes::ctrUltimaEntrega();
    foreach ($UltimaEntregada as $key => $ultima) {
	                echo'
	                
	                
	                 <center>
	                 <div class="col-sm-2 columnaultimaentrega"><h5><b>ULTIMA ENTREGA </b>  ➜</h5></div>
	                   <div class="col-sm-2 columnaultimaentrega"><h5><b>ORDEN: '.$ultima["id"].'</b></h5></div>
	                     <div class="col-sm-1 columnaultimaentrega">';
	                     if($_SESSION["perfil"] == "administrador"){
	                         echo'<a href= "extensiones/tcpdf/pdf/ticketOrden.php/?idOrden='.$ultima["id"].'&empresa='.$ultima["id_empresa"].'&asesor='.$ultima["id_Asesor"].'&cliente='.$ultima["id_usuario"].'&tecnico='.$ultima["id_tecnico"].'" class="btn btn-success"  target="_blank" style="border-radius:15px">';}
	                    echo'<i class="fas fa-ticket-alt" style"margin-bottom: -25px; "></i></a></div>
	                 
	            </div></div></center>'; } ?>
	            </div>
	            </div>
	            </div>

	       



         <!-- <button class='btn btn-info' data-toggle='modal' data-target='#modalAsignarPedido'><i class='fa fa-sort'></i></button>-->



         </div>



          

          

	        <br>

       

         <!-- <div class="form-1-2">



            <label for="buscadroOrdenesLbael">Buscar:</label>

            <input type="text"  class="caja_Busqueda">



          </div>-->
          
	            
	            
        

          	        

	        <table id="datatableordenes" class="table stripe ordenes order-table display compact cell-border hover row-border">
 
	          <thead>

	            

	            <tr>

	              

	              <th>#</th>

                <th>Empresa</th>

	              <th>No. Orden</th>

	              <th>TOTAL</th>

	              <th>Estado</th>

	              <th>Fecha Entrada</th>

                <th>Ultima modificación</th>

                <th>Fecha de Salida</th>

                 <th>Editar</th>

                <th>Imprimir Ticket</th>

                

	            </tr>



	          </thead> 

            

            <tbody>



            </tbody>





	        </table>





	      </div>



	    </div>



	  </section>



</div>



<!--=====================================

MODAL AGREGAR OORDENES

======================================-->

<div id="modalAgregarOrden" class="modal fade" role="dialog">

  

   <div class="modal-dialog modal-lg">

     

     <div style ="border-radius: 10px;" class="modal-content">

       

       <!-- <form role="form" method="post" enctype="multipart/form-data"> -->

         

         <!--=====================================

        CABEZA DEL MODAL

        ======================================-->

        <div class="modal-header" style="border-radius: 10px 10px 0% 0%; background:#138a1e; color:white">



          <button type="button" class="close" data-dismiss="modal">&times;</button>



          <h4 class="modal-title">Agregar Orden</h4>



        </div>



        <!--=====================================

        CUERPO DEL MODAL

        ======================================-->



        <div class="modal-body">



          <div class="box-body">

            

            <!-- ENTRADA PARA EL NOMBRE DE LA EMPRESA QUE VENDE-->



            <div class="form-group">

              

              <div class="input-group">

                



                  <?php

                

                  echo'<input  type="hidden" value="'.$_SESSION["empresa"].'" class="empresa">';



                  ?>



                        

              </div>



            </div>

            <!--=====================================

            ENTRADA PARA EL TÍTULO

            ======================================-->



            <div class="form-group">

              

                <div class="input-group">

              

                  <span style ="border-radius: 3px;" class="input-group-addon"><i class='fas fa-edit'></i></span> 



                  <input style ="border-radius: 3px;"  type="text" class="form-control form-control-sm  validarOrden tituloOrden nce"  placeholder="Ingresar título orden">



                </div>



            </div>



            <!--=====================================

            ENTRADA PARA LA RUTA DEL PRODUCTO

            ======================================-->



            <div class="form-group">

              

                <div class="input-group">

              

                  <span style ="border-radius: 3px;" class="input-group-addon"><i class="fas fa-link"></i></span> 



                  <input style ="border-radius: 3px;" type="text" class="form-control form-control-sm rutaOrden" placeholder="Ruta url de la orden" readonly>



                </div>



            </div>







            <!--=====================================

            ENTRADA PARA AGREGAR MULTIMEDIA

            ======================================-->



            <div class="form-group agregarMultimedia"> 







                <!--=====================================

                ENTRADA PARA EL TECNICO

                ======================================-->             

                <div class="form-group">

                  

                  <div class="input-group">

                    

                    <span style ="border-radius: 3px;" class="input-group-addon"><i class="fas fa-cogs"></i></span>



                    <select style ="border-radius: 3px;" class="form-control form-control-sm tecnico">

                      

                      <option value="">

                        

                  Seleccionar técnico 



                      </option>



                      <?php



                        //$tecnico = ControladorAdministradores::ctrMostrarTecnicosActivos();



                      

                                  $item = "id_empresa";



                                  $valor = $_SESSION["empresa"];







                                  $tecnico = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas($item,$valor);





                        foreach ($tecnico as $key => $valueTecnicoActivo) {

                          

                          echo'<option value="'.$valueTecnicoActivo["id"].'" class="text-uppercase">'.$valueTecnicoActivo["nombre"].'</option>';

                        }





                      ?>



                    </select>



                  </div>



                </div>



                  <!--=====================================

                ENTRADA PARA EL ASESOR

                ======================================-->      

                <div class="form-group">

                  

                  <div class="input-group">

                    

                    <span style ="border-radius: 3px;" class="input-group-addon"><i class="fas fa-user"></i></span>

                      

                    <select style ="border-radius: 3px;" class="form-control form-control-sm asesor">

                      

                      <option>

                        

                        Seleccionar asesor



                      </option>

                      

                      <?php





                        //$asesorActivo = ControladorAdministradores::ctrMostrarAdministradoresActvisoEnVentas();



                                  $itemUno = "id_empresa";



                                  $valorDos = $_SESSION["empresa"];



                                  $asesorParaSelect = Controladorasesores::ctrMostrarAsesoresEmpresas($itemUno,$valorDos);



                            foreach($asesorParaSelect as $key => $valueAsesoresActivos){

                            

                             echo '<option value="'.$valueAsesoresActivos["id"].'" idAsesor='.$valueAsesoresActivos["id"].' class="seleccionarElAsesor text-uppercase">'.$valueAsesoresActivos["nombre"].'</option>';



                            }                         



                      ?>



                    </select>



                  </div>



                </div>

                

                <!--=====================================

                ENTRADA PARA EL CLIENTE

                ======================================-->   

                <div class="form-group">

                  

                  <div class="input-group">

                    

                    <span style ="border-radius: 3px;" class="input-group-addon"><i class="fas fa-user"></i></i></span>



                    <select style ="border-radius: 3px;" class="form-control  cliente" style="width: 100%; height: 100%">

                      

                      <option value="">

                        

                        Seleccionar cliente



                      </option>



                       <?php



                            $item = "id_empresa";

                            $valor = $_SESSION["empresa"];



                            $usuario = ControladorClientes::ctrMostrarClientesTabla($item,$valor);



                            foreach ($usuario as $key => $value) {

                              

                                echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';



                              }



                          ?>



                    </select>



                  </div>



                </div>

                <!--=====================================

                ENTRADA PARA EL STATUS

                ======================================-->

                <div class="form-group">

                  

                  <div class="input-group">

                    

                    <span style ="border-radius: 3px;" class="input-group-addon"><i class="fas fa-toggle-on"></i></span> 



                    <select style ="border-radius: 3px;" class="form-control form-control-sm status">



                      <option value="">Seleccionar estado</option>



                      <?php

                      

                        if ($_SESSION["perfil"] == "tecnico") {

                          

                            echo '<option value="En revisión (REV)">En revisión (REV)</option>
                                  <option value="Supervisión (SUP)">Supervisión (SUP)</option>
                                  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía</option>';

                        }



                        if ($_SESSION["perfil"] == "editor") {

                          

                            echo '<option value="En revisión (REV)">En revisión (REV)</option>

                                  <option value="Aceptado (ok)">Aceptado (ok)</option>
                                  

                                  <option value="Terminada (ter)">Terminada (ter)</option>
                                  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía</option>';

                        }

                        

                        if ($_SESSION["perfil"] == "vendedor") {

                              echo '<option value="En revisión (REV)">En revisión (REV)</option>
                              <option value="Supervisión (SUP)">Supervisión (SUP)</option>
                              <option value="Pendiente de autorización (AUT)">Pendiente de autorización (AUT)</option>
                                    <option value="Terminada (ter)">Terminada (ter)</option>
                                    <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía</option>';

                        }

                        if ($_SESSION["perfil"] == "administrador") {

                          

                            echo '<option value="En revisión (REV)">En revisión (REV)</option>

                                  <option value="Supervisión (SUP)">Supervisión (SUP)</option>

                                  <option value="Pendiente de autorización (AUT)">Pendiente de autorización (AUT)</option>

                                  <option value="Aceptado (ok)">Aceptado (ok)</option>

                                  <option value="Terminada (ter)">Terminada (ter)</option>

                                  <option value="Cancelada (can)">Cancelada (can)</option>

                                  <option value="Entregado (Ent)">Entregado (Ent)</option>

                                  <option value="Sin reparación (SR)">Sin reparación (SR)</option>

                                  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía</option>
                                  <option class="garantia" value="Garantía aceptada (GA)">Garantía aceptada (GA)</option>';

                        }



                      ?>

                    

                      

                       

                       



                       



                       

                       

                       



                    </select>



                  </div>



                </div>

              <!--=====================================

              SUBIR MULTIMEDIA DE PRODUCTO FÍSICO

              ======================================-->

              

			     <div class="multimediaOrden needsclick dz-clickable">



                <div class="dz-message needsclick cuadro multimedia" style="border-radius: 20px; background: #CDCDCD; border: 3px dashed black; height: 150px; font-size: 25px; font-weight: bold;">

                  

                  <center>Arrastrar o dar click para subir imagenes</center><br>

                  <center><span class="glyphicon glyphicon-open" aria-hidden="true" style="font-size:60px;"></span></center>



                </div>



              </div>



            </div>
                          
            <!--=====================================

            MARCA

            ======================================-->
            <div class="form-group">

              

              <div class="input-group">

              

                <span style ="border-radius: 3px;" class="input-group-addon"><i class="far fa-copyright"></i></span> 



                <input style ="border-radius: 3px;" id="marca" class="form-control form-control-lg marcaDelEquipo"  type="text" placeholder="Marca del equipo" name="marcaDelEquipo"><span id="spanmarca" style="color:red;" ></span>



            </div>
            
             <!--=====================================

            MODELO

            ======================================-->
            <div class="form-group">

              

              <div class="input-group">

              

                <span style ="border-radius: 3px;" class="input-group-addon"><i class="fas fa-kaaba"></i></span> 



                <input style ="border-radius: 3px;" id="modelo" class="form-control form-control-lg modeloDelEquipo"  type="text" placeholder="Modelo del equipo" name="modeloDelEquipo"><span id="spanmodelo" style="color:red;"></span>



            </div>
            
            <!--=====================================

            NUMERO DE SERIE

            ======================================-->
            <div class="form-group">

              

              <div class="input-group">

              

                <span style ="border-radius: 3px;" class="input-group-addon"><i class="fas fa-barcode"></i></span> 


                <input style ="border-radius: 3px;" id="numeroserial" class="form-control form-control-lg numeroDeSerieDelEquipo"  type="text" placeholder="Numero Serial" name="numeroDeSerieDelEquipo"><span id="spannumeroserie" style="color:red;"></span>



            </div>

           

         



           <!--=====================================

            AGREGAR DESCRIPCIÓN

            ======================================-->



            <div class="form-group">

              

              <div class="input-group">

              

                <span style ="border-radius: 3px;" class="input-group-addon"><i class='fas fa-edit'></i></span>



                <textarea style ="border-radius: 3px; text-transform: uppercase;"  type="text" rows="3" class="form-control form-control-sm descripcionOrden" id="textareaDetallesInternos" placeholder="Ingresar detalles internos" ></textarea>



              </div>



            </div>



                <div class="form-group row">



                



            <!--=====================================

            ENTRADA PARA AGREGAR NUEVA PARTIDA

            ======================================-->



            <div class="form-group row ocultarimagen">
                

              <div class="col col-lg-6">

              <div class="panel"><h3>SUBIR FOTO PORTADA</h3></div>



              <input type="file" class="fotoPortada form-control-file">

              <input type="hidden" class="antiguaFotoPortada">



              <p class="help-block">Tamaño recomendado 1280px * 720px <br> Peso máximo de la foto 2MB</p>

              </div>

              <div class="col col-lg-6">



              <img loading="lazy" src="vistas/img/cabeceras/default/default.jpg" class="img-thumbnail previsualizarPortada" style="width=200px;">

              </div>



            </div>



            <!--=====================================

            AGREGAR FOTO DE MULTIMEDIA

            ======================================-->



            <div class="form-group row ocultarimagen">

                <div class="col col-lg-6">

              <div class="panel"><h3>SUBIR FOTO PRINCIPAL DEL PRODUCTO</h3></div>



              <input type="file" class="fotoPrincipal form-control-file">



              <p class="help-block">Tamaño recomendado 400px * 450px <br> Peso máximo de la foto 2MB</p>

              </div>

              <div class="col col-lg-6">



              <img loading="lazy" src="vistas/img/productos/default/default.jpg" class="img-thumbnail previsualizarPrincipal" style="width=200px;">

              </div>



            </div>

              

        

            <!--=====================================

            PARTIDA UNO

            ======================================

            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida1 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio1 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

            <!--=====================================

            PARTIDA DOS

            ======================================

            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida2 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio2 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

            <!--=====================================

            PARTIDA TRES

            ======================================



          <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida3 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio3 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

            <!--=====================================

            PARTIDA CUATRO

            ======================================



          <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida4 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio4 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

            <!--=====================================

            PARTIDA CINCO

            ======================================



                      <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida5 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio5 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

            <!--=====================================

            PARTIDA SEIS

            ======================================

                      <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida6 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio6 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

            <!--=====================================

            PARTIDA SIETE

            ======================================

                      <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida7 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio7 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

            <!--=====================================

            PARTIDA OCHO

            ======================================

                                  <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida8 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio8 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

            <!--=====================================

            PARTIDA NUEVE

            ======================================

            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida9 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio9 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

            <!--=====================================

            PARTIDA DIEZ

            ======================================

            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida10 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio10 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>-->

        </div>





                    </div>



                     <div class="form-group">

                
            <!--=====================================
            BOTON AGREGAR PARTIDAS
            ======================================-->
            <div class="panel"><h3>AGREGAR PARTIDAS</h3></div>

                 <a href="#" onclick="AgregarCampos();">

                    <div id="campos">

                      <input type="button" class="btn btn-primary " value="Agregar Partida"/>
                      
                  </a>
                       <!--<input type="button" class="btn btn-success" id="agregarCaracteristicas" value="Agregar Caracteristicas"/></br></br>-->

                  
            </div>



            <!--=====================================

                PRODUCTO CALCULAR TOTALES

                ======================================-->

                 <div class="form-group row">



                <!--=====================================

                CAMBIO A REGRESAR

                ======================================-->

                        

                <div class="col-xs-6">



                 



                </div>



                <div class="col-xs-6">

                        <span><h5><center>TOTAL</center></h5></span>

                  <div class="input-group"> 



                    <span style ="border-radius: 3px;" class="input-group-addon"><i class="fas fa-hand-holding-usd"></i></span>



                    <input style ="border-radius: 3px;" type="number" class="form-control input-lg totalOrden"  min="0" value="0"  step="any" readonly>



                  </div>



                </div>



              </div>

              </div>           



          </div>





        <!--=====================================

        PIE DEL MODAL

        ======================================-->



        <div class="modal-footer">



          <div class="preload"></div>

  

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>



          <button type="button" id="btncompletarorden" class="btn btn-primary guardarOrden">Guardar Orden</button>
          

<span style="color:red; float:right;" id="spanboton"></span>

        </div>



       </form> 



     </div>



   </div>



</div>

<!--=====================================

MODAL AGREGAR OBSERVACION

======================================



<div id="modalAgregarDetalle" class="modal fade" role="dialog">

  

  <div class="modal-dialog">



    <div class="modal-content">-->



        <!--=====================================

        CABEZA DEL MODAL

        ======================================



        <div class="modal-header" style="background:#138a1e; color:white">



          <button type="button" class="close" data-dismiss="modal">&times;</button>



          <h4 class="modal-title">Agregar Observacion</h4>



        </div>-->



        <!--=====================================

        CUERPO DEL MODAL

        ======================================



        <div class="modal-body">



          	<div class="box-body">



          		<div class="form-group">

                

              		<div class="panel">AGREGAR NUEVA OBSERVACION</div>



                 		<a href="#" onclick="AgregarObservacion();">

                          <div id="camposObservacion">

                         	

                         	<input type="button" class="btn btn-primary " value="Agregar observación"/></br></br>

                          <?php



                         // echo'<input type="hidden" class="creador" value="'.$_SESSION["id"].'">

                            //  <input type="hidden" class="idOrdenObservacion">';



                          ?>

                          </a>e

                    </div>

            </div>-->



             <!--=====================================

            OBSERVACION UNO

            ======================================

             <div class="form-group row">

              

               <div class="col-xs-6">



                <div class="input-group">

                  <input class="form-control input-lg fecha" type="text"  readonly>

                  </div>

              </div>



              <div class="col-xs-6">

              

                <div class="input-group">



                <textarea type="text"  rows="3" class="form-control input-lg observacionUno" placeholder="Ingresar detalles internos" readonly></textarea>



                <span class="input-group-addon"><i class="fa fa-edit"></i></span> 



              </div>

            </div>



          </div>

  

          </div>



        </div>-->



        <!--=====================================

        PIE DEL MODAL

        ======================================

  

        <div class="modal-footer">



          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>



          <button type="submit" class="btn btn-primary agregarObservacion">Agregar observación</button>



        </div>





    </div>



  </div>



</div>-->



<!--=====================================

MODAL EDITAR ORDEN

======================================-->

<div id="modalEditarOrden" class="modal fade" role="dialog">

  

  <div class="modal-dialog">

    

    <div class="modal-content">

      

        <!--=====================================

        CABEZA DEL MODAL

        ======================================-->

        <div class="modal-header" style="background: #138a1e; color:white;">

          

          <button type="button" class="close" data-dissmiss="modal">&times;</button>



          <center><h2><b>ORDEN:</b></h2><h2 class="modal-title NumeroDeOrden"></h2></center>



        </div>



        <!--=====================================

        CUERPO DEL MODAL

        ======================================-->

        <div class="modal-body">

          

          <div class="box-body">

            <!--=====================================

            ENTRADA PARA EL TÍTULO

            ======================================-->

            <div class="form-group">

              

              <div class="input-group">

              

                <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span> 



                <input type="text" class="form-control input-lg validarOrden tituloOrden" readonly>



                <input type="hidden" class="idOrden">



              </div>



            </div>



            <!--=====================================

            ENTRADA PARA LA RUTA DE LA ORDEN

            ======================================-->

            <div class="form-group">

              

              <div class="input-group">

                

                <span class="input-group-addon"><i class="fas fa-link"></i></span>



                <input type="text" class="form-control input-lg rutaOrden" readonly>



              </div>



            </div>

            <!--=====================================

            ENTRADA PARA EL TECNICO

            ======================================-->

            <div class="form-group">

              

              <div class="input-group">

                

                <span class="input-group-addon"><i class="fas fa-cogs"></i></span>



                <select class="form-control input-lg seleccionarTecnico">

                  

                  <option class="optionEditarTecnico"></option>



                  <?php



                     $item = null;

                      $valor = null;



                      $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);



                      foreach ($tecnico as $key => $value) {

                        

                        echo '



                        <option value="'.$value["id"].'">'.$value["nombre"].'</option>';



                      }



                  ?>



                </select>



              </div>



            </div>

            <!--=====================================

            ENTRADA PARA EL ASESOR

            ======================================-->

            <div class="form-group">

              

              <div class="input-group">

                

                <span class="input-group-addon"><i class="fas fa-user"></i></span>



                <select class="form-control input-lg seleccionarAsesor">

                  

                  <option class="optionEditarAsesor"></option>



                  <?php



                     $item = null;

                      $valor = null;



                      $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);



                      foreach ($asesor as $key => $value) {

                        

                        echo '



                        <option value="'.$value["id"].'">'.$value["nombre"].'</option>';



                      }



                  ?>



                </select>



              </div>



            </div>

            <!--=====================================

            ENTRADA PARA EL CLIENTE

            ======================================-->

            <div class="form-group">

              

              <div class="input-group">

                

                <span class="input-group-addon"><i class="fas fa-user"></i></span>



                <select class="form-control input-lg seleccionarCliente" readonly>

                  

                  <option class="optionEditarCliente" ></option readonly>



                  <?php



                     $item = null;

                      $valor = null;



                      $usuario = ControladorClientes::ctrMostrarClientesOrdenes($item,$valor);



                      foreach ($usuario as $key => $value) {

                        

                        echo '



                        <option value="'.$value["id"].'">'.$value["nombre"].'</option>';



                      }



                  ?>



                </select>



              </div>



            </div>



            <?php



            if ($_SESSION["perfil"] != "tecnico") {

             

              echo'<div class="form-group">



                    <div class="input-group">



                      <span class="input-group-addon"><i class="fas fa-envelope"></i></span>



                      <input type="email" class="form-control input-lg correoCliente" readonly>



                    </div>





              </div>

              <div class="form-group">



                    <div class="input-group">



                      <span class="input-group-addon"><i class="fas fa-headphones"></i></span>



                      <input type="tel" class="form-control input-lg numeroCliente" readonly>



                    </div>





              </div>

               <div class="form-group">



                    <div class="input-group">



                      <span class="input-group-addon"><i class="fas fa-headphones"></i></span>



                      <input type="tel" class="form-control input-lg numeroClienteDos" readonly>



                    </div>





              </div>';



            }



            ?>

            <!--=====================================

            ENTRADA PARA EL ESTADO

            ======================================-->

            <div class="form-group">

              

              <div class="input-group">

                

                <span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>



                <select class="form-control input-lg seleccionarEstatus">

                  

                  <option class="optionEditarEstatus"></option>



                  <?php

                      

                        if ($_SESSION["perfil"] == "tecnico") {

                          

                            echo '<option class="pen" value="En revisión (REV)" style="display:none">En revisión (REV)</option>



                                  <option class="sup" value="Supervisión (SUP)" style="display:none">Supervisión (SUP)</option>



                                  <option class="ter" value="Terminada (ter)" style="display:none">Terminada (ter)</option>
                                  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía</option>';

                        }



                        if ($_SESSION["perfil"] == "editor") {

                          

                            echo '<option class="pen" value="En revisión (REV)" style="display:none">En revisión (REV)</option>

                                 <option class="aut" value="Pendiente de autorización (AUT)" style="display:none">Pendiente de autorización (AUT)</option>

                                  <option class="ok" value="Aceptado (ok)" style="display:none">Aceptado (ok)</option>

                                  <option class="ter" value="Terminada (ter)" style="display:none">Terminada (ter)</option>

                                  <option class="ent" value="Entregado (Ent)" style="display:none">Entregado (Ent)</option>
                                  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía</option>';

                        }

                         

                        if ($_SESSION["perfil"] == "vendedor") {

                              echo '<option value="En revisión (REV)">En revisión (REV)</option>

                                  <option value="Aceptado (ok)">Aceptado (ok)</option>

                                  <option value="Terminada (ter)">Terminada (ter)</option>

                                  <option value="Entregado (Ent)">Entregado (Ent)</option>
                                  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía</option>';

                        }

                        if ($_SESSION["perfil"] == "administrador") {

                          

                            echo '<option class="pen" value="En revisión (REV)">En revisión (REV)</option>

                                  <option class="sup" value="Supervisión (SUP)">Supervisión (SUP)</option>

                                  <option class="aut" value="Pendiente de autorización (AUT)">Pendiente de autorización (AUT)</option>

                                  <option class="ok" value="Aceptado (ok)">Aceptado (ok)</option>

                                  <option class="ter" value="Terminada (ter)">Terminada (ter)</option>

                                  <option class="can" value="Cancelada (can)">Cancelada (can)</option>

                                  <option class="ent" value="Entregado (Ent)">Entregado (Ent)</option>

                                  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía</option>
                                  <option class="garantia"  value="Garantía aceptada (GA)"> Garantía aceptada (GA)</option>';

                        }



                      ?>



                </select>



              </div>



            </div>

            <!--=====================================

            AGREGAR MULTIMEDIA

            ======================================-->

            <div class="form-group agregarMultimedia">

              

              <div class="row previsualizarImgFisico"></div>



              <div class="multimediaFisica needsclick dz-clickable">

                

                <div class="dz-message needsclick">

                  

                  Arrastrar o dar click para subir imagenes.



                </div>



              </div>



            </div>
            <!--=====================================

            CATEGORIA DE EQUIPO

            
            
            <div class="form-group">

              

              <div class="input-group disabled">

              

                <span class="input-group-addon"><i class="fab fa-buromobelexperte"></i></span> 



                <select class="form-control" id="exampleFormControlSelect1">
                  <option>ALL IN ONE</option>
                  <option>PC ESCRITORIO</option>
                  <option>LAPTOP</option>
                  <option>TABLET</option>
                  <option>CELULAR</option>
                  <option>IMPRESORA</option>
                </select>



            </div>
            ======================================-->
            
            
            <!--=====================================

            MODELO

            ======================================-->
            <div class="form-group">

              

              <div class="input-group disabled">

              

                <span class="input-group-addon"><i class="fas fa-kaaba"></i></span> 



                <input class="form-control form-control-lg" type="text" placeholder="Modelo del equipo">



            </div>
            <!--=====================================

            NUMERO DE SERIE

            ======================================-->
            <div class="form-group">

              

              <div class="input-group disabled">

              

                <span class="input-group-addon"><i class="fas fa-barcode"></i></span> 



                <input class="form-control form-control-lg" type="text" placeholder="Numero Serial">



            </div>



            <!--=====================================

            DETALLES INTERNOS

            ======================================-->

            <div class="form-group">

              

              <div class="input-group">

              

                <span class="input-group-addon"><i class="fas fa-edit"></i></span> 



                <textarea type="text"  rows="3" class="form-control input-lg descripcionOrden" placeholder="Ingresar detalles internos"></textarea>



            </div>
            
            



            <!--=====================================

            AGREGAR FOTO DE PORTADA

            ======================================-->

            <div class="form-group row">

              <div class="col col-lg-6">

              <div class="panel"><h3>SUBIR FOTO PORTADA</h3></div>



              <input type="file" class="fotoPortada form-control-file">

              <input type="hidden" class="antiguaFotoPortada">



              <p class="help-block">Tamaño recomendado 1280px * 720px <br> Peso máximo de la foto 2MB</p>

              </div>

              <div class="col col-lg-6">



              <img loading="lazy" src="vistas/img/cabeceras/default/default.jpg" class="img-thumbnail previsualizarPortada" style="width=200px;">

              </div>



            </div>



            <!--=====================================

            AGREGAR FOTO DE MULTIMEDIA

            ======================================-->



            <div class="form-group row">

                <div class="col col-lg-6">

              <div class="panel"><h3>SUBIR FOTO PRINCIPAL DEL PRODUCTO</h3></div>



              <input type="file" class="fotoPrincipal form-control-file">



              <p class="help-block">Tamaño recomendado 400px * 450px <br> Peso máximo de la foto 2MB</p>

              </div>

              <div class="col col-lg-6">



              <img loading="lazy" src="vistas/img/productos/default/default.jpg" class="img-thumbnail previsualizarPrincipal" style="width=200px;">

              </div>



            </div>

            <!--=====================================

            PARTIDA UNO

            ======================================-->

            <div class="form-group row">



              <div class="col-xs-">



                <div class="input-group"> 



                  <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida1 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)" readonly></textarea> 



                </div> 



              </div>



              <div>



                <div class="col-xs-6">



                  <div class="input-group">



                    <input class="form-control input-lg precio1 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio" readonly>



                    <span class="input-group-addon" ><i class="fas fa-dollar-sign"></i></span>



                  </div>



                </div>



              </div>



            </div>



            <!--=====================================

            PARTIDA DOS

            ======================================-->

            <div class="form-group row">



              <div class="col-xs-6">



                <div class="input-group"> 



                  <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida2 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)" ></textarea> 



                </div> 



              </div>



              <div>



                <div class="col-xs-6">



                  <div class="input-group">



                    <input class="form-control input-lg precio2 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio" >



                    <span class="input-group-addon" ><i class="fas fa-dollar-sign"></i></span>



                  </div>



                </div>



              </div>



            </div>

            <!--=====================================

            PARTIDA TRES

            ======================================-->

            <div class="form-group row">



              <div class="col-xs-6">



                <div class="input-group"> 



                  <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida3 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)" ></textarea> 



                </div> 



              </div>



              <div>



                <div class="col-xs-6">



                  <div class="input-group">



                    <input class="form-control input-lg precio3 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio" >



                    <span class="input-group-addon" ><i class="fas fa-dollar-sign"></i></span>



                  </div>



                </div>



              </div>



            </div>

            <!--=====================================

            PARTIDA CUATRO

            ======================================-->

            <div class="form-group row">



              <div class="col-xs-6">



                <div class="input-group"> 



                  <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida4 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 



              </div>



              <div>



                <div class="col-xs-6">



                  <div class="input-group">



                    <input class="form-control input-lg precio4 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">



                    <span class="input-group-addon" ><i class="fas fa-dollar-sign"></i></span>



                  </div>



                </div>



              </div>



            </div>

            <!--=====================================

            PARTIDA CINCO

            ======================================-->

            <div class="form-group row">



              <div class="col-xs-6">



                <div class="input-group"> 



                  <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida5 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 



              </div>



              <div>



                <div class="col-xs-6">



                  <div class="input-group">



                    <input class="form-control input-lg precio5 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">



                    <span class="input-group-addon" ><i class="fas fa-dollar-sign"></i></span>



                  </div>



                </div>



              </div>



            </div>



            <!--=====================================

            PARTIDA SEIS

            ======================================-->

            <div class="form-group row">



              <div class="col-xs-6">



                <div class="input-group"> 



                  <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida6 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 



              </div>



              <div>



                <div class="col-xs-6">



                  <div class="input-group">



                    <input class="form-control input-lg precio6 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">



                    <span class="input-group-addon" ><i class="fas fa-dollar-sign"></i></span>



                  </div>



                </div>



              </div>



            </div>



            <!--=====================================

            PARTIDA SIETE

            ======================================-->

            <div class="form-group row">



              <div class="col-xs-6">



                <div class="input-group"> 



                  <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida7 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 



              </div>



              <div>



                <div class="col-xs-6">



                  <div class="input-group">



                    <input class="form-control input-lg precio7 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">



                    <span class="input-group-addon" ><i class="fas fa-dollar-sign"></i></span>



                  </div>



                </div>



              </div>



            </div>

            <!--=====================================

            PARTIDA OCHO

            ======================================-->

            <div class="form-group row">



              <div class="col-xs-6">



                <div class="input-group"> 



                  <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida8 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 



              </div>



              <div>



                <div class="col-xs-6">



                  <div class="input-group">



                    <input class="form-control input-lg precio8 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">



                    <span class="input-group-addon" ><i class="fas fa-dollar-sign"></i></span>



                  </div>



                </div>



              </div>



            </div>

            <!--=====================================

            PARTIDA NUEVE

            ======================================-->

            <div class="form-group row">



              <div class="col-xs-6">



                <div class="input-group"> 



                  <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida9 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 



              </div>



              <div>



                <div class="col-xs-6">



                  <div class="input-group">



                    <input class="form-control input-lg precio9 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">



                    <span class="input-group-addon" ><i class="fas fa-dollar-sign"></i></span>



                  </div>



                </div>



              </div>



            </div>



            <!--=====================================

            PARTIDA DIEZ

            ======================================-->

            <div class="form-group row">



              <div class="col-xs-6">



                <div class="input-group"> 



                  <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida10 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 



              </div>



              <div>



                <div class="col-xs-6">



                  <div class="input-group">



                    <input class="form-control input-lg precio10 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">



                    <span class="input-group-addon" ><i class="fas fa-dollar-sign"></i></span>



                  </div>



                </div>



              </div>



            </div>

            <!--=====================================

            TOTAL ORDEN

            ======================================-->

            <div class="form-group row">

            <!--=====================================

            CAMBIO A REGRESAR

            ======================================-->

            <div class="col-xs-6">

                



            </div>



            <div class="col-xs-6">

              <span><h5><center>TOTAL</center></h5></span>

              <div class="input-group"> 



                <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>



                <input type="number" class="form-control input-lg totalOrdenEditar"  min="0" value="0"  step="any" readonly>



               </div>                 

                

              </div>



            </div>



              <!--=====================================

                SELECCIONAR PEDIDO

                ======================================-->

                <div class="form-group">

                  

                  <select class="form-control input-lg selActivarPedido">

                    

                    <option value="">No tiene pedido</option>



                    <option value="pedido">Activar Pedido</option>

                    

                  </select>                  



                </div> 

              <!--=====================================

              DATOS DEL PEDIDO

              ======================================-->

              <div class="datosPedido" style="display:none">

                

                  <select class="form-control input-lg seleccionarPedido">

                  

                    <option class="optionEditarPedidos"></option>



                    <?php



                       $item = null;

                        $valor = null;



                        $pedido = ControladorPedidos::ctrMostrarorpedidosParaValidar($item,$valor);



                        foreach ($pedido as $key => $value) {

                          

                          echo '



                          <option value="'.$value["id"].'">'.$value["id"].'</option>';



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



          <div class="preload"></div>

  

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>



          <button type="button" class="btn btn-primary guardarCambiosOrden">Guardar cambios</button>



        </div>



    </div>



  </div>



</div>

</div>

<?php



  $eliminarOrden = new controladorOrdenes();

  $eliminarOrden -> ctrEliminarOrden();

?>

