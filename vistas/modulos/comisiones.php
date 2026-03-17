<?php
if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "tecnico" AND $_SESSION["perfil"]!= "secretaria"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;
}
?>
<script>
swal({
  title: "Las comisiones se borraran al terminar el mes",
  text: "ES SU RESPONSABILIDAD DESCARGARLAS ANTES",
  type: "warning",
  buttons: true,
  dangerMode: true,
})

    $(document).ready(function(){
        $('#1ERAQUINCENA').hide(); 
         $('#2DAQUINCENA').hide();
       $('#primera').click( function(){
          $('#1ERAQUINCENA').show(); 
          $('#2DAQUINCENA').hide();
       })
       $('#segunda').click( function(){
          $('#1ERAQUINCENA').hide(); 
          $('#2DAQUINCENA').show();
       })

   
    $('#tabletecnico').DataTable({
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
    } });
    
    
    $('#tabletecnico2').DataTable({
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
    } });
    
    });
</script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>

<div class="content-wrapper">
	
	<section class="content-header">
		
		<h2><center>Comisiones</center></h2><br><br><button class="btn btn-success" id="primera"> Primera Quincena </button>    <button class="btn btn-success" id="segunda"> Segunda Quincena </button>

		<ol class="breadcrumb">
			
			<li><a href="#"><i class="fas fa-dashboard"></i>Inicio</a></li>

			<li class="active">Comisiones</li>

		</ol>

	</section>

	<section class="content">
		
<div class="box box-success" id="1ERAQUINCENA">
					

					<div class="box-header whith-border"><h3>Comisiones 1ra Quincena</h3></div>
		<div class="box-body">

	        <div class="box-tools">
				
	        	


				 

			</div>

		

		    <table id="tabletecnico" class="table stripe ordenes order-table display compact cell-border hover row-border" style="width:100%">
        
          <thead>
            
            <tr>
              
              <th>#</th>
              <th>No. Orden</th> 
              
              <th>Cliente</th>
              <th>Comision</th>
              <th>Imagen</th>
              <th>Fecha</th>

            </tr>

          </thead> 
          
          <tbody>
              <?php 
              $itemUno = "correo";

      $valorUno =  $_SESSION["email"];



      $tecnicoEnSession = ControladorTecnicos::ctrMostrarTecnicos($itemUno,$valorUno);



      $session_id = $tecnicoEnSession["id"];
              
              $ordenesECPQ = controladorOrdenes::ctrMostrarComisionesPorPersonaPrimera($session_id);
              $sumandotecnico = 0;
            foreach ($ordenesECPQ as $key => $value) {
                $item = "id";

                  $valor = $value["id_usuario"];



                  $usuario = ControladorClientes::ctrMostrarClientesOrdenes($item,$valor);



                  $NombreUsuario = $usuario["nombre"];
$total = $value["total"];
$totalinversiones=$value["totalInversion"];
$totalsininversion= $total-$totalinversiones;
$totalsiniva = ($totalsininversion/1.16);
$totalsiniva1 = ($total/1.16);
$subtotalE= $total-($totalinversiones+$ivadeltotal);
if($tecnicoEnSession["departamento"]='electronica' AND $tecnicoEnSession["departamento"]='impresoras' ){
    $comision = ($totalsiniva*.2); 
}else{
    $comision = ($totalsiniva1*.04); 
}

                  
               echo'
              <tr>
                  <td>'.($key+1).'</td>
                  <td>'.$value["id"].'</td>
                  <td>'.$NombreUsuario.'</td>';
                if($value["totalInversion"]==0 AND $comision>100){echo'<td style="background-color: yellow;">$ '.number_format($comision,2).' <small>*Probable falta de inversion</small></td><td>';}else{
                  echo'<td>$ '.number_format($comision,2).'</td><td>';} 
                  $sumandotecnico = $sumandotecnico + $comision;
                  $AlbumDeImagenes = json_decode($value["multimedia"], true);
                  $i = 0;
                  foreach ($AlbumDeImagenes as $key => $valueImagenes) {
                     echo '<img class="img-thumbnail" width="100px" src="'.$valueImagenes["foto"].'">';
                      if (++$i == 1) break;
                  }
                  echo'</td>
                  <td>'.$value["fecha_Salida"].'</td>
                  
              </tr>
              '; 
            }
              
              ?>
              
              
          </tbody>
          <tfoot>
              <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><b>TOTAL: $<?php
