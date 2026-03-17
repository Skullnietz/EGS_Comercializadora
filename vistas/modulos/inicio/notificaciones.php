<?php

//if($_SESSION["perfil"] != "administrador"){

	//return;	

//}

$notificaciones = ControladorNotificaciones::ctrMostrarNotificaciones();

$totalNotificaciones = $notificaciones["nuevosUsuarios"] + $notificaciones["nuevasVentas"] + $notificaciones["nuevasVisitas"];

?>
<!--=====================================
NOTIFICACIONES PARA SUPER USUARIOS ADMINISTRADORES
======================================-->
<?php

if($_SESSION["perfil"] == "administrador"){

	echo'<!-- notifications-menu -->
	<li class="dropdown notifications-menu">
		
		<!-- dropdown-toggle -->
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			
			<i class="fas fa-bell"></i>
			
			<span class="label label-warning">'.$totalNotificaciones.'</span>
		
		</a>
		<!-- dropdown-toggle -->

		<!--dropdown-menu -->
		<ul class="dropdown-menu">

			<li class="header">Tienes '.$totalNotificaciones.' notificaciones</li>

			<li>
				<!-- menu -->
				<ul class="menu">

					<!-- usuarios -->
					<li>
					
						<a href="" class="actualizarNotificaciones" item="nuevosUsuarios">
						
							<i class="fas fa-users text-aqua"></i> '. $notificaciones["nuevosUsuarios"] .' nuevos usuarios registrados
						
						</a>

					</li>

					<!-- ventas -->
					<li>
					
						<a href="" class="actualizarNotificaciones" item="nuevasVentas">
						
							<i class="fas fa-shopping-cart text-aqua"></i> '.$notificaciones["nuevasVentas"] .' nuevas ventas
						
						</a>

					</li>
					
					<!-- visitas -->
					<li>
					
						<a href="" class="actualizarNotificaciones" item="nuevasVisitas">
						
							<i class="fas fa-map-marker text-aqua"></i> '. $notificaciones["nuevasVisitas"] .' nuevas visitas
						
						</a>

					</li>

				</ul>
				<!-- menu -->

			</li>

		</ul>
		<!--dropdown-menu -->

	</li>

	';


}

if($_SESSION["perfil"] == "vendedor"){
										
	//TRAER ORDENES CON ATRASO 
	$item = "correo";
	$valor =  $_SESSION["email"];

	$Asesores = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);

	$id_Asesor = $Asesores["id"];

	//echo'<pre>'.$id_Asesor.'</pre>';

	$ordenesDelAsesor = controladorOrdenes::ctrMostrarOrdenesDelAsesor($id_Asesor);

	foreach ($ordenesDelAsesor as $key => $valueUno) {

	}
	 $numero = count($valueUno);

echo'<!--=====================================
NOTIFICACIONES  PARA TECNICOS Y VENDEDORES
======================================-->

<!-- notifications-menu -->
<li class="dropdown notifications-menu">
	
	<!-- dropdown-toggle -->
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">
		
		<i class="fas fa-bell-o"></i>
		
		<span class="label label-warning">'.$numero.'</span>
	
	</a>
	<!-- dropdown-toggle -->

	<!--dropdown-menu -->
	<ul class="dropdown-menu">

		<li class="header">Tienes '.$numero.' notificaciones</li>

		<li>
			<!-- menu -->
			<ul class="menu">

				';


						foreach ($ordenesDelAsesor as $key => $ordenesQueTieneElAsesor) {
	
							//echo'<pre>'.$ordenesQueTieneElAsesor.'</pre>';

							date_default_timezone_set("America/Mexico_City");

						 	$fecha = date("Y-m-d H:i:s",strtotime($ordenesQueTieneElAsesor["fecha_ingreso"]."+ 5 days"));

						 	if ($fecha  >= $ordenesQueTieneElAsesor["fecha_ingreso"] and $ordenesQueTieneElAsesor["estado"] != "Entregado (Ent)" and $ordenesQueTieneElAsesor["estado"] != "Cancelada (can)"){

						 		
						 		echo'<!-- ORDENES -->
									
									<li>
				
										<a class="btnVerInfoOrden" idOrden="'.$ordenesQueTieneElAsesor["id"].'" cliente="'.$ordenesQueTieneElAsesor["id_usuario"].'" tecnico="'.$ordenesQueTieneElAsesor["id_tecnico"].'" asesor="'.$ordenesQueTieneElAsesor["id_Asesor"].'" empresa="'.$ordenesQueTieneElAsesor["id_empresa"].'" pedido="'.$ordenesQueTieneElAsesor["id_pedido"].'" item="nuevasVisitas"><i class="fas fa-tv"></i> '.$ordenesQueTieneElAsesor["id"].' Orden con atraso de entrega </br>
										</a>

								</li>';
								$AlbumDeImagenes = json_decode($ordenesQueTieneElAsesor["multimedia"], true);

								if (is_array($AlbumDeImagenes) || is_object($AlbumDeImagenes)) {
									
									foreach ($AlbumDeImagenes as $key => $valueImagenes) {
										
										
									}
								}
								
								echo'<!-- notifications-push -->	


									<script>

										Push.create("ORDEN CON ATRASO DE ENTREGA",{

											body:"ORDEN: '.$ordenesQueTieneElAsesor["id"].'",
											icon:"'.$valueImagenes["foto"].'",
											timeout:20000,
											onClick: function(){
												window.location="inicio";
												this.close();
											}

											});
									</script>';
						 	}
						}

					
					echo'

			</ul>
			<!-- menu -->

		</li>

	</ul>
	<!--dropdown-menu -->

