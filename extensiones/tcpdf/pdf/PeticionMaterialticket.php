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
    
    <div class="zona_impresion">
        
         <br>
<!-- Mostramos los datos de la empresa en el documento HTML -->
                        <div><img src="https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/images/logoEGS (1).png" alt="LOGO" style="float: left"></div>
                        <center>
                        <div style="margin-top:20px;font-size: 200%"><strong>'Comercializadora EGS'</strong></div>
                        <hr><div style="font-size: 130%"><b>PETICIÓN DE MATERIAL</b></div><br></center>
                        <div><b><h3>Número de petición:</h3> </b></div>
                        <div><b><h3>Número de orden:</h3> </b></div>
                        <div><h3><b>Técnio:</b><h3></div>
                        <div><h3><b>Material solicitado:</b><h3></div>
                        <div class=""><h3>Entrega</h3></div>
 <?php 

# Configurar
date_default_timezone_set("America/Mexico_City");
# Después de configurar
echo " " . date("d-m-Y  h:i.s a");


?>
                        
                           <div><b><h3>Devolución:</h3> </b></div>
                
                <td colspan="4">====================================================</td>
<br>
<table border="0" align="center" width="200px">
 <tr> 
          <br>      
          <br><br>
                <b><hr></b>
                 <center>
                Ing. Erick Guizar Serrano
                </center> </tr>
                
                <br>
                  <br>
 <tr> 
      <br>          
                <b><hr></b>
                 <center>
                Técnico que solicita
                 </center>
                </tr>
                <br>