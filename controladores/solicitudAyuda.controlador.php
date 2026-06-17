<?php

class controladorSolicitudAyuda
{
    /*=============================================
    LISTAR SOLICITUDES DEL CLIENTE
    =============================================*/
    static public function ctrListar($idCliente)
    {
        ModeloSolicitudAyuda::mdlCrearTabla();
        return ModeloSolicitudAyuda::mdlListarPorCliente(intval($idCliente));
    }

    /*=============================================
    GUARDAR SOLICITUD (POST público desde el portal)
    Lee 'mensajeAyuda' y opcional 'idOrdenAyuda'.
    El idCliente lo provee la vista (validado vía token).
    Devuelve "ok" | "vacio" | "duplicate" | "error".
    =============================================*/
    static public function ctrlGuardar($idCliente)
    {
        if (!isset($_POST["mensajeAyuda"])) {
            return null;
        }

        $mensaje = trim((string) $_POST["mensajeAyuda"]);
        if ($mensaje === "") {
            return "vacio";
        }
        if (mb_strlen($mensaje) > 2000) {
            $mensaje = mb_substr($mensaje, 0, 2000);
        }

        ModeloSolicitudAyuda::mdlCrearTabla();

        if (ModeloSolicitudAyuda::mdlDuplicadoReciente(intval($idCliente), $mensaje)) {
            return "duplicate";
        }

        $idOrden = null;
        if (isset($_POST["idOrdenAyuda"]) && $_POST["idOrdenAyuda"] !== "") {
            $idOrden = intval($_POST["idOrdenAyuda"]);
            if ($idOrden <= 0) $idOrden = null;
        }

        $datos = array(
            "id_cliente" => intval($idCliente),
            "id_orden"   => $idOrden,
            "mensaje"    => $mensaje
        );

        return ModeloSolicitudAyuda::mdlCrear($datos);
    }
}
