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
      Administrador de Asesores
    </h1>

    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>

      <li class="active">Administrar Asesores</li>

    </ol>

  </section>

  <section class="content">

    <div class="box">
       
      <div class="box-header with-border">
         
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarAsesor">

          
          Agregar Asesor

        </button>

      </div>

      <div class="box-body">

        <table class="table table-bordered table-striped dt-responsive tablaAsesores" width="100%">
        
          <thead>
         
            <tr>
             
               <th style="width:10px">#</th>
               <th>Nombre</th>
               <th>correo</th>
               <th>Numero telefono</th>
               <th>Numero de telefono(dos)</th>
               <th>estado</th>


            </tr> 

          </thead>  

          <tbody>
            
            <?php


              if ($_SESSION["empresa"] == "Super-Administrador"){
                
                $itemUno = null;
                $valorDos = null;

                $asesores = Controladorasesores::ctrMostrarAsesoresEleg($itemUno, $valorDos);

              }else{

                $item = "id_empresa";
                $valor = $_SESSION["empresa"];
                $asesores = Controladorasesores::ctrMostrarAsesoresEmpresas($item, $valor);

              }

              foreach ($asesores as $key => $value){


                echo ' <tr>
                          <td>'.($key+1).'</td>
                          <td>'.$value["nombre"].'</td>
                          <td>'.$value["correo"].'</td>
                          <td>'.$value["numerodeCelular"].'</td>
                          <td>'.$value["numeroTelefono"].'</td>';

                         echo '<td>

                          <div class="btn-group">
                              
                            <button class="btn btn-warning btnEditarDatosAsesor" idAsesor="'.$value["id"].'" data-toggle="modal" data-target="#modalAgregarAsesorEditado"><i class="fas fa-edit"></i></button>

                            <button class="btn btn-danger btnEliminarAsesor" idAsesor="'.$value["id"].'"><i class="fas fa-times"></i></button>

                          </div>  

                        </td>

                      </tr>';            
             }


            ?>
      

          </tbody> 
     
        </table>
          
      </div>

    </div>

  </section>

</div>

<!--=====================================
MODAL AGREGAR PERFIL
======================================-->

<div id="modalAgregarAsesor" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" onsubmit="return validarAsesores()" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Asesor</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-user"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoNombreAsesor" placeholder="Ingresar nombre del Asesor" id="nuevoNombreAsesor" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL EMAIL -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-envelope"></i></span> 

                <input type="email" class="form-control input-lg" name="nuevoEmailAsesor" placeholder="Ingresar Email deL Asesor" id="nuevoEmailAsesor" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required>

              </div>

            </div>

            <!-- ENTRADA PARA El  NUMERO TELEFONICO UNO -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                <input type="tel" class="form-control input-lg" id="nuevoNumeroUno" name="nuevoNumeroUno" placeholder="Ingresa Numero Telefonico 1"  required>

              </div>

            </div>


            <!-- ENTRADA PARA El  NUMERO TELEFONICO DOS-->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                <input type="tel" class="form-control input-lg" id="nuevoNumeroDos" name="nuevoNumeroDos" placeholder="Ingresa Numero Telefonico 2" required>
                <?php
                echo'<input  type="hidden" value="'.$_SESSION["empresa"].'" name="empresa">';
                ?>
              </div>

            </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Guardar Asesor</button>

        </div>

        <?php

         $crearPerfilAsesor = new Controladorasesores();
         $crearPerfilAsesor -> ctrCrearPerfil();

        ?>

      </form>

    </div>

  </div>

</div>
</div>
</div>


<!--=====================================
MODAL EDITAR ASESOR
======================================-->

<div id="modalAgregarAsesorEditado" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Editar Asesor</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-user"></i></span> 

                <input type="text" class="form-control input-lg" name="editarNombreAsesor" id="editarNombreAsesor" placeholder="Ingresar nombre del Asesor" required>

                <input type="hidden" id="idAsesor" name="idAsesor">

              </div>

            </div>

             <!-- ENTRADA PARA EL EMAIL -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-envelope"></i></span> 

                <input type="email" class="form-control input-lg" name="editarEmailAsesor" placeholder="Ingresar Email deL Asesor" id="editarEmailAsesor" required>

              </div>

            </div>
           <!-- ENTRADA PARA El  NUMERO TELEFONICO UNO -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                <input type="tel" class="form-control input-lg" name="editarNumeroUno" id="editarNumeroUno" placeholder="Ingresa Numero Telefonico 1" required>

              </div>

            </div>
            <!-- ENTRADA PARA EL TELEFONO DOS -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                <input type="tel" class="form-control input-lg" id="editarTelefonoDos" name="editarTelefonoDos" placeholder="Ingresa Numero Telefonico 2" required>

              </div>

            </div>
                <!--=====================================
                ENTRADA PARA EL ESTADO
                ======================================-->             
                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fas fa-cogs"></i></span>

                    <select class="form-control input-lg estado" name="estado">
                      
                      <option class="estadoDelAsesor">
                        
                      </option>

                      <option value="Activo">
                        Activo
                      </option>
                      <option value="Inactivo">
                        Inactivo
                      </option>

                    </select>

                  </div>

                </div>

  
          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->
  
        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Modificar Perfil </button>

        </div>

     <?php

          $editarPerfil = new Controladorasesores();
          $editarPerfil -> ctrEditarAsesor();

        ?> 

      </form>

    </div>

  </div>

</div>

<?php

  $eliminarAsesor = new Controladorasesores();
  $eliminarAsesor -> ctrEliminarAsesor();

