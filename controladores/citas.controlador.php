<?php

class ControladorCitas
{

    /*=============================================
    MOSTRAR CITAS
    =============================================*/
    static public function ctrMostrarCitas($item, $valor)
    {

        $tabla = "citas";
        $respuesta = ModeloCitas::mdlMostrarCitas($tabla, $item, $valor);
        return $respuesta;

    }

    /*=============================================
    CREAR CITA
    =============================================*/
    static public function ctrCrearCita()
    {

        if (isset($_POST["tituloCita"])) {

            if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["tituloCita"])) {

                $tabla = "citas";

                $datos = array(
                    "title" => $_POST["tituloCita"],
                    "start" => $_POST["fechaCita"],
                    "end" => $_POST["fechaCita"],
                    "description" => "",
                    "color" => $_POST["colorCita"],
                    "id_orden" => isset($_POST["idOrden"]) && !empty($_POST["idOrden"]) ? $_POST["idOrden"] : null
                );

                $respuesta = ModeloCitas::mdlIngresarCita($tabla, $datos);

                if ($respuesta == "ok") {

                    echo '<script>

					swal({
						  type: "success",
						  title: "La cita ha sido guardada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "pantallacitas";

									}
								})

					</script>';

                }

            } else {

                echo '<script>

					swal({
						  type: "error",
						  title: "¡El título no puede ir vacío o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "pantallacitas";

							}
						})

			  	</script>';

            }

        }

    }

    /*=============================================
    OCULTAR CITA
    =============================================*/
    static public function ctrOcultarCita()
    {

        if (isset($_GET["idCita"])) {

            $tabla = "citas";
            $datos = $_GET["idCita"];

            $respuesta = ModeloCitas::mdlOcultarCita($tabla, $datos);

            if ($respuesta == "ok") {

                echo '<script>

				swal({
					  type: "success",
					  title: "La cita ha sido borrada correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar"
					  }).then(function(result){
								if (result.value) {

								window.location = "pantallacitas";

								}
							})

				</script>';

            }

        }

    }

}
