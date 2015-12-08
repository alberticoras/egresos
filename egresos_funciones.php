<?php

function changeDateFormats($ini){
    $pos = strpos($ini, '-');
    if($pos === false){
        return '';   
    }else{
        $day = substr($ini, 0, 2);
        $month = substr($ini, 3, 2);
        $year = substr($ini, 6, 4);
        return $year.'-'.$month.'-'.$day;
    }
}

function egresos_menuInicio() 
{
/*
	if(isset($_SESSION['search_date']))
		$_POST["filterDate2"] = $_SESSION['search_date'];
	else
		$_SESSION['search_date'] = $_POST["filterDate2"];
*/
		
    if ($_POST["filterDate2"] != '') 
	{
			
		$iparr = split(" - ", $_POST["filterDate2"]);
		$iparr[0] = str_replace('/', "-", $iparr[0]);
		$iparr[1] = str_replace('/', "-", $iparr[1]);
		$fechaIniFormat = normalize_date2($iparr[0]);
		$fechaFinFormat = normalize_date2($iparr[1]);
		$fechaIni = $fechaIniFormat;
		$fechaFin = $fechaFinFormat;
            
        //OBTENER LA TABLA DE CUENTAS
        liberar_bd();
        $selectTablaCuentas = 'call sp_get_docs_for_period(\''.($fechaIni).'\', \''.($fechaFin).'\')';
        $tablaCuentas = consulta($selectTablaCuentas);
        $total_pendiente = 0;
        while ($cuenta = siguiente_registro($tablaCuentas))
        {
            $total_pendiente += floatval($cuenta["saldo_pendiente"]);
            $status = '';
            $ref = '';
            $tipo = '';
            switch(utf8_encode($cuenta['estatus'])){
                case '0':
                    $status = '<span class="banner_rojo">CANCELADO<span>';
                    break;
                case '1':
                    $status = '<span class="banner_negro">PROYECTADO<span>';
                    break;
                case '3':
                    $status = '<span class="banner_negro">CAPTURADO<span>';
                    break;
                case '2':
                    $status = '<span class="banner_azul">PROGRAMADO<span>';
                    $ref = '#myModal5';
                    break;
                case '4':
                    $status = '<span class="banner_verde">LIQUIDADO<span>';
                    break;
                case '5':
                    $status = '<span class="banner_amarillo">ABONADO<span>';
                    $ref = '#myModal5';
                    break;
                case '6':
                    $status = '<span class="banner_negro" style="background-color:#fc6e51">PENDIENTE<span>';
                    break;

            }
            switch(utf8_encode($cuenta['tipo_documento_proveedor'])){
                case '1':
                    $tipo = 'factura';
                    break;
                case '2':
                    $tipo = 'remisión';
                    break;
                case '3':
                    $tipo = 'cheque';
                    break;
                default:
                    $tipo = 'pendiente';
                    break;
            }
            
            $id = $cuenta["id_documentos_proveedor"];
            $cuenta['estatus'] == 4 ? $icon = 'fa-thumbs-up' : $icon = 'fa-money';
            
            if($cuenta['estatus'] != 0)
            {
                $tabla .= '<tr><td>'.$cuenta["nombre_proveedor"].'</td><td class="money">'.$cuenta["monto_documento"].'</td><td class="money">'.$cuenta["saldo_pendiente"].'</td><td>'.$status.'</td><td>No. '.$cuenta["folio"].' - ('.$tipo.')</td>';
                
                $cuenta['estatus'] != '3' ? $tabla .= '<td>'.$cuenta["fecha_comp"].'</td>' : $tabla.='<td><input type="text" id="txtModifiedDate" style="text-align:center;" value="'.$cuenta['fecha_comp'].'"/></td>';
                
                $tabla .= '<td class="thAcciones" style="text-align:left;"><a class="btn btn-default-alt btn-sm" onClick="document.frmSistema.idEgreso.value = ' .$cuenta["id_documentos_proveedor"]. '; navegar(\'Ver detalles\');"><i title="Ver detalles" class="fa fa-eye"></i></a>';
                
                $cuenta['estatus'] != 3 ? $tabla.='<a class="btn btn-default-alt btn-sm" name="'.$cuenta["id_documentos_proveedor"].'" id="btnPagarDocDet" href="'.$ref.'" data-toggle="modal"><i title="Pagar" class="fa '.$icon.'"></i></a><a class="btn btn-default-alt btn-sm" onClick="imprime_egreso(\'' .$cuenta["id_documentos_proveedor"].'\');"><i title="Reimprimir" class="fa fa-print"></i></a></td></tr>' : $tabla .= '<a class="btn btn-default-alt btn-sm" name="'.$cuenta["id_documentos_proveedor"].'" id="btnAutorizarDocDet" href="'.$ref.'" data-toggle="modal" onclick="programDocument(\''.$cuenta['id_documentos_proveedor'].'\', \''.$cuenta["monto_documento"].'\',\''.$cuenta["fecha_comp"].'\', \'2\');"><i title="Autorizar" class="fa fa-check"></i></a><a class="btn btn-default-alt btn-sm" onclick="programDocument(\''.$cuenta['id_documentos_proveedor'].'\', \''.$cuenta["monto_documento"].'\',\''.$cuenta["fecha_comp"].'\', \'0\')"><i title="Rechazar" class="fa fa-times-circle-o"></i></a></td></tr>';
            }
        }
        
        
    } 
	else 
	{
// 		unset($_SESSION['search_date']);
		
        $hoy = date("Y-m-d");
        //CHECAMOS FILTROS
        $fechaFormt = date('Y-m-d');
        $primerDia = date('Y-m');
        
		$fechaIni = $primerDia . '-01 00:00:00';
		$fechaFin = $fechaFormt . ' 23:59:59';
		$fechaIniCampo = normalize_date($fechaIni);
		$fechaFinCampo = normalize_date($fechaFin);
		//$_POST["filterDate2"] = changeDateFormats($fechaIniCampo) . " - " . changeDateFormats($fechaFinCampo);
        
        //OBTENER LA TABLA DE CUENTAS
        liberar_bd();
        $selectTablaCuentas = 'call sp_get_documento_proveedor(\'10\')';
        $tablaCuentas = consulta($selectTablaCuentas);
        $total_pendiente = 0;
        while ($cuenta = siguiente_registro($tablaCuentas))
        {
            $total_pendiente += floatval($cuenta["saldo_pendiente"]);
            $status = '';
            $ref = '';
            $tipo = '';
            switch(utf8_encode($cuenta['estatus'])){
                case '0':
                    $status = '<span class="banner_rojo">CANCELADO<span>';
                    break;
                case '1':
                    $status = '<span class="banner_negro">PROYECTADO<span>';
                    break;
                case '3':
                    $status = '<span class="banner_negro">CAPTURADO<span>';
                    break;
                case '2':
                    $status = '<span class="banner_azul">PROGRAMADO<span>';
                    $ref = '#myModal5';
                    break;
                case '4':
                    $status = '<span class="banner_verde">LIQUIDADO<span>';
                    break;
                case '5':
                    $status = '<span class="banner_amarillo">ABONADO<span>';
                    $ref = '#myModal5';
                    break;
                case '6':
                    $status = '<span class="banner_negro" style="background-color:#fc6e51">PENDIENTE<span>';
                    break;

            }
            switch(utf8_encode($cuenta['tipo_documento_proveedor'])){
                case '1':
                    $tipo = 'factura';
                    break;
                case '2':
                    $tipo = 'remisión';
                    break;
                case '3':
                    $tipo = 'cheque';
                    break;
                default:
                    $tipo = 'pendiente';
                    break;
            }
            
            $id = $cuenta["id_documentos_proveedor"];
            $cuenta['estatus'] == 4 ? $icon = 'fa-thumbs-up' : $icon = 'fa-money';
            
            if($cuenta['estatus'] != 0)
            {
                $tabla .= '<tr><td>'.$cuenta["nombre_proveedor"].'</td><td class="money">'.$cuenta["monto_documento"].'</td><td class="money">'.$cuenta["saldo_pendiente"].'</td><td>'.$status.'</td><td>No. '.$cuenta["folio"].' - ('.$tipo.')</td>';
                
                $cuenta['estatus'] != '3' ? $tabla .= '<td>'.$cuenta["fecha_comp"].'</td>' : $tabla.='<td><input type="text" id="txtModifiedDate" style="text-align:center;" value="'.$cuenta['fecha_comp'].'"/></td>';
                
                $tabla .= '<td class="thAcciones" style="text-align:left;"><a class="btn btn-default-alt btn-sm" onClick="document.frmSistema.idEgreso.value = ' .$cuenta["id_documentos_proveedor"]. '; navegar(\'Ver detalles\');"><i title="Ver detalles" class="fa fa-eye"></i></a>';
                
                $cuenta['estatus'] != 3 ? $tabla.='<a class="btn btn-default-alt btn-sm" name="'.$cuenta["id_documentos_proveedor"].'" id="btnPagarDocDet" href="'.$ref.'" data-toggle="modal"><i title="Pagar" class="fa '.$icon.'"></i></a><a class="btn btn-default-alt btn-sm" onClick="imprime_egreso(\'' .$cuenta["id_documentos_proveedor"].'\');"><i title="Reimprimir" class="fa fa-print"></i></a></td></tr>' : $tabla .= '<a class="btn btn-default-alt btn-sm" name="'.$cuenta["id_documentos_proveedor"].'" id="btnAutorizarDocDet" href="'.$ref.'" data-toggle="modal" onclick="programDocument(\''.$cuenta['id_documentos_proveedor'].'\', \''.$cuenta["monto_documento"].'\',\''.$cuenta["fecha_comp"].'\', \'2\');"><i title="Autorizar" class="fa fa-check"></i></a><a class="btn btn-default-alt btn-sm" onclick="programDocument(\''.$cuenta['id_documentos_proveedor'].'\', \''.$cuenta["monto_documento"].'\',\''.$cuenta["fecha_comp"].'\', \'0\')"><i title="Rechazar" class="fa fa-times-circle-o"></i></a></td></tr>';
                
            }
        }
    }

    $btnVerdetalles = false;
    $btnAlta = false;
    $btnElimina = false;
    $btnEditar = false;
	$btnImprime= false;

	//PREMISOS DE ACCIONES
    liberar_bd();
    $selectPermisosAcciones = 'CALL sp_sistema_select_permisos_acciones_modulo(' . $_SESSION["idPerfil"] . ', ' . $_SESSION["mod"] . ');';
    $permisosAcciones = consulta($selectPermisosAcciones);
    while ($acciones = siguiente_registro($permisosAcciones)) 
	{
		switch (utf8_encode($acciones["accion"])) 
		{
			case 'Ver detalles':
				$btnVerdetalles = true;
			break;
			case 'Alta':
				$btnAlta = true;
			break;
			case 'Editar':
				$btnEditar = true;
			break;
			case 'Eliminación':
				$btnElimina = true;
			break;
			case 'Reimprimir':
				$btnImprime = true;
			break;
		}
    }
    

    $pagina = '	<div id="page-heading" id="egresos_page">	
					<ol class="breadcrumb">
						<li><a href="javascript:navegar_modulo(0);">Dashboard</a></li>
						<li class="active" id="lbltest">
							' . $_SESSION["moduloPadreActual"];
    
    $pagina.= '
						</li>
					</ol>
					<h1>' . $_SESSION["moduloPadreActual"] . '</h1>
					<div class="options">
						<div class="btn-toolbar">
							<input type="hidden" id="idEgreso" name="idEgreso" value="" />
							<input type="hidden" name="txtIndice" />';
							if ($btnAlta) 
							{
							$pagina.= '	<i title="Nuevo Egreso" style="cursor:pointer;" onclick="navegar(\'Nuevo\')" class="btn btn-warning" >
											Nueva C X P
										</i>';
							}
	$pagina.= '			</div>
					</div>										
				</div>										
				<div class="container">
					<div class="row">
						<div class="col-sm-6">
							
								
                        <div class="form-horizontal" style="margin-bottom:50px;">
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input readonly="readonly" type="text" class="form-control" id="filterDate2" name="filterDate2" value="' . $_POST["filterDate2"] . '"/>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default" onclick="navegar()">Buscar</button>
                                    <button type="button" class="btn btn-default" id="btnResetFilter" onclick="">Resetear Filtro</button>
                                </div>
                            </div>
                        </div>	
							
						</div>							
					</div>							
					<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-danger">
								<div class="panel-heading">
									<h4>
                                        <ul class="nav nav-tabs">
								            <li class="active" id="nav_cxp"><a href="javascript:;">Cuentas por Pagar</a></li>
                                            <li id="nav_payments"><a href="javascript:;">Cuentas Por Aprobar</a></li>
                                            <li id="nav_cancelled"><a href="javascript:;">Cuentas Rechazadas</a></li>
										</ul>
                                    </h4>
									<div class="options">   
                                        <span id="sp_total_pendiente">total pendiente: $ '.$total_pendiente.'</span>
										<a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
									</div>
								</div>
								<div class="panel-body collapse in">
									<div class="table-responsive">
										<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered datatables" id="example">
											<thead>
												<tr>
                                                    <th style="text-align:center;">PROVEEDOR</th>
                                                    <th style="text-align:center;">MONTO TOTAL</th>
                                                    <th style="text-align:center;">SALDO PENDIENTE</th>
                                                    <th style="text-align:center;">ESTATUS</th>
                                                    <th style="text-align:center;">FOLIO</th>
                                                    <th style="text-align:center;">FECHA LIMITE</th>
                                                    <th style="text-align:center;">ACCIONES</th>
                                                </tr>
											</thead>	
											<tbody id="table_egresos_body" style="text-align:center;">';

											
											/*----------- AQUI CARGA LOS EGRESOS EN JAVASCRIPT ---------------------*/
                $pagina.= $tabla;
											
    			$pagina.= '					</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-dialogSpecial">
						<div class="modal-content">
							<div id="divFormPago">
								<div class="modal-header">
									<h4 class="modal-title">Visualizar egreso</h4>
									<span id="spIdDoc" style="display:block">'.$id.'</span>
								</div>
								<div class="modal-body">
									<div id="divVerEgreso">
										
									</div>																						
								</div>
								<div class="modal-footer">									
									<i class="btn-danger btn" onclick="" data-dismiss="modal">Cerrar</i>
								</div>
							</div>
						</div>
					</div>
				</div>
                <div class="modal fade" id="myModal5" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-dialogSpecial">
						<div class="modal-content">
							<div id="divFormPago">
								<div class="modal-header">
									<h4 class="modal-title">Realizar Pago</h4>
                                    <span id="spIdDoc" style="display:none">'.$id.'</span>
								</div>
								<div class="modal-body">
									<div class="form-horizontal">
                                        <div class="form-group">
                                            <label for="fechaPago" class="col-sm-3 control-label">Fecha de Pago:</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    <input readonly="readonly" type="text" class="form-control" id="datepicker" name="fechaPago" value="' . $hoy . '"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="montoPago" class="col-sm-3 control-label">Monto del Pago:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="txtMontoPago_newPay" name="MontoPago" value="0.00" maxlength="100"/>
                                                
                                            </div>
                                            <button class="btn btn-info" id="btn_settle_new_pay"  style="margin:0 auto;" type="button">
														<i class="fa">LIQUIDAR</i>
													</button>
                                        </div>
                                        <div class="form-group">
                                            <label for="txtCuentaPago" class="col-sm-3 control-label">Cuenta de Pago:</label>
                                            <div class="col-sm-6">
                                                <select id="txtCuentaPago" name="txtCuentaPago" style="width:100% !important" class="selectSerch">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="id_tipo_pago" class="col-sm-3 control-label">Tipo de Pago:</label>
                                            <div class="col-sm-6">
                                                <select id="id_tipo_pago" name="id_tipo_pago" style="width:100% !important" class="selectSerch">
                                                    <option value="1">SPEI</option>
                                                    <option value="2">Cheque</option>
                                                    <option value="3">Efectivo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="txtCuentaPago" class="col-sm-3 control-label">Referencia de Pago:</label>
                                            <div class="col-sm-6">
                                                <input type="text" id="txtReferenciaPago" name="txtReferenciaPago" style="width:100% !important" maxlength="45" />
                                            </div>
                                        </div>
                                        </div>
                                    </div>																					
								</div>
								<div class="modal-footer">									
									<i class="btn-danger btn" onclick="" data-dismiss="modal">Cerrar</i>
                                    <i class="btn-success btn" style="margin:10px 3px 10px 10px;" data-dismiss="modal" id="btnRegistrarPagoForDoc" >Realizar Pago</i>
								</div>
							</div>
						</div>
					</div>
				</div>
                <div class="modal fade" id="myModal6" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-dialogSpecial">
						<div class="modal-content">
							<div id="divFormPago">
								<div class="modal-header">
									<h4 class="modal-title">Cancelacion de Pago</h4>
                                    <span id="to_pay_doc"></span>
								</div>
								<div class="modal-body">
									<div id="divVerEgreso">
										<div class="form-horizontal">
                                            <div class="form-group">
                                                <label for="txtSucursalPago" class="col-sm-3 control-label">Motivo de la Cancelacion:</label>
                                                <div class="col-sm-6">
                                                    <textarea id="txtMotiveCancel" name="txtSucursalPago" style="width:350px;height:120px !important" class=""></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                            <label for="txtCuentaCancel" class="col-sm-3 control-label">Cuenta de Reembolso</label>
                                            <div class="col-sm-6">
                                                <select id="txtCuentaCancel" name="txtCuentaPago" style="width:100% !important" class="selectSerch">
                                                </select>
                                            </div>
                                        </div>
                                        </div>
									</div>																						
								</div>
								<div class="modal-footer">									
									<i class="btn-danger btn" id="btnConfirmCancel" data-dismiss="modal">Eliminar</i>
								</div>
							</div>
						</div>
					</div>
				</div>';
				//$pagina.=$selectEgresos;
    return $pagina;
}

