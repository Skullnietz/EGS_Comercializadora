<?php
$item = null;

$valor = null;

$ventas = ControladorVentas::ctrMostrarVentasParaTiket($item, $valor);

$asesores = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);

$arrayVendedores = array();

$arrayListaVendedores = array();


foreach ($ventas as $key => $valueVentas) {

  foreach ($asesores as $key => $valueAsesores) {

    if($valueAsesores["nombre"] == $valueVentas["asesor"]){

        #Capturamos los vendedores en un array
        array_push($arrayVendedores, $valueAsesores["nombre"]);

        #Capturamos las nombres y los valores netos en un mismo array
        $arrayListaVendedores = array($valueAsesores["nombre"] => $valueVentas["pago"]);

         #Sumamos los netos de cada vendedor

        foreach ($arrayListaVendedores as $key => $value) {

            $sumaTotalVendedores[$key] += $value;

         }

    }
  
  }

}

#Evitamos repetir nombre
$noRepetirNombres = array_unique($arrayVendedores);

?>
<!--=====================================
VENDEDORES
======================================-->

<div class="box box-success">
	
	<div class="box-header with-border">
    
    	<h3 class="box-title">Vendedores</h3>
  
  	</div>

  	<div class="box-body">
  		
		<div class="chart-responsive">
			
			<div class="chart" id="bar-chart1" style="height: 300px;"></div>

		</div>

  	</div>

</div>

<script>
	
	//BAR CHART
    var bar = new Morris.Bar({
      element: 'bar-chart1',
      resize: true,
      data: [

      		  <?php
    
    foreach($noRepetirNombres as $value){

      echo "{y: '".$value."', a: '".$sumaTotalVendedores[$value]."'},";

    }
    ?>
      ],
      barColors: ['#00a65a', '#f56954'],
      xkey: 'y',
      ykeys: ['a'],
      labels: ['Ventas'],
      preUnits:'$',
      hideHover: 'auto'
    });

</script>