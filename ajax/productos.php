<?php

if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"] != "vendedor"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}
?>
<div class="content-wrapper">

  <section class="content-header">

     <h1>Gestor Productos</h1>

     <ol class="breadcrumb"> 

      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>

      <li class="active">Gestor Productos</li> 

    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">

        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarProducto">Agregar Producto</button>
        <!-------------
          AQUI SE VA A AGREGAR EL BOTON DE DESCARGAR REPORTE
        -->


        <?php
        echo'<a href="vistas/modulos/descargar-reporte-productos.php?reporte=productos">
            
            <button class="btn btn-success">Descargar productos</button>

        </a>';
        
        ?>


      </div>

      <div class="box-body">

        <table class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

          <thead>

            <tr>

              <th style="width:10px">#</th>
              <th>Códig de Producto</th>  
              <th>Empresa</th>
              <th>Titulo</th>
              <th>Categoria</th>
              <th>Subcategoria</th>
              <th>Ruta</th>
              <th>Estado</th>
              <th>Tipo</th>
              <th>Descripción</th>
              <th>palabras clave</th>
              <th>Portada</th>
              <th>imagen principal</th>
              <th>Multimedia</th>
              <th>Detalles</th>
              <th>Precio</th>
              <th>Peso</th>
              <th>Tiempo de Entrega</th>
              <th>Disponibilidad</th>
              <th>Proveedor</th>
              <th>Tipo de Oferta</th>
              <th>Valor Oferta</th>
              <th>Imagen Oferta</th>
              <th>Fin Oferta</th>
              <th>Acciones</th>

            </tr>

          </thead>
          <?php

          echo'<input  type="hidden" id="tipoDePerfil" value="'.$_SESSION["perfil"].'"  placeholder="'.$_SESSION["perfil"].'">
              
          <input  type="hidden" id="id_empresa" value="'.$_SESSION["empresa"].'">';

          ?>
        </table>

      </div>

    </div>

  </section>

</div>


<?php

  	$item = null;
  	$valor = null;

  	$productos = ControladorProductos::ctrMostrarProductos($item, $valor);

  		foreach ($productos as $key => $valueP) {
  		
  		

  	$item3 = "ruta";
		$valor3 = $valueP["ruta"];

		$cabeceras = ControladorCabeceras::ctrMostrarCabeceras($item3, $valor3);

    $acciones = "<div class='btn-group'><button class='btn btn-warning btnEditarProducto' idProducto='".$valueP["id"]."' data-toggle='modal' data-target='#modalEditarProducto'><i class='fas fa-pencil'></i></button><button class='btn btn-danger btnEliminarProducto' idProducto='".$valueP["id"]."' imgOferta='".$valueP["imgOferta"]."' rutaCabecera='".$valueP["ruta"]."' imgPortada='".$cabeceras["portada"]."' imgPrincipal='".$valueP["portada"]."'><i class='fas fa-times'></i></button></div>";

}
?>
<script type="text/javascript">
	
//$(document).ready(function(){
          // $(".tablaProductos").DataTable({
             // "processing": true,
             // "serverSide": true,
             // "sAjaxSource": "ServerSide/serversideProductos.php",
             // "columnDefs":[{
             //     "targets": -1,        
            //	"defaultContent": "<div class='btn-group'><button class='btn btn-warning  btnEditarProducto'  data-toggle='modal' data-target='#modalEditarProducto'><i class='fas fa-pencil'></i></button></div>"
   
           // }]   
   // }); 
//});
</script>