</li>
<!-- notifications-menu -->	';

}
?>

<?php
if($_SESSION["perfil"] == "tecnico"){
										
	//TRAER ORDENES CON ATRASO 
	$item = "correo";
	$valor =  $_SESSION["email"];

	$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);

	$id_tecnico = $tecnico["id"];

	//echo'<pre>'.$id_tecnico.'</pre>';

	$ordenesDelTecnico = controladorOrdenes::ctrMostrarOrdenesDelTecncio($id_tecnico);

	foreach ($ordenesDelTecnico as $key => $valueDos) {

		//echo '<pre>'.$valueOrdenesDeTecnico["titulo"].'</pre>';

	}

	$numero = count($valueDos);
echo'<!--=====================================
NOTIFICACIONES  PARA TECNICOS Y VENDEDORES
======================================-->

<!-- notifications-menu -->
<li class="dropdown notifications-menu">
	
	<!-- dropdown-toggle -->
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">
		
		<i class="fas fa-bell-o"></i>
		
		<span class="label label-warning">'.$numero.'</span>
	
	</a>
	<!-- dropdown-toggle -->

	<!--dropdown-menu -->
	<ul class="dropdown-menu">

		<li class="header">Tienes '.$numero.' notificaciones</li>

		<li>
			<!-- menu -->
			<ul class="menu">

				';


						foreach ($ordenesDelTecnico as $key => $ordenesQueTieneElTecnico) {
	
							//echo'<pre>'.$ordenesQueTieneElTecnico.'</pre>';

							if ($ordenesQueTieneElTecnico["estado"] == "Aceptado (ok)") {
						 			
						 			echo'<li>
				
										<a class="btnVerInfoOrden" idOrden="'.$ordenesQueTieneElTecnico["id"].'" cliente="'.$ordenesQueTieneElTecnico["id_usuario"].'" tecnico="'.$ordenesQueTieneElTecnico["id_tecnico"].'" asesor="'.$ordenesQueTieneElTecnico["id_Asesor"].'" empresa="'.$ordenesQueTieneElTecnico["id_empresa"].'" pedido="'.$ordenesQueTieneElTecnico["id_pedido"].'" item="nuevasVisitas" style="background-color:#C0FFAD; color:black"><i class="fas fa-tv"></i> '.$ordenesQueTieneElTecnico["id"].' Orden Aceptada Inicar Reparacion</br>
										</a>

									</li>';

						 		}

							date_default_timezone_set("America/Mexico_City");

						 	$fecha = date("Y-m-d H:i:s",strtotime($ordenesQueTieneElTecnico["fecha_ingreso"]."+ 5 days"));

						 	//$diff = $fecha1->diff($fecha2);

							// El resultados sera 3 dias
							//echo $diff->days . ' dias';

						 	if ($fecha  >= $ordenesQueTieneElTecnico["fecha_ingreso"] and $ordenesQueTieneElTecnico["estado"] != "Entregado (Ent)" and $ordenesQueTieneElTecnico["estado"] != "Cancelada (can)" and $ordenesQueTieneElTecnico["estado"] != "Aceptado (ok)" and $ordenesQueTieneElTecnico["estado"] != "Pendiente de autorización (AUT)"  ){
						 		
						 		echo'<!-- ORDENES -->
									
									<li>
				
										<a class="btnVerInfoOrden" idOrden="'.$ordenesQueTieneElTecnico["id"].'" cliente="'.$ordenesQueTieneElTecnico["id_usuario"].'" tecnico="'.$ordenesQueTieneElTecnico["id_tecnico"].'" asesor="'.$ordenesQueTieneElTecnico["id_Asesor"].'" empresa="'.$ordenesQueTieneElTecnico["id_empresa"].'" pedido="'.$ordenesQueTieneElTecnico["id_pedido"].'" item="nuevasVisitas"><i class="fas fa-tv"></i> '.$ordenesQueTieneElTecnico["id"].' Orden con atraso de entrega </br>
										</a>

								</li>';
								$AlbumDeImagenes = json_decode($ordenesQueTieneElTecnico["multimedia"], true);
								if (is_array($AlbumDeImagenes) || is_object($AlbumDeImagenes)) {
  								foreach ($AlbumDeImagenes as $key => $valueImagenes) {
									# code...
								}
								}
								
								
								echo'<!-- notifications-push -->	


									<script>

										Push.create("ORDEN CON ATRASO DE ENTREGA",{

											body:"ORDEN: '.$ordenesQueTieneElTecnico["id"].'",
											icon:"'.$valueImagenes["foto"].'",
											timeout:10000,
											onClick: function(){
												window.location="inicio";
												this.close();
											}

											});
									</script>';
						 	}
						}

					
					echo'

			</ul>
			<!-- menu -->

		</li>

	</ul>
	<!--dropdown-menu -->

</li>


';


}

?>
