<?php
if($_SESSION["perfil"] != "administrador"  AND $_SESSION["perfil"]!= "vendedor"){

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
      		$valor = $_GET["cliente"];
        	$clientes = ControladorCRM::ctrlMostrarClientesenCRM($item, $valor);

		?>

				<h2><center>Cliente: <?echo $clientes["nombre"]?></center></h2>

		<ol class="breadcrumb">

	      		<li><a href="#"><i class="fas fa-dashboard"></i> Inicio</a></li>
	      		<li class="active">Cliente <?echo $clientes["nombre"]?></li>

	    </ol>
	</section>
	
	<section class="content">

		<div class="row">

			<div class="col-lg-5 col-xs-12">

				<div class="box box-success">

					<div class="box-header with-border"></div>

					<div class="box-body">

						<div class="box">

							<!--=======================
							 TELEFONO DEL CLIENTE
							===========================-->
							<div class="form-group">

								<div class="input-group">

									<span class="input-group-addon">Telefono #1</span> 

                    				<input type="text" class="form-control" value="<?php echo $clientes["telefono"] ?>" name="nombreCliente" readonly>

								</div>

							</div>
						

							<!--=======================
							 ULTIMA COMPRA
							===========================-->
							<?php
								//TRAER ORDENES  
						        $item = "id_usuario";
						        $valor = $_GET["cliente"];

						        $ordenesClientes  = controladorOrdenes::ctrMostrarordenesParaValidar($item,$valor);
						        foreach ($ordenesClientes as $key => $value) {
						        	$fecha = $value["fecha_Salida"];
						        }
							    ?>
							<div class="form-group">

								<div class="input-group">

									<span class="input-group-addon">Ultima compra</span> 

                    				<input type="text" class="form-control" value="<?php echo $fecha ?>" name="nombreCliente" readonly>

								</div>

							</div>

							<div class="form-group">

								<div class="input-group">

									<span class="input-group-addon">Correo</span> 
                    				<input type="text" class="form-control" value="<?php echo $clientes["correo"] ?>" name="nombreCliente" readonly>

								</div>

							</div>
							<div class="form-group">

								<div class="input-group">

									<span class="input-group-addon">Monto</span> 
                    				<input type="text" class="form-control" value="$ <?php echo $value["total"] ?>" name="nombreCliente" readonly>

								</div>

							</div>

						</div>

					</div>

				</div>

			</div>

			<!--====================================
			SEGUNDO CONTENEDOR DE DATOS DEL USUARIO
			=======================================-->
			<div class="col-lg-5 col-xs-12">

				<div class="box box-success">

					<div class="box-header with-border"></div>

					<div class="box-body">

						<div class="box">

							<!--=======================
							 TELEFONO DOS DEL CLIENTE
							===========================-->
							<div class="form-group">

								<div class="input-group">

									<span class="input-group-addon">Telefono #2</span> 

                    				<input type="text" class="form-control" value="<?php echo $clientes["telefonoDos"] ?>" name="nombreCliente" readonly>

								</div>

							</div>
							<!--=======================
							 TELEFONO DOS DEL CLIENTE
							===========================-->
							<div class="form-group">

								<div class="input-group">

									<span class="input-group-addon">Descripción</span> 

                    				<input type="text" class="form-control" value="<?php echo $value["partidaUno"] ?>" name="nombreCliente" readonly>

								</div>

							</div>
							<!--=======================
							 ETIQUETA DE CLIENTE
							===========================-->
							<div class="form-group">

								<div class="input-group">

									<span class="input-group-addon">Etiqueta</span> 

                    				<input type="text" class="form-control" value="<?php echo $clientes["etiqueta"] ?>" name="nombreCliente" readonly>

								</div>

							</div>
							<!--=======================
							Monto de orden
							===========================-->
							<div class="form-group">

								<div class="input-group">

									<span class="input-group-addon">Consumo</span> 

                    				<input type="text" class="form-control" value="$ <?php echo $value["total"] ?>" name="nombreCliente" readonly>

								</div>

							</div>

						</div>

					</div>

				</div>

			</div>


		</div>

	</section>

</div>