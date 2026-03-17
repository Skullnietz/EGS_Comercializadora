<div class="content-wrapper">
  
  <section class="content-header">
    
    <?php

      $item = "id";
      $valor = $_GET["idTicket"];

      $respuesta = ControladorTickets::ctrMostrarTickets($item,$valor);


    ?>

    <h2><center>Ticket Número: <?echo $_GET["idTicket"]?></center></h2>

    <ol class="breadcrumb">
    	
    	<li><a href="#"><i class="fas fa-dashboard"></i> Inicio</a></li>

      	<li class="active">Ticket <?echo $_GET["idTicket"]?></li>

    </ol>

  </section>

  <section class="content">
  	
  	<div class="row">
  		
  		<div class="col-lg-12 col-xs-12">
  			
  			<div class="box box-success">

  				<div class="box-header with-border"></div>

  				<div class="box-body">
  					
  					<div class="box">
  						<!--ENTRADA DEL CODIGO DEL TICKET-->
  						<div class="form-group">
  							
  							<div class="input-group">

  								<span class="input-group-addon"><i class="fas fa-ticket"></i></span>

  								<input type="text" class="form-control input-lg" value="<?php echo $respuesta["codigo"] ?>" readonly>
  								
  							</div>

  						</div>
  						<!--ENTRADA PARA PRODUCTOS DEL TICKET-->
  						<div class="col-lg-12">
  							
  							<div class="box box-warning">
  								
  								<div class="box-header whith-border"></div>

  								<div class="box-body">
  									
  									<table class="table table-borderd table-striped dt-responsive">
  										
  										<thead>
  											
  											<tr>
  												
  												<th>Productos</th>
  												<th>Cantidad</th>
  												<th>Sub total</th>
  												<th>total</th>

  											</tr>

  										</thead>

  										<tbody>
  											
  											<?php

  											$productos = json_decode($respuesta["productos"], true);	

      										foreach ($productos as $key => $valueProductos) {
      								

      											echo'<tr>

      												<td>'.$valueProductos["titulo"].'</td>
      												<td>'.$valueProductos["cantidad"].'</td>
      												<td>$'.$valueProductos["total"].'</td>

      											</tr>';		
      										}


  											?>
  											<tr>
      											
      											<td></td>
      											<td></td>
      											<td></td>
      											<td>

      									
      												<div class="input-group">
      										
      													<span class="input-group-addon"><i class="fas fa-dollar"></i>			</span>
      													<input type="number" class="form-control" value="<?php echo $respuesta["total"] ?>" readonly>

      												</div>

      								
      											</td>	
      							
      										</tr>


  										</tbody>


  									</table>

  								</div>

  							</div>

  						</div>

  					</div>

  				</div>  				

  			</div>

  		</div>

  	</div>

  </section>


</div>