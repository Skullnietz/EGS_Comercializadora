<?php
if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "Super-Administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}

?>

<div class="content-wrapper">

  <section class="content-header">

   <h1>
      Administrador de Empresas
    </h1>

    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>

      <li class="active">Administrar Empresas</li>

    </ol>

  </section>

  <section class="content">

    <div class="box">
       
      <div class="box-header with-border">
         
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarEmpresa">

          
          Agregar Empresa

        </button>

      </div>

      <div class="box-body">

        <table class="table table-bordered table-striped dt-responsive tablaEmpresas" width="100%">
        
          <thead>
         
            <tr>
             
               <th style="width:10px">#</th>
               <th>Empresa</th>
               <th>Correo</th>
               <th>Numero telefono</th>
               <th>Numero de telefono(dos)</th>
               <th>Direccion</th>
               <th>Horario</th>
               <th>Editar</th>
               <th>Eliminar</th>

            </tr> 

          </thead>  

          <tbody>
            
            <?php
            
              $item =null;
              $valor = null;

             $empresas = ControladorEmpresas::ctrMostrarEmpresas($item, $valor);

              foreach ($empresas as $key => $value){


                echo ' <tr>
                          <td>'.($key+1).'</td>
                          <td>'.$value["empresa"].'</td>
                          <td>'.$value["correo"].'</td>
                          <td>'.$value["telefono"].'</td>
                          <td>'.$value["telefonoDos"].'</td>
                          <td>'.$value["direccion"].'</td>
                          <td>'.$value["Horario"].'</td>
                          <td><button class="btn btn-warning btnEditarEmpresa" idEmpresa="'.$value["id"].'" data-toggle="modal" data-target="#modalAgregarEmpresaEditada"><i class="fas fa-pencil"></i></button></td>

                          <td><button class="btn btn-danger btnEliminarEmpresa" idEmpresa="'.$value["id"].'"><i class="fas fa-times"></i></button></td>

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
MODAL AGREGAR EMPRESA
======================================-->

<div id="modalAgregarEmpresa" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" onsubmit="return validarEmpresa()" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Empresa</h4>

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

                <input type="text" class="form-control input-lg" id="empresa" name="empresa" placeholder="Ingresar nombre de la empresa" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL EMAIL -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-envelope"></i></span> 

                <input type="email" class="form-control input-lg" name="correo" placeholder="Ingresar Email de la empresa" id="correo" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required>

              </div>

            </div>

            <!-- ENTRADA PARA El  NUMERO TELEFONICO UNO -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                <input type="tel" class="form-control input-lg" id="telefonoDeEmpresa" name="telefonoDeEmpresa" placeholder="Ingresa Numero Telefonico 1" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required>

              </div>

            </div>


            <!-- ENTRADA PARA El  NUMERO TELEFONICO DOS-->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                <input type="tel" class="form-control input-lg" id="telefonoDosDeEmpresa" name="telefonoDosDeEmpresa" placeholder="Ingresa Numero Telefonico 2" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required>

              </div>

            </div>

            <!-- ENTRADA PARA LA DIRECCION-->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-street-view"></i></span> 

                <input type="text" class="form-control input-lg" id="direccion" name="direccion" placeholder="Ingresa Numero direccion" pattern="[A-Za-z0-9]+" required>

              </div>

            </div>


            <!-- ENTRADA PARA El  HORARIO-->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-hourglass-start"></i></span> 

                <input type="text" class="form-control input-lg" id="Horario" name="Horario" placeholder="Ingresa Hora de apertura y cierre" pattern="[A-Za-z0-9]+" required>

              </div>

            </div>
            
             <!-- ENTRADA PARA EL FACEBOOK-->
             
             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-facebook"></i></span> 

                <input type="text" class="form-control input-lg" id="Facebook" name="Facebook" placeholder="Ingresa Usuario de Facebook" pattern="https?://.+" required>

              </div>

            </div>
             <!-- ENTRADA PARA SITIO WEB-->
             
             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-code"></i></span> 

                <input type="text" class="form-control input-lg" id="Sitio" name="Sitio" placeholder="Ingresa Sitio Web" pattern="https?://.+" required>

              </div>

            </div>
            
        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Guardar Empresa</button>

        </div>

        <?php

         $crearEmpresa = new ControladorEmpresas();
         $crearEmpresa -> ctrCrearEmpresa();

        ?>

      </form>

    </div>

  </div>

</div>
</div>
</div>


<!--=====================================
MODAL EDITAR EMPRESA
======================================-->

<div id="modalAgregarEmpresaEditada" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Editar Empresa</h4>

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

                <input type="text" class="form-control input-lg" name="editarNombreEmpresa" id="editarNombreEmpresa" placeholder="Ingresar nombre de Empresa" required>

                <input type="hidden" id="idEmpresa" name="idEmpresa">

              </div>

            </div>

             <!-- ENTRADA PARA EL EMAIL -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-envelope"></i></span> 

                <input type="email" class="form-control input-lg" name="editarCorreoEmpresa" placeholder="Ingresar Email deL Asesor" id="editarCorreoEmpresa" required>

              </div>

            </div>
           <!-- ENTRADA PARA El  NUMERO TELEFONICO UNO -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                <input type="tel" class="form-control input-lg" name="editarNumeroUnoDeEmpresa" id="editarNumeroUnoDeEmpresa" placeholder="Ingresa Numero Telefonico 1" required>

              </div>

            </div>
            <!-- ENTRADA PARA EL TELEFONO DOS -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                <input type="tel" class="form-control input-lg" id="telefonoDosDeEmpresaEditado" name="telefonoDosDeEmpresaEditado" placeholder="Ingresa Numero Telefonico 2" required>

              </div>

            </div>

            <!-- ENTRADA PARA LA DIRECCION -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-street-view"></i></span> 

                <input type="tel" class="form-control input-lg" id="EditarDireccion" name="EditarDireccion" placeholder="Ingresa Numero direccion" required>

              </div>

            </div>
            <!-- ENTRADA PARA El  HORARIO DE COMIDA-->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-hourglass-start"></i></span> 

                <input type="text" class="form-control input-lg" id="HoraEditada" name="HoraEditada" placeholder="Ingresa Hora de apertura y cierre" required>

              </div>

            </div>
            
             <!-- ENTRADA EDICION DE FACEBOOK-->
        
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-facebook"></i></span> 

                <input type="text" class="form-control input-lg" id="Facebook" name="FacebookEditado" required>

              </div>

            </div>
            
            <!-- ENTRADA EDICION DE SITIO-->
        
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-code"></i></span> 

                <input type="text" class="form-control input-lg" id="Sitio" name="SitioEditado" required>

              </div>

            </div>
            
              <!-- CIERRE MODAL -->

  
          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->
  
        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Modificar Empresa</button>

        </div>

     <?php

          $editarPerfil = new ControladorEmpresas();
          $editarPerfil -> ctrEditarEmpresa();

        ?> 

      </form>

    </div>

  </div>

</div>

<?php

  $eliminarAsesor = new ControladorEmpresas();
  $eliminarAsesor -> ctrEliminarEmpresa();

