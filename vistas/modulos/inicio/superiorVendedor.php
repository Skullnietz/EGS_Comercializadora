<?php
  $itemVentas = "nombre";
  $valorVentas = $_SESSION["nombre"];
  $itemVentasDos = "id_empresa";
  $valorventasDos = $_SESSION["empresa"];
  $ventas = ControladorVentas::ctrlMostrarventaPorAsesoryEmpresa($itemVentas, $valorVentas, $itemVentasDos, $valorventasDos);

/*======================================
TRAER ASESOR EN SESSION 
=======================================*/
$item = "correo";
$valor = $_SESSION["email"];
$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);
$idAsesor = $asesor["id"];
$ordenes = controladorOrdenes::ctrMostrarOrdenesSumaAsesor($idAsesor);
foreach ($ordenes as $key => $valueOrdenes) {
  $totalOrdenesA += $valueOrdenes["total"];
}

$totalordenes = controladorOrdenes::ctrListarOrdenesAsesor($idAsesor);
 foreach ($totalordenes as $key => $valueTotal) {
       $totalordenesImprimir = $valueTotal[0];
      }
$totalVentas= $ventas["total"] + $totalOrdenes;

$usuarios = ControladorUsuarios::ctrMostrarTotalUsuariosMesAsesor("id",$idAsesor);
$totalUsuarios = count($usuarios);
$entradasMes = ControladorOrdenes::ctrMostrarOrdenesEntradaAsesor($idAsesor);
$totalentradasMesAsesor = count($entradasMes);
$pedidos = ControladorPedidos::ctrMostrarTotalPedidos("id");
$totalPedidos = count($pedidos);

?>
<!-- col -->
<div class="col-lg-3 col-xs-6">

  <!-- small box -->
  <div class="small-box bg-aqua">
    
    <!-- inner -->
    <div class="inner">
      
      <h3>$<?php echo number_format($totalOrdenesA); ?></h3>

      <p>Total entregadas / Mes</p>
    
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


<!--===========================================================================-->

<!-- col -->
<div class="col-lg-3 col-xs-6">
  
  <!-- small box -->
  <div class="small-box bg-green">

    <!-- inner -->
    <div class="inner">
      
      <h3><?php echo $totalordenesImprimir;?></h3>

      <p>Ordenes entregadas / Mes</p>
    
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

    <a href="index.php?ruta=clientes" class="small-box-footer">Más Info <i class="fa fa-arrow-circle-right"></i></a>
  
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
  <div class="small-box bg-red">
  
    <!-- inner -->
    <div class="inner">
    
      <h3><?php echo number_format($totalentradasMesAsesor); ?></h3>

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
