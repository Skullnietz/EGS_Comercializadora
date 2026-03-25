<?php
if (!class_exists('ReporteHelper')) {
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

            // CSV Headers
            $csvName = $filename . '.csv';
            header('Expires: 0');
            header('Cache-control: private');
            header('Content-Type: text/csv; charset=UTF-8');
            header('Cache-Control: cache, must-revalidate');
            header('Content-Description: File Transfer');
            header('Last-Modified: '.date('D, d M Y H:i:s'));
            header('Pragma: public');
            header('Content-Disposition: attachment; filename="'.$csvName.'"');
            header('Content-Transfer-Encoding: binary');

            $output = fopen('php://output', 'w');
            // BOM UTF-8 for Excel/Sheets compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($output, ['#', 'Folio', 'Empresa', 'Cliente', 'Teléfono', 'WhatsApp', 'Título', 'Estado', 'Total', 'Fecha']);

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

                fputcsv($output, [
                    $key + 1,
                    $value["id"],
                    $value["id_empresa"],
                    $nombreCliente,
                    $telefono,
                    $whatsapp,
                    $value["titulo"],
                    $value["estado"],
                    number_format($value["total"], 2),
                    $value["fecha"]
                ]);
            }

            fclose($output);
        }
    }
}
?>
