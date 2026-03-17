<?php
if (!class_exists('ReporteHelper')) {
    class ReporteHelper {
        public static function generarReporteExcel($statusFilter, $empresaId, $filename) {
            
            // Connect to DB directly using the existing connection class pattern or fetch via models
            // Ideally we use the existing models.
            // Requirement: "bypassing missing controller". So we must use Models directly.

            // Fetch orders based on status
            // We need to know the SQL logic for each status. 
            // Since we can't see the controller, we will replicate the likely logic using `ModeloOrdenes`.
            // If the model doesn't have a specific method, we might need to add one or use a generic one and filter in PHP.
            // `mdlMostrarOrdenesNew` (line 215 in ordenes.modelo.php) fetches all orders: SELECT ... FROM $tabla ORDER BY id DESC
            
            $ordenes = ModeloOrdenes::mdlMostrarOrdenesNew("ordenes", null, null); 
            
            // Filter by Status and Company in PHP to be safe and flexible
            $filteredOrdenes = [];
            foreach ($ordenes as $orden) {
                if ($orden['id_empresa'] != $empresaId) {
                    continue;
                }
                
                // Status Filtering Logic
                if ($statusFilter !== 'TODOS') {
                    // Check strict match or partial match depending on how the old system worked.
                    // Based on filenames:
                    // PEN -> 'En revisión (REV)'? Or 'Pendiente de autorización (AUT'? 
                    // Let's infer from the passed $statusFilter string.
                    if ($orden['estado'] != $statusFilter) {
                        continue;
                    }
                }
                $filteredOrdenes[] = $orden;
            }

            // Headers
            header('Expires: 0');
            header('Cache-control: private');
            header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
            header("Cache-Control: cache, must-revalidate"); 
            header('Content-Description: File Transfer');
            header('Last-Modified: '.date('D, d M Y H:i:s'));
            header("Pragma: public"); 
            header('Content-Disposition:; filename="'.$filename.'.xls"');
            header("Content-Transfer-Encoding: binary");

            echo utf8_decode("
                <table border='0'> 
                    <tr> 
                        <td style='font-weight:bold; border:1px solid #eee;'>#</td> 
                        <td style='font-weight:bold; border:1px solid #eee;'>FOLIO</td> 
                        <td style='font-weight:bold; border:1px solid #eee;'>EMPRESA</td>
                        <td style='font-weight:bold; border:1px solid #eee;'>CLIENTE</td>
                        <td style='font-weight:bold; border:1px solid #eee;'>TELEFONO</td>
                        <td style='font-weight:bold; border:1px solid #eee;'>WHATSAPP</td>
                        <td style='font-weight:bold; border:1px solid #eee;'>TITULO</td>
                        <td style='font-weight:bold; border:1px solid #eee;'>ESTADO</td>
                        <td style='font-weight:bold; border:1px solid #eee;'>TOTAL</td>
                        <td style='font-weight:bold; border:1px solid #eee;'>FECHA</td>
                    </tr>");

            foreach ($filteredOrdenes as $key => $value) {
                
                // Fetch Client
                $cliente = ModeloClientes::mdlMostrarClientes("clientesTienda", "id", $value["id_usuario"]);
                // mdlMostrarClientes returns fetchAll, so we need index 0 if it returns an array of arrays
                $clienteData = $cliente[0] ?? null;

                $nombreCliente = $clienteData ? $clienteData["nombre"] : "Desconocido";
                $telefono = "";
                $whatsappLink = "";

                if ($clienteData) {
                    // Phone logic
                    $t1 = $clienteData["telefono"];
                    $t2 = $clienteData["telefonoDos"];
                    
                    $validT1 = (strlen($t1) == 10 && is_numeric($t1));
                    $validT2 = (strlen($t2) == 10 && is_numeric($t2));
                    
                    $finalPhone = "";

                    if ($validT1 && $validT2 && $t1 !== $t2) {
                        // Both valid and different? Pick first one? Or show both?
                        // Task says: "validar que no se repitan en las 2 celdas".
                        // Assuming this means "if they are different, which one to use?"
                        // Let's prefer T1. 
                        $finalPhone = $t1; 
                    } elseif ($validT1) {
                        $finalPhone = $t1;
                    } elseif ($validT2) {
                        $finalPhone = $t2;
                    }
                    
                    $telefono = $finalPhone;
                    
                    if ($finalPhone != "") {
                        $whatsappLink = "<a href='https://wa.me/52".$finalPhone."' target='_blank'>Enviar Msj</a>";
                    }
                }

                 // Fetch Empresa Name (Optional, for display)
                 // $empresa = ControladorEmpresas::ctrMostrarEmpresas("empresas", "id", $value["id_empresa"]);
                 
                 echo utf8_decode("<tr>
                        <td style='border:1px solid #eee;'>".($key+1)."</td>
                        <td style='border:1px solid #eee;'>".$value["id"]."</td>
                        <td style='border:1px solid #eee;'>".$value["id_empresa"]."</td> 
                        <td style='border:1px solid #eee;'>".$nombreCliente."</td>
                        <td style='border:1px solid #eee;'>".$telefono."</td>
                        <td style='border:1px solid #eee;'>".$whatsappLink."</td>
                        <td style='border:1px solid #eee;'>".$value["titulo"]."</td>
                        <td style='border:1px solid #eee;'>".$value["estado"]."</td>
                        <td style='border:1px solid #eee;'>$ ".number_format($value["total"],2)."</td>
                        <td style='border:1px solid #eee;'>".$value["fecha"]."</td>
                    </tr>");
            }

            echo "</table>";
        }
    }
}
?>
