<?php
if($_SESSION["perfil"] != "administrador"  AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "tecnico"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;
}

?>

<div class="content-wrapper">

	<section class="content-header">

		<?php

 			$item = "id";
      		$valor = $_GET["idOrden"];

      		$ordenes = controladorOrdenes::ctrMostrarordenesParaValidar($item,$valor);

		?>

		<h2><center>Orden Numero: <?echo $_GET["idOrden"]?></center></h2>

		<ol class="breadcrumb">

      		<li><a href="#"><i class="fas fa-dashboard"></i> Inicio</a></li>
      		<li class="active">Orden <?echo $_GET["idOrden"]?></li>    

    	</ol>

	</section>

	<section class="content">
		
		<div class="row">
			
			<div class="col-lg-5 col-xs-12">
				
				<div class="box box-success">
					
					<div class="box-header with-border"></div>

					<div class="box-body">
						
						<div class="box">
								
							<!--=====================================
				            ENTRADA DATOS DEL CLIENTE
				            ======================================-->
				            <?php
							//TRAER CLIENTE (USUARIO)
							$item = "id";
						    $valor = $_GET["cliente"];
						    $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);
						    ?>
						    <div class="form-group">
						    	
						    	<div class="input-group">
						    		
						    		<span class="input-group-addon"><i class="fas fa-user"></i></span>
						    		
						    		<input type="text" class="form-control" value="<?php echo $usuario["nombre"] ?>" name="nombreCliente" readonly>

						    	</div>

						    </div>

						    <div class="form-group">
						    	
						    	<div class="input-group">
						    		
						    		<span class="input-group-addon"><i class="fas fa-envelope"></i></span> 
									
									<input type="text" class="form-control" name="correoCliente" value="<?php echo $usuario["correo"] ?>" readonly>	

						    	</div>

						    </div>

						    <div class="form-group">
						    	
						    	<div class="input-group">
						    		
						    		<span class="input-group-addon"><i class="fas fa-envelope"></i></span> 

						    		<input type="text" class="form-control" value="<?php echo $usuario["telefono"] ?>" readonly>

						    	</div>

						    </div>

						    <div class="form-group">
						    	
						    	<div class="input-group">
						    		
						    		<span class="input-group-addon"><i class="fas fa-envelope"></i></span> 

                    				<input type="text" class="form-control" value="<?php echo $usuario["telefonoDos"] ?>" readonly>

						    	</div>

						    </div>

						    <!--====================
							INFORMACION DE FECHAS DE INGRESO
                			====================-->
                			<div class="form-group">
                				
                				<div class="input-group">
                					 
                					 <span class="input-group-addon">Entrada</span>
                					 <?php
								 		
								 		$item = "id";
								      	$valor = $_GET["idOrden"];

								      	$ordenes = controladorOrdenes::ctrMostrarordenesParaValidar($item,$valor);
								      	
								      	foreach ($ordenes as $key => $valueOrdenesFecha) {
								      			
								      	}
								      	
								      	echo'<input type="text" class="form-control" value="'.$valueOrdenesFecha["fecha_ingreso"].'" readonly>';	

                						?>

                				</div>

                			</div>


						</div>

						<div class="form-group">
							
							<div class="input-group">
								
								<?
								
								echo'<span class="input-group-addon">Ultima modificacion</span>
									
									<input type="text" class="form-control" value="'.$valueOrdenesFecha["fecha"].'" readonly>';
								?>

							</div>

						</div>

						<?php

							if (isset($_GET["idOrden"])){
									
								echo '<a href="vistas/modulos/EnviarCorreoCliente.php?correo=correo&idOrden='.$_GET["idOrden"].'&empresa='.$_GET["empresa"].'&asesor='.$_GET["asesor"].'&cliente='.$_GET["cliente"].'&tecnico='.$_GET["tecnico"].'">

										<button class="btn btn-success" style="margin-top:5px"><i class="fas fa-envelope"></i> Informar estado de orden a cliente</button>
									</a>';
								}

							?>


					</div>

				</div>

			</div>

			<!--=====================================
		    LA TABLA DE PRODUCTOS
		    =======================================-->
		    <div class="col-lg-7">
		    	
		    	<div class="box box-warning">
		    		
		    		<div class="box-header with-border"></div>

		    		<div class="box-body">
		    			
		    			<?php

	 						$item = "id";
	      					$valor = $_GET["idOrden"];
			      			$ordenes = controladorOrdenes::ctrMostrarordenesParaValidar($item,$valor);

			      			foreach($ordenes as $key => $value) {
			      				
			      				$portada = $value["portada"];

				      			$AlbumDeImagenes = json_decode($value["multimedia"], true);

				      			$estado = $value["estado"];
				      			
				      			$partidaUno = $value["partidaUno"];

				      			$precioUno = $value["precioUno"];

				      			$partidaDos = $value["partidaDos"];

				      			$precioDos = $value["precioDos"];

				      			$partidaTres = $value["partidaTres"];

				      			$precioTres = $value["precioTres"];

				      			$partidaCuatro = $value["partidaCuatro"];

								$precioCuatro = $value["precioCuatro"];

								$partidaCinco = $value["partidaCinco"];

								$PrecioCinco = $value[19];

								$partidaSeis = $value["partidaSeis"];

								$precioSeis = $value["precioSeis"];

								$partidaSiete = $value["partidaSiete"];

								$precioSiete = $value["precioSiete"];

								$partidaOcho = $value["partidaOcho"];

								$precioOcho = $value["precioOcho"];

								$partidaNueve = $value["partidaNueve"];

								$precioNueve = $value["precioNueve"];

								$partidaDiez = $value["partidaDiez"];

								$precioDiez = $value["precioDiez"];

								$partidas = json_decode($value["partidas"], true);	

								$observaciones = json_decode($value["observaciones"], true);

								$inversiones = json_decode($value["inversiones"], true);

								$descripcion = $value["descripcion"];

								$fecha_Salida = $value["fecha_Salida"];

								$fecha_ingreso = $value["fecha_ingreso"];

								$recarga = $value["recargaCartucho"];

								$precioRecarga = $value["totalRecargaDeCartucho"];

								$partidasTecnicoDos = json_decode($value["partidasTecnicoDos"], true);

								$totalTecnicosDos = $value["TotalTecnicoDos"];

								$tecnicoDos = $value["id_tecnicoDos"];
			      			}

	      				?>

	      				<table class="table table-bordered table-striped dt-responsive">

	      					<thead>
	      						
	      						<tr>
	      							
	      							<th>Principal</th>
	      							<th>Multimedia</th>

	      							<?php

                   						date_default_timezone_set("America/Mexico_City");

	               						$fecha = date("Y-m-d H:i:s",strtotime($fecha_ingreso."+ 5 days"));

					                    if ($fecha >= $fecha_ingreso) {

                  							echo'<th>Entraga</th>';
                   						}

                  					?>

	      						</tr>

	      					</thead>

	      					<tbody>
	      						
	      						<tr>
	      							
	      							<?php

	      								foreach ($AlbumDeImagenes as $key => $valueImagenes) {
	      									
	      									echo'<td><img loading="lazy" src="'.$valueImagenes["foto"].'" class="img-thumbnail" width="130px">';
	      								}

	      									echo'<img loading="lazy" src="'.$portada.'" class="img-thumbnail" width="130px"></td>';

	      							
	      								date_default_timezone_set("America/Mexico_City");

	                  					$fecha = date("Y-m-d H:i:s",strtotime($fecha_ingreso."+ 5 days"));

	                  					if ($fecha >= $fecha_ingreso) {
	                  	
						                  	echo'<td style="background:#EB9A93;">
						                  		<h3>Atraso en servicio</h3>
						                  	</td>';

						                }

	      							?>

	      						</tr>

	      					</tbody>

	      				</table>

		    		</div>

		    	</div>

		    </div>

		    <!--=====================================
			ENTRADA DATOS TECNICO Y ASESOR
			======================================-->

			<div class="col-lg-12">
				
				<div class="box box-danger">
					
					<div class="box-header with-border"></div>

					<form role="form" method="post" class="formularioPartidas">
						
						<div class="box-body">
							
							<div class="box">
								<!--=====================================
	                			ASESOR
	                			======================================-->
	                			<?php
                     				$item = "id";
				      				$valor = $_GET["asesor"];
                      				$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);
	                			?>

	                			<div class="form-group">
	                				
	                				<span><b><h3>Asesor</h3></b></span>

	                				<div class="input-group">
	                					
	                					<span class="input-group-addon"><i class="fas fa-user"></i></span>

	                					<select class="form-control input-lg selector" name="asesorEditadoEnOrdenDianmica" required>
	                						
	                						<option value="<?php echo $asesor["id"] ?>"><?php echo $asesor["nombre"] ?></option>

	                						<?php 

											  $item = "id";
						                      $valor = $_GET["asesor"];
                     						  $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);

                     						  $itemUno = null;
						                      $valorDos = null;
                     						  $asesorParaSelect = Controladorasesores::ctrMostrarAsesoresEleg($itemUno,$valorDos);

                     						  foreach ($asesorParaSelect as $key => $valueAasesores){

                        						echo '<option value="'.$valueAasesores["id"].'">'.$valueAasesores["nombre"].'</option>';

                      						  }

                     						?>

	                					</select>

	                				</div>

	                			</div>

	                			<!--=====================================
	                			ENTRADA PARA EL TECNICO
	                			======================================--> 
	                			<?
	                				$item = "id";
				      				$valor = $_GET["tecnico"];
                      				$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);

	                			?>

	                			<div class="form-group">
	                				
	                				<span><b><h3>Técnico</h3></b></span>

	                				<div class="input-group">
	                					
	                					<span class="input-group-addon"><i class="fas fa-cogs"></i></span> 

	                					<select class="form-control input-lg selector" name="tecnicoEditadoEnOrdenDianmica" required>

	                						<option value="<?php echo $tecnico["id"] ?>"><?php echo $tecnico["nombre"] ?></option>

	                						<?php 

						                      $item = null;
						                      $valor = null;
                     						  $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);

                     						  foreach ($tecnico as $key => $valuetecnico) {

                        						echo '<option value="'.$valuetecnico["id"].'">'.$valuetecnico["nombre"].'</option>';

                      						  }


                     						?>

	                					</select>

	                				</div>

	                			</div>

	                			<!--=====================================
	                			BOTON AGREGAR NUEVO TECNICO
	                			======================================--> 

	                			<button type="button" class="btn pull-right btn-success btn-md agregarNuevoTecnicoAorden"><i class="fas fa-wrench"></i>  Agregar Técnico</button>

	                			<br><br>

	                			<div class="input-group selecnuevoTec" style="display:none;">
	                				
	                				<span class="input-group-addon"><i class="fas fa-cogs"></i></span> 

	                				<select class="form-control input-lg selector" name="NuevoTecnicoEnOrden" required>

	                					<?php 

						                $item = null;

						                $valor = null;

                     					$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);

                     					foreach ($tecnico as $key => $valuetecnico) {

                        					echo '<option value="'.$valuetecnico["id"].'">'.$valuetecnico["nombre"].'</option>';

                      					}

										?>

	                				</select>

	                			</div>

							</div>

							<!--===================================
	                		VISTA TECNICO DOS
	                		======================================-->
	                		<?php

	                			$itemTec = "id";
			      				$valorTec = $tecnicoDos;

                      			$tecnico = ControladorTecnicos::ctrMostrarTecnicos($itemTec,$valorTec);

                      			if ($tecnicoDos != 0 || $tecnicoDos != null) {
                      				
                      				echo '<div class="input-group selecnuevoTec">

                      					 <span class="input-group-addon"><i class="fas fa-cogs"></i></span> 

                      					 <select class="form-control input-lg selector" name="NuevoTecnicoEnOrden" required>

                      					 <option value='.$tecnico["id"].'>'.$tecnico["nombre"].'</option>';
 											
 											$item = null;
						                    $valor = null;

                     						$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);

                     						foreach ($tecnico as $key => $valuetecnico) {

                        						echo '<option value="'.$valuetecnico["id"].'">'.$valuetecnico["nombre"].'</option>';


                      						  }

                      						echo' </select>

			                  				</div>

                      				</div>';
                      			}
	                		?>

	                		<!--=====================================
	                		ENTRADA PARA EL ESTADO DE ORDEN 
	                		======================================--> 

	                		<div class="form-group">

	                			<div class="input-group">

	                				<?php
	                				
	                				utf8_encode(utf8_decode($EstadoRevision = "En revisión (REV)"));

									utf8_encode(utf8_decode($EstadoAUT = "Pendiente de autorización (AUT)"));

									utf8_encode(utf8_decode($EstadoSUP= "Supervisión (SUP)"));

									if ($estado !== "Entregado (Ent)") {
									 		
									 	if ($_SESSION["perfil"] == "tecnico") {
									 		
									 		echo'<span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>
                                    
                                    	    <select class="form-control input-lg selector" name="estado">
                                    														
                                    		<option>'.$estado.'</option>';

                                    		if ($estado == $EstadoRevision) {
                                    			
                                    			echo '<option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option>';

                                    		}else if ($estado == "Aceptado (ok)") {
                                    			
                                    			echo'<option class="ter" value="Terminada (ter)">Terminada (ter)</option>
                                    			<option class="can" value="Cancelada (can)">Cancelada (can)</option>
                                    			<option value="Sin reparación (SR)">Sin reparación (SR)</option>';
                                    		}
									 	}
									 	echo'</select>';
								
										if($_SESSION["perfil"] == "editor") {
											
										   echo'<span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>
	                                    
	                                    	<select class="form-control input-lg selector" name="estado">
	                                    														
	                                    	<option>'.$estado.'</option>';	

	                                    	if($estado == $EstadoRevision) {
	                                    		
	                                    		echo '<option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option>';

	                                    	}else if ($estado == $EstadoSUP) {
	                                    		
	                                    		echo'<option class="aut" value="Pendiente de autorizaci&oacute;n (AUT)">Pendiente de autorizaci&oacute;n (AUT)</option>';

	                                    	}else if($estado == $EstadoAUT){

	                                    		echo'<option class="ok" value="Aceptado (ok)">Aceptado (ok)</option>';

	                                    	}else if ($estado == "Aceptado (ok)") {
	                                    		
	                                    		echo'<option class="ter" value="Terminada (ter)">Terminada (ter)</option>
	                                    			<option class="can" value="Cancelada (can)">Cancelada (can)</option>';
	                                    	
	                                    	}else if ($estado == "Cancelada (can)") {
	                                    		
	                                    		echo'<option class="can" value="Cancelada (can)">Cancelada (can)</option>';

	                                    	}

	                                    	echo'</select>';
										}
										if($_SESSION["perfil"] == "vendedor"){
											
											echo'<span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>
	                                    
	                                    	<select class="form-control input-lg selector" name="estado">
	                                    														
	                                    	<option>'.$estado.'</option>';

	                                    	if ($estado == $EstadoRevision) {
	                                    		
	                                    		echo '<option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option>';

	                                    	}else if ($estado == $EstadoSUP) {
	                                    		
	                                    		echo'<option class="aut" value="Pendiente de autorizaci&oacute;n (AUT)">Pendiente de autorizaci&oacute;n (AUT)</option>';

	                                    	}else if ($estado == $EstadoAUT) {
	                                    		
	                                    		echo'<option class="ok" value="Aceptado (ok)">Aceptado (ok)</option>';

	                                    	}else if ($estado == "Aceptado (ok)") {
	                                    		
	                                    		echo'<option class="ter" value="Terminada (ter)">Terminada (ter)</option>';
	                                    	}else if ($estado == "Cancelada (can)") {
	                                    		
	                                    		echo'<option class="can" value="Cancelada (can)">Cancelada (can)</option>';
	                                    	}
										}

										if ($_SESSION["perfil"] == "administrador") {
											
											echo'<span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>
	                                    
	                                    	<select class="form-control input-lg selector" name="estado">
	                                    														
	                                    		<option>'.$estado.'</option>
	                                    				
	                                    		<option class="sup" value="Supervisi&oacute;n (SUP)">Supervisi&oacute;n (SUP)</option>
	                                    				
	                                    		<option class="aut" value="Pendiente de autorizaci&oacute;n (AUT)">Pendiente de autorizaci&oacute;n (AUT)</option>
	                                    				
	                                    		<option class="ok" value="Aceptado (ok)">Aceptado (ok)</option>
	                                    
	                                    		<option class="ter" value="Terminada (ter)">Terminada (ter)</option>
	                                    
	                                    		<option class="can" value="Cancelada (can)">Cancelada (can)</option>

	                                    		<option value="Sin reparación (SR)">Sin reparación (SR)</option>
	                                    				
	                                    		<option class="ent" value="Entregado (Ent)">Entregado (Ent)</option>
	                                    
	                                    	</select>';
										}

									}else{

										echo'<input type="hidden" name="estado" value="Entregado (Ent)">
                                    	<center><span><b><h1>ENTREGADO EL: '.$fecha_Salida.'</h1></b></span></center>';
									}

									?>

	                			</div>

	                		</div>
							<!--=====================================
               				ENTRADA PARA VISUALIZAR PRIMERA PARTIDA                	======================================--> 
               				<?php
               				if ($partidaUno != null) {
               					
               					if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico"){
               						
               						echo'<div class="form-group row">

               							<div class="col-xs-6">

               								<div class="input-group">

               									<span class="input-group-addon"><i class="fas fa-pencil"></i></span>

               									<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaUno" readonly>'.$partidaUno.'</textarea>

               								</div>

               							</div>

               							<div class="col-xs-6">

               								<div class="input-group">

               									<input class="form-control input-lg precioPartidaGuardada" name="precioUno" type="number" value="'.$precioUno.'" readonly>
               									<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

               								</div>

               							</div>

               						</div>';

               					}else{

	               					echo '<div class="form-group row">

	               						<div class="col-xs-6">

	               							<div class="input-group"> 

	               								<span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>

	               								<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaUno">'.$partidaUno.'</textarea> 
	               							
	               							</div>

	               						</div>
	               						<div class="col-xs-6">

	               							<div class="input-group">

	               								<input class="form-control input-lg precioPartidaGuardada" name="precioUno" type="number" value="'.$precioUno.'">

	               								<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

	               							</div>

	               						</div>

	               					</div>';
	               				}
               				}

               				echo'<!--=====================================
               				ENTRADA PARA VISUALIZAR SEGUNDA PARTIDA
               				======================================-->';
               				if ($partidaDos != null) {
               					
               					if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico") {
               						
               						echo '<div class="form-group row">

               							<div class="col-xs-6">

               								<div class="input-group"> 

               									<span class="input-group-addon"><i class="fas fa-pencil"></i></span>

               									<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaDos" readonly>'.$partidaDos.'</textarea> 

               								</div>

               							</div>
               							<div class="col-xs-6">

               								<div class="input-group">

               									<input class="form-control input-lg precioPartidaGuardada" name="precioDos" type="number" value="'.$precioDos.'" readonly>

               									<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

               								</div>

               							</div>

               						</div>';
               					}else{

               						echo '<div class="form-group row">

               							<div class="col-xs-6">

               								<div class="input-group">

               									<span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>

               									 <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaDos">'.$partidaDos.'</textarea> 

               								</div>

               							</div>
               							<div class="col-xs-6">

               								<div class="input-group">

               									<input class="form-control input-lg precioPartidaGuardada" name="precioDos" type="number" value="'.$precioDos.'">
               									<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

               								</div>

               							</div>

               						</div>';
               					}
               				}

               				echo '<!--=====================================
                			ENTRADA PARA VISUALIZAR TERCERA PARTIDA
							======================================-->';
							if ($partidaTres != null) {
								
								if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico"){
									
									echo '<div class="form-group row">

										<div class="col-xs-6">

											<div class="input-group"> 

											 <span class="input-group-addon"><i class="fas fa-pencil"></i></span>
											  <textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaTres" readonly>'.$partidaTres.'</textarea> 

											</div>

										</div>
										<div class="col-xs-6">

											<div class="input-group">

												<input class="form-control input-lg precioPartidaGuardada" name="precioTres" type="number" value="'.$precioTres.'" readonly>
												<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

											</div>

										</dvi>

									</div>';

								}else{

									echo '<div class="form-group row">

										<div class="col-xs-6">

											<div class="input-group">

												<span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>
												<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaTres">'.$partidaTres.'</textarea>

											</div>

										</div>
										<div class="col-xs-6">

											<div class="input-group">

												<input class="form-control input-lg precioPartidaGuardada" name="precioTres" type="number" value="'.$precioTres.'">
												<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

											</div>

										</div>

									</div>';

								}
							}

							echo'<!--=====================================
               				ENTRADA PARA VISUALIZAR CUARTA PARTIDA
               				=============================================-->';
               				if ($partidaCuatro != null){
               				 	
               				 	if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico") {
               				 		
               				 		echo '<div class="form-group row">

               				 			<div class="col-xs-6">

               				 				<div class="input-group"> 

               				 					<span class="input-group-addon"><i class="fas fa-pencil"></i></span>
               				 					<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaCuatro" readonly>'.$partidaCuatro.'</textarea>

               				 				</div>

               				 			</div>
               				 			<div class="col-xs-6">

               				 				<div class="input-group">

               				 					<input class="form-control input-lg precioPartidaGuardada" name="precioCuatro" type="number" value="'.$precioCuatro.'" readonly>
               				 					<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

               				 				</div>

               				 			</div>

               				 		</div>';
               				 	}else{

               				 		echo '<div class="form-group row">

               				 			<div class="col-xs-6">

               				 				<div class="input-group"> 

               				 					<span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>
               				 					<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaCuatro">'.$partidaCuatro.'</textarea> 

               				 				</div>

               				 			</div>
               				 			<div class="col-xs-6">

               				 				<div class="input-group">

               				 					<input class="form-control input-lg precioPartidaGuardada" name="precioCuatro" type="number" value="'.$precioCuatro.'">
               				 					<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

               				 				</div>

               				 			</div>

               				 		</div>';
               				 	}
               				 } 

               				 echo'<!--=====================================
                			 ENTRADA PARA VISUALIZAR QUINTA PARTIDA
                			 ======================================-->';
                			 if ($partidaCinco != null) {
                			 	
                			 	if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico"){

                			 		echo '<div class="form-group row">

                			 			<div class="col-xs-6">

                			 				<div class="input-group">

                			 					<div class="input-group">

                			 						<span class="input-group-addon"><i class="fas fa-pencil"></i></span>
                			 						<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaCinco" readonly>'.$partidaCinco.'</textarea> 

                			 					</div>

                			 				</div>

                			 			</div>
                			 			<div class="col-xs-6">

                			 				<div class="input-group">

                			 					<input class="form-control input-lg precioPartidaGuardada" name="precioCinco" type="number" value="'.$PrecioCinco.'" readonly>
                			 					<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

                			 				</div>

                			 			</div>

                			 		</div>';
                			 	}else{

                			 		echo '<div class="form-group row">

                			 			<div class="col-xs-6">

                			 				<div class="input-group"> 

                			 					<span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>
                			 					<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase" name="partidaCinco">'.$partidaCinco.'</textarea>
                			 				</div>

                			 			</div>
                			 			<div class="col-xs-6">

                			 				<div class="input-group">

                			 					<input class="form-control input-lg precioPartidaGuardada" name="precioCinco" type="number" value="'.$PrecioCinco.'">
                			 					<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

                			 				</div>

                			 			</div>

                			 		</div>';
                			 	}
                			 }
                			 echo'<!--================================
                			 ENTRADA PARA MOSTRAR PARTIDAS JSON
                			 ======================================-->';
                			 if (is_array($partidas) || is_object($partidas)) {
                			 	
                			 	foreach ($partidas as $key => $itemDetallesPartidas){
                			 		
                			 		$itemPartida = "descripcion";
          							$descripcioPartida = $itemDetallesPartidas["descripcion"];
          							$itemPartida = "precioPartida";

          							$valorProducto = $itemDetallesPartidas["precioPartida"];

          							if ($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico"){
          								
          								echo '<div class="form-group row">

          									<div class="col-xs-6">

          										<div class="input-group">

          											<span class="input-group-addon"><i class="fas fa-pencil"></i></span>
          											<textarea type="text" maxlength="320" rows="3" class="form-control  input-lg text-uppercase NuevaPartidaAgregada" readonly>'.$itemDetallesPartidas["descripcion"].'</textarea>

          										</div>

          									</div>
          									<div class="col-xs-6">

          										<div class="input-group">

          											<input class="form-control input-lg precioPartidaGuardada precioPartidaListada" type="number" value="'.$itemDetallesPartidas["precioPartida"].'" readonly>

          											<input type="hidden"  name="partidaYaListada">
          											<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

          										</div>

          									</div>

          								</div>';
          							}else if ($_SESSION["perfil"] == "administrador"){
       									
       									echo'<div class="form-group row">

       										<div class="col-xs-6">

       											<div class="input-group"> 

       												<span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>
       												<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase NuevaPartidaAgregada">'.$itemDetallesPartidas["descripcion"].'</textarea>

       											</div>

       										</div>
       										<div class="col-xs-6">

       											<div class="input-group">

       												<input class="form-control input-lg precioPartidaGuardada precioPartidaListada" type="number" value="'.$itemDetallesPartidas["precioPartida"].'">
       												<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

       											</div>

       										</div>

       									</div>';
          							}

                			 	}
                			 }
                			 ?>
                			 <!--================================
                			 ENTRADA PARA AGREGAR PARTIDA
                			 ======================================-->
                			 <div class="NuevaPartida">
							 </div>
							 <!--=======================================
							 INPUT OCULTO PARA LISTAR LAS PARTIDAS NUEVAS
							 =========================================-->
							 <input type="hidden" id="listatOrdenesNuevas" name="listatOrdenesNuevas">
							 <!--=======================================
							 INPUT OCULTO PARA LISTAR TODAS LAS PARTIDAS
							 =========================================-->
							 <input type="hidden" id="listatOrdenes" name="listatOrdenes">
							  <!--=======================================
							 INPUT OCULTO MANDAR DATOS DEL CLIENTE
							 =========================================-->
							 <input type="hidden" class="form-control" value="<?php echo $usuario["nombre"] ?>" name="nombreCliente" readonly>
							 <input type="hidden" class="form-control" value="<?php echo $usuario["correo"] ?>" name="correoCliente" readonly >
							 <input type="hidden" value="<?php echo $fecha_ingreso ?>" name="fecha_ingreso">
							<input type="hidden" value= "<?php echo $valor = $_GET["idOrden"]; ?>" name="idOrden">

							<?php
                			 echo'<!--=====================================
                			 MOSTRAR RECARGA DE CARTUCHO
               				 ======================================-->';
               				 if ($recarga != "") {
               				 	
               				 	echo '<div class="form-group row">

               				 		<div class="col-xs-6">

               				 			<div class="input-group">

               				 				<span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>
               				 				<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase  NuevaRecargaAgregada">'.$recarga.'</textarea>

               				 			</div>

               				 		</div>
               				 		<div class="col-xs-6">

               				 			<div class="input-group">

               				 				<input class="form-control input-lg nuevoPrecioOrden precioPartidaGuardada preciodeRecarganueva"  type="number" value="'.$precioRecarga.'">
               				 				<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

               				 			</div>

               				 		</div>

               				 	</div>';
               				 }
               				 echo'<!--=============================
							 MOSTRAR PARTIDAS TECNICO DOS
							 =================================-->';
							 if ($partidasTecnicoDos != "") {
							 	
							 	foreach ($partidasTecnicoDos as $key => $valuePartidasTecnivosDos){
							 		
							 		echo '<div class="form-group row">

							 			<div class="col-xs-6">

							 				<div class="input-group">

							 					<span class="input-group-addon"><button type="button" class="btn btn-danger quitarPartida" btn-lg><i class="fas fa-times"></i></button></span>
							 					<textarea type="text" maxlength="320" rows="3" class="form-control input-lg text-uppercase  NuevaRecargaAgregada">'.$valuePartidasTecnivosDos["descripcion"].'</textarea>

							 				</div>

							 			</div>
							 			<div class="col-xs-6">

							 				<div class="input-group">

							 					<input class="form-control input-lg nuevoPrecioOrden precioPartidaGuardada preciodeRecarganueva"  type="number" value="'.$valuePartidasTecnivosDos["precioPartida"].'">
							 					<span class="input-group-addon" ><i class="fas fa-dollar"></i></span>

							 				</div>

							 			</div>

							 		</div>

							 		<!--=====================================
                					ENTRADA PARA INVERSIONES EN JSON
                					======================================-->';
                					if (is_array($inversiones) || is_object($inversiones)) {
                						
                						foreach ($inversiones as $key => $valueinversiones){
                							
                							if ($_SESSION["perfil"] =="administrador"){
                								
                								echo'<div class="form-group row">

                									<div class="col-xs-6">

                										<div class="input-group">

                											<span class="input-group-addon"><button type="button" class="btn btn-danger quitarInversion" btn-lg><i class="fas fa-times"></i></button></span>
                											<span class="input-group-addon">Detalle</span>
                											<input type="text" class="form-control input-lg detalleInversion" value="'.$valueinversiones["observacion"].'">

                										</div>

                									</div>
                									<div class="col-xs-6">

                										<div class="input-group">

                											<span class="input-group-addon">Inversion</span>
                											<span class="input-group-addon"><i class="fas fa-dollar"></i></span>
                											<input type="number" class="form-control input-lg precioNuevainversion" value="'.$valueinversiones["invsersion"].'">

                										</div>

                									</div>

                								</div>';
                							}
                						}
                					}
							 	}
							 }

               				?>
               				<!--========================
               				ENTRADA PARA MOSTRAR TOTAL
               				==========================-->
               				<div class="form-group">

               					<h4><b>TOTAL:</b></h4>

               					<div class="input-group">

               						<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>

               						<input type="number" class="form-control input-lg" id="costoTotalDeOrden" name="costoTotalDeOrden" readonly>

               					</div>

               				</div>
               				<!--=====================================
               				ENTRADA PARA MOSTRAR TOTAL DE TODAS LAS 
               				INVERSIONES EN LA ORDEN                				======================================-->
               				<div class="form-group">

               					<h4><b>Inversion</b></h4>

               					<div class="input-group">

               						<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>

               						<?php

                						if ($_SESSION["perfil"] =="administrador") {
                							echo'<input type="number" name="totalInversiones" class="form-control input-lg" id="costoTotalInversiones" readonly>';
                						}

                					?>

               					</div>

               				</div>

               				<button type="button" class="btn btn-primary AgregarCamposDePartida">Agregar Nueva Partida</button>

						</div>

						<?php
						$objeto = new controladorOrdenes();
						$objeto -> ctrEditarOrdenDinamica();

						?>

					</form>

				</div>

			</div>

		</div>


	</section>

