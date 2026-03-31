<?php



require_once "conexionWordpress.php";



class ModeloOrdenes{
     // Historial de Cliente
    static public function mdlMostrarHistorial($tabla, $valor){
        
    $stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE id_usuario = $valor ");
    
   $stmt -> execute();



		return $stmt -> fetchAll();







	}

// Mostrar Material Disponible de Equipo dejado hace -1 MONTH
	static public function mdlMostrarOrdenesMaterial($tabla){



		
		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE estado IN('Aceptado (ok)','Pendiente de autorización (AUT','	Terminada (ter)','Producto para venta') AND fecha_ingreso <= NOW() - INTERVAL 1 MONTH  ORDER BY `ordenes`.`fecha_ingreso`  DESC");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}

// COMISIONES DEL MES TECNICO Y VENDEDOR 1ERA QUINCENA
	static public function mdlMostrarComisionesPrimera($tabla){



		
		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE MONTH(fecha_Salida) = MONTH(CURDATE()) AND YEAR(fecha_Salida) = YEAR(CURDATE()) AND DAY(fecha_Salida) <= 15 AND estado = 'Entregado (Ent)' ORDER BY `ordenes`.`fecha_Salida`  DESC");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}
// TODAS LAS COMISIONES DEL MES  1ERA QUINCENA (ADMINSTRADOR)
	static public function mdlMostrarComisionesPorPersonaPrimera($tabla , $session_id){



		
		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE MONTH(fecha_Salida) = MONTH(now()) AND YEAR(fecha_Salida) = YEAR(now()) AND DAY(fecha_Salida) <= 15 AND estado = 'Entregado (Ent)' AND (id_tecnico = $session_id OR id_tecnicoDos = $session_id) ORDER BY `ordenes`.`fecha_Salida` DESC");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}
// COMISIONES DEL MES TECNICO Y VENDEDOR 2DA QUINCENA
	static public function mdlMostrarComisionesSegunda($tabla){



		
		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE MONTH(fecha_Salida) = MONTH(CURDATE()) AND YEAR(fecha_Salida) = YEAR(CURDATE()) AND DAY(fecha_Salida) >= 15 AND estado = 'Entregado (Ent)' ORDER BY `ordenes`.`fecha_Salida`  DESC");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}
// TODAS LAS COMISIONES DEL MES  2DA QUINCENA (ADMINSTRADOR)
	static public function mdlMostrarComisionesPorPersonaSegunda($tabla , $session_id2){



		
		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE MONTH(fecha_Salida) = MONTH(now()) AND YEAR(fecha_Salida) = YEAR(now()) AND DAY(fecha_Salida) >= 15 AND estado = 'Entregado (Ent)' AND (id_tecnico = $session_id2 OR id_tecnicoDos = $session_id2) ORDER BY `ordenes`.`fecha_Salida` DESC");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}
	
	
	
// Mostrar ordenes campos utilizados
	static public function mdlMostrarOrdenes($tabla, $campo, $empresa){



		
		$stmt = ConexionWP::conectarWP()->prepare("SELECT id, id_empresa, id_tecnico,id_tecnicoDos, id_usuario, estado, fecha_ingreso, fecha, fecha_Salida, id_Asesor,id_pedido, total FROM $tabla WHERE estado  IN('Aceptado (ok)','Pendiente de autorización (AUT','En revisión (REV)', 'Supervisión (SUP)','Terminada (ter)','Producto para venta','En revisión probable garantía')ORDER BY id DESC");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}
	// MANDAR ULTIMA ORDEN EN REGISTRARSE
		static public function mdlUltimaEntrega($tabla){



		
		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla ORDER BY fecha_Salida DESC LIMIT 1");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}
	static public function mdlMostrarOrdenesNew($tabla, $campo, $empresa){



		
		$stmt = ConexionWP::conectarWP()->prepare("SELECT id, id_empresa, id_tecnico, id_usuario, estado, fecha_ingreso, fecha, fecha_Salida, id_Asesor,id_pedido, total FROM $tabla ORDER BY id DESC");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}





	/*=============================================

	SUMAR ORDENES MES

	=============================================*/


	static public function mdlMostrarOrdenesSuma($tabla){



		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE `estado` = 'Entregado (Ent)' AND MONTH(`fecha_Salida`) = MONTH(NOW()) AND YEAR(`fecha_Salida`) = YEAR(NOW())");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}
		/*=============================================

	SUMAR ORDENES MES ASESOR 

	=============================================*/


	static public function mdlMostrarOrdenesSumaAsesor($tabla,$idAsesor){



		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE `estado` = 'Entregado (Ent)' AND MONTH(`fecha_Salida`) = MONTH(NOW()) AND YEAR(`fecha_Salida`) = YEAR(NOW()) AND `id_Asesor` = $idAsesor");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}

	/*=============================================

	MOSTRAR ORDENES

	=============================================*/



	static public function mdlMostrarordenesParaValidar($tabla, $item, $valor){



		if($item != null){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");



			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);



			$stmt -> execute();



			return $stmt -> fetchAll();



		}else{



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla ORDER BY id DESC");



			$stmt -> execute();



			return $stmt -> fetchAll();



		}











	}

	/*=============================================

	MOSTRAR ORDENES POR EMPRESA Y PERFIL

	=============================================*/



	static public function mdlMostrarordenesEmpresayPerfil($tabla, $itemOrdenes, $valorOrdenes, $iteDosOrdenes, $valorDosOrdenes){



		if($itemOrdenes != null){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE $itemOrdenes = :$itemOrdenes AND $iteDosOrdenes = :$iteDosOrdenes");



			$stmt -> bindParam(":".$itemOrdenes, $valorOrdenes, PDO::PARAM_STR);

			$stmt -> bindParam(":".$iteDosOrdenes, $valorDosOrdenes, PDO::PARAM_STR);



			$stmt -> execute();



			return $stmt -> fetchAll();



		}else{



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla ORDER BY id DESC");



			$stmt -> execute();



			return $stmt -> fetchAll();



		}











	}

	/*=============================================

	MOSTRAR ORDENES ORDENADAS

	=============================================*/



	static public function mdlMostrarordenesOrdenadas($tabla, $ordenar, $item, $valor, $base, $tope, $modo){

		

		if($item != null){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT *FROM $tabla WHERE $item = :$item ORDER BY $ordenar $modo LIMIT $base, $tope");



			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);



			$stmt -> execute();



			return $stmt -> fetchAll();


		}else{



			$stmt = ConexionWP::conectarWP()->prepare("SELECT *FROM $tabla ORDER BY $ordenar $modo LIMIT $base, $tope");



			$stmt -> execute();



			return $stmt -> fetchAll();



		}









	}



	/*=============================================

	CREAR PRODUCTO

	=============================================*/



	static public function mdlIngresarOrden($tabla, $datos){



		$stmt = ConexionWP::conectarWP()->prepare("INSERT INTO $tabla(ruta, titulo, id_empresa, descripcion, multimedia, portada, id_tecnico, id_Asesor, id_usuario, partidaUno, precioUno, partidaDos, precioDos, partidaTres, precioTres, partidaCuatro, precioCuatro, partidaCinco, precioCinco, partidaSeis, precioSeis, partidaSiete, precioSiete, partidaOcho, precioOcho, partidaNueve, precioNueve, partidaDiez, precioDiez, total, estado, fecha_ingreso, marcaDelEquipo, modeloDelEquipo, numeroDeSerieDelEquipo) VALUES (:ruta, :titulo, :id_empresa, :descripcion, :multimedia, :portada, :id_tecnico, :id_Asesor, :id_usuario, :partidaUno, :precioUno, :partidaDos, :precioDos, :partidaTres, :precioTres, :partidaCuatro, :precioCuatro, :partidaCinco, :precioCinco, :partidaSeis, :precioSeis, :partidaSiete, :precioSiete, :partidaOcho, :precioOcho, :partidaNueve, :precioNueve, :partidaDiez, :precioDiez, :total, :estado, :fecha_ingreso, :marcaDelEquipo, :modeloDelEquipo, :numeroDeSerieDelEquipo)");

//:marcaDelEquipo, :modeloDelEquipo, :numeroDeSerieDelEquipo

//marcaDelEquipo, modeloDelEquipo, numeroDeSerieDelEquipo

		$stmt->bindParam(":ruta", $datos["ruta"], PDO::PARAM_STR);

		$stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);

		$stmt->bindParam(":fecha_ingreso", $datos["fecha_ingreso"], PDO::PARAM_STR);

		$stmt->bindParam(":id_empresa", $datos["empresa"], PDO::PARAM_STR);

		//$stmt->bindParam(":id_creador", $datos["creador"], PDO::PARAM_INT);

		$stmt->bindParam(":multimedia", $datos["multimedia"], PDO::PARAM_STR);

		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);

		$stmt->bindParam(":portada", $datos["imgFotoPrincipal"], PDO::PARAM_STR);

		$stmt->bindParam(":id_tecnico", $datos["tecnico"], PDO::PARAM_INT);

		$stmt->bindParam(":id_Asesor", $datos["asesor"], PDO::PARAM_INT);

		$stmt->bindParam(":id_usuario", $datos["cliente"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaUno", $datos["partida1"], PDO::PARAM_STR);

		$stmt->bindParam(":precioUno", $datos["precio1"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaDos", $datos["partida2"], PDO::PARAM_STR);

		$stmt->bindParam(":precioDos", $datos["precio2"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaTres", $datos["partida3"], PDO::PARAM_STR);

		$stmt->bindParam(":precioTres", $datos["precio3"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaCuatro", $datos["partida4"], PDO::PARAM_STR);

		$stmt->bindParam(":precioCuatro", $datos["precio4"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaCinco", $datos["partida5"], PDO::PARAM_STR);

		$stmt->bindParam(":precioCinco", $datos["precio5"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaSeis", $datos["partida6"], PDO::PARAM_STR);

		$stmt->bindParam(":precioSeis", $datos["precio6"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaSiete", $datos["partida7"], PDO::PARAM_STR);

		$stmt->bindParam(":precioSiete", $datos["precio7"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaOcho", $datos["partida8"], PDO::PARAM_STR);

		$stmt->bindParam(":precioOcho", $datos["precio8"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaNueve", $datos["partida9"], PDO::PARAM_STR);

		$stmt->bindParam(":precioNueve", $datos["precio9"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaDiez", $datos["partida10"], PDO::PARAM_STR);

		$stmt->bindParam(":precioDiez", $datos["precio10"], PDO::PARAM_INT);

		$stmt->bindParam(":total", $datos["totalOrden"], PDO::PARAM_STR);

		$stmt->bindParam(":estado", $datos["status"], PDO::PARAM_STR);

		$stmt->bindParam(":marcaDelEquipo", $datos["marcaDelEquipo"], PDO::PARAM_STR);

		$stmt->bindParam(":modeloDelEquipo", $datos["modeloDelEquipo"], PDO::PARAM_STR);

		$stmt->bindParam(":numeroDeSerieDelEquipo", $datos["numeroDeSerieDelEquipo"], PDO::PARAM_STR);



		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}







	}



	/*=============================================

	EDITAR ORDEN

	=============================================*/



	static public function mdlEditarOrden($tabla, $datos){



		$stmt = ConexionWP::conectarWP()->prepare("UPDATE $tabla SET id_tecnico = :id_tecnico, id_Asesor = :id_Asesor, id_usuario = :id_usuario, estado = :estado, ruta = :ruta, descripcion = :descripcion, titulo = :titulo, partidaUno = :partidaUno, precioUno = :precioUno, partidaDos = :partidaDos, precioDos = :precioDos, partidaTres = :partidaTres,  precioTres = :precioTres, partidaCuatro = :partidaCuatro, precioCuatro = :precioCuatro, partidaCinco = :partidaCinco, precioCinco = :precioCinco, partidaSeis = :partidaSeis, precioSeis = :precioSeis, partidaSiete = :partidaSiete, precioSiete = :precioSiete, partidaOcho = :partidaOcho, precioOcho = :precioOcho, partidaNueve = :partidaNueve, precioNueve = :precioNueve, partidaDiez = :partidaDiez, precioDiez = :precioDiez, multimedia = :multimedia, portada = :portada, total = :total, id_pedido = :id_pedido WHERE id = :id");



		$stmt->bindParam(":id_pedido", $datos["seleccionarPedido"], PDO::PARAM_INT);

		$stmt->bindParam(":id_tecnico", $datos["idTecnico"], PDO::PARAM_INT);

		$stmt->bindParam(":id_Asesor", $datos["idAsesor"], PDO::PARAM_INT);

		$stmt->bindParam(":id_usuario", $datos["idCliente"], PDO::PARAM_INT);

		$stmt->bindParam(":ruta", $datos["rutaOrden"], PDO::PARAM_STR);

		$stmt->bindParam(":descripcion", $datos["descripcionOrden"], PDO::PARAM_STR);

		$stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);

		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);



		$stmt->bindParam(":partidaUno", $datos["partida1"], PDO::PARAM_STR);

		$stmt->bindParam(":precioUno", $datos["precio1"], PDO::PARAM_INT);



		$stmt->bindParam(":partidaDos", $datos["partida2"], PDO::PARAM_STR);

		$stmt->bindParam(":precioDos", $datos["precio2"], PDO::PARAM_INT);



		$stmt->bindParam(":partidaTres", $datos["partida3"], PDO::PARAM_STR);

		$stmt->bindParam(":precioTres", $datos["precio3"], PDO::PARAM_INT);

		

		$stmt->bindParam(":partidaCuatro", $datos["partida4"], PDO::PARAM_STR);

		$stmt->bindParam(":precioCuatro", $datos["precio4"], PDO::PARAM_INT);



		$stmt->bindParam(":partidaCinco", $datos["partida5"], PDO::PARAM_STR);

		$stmt->bindParam(":precioCinco", $datos["precio5"], PDO::PARAM_INT);

		

		$stmt->bindParam(":partidaSeis", $datos["partida6"], PDO::PARAM_STR);

		$stmt->bindParam(":precioSeis", $datos["precio6"], PDO::PARAM_INT);





		$stmt->bindParam(":partidaSiete", $datos["partida7"], PDO::PARAM_STR);

		$stmt->bindParam(":precioSiete", $datos["precio7"], PDO::PARAM_INT);



		$stmt->bindParam(":partidaOcho", $datos["partida8"], PDO::PARAM_STR);

		$stmt->bindParam(":precioOcho", $datos["precio8"], PDO::PARAM_INT);



		$stmt->bindParam(":partidaNueve", $datos["partida9"], PDO::PARAM_STR);

		$stmt->bindParam(":precioNueve", $datos["precio9"], PDO::PARAM_INT);



		$stmt->bindParam(":partidaDiez", $datos["partida10"], PDO::PARAM_STR);

		$stmt->bindParam(":precioDiez", $datos["precio10"], PDO::PARAM_INT);



		$stmt->bindParam(":multimedia", $datos["multimedia"], PDO::PARAM_STR);

		



		$stmt->bindParam(":portada", $datos["imgFotoPrincipal"], PDO::PARAM_STR);



		$stmt->bindParam(":total", $datos["totalOrdenEditar"], PDO::PARAM_INT);

		$stmt -> bindParam(":id", $datos["id"], PDO::PARAM_INT);



		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}







	}



	/*=============================================

	EDITAR FECHA DE SALIDA

	=============================================*/



	static public function mdlEditarFechaSalida($tabla, $datos){



		$stmt = ConexionWP::conectarWP()->prepare("UPDATE $tabla SET id_tecnico = :id_tecnico,id_tecnicoDos = :id_tecnicoDos, id_Asesor = :id_Asesor, partidaUno = :partidaUno, precioUno = :precioUno, partidaDos = :partidaDos, precioDos = :precioDos, partidaTres = :partidaTres,  precioTres = :precioTres, partidaCuatro = :partidaCuatro, precioCuatro = :precioCuatro, partidaCinco = :partidaCinco, precioCinco = :precioCinco, partidaSeis = :partidaSeis, precioSeis = :precioSeis, partidaSiete = :partidaSiete, precioSiete = :precioSiete, partidaOcho = :partidaOcho, precioOcho = :precioOcho, partidaNueve = :partidaNueve, precioNueve = :precioNueve, partidaDiez = :partidaDiez, precioDiez = :precioDiez, total = :total, partidas = :partidas,  inversiones = :inversiones, fecha_Salida = :fecha_Salida, totalInversion = :totalInversion  WHERE id = :id");

// ,  marcaDelEquipo = :marcaDelEquipo, modeloDelEquipo = :modeloDelEquipo, numeroDeSerieDelEquipo = :numeroDeSerieDelEquipo

		$stmt->bindParam(":fecha_Salida", $datos["fecha_Salida"], PDO::PARAM_STR);

		$stmt->bindParam(":id_tecnico", $datos["tecnico"], PDO::PARAM_INT);
		
		$stmt->bindParam(":id_tecnicoDos", $datos["tecnicodos"], PDO::PARAM_INT);

		$stmt->bindParam(":id_Asesor", $datos["asesor"], PDO::PARAM_INT);

		$stmt->bindParam(":partidas", $datos["listatOrdenes"], PDO::PARAM_STR);

		$stmt->bindParam(":inversiones", $datos["listarinversiones"], PDO::PARAM_STR);

		$stmt->bindParam(":totalInversion", $datos["totalInversiones"], PDO::PARAM_STR);


		$stmt->bindParam(":partidaUno", $datos["partidaUno"], PDO::PARAM_STR);

		$stmt->bindParam(":precioUno", $datos["precioUno"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaDos", $datos["partidaDos"], PDO::PARAM_STR);

		$stmt->bindParam(":precioDos", $datos["precioDos"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaTres", $datos["partidaTres"], PDO::PARAM_STR);

		$stmt->bindParam(":precioTres", $datos["precioTres"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaCuatro", $datos["partidaCuatro"], PDO::PARAM_STR);

		$stmt->bindParam(":precioCuatro", $datos["precioCuatro"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaCinco", $datos["partidaCinco"], PDO::PARAM_STR);

		$stmt->bindParam(":precioCinco", $datos["precioCinco"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaSeis", $datos["partidaSeis"], PDO::PARAM_STR);

		$stmt->bindParam(":precioSeis", $datos["precioSeis"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaSiete", $datos["partidaSiete"], PDO::PARAM_STR);

		$stmt->bindParam(":precioSiete", $datos["precioSiete"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaOcho", $datos["partidaOcho"], PDO::PARAM_STR);

		$stmt->bindParam(":precioOcho", $datos["precioOcho"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaNueve", $datos["partidaNueve"], PDO::PARAM_STR);

		$stmt->bindParam(":precioNueve", $datos["precioNueve"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaDiez", $datos["partidaDiez"], PDO::PARAM_STR);

		$stmt->bindParam(":precioDiez", $datos["precioDiez"], PDO::PARAM_INT);

		$stmt->bindParam(":total", $datos["costoTotalDeOrden"], PDO::PARAM_INT);

		

		//$stmt->bindParam(":marcaDelEquipo", $datos["marcaDelEquipo"], PDO::PARAM_STR);

		//$stmt->bindParam(":modeloDelEquipo", $datos["modeloDelEquipo"], PDO::PARAM_STR);

		//$stmt->bindParam(":numeroDeSerieDelEquipo", $datos["numeroDeSerieDelEquipo"], PDO::PARAM_STR);

		

		$stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);

		

		



		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}







	}

	

	

	/*=============================================

	ELIMINAR PRODUCTO

	=============================================*/



	static public function mdlEliminarOrden($tabla, $datos){



		$stmt = ConexionWP::conectarWP()->prepare("DELETE FROM $tabla WHERE id = :id");



		$stmt -> bindParam(":id", $datos, PDO::PARAM_INT);



		if($stmt -> execute()){



			return "ok";

		

		}else{



			return "error";	



		}









	}



	public function mdlIngresarObservacion($tabla, $datos){

		

		$stmt = ConexionWP::conectarWP()->prepare("INSERT INTO $tabla(id_creador, id_orden, observacionUno, observacionDos, observacionTres, observacionCuatro, observacionCinco, observacionSeis, observacionSiete, observacionOcho, observacionNueve, observacionDiez) VALUES (:id_creador, :id_orden, :observacionUno, :observacionDos, :observacionTres, :observacionCuatro, :observacionCinco, :observacionSeis, :observacionSiete, :observacionOcho, :observacionNueve, :observacionDiez)");



		$stmt->bindParam(":id_creador", $datos["creador"], PDO::PARAM_INT);

		$stmt->bindParam(":id_orden", $datos["idOrdenObservacion"], PDO::PARAM_INT);

		$stmt->bindParam(":observacionUno", $datos["observacion1"], PDO::PARAM_STR);

		$stmt->bindParam(":observacionDos", $datos["observacion2"], PDO::PARAM_STR);

		$stmt->bindParam(":observacionTres", $datos["observacion3"], PDO::PARAM_STR);

		$stmt->bindParam(":observacionCuatro", $datos["observacion4"], PDO::PARAM_STR);

		$stmt->bindParam(":observacionCinco", $datos["observacion5"], PDO::PARAM_STR);

		$stmt->bindParam(":observacionSeis", $datos["observacion6"], PDO::PARAM_STR);

		$stmt->bindParam(":observacionSiete", $datos["observacion7"], PDO::PARAM_STR);

		$stmt->bindParam(":observacionOcho", $datos["observacion8"], PDO::PARAM_STR);

		$stmt->bindParam(":observacionNueve", $datos["observacion9"], PDO::PARAM_STR);

		$stmt->bindParam(":observacionDiez", $datos["observacion10"], PDO::PARAM_STR);

		



		if($stmt->execute()){



			return "ok";	



		}else{



			return "error";

		

		}




		


	}



	/*=============================================

	RANGO FECHAS POR EMPRESA

	=============================================*/	



	static public function mdlRangoFechasOrdenesPorEmpresa($tabla, $fechaInicial, $fechaFinal, $itemUno, $valorUno){

		if($fechaInicial == null){

			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE $itemUno = :emp ORDER BY id DESC");
			$stmt->bindParam(":emp", $valorUno, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll();

		}else{

			$fechaFinal2 = new DateTime($fechaFinal);
			$fechaFinal2->add(new DateInterval("P1D"));
			$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");

			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha >= :fi AND fecha < :ff AND $itemUno = :emp ORDER BY id DESC");
			$stmt->bindParam(":fi", $fechaInicial, PDO::PARAM_STR);
			$stmt->bindParam(":ff", $fechaFinalMasUno, PDO::PARAM_STR);
			$stmt->bindParam(":emp", $valorUno, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll();

		}

	}

	/*=============================================

	RANGO FECHAS PARA SUPER ADMIN

	=============================================*/	

	static public function mdlRangoFechasOrdenesParSuperAdmin($tabla, $fechaInicial, $fechaFinal){

		if($fechaInicial == null){

			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla ORDER BY id DESC");
			$stmt -> execute();
			return $stmt -> fetchAll();

		}else{

			// Siempre sumar 1 día al final para incluir registros del último día completo
			$fechaFinal2 = new DateTime($fechaFinal);
			$fechaFinal2->add(new DateInterval("P1D"));
			$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");

			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha >= :fi AND fecha < :ff ORDER BY id DESC");
			$stmt->bindParam(":fi", $fechaInicial, PDO::PARAM_STR);
			$stmt->bindParam(":ff", $fechaFinalMasUno, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetchAll();

		}

	}

	/*=============================================

	RANGO FECHAS

	=============================================*/	

	static public function mdlRangoFechasOrdenes($tabla, $fechaInicial, $fechaFinal, $itemUno, $valorUno){

		if($fechaInicial == null){

			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE $itemUno = :emp ORDER BY id DESC");
			$stmt->bindParam(":emp", $valorUno, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll();

		}else{

			// Siempre sumar 1 día al final para incluir registros del último día completo
			$fechaFinal2 = new DateTime($fechaFinal);
			$fechaFinal2->add(new DateInterval("P1D"));
			$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");

			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha >= :fi AND fecha < :ff AND $itemUno = :emp ORDER BY id DESC");
			$stmt->bindParam(":fi", $fechaInicial, PDO::PARAM_STR);
			$stmt->bindParam(":ff", $fechaFinalMasUno, PDO::PARAM_STR);
			$stmt->bindParam(":emp", $valorUno, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll();

		}

	}

	/*=============================================

	RANGO FECHAS ORDENES ENTREGADAS

	=============================================*/	



	static public function mdlRangoFechasOrdenesENT($tabla, $fechaInicial, $fechaFinal, $itemEmpresa, $valorEmpresa){



		if($fechaInicial == null){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE estado = 'Entregado (Ent)' AND $itemEmpresa = $valorEmpresa ORDER BY fecha_Salida DESC");



			$stmt -> execute();



			return $stmt -> fetchAll();	





		}else if($fechaInicial == $fechaFinal){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha_Salida like '%$fechaFinal%'  AND estado = 'Entregado (Ent)' AND $itemEmpresa = :$itemEmpresa ORDER BY fecha_Salida DESC");



			$stmt -> bindParam(":fecha_Salida", $fechaFinal, PDO::PARAM_STR);

			$stmt -> bindParam(":".$itemEmpresa, $valorEmpresa, PDO::PARAM_STR);



			$stmt -> execute();



			return $stmt -> fetchAll();



		}else{



			$fechaActual = new DateTime();

			$fechaActual ->add(new DateInterval("P1D"));

			$fechaActualMasUno = $fechaActual->format("Y-m-d");



			$fechaFinal2 = new DateTime($fechaFinal);

			$fechaFinal2 ->add(new DateInterval("P1D"));

			$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");



			if($fechaFinalMasUno == $fechaActualMasUno){



				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha_Salida BETWEEN '$fechaInicial' AND '$fechaFinalMasUno' AND estado = 'Entregado (Ent)' AND $itemEmpresa = $valorEmpresa ORDER BY fecha_Salida DESC");



			}else{





				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha_Salida BETWEEN '$fechaInicial' AND '$fechaFinal' AND estado = 'Entregado (Ent)' AND $itemEmpresa = $valorEmpresa ORDER BY fecha_Salida DESC");



			}

		

			$stmt -> execute();



			return $stmt -> fetchAll();



		}



	}



	/*=============================================

	RANGO FECHAS ORDENES ACEPTADAS OK

	=============================================*/	



	static public function mdlRangoFechasOrdenesOk($tabla, $fechaInicial, $fechaFinal, $itemEmpresa, $valorEmpresa){



		if($fechaInicial == null){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE estado = 'Aceptado (ok)' AND $itemEmpresa = $valorEmpresa ORDER BY id DESC");



			$stmt -> execute();



			return $stmt -> fetchAll();	





		}else if($fechaInicial == $fechaFinal){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha like '%$fechaFinal%'  AND estado = 'Aceptado (ok)' AND $itemEmpresa = :$itemEmpresa ORDER BY id DESC");



			$stmt -> bindParam(":fecha", $fechaFinal, PDO::PARAM_STR);

			$stmt -> bindParam(":".$itemEmpresa, $valorEmpresa, PDO::PARAM_STR);



			$stmt -> execute();



			return $stmt -> fetchAll();



		}else{



			$fechaActual = new DateTime();

			$fechaActual ->add(new DateInterval("P1D"));

			$fechaActualMasUno = $fechaActual->format("Y-m-d");



			$fechaFinal2 = new DateTime($fechaFinal);

			$fechaFinal2 ->add(new DateInterval("P1D"));

			$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");



			if($fechaFinalMasUno == $fechaActualMasUno){



				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinalMasUno' AND estado = 'Aceptado (ok)' AND $itemEmpresa = $valorEmpresa ORDER BY id DESC");



			}else{





				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinal' AND estado = 'Aceptado (ok)' AND $itemEmpresa = $valorEmpresa ORDER BY id DESC");



			}

		

			$stmt -> execute();



			return $stmt -> fetchAll();



		}



	}



	/*=============================================

	RANGO FECHAS ORDENES ACEPTADAS TER

	=============================================*/	



	static public function mdlRangoFechasOrdenesTer($tabla, $fechaInicial, $fechaFinal, $itemEmpresa, $valorEmpresa){



		if($fechaInicial == null){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE estado = 'Terminada (ter)' AND $itemEmpresa = $valorEmpresa ORDER BY id DESC");



			$stmt -> execute();



			return $stmt -> fetchAll();	





		}else if($fechaInicial == $fechaFinal){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha like '%$fechaFinal%'  AND estado = 'Terminada (ter)' AND $itemEmpresa = :$itemEmpresa ORDER BY id DESC");



			$stmt -> bindParam(":fecha", $fechaFinal, PDO::PARAM_STR);

			$stmt -> bindParam(':'.$itemEmpresa, $valorEmpresa, PDO::PARAM_STR);



			$stmt -> execute();



			return $stmt -> fetchAll();



		}else{



			$fechaActual = new DateTime();

			$fechaActual ->add(new DateInterval("P1D"));

			$fechaActualMasUno = $fechaActual->format("Y-m-d");



			$fechaFinal2 = new DateTime($fechaFinal);

			$fechaFinal2 ->add(new DateInterval("P1D"));

			$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");



			if($fechaFinalMasUno == $fechaActualMasUno){



				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinalMasUno' AND estado = 'Terminada (ter)' AND $itemEmpresa = $valorEmpresa ORDER BY id DESC");



			}else{





				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinal' AND estado = 'Terminada (ter)' AND $itemEmpresa = $valorEmpresa");



			}

		

			$stmt -> execute();



			return $stmt -> fetchAll();



		}



	}



	/*=============================================

	RANGO FECHAS ORDENES ACEPTADAS AUT

	=============================================*/	



	static public function mdlRangoFechasOrdenesAut($tabla, $fechaInicial, $fechaFinal, $itemEmpresa, $valorEmpresa){



		if($fechaInicial == null){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE estado = 'Pendiente de autorización (AUT' AND $itemEmpresa = $valorEmpresa ORDER BY id DESC");



			$stmt -> execute();



			return $stmt -> fetchAll();	





		}else if($fechaInicial == $fechaFinal){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha like '%$fechaFinal%'  AND estado = 'autorización (AUT' AND $itemEmpresa = :itemEmpresa ORDER BY id DESC");




			$stmt -> bindParam(":".$itemEmpresa, $valorEmpresa, PDO::PARAM_STR);



			$stmt -> execute();



			return $stmt -> fetchAll();



		}else{



			$fechaActual = new DateTime();

			$fechaActual ->add(new DateInterval("P1D"));

			$fechaActualMasUno = $fechaActual->format("Y-m-d");



			$fechaFinal2 = new DateTime($fechaFinal);

			$fechaFinal2 ->add(new DateInterval("P1D"));

			$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");



			if($fechaFinalMasUno == $fechaActualMasUno){



				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinalMasUno' AND estado = 'Pendiente de autorización (AUT'  AND $itemEmpresa = $valorEmpresa ORDER BY id DESC");



			}else{





				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinal' AND estado = 'Pendiente de autorización (AUT' AND $itemEmpresa = $valorEmpresa ORDER BY id DESC");



			}

		

			$stmt -> execute();



			return $stmt -> fetchAll();



		}



	}



	/*=============================================

	RANGO FECHAS ORDENES ACEPTADAS SUP

	=============================================*/	



	static public function mdlRangoFechasOrdenesSup($tabla, $fechaInicial, $fechaFinal, $itemEmpresa, $valorEmpresa){



		if($fechaInicial == null){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE estado = 'Supervisión (SUP)' AND $itemEmpresa = $valorEmpresa ORDER BY id DESC");



			$stmt -> execute();



			return $stmt -> fetchAll();	





		}else if($fechaInicial == $fechaFinal){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha like '%$fechaFinal%'  AND estado = 'Supervisión (SUP)' AND $itemEmpresa = :$itemEmpresa ORDER BY id DESC");



			$stmt -> bindParam(":fecha", $fechaFinal, PDO::PARAM_STR);

			$stmt -> bindParam(":".$itemEmpresa, $valorEmpresa, PDO::PARAM_STR);



			$stmt -> execute();



			return $stmt -> fetchAll();



		}else{



			$fechaActual = new DateTime();

			$fechaActual ->add(new DateInterval("P1D"));

			$fechaActualMasUno = $fechaActual->format("Y-m-d");



			$fechaFinal2 = new DateTime($fechaFinal);

			$fechaFinal2 ->add(new DateInterval("P1D"));

			$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");



			if($fechaFinalMasUno == $fechaActualMasUno){



				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinalMasUno' AND estado = 'Supervicion (sup)' AND $itemEmpresa = $valorEmpresa ORDER BY id DESC");



			}else{





				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinal' AND estado = 'Supervicion (sup)' AND $itemEmpresa = $valorEmpresa ORDER BY id DESC");



			}

		

			$stmt -> execute();



			return $stmt -> fetchAll();



		}



	}

	/*=============================================

	RANGO FECHAS ORDENES PENDIENTES DE REVISION

	=============================================*/	



	static public function mdlRangoFechasOrdenesPenR($tabla, $fechaInicial, $fechaFinal, $itemEmpresa, $valorEmpresa){



		if($fechaInicial == null){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE estado = 'En revisión (REV)' AND $itemEmpresa = $valorEmpresa ORDER BY id DESC");



			$stmt -> execute();



			return $stmt -> fetchAll();	



		}else if($fechaInicial == $fechaFinal){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha like '%$fechaFinal%' AND estado = 'En revisión (REV)' AND $itemEmpresa = :$itemEmpresa ORDER BY id DESC");



			$stmt -> bindParam(":fecha", $fechaFinal, PDO::PARAM_STR);

			$stmt -> bindParam(":".$itemEmpresa, $valorEmpresa, PDO::PARAM_STR);



			$stmt -> execute();



			return $stmt -> fetchAll();



		}else{



			$fechaActual = new DateTime();

			$fechaActual ->add(new DateInterval("P1D"));

			$fechaActualMasUno = $fechaActual->format("Y-m-d");



			$fechaFinal2 = new DateTime($fechaFinal);

			$fechaFinal2 ->add(new DateInterval("P1D"));

			$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");



			if($fechaFinalMasUno == $fechaActualMasUno){



				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinalMasUno' AND estado = 'En revisión (REV)' AND $itemEmpresa = $valorEmpresa ORDER BY id DESC");



			}else{





				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinal' AND estado = 'En revisión (REV)' AND $itemEmpresa = $valorEmpresa ORDER BY id DESC");



			}

		

			$stmt -> execute();



			return $stmt -> fetchAll();



		}



	}



	/*=============================================

	SELECCIONAR ORDENES POR ESTATUS

	=============================================*/

	

	static public function mdlMostrarOrdenesPorEstado($tabla, $estado, $item, $valor){



		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE estado = '$estado' AND $item = $valor ORDER BY id DESC");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}



	/*=============================================

	SELECCIONAR ORDENES POR ESTATUS Y DE LA EMPRESA CORRESPODNIENTE

	=============================================*/

	

	static public function mdlMostrarOrdenesPorEstadoyEmpresa($tabla, $estado, $valorEmpresa){



		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE estado = '$estado' AND id_empresa = $valorEmpresa");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}

	/*=============================================

	SUMAR EL TOTAL DE LAS DE PAGOS POR ORDENES

	=============================================*/

	static public function mdlSumarTotalOrdenes($tabla, $fechaInicial, $fechaFinal, $valorEmpresa, $estado){

		

	if($fechaInicial == null){



				$stmt = ConexionWP::conectarWP()->prepare("SELECT SUM(total) as total FROM $tabla WHERE id_empresa = $valorEmpresa AND estado = '$estado'");



				$stmt -> execute();



				return $stmt -> fetchAll();	





			}else if($fechaInicial == $fechaFinal){



				$stmt = ConexionWP::conectarWP()->prepare("SELECT SUM(total) as total FROM $tabla WHERE fecha like '%$fechaFinal%' AND 

					id_empresa = $valorEmpresa AND estado = '$estado'");



				$stmt -> bindParam(":fecha", $fechaFinal, PDO::PARAM_STR);



				$stmt -> execute();



				return $stmt -> fetchAll();



			}else{



				$fechaActual = new DateTime();

				$fechaActual ->add(new DateInterval("P1D"));

				$fechaActualMasUno = $fechaActual->format("Y-m-d");



				$fechaFinal2 = new DateTime($fechaFinal);

				$fechaFinal2 ->add(new DateInterval("P1D"));

				$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");



				if($fechaFinalMasUno == $fechaActualMasUno){



					$stmt = ConexionWP::conectarWP()->prepare("SELECT SUM(total) as total FROM $tabla WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinalMasUno' AND id_empresa = $valorEmpresa AND estado = '$estado'");



				}else{





					$stmt = ConexionWP::conectarWP()->prepare("SELECT SUM(total) as total FROM $tabla  WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinal' AND id_empresa = $valorEmpresa AND estado = '$estado'");



				}

			

				$stmt -> execute();



				return $stmt -> fetchAll();



			}



		}



/*=============================================

	SUMAR EL TOTAL DE LAS DE PAGOS POR ORDENES EN GENERAL

	=============================================*/

	static public function mdlSumarTotalOrdenesGeneral($tabla, $fechaInicial, $fechaFinal, $valorEmpresa){

		

	if($fechaInicial == null){



				$stmt = ConexionWP::conectarWP()->prepare("SELECT SUM(total) as total FROM $tabla WHERE id_empresa = $valorEmpresa");



				$stmt -> execute();



				return $stmt -> fetchAll();	





			}else if($fechaInicial == $fechaFinal){



				$stmt = ConexionWP::conectarWP()->prepare("SELECT SUM(total) as total FROM $tabla WHERE fecha like '%$fechaFinal%' AND 

					id_empresa = $valorEmpresa");



				$stmt -> bindParam(":fecha", $fechaFinal, PDO::PARAM_STR);



				$stmt -> execute();



				return $stmt -> fetchAll();



			}else{



				$fechaActual = new DateTime();

				$fechaActual ->add(new DateInterval("P1D"));

				$fechaActualMasUno = $fechaActual->format("Y-m-d");



				$fechaFinal2 = new DateTime($fechaFinal);

				$fechaFinal2 ->add(new DateInterval("P1D"));

				$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");



				if($fechaFinalMasUno == $fechaActualMasUno){



					$stmt = ConexionWP::conectarWP()->prepare("SELECT SUM(total) as total FROM $tabla WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinalMasUno' AND id_empresa = $valorEmpresa");



				}else{





					$stmt = ConexionWP::conectarWP()->prepare("SELECT SUM(total) as total FROM $tabla  WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinal' AND id_empresa = $valorEmpresa");



				}

			

				$stmt -> execute();



				return $stmt -> fetchAll();



			}



		}



	/*=============================================

	EDITAR PEDIDO

	=============================================*/



	static public function mdlEditarPedidoEnOrden($tabla, $datos){



		$stmt = ConexionWP::conectarWP()->prepare("UPDATE $tabla SET estado = :estado WHERE id = :id");



		$stmt->bindParam(":estado", $datos["EstadoDelPedido"], PDO::PARAM_STR);

		$stmt -> bindParam(":id", $datos["idPedido"], PDO::PARAM_INT);



		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}







	}





 	/*=============================================

	EDITAR ORDEN

	=============================================*/

	static public function mdlEditarOrdenDinamica($tabla, $datos){



		$stmt = ConexionWP::conectarWP()->prepare("UPDATE $tabla SET id_tecnico = :id_tecnico,id_tecnicoDos = :id_tecnicoDos, id_Asesor = :id_Asesor, estado = :estado, partidaUno = :partidaUno, precioUno = :precioUno, partidaDos = :partidaDos, precioDos = :precioDos, partidaTres = :partidaTres,  precioTres = :precioTres, partidaCuatro = :partidaCuatro, precioCuatro = :precioCuatro, partidaCinco = :partidaCinco, precioCinco = :precioCinco, partidaSeis = :partidaSeis, precioSeis = :precioSeis, partidaSiete = :partidaSiete, precioSiete = :precioSiete, partidaOcho = :partidaOcho, precioOcho = :precioOcho, partidaNueve = :partidaNueve, precioNueve = :precioNueve, partidaDiez = :partidaDiez, precioDiez = :precioDiez, total = :total, partidas = :partidas,  inversiones = :inversiones, totalInversion = :totalInversion WHERE id = :id");

//marcaDelEquipo = :marcaDelEquipo, modeloDelEquipo = :modeloDelEquipo, numeroDeSerieDelEquipo = :numeroDeSerieDelEquipo

		$stmt->bindParam(":id_tecnico", $datos["tecnico"], PDO::PARAM_INT);
		
		$stmt->bindParam(":id_tecnicoDos", $datos["tecnicodos"], PDO::PARAM_INT);

		$stmt->bindParam(":id_Asesor", $datos["asesor"], PDO::PARAM_INT);

		$stmt->bindParam(":partidas", $datos["listatOrdenes"], PDO::PARAM_STR);

		$stmt->bindParam(":inversiones", $datos["listarinversiones"], PDO::PARAM_STR);

		$stmt->bindParam(":totalInversion", $datos["totalInversiones"], PDO::PARAM_STR);


		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

		$stmt->bindParam(":partidaUno", $datos["partidaUno"], PDO::PARAM_STR);

		$stmt->bindParam(":precioUno", $datos["precioUno"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaDos", $datos["partidaDos"], PDO::PARAM_STR);

		$stmt->bindParam(":precioDos", $datos["precioDos"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaTres", $datos["partidaTres"], PDO::PARAM_STR);

		$stmt->bindParam(":precioTres", $datos["precioTres"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaCuatro", $datos["partidaCuatro"], PDO::PARAM_STR);

		$stmt->bindParam(":precioCuatro", $datos["precioCuatro"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaCinco", $datos["partidaCinco"], PDO::PARAM_STR);

		$stmt->bindParam(":precioCinco", $datos["precioCinco"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaSeis", $datos["partidaSeis"], PDO::PARAM_STR);

		$stmt->bindParam(":precioSeis", $datos["precioSeis"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaSiete", $datos["partidaSiete"], PDO::PARAM_STR);

		$stmt->bindParam(":precioSiete", $datos["precioSiete"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaOcho", $datos["partidaOcho"], PDO::PARAM_STR);

		$stmt->bindParam(":precioOcho", $datos["precioOcho"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaNueve", $datos["partidaNueve"], PDO::PARAM_STR);

		$stmt->bindParam(":precioNueve", $datos["precioNueve"], PDO::PARAM_INT);

		$stmt->bindParam(":partidaDiez", $datos["partidaDiez"], PDO::PARAM_STR);

		$stmt->bindParam(":precioDiez", $datos["precioDiez"], PDO::PARAM_INT);

		$stmt->bindParam(":total", $datos["costoTotalDeOrden"], PDO::PARAM_INT);

		

	//	$stmt->bindParam(":marcaDelEquipo", $datos["marcaDelEquipo"], PDO::PARAM_STR);

	//	$stmt->bindParam(":modeloDelEquipo", $datos["modeloDelEquipo"], PDO::PARAM_STR);

	//	$stmt->bindParam(":numeroDeSerieDelEquipo", $datos["numeroDeSerieDelEquipo"], PDO::PARAM_STR);



		$stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);



		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}







	}

	/*=============================================

	EDITAR OBSERVACIONES YA EXISTENTES EN ORDEN

	=============================================*/



	static public function mdlEditarObservacionesYaExistentes($tabla, $datos){



		$stmt = ConexionWP::conectarWP()->prepare("UPDATE $tabla SET  descripcion = :descripcion, observaciones = :observaciones WHERE id = :id");



		$stmt->bindParam(":descripcion", $datos["observaciones"], PDO::PARAM_STR);

		$stmt->bindParam(":observaciones", $datos["listarObservaciones"], PDO::PARAM_STR);

		$stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);



		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}







	}

	/*=============================================

	EDITAR inversion

	=============================================*/



	static public function mdlEditarInversiones($tabla, $datos){



		$stmt = ConexionWP::conectarWP()->prepare("UPDATE $tabla SET  inversiones = :inversiones, totalInversion = :totalInversion, estado = :estado WHERE id = :id");



		$stmt->bindParam(":inversiones", $datos["listarinversiones"], PDO::PARAM_STR);

		$stmt->bindParam(":totalInversion", $datos["totalInversiones"], PDO::PARAM_STR);

		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

		$stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);



		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}







	}



	/*=============================================

	BUSCAR ORDENES

	=============================================*/

	static public function mdlBuscarOrdenes($tabla,  $consulta){



		$stmt = ConexionWP::conectarWP()->prepare("SELECT id, id_empresa, id_tecnico, id_usuario, estado, fecha_ingreso, fecha, fecha_Salida, id_Asesor,id_pedido, total FROM $tabla WHERE id like '%$consulta%'");





		$stmt -> execute();



		return $stmt -> fetchAll();









	}
	/*=============================================

	BUSCAR ORDENES

	=============================================*/

	static public function mdlBuscarOrdenesNew($tabla,  $busqueda){



		$stmt = ConexionWP::conectarWP()->prepare("SELECT id, id_empresa, id_tecnico, id_usuario, estado, fecha_ingreso, fecha, fecha_Salida, id_Asesor,id_pedido, total FROM $tabla WHERE id like '%$busqueda%'");

		$stmt -> execute();

		return $stmt -> fetchAll();





	}



	static public function mdlIngresarPedidoDinamico($tabla, $datos){

		

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(id_empresa, id_Asesor, id_cliente, productos, total, estado, pagos, adeudo, id_orden) VALUES (:id_empresa, :id_Asesor, :id_cliente, :productos, :total, :estado, :pagos, :adeudo, :id_orden)");



		$stmt->bindParam(":id_empresa", $datos["empresa"], PDO::PARAM_INT);

		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

		$stmt->bindParam(":pagos", $datos["pago"], PDO::PARAM_STR);

		$stmt->bindParam(":adeudo", $datos["adeudo"], PDO::PARAM_STR);

		$stmt->bindParam(":id_Asesor", $datos["asesor"], PDO::PARAM_INT);

		$stmt->bindParam(":id_cliente", $datos["cliente"], PDO::PARAM_INT);

		$stmt->bindParam(":productos", $datos["productos"], PDO::PARAM_STR);

		$stmt->bindParam(":total", $datos["total"], PDO::PARAM_STR);

		$stmt->bindParam(":id_orden", $datos["id_orden"], PDO::PARAM_INT);

		



		if($stmt->execute()){



			return "ok";	



		}else{



			return "error";

		

		}




		


	}	



	/*=============================================

	MOSTRAR ORDENES DEL ASESOR EN SESSION

	=============================================*/

	static public function mdlMostrarOrdenesConAtraso($id_Asesor,$tabla){

		

		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE id_Asesor = $id_Asesor");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}

	/*=============================================

	MOSTRAR ORDENES DEL TECNICO EN SESSION

	=============================================*/

	static public function mdlMostrarOrdenesDelTecnico($id_tecnico,$tabla){

		

		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE (id_tecnico = $id_tecnico OR id_tecnicoDos = $id_tecnico) ORDER BY id DESC");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}

	/*=============================================

	LISTAR ORDENES MES ENTREGADAS

	=============================================*/	

	static public function mdlListarOrdenes($tabla){

		

		$stmt = ConexionWP::conectarWP()->prepare("SELECT count(*) FROM $tabla WHERE `estado` = 'Entregado (Ent)' AND MONTH(`fecha_Salida`) = MONTH(NOW()) AND YEAR(`fecha_Salida`) = YEAR(NOW())");

			

		$stmt -> execute();



		return $stmt -> fetchAll();







	}
	
		/*=============================================

	LISTAR ORDENES MES ASESOR ENTREGADAS

	=============================================*/	

	static public function mdlListarOrdenesAsesor($tabla,$idAsesor){

		

		$stmt = ConexionWP::conectarWP()->prepare("SELECT count(*) FROM $tabla WHERE `estado` = 'Entregado (Ent)' AND MONTH(`fecha_Salida`) = MONTH(NOW()) AND YEAR(`fecha_Salida`) = YEAR(NOW()) AND `id_Asesor` = $idAsesor");

			

		$stmt -> execute();



		return $stmt -> fetchAll();







	}
	
	static public function mdlMostrarOrdenesEntrada($tabla){

		

		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE MONTH(`fecha_ingreso`) = MONTH(NOW()) AND YEAR(`fecha_ingreso`) = YEAR(NOW())");

			

		$stmt -> execute();



		return $stmt -> fetchAll();







	}
	
		static public function mdlMostrarOrdenesEntradaAsesor($tabla,$idAsesor){

		

		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE MONTH(`fecha_ingreso`) = MONTH(NOW()) AND YEAR(`fecha_ingreso`) = YEAR(NOW()) AND `id_Asesor` = $idAsesor");

			

		$stmt -> execute();



		return $stmt -> fetchAll();







	}



	static public function mdlMostrarOrdenesConTope($tabla, $base, $tope){



		$stmt = ConexionWP::conectarWP()->prepare("SELECT *FROM $tabla LIMIT $base, $tope");



		$stmt -> execute();



		return $stmt -> fetchAll();











	}

	/*=============================================

	AGREGAR TIPO DE REPARACION

	=============================================*/



	static public function mdlIngresartipoDeRepacion($tabla, $idOrden, $tipo_de_reparacion){



		$stmt = ConexionWP::conectarWP()->prepare("UPDATE $tabla SET tipoDeReparacion = :tipoDeReparacion WHERE id = :id");



		$stmt->bindParam(":tipoDeReparacion", $tipo_de_reparacion, PDO::PARAM_STR);

		$stmt -> bindParam(":id", $idOrden, PDO::PARAM_INT);



		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}







	}



	/*=============================================

	AGREGAR RECARGA

	=============================================*/



	static public function mdlIngresarRecarga($tabla, $idOrden, $recarga, $precioRecarga){



		$stmt = ConexionWP::conectarWP()->prepare("UPDATE $tabla SET recargaCartucho = :recargaCartucho, totalRecargaDeCartucho = :totalRecargaDeCartucho WHERE id = :id");



		$stmt->bindParam(":recargaCartucho", $recarga, PDO::PARAM_STR);

		$stmt->bindParam(":totalRecargaDeCartucho", $precioRecarga, PDO::PARAM_STR);

		$stmt -> bindParam(":id", $idOrden, PDO::PARAM_INT);



		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}







	}



	/*=============================================

	RANGO FECHAS ORDENES ENTREGADAS DEL TECNICO

	=============================================*/	



	static public function mdlRangoFechasOrdenesENTTecnico($tabla, $id_tecnico, $fechaInicial, $fechaFinal){



		if($fechaInicial == null){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE estado = 'Entregado (Ent)' AND (id_tecnico = $id_tecnico OR id_tecnicoDos = $id_tecnico) ORDER BY id DESC");



			$stmt -> execute();



			return $stmt -> fetchAll();	





		}else if($fechaInicial == $fechaFinal){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha_Salida like '%$fechaFinal%' AND estado = 'Entregado (Ent)' AND (id_tecnico = $id_tecnico OR id_tecnicoDos = $id_tecnico) ORDER BY id DESC");



			$stmt -> bindParam(":fecha_Salida", $fechaFinal, PDO::PARAM_STR);



			$stmt -> execute();



			return $stmt -> fetchAll();



		}else{



			$fechaActual = new DateTime();

			$fechaActual ->add(new DateInterval("P1D"));

			$fechaActualMasUno = $fechaActual->format("Y-m-d");



			$fechaFinal2 = new DateTime($fechaFinal);

			$fechaFinal2 ->add(new DateInterval("P1D"));

			$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");



			if($fechaFinalMasUno == $fechaActualMasUno){



				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha_Salida BETWEEN '$fechaInicial' AND '$fechaFinalMasUno' AND estado = 'Entregado (Ent)' AND (id_tecnico = $id_tecnico OR id_tecnicoDos = $id_tecnico) ORDER BY id DESC");



			}else{





				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha_Salida BETWEEN '$fechaInicial' AND '$fechaFinal' AND estado = 'Entregado (Ent)' AND (id_tecnico = $id_tecnico OR id_tecnicoDos = $id_tecnico) ORDER BY id DESC");



			}

		

			$stmt -> execute();



			return $stmt -> fetchAll();



		}



	}



	public function mdlRangoFechasOrdenesENTTecnicoSinFecha($tabla, $estado, $id_tecnico){

	

		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE estado = '$estado' AND (id_tecnico = $id_tecnico OR id_tecnicoDos = $id_tecnico)");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}



	/*=============================================

	AGREGAR PARTIDAS TECNICO DOS

	=============================================*/



	static public function mdlIngresarPartidasTecnico($tabla, $idOrden, $partidasTecnicoDos, $TotalPartidasTecnicoDos){



		$stmt = ConexionWP::conectarWP()->prepare("UPDATE $tabla SET partidasTecnicoDos = :partidasTecnicoDos, TotalTecnicoDos = :TotalTecnicoDos WHERE id = :id");



		$stmt->bindParam(":partidasTecnicoDos", $partidasTecnicoDos, PDO::PARAM_STR);

		$stmt->bindParam(":TotalTecnicoDos", $TotalPartidasTecnicoDos, PDO::PARAM_STR);

		$stmt -> bindParam(":id", $idOrden, PDO::PARAM_INT);



		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}







	}



	/*=============================================

	AGREGAR PARTIDAS TECNICO

	=============================================*/



	static public function mdlIngresarTecnicoDos($tabla, $idOrden, $idTecnicoDos){



		$stmt = ConexionWP::conectarWP()->prepare("UPDATE $tabla SET id_tecnicoDos = :id_tecnicoDos WHERE id = :id");



		$stmt->bindParam(":id_tecnicoDos", $idTecnicoDos, PDO::PARAM_INT);

		$stmt -> bindParam(":id", $idOrden, PDO::PARAM_INT);



		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}







	}



	/*===========================================

	TRAER ORDENES ENTREGADAS DE ASESOR DE VENTAS

	=============================================*/



	static public function mdlRangoFechasOrdenesENTasesor($tabla, $id_Asesor, $fechaInicial, $fechaFinal){



		if($fechaInicial == null){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE estado = 'Entregado (Ent)' AND id_Asesor = $id_Asesor");



			$stmt -> execute();



			return $stmt -> fetchAll();	





		}else if($fechaInicial == $fechaFinal){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha_Salida like '%$fechaFinal%'  AND estado = 'Entregado (Ent)' AND id_Asesor = $id_Asesor");



			$stmt -> bindParam(":fecha_Salida", $fechaFinal, PDO::PARAM_STR);



			$stmt -> execute();



			return $stmt -> fetchAll();



		}else{



			$fechaActual = new DateTime();

			$fechaActual ->add(new DateInterval("P1D"));

			$fechaActualMasUno = $fechaActual->format("Y-m-d");



			$fechaFinal2 = new DateTime($fechaFinal);

			$fechaFinal2 ->add(new DateInterval("P1D"));

			$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");



			if($fechaFinalMasUno == $fechaActualMasUno){



				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha_Salida BETWEEN '$fechaInicial' AND '$fechaFinalMasUno' AND estado = 'Entregado (Ent)' AND id_Asesor = $id_Asesor");



			}else{





				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha_Salida BETWEEN '$fechaInicial' AND '$fechaFinal' AND estado = 'Entregado (Ent)' AND id_Asesor = $id_Asesor");



			}

		

			$stmt -> execute();



			return $stmt -> fetchAll();



		}



	}

	/*=============================================

	RANGO FECHAS INGRESOS DE ORDENES

	=============================================*/	



	static public function mdlRangoFechasOrdenesingresadas($tabla, $fechaInicial, $fechaFinal, $itemUno ,$valorEmpresa){



		if($fechaInicial == null){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla ORDER BY fecha_ingreso AND $itemUno = $valorEmpresa DESC");



			$stmt -> execute();



			return $stmt -> fetchAll();	





		}else if($fechaInicial == $fechaFinal){



			$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha_ingreso like '%$fechaFinal%' ORDER BY fecha_ingreso AND $itemUno = :$itemUno DESC");



			$stmt -> bindParam(":fecha_ingreso", $fechaFinal, PDO::PARAM_STR);

			$stmt -> bindParam(":".$itemUno, $valorEmpresa, PDO::PARAM_STR);



			$stmt -> execute();



			return $stmt -> fetchAll();



		}else{



			$fechaActual = new DateTime();

			$fechaActual ->add(new DateInterval("P1D"));

			$fechaActualMasUno = $fechaActual->format("Y-m-d");



			$fechaFinal2 = new DateTime($fechaFinal);

			$fechaFinal2 ->add(new DateInterval("P1D"));

			$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");



			if($fechaFinalMasUno == $fechaActualMasUno){



				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha_ingreso BETWEEN '$fechaInicial' AND '$fechaFinalMasUno' ORDER BY fecha_ingreso AND $itemUno = $valorEmpresa DESC");



			}else{





				$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE fecha_ingreso BETWEEN '$fechaInicial' AND '$fechaFinal' AND $itemUno = $valorEmpresa ORDER BY fecha_ingreso DESC");



			}

		

			$stmt -> execute();



			return $stmt -> fetchAll();



		}



	}



	/*=============================================

	SELECCIONAR ORDENES POR ESTATUS EMPRESA Y TECNICO

	=============================================*/

	

	static public function mdlMostrarOrdenesPorEstadoEmpresayTecnico($tabla, $estado, $item, $valor, $tecnico, $valorTecnico){



		$stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM $tabla WHERE estado = '$estado' AND $item = $valor AND ($tecnico = $valorTecnico OR id_tecnicoDos = $valorTecnico) ORDER BY id DESC");



		$stmt -> execute();



		return $stmt -> fetchAll();







	}



	/*=============================================

	EDITAR ORDEN

	=============================================*/



	static public function mdlEditarMarca($tabla, $datos){



		$stmt = ConexionWP::conectarWP()->prepare("UPDATE $tabla SET marcaDelEquipo = :marcaDelEquipo, modeloDelEquipo = :modeloDelEquipo, numeroDeSerieDelEquipo = :numeroDeSerieDelEquipo WHERE id = :id");



		$stmt->bindParam(":marcaDelEquipo", $datos["marcaDelEquipo"], PDO::PARAM_STR);

		$stmt->bindParam(":modeloDelEquipo", $datos["modeloDelEquipo"], PDO::PARAM_STR);

		$stmt->bindParam(":numeroDeSerieDelEquipo", $datos["numeroDeSerieDelEquipo"], PDO::PARAM_STR);

		$stmt -> bindParam(":id", $datos["id"], PDO::PARAM_INT);



		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}







	}

	/*=============================================
	DASHBOARD ASESOR — RESUMEN KPIs EN UNA SOLA CONSULTA
	=============================================*/

	static public function mdlDashboardKpisAsesor($idAsesor){

		$pdo = ConexionWP::conectarWP();
		$idAsesor = intval($idAsesor);

		$stmt = $pdo->prepare(
			"SELECT
				COALESCE(SUM(CASE WHEN `estado` = 'Entregado (Ent)'
					AND MONTH(`fecha_Salida`) = MONTH(NOW())
					AND YEAR(`fecha_Salida`) = YEAR(NOW()) THEN `total` END), 0) AS total_entregado,
				COALESCE(SUM(CASE WHEN `estado` = 'Entregado (Ent)'
					AND MONTH(`fecha_Salida`) = MONTH(NOW())
					AND YEAR(`fecha_Salida`) = YEAR(NOW()) THEN 1 END), 0) AS num_entregadas,
				COALESCE(SUM(CASE WHEN MONTH(`fecha_ingreso`) = MONTH(NOW())
					AND YEAR(`fecha_ingreso`) = YEAR(NOW()) THEN 1 END), 0) AS num_entradas
			FROM ordenes
			WHERE `id_Asesor` = :idAsesor"
		);

		$stmt->bindParam(":idAsesor", $idAsesor, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	/*=============================================
	DASHBOARD ASESOR — ORDENES POR ESTADO Y ASESOR
	=============================================*/

	static public function mdlOrdenesAsesorPorEstado($idEmpresa, $idAsesor, $estado){

		$pdo = ConexionWP::conectarWP();

		$stmt = $pdo->prepare(
			"SELECT * FROM ordenes
			WHERE `estado` LIKE :estado
			AND `id_empresa` = :idEmpresa
			AND `id_Asesor` = :idAsesor
			ORDER BY id DESC"
		);

		$stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
		$stmt->bindParam(":idEmpresa", $idEmpresa, PDO::PARAM_INT);
		$stmt->bindParam(":idAsesor", $idAsesor, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/*=============================================
	DASHBOARD ASESOR — ORDENES RECIENTES (últimos N meses)
	Limita la carga para evitar traer todo el histórico.
	=============================================*/

	static public function mdlMostrarordenesEmpresayPerfilRecientes($tabla, $itemOrdenes, $valorOrdenes, $iteDosOrdenes, $valorDosOrdenes, $meses = 13){

		$pdo = ConexionWP::conectarWP();

		$stmt = $pdo->prepare(
			"SELECT * FROM $tabla
			WHERE $itemOrdenes = :item1
			AND $iteDosOrdenes = :item2
			AND `fecha_ingreso` >= DATE_SUB(NOW(), INTERVAL :meses MONTH)
			ORDER BY id DESC"
		);

		$stmt->bindParam(":item1", $valorOrdenes, PDO::PARAM_STR);
		$stmt->bindParam(":item2", $valorDosOrdenes, PDO::PARAM_STR);
		$stmt->bindParam(":meses", $meses, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}


}