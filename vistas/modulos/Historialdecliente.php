<?php
if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "vendedor"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}

?>
<script>
    $(document).ready(function(){
       
    $('#myModal').modal('toggle');
    $('#ORDEN').hide();
    $('#PEDIDO').hide();
    $( "#btnPEDIDO" ).click(function() {
  $('#PEDIDO').show();
  $('#ORDEN').hide();
  $('#myModal').modal('toggle');
});
$( "#btnORDEN" ).click(function() {
  $('#ORDEN').show();
  $('#PEDIDO').hide();
  $('#myModal').modal('toggle');
});
$( "#btnAMBOS" ).click(function() {
  $('#PEDIDO').show();
  $('#ORDEN').show();
  $('#myModal').modal('toggle');
});

    $('#datatableh').DataTable({
        "language": {
        "decimal": ",",
        "thousands": ".",
        "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        "infoPostFix": "",
        "infoFiltered": "(filtrado de un total de _MAX_ registros)",
        "loadingRecords": "Cargando...",
        "lengthMenu": "Mostrar _MENU_ registros",
        "paginate": {
            "first": "Primero",
            "last": "Último",
            "next": "Siguiente",
            "previous": "Anterior"
        },
        "processing": "Procesando...",
        "search": "Buscar:",
        "searchPlaceholder": "Término de búsqueda",
        "zeroRecords": "No se encontraron resultados",
        "emptyTable": "Ningún dato disponible en esta tabla",
        "aria": {
            "sortAscending":  ": Activar para ordenar la columna de manera ascendente",
            "sortDescending": ": Activar para ordenar la columna de manera descendente"
        },
        //only works for built-in buttons, not for custom buttons
        "buttons": {
            "create": "Nuevo",
            "edit": "Cambiar",
            "remove": "Borrar",
            "copy": "Copiar",
            "csv": "fichero CSV",
            "excel": "tabla Excel",
            "pdf": "documento PDF",
            "print": "Imprimir",
            "colvis": "Visibilidad columnas",
            "collection": "Colección",
            "upload": "Seleccione fichero...."
        },
        "select": {
            "rows": {
                _: '%d filas seleccionadas',
                0: 'clic fila para seleccionar',
                1: 'una fila seleccionada'
            }
        }
    } } );
    $('#datatablep').DataTable({
        "language": {
        "decimal": ",",
        "thousands": ".",
        "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        "infoPostFix": "",
        "infoFiltered": "(filtrado de un total de _MAX_ registros)",
        "loadingRecords": "Cargando...",
        "lengthMenu": "Mostrar _MENU_ registros",
        "paginate": {
            "first": "Primero",
            "last": "Último",
            "next": "Siguiente",
            "previous": "Anterior"
        },
        "processing": "Procesando...",
        "search": "Buscar:",
        "searchPlaceholder": "Término de búsqueda",
        "zeroRecords": "No se encontraron resultados",
        "emptyTable": "Ningún dato disponible en esta tabla",
        "aria": {
            "sortAscending":  ": Activar para ordenar la columna de manera ascendente",
            "sortDescending": ": Activar para ordenar la columna de manera descendente"
        },
        //only works for built-in buttons, not for custom buttons
        "buttons": {
            "create": "Nuevo",
            "edit": "Cambiar",
            "remove": "Borrar",
            "copy": "Copiar",
            "csv": "fichero CSV",
            "excel": "tabla Excel",
            "pdf": "documento PDF",
            "print": "Imprimir",
            "colvis": "Visibilidad columnas",
            "collection": "Colección",
            "upload": "Seleccione fichero...."
        },
        "select": {
            "rows": {
                _: '%d filas seleccionadas',
                0: 'clic fila para seleccionar',
                1: 'una fila seleccionada'
            }
        }
    } } );
  });
</script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
 
<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
<div class="content-wrapper">
	
	<section class="content-header">

		
		<h2><center>Historial de cliente: <?php echo $_GET["nombreCliente"] ?></center></h2>
		
		<button class="btn btn-primary" style="border-radius:15px" data-toggle="modal" data-target="#myModal">

	          

	          BUSCAR POR



	        </button>
		
		<ol class="breadcrumb">
		
			<li><a href="#"><i class="fas fa-dashboard"></i></a></li>

			<li class="active">Cliente </li>

		</ol>

	</section>



	<div class="content">
		
		<div class="row" id="ORDEN">
		    
			
			<div class="col-8">

				<div class="box box-success">
					

					<div class="box-header whith-border"><h3>Ordenes del cliente</h3></div>

					<div class="box-body">
					    
					    
						
						<div class="box">
						    <table id="datatableh" class="table stripe ordenes order-table display compact cell-border hover row-border" style="width:100%">
					        <thead>
            <tr>
                <th>#</th>
                <th>Orden</th>
                <th>Asesor</th>
                <th>Imagenes</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>N/Serie</th>
                <th>Estado</th>
                <th>Total</th>
                <th>Ingreso</th>
                <th></th>
            </tr>
                        </thead>
                        <tbody>
            <?php 

		
			$valor = $_GET["idCliente"];
			$ordenes = ControladorOrdenes::ctrMostrarHistorial($tabla, $valor);
