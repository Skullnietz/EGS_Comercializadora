<div class="content-wrapper">

    <section class="content-header">

        <h1>

            Historial de Cotizaciones

        </h1>

        <ol class="breadcrumb">

            <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>

            <li class="active">Historial de Cotizaciones</li>

        </ol>

    </section>

    <section class="content">

        <div class="box">

            <div class="box-header with-border">

                <a href="index.php?ruta=cotizacion" class="btn btn-primary">

                    Nueva Cotización

                </a>

            </div>

            <div class="box-body">

                <table
                    class="table stripe ordenes order-table display compact cell-border hover row-border dt-responsive tablas"
                    width="100%">

                    <thead>

                        <tr>

                            <th style="width:10px">#</th>
                            <th>Fecha</th>
                            <th>Empresa</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Vigencia</th>
                            <th>Total</th>
                            <th>QR</th>
                            <th>Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php

                        $item = null;
                        $valor = null;

                        $cotizaciones = ControladorCotizaciones::ctrMostrarCotizaciones($item, $valor);

                        foreach ($cotizaciones as $key => $value) {

                            echo '<tr>

                    <td>' . ($key + 1) . '</td>

                    <td>' . $value["fecha"] . '</td>

                    <td>' . $value["empresa"] . '</td>

                    <td>' . $value["nombre_cliente"] . '</td>

                    <td>' . $value["id_vendedor"] . '</td>

                    <td>' . $value["vigencia"] . '</td>

                    <td>$' . number_format($value["total"], 2) . '</td>

                    <td><img src="' . $value["codigo_qr"] . '" width="60" ></td>

                    <td>

                      <div class="btn-group">
                          
                        <!-- Aquí se podría agregar botón para editar o reimprimir si se tuviera la lógica -->
                        <button class="btn btn-info"><i class="fas fa-eye"></i></button>

                      </div>  

                    </td>

                  </tr>';
                        }


                        ?>

                    </tbody>

                </table>

            </div>

        </div>

    </section>

</div>

<script>
    $(document).ready(function () {
        $('.tablas').DataTable({
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }
        });
    });
</script>