function egresos_formularioNuevo() 
{
	//LISTA DE CUENTAS
    liberar_bd();
    $selectListCuentas = 'CALL sp_sistema_lista_ctas_bancos();';
    $listaCuentas = consulta($selectListCuentas);
    while ($cue = siguiente_registro($listaCuentas)) 
	{
		$optCuentas .= '<option value="' . $cue["id"] . '">'. utf8_encode($cue["nombre"]) . '(' . $cue["numero"] . ')</option>';
    }

    $_SESSION["idProyectoActual"] = '';
    $_SESSION["idProveedorActual"] = '';

	//LISTA DE PROVEEDORES 
    liberar_bd();
    $selectListProveedores = 'CALL sp_sistema_lista_proveedores();';
    $listaProveedores = consulta($selectListProveedores);
    while ($prov = siguiente_registro($listaProveedores)) 
	{
        if($prov["id"] == '11')
            $sel = 'selected';
		$optProveedores .= '<option '.$sel.' value="' . $prov["id"] . '">'. utf8_encode($prov["nombre"]) . '</option>';
    }

	//LISTA DE CUENTAS HIJO (TIPO DE EGRESO)
    liberar_bd();
    $selectListaCuentasHijo = 'CALL sp_sistema_lista_cuentas_hijo();';
    $listaCuentasHijo = consulta($selectListaCuentasHijo);
    while ($cueReg = siguiente_registro($listaCuentasHijo)) 
	{
		$optCuentasHijo .= '<option value="' . $cueReg["id"] . '">'. utf8_encode($cueReg["nombre"]) . '</option>';
    }

	//LISTA DE PROYECTOS (PROYECTO)
    /*liberar_bd();
    $selectListaProyectos = 'CALL sp_sistema_lista_proyectos();';
    $listaProyectos = consulta($selectListaProyectos);
    while ($proyecto = siguiente_registro($listaProyectos)) {
	$optProyecto .= '<option value="' . $proyecto["id"] . '">'
		. utf8_encode($proyecto["nombre"]) . '</option>';
    }
	*/
    
    $optClientes .= '<option value="1">Factura</option><option value="2">Remision</option>';

	//LISTA DE ESTADOS
    liberar_bd();
    $selectEstados = 'CALL sp_sistema_lista_estados();';
    $estados = consulta($selectEstados);
    while ($est = siguiente_registro($estados)) 
	{
		$selecEdo = '';
		if ($est["id"] == 12)
			$selecEdo = 'selected="selected"';
		$optEstados .= '<option ' . $selecEdo . ' value="' . $est["id"] . '">'. utf8_encode($est["nombre"]) . '</option>';
    }

	//LISTA DE CIUDADES
    liberar_bd();
    $selectCiudades = 'CALL sp_sistema_select_lista_sucursales();';
    $ciudades = consulta($selectCiudades);
    while ($ciu = siguiente_registro($ciudades)) 
	{
		$selecMpo = '';
		if ($ciu["id"] == 6)
			$selecMpo = 'selected="selected"';
		$optCiudades .= '<option ' . $selecMpo . ' value="' . $ciu["id"] . '">'. utf8_encode($ciu["nombre"]) . '</option>';
    }

    $hoy = date("Y-m-d");
    $pagina = '	<div id="page-heading">
					<ol class="breadcrumb">
						<li><a href="javascript:navegar_modulo(0);">Dashboard</a></li>
						<li><a href="javascript:navegar_modulo(' . $_SESSION["mod"] . ');">'
						. $_SESSION["moduloPadreActual"] . '</a></li>
						<li class="active">
							' . $_SESSION["moduloHijoActual"] . '
						</li>
					</ol>
					<h1>' . substr ($_SESSION["moduloHijoActual"], 0, strlen($_SESSION["moduloHijoActual"])-1) . 'a cuenta por pagar</h1>
					<div class="options">
						<div class="btn-toolbar">
							<input type="hidden" id="idEgresoActual" name="idEgresoActual" value="0" readonly="readonly"/>
							<input type="hidden" id="sumDetalles" name="sumDetalles" value="0" readonly="readonly"/>
							<input type="hidden" id="idTipoEntrega" name="idTipoEntrega" value="1" readonly="readonly"/>
						</div>
					</div>
				</div>
                
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<div class = "panel-body collapse in" id = "divDatosEgreso" style="background:#ffffff;">
								
								
                                    <input type="button" id="btnLoadXML" value="Cargar Datos de Archivo" />
									<h3><span class="label label-ribbon" style="margin-left:-31px;box-shadow:2px 2px 5px #bfbfbf;">Datos generales</span></h3>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="idProveedor" class="col-sm-3 control-label">Proveedor:</label>
												<div class="col-sm-6">
													<select style="width:100% !important" class="selectSerch" name="idProveedor" id="idProveedor">
														' . $optProveedores . '
													</select>
												</div>
												<div class="input-group-btn">
													<button class="btn btn-info" type="button" href="#myModal1" id="bootbox-demo-5" data-toggle="modal">
														<i class="fa fa-plus"></i>
													</button>
												</div>
											</div>
										</div>
										 <div class="col-md-6">
											<div class="form-group">
												<label for="idCliente" class="col-sm-3 control-label">Tipo de Documento:</label>
												<div id="proyectoCabezal" class="col-sm-6">
													<select style="width:100% !important" class="selectSerch" name="idTipoDoc" id="idTipoDoc">
														
														 ' . $optClientes . '
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label class="col-sm-3 control-label">Fecha del Documento:</label>
												<div class="col-sm-6">
													<div class="input-group">
														<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
														<input readonly="readonly" style="background-color:white" type="text" class="form-control" id="datepicker" name="fechaDoc" value="' . $hoy . '"/>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="folioCliente" class="col-sm-3 control-label">Fecha Limite:</label>
												<div class="col-sm-6">
													<div class="input-group">
														<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
														<input readonly="readonly" style="background-color:white"  type="text" class="form-control" id="datepicker" name="fechaCompromiso" value="' . $hoy . '"/>
													</div>
												</div>
											</div>
										</div>
									</div><br/>
									<div class="row" id="lastRowHeader">
										
										<div class="col-md-6">
											<div class="form-group">
												<label for="folioCliente" class="col-sm-3 control-label">Folio Documento:</label>
												<div class="col-sm-6">
													<input type="text" class="form-control" maxlength="200" name="folioDocumento" id="folioDocumento" autocomplete="off" />
												</div>
											</div>
										</div>
                                        <div class="col-md-6">
											<div class="form-group">
												<label for="obs_documento" class="col-sm-3 control-label">Concepto:</label>
												<div class="col-sm-6">
													<input type="text" class="form-control" maxlength="200" name="obs_documento" id="obs_documento" autocomplete="off" />
												</div>
											</div>
										</div>
									</div>
                                    <div class="row" style="border-top:1px solid #d2d3d4; padding-top:20px; margin:20px 0 40px 0;">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                            </div>
                                        </div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="col-sm-3 control-label" style="font-weight:600;color:#e43e41;">Total:</label>
												<div class="col-sm-6">
													<input type="text" class="form-control" maxlength="100" name="txtTotal_cabezal" id="txtTotal_cabezal" autocomplete="off" />
												</div>
											</div>
										</div>
                                        <div class="col-md-6">
                                            <div class="form-group" style="display:none;">
												<label class="col-sm-3 control-label">UUID:</label>
												<div class="col-sm-6">
													<input type="text" class="form-control" maxlength="100" name="concepto" id="concepto" autocomplete="off" />
												</div>
											</div>
										</div>
                                    </div>
                                    
                                    <div class="panel-body collapse in" id="pnl-body-details-added" style="border-top:1px solid #d2d3d6;">
                                        <table id="tblAdded" class="table table-striped table-bordered" style="overflow:scroll;" cellpadding="0" cellspacing="0" border="0">
                                            <thead>
                                                <th>cantidad</th>
                                                <th>descripcion</th>
                                                <th>precio unitario</th>
                                                <th>precio total</th>
                                                <th>periodo</th>
                                                <th>sucursal</th>
                                                <th>concepto</th>
                                            </thead>
                                            <tbody id="tblAddedBody">
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="panel-body collapse in" id="pnl-body-details" style="border-top:1px solid #d2d3d6;margin-top:10px;">
                                        
                                        
                                        <table class="table table-striped table-bordered" style="overflow:scroll;text-align:center;" cellpadding="0" cellspacing="0" border="0">
                                            <thead>
                                                <th style="text-align:center;">cantidad</th>
                                                <th style="text-align:center;">Descripcion</th>
                                                <th style="text-align:center;">precio unitario</th>
                                                <th style="text-align:center;">precio total</th>
                                                <th style="text-align:center;">periodo</th>
                                                <th style="text-align:center;">sucursal</th>
                                                <th style="text-align:center;">concepto</th>
                                                <th style="text-align:center;">agregar detalle</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="number" class="form-control-doc" style="width:100%;" maxlength="10" name="cantidad" id="txtCantidad1" autocomplete="off" value="0"/>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control-doc" style="width:100%;" maxlength="100" name="concepto" id="txtDescripcion1" autocomplete="off" />
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control-doc" style="width:100%;" maxlength="10" name="precio_u" id="txtPrecioU1" autocomplete="off" value="0"/>
                                                        </td>
                                                    <td>
                                                        <input type="number" class="form-control-doc" style="width:100%;" maxlength="10" name="precio_t" id="txtPrecioT1" autocomplete="off" readonly />
                                                    </td>
                                                    <td>
                                                        <div style="width:100%;">
                                                            <input type="text" class="form-control" id="filterDate2" name="filterDate2" value="' . $_POST["filterDate2"] . '"/>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div style="width:100%;">
                                                            <select class="selectSerch" name="idSucursal" id="idSucursal">
                                                            '.$optCiudades.'
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div style="width:100%;">
                                                            <select class="selectSerch" name="idCliente" id="idConcepto">
                                                                <option value="0">Seleccione un concepto </option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-info" id="btn_add_detail" style="width:100%;" type="button">
														  <i>Agregar</i>
													    </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
									<hr style="margin-top:0; margin-bottom:10px;">
									<h3><span class="label label-danger">Totales</span></h3>
									<hr style="margin-top:0; margin-bottom:10px;">
									<div class="row">
										<div class="col-md-4" style="float:right;">
											<div class="form-group">
												<label class="col-sm-3 control-label">TOTAL:</label>
												<div class="col-sm-6">
													<input type="text" readonly style="background-color:white;" class="form-control" maxlength="18" name="txtTotal" id="txtTotal" value="0.00" autocomplete="off"/>
												</div>
											</div>
										</div>
									</div>
									<h3></h3>
									<hr style="margin-top:0; margin-bottom:10px;">
									<div class="row">
										<div class="col-sm-12">
											<div class="btn-toolbar id="btnsGuarCanCont" style="float:right">
												<i class="btn-danger btn" onclick="navegar();">Cancelar</i>
                                                 <!--id="btnGuardarDoc"-->
                                                <button class="btn-warning btn" id="btnRegistrarDocumento" href="" data-toggle="modal">
                                                    <i>Guardar</i>
                                                </button>
												<button class="btn btn-save" style="background:#a0d468;" type="button" href="#myModal4" id="bootbox-demo-5" data-toggle="modal">
                                                    <i>Pagar Ahora</i>
                                                </button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row" id="divCapturaDetalles" style="display:none;">
						<div class="col-md-12">
							<div class="panel panel-danger">
								<div class="panel-body collapse in">
									<div class="row">
										<div class="col-md-12">
											<div class="table-responsive">
												<table class="table table-bordered table-striped">
													<thead>
														<tr>
															<th>CANTIDAD</th>
															<th>PRODUCTO</th>
															<th>TIPO EGRESO</th>
															<th class="thProyecto">CLIENTE</th>
															<th class="thProyecto">PROYECTO</th>
															<th>IMPORTE</th>
															<th>IVA(%)</th>
															<th>IVA(%)</th>
															<th>ACCIONES</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td style="width:70px;">
																<input class="form-control" type="text" autocomplete="off" name="cantidad" id="cantidad" maxlength="50">
															</td>
															<td style="width:150px;">
																<input class="form-control" type="text" autocomplete="off" name="producto" id="producto" maxlength="50">
															</td>
															<td>
																<select style="width:100% !important" name="idTipoEgreso" id="idTipoEgreso" class="selectSerch">
																	<option value="0" selected="selected">Seleccione tipo egreso</option>
																	' . $optCuentasHijo . '
																</select>
															</td>
															<td class="thProyecto">
																<select style="width:100% !important" name="idDetalleCliente" id="idDetalleCliente" class="selectSerch">
																	<option value="0" selected="selected">Seleccione un cliente</option>
																	' . $optClientes . '
																</select>
															</td>
															<td class="thProyecto">
																<select style="width:100% !important" name="idDetalleProyecto" id="idDetalleProyecto" class="selectSerch">
																	<option value="0" selected="selected">Seleccione un proyecto</option>                                                    
																</select>
															</td>
															<td style="width:150px;">
																<input name="importe" id="importe" maxlength="20" class="form-control" type="text" autocomplete="off">
															</td>
															<td style="width:150px;">
																<input name="FactorIva" id="factorIva" maxlength="20" class="form-control" type="text" autocomplete="off">
															</td>
															<td>
																<div class="btn-group">
																	<a class="btn btn-default" onclick="guardarDetalleEgreso();">
																		<i class="icon-plus-sign"></i>
																		<span>Agregar</span>
																	</a>
																</div>
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
									<div class="row">	
										<div class="col-md-12">												
											<div id="divDetallesEgreso" style="height:357px;">
												<div class="table-responsive">
													<table class="table table-bordered table-striped" id="js-tabla">
														<thead>
															<tr>
																<th>CANTIDAD</th>
																<th>PRODUCTO</th>
																<th>TIPO DE EGRESO</th>
																<th>PROYECTO</th>
																<th>SUBTOTAL</th>
																<th>IVA(%)</th>
																<th>TOTAL</th>
																<th>ACCION</th>  
															</tr>	
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
												<div id="detallesProducto"></div> 
											</div>
										</div>												
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" 
				aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div id="divFormPago">
								<div class="modal-header">
									<h4 class="modal-title">Nuevo proveedor</h4>
								</div>
								<div class="modal-body">
									<div class="form-horizontal">
										<div class="form-group">
											<label for="nombreCliente" class="col-sm-3 control-label">Nombre comercial:</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="nombreProveedor" name="nombreProveedor" maxlength="100"/>
											</div>
										</div>
										<div class="form-group">
											<label for="razon" class="col-sm-3 control-label">Razón social:</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="razon" name="razon" maxlength="100"/>
											</div>
										</div>
										<div class="form-group">
											<label for="rfcProveedor" class="col-sm-3 control-label">RFC:</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="rfcProveedor" name="rfcProveedor" maxlength="12"/>
											</div>
										</div>
										<div class="form-group">
											<label for="calleProveedor" class="col-sm-3 control-label">Calle:</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="calleProveedor" name="calleProveedor" maxlength="100"/>
											</div>
										</div>
										<div class="form-group">
											<label for="numExtProveedor" class="col-sm-3 control-label">Num Ext:</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="numExtProveedor" name="numExtProveedor" maxlength="100"/>
											</div>
										</div>
										<div class="form-group">
											<label for="numIntProveedor" class="col-sm-3 control-label">Num Int:</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="numIntProveedor" name="numIntProveedor" maxlength="100"/>
											</div>
										</div>
										<div class="form-group">
											<label for="coloniaProveedor" class="col-sm-3 control-label">Colonia:</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="coloniaProveedor" name="coloniaProveedor" maxlength="100"/>
											</div>
										</div>
										<div class="form-group">
											<label for="cpProveedor" class="col-sm-3 control-label">CP:</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="cpProveedor" name="cpProveedor" maxlength="5"/>
											</div>
										</div>
										<div class="form-group">
											<label for="id_estado" class="col-sm-3 control-label">Estado:</label>
											<div class="col-sm-6">
												<select id="id_estado" name="id_estado" style="width:100% !important" class="selectSerch">
													' . $optEstados . '
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="id_ciudad" class="col-sm-3 control-label">Ciudad:</label>
											<div class="col-sm-6">
												<span id="city_spn" >
													<select id="id_ciudad" name="id_ciudad" style="width:100% !important" class="selectSerch">
														' . $optCiudades . '
													</select>
												</span>
											</div>
										</div>
										<div class="form-group">
											<label for="nombreContactoProveedor" class="col-sm-3 control-label">
								Nombre de contacto:</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="nombreContactoProveedor" name="nombreContactoProveedor" maxlength="100"/>
											</div>
										</div>
										<div class="form-group">
											<label for="correoProveedor" class="col-sm-3 control-label">Correo:</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="correoProveedor" name="correoProveedor" maxlength="255"/>
											</div>
										</div>
										<div class="form-group">
											<label for="ladaProveedor" class="col-sm-3 control-label">Tel&eacute;fono:</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="ladaProveedor" name="ladaProveedor" style="width:20%; float:left;" maxlength="3"/>
												<input type="text" class="form-control" id="telProveedor" name="telProveedor" style="width:80%;" maxlength="7"/>
											</div>
										</div>
										<div class="form-group">
											<label for="saldo" class="col-sm-3 control-label">Saldo inicial:</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="saldo" name="saldo" maxlength="100" placeholder="0.00"/>
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<i class="btn-danger btn" onclick="" data-dismiss="modal">Cancelar</i>
									<i class="btn-success btn" id="btnAgregarProveedor" data-dismiss="modal">Guardar</i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div id="divFormPago">
								<div class="modal-header">
									<h4 class="modal-title">Nuevo proyecto</h4>
								</div>
								<div class="modal-body">
									<div class="form-horizontal">
										<div class="form-group">
											<label for="nombreCliente" class="col-sm-3 control-label">Titulo:</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="nombreProyecto" name="nombreProyecto" maxlength="100"/>
											</div>
										</div>
										<div class="form-group">
											<label for="id_tipos" class="col-sm-3 control-label">Cliente:</label>
											<div class="col-sm-6">
												<select id="idCliente" name="idCliente" style="width:100% !important" class="selectSerch">
													<option selected disabled value="">Seleccione un cliente</option>
													' . $optClientes . '
												</select>
											</div>
										</div>											
										<div class="form-group">
											<label for="txtProducto" class="col-sm-3 control-label">Descripci&oacute;n:</label>
											<div class="col-sm-6">
												<textarea class="form-control" id="txtProyecto" name="txtProyecto"></textarea>
											</div>
										</div>				
									</div>
								</div>
								<div class="modal-footer">
									<i class="btn-danger btn" data-dismiss="modal">Cancelar</i>
									<i class="btn-success btn" onclick="agregar_proyecto();">Guardar</i>										
								</div>	
							</div>
						</div>
					</div>
				</div>
				<div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div id="divFormPago">
								<div class="modal-header">
									<h4 class="modal-title">Cuenta de egreso</h4>
								</div>
								<div class="modal-body">
									<div class="form-horizontal">	
										<div class="form-group">
											<label class="col-sm-3 control-label">Fecha de pago:</label>
											<div class="col-sm-6">
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
													<input readonly="readonly" type="text" class="form-control" id="datepicker" name="fechaPago" value="' . $hoy . '"/>
												</div>
											</div>
										</div>								
										<div class="form-group">
											<label for="idCta" class="col-sm-3 control-label">Cuenta:</label>
											<div class="col-sm-6">
												<span id="city_spn" >
													<select id="idCta" name="idCta" style="width:100% !important" class="selectSerch">
														<option value="0">Seleccione una cuenta</option>
														' . $optCuentas . '
													</select>
												</span>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Forma de pago:</label>
											<div class="col-sm-6">
												<div class="radio">
													<label>
														<input type="radio" name="optFormaPago" id="optFormaPago" value="3" checked>
														Efectivo
													</label>
												</div>
												<div class="radio">
													<label>
														<input type="radio" name="optFormaPago" id="optFormaPago" value="2">
														Cr&eacute;dito/D&eacute;bito
													</label>
												</div>
												<div class="radio">
													<label>
														<input type="radio" name="optFormaPago" id="optFormaPago" value="1">
														Cheque
													</label>
												</div>
												<div class="radio">
													<label>
														<input type="radio" name="optFormaPago" id="optFormaPago" value="4">
														Trasferencia
													</label>
												</div>
											</div>
										</div>																			
									</div>									
								</div>
								<div class="modal-footer">									
									<i class="btn-danger btn" onclick="" data-dismiss="modal">Cancelar</i>
									 <i class="btn-warning btn" id="btnTerminaEntrada" onclick="terminar_egreso(2);">Guardar/Imprimir</i>
									<i class="btn-success btn" id="btnTerminaEntrada" onclick="terminar_egreso(1);">Guardar</i>
								</div>
							</div>
						</div>
					</div>
				</div>
                
                <div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
						<div class="modal-content">
							<div id="divFormPago">
								<div class="modal-header">
									<h4 class="modal-title">Realizar Nuevo Pago</h4>
								</div>
                                <div class="modal-body">
                                    <div class="form-horizontal">
                                        <div class="form-group">
                                            <label for="fechaPago" class="col-sm-3 control-label">Fecha de Pago:</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    <input readonly="readonly" type="text" class="form-control" id="datepicker" name="fechaPago" value="' . $hoy . '"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="montoPago" class="col-sm-3 control-label">Monto del Pago:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="txtMontoPago_modal" name="MontoPago" value="0.00" maxlength="100"/>
                                                
                                            </div><button class="btn btn-info" id="btn_settle"  style="margin:0 auto;" type="button">
														<i class="fa">LIQUIDAR</i>
													</button>
                                        </div>
                                        <div class="form-group">
                                            <label for="id_tipo_pago" class="col-sm-3 control-label">Tipo de Pago:</label>
                                            <div class="col-sm-6">
                                                <select id="id_tipo_pago" name="id_tipo_pago" style="width:100% !important" class="selectSerch">
                                                    <option value="1">SPEI</option>
                                                    <option value="2">Cheque</option>
                                                    <option value="3">Efectivo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="txtCuentaPago" class="col-sm-3 control-label">Cuenta de Pago:</label>
                                            <div class="col-sm-6">
                                                <select id="txtCuentaPago" name="txtCuentaPago" style="width:100% !important" class="selectSerch">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="txtCuentaPago" class="col-sm-3 control-label">Referencia de Pago:</label>
                                            <div class="col-sm-6">
                                                <input type="text" id="txtReferenciaPago" name="txtReferenciaPago" style="width:100% !important" maxlength="45" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
								<div class="modal-footer">									
									<i class="btn-danger btn" style="margin:10px 3px 10px 10px;" onclick="" data-dismiss="modal">Cancelar</i>
									<i class="btn-success btn" style="margin:10px 3px 10px 10px;" id="btnRegistrarPago" >Realizar Pago</i>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal fade" id="myModal8" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div id="divFormPago">
								<div class="modal-header">
									<h4 class="modal-title">Programacion de pagos</h4>
								</div>
                                <div class="modal-body">
                                    <div class="form-horizontal">
                                        <h4 class="modal-title" style="color:#36bcde;width:100%" id="title_total"></h4>
                                    </div>
                                    <div class="form-horizontal" style="border-top:1px solid #e5e5e5;padding-top:20px;">
                                        <div class="form-group">
                                            <label for="fechaPago" class="col-sm-3 control-label">Fecha de Pago:</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    <input readonly="readonly" type="text" class="form-control" id="datepicker" name="fechaPago" value="' . $hoy . '"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="montoPago" class="col-sm-3 control-label">Monto del Pago:</label>
                                            <div class="col-sm-6">
                                                <input type="nummber" class="form-control" id="txtMonto_to_pay" name="txtMonto_to_pay" value="0.00" maxlength="100"/>
                                                <button class="btn btn-info" id="btn_add_plan" style="margin:20px auto;float:right" type="button">
                                                    <i class="fa">AGREGAR</i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-horizontal" style="border-top: 1px solid #e5e5e5;overflow:auto;" id="pnl_added_planned_payments">

                                    </div>
                                </div>
                                
                                
								<div class="modal-footer">									
									<i class="btn-danger btn" style="margin:10px 3px 10px 10px;" onclick="" data-dismiss="modal">Cancelar</i>
									<i class="btn-success btn" style="margin:10px 3px 10px 10px;" id="btnCrearPagosProyectados" >Confirmar</i>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
				
				<div id="div_articulos"></div>';

    return $pagina;
}

