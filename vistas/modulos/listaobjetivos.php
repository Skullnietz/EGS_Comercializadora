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

	      <li class="active">Lista de Objetivos</li>
      
    	</ol>

	</section>
    <section class="content">

	    <div class="box"> 

	       <div class="box-body">

	          
           <br>
<h3>
  Lista de Metas
</h3>
<br>
<div class="btn-group btn-group-toggle" data-toggle="buttons">
  <label class="btn btn-primary active">
    <input type="radio" name="options" id="option1" autocomplete="off" checked> Todos
  </label>
  <label class="btn btn-primary">
    <input type="radio" name="options" id="option2" autocomplete="off"> Asesores
  </label>
  <label class="btn btn-primary">
    <input type="radio" name="options" id="option3" autocomplete="off"> Asesores Externos
  </label>
  <label class="btn btn-primary">
    <input type="radio" name="options" id="option3" autocomplete="off"> Electronica
  </label>
  <label class="btn btn-primary">
    <input type="radio" name="options" id="option3" autocomplete="off"> Impresoras
  </label>
  <label class="btn btn-primary">
    <input type="radio" name="options" id="option3" autocomplete="off"> Sistemas
  </label>
</div> 
<div class="btn-group btn-group-toggle " data-toggle="buttons" style="float: right">
 
  <label class="btn active btn-sm">
    <input type="radio" name="options" id="option1" autocomplete="off" checked> Todos
  </label>
  <label class="btn  btn-sm">
    <input type="radio" name="options" id="option2" autocomplete="off"> Terminado
  </label>
  <label class="btn  btn-sm">
    <input type="radio" name="options" id="option2" autocomplete="off"> Pendiente
  </label>
</div>
<br>
<form action="../../form-result.php" method="post" target="_blank">

  <p style="float: right" >

 <input class="form-control input-sm" placeholder="Buscar..." type="search" name="busquedacodigo"  maxlength="4">

    

  </p>
  <br>

</form>
<br>



<div >
<table class="table table-bordered table-striped dt-responsive tablaMetas" width="100%">
<thead class="thead-light">
<tr>
<th>#</th>    
<th>Usuario</th>
<th>Perfil</th>
<th>Tipo</th>
<th>Observacion</th>
<th>Estado</th>
<th>Descripcion</th>
<th>Progreso</th>
<th></th>
<th>Fecha</th>
</tr>
</thead>
<tbody>
<?php
//INICIA LA PAGINACION//


$metas = ControladorMetas::ctrMostrarMetas($item, $valor);
foreach ($metas as $key => $value){
$idperfil = $value["id_perfil"];    
$nombre = ControladorMetas::ctrMostrarNombrePorIdPerfil($tabla, $idperfil);


if($value["estado"] == 0){
$estado="Pendiente";
}else{
$estado="Terminado";
}



  echo '<tr>';
  echo '<th>'.($key+1).'</th>';
  foreach ($nombre as $val){ 
  echo '<td>'.$val["nombre"].'</td>';
    } 
  echo '<td>'.$value["area"].'</td>';
  echo '<td>'.$value["tipo"].'</td>';
  echo '<td>'.$value["observacion"].'</td>';
  echo '<td>'.$estado.'</td>';
  echo '<td>'.$value["descripcion"].'</td>';
  echo '<td>'.$value["progreso"].' / '.$value["actividades"].'</td>';
  echo '<td><div class="btn-group btn-group-toggle" data-toggle="buttons"><button class="btn btn-warning btn-sm btnEditarMeta" idMeta="'.$value["id"].'"><i class="fas fa-edit"></i></button>  <button class="btn btn-danger btn-sm btnEliminarMeta" idMeta="'.$value["id"].'"><i class="fas fa-trash"></i></button></div></td>';
  echo '<td>'.$value["fecha"].'</td>';
        echo '</tr>';
     
}


?>
</tbody>
</table>
</div>
            </div>
   </section>
</div>
<?php

  $eliminarMeta = new ControladorMetas();
  $eliminarMeta -> ctrEliminarMeta();
  