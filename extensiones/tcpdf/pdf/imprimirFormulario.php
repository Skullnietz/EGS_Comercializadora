<?php
require_once "../../../modelos/conexion.php";

if (!isset($_GET["idObs"])) {
    die("ID de observacion no proporcionado");
}

$idObs = intval($_GET["idObs"]);

try {
    $pdo = Conexion::conectar();
    $stmt = $pdo->prepare("SELECT observacion, fecha FROM observacionesOrdenes WHERE id = :id");
    $stmt->bindParam(":id", $idObs, PDO::PARAM_INT);
    $stmt->execute();
    $observacion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$observacion) {
        die("Observacion no encontrada en la base de datos.");
    }

    $obsStr = trim($observacion["observacion"]);
    
    if (strpos($obsStr, "FORMULARIO_TABLET_JSON:") !== 0) {
        die("Esta observacion no es un formulario digital.");
    }

    $jsonStr = substr($obsStr, 24);
    $formData = json_decode($jsonStr, true);

    if (!is_array($formData)) {
        die("Error leyendo el formato digital JSON.");
    }

} catch (Exception $e) {
    die("Error de Base de Datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Imprimir Formulario EGS</title>
    <style>
        @page {
            size: letter;
            margin: 20mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            margin: 0;
            padding: 0;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #ccc;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0 0 5px 0;
            font-size: 22px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 14px;
            color: #555;
        }
        .info-basica {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .info-basica strong {
            display: inline-block;
            width: 150px;
        }
        table.respuestas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.respuestas th, table.respuestas td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            font-size: 13px;
        }
        table.respuestas th {
            background: #f4f5f7;
            width: 35%;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
        }
        .firma-container {
            margin-top: 40px;
            text-align: center;
            page-break-inside: avoid;
        }
        .firma-container img {
            max-width: 300px;
            height: auto;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
            margin-bottom: 5px;
        }
        .firma-container p {
            margin: 0;
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            margin-top: 40px;
            font-size: 11px;
            color: #888;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print();" style="padding: 10px 20px; font-size: 16px; background: #6366f1; color: white; border: none; border-radius: 5px; cursor: pointer;">Imprimir Documento</button>
    </div>

    <div class="header">
        <h1><?php echo htmlspecialchars($formData["tipo_formulario"]); ?></h1>
        <p>Comercializadora EGS - Resguardo Digital de Ordenes</p>
    </div>

    <div class="info-basica">
        <div><strong>Orden Asignada:</strong> #<?php echo htmlspecialchars($_GET["idOrden"]); ?></div>
        <div style="margin-top:8px"><strong>Equipo:</strong> <?php echo htmlspecialchars($formData["marcaModelo"]); ?></div>
        <div style="margin-top:8px"><strong>Fecha de Registro:</strong> <?php echo date("d/m/Y H:i:s", strtotime($observacion["fecha"])); ?></div>
    </div>

    <table class="respuestas">
        <tbody>
            <?php foreach($formData["respuestas"] as $pregunta => $respuesta): 
                $preguntaP = str_replace("_", " ", $pregunta);    
            ?>
            <tr>
                <th><?php echo htmlspecialchars($preguntaP); ?></th>
                <td><?php echo htmlspecialchars($respuesta); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (isset($formData["firma"]) && !empty($formData["firma"])): ?>
    <div class="firma-container">
        <img src="<?php echo htmlspecialchars($formData["firma"]); ?>" alt="Firma del Cliente">
        <p>FIRMA DIGITAL DEL CLIENTE</p>
        <p style="font-size:12px; font-weight:normal; margin-top:5px; color:#555">Este documento firma conformidad de las condiciones estipuladas.</p>
    </div>
    <?php endif; ?>

    <div class="footer">
        Documento generado automáticamente por el sistema de tablets EGS - www.comercializadoraegs.com
    </div>

</body>
</html>
