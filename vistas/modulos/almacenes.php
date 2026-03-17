<?php
if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "vendedor"  AND $_SESSION["perfil"]!= "Super-Administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}

?>
<div class="content-wrapper">
    
  <section class="content-header">
      
    <h1>
      Gestor Almacenes
    </h1>

    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>

      <li class="active">Gestor Almacenes</li>
      
    </ol>

  </section>

    <section class="content">

    <div class="box"> 

      <div class="box-body">

        <div class="box-tools">

          <a href="vistas/modulos/reporte.almacenes.php?reporte=compras&empresa=<?echo $_SESSION["empresa"]?>">
            
              <button class="btn btn-success">Descargar Reporte En Excel</button>

          </a>


        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarAlmacen">
          
          Agregar Almacen

        </button>
      

        <br>
        
        <table class="table table-bordered table-striped dt-responsive tablaAlmacenes" width="100%">
        
          <thead>
            
            <tr>
              
              <th style="width:10px">#</th>
              <th>Nombre</th>
              <th>Dirección</th>
              <th>Telefono</th>
              <th>fecha</th>
              <th>Acciones</th>

            </tr>

          </thead> 

                  <?php
            
             // $administrador = ControladorAdministradores::ctrMostrarAdministradores($item, $valor);
               //foreach ($administrador as $key => $valueA) {
                 echo'

                 <input  type="hidden" id="tipoDePerfil" value="'.$_SESSION["perfil"].'"  placeholder="'.$_SESSION["perfil"].'">
              
                <input  type="hidden" id="id_empresa" value="'.$_SESSION["empresa"].'">

                ';
                //}
            ?>
        
        </table>


      </div>

    </div>

  </section>

</div>



<!--=====================================
MODAL AGREGAR ALMACEN
======================================-->

<div id="modalAgregarAlmacen" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Almacen</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-warehouse"></i></span> 

                <input type="text" class="form-control input-lg" name="nombreAlmacen" placeholder="Ingresar nombre del Almacen" required>

              </div>

            </div>

            <!-- ENTRADA PARA LA DIRECCION -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-map-signs"></i></span> 

                <input type="text" class="form-control input-lg" name="direccionAlmacen" placeholder="Ingresar la dirección del Almacen" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL TELEFONO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-phone-alt"></i></span> 

                <input type="text" class="form-control input-lg" name="telefonoAlmacen" placeholder="Ingresar el telefono del Almacen" required>

              </div>

            </div>
         
          <input  type="hidden" name="id_empresa" value="<?php echo $_SESSION['empresa']?> ">


        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Guardar Almacen</button>

        </div>

        <?php

          $objeto = new AlmacenesControlador();
          $objeto -> ctrlIngresarAlmacen();

        ?>

      </form>

    </div>

  </div>

</div>
</div>
</div>

