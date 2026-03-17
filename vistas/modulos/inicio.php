<!--=====================================
PÁGINA DE INICIO
======================================-->
<!-- content-wrapper -->
<div class="content-wrapper">

  <!-- content-header -->
  <section class="content-header">
    
    <h1>
    Tablero
    <small>Panel de Control</small>
    </h1>

    <ol class="breadcrumb">

      <li><a href="index.php?ruta=inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>
      <li class="active">Tablero</li>

    </ol>

  </section>
  <!-- content-header -->

  <!-- content -->
  <section class="content">
    
    <!-- row -->
    <div class="row">

       <?php

        if($_SESSION["perfil"] == "administrador"){

        include "inicio/superioresAdmin.php";

        }else if ($_SESSION["perfil"] == "vendedor"){
          
          include "inicio/superiorVendedor.php";

        }else if ($_SESSION["perfil"] == "tecnico") {
          
          include_once "inicio/superiorTecnicos.php";

        }

      
      ?>

    </div>
    <!-- row -->

    <!-- row -->
    <div class="row">
         <?php

         if($_SESSION["perfil"] == "administrador"){

          echo '<div class="col-lg-6">';
       
          include "inicio/asesores-caja.php";

          echo '</div>';

          }else if ($_SESSION["perfil"] == "tecnico") {
            
            echo '<div class="col-lg-6">';
       
              include "inicio/ordenes-caja.php";

            echo '</div>';


          }else if ($_SESSION["perfil"] == "vendedor") {
            echo '<div class="col-lg-6">';
       
              include "inicio/ordenes-AUT-caja.php";

            echo '</div>';
          }

        ?>

     


        
         <?php

          if($_SESSION["perfil"] == "administrador"){

            echo ' <div class="col-lg-6 col-md-6">';
         
            include "inicio/tecnicos-caja.php";

            echo '</div>'; 

          }else if($_SESSION["perfil"] == "tecnico") {
            
            echo ' <div class="col-lg-6">';
         
            include "inicio/ordenesEnRev.php";

            echo '</div>'; 

          }    

        ?>

       <div class="col-lg-12">

        <?php

        //include "inicio/productos-recientes.php";

       

        ?>
        

      </div>

    </div>
    <!-- row -->

 </section>
  <!-- content -->

</div>
  