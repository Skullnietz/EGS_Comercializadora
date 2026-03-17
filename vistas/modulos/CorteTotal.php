<?php

if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "secretaria" AND $_SESSION["perfil"]!= "Super-Administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}

?>

<div class="content-wrapper">
  
   <section class="content-header">
      
    <h1>
      Corte Ventas
    </h1>

    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>

      <li class="active">Corte ventas Rapidas</li>
      
    </ol>

  </section>


  <section class="content">

    <div class="box"> 

      <div class="box-body">

        <div class="box-tools">

        <?php

        if(isset($_GET["fechaInicial"])){

          echo '<a href="vistas/modulos/descargar-reporte.php?reporte=reporte&fechaInicial='.$_GET["fechaInicial"].'&fechaFinal='.$_GET["fechaFinal"].'&empresa='.$_SESSION["empresa"].'">';

        }else{

           echo '<a href="vistas/modulos/reporte.ventasR.php?reporte=compras&empresa='.$_SESSION["empresa"].'">';

        }         

        ?>

          <button class="btn btn-success" style="margin-top:5px">Descargar reporte en Excel</button>

        </a>

        <button type="button" class="btn btn-default" id="daterange-btn2">
          
          <span>
            <i class="fas fa-calendar"></i>Rango de Fecha
          </span>

          <i class="fas fa-caret-down"></i>

        </button>


        </div>
  

        <br>
        
        <table class="table table-bordered table-striped dt-responsive tablaVentasRCorte" width="100%">
        
          <thead>
            
            <tr>
              
              <th style="width:10px">#</th>
              <th style="width:15px">No. Orden</th> 
              <th style="width:30px">Empresa</th>
              <th style="width:15px">Total</th>
              <th style="width:30px">Metodo</th>
              <th style="width:40px">Fecha</th>


            </tr>

          </thead> 

          <?php
          //TRAER VENTAS POR RANGO DE FECHA EN LA TABAL

         
          if(isset($_GET["fechaInicial"])){

              $fechaInicial = $_GET["fechaInicial"];
              $fechaFinal = $_GET["fechaFinal"];

          }else{

            $fechaInicial = null;
            $fechaFinal = null;

          }
          
          $itemVentas = "id_empresa";
          $valorVentas = $_SESSION["empresa"];

          $respuesta = ControladorVentas::ctrRangoFechasVentas($fechaInicial, $fechaFinal, $itemVentas, $valorVentas);
          

              foreach ($respuesta as $key => $value){

                $item = "id";
                $valor = $value["empresa"];

                $respuestaEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

                $NombreEmpresa = $respuestaEmpresa["empresa"];
                   
                      echo ' <tr>
                          <td>'.($key+1).'</td>
                          <td>'.$value["id"].'</td>
                          <td>'.$NombreEmpresa.'</td>
                          <td>$'.$value["pago"].'</td>
                          <td>'.$value["metodo"].'</td>
                          <td>'.$value["fecha"].'</td>
                      </tr>';            
             }

          ?>
        
        </table>


      </div>

    </div>

  </section>

</div>
