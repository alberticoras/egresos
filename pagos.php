<?php

	session_start();

	include_once('pagos_funciones.php');
	//DATOS DEL MODULO
	liberar_bd();
	$selectDatosModulo = 'CALL sp_sistema_select_datos_modulo('.$_SESSION["mod"].');';
	$datosModulo = consulta($selectDatosModulo);
	$datMod = siguiente_registro($datosModulo);
	$_SESSION["moduloPadreActual"] = utf8_encode($datMod["nombre"]);

	switch($_POST['accion'])
	{
		case 'Nuevo':
			$_SESSION["moduloHijoActual"] = utf8_encode($_POST['accion']);
			$modulo .= egresos_formularioNuevo();
			break;
		
		case 'Guardar':
			$_SESSION["moduloHijoActual"] = utf8_encode($_POST['accion']);
			$modulo .= egresos_guardar();
			break;
		
		case 'GuardarEdit':
			$_SESSION["moduloHijoActual"] = utf8_encode($_POST['accion']);
			$modulo .= egresos_editar();
			break;
		
		case 'Editar':
			$_SESSION["moduloHijoActual"] = utf8_encode($_POST['accion']);
			$modulo .= egresos_formularioEditar($_POST["idPago"]);
			break;
		
		case 'Eliminar':
			$_SESSION["moduloHijoActual"] = utf8_encode($_POST['accion']);
			$modulo .= egresos_eliminar();
			break;
		
		case 'GuardarEliminar':
			$_SESSION["moduloHijoActual"] = utf8_encode($_POST['accion']);
			$modulo .= egresos_guardarEliminar();
			break;		
		
		case 'Ver detalles':
			$_SESSION["moduloHijoActual"] = utf8_encode($_POST['accion']);
			$modulo .= egresos_detalles($_POST["idPago"]);
			break;
		
		case 'Cancelar egreso':
			$_SESSION["moduloHijoActual"] = utf8_encode($_POST['accion']);
			$modulo .= egresos_cancelar();
			break;
			
		default:
			$modulo .= egresos_menuInicio();
		break;
		
	}
	
?>