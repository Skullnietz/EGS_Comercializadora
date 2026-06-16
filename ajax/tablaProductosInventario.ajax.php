<?php

require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";
require_once "../controladores/categorias.controlador.php";
require_once "../modelos/categorias.modelo.php";
require_once "../config/InventarioHelper.php";

class TablaProductosInventario
{
    public function mostrarTabla()
    {
        $empresa = isset($_GET["empresa"]) ? intval($_GET["empresa"]) : 0;
        $filtro = isset($_GET["filtro"]) ? $_GET["filtro"] : "todos";
        $tipoCambio = isset($_GET["tipoCambio"]) ? floatval($_GET["tipoCambio"]) : InventarioHelper::getTipoCambioUsd();

        if ($tipoCambio <= 0) {
            $tipoCambio = InventarioHelper::DEFAULT_TIPO_CAMBIO;
        }

        $productos = ControladorProductos::ctrMostrarProductos("id_empresa", $empresa);
        $datosJson = '{"data":[';

        $primero = true;
        $num = 0;
        foreach ($productos as $producto) {
            if (!$this->pasaFiltro($producto, $filtro)) {
                continue;
            }
            $num++;

            $categoria = $this->obtenerCategoria($producto["id_categoria"]);
            $imagen = $this->obtenerImagen($producto["portada"]);
            $codigo = htmlspecialchars($producto["codigo"], ENT_QUOTES, "UTF-8");
            $titulo = htmlspecialchars($producto["titulo"], ENT_QUOTES, "UTF-8");
            $catHtml = htmlspecialchars($categoria, ENT_QUOTES, "UTF-8");
            $stock = intval($producto["disponibilidad"]);
            $medida = htmlspecialchars($producto["medida"] ?: "PZAS", ENT_QUOTES, "UTF-8");
            $badgeClass = InventarioHelper::stockBadgeClass($stock);
            $stockHtml = '<span class="inv-badge ' . $badgeClass . '">' . $stock . ' ' . $medida . '</span>';

            $precio = floatval($producto["precio"]);
            $precioMxn = $precio <= 0
                ? '<span class="inv-muted">Gratis</span>'
                : InventarioHelper::formatPrecioMxn($precio);
            $precioUsd = $precio <= 0
                ? '<span class="inv-muted">—</span>'
                : '<span class="inv-usd">' . InventarioHelper::formatPrecioUsd($precio, $tipoCambio) . '</span>';

            $productoCell = '<div><strong class="inv-prod-title">' . $titulo . '</strong>'
                . '<br><small class="inv-prod-cat">' . $catHtml . '</small></div>';

            $codigoCell = '<span class="inv-codigo" data-codigo="' . $codigo . '" title="Clic para copiar">'
                . $codigo . ' <i class="fa-solid fa-copy inv-copy-icon"></i></span>';

            $id = intval($producto["id"]);
            $portada = htmlspecialchars($producto["portada"], ENT_QUOTES, "UTF-8");
            $acciones = '<div class="inv-actions">'
                . '<button class="btn btn-xs btn-warning btnEditarProducto" idProducto="' . $id . '" data-toggle="modal" data-target="#modalEditarProducto" title="Editar"><i class="fas fa-pencil-alt"></i></button> '
                . '<button class="btn btn-xs btn-info btnAjustarStock" idProducto="' . $id . '" stockActual="' . $stock . '" tituloProducto="' . $titulo . '" title="Ajustar stock"><i class="fa-solid fa-boxes-stacked"></i></button> '
                . '<button class="btn btn-xs btn-default btnVerQr" idProducto="' . $id . '" codigoProducto="' . $codigo . '" tituloProducto="' . $titulo . '" precioProducto="' . $precio . '" title="Ver QR"><i class="fa-solid fa-qrcode"></i></button> '
                . '<button class="btn btn-xs btn-success btnImprimirEtiqueta" codigoProducto="' . $codigo . '" tituloProducto="' . $titulo . '" precioProducto="' . $precio . '" title="Imprimir etiqueta"><i class="fa-solid fa-barcode"></i></button> '
                . '<button class="btn btn-xs btn-danger btnEliminarProducto" idProducto="' . $id . '" imgOferta="' . htmlspecialchars($producto["imgOferta"], ENT_QUOTES, "UTF-8") . '" rutaCabecera="' . htmlspecialchars($producto["ruta"], ENT_QUOTES, "UTF-8") . '" imgPortada="" imgPrincipal="' . $portada . '" title="Eliminar"><i class="fa fa-times"></i></button>'
                . '</div>';

            $fila = [
                $num,
                $imagen,
                $codigoCell,
                $productoCell,
                $stockHtml,
                $precioMxn,
                $precioUsd,
                $acciones,
            ];

            if (!$primero) {
                $datosJson .= ',';
            }
            $primero = false;
            $datosJson .= json_encode($fila, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $datosJson .= ']}';
        echo $datosJson;
    }

    private function pasaFiltro($producto, $filtro)
    {
        $stock = intval($producto["disponibilidad"]);
        $estado = intval($producto["estado"]);

        switch ($filtro) {
            case "bajo":
                return $stock > 0 && $stock <= 15;
            case "sin":
                return $stock === 0;
            case "activos":
                return $estado === 1;
            case "inactivos":
                return $estado === 0;
            default:
                return true;
        }
    }

    private function obtenerCategoria($idCategoria)
    {
        $cat = ControladorCategorias::ctrMostrarCategorias("id", $idCategoria);
        if (!$cat || empty($cat["categoria"])) {
            return "Sin categoría";
        }
        return $cat["categoria"];
    }

    private function obtenerImagen($portada)
    {
        $src = $portada ?: "vistas/img/default/default.png";
        return "<img loading='lazy' src='" . htmlspecialchars($src, ENT_QUOTES, "UTF-8") . "' class='inv-thumb' width='44' height='44'>";
    }
}

$activar = new TablaProductosInventario();
$activar->mostrarTabla();
