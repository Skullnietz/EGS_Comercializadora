
  
<?php

if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"] != "Super-Administrador"){

  echo '<script>

  window.location = "inicio";
  
  

  </script>';
  

  return;

}

?>
<div class="content-wrapper">
	
	<section class="content-header">
		
		<h1>
			Gestor Objetivos
		</h1>

		<ol class="breadcrumb">

	      <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>

	      <li class="active"> Reporte de campaña</li>
      
    	</ol>

	</section>
    <section class="content">

	    <div class="box"> 

	       <div class="box-body">
	           <a href="#" class="btn btn-primary"> Reporte metas</a>
<hr>
<!-- Fila principal -->

<div class="row">
    <!-- Grafica principal -->
  <div class="col-sm-5  " style="width:100%;max-width:650px">
      <h2 class="text" style="text-align:center">General</h2>
  <br>
  <br>
  <canvas id="myChart" ></canvas>
  </div>
  <!-- Fin de grafica principal -->
   <!-- Inicio de separacion -->
  <div class="col-sm-1" style="width:7px">
  </div>
  <!-- Fin de separacion -->
  <!-- Grafica por departamentos -->
  <div class="col-sm-5 box" style="width:100%;max-width:950px;height:100%;max-height:1000px">
      <div class="box-header"><h2 class="text" style="text-align:center">Departamentos</h2></div> 
  <div class="box-body">
        <div class="row">
   <div class="col-sm-6 small-box" style="background-color: rgb(240, 248, 255); color: rgb(31, 45, 61); width:100%;max-width:450px"><canvas id="myChart1"></canvas><a href="objetivosventas" class="small-box-footer">Mas Info <i class="fa fa-arrow-circle-right"></i></a></div>
   <div class="col-sm-6 small-box" style="background-color: rgb(250, 235, 215); color: rgb(31, 45, 61); width:100%;max-width:450px"><canvas id="myChart5"></canvas><a href="objetivosventasext" class="small-box-footer">Mas Info <i class="fa fa-arrow-circle-right"></i></a></div>       
  </div>
  <div class="row">
    <div class="col-sm-6 small-box" style="background-color: rgb(250, 250, 210); color: rgb(31, 45, 61);; width:100%;max-width:450px"><canvas id="myChart3"></canvas><a href="objetivoselectronica" class="small-box-footer">Mas Info <i class="fa fa-arrow-circle-right"></i></a></div>
    <div class="col-sm-6 small-box" style="background-color: #F5FFFA; color: rgb(31, 45, 61); width:100%;max-width:450px"><canvas id="myChart4"></canvas><a href="objetivosimpresoras" class="small-box-footer">Mas Info <i class="fa fa-arrow-circle-right"></i></a></div>   
  </div>
  <div class="row">
    <div class="col-sm-6 small-box" style="background-color: rgb(250, 235, 215); color: rgb(31, 45, 61); width:100%;max-width:450px"><canvas id="myChart2"></canvas><a href="objetivossistemas" class="small-box-footer">Mas Info <i class="fa fa-arrow-circle-right"></i></a></div> 
    
  </div>
  </div>

  </div>
  <!-- Fin de grafica por departamentos -->
</div>
<!-- Fin de fila principal-->

	       

            </div>
   </section>
