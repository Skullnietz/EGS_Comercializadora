<!-- =============================================== -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    
<!--=====================================
SECCIÓN PERFIL
======================================-->

<div class="container-fluid">

	<div class="container">

		<ul class="nav nav-tabs">
		  
	  		<li class="active">	  			
			  	<a data-toggle="tab" href="#compras">
			  	<i class="fas fa-list-ul"></i>COMPRAS DE USUARIO</a>
	  		</li>

	  		<li> 				
		  		<a data-toggle="tab" href="#deseos">
		  		<i class="fas fa-gift"></i>LISTA DE DESEOS DE CLIENTE </a>
	  		</li>

	  		<li>				
	  			<a data-toggle="tab" href="#perfil">
	  			<i class="fas fa-user"></i> DATOS CLIENTE</a>
	  		</li>

	  		<li>				
		 	 	<a href="<?php echo $url; ?>ofertas">
		 	 	<i class="fas fa-star"></i>CONTACTAR CLIENTE</a>
	  		</li>
		
		</ul>
		
		
		<div class="tab-content">

			<!--=====================================
			PESTAÑA COMPRAS
			======================================-->

	  		<div id="compras" class="tab-pane fade in active">
		    
				<div class="panel-group">
			<?php

					$item = "id_usuario";
					$valor = $_SESSION["id"];
					
					$compras = ControladorUsuarios::ctrMostrarCompras($item, $valor);
					
					if(!$compras){

						echo '<div class="col-xs-12 text-center error404">
				               
				    		<h1><small>¡Oops!</small></h1>
				    
				    		<h2>ESTE USUARIO AUN NO REALIZA COMPRAS</h2>

				  		</div>';

					}else{
					
				foreach ($compras as $key => $value1) {

							$ordenar = "id";
							$item = "id";
							$valor = $value1["id_producto"];

							$productos = ControladorProductos::ctrListarProductos($ordenar, $item, $valor);

							foreach ($productos as $key => $value2) {
							
								echo '<div class="panel panel-default">
									    
									    <div class="panel-body">

											<div class="col-md-2 col-sm-6 col-xs-12">

												<figure>
												
													<img loading="lazy" class="img-thumbnail" src="'.$servidor.$value2["portada"].'">

												</figure>

											</div>

											<div class="col-sm-6 col-xs-12">

												<h1><small>'.$value2["titulo"].'</small></h1>

												<p>'.$value2["titular"].'</p>';

												if($value2["tipo"] == "virtual"){

													echo '<a href="'.$url.'curso/'.$value1["id"].'/'.$value1["id_usuario"].'/'.$value1["id_producto"].'/'.$value2["ruta"].'">
														<button class="btn btn-default pull-left">Ir al curso</button>
														</a>';

												}else{

													echo '<h6>Proceso de entrega: '.$value2["entrega"].' días hábiles</h6>';

													if($value1["envio"] == 0){

														echo '<div class="progress">

															<div class="progress-bar progress-bar-info" role="progressbar" style="width:33.33%">
																<i class="fas fa-check"></i> Despachado
															</div>

															<div class="progress-bar progress-bar-default" role="progressbar" style="width:33.33%">
																<i class="fas fa-clock-o"></i> Enviando
															</div>

															<div class="progress-bar progress-bar-success" role="progressbar" style="width:33.33%">
																<i class="fas fa-clock-o"></i> Entregado
															</div>

														</div>';

													}

													if($value1["envio"] == 1){

														echo '<div class="progress">

															<div class="progress-bar progress-bar-info" role="progressbar" style="width:33.33%">
																<i class="fas fa-check"></i> Despachado
															</div>

															<div class="progress-bar progress-bar-default" role="progressbar" style="width:33.33%">
																<i class="fas fa-check"></i> Enviando
															</div>

															<div class="progress-bar progress-bar-success" role="progressbar" style="width:33.33%">
																<i class="fas fa-clock-o"></i> Entregado
															</div>

														</div>';

													}

													if($value1["envio"] == 2){

														echo '<div class="progress">

															<div class="progress-bar progress-bar-info" role="progressbar" style="width:33.33%">
																<i class="fas fa-check"></i> Despachado
															</div>

															<div class="progress-bar progress-bar-default" role="progressbar" style="width:33.33%">
																<i class="fas fa-check"></i> Enviando
															</div>

															<div class="progress-bar progress-bar-success" role="progressbar" style="width:33.33%">
																<i class="fas fa-check"></i> Entregado
															</div>

														</div>';

													}

												}

												echo '<h4 class="pull-right"><small>Comprado el '.substr($value1["fecha"],0,-8).'</small></h4>
																
											</div>

																<div class="col-md-4 col-xs-12">';

											$datos = array("idUsuario"=>$_SESSION["id"],
															"idProducto"=>$value2["id"] );

											$comentarios = ControladorUsuarios::ctrMostrarComentariosPerfil($datos);

												echo '<div class="pull-right">

													<a class="calificarProducto" href="#modalComentarios" data-toggle="modal" idComentario="'.$comentarios["id"].'">
													
														<button class="btn btn-default backColor">Calificar Producto</button>

													</a>

												</div>

												<br><br>

												<div class="pull-right">

													<h3 class="text-right">';

													if($comentarios["calificacion"] == 0 && $comentarios["comentario"] == ""){

														echo '<i class="fas fa-star-o text-success" aria-hidden="true"></i>
																<i class="fas fa-star-o text-success" aria-hidden="true"></i>
																<i class="fas fa-star-o text-success" aria-hidden="true"></i>
																<i class="fas fa-star-o text-success" aria-hidden="true"></i>
																<i class="fas fa-star-o text-success" aria-hidden="true"></i>';

													}else{

														switch($comentarios["calificacion"]){

															case 0.5:
															echo '<i class="fas fa-star-half-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>';
															break;

															case 1.0:
															echo '<i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>';
															break;

															case 1.5:
															echo '<i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-half-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>';
															break;

															case 2.0:
															echo '<i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>';
															break;

															case 2.5:
															echo '<i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-half-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>';
															break;

															case 3.0:
															echo '<i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>';
															break;

															case 3.5:
															echo '<i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-half-o text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>';
															break;

															case 4.0:
															echo '<i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-o text-success" aria-hidden="true"></i>';
															break;

															case 4.5:
															echo '<i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star-half-o text-success" aria-hidden="true"></i>';
															break;

															case 5.0:
															echo '<i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>
																  <i class="fas fa-star text-success" aria-hidden="true"></i>';
															break;

														}


													}
												
														
													echo '</h3>

													<p class="panel panel-default text-right" style="padding:5px">

														<small>

														'.$comentarios["comentario"].'

														</small>
													
													</p>

												</div>

											</div>

									    </div>

									</div>';

							}
							
						}
					}
				?>
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->