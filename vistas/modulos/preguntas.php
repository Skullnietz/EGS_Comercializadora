<?php
if($_SESSION["perfil"] != "administrador"  AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "tecnico" AND $_SESSION["perfil"]!= "secretaria" AND $_SESSION["perfil"]!= "Super-Administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;
}
?>
<style>
.gif{
     height: 300px; margin: auto; border: 3px solid #73AD21;border-radius:3px; padding: 10px;  
}
.recortegif{
     height: 100px;  margin: auto; border: 3px solid #73AD21;border-radius:3px; padding: 10px;  
}
 
</style>
<div class="content-wrapper">
	
	<section class="content">
        		
        		<div class="container">
            
            <div class="panel-group" id="accordion">
<?php
if($_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "tecnico" AND $_SESSION["perfil"]!= "secretaria"){

  echo '

                <div class="faqHeader"><h3><b>Perfiles</b></h3></div>

                <div class="panel panel-default">

                    <div class="panel-heading">
                    
                        <h4 class="panel-title">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapsPerfiles">¿Cómo puedo registrar un nuevo perfil?</a>
                        </h4>
                    
                    </div>
                    
                    <div id="collapsPerfiles" class="panel-collapse collapse in">
                    
                        <div class="panel-body">
                           Para agregar a un nuevo perfil es necesario encontrarse en el módulo <b>Gestor Perfiles</b> el cual tiene como ícono "<i class="fas fa-key"></i>" 
                           <p>Una vez dentro del módulo, del lado izquierdo superior se encuentra de color azul el botón <b>Agregar Perfil</b>, al presionarlo se abrirá el panel de agregado</p>
                        <ul>
                            <li><p><b>Nombre:</b> Se encuentra marcado con el ícono "<i class="fas fa-user"></i>" en este campo se ingresa el nombre <b>COMPLETO</b> del nuevo Perfil</p></li>
                            <li><p><b>Correo:</b> Se encuentra marcado con el ícono "<i class="fas fa-at"></i>" en este campo se ingresa el correo electrónico del Perfil (palabras clave: "@" y ".com")</p></li>
                            <li><p><b>Contraseña:</b> Se encuentra marcado con el ícono "<i class="fas fa-lock"></i>" en este campo se ingresa el número telefónico del Perfil <b>ÚNICAMENTE NÚMEROS</b></p></li>
                            <li><p><b>Seleccionar Perfil:</b> Se encuentra marcado con el ícono "<i class="fas fa-users"></i>" en este campo se selecciona uno de los 4 perfiles disponibles en EGS: <b>Administrador, Vendedor, Técnico</b> o <b>Secretaria</b></p></li>
                            <li><p><b>Subir Foto:</b> En este campo se sube la fotografía del perfil, para esto se presiona el botón <b>Seleccionar Archivo</b>, se abrirá el Explorador de Archivos de Windows y se buscará la fotografía correcta, al encontrarla se puede dar doble clic o seleccionarla y presionar en <b>Aceptar</b></p></li>
                        </ul>
                            Finalmente se debe seleccionar el botón azul <b>Agregar Perfil</b> el cual se encuentra del lado derecho inferior del formulario
                    
                        </div> 
                       <center> <img  class="gif" src="vistas/img/Preguntasgifs/Agregar perfil.gif" alt="Funny image"> </center>
                        
                        

                    </div>
                
                </div>

                <div class="panel panel-default">
                
                    <div class="panel-heading">
                
                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsePerfilesEditar">¿Cómo puedo editar un perfil?</a>
                        </h4>
                    
                    </div>
                    
                    <div id="collapsePerfilesEditar" class="panel-collapse collapse">
                    
                        <div class="panel-body">
                    
                            Para poder editar un perfil se debe recurrir al botón con el ícono <i class = "fas fa-edit"></i> una vez presionado se abrirá el formulario de edición
                    
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/Editar perfil.gif" alt="Funny image"> </center>
                    
                    </div>
                
                </div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsePerfilesEliminar">¿Cómo puedo eliminar un perfil?</a>
                        </h4>
                    
                    </div>

                    <div id="collapsePerfilesEliminar" class="panel-collapse collapse">
                        
                        <div class="panel-body">
                            
                            Para poder eliminar un perfil se debe recurrir al botón con el ícono <i class = "fas fa-times"></i> al presionarlo saldrá una confirmación de eliminación, esto con el objetivo de dar pauta a correciones
                       
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/Eliminar perfil.gif" alt="Funny image"> </center>

                        
                    </div>

                </div>

                <div class="panel panel-default">

<div class="panel-heading">

    <h4 class="panel-title">
        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsePerfilesDesactivar">¿Cómo puedo desactivar o activar un perfil?</a>
    </h4>

</div>

<div id="collapsePerfilesDesactivar" class="panel-collapse collapse">
    
    <div class="panel-body">
        
        Para poder activar o desactivar el perfil es necesario encontrarse en el gestor de perfiles y en la columna <b>ESTADO</b> estará el botón para realizar lo necesario
   
    </div>
    <center> <img  class="recortegif" src="vistas/img/Preguntasgifs/Activar desactivar perfil.gif" alt="Funny image"> </center>
    
</div>

</div>

                <div class="faqHeader"><h3><b>Técnicos</b></h3></div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTecnico">¿Cómo puedo dar de alta un técnico?</a>
                        </h4>

                    </div>

                    <div id="collapseTecnico" class="panel-collapse collapse">
                        
                        <div class="panel-body">
                        Para agregar a un Técnico es necesario encontrarse en el módulo <b>Técnicos</b> el cual tiene como ícono "<i class="fas fa-user-cog"></i>" 
                           <p>Una vez dentro del módulo, del lado izquierdo superior se encuentra de color azul el botón <b>Agregar Técnico</b>, al presionarlo se abrirá el panel de agregado</p>
                        <ul>
                            <li><p><b>Nombre del Técnico:</b> Se encuentra marcado con el ícono "<i class="fas fa-user"></i>" en este campo se ingresa el nombre <b>COMPLETO</b> del Técnico </p></li>
                            <li><p><b>Correo del Técnico:</b> Se encuentra marcado con el ícono "<i class="fas fa-at"></i>" en este campo se ingresa el correo electrónico del Técnico (palabras clave: "@" y ".com")</p></li>
                            <li><p><b>Número Telefónico 1 y 2:</b> Se encuentra marcado con el ícono "<i class="fas fa-headphones"></i>" en este campo se ingresa el número telefónico del Técnico <b>ÚNICAMENTE NÚMEROS</b></p></li>
                            <li><p><b>Hora de Comida:</b> Se encuentra marcado con el ícono "<i class="fas fa-hourglass-start"></i>" en este campo se ingresa la hora de comida correspondiente al técnico que se está agregando con el siguiente formato: "2:00pm a 3:00pm", "3:00pm a 4:00pm", etc.</p></li>
                            <li><p><b>Seleccionar Área:</b> Se encuentra marcado con el ícono "<i class="fas fa-cogs"></i>" en este campo se selecciona una de las 3 áreas disponibles en EGS: <b>Electrónica, Impresoras</b> o <b>Sistemas</b></p></li>
                        </ul>
                            Finalmente se debe seleccionar el botón azul <b>Agregar Técnico</b> el cual se encuentra del lado derecho inferior del formulario
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/Agregar nuevo tecnico.gif" alt="Funny image"> </center>

                    </div>

                </div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTecnicoEditar">¿Cómo puedo editar datos de un Técnico</a>
                        </h4>

                    </div>

                    <div id="collapseTecnicoEditar" class="panel-collapse collapse">

                        <div class="panel-body">
                            Para poder editar a un Técnico se debe recurrir al botón con el ícono <i class = "fas fa-edit"></i> una vez presionado se abrirá el formulario de edición
                            </ul>
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/Editar tecnico.gif" alt="Funny image"> </center>

                    </div>

                </div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTecnicoEliminar">¿Cómo puedo eliminar a un Técnico?</a>
                        </h4>

                    </div>

                    <div id="collapseTecnicoEliminar" class="panel-collapse collapse">

                        <div class="panel-body">
                        Para poder eliminar a un técnico se debe recurrir al botón con el ícono <i class = "fas fa-times"></i> al presionarlo saldrá una confirmación de eliminación, esto con el objetivo de dar pauta a correciones
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/Eliminar tecnico.gif" alt="Funny image"> </center>

                    </div>
                    

                </div>
                

                <div class="faqHeader"><h3><b>Asesores de ventas</b></h3></div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseAsesor">¿Cómo puedo agregar un asesor de ventas?</a>
                        </h4>

                    </div>
                    <div id="collapseAsesor" class="panel-collapse collapse">

                        <div class="panel-body">
                        Para agregar a un Asesor de Ventas es necesario encontrarse en el módulo <b>Asesores de Ventas</b> el cual tiene como ícono "<i class="fas fa-user-tie"></i>" 
                           <p>Una vez dentro del módulo, del lado izquierdo superior se encuentra de color azul el botón <b>Agregar Asesor</b>, al presionarlo se abrirá el panel de agregado</p>
                        <ul>
                            <li><p><b>Nombre del Asesor:</b> Se encuentra marcado con el ícono "<i class="fas fa-user"></i>" en este campo se ingresa el nombre <b>COMPLETO</b> del Asesor </p></li>
                            <li><p><b>Correo del Asesor:</b> Se encuentra marcado con el ícono "<i class="fas fa-at"></i>" en este campo se ingresa el correo electrónico del Asesor (palabras clave: "@" y ".com")</p></li>
                            <li><p><b>Número Telefónico 1 y 2:</b> Se encuentra marcado con el ícono "<i class="fas fa-headphones"></i>" en este campo se ingresa el número telefónico del Asesor <b>ÚNICAMENTE NÚMEROS</b></p></li>
                        </ul>
                            Finalmente se debe seleccionar el botón azul <b>Agregar Técnico</b> el cual se encuentra del lado derecho inferior del formulario
                    </div>
                    <center> <img  class="gif" src="vistas/img/Preguntasgifs/Agregar asesor.gif" alt="Funny image"> </center>

                    </div>

                </div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseAsesorEditar">¿Cómo puedo editar un asesor de venta?</a>
                        </h4>

                    </div>

                    <div id="collapseAsesorEditar" class="panel-collapse collapse">

                        <div class="panel-body">
                        Para poder editar a un <b>Asesor de Venta</b> se debe recurrir al botón con el ícono <i class = "fas fa-edit"></i> una vez presionado se abrirá el formulario de edición
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/Editar asesor.gif" alt="Funny image"> </center>

                    </div>

                </div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseAsesorEliminar">Eliminar un asesor de ventas</a>
                        </h4>

                    </div>

                    <div id="collapseAsesorEliminar" class="panel-collapse collapse">

                        <div class="panel-body">
                        Para poder eliminar a un <b>Asesor de Venta</b> se debe recurrir al botón con el ícono <i class = "fas fa-times"></i> al presionarlo saldrá una confirmación de eliminación, esto con el objetivo de dar pauta a correciones
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/Eliminar asesor.gif" alt="Funny image"> </center>

                    </div>

                </div>
';

}
?>
                <div class="faqHeader"><h3><b>Clientes</b></h3></div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseAgregarCliente">¿Cómo puedo agregar un Cliente?</a>
                        </h4>

                    </div>

                    <div id="collapseAgregarCliente" class="panel-collapse collapse">
                      
                        <div class="panel-body">
                            Para agregar a un cliente es necesario encontrarse en el módulo <b>Gestor Clientes</b> el cual tiene como ícono "<i class="fas fa-users"></i>" 
                           <p>Una vez dentro del módulo, del lado izquierdo superior se encuentra de color azul el botón <b>Agregar Cliente</b>, al presionarlo se abrirá el panel de agregado</p>
                        <ul>
                            <li><p><b>Nombre del Cliente:</b> Se encuentra marcado con el ícono "<i class="fas fa-user"></i>" en este campo se ingresa el nombre <b>COMPLETO</b> del cliente </p></li>
                            <li><p><b>Correo del Cliente:</b> Se encuentra marcado con el ícono "<i class="fas fa-at"></i>" en este campo se ingresa el correo electrónico del cliente (palabras clave: "@" y ".com")</p></li>
                            <li><p><b>Número Telefónico 1 y 2:</b> Se encuentra marcado con el ícono "<i class="fas fa-headphones"></i>" en este campo se ingresa el número telefónico del cliente <b>ÚNICAMENTE NÚMEROS</b></p></li>
                            <li><p><b>Etiqueta de Cliente:</b> Se encuentra marcado con el ícono "<i class="fas fa-tag"></i>" en este campo se selecciona la etiqueta del cliente y en las opciones se encuentran: <b>Nuevo, Frecuente</b> y <b>Problemático</b></p></li>
                            <li><p><b>Seleccionar Asesor:</b> Se encuentra marcado con el ícono "<i class="fas fa-headphones"></i>" en este campo se selecciona al asesor que se hará cargo de todo el proceso de la orden del cliente</p></li>
                        </ul>
                            Finalmente se debe seleccionar el botón azul <b>Agregar Cliente</b> el cual se encuentra del lado derecho inferior del fomulario
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/Agregar un cliente.gif" alt="Funny image"> </center>

                    </div>

                </div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseEditarCLiente">¿Cómo puedo editar un cliente?</a>
                        </h4>

                    </div>

                    <div id="collapseEditarCLiente" class="panel-collapse collapse">

                        <div class="panel-body">
                            Para poder editar a un <b>Cliente</b> se debe recurrir al botón con el ícono "<i class = "fas fa-edit"></i>" una vez presionado se abrirá el formulario de edición
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/Editar cliente.gif" alt="Funny image"> </center>

                    </div>

                </div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseReporteCliente">¿Cómo puedo descargar el reporte de clientes general?</a>
                        </h4>

                    </div>

                    <div id="collapseReporteCliente" class="panel-collapse collapse">

                        <div class="panel-body">
                           Para descargar el <b>Reporte de Clientes General</b> se debe recurrir pulsar el botón izquierdo superior con la leyenda: <b>"Descargar Reporte en Excel"</b> y automáticamente se agregará una descarga de navegador, es un archivo excel el cual puedes abrir con la aplicación de office correspondiente (excel)
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/Descargar reporte de clientes general.gif" alt="Funny image"> </center>

                    </div>

                </div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseReporteClienteEnt">¿Cómo puedo descargar el reporte de clientes con órdenes entregadas?</a>
                        </h4>

                    </div>

                    <div id="collapseReporteClienteEnt" class="panel-collapse collapse">

                        <div class="panel-body">
                           Para descargar el <b>Reporte de Clientes con Órdenes Entregadas</b> se debe pulsar el botón izquierdo superior con la leyenda <b> Descargar Reporte Usuarios ENT </b> y automáticamente se agregará una descarga de navegador, es un archivo excel el cual puedes abrir con la aplicación de office correspondiente (excel) 
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/Descargar reporte ordenes entregadas.gif" alt="Funny image"> </center>

                    </div>

                </div>
        		
        		<div class="faqHeader"><h3><b>Corte de caja</b></h3></div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseCorteDeCaja">¿Cómo puedo descargar un reporte de corte de caja general?</a>
                        </h4>

                    </div>

                    <div id="collapseCorteDeCaja" class="panel-collapse collapse">

                        <div class="panel-body">
                            Para poder descargar un reporte <b>Corte de Caja General</b> se debe pulsar el botón izquierdo superior con la leyenda <b>Descargar Reporte de Caja</b> y automáticamente se agregará una descarga de navegador, es un archivo excel el cual puedes abrir con la aplicación de office correspondiente (excel)
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/Descarga de caja general.gif" alt="Funny image"> </center>

                    </div>

                </div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseCorteDeCajaPorFecha">¿Cómo puedo descargar un corte de caja por rango de fecha?</a>
                        </h4>

                    </div>

                    <div id="collapseCorteDeCajaPorFecha" class="panel-collapse collapse">

                        <div class="panel-body">
                            Para poder descargar un reporte <b>Corte de caja por rango de fecha</b> se debe pulsar el botón izquierdo superior con la leyenda <b>Rango de Fecha</b>, al presionarlo nos permitirá las siguientes opciones:
                            <ul>
                                <li><p><b>Hoy: </b>Esta opción nos permite descargar el reporte del día</p></li>
                                <li><p><b>Ayer: </b>Esta opción nos permite descargar el reporte de un día anterior al actual (ayer)</p></li>
                                <li><p><b>Últimos 7 días: </b>Esta opción nos permite descargar el reporte de la semana</p></li>
                                <li><p><b>Este Mes: </b>Esta opción nos permite descargar el reporte del mes</p></li>
                                <li><p><b>Último Mes: </b>Esta opción nos permite descargar el reporte del mes anterior al actual, por ejemplo: Reporte de Julio estando en Agosto</p></li>
                                <li><p><b>Rango Personalizado: </b>Esta opción nos permite descargar el reporte de una fecha personalizada en un rango amplio, para esto debemos seleccionar un día antes y un día después del rango que necesitamos, por ejemplo: Queremos el Rango del día 15 al 25, entonces para esto debemos seleccionar el rango en 14 al 26 para así obtener el rango deseado/meta 15 al 25</p></li>
                            </ul>
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/Descargar caja por rango de fecha.gif" alt="Funny image"> </center>

                    </div>

                </div>

        		<div class="faqHeader"><h3><b>Órdenes</b></h3></div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOrden">¿Cómo puedo agregar una orden?</a>
                        </h4>

                    </div>

                   
                    <div id="collapseOrden" class="panel-collapse collapse">

                        <div class="panel-body">
                            Para agregar una orden es necesario posicionarse en el módulo <b>órdenes</b> y presionar el botón superior izquierdo <b>"Agregar Orden"</b>, una vez presionado tendremos que llenar los siguientes campos:
                            <ul>
                                <li><p><b>Ingresar título de orden: </b>Este campo sirve para ingresar título a la orden a agregar</p></li>
                                <li><p><b>Ruta URL de la orden: </b>Este campo se ingresa automáticamente al llenar el título de la orden, no es necesario realizar una accón adicional</p></li>
                                <li><p><b>Seleccionar Técnico: </b>Este campo sirve para seleccionar al técnico encargado de la orden</p></li>
                                <li><p><b>Seleccionar Asesor: </b>Este campo sirve para seleccionar el asesor encargado de la orden</p></li>
                                <li><p><b>Seleccionar Cliente: </b>Este campo sirve para seleccionar al cliente propietario del dispositivo de la orden</p></li>
                                <li><p><b>Arrastrar o dar click para subir imágenes: </b>Si se presiona se abrirá el explorador de archivos de windows donde podrá seleccionar la/las imágenes correspondientes</p></li>
                                <li><p><b>Ingresar detalles internos: </b>Se ingresan todos los detalles que tenga la orden ingresada para análisis privado</p></li>
                                <li><p><b>Subir foto de portada: </b>Al presionar el botón <b>Seleccionar archivo</b> se abrirá el explorador de archivos de windows donde podrá seleccionar la/las imágenes correspondientes</p></li>
                                <li><p><b>Subir foto principal del producto: </b>Al presionar el botón <b>Seleccionar archivo</b> se abrirá el explorador de archivos de windows donde podrá seleccionar la/las imágenes correspondientes</p></li>
                                <li><p><b>Agregar Partida: </b>Este campo sirve para ingresar todo lo que requerirá la orden en forma de partias (hasta 11 partidas por orden)</p></li>
                            </ul> 
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifagregarorden.gif"> </center>

                    </div>

                </div>

              
                <div class="panel panel-default">
                   
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOrdenImagenes">¿Es necesario agregar imágenes en cada campo que lo requiera al llenar una orden?</a>
                        </h4>

                    
                    </div>
                    
                    <div id="collapseOrdenImagenes" class="panel-collapse collapse">
                        
                        <div class="panel-body">
                            <b>Si</b>, para una correcta admnistración y comprobación de la orden.
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifagregarimagenes.gif"> </center>

                    </div>
                
                </div>
                
                <div class="panel panel-default">
                   
                    <div class="panel-heading">
                        <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOrdenDatos">¿Qué campos son obligatorios al ingresar una orden?</a>
                        </h4>
                   
                    </div>

                    <div id="collapseOrdenDatos" class="panel-collapse collapse">

                        <div class="panel-body">
                            <b>Todos son obligatorios  </b>para una correcta administración y comprobación de la orden, en especial los datos del cliente y datos del producto en cuestión para la orden 
                        </div>
                    <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifcamposobligatoriosorden.gif"> </center>
                    </div>
               
                </div>
                 
                  
                  <div class="panel panel-default">
                   
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOrdenBuscar">¿Cómo puedo buscar una orden?</a>
                        </h4>
                    
                    </div>
                    
                    <div id="collapseOrdenBuscar" class="panel-collapse collapse">
                       
                        <div class="panel-body">
                            Para buscar una orden es necesario encontrarse en el módulo <b>Órdenes</b> y dirigirse a la <b>barra buscadora central</b> e ingresar el número de orden, nombre del cliente o el nombre de asesor/técnico
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifbuscarorden1.gif"> </center>
                   
                    </div>

                </div>

               
                <div class="panel panel-default">
                   
                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOrdeninfo">¿Cómo puedo visualizar la información adicional de una orden?</a>
                        </h4>
                    
                    </div>
                    
                    <div id="collapseOrdeninfo" class="panel-collapse collapse">
                        
                        <div class="panel-body">
                            Para visualizar la información adicional de una orden es necesario encontrarse en el módulo de <b>órdenes</b>, buscarla con el número de orden en la barra buscadora y presionar el botón con el siguiente ícono: <i class="fas fa-eye"></i>
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifverordentecnico.gif"> </center>

                    </div>

                </div>

                
                <div class="panel panel-default">
                    
                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOrdeninfo1">¿Cómo puedo enviar información al cliente sobre su orden?</a>
                        </h4>
                    
                    </div>

                    <div id="collapseOrdeninfo1" class="panel-collapse collapse">
                       
                        <div class="panel-body">
                            Para hacer esto es necesario encontrarse en el módulo <b>Órdenes</b> 
                        </div>
                        <ul>
                            <li>Dar click en el botón  <button class="btn btn-xs btn-warning"><i class="fas fa-eye"></i></button>   del número de orden a la que deseamos notificar al cliente</li>
                            <li>Después de entrar en la información adicional de la orden, hay que seleccionar el botón:  <button class="btn btn-xs btn-success" <i class="fas fa-envelope"></i> Informar estado de orden a cliente</button></li>
                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifenviarestadodeordencliente1.gif"> </center>

                    </div>

                </div>

                
                <div class="panel panel-default">
                   
                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOrdenImagesModif">¿Se pueden modificar las imágenes de una orden?</a>
                        </h4>
                    
                    </div>
                    
                    <div id="collapseOrdenImagesModif" class="panel-collapse collapse">
                       
                        <div class="panel-body">
                            <!--=========================
                            CLOCAR AQUI INSTRCUCIONES
                            ============================-->
                           <b>No, las imágenes son únicas e irrepetibles, por lo que si requiere cambiar una imagen debe consultar a soporte técnico.</b> <br>Se hace de esta forma por reglas de procedimiento de las ordenes, ya que se deben realizar cuando entra un equipo, no despues de entrar.
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifimagenesdeorden.gif"> </center>

                    </div>

                </div>

                
                <div class="panel panel-default">
                  
                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOrdenAsesor">¿Como puedo cambiar de asesor?</a>
                        </h4>
                   
                    </div>
                    
                    <div id="collapseOrdenAsesor" class="panel-collapse collapse">

                        <div class="panel-body">
                            Para hacer esto es necesario encontrarse en el módulo <b>Órdenes</b> 
                        </div>
                        <ul>
                            <li>Dar click en el botón  <button class="btn btn-xs btn-warning"><i class="fas fa-eye"></i></button>   del número de orden a la que deseamos cambiar el asesor</li>
                            <li>Una vez dentro de los detalles de la orden necesitaremos encontrar la sección para asesores</li> 
                            <div class="input-group " >
                            <span class="input-group-addon"><i class="fas fa-user"></i></span>
                            <select class="form-control input-sm selector" autocomplete="off">
                            <option value="">Seleccionar Asesor</option></select>
                            </div>
                            <li>Le daremos click y seleccionaremos el asesor al que deseamos cambiar</li>
                            <li>Bajaremos nuestro menú y le daremos en <button class="btn btn-primary btn-xs">Guardar</button></li>
                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifcambiarasesororden.gif"> </center>

                    </div>
                    

                </div>

               
                <div class="panel panel-default">
                   
                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOrdenTec">¿Como puedo cambiar de técnico?</a>
                        </h4>
                    
                    </div>
                    
                    <div id="collapseOrdenTec" class="panel-collapse collapse">

                        <div class="panel-body">
                            <!--=========================
                            CLOCAR AQUI INSTRCUCIONES
                            ============================-->
                           Para hacer esto es necesario encontrarse en el módulo <b>Órdenes</b>
                        </div>
                        <ul>
                            <li>Dar click en el botón  <button class="btn btn-xs btn-warning"><i class="fas fa-eye"></i></button>   del número de orden a la que deseamos cambiar el técnico</li>
                            <li>Una vez dentro de los detalles de la orden necesitaremos encontrar la sección para técnicos</li> 
                            <div class="input-group " >
                            <span class="input-group-addon"><i class="fas fa-cogs"></i></span>
                            <select class="form-control input-sm selector" autocomplete="off">
                            <option value="">Seleccionar Técnico</option></select>
                            </div>
                            <li>Le daremos click y seleccionaremos el técnico al que deseamos cambiar</li>
                            <li>Bajaremos nuestro menú y le daremos en <button class="btn btn-primary btn-xs">Guardar</button></li>
                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifcambiartecnicodeorden.gif"> </center>

                    </div>

                </div>

               
                <div class="panel panel-default">
                  
                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOrdenEstado">¿Como puedo cambiar el estado de la orden?</a>
                        </h4>
                   
                    </div>
                   
                    <div id="collapseOrdenEstado" class="panel-collapse collapse">

                        <div class="panel-body">
                            <!--=========================
                            CLOCAR AQUI INSTRCUCIONES
                            ============================-->
                            Para hacer esto es necesario encontrarse en el módulo <b>Órdenes</b>
                        </div>
                        <ul>
                            <li>Dar click en el botón  <button class="btn btn-xs btn-warning"><i class="fas fa-eye"></i></button>   del número de orden a la que deseamos cambiar el estado de orden</li>
                            <li>Una vez dentro de los detalles de la orden necesitaremos encontrar la sección del estado de orden</li> 
                            <div class="input-group " >
                            <span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>
                            <select class="form-control input-sm selector" autocomplete="off">
                            <option value="">Seleccionar Estado</option></select>
                            </div>
                            <li>Le daremos click y seleccionaremos el estado al que deseamos cambiar</li>
                            <li>Bajaremos nuestro menú y le daremos en <button class="btn btn-primary btn-xs">Guardar</button></li>
                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifcambiartecnicodeorden.gif"> </center>
                        

                    </div>

                </div>

                
                <div class="panel panel-default">
                   
                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOrdenEstadoTec">¿Que estados de orden puedo cambiar siendo técnico?</a>
                        </h4>
                    
                    </div>

                    
                    <div id="collapseOrdenEstadoTec" class="panel-collapse collapse">

                        <div class="panel-body">
                            <!--=========================
                            CLOCAR AQUI INSTRCUCIONES
                            ============================-->
                           <b>Los técnicos, pueden cambiar a solo 3 estados:</b>
                           <ul>
                               <li>
                                   <b>En revisión (REV)</b><br>
                                   Estado predeterminado de creación de una orden, en este estado el técnico revisa el equipo para diagnosticarlo, una vez que se hacen las observaciones, el técnico puede cambiar el estado de la orden a <b>Supervisión (SUP)</b>.
                               </li>
                               <li>
                                   <b>Supervisión (SUP)</b><br>
                                   Cuando el equipo esta diagnosticado técnico debe solicitar la supervisión del administrador para dar presupuesto de la orden y mandarla a <b> Pendientes de autorización (AUT) </b>.
                               </li>
                               <li>
                                   <b>Terminada (REV)</b><br> Una vez que este reparado el equipo debe cambiarse el estado de orden para dar aviso al cliente, para posteriormente entregarse.
                               </li>
                               <li>
                                   <b>Cancelada</b> y <b>Terminada</b> <br> Estos estados deben consultarse con el personal administrativo antes de su ejecucion
                               </li>
                           </ul>
                           <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifcambiarestadosiendotecnico.gif"> </center>
                        </div>

                    </div>
               
                </div>

                
                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOrdenPartidas">¿Como puedo agregar nuevas partidas?</a>
                        </h4>

                    </div>
                   
                    <div id="collapseOrdenPartidas" class="panel-collapse collapse">

                        <div class="panel-body">
                            <!--=========================
                            CLOCAR AQUI INSTRCUCIONES
                            ============================-->
                            <b>*Las partidas las puede ver el cliente, hágase con precaución*</b><br>
                        
                           Para hacer esto es necesario encontrarse en el módulo <b>Órdenes</b>
                        </div>
                        <ul>
                            <li>Dar click en el botón  <button class="btn btn-xs btn-warning"><i class="fas fa-eye"></i></button>   del número de orden a la que deseamos ingresar una nueva partida</li>
                            <li>Una vez dentro de los detalles de la orden necesitaremos encontrar el botón </li> 
                            <button type="button" class="btn btn-primary btn-xs" autocomplete="off">Agregar Nueva Partida</button>
                            <li>Le daremos click y nos desplegará una nueva partida y podremos llenar los datos</li>
                            <li>Bajaremos nuestro menú y le daremos en <button class="btn btn-primary btn-xs"> Guardar</button></li>
                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifagregarpartida1.gif"> </center>
                   
                    </div>

                </div> 

                <div class="panel panel-default">

                    <div class="panel-heading">
                    
                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOrdenInversion">¿Como puedo agregar Inversión en orden?</a>
                        </h4>
                    
                    </div>
                    
                    <div id="collapseOrdenInversion" class="panel-collapse collapse">
                    
                        <div class="panel-body">
                             Para hacer esto es necesario encontrarse en el módulo <b>Órdenes</b>
                        </div>
                        <ul>
                            <li>Dar click en el botón  <button class="btn btn-xs btn-warning"><i class="fas fa-eye"></i></button>   del número de orden a la que deseamos agregar una Inversión</li>
                            <li>Una vez dentro de los detalles de la orden necesitaremos encontrar el botón </li> 
                            <button type="button" class="btn btn-primary btn-xs" autocomplete="off">Agregar Inversión</button>
                            <li>Le daremos click y nos desplegará una inversión y podremos llenar los datos</li>
                            <li>En <b>Detalle</b> podremos marcar el titulo de la inversion</li>
                            <li>En <b>Inversión</b> podremos marcar el monto asignado a la inversión</li>
                            <li>Para completar bajaremos nuestro menú y le daremos en <button class="btn btn-primary btn-xs"> Guardar</button></li>
                        </ul>
                    <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifagregarinversion.gif"> </center>
                    </div>
                
                </div>

        		<div class="panel panel-default">
                
                    <div class="panel-heading">
                
                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOrdenObservaciones">¿Como puedo agregar Observaciones?</a>
                        </h4>
                
                    </div>
                
                    <div id="collapseOrdenObservaciones" class="panel-collapse collapse">
                
                         <div class="panel-body">
                             Para hacer esto es necesario encontrarse en el módulo <b>Órdenes</b>
                        </div>
                        <ul>
                            <li>Dar click en el botón  <button class="btn btn-xs btn-warning"><i class="fas fa-eye"></i></button>   del número de orden a la que deseamos agregar una Observación</li>
                            <li>Una vez dentro de los detalles de la orden necesitaremos encontrar el botón </li> 
                            <button type="button" class="btn btn-primary btn-xs" autocomplete="off">Agregar Nueva Observación</button>
                            <li>Le daremos click y nos desplegará una nueva observación y podremos llenar los datos</li>
                            <li><b>Recordemos</b> las observaciones se hacen para asuntos internos de la empresa</li>
                            <li>Bajaremos nuestro menú y le daremos en <button class="btn btn-primary btn-xs"> Guardar</button></li>
                        </ul>
                <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifagregarobservacionordenes.gif"> </center>
                    </div>
                
                </div> 
                

                	<div class="faqHeader"><h3><b>Estado del Servicio</b></h3></div>
                
                <div class="panel panel-default">
                
                    <div class="panel-heading">
                
                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsereporte">¿Como puedo descargar un reporte?</a>
                
                        </h4>
                
                    </div>
                
                    <div id="collapsereporte" class="panel-collapse collapse">
                
                        <div class="panel-body">
                            <!--=========================
                            CLOCAR AQUI INSTRCUCIONES
                            ============================-->
                            <?php
                            if($_SESSION["perfil"] == "tecnico"){

  echo '
                            Podemos descargar el reporte de los estados de ordenes estan bajo nuestro nombre, ya sean ordenes terminadas, entregadas, aceptadas o en revisión.<br>
                        Es importante hacerlo <b>diariamente</b> para ir verificando la información y tenerla a mano, cuando ésta sea requerida.<br>
                        <b>Esta herramienta puede ayudarte a sacar el reporte de comisiones manualmente, descargará la información de TODAS las órdenes</b>
                           
                        </div>
                        <ul>
                        <li>Para hacer esto es necesario encontrarse en el módulo <b>Estado del servicio</b></li>
                        <li>Una vez dentro de Estado del Servicio, identifica que tipo de reporte quieres descargar</li>
                        <li>Después de identificar el boton de descarga asignado, presionalo para descargar tu reporte</li>
                        </ul>
                        ';}
                        
                            if($_SESSION["perfil"] == "vendedor"){

  echo '
                            Podemos descargar el reporte de los estados de ordenes estan bajo nuestro nombre, ya sean ordenes terminadas, entregadas, aceptadas o en revisión.<br>
                        Es importante hacerlo <b>diariamente</b> para ir verificando la información y tenerla a mano, cuando ésta sea requerida.<br>
                        <b>Esta herramienta puede ayudarte a sacar el reporte de comisiones manualmente, descargará la información de TODAS las órdenes</b>
                           
                        </div>
                        <ul>
                        <li>Para hacer esto es necesario encontrarse en el módulo <b>Estado del servicio</b></li>
                        <li>Una vez dentro de Estado del Servicio, identifica que tipo de reporte quieres descargar</li>
                        <li>Después de identificar el boton de descarga asignado, presionalo para descargar tu reporte</li>
                        </ul>
                        ';}
                        
                            if($_SESSION["perfil"] != "vendedor" AND $_SESSION["perfil"] != "tecnico"){

  echo '
                            Podemos descargar el reporte de todos los estados de ordenes. <br>
                           
                        </div>
                        <ul>
                        <li>Para hacer esto es necesario encontrarse en el módulo <b>Estado del servicio</b></li>
                        <li>Una vez dentro de Estado del Servicio, identifica que tipo de reporte quieres descargar</li>
                        <li>Después de identificar el boton de descarga asignado, presionalo para descargar tu reporte</li>
                        </ul>
                        ';}
                        ?>
                        
                <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifdescargarreporteordenes.gif"> </center>
                    </div>
                
                </div>
                
                <div class="panel panel-default">
                
                    <div class="panel-heading">
                
                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsereporteFecha">¿Como puedo descargar un reporte por fecha?</a>
                        </h4>
                
                    </div>
                
                    <div id="collapsereporteFecha" class="panel-collapse collapse">
                
                        <div class="panel-body">
                            <!--=========================
                            CLOCAR AQUI INSTRCUCIONES
                            ============================-->
                        <?php
                            if($_SESSION["perfil"] == "tecnico"){
                        echo '
                           Podemos descargar el reporte de los estados de ordenes estan bajo nuestro nombre, ya sean ordenes terminadas, entregadas, aceptadas o en revisión.<br>
                        Es importante hacerlo <b>diariamente</b> para ir verificando la información y tenerla a mano, cuando ésta sea requerida.<br>
                        <b>Esta herramienta puede ayudarte a sacar el reporte de comisiones manualmente.</b>
                        </div>
                        <ul>
                        <li>Para hacer esto es necesario encontrarse en el módulo <b>Estado del servicio</b></li>
                        <li>Selecciona el rango de fecha del reporte que quieres descargar</li>
                        <li>Identifica que tipo de reporte quieres descargar</li>
                        <li>Después de identificar el boton de descarga asignado, presionalo para descargar tu reporte</li>
                        </ul>
                        ';}
                        ?>
                        <?php
                            if($_SESSION["perfil"] == "vendedor"){
                        echo '
                           Podemos descargar el reporte de los estados de ordenes estan bajo nuestro nombre, ya sean ordenes terminadas, entregadas, aceptadas o en revisión.<br>
                        Es importante hacerlo <b>diariamente</b> para ir verificando la información y tenerla a mano, cuando ésta sea requerida.<br>
                        <b>Esta herramienta puede ayudarte a sacar el reporte de comisiones manualmente.</b>
                        </div>
                        <ul>
                        <li>Para hacer esto es necesario encontrarse en el módulo <b>Estado del servicio</b></li>
                        <li>Selecciona el rango de fecha del reporte que quieres descargar</li>
                        <li>Identifica que tipo de reporte quieres descargar</li>
                        <li>Después de identificar el boton de descarga asignado, presionalo para descargar tu reporte</li>
                        </ul>
                        ';}
                        ?>
                        <?php
                            if($_SESSION["perfil"] != "vendedor" AND $_SESSION["perfil"] != "tecnico"){
                        echo '
                           Podemos descargar el reporte por rango de fecha de todos los estados de ordenes.<br>
                        
                        </div>
                        <ul>
                        <li>Para hacer esto es necesario encontrarse en el módulo <b>Estado del servicio</b></li>
                        <li>Selecciona el rango de fecha del reporte que quieres descargar</li>
                        <li>Identifica que tipo de reporte quieres descargar</li>
                        <li>Después de identificar el boton de descarga asignado, presionalo para descargar tu reporte</li>
                        </ul>
                        ';}
                        ?>
                <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifdescargareporteporfecha.gif"> </center>
                    </div>
                
                </div>
                

        		<div class="faqHeader"><h3><b>Pedidos</b></h3></div>

                <div class="panel panel-default">
                
                    <div class="panel-heading">
                
                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsepedido">¿Como puedo crear un pedido?</a>
                        </h4>
                
                    </div>
                
                    <div id="collapsepedido" class="panel-collapse collapse">
                
                        <div class="panel-body">
                            <b>Los pedidos se realizan cada que se necesite un producto, asesores y técnicos pueden hacer este formulario cada que necesiten de material para completar la orden.</b><br><br>
                            Para hacer esto es necesario encontrarse en el módulo <b>Pedidos</b><br><br>
                            <ul>
                                <li> Seleccionar el botón </li>
                                <button class="btn btn-primary">Agregar Pedido</button><br>Esto nos abrirá el formulario de agregar pedido
                                <li>Seleccionaremos el asesor</li>
                                <li>Seleccionaremos el cliente</li>
                                <li>Presionamos el botón de agregar producto que se va a pedir, pueden ser más de uno</li>
                                Llenamos los datos del producto, nombre, cantidad, precio
                                <li>Insertamos el pago que se va a realizar, también este puede ser el apartado que se dejo, abajo indica lo que resta por pagar</li>
                                <li>En el campo de fecha de pago podemos insertar la fecha en que quedan en pagar lo restante</li>
                                <li>Indicaremos el estado en que se encuentra el pedido</li>
                                <li>Asignamos el numero de la orden, esto es necesario para vincular el pedido a la orden</li>

                            
                                
                            </ul>
                            <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifagregarpedido.gif"> </center>
                        </div>
                
                    </div>
                
                </div>
                
                <div class="panel panel-default">
                
                    <div class="panel-heading">
                
                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsepedidoConOrden">¿Como puedo enlazar un pedido con una orden?</a>
                        </h4>
                
                    </div>
                
                    <div id="collapsepedidoConOrden" class="panel-collapse collapse">
                
                        <div class="panel-body">
                            Para hacer esto es necesario encontrarse en el módulo <b>Pedidos</b><br><br>
                        </div>
                        <ul>
                            <li>Abre la Información del pedido de la orden que correspondiente</li>
                            Para ello necesitas dar click en el botón <br><button class="btn btn-warning" ><i class="fas fa-edit"></i></button><br> Se encuentra debajo de la columna de "Info Pedido", esto nos abrirá la información del pedido
                            <li>Para vincular un pedido sin orden, o editar un pedido con nombre podemos dar click en el botón <button class="btn btn-info">Asignar Pedido A Orden <i class="fas fa-sort"></i></button></li>
                            Esto desplegará una ventana para editar o vincular el pedido de la orden.
                            <li>En "Asignar Orden" seleccionaremos la orden a la cual necesitamos vincular nuestro pedido</li>
                            <li>Presionaremos el botón de <br> <button  class="btn btn-primary" >Guardar Pedido</button> </li>
                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifasignarpedido.gif"> </center>

                
                    </div>
                
                </div>
                
                <div class="panel panel-default">
                
                    <div class="panel-heading">
                
                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsepedidoInfo">¿Como puedo visualizar la informacion de mi pedido?</a>
                            
                        </h4>
                
                    </div>
                
                    <div id="collapsepedidoInfo" class="panel-collapse collapse">
                
                        <div class="panel-body">
                          Para hacer esto es necesario encontrarse en el módulo <b>Pedidos</b><br><br>
                          <li>Para ello necesitas dar click en el botón</li>
                             <br><button class="btn btn-warning" ><i class="fas fa-edit"></i></button><br> Se encuentra debajo de la columna de "Info Pedido", esto nos abrirá la información del pedido
                        </div>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifverpedidoinfo.gif"> </center>
                
                    </div>
                
                </div>
                
                <div class="panel panel-default">
                
                    <div class="panel-heading">
                
                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsepedidoEstado">¿Como puedo cambiar el estado del pedido?</a>
                        </h4>
                
                    </div>
                
                    <div id="collapsepedidoEstado" class="panel-collapse collapse">
                
                        <div class="panel-body">
                           Para hacer esto es necesario encontrarse en el módulo <b>Pedidos</b><br><br>
                        </div>
                        <ul>
                            <li>Abre la Información del pedido de la orden que correspondiente</li>
                            Para ello necesitas dar click en el botón <br><button class="btn btn-warning" ><i class="fas fa-edit"></i></button><br> Se encuentra debajo de la columna de "Info Pedido", esto nos abrirá la información del pedido.
                            <li> Dentro de la información de la orden, cambia el campo de Estado del pedido, al estado que deseas guardar.</li>
                            <li>Presionaremos el botón de <br> <button  class="btn btn-primary" >Guardar Pedido</button> </li>
                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifeditarestadopedido.gif"> </center>
                    </div>
               
                </div>
               
                <div class="panel panel-default">
               
                    <div class="panel-heading">
               
                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsepedidoModif">¿Como puedo modificar datos de pedido?</a>
                        </h4>
               
                    </div>
               
                    <div id="collapsepedidoModif" class="panel-collapse collapse">
               
                        <div class="panel-body">
                            Para hacer esto es necesario encontrarse en el módulo <b>Pedidos</b><br><br>
                        </div>
                        <ul>
                            <li>Abre la Información del pedido de la orden que correspondiente</li>
                            Para ello necesitas dar click en el botón <br><button class="btn btn-warning" ><i class="fas fa-edit"></i></button><br> Se encuentra debajo de la columna de "Info Pedido", esto nos abrirá la información del pedido.
                            <li> Dentro de la información de la orden, puedes cambiar campos de los productos, como deseas guardarlos.</li>
                             <li>Presionaremos el botón de <br> <button  class="btn btn-primary" >Guardar Pedido</button> </li>
                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifeditarproductopedido.gif"> </center>
               
                    </div>
               
                </div>
               
                <div class="panel panel-default">
               
                    <div class="panel-heading">
               
                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsepedidopagos">¿Como puedo agregar pagos al pedido?</a>
                        </h4>
               
                    </div>
               
                    <div id="collapsepedidopagos" class="panel-collapse collapse">
               
                        <div class="panel-body">
                             Para hacer esto es necesario encontrarse en el módulo <b>Pedidos</b><br><br>
                        </div>
                        <ul>
                            <li>Abre la Información del pedido de la orden que correspondiente</li>
                            Para ello necesitas dar click en el botón <br><button class="btn btn-warning" ><i class="fas fa-edit"></i></button><br> Se encuentra debajo de la columna de "Info Pedido", esto nos abrirá la información del pedido.
                            <li> Desliza hacia abajo hasta la sección de pagos.</li>
                            <li> Presiona el botón </li>
                            <button type="button" class="btn btn-primary">Agregar Nuevo Pago</button>
                            <li>Esto desliza unos nuevos campos, uno de pago y la fecha que se realizó el pago</li>
                            <li>Llena estos campos y asegurate de que esten realizados correctamente</li>
                             <li>Presionaremos el botón de <br> <button  class="btn btn-primary" >Guardar Pedido</button> </li>
                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifagregarpagopedido.gif"> </center>
               
                    </div>
               
                </div>

        		<div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsepedidoReportes">¿Como puedo descargar reporte de pedidos?</a>
                        </h4>

                    </div>

                    <div id="collapsepedidoReportes" class="panel-collapse collapse">

                        <div class="panel-body">
                           Para hacer esto es necesario encontrarse en el módulo <b>Pedidos</b><br><br>
                           Podemos descargar los reportes de todos los estados de los pedidos, pendientes, adquiridos, entregados al asesor, pagados, a credito, de enlace.
                        </div>
                        <ul>
                            <li> Seleccionaremos el boton </li>
                            <button class="btn btn-success">Descargar reporte...</button><br>Con el estado de pedido correspondiente, si deseamos descargar todas podemos seleccionar el de "Descargar reporte en excel" 
                            
                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifdescargarreporteexcelpedidos.gif"> </center>

                    </div>

                </div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsepedidoTickets">¿Como puedo imprimir ticket de pedidos?</a>
                        </h4>

                    </div>

                    <div id="collapsepedidoTickets" class="panel-collapse collapse">

                        <div class="panel-body">
                            Para hacer esto es necesario encontrarse en el módulo <b>Pedidos</b><br><br>
                        </div>
                        <ul>
                            <li> Identifica la orden que deseas imprimir </li>
                            <li> Selecciona el botón </li>
                            <button class="btn btn-warning"><i class="fas fa-ticket-alt"></i></button><br>
                            Esto abrirá una nueva pestaña con la impresión del pedido.
                            
                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifimprimirpedido.gif"> </center>

                    </div>

                </div>


                <div class="faqHeader"><h3><b>Ventas Rápidas (Ventas R)</b></h3></div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseVentasR">¿Como puedo crear una Venta R?</a>
                        </h4>

                    </div>

                    <div id="collapseVentasR" class="panel-collapse collapse">

                        <div class="panel-body">
                            Para hacer esto es necesario encontrarse en el módulo <b>Ventas R</b><br><br>
                        </div>
                        <ul>
                            <li>Selecciona el botón de </li>
                            <button class="btn btn-primary">Agregar Venta</button><br>Esto despliega un formulario que deberás llenar
                            <li>Selecciona el Asesor</li>
                            <li>Llena el campo de <b>nombre del cliente</b></li>
                            <li>Llena el de <b>correo del cliente</b></li>
                            <li>Selecciona el <b>número de productos</b> que se venderan</li> Se desplegaran la fila o filas de los productos agregados con sus campos
                            <li>Llena los campos: <b>Nombre, Precio y Cantidad</b></li> Puedes eliminar un producto dando click en el <b>botón (-)</b> o agregar un nuevo producto dando en el <b>botón (+)</b>
                            <li>Selecciona el método de pago</b>, si este medio es en efectivo se despliega una ayuda de cálculo de cambio a regresar.</li>
                            <li>Una vez comprobada la información dentro del formato puedes guardar la venta seleccionando el botón de</li><button class="btn btn-primary" autocomplete="off">Guardar Venta</button>
                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/agregarunaventar.gif"> </center>


                    </div>

                </div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseVentasTicket">¿Como puedo imprimir ticket de venta R?</a>
                        </h4>

                    </div>

                    <div id="collapseVentasTicket" class="panel-collapse collapse">

                        <div class="panel-body">
                           Para hacer esto es necesario encontrarse en el módulo <b>Ventas R</b><br><br>
                        </div>
                        <ul>
                            <li>Identifica la venta que deseas imprimir</li>
                            <li>Selecciona el botón de</li><button class="btn btn-warning "><i class="fas fa-ticket-alt"></i></button><br>Esto nos abrira una ventana con la impresión del ticket de venta.
                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifimprimirunaventar.gif"> </center>

                    </div>

                </div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseVentasEliminar">¿Como puedo eliminar una venta?</a>
                        </h4>

                    </div>

                    <div id="collapseVentasEliminar" class="panel-collapse collapse">

                        <div class="panel-body">
                          Para hacer esto es necesario encontrarse en el módulo <b>Ventas R</b><br><br>
                        </div>
                        <ul>
                            <li>Identifica la venta que deseas eliminar</li>
                            <li>Selecciona el botón de</li> <button class="btn btn-danger"><i class="fa fa-times"></i></button><br> Desplegara una alerta para confirmar la eliminación de la venta.
                            <li>Confirmamos la eliminación de la venta</li>
                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/eliminarunaventar.gif"> </center>

                    </div>

                </div>

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseVentasReporte">¿Como puedo descargar reporte de Ventas R?</a>
                        </h4>

                    </div>

                    <div id="collapseVentasReporte" class="panel-collapse collapse">

                        <div class="panel-body">
                           Para hacer esto es necesario encontrarse en el módulo <b>Ventas R</b><br><br>
                        </div>
                        <ul>
                            <li>Seleccionamos el botón de </li><button class="btn btn-success">Descargar Reporte En Excel</button>
                            <br>Esto nos descargara un Excel con <b>todas</b> las ventas rápidas

                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifdescargarreportedeventasr.gif"> </center>
         
                    </div>
         
                </div>
         
                <div class="panel panel-default">
         
                    <div class="panel-heading">
         
                        <h4 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseVentasBuscar">¿Como puedo buscar venta?</a>
                        </h4>
         
                    </div>
         
                    <div id="collapseVentasBuscar" class="panel-collapse collapse">
         
                        <div class="panel-body">
                           Para hacer esto es necesario encontrarse en el módulo <b>Ventas R</b><br><br>
                        </div>
                        <ul>
                            <li>Daremos click en la barra buscadora</li>
                            <li>Escribimos el dato que identifique a nuestra orden ya sea <b> numero de venta, nombre del cliente, producto vendido</b></li>
                        </ul>
                        <center> <img  class="gif" src="vistas/img/Preguntasgifs/gifbuscarenventasr.gif"> </center>

                    </div>
         
                </div>

            </div>

        </div>

	</section>

</div>