/*LINK DE IMPRESION DE EDITAR ORDEN https://backend.comercializadoraegs.com/index.php?ruta=infoOrden&idOrden=5240&empresa=1&asesor=9&cliente=2726&tecnico=4&pedido=0*/

			foreach ($ordenes as $key => $value) {
			    
				echo ' 
				<tr>
                <td>'.($key+1).'</td>
                <td> <b>'.$value["id"].'</b></td>';
$id_perfil = $value["id_Asesor"];    
$nombreasesor = ControladorClientes::ctrMostrarAsesor($tabla, $id_perfil);  
foreach ($nombreasesor as $val){ 
            echo '<td>'.$val["nombre"].'</td>';
            } 
                
                echo'<td>';
                $AlbumDeImagenes = json_decode($value["multimedia"], true);
  $i = 0;
  foreach ($AlbumDeImagenes as $key => $valueImagenes) {
     echo '<img class="img-thumbnail" width="100px" src="'.$valueImagenes["foto"].'">';
      if (++$i == 2) break;
  }
                if($value["marcaDelEquipo"]==""){
                    echo '</td><td style="background-color:#EF9A9A;">S/N</td>';  
                }else{
                 echo '</td><td >'.$value["marcaDelEquipo"].'</td>';   
                }
                if($value["numeroDeSerieDelEquipo"]==""){
                    echo '<td style="background-color:#EF9A9A;">S/N</td>';  
                }else{
                 echo '<td>'.$value["modeloDelEquipo"].'</td>';   
                }
                if($value["modeloDelEquipo"]==""){
                    echo '<td style="background-color:#EF9A9A;">S/N</td>';  
                }else{
                 echo '<td>'.$value["numeroDeSerieDelEquipo"].'</td>';   
                }
                echo '
                <td>'.$value["estado"].'</td>
                <td>'.$value["total"].'</td>
                <td>'.$value["fecha_ingreso"].'</td>
                <td><a class="btn btn-warning" href="index.php?ruta=infoOrden&idOrden='.$value["id"].'&empresa='.$value["id_empresa"].'&asesor='.$value["id_Asesor"].'&cliente='.$value["id_usuario"].'&tecnico='.$value["id_tecnico"].'&tecnicodos='.$value["id_tecnicoDos"].'&pedido='.$value["id_pedido"].'"><i class="fas fa-eye" target="_blank"></i></a></td>
                </tr>';
			}

		?>
            
					 </tbody>       
					    </table>
							



</div>
</div>
</div>
</div>
</div>
<div class="row" id="PEDIDO">
		    
			
			<div class="col-8">

				<div class="box box-primary">
					

					<div class="box-header whith-border"><h3>Pedidos del cliente</h3></div>

					<div class="box-body">
					    
					    
						
						<div class="box">
						    <table id="datatablep" class="table stripe ordenes order-table display compact cell-border hover row-border" style="width:100%">
					        <thead>
            <tr>
                <th>#</th>
                <th>Pedido</th>
                <th>Asesor</th>
                <th>Orden</th>
                <th>Estado</th>
                <th>Productos</th>
                <th>Adeudo</th>
                <th>Total</th>
                <th></th>
            </tr>
                        </thead>
                        <tbody>
            <?php 

		
			$valoru = $_GET["idCliente"];
			$pedidos = ControladorPedidos::ctrMostrarHistorial($tabla, $valoru);
/*LINK DE IMPRESION DE EDITAR ORDEN https://backend.comercializadoraegs.com/index.php?ruta=infopedido&idPedido=2411&empresa=1&asesor=34&cliente=2974*/
			foreach ($pedidos as $keyp => $valu) {
				echo ' 
				<tr>
                <td>'.($keyp+1).'</td>
                <td><b>'.$valu["id"].'</b></td>
                ';
                $id_perfil = $valu["id_Asesor"];    
$nombreasesor = ControladorClientes::ctrMostrarAsesor($tabla, $id_perfil);  
foreach ($nombreasesor as $val){ 
            echo '<td>'.$val["nombre"].'</td>';
            }
                if($valu["id_orden"]=="0"){
                    echo '<td style="background-color:#EF9A9A;">S/O</td>';  
                }else{
                 echo '<td>Orden: <b>'.$valu["id_orden"].'</b></td>';   
                }
                echo '
                <td>'.$valu["estado"].'</td><td>';
                $Productos = json_decode($valu["productos"], true);
  $i = 0;
  foreach ($Productos as $key => $valueProductos) {
     echo '<div>'.$valueProductos["Descripcion"].'</div>';
      if (++$i == 1) break;
  }
  echo '</td><td>'.$valu["adeudo"].'</td><td>'.$valu["total"].'</td><td><a class="btn btn-warning" href="index.php?ruta=infopedido&idPedido='.$valu["id"].'&empresa='.$valu["id_empresa"].'&asesor='.$valu["id_Asesor"].'&cliente='.$valu["id_cliente"].'"><i class="fas fa-eye" target="_blank"></i></a></td>';
			}

		?>
            
					 </tbody>       
					    </table>
							



</div>
</div>
</div>
</div>
</div>
</div>
</div>

<!-- Modal HTML -->
        <div id="myModal" class="modal fade">
            <div class="modal-dialog modal-login">
                <div class="modal-content">
                    <div class="modal-header">
                        <center><h3 class="modal-title">BUSCAR HISTORIAL DE</h3></center>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        
                            <div class="form-group">
                                <button id="btnORDEN" class="btn btn-success btn-block btn-lg"><i class="fas fa-clipboard-list"></i><h3>ORDENES</h3> </button>
                            </div>
                            <div class="form-group">
                                <button id="btnPEDIDO" class="btn btn-primary btn-block btn-lg"><i class="fas fa-box-open"></i><h3>PEDIDOS</h3> </button>
                            </div>
                            <div class="form-group">
                                <button id="btnAMBOS" class="btn btn-warning btn-block btn-lg"><i class="fas fa-history"></i> <h3>TODOS LOS REGISTROS</h3></button>
                            </div>
                            
                        
                    </div>
                    
                </div>
            </div>
        </div>