function selectLista($consulta, $columna) {
    liberar_bd();
    $selectLista = 'CALL sp_sistema_lista_' . $consulta;
    $lista = consulta($selectLista);
    foreach ($lista as $reg) {
	if (($consulta == 'estados()' && $reg["id"] == 12) || ($consulta == 'ciudades_edoId(12)' && $reg["id"] == 462)) {
	    $selected = 'selected="selected"';
	}
	if ($consulta == 'ctas_bancos()') {
	    $numero = '(' . $reg["numero"] . ')';
	}
	$options .= '<option ' . $selected . ' value="' . $reg["id"] . '">'
		. utf8_encode($reg[$columna]) . $numero . ' </option>';
    }
    return $options;
}

function egresos_formularioEditar($id) {
    $ctasbancos = 'ctas_bancos()';
    $cuentasHijo = 'cuentas_hijo()';
    $proyectos = 'proyectos()';
    $estados = 'estados()';
    $ciudadesedoId = 'ciudades_edoId(12)';
    $clientes = 'clientes()';
    $datosEgreso = 'datos_egreso(' . $id . ');';
    $proveedorEgreso = 'proveedor_egreso(' . $id . ');';
    $detallesidEgreso = 'detalles_idEgreso(' . $id . ');';
    $columnas = array("cantidad", "producto", "tipo", "proyecto", "subtotal", "iva", "total");

    $tablaDetalles = selectTable($detallesidEgreso, $columnas);

    $optEstados = selectLista($estados, "nombre");

    $optCiudades = selectLista($ciudadesedoId, "nombre");

    $optClientes = selectLista($clientes, "nombre");

    $optCuentas = selectLista($ctasbancos, "nombre");

    $optProyecto = selectLista($proyectos, "nombre");

//LISTA DE CUENTAS HIJO (TIPO DE EGRESO)
    $optCuentasHijo = selectLista($cuentasHijo, "nombre");

//SELECT PROVEEDOR
    $optProveedores = selectLabel($proveedorEgreso, "proveedor");

//SELECT DATOS EGRESO
    $fechaDoc = selectLabel($datosEgreso, "fechaDoc");

//SELECT FOLIO
    $folioDoc = selectLabel($datosEgreso, "folioDoc");

//SELECT CONCEPTO
    $concepto = selectLabel($datosEgreso, "concepto");

//SELECT OBSERVACION
    $observacion = selectLabel($datosEgreso, "observacion");

//SELECT TOTAL
    $total = selectLabel($datosEgreso, "total");

//SELECT SUBTOTAL
    $subtotal = selectLabel($datosEgreso, "subtotal");

//SELECT IVA
    $iva = selectLabel($datosEgreso, "iva");

    $_SESSION["idProyectoActual"] = '';
    $_SESSION["idProveedorActual"] = '';

    $pagina.='
<div id = "page-heading">
    <ol class = "breadcrumb">
	<li><a href = "javascript:navegar_modulo(0);">Dashboard</a></li>
	<li><a href = "javascript:navegar_modulo(' . $_SESSION["mod"] . ');">'
	    . $_SESSION["moduloPadreActual"] . '</a></li>
	<li class = "active">
	    ' . $_SESSION["moduloHijoActual"] . '
	</li>
    </ol>
    <h1>' . $_SESSION["moduloHijoActual"] . '</h1>
    <div class = "options">
	<div class = "btn-toolbar">
	    <input type = "hidden" id = "idEgresoActual" name = "idEgresoActual" 
	    value = "' . $id . '" readonly = "readonly"/>
	    <input type = "hidden" id = "idProyectoActualEditar" name = "idProyectoActual" 
	    value = "0" readonly = "readonly"/>
	    <input type = "hidden" id = "sumDetallesEditar" name = "sumDetalles" 
	    value = "0" readonly = "readonly"/>
	</div>
    </div>
</div>
<div class = "container">
    <div class = "row">
	<div class = "col-md-12">
	    <div class = "panel panel-danger">
		<div class = "panel-heading">
		    <h4></h4>
		    <div class = "options">
			<a href = "javascript:;" class = "panel-collapse">
			<i class = "icon-chevron-down"></i></a>
		    </div>
		</div>
		<div class = "panel-body collapse in" id = "divDatosEgreso">
		    <hr style = "margin-top:0; margin-bottom:10px;">
		    <h3><span class = "label label-danger">Datos generales</span></h3>
		    <hr style = "margin-top:0; margin-bottom:10px;">
		    <div class = "row">
			<div class = "col-md-6">
			    <div class = "form-group">
				<label for = "idProveedor" class = "col-sm-3 control-label">Proveedor:</label>
				<div class = "col-sm-6">                                        
				    ' . $optProveedores . '
				</div>

			    </div>
			</div>                            
		    </div>
		    <div class = "row">
			<div class = "col-md-6">
			    <div class = "form-group">
				<label class = "col-sm-3 control-label">Fecha del Documento:</label>
				<div class = "col-sm-6">
				    <div class = "input-group">
					' . $fechaDoc . '
				    </div>
				</div>
			    </div>
			</div>
			<div class = "col-md-6">
			    <div class = "form-group">
				<label for = "folioCliente" class = "col-sm-3 control-label">
				Folio Documento:</label>
				<div class = "col-sm-6">
				    ' . $folioDoc . '
				</div>
			    </div>
			</div>
		    </div>
		    <div class = "row">
			<div class = "col-md-6">
			    <div class = "form-group">
				<label class = "col-sm-3 control-label">Concepto:</label>
				<div class = "col-sm-6">
				    ' . $concepto . '
				</div>
			    </div>
			</div>
			<div class = "col-md-6">
			    <div class = "form-group">
				<label class = "col-sm-3 control-label">Observación:</label>
				<div class = "col-sm-6">
				    ' . $observacion . '
				</div>
			    </div>
			</div>
		    </div>
		    <hr style = "margin-top:0; margin-bottom:10px;">
		    <h3><span class = "label label-danger">Totales</span></h3>
		    <hr style = "margin-top:0; margin-bottom:10px;">
		    <div class = "row">
			<div class = "col-md-4">
			    <div class = "form-group">
				<label class = "col-sm-3 control-label">Subtotal:</label>
				<div class = "col-sm-6">
				    $' . $subtotal . '
				</div>
			    </div>
			</div>
			<div class = "col-md-4">
			    <div class = "form-group">
				<label class = "col-sm-3 control-label">IVA:</label>
				<div class = "col-sm-6">
				    ' . $iva . '%
				</div>
			    </div>
			</div>
			<div class = "col-md-4">
			    <div class = "form-group">
				<label class = "col-sm-3 control-label">TOTAL:</label>
				<div class = "col-sm-6">
				    $' . round_to_2dp($total) . '
				</div>
			    </div>
			</div>
		    </div>
		    <h3></h3>
		    <hr style = "margin-top:0; margin-bottom:10px;">
		    <div class = "row">
			<div class = "col-sm-12">
			    <div class = "btn-toolbar btnsGuarCan">
				<i class="btn-danger btn" onclick="navegar();">Cancelar</i>
                                <i class="btn-success btn" onclick="guardarEgreso();">Continuar</i>
			    </div>
			</div>
		    </div>
		</div>
	    </div>
	</div>
    </div>';
    $pagina.='
    <div class="row" id="divCapturaDetalles">
        <div class="col-md-12">
            <div class="panel panel-danger">
                <div class="panel-body collapse in">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>CANTIDAD</th>
                                            <th>PRODUCTO</th>
                                            <th>TIPO EGRESO</th>
                                            <th class="thProyecto">PROYECTO</th>
                                            <th>IMPORTE</th>
                                            <th>IVA</th>
                                            <th>ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width:70px;">
                                                <input class="form-control" type="text" autocomplete="off" 
						name="cantidad" id="cantidadEditar" maxlength="50">
                                            </td>
                                            <td style="width:150px;">
                                                <input class="form-control" type="text" autocomplete="off" 
						name="producto" id="productoEditar" maxlength="50">
                                            </td>
                                            <td>
                                                <select style="width:100% !important" name="idTipoEgreso" 
						id="idTipoEgresoEditar" class="selectSerch">
                                                    <option value="0" selected="selected">
						    Seleccione tipo egreso</option>
                                                    ' . $optCuentasHijo . '
                                                </select>
                                            </td>
                                            <td class="thProyecto">
                                                <select style="width:100% !important" 
						name="idDetalleProyecto" id="idDetalleProyectoEditar" 
						class="selectSerch">
                                                    <option value="0" selected="selected">
						    Seleccione Proyecto</option>
                                                    ' . $optProyecto . '
                                                </select>
                                            </td>
                                            <td style="width:150px;">
                                                <input name="importe" id="importeEditar" maxlength="20" 
						class="form-control" type="text" autocomplete="off">
                                            </td>
                                            <td style="width:150px;">
                                                <input name="FactorIva" id="factorIvaEditar" maxlength="20" 
						class="form-control" type="text" autocomplete="off">
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a class="btn btn-default" 
						    onclick="guardarDetalleEgresoEditar();">
                                                        <i class="icon-plus-sign"></i>
                                                        <span>Agregar</span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">	
                        <div class="col-md-12">												
                            <div id="divDetallesEgreso" style="height:357px;">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="js-tabla">
                                        <thead>
                                            <tr>
                                                <th>CANTIDAD</th>
                                                <th>PRODUCTO</th>
                                                <th>PRECIO UNITARIO</th>
                                                <th>PERIODO</th>
                                                <th>SUBTOTAL</th>
                                                <th>IVA</th>
                                                <th>TOTAL</th>
                                                <th>ACCION</th>  
                                            </tr>	
                                        </thead>
                                        <tbody>
					' . $tablaDetalles . '
                                        </tbody>
                                    </table>
                                </div>
                                <div id="detallesProductoEditar"></div> 
                            </div>
                        </div>												
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" 
aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="divFormPago">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo proveedor</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label for="nombreCliente" class="col-sm-3 control-label">Nombre comercial:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="nombreProveedor" 
				name="nombreProveedor" maxlength="100"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="razon" class="col-sm-3 control-label">Razón social:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="razon" 
				name="razon" maxlength="100"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="rfcProveedor" class="col-sm-3 control-label">RFC:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="rfcProveedor" 
				name="rfcProveedor" maxlength="12"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="calleProveedor" class="col-sm-3 control-label">Calle:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="calleProveedor" 
				name="calleProveedor" maxlength="100"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="numExtProveedor" class="col-sm-3 control-label">Num Ext:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="numExtProveedor" 
				name="numExtProveedor" maxlength="100"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="numIntProveedor" class="col-sm-3 control-label">Num Int:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="numIntProveedor" 
				name="numIntProveedor" maxlength="100"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="coloniaProveedor" class="col-sm-3 control-label">Colonia:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="coloniaProveedor" 
				name="coloniaProveedor" maxlength="100"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cpProveedor" class="col-sm-3 control-label">CP:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="cpProveedor" 
				name="cpProveedor" maxlength="5"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="id_estado" class="col-sm-3 control-label">Estado:</label>
                            <div class="col-sm-6">
                                <select id="id_estado" name="id_estado" style="width:100% !important" 
				class="selectSerch">
                                    ' . $optEstados . '
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="id_ciudad" class="col-sm-3 control-label">Ciudad:</label>
                            <div class="col-sm-6">
                                <span id="city_spn" >
                                    <select id="id_ciudad" name="id_ciudad" style="width:100% !important" 
				    class="selectSerch">
                                        ' . $optCiudades . '
                                    </select>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nombreContactoProveedor" class="col-sm-3 control-label">
			    Nombre de contacto:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="nombreContactoProveedor" 
				name="nombreContactoProveedor" maxlength="100"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="correoProveedor" class="col-sm-3 control-label">Correo:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="correoProveedor" 
				name="correoProveedor" maxlength="255"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ladaProveedor" class="col-sm-3 control-label">Tel&eacute;fono:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="ladaProveedor" 
				name="ladaProveedor" style="width:20%; float:left;" maxlength="3"/>
                                <input type="text" class="form-control" id="telProveedor" 
				name="telProveedor" style="width:80%;" maxlength="7"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="saldo" class="col-sm-3 control-label">Saldo inicial:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="saldo" name="saldo" 
				maxlength="100" placeholder="0.00"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <i class="btn-danger btn" onclick="" data-dismiss="modal">Cancelar</i>
                    <i class="btn-success btn" onclick="agregar_proveedor()">Guardar</i>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="divFormPago">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo proyecto</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label for="nombreCliente" class="col-sm-3 control-label">Titulo:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="nombreProyecto" 
				name="nombreProyecto" maxlength="100"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="id_tipos" class="col-sm-3 control-label">Cliente:</label>
                            <div class="col-sm-6">
                                <select id="idCliente" name="idCliente" style="width:100% !important" 
				class="selectSerch">
                                    <option selected disabled value="">Seleccione un cliente</option>
                                    ' . $optClientes . '
                                </select>
                            </div>
                        </div>											
                        <div class="form-group">
                            <label for="txtProducto" class="col-sm-3 control-label">Descripci&oacute;n:</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" id="txtProyecto" name="txtProyecto"></textarea>
                            </div>
                        </div>				
                    </div>
                </div>
                <div class="modal-footer">
                    <i class="btn-danger btn" data-dismiss="modal">Cancelar</i>
                    <i class="btn-success btn" onclick="agregar_proyecto();">Guardar</i>										
                </div>	
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="divFormPago">
                <div class="modal-header">
                    <h4 class="modal-title">Cuenta de egreso</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">	
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Fecha de pago:</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input readonly="readonly" type="text" class="form-control" 
				    id="datepicker" name="fechaPago" value="' . $hoy . '"/>
                                </div>
                            </div>
                        </div>								
                        <div class="form-group">
                            <label for="idCta" class="col-sm-3 control-label">Cuenta:</label>
                            <div class="col-sm-6">
                                <span id="city_spn" >
                                    <select id="idCta" name="idCta" style="width:100% !important" 
				    class="selectSerch">
                                        <option value="0">Seleccione una cuenta</option>
                                        ' . $optCuentas . '
                                    </select>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Forma de pago:</label>
                            <div class="col-sm-6">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="optFormaPago" id="optFormaPago" 
					value="3" checked>
                                        Efectivo
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="optFormaPago" id="optFormaPago" value="2">
                                        Cr&eacute;dito/D&eacute;bito
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="optFormaPago" id="optFormaPago" value="1">
                                        Cheque
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="optFormaPago" id="optFormaPago" value="4">
                                        Trasferencia
                                    </label>
                                </div>
                            </div>
                        </div>																			
                    </div>									
                </div>
                <div class="modal-footer">									
                    <i class="btn-danger btn" onclick="" data-dismiss="modal">Cancelar</i>
                    <i class="btn-success btn" id="btnTerminaEntrada" onclick="terminar_egreso();">Guardar</i>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="div_articulosEditar"></div>';

    return $pagina;
}

function egresos_guardar() 
{
    $fechaPago = normalize_date2($_POST["fechaPago"]);

	//ACTUALIZAMOS FECHA DE PAGO, CTA DE PAGO Y TIPO DE PAGO
    liberar_bd();
    $updateEgresoPagoCta = 'CALL sp_sistema_update_egreso_pago_cta(	'. $_SESSION["idEgresoActual"] . ',
																	"'. $fechaPago . '",
																	'. $_POST["optFormaPago"] . ',
																	'. $_POST["idCta"] . ');';
    $updateEPC = consulta($updateEgresoPagoCta);

    if ($updateEPC) 
	{
		//DATOS DE LA CUENTA
		liberar_bd();
		$selectDatosCuenta = 'CALL sp_sistema_select_datos_cuentas(' . $_POST["idCta"] . ');';
		$datosCuenta = consulta($selectDatosCuenta);
		$cuenta = siguiente_registro($datosCuenta);
	
		$nvoSaldo = $cuenta["monto"] - $_POST["totalEgreso"];

		//GUARDAMOS NUEVO SALDO
		liberar_bd();
		$updateCuenta = 'CALL sp_sistema_update_saldo_cuenta('
			. $_POST["idCta"] . ', "'
			. $nvoSaldo . '", '
			. $_SESSION[$varIdUser] . ');';
		$update = consulta($updateCuenta);

		//ACTUALIZAMOS ESTATUS DE DETALLES DE EGRESO
		liberar_bd();
		$updateEstatusDetallesEgreso = 'CALL sp_sistema_update_estatus_detalles_egreso('
			. $_SESSION["idEgresoActual"]
			. ', 1, '
			. $_SESSION[$varIdUser] . ');';
		$updateEDE = consulta($updateEstatusDetallesEgreso);

		//ACTUALIZAMOS ESTATUS DE EGRESO
		liberar_bd();
		$updateEstatusEgreso = 'CALl sp_sistema_update_estatus_egreso('. $_SESSION["idEgresoActual"]. ', 1, '. $_SESSION[$varIdUser] . ');';
		$updateE = consulta($updateEstatusEgreso);
		
		//CREAMOS EL RECIBO
		//DATOS DE LA EMPRESA
		liberar_bd();
		$selecDatosEmpresa = "CALL sp_sistema_select_datos_empresa();";							  
		$datosEmpresa = consulta($selecDatosEmpresa);	
		$empresa = siguiente_registro($datosEmpresa);
		
		//DATOS DEL EGRESO
		liberar_bd();
		$selectDatosEgreso = 'CALL sp_sistema_select_datos_egreso('.$_SESSION["idEgresoActual"].');';
		$datosEgresos = consulta($selectDatosEgreso);
		$dateEgr = siguiente_registro($datosEgresos);
		
		$subtotalEgr = $dateEgr["subtotal"]; 
		$ivaEgr = $dateEgr["iva"];	
		$totalEgr = $dateEgr["total"];	
		
		//CHECAMOS SI SE ASIGNO PROVEEDOR
		liberar_bd();
		$selectProveedorEgreso = 'CALL sp_sistema_select_proveedor_egreso('.$_SESSION["idEgresoActual"].');';
		$proveedorEgreso = consulta($selectProveedorEgreso);
		$ctaProveedorEgreso = cuenta_registros($proveedorEgreso);
		if($ctaProveedorEgreso != 0)
		{
			$provEgr = siguiente_registro($proveedorEgreso);
			$datosProveedor = 'Proveedor: '.utf8_encode($provEgr["proveedor"]);
		}
		else
			$datosProveedor = 'Proveedor: Sin proveedor';
			
		//DATOS DEL PROYECTO
		if($_SESSION["idProyectoActual"] != '')
		{
			liberar_bd();
			$selectProyecto = 'CALL sp_sistema_select_datos_proyecto('.$_SESSION["idProyectoActual"].');';
			$proyecto = consulta($selectProyecto);
			$proy = siguiente_registro($proyecto);
			$datosProyecto = ' Proyecto: '.utf8_encode($proy["nombre"]);
		}
		else
			$datosProyecto = '';
		
		$fechaFormt = date('Y-m-d');
		$primerDia = date('Y-m');
		$fechaFormtFiltro = date('d/m/Y');
		$primerDiaFiltro = date('m/Y');
		
		$header = 	'	<style>
							@page 
							{
	
							}
	
							body
							{
								color: #666666;
								font-family: Arial,Helvetica,sans-serif;
								font-size: 13px;
								line-height: 16px;
								font: 13px/1.231 sans-serif;
								text-align: justify !important;
							}
	
							.tablaDetalles
							{
								width:100%;
							}
							
							.tablaDetalles-header + .table 
							{
								border-top: 0;
							}
							
							.tablaDetalles  thead,
							.tablaDetalles  tbody  tr  th,
							.tablaDetalles  tfoot  tr  th 
							{
								text-align: center;
							}
	
							.tablaDetalles  tbody  tr .tdNumerico,
							.tablaDetalles  tfoot  tr .tdNumerico
							{
								text-align:right !important;
							}	
							
							.txtFecha
							{
								text-align:right !important;
							}
	
							.contenedorImg 
							{
								width: 100%;
								display: table;
								text-align: center;
							}
	
							.divImg
							{
								vertical-align: middle;
								display: table-cell;
								table-layout: fixed;
							}
	
							.divImgHojas
							{
								width: 100%;							
							}
	
							.imgHojas
							{
								float: right;
								margin-top: -40px;
								margin-right: -40px;
							}
	
							.imgLogo 
							{
								
							}
	
							.contenedorImgs
							{
								width: 100%;
								display: table;
								text-align: center;
							}
	
							.divImgPre 
							{
								width: 33%;
								float: left;
							}
	
							.imgPre
							{
								width: 90%;
							}
	
							.contenedor
							{
								padding:0 25px;
							}
									
						</style>					
					';	
	
		$htmlHeader = '';	
					  
		$html = '	<div class="contenedor">
						<table class="tablaDetalles">
							<tbody>
								<tr>
									<td>
										<p id="txtDatos">'.convertMayus($datosProveedor).'<br>
										 '.convertMayus($datosProyecto).'<br>
										 Fecha del Documento: '.normalize_date($dateEgr["fechaDoc"]).'<br>
										 Folio Documento: '.$dateEgr["folioDoc"].'<br>
										 Concepto: '.utf8_encode($dateEgr["concepto"]).'<br>
										 Observación: '.utf8_encode($dateEgr["observacion"]).'</p>
									</td>
									<td style="text-align:right;">
										<div class="contenedorImg">
											<div class="divImg">
												<img width="100px" alt="" src="imagenes/empresa/'.$empresa["logo"].'" />
												<p>Tel. '.utf8_encode($empresa["telefono"]).'</p>																	  		
											</div>
										</div>	
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="contenedor">
						<table class="tablaDetalles">
							<thead>
								<th>CANTIDAD</th>
								<th>PRODUCTO</th>
								<th>TIPO EGRESO</th>
								<th class="thProyecto">PROYECTO</th>
								<th>TOTAL</th>
							</thead>
							<tbody>';
					
					
		//DETALLES DEL EGRESO
		liberar_bd();
		$selectDetallesEgreso = 'CALL sp_sistema_select_detalles_idEgreso('.$_SESSION["idEgresoActual"].');';
		$detallesEgreso = consulta($selectDetallesEgreso);
		while($det=siguiente_registro($detallesEgreso))
		{
			//CHECAMOS PROYECTO DEL DETALLE
			liberar_bd();
			$selectProyectoDetalle = 'CALL sp_sistema_select_proyecto_detalle_egreso('.$det["id"].');';
			$proyectoDetalle = consulta($selectProyectoDetalle);
			$ctaProyectoDetalle = cuenta_registros($proyectoDetalle);
			if($ctaProyectoDetalle != 0)
			{
				$proyDet = siguiente_registro($proyectoDetalle);
				$detProyecto = utf8_encode($proyDet["nombre"]);
			}
			else
				$detProyecto = '';
				
			$html.='<tr>
						<td>'.$det["cantidad"].'</td>
						<td>'.$det["producto"].'</td>
						<td>'.$det["tipo"].'</td>
						<td>'.$detProyecto.'</td>
						<td class="tdNumerico">'.number_format($det["total"],2).'</td>
					</tr>';
		}	
		
		$html .= '		</tbody>
						<tfoot>
							<tr>
								<th class="tdNumerico" colspan="4">TOTAL</th>
								<th class="tdNumerico">$'.number_format($totalEgr,2).'</th>
							</tr>	
							<tr>
								<th class="tdNumerico" colspan="4"></th>
								<th class="tdNumerico"></th>
							</tr>						
						</tfoot>					
					</table>
				</div>
				<div class="contenedor">
					<p id="txtDatos">Recibí de Claustro Santa Fe la cantidad de $'.number_format($totalEgr,2).'<br>
									 Por concepto '.utf8_encode($dateEgr["observacion"]).'<br><br><br>
									 Nombre: _________________________________________________________<br><br>
									 Firma:  ___________________________________________________________</p>
				</div>';
	
	
		$htmlFooter = '';
		
		include_once('../clases/mpdf/mpdf.php');
			
		$src =	date("YmdHis").".pdf";
		$htmlSalidas = utf8_encode($html);
		$mpdf=new mPDF('utf-8');
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->DefHTMLHeaderByName('Chapter2Header','<div style="text-align: right; border-bottom: 1px solid #000000; font-weight: bold; font-size: 10pt;">Chapter 2</div>');
		$mpdf->WriteHTML($header);
		$mpdf->WriteHTML($htmlSalidas);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->Output("imagenes/egresos/".$src,"F");
		
		//GUARDAMOS URL DEL EGRESO
		liberar_bd();
		$updateUrlEgreso = 'CALL sp_sistema_update_url_egreso('.$_SESSION["idEgresoActual"].', "'.$src.'");';
		$updateUE = consulta($updateUrlEgreso);
		
		if($_POST["idTipoEntrega"] == 1)
			$imprimeDoc = '';
		elseif($_POST["idTipoEntrega"] == 2)
			$imprimeDoc = '<script>
								var idArchivo = \''.$src.'\';
								$(document).ready( function()
								 {
									$("#myModal2").modal("toggle");
									var archivo = "imagenes/egresos/"+idArchivo;
									$("#divVerEgreso").html("");
									$("#divVerEgreso").html(\'<embed src="" style="width:100%; height:500px;">\');
									$("#divVerEgreso embed").attr("src", archivo);				
								 });
							</script>';
		
		$error = 'Se ha creado el egreso.';
		$msj = sistema_mensaje("exito", $error);
		$res = $msj . egresos_menuInicio().$imprimeDoc;
    } 
	else 
	{
		$error = 'No se ha podido guardar el egreso.';
		$msj = sistema_mensaje("error", $error);
		$res = $msj . egresos_menuInicio();
    }

    return $res . $pagina;
}

