<?php

require_once "conexion.php";
require_once "conexionWordpress.php";

class ModeloNotificaciones{

	/*=============================================
	MOSTRAR NOTIFICACIONES (legacy — ecommerce BD)
	=============================================*/

	static public function mdlMostrarNotificaciones($tabla){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");

		$stmt -> execute();

		return $stmt -> fetch();

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	ACTUALIZAR NOTIFICACIONES (legacy)
	=============================================*/

	static public function mdlActualizarNotificaciones($tabla, $item, $valor){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item = :$item");

		$stmt -> bindParam(":".$item, $valor, PDO::PARAM_INT);

		if($stmt -> execute()){

			return "ok";

		}else{

			return "error";

		}

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	CREAR TABLA notificaciones_estado SI NO EXISTE
	(se ejecuta una sola vez, auto-verificación)
	=============================================*/

	static public function mdlCrearTablaEstado(){

		$pdo = ConexionWP::conectarWP();

		// Operación normal: una lectura vacía valida tabla y columnas sin ejecutar DDL.
		try {
			$pdo->query(
				"SELECT id, tipo, leido_tecnico, id_tecnicoDos
				 FROM notificaciones_estado LIMIT 0"
			);
			return true;
		} catch (Exception $e) {
			// Instalación o esquema antiguo: continuar con la reparación idempotente.
		}

		$pdo->exec("CREATE TABLE IF NOT EXISTS `notificaciones_estado` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`id_orden` INT(11) NOT NULL,
			`estado_anterior` VARCHAR(100) NOT NULL,
			`estado_nuevo` VARCHAR(100) NOT NULL,
			`id_usuario_accion` INT(11) DEFAULT NULL,
			`nombre_usuario` VARCHAR(150) DEFAULT NULL,
			`titulo_orden` VARCHAR(255) DEFAULT NULL,
			`id_empresa` INT(11) DEFAULT NULL,
			`id_asesor` INT(11) DEFAULT NULL,
			`id_tecnico` INT(11) DEFAULT NULL,
			`id_tecnicoDos` INT(11) DEFAULT NULL,
			`tipo` VARCHAR(20) NOT NULL DEFAULT 'estado',
			`leido_admin` TINYINT(1) NOT NULL DEFAULT 0,
			`leido_vendedor` TINYINT(1) NOT NULL DEFAULT 0,
			`leido_tecnico` TINYINT(1) NOT NULL DEFAULT 0,
			`fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`),
			KEY `idx_empresa_leido` (`id_empresa`, `leido_admin`, `fecha`),
			KEY `idx_asesor_leido` (`id_asesor`, `leido_vendedor`, `fecha`),
			KEY `idx_tecnico_leido` (`id_tecnico`, `leido_tecnico`, `fecha`),
			KEY `idx_fecha` (`fecha`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");

		$columnas = $pdo->query("SHOW COLUMNS FROM `notificaciones_estado`")->fetchAll(PDO::FETCH_COLUMN);

		if (!in_array("leido_tecnico", $columnas, true)) {
			$pdo->exec("ALTER TABLE `notificaciones_estado` ADD COLUMN `leido_tecnico` TINYINT(1) NOT NULL DEFAULT 0 AFTER `leido_vendedor`");
		}
		if (!in_array("tipo", $columnas, true)) {
			$pdo->exec("ALTER TABLE `notificaciones_estado` ADD COLUMN `tipo` VARCHAR(20) NOT NULL DEFAULT 'estado' AFTER `id_tecnico`");
		}
		if (!in_array("id_tecnicoDos", $columnas, true)) {
			$pdo->exec("ALTER TABLE `notificaciones_estado` ADD COLUMN `id_tecnicoDos` INT(11) DEFAULT NULL AFTER `id_tecnico`");
		}

		return true;

	}

	/*=============================================
	INSERTAR NOTIFICACIÓN DE CAMBIO DE ESTADO
	=============================================*/

	static public function mdlInsertarNotifEstado($datos){

		$pdo = ConexionWP::conectarWP();

		$tipo = isset($datos["tipo"]) ? $datos["tipo"] : "estado";

		$idTecDos = isset($datos["id_tecnicoDos"]) ? intval($datos["id_tecnicoDos"]) : 0;

		$stmt = $pdo->prepare(
			"INSERT INTO notificaciones_estado
				(id_orden, estado_anterior, estado_nuevo, id_usuario_accion,
				 nombre_usuario, titulo_orden, id_empresa, id_asesor, id_tecnico, id_tecnicoDos, tipo)
			 VALUES
				(:id_orden, :estado_anterior, :estado_nuevo, :id_usuario_accion,
				 :nombre_usuario, :titulo_orden, :id_empresa, :id_asesor, :id_tecnico, :id_tecnicoDos, :tipo)"
		);

		$stmt->bindParam(":id_orden",           $datos["id_orden"],           PDO::PARAM_INT);
		$stmt->bindParam(":estado_anterior",     $datos["estado_anterior"],    PDO::PARAM_STR);
		$stmt->bindParam(":estado_nuevo",        $datos["estado_nuevo"],       PDO::PARAM_STR);
		$stmt->bindParam(":id_usuario_accion",   $datos["id_usuario_accion"], PDO::PARAM_INT);
		$stmt->bindParam(":nombre_usuario",      $datos["nombre_usuario"],    PDO::PARAM_STR);
		$stmt->bindParam(":titulo_orden",        $datos["titulo_orden"],      PDO::PARAM_STR);
		$stmt->bindParam(":id_empresa",          $datos["id_empresa"],        PDO::PARAM_INT);
		$stmt->bindParam(":id_asesor",           $datos["id_asesor"],         PDO::PARAM_INT);
		$stmt->bindParam(":id_tecnico",          $datos["id_tecnico"],        PDO::PARAM_INT);
		$stmt->bindParam(":id_tecnicoDos",       $idTecDos,                   PDO::PARAM_INT);
		$stmt->bindParam(":tipo",                $tipo,                       PDO::PARAM_STR);

		if($stmt->execute()){
			return "ok";
		}
		return "error";

	}

	/*=============================================
	OBTENER NOTIFICACIONES DE ESTADO NO LEÍDAS
	- Admin: empresa + leido_admin=0
	- Vendedor: asesor + leido_vendedor=0
	- Técnico: técnico + leido_tecnico=0 + solo Aceptadas
	=============================================*/

	static public function mdlNotifEstadoNoLeidas($perfil, $idEmpresa, $idRol = null, $limite = 30){

		$pdo = ConexionWP::conectarWP();

		if ($perfil === 'administrador') {

			$stmt = $pdo->prepare(
				"SELECT * FROM notificaciones_estado
				 WHERE id_empresa = :empresa AND leido_admin = 0
				 ORDER BY fecha DESC
				 LIMIT :limite"
			);
			$stmt->bindParam(":empresa", $idEmpresa, PDO::PARAM_INT);

		} elseif ($perfil === 'tecnico') {

			$stmt = $pdo->prepare(
				"SELECT * FROM notificaciones_estado
				 WHERE (id_tecnico = :tecnico OR id_tecnicoDos = :tecnico2) AND leido_tecnico = 0
				   AND (
				     (tipo = 'estado' AND (estado_nuevo LIKE '%Aceptado%' OR estado_nuevo LIKE '%ok%'))
				     OR tipo = 'traspaso'
				   )
				 ORDER BY fecha DESC
				 LIMIT :limite"
			);
			$stmt->bindParam(":tecnico", $idRol, PDO::PARAM_INT);
			$stmt->bindParam(":tecnico2", $idRol, PDO::PARAM_INT);

		} else {

			$stmt = $pdo->prepare(
				"SELECT * FROM notificaciones_estado
				 WHERE id_asesor = :asesor AND leido_vendedor = 0 AND tipo <> 'etiqueta'
				 ORDER BY fecha DESC
				 LIMIT :limite"
			);
			$stmt->bindParam(":asesor", $idRol, PDO::PARAM_INT);

		}

		$stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();

	}

	/*=============================================
	OBTENER TODAS LAS NOTIFICACIONES (para vista completa)
	- Incluye estado + traspaso
	- Paginado con offset + limite
	=============================================*/

	static public function mdlTodasNotificaciones($perfil, $idEmpresa, $idRol = null, $limite = 50, $offset = 0){

		$pdo = ConexionWP::conectarWP();

		if ($perfil === 'administrador') {

			$stmt = $pdo->prepare(
				"SELECT * FROM notificaciones_estado
				 WHERE id_empresa = :empresa
				 ORDER BY fecha DESC
				 LIMIT :limite OFFSET :offset"
			);
			$stmt->bindParam(":empresa", $idEmpresa, PDO::PARAM_INT);

		} elseif ($perfil === 'tecnico') {

			$stmt = $pdo->prepare(
				"SELECT * FROM notificaciones_estado
				 WHERE (id_tecnico = :tecnico OR id_tecnicoDos = :tecnico2)
				   AND (
				     (tipo = 'estado' AND (estado_nuevo LIKE '%Aceptado%' OR estado_nuevo LIKE '%ok%'))
				     OR tipo = 'traspaso'
				   )
				 ORDER BY fecha DESC
				 LIMIT :limite OFFSET :offset"
			);
			$stmt->bindParam(":tecnico", $idRol, PDO::PARAM_INT);
			$stmt->bindParam(":tecnico2", $idRol, PDO::PARAM_INT);

		} else {

			$stmt = $pdo->prepare(
				"SELECT * FROM notificaciones_estado
				 WHERE id_asesor = :asesor AND tipo <> 'etiqueta'
				 ORDER BY fecha DESC
				 LIMIT :limite OFFSET :offset"
			);
			$stmt->bindParam(":asesor", $idRol, PDO::PARAM_INT);

		}

		$stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
		$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();

	}

	/*=============================================
	OBTENER DATOS DE ÓRDENES POR LISTA DE IDs
	(marca, modelo, cliente, datos para URL)
	=============================================*/

	static public function mdlDatosOrdenesPorIds($ids){

		if (empty($ids)) return array();

		$pdo = ConexionWP::conectarWP();

		// Construir placeholders
		$placeholders = implode(',', array_fill(0, count($ids), '?'));
		$stmt = $pdo->prepare(
			"SELECT id, marcaDelEquipo, modeloDelEquipo, id_usuario,
			        id_empresa, id_Asesor, id_tecnico, id_tecnicoDos, id_pedido
			 FROM ordenes
			 WHERE id IN ($placeholders)"
		);
		$stmt->execute(array_values($ids));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// Indexar por id
		$result = array();
		foreach ($rows as $r) {
			$result[intval($r['id'])] = $r;
		}
		return $result;
	}

	/*=============================================
	MARCAR NOTIFICACIONES COMO LEÍDAS
	=============================================*/

	static public function mdlMarcarLeidasEstado($perfil, $idEmpresa, $idRol = null){

		$pdo = ConexionWP::conectarWP();

		if ($perfil === 'administrador') {

			$stmt = $pdo->prepare(
				"UPDATE notificaciones_estado SET leido_admin = 1
				 WHERE id_empresa = :empresa AND leido_admin = 0"
			);
			$stmt->bindParam(":empresa", $idEmpresa, PDO::PARAM_INT);

		} elseif ($perfil === 'tecnico') {

			$stmt = $pdo->prepare(
				"UPDATE notificaciones_estado SET leido_tecnico = 1
				 WHERE (id_tecnico = :tecnico OR id_tecnicoDos = :tecnico2) AND leido_tecnico = 0
				   AND (
				     (tipo = 'estado' AND (estado_nuevo LIKE '%Aceptado%' OR estado_nuevo LIKE '%ok%'))
				     OR tipo = 'traspaso'
				   )"
			);
			$stmt->bindParam(":tecnico", $idRol, PDO::PARAM_INT);
			$stmt->bindParam(":tecnico2", $idRol, PDO::PARAM_INT);

		} else {

			$stmt = $pdo->prepare(
				"UPDATE notificaciones_estado SET leido_vendedor = 1
				 WHERE id_asesor = :asesor AND leido_vendedor = 0"
			);
			$stmt->bindParam(":asesor", $idRol, PDO::PARAM_INT);

		}

		if($stmt->execute()){
			return "ok";
		}
		return "error";

	}

	/*=============================================
	HISTORIAL COMPLETO DE UNA ORDEN (seguimiento)
	Devuelve todos los cambios de estado y traspasos
	=============================================*/
	static public function mdlHistorialOrden($idOrden){

		$pdo = ConexionWP::conectarWP();

		$stmt = $pdo->prepare(
			"SELECT * FROM notificaciones_estado
			 WHERE id_orden = :id_orden
			 ORDER BY fecha DESC"
		);

		$stmt->bindParam(":id_orden", $idOrden, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}

}
