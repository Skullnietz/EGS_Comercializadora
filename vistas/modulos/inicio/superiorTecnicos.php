<?php
	
	$itemUno = "correo";
    
    $valorUno =  $_SESSION["email"];
	
	$tecnicoEnSession = ControladorTecnicos::ctrMostrarTecnicos($itemUno,$valorUno);
    
  $id_tecnico = $tecnicoEnSession["id"];



  $estadoUno = "Aceptado (ok)";

	$item = "id_empresa";


	$valor = $_SESSION["empresa"]; 

	$tecnico = "id_tecnico"; 

	$valorTecnico = $id_tecnico;

	$oredenesOk = controladorOrdenes::ctrlMostrarOrdenesPorEstadoEmpresayTecnico($estadoUno, $item, $valor, $tecnico, $valorTecnico);

		
		$totalOrdenesOk = count($oredenesOk);
	
	
	$estadoDos = "Pendiente de autorización (AUT";

	$oredenesAUT = controladorOrdenes::ctrlMostrarOrdenesPorEstadoEmpresayTecnico($estadoDos, $item, $valor, $tecnico, $valorTecnico);

		
	$TotaloredenesAUT = count($oredenesAUT);


	$estadoTres = "Terminada (ter)";

	$oredenesTer = controladorOrdenes::ctrlMostrarOrdenesPorEstadoEmpresayTecnico($estadoTres, $item, $valor, $tecnico, $valorTecnico);

		
	$TotaloredenesTer = count($oredenesTer);


	$estadoCuatro = "En revisión (REV)";

	$oredenesREV = controladorOrdenes::ctrlMostrarOrdenesPorEstadoEmpresayTecnico($estadoCuatro, $item, $valor, $tecnico, $valorTecnico);

		
	$TotaloredenesREV = count($oredenesREV);


?>

<div class="col-lg-3 col-xs-6">
	
	<div class="small-box bg-aqua">
		
		<div class="inner">
			
			<h3><?php echo $totalOrdenesOk; ?> OK</h3>

      		<p>Aceptadas Ok</p>

		</div>
		<!-- icon -->
	    <div class="icon">
	    
	      <i class="fas fa-clipboard-check"></i>
	    
	    </div>
	    
	    <!-- icon -->
      <?php

      $itemUno = "correo";
    
      $valorUno =  $_SESSION["email"];
        
      $tecnicoEnSession = ControladorTecnicos::ctrMostrarTecnicos($itemUno,$valorUno);
          
      $estadoreporte = "Aceptado (ok)";

      echo'<a href="vistas/modulos/descargar-reporte-OrdenesPorEstado.php?reporte=ordenesOk&empresa='.$_SESSION["empresa"].'&estado='.$estadoreporte.'&tecnico='.$tecnicoEnSession["id"].'" class="small-box-footer">

               Más Info <i class="fa fa-arrow-circle-right"></i>

      </a>';

      ?>

	</div>

</div>


<!-- col -->
<div class="col-lg-3 col-xs-6">
  
  <!-- small box -->
  <div class="small-box bg-green">

    <!-- inner -->
    <div class="inner">
      
      <h3><?php echo $TotaloredenesAUT; ?> AUT</h3>

      <p>Pendiente de autorización</p>
    
    </div>
    <!-- inner -->
    
    <!-- icon -->
    <div class="icon">
      
      <i class="fas fa-headset"></i>
    
    </div>
    <!-- icon -->

    <?php

      $itemUno = "correo";
    
      $valorUno =  $_SESSION["email"];
        
      $tecnicoEnSession = ControladorTecnicos::ctrMostrarTecnicos($itemUno,$valorUno);
          
      $estadoreporte = "Pendiente de autorización (AUT)";

      echo'<a href="vistas/modulos/descargar-reporte-OrdenesPorEstado.php?reporte=ordenesAUT&empresa='.$_SESSION["empresa"].'&estado='.$estadoreporte.'&tecnico='.$tecnicoEnSession["id"].'" class="small-box-footer">

               Más Info <i class="fa fa-arrow-circle-right"></i>

      </a>';

      ?>
  
  </div>
  <!-- small box -->

</div>

<!-- col -->
<div class="col-lg-3 col-xs-6">
  
  <!-- small box -->
  <div class="small-box bg-yellow">
    
    <!-- inner -->
    <div class="inner">
      
      <h3><?php echo $TotaloredenesTer ?> TER</h3>     
  	  

      <p>Terminadas</p>
    
    </div>
    <!-- inner -->

    <!-- icon -->
    <div class="icon">
      
      <i class="fas fa-sign-out-alt"></i>
    
    </div>
    <!-- icon -->

      <?php

      $itemUno = "correo";
    
      $valorUno =  $_SESSION["email"];
        
      $tecnicoEnSession = ControladorTecnicos::ctrMostrarTecnicos($itemUno,$valorUno);
          
      $estadoreporte = "Terminada (ter)";

      echo'<a href="vistas/modulos/descargar-reporte-OrdenesPorEstado.php?reporte=ordenesTER&empresa='.$_SESSION["empresa"].'&estado='.$estadoreporte.'&tecnico='.$tecnicoEnSession["id"].'" class="small-box-footer">

               Más Info <i class="fa fa-arrow-circle-right"></i>

      </a>';

      ?>

  
  </div>
  <!-- small box -->

</div>
<!-- col -->


<!-- col -->
<div class="col-lg-3 col-xs-6">
  
  <!-- small box -->
  <div class="small-box bg-red">
  
    <!-- inner -->
    <div class="inner">
    
      <h3><?php echo $TotaloredenesREV; ?> REV</h3>

      <p>En revisión</p>

    </div>
    <!-- inner -->
    
    <!-- icon -->
    <div class="icon">
      
      <i class="fas fa-diagnoses"></i>
    
    </div>
    <!-- icon -->
    
    <?php

      $itemUno = "correo";
    
      $valorUno =  $_SESSION["email"];
        
      $tecnicoEnSession = ControladorTecnicos::ctrMostrarTecnicos($itemUno,$valorUno);
          
      $estadoreporte = "En revisión (REV)";

      echo'<a href="vistas/modulos/descargar-reporte-OrdenesPorEstado.php?reporte=ordenesREV&empresa='.$_SESSION["empresa"].'&estado='.$estadoreporte.'&tecnico='.$tecnicoEnSession["id"].'" class="small-box-footer">

               Más Info <i class="fa fa-arrow-circle-right"></i>

      </a>';

      ?>
  
  </div>
  <!-- small box -->

</div>
<!-- col -->

<!-- col -->