function egresos_detalles2() {
//DATOS DEL EGRESO
    liberar_bd();
    $selectDatosEgreso = 'CALL sp_sistema_select_datos_egreso(' . $_POST["idEgreso"] . ');';
    $datosEgreso = consulta($selectDatosEgreso);
    $egr = siguiente_registro($datosEgreso);

//CUENTA
    liberar_bd();
    $selectCuenta = 'CALL sp_sistema_select_datos_cuentas(' . $egr["idCta"] . ');';
    $cuenta = consulta($selectCuenta);
    $cue = siguiente_registro($cuenta);

    $pagina = '	<div id="page-heading">	
    <ol class="breadcrumb">
        <li><a href="javascript:navegar_modulo(0);">Dashboad</a></li> 
        <li><a href="javascript:navegar_modulo(' . $_SESSION["mod"] . ');">'
	    . $_SESSION["moduloPadreActual"] . '</a></li>    
        <li class="active">
            ' . $_SESSION["moduloHijoActual"] . '
        </li>
    </ol>  
    <h1>' . $_SESSION["moduloHijoActual"] . '</h1>
    <div class="options">
        <div class="btn-toolbar">									
        </div>
    </div>										
</div>
<div class="container">							
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h4>Detalles de egreso</h4>
                </div>
                <div class="panel-body" style="border-radius: 0px;">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label for="entidadIngr" class="col-sm-3 control-label">Entidad:</label>
                            <div class="col-sm-6">
                                <input type="text" readonly="readonly" 
				class="form-control" id="entidadIngr" name="entidadIngr" maxlength="100" 
				value="' . utf8_encode($egr["entidad"]) . '"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="idCuenta" class="col-sm-3 control-label">Cuenta:</label>
                            <div class="col-sm-6">
                                <input type="text" readonly="readonly" class="form-control" 
				id="idCuenta" name="idCuenta" maxlength="100" value="'
	    . utf8_encode($cue["banco"]) . '(' . $cue["numero"] . ')"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="montoEgr" class="col-sm-3 control-label">Monto:</label>
                            <div class="col-sm-6">
                                <input type="text" readonly="readonly" class="form-control" 
				id="montoEgr" name="montoEgr" maxlength="100" value="'
	    . number_format($egr["cantidad"], 2) . '"/>
                            </div>				
                        </div>
                        <div class="form-group">
                            <label for="datepicker" class="col-sm-3 control-label">Fecha de egreso:</label>
                            <div class="col-sm-6">
                                <input type="text" readonly="readonly" class="form-control" 
				id="datepicker" name="datepicker" maxlength="100" value="'
	    . normalize_date($egr["fecha"]) . '"/>
                            </div>						
                        </div>	
                        <div class="form-group">
                            <label for="conceptoEgr" class="col-sm-3 control-label">Concepto:</label>
                            <div class="col-md-6">	
                                <textarea readonly="readonly" class="form-control autosize" 
				name="conceptoEgr" id="conceptoEgr">'
	    . utf8_encode($egr["concepto"]) . '</textarea>
                            </div>													
                        </div>										
                        <div class="form-group">
                            <label for="txtEgreso" class="col-sm-3 control-label">Observaciones:</label>
                            <div class="col-md-6">	
                                <textarea readonly="readonly" class="form-control autosize" 
				name="txtEgreso" id="txtEgreso">'
	    . utf8_encode($egr["txt"]) . '</textarea>
                            </div>						
                        </div>									
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="btn-toolbar btnsGuarCan">
                                <i class="btn-danger btn" onclick="navegar();">Cancelar</i>
                                <i class="btn-success btn" onclick="navegar(\'Guardar\');">Guardar</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';

    return $pagina;
}

