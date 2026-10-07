<?php

/** Clasifica rechazos definitivos del servidor sin exponer SQL, cuentas ni contraseñas. */
class PedidosErrores
{
    public static function respuesta(Throwable $error)
    {
        if ($error instanceof PDOException) {
            $codigo = (int)($error->errorInfo[1] ?? $error->getCode());
            if (in_array($codigo, [1044, 1045, 1142, 1143, 1227], true)) {
                $tabla = '';
                if (preg_match('/for table [\'"`]([^\'"`]+)[\'"`]/i', $error->getMessage(), $coincidencia)) {
                    $partes = explode('.', $coincidencia[1]);
                    $tabla = end($partes);
                }
                $motivo = $tabla === 'ordenes'
                    ? 'El servidor no permite vincular la orden con el pedido.'
                    : ($tabla === 'pedidos_guardados'
                        ? 'El servidor no tiene los permisos necesarios para confirmar el guardado.'
                        : 'El servidor no tiene los permisos necesarios para guardar el pedido.');
                return ['http' => 500, 'body' => ['ok' => false, 'tipo' => 'permisos_servidor', 'codigo' => $codigo,
                    'reintentar' => false, 'mensaje' => $motivo . ' Tu captura sigue aquí. Soporte debe corregir los permisos de la base de datos (código ' . $codigo . ').']];
            }
            $definitivo = in_array($codigo, [1054, 1062, 1146, 1406, 1451, 1452], true);
            $mensaje = $codigo === 1406
                ? 'El pedido llegó al límite de espacio para ese dato y el cambio no se guardó. Avisa a soporte (código 1406).'
                : ($codigo === 1146 || $codigo === 1054
                    ? 'Falta actualizar la base de datos para guardar pedidos. Tu captura sigue aquí. Avisa a soporte (código ' . $codigo . ').'
                    : 'La base de datos no confirmó el guardado (código ' . $codigo . '). Tu captura sigue aquí. Avisa a soporte con este código.');
            return ['http' => 500, 'body' => ['ok' => false, 'codigo' => $codigo, 'reintentar' => !$definitivo, 'mensaje' => $mensaje]];
        }
        $validacion = $error instanceof InvalidArgumentException || $error instanceof RuntimeException;
        return ['http' => $validacion ? 409 : 500, 'body' => ['ok' => false, 'reintentar' => false,
            'mensaje' => $validacion ? $error->getMessage() : 'Ocurrió un error interno y el cambio no se guardó. Avisa a soporte.']];
    }
}
