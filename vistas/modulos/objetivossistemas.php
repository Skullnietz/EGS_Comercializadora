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
	      <li><a href="objetivos"><i class="fas fa-dashboard"></i>Reporte de campaña</a></li>
	      <li class="active">Reporte de sistemas</li>
      
    	</ol>

	</section>
    <section class="content">

	    <div class="box"> 

	       <div class="box-body">
	           <a href="#" class="btn btn-primary"> Agregar meta</a>
           <br>
<hr>
<h3>
  REPORTE DE METAS | DEPARTAMENTO SISTEMAS
</h3>
<br>
<div class="row">
  <div class="col-sm-6">
    <div class="box">
      <canvas id="myChart1"></canvas> 
    </div>
  </div>
 
   <div class="col-sm-6">
    <div class="box">
      <canvas id="myChart2"></canvas> 
    </div>
  </div>
</div>          
            </div>
        </div>
   </section>
</div>
<script
src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js">
</script>
<script>
var xValues1 = [<?php
$area = "Sistemas";
        $DepartamentoSistemas = ControladorMetas::ctrMostrarPersonalDepartamento($tabla, $area);
        foreach ($DepartamentoSistemas as $key => $value){
            echo '" '.$value["nombre"].' ",';
            }
            ?>];
var yValues1 = [<?php
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


new Chart("myChart1", {
  type: "doughnut",
  data: {
    labels: xValues1,
    datasets: [{
        label: 'Departamento de Sistemas',
       backgroundColor: [
      'rgba(255, 99, 132, 0.2)',
      'rgba(255, 159, 64, 0.2)',
      'rgba(255, 205, 86, 0.2)',
      'rgba(75, 192, 192, 0.2)',
      'rgba(54, 162, 235, 0.2)',
      'rgba(153, 102, 255, 0.2)',
      'rgba(201, 203, 207, 0.2)'
    ],
    borderColor: [
      'rgb(255, 99, 132)',
      'rgb(255, 159, 64)',
      'rgb(255, 205, 86)',
      'rgb(75, 192, 192)',
      'rgb(54, 162, 235)',
      'rgb(153, 102, 255)',
      'rgb(201, 203, 207)'
    ],
    borderWidth: 1,
      data: yValues1
    }]
  },
  options: {
    title: {
      display: true,
      text: "Grafica Metas Sistemas"
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


new Chart("myChart2", {
  type: "bar",
  data: {
    labels: xValues2,
    datasets: [{
        label: 'Departamento de Sistemas',
       backgroundColor: [
      'rgba(255, 99, 132, 0.2)',
      'rgba(255, 159, 64, 0.2)',
      'rgba(255, 205, 86, 0.2)',
      'rgba(75, 192, 192, 0.2)',
      'rgba(54, 162, 235, 0.2)',
      'rgba(153, 102, 255, 0.2)',
      'rgba(201, 203, 207, 0.2)'
    ],
    borderColor: [
      'rgb(255, 99, 132)',
      'rgb(255, 159, 64)',
      'rgb(255, 205, 86)',
      'rgb(75, 192, 192)',
      'rgb(54, 162, 235)',
      'rgb(153, 102, 255)',
      'rgb(201, 203, 207)'
    ],
    borderWidth: 1,
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
</script>  