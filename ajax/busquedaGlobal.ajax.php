<?php
/**
 * Búsqueda global — endpoint AJAX para el navbar.
 *
 * Recibe POST["q"] (mínimo 2 caracteres) y busca en:
 *   - ordenes        (egsequip_respaldo  / ConexionWP)
 *   - clientesTienda (egsequip_ecomerce  / Conexion)
 *   - productos      (egsequip_ecomerce  / Conexion)
 *   - cotizaciones   (egsequip_ecomerce  / Conexion)
 *   - pedidos        (egsequip_ecomerce  / Conexion)
 *   - compras/ventas (egsequip_ecomerce  / Conexion)
 *
 * Responde JSON: { results: [ { group, items: [{id,label,sub,icon,url}] } ] }
 */
session_start();

if (!isset($_SESSION["validarSesionBackend"]) || $_SESSION["validarSesionBackend"] !== "ok") {
    http_response_code(401);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

require_once "../config/Database.php";

$q = isset($_POST["q"]) ? trim($_POST["q"]) : "";

if (mb_strlen($q) < 2) {
    echo json_encode(["results" => []]);
    exit;
}

$idEmpresa = isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0;
$like       = "%" . $q . "%";
$limit      = 5; // máximo resultados por categoría
$results    = [];

/* ─────────────────────────────────────────────
   1. ÓRDENES DE SERVICIO  (egsequip_respaldo)
   ───────────────────────────────────────────── */
try {
    $pdo = Database::conectar(Database::WORDPRESS);

    $sql = "SELECT id, titulo, marcaDelEquipo, modeloDelEquipo, estado
            FROM ordenes
            WHERE (id LIKE :q1
               OR titulo LIKE :q2
               OR marcaDelEquipo LIKE :q3
               OR modeloDelEquipo LIKE :q4
               OR numeroDeSerieDelEquipo LIKE :q5)";

    if ($idEmpresa > 0) {
        $sql .= " AND id_empresa = :emp";
    }
    $sql .= " ORDER BY id DESC LIMIT $limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":q1", $like, PDO::PARAM_STR);
    $stmt->bindParam(":q2", $like, PDO::PARAM_STR);
    $stmt->bindParam(":q3", $like, PDO::PARAM_STR);
    $stmt->bindParam(":q4", $like, PDO::PARAM_STR);
    $stmt->bindParam(":q5", $like, PDO::PARAM_STR);
    if ($idEmpresa > 0) {
        $stmt->bindParam(":emp", $idEmpresa, PDO::PARAM_INT);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($rows) {
        $items = [];
        foreach ($rows as $r) {
            $sub = $r["marcaDelEquipo"] ?: $r["titulo"];
            $items[] = [
                "id"    => $r["id"],
                "label" => "Orden #" . $r["id"],
                "sub"   => mb_substr($sub, 0, 50),
                "icon"  => "fa-solid fa-clipboard-list",
                "url"   => "index.php?ruta=infoOrden&idOrden=" . $r["id"],
            ];
        }
        $results[] = ["group" => "Órdenes de Servicio", "items" => $items];
    }
} catch (Exception $e) { /* silenciar errores de conexión */ }

/* ─────────────────────────────────────────────
   2. CLIENTES  (egsequip_ecomerce)
   ───────────────────────────────────────────── */
try {
    $pdo = Database::conectar(Database::ECOMMERCE);

    $sql = "SELECT id, nombre, correo, telefono
            FROM clientesTienda
            WHERE (nombre LIKE :q1
               OR correo LIKE :q2
               OR telefono LIKE :q3)";

    if ($idEmpresa > 0) {
        $sql .= " AND id_empresa = :emp";
    }
    $sql .= " ORDER BY id DESC LIMIT $limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":q1", $like, PDO::PARAM_STR);
    $stmt->bindParam(":q2", $like, PDO::PARAM_STR);
    $stmt->bindParam(":q3", $like, PDO::PARAM_STR);
    if ($idEmpresa > 0) {
        $stmt->bindParam(":emp", $idEmpresa, PDO::PARAM_INT);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($rows) {
        $items = [];
        foreach ($rows as $r) {
            $items[] = [
                "id"    => $r["id"],
                "label" => $r["nombre"],
                "sub"   => $r["correo"] ?: $r["telefono"],
                "icon"  => "fa-solid fa-user",
                "url"   => "index.php?ruta=clientes&idCliente=" . $r["id"],
            ];
        }
        $results[] = ["group" => "Clientes", "items" => $items];
    }
} catch (Exception $e) { /* silenciar */ }

/* ─────────────────────────────────────────────
   3. PRODUCTOS  (egsequip_ecomerce)
   ───────────────────────────────────────────── */
try {
    $pdo = Database::conectar(Database::ECOMMERCE);

    $sql = "SELECT id, titulo, codigo, precio
            FROM productos
            WHERE (titulo LIKE :q1
               OR codigo LIKE :q2)";

    if ($idEmpresa > 0) {
        $sql .= " AND id_empresa = :emp";
    }
    $sql .= " ORDER BY id DESC LIMIT $limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":q1", $like, PDO::PARAM_STR);
    $stmt->bindParam(":q2", $like, PDO::PARAM_STR);
    if ($idEmpresa > 0) {
        $stmt->bindParam(":emp", $idEmpresa, PDO::PARAM_INT);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($rows) {
        $items = [];
        foreach ($rows as $r) {
            $label = $r["titulo"];
            if ($r["codigo"]) $label .= " [" . $r["codigo"] . "]";
            $items[] = [
                "id"    => $r["id"],
                "label" => mb_substr($label, 0, 60),
                "sub"   => "$" . number_format($r["precio"], 2),
                "icon"  => "fab fa-product-hunt",
                "url"   => "index.php?ruta=productos&idProducto=" . $r["id"],
            ];
        }
        $results[] = ["group" => "Productos", "items" => $items];
    }
} catch (Exception $e) { /* silenciar */ }

/* ─────────────────────────────────────────────
   4. COTIZACIONES  (egsequip_ecomerce)
   ───────────────────────────────────────────── */
try {
    $pdo = Database::conectar(Database::ECOMMERCE);

    $sql = "SELECT id, nombre_cliente, asunto, codigo_qr, total
            FROM cotizaciones
            WHERE (nombre_cliente LIKE :q1
               OR asunto LIKE :q2
               OR codigo_qr LIKE :q3
               OR id LIKE :q4)";

    if ($idEmpresa > 0) {
        $sql .= " AND empresa = :emp";
    }
    $sql .= " ORDER BY id DESC LIMIT $limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":q1", $like, PDO::PARAM_STR);
    $stmt->bindParam(":q2", $like, PDO::PARAM_STR);
    $stmt->bindParam(":q3", $like, PDO::PARAM_STR);
    $stmt->bindParam(":q4", $like, PDO::PARAM_STR);
    if ($idEmpresa > 0) {
        $stmt->bindParam(":emp", $idEmpresa, PDO::PARAM_INT);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($rows) {
        $items = [];
        foreach ($rows as $r) {
            $items[] = [
                "id"    => $r["id"],
                "label" => "Cotización #" . $r["id"] . " — " . ($r["nombre_cliente"] ?: $r["asunto"]),
                "sub"   => "$" . number_format($r["total"], 2),
                "icon"  => "fa-solid fa-file-invoice-dollar",
                "url"   => "index.php?ruta=cotizacion&idCotizacion=" . $r["id"],
            ];
        }
        $results[] = ["group" => "Cotizaciones", "items" => $items];
    }
} catch (Exception $e) { /* silenciar */ }

/* ─────────────────────────────────────────────
   5. PEDIDOS  (egsequip_ecomerce)
   ───────────────────────────────────────────── */
try {
    $pdo = Database::conectar(Database::ECOMMERCE);

    $sql = "SELECT p.id, p.estado, p.total, c.nombre AS cliente
            FROM pedidos p
            LEFT JOIN clientesTienda c ON c.id = p.id_cliente
            WHERE (p.id LIKE :q1
               OR c.nombre LIKE :q2)";

    if ($idEmpresa > 0) {
        $sql .= " AND p.id_empresa = :emp";
    }
    $sql .= " ORDER BY p.id DESC LIMIT $limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":q1", $like, PDO::PARAM_STR);
    $stmt->bindParam(":q2", $like, PDO::PARAM_STR);
    if ($idEmpresa > 0) {
        $stmt->bindParam(":emp", $idEmpresa, PDO::PARAM_INT);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($rows) {
        $items = [];
        foreach ($rows as $r) {
            $items[] = [
                "id"    => $r["id"],
                "label" => "Pedido #" . $r["id"] . ($r["cliente"] ? " — " . $r["cliente"] : ""),
                "sub"   => "$" . number_format($r["total"], 2) . " · " . $r["estado"],
                "icon"  => "fa-solid fa-box-open",
                "url"   => "index.php?ruta=pedidos&idPedido=" . $r["id"],
            ];
        }
        $results[] = ["group" => "Pedidos", "items" => $items];
    }
} catch (Exception $e) { /* silenciar */ }

/* ─────────────────────────────────────────────
   6. VENTAS  (egsequip_ecomerce — tabla compras)
   ───────────────────────────────────────────── */
try {
    $pdo = Database::conectar(Database::ECOMMERCE);

    $sql = "SELECT id, nombreCliente, total, metodo, fecha
            FROM compras
            WHERE (id LIKE :q1
               OR nombreCliente LIKE :q2)";

    if ($idEmpresa > 0) {
        $sql .= " AND id_empresa = :emp";
    }
    $sql .= " ORDER BY id DESC LIMIT $limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":q1", $like, PDO::PARAM_STR);
    $stmt->bindParam(":q2", $like, PDO::PARAM_STR);
    if ($idEmpresa > 0) {
        $stmt->bindParam(":emp", $idEmpresa, PDO::PARAM_INT);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($rows) {
        $items = [];
        foreach ($rows as $r) {
            $items[] = [
                "id"    => $r["id"],
                "label" => "Venta #" . $r["id"] . " — " . $r["nombreCliente"],
                "sub"   => "$" . number_format($r["total"], 2) . " · " . $r["metodo"],
                "icon"  => "fa-solid fa-shopping-cart",
                "url"   => "index.php?ruta=creararventa&idVenta=" . $r["id"],
            ];
        }
        $results[] = ["group" => "Ventas", "items" => $items];
    }
} catch (Exception $e) { /* silenciar */ }

/* ── Respuesta final ── */
header("Content-Type: application/json; charset=utf-8");
echo json_encode(["results" => $results], JSON_UNESCAPED_UNICODE);