</div>
<script
src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js">
</script>
<script>
Chart.defaults.global.defaultFontSize  = 12;
var xValues = ["Asesores", "Electronica", "Impresoras", "Sistemas"];
var yValues = [<?php 
$estado = 1; 
$area= "Asesores";
$DepartamentoV = ControladorMetas::ctrMostrarMetasCompletadasPorDepartamento($tabla, $area, $estado);
foreach ($DepartamentoV as $key => $value){echo '" '.$value["metaspordepartamento"].' "';}?>, <?php 
$estado = 1; 
$area= "Electronica";
$DepartamentoV = ControladorMetas::ctrMostrarMetasCompletadasPorDepartamento($tabla, $area, $estado);
foreach ($DepartamentoV as $key => $value){echo '" '.$value["metaspordepartamento"].' "';}?>, <?php 
$estado = 1; 
$area= "Impresoras";
$DepartamentoV = ControladorMetas::ctrMostrarMetasCompletadasPorDepartamento($tabla, $area, $estado);
foreach ($DepartamentoV as $key => $value){echo '" '.$value["metaspordepartamento"].' "';}?>, <?php 
$estado = 1; 
$area= "Sistemas";
$DepartamentoV = ControladorMetas::ctrMostrarMetasCompletadasPorDepartamento($tabla, $area, $estado);
foreach ($DepartamentoV as $key => $value){echo '" '.$value["metaspordepartamento"].' "';}?>];
var barColors = [
  "#33FFA5",
  "#b91d47",
  "#00aba9",
  "#2b5797",
  "#e8c3b9",
  "#1e7145"
];

new Chart("myChart", {
  type: "pie",
  data: {
    labels: xValues,
    datasets: [{
      backgroundColor: barColors,
      data: yValues
    }]
  },
  options: {
    title: {
      display: true,
      text: "Grafica General de Metas"
    }
  }
});

var xValues1 = [<?php
$area = "Ventas";
        $DepartamentoVentas = ControladorMetas::ctrMostrarPersonalDepartamento($tabla, $area);
        foreach ($DepartamentoVentas as $key => $value){
            echo '" '.$value["nombre"].' ",';
            }
            ?>];
var yValues1 = [<?php
$area = "Ventas";
$DepartamentoVentas = ControladorMetas::ctrMostrarPersonalDepartamento($tabla, $area);
        foreach ($DepartamentoVentas as $key => $value){
        $id_perfil = $value["id"]; 
        $estado = "1";
        $Metasporid = ControladorMetas::ctrMostrarMetasCompletadasPorId($tabla, $id_perfil, $estado);
        foreach ($Metasporid as $key => $value){
            echo '" '.$value["metasporid"].' ",';
            
        }
            
        }
        
            ?>];
var barColors1 = [
  "#33FFA5",
  "#b91d47",
  "#00aba9",
  "#2b5797",
  "#e8c3b9",
  "#1e7145"
];

new Chart("myChart1", {
  type: "doughnut",
  data: {
    labels: xValues1,
    datasets: [{
      backgroundColor: barColors,
      data: yValues1
    }]
  },
  options: {
    title: {
      display: true,
      text: "Grafica Campaña Asesores"
    }
  }
});
var xValues2 = [<?php
$area = "Sistemas";
        $DepartamentoSistemas = ControladorMetas::ctrMostrarPersonalDepartamento($tabla, $area);
        foreach ($DepartamentoSistemas as $key => $value){
            echo '" '.$value["nombre"].' ",';
            }
            ?>];
var yValues2 = [<?php
$area = "Sistemas";
$DepartamentoSistemas = ControladorMetas::ctrMostrarPersonalDepartamento($tabla, $area);
        foreach ($DepartamentoSistemas as $key => $value){
        $id_perfil = $value["id"]; 
        $estado = "1";
        $Metasporid = ControladorMetas::ctrMostrarMetasCompletadasPorId($tabla, $id_perfil, $estado);
        foreach ($Metasporid as $key => $value){
            echo '" '.$value["metasporid"].' ",';
            
        }
            
        }
        
            ?>];
var barColors2 = [
  "#33FFA5",
  "#b91d47",
  "#00aba9",
  "#2b5797",
  "#e8c3b9",
  "#1e7145"
];

new Chart("myChart2", {
  type: "doughnut",
  data: {
    labels: xValues2,
    datasets: [{
      backgroundColor: barColors2,
      data: yValues2
    }]
  },
  options: {
    title: {
      display: true,
      text: "Grafica Metas Sistemas"
    }
  }
});
var xValues3 = [
    <?php
    $area = "Electronica";
        $DepartamentoElectronica = ControladorMetas::ctrMostrarPersonalDepartamento($tabla, $area);
        foreach ($DepartamentoElectronica as $key => $value){
            echo '" '.$value["nombre"].' ",';
            }
            ?>
    ];
