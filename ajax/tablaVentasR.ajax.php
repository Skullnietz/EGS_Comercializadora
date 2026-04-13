<?php
require_once "../controladores/ventas.controlador.php";
require_once "../modelos/ventas.modelo.php";
require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";
require_once "../controladores/usuarios.controlador.php";
require_once "../modelos/usuarios.modelo.php";
require_once "../controladores/empresas.controlador.php";
require_once "../modelos/empresas.modelo.php";

class TablaVentas{
    /*=============================================
    MOSTRAR LA TABLA DE VENTAS
    =============================================*/
    public function mostrarTabla(){    
        if ($_GET["perfil"] == "Super-Administrador") {
            $itemUno = null;
            $valorUno = null;
            $ventas = ControladorVentas::ctrMostrarVentasParaTiket($itemUno, $valorUno);
        } else {
            $empresa = $_GET["empresa"];
            $item = "id_empresa";
            $valor = $empresa;
            $ventas = ControladorVentas::ctrMostrarVentasParaEmpresa($item, $valor);
        }

        $fechaInicial = isset($_GET["fechaInicial"]) ? trim($_GET["fechaInicial"]) : "";
        $fechaFinal = isset($_GET["fechaFinal"]) ? trim($_GET["fechaFinal"]) : "";

        if ($fechaInicial !== "" || $fechaFinal !== "") {
            if ($fechaInicial === "") {
                $fechaInicial = $fechaFinal;
            }

            if ($fechaFinal === "") {
                $fechaFinal = $fechaInicial;
            }

            $inicioTs = strtotime($fechaInicial . " 00:00:00");
            $finTs = strtotime($fechaFinal . " 23:59:59");

            if ($inicioTs !== false && $finTs !== false) {
                $ventas = array_values(array_filter($ventas, function($venta) use ($inicioTs, $finTs) {
                    if (!isset($venta["fecha"])) {
                        return false;
                    }

                    $ventaTs = strtotime($venta["fecha"]);
                    if ($ventaTs === false) {
                        return false;
                    }

                    return $ventaTs >= $inicioTs && $ventaTs <= $finTs;
                }));
            }
        }

        $datos = array(); // Inicializar el array de datos

        for ($i = 0; $i < count($ventas); $i++) {
            $ticket = "<button class='btn btn-warning btnImprimirComprovanteDeVentaR' idventa='".$ventas[$i]["id"]."' empresa='".$ventas[$i]["empresa"]."' data-toggle='modal'><i class='fas fa-ticket-alt'></i></button>";

            $eliminarVenta = "";
            if ($_GET["perfil"] == "administrador") {
                $eliminarVenta = "<button class='btn btn-danger btnEliminarVenta' idventa='".$ventas[$i]["id"]."'><i class='fa fa-times'></i></button>";
            }

            $itemEmpresa = "id";
            $valorEmpresa = $ventas[$i]["empresa"];
            $respuestaEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($itemEmpresa, $valorEmpresa);
            $NombreEmpresa = $respuestaEmpresa["empresa"];

            $cliente = trim($ventas[$i]["nombreCliente"]);
            $idCliente = isset($ventas[$i]["id_cliente"]) ? intval($ventas[$i]["id_cliente"]) : 0;
            $nombreClienteUrl = rawurlencode($cliente !== "" ? $cliente : "Cliente");

            if ($idCliente > 0) {
                $historialUrl = "index.php?ruta=Historialdecliente&idCliente=".$idCliente."&nombreCliente=".$nombreClienteUrl;
                $cliente = "<a href='".$historialUrl."' target='_blank' style='font-weight:600;color:#2563eb;text-decoration:none;'>".htmlspecialchars($cliente, ENT_QUOTES, "UTF-8")."</a>";
                $historialVenta = "<a class='btn btn-info btn-sm' href='".$historialUrl."' target='_blank' title='Ver historial del cliente'><i class='fa-solid fa-clock-rotate-left'></i></a>";
            } else {
                $cliente = htmlspecialchars($cliente !== "" ? $cliente : "Sin cliente", ENT_QUOTES, "UTF-8");
                $historialVenta = "<button class='btn btn-default btn-sm' type='button' disabled title='Venta sin cliente vinculado'><i class='fa-solid fa-clock-rotate-left'></i></button>";
            }
            
            // Añadir cada fila como un array asociativo
            $datos[] = array(
                ($i + 1),
                $ventas[$i]["id"],
                $cliente,
                $ventas[$i]["productoUno"],
                $ventas[$i]["cantidadProductos"],
                $ventas[$i]["pago"],
                $historialVenta,
                $ticket,
                $eliminarVenta
            );
        }

        // Codificar el array a JSON
        echo json_encode(array('data' => $datos));
    }
}

/*=============================================
ACTIVAR TABLA DE VENTAS
=============================================*/
$activar = new TablaVentas();
$activar->mostrarTabla();
?>
