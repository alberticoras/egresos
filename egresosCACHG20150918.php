<?php

	session_start();
	include_once('egresos_funciones.php');
	switch($_POST['accion'])
	{
		default:
			$modulo .= egresos_menuInicio();
			$regresar = '';
		break;
		
	}
	
?>