var yValues3 = [<?php
$area = "Electronica";
$DepartamentoElectronica = ControladorMetas::ctrMostrarPersonalDepartamento($tabla, $area);
        foreach ($DepartamentoElectronica as $key => $value){
        $id_perfil = $value["id"]; 
        $estado = "1";
        $Metasporid = ControladorMetas::ctrMostrarMetasCompletadasPorId($tabla, $id_perfil, $estado);
        foreach ($Metasporid as $key => $value){
            echo '" '.$value["metasporid"].' ",';
            
        }
            
        }
        
            ?>];
var barColors3 = [
 "#33FFA5",
  "#b91d47",
  "#00aba9",
  "#2b5797",
  "#e8c3b9",
  "#1e7145",
];

new Chart("myChart3", {
  type: "doughnut",
  data: {
    labels: xValues3,
    datasets: [{
      backgroundColor: barColors3,
      data: yValues3
    }]
  },
  options: {
    title: {
      display: true,
      text: "Grafica Metas Electronica"
    }
  }
});
var xValues4 = [<?php
$area = "Impresoras";
        $DepartamentoImpresoras = ControladorMetas::ctrMostrarPersonalDepartamento($tabla, $area);
        foreach ($DepartamentoImpresoras as $key => $value){
            echo '" '.$value["nombre"].' ",';
            }
            ?>];
var yValues4 = [<?php
$area = "Impresoras";
$DepartamentoImpresoras = ControladorMetas::ctrMostrarPersonalDepartamento($tabla, $area);
        foreach ($DepartamentoImpresoras as $key => $value){
        $id_perfil = $value["id"]; 
        $estado = "1";
        $Metasporid = ControladorMetas::ctrMostrarMetasCompletadasPorId($tabla, $id_perfil, $estado);
        foreach ($Metasporid as $key => $value){
            echo '" '.$value["metasporid"].' ",';
            
        }
            
        }
        
            ?>];
var barColors4= [
  "#33FFA5",
  "#b91d47",
  "#00aba9",
  "#2b5797",
  "#e8c3b9",
  "#1e7145"
];

new Chart("myChart4", {
  type: "doughnut",
  data: {
    labels: xValues4,
    datasets: [{
      backgroundColor: barColors4,
      data: yValues4
    }]
  },
  options: {
    title: {
      display: true,
      text: "Grafica Metas Impresoras"
    }
  }
});
var xValues5 = [<?php
$area = "Ventas Externas";
        $DepartamentoVentasExternas = ControladorMetas::ctrMostrarPersonalDepartamento($tabla, $area);
        foreach ($DepartamentoVentasExternas as $key => $value){
            echo '" '.$value["nombre"].' ",';
            }
            ?>];
var yValues5 = [<?php
$area = "Ventas Externas";
$DepartamentoVentasExternas = ControladorMetas::ctrMostrarPersonalDepartamento($tabla, $area);
        foreach ($DepartamentoVentasExternas as $key => $value){
        $id_perfil = $value["id"]; 
        $estado = "1";
        $Metasporid = ControladorMetas::ctrMostrarMetasCompletadasPorId($tabla, $id_perfil, $estado);
        foreach ($Metasporid as $key => $value){
            echo '" '.$value["metasporid"].' ",';
            
        }
            
        }
        
            ?>];
var barColors5= [
  "#33FFA5",
  "#b91d47",
  "#00aba9",
  "#2b5797",
  "#e8c3b9",
  "#1e7145"
];

new Chart("myChart5", {
  type: "doughnut",
  data: {
    labels: xValues5,
    datasets: [{
      backgroundColor: barColors5,
      data: yValues5
    }]
  },
  options: {
    title: {
      display: true,
      text: "Grafica Campaña Asesores Externos"
    }
  }
});
</script>

    
