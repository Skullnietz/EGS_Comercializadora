<?php

class ControladorCategoriasdeOrden
{
	
	public function ctrlMostrarCategoriasdeorden($item,$valor)
	{
		
		$tabla = "categoriasOrden";

		$respuesta = ModeloCategoriasOrden::mdlMostrarCategoriasOrden($tabla, $item, $valor);

		return $respuesta;
	}
}