function selectLabel($consulta, $columna) {
    liberar_bd();
    $consulta = 'call sp_get_documento_proveedor_for_id' . $consulta;
    $registros = consulta($consulta);
    foreach ($registros as $reg) {
	$label.= '<label id="' . $reg["id"] . '">' . utf8_encode($reg[$columna]) . '</label>';
    }
    return $label;
}

function selectTable($consulta, $columnas) {
    liberar_bd();
    $consulta = 'call sp_get'.$consulta;
    $registros = consulta($consulta);
    foreach ($registros as $reg) {
	$tabla.='<tr  id="det' . $reg["id_detalle"] . '">';
	foreach ($columnas as $columna) {
        if($columna == 'subtotal'){
           $reg[$columna] ;
        }else{
	       $tabla.= '<td>' . utf8_encode($reg[$columna]) . '</td>';
        }
	}

	$tabla.='</tr>';
    }
    return $tabla;
}

function egresos_detalles($id) {
    $datosEgreso = '(\''.$id.'\');';
    $detallesidEgreso = '_detalle_documento(' . $id . ');';
    $columnas = array("producto", "cantidad", "precio_u", "iva", "precio_total", "id_sucursal", "idConcepto");

//SELECT PROVEEDOR
    $optProveedores = selectLabel($datosEgreso, "nombre_proveedor");

//SELECT DATOS EGRESO
    $fechaDoc = selectLabel($datosEgreso, "fecha_doc");
    
//SELECT STATUS
    $statusDoc = selectLabel($datosEgreso, "estatus");

//SELECT FOLIO
    $folioDoc = selectLabel($datosEgreso, "folio");

//SELECT CONCEPTO
    $concepto = selectLabel($datosEgreso, "concepto_documento");

//SELECT OBSERVACION
    $observacion = selectLabel($datosEgreso, "observaciones");

//SELECT TOTAL
    $total = selectLabel($datosEgreso, "monto_documento");

//SELECT SUBTOTAL
    $subtotal = selectLabel($datosEgreso, "monto_documento");

//SELECT IVA
    $iva = selectLabel($datosEgreso, "saldo_pendiente");

    $tablaDetalles = selectTable($detallesidEgreso, $columnas);
    
    //SELECCIONA LOS PAGOS PARA EL DOCUMENTO
    $tablaPagos = selectTable('_pagos("10", "'.$id.'");', array("id_pagos", "fecha_pago", "monto_pago", "estatus", "usuario_pago", "nombre_sucursales", "numero_ctas_banco", "banco_ctas_banco"));

    $_SESSION["idProyectoActual"] = '';
    $_SESSION["idProveedorActual"] = '';
    
    $hoy = date("Y-m-d");
                     
    switch(trim($statusDoc)){
     
        case '<label id="">3</label>':
            $span = '<span style="font-size:14px; font-weight:bold;" class="banner_magenta">CAPTURADO';
            $class = 'magenta';
            break;
        case '<label id="">2</label>':
            $span = '<span style="font-size:14px; font-weight:bold;" class="banner_azul">PROGRAMADO';
            $class = 'azul';
            break;
        case '<label id="">4</label>':
            $span = '<span style="font-size:14px; font-weight:bold;" class="banner_verde">LIQUIDADO';
            $class = 'verde';
            break;
        case '<label id="">5</label>':
            $span = '<span style="font-size:14px; font-weight:bold;" class="banner_amarillo">ABONADO';
            $class = 'amarillo';
            break;
        default:
            $span = '<span style="font-size:14px; font-weight:bold;" class="banner_magenta">'.selectLabel($datosEgreso, "estatus");
            $class = 'magenta';
            break;
    }

    $pagina.='
<div id = "page-heading">
    <ol class = "breadcrumb">
	<li><a href = "javascript:navegar_modulo(0);">Dashboard</a></li>
	<li><a href = "javascript:navegar_modulo(' . $_SESSION["mod"] . ');">'
	    . $_SESSION["moduloPadreActual"] . '</a></li>
	<li class = "active">
	    ' . $_SESSION["moduloHijoActual"] . '
	</li>
    </ol>
    <h1>' . $_SESSION["moduloHijoActual"] . '</h1>
    <div class = "options">
	<div class = "btn-toolbar">
	    <input type = "hidden" id="idEgresoActual" name = "idEgresoActual" 
	    value = "0" readonly = "readonly"/>
	    <input type = "hidden" id = "idProyectoActual" name = "idProyectoActual" 
	    value = "0" readonly = "readonly"/>
	    <input type = "hidden" id = "sumDetalles" name = "sumDetalles" 
	    value = "0" readonly = "readonly"/>
	</div>
    </div>
</div>
<div class = "container">
    <div class = "row">
        <div class = "col-md-12">  
            <div class = "panel-body collapse in" id = "divDatosEgreso" style="background:#ffffff;">
                <h3><span class = "label label-ribbon" style="margin-left:-31px;">Datos generales</span></h3>
                <h4 class="upper-ribbon-'.$class.'">'.$span.'</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class = "form-group">
                            <label for = "idProveedor" class = "col-sm-3 control-label">Proveedor:</label>
                            <div class = "col-sm-6">                                        
                                <span style="font-size:14px; font-weight:bold;">' . $optProveedores . '</span>
                            </div>
                        </div>
                    </div>        
                    <div class="col-md-6">
                        <div class = "form-group">
                            <label for = "idProveedor" class = "col-sm-3 control-label">Estatus:</label>
                            <div class = "col-sm-6"><span id="sp_id_doc" style="display:none">'.$id.'</span>'; 
    
        $pagina.= '
                            </div>
                        </div>
                    </div>   
                </div>
                <div class = "row">
                <div class = "col-md-6" style="width:50%">
                    <div class = "form-group">
                    <label class = "col-sm-3 control-label">Fecha del Documento:</label>
                    <div class = "col-sm-6">
                        <div class = "input-group">
                        <span style="font-size:14px; font-weight:bold;" id="sp_fecha_doc">' . $fechaDoc . '</span>
                        </div>
                    </div>
                    </div>
                </div>
                <div class = "col-md-6" style="width:50%">
                    <div class = "form-group">
                    <label for = "folioCliente" class = "col-sm-3 control-label">
                    Folio Documento:</label>
                    <div class = "col-sm-6">
                        <span style="font-size:14px; font-weight:bold; color:#470f0f">' . $folioDoc . '</span>
                    </div>
                    </div>
                </div>
                </div>
                <div class = "row">
                <div class = "col-md-6" style="width:50%">
                    <div class = "form-group">
                    <label class = "col-sm-3 control-label">Concepto:</label>
                    <div class = "col-sm-6">
                        <span style="font-size:14px; font-weight:bold;">' . $concepto . '</span>
                    </div>
                    </div>
                </div>
                <div class = "col-md-6" style="width:50%">
                    <div class = "form-group">
                        <label class = "col-sm-3 control-label">Observación:</label>
                        <div class = "col-sm-6">
                            ' . $observacion . '
                        </div>
                    </div>
                </div>
                </div>
                <h3><span class="label label-ribbon" style="margin-left:-31px;">Totales</span></h3>
                <div class = "row">
                <div class = "col-md-4">
                </div>
                <div class = "col-md-4">
                    <div class = "form-group">
                    <label class = "col-sm-3 control-label">SALDO PENDIENTE:</label>
                    <div class = "col-sm-6">
                        <span style="font-size:15px; font-weight:bold;color:#DA4453">$ ' . money_string_format($iva) . '</span>
                    </div>
                    </div>
                </div>
                <div class = "col-md-4">
                    <div class = "form-group">
                    <label id="ribbon-total" class = "col-sm-3 control-label">TOTAL:</label>
                    <div class = "col-sm-6">
                        <span style="font-size:14px; font-weight:bold;" id="sp_total_doc">$' . money_string_format($total) . '</span>
                    </div>
                    </div>
                </div>
                </div>
                <h3></h3>
                <hr style = "margin-top:0; margin-bottom:10px;">
                <div class = "row">
                <div class = "col-sm-12">
                    <div class = "btn-toolbar btnsGuarCan">
                        <i class = "btn-danger btn"'; 
                        if (strpos($statusDoc, '4') === false && strpos($statusDoc, '3') === false){
                            $pagina.=' href="#myModal7" style="background:#8cc152;"'; 
                        }
                        else{
                            $pagina.=' style="background:#656d78; opacity:0.2"';
                        }
    
                        $pagina.='id="btnRealizarPago" data-toggle="modal">Realizar Pago</i>';
                        if(!strpos($statusDoc, '3') === false){
	                    	$pagina.= '<i class = "btn-danger btn" style="background:#ed5565" id="btnAutorizarDoc">Autorizar</i>';
                        }
                        $pagina.= '<i class = "btn-danger btn" style="background:#aab2bd" onclick="navegar();">Regresar</i>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
    </div>';
    $pagina.='
	<div class = "col-md-12">
		<div class = "panel-body collapse in" style="background:#ffffff;margin: 0 20px 20px 20px;">                        
        
        <div class="panel-heading">
            <h4>DETALLES</h4>
            <div class="options">
                <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
            </div>
        </div>
        <div class="panel-body collapse">
			<div class = "col-md-12">
                <div id = "divDetallesEgreso" style = "height:207px;">
                    <div class = "table-responsive">
                        <table class = "table table-bordered table-striped" id = "js-tabla">
                            <thead>
                                <tr>
                                    <th>DESCRIPCION</th>
                                    <th>CANTIDAD</th>
                                    <th>PRECIO UNITARIO</th>
                                    <th>IVA(%)</th>
                                    <th>TOTAL</th>
                                    <th>SUCURSAL</th>
                                    <th>CONCEPTO</th>
                                </tr>
                            </thead>
                            <tbody>
                                ' . $tablaDetalles . '
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
		</div>
        <div class="panel-heading">
            <h4>PAGOS AL DOCUMENTO</h4>
            <div class="options">
                <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
            </div>
        </div>
<div class="panel-body collapse" >                        
            <div class = "row">
                <div class = "col-md-12">
                    <div id = "divDetallesEgreso" style="height:287px;">
                        <div class = "table-responsive">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered datatables" id="example">
                                <thead>
                                    <tr>
                                        <th>No de Pago</th>
                                        <th>FECHA DE PAGO</th>
                                        <th>MONTO DE PAGO</th>
                                        <th>ESTATUS</th>
                                        <th>USUARIO</th>
                                        <th>SUCURSAL</th>
                                        <th>CUENTA</th>
                                        <th>BANCO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ' . $tablaPagos . '
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="detallesProducto"></div>
                    </div>
                </div>
            </div>
		</div>
	    </div>
</div>';
    
    $pagina .= '<div class="modal fade" id="myModal7" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
						<div class="modal-content">
							<div id="divFormPago">
								<div class="modal-header">
									<h4 class="modal-title">Realizar Nuevo Pago</h4>
                                    <span id="spIdDoc" style="display:none">'.$id.'</span>
								</div>
                                <div class="modal-body">
                                    <div class="form-horizontal">
                                        <div class="form-group">
                                            <label for="fechaPago" class="col-sm-3 control-label">Fecha de Pago:</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    <input readonly="readonly" type="text" class="form-control" id="datepicker" name="fechaPago" value="' . $hoy . '"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="montoPago" class="col-sm-3 control-label">Monto del Pago:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="txtMontoPago_modal" name="MontoPago" value="0.00" maxlength="100"/>
                                                
                                            </div>
                                            <button class="btn btn-info" id="btn_settle_pay_detail"  style="margin:0 auto;" type="button">
                                                <i class="fa">LIQUIDAR</i>
                                            </button>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="txtCuentaPago" class="col-sm-3 control-label">Cuenta de Pago:</label>
                                            <div class="col-sm-6">
                                                <select id="txtCuentaPago" name="txtCuentaPago" style="width:100% !important" class="selectSerch">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="id_tipo_pago" class="col-sm-3 control-label">Tipo de Pago:</label>
                                            <div class="col-sm-6">
                                                <select id="id_tipo_pago" name="id_tipo_pago" style="width:100% !important" class="selectSerch">
                                                    <option value="1">SPEI</option>
                                                    <option value="2">Cheque</option>
                                                    <option value="3">Efectivo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="txtCuentaPago" class="col-sm-3 control-label">Referencia de Pago:</label>
                                            <div class="col-sm-6">
                                                <input type="text" id="txtReferenciaPago" name="txtReferenciaPago" style="width:100% !important" maxlength="45" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
								<div class="modal-footer">									
									<i class="btn-danger btn" style="margin:10px 3px 10px 10px;" onclick="" data-dismiss="modal">Cancelar</i>
									<i class="btn-success btn" style="margin:10px 3px 10px 10px;" data-dismiss="modal" id="btnPagarDetalle" >Realizar Pago</i>
								</div>
                            </div>
                        </div>
                    </div>
                </div>';
    return $pagina;
}

