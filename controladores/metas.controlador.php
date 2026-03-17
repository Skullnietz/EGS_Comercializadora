<?php

class ControladorMetas{
    /*======================================
	      METODO PARA REGISTRAR META
	=======================================*/
	public function ctrlSUbirMeta(){
		
		if (isset($_POST["area"])) {
			
			$datos = array("area" => $_POST["area"],
						   "usuario" => $_POST["usuario"],
						   "tipo" => $_POST["tipo"],
						   "descripcion" => $_POST["descripcion"],
						   "actividades" => $_POST["actividades"],
						   "progreso" => 0,
						   "observaciones" => "Sin observación",
						   "estado" => "Pendiente"
			);

		$tabla = "metas";

		$respuesta = ModeloMetas::mdlSubirMeta($datos,$tabla);


		if($respuesta == "ok"){

				echo'<script>

				swal({
					  type: "success",
					  title: "La meta ha sido Guardada correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar",
					  closeOnConfirm: false
					  }).then(function(result) {
								if (result.value) {

								window.location = "index.php?ruta=createobjetivo";

								}
							})

				</script>';

		}else{

			echo'<script>

				swal({
					  type: "error",
					  title: "La meta No ha sido Guardada correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar",
					  closeOnConfirm: false
					  }).then(function(result) {
								if (result.value) {

								window.location = "index.php?ruta=createobjetivo";

								}
							})

				</script>';
		}



		}
		
	}
	/*======================================
        	METODO PARA MOSTRAR META
	=======================================*/
	 static public function ctrMostrarMetas($item, $valor){
		
		$tabla = "metas";

		$respuesta = ModeloMetas::mdlMostrarMetas($tabla, $item, $valor);

		return $respuesta;

	}
	/*======================================
	METODO PARA MOSTRAR METAS POR ID PERFIL
	=======================================*/
		public function ctrMostrarMetasPorIdPerfil($tabla, $id_perfil){
		
		$tabla = "metas";

		$respuesta = ModeloMetas::mdlMostrarMetasPorIdPerfil($tabla, $id_perfil);

		return $respuesta;
	}
	
	/*======================================
	METODO PARA MOSTRAR NOMBRE POR ID PERFIL
	=======================================*/
	
		public function ctrMostrarNombrePorIdPerfil($tabla, $idperfil){
		
		$tabla = "administradores";

		$respuesta = ModeloMetas::mdlMostrarNombrePorIdPerfil($tabla, $idperfil);

		return $respuesta;
	}
	
	/*======================================
	METODO PARA MOSTRAR METAS POR ID META
	=======================================*/
		public function ctrMostrarMetasPorIdMeta($tabla, $id_perfil,$id_meta){
		
		$tabla = "metas";

		$respuesta = ModeloMetas::mdlMostrarMetasPorIdMeta($tabla, $id_perfil,$id_meta);

		return $respuesta;
	}
	/*===================================================
	METODO PARA MOSTRAR PERSONAL DEPARTAMENTO ELECTRONICA
	=====================================================*/
		public function ctrMostrarPersonalDepartamento($tabla, $area){
		
		$tabla = "administradores";
		

		$respuesta = ModeloMetas::mdlMostrarPersonalDepartamento($tabla, $area);

		return $respuesta;
	}
	/*============================================
	 METODO PARA CONTAR LAS METAS COMPLETADAS POR ID
	==============================================*/
		public function ctrMostrarMetasCompletadasPorId($tabla, $id_perfil, $estado){
		
		$tabla = "metas";

		$respuesta = ModeloMetas::mdlMostrarMetasCompletadasPorId($tabla, $id_perfil, $estado);

		return $respuesta;
	}
	/*============================================
	 METODO PARA CONTAR LAS METAS COMPLETADAS POR DEPARTAMENTO
	==============================================*/
		public function ctrMostrarMetasCompletadasPorDepartamento($tabla, $area, $estado){
		
		$tabla = "metas";

		$respuesta = ModeloMetas::mdlMostrarMetasCompletadasPorDepartamento($tabla, $area, $estado);

		return $respuesta;
	}
	/*======================================
	      METODO PARA ELIMINAR META
	=======================================*/
	static public function ctrEliminarMeta(){

		if(isset($_GET["idmeta"])){

			$tabla ="metas";
			$datos = $_GET["idmeta"];

			$respuesta = ModeloMetas::mdlEliminarMeta($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

				swal({
					  type: "success",
					  title: "La meta ha sido borrada correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar",
					  closeOnConfirm: false
					  }).then(function(result) {
								if (result.value) {

								window.location = "index.php?ruta=listaobjetivos";

								}
							})

				</script>';

			}		

		}

	}
	
	
}
    