<!--=====================================
CAJAS SUPERIORES
======================================-->
<?php
$ordenes = controladorOrdenes::ctrMostrarOrdenesSuma();
foreach ($ordenes as $key => $valueOrdenes) {
  $totalOrdenes += $valueOrdenes["total"];
}
$totalordenes = controladorOrdenes::ctrListarOrdenes();
 foreach ($totalordenes as $key => $valueTotal) {
       $totalordenesImprimir = $valueTotal[0];
      }
$totalVentas=  $totalOrdenes;

$usuarios = ControladorUsuarios::ctrMostrarTotalUsuariosMes("id");
$totalUsuarios = count($usuarios);
$entradasMes = ControladorOrdenes::ctrMostrarOrdenesEntrada("id");
$totalentradasMes = count($entradasMes);
$ventas = ControladorVentas::ctrMostrarTotalVentasMes("id");
$totalventas = $ventas[0];
$pedidos = ControladorPedidos::ctrMostrarTotalPedidosMes("id");
foreach ($pedidos as $key => $valuePedidos) {
  $totalPedidos += $valuePedidos["total"];
  
}

$VentasyPedidosTotal= $totalventas;
?>
<!-- col -->
<div class="col-lg-3 col-xs-6">

  <!-- small box -->
  <div class="small-box bg-aqua">
    
    <!-- inner -->
    <div class="inner">
      
      <h3>$<?php echo number_format($totalVentas); ?></h3>

      <p>Total Ordenes / Mes</p>
    
    </div>
    <!-- inner -->

    <!-- icon -->
    <div class="icon">
    
      <i class="ion ion-bag"></i>
    
    </div>
    <!-- icon -->
    
    <a href="index.php?ruta=ventasR" class="small-box-footer">Más Info <i class="fa fa-arrow-circle-right"></i></a>
  
  </div>
  <!-- small-box -->

</div>

<!-- col -->

<!--===========================================================================-->
<!-- col -->
<div class="col-lg-3 col-xs-6">
  
  <!-- small box -->
  <div class="small-box bg-red">
  
    <!-- inner -->
    <div class="inner">
    
      <h3><?php echo number_format($totalentradasMes); ?></h3>

      <p>Ordenes Ingresadas / Mes</p>

    </div>
    <!-- inner -->
    
    <!-- icon -->
    <div class="icon">
      
      <i class="ion ion-pie-graph"></i>
    
    </div>
    <!-- icon -->
    
    <a href="index.php?ruta=pedidos" class="small-box-footer">Más Info <i class="fa fa-arrow-circle-right"></i></a>
  
  </div>
  <!-- small box -->

</div>
<!-- col -->

<!-- col -->
<div class="col-lg-3 col-xs-6">
  
  <!-- small box -->
  <div class="small-box bg-green">

    <!-- inner -->
    <div class="inner">
      
      <h3><?php echo $totalordenesImprimir;?></h3>

      <p>Ordenes Entregadas / Mes </p>
    
    </div>
    <!-- inner -->
    
    <!-- icon -->
    <div class="icon">
      
      <i class="ion ion-stats-bars"></i>
    
    </div>
    <!-- icon -->

    <a href="index.php?ruta=ordenes" class="small-box-footer">Más Info <i class="fa fa-arrow-circle-right"></i></a>
  
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

      <p>Prospectos / Mes</p>
    
    </div>
    <!-- inner -->

    <!-- icon -->
    <div class="icon">
      
      <i class="ion ion-person-add"></i>
    
    </div>
    <!-- icon -->

    <a href="index.php?ruta=perfiles" class="small-box-footer">Más Info <i class="fa fa-arrow-circle-right"></i></a>
  
  </div>
  <!-- small box -->

</div>
<!-- col -->

<!--===========================================================================-->
<!-- col -->

<!--===========================================================================-->


<!-- col -->
<div class="col-lg-3 col-xs-6">
  
  <!-- small box -->
  <div class="small-box bg-black">
  
    <!-- inner -->
    <div class="inner">
    
      <h3><?php echo '$'.number_format($VentasyPedidosTotal); ?></h3>

      <p>Total Ventas / Mes</p>

    </div>
    <!-- inner -->
    
    <!-- icon -->
    <div class="icon">
      
      <i class="ion ion-cash" style="color:white"></i>
    
    </div>
    <!-- icon -->
    
    <a href="index.php?ruta=pedidos" class="small-box-footer">Más Info <i class="fa fa-arrow-circle-right"></i></a>
  
  </div>
  <!-- small box -->

</div>
<!-- col -->

<!-- col -->