</div>


<div class="content-wrapper">

	<div class="col-lg-12">
				    	
		<div class="box box-danger">
				    		
			<div class="box-header with-border"></div>

				<div class="box-body">

					<section class="content">

						<div class="box">
							
							<div class="box-header with-border">

								<form role="form" method="post" class="formularioObervaciones">

									<div class="box-body">

										<div class="box">

											<div class="form-group">

												<div class="input-group">

													<span class="input-group-addon"><i class="fas fa-pencil"></i></span>
													<?php

								 					echo '<textarea type="text"  class="form-control input-lg" style="text-alinging:right; font-weight: bold;" name="observaciones">'.$descripcion.'</textarea>

														<input type="hidden" value="'.$_GET["idOrden"].'" name="idOrden">';
														//OBBSERVACIONES DE LA TABLA observacionesOrdenes

								 					?>

												</div>

											</div>
											<div class="form-group">

												<?php

												$item = "id_orden";
												$valor = $_GET["idOrden"];

												$observacionespormediodeId =controladorObservaciones::ctrMostrarobservaciones($item,$valor);

												$item2 = "id";
												$valor2 = $_SESSION["id"];

												$creadorObservacion = ControladorAdministradores::ctrMostrarAdministradores($item2,$valor2);

												//TRAER EL CREADOR DE LA ORDEN
												foreach($observacionespormediodeId as $key => $valueObservacionesId){
													
													echo '<span class="input-group-addon">'.$creadorObservacion["nombre"].' '.$valueObservacionesId["fecha"].'</span>

													<div class="input-group">

														<span class="input-group-addon"><i class="fas fa-pencil"></i></span>

														<textarea type="text"  class="form-control input-lg"  style="text-alinging:right; font-weight: bold;" readonly name="obsrvacionConId">'.$valueObservacionesId["observacion"].'</textarea>
														<input type="hidden" value="'.$_GET["idOrden"].'" name="idOrdenId">

													</div>';
												}

												?>

											</div>
											<!--================================
											OBSERVACIONES EN JSON
											=================================-->
											<?php

												if (is_array($observaciones) || is_object($observaciones)) {
													
													foreach($observaciones as $key => $valueObservaciones){
														
														if($_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico"){
															
															echo'<div class="form-group">

																<span class="input-group-addon">'.$valueObservaciones["creador"].' '.$valueObservaciones["fecha"].'</span>
																
																<div class="input-group">

																	<span class="input-group-addon"><i class="fas fa-pencil"></i></span>

																	<textarea type="text"  class="form-control input-lg nuevaObservacion"  style="text-alinging:right; font-weight: bold;"readonly>'.$valueObservaciones["observacion"].'</textarea>

																</div>

															</div>';
														}else{

															echo '<div class="form-group">

																<span class="input-group-addon">'.$valueObservaciones["creador"].' '.$valueObservaciones["fecha"].'</span>

																<div class="input-group">

																	<span class="input-group-addon"><button type="button" class="btn btn-danger quitarObservacion" btn-lg><i class="fas fa-times"></i></button></span>

																	<textarea type="text"  class="form-control input-lg nuevaObservacion"  style="text-alinging:right; font-weight: bold;">'.$valueObservaciones["observacion"].'</textarea>

																</div>
															</div>';
														}
													}
												}

											?>

											<div class="NuevaObservacion">
											</div>
											<?php
											echo'<input type="hidden" class="usuarioQueCaptura" value="'.$_SESSION["nombre"].'" name="usuarioQueCaptura">';
											?>
											<textarea type="hide"  class="form-control input-lg" id="fechaVista" style="display:none;"></textarea>

											<input type="hidden" id="listarObservaciones" name="listarObservaciones">

											<input type="hidden" name="listarinversiones" id="listarinversiones">

										</div>

									</div>
									<button type="button" class="btn btn-primary AgregarCampoDeObservacion">Agregar Nueva Observación </button>
									<div class="box-footer">
						
										<button type="submit" class="btn btn-primary pull-right">Guardar</button>
					
									</div>
									<?php

									$observacionesDePartidas = new controladorOrdenes();
									$observacionesDePartidas -> ctrEditarObservacionesYaExistentes();

									$objetoUno = new controladorOrdenes();
									$objetoUno -> ctrEditarOrdenDinamica();

									$objetoDos = new controladorOrdenes();
									$objetoDos -> ctrEditarInversiones();

									$editarEstadoOrden = new ControladorPedidos();
									$editarEstadoOrden -> ctrEditarPedidoEnEstado();

									//$ingresarobservaciones = new controladorObservaciones();
									//$ingresarobservaciones -> ctrlCrearObservacion();
									$ingresarTipoDeOrden = new controladorOrdenes();
									$ingresarTipoDeOrden -> ctrlAgregarTipoDeOrden();
									$ingresarRecarga = new controladorOrdenes();
									$ingresarRecarga -> ctrlAgregarRecargaDeCartucho();

									$ingresarPartidasTecnicoDos = new controladorOrdenes();
									$ingresarPartidasTecnicoDos -> ctrlAgregarNuevaPartidaTecnicoDos();

									$ingresarTecnicoDos = new controladorOrdenes();
									$ingresarTecnicoDos -> ctrlAgregarTecnicoDos();
									?>

								</form>

							</div>
							
						</div>

					</section>

				</div>
		</div>

	</div>

</div>		