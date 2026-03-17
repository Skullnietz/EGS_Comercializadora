<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    
<?php




if($_SESSION["perfil"] != "administrador"  AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "tecnico" AND $_SESSION["perfil"]!= "secretaria" AND $_SESSION["perfil"]!= "Super-Administrador"){

 

  echo '<script>



  window.location = "inicio";

  </script>';

  

  return;



}?>
<div class="content-wrapper">

	

<section class="content-header">

 <h1>

    Petición de Material

  </h1>
<ol class="breadcrumb">
    



      <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>



      <li class="active">Petición material</li>


    </ol>
    
    </section>

  <section class="content">



    <div class="box"> 



       <div class="box-body">



          

         <br>


<div class="container">
    <h3>

Datos para solicitar material 

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



                    <select style ="border-radius: 3px;" class="form-control form-control-sm tecnico" name="tecnico_solicita">

                      

                      <option value="" id="tecnico_solicita">

                  Seleccionar técnico 

                      </option>

                 
 
                     //TRAER TECNICO

                  
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

            Material solicitado 
            ======================================-->


<div class=""><h4>Material que solicita</h4></div>
            <div class="form-group">

             <div class="input-group">


                <span style ="border-radius: 3px;" class="input-group-addon"><i class="fas fa-tools"></i></i></span>


                <textarea style ="border-radius: 3px;"  type="text" rows="3" class="form-control form-control-sm material materialsolicitado" name="material_solicitado"  placeholder="Coloque el material o materiales que solicita"></textarea>



              </div>

 <!--=====================================

            Material entregado 
            ======================================-->

   <div class="form-group">

             <div class="input-group">


                <input type="hidden"  name="entregado"  placeholder=
  </div>
</div>

            
 
</input>
         </div>   
 <!--=====================================

            ENTREGA
            ======================================-->

            </div>
           
            <div class="form-group">

             <div class="input-group">


                <input type="hidden"  name="entrega"  placeholder="<?php 

# Configurar
date_default_timezone_set("America/Mexico_City");
# Después de configurar
echo " " . date("d-m-Y  h:i.s a");


?>"
  </div>
</div>

            
 
</input>
         </div>   
             <!--=====================================

           DEVOLUCION
            ======================================-->

            <div class="form-group">

             <div class="input-group">


                <input type="hidden"  name="devolucion"  placeholder="<?php 
     date_default_timezone_set("America/Mexico_City");
$mifecha= date('d-m-Y  h:i.s a'); 
$NuevaFecha = strtotime ( '+2 hour' , strtotime ($mifecha) ) ; 
$NuevaFecha = date ( 'd-m-Y  h:i.s a' , $NuevaFecha); 
echo $NuevaFecha;

?>"
  </div>
</div>

            
 
</input>
         </div>
         <!--=====================================

            Material devuelto
            ======================================-->

   <div class="form-group">

             <div class="input-group">


                <input type="hidden"  name="devuelto"  placeholder=
  </div>
</div>

            
 
</input>
         </div>   
            
    <!--=====================================
               <div class=""><h4>Entrega</h4></h4></div>
       <?php 

# Configurar
date_default_timezone_set("America/Mexico_City");
# Después de configurar
echo " " . date("d-m-Y  h:i.s a");


?>

<div class=""><h4>Devolución</h4></h4></div>
      <?php 
     date_default_timezone_set("America/Mexico_City");
$mifecha= date('d-m-Y  h:i.s a'); 
$NuevaFecha = strtotime ( '+2 hour' , strtotime ($mifecha) ) ; 
$NuevaFecha = date ( 'd-m-Y  h:i.s a' , $NuevaFecha); 
echo $NuevaFecha;

?>

            BOTON GUARDAR
            ======================================-->


 <div class="modal-footer">
     
 <button type="submit" class="btn btn-primary">
                    
                    Guardar Petición

                  </button>

              

      
        <?php

         // $crearpeticionM = new ControladorPeticionM();
          //$crearpeticionM -> ctrlCrearPeticion();

        ?>


        </div>



       </form> 



     </div>



   

<div class="col-lg-7">
            <h3>Materiales prestados</h3>
              <table class="table table-bordered table-striped dt-responsive tablaPeticiones" width="100%">
      
         <thead>

	            

	            <tr>

	              

	              <th>#</th>

                <th>Orden</th>

	              <th>Tecnico</th>

	             
	              <th>Material solicitado</th>
	              <th>Entrega</th>
	               <th>Entregado</th>
	              <th>Devolucion</th>
	             <th>Devuelto</th>

	             

    </tr>
  </thead>
  

      
<?php

      $peticionesmaterial = ControladorPeticionM::ctrMostrarUltimasPeticiones($tabla,$valor);
      
foreach ($peticionesmaterial as $key => $value) {
 $fecha = date("Y-m-d h:i:s",strtotime($fechaDeIngreso));
                  //TRAER TECNICO

                  $item = "id";

                  $valor = $value["tecnico_solicita"];



                  $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);
                  $NombreTecnico = $tecnico["nombre"];
                  
   echo'<tr>
    <th scope="row"> '.$value["id_peticionM"].'</th>
    
    <td> '.$value["material_orden"].'</td> 
    
   <td>  '.$NombreTecnico.'</td>

    <td> '.$value["material_solicitado"].'</td>
      
    <td> '.$value["entrega"].' </td>';
    
    
      if($value["entregado"] != 0){

                          echo '<td><button class="btn btn-success btn-xs btnActivar" id_peticionM="'.$value["id_peticionM"].'" estadoPeticion="0">Entregado</button></td>';

                        }else{

                          echo '<td><button class="btn btn-danger btn-xs btnActivar" id_peticionM="'.$value["id_peticionM"].'" estadoPeticion="1">No entregado</button></td>';

                        } 
    echo'<td> '.$value["devolucion"].' </td>';
    
  
    
    if($value["devuelto"] != 0){
      echo '<td><button class="btn btn-success btn-xs btnActivar" id_peticionM="'.$value["id_peticionM"].'" estadoPeticion="0">Devuelto</button></td>';

                        }else{

                          echo '<td><button class="btn btn-danger btn-xs btnActivar" id_peticionM="'.$value["id_peticionM"].'" estadoPeticion="1">No devuelto</button></td>';

                        } 
    echo'
   
    <td><button class="btn btn-sm btn-info" data-toggle="modal" data-target="	imprimir"><i class="fas fa-ticket-alt"></i> Imprimir</button>';
    
}
              
?>
</table>
</div>
<?php

//$peticionesmaterial = new ControladorPeticionM();
//$peticionesmaterial-> ctrMostrarUltimasPeticiones();
?>



      
  

