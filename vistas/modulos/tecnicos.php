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
			Gestor Técnicos
		</h1>

		<ol class="breadcrumb">

	      <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>

	      <li class="active">Gestor Técnicos</li>
      
    	</ol>

	</section>

	<section class="content">

	    <div class="box"> 

	      <div class="box-body">

	        <div class="box-tools">

	        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarTecnico">
	          
	          Agregar Técnico

	        </button>

	        </div>


	        <br>
	        
	        <table class="table table-bordered table-striped dt-responsive tablaTecnicos" width="100%">
	        
	          <thead>
	            
	            <tr>
	              
	              <th style="width:10px">#</th>
	              <th>Nombre</th>
                <th>Correo</th>
	              <th>Teléfono</th>
	              <th>TeléfonoDos</th>
                <th>Departamento</th>
                <th>Horario De Comida</th>
	              <th>Fecha</th>
                <th>Acciones</th>

	            </tr>

	          </thead> 


	             <tbody>
            
            <?php

            //TRAER TECNICO


          if ($_SESSION["perfil"] == "Super-Administrador"){

            $item = null;
            $valor = null;

            $tecnico = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas($item,$valor);

          }else{

            $item = "id_empresa";
            $valor = $_SESSION["empresa"];

            $tecnico = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas($item,$valor);
          }

              foreach ($tecnico as $key => $value){


                echo ' <tr>
                          <td>'.($key+1).'</td>
                          <td>'.$value["nombre"].'</td>
                          <td>'.$value["correo"].'</td>
                          <td>'.$value["telefono"].'</td>
                          <td>'.$value["telefonoDos"].'</td>
                          <td>'.$value["departamento"].'</td>
                          <td>'.$value["HoraDeComida"].'</td>
                          <td>'.$value["fecha"].'</td>';



                         echo '<td>

                          <div class="btn-group">
                              
                            <button class="btn btn-warning btnEditarDatosTecnico" idTecnico="'.$value["id"].'" data-toggle="modal" data-target="#modalAgregarTecnicoEditado"><i class="fas fa-edit"></i></button><button class="btn btn-danger btnEliminarTecnico" idTecnico="'.$value["id"].'"><i class="fas fa-times"></i></button>

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

<div id="modalAgregarTecnico" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Técnico</h4>

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

                <input type="text" class="form-control input-lg" name="NombreDelTecnico" placeholder="Ingresar nombre del Técnico" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL EMAIL -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-envelope"></i></span> 

                <input type="email" class="form-control input-lg" name="Emailtecnico" placeholder="Ingresar Email deL Técnico" id="Emailtecnico" required>

              </div>

            </div>

            <!-- ENTRADA PARA El  NUMERO TELEFONICO UNO -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                <input type="tel" class="form-control input-lg" name="numeroTelTecnico" placeholder="Ingresa Número Telefónico 1" required>

              </div>

            </div>


            <!-- ENTRADA PARA El  NUMERO TELEFONICO DOS-->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                <input type="tel" class="form-control input-lg" name="numeroTelDosTecnico" placeholder="Ingresa Número Telefónico 2" required>

              </div>

            </div>

            <!-- ENTRADA PARA El  HORARIO DE COMIDA-->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-hourglass-start"></i></span> 

                <input type="text" class="form-control input-lg" name="HoraDeComida" placeholder="Ingresa Hora de Comida" required>
                <?php
                echo'<input  type="hidden" value="'.$_SESSION["empresa"].'" name="empresa">';
                ?>
              </div>

            </div>

            <!--============
              ENTRADA PARA SELECCIONAR PERFIL
              ==============-->
              <div class="form-group">
                
                <div class="input-group">
                  
                  <span class="input-group-addon"><i class="fas fa-cogs"></i></span>

                  <select class="form-control input-lg tecnico" name="areratecnico">
                    
                    <option>Seleccionar área</option>

                    <option value="electronica">Electrónica</option>
                    <option value="impresoras">Impresoras</option>
                    <option value="sistemas">Sistemas</option>

                  </select>

                </div>

              </div>


        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Guardar Técnico</button>

        </div>

        <?php

         $crearPerfilTecnico = new ControladorTecnicos();
         $crearPerfilTecnico -> ctrCrearTecnico();

        ?>

      </form>

    </div>

  </div>

</div>
</div>
</div>


<!--=====================================
MODAL EDITAR TECNICO
======================================-->

<div id="modalAgregarTecnicoEditado" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Editar Técnico</h4>

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

                <input type="text" class="form-control input-lg" name="editarNombreTecnico" id="editarNombreTecnico" placeholder="Ingresar nombre del Técnico" required>

                <input type="hidden" id="idTecnico" name="idTecnico">

              </div>

            </div>

             <!-- ENTRADA PARA EL EMAIL -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-envelope"></i></span> 

                <input type="email" class="form-control input-lg" name="editarEmailTecnico" placeholder="Ingresar Email deL Técnico" id="editarEmailTecnico" required>

              </div>

            </div>
           <!-- ENTRADA PARA El  NUMERO TELEFONICO UNO -->
             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                <input type="tel" class="form-control input-lg" name="editarNumeroUnoTecnico" id="editarNumeroUnoTecnico" placeholder="Ingresa Numero Telefonico 1" required>

              </div>

            </div>
            <!-- ENTRADA PARA EL TELEFONO DOS -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span> 

                <input type="tel" class="form-control input-lg" id="editarTelefonoDosTecnico" name="editarTelefonoDosTecnico" placeholder="Ingresa Numero Telefonico 2" required>

              </div>

            </div>

            <!-- ENTRADA PARA El  HORARIO DE COMIDA-->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-hourglass-start"></i></span> 

                <input type="text" class="form-control input-lg" id="HoraDeComidaEditada" name="HoraDeComidaEditada" placeholder="Ingresa Hora de Comida" required>

              </div>

            </div>

                <!--=====================================
                ENTRADA PARA EL ESTADO
                ======================================-->             
                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fas fa-cogs"></i></span>

                    <select class="form-control input-lg estado" name="estado">
                      
                      <option class="estadoDelTecnico">
                        
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

          <button type="submit" class="btn btn-primary">Modificar Perfil</button>

        </div>

     	<?php

          $editarTecnico = new ControladorTecnicos();
          $editarTecnico -> ctrEditarTecnico();

        ?> 

      </form>

    </div>

  </div>

</div>

<?php

  $eliminarTecnico = new ControladorTecnicos();
  $eliminarTecnico -> ctrEliminarTecnico();

