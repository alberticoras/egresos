<?php

	session_start();
	include_once('cuentasContables_funciones.php');
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
			$modulo .= cuentas_formularioNuevo();
			break;
		
		case 'Guardar':
			$_SESSION["moduloHijoActual"] = utf8_encode($_POST['accion']);
			$modulo .= cuentas_guardar();
			break;
			
		case 'Agregar':
			$_SESSION["moduloHijoActual"] = utf8_encode($_POST['accion']);
			$modulo.=cuentas_formularioNuevaSub();
			break;
		
		case 'GuardarAgregar':
			$_SESSION["moduloHijoActual"] = utf8_encode($_POST['accion']);
			$modulo .= cuentas_guardarsubCuenta();
			break;
		
		case 'Editar':
			$_SESSION["moduloHijoActual"] = utf8_encode($_POST['accion']);
			$modulo .= cuentas_formularioEditar();
			break;
			
		case 'GuardarEdit':
			$_SESSION["moduloHijoActual"] = utf8_encode($_POST['accion']);
			$modulo .= cuentas_editarCuenta();
			break;
		
		case 'Eliminar':
			$_SESSION["moduloHijoActual"] = utf8_encode($_POST['accion']);
			$modulo .= cuentas_eliminarCuenta();
			break;
			
		default:
			$modulo .= cuentas_menuInicio();
		break;
		
	}
	
?>