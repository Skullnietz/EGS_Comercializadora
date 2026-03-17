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
      Gestor Órdenes De Servicio
    </h1>

    <ol class="breadcrumb">

        <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>

        <li class="active">Gestor Órdenes De Servicio</li>
      
      </ol>

  </section>

  <section class="content">

      <div class="box"> 

        <div class="box-header with-border">
          
          <?php

          //include "inicio/grafico-ventas.php";

          ?>

        </div>

        <div class="box-body">

          <div class="box-tools">

  
          <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarOrden">
            
            Agregar Orden

          </button>

          <button class='btn btn-info' data-toggle='modal' data-target='#modalAsignarPedido'><i class='fas fa-sort'></i></button>

         </div>

          <!--<label>Buscador</label>

            <input class="form-control col-md-3 light-table-filter" data-table="order-table" type="text" placeholder="Buscar..">
          
          <br>-->

         <!-- <div class="form-1-2">

            <label for="buscadroOrdenesLbael">Buscar:</label>
            <input type="text"  class="caja_Busqueda">

          </div>-->
                    
          <table class="table table-bordered table-striped dt-responsive tablaOrdenes order-table" width="100%">
          
            <thead>
              
              <tr>
                
                <th style="width:10px">#</th>
                <th>Empresa</th>
                <th>No. Orden</th>
                <th>Técnico</th>
                <th>Asesor</th>
                <th>Cliente</th>
                <th>TOTAL</th>
                <th>Estado</th>
                <!--<th>Fecha Entrada</th>-->
                <!--<th>Ultima modificación</th>-->
                <!--<th>Fecha de Salida</th>-->
                <th>Editar</th>
                <th>Eliminar</th>
                <th>Imprimir Ticket</th>
                
              </tr>

            </thead> 
            
            <?php
            
             // $administrador = ControladorAdministradores::ctrMostrarAdministradores($item, $valor);
               //foreach ($administrador as $key => $valueA) {
                 echo'

                 <input  type="hidden" id="tipoDePerfil" value="'.$_SESSION["perfil"].'"  placeholder="'.$_SESSION["perfil"].'">';
                //}
            ?>
        

          </table>


        </div>

      </div>

    </section>

</div>

