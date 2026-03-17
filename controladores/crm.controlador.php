<?php
class ControladorCRM{

    function ctrlMostrarClientesenCRM($item,$valor){

        $tabla = "clientesTienda";

        $respuesta = ModeloCRM::mdlMostrarClientes($tabla,$item,$valor);

        return $respuesta;
    }
}