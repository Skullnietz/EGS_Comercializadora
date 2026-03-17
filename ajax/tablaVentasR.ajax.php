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
            
            // Añadir cada fila como un array asociativo
            $datos[] = array(
                ($i + 1),
                $ventas[$i]["id"],
                $cliente,
                $ventas[$i]["productoUno"],
                $ventas[$i]["cantidadProductos"],
                $ventas[$i]["pago"],
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