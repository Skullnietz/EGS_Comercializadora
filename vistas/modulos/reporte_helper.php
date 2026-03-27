<?php
if (!class_exists('ReporteHelper')) {
    require_once __DIR__ . '/excel_export_helper.php';

    class ReporteHelper {
        public static function generarReporteExcel($statusFilter, $empresaId, $filename) {

            if (isset($_GET['fechaInicial']) && isset($_GET['fechaFinal'])) {
                $ordenes = ModeloOrdenes::mdlRangoFechasOrdenesPorEmpresa(
                    'ordenes',
                    $_GET['fechaInicial'],
                    $_GET['fechaFinal'],
                    'id_empresa',
                    $empresaId
                );
            } else {
                $ordenes = ModeloOrdenes::mdlMostrarordenesParaValidar('ordenes', 'id_empresa', $empresaId);
            }

            if (!is_array($ordenes)) {
                $ordenes = array();
            }

            // Filter by Status and Company in PHP
            $filteredOrdenes = [];
            foreach ($ordenes as $orden) {
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

            $rangoTexto = (isset($_GET['fechaInicial']) && isset($_GET['fechaFinal']))
                ? (($_GET['fechaInicial'] === $_GET['fechaFinal'])
                    ? $_GET['fechaInicial']
                    : $_GET['fechaInicial'] . ' a ' . $_GET['fechaFinal'])
                : 'Todas las ordenes';

            $isReporteAceptados = ($statusFilter === 'Aceptado (ok)');

            if ($isReporteAceptados) {
                $headers = array('Empresa', 'Asesor', 'Tecnico principal', 'Cliente', 'Telefono', 'Mensaje', 'Fecha', 'Estado', 'Cantidad/Monto', 'Fecha ingreso');
            } else {
                $headers = array('#', 'Folio', 'Empresa', 'Cliente', 'Telefono', 'WhatsApp', 'Titulo', 'Estado', 'Total', 'Fecha');
            }
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

                $empresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $value["id_empresa"]);
                $asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
                $tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

                if ($isReporteAceptados) {
                    $rows[] = array(
                        $empresa["empresa"] ?? $value["id_empresa"],
                        $asesor["nombre"] ?? "",
                        $tecnico["nombre"] ?? "",
                        $nombreCliente,
                        $telefono,
                        $value["titulo"] ?? "",
                        $value["fecha"] ?? "",
                        $value["estado"] ?? "",
                        floatval($value["total"]),
                        $value["fecha_ingreso"] ?? ""
                    );
                } else {
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
                }

                $sumaTotal += floatval($value["total"]);
            }

            $currencyColumns = array(8);
            $dateColumns = $isReporteAceptados ? array(6, 9) : array(9);
            $footerTotalLabelColumn = 7;
            $footerTotalValueColumn = 8;
            $hyperlinkColumns = $isReporteAceptados ? array() : array(5 => 'whatsapp_api');

            ExcelExportHelper::downloadXlsx($filename, $headers, $rows, array(
                'sheetName' => 'Ordenes',
                'title' => 'Reporte de Ordenes por Estado',
                'subtitle' => 'Estado: ' . $statusFilter . ' | Rango: ' . $rangoTexto . ' | Generado: ' . date('Y-m-d H:i'),
                'currencyColumns' => $currencyColumns,
                'dateColumns' => $dateColumns,
                'hyperlinkColumns' => $hyperlinkColumns,
                'footerRows' => array(
                    array('values' => array(0 => 'Registros', 1 => count($rows), $footerTotalLabelColumn => 'Total', $footerTotalValueColumn => $sumaTotal))
                )
            ));
        }

        /**
         * CRM Contact Export: Pendiente de Autorización + Terminada
         * For vendedor to contact clients by phone/WhatsApp
         */
        public static function generarReporteCRMContacto($empresaId, $filename) {
            $statusFiltros = array(
                'Pendiente de autorización (AUT',
                'Terminada (ter)'
            );

            $ordenes = self::obtenerOrdenesFiltradas($empresaId, $statusFiltros);

            $rangoTexto = self::obtenerRangoTexto();

            $headers = array('#', 'Folio', 'Cliente', 'Teléfono', 'Teléfono 2', 'WhatsApp', 'Equipo', 'Estado', 'Total', 'Días pendiente', 'Fecha Ingreso', 'Asesor');
            $rows = array();
            $sumaTotal = 0.0;

            foreach ($ordenes as $key => $value) {
                $cliente = ModeloClientes::mdlMostrarClientes("clientesTienda", "id", $value["id_usuario"]);
                $clienteData = $cliente[0] ?? null;

                $nombreCliente = $clienteData ? $clienteData["nombre"] : "Desconocido";
                $telefono = "";
                $telefonoDos = "";
                $whatsapp = "";

                if ($clienteData) {
                    $t1 = (string)($clienteData["telefono"] ?? "");
                    $t2 = (string)($clienteData["telefonoDos"] ?? "");

                    $telefono = (strlen($t1) == 10 && is_numeric($t1)) ? $t1 : "";
                    $telefonoDos = (strlen($t2) == 10 && is_numeric($t2)) ? $t2 : "";

                    $finalPhone = $telefono != "" ? $telefono : $telefonoDos;
                    $whatsapp = ($finalPhone != "") ? "52" . $finalPhone : "";
                }

                $asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
                $nombreAsesor = $asesor["nombre"] ?? "";

                $marca = $value["marcaDelEquipo"] ?? "";
                $modelo = $value["modeloDelEquipo"] ?? "";
                $equipo = trim($marca . " " . $modelo);

                $diasPendiente = "";
                $fechaIng = $value["fecha_ingreso"] ?? "";
                if ($fechaIng != "") {
                    $diff = (new \DateTime($fechaIng))->diff(new \DateTime());
                    $diasPendiente = $diff->days;
                }

                $rows[] = array(
                    $key + 1,
                    $value["id"],
                    $nombreCliente,
                    $telefono,
                    $telefonoDos,
                    $whatsapp,
                    $equipo,
                    $value["estado"],
                    floatval($value["total"]),
                    $diasPendiente,
                    $fechaIng,
                    $nombreAsesor
                );

                $sumaTotal += floatval($value["total"]);
            }

            ExcelExportHelper::downloadXlsx($filename, $headers, $rows, array(
                'sheetName' => 'CRM Contacto',
                'title' => 'CRM — Seguimiento a Clientes',
                'subtitle' => 'Estados: Pendiente de Autorización + Terminada | Rango: ' . $rangoTexto . ' | Generado: ' . date('Y-m-d H:i'),
                'currencyColumns' => array(8),
                'dateColumns' => array(10),
                'hyperlinkColumns' => array(5 => 'whatsapp_api'),
                'footerRows' => array(
                    array('values' => array(0 => 'Registros', 1 => count($rows), 7 => 'Total', 8 => $sumaTotal))
                )
            ));
        }

        /**
         * CRM Technician Follow-up Export: Aceptada + Revisión
         * For vendedor to pressure technicians to advance orders
         */
        public static function generarReporteCRMSeguimiento($empresaId, $filename) {
            $statusFiltros = array(
                'Aceptado (ok)',
                'En revisión (REV)'
            );

            $ordenes = self::obtenerOrdenesFiltradas($empresaId, $statusFiltros);

            $rangoTexto = self::obtenerRangoTexto();

            $headers = array('#', 'Folio', 'Técnico', 'Cliente', 'Tel. Cliente', 'Equipo', 'Estado', 'Total', 'Días en taller', 'Fecha Ingreso', 'Asesor');
            $rows = array();
            $sumaTotal = 0.0;

            foreach ($ordenes as $key => $value) {
                $tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);
                $nombreTecnico = $tecnico["nombre"] ?? "Sin asignar";

                $cliente = ModeloClientes::mdlMostrarClientes("clientesTienda", "id", $value["id_usuario"]);
                $clienteData = $cliente[0] ?? null;
                $nombreCliente = $clienteData ? $clienteData["nombre"] : "Desconocido";

                $telefono = "";
                if ($clienteData) {
                    $t1 = (string)($clienteData["telefono"] ?? "");
                    $t2 = (string)($clienteData["telefonoDos"] ?? "");
                    $telefono = (strlen($t1) == 10 && is_numeric($t1)) ? $t1 : ((strlen($t2) == 10 && is_numeric($t2)) ? $t2 : "");
                }

                $asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
                $nombreAsesor = $asesor["nombre"] ?? "";

                $marca = $value["marcaDelEquipo"] ?? "";
                $modelo = $value["modeloDelEquipo"] ?? "";
                $equipo = trim($marca . " " . $modelo);

                $diasEnTaller = "";
                $fechaIng = $value["fecha_ingreso"] ?? "";
                if ($fechaIng != "") {
                    $diff = (new \DateTime($fechaIng))->diff(new \DateTime());
                    $diasEnTaller = $diff->days;
                }

                $rows[] = array(
                    $key + 1,
                    $value["id"],
                    $nombreTecnico,
                    $nombreCliente,
                    $telefono,
                    $equipo,
                    $value["estado"],
                    floatval($value["total"]),
                    $diasEnTaller,
                    $fechaIng,
                    $nombreAsesor
                );

                $sumaTotal += floatval($value["total"]);
            }

            ExcelExportHelper::downloadXlsx($filename, $headers, $rows, array(
                'sheetName' => 'CRM Tecnicos',
                'title' => 'CRM — Seguimiento a Técnicos',
                'subtitle' => 'Estados: Aceptada + En Revisión | Rango: ' . $rangoTexto . ' | Generado: ' . date('Y-m-d H:i'),
                'currencyColumns' => array(7),
                'dateColumns' => array(9),
                'footerRows' => array(
                    array('values' => array(0 => 'Registros', 1 => count($rows), 6 => 'Total', 7 => $sumaTotal))
                )
            ));
        }

        /* ── Helpers internos ── */

        private static function obtenerOrdenesFiltradas($empresaId, array $statusFiltros) {
            if (isset($_GET['fechaInicial']) && isset($_GET['fechaFinal'])) {
                $ordenes = ModeloOrdenes::mdlRangoFechasOrdenesPorEmpresa(
                    'ordenes',
                    $_GET['fechaInicial'],
                    $_GET['fechaFinal'],
                    'id_empresa',
                    $empresaId
                );
            } else {
                $ordenes = ModeloOrdenes::mdlMostrarordenesParaValidar('ordenes', 'id_empresa', $empresaId);
            }

            if (!is_array($ordenes)) {
                $ordenes = array();
            }

            $filteredOrdenes = array();
            foreach ($ordenes as $orden) {
                if (in_array($orden['estado'], $statusFiltros, true)) {
                    $filteredOrdenes[] = $orden;
                }
            }

            usort($filteredOrdenes, function ($a, $b) {
                return strtotime((string)($a['fecha_ingreso'] ?? '')) <=> strtotime((string)($b['fecha_ingreso'] ?? ''));
            });

            return $filteredOrdenes;
        }

        private static function obtenerRangoTexto() {
            return (isset($_GET['fechaInicial']) && isset($_GET['fechaFinal']))
                ? (($_GET['fechaInicial'] === $_GET['fechaFinal'])
                    ? $_GET['fechaInicial']
                    : $_GET['fechaInicial'] . ' a ' . $_GET['fechaFinal'])
                : 'Todas las ordenes';
        }
    }
}
?>
