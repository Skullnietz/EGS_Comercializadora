
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    
    <?php

if($_SESSION["perfil"] != "administrador"  AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "tecnico" AND $_SESSION["perfil"]!= "secretaria" AND $_SESSION["perfil"]!= "Super-Administrador"){



  echo '<script>



  window.location = "index.php?ruta=ordenes";



  </script>';



  return;

}
?>
<div class="content-wrapper">

	

<section class="content-header">

 <h1>

    PETICIÓN MODIFACACIÓN DE ORDEN

  </h1>
<ol class="breadcrumb">



      <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>



      <li class="active">Modificación</li>

    

    </ol>
    
    </section>

  <section class="content">



    <div class="box"> 



       <div class="box-body">



          

         <br>


<div class="container">
    <h3>

Datos para modificación

</h3>
<div class="row">
        <div class="col-lg-5">
            <form method="POST">
                  <!--ASIGNAR ORDEN -->
                <div class="form-group">

                  

                  <div class="input-group">

                    

                    <span style ="border-radius: 3px;" class="input-group-addon"><i class="fas fa-clipboard-list"></i></span>



                    <select style ="border-radius: 3px;" class="form-control form-control-sm orden" name=material_orden>
                  
                      <option value=""id="material_orden">

                        

                  Seleccionar orden

                      </option>

                    <?php

                       $item = "id_empresa";
                        $valor = $_SESSION["empresa"];

                        $pedido = controladorOrdenes::ctrMostrarOrdenes($item,$valor);

                        foreach ($pedido as $key => $valueOrdenes) {
                          
                          echo '

                          <option value="'.$valueOrdenes["id"].'">'.$valueOrdenes["id"].'</option>';

                        }

                    ?>
                     </select>

                </div> 
                   </div>
                 
<!--=====================================

                ENTRADA PARA EL TECNICO

                ======================================-->             

                <div class="form-group">

                  

                  <div class="input-group">

                    

                    <span style ="border-radius: 3px;" class="input-group-addon"><i class="fas fa-user-cog"></i></span>



                    <select style ="border-radius: 3px;" class="form-control form-control-sm tecnico">

                      

                      <option value="">

                        

                  Seleccionar técnico 



                      </option>



                      <?php

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

            DEPARTAMENTO DE DESARROLLO

            ======================================-->

<div class=""><h4>Departamento desarrollo </h4></div>
            <div class="form-group">


              <div class="input-group">

              
                <span style ="border-radius: 3px;" class="input-group-addon"><i class="fas fa-user-tie"></i></span>



                <textarea style ="border-radius: 3px;"  type="text" rows="1" class="form-control form-control-sm descripcionOrden" id="textareaDetallesInternos" placeholder="Nombre de quien realiza la modificación"></textarea>



              </div>



            </div>

                <!--=====================================

            ACCCIONES A REALIZAR CON LA ORDEN 

            ======================================-->


<div class=""><h4>Accciones a realizar con la orden</h4></div>
            <div class="form-group">

              

              <div class="input-group">

              

                <span style ="border-radius: 3px;" class="input-group-addon"><i class='fas fa-edit'></i></span>



                <textarea style ="border-radius: 3px;"  type="text" rows="3" class="form-control form-control-sm descripcionOrden" id="acciones a realizar" name="" placeholder="Ingresar acciones a realizar con la orden"></textarea>



              </div>



            </div>
            
             <!--=====================================

            MOTIVOS PARA REALIZAR CAMBIOS
            ======================================-->


<div class=""><h4>Motivos por el cual se requiere la acción</h4></div>
            <div class="form-group">

             <div class="input-group">


                <span style ="border-radius: 3px;" class="input-group-addon"><i class='fas fa-edit'></i></span>


                <textarea style ="border-radius: 3px;"  type="text" rows="3" class="form-control form-control-sm descripcionOrden" id="textareaDetallesInternos"  placeholder="Especifica los motivos por el cual requiere acciones o cambios en la orden"></textarea>



              </div>



            </div>
            
            <!--=====================================

           Fecha y hora
            ======================================-->
            <div class=""><h4>Fecha y hora de realización de cambios en la orden</h4></h4></div>
             <?php 

# Configurar hora
date_default_timezone_set("America/Mexico_City");
# Después de configurar
echo " " . date("d-m-Y  h:i.s a");


?>


          <!--=====================================

            BOTON GUARDAR
            ======================================-->
 <div class="modal-footer">



          <div class="preload"></div>

  

           <button onclick="setTimeout(function(){location.reload();});" class="btn btn-default pull-left" data-dismiss="modal">Recargar</button>


          <button type="button" id="btncompletarorden" class="btn btn-primary guardarOrden">Guardar Petición</button>
          
          <?php

         // $crearpeticionO = new ControladorPeticionO();
          //$crearpeticionO -> ctrlCrearPeticionO();

        ?>


        </div>



       </form> 



     </div>

<div class="col-lg-7">
            <h3>Modificaciones recientes</h3>
             <table class="table table-bordered table-striped dt-responsive" width="100%">

  <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Orden para modificar</th>
      <th scope="col">Tecnico</th>
      <th scope="col">Departamento desarrollo</th>
      <th scope="col">Acciones</th>
      <th scope="col">Motivos</th>
      <th scope="col">Hora y fecha de modificacion</th>
    </tr>
  </thead>
   </div>

            


<?php

      $peticionesorden = ControladorPeticionO::ctrMostrarUltimasPeticionesO($tabla,$valor);
      
foreach ($peticionesorden as $key => $value) {
 $fecha = date("Y-m-d h:i:s",strtotime($fechahoracambios));
                  //TRAER TECNICO

                  $item = "id";

                  $valor = $value["tecnico_solicita"];



                  $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);
                  $NombreTecnico = $tecnico["nombre"];
                  
   echo'<tr>
    <th scope="row"> '.$value["id_peticionO"].'</th>
    
    <td> '.$value["orden_mod"].'</td> 
    
   <td>  '.$NombreTecnico.'</td>

    <td> '.$value["dep_desarrollo"].'</td>
      
    <td> '.$value["acciones"].' </td>
    
     <td> '.$value["motivos"].' </td>
     
      <td> '.$value["fecha_hora"].' </td>';
     
   
    echo'
   
    <td><button class="btn btn-sm btn-info" data-toggle="modal" data-target="	imprimir"><i class="fas fa-ticket-alt"></i> Imprimir</button>';
    
}
              
?>
</table>
</div>
<?php

//$peticionesorden = new ControladorPeticionO();
//$peticionesorden-> ctrMostrarUltimasPeticionesO();
?>
        </div>
  


</div>

  