<!--=====================================
MODAL AGREGAR PRODUCTO
======================================-->
<div id="modalAgregarProducto" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <!-- <form role="form" method="post" enctype="multipart/form-data"> -->

      <!--=====================================
      CABEZA DEL MODAL
      ======================================-->
      <div class="modal-header" style="background:#138a1e; color:white">

        <button type="button" class="close" data-dismiss="modal">&times;</button>

        <h4 class="modal-title">Agregar producto</h4>

      </div>

      <!--=====================================
      CUERPO DEL MODAL
      ======================================-->
      <div class="modal-body">

        <div class="box-body">

          <!--=====================================
          ENTRADA PARA LA EMPRESA QUE VENDE EL PRODUCTO
          ======================================-->
          <!--<div class="form-group">

            <div class="input-group">

              <span class="input-group-addon"><i class="fas fa-building"></i></span>

              <select class="form-control input-lg empresa">

                <option value="">Seleccionar Empresa</option>

                  <?php

                    //$item = null; 
                    //$valor = null;

                    //$respuesta = ControladorEmpresas::ctrMostrarEmpresasParaEditar($item, $valor);

                    //foreach ($respuesta as $key => $value) {

                      //echo '<option>'.$value["empresa"].'</option>';

                    //}
                

                  echo'<input  type="hidden" value="'.$_SESSION["empresa"].'" class="empresa">';



                  ?>

                </select>

            </div>

          </div>-->
          <!--=====================================
          ENTRADA PARA EL TÍTULO 
          ======================================-->
          <div class="form-group">

            <div class="input-group">

              <span class="input-group-addon"><i class="fab fa-product-hunt"></i></span>
              <input type="text" class="form-control input-lg validarProducto tituloProducto"  placeholder="Ingresar título producto">

            </div>

          </div>
          <!--=====================================
          ENTRADA PARA LA RUTA DEL PRODUCTO
          ======================================-->
          <div class="form-group">

            <div class="input-group">

              <span class="input-group-addon"><i class="fas fa-link"></i></span>
              <input type="text" class="form-control input-lg rutaProducto" placeholder="Ruta url del producto" readonly>

            </div>

          </div>

          <!--=====================================
          CODIGO DE PRODUCTO
          ======================================-->
          <div class="form-group EntradaCodigo">

            <div class="input-group">

              <span class="input-group-addon"><i class="fas fa-barcode"></i></span>
              <input type="text" class="form-control input-lg SubircodigoProducto" id="codigoProducto" placeholder="Ingresa el codigo del producto">

            </div>
            </br>
            <button class="btn btn-success botonGenerarCodigo" type="button" onclick="generarbarcode()">Generar</button>
            <button class="btn btn-info botonImprimirCodigo" type="button" onclick="imprimir()">Imprimir</button> 

            <div id="print">

              <svg id="barcode"></svg>

            </div>

          </div>
          <!--=====================================
          ENTRADA PARA EL ALMACEN
          ======================================-->
          <input  type="hidden" class="id_empresa" value="<?php echo $_SESSION['empresa']?> ">
          <!--=====================================
          ENTRADA PARA EL ALMACEN
          ======================================-->
          <div class="form-group">

            <div class="input-group">
              
              <span class="input-group-addon"><i class="fas fa-bookmark"></i></span>
              <select class="form-control input-lg id_almacen">

                <option value="">Selecionar Almacen</option>
                
                <?php

                  $item = "id_empresa";
                  $valor = $_SESSION["empresa"];

                  $respuestaAlmacenes = AlmacenesControlador::ctrlMostrarAlmacenes($item, $valor);



                  foreach ($respuestaAlmacenes as $key => $valueAlmacenes) {
                    echo '<option value="'.$valueAlmacenes["id"].'" >'.$valueAlmacenes["nombre"].'</option>';

                  }

                ?>

              </select>

            </div>
          </div>
          <!--=====================================
          ENTRADA PARA LA RUTA DEL PRODUCTO
          ======================================-->
          <div class="form-group">

            <div class="input-group">
              
              <span class="input-group-addon"><i class="fas fa-bookmark"></i></span>
              <select class="form-control input-lg seleccionarTipo">

                <option value="">Selecionar tipo de producto</option>
                <option value="virtual">Servicio</option>
                <option value="fisico">Físico</option>

              </select>

            </div>
          </div>
          <!--=====================================
          ENTRADA PARA AGREGAR MULTIMEDIA
          ======================================-->
          <div class="form-group agregarMultimedia">
            <!--=====================================
            SUBIR MULTIMEDIA DE PRODUCTO VIRTUAL
            =====================================
            <div class="input-group multimediaVirtual" style="display:none">

              <span class="input-group-addon"><i class="fas fa-youtube-play"></i></span>
              <input type="text" class="form-control input-lg multimedia" placeholder="Ingresar código video youtube">
            </div>
            </br>=-->
            <!--=====================================
            Campos para datos clientes
            ======================================-->
            <!-- ENTRADA PARA EL NOMBRE -->
            <div class="form-group agregarnombre" style="display:none">
              
              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-user"></i></span>

                <input type="text"  class="form-control input-lg nombre"  id="Nombre"  placeholder="Ingresa Nombre Del Cliente" required>

              </div>

            </div>
            <!-- ENTRADA PARA El  NUMERO TELEFONICO -->
            <div class="form-group entradatelefono"  style="display:none">

              <div class="input-group">
                
                <span class="input-group-addon"><i class="fas fa-headphones"></i></span>
                <input type="tel" class="form-control input-lg numerotelcliente" name="numerotelcliente" placeholder="Ingresa Numero Telefonico 1" required>

              </div>

            </div>
            <!-- ENTRADA PARA El  NUMERO TELEFONICO DOS-->
            <div class="form-group entradatelefonoDos" style="display:none">

              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-headphones"></i></span>
                <input type="tel" class="form-control input-lg nuevoNumeroDosc" name="nuevoNumeroDosc" placeholder="Ingresa Numero Telefonico 2" required>

              </div>

            </div>
            <!-- ENTRADA PARA EL EMAIL -->
            <div class="form-group entradacorreo" style="display:none">

              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control input-lg entradaCorreoc" name="entradaCorreoc" placeholder="Ingresar Email deL Cliente" id="email del client" required>

              </div>

            </div>
            <!-- ENTRADA PARA DETALLES VENTAS -->
            <div class="form-group entradadetallesventas" style="display:none">

              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                <textarea type="text" maxlength="320" rows="3" class="form-control input-lg descripcionventas" placeholder="Ingresar detalles ventas"></textarea>

              </div>

            </div>

            <!-- ENTRADA PARA DETALLES VENTAS -->
            <div class="form-group entradadetallestecnicos" style="display:none">

              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                <textarea type="text" maxlength="320" rows="3" class="form-control input-lg descripciondetalles" placeholder="Ingresar detalles tecnicos"></textarea>

              </div>

            </div>
            <!--=====================================
            SUBIR MULTIMEDIA DE PRODUCTO FÍSICO
            ======================================-->
            <div class="multimediaFisica needsclick dz-clickable" style="display:none">

              <div class="dz-message needsclick">

                Arrastrar o dar click para subir imagenes.

              </div>

            </div>

          </div>
          <!--=====================================
          AGREGAR DETALLES VIRTUALES
          ======================================-->
          <div class="detallesVirtual" style="display:none">

            <div class="panel">DETALLES</div>

              <!-- Tipo de servicio -->
              <div class="form-group row">

                <div class="col-xs-3">

                  <input class="form-control input-lg" type="text" value="Servicio" readonly>

                </div>
                <div class="col-xs-9">
                  <!--<input class="form-control input-lg tagsInput detalleColor" data-role="tagsinput" type="text" placeholder="Descripción">-->

                </div>

              </div>

            </div>
            <!--=====================================
            AGREGAR DETALLES FÍSICOS
            ======================================-->
            <div class="detallesFisicos" style="display:none">

              <div class="panel">DETALLES</div>

              <!-- COLOR -->
              <div class="form-group row">

                <div class="col-xs-3">

                  <input class="form-control input-lg" type="text" value="Detalles" readonly>

                </div>
                <div class="col-xs-9">

                  <input class="form-control input-lg tagsInput detalleColor" data-role="tagsinput" type="text" placeholder="Separe valores con coma">

                </div>

              </div>

            </div>
            <!--=====================================
            AGREGAR CATEGORÍA
            ======================================--> 
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-th"></i></span>
                <select class="form-control input-lg seleccionarCategoria">

                  <option value="">Selecionar categoría</option>
                  <?php

                    $item = null;
                    $valor = null;

                    $categorias = ControladorCategorias::ctrMostrarCategorias($item, $valor);

                    foreach ($categorias as $key => $value) {

                      echo '<option value="'.$value["id"].'">'.$value["categoria"].'</option>';
                    }

                  ?>
                </select>

              </div>
            
            </div>
            <!--=====================================
            AGREGAR SUBCATEGORÍA
            ======================================-->
            <div class="form-group  entradaSubcategoria" style="display:none">

              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-th"></i></span>
                <select class="form-control input-lg seleccionarSubCategoria">

                </select>

              </div>

            </div>
            <!--=====================================
            AGREGAR DESCRIPCIÓN
            ======================================-->
            <div class="form-group">

              <div class="input-group">

               <span class="input-group-addon"><i class="fas fa-pencil-alt"></i></span>
               <textarea type="text" maxlength="320" rows="3" class="form-control input-lg descripcionProducto" placeholder="Ingresar descripción producto"></textarea>

              </div>

            </div>
            <!--=====================================
            AGREGAR PALABRAS CLAVES
            ======================================-->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fas fa-key"></i></span>
                <input type="text" class="form-control input-lg tagsInput pClavesProducto" data-role="tagsinput"  placeholder="Ingresar palabras claves">

              </div>

            </div>
            <!--=====================================
            AGREGAR FOTO DE PORTADA
            ======================================-->
            <div class="form-group">

              <div class="panel">SUBIR FOTO PORTADA</div>

              <input type="file" class="fotoPortada">

              <p class="help-block">Tamaño recomendado 1280px * 720px <br> Peso máximo de la foto 2MB</p>

              <img loading="lazy" src="vistas/img/default/default.png" class="img-thumbnail previsualizarPortada" width="100%">

            </div>
            <!--=====================================
            AGREGAR FOTO DE MULTIMEDIA
            ======================================-->
            <div class="form-group">

              <div class="panel">SUBIR FOTO PRINCIPAL DEL PRODUCTO</div>

                <input type="file" class="fotoPrincipal">

                  <p class="help-block">Tamaño recomendado 400px * 450px <br> Peso máximo de la foto 2MB</p>
                  <img loading="lazy" src="vistas/img/default/default.png" class="img-thumbnail previsualizarPrincipal" width="200px">

              </div>
              <!--=====================================
              AGREGAR PRECIO, PESO Y ENTREGA
              ======================================-->
              <div class="form-group row">

                <!-- PRECIO -->
                <div class="col-md-4 col-xs-12">

                  <div class="panel">PRECIO</div>

                  <div class="input-group">

                    <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>
                    <input type="number" class="form-control input-lg precio" min="0" step="any">

                  </div>

                </div>
                <!-- PESO -->
                <div class="col-md-4 col-xs-12">

                  <div class="panel">PESO</div>

                    <div class="input-group">

                      <span class="input-group-addon"><i class="fas fa-balance-scale"></i></span>
                      <input type="number" class="form-control input-lg peso" min="0" step="any" value="0">

                    </div>

                  </div>
                  <!-- ENTREGA -->
                  <div class="col-md-4 col-xs-12">

                    <div class="panel">DÍAS DE ENTREGA</div>

                    <div class="input-group">

                      <span class="input-group-addon"><i class="fas fa-truck"></i></span>
                      <input type="number" class="form-control input-lg entrega" min="0" value="0">

                    </div>

                  </div>

                </div>
                <div class="form-group row">
                  <!--=====================================
                  AGREGAR CANTIDAD DISPONIBLE              
                  ======================================-->
                  <div class="col-md-5 col-xs-12">
                    <br>
                    <div class="panel">CANTIDAD DISPONIBLE</div>

                      <div class="input-group">

                        <span class="input-group-addon"><i class="fas fa-industry"></i></span>
                        <input type="number" class="form-control input-lg disponibilidad" min="0" value="0">
                        <span class="input-group-addon">
                          <select class="seleccionarMedida">
                            <option>PZAS</option>
                                            <option>GRS</option>
                                            <option>KG</option>
                                            <option>cuartillo</option>
                                            <option>Tapa</option>
                                            <option>Caja</option>
                                            <option>lister</option>
                          </select>
                        </span>

                      </div>

                    </div>
                    <!--=====================================
                    AGREGAR PROVEEDOR
                    ======================================-->
                    <div class="col-md-4 col-xs-12">

                      <br>

                      <div class="panel">PROVEEDOR</div>

                        <div class="input-group">

                          <span class="input-group-addon"><i class="fas fa-building"></i></span>
                          <input type="text" class="form-control input-lg Proveedor"  placeholder="Proveedor"> 

                        </div>

                      </div>
                      <div class="col-md-4 col-xs12">

                        <br>

                        <div class="panel">Inversión</div>

                          <div class="input-group">

                            <span class="input-group-addon"><i class="fas fa-money-check-alt"></i></span>
                            <input type="number" class="form-control input-lg EntradInversion">

                          </div>

                        </div>

                      </div>
                      <!--=====================================
                      AGREGAR TIPO
                      ======================================-->
                      <div class="form-group">
                        
                        <select class="form-control input-lg selActivarTipo">
                          
                          <option>Escoger tipo</option>
                          <option value="caja">Caja</option>
                          <option>Lister</option>

                        </select>

                      </div>
                      <div class="datosTipo" style="display:none">

                        <div class="form-group row">

                          <div class="col-xs-6"> 
                            
                            <span class="input-group-addon"><i class="fas fa-cubes"></i></span>
                              <input class="form-control input-lg cantidadTipo" type="number" value="0"   min="0" placeholder="Cantidad"> 

                          </div>
                          
                        </div>

                      </div>

                        <!--=====================================
                        VALOR OFERTAS
                        ======================================-->

                      <!--=====================================
                      AGREGAR OFERTAS
                      ======================================-->
                      <div class="form-group">

                        <select class="form-control input-lg selActivarOferta">
                          
                          <option value="">No tiene oferta</option>
                          <option value="oferta">Activar oferta</option>

                        </select>
                      </div>
                      <div class="datosOferta" style="display:none">

                        <!--=====================================
                        VALOR OFERTAS
                        ======================================-->
                        <div class="form-group row"> 

                          <div class="col-xs-6"> 

                            <div class="input-group">

                              <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>
                              <input class="form-control input-lg valorOferta precioOferta" tipo="oferta" type="number" value="0"   min="0" placeholder="Precio"> 

                            </div>

                          </div>
                          <div class="col-xs-6">

                            <div class="input-group">

                              <input class="form-control input-lg valorOferta descuentoOferta" tipo="descuento" type="number" value="0"  min="0" placeholder="Descuento">

                               <span class="input-group-addon"><i class="fas fa-percent"></i></span>

                            </div>

                          </div>

                        </div>
                        <!--===================================== 
                        FECHA FINALIZACIÓN OFERTA
                        ======================================-->
                        <div class="form-group">

                          <div class="input-group date">

                            <input type='text' class="form-control datepicker input-lg valorOferta finOferta">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                            </span>

                          </div>

                        </div>
                        <!--=====================================
                        FOTO OFERTA
                        ======================================--> 
                        <div class="form-group">

                          <div class="panel">SUBIR FOTO OFERTA</div>

                            <input type="file" class="fotoOferta valorOferta">
                            <p class="help-block">Tamaño recomendado 640px * 430px <br> Peso máximo de la foto 2MB</p>

                            <img loading="lazy" src="vistas/img/ofertas/default/default.jpg" class="img-thumbnail previsualizarOferta" width="100px">

                          </div>

                          <div class="col-md-4 col-xs12">
                            
                            <br>
                            <div class="panel">Inversión</div> 

                              <div class="input-group">

                                <span class="input-group-addon"><i class="fas fa-money"></i></span>
                                <input type="number" class="form-control input-lg inversionEditada">

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
                          <button type="button" class="btn btn-primary guardarProducto">Guardar producto</button>

                        </div>

                      </form>

                    </div>

                  </div>

                </div>


                <!--=====================================
                MODAL EDITAR PRODUCTO
                ======================================-->
                <div id="modalEditarProducto" class="modal fade" role="dialog">

                  <div class="modal-dialog"> 

                    <div class="modal-content">

                      <!--=====================================
                      CABEZA DEL MODAL
                      ======================================-->
                      <div class="modal-header" style="background:#138a1e; color:white">

                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                          <h4 class="modal-title">Editar producto</h4>
                      </div>        
                      <!--=====================================
                      CUERPO DEL MODAL
                      ======================================-->
                      <div class="modal-body"> 

                        <div class="box-body">

                          <!--=====================================
                          ENTRADA PARA LA EMPRESA QUE VENDE EL PRODUCTO 
                          ======================================-->
                         <div class="form-group"> 

                            <div class="input-group">

                              <span class="input-group-addon"><i class="fas fa-building"></i></span>
                                <?php

                                  $item = "id";
                                  $valor = $_SESSION["empresa"];

                                  $respuesta = ControladorEmpresas::ctrMostrarEmpresasParaEditar($item, $valor);

                                  //foreach ($respuesta as $key => $value){

                                    //echo '<option>'.$value["empresa"].'</option>'; 
                              

                                  //} 

                                 echo'<input  type="text" value="'.$respuesta["empresa"].'" class="form-control input-lg empresa" readonly>';
                                ?> 

                            </div>

                          </div>
                        <!--=====================================
                        ENTRADA PARA EL TÍTULO
                        ======================================-->
                        <div class="form-group">

                          <div class="input-group">

                            <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span>
                            <input type="text" class="form-control input-lg validarProducto tituloProducto" readonly>
                            <input type="hidden" class="idProducto">
                            <input type="hidden" class="idCabecera">

                          </div>

                        </div> 
                        <!--=====================================
                        ENTRADA PARA LA RUTA DEL PRODUCTO
                        ======================================-->
                        <div class="form-group">

                          <div class="input-group">
                            
                            <span class="input-group-addon"><i class="fas fa-link"></i></span>
                            <input type="text" class="form-control input-lg rutaProducto" readonly>

                          </div>

                        </div> 
                        <!--=====================================
                        ENTRADA PARA LA EDICION DEL CODIGO DEL PRODUCTO
                        ======================================-->
                        <div class="form-group">

                          <div class="input-group">

                            <span class="input-group-addon"><i class="fas fa-barcode"></i></span>
                            <input type="text" class="form-control input-lg codigoEditado" id="codigoProductoEditado" required>

                          </div>
                          
                          <br>
                          
                          <button class="btn btn-success" type="button" onclick="generarbarcodeEditado()">Generar</button>

                          <button class="btn btn-info " type="button" onclick="imprimirCodigoEditado()">Imprimir</button>               
                          <!--=====================================
                          AREAA DE IMPRECIO NDE CODIGO
                          ======================================-->
                          <div id="print">

                            <svg id="barcode"></svg>

                          </div>

                        </div>
                        <!--=====================================
                        ENTRADA PARA SELECCIONAR EL TIPO DEL PRODUCTO 
                        ======================================-->
                        <div class="form-group">

                          <div class="input-group">

                            <span class="input-group-addon"><i class="fas fa-bookmark-o"></i></span> 
                            <input type="text" class="form-control input-lg seleccionarTipo" readonly>

                          </div>

                        </div>
                        <!--=====================================
                        ENTRADA PARA AGREGAR MULTIMEDIA
                        ======================================-->
                        <div class="form-group agregarMultimedia">

                          <!--=====================================
                          SUBIR MULTIMEDIA DE PRODUCTO VIRTUAL
                          ======================================-->
                          <div class="input-group multimediaVirtual" style="display:none">

                            <span class="input-group-addon"><i class="fas fa-youtube-play"></i></span>
                            <input type="text" class="form-control input-lg multimedia">

                          </div>
                          <!--===================================== 
                          SUBIR MULTIMEDIA DE PRODUCTO FÍSICO
                          ======================================-->
                          <div class="row previsualizarImgFisico"></div>

                            <div class="multimediaFisica needsclick dz-clickable" style="display:none">

                              <div class="dz-message needsclick">
                              Arrastrar o dar click para subir imagenes.
                              </div>

                            </div> 

                          </div>
                          <!--=====================================
                          AGREGAR DETALLES VIRTUALES
                          ======================================-->
                          <div class="detallesVirtual" style="display:none">

                            <div class="panel">DETALLES</div>
                            <!-- CLASES -->
                            <div class="form-group row">

                              <div class="col-xs-3"> 

                                <input class="form-control input-lg" type="text" value="Reparacion" readonly>

                              </div>
                              <div class="col-xs-9">

                                <input type="text" class="form-control input-lg detalleReparacion" placeholder="Descripción">

                              </div>

                            </div>

                          </div>
                          <!--=====================================
                          AGREGAR DETALLES FÍSICOS
                          ======================================-->
                          <div class="detallesFisicos" style="display:none">

                            <!-- COLOR -->
                            <div class="form-group row">

                              <div class="col-xs-3">

                                <input class="form-control input-lg" type="text" value="Detalles" readonly>

                              </div>

                              <div class="col-xs-9 editarColor">

                                <!--   <input class="form-control input-lg tagsInput detalleColor" data-role="tagsinput" type="text" placeholder="Separe valores con coma"> -->
                              </div>

                            </div> 
                          
                           </div>
                           <!--=====================================
                           AGREGAR CATEGORÍA 
                          ======================================--> 
                          <div class="form-group">

                            <div class="input-group"> 

                              <span class="input-group-addon"><i class="fas fa-th"></i></span>
                              <select class="form-control input-lg seleccionarCategoria">

                                <option class="optionEditarCategoria"></option>
                                <?php

                                  $item = null;
                                  $valor = null;

                                  $categorias = ControladorCategorias::ctrMostrarCategorias($item, $valor);

                                     foreach ($categorias as $key => $value) {  

                                      echo '<option value="'.$value["id"].'">'.$value["categoria"].'</option>';

                                    } 
                                ?>
                              </select>

                            </div>

                          </div>
                          <!--=====================================
                          AGREGAR SUBCATEGORÍA 
                          ======================================-->
                          <div class="form-group entradaSubcategoria">

                            <div class="input-group">

                              <span class="input-group-addon"><i class="fas fa-th"></i></span>
                              <select class="form-control input-lg seleccionarSubCategoria">

                                <option class="optionEditarSubCategoria"></option>

                              </select>

                            </div> 

                          </div>
                          <!--=====================================
                          AGREGAR DESCRIPCIÓN
                          ======================================-->
                          <div class="form-group">

                            <div class="input-group"> 

                              <span class="input-group-addon"><i class="fas fa-pencil"></i></span>
                              <textarea type="text" maxlength="320" rows="3" class="form-control input-lg descripcionProducto"></textarea>

                            </div> 

                          </div>
                          <!--=====================================
                          AGREGAR PALABRAS CLAVES
                          ======================================-->
                          <div class="form-group editarPalabrasClaves">

                            <!--   <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-key"></i></span>
                            <input type="text" class="form-control input-lg tagsInput pClavesProducto" data-role="tagsinput"  placeholder="Ingresar palabras claves">
                            </div> -->

                          </div> 
                          <!--=====================================
                          AGREGAR FOTO DE PORTADA
                          ======================================-->
                          <div class="form-group">

                            <div class="panel">SUBIR FOTO PORTADA</div>

                              <input type="file" class="fotoPortada"> 
                              <input type="hidden" class="antiguaFotoPortada">

                              <p class="help-block">Tamaño recomendado 1280px * 720px <br> Peso máximo de la foto 2MB</p>

                              <img loading="lazy" src="vistas/img/default/default.png" class="img-thumbnail previsualizarPortada" width="100%">

                            </div>
                            <!--=====================================
                            AGREGAR FOTO DE MULTIMEDIA 
                            ======================================-->
                            <div class="form-group">

                              <div class="panel">SUBIR FOTO PRINCIPAL DEL PRODUCTO</div>

                                <input type="file" class="fotoPrincipal">
                                <input type="hidden" class="antiguaFotoPrincipal">
                                <p class="help-block">Tamaño recomendado 400px * 450px <br> Peso máximo de la foto 2MB</p>

                                <img loading="lazy" src="vistas/img/default/default.png" class="img-thumbnail previsualizarPrincipal" width="200px">

                              </div>
                              <!--=====================================
                              AGREGAR PRECIO, PESO Y ENTREGA
                              ======================================-->
                              <div class="form-group row">
                                <!-- PRECIO -->
                                <div class="col-md-4 col-xs-12">

                                  <div class="panel">PRECIO</div>

                                    <div class="input-group">

                                      <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>
                                      <input type="number" class="form-control input-lg precio" min="0" step="any">

                                    </div>

                                  </div>
                                  <!-- PESO -->
                                  <div class="col-md-4 col-xs-12">

                                    <div class="panel">PESO</div>

                                      <div class="input-group"> 

                                        <span class="input-group-addon"><i class="fas fa-balance-scale"></i></span>

                                          <input type="number" class="form-control input-lg peso" min="0" step="any" value="0"> 

                                      </div>

                                    </div>
                                    <!-- ENTREGA -->
                                    <div class="col-md-4 col-xs-12">

                                      <div class="panel">DÍAS DE ENTREGA</div> 

                                        <div class="input-group">

                                          <span class="input-group-addon"><i class="fas fa-truck"></i></span>
                                          <input type="number" class="form-control input-lg entrega" min="0" value="0">

                                        </div>

                                      </div>

                                    </div>
                                    <!-- Disponibilidad -->
                                    <div class="col-md-5 col-xs-12">

                                      <br>

                                      <div class="panel">CANTIDAD DISPONIBLE</div>

                                      <div class="input-group">

                                        <span class="input-group-addon"><i class="fas fa-industry"></i></span>
                                        <input type="number" class="form-control input-lg disponibilidad" min="0" value="0">
                                        <span class="input-group-addon">
                                          <select class="medida">

                                            <option>PZAS</option>
                                            <option>GRS</option>
                                            <option>KG</option>
                                            <option>cuartillo</option>
                                            <option>Tapa</option>
                                            <option>Caja</option>
                                            <option>lister</option>
                                          
                                          </select>

                                        </span>

                                      </div>

                                    </div>
                                    <!--=====================================
                                    AGREGAR PROVEEDOR 
                                    ======================================-->
                                    <div class="col-md-4 col-xs-12">

                                      <br>
                                      <div class="panel">PROVEEDOR</div>

                                        <div class="input-group">

                                          <span class="input-group-addon"><i class="fas fa-building"></i></span>
                                          <input type="text" class="form-control input-lg Proveedor" id="Proveedor" placeholder="Proveedor">

                                        </div>
                                        </br></br>
                                      </div>
                                      <div class="col-md-4 col-xs12">
                                        
                                        <br>

                                        <div class="panel">Inversión</div>

                                          <div class="input-group">

                                            <span class="input-group-addon"><i class="fas fa-money"></i></span>
                                            <input type="number" class="form-control input-lg inversionEditada">

                                          </div>

                                        </div>
                                        <!--=====================================
                                        AGREGAR OFERTAS
                                        ======================================-->
                                        <div class="form-group">

                                          <select class="form-control input-lg selActivarOferta">

                                            <option value="">No tiene oferta</option> 
                                            <option value="oferta">Activar oferta</option>

                                          </select>

                                        </div>
                                        <div class="datosOferta" style="display:none">
                                        <!--=====================================
                                        VALOR OFERTAS
                                        ======================================-->
                                        <div class="form-group row">

                                          <div class="col-xs-6">

                                            <div class="input-group">

                                              <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>
                                              <input class="form-control input-lg valorOferta precioOferta" tipo="oferta" type="number" value="0" min="0" placeholder="Precio">

                                            </div> 

                                          </div>

                                        <div class="col-xs-6">

                                          <div class="input-group"> 

                                            <input class="form-control input-lg valorOferta descuentoOferta" tipo="descuento" type="number" value="0"  min="0" placeholder="Descuento">
                                            <span class="input-group-addon"><i class="fas fa-percent"></i></span>

                                          </div>

                                        </div> 

                                      </div> 
                                      <!--=====================================
                                      FECHA FINALIZACIÓN OFERTA
                                      ======================================-->
                                      <div class="form-group">

                                        <div class="input-group date">

                                          <input type='text' class="form-control datepicker input-lg valorOferta finOferta">
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                          </span>

                                        </div> 

                                      </div>
                                      <!--===================================== 
                                      FOTO OFERTA
                                      ======================================--> 
                                      <div class="form-group">

                                        <div class="panel">SUBIR FOTO OFERTA</div>

                                          <input type="file" class="fotoOferta valorOferta">
                                          <input type="hidden" class="antiguaFotoOferta">

                                          <p class="help-block">Tamaño recomendado 640px * 430px <br> Peso máximo de la foto 2MB</p>

                                          <img loading="lazy" src="vistas/img/ofertas/default/default.jpg" class="img-thumbnail previsualizarOferta" width="100px">

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

                                      <button type="button" class="btn btn-primary guardarCambiosProducto">Guardar cambios</button>
                                  
                                    </div>

                                  </div> 

                                </div>

                              </div>

                              <?php  

                              $eliminarProducto = new ControladorProductos();  
                              $eliminarProducto -> ctrEliminarProducto();