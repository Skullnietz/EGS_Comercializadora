<?php
// Un fallo de la entrega debe detener los guardados secundarios y sus avisos de éxito.
$controlOrden = new controladorOrdenes();
$controlOrden->ctrEditarObservacionesYaExistentes();
$ordenGuardada = $controlOrden->ctrEditarOrdenDinamica();
if ($ordenGuardada !== false) {
    $controlOrden->ctrEditarInversiones();
    $controlPedido = new ControladorPedidos();
    $controlPedido->ctrEditarPedidoEnEstado();
    $controlOrden->ctrlAgregarTipoDeOrden();
    $controlOrden->ctrlAgregarRecargaDeCartucho();
    $controlOrden->ctrlAgregarNuevaPartidaTecnicoDos();
    $controlOrden->ctrlAgregarTecnicoDos();
    $controlOrden->ctrlEditarMarca();
}