<!--=====================================
MODAL AGREGAR OORDENES
======================================-->
<div id="modalAgregarOrden" class="modal fade" role="dialog">
  
   <div class="modal-dialog">
     
     <div class="modal-content">
       
       <!-- <form role="form" method="post" enctype="multipart/form-data"> -->
         
         <!--=====================================
        CABEZA DEL MODAL
        ======================================-->
        <div class="modal-header" style="background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Orden</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">
            
            <!-- ENTRADA PARA EL NOMBRE DE LA EMPRESA QUE VENDE-->

            <div class="form-group">
              
              <div class="input-group">
                
                 <span class="input-group-addon"><i class="fas fa-building"></i></span> 

                 <select class="form-control input-lg empresa" required>
                   
                   <option>
                     
                     Seleccionar Empresa

                   </option>

                    <?php
                      
                      $item = null;
                      $valor = null;

                      $respuesta = ControladorEmpresas::ctrMostrarEmpresasParaEditar($item, $valor);

                      foreach ($respuesta as $key => $value) {
                        
                        echo '

                        <option value="'.$value["id"].'">'.$value["empresa"].'</option>';

                      }
                  ?>
                        
                 </select>
                        
              </div>

            </div>
            <!--=====================================
            ENTRADA PARA EL TÍTULO
            ======================================-->

            <div class="form-group">
              
                <div class="input-group">
              
                  <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span> 

                  <input type="text" class="form-control input-lg validarOrden tituloOrden"  placeholder="Ingresar título orden">

                </div>

            </div>

            <!--=====================================
            ENTRADA PARA LA RUTA DEL PRODUCTO
            ======================================-->

            <div class="form-group">
              
                <div class="input-group">
              
                  <span class="input-group-addon"><i class="fas fa-link"></i></span> 

                  <input type="text" class="form-control input-lg rutaOrden" placeholder="Ruta url de la orden" readonly>

                </div>

            </div>



            <!--=====================================
            ENTRADA PARA AGREGAR MULTIMEDIA
            ======================================-->

            <div class="form-group agregarMultimedia"> 



                <!--=====================================
                ENTRADA PARA EL TECNICO
                ======================================-->             
                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fas fa-cogs"></i></span>

                    <select class="form-control input-lg tecnico">
                      
                      <option value="">
                        
                  Seleccionar Tecnico 

                      </option>

                      <?php

                        $item = null;
                        $valor =null;

                        $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);

                        foreach ($tecnico as $key => $value) {
                          
                          echo'<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                        }


                      ?>

                    </select>

                  </div>

                </div>

                  <!--=====================================
                ENTRADA PARA EL ASESOR
                ======================================-->      
                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fas fa-user"></i></span>
                      
                    <select class="form-control input-lg asesor">
                      
                      <option>
                        
                        Seleccionar asesor

                      </option>
                      
                      <?php

                        $item= null;
                        $valor=null;

                        $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);
                            foreach ($asesor as $key => $value) {
                              
                              echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';

                            }                         

                      ?>

                    </select>

                  </div>

                </div>
                
                <!--=====================================
                ENTRADA PARA EL CLIENTE
                ======================================-->   
                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fas fa-user"></i></i></span>

                    <select class="form-control input-lg cliente" style="width: 100%; height: 100%">
                      
                      <option value="">
                        
                        Seleccionar Cliente

                      </option>

                       <?php

                            $item = null;
                            $valor = null;

                            $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                            foreach ($usuario as $key => $value) {
                              
                                echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';

                              }

                          ?>

                    </select>

                  </div>

                </div>
                <!--=====================================
                ENTRADA PARA EL STATUS
                ======================================-->
                <div class="form-group">
                  
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fas fa-toggle-on"></i></span> 

                    <select class="form-control input-lg status">

                      <option value="">Selecionar Estado</option>

                      <?php
                      
                        if ($_SESSION["perfil"] == "tecnico") {
                          
                            echo '<option value="En revisión">En revisión</option>

                                  <option value="Supervisión (SUP)">Supervisión (SUP)</option>

                                  <option value="Terminada (ter)">Terminada (ter)</option>';
                        }

                        if ($_SESSION["perfil"] == "editor") {
                          
                            echo '<option value="En revisión">En revisión</option>
                                  <option value="Aceptado (ok)">Aceptado (ok)</option>
                                  <option value="Terminada (ter)">Terminada (ter)</option>
                                  <option value="Entregado (Ent)">Entregado (Ent)</option>';
                        }
                        
                        if ($_SESSION["perfil"] == "vendedor") {
                              echo '<option value="En revisión">En revisión</option>
                                  <option value="Aceptado (ok)">Aceptado (ok)</option>
                                  <option value="Terminada (ter)">Terminada (ter)</option>
                                  <option value="Entregado (Ent)">Entregado (Ent)</option>';
                        }
                        if ($_SESSION["perfil"] == "administrador") {
                          
                            echo '<option value="En revisión">En revisión</option>
                                  <option value="Supervisión (SUP)">Supervisión (SUP)</option>
                                  <option value="Pendiente de autorización (AUT">Pendiente de autorización (AUT</option>
                                  <option value="Aceptado (ok)">Aceptado (ok)</option>
                                  <option value="Terminada (ter)">Terminada (ter)</option>
                                  <option value="Cancelada (can)">Cancelada (can)</option>
                                  <option value="Entregado (Ent)">Entregado (Ent)</option>
                                  ';
                        }

                      ?>
                    
                      
                       
                       

                       

                       
                       
                       

                    </select>

                  </div>

                </div>
              <!--=====================================
              SUBIR MULTIMEDIA DE PRODUCTO FÍSICO
              ======================================-->
              
           <div class="multimediaOrden needsclick dz-clickable">

                <div class="dz-message needsclick cuadro multimedia">
                  
                  Arrastrar o dar click para subir imagenes.

                </div>

              </div>

            </div>
           
         

           <!--=====================================
            AGREGAR DESCRIPCIÓN
            ======================================-->

            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-pencil"></i></span>

                <textarea type="text" rows="3" class="form-control input-lg descripcionOrden" placeholder="Ingresar detalles internos"></textarea>

              </div>

            </div>

                <div class="form-group row">

                

            <!--=====================================
            ENTRADA PARA AGREGAR NUEVA PARTIDA
            ======================================-->

            <div class="form-group">
              
              <div class="panel">SUBIR FOTO PORTADA</div>

              <input type="file" class="fotoPortada">

              <p class="help-block">Tamaño recomendado 1280px * 720px <br> Peso máximo de la foto 2MB</p>

              <img loading="lazy" src="vistas/img/cabeceras/default/default.jpg" class="img-thumbnail previsualizarPortada" width="100%">

            </div>

            <!--=====================================
            AGREGAR FOTO DE MULTIMEDIA
            ======================================-->

            <div class="form-group">
                
              <div class="panel">SUBIR FOTO PRINCIPAL DEL PRODUCTO</div>

              <input type="file" class="fotoPrincipal">

              <p class="help-block">Tamaño recomendado 400px * 450px <br> Peso máximo de la foto 2MB</p>

              <img loading="lazy" src="vistas/img/productos/default/default.jpg" class="img-thumbnail previsualizarPrincipal" width="200px">

            </div>
              
        
            <!--=====================================
            PARTIDA UNO
            ======================================
            <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida1 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio1 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA DOS
            ======================================
            <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida2 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio2 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA TRES
            ======================================

          <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida3 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio3 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA CUATRO
            ======================================

          <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida4 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio4 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA CINCO
            ======================================

                      <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida5 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio5 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA SEIS
            ======================================
                      <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida6 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio6 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA SIETE
            ======================================
                      <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida7 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio7 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA OCHO
            ======================================
                                  <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida8 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio8 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA NUEVE
            ======================================
            <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida9 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio9 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

            </div>
          </div>
        </div>
        </div>-->
            <!--=====================================
            PARTIDA DIEZ
            ======================================
            <div class="form-group row">
              <div class="col-xs-6">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>
                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida10 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 
              </div>
            <div>
            <div class="col-xs-6"><div class="input-group">

              <input class="form-control input-lg precio10 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">

              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

            </div>
          </div>
        </div>-->
        </div>


                    </div>

                     <div class="form-group">
                
              <div class="panel">AGREGAR NUEVA ORDEN</div>

                 <a href="#" onclick="AgregarCampos();">
                          <div id="campos">
                         <input type="button" class="btn btn-primary " value="Agregar Partida"/></br></br>
                          </a>
            </div>
              

                    <!--=====================================
                PRODUCTO CALCULAR TOTALES
                ======================================-->
                 <div class="form-group row">

                <!--=====================================
                CAMBIO A REGRESAR
                ======================================-->
                        
                <div class="col-xs-6">

                 

                </div>

                <div class="col-xs-6">
                        <span><h5><center>TOTAL</center></h5></span>
                  <div class="input-group"> 

                    <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

                    <input type="number" class="form-control input-lg totalOrden"  min="0" value="0"  step="any" readonly>

                  </div>

                </div>

              </div>
              </div>           

          </div>


        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <div class="preload"></div>
  
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="button" class="btn btn-primary guardarOrden">Guardar Orden</button>

        </div>

       </form> 

     </div>

   </div>

</div>
<!--=====================================
MODAL AGREGAR OBSERVACION
======================================

<div id="modalAgregarDetalle" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">-->

        <!--=====================================
        CABEZA DEL MODAL
        ======================================

        <div class="modal-header" style="background:#138a1e; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Observacion</h4>

        </div>-->

        <!--=====================================
        CUERPO DEL MODAL
        ======================================

        <div class="modal-body">

            <div class="box-body">

              <div class="form-group">
                
                  <div class="panel">AGREGAR NUEVA OBSERVACION</div>

                    <a href="#" onclick="AgregarObservacion();">
                          <div id="camposObservacion">
                          
                          <input type="button" class="btn btn-primary " value="Agregar observación"/></br></br>
                          <?php

                         // echo'<input type="hidden" class="creador" value="'.$_SESSION["id"].'">
                            //  <input type="hidden" class="idOrdenObservacion">';

                          ?>
                          </a>e
                    </div>
            </div>-->

             <!--=====================================
            OBSERVACION UNO
            ======================================
             <div class="form-group row">
              
               <div class="col-xs-6">

                <div class="input-group">
                  <input class="form-control input-lg fecha" type="text"  readonly>
                  </div>
              </div>

              <div class="col-xs-6">
              
                <div class="input-group">

                <textarea type="text"  rows="3" class="form-control input-lg observacionUno" placeholder="Ingresar detalles internos" readonly></textarea>

                <span class="input-group-addon"><i class="fas fa-pencil"></i></span> 

              </div>
            </div>

          </div>
  
          </div>

        </div>-->

        <!--=====================================
        PIE DEL MODAL
        ======================================
  
        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary agregarObservacion">Agregar observación</button>

        </div>


    </div>

  </div>

</div>-->

<!--=====================================
MODAL EDITAR ORDEN
======================================-->
<div id="modalEditarOrden" class="modal fade" role="dialog">
  
  <div class="modal-dialog">
    
    <div class="modal-content">
      
        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->
        <div class="modal-header" style="background: #138a1e; color:white;">
          
          <button type="button" class="close" data-dissmiss="modal">&times;</button>

          <center><h2><b>ORDEN:</b></h2><h2 class="modal-title NumeroDeOrden"></h2></center>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->
        <div class="modal-body">
          
          <div class="box-body">
            <!--=====================================
            ENTRADA PARA EL TÍTULO
            ======================================-->
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span> 

                <input type="text" class="form-control input-lg validarOrden tituloOrden" readonly>

                <input type="hidden" class="idOrden">

              </div>

            </div>

            <!--=====================================
            ENTRADA PARA LA RUTA DE LA ORDEN
            ======================================-->
            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fas fa-link"></i></span>

                <input type="text" class="form-control input-lg rutaOrden" readonly>

              </div>

            </div>
            <!--=====================================
            ENTRADA PARA EL TECNICO
            ======================================-->
            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fas fa-cogs"></i></span>

                <select class="form-control input-lg seleccionarTecnico">
                  
                  <option class="optionEditarTecnico"></option>

                  <?php

                     $item = null;
                      $valor = null;

                      $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);

                      foreach ($tecnico as $key => $value) {
                        
                        echo '

                        <option value="'.$value["id"].'">'.$value["nombre"].'</option>';

                      }

                  ?>

                </select>

              </div>

            </div>
            <!--=====================================
            ENTRADA PARA EL ASESOR
            ======================================-->
            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fas fa-user"></i></span>

                <select class="form-control input-lg seleccionarAsesor">
                  
                  <option class="optionEditarAsesor"></option>

                  <?php

                     $item = null;
                      $valor = null;

                      $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);

                      foreach ($asesor as $key => $value) {
                        
                        echo '

                        <option value="'.$value["id"].'">'.$value["nombre"].'</option>';

                      }

                  ?>

                </select>

              </div>

            </div>
            <!--=====================================
            ENTRADA PARA EL CLIENTE
            ======================================-->
            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fas fa-user"></i></span>

                <select class="form-control input-lg seleccionarCliente" readonly>
                  
                  <option class="optionEditarCliente" ></option readonly>

                  <?php

                     $item = null;
                      $valor = null;

                      $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                      foreach ($usuario as $key => $value) {
                        
                        echo '

                        <option value="'.$value["id"].'">'.$value["nombre"].'</option>';

                      }

                  ?>

                </select>

              </div>

            </div>

            <?php

            if ($_SESSION["perfil"] != "tecnico") {
             
              echo'<div class="form-group">

                    <div class="input-group">

                      <span class="input-group-addon"><i class="fas fa-envelope"></i></span>

                      <input type="email" class="form-control input-lg correoCliente" readonly>

                    </div>


              </div>
              <div class="form-group">

                    <div class="input-group">

                      <span class="input-group-addon"><i class="fas fa-headphones"></i></span>

                      <input type="tel" class="form-control input-lg numeroCliente" readonly>

                    </div>


              </div>
               <div class="form-group">

                    <div class="input-group">

                      <span class="input-group-addon"><i class="fas fa-headphones"></i></span>

                      <input type="tel" class="form-control input-lg numeroClienteDos" readonly>

                    </div>


              </div>';

            }

            ?>
            <!--=====================================
            ENTRADA PARA EL ESTADO
            ======================================-->
            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>

                <select class="form-control input-lg seleccionarEstatus">
                  
                  <option class="optionEditarEstatus"></option>

                  <?php
                      
                        if ($_SESSION["perfil"] == "tecnico") {
                          
                            echo '<option class="pen" value="En revisión" style="display:none">En revisión</option>

                                  <option class="sup" value="Supervisión (SUP)" style="display:none">Supervisión (SUP)</option>

                                  <option class="ter" value="Terminada (ter)" style="display:none">Terminada (ter)</option>';
                        }

                        if ($_SESSION["perfil"] == "editor") {
                          
                            echo '<option class="pen" value="En revisión" style="display:none">En revisión</option>
                                 <option class="aut" value="Pendiente de autorización (AUT" style="display:none">Pendiente de autorización (AUT</option>
                                  <option class="ok" value="Aceptado (ok)" style="display:none">Aceptado (ok)</option>
                                  <option class="ter" value="Terminada (ter)" style="display:none">Terminada (ter)</option>
                                  <option class="ent" value="Entregado (Ent)" style="display:none">Entregado (Ent)</option>';
                        }
                         
                        if ($_SESSION["perfil"] == "vendedor") {
                              echo '<option value="En revisión">En revisión</option>
                                  <option value="Aceptado (ok)">Aceptado (ok)</option>
                                  <option value="Terminada (ter)">Terminada (ter)</option>
                                  <option value="Entregado (Ent)">Entregado (Ent)</option>';
                        }
                        if ($_SESSION["perfil"] == "administrador") {
                          
                            echo '<option class="pen" value="En revisión">En revisión</option>
                                  <option class="sup" value="Supervisión (SUP)">Supervisión (SUP)</option>
                                  <option class="aut" value="Pendiente de autorización (AUT">Pendiente de autorización (AUT</option>
                                  <option class="ok" value="Aceptado (ok)">Aceptado (ok)</option>
                                  <option class="ter" value="Terminada (ter)">Terminada (ter)</option>
                                  <option class="can" value="Cancelada (can)">Cancelada (can)</option>
                                  <option class="ent" value="Entregado (Ent)">Entregado (Ent)</option>
                                  ';
                        }

                      ?>

                </select>

              </div>

            </div>
            <!--=====================================
            AGREGAR MULTIMEDIA
            ======================================-->
            <div class="form-group agregarMultimedia">
              
              <div class="row previsualizarImgFisico"></div>

              <div class="multimediaFisica needsclick dz-clickable">
                
                <div class="dz-message needsclick">
                  
                  Arrastrar o dar click para subir imagenes.

                </div>

              </div>

            </div>

            <!--=====================================
            DETALLES INTERNOS
            ======================================-->
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-pencil"></i></span> 

                <textarea type="text"  rows="3" class="form-control input-lg descripcionOrden" placeholder="Ingresar detalles internos"></textarea>

            </div>

            <!--=====================================
            AGREGAR FOTO DE PORTADA
            ======================================-->
            <div class="form-group">
              
              <div class="panel">SUBIR FOTO PORTADA</div>

              <input type="file" class="fotoPortada">
              <input type="hidden" class="antiguaFotoPortada">

              <p class="help-block">Tamaño recomendado 1280px * 720px <br> Peso máximo de la foto 2MB</p>

              <img loading="lazy" src="vistas/img/cabeceras/default/default.jpg" class="img-thumbnail previsualizarPortada" width="100%">

            </div>

            <!--=====================================
            AGREGAR FOTO DE MULTIMEDIA
            ======================================-->

            <div class="form-group">
                
              <div class="panel">SUBIR FOTO PRINCIPAL DEL PRODUCTO</div>

              <input type="file" class="fotoPrincipal">
              <input type="hidden" class="antiguaFotoPrincipal">

              <p class="help-block">Tamaño recomendado 400px * 450px <br> Peso máximo de la foto 2MB</p>

              <img loading="lazy" src="vistas/img/productos/default/default.jpg" class="img-thumbnail previsualizarPrincipal" width="200px">

            </div>
            <!--=====================================
            PARTIDA UNO
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida1 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)" readonly></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio1 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio" readonly>

                    <span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

                  </div>

                </div>

              </div>

            </div>

            <!--=====================================
            PARTIDA DOS
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida2 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)" ></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio2 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio" >

                    <span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

                  </div>

                </div>

              </div>

            </div>
            <!--=====================================
            PARTIDA TRES
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida3 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)" ></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio3 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio" >

                    <span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

                  </div>

                </div>

              </div>

            </div>
            <!--=====================================
            PARTIDA CUATRO
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida4 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio4 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">

                    <span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

                  </div>

                </div>

              </div>

            </div>
            <!--=====================================
            PARTIDA CINCO
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida5 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio5 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">

                    <span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

                  </div>

                </div>

              </div>

            </div>

            <!--=====================================
            PARTIDA SEIS
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida6 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio6 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">

                    <span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

                  </div>

                </div>

              </div>

            </div>

            <!--=====================================
            PARTIDA SIETE
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida7 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio7 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">

                    <span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

                  </div>

                </div>

              </div>

            </div>
            <!--=====================================
            PARTIDA OCHO
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida8 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio8 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">

                    <span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

                  </div>

                </div>

              </div>

            </div>
            <!--=====================================
            PARTIDA NUEVE
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida9 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio9 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">

                    <span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

                  </div>

                </div>

              </div>

            </div>

            <!--=====================================
            PARTIDA DIEZ
            ======================================-->
            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fas fa-pencil"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida10 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 

                </div> 

              </div>

              <div>

                <div class="col-xs-6">

                  <div class="input-group">

                    <input class="form-control input-lg precio10 precioOrdenEditar" type="number" value="0"  min="0" step="any" placeholder="Precio">

                    <span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

                  </div>

                </div>

              </div>

            </div>
            <!--=====================================
            TOTAL ORDEN
            ======================================-->
            <div class="form-group row">
            <!--=====================================
            CAMBIO A REGRESAR
            ======================================-->
            <div class="col-xs-6">
                

            </div>

            <div class="col-xs-6">
              <span><h5><center>TOTAL</center></h5></span>
              <div class="input-group"> 

                <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

                <input type="number" class="form-control input-lg totalOrdenEditar"  min="0" value="0"  step="any" readonly>

               </div>                 
                
              </div>

            </div>

              <!--=====================================
                SELECCIONAR PEDIDO
                ======================================-->
                <div class="form-group">
                  
                  <select class="form-control input-lg selActivarPedido">
                    
                    <option value="">No tiene pedido</option>

                    <option value="pedido">Activar Pedido</option>
                    
                  </select>                  

                </div> 
              <!--=====================================
              DATOS DEL PEDIDO
              ======================================-->
              <div class="datosPedido" style="display:none">
                
                  <select class="form-control input-lg seleccionarPedido">
                  
                    <option class="optionEditarPedidos"></option>

                    <?php

                       $item = null;
                        $valor = null;

                        $pedido = ControladorPedidos::ctrMostrarorpedidosParaValidar($item,$valor);

                        foreach ($pedido as $key => $value) {
                          
                          echo '

                          <option value="'.$value["id"].'">'.$value["id"].'</option>';

                        }

                    ?>

                  </select>


              </div>                        

          </div>

        </div>


        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <div class="preload"></div>
  
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="button" class="btn btn-primary guardarCambiosOrden">Guardar cambios</button>

        </div>

    </div>

  </div>

