<?php
require 'serverside.php';


require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

require_once "../controladores/categorias.controlador.php";
require_once "../modelos/categorias.modelo.php";

require_once "../controladores/subcategorias.controlador.php";
require_once "../modelos/subcategorias.modelo.php";

require_once "../controladores/cabeceras.controlador.php";
require_once "../modelos/cabeceras.modelo.php";


$table_data->get('productos','id',array('id', 'id','empresa','titulo','id_categoria','id_subcategoria','ruta', 'estado', 'tipo', 'descripcion', 'portada', 'multimedia', 'detalles', 'precio', 'peso', 'entrega', 'disponibilidad', 'Proveedor', 'ofertadoPorCategoria', 'precioOferta', 'imgOferta', 'finOferta'));

?>