<?php
if (!class_exists('ReporteHelper')) {
    require_once __DIR__ . '/excel_export_helper.php';

    class ReporteHelper {
        public static function generarReporteExcel($statusFilter, $empresaId, $filename) {

            $ordenes = ModeloOrdenes::mdlMostrarOrdenesNew("ordenes", null, null);

            // Filter by Status and Company in PHP
            $filteredOrdenes = [];
            foreach ($ordenes as $orden) {
                if ($orden['id_empresa'] != $empresaId) {
                    continue;
                }
                if ($statusFilter !== 'TODOS') {
                    if ($orden['estado'] != $statusFilter) {
                        continue;
                    }
                }
                $filteredOrdenes[] = $orden;
            }

            usort($filteredOrdenes, function ($a, $b) {
                return intval($a['id']) <=> intval($b['id']);
            });

            usort($filteredOrdenes, function ($a, $b) {
                return strtotime((string)($a['fecha'] ?? '')) <=> strtotime((string)($b['fecha'] ?? ''));
            });

            $headers = array('#', 'Folio', 'Empresa', 'Cliente', 'Telefono', 'WhatsApp', 'Titulo', 'Estado', 'Total', 'Fecha');
            $rows = array();
            $sumaTotal = 0.0;

            foreach ($filteredOrdenes as $key => $value) {

                $cliente = ModeloClientes::mdlMostrarClientes("clientesTienda", "id", $value["id_usuario"]);
                $clienteData = $cliente[0] ?? null;

                $nombreCliente = $clienteData ? $clienteData["nombre"] : "Desconocido";
                $telefono = "";
                $whatsapp = "";

                if ($clienteData) {
                    $t1 = $clienteData["telefono"];
                    $t2 = $clienteData["telefonoDos"];

                    $validT1 = (strlen($t1) == 10 && is_numeric($t1));
                    $validT2 = (strlen($t2) == 10 && is_numeric($t2));

                    $finalPhone = "";
                    if ($validT1 && $validT2 && $t1 !== $t2) {
                        $finalPhone = $t1;
                    } elseif ($validT1) {
                        $finalPhone = $t1;
                    } elseif ($validT2) {
                        $finalPhone = $t2;
                    }

                    $telefono = $finalPhone;
                    $whatsapp = ($finalPhone != "") ? "52".$finalPhone : "";
                }

                $rows[] = array(
                    $key + 1,
                    $value["id"],
                    $value["id_empresa"],
                    $nombreCliente,
                    $telefono,
                    $whatsapp,
                    $value["titulo"],
                    $value["estado"],
                    floatval($value["total"]),
                    $value["fecha"]
                );

                $sumaTotal += floatval($value["total"]);
            }

            ExcelExportHelper::downloadXlsx($filename, $headers, $rows, array(
                'sheetName' => 'Ordenes',
                'title' => 'Reporte de Ordenes por Estado',
                'subtitle' => 'Estado: ' . $statusFilter . ' | Generado: ' . date('Y-m-d H:i'),
                'currencyColumns' => array(8),
                'dateColumns' => array(9),
                'hyperlinkColumns' => array(5 => 'whatsapp_api'),
                'footerRows' => array(
                    array('values' => array(0 => 'Registros', 1 => count($rows), 7 => 'Total', 8 => $sumaTotal))
                )
            ));
        }
    }
}
?>
