<div class="content-wrapper">
	
	<section class="content-header">
		
		<h2><center>Comisiones</center></h2>

		<ol class="breadcrumb">
			
			<li><a href="#"><i class="fas fa-dashboard"></i>Inicio</a></li>

			<li class="active">Comiciones</li>

		</ol>

	</section>

	<section class="content">
		
		<div class="row">
			
			<div class="col-lg-12 col-xs-12">
	
				<div class="box box-success">

					<div class="box-header with-border">
			
						<div class="box-body">
				
							<div class="box">

								<form role="form" method="get" <?php echo'action="index.php?ruta=comisionesDos&idempleados='.$_GET["idempleados"].'&fechaInicial='.$_GET["fechaInicial"].'&fechaFinal='.$_GET["fechaFinal"].'&PorcentajeDeComision='.$_GET["PorcentajeDeComision"].'';?>>
								
									<div class="form-group">
										
										<div class="input-group">
										
											<span class="input-group-addon"><i class="fas fa-user"></i></span>

											<select class="form-control input-lg" name="idempleados">
											
												<?php

												$respuesta = Controladorasesores::ctrlMostrarTodosLosEmpleado();
											
												foreach ($respuesta as $key => $value) {
												
													echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
												}

												?>

											</select>

										</div>

									</div>

									<div class="form-group row">

										<div class="col-xs-6">

											<div class="form-group">
									
												<span class="input-group"><i class="fas fa-calender"></i></span>

												<input type="date" class="form-control input-lg" name="fechaInicial">

											</div>
									
										</div>
										<div class="col-xs-6">

											<div class="form-group">
									
												<span class="input-group"><i class="fas fa-calender"></i></span>

												<input type="date" class="form-control input-lg" name="fechaFinal">

											</div>
									
										</div>
									
									</div>

									<div class="form-group">
										
										<div class="input-group">
										
											<span class="input-group-addon"><i class="fas fa-percent"></i></span>

											<input type="number" class="form-control input-lg" name="PorcentajeDeComision">

										</div>

									</div>
								
								</div>

							</div>

							  <div class="modal-footer">
							  	<?php

          						
echo '<a href="vistas/modulos/descargar-reporte-comisiones.php?ruta=ruta='.$_GET["ruta"].'&idempleados='.$_GET["idempleados"].'&fechaInicial='.$_GET["fechaInicial"].'&fechaFinal='.$_GET["fechaFinal"].'&PorcentajeDeComision='.$_GET["PorcentajeDeComision"].'">

		<button class="btn btn-success pull-left" style="margin-top:5px">Descargar reporte en Excel</button>

	</a>';

	var_dump($_GET["PorcentajeDeComision"]);
	var_dump($_GET["fechaInicial"]);
          						?>

          					<button type="submit" class="btn btn-primary">Generar reporte</button>


       						 </div>

       						 <?php

       						 	//$respuesta = new ControladorComisiones();
       						 	//$respuesta -> ctrlMostrarComicion();	

       						 ?>


						</form>	

						    
					</div>
		
				</div>

			</div>

		</div>

	</section>

</div>