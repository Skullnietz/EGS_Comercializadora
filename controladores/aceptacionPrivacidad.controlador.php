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
    Lee del POST 'aceptaPrivacidad' (1 acepta, 0 rechaza).
    El idCliente lo provee la vista (ya validado vía token).
    Devuelve "ok" | "invalida" | "error".
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

        ModeloAceptacionPrivacidad::mdlCrearTabla();

        $datos = array(
            "id_cliente" => intval($idCliente),
            "aceptado"   => intval($valor),
            "ip"         => isset($_SERVER["REMOTE_ADDR"]) ? substr($_SERVER["REMOTE_ADDR"], 0, 45) : null,
            "user_agent" => isset($_SERVER["HTTP_USER_AGENT"]) ? substr($_SERVER["HTTP_USER_AGENT"], 0, 255) : null
        );

        return ModeloAceptacionPrivacidad::mdlGuardar($datos);
    }
}
