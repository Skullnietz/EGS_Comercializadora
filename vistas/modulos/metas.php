
<?php
if($_SESSION["perfil"] != "administrador"  AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "tecnico" AND $_SESSION["perfil"]!= "secretaria" AND $_SESSION["perfil"]!= "Super-Administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;
}

?>

<style>
    #carrusel {
    margin-left:15px;
    width:1200px;
    overflow:hidden;
    height:272px;
    position:relative;
    margin-top:20px;
    margin-bottom:20px;
}
 
#carrusel .left-arrow {
    position:absolute;
    margin-right:100px;
    left:1px;
    z-index:1;
    top:50%;
    margin-top:-9px;
}
 
#carrusel .right-arrow {
    position:absolute;
    right:10px;
    z-index:1;
    top:50%;
    margin-top:-9px;
}
 
.carrusel {
    width:4000px;
    left:0px;
    position:absolute;
    z-index:0;
}
#carrusel .box {
  
  --v:calc( ((18/5) * var(--p) - 90)*1deg);

  width:100px;
  height:100px;
  display:inline-block;
  border-radius:50%;
  padding:10px;
  background:
    linear-gradient(#ccc,#ccc) content-box,
    linear-gradient(var(--v), #f2f2f2     50%,transparent 0) 0/min(100%,(50 - var(--p))*100%),
    linear-gradient(var(--v), transparent 50%,green       0) 0/min(100%,(var(--p) - 50)*100%),
    linear-gradient(to right, #f2f2f2 50%,green 0);
}

.carrusel>div {
    float: left;
    height: 272px;
    margin-right: 5px;
    border-radius: 20px;
    width: 195px;
    text-align:center;
}
 
.carrusel img {
    cursor:pointer;
}
 
.product {
    border:#CCCCCC 1px solid;
}

.lb-lg {
  font-size: 25px;
}

</style>
<script>
$(document).ready(function (){
    $(".btnMempleados").click(function() {
        var idmeta = $(this).data('idmeta');
        var actividades = $(this).data('act');
        var progreso = $(this).data('prog');
        var porcentaje = $(this).data('perc');
        var descripcion = $(this).data('desc');
        var tipo = $(this).data('tipo');
        
        var contenidoidmeta = "<center><h3>Meta "+idmeta+"</h3></center>";
        var contenidoprogressbar ='<label for="colFormLabelLg" class="col-sm-2 col-form-label col-form-label-lg">Progreso</label>'+
    '<div style ="border-radius: 10px;" class="progress col-sm-8" style="margin-right: 10px;">'+
  '<div class="progress-bar" role="progressbar" style="width: '+porcentaje+'%;" aria-valuenow="'+progreso+'" aria-valuemin="0" aria-valuemax="'+actividades+'">'+porcentaje+'%</div>'+
'</div><center><b>'+progreso+'/'+actividades+'</center></b>';
        var contenidodescripcion = '<textarea style ="border-radius: 10px;" rows="3" type="text" class="form-control "  disabled>'+descripcion+'</textarea>';
        var contenidotipo = '<input style ="border-radius: 20px;" type="text" class="form-control " value="'+tipo+'" disabled>';
        
        
        $("#idmeta").html(contenidoidmeta);
        $("#progressbar").html(contenidoprogressbar);
        $("#descripcion").html(contenidodescripcion);
        $("#tipo").html(contenidotipo);
        
        
    });
    $(".btnMadmin").click(function() {
        var idmetaadmin = $(this).data('idmeta');
        var actividadesadmin  = $(this).data('act');
        var progresoadmin  = $(this).data('prog');
        var porcentajeadmin  = $(this).data('perc');
        var descripcionadmin  = $(this).data('desc');
        var tipoadmin  = $(this).data('tipo');
        var areaadmin  = $(this).data('area');
        var nombreadmin  = $(this).data('nombre');
        
        var contenidoidmeta = "<center><h3>Meta "+idmetaadmin +"</h3></center>";
        var contenidoprogressbar ='<label for="colFormLabelLg" class="col-sm-2 col-form-label col-form-label-lg">Progreso</label>'+
    '<div style ="border-radius: 10px;" class="progress col-sm-8" style="margin-right: 10px;">'+
  '<div class="progress-bar" role="progressbar" style="width: '+porcentajeadmin +'%;" aria-valuenow="'+progresoadmin +'" aria-valuemin="0" aria-valuemax="'+actividadesadmin +'">'+porcentajeadmin +'%</div>'+
'</div><center><b>'+progresoadmin +'/'+actividadesadmin +'</center></b>';
        var contenidodescripcion = '<textarea style ="border-radius: 10px;" rows="3" type="text" class="form-control "  >'+descripcionadmin +'</textarea>';
        var contenidotipo = '<input style ="border-radius: 20px;" type="text" class="form-control " value="'+tipoadmin+'" >';
        var contenidoarea = areaadmin;
        var contenidonombre = nombreadmin;
        var contenidoactividades = actividadesadmin;
        
        
        $("#idmetaadmin").html(contenidoidmeta);
        $("#progressbaradmin").html(contenidoprogressbar);
        $("#descripcionadmin").html(contenidodescripcion);
        $("#tipoadmin").html(contenidotipo);
        $("#areaadmin").html(contenidoarea);
        $("#nombreadmin").html(contenidonombre);
        $("#actividadesadmin").html(contenidoactividades);
        
        
    });
})
</script>

<div class="content-wrapper">
	
	<section class="content-header">
		
		<h1>
			Gestor Objetivos
		</h1>

		<ol class="breadcrumb">

	      <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>

	      <li class="active"><?php
if($_SESSION["perfil"] == "administrador"  || $_SESSION["perfil"]== "Super-Administrador"){

  echo 'Todas las metas';

}
?>
<?php
if($_SESSION["perfil"]== "vendedor" || $_SESSION["perfil"]== "tecnico" || $_SESSION["perfil"]== "secretaria"){

  echo 'Mis metas';

}
?></li>
      
    	</ol>

	</section>
    <section class="content">

	    <div class="box"> 

	       <div class="box-body">

	          
<h3>
  <?php
if($_SESSION["perfil"] == "administrador"  || $_SESSION["perfil"]== "Super-Administrador"){

  echo 'Todas las metas <a style="float:right" class="btn btn-primary" type="button" href="https://backend.comercializadoraegs.com/createobjetivo"><i class="fas fa-plus"></i> Agregar Meta
</a>';

}
?>
<?php
if($_SESSION["perfil"]== "vendedor" || $_SESSION["perfil"]== "tecnico" || $_SESSION["perfil"]== "secretaria"){

  echo '<center><div class=""><span class="label lb-lg label-success"><i class="fas  fa-bullseye"></i> METAS POR REALIZAR</span></div></center>';

}
?>
</h3>

<hr>
<center>
<div id="carrusel">
    <a href="#" class="left-arrow"><img src="https://img.icons8.com/windows/25/000000/chevron-left.png"/></a>
    <a href="#" class="right-arrow"><img src="https://img.icons8.com/windows/25/000000/chevron-right.png"/></a>
    <div class="carrusel">
<?php
if($_SESSION["perfil"] == "administrador"  || $_SESSION["perfil"]== "Super-Administrador"){
$metas = ControladorMetas::ctrMostrarMetas($item, $valor);
foreach ($metas as $key => $value){
$idperfil = $value["id_perfil"];    
$nombre = ControladorMetas::ctrMostrarNombrePorIdPerfil($tabla, $idperfil);
    $progbar = ($value["progreso"]*100)/$value["actividades"];
  
        echo '<div class="product" id="product_0">';
            echo '<br>';
            foreach ($nombre as $val){ 
            echo '<h5 style="margin-top:-10px; font-weight: bold;">META '.($key+1).' DE '.$val["nombre"].'</h5>';
            } 

          echo '<hr>';
          echo '<div class="box" style="--p:'.$progbar.';"><br>'.$value["progreso"].'/'.$value["actividades"].'</div>';
            echo '<p>'.$value["tipo"].'</p>
            
            
<button  class="btn btn-warning btn-sm btnMadmin" type="button" data-toggle="modal" data-target="#modal1" data-idmeta="'.($key+1).'" data-act="'.$value["actividades"].'" data-tipo="'.$value["tipo"].'" data-prog="'.$value["progreso"].'" data-desc="'.$value["descripcion"].'" data-perc="'.$progbar.'" data-area="'.$value["area"].'" data-nombre="'.$val["nombre"].'"><i class="fas fa-edit"></i>
</button>
</div>';
}
}
?>

        
        <?php
if($_SESSION["perfil"]== "vendedor" || $_SESSION["perfil"]== "tecnico" || $_SESSION["perfil"]== "secretaria"){
$id_perfil = $_SESSION["id"];
$metasperfil = ControladorMetas::ctrMostrarMetasPorIdPerfil($tabla, $id_perfil);
foreach ($metasperfil as $key => $value){  
    $progbar = ($value["progreso"]*100)/$value["actividades"];
  echo '
        <div class="product" id="product_0">
            <br>
            <h3 style="margin-top:-10px;">Meta '.($key+1).'</h3>
            

          <hr>
          <div class="box" style="--p:'.$progbar.';"><br>'.$value["progreso"].'/'.$value["actividades"].'</div>
            <p>'.$value["tipo"].'</p>
            
            
<center><button class="btn btn-sm btn-success btnMempleados" type="button" data-toggle="modal" data-target="#modal2" data-idmeta="'.($key+1).'" data-act="'.$value["actividades"].'" data-tipo="'.$value["tipo"].'" data-prog="'.$value["progreso"].'" data-desc="'.$value["descripcion"].'" data-perc="'.$progbar.'"><i class="fas fa-eye"></i>
</button></center>
</div>';
}
}
?>
        
    </div>
</div>
</center>
<hr>
</div>



            </div>
   </section>
</div>
<!-- /////////////////////////////////////////// MODAL PARA EDITAR //////////////////////////////////////////////////////-->

    <div class="modal fade" tabindex="-1" id="modal1">
        <div class="modal-dialog modal-lg">
            <div style ="border-radius: 20px;" class="modal-content">
            <div style="border-radius: 20px 20px 0% 0%; background:#138a1e; color:white" class="modal-header ">
                <button class="btn btn-danger" data-dismiss="modal" style="float: right"><i class="fas fa-times"></i></button>
                <div id="idmetaadmin"></div>
            </div>
            <div class="modal-body">
            <div id="progressbaradmin"></div>
                <form>
                

 <br>
<hr>
<center>
<div class="form-group row" name="area">
    <label for="colFormLabelLg" class="col-sm-2 col-form-label">Area:</label>
    <div class="col-sm-9">
      <select style ="border-radius: 20px;"  class="form-control ">
      <option id="areaadmin"></option>
      </select>
    </div>
  </div>
  </center>
  <hr>
  <center>
  <div class="form-group row" name="nombre">
    <label for="colFormLabelLg" class="col-sm-2 col-form-label">Usuario:</label>
    <div class="col-sm-9">
      <select style ="border-radius: 20px;"  class="form-control ">
      <option id="nombreadmin"></option>
      </select>
    </div>
  </div>
  </center>
  <hr>
  <center>
  <div class="form-group row" name="tipo">
    <label for="colFormLabelLg" class="col-sm-2 col-form-label col-form-label-lg">Tipo de Meta:</label>
    <div class="col-sm-9">
      <div id="tipoadmin"></div>
    </div>
  </div>
  </center>
  <hr>
  <br>
  <center>
  <div class="form-group row" name="descripcion">
    <label for="colFormLabelLg" class="col-sm-2 col-form-label col-form-label-lg">Descripción:</label>
    <div class="col-sm-9">
      <div id="descripcionadmin"></div>
    </div>
  </div>
  </center>
  <hr>
  <br>
  <center>
    <div class="form-group row" name="actividades">
    <label for="colFormLabelLg" class="col-sm-2 col-form-label">Actividades:</label>
    <div class="col-sm-2">
      <select style ="border-radius: 20px;"  class="form-control input" name="actividades">
  <option id="actividadesadmin"></option>
  <?php

    for ($i=0; $i <= 100; $i++) { 
      
      echo '<option>'.$i.'</option>';
    }

    ?>
</select>
    </div>
  </div>
  </center>
  <hr>
  <br>
  
            </div>
            <div class"modal-footer">
                <a class="btn btn-primary" style="float: right; margin-right: 15px" type="submit"> Guardar Meta </a>
</form><br><br>
            </div>
            </div>
        </div>
    </div>


<!-- /////////////////////////////////////////// MODAL PARA EMPLEADOS //////////////////////////////////////////////////////-->
<?php
if($_SESSION["perfil"]== "vendedor" || $_SESSION["perfil"]== "tecnico" || $_SESSION["perfil"]== "secretaria"){
    echo '

    <div class="modal fade" tabindex="-1" id="modal2">
        <div class="modal-dialog modal-lg">
            <div style ="border-radius: 20px;" class="modal-content">
            <div style="border-radius: 20px 20px 0% 0%; background:#138a1e; color:white" class="modal-header ">
                <button class="btn btn-danger" data-dismiss="modal" style="float: right"><i class="fas fa-times"></i></button>
                <div id="idmeta"></div>
            </div>
            <div class="modal-body"> 
            <div class="form-group row">
    <div id="progressbar"></div>
  </div>

<hr>
  <div class="form-group row">
    <label for="colFormLabelLg" class="col-sm-2 col-form-label col-form-label-lg">Tipo de Meta</label>
    <div class="col-sm-10">
    <div id="tipo"></div>
     
    </div>
  </div>
  <br>
  
  <div class="form-group row">
    <label for="colFormLabelLg" class="col-sm-2 col-form-label col-form-label-lg">Descripción</label>
    <div class="col-sm-10">
      <div id="descripcion"></div>
    </div>
  </div>
  <hr>
  
  <br>
  
            </div>
            <div class"modal-footer">
               
<br><br>
            </div>
            </div>
        </div>
    </div>
</div>';
}
?>


<script src="https://code.jquery.com/jquery-3.2.1.js"></script>
<script>
var current = 0;
var imagenes = new Array();
 
$(document).ready(function() {
    var numImages = 10;
    if (numImages <= 6) {
        $('.right-arrow').css('display', 'none');
        $('.left-arrow').css('display', 'none');
    }
 
    $('.left-arrow').on('click',function() {
        if (current > 0) {
            current = current - 1;
        } else {
            current = numImages - 6;
        }
 
        $(".carrusel").animate({"left": -($('#product_'+current).position().left)}, 600);
 
        return false;
    });
 
    $('.left-arrow').on('hover', function() {
        $(this).css('opacity','0.5');
    }, function() {
        $(this).css('opacity','1');
    });
 
    $('.right-arrow').on('hover', function() {
        $(this).css('opacity','0.5');
    }, function() {
        $(this).css('opacity','1');
    });
 
    $('.right-arrow').on('click', function() {
        if (numImages > current + 6) {
            current = current+1;
        } else {
            current = 0;
        }
 
        $(".carrusel").animate({"left": -($('#product_'+current).position().left)}, 600);
 
        return false;
    }); 
 });
</script>