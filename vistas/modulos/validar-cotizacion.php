<?php
$codigo = isset($_GET["codigo"]) ? trim($_GET["codigo"]) : null;
$cotizacion = null;

if ($codigo) {
    $cotizaciones = CotizacionesControlador::ctrMostrarCotizacion("codigo_qr", $codigo);
    $cotizacion = !empty($cotizaciones) ? $cotizaciones[0] : null;
}
?>
<div class="content-wrapper" style="margin-left: 0 !important; background: #f4f6f9;">
    <section class="content"
        style="padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 90vh;">

        <div class="box box-solid"
            style="width: 100%; max-width: 600px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

            <?php if (is_array($cotizacion) && !empty($cotizacion)): ?>
                <div class="box-header with-border"
                    style="background: #00a65a; color: white; border-radius: 10px 10px 0 0; text-align: center; padding: 20px;">
                    <h3 class="box-title" style="font-size: 24px; font-weight: bold;">
                        <i class="fa fa-check-circle"></i> Cotización Válida
                    </h3>
                </div>

                <div class="box-body" style="padding: 30px;">
                    <div class="text-center" style="margin-bottom: 20px;">
                        <img src="vistas/img/plantilla/Captura3.PNG" alt="Logo" style="max-height: 80px;">
                        <h4 style="margin-top: 10px; font-weight: 600;">COMERCIALIZADORA EGS</h4>
                    </div>

                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>Cliente</b> <a class="pull-right"><?php echo $cotizacion["nombre_cliente"]; ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Fecha</b> <a class="pull-right"><?php echo $cotizacion["fecha"]; ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Vigencia</b> <a class="pull-right"><?php echo $cotizacion["vigencia"]; ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Total</b> <a class="pull-right"
                                style="font-size: 18px; font-weight: bold;">$<?php echo number_format($cotizacion["total"], 2); ?></a>
                        </li>
                    </ul>

                    <h5 style="margin-top: 20px; font-weight: bold;">Productos:</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th class="text-right">Cant</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $productos = json_decode($cotizacion["productos"], true);
                                foreach ($productos as $producto):
                                    ?>
                                    <tr>
                                        <td><?php echo $producto["descripcion"]; ?></td>
                                        <td class="text-right"><?php echo $producto["cantidad"]; ?></td>
                                        <td class="text-right">$<?php echo number_format($producto["total"], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <div class="box-header with-border"
                    style="background: #dd4b39; color: white; border-radius: 10px 10px 0 0; text-align: center; padding: 20px;">
                    <h3 class="box-title" style="font-size: 24px; font-weight: bold;">
                        <i class="fa fa-times-circle"></i> Cotización Inválida
                    </h3>
                </div>
                <div class="box-body" style="padding: 30px; text-center;">
                    <p class="text-center" style="font-size: 16px; color: #555;">
                        El código proporcionado no corresponde a ninguna cotización registrada en nuestro sistema.
                    </p>
                    <div class="text-center" style="margin-top: 20px;">
                        <a href="inicio" class="btn btn-default">Ir al Inicio</a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>