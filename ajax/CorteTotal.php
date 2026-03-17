<?php

//if($_SESSION["perfil"] != "administrador"){

  //echo '<script>

  //window.location = "inicio";

  //</script>';

 // return;

//}

?>

<div class="content-wrapper">
  
   <section class="content-header">
      
    <h1>
      Corte Ventas
    </h1>

    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>

      <li class="active">Corte ventas Rapidas</li>
      
    </ol>

  </section>


  <section class="content">

    <div class="box"> 

      <div class="box-body">

        <div class="box-tools">

        <?php

        if(isset($_GET["fechaInicial"])){

          echo '<a href="vistas/modulos/descargar-reporte.php?reporte=reporte&fechaInicial='.$_GET["fechaInicial"].'&fechaFinal='.$_GET["fechaFinal"].'">';

        }else{

           echo '<a href="vistas/modulos/reporte.ventasR.php?reporte=compras">';

        }         

        ?>

          <button class="btn btn-success" style="margin-top:5px">Descargar reporte en Excel</button>

        </a>

        <button type="button" class="btn btn-default" id="daterange-btn2">
          
          <span>
            <i class="fa fa-calendar"></i>Rango de Fecha
          </span>

          <i class="fa fa-caret-down"></i>

        </button>


        </div>
  

        <br>
        
        <table class="table table-bordered table-striped dt-responsive tablaVentasRCorte" width="100%">
        
          <thead>
            
            <tr>
              
              <th style="width:10px">#</th>
              <th>No. Orden</th> 
              <th>Empresa</th>
              <th>Producto Uno</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Producto Dos</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Producto Tres</th>
              <th>Cantidad</th>  
              <th>Precio</th>         
              <th>Total</th>
              <th>Metodo</th>
              <th>Asesor</th>
              <th>Fecha</th>


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

          $respuesta = ControladorVentas::ctrRangoFechasVentas($fechaInicial, $fechaFinal);


          $item = "id";
          $valor = $value["empresa"];

          $NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

          $NombreEmpresa = $NameEmpresa["empresa"];

              foreach ($respuesta as $key => $value){


                echo ' <tr>
                          <td>'.($key+1).'</td>
                          <td>'.$value["id"].'</td>
                          <td>'.$NombreEmpresa.'</td>
                          <td>'.$value["productoUno"].'</td>
                          <td>'.$value["cantidadUno"].'</td>
                          <td>'.$value["precioUno"].'</td>
                          <td>'.$value["productoDos"].'</td>
                          <td>'.$value["cantidadDos"].'</td>
                          <td>'.$value["precioDos"].'</td>
                          <td>'.$value["productoTres"].'</td>
                          <td>'.$value["cantidadTres"].'</td>
                          <td>'.$value["precioTres"].'</td>
                          <td>'.$value["pago"].'</td>
                          <td>'.$value["metodo"].'</td>
                          <td>'.$value["asesor"].'</td>
                          <td>'.$value["fecha"].'</td>
                      </tr>';            
             }

          ?>
        
        </table>


      </div>

    </div>

  </section>

</div>
