<?php

class ConexionWP{

	static public function conectarWP(){

		$link = new PDO("mysql:host=localhost;dbname=egsequip_respaldo",
						"egsequip_wp54331",
						"Qlf24011607",
						array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		                      PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
						);

		return $link;

	}


}