<?php
ob_start();
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<link href="https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/ticket.css" rel="stylesheet" type="text/css">
</head>
<body onload="window.print();">

<?php
  

require_once "../../../controladores/pedidos.controlador.php";
require_once "../../../modelos/pedidos.modelo.php";
require_once "../../../controladores/ventas.controlador.php";
require_once "../../../modelos/ventas.modelo.php";
require_once "../../../controladores/controlador.asesore.php";
require_once "../../../modelos/modelo.asesores.php";
require_once "../../../controladores/clientes.controlador.php";
require_once "../../../modelos/clientes.modelo.php";
require_once "../../../controladores/tecnicos.controlador.php";
require_once "../../../modelos/tecnicos.modelo.php";
/**MANDAR INFORMACION DE LA VENTA**/
class ImprimirTicketsPedido{
  
  function TraerImpresionTicketPedido(){
   
   //TRAER LA INFORMACION DE LA VENTA
      
      $item = "id";
      $valor = $_GET["idPedido"];

      $pedidos = ControladorPedidos::ctrMostrarorpedidosParaValidar($item,$valor);
      foreach ($pedidos as $key => $value) {
        # code...
      
    


      //TRAER LA INFORMACION DE LA EMPRESA
      
      $item = "id";
      $valor = $_GET["empresa"];

      $respuesta = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

      $NombreEmpresa = $respuesta["empresa"];
      $Direccion = $respuesta["direccion"];
      $Telefono = $respuesta["telefono"];
      $Telefono2 = $respuesta["telefonoDos"];
      $Telefono3 = $respuesta["telefonoTres"];
      $Telefono4 = $respuesta["telefonoCuatro"];
      $Facebook = $respuesta["Facebook"];
      $Sitio = $respuesta["Sitio"];

      //TRAER CLIENTE (USUARIO)
      $item = "id";
      $valor = $_GET["cliente"];

      $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

      $NombreUsuario = $usuario["nombre"];

       echo '<div class="zona_impresion">
         <br>

<!-- Mostramos los datos de la empresa en el documento HTML -->
                        <div><img src="https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/images/logoEGS (1).png" alt="LOGO" style="float: left"></div>
                        <center>
                        <div style="margin-top:20px;font-size: 200%"><strong>'.$NombreEmpresa.'</strong></div>
                        <hr><div style="font-size: 100%"><b>'.$Sitio.'</b></div><br>
                        <div><h3><b>'.$Direccion.' <h3><b> TELEFONOS<br> '.$Telefono.' <br> '.$Telefono3.' <br> '.$Telefono4.'</b></h3></div><div>Sólo Whatsapp (No llamar): '.$Telefono2.'</div>
                        <div><b><h4>Conoce el estado de tu orden en: comercializadoraegs.com/ordenes</h4> </b></div></center>
                
                
<b><hr></b>

        <table border="0" align="center" width="200px">';
            
            
            $fecha = date_create($vvalue["fechaDePedido"]);
            $nuevaFecha = date_format($fecha, 'd/m/Y H:i:s');

            echo'<tr>
                <td align="center">'.$nuevaFecha.'</td>
            </tr>

            <tr>
                <td align="center"><b>Pedido No.'.$value["id"].'</b></td>
            </tr>

            <tr>
                <td align="center"><b>'.$value["estado"].'</b></td>
            </tr>

            <!-- Mostramos los datos del cliente en el documento HTML -->
       
            <tr>
              <td align="center">Cliente: '.$NombreUsuario.'</td>
            </tr>
   
        </table>

        <br>

        <!-- Mostramos los detalles de la venta en el documento HTML -->

        <table border="0" align="center" width="10px">
            
            <tr>
                <td>Pzas.</td>
                <td>Productos<td>

                <!---<td align="left">Precio</td>--->
                <td align="left">TOTAL</td>

            </tr>

          <tr>

            <td colspan="4">==========================================</td>
             
          </tr>';
              
              //$precioTotal = number_format($item["pago"], 2);
              if ($value["productoUno"] != undefined and $value["productoUno"] != "") {
              
               echo '<tr>
                      
                    </tr>';
          

                  echo'<!-- Mostramos los totales de la venta en el documento HTML -->
                    <tr>
                      <td>'.$value["cantidaProductoUno"].'</td>
                      <td>'.$value["productoUno"].'</br></br>
                      <td>&nbsp;</td>                      
                      <center><td align="right"><b>$'.$value["totalPedidoUno"].'</b></td></center>
                    </tr>
                    <tr></tr>
                    <tr>';
                
                }

              if ($value["ProductoDos"] != undefined and $value["ProductoDos"] != "") {
                  echo'<!-- producto dos -->
                    <td>'.$value["cantidadProductoDos"].'</td>
                      <td>'.$value["ProductoDos"].'</br></br>
                      <td>&nbsp;</td>                     
                      <td align="right"><b>$'.$value["totalPedidoDos"].'</b></td>
                    </tr>
                    <tr>';
              }
              
              if ($value["ProductoTres"] != undefined and $value["ProductoTres"] != ""){
                 echo'<!-- producto tres-->
                    <td>'.$value["cantidadProductoTres"].'</td>
                      <td>'.$value["ProductoTres"].'</br></br>
                      <td>&nbsp;</td>                    
                      <td align="right"><b>$'.$value["totalPedidoTres"].'</b></td>
                    </tr>';
                }  
                
                if ($value["ProductoCuatro"] != undefined and $value["ProductoCuatro"] != ""){
                 echo'<!-- producto cuatro -->
                    <td>'.$value["cantidadProductoCuatro"].'</td>
                      <td>'.$value["ProductoCuatro"].'</br></br>
                      <td>&nbsp;</td>                
                      <td align="right"><b>$'.$value["totalPedidoCuatro"].'</b></td>
                    </tr>';
                }  

                if ($value["ProductoCinco"] != undefined and $value["ProductoCinco"] != ""){
                 echo'<!-- Producto Cinco -->
                    <td>'.$value["cantidadProductoCinco"].'</td>
                      <td>'.$value["ProductoCinco"].'</br></br>
                      <td>&nbsp;</td>
                      <td align="right"><b>$'.$value["totalPedidoCinco"].'</b></td>
                    </tr>';
                }  



                $productos = json_decode($value["productos"], true); 

                foreach ($productos as $key => $valueProductos) {
                        

                          echo'<!-- Producto Cinco -->
                          <td>'.$valueProductos["cantidad"].'</td>
                            <td style="text-transform:uppercase;">'.$valueProductos["Descripcion"].'</br></br>
                            <td>&nbsp;</td>
                            <td align="right"><b>$'.$valueProductos["precio"].'</b></td>
                          </tr>';   
                      }

                echo'<tr>                            
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td align="right"><b>TOTAL: $'.$value["total"].'</b></td>
                </tr>
            <tr>

              <td colspan="4">==========================================</td>

                 <tr>
                     <td>Abonado</td>
                     <td display:"none"></td>
                     <td display:"none"></td>
                     <td>Adeudo</td>
                 </tr>';

                 if ($value["pagoPedido"] != "" and $value["pagoPedido"] != null) {
                    echo'<tr>
                    <td><b> pago: $'.$value["pagoPedido"].'</b></td>
                     <td display:"none"></td>
                     <td display:"none"></td>
                 </tr>';

                 }
                

                 $pagos = json_decode($value["pagos"], true);

                 foreach ($pagos as $key => $valuePagos) {
                      
                      echo'<tr>
                            <td><b> pago: $'.$valuePagos["pago"].'</b></td>
                             <td display:"none"></td>
                             <td display:"none"></td>
                         </tr>';
                 }

                         echo'<tr>
                             <td display:"none"></td>
                             <td display:"none"></td>
                             <td display:"none"></td>
                             <td style="align : right;"><b> adeudo: $'.$value["adeudo"].'</b></td>
                         </tr>';
                

                echo'<tr>
                    <td colspan="4">&nbsp;</td>


                </tr> 

                <ul>
                    <td colspan="4" align="left">

                      <li>Dentro de las primeras 24 a 48 horas puedes cancelar tu pedido</li>

                <ul>
                <tr>
                    
                    <td colspan="4" align="left"> <li>La garantía del servicio es por 30 días a partir de la fecha de entrega. </li></td>
                </tr>
                <tr>
                  
                    <td colspan="4" align="left"> <li>La empresa no se responsabiliza por accesorios que el cliente reclame y no se encuentren detallados en esta orden. </li></td>
                </tr>
                
                <tr>
                  
                    <td colspan="4" align="left"> <li>El equipo será entregado solamente al portador de esta orden, en su defecto debera retirar el propietario que en esta orden funge como CLIENTE exhibiendo copia de documento de identidad INE. </li></td>
                </tr>

                <tr>
                    <td colspan="4" align="left"> <li>Toda reparación debe ser retirada dentro de los 30 días de comunicado su arreglo, caso contrario se perderá el reclamo. </li></td>
                </tr>
                
                <tr>
                    
                    <td colspan="4" align="left"> <li>En consumibles originales y compatibles no hay garantía. </li></td>
                </tr>
                <tr>
                    <td colspan="4" align="left"> <li>No nos responsabilizamos de la información contenida en su equipo. </li></td>
                </tr>
                 <tr>
                    <td colspan="4" align="left"><li>Al ingresar su orden acepta que esta sera publica en nuestro stio web www.comercializadoraegs.com sin mostrar datos del cliente solamente datos tecnicos y fotos del equipo recibido</li></td>
                </tr>
                </ul>

                      <!--<li>Puedes transferir el monto total de tu pedido al numero de cuenta </li>-->

                    </td>

                </ul>

                <tr>  
                <tr>
                  <td colspan="4" align="left"><b><center><h4>Recuerda que para facturación debes solicitar tu factura en: https://comercializadoraegs.com/facturacion</h4></center></b></td>
                </tr>
                <tr>
                <td colspan="4" align="left"><b><center><h4>Contestando nuestra encuenta, obtén un cupón de regalo en: https://comercializadoraegs.com/encuesta/</h4></center></b></td>
                </tr>
                    <!-- Mostramos los datos del cliente en el documento HTML -->
                    <br><br>
                    <td colspan="4" align="center">'.$nombreDelCliente.'</td>

                </tr>
                   
              </tr>

            </table>
                  
                  <hr size="5" /style="position: line; margin: 20px;">
            <table>
        
              <center>¡Gracias por su compra!</center>
              <center>
               <hr size="7" /style="position: line; margin: 20px;">
              <h3>¡Descubre nuestra selección de productos de TI de primera calidad escaneando este código QR y compra ahora mismo! 🛒💻 #TecnologíaAlAlcanceDeTuMano</h3>
              <div><img src="https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/images/qr-code-shop.png" alt="QRCODE" style="width:200px"></div>
              
              <hr size="7" /style="position: line; margin: 20px;">
              </center>
                
            <tr>
            
          </table><br>


                
        </div>
        
        <p>&nbsp;</p>';
}
    
  }
} 

//datos de venta
$ticket = new ImprimirTicketsPedido();
$ticket -> TraerImpresionTicketPedido();


//$empresas = new ctrMostrarEmpresasParaEditar();
//$empresas ->ControladorEmpresas();


?>

</body>
</html>