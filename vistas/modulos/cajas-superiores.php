<?php

$ventas = ControladorVentas::ctrMostrarTotalVentas();

//$visitas = ControladorVisitas::ctrMostrarTotalVisitas();

$usuarios = ControladorUsuarios::ctrMostrarTotalUsuarios("id");
$totalUsuarios = count($usuarios);

$pedidos = ControladorPedidos::ctrMostrarTotalPedidos("id");
$totalPedidos = count($pedidos);

$ordenes = controladorOrdenes::ctrMostrarOrdenesSuma();
foreach ($ordenes as $key => $valueOrdenes) {
  $totalOrdenes += $valueOrdenes["total"];
}
$totalordenes = controladorOrdenes::ctrListarOrdenes();
 foreach ($totalordenes as $key => $valueTotal) {
       $totalordenesImprimir = $valueTotal[0];
      }
$totalVentas= $ventas["total"] + $totalOrdenes;
?>

<!--=====================================
CAJAS SUPERIORES
======================================-->

<!-- col -->
<div class="col-lg-3 col-xs-6">

  <!-- small box -->
  <div class="small-box bg-aqua">
    
    <!-- inner -->
    <div class="inner">
      
      <h3>$<?php echo number_format($totalVentas); ?></h3>

      <p>Ventas del Mes</p>
    
    </div>
    <!-- inner -->

    <!-- icon -->
    <div class="icon">
    
      <i class="ion ion-bag"></i>
    
    </div>
    <!-- icon -->
    
    <a href="ventas" class="small-box-footer">Más Info <i class="fa fa-arrow-circle-right"></i></a>
  
  </div>
  <!-- small-box -->

</div>
<!-- col -->

<!--===========================================================================-->

<!-- col -->
<div class="col-lg-3 col-xs-6">
  
  <!-- small box -->
  <div class="small-box bg-green">

    <!-- inner -->
    <div class="inner">
      
      <h3><?php echo $totalordenesImprimir;?></h3>

      <p>Ordenes</p>
    
    </div>
    <!-- inner -->
    
    <!-- icon -->
    <div class="icon">
      
      <i class="ion ion-stats-bars"></i>
    
    </div>
    <!-- icon -->

    <a href="visitas" class="small-box-footer">Más Info <i class="fa fa-arrow-circle-right"></i></a>
  
  </div>
  <!-- small box -->

</div>
<!-- col -->

<!--===========================================================================-->

<!-- col -->
<div class="col-lg-3 col-xs-6">
  
  <!-- small box -->
  <div class="small-box bg-yellow">
    
    <!-- inner -->
    <div class="inner">
    
      <h3><?php echo number_format($totalUsuarios); ?></h3>

      <p>Usuarios</p>
    
    </div>
    <!-- inner -->

    <!-- icon -->
    <div class="icon">
      
      <i class="ion ion-person-add"></i>
    
    </div>
    <!-- icon -->

    <a href="usuarios" class="small-box-footer">Más Info <i class="fa fa-arrow-circle-right"></i></a>
  
  </div>
  <!-- small box -->

</div>
<!-- col -->

<!--===========================================================================-->

<!-- col -->
<div class="col-lg-3 col-xs-6">
  
  <!-- small box -->
  <div class="small-box bg-red">
  
    <!-- inner -->
    <div class="inner">
    
      <h3><?php echo number_format($totalPedidos); ?></h3>

      <p>Pedidos</p>

    </div>
    <!-- inner -->
    
    <!-- icon -->
    <div class="icon">
      
      <i class="ion ion-pie-graph"></i>
    
    </div>
    <!-- icon -->
    
    <a href="productos" class="small-box-footer">Más Info <i class="fa fa-arrow-circle-right"></i></a>
  
  </div>
  <!-- small box -->

</div>
<!-- col -->
