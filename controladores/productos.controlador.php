<?php



class ControladorProductos{



	/*=============================================

	MOSTRAR TOTAL PRODUCTOS

	=============================================*/



	static public function ctrMostrarTotalProductos($orden){



		$tabla = "productos";



		$respuesta = ModeloProductos::mdlMostrarTotalProductos($tabla, $orden);



		return $respuesta;



	}



	/*=============================================

	MOSTRAR SUMA VENTAS

	=============================================*/



	static public function ctrMostrarSumaVentas(){



		$tabla = "productos";



		$respuesta = ModeloProductos::mdlMostrarSumaVentas($tabla);



		return $respuesta;



	}



	/*=============================================

	MOSTRAR PRODUCTOS

	=============================================*/



	static public function ctrMostrarProductos($item, $valor){



		$tabla = "productos";



		$respuesta = ModeloProductos::mdlMostrarProductos($tabla, $item, $valor);



		return $respuesta;

	

	}



	/*=============================================

	SUBIR MULTIMEDIA

	=============================================*/



	static public function ctrSubirMultimedia($datos, $ruta){



		if(isset($datos["tmp_name"]) && !empty($datos["tmp_name"])){



			/*=============================================

			DEFINIMOS LAS MEDIDAS

			=============================================*/



			list($ancho, $alto) = getimagesize($datos["tmp_name"]);	



			$nuevoAncho = 1000;

			$nuevoAlto = 1000;



			/*=============================================

			CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DE LA MULTIMEDIA

			=============================================*/



			$directorio = "../vistas/img/multimedia/".$ruta;



			/*=============================================

			PRIMERO PREGUNTAMOS SI EXISTE UN DIRECTORIO DE MULTIMEDIA CON ESTA RUTA

			=============================================*/



			if (!file_exists($directorio)){



				mkdir($directorio, 0755);

			

			}



			/*=============================================

			DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP

			=============================================*/



			if($datos["type"] == "image/jpeg"){



				/*=============================================

				GUARDAMOS LA IMAGEN EN EL DIRECTORIO

				=============================================*/



				$rutaMultimedia = $directorio."/".$datos["name"];



				$origen = imagecreatefromjpeg($datos["tmp_name"]);						



				$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



				imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



				imagejpeg($destino, $rutaMultimedia);



			}



			if($datos["type"] == "image/png"){



				

				/*=============================================

				GUARDAMOS LA IMAGEN EN EL DIRECTORIO

				=============================================*/



				$rutaMultimedia = $directorio."/".$datos["name"];



				$origen = imagecreatefrompng($datos["tmp_name"]);						



				$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



				imagealphablending($destino, FALSE);

		

				imagesavealpha($destino, TRUE);



				imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



				imagepng($destino, $rutaMultimedia);



			}



			return $rutaMultimedia;	

		}



	}



	/*=============================================

	CREAR PRODUCTOS

	=============================================*/