echo number_format($sumandotecnico,2);?> </b><small>  *Comision aproximada, sujeto a cambios*</small></td>
                  <td></td>
                  <td></td>
              </tr>
          </tfoot>
          
		

	</table>
</div>
</div>
	

<!--- SEGUNDA QUINCENA TECNICOS --->
<div class="box box-success" id="2DAQUINCENA">
					

					<div class="box-header whith-border"><h3>Comisiones 2da Quincena</h3></div>
		<div class="box-body">

	        <div class="box-tools">
				
	        	


				 

			</div>

		

		    <table id="tabletecnico2" class="table stripe ordenes order-table display compact cell-border hover row-border" style="width:100%">
        
          <thead>
            
            <tr>
              
              <th>#</th>
              <th>No. Orden</th> 
              
              <th>Cliente</th>
              <th>Comision</th>
              <th>Imagen</th>
              <th>Fecha</th>

            </tr>

          </thead> 
          
          <tbody>
              <?php 
              



      $session_id2 = $tecnicoEnSession["id"];
              
              $ordenesECSQ = ControladorOrdenes::ctrMostrarComisionesPorPersonaSegunda($session_id2);
              $sumandotecnico = 0;
            foreach ($ordenesECSQ as $key => $valueSegunda) {
                $item = "id";

                  $valor = $valueSegunda["id_usuario"];



                  $usuario = ControladorClientes::ctrMostrarClientesOrdenes($item,$valor);



                  $NombreUsuario = $usuario["nombre"];
$total = $valueSegunda["total"];
$totalinversiones=$valueSegunda["totalInversion"];
$totalsininversion= $total-$totalinversiones;
$totalsiniva = ($totalsininversion/1.16);
$totalsiniva1 = ($total/1.16);
$subtotalE= $total-($totalinversiones+$ivadeltotal);
if($tecnicoEnSession["departamento"]='electronica' AND $tecnicoEnSession["departamento"]='impresoras' ){
    $comision = ($totalsiniva*.2); 
}else{
    $comision = ($totalsiniva1*.04); 
}

                  
               echo'
              <tr>
                  <td>'.($key+1).'</td>
                  <td>'.$valueSegunda["id"].'</td>
                  <td>'.$NombreUsuario.'</td>';
                if($value["totalInversion"]==0 AND $comision>100){echo'<td style="background-color: yellow;">$ '.number_format($comision, 2).' <small>*Probable falta de inversion</small></td><td>';}else{
                  echo'<td>$ '.number_format($comision, 2).'</td><td>';} 
                  $sumandotecnico = $sumandotecnico + $comision;
                  $AlbumDeImagenes = json_decode($valueSegunda["multimedia"], true);
                  $i = 0;
                  foreach ($AlbumDeImagenes as $key => $valueImagenes) {
                     echo '<img class="img-thumbnail" width="100px" src="'.$valueImagenes["foto"].'">';
                      if (++$i == 1) break;
                  }
                  echo'</td>
                  <td>'.$valueSegunda["fecha_Salida"].'</td>
                  
              </tr>
              '; 
            }
              
              ?>
              
              
          </tbody>
          <tfoot>
              <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><b>TOTAL: $<?php
echo number_format($sumandotecnico, 2);?> </b><small>  *Comision aproximada, sujeto a cambios*</small></td>
                  <td></td>
                  <td></td>
              </tr>
          </tfoot>
          
		

	</table>

	</section>
</div>
</div>