function egresos_editar() {

    if ($_POST["datepicker"] == '')
	$_POST["datepicker"] = '01-01-1000';

    $_POST["datepicker"] = str_replace('/', "-", $_POST["datepicker"]);
    $fechaPago = normalize_date2($_POST["datepicker"]);
    $fechaPago = $fechaPago . ' 00:00:00';

    liberar_bd();
    $updateEgreso = " CALL sp_sistema_update_egreso(	"
	    . $_POST["idEgreso"] . ","
	    . $_POST["idCuenta"] . ",'"
	    . $_POST["montoEgr"] . "','"
	    . $fechaPago . "','"
	    . ($_POST["txtEgreso"]) . "',"
	    . $_SESSION[$varIdUser] . ");";
    $update = consulta($updateEgreso);

    if ($update) {
	/* $error='Se ha editado el egreso.';
	  $msj = sistema_mensaje("exito",$error); */
	$res = $msj . egresos_menuInicio();
    } else {
	$error = 'No se ha podido editar el egreso.';
	$msj = sistema_mensaje("error", $error);
//DATOS DEL EGRESO
	liberar_bd();
	$selectDatosEgreso = 'CALL sp_sistema_select_datos_egreso(' . $_POST["idEgreso"] . ');';
	$datosEgreso = consulta($selectDatosEgreso);
	$egr = siguiente_registro($datosEgreso);

//LISTA DE CUENTAS 
	liberar_bd();
	$selectListCuentas = 'CALL sp_sistema_lista_ctas_bancos();';
	$listaCuentas = consulta($selectListCuentas);
	while ($cue = siguiente_registro($listaCuentas)) {
	    $selectCta = '';
	    if ($cue["id"] == $egr["idCta"])
		$selectCta = 'selected="selected"';
	    $optCuentas .= '<option '
		    . $selectCta . ' value="'
		    . $cue["id"] . '">'
		    . utf8_encode($cue["nombre"])
		    . '(' . $cue["numero"] . ')</option>';
	}

	$pagina = '	<div id="page-heading">	
    ol class="breadcrumb">
    <li><a href="javascript:navegar_modulo(0);">Dashboad</a></li> 
    <li><a href="javascript:navegar_modulo(' . $_SESSION["mod"] . ');">'
		. $_SESSION["moduloPadreActual"] . '</a></li>    
    <li class="active">
        ' . $_SESSION["moduloHijoActual"] . '
    </li>
</ol>  
<h1>' . $_SESSION["moduloHijoActual"] . '</h1>
<div class="options">
    <div class="btn-toolbar">
        <input type="hidden" id="idEgreso" name="idEgreso" value="' . $_POST["idEgreso"] . '" />
    </div>
</div>										
</div>
<div class="container">							
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h4>Editar egreso</h4>
                </div>
                <div class="panel-body" style="border-radius: 0px;">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label for="idCuenta" class="col-sm-3 control-label">Cuenta:</label>
                            <div class="col-sm-6">
                                <select id="idCuenta" name="idCuenta" style="width:100% !important" 
				class="selectSerch">
                                    ' . $optCuentas . '
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="montoEgr" class="col-sm-3 control-label">Monto:</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="text" class="form-control" id="montoEgr" name="montoEgr" 
				    value="' . $_POST["montoEgr"] . '"/>
                                </div>
                            </div>																														
                        </div>
                        <div class="form-group">
                            <label for="datepicker" class="col-sm-3 control-label">Fecha de egreso:</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                    <input type="text" class="form-control" id="datepicker" 
				    name="datepicker" value="' . $_POST["datepicker"] . '"/>
                                </div>
                            </div>
                        </div>										
                        <div class="form-group">
                            <label for="txtEgreso" class="col-sm-3 control-label">Observaciones:</label>
                            <div class="col-md-6">	
                                <textarea class="form-control autosize" name="txtEgreso" id="txtEgreso">'
		. $egr["txtEgreso"] . '</textarea>				
                            </div>								
                        </div>									
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="btn-toolbar btnsGuarCan">
                                <i class="btn-default btn" onclick="navegar();">Cancelar</i>
                                <i class="btn-primary btn" onclick="nuevoEgreso(\'GuardarEdit\');">Guardar</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
	$res = $msj . $pagina;
    }
    return $res;
}