	static public function ctrCrearProducto($datos){



		if(isset($datos["tituloProducto"])){



			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $datos["tituloProducto"]) && preg_match('/^[,\\.\\a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["descripcionProducto"]) ){



				/*=============================================

				VALIDAR IMAGEN PORTADA

				=============================================*/



				$rutaPortada = "../vistas/img/default/default.png";



				if(isset($datos["fotoPortada"]["tmp_name"]) && !empty($datos["fotoPortada"]["tmp_name"])){



					/*=============================================

					DEFINIMOS LAS MEDIDAS

					=============================================*/



					list($ancho, $alto) = getimagesize($datos["fotoPortada"]["tmp_name"]);	



					$nuevoAncho = 1280;

					$nuevoAlto = 720;





					/*=============================================

					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP

					=============================================*/



					if($datos["fotoPortada"]["type"] == "image/jpeg"){



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100,999);



						$rutaPortada = "../vistas/img/cabeceras/".$datos["rutaProducto"].".jpg";



						$origen = imagecreatefromjpeg($datos["fotoPortada"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagejpeg($destino, $rutaPortada);



					}



					if($datos["fotoPortada"]["type"] == "image/png"){



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100,999);



						$rutaPortada = "../vistas/img/cabeceras/".$datos["rutaProducto"].".png";



						$origen = imagecreatefrompng($datos["fotoPortada"]["tmp_name"]);						



						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagealphablending($destino, FALSE);

				

						imagesavealpha($destino, TRUE);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagepng($destino, $rutaPortada);



					}



				}



				/*=============================================

				VALIDAR IMAGEN PRINCIPAL

				=============================================*/



				$rutaFotoPrincipal = "../vistas/img/default/default.png";



				if(isset($datos["fotoPrincipal"]["tmp_name"]) && !empty($datos["fotoPrincipal"]["tmp_name"])){



					/*=============================================

					DEFINIMOS LAS MEDIDAS

					=============================================*/



					list($ancho, $alto) = getimagesize($datos["fotoPrincipal"]["tmp_name"]);	



					$nuevoAncho = 400;

					$nuevoAlto = 450;



					/*=============================================

					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP

					=============================================*/



					if($datos["fotoPrincipal"]["type"] == "image/jpeg"){



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100,999);



						$rutaFotoPrincipal = "../vistas/img/productos/".$datos["rutaProducto"].".jpg";



						$origen = imagecreatefromjpeg($datos["fotoPrincipal"]["tmp_name"]);						



						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagejpeg($destino, $rutaFotoPrincipal);



					}



					if($datos["fotoPrincipal"]["type"] == "image/png"){



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100,999);



						$rutaFotoPrincipal = "../vistas/img/productos/".$datos["rutaProducto"].".png";



						$origen = imagecreatefrompng($datos["fotoPrincipal"]["tmp_name"]);						



						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagealphablending($destino, FALSE);

				

						imagesavealpha($destino, TRUE);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagepng($destino, $rutaFotoPrincipal);



					}



				}



				/*=============================================

				VALIDAR IMAGEN OFERTA

				=============================================*/



				$rutaOferta = "";



				if(isset($datos["fotoOferta"]["tmp_name"]) && !empty($datos["fotoOferta"]["tmp_name"])){



					/*=============================================

					DEFINIMOS LAS MEDIDAS

					=============================================*/



					list($ancho, $alto) = getimagesize($datos["fotoOferta"]["tmp_name"]);



					$nuevoAncho = 640;

					$nuevoAlto = 430;





					/*=============================================

					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP

					=============================================*/



					if($datos["fotoOferta"]["type"] == "image/jpeg"){



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100,999);



						$rutaOferta = "../vistas/img/ofertas/".$datos["rutaProducto"].".jpg";



						$origen = imagecreatefromjpeg($datos["fotoOferta"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagejpeg($destino, $rutaOferta);



					}



					if($datos["fotoOferta"]["type"] == "image/png"){



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100,999);



						$rutaOferta = "../vistas/img/ofertas/".$datos["rutaProducto"].".png";



						$origen = imagecreatefrompng($datos["fotoOferta"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagealphablending($destino, FALSE);

				

						imagesavealpha($destino, TRUE);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagepng($destino, $rutaOferta);



					}



				}



				/*=============================================

				PREGUNTAMOS SI VIENE OFERTE EN CAMINO

				=============================================*/



				if($datos["selActivarOferta"] == "oferta"){



					$datosProducto = array(

						   "titulo"=>$datos["tituloProducto"],

						   "idCategoria"=>$datos["categoria"],

						   "idSubCategoria"=>$datos["subCategoria"],

						   "empresa"=>$datos["empresa"],

						   "tipo"=>$datos["tipo"],

						   "detalles"=>$datos["detalles"],

						   "multimedia"=>$datos["multimedia"],

						   "ruta"=>$datos["rutaProducto"],

						   "estado"=> 1,

						   "titular"=> substr($datos["descripcionProducto"], 0, 225)."...",

						   "descripcion"=> $datos["descripcionProducto"],

						   "palabrasClaves"=> $datos["pClavesProducto"],

						   "precio"=> $datos["precio"],

						   "peso"=> $datos["peso"],

						   "entrega"=> $datos["entrega"],

						   "disponibilidad"=> $datos["disponibilidad"],

						   "Proveedor"=> $datos["Proveedor"],

						   "imgPortada"=>substr($rutaPortada,3),

						   "imgFotoPrincipal"=>substr($rutaFotoPrincipal,3),

						   "oferta"=>1,

						   "precioOferta"=>$datos["precioOferta"],

						   "descuentoOferta"=>$datos["descuentoOferta"],

						   "imgOferta"=>substr($rutaOferta,3),

						   "finOferta"=>$datos["finOferta"],

						   "SubircodigoProducto"=>$datos["SubircodigoProducto"],

						   "EntradInversion"=>$datos["EntradInversion"],

						   "medida"=>$datos["medida"],

							"cantidadTipo"=>$datos["cantidadTipo"],

							"id_almacen"=>$datos["id_almacen"],


							"id_empresa"=>$datos["id_empresa"]						   
						   

					   );





				}else{



					$datosProducto = array(

						   "titulo"=>$datos["tituloProducto"],

						   "idCategoria"=>$datos["categoria"],

						   "idSubCategoria"=>$datos["subCategoria"],

						   "empresa"=>$datos["empresa"],

						   "tipo"=>$datos["tipo"],

						   "detalles"=>$datos["detalles"],

						   "multimedia"=>$datos["multimedia"],

						   "ruta"=>$datos["rutaProducto"],

						   "estado"=> 1,

						   "titular"=> substr($datos["descripcionProducto"], 0, 225)."...",

						   "descripcion"=> $datos["descripcionProducto"],

						   "palabrasClaves"=> $datos["pClavesProducto"],

						   "precio"=> $datos["precio"],

						   "peso"=> $datos["peso"],

						   "entrega"=> $datos["entrega"],

						   "disponibilidad"=> $datos["disponibilidad"],

						   "SubircodigoProducto"=> $datos["SubircodigoProducto"],

						   "Proveedor"=> $datos["Proveedor"],  

						   "imgPortada"=>substr($rutaPortada,3),

						   "imgFotoPrincipal"=>substr($rutaFotoPrincipal,3),

						   "oferta"=>0,

						   "precioOferta"=>0,

						   "descuentoOferta"=>0,

						   "imgOferta"=>"",

						   "finOferta"=>"",

						   "EntradInversion"=>$datos["EntradInversion"],
						   
						   "medida"=>$datos["medida"],

						   "cantidadTipo"=>$datos["cantidadTipo"],

						   "id_almacen"=> $datos["id_almacen"],

						   "id_empresa"=>$datos["id_empresa"]

					   );



				}



				ModeloCabeceras::mdlIngresarCabecera("cabeceras", $datosProducto);



				$respuesta = ModeloProductos::mdlIngresarProducto("productos", $datosProducto);



				return $respuesta;

				



			}else{



					echo'<script>



					swal({

						  type: "error",

						  title: "¡El nombre del producto no puede ir vacía o llevar caracteres especiales!",

						  showConfirmButton: true,

						  confirmButtonText: "Cerrar"

						  }).then(function(result){

							if (result.value) {



							window.location = "index.php?ruta=productos";



							}

						})



			  	</script>';







			}

		

		}



	}



	/*=============================================

	LISTAR PRODUCTOS

	=============================================*/



	static public function ctrListarProductos($ordenar, $item, $valor){



		$tabla = "productos";



		$respuesta = ModeloProductos::mdlListarProductos($tabla, $ordenar, $item, $valor);



		return $respuesta;



	}





		/*=============================================

	EDITAR PRODUCTOS

	=============================================*/



	static public function ctrEditarProducto($datos){



		if(isset($datos["idProducto"])){



			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $datos["tituloProducto"])  && preg_match('/^[,\\.\\a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["descripcionProducto"]) ){



				/*=============================================

				ELIMINAR LAS FOTOS DE MULTIMEDIA DE LA CARPETA

				=============================================*/



				if($datos["tipo"] == "fisico"){



					$item = "id";

					$valor = $datos["idProducto"];



					$traerProductos = ModeloProductos::mdlMostrarProductos("productos", $item, $valor);



					foreach ($traerProductos as $key => $value) {

					

						$multimediaBD = json_decode($value["multimedia"],true);

						$multimediaEditar = json_decode($datos["multimedia"],true);



						$objectMultimediaBD = array();

						$objectMultimediaEditar = array();



						foreach ($multimediaBD as $key => $value) {



						  array_push($objectMultimediaBD, $value["foto"]);



						}



						foreach ($multimediaEditar as $key => $value) {



						  array_push($objectMultimediaEditar, $value["foto"]);



						}



						$borrarFoto = array_diff($objectMultimediaBD, $objectMultimediaEditar);



						foreach ($borrarFoto as $key => $value) {

							

							unlink("../".$value);



						}



					}				



				}



				/*=============================================

				VALIDAR IMAGEN PORTADA

				=============================================*/



				$rutaPortada = "../".$datos["antiguaFotoPortada"];



				if(isset($datos["fotoPortada"]["tmp_name"]) && !empty($datos["fotoPortada"]["tmp_name"])){



					/*=============================================

					BORRAMOS ANTIGUA FOTO PORTADA

					=============================================*/



					unlink("../".$datos["antiguaFotoPortada"]);



					/*=============================================

					DEFINIMOS LAS MEDIDAS

					=============================================*/



					list($ancho, $alto) = getimagesize($datos["fotoPortada"]["tmp_name"]);	



					$nuevoAncho = 1280;

					$nuevoAlto = 720;





					/*=============================================

					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP

					=============================================*/



					if($datos["fotoPortada"]["type"] == "image/jpeg"){



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100,999);



						$rutaPortada = "../vistas/img/cabeceras/".$datos["rutaProducto"].".jpg";



						$origen = imagecreatefromjpeg($datos["fotoPortada"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagejpeg($destino, $rutaPortada);



					}



					if($datos["fotoPortada"]["type"] == "image/png"){



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100,999);



						$rutaPortada = "../vistas/img/cabeceras/".$datos["rutaProducto"].".png";



						$origen = imagecreatefrompng($datos["fotoPortada"]["tmp_name"]);						



						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagealphablending($destino, FALSE);

				

						imagesavealpha($destino, TRUE);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagepng($destino, $rutaPortada);



					}



				}



				/*=============================================

				VALIDAR IMAGEN PRINCIPAL

				=============================================*/



				$rutaFotoPrincipal = "../".$datos["antiguaFotoPrincipal"];



				if(isset($datos["fotoPrincipal"]["tmp_name"]) && !empty($datos["fotoPrincipal"]["tmp_name"])){



					/*=============================================

					BORRAMOS ANTIGUA FOTO PRINCIPAL

					=============================================*/



					unlink("../".$datos["antiguaFotoPrincipal"]);



					/*=============================================

					DEFINIMOS LAS MEDIDAS

					=============================================*/



					list($ancho, $alto) = getimagesize($datos["fotoPrincipal"]["tmp_name"]);	



					$nuevoAncho = 400;

					$nuevoAlto = 450;





					/*=============================================

					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP

					=============================================*/



					if($datos["fotoPrincipal"]["type"] == "image/jpeg"){



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100,999);



						$rutaFotoPrincipal = "../vistas/img/productos/".$datos["rutaProducto"].".jpg";



						$origen = imagecreatefromjpeg($datos["fotoPrincipal"]["tmp_name"]);						



						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagejpeg($destino, $rutaFotoPrincipal);



					}



					if($datos["fotoPrincipal"]["type"] == "image/png"){



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100,999);



						$rutaFotoPrincipal = "../vistas/img/productos/".$datos["rutaProducto"].".png";



						$origen = imagecreatefrompng($datos["fotoPrincipal"]["tmp_name"]);						



						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagealphablending($destino, FALSE);

				

						imagesavealpha($destino, TRUE);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagepng($destino, $rutaFotoPrincipal);



					}



				}



				/*=============================================

				VALIDAR IMAGEN OFERTA

				=============================================*/



				$rutaOferta = "../".$datos["antiguaFotoOferta"];



				if(isset($datos["fotoOferta"]["tmp_name"]) && !empty($datos["fotoOferta"]["tmp_name"])){



					/*=============================================

					BORRAMOS ANTIGUA FOTO OFERTA

					=============================================*/



					if($datos["antiguaFotoOferta"] != ""){



						unlink("../".$datos["antiguaFotoOferta"]);



					}



					/*=============================================

					DEFINIMOS LAS MEDIDAS

					=============================================*/



					list($ancho, $alto) = getimagesize($datos["fotoOferta"]["tmp_name"]);



					$nuevoAncho = 640;

					$nuevoAlto = 430;





					/*=============================================

					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP

					=============================================*/



					if($datos["fotoOferta"]["type"] == "image/jpeg"){



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100,999);



						$rutaOferta = "../vistas/img/ofertas/".$datos["rutaProducto"].".jpg";



						$origen = imagecreatefromjpeg($datos["fotoOferta"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagejpeg($destino, $rutaOferta);



					}



					if($datos["fotoOferta"]["type"] == "image/png"){



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100,999);



						$rutaOferta = "../vistas/img/ofertas/".$datos["rutaProducto"].".png";



						$origen = imagecreatefrompng($datos["fotoOferta"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagealphablending($destino, FALSE);

				

						imagesavealpha($destino, TRUE);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagepng($destino, $rutaOferta);



					}



				}			



				/*=============================================

				PREGUNTAMOS SI VIENE OFERTE EN CAMINO

				=============================================*/



				if($datos["selActivarOferta"] == "oferta"){



					$datosProducto = array(

								   "id"=>$datos["idProducto"],

								   "titulo"=>$datos["tituloProducto"],

								   "idCategoria"=>$datos["categoria"],

								   "idSubCategoria"=>$datos["subCategoria"],

								   "empresa"=>$datos["empresa"],

								   "tipo"=>$datos["tipo"],

								   "detalles"=>$datos["detalles"],

								   "multimedia"=>$datos["multimedia"],

								   "ruta"=>$datos["rutaProducto"],

								   "estado"=> 1,

								   "idCabecera"=>$datos["idCabecera"],

								   "titular"=> substr($datos["descripcionProducto"], 0, 225)."...",

								   "descripcion"=> $datos["descripcionProducto"],

								   "palabrasClaves"=> $datos["pClavesProducto"],

								   "precio"=> $datos["precio"],

								   "peso"=> $datos["peso"],

								   "entrega"=> $datos["entrega"], 

								   "disponibilidad"=> $datos["disponibilidad"],

								   "Proveedor"=> $datos["Proveedor"],  

								   "imgPortada"=>substr($rutaPortada,3),

								   "imgFotoPrincipal"=>substr($rutaFotoPrincipal,3),

								   "oferta"=>1,

								   "precioOferta"=>$datos["precioOferta"],

								   "descuentoOferta"=>$datos["descuentoOferta"],

								   "imgOferta"=>substr($rutaOferta,3),

								   "finOferta"=>$datos["finOferta"],

								   "codigoEditado"=>$datos["codigoEditado"],

								   "inversionEditada"=>$datos["inversionEditada"],

								   "medida"=>$datos["medida"]

								   );



				}else{



					$datosProducto = array(

						 		   "id"=>$datos["idProducto"],

								   "titulo"=>$datos["tituloProducto"],

								   "idCategoria"=>$datos["categoria"],

								   "idSubCategoria"=>$datos["subCategoria"],

								   "empresa"=>$datos["empresa"], 

								   "tipo"=>$datos["tipo"],

								   "detalles"=>$datos["detalles"],

								   "multimedia"=>$datos["multimedia"],

								   "ruta"=>$datos["rutaProducto"],

								   "estado"=> 1,

								   "idCabecera"=>$datos["idCabecera"],

								   "titular"=> substr($datos["descripcionProducto"], 0, 225)."...",

								   "descripcion"=> $datos["descripcionProducto"],

								   "palabrasClaves"=> $datos["pClavesProducto"],

								   "precio"=> $datos["precio"],

								   "peso"=> $datos["peso"],

								   "entrega"=> $datos["entrega"],

								   "disponibilidad"=> $datos["disponibilidad"],

								   "Proveedor"=> $datos["Proveedor"],

								   "imgPortada"=>substr($rutaPortada,3),

								   "imgFotoPrincipal"=>substr($rutaFotoPrincipal,3),

								   "oferta"=>0,

								   "precioOferta"=>0,

								   "descuentoOferta"=>0,

								   "imgOferta"=>"",								   

								   "finOferta"=>"",

								   "codigoEditado"=>$datos["codigoEditado"],

								   "inversionEditada"=>$datos["inversionEditada"],

								   "medida"=>$datos["medida"]

								   );



				}



				ModeloCabeceras::mdlEditarCabecera("cabeceras", $datosProducto);



				$respuesta = ModeloProductos::mdlEditarProducto("productos", $datosProducto);



				return $respuesta;





			}else{



				echo'<script>



					swal({

						  type: "error",

						  title: "¡El nombre del producto no puede ir vacío o llevar caracteres especiales!",

						  showConfirmButton: true,

						  confirmButtonText: "Cerrar"

						  }).then(function(result){

							if (result.value) {



							window.location = "index.php?ruta=productos";



							}

						})



			  	</script>';



			}



		}

		

	}



	/*=============================================

	ELIMINAR PRODUCTO

	=============================================*/



	static public function ctrEliminarProducto(){



		if(isset($_GET["idProducto"])){



			$datos = $_GET["idProducto"];



			/*=============================================

			ELIMINAR MULTIMEDIA

			=============================================*/



			$borrar = glob("vistas/img/multimedia/".$_GET["rutaCabecera"]."/*");



				foreach($borrar as $file){



					unlink($file);



				}



			rmdir("vistas/img/multimedia/".$_GET["rutaCabecera"]);



			/*=============================================

			ELIMINAR FOTO PRINCIPAL

			=============================================*/



			if($_GET["imgPrincipal"] != "" && $_GET["imgPrincipal"] != "vistas/img/default/default.png"){



				unlink($_GET["imgPrincipal"]);		



			}



			/*=============================================

			ELIMINAR OFERTA

			=============================================*/



			if($_GET["imgOferta"] != ""){



				unlink($_GET["imgOferta"]);		



			}



			/*=============================================

			ELIMINAR CABECERA

			=============================================*/



			if($_GET["imgPortada"] != "" && $_GET["imgPortada"] != "vistas/img/default/default.png"){



				unlink($_GET["imgPortada"]);		



			}



			ModeloCabeceras::mdlEliminarCabecera("cabeceras", $_GET["rutaCabecera"]);



			$respuesta = ModeloProductos::mdlEliminarProducto("productos", $datos);



			if($respuesta == "ok"){



				echo'<script>



				swal({

					  type: "success",

					  title: "El producto ha sido borrado correctamente",

					  showConfirmButton: true,

					  confirmButtonText: "Cerrar"

					  }).then(function(result){

								if (result.value) {



								window.location = "index.php?ruta=productos";



								}

							})



				</script>';



			}		







		}



	}



	static public function ctrMostrarProductosConAlertaDeStock(){

		

		$tabla = "productos";



		$respuesta = ModeloProductos::mdlMostrarProdutosConAletaDeStock($tabla);



		return $respuesta;

	}



	static public function ctrMostrarProductosApuntoDeAgotarse(){

		

		$tabla = "productos";



		$respuesta = ModeloProductos::mdlMostrarProductosApuntoDeAgotarse($tabla);



		return $respuesta;

	}

	static public function ctrEditarStock(){
		
		if (isset($_POST["StockEditado"])) {
			
			
			$tabla = "productos";

			$datos = array("id" => $_POST["idProducto"], 
						   "stock" => $_POST["StockEditado"], 
						   "inversion" => $_POST["InversioProductoEditada"]
						);
			
			$respuesta = ModeloProductos::mdlEditarStock($tabla,$datos);

			if ($respuesta == "ok") {
						
				echo '<script>

						swal({

							type: "success",
							title: "¡El stock se actualizo correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"

						}).then(function(result){

							if(result.value){
							
								window.location = "index.php?ruta=stock";

							}

						});
					

					</script>';
			}
		}


	}

	static public function ctrDescargarReporteProductos(){

		if (isset($_GET["reporte"])){

			if($_GET["reporte"] == "productos"){

				$tabla = "productos";

				$Datostabla = ModeloProductos::mdlMostrarProductos($tabla, null, null);

				$Name = $_GET["reporte"].'.xls';

				header('Expires: 0');
				header('Cache-control: private');
				header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
				header("Cache-Control: cache, must-revalidate"); 
				header('Content-Description: File Transfer');
				header('Last-Modified: '.date('D, d M Y H:i:s'));
				header("Pragma: public"); 
				header('Content-Disposition:; filename="'.$Name.'"');
				header("Content-Transfer-Encoding: binary");
				

				echo utf8_decode("<table border='0'> 

					<tr> 	
					<td style='font-size: smaller; border:1px solid #eee;'>ID</td>
					<td style='font-size: smaller; border:1px solid #eee;'>CATEGORIA</td>
					<td style='font-size: smaller; border:1px solid #eee;'>SUBCATEGORIA</td>
					<td style='font-size: smaller; border:1px solid #eee;'>TITULO</td>
					<td style='font-size: smaller; border:1px solid #eee;'>DESCRIPCION</td>
					<td style='font-size: smaller; border:1px solid #eee;'>STOCK</td>
					<td style='font-size: smaller; border:1px solid #eee;'>FECHA</td>


					</tr>"
				);

				foreach ($Datostabla as $key => $value) {


					$tablaDos="categorias";
					$item="id";
					$valor=$value["id_categoria"];
					$categorias = ModeloCategorias::mdlMostrarCategorias($tablaDos,$item,$valor);

					$tablaTres="subcategorias";
					$itemSubcategorias="id";
					$valor2=$value["id_subcategoria"];

					$subcategorias = ModeloSubCategorias::mdlMostrarSubCategorias($tablaTres, $itemSubcategorias, $valor2);
					foreach ($subcategorias as $key => $valueSubcategorias) {
						
						$subcategoriaProducto = $valueSubcategorias["subcategoria"];
					}

					echo utf8_decode("<tr>
						<td style='font-size: smaller;'border:1px solid #eee;'>".$value["id"]."</td>
						<td style='font-size: smaller;'border:1px solid #eee;'>".$categorias["categoria"]."</td>
						<td style='font-size: smaller;'border:1px solid #eee;'>".$subcategoriaProducto."</td>
						<td style='font-size: smaller;'border:1px solid #eee;'>".$value["titulo"]."</td>
						<td style='font-size: smaller;'border:1px solid #eee;'>".$value["descripcion"]."</td>
						<td style='font-size: smaller;'border:1px solid #eee;'>".$value["disponibilidad"]."</td>
						<td style='font-size: smaller;'border:1px solid #eee;'>".$value["fecha"]."</td>


						</tr>
						");
 				

				}

				echo utf8_decode("</table>

					");
				
			}
		}
	}

}