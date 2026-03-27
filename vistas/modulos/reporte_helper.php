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

            $isReporteAceptados  = ($statusFilter === 'Aceptado (ok)');
            $isReporteTerminados = ($statusFilter === 'Terminada (ter)');
            $isReporteEntregados = ($statusFilter === 'Entregado (Ent)');
            $isReporteIngresos   = ($statusFilter === 'En revisión (REV)');
            $isEstiloAceptados   = ($isReporteAceptados || $isReporteTerminados || $isReporteEntregados || $isReporteIngresos);

            if ($isReporteTerminados) {
                $headers = array('Orden', 'EQUIPO', 'Asesor', 'Tecnico principal', 'Cliente', 'Telefono', 'Mensaje 1', 'Mensaje 2', 'fecha', 'Estado', 'CANTIDAD', 'FECHA INGRESO');
            } elseif ($isEstiloAceptados) {
                $headers = array('Orden', 'EQUIPO', 'Asesor', 'Tecnico principal', 'Cliente', 'Telefono', 'Mensaje', 'fecha', 'Estado', 'CANTIDAD', 'FECHA INGRESO');
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
                    $t1 = (string)($clienteData["telefono"] ?? "");
                    $t2 = (string)($clienteData["telefonoDos"] ?? "");

                    $validT1 = (strlen($t1) == 10 && is_numeric($t1));
                    $validT2 = (strlen($t2) == 10 && is_numeric($t2));

                    if ($isEstiloAceptados) {
                        $telefono = $validT1 ? $t1 : "";
                        $whatsapp = ($telefono !== "") ? "52" . $telefono : "";
                    } else {
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
                }

                $asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
                $tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

                if ($isEstiloAceptados) {
                    $equipo = trim((string)($value["marcaDelEquipo"] ?? '') . ' ' . (string)($value["modeloDelEquipo"] ?? ''));
                    $mensaje = ($telefono !== "") ? "Enviar Msj" : "";

                    if ($isReporteTerminados) {
                        $rows[] = array(
                            $value["id"] ?? "",
                            $equipo,
                            $asesor["nombre"] ?? "",
                            $tecnico["nombre"] ?? "",
                            $nombreCliente,
                            $telefono,
                            $mensaje,
                            $mensaje,
                            $value["fecha"] ?? "",
                            $value["estado"] ?? "",
                            floatval($value["total"]),
                            $value["fecha_ingreso"] ?? ""
                        );
                    } else {
                        $rows[] = array(
                            $value["id"] ?? "",
                            $equipo,
                            $asesor["nombre"] ?? "",
                            $tecnico["nombre"] ?? "",
                            $nombreCliente,
                            $telefono,
                            $mensaje,
                            $value["fecha"] ?? "",
                            $value["estado"] ?? "",
                            floatval($value["total"]),
                            $value["fecha_ingreso"] ?? ""
                        );
                    }
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

            // Configuración de columnas y mensajes según tipo de reporte
            if ($isReporteTerminados) {
                $currencyColumns     = array(10);
                $dateColumns         = array(8, 11);
                $footerTotalLabelColumn = 9;
                $footerTotalValueColumn = 10;
                $columnWidths        = array(0 => 8);
                $msg1 = 'BUEN DIA LE INFORMAMOS QUE SU EQUIPO YA ESTA TERMINADO OJALÁ PODAMOS CONTAR CON SU RECOLECCIÓN PARA MAYOR INFORMACIÓN 7222831159/7221671684/7222144416 EN UN HORARIO DE LUNES A VIERNES DE 10 A 2 Y DE 4 A 6:30 SÁBADOS DE 9 A 2 GRACIAS. ORDEN *[ORDEN]* ASESOR *[ASESOR]* TÉCNICO *[TECNICO]* https://comercializadoraegs.com';
                $msg2 = 'BUEN DIA PARA BRINDARLE UN MEJOR SERVICIO LE AGRADECERÍAMOS NOS PUEDA CONFIRMAR SU CITA HOY O MAÑANA * PARA LA RECOLECCIÓN DE SU EQUIPO GRACIAS* https://comercializadoraegs.com SI USTED YA PASO POR EL EQUIPO O TIENE CITA PROGRAMADA POR FAVOR INFORMENOS POR ESTE MEDIO GRACIAS';
                $hyperlinkColumns = array(
                    6 => function ($value, $row) use ($msg1) {
                        $digits = preg_replace('/\D+/', '', (string)($row[5] ?? ''));
                        if (strlen($digits) !== 10) return '';
                        $mensaje = str_replace(
                            array('[ORDEN]', '[ASESOR]', '[TECNICO]'),
                            array((string)($row[0] ?? ''), (string)($row[2] ?? ''), (string)($row[3] ?? '')),
                            $msg1
                        );
                        return 'https://api.whatsapp.com/send?phone=52' . $digits . '&text=' . rawurlencode($mensaje);
                    },
                    7 => function ($value, $row) use ($msg2) {
                        $digits = preg_replace('/\D+/', '', (string)($row[5] ?? ''));
                        if (strlen($digits) !== 10) return '';
                        return 'https://api.whatsapp.com/send?phone=52' . $digits . '&text=' . rawurlencode($msg2);
                    }
                );
            } elseif ($isReporteEntregados) {
                $currencyColumns     = array(9);
                $dateColumns         = array(7, 10);
                $footerTotalLabelColumn = 8;
                $footerTotalValueColumn = 9;
                $columnWidths        = array(0 => 8);
                $msgEnt = 'BUEN DIA PARA BRINDARLE UN MEJOR SERVICIO LE AGRADECERÍAMOS NOS PUEDA *COMENTAR COMO ESTA TRABAJANDO EL EQUIPO QUE NOS TRAJO A REPARACION, PARA NOSOTROS ES MUY IMPORTANTE SU SATISFACCION GRACIAS https://comercializadoraegs.com';
                $hyperlinkColumns = array(
                    6 => function ($value, $row) use ($msgEnt) {
                        $digits = preg_replace('/\D+/', '', (string)($row[5] ?? ''));
                        if (strlen($digits) !== 10) return '';
                        return 'https://api.whatsapp.com/send?phone=52' . $digits . '&text=' . rawurlencode($msgEnt);
                    }
                );
            } elseif ($isReporteIngresos) {
                $currencyColumns     = array(9);
                $dateColumns         = array(7, 10);
                $footerTotalLabelColumn = 8;
                $footerTotalValueColumn = 9;
                $columnWidths        = array(0 => 8);
                $msgRev = 'Somos COMERCIALIZADORA EGS * *https://comercializadoraegs.com gracias por venir y permitirnos apoyarte en tu proyecto de REPARACION DE EQUIPOS DE COMPUTO recuerda que es importante seguir en comunicación por este medio en un horario de LUNES A VIERNES DE 10 A 2 Y DE 4 A 6:30 SÁBADOS DE 9 A 2 o a los teléfonos 7222831159/7221671684/7222144416.';
                $hyperlinkColumns = array(
                    6 => function ($value, $row) use ($msgRev) {
                        $digits = preg_replace('/\D+/', '', (string)($row[5] ?? ''));
                        if (strlen($digits) !== 10) return '';
                        return 'https://api.whatsapp.com/send?phone=52' . $digits . '&text=' . rawurlencode($msgRev);
                    }
                );
            } elseif ($isReporteAceptados) {
                $currencyColumns     = array(9);
                $dateColumns         = array(7, 10);
                $footerTotalLabelColumn = 8;
                $footerTotalValueColumn = 9;
                $columnWidths        = array(0 => 8);
                $hyperlinkColumns = array(
                    6 => function ($value, $row) {
                        $digits = preg_replace('/\D+/', '', (string)($row[5] ?? ''));
                        if (strlen($digits) !== 10) return '';
                        $orden = (string)($row[0] ?? '');
                        $mensajeBase = 'NOS DA GUSTO INFORMARTE QUE YA TENEMOS TU PRESUPUESTO PODRÁS COMUNICARTE POR FAVOR PARA EXPLICARTE MEJOR A LOS TELÉFONOS 7222831159/7221671684/7222144416/720-3321271 EN UN HORARIO DE LUNES A VIERNES DE 10 A 2 Y DE 4 A 6:30 SÁBADOS DE 9 A 2 GRACIAS. ORDEN **. ESTE NUMERO ES SOLO PARA MENSAJES';
                        $mensaje = str_replace('**', $orden, $mensajeBase);
                        return 'https://api.whatsapp.com/send?phone=52' . $digits . '&text=' . rawurlencode($mensaje);
                    }
                );
            } else {
                $currencyColumns     = array(8);
                $dateColumns         = array(9);
                $footerTotalLabelColumn = 7;
                $footerTotalValueColumn = 8;
                $columnWidths        = array();
                $hyperlinkColumns    = array(5 => 'whatsapp_api');
            }

            ExcelExportHelper::downloadXlsx($filename, $headers, $rows, array(
                'sheetName' => 'Ordenes',
                'title' => 'Reporte de Ordenes por Estado',
                'subtitle' => 'Estado: ' . $statusFilter . ' | Rango: ' . $rangoTexto . ' | Generado: ' . date('Y-m-d H:i'),
                'baseFontSize' => $isEstiloAceptados ? 9 : 11,
                'currencyColumns' => $currencyColumns,
                'dateColumns' => $dateColumns,
                'columnWidths' => $columnWidths,
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