function egresos_eliminar() {
    $pagina = '	<div id="page-heading">	
    <ol class="breadcrumb">
        <li><a href="javascript:navegar_modulo(0);">Dashboad</a></li> 
        <li><a href="javascript:navegar_modulo(' . $_SESSION["mod"] . ');">'
	    . $_SESSION["moduloPadreActual"] . '</a></li>    
        <li class="active">
            ' . $_SESSION["moduloHijoActual"] . '
        </li>
    </ol>  
    <h1>' . $_SESSION["moduloHijoActual"] . '</h1>
    <div class="options">
        <div class="btn-toolbar">
            <input type="hidden" id="idEgreso" name="idEgreso" value="' . $_POST["idEgreso"] . '" />
        </div>
    </div>										
</div>
<div class="container">							
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h4></h4>
                </div>
                <div class="panel-body" style="border-radius: 0px;">
                    <div class="form-horizontal">											
                        <div class="form-group">
                            <label for="conceptoEgr" class="col-sm-3 control-label">Motivo:</label>
                            <div class="col-md-6">	
                                <textarea class="form-control autosize" name="motivoEgr" id="motivoEgr"></textarea>											
                            </div>							
                        </div>								
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="btn-toolbar btnsGuarCan">
                                <i class="btn-default btn" onclick="navegar();">Cancelar</i>
                                <i class="btn-success btn" 
				onclick="eliminarEgreso(\'GuardarEliminar\');">Guardar</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
    return $pagina;
}

function egresos_guardarEliminar() {
//DATOS DEL EGRESOS
    liberar_bd();
    $selectDatosEgresos = 'CALL sp_sistema_select_datos_egreso(' . $_POST["idEgreso"] . ');';
    $datosEgreso = consulta($selectDatosEgresos);
    $egresos = siguiente_registro($datosEgreso);

//DATOS DE LA CUENTA
    liberar_bd();
    $selectDatosCuenta = 'CALL sp_sistema_select_datos_cuentas(' . $egresos["idCta"] . ');';
    $datosCuenta = consulta($selectDatosCuenta);
    $cuenta = siguiente_registro($datosCuenta);

    $nvoSaldo = $cuenta["monto"] + $egresos["cantidad"];

//CANCELAMOS EL EGRESO
    liberar_bd();
    $cancelarEgreso = 'CALL sp_sistema_cancelar_egreso('
	    . $_POST["idEgreso"] . ', '
	    . $_SESSION[$varIdUser] . ');';
    $cancel = consulta($cancelarEgreso);

    if ($cancel) {
//GUARDAMOS NUEVO SALDO
	liberar_bd();
	$updateCuenta = 'CALL sp_sistema_update_saldo_cuenta('
		. $egresos["idCta"] . ', "'
		. $nvoSaldo . '", '
		. $_SESSION[$varIdUser] . ');';
	$update = consulta($updateCuenta);

//GUARDAMOS MOTIVO DE CANCELACION
	liberar_bd();
	$insertMotivoCancela = 'CALL sp_insert_motivo_cancela_egreso('
		. $_POST["idEgreso"] . ', "'
		. ($_POST["motivoEgr"]) . '", '
		. $_SESSION[$varIdUser] . ');';
	$insertMot = consulta($insertMotivoCancela);
    } else {
	$error = 'No se ha podido eliminar el egreso.';
	$msj = sistema_mensaje("error", $error);
    }

    return egresos_menuInicio() . $msj;
}

function egresos_cancelar() {
//ELIMINAR ASIGNACIONES DETALLE PROYECTO
    liberar_bd();
    $deleteDetalleEgresoProy = 'CALL sp_sistema_eliminar_detalle_egreso_proyecto('
	    . $_SESSION["idEgresoActual"] . ');';
    $elimiarDetEgrProy = consulta($deleteDetalleEgresoProy);

//ELIMINAR DETALLES DE EL EGRESO
    liberar_bd();
    $eliminarDetallesrEgreso = 'CALL sp_sistema_eliminar_detalles_egreso(' . $_SESSION["idEgresoActual"] . ');';
    $eliminarDetalles = consulta($eliminarDetallesrEgreso);

//ELIMINAR PROVEEDOR DE EGRESO
    liberar_bd();
    $eliminarProveedorEgreso = 'CALL sp_sistema_eliminar_proveedor_egreso('
	    . $_SESSION["idEgresoActual"] . ');';
    $eliminarProveedor = consulta($eliminarProveedorEgreso);

//ELIMINAR EL EGRESO
    liberar_bd();
    $eliminarEgreso = 'CALL sp_sistema_eliminar_egreso(' . $_SESSION["idEgresoActual"] . ');';
    $eliminaEgreso = consulta($eliminarEgreso);
    return egresos_menuInicio();
}

function money_string_format($str){
    $newstr = '';
    $len = strlen($str);
    if($len > 3)
        $newstr = substr_replace($str, ',', -3, 0);
        
    return $newstr;
}