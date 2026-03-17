<?php
class ControladorTickets{
	
	function ctrCrearTicket(){
		
		if (isset($_POST["listaProductosDelTicket"])){
			
			/*=============================================
			REDUCIR EL STOCK
			=============================================*/
			$listaDeProductos = json_decode($_POST["listaProductosDelTicket"], true);

			//var_dump($listaDeProductos);

			foreach ($listaDeProductos as $key => $value) {
					
				$tablaProductos = "productos";

				$valor = $value["id"];
				$item1b = "disponibilidad";
				$valor1b = $value["stock"];

				//var_dump($valor1b);

				$nuevoStock = ModeloProductos::mdlActualizarProductoVentasDinamicas($tablaProductos, $item1b, $valor1b, $valor);

			}

			/*=============================================
			GUARDAR EL TICKET
			=============================================*/
			$tablaTickets = "ticket";
			$datosTicket = array("empresa" =>$_POST["empresa"],
								 "codigo"=> $_POST["codigoTicket"],
								 "productos"=> $_POST["listaProductosDelTicket"],
								 "total"=>$_POST["TotalProductosTicket"]);
			//var_dump($datosTicket);
			$respuesta = ModeloTickets::mdlIngresarTicket($tablaTickets,$datosTicket);

			if($respuesta == "ok"){
				
				echo '<script>
					swal({
						type: "success",
						title: "¡La venta se ha realizado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
					}).then(function(result){
						if(result.value){
							window.location = "tickets";
						}
					});
					</script>';

			}else{

				echo '<script>
					swal({
						type: "error",
						title: "¡Los campos no pueden ir vacíos o llevar caracteres especiales!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){
						if(result.value){
							window.location = "tickets";

						}
					});

				</script>';
			}


		}

	}


	public function ctrMostrarTickets($item,$valor)
	{
		
		$tabla = "ticket";

		$respuesta = ModeloTickets::mdlMostrarTickets($tabla, $item, $valor);



		return $respuesta;
	}
}