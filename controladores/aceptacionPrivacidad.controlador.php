<?php

class controladorAceptacionPrivacidad
{
    /*=============================================
    OBTENER LA DECISIÓN ACTUAL DEL CLIENTE
    Devuelve fila o null. Asegura que la tabla exista.
    =============================================*/
    static public function ctrObtener($idCliente)
    {
        ModeloAceptacionPrivacidad::mdlCrearTabla();
        return ModeloAceptacionPrivacidad::mdlObtener(intval($idCliente));
    }

    /*=============================================
    GUARDAR DECISIÓN (acepta / rechaza)

    Lee del POST:
      - 'aceptaPrivacidad' (1 acepta, 0 rechaza)
      - 'firmaPrivacidad'  (opcional; data URL image/png base64)
        ► Si aceptado=1, la firma es OBLIGATORIA. Sin firma → "sin_firma".
        ► Si aceptado=0, la firma se ignora (rechazo no requiere firma).

    El idCliente lo provee la vista (validado vía token).
    Devuelve "ok" | "invalida" | "sin_firma" | "error".
    =============================================*/
    static public function ctrlGuardar($idCliente)
    {
        if (!isset($_POST["aceptaPrivacidad"])) {
            return null;
        }

        $valor = $_POST["aceptaPrivacidad"];
        if ($valor !== "1" && $valor !== "0") {
            return "invalida";
        }
        $aceptado = intval($valor);

        // Firma: requerida solo cuando acepta
        $firma = isset($_POST["firmaPrivacidad"]) ? (string) $_POST["firmaPrivacidad"] : "";
        if ($aceptado === 1) {
            // Validación básica del data URL
            if ($firma === "" || strpos($firma, "data:image/png;base64,") !== 0) {
                return "sin_firma";
            }
            // Tamaño máximo razonable (~1 MB en base64)
            if (strlen($firma) > 1500000) {
                return "invalida";
            }
        } else {
            $firma = null; // rechazo: no guardamos firma
        }

        ModeloAceptacionPrivacidad::mdlCrearTabla();

        $datos = array(
            "id_cliente" => intval($idCliente),
            "aceptado"   => $aceptado,
            "firma"      => $firma,
            "ip"         => isset($_SERVER["REMOTE_ADDR"]) ? substr($_SERVER["REMOTE_ADDR"], 0, 45) : null,
            "user_agent" => isset($_SERVER["HTTP_USER_AGENT"]) ? substr($_SERVER["HTTP_USER_AGENT"], 0, 255) : null
        );

        return ModeloAceptacionPrivacidad::mdlGuardar($datos);
    }
}
