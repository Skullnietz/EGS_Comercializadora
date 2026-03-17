
<div class="content-wrapper">
	
	<section class="content-header">
		
		<h1>
			Lista Peticiones de Material
		</h1>

		<ol class="breadcrumb">

	      <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>

	      <li class="active">Lista peticionesM</li>
      
    	</ol>

	</section>

	<section class="content">
	    
	    <div class="box"> 
	<div class="box-header with-border">
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

    //  $peticionesmaterial = ControladorPeticionM::ctrMostrarPeticiones($tabla,$valor);
      
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
      
   
    <td> '.$value["entrega"].' </td>
    
    <td> '.$value["entregado"].' </td>
    
    <td> '.$value["devolucion"].' </td>
    
    <td> '.$value["devuelto"].' </td>';
    
    
}
              
?>
 
 
</div>

</div>
</table> 
</div>
        
	      
	        

	  </section>


