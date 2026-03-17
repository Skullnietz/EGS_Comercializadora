<?php
if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "tecnico" AND $_SESSION["perfil"]!= "secretaria" AND $_SESSION["perfil"]!= "Super-Administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;
}

?>

<div class="content-wrapper">
  
   <section class="content-header">
      
    <h1>
      Corte Ordenes
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
        if ($_SESSION["perfil"] == "administrador") {
          if(isset($_GET["fechaInicial"])){

            echo '<a href="vistas/modulos/descargar-reporte-Ordenes.php?reporte=reporte&fechaInicial='.$_GET["fechaInicial"].'&fechaFinal='.$_GET["fechaFinal"].'&empresa='.$_SESSION["empresa"].'">';

          }else{

             echo '<a href="vistas/modulos/descargar-reporte-Ordenes.php?reporte=ordenes&empresa='.$_SESSION["empresa"].'">';

          }         

            echo'<button class="btn btn-success" style="margin-top:5px">Descargar reporte en Excel</button>

          </a>';

          echo '<a href="vistas/modulos/descargar-reporte-infoOrden.php?reporte=infoordenes&empresa='.$_SESSION["empresa"].'">

            <button class="btn btn-success" style="margin-top:5px">Descargar informacion Ordenes</button>

          </a>';


        }  
      ?>

        <?php
        if ($_SESSION["perfil"] == "administrador") {

          if(isset($_GET["fechaInicial"])){

            echo '<a href="vistas/modulos/descargar-reporte-Ordenes-ingresos.php?reporte=reporteIngresos&fechaInicial='.$_GET["fechaInicial"].'&fechaFinal='.$_GET["fechaFinal"].'&empresa='.$_SESSION["empresa"].'">';

          }else{

             echo '<a href="vistas/modulos/descargar-reporte-Ordenes-ingresos.php?reporte=ordenesIgresos&empresa='.$_SESSION["empresa"].'">';

          }         

          

            echo'<button class="btn btn-success" style="margin-top:5px">Ingresos</button>

          </a>';
        }  

      ?>
      
      <?php

      if ($_SESSION["perfil"] == "administrador"  || $_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "vendedor") {

        if (isset($_GET["fechaInicial"])) {
          
          echo '<a href="vistas/modulos/descargar-reporte-OrdenesPEN.php?reporte=reporte&fechaInicial='.$_GET["fechaInicial"].'&fechaFinal='.$_GET["fechaFinal"].'&empresa='.$_SESSION["empresa"].'">';
          
        
        }else{

          echo '<a href="vistas/modulos/descargar-reporte-OrdenesPEN.php?reporte=ordenesPEN&empresa='.$_SESSION["empresa"].'">';

        }
     
        

        echo'<button class="btn btn-success" style="margin-top:5px">Descargar reporte REV</button>

        </a>';
       }
      ?>
      <?php
      if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor") {

        if (isset($_GET["fechaInicial"])) {
          
          echo '<a href="vistas/modulos/descargar-reporte-OrdenesSup.php?reporte=reporte&fechaInicial='.$_GET["fechaInicial"].'&fechaFinal='.$_GET["fechaFinal"].'&empresa='.$_SESSION["empresa"].'">';
          
        
        }else{

          echo '<a href="vistas/modulos/descargar-reporte-OrdenesSup.php?reporte=ordenesSup&empresa='.$_SESSION["empresa"].'">';

        }

        

        echo'<button class="btn btn-success" style="margin-top:5px">Descargar reporte SUP</button>

        </a>';
        }
        ?>
        <?php
        if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "editor"|| $_SESSION["perfil"] == "vendedor") {
        
        if (isset($_GET["fechaInicial"])) {
          
          echo '<a href="vistas/modulos/descargar-reporte-OrdenesAut.php?reporte=reporte&fechaInicial='.$_GET["fechaInicial"].'&fechaFinal='.$_GET["fechaFinal"].'&empresa='.$_SESSION["empresa"].'">';
          
        
        }else{

          echo '<a href="vistas/modulos/descargar-reporte-OrdenesAut.php?reporte=ordenesAUT&empresa='.$_SESSION["empresa"].'">';

        }

        echo'<button class="btn btn-success" style="margin-top:5px">Descargar reporte AUT</button>

        </a>';
        }
        ?>
        <?php
        if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "tecnico"|| $_SESSION["perfil"] == "vendedor") {

          if (isset($_GET["fechaInicial"])) {
            
            echo '<a href="vistas/modulos/descargar-reporte-OrdenesOK.php?reporte=reporte&fechaInicial='.$_GET["fechaInicial"].'&fechaFinal='.$_GET["fechaFinal"].'&empresa='.$_SESSION["empresa"].'">';
            
          
          }else{

            echo '<a href="vistas/modulos/descargar-reporte-OrdenesOK.php?reporte=ordenesOk&empresa='.$_SESSION["empresa"].'">';

          }
          

          echo'<button class="btn btn-success" style="margin-top:5px">Descargar reporte Ok</button>

          </a>';
        }
        ?>

        <?php
          if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "vendedor") {

             
              if (isset($_GET["fechaInicial"])) {
                
                echo '<a href="vistas/modulos/descargar-reporte-OrdenesTer.php?reporte=reporte&fechaInicial='.$_GET["fechaInicial"].'&fechaFinal='.$_GET["fechaFinal"].'&empresa='.$_SESSION["empresa"].'">';
                
              
              }else{

                echo '<a href="vistas/modulos/descargar-reporte-OrdenesTer.php?reporte=ordenesTer&empresa='.$_SESSION["empresa"].'&empresa='.$_SESSION["empresa"].'">';

              }
           
           

            echo'<button class="btn btn-success" style="margin-top:5px">Descargar reporte Ter</button>

            </a>';
          }

        ?>
        <?php
        if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "secretaria" || $_SESSION["perfil"] == "vendedor") {
          
          if (isset($_GET["fechaInicial"])) {
            
            echo '<a href="vistas/modulos/descargar-reporte-OrdenesEntregadas.php?reporte=reporte&fechaInicial='.$_GET["fechaInicial"].'&fechaFinal='.$_GET["fechaFinal"].'&empresa='.$_SESSION["empresa"].'">';
            
          
          }else{

            echo '<a href="vistas/modulos/descargar-reporte-OrdenesEntregadas.php?reporte=ordenesENT&empresa='.$_SESSION["empresa"].'">';

          }

          
          echo'<button class="btn btn-success" style="margin-top:5px">Descargar reporte ENT</button>

          </a>';
        } 
        ?>

        <?php
        if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "secretaria" || $_SESSION["perfil"] == "vendedor") {
          
          if (isset($_GET["fechaInicial"])) {
            
            echo '<a href="vistas/modulos/descargar-reporte-OrdenesParaVenta.php?reporte=reporte&fechaInicial='.$_GET["fechaInicial"].'&fechaFinal='.$_GET["fechaFinal"].'&empresa='.$_SESSION["empresa"].'">';
            
          
          }else{

            echo '<a href="vistas/modulos/descargar-reporte-OrdenesParaVenta.php?reporte=ordenesVenta&empresa='.$_SESSION["empresa"].'">';

          }

          
          echo'<button class="btn btn-success" style="margin-top:5px">Descargar reporte Ordenes Para Venta</button>

          </a>';
        } 

        if ($_SESSION["perfil"] == "administrador") {
          
          echo'<a href="vistas/modulos/descargar-reporte-marca.php?reporte=ordenespormarca&empresa='.$_SESSION["empresa"].'">

            <button class="btn btn-success" style="margin-top:5px">Descargar reporte Por Marca</button>

            </a>';

        }

        if ($_SESSION["perfil"] == "tecnico"){

          if (isset($_GET["fechaInicial"])) {
            
            echo '<a href="vistas/modulos/descargar-reporte-OrdenesEntregadas.php?reporte=entregadas&fechaInicial='.$_GET["fechaInicial"].'&fechaFinal='.$_GET["fechaFinal"].'&empresa='.$_SESSION["empresa"].'">';
            
          
          }else{

            $itemUno = "correo";
    
            $valorUno =  $_SESSION["email"];
              
            $tecnicoEnSession = ControladorTecnicos::ctrMostrarTecnicos($itemUno,$valorUno);
                
            $estadoreporte = "Entregado (Ent)";

            echo '<a href="vistas/modulos/descargar-reporte-OrdenesPorEstado.php?reporte=ordenesTER&empresa='.$_SESSION["empresa"].'&estado='.$estadoreporte.'&tecnico='.$tecnicoEnSession["id"].'">';

          }
          
          echo'<button class="btn btn-success" style="margin-top:5px">Descargar reporte ENT</button>

          </a>';
        }
        ?>
        <!---boton para colocar las fechas deseadas en los reportes-->

          </br>
          </br>

        <button type="button" class="btn btn-default" id="daterange-btnOrdenes">

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
              <th>No. Orden</th> 
              <th>Empresa</th>
              <th>Asesor</th>
              <th>estado</th>
              <th>Cliente</th>
              <th>total</th>
              <th>fecha</th>

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

          if ($_SESSION["perfil"] == "Super-Administrador"){
     
            $respuesta = controladorOrdenes::ctrRangoFechasOrdenesSuperAdmin($fechaInicial, $fechaFinal);

          }else{

            $itemUno = "id_empresa";
            $valorUno = $_SESSION["empresa"];

            $respuesta = controladorOrdenes::ctrRangoFechasOrdenes($fechaInicial, $fechaFinal, $itemUno, $valorUno);
          }
          


              foreach ($respuesta as $key => $value){

              $item = "id";
              $valor = $value["id_empresa"];

              $NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

              $NombreEmpresa = $NameEmpresa["empresa"];

              //TRAER ASESOR
                    
              $item = "id";
              $valor = $value["id_Asesor"];

              $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);

              $NombreAsesor = $asesor["nombre"];

             //TRAER CLIENTE (USUARIO)

                $item = "id";
                $valor = $value["id_usuario"];

                $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                $NombreUsuario = $usuario["nombre"];
                echo ' <tr>
                          <td>'.($key+1).'</td>
                          <td>'.$value["id"].'</td>
                          <td>'.$NombreEmpresa.'</td>
                          <td>'.$NombreAsesor.'</td>
                          <td>'.$value["estado"].'</td>
                          <td>'.$NombreUsuario.'</td>
                          <td>$'.$value["total"].'</td>
                          <td>'.$value["fecha"].'</td>
                      </tr>';            
             }

          ?>
        
        </table>


      </div>

    </div>

  </section>

</div>