</div>
</div>
<?php

  $eliminarOrden = new controladorOrdenes();
  $eliminarOrden -> ctrEliminarOrden();
?>

<!--=====================================
MODAL AGREGAR PEDIDO
======================================-->
<div id="modalAsignarPedido" class="modal fade" role="dialog"> 

  <form role="form" method="post" class="formularioPedidosDinamicos">
  
    <div class="modal-dialog">
      
      <div class="modal-content">
        
        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->
        <div class="modal-header" style="background:#138a1e; color:white">
          
          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4>Asignar Pedido</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->
        <div class="modal-body">
          
          <div class="box-body">

              <!--ASIGNAR ORDEN -->

              <div class="form-group">

                <div class="input-group">
                  
                  <select class="form-control input-lg" name="AsignarPedidoDinamico">
                  
                    <option>Asignar Pedido</option>

                    <?php

                       $item = null;
                        $valor = null;

                        $pedido = ControladorPedidos::ctrMostrarorpedidosParaValidar($item,$valor);

                        foreach ($pedido as $key => $value) {
                          
                          echo '

                          <option value="'.$value["id"].'">'.$value["id"].'</option>';

                        }

                    ?>

                  </select>

                </div> 

              </div>
              <!--=====================================
              ENTRADA PARA NUMERO DE ORDEN
              ======================================-->
              <div class="form-group">
                
                <div class="input-group">
                  
                  <select class="form-control input-lg" name="AsignarOrdenDinamico">
                    

                    <option>Asignar Orden</option>

                    <?php

                
                        $orden = controladorOrdenes::ctrMostrarOrdenes();

                        foreach ($orden as $key => $valueOrden) {
                          
                          echo '

                          <option value="'.$valueOrden["id"].'">'.$valueOrden["id"].'</option>';

                        }

                    ?>


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

          <button type="submit" class="btn btn-primary">Guardar Pedido</button>

        </div>


        </div>

      </div>
    
  </div>
    </div>

    <?php

         $crearPedido = new ControladorPedidos();
         $crearPedido -> ctrAsignarPedidoEnOrden();

        ?>

  </form>

</div>