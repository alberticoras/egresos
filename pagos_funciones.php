<?php
//nueva prueba
function egresos_menuInicio() 
{
    $hoy = date("Y-m-d");
	//CHECAMOS FILTROS
    $fechaFormt = date('Y-m-d');
    $primerDia = date('Y-m');
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
//        $selectTablaCuentas = 'call sp_get_payments_dates(\''.($fechaIni).'\', \''.($fechaFin).'\')';
        $selectTablaCuentas = 'call sp_get_pagos_documentos_proveedores_bancos_dates(\''.($fechaIni).'\', \''.($fechaFin).'\')';
        $tablaCuentas = consulta($selectTablaCuentas);
        $total_pendiente = 0;
        while ($cuenta = siguiente_registro($tablaCuentas))
        {
            switch($cuenta["tipo_pago"]){
             
                case '1':
                    $ref = 'cheque';
                    break;
                case '2':
                    $ref = 'transferencia';
                    break;
                case '3':
                    $ref = 'SPEI';
                    break;
                default:
                    $ref = 'N/A';
                    break;
            }
            
            switch($cuenta["tipo_documento_proveedor"]){
                
                case '1':
                    $doc = 'factura';
                    break;
                case '0':
                    $doc = 'remision';
                    break;
                case '3':
                    $doc = '';
                    break;
                default:
                    $doc = '';
                    break;
                
            }
            
                $tabla .= '<tr><td>'.$cuenta["fecha_pago"].'</td><td>$ '.$cuenta["monto_pago"].'</td><td>'.$cuenta["banco_ctas_banco"].'</td><td>'.$cuenta["numero_ctas_banco"].'</td><td>'.$ref.'</td><td>'.$cuenta["referencia"].'</td><td>'.$cuenta["nombre_proveedor"].'</td><td>'.$cuenta["observaciones"].'</td><td>'.$doc.'</td><td>'.$cuenta["folio"].'</td></tr>';
            
        }
    } 
	else 
	{
		$fechaIni = $primerDia . '-01 00:00:00';
		$fechaFin = $fechaFormt . ' 23:59:59';
		$fechaIniCampo = normalize_date($fechaIni);
		$fechaFinCampo = normalize_date($fechaFin);
		$_POST["filterDate2"] = $fechaIniCampo . " - " . $fechaFinCampo;
        
        //OBTENER LA TABLA DE CUENTAS
        liberar_bd();
        $selectTablaCuentas = 'call sp_get_pagos_documentos_proveedores_bancos()';
        $tablaCuentas = consulta($selectTablaCuentas);
        $total_pendiente = 0;
        while ($cuenta = siguiente_registro($tablaCuentas))
        {
            
            switch($cuenta["tipo_pago"]){
             
                case '1':
                    $ref = 'cheque';
                    break;
                case '2':
                    $ref = 'transferencia';
                    break;
                case '3':
                    $ref = 'SPEI';
                    break;
                default:
                    $ref = 'N/A';
                    break;
            }
            
            switch($cuenta["tipo_documento_proveedor"]){
                
                case '1':
                    $doc = 'factura';
                    break;
                case '0':
                    $doc = 'remision';
                    break;
                case '3':
                    $doc = '';
                    break;
                default:
                    $doc = '';
                    break;
                
            }
            
                $tabla .= '<tr><td>'.$cuenta["fecha_pago"].'</td><td>$ '.$cuenta["monto_pago"].'</td><td>'.$cuenta["banco_ctas_banco"].'</td><td>'.$cuenta["numero_ctas_banco"].'</td><td>'.$ref.'</td><td>'.$cuenta["referencia"].'</td><td>'.$cuenta["nombre_proveedor"].'</td><td>'.$cuenta["observaciones"].'</td><td>'.$doc.'</td><td>'.$cuenta["folio"].'</td><td class="thAcciones" style="text-align:left;"><a class="btn btn-default-alt btn-sm" onClick="document.frmSistema.idPago.value = ' .$cuenta["id_pagos"]. '; navegar(\'Ver detalles\');"><i title="Ver detalles" class="fa fa-eye"></i></a></td></tr>';
            
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
							<input type="hidden" id="idPago" name="idPago" value="" />
							<input type="hidden" name="txtIndice" />';
							
							$pagina.= '	<i title="Nuevo Pago" style="cursor:pointer;" onclick="navegar(\'Nuevo\')" class="btn btn-warning" >
											Nuevo Pago
										</i>';
							
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
                                        <button type="button" class="btn btn-default" onclick="navegar();">Buscar</button>
                                        <button type="button" class="btn btn-default" id="btnResetFilter" onclick="">Resetear Filtro</button>
                                    </div>
                                </div>
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
								            <li class="active"><a href="javascript:;">Todos los Pagos</a></li>
                                            <li id="nav_planned_payments"><a href="javascript:;">Pagos Proyectados</a></li>
										</ul>
                                    </h4>
									<div class="options">   
										<a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
									</div>
								</div>
								<div class="panel-body collapse in">
									<div class="table-responsive">
										<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered tablesorter" id="example">
											<thead id="table_egresos_head">
												<tr>
                                                    <th>FECHA</th>
                                                    <th>MONTO</th>
                                                    <th>BANCO</th>
                                                    <th>CUENTA</th>
                                                    <th>DOC. DE EGRESO</th>
                                                    <th>REFERENCIA</th>
                                                    <th>PROVEEDOR</th>
                                                    <th>CONCEPTO DEL DOC.</th>
                                                    <th>TIPO DE DOC</th>
                                                    <th>FOLIO</th>
                                                    <th style="text-align:center;">ACCIONES</th>
                                                </tr>
											</thead>	
											<tbody id="table_egresos_body">';
											
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
                                    <span id="to_pay_doc"></span>
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
                                            <label for="txtSucursalPago" class="col-sm-3 control-label">Sucursal Emisora:</label>
                                            <div class="col-sm-6">
                                                <select id="txtSucursalPago" name="txtSucursalPago" style="width:100% !important" class="selectSerch">
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
                                    </div>																					
								</div>
								<div class="modal-footer">									
									<i class="btn-danger btn" onclick="" data-dismiss="modal">Cerrar</i>
                                    <i class="btn-success btn" style="margin:10px 3px 10px 10px;" data-dismiss="modal" id="btnRegistrarPagoForDoc" >Realizar Pago</i>
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
/*
    liberar_bd();
    $selectListProveedores = 'CALL sp_get_documento_proveedor(10);';
    $listaProveedores = consulta($selectListProveedores);
    while ($prov = siguiente_registro($listaProveedores)) 
	{
        if($prov["estatus"] != "4" && $prov["estatus"] != "0" && $prov["estatus"] != "3")
		  $optProveedores .= '<option value="' . $prov["id_documentos_proveedor"] . '">#'. $prov["folio"] . ' - '.$prov["fecha_pago_documento"].' ($'.$prov["monto_documento"].')</option>';
    }
*/

	liberar_bd();
    $selectListProveedores = 'CALL sp_sistema_lista_proveedores();';
    $listaProveedores = consulta($selectListProveedores);
    while ($prov = siguiente_registro($listaProveedores)) 
	{
		  $optProveedores .= '<option value="' . $prov["id"] . '">'.$prov["nombre"].'</option>';
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
    
    $optClientes .= '<option value="1">Cheque</option><option value="2">Transferencia</option><option value="2">SPEI</option>';

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
					<h1>' . substr ($_SESSION["moduloHijoActual"], 0, strlen($_SESSION["moduloHijoActual"])-1) . 'o pago</h1>
					<div class="options">
						<div class="btn-toolbar">
							<input type="hidden" id="idPagoActual" name="idPagoActual" value="0" readonly="readonly"/>
							<input type="hidden" id="sumDetalles" name="sumDetalles" value="0" readonly="readonly"/>
							<input type="hidden" id="idTipoEntrega" name="idTipoEntrega" value="1" readonly="readonly"/>
						</div>
					</div>
				</div>
                
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-danger">
								<div class="panel-body collapse in" id="divDatosEgreso" style="border-top:1px solid #d2d3d6;">
									<h3><span class="label label-ribbon" style="margin-left:-31px;">Datos del Pago</span></h3>
									
                                    <div class="row" style="text-align:right">
                                        <div class="col-sm-3 control-label" style="float:right;margin-bottom:20px;">
                                            <div class="col-sm-6">
                                                <label for="idProveedor" class="col-sm-3 control-label">Pago Proyectado:</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="wrapper">
                                                  <input type="checkbox" name="toggle" id="toggle">
                                                  <label for="toggle"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom:20px;">
                                    	<div class="col-md-6">
											<div class="form-group">
												<label for="idProveedor" class="col-sm-3 control-label">Proveedor:</label>
												<div class="col-sm-6">
													<select style="width:100% !important" class="selectSerch" name="id_select_proveedor" id="id_select_proveedor">
														' . $optProveedores . '
													</select>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="idProveedor" class="col-sm-3 control-label">Cuenta por pagar:</label>
												<div class="col-sm-6">
													<select style="width:100% !important" class="selectSerch" name="idCuenta" id="idCuentaXPagar">
													</select>
												</div>
											</div>
										</div>
                                    </div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label class="col-sm-3 control-label">Referencia de pago:</label>
												<div class="col-sm-6">
													<input type="text" class="form-control" value="" maxlength="100" name="txtReferencia" id="txtReferencia" autocomplete="off" />
												</div>
											</div>
										</div>
										 <div class="col-md-6">
											<div class="form-group">
												<label for="idCliente" class="col-sm-3 control-label">Documento de Pago:</label>
												<div id="proyectoCabezal" class="col-sm-6">
													<select style="width:100% !important" class="selectSerch" name="idDocPago" id="idDocPago">
														<option value="0">Seleccione un tipo</option>
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
														<input readonly="readonly" type="text" class="form-control" id="datepicker" name="newFechaDoc" value="' . $hoy . '"/>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="folioCliente" class="col-sm-3 control-label">Observaciones:</label>
												<div class="col-sm-6">
													<textarea type="text" class="form-control" name="obsPago" id="obsPago"></textarea>
												</div>
											</div>
										</div>
									</div><br/>
									<div class="row" id="lastRowHeader">
										<div class="col-md-6">
											<div class="form-group">
												<label class="col-sm-3 control-label">Monto del pago:</label>
												<div class="col-sm-6">
													<input type="text" class="form-control" value="0.00" maxlength="100" name="txtMontoPagoNew" id="txtMontoPagoNew" autocomplete="off" />
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="col-sm-3 control-label">Cuenta de Pago:</label>
												<div class="col-sm-6">
													<select style="width:100% !important" class="selectSerch" name="idCuentaPago" id="idCuentaPago">
														'.$optCuentas.'
                                                    </select>
												</div>
											</div>
										</div>
									</div>
                                    <div class="panel-body collapse in" id="pnl-body-details-added" style="border-top:1px solid #d2d3d6;margin-top:20px">
                                        <table id="tblAdded" class="table table-striped table-bordered" style="overflow:scroll;" cellpadding="0" cellspacing="0" border="0">
                                            <thead>
                                                <th>No. Documento</th>
                                                <th>Proveedor</th>
                                                <th>Estatus</th>
                                                <th>Concepto</th>
                                                <th>Monto</th>
                                                <th>Fecha</th>
                                                <th>Saldo</th>
                                                <th>Pago Asignado</th>
                                            </thead>
                                            <tbody id="tblAddedBodyForDocs">
                                            </tbody>
                                        </table>
                                    </div>
									<div class="row" style="margin-top:10px;">
										<div class="col-md-4" style="float:right;display:none;">
											<div class="form-group">
												<label class="col-sm-3 control-label">SALDO RESTANTE TOTAL:</label>
												<div class="col-sm-6">
													<input type="text" class="form-control" maxlength="18" name="total" id="pay_total" value="0.00" autocomplete="off"/>
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
												<button class="btn btn-save" href="" data-toggle="modal" type="button" id="btnPayMultiple">
                                                    <i>Realizar Pago</i>
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
                                            </div>
                                            <button class="btn btn-info" id="btn_settle"  style="margin:0 auto;" type="button">
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
                                            <label for="txtSucursalPago" class="col-sm-3 control-label">Sucursal Emisora:</label>
                                            <div class="col-sm-6">
                                                <select id="txtSucursalPago" name="txtSucursalPago" style="width:100% !important" class="selectSerch">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" id="frmCuenta">
                                            <label for="txtCuentaPago" class="col-sm-3 control-label">Cuenta de Pago:</label>
                                            <div class="col-sm-6">
                                                <select id="txtCuentaPago" name="txtCuentaPago" style="width:100% !important" class="selectSerch">
                                                </select>
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
				<div id="div_articulos"></div>';

    return $pagina;
}

function selectLista($consulta, $columna) {
    liberar_bd();
    $selectLista = 'CALL sp_' . $consulta;
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
    $datosEgreso = 'get_pagos(\'10\',\'0\');';
    $proveedorEgreso = 'proveedor_egreso(' . $id . ');';
    $detallesidPago = 'detalles_idPago(' . $id . ');';
    $columnas = array("cantidad", "producto", "tipo", "proyecto", "subtotal", "iva", "total");

    $tablaDetalles = selectTable($detallesidPago, $columnas);

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
	    <input type = "hidden" id = "idPagoActual" name = "idPagoActual" 
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
		    <h3><span class = "label label-danger">Datos del Pago</span></h3>
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
				    $' . $total . '
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
                                                <th>TIPO DE EGRESO</th>
                                                <th>PROYECTO</th>
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
    $updateEgresoPagoCta = 'CALL sp_sistema_update_egreso_pago_cta(	'. $_SESSION["idPagoActual"] . ',
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
			. $_SESSION["idPagoActual"]
			. ', 1, '
			. $_SESSION[$varIdUser] . ');';
		$updateEDE = consulta($updateEstatusDetallesEgreso);

		//ACTUALIZAMOS ESTATUS DE EGRESO
		liberar_bd();
		$updateEstatusEgreso = 'CALl sp_sistema_update_estatus_egreso('. $_SESSION["idPagoActual"]. ', 1, '. $_SESSION[$varIdUser] . ');';
		$updateE = consulta($updateEstatusEgreso);
		
		//CREAMOS EL RECIBO
		//DATOS DE LA EMPRESA
		liberar_bd();
		$selecDatosEmpresa = "CALL sp_sistema_select_datos_empresa();";							  
		$datosEmpresa = consulta($selecDatosEmpresa);	
		$empresa = siguiente_registro($datosEmpresa);
		
		//DATOS DEL EGRESO
		liberar_bd();
		$selectDatosEgreso = 'CALL sp_sistema_select_datos_egreso('.$_SESSION["idPagoActual"].');';
		$datosEgresos = consulta($selectDatosEgreso);
		$dateEgr = siguiente_registro($datosEgresos);
		
		$subtotalEgr = $dateEgr["subtotal"]; 
		$ivaEgr = $dateEgr["iva"];	
		$totalEgr = $dateEgr["total"];	
		
		//CHECAMOS SI SE ASIGNO PROVEEDOR
		liberar_bd();
		$selectProveedorEgreso = 'CALL sp_sistema_select_proveedor_egreso('.$_SESSION["idPagoActual"].');';
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
		$selectDetallesEgreso = 'CALL sp_sistema_select_detalles_idPago('.$_SESSION["idPagoActual"].');';
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
		$updateUrlEgreso = 'CALL sp_sistema_update_url_egreso('.$_SESSION["idPagoActual"].', "'.$src.'");';
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
    $selectDatosEgreso = 'CALL sp_sistema_select_datos_egreso(' . $_POST["idPago"] . ');';
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
    $consulta = 'call sp_' . $consulta;
    $registros = consulta($consulta);
    foreach ($registros as $reg) {
	$label.= '<label id="' . $reg["id"] . '">' . utf8_encode($reg[$columna]) . '</label>';
    }
    return $label;
}

function selectTable($consulta, $columnas) {
    liberar_bd();
    $consulta = 'call ' . $consulta;
    $registros = consulta($consulta);
    foreach ($registros as $reg) {
	$tabla.='<tr  id="det' . $reg["id"] . '">';
	foreach ($columnas as $columna) {
	    $tabla.= '<td>' . utf8_encode($reg[$columna]) . '</td>';
	}
	$tabla.='<td>
								<a class="btn btn-default-alt btn-sm" onClick="if(confirm(\'Desea eliminar este detalle\')){eliminar_detalle_egreso(' . $reg["id"] . ');}">
									<i class="fa fa-trash-o" title="Eliminar"></i>
								</a>
							</td>';

	$tabla.='</tr>';
    }
    return $tabla;
}

function egresos_detalles($id) {
    $datosEgreso = 'get_pagos_id(' . $id . ');';
    $proveedorEgreso = 'proveedor_egreso(' . $id . ');';
    $detallesidPago = 'sp_get_pagos_for_docs(' . $id . ');';
    $columnas = array("fecha_pago_documento", "estatus doc", "monto_pago", "concepto_documento", "folio", "saldo_pendiente", "monto_documento");

//SELECT PROVEEDOR
    $optProveedores = selectLabel($proveedorEgreso, "proveedor");

//SELECT DATOS EGRESO
    $fechaDoc = selectLabel($datosEgreso, "fecha_pago");

//SELECT CONCEPTO
    $concepto = selectLabel($datosEgreso, "banco_ctas_banco");
    
//SELECT STATUS
    $statusPay = selectLabel($datosEgreso, "estatus");

//SELECT OBSERVACION
    $observacion = selectLabel($datosEgreso, "numero_ctas_banco");

//SELECT TOTAL
    $total = selectLabel($datosEgreso, "monto_pago");

//SELECT SUBTOTAL
    $subtotal = selectLabel($datosEgreso, "subtotal");

//SELECT IVA
    $iva = selectLabel($datosEgreso, "iva");
    
//SELECT NO DE PAGO
    $folioDoc = selectLabel($datosEgreso, "id_pagos");

    $tablaDetalles = selectTable($detallesidPago, $columnas);

    $_SESSION["idProyectoActual"] = '';
    $_SESSION["idProveedorActual"] = '';
    
    
    switch(trim($statusPay)){
     
        case '<label id="">0</label>':
            $span = '<span style="font-size:14px; font-weight:bold;" class="banner_rojo">CAPTURADO';
            $class = 'magenta';
            break;
        case '<label id="">3</label>':
            $span = '<span style="font-size:14px; font-weight:bold;" class="banner_azul">PROGRAMADO';
            $class = 'azul';
            break;
        case '<label id="">1</label>':
            $span = '<span style="font-size:14px; font-weight:bold;" class="banner_verde">ACTIVO';
            $class = 'verde';
            break;
        case '<label id="">5</label>':
            $span = '<span style="font-size:14px; font-weight:bold;" class="banner_amarillo">ABONADO';
            $class = 'amarillo';
            break;
        default:
            $span = '<span style="font-size:14px; font-weight:bold;" class="banner_magenta">full';
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
	    <input type = "hidden" id = "idPagoActual" name = "idPagoActual" 
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
		    <h3><span class = "label label-ribbon" style="margin-left:-31px;box-shadow:2px 2px 5px #bfbfbf;">Datos generales</span></h3>
            <h4 class="upper-ribbon-'.$class.'">'.$span.'</h4>
		    <div class = "row">
			<div class = "col-md-6">
			    
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
				No. de Pago:</label>
				<div class = "col-sm-6">
				    ' . $folioDoc . '
				</div>
			    </div>
			</div>
		    </div>
		    <div class = "row">
			<div class = "col-md-6">
			    <div class = "form-group">
				<label class = "col-sm-3 control-label">Banco Emisor:</label>
				<div class = "col-sm-6">
				    ' . $concepto . '
				</div>
			    </div>
			</div>
			<div class = "col-md-6">
			    <div class = "form-group">
				<label class = "col-sm-3 control-label">No. de Cuenta:</label>
				<div class = "col-sm-6">
				    ' . $observacion . '
				</div>
			    </div>
			</div>
		    </div>
		    <h3><span class="label label-ribbon" style="margin-left:-31px;box-shadow:2px 2px 5px #bfbfbf;">Totales</span></h3>
		    <div class = "row">
			<div class = "col-md-4">
			    <div class = "form-group">
				<label class = "col-sm-3 control-label">TOTAL:</label>
				<div class = "col-sm-6">
				    $ ' . $total . '
				</div>
			    </div>
			</div>
		    </div>
		    <h3></h3>
		    <hr style = "margin-top:0; margin-bottom:10px;">
		    <div class = "row">
			<div class = "col-sm-12">
			    <div class = "btn-toolbar btnsGuarCan">
				<i class = "btn-danger btn" style="background:#aab2bd" onclick = "navegar();">Regresar</i>
			    </div>
			</div>
		    </div>
		</div>
	    </div>
	</div>
    </div>';
    $pagina.='
    <div class = "row" id = "divCapturaDetalles">
        <div class = "col-md-12">

            <div class = "panel-body collapse in" style="background:#ffffff;margin: 0 20px 20px 20px;">                        

                <div class="panel-heading">
                    <h4>DETALLES</h4>
                    <div class="options">
                        <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                    </div>
                </div>
                <div class = "panel-body collapse in">                        
                    <div class = "row">
                    <div class = "col-md-12">
                        <div id = "divDetallesEgreso" style = "height:357px;">
                        <div class = "table-responsive">
                            <table class = "table table-bordered table-striped" id = "js-tabla">
                            <thead>
                                <tr>
                                <th>FECHA DEL DOCUMENTO</th>
                                <th>ESTATUS</th>
                                <th>MONTO DEL PAGO</th>
                                <th>CONCEPTO</th>
                                <th>FOLIO</th>
                                <th>SALDO RESTANTE</th>
                                <th>TOTAL DEL DOCUMENTO</th>
                                </tr>
                            </thead>
                            <tbody>
                                ' . $tablaDetalles . '
                            </tbody>
                            </table>
                        </div>
                        <div id = "detallesProducto"></div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
';
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
	    . $_POST["idPago"] . ","
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
	$selectDatosEgreso = 'CALL sp_sistema_select_datos_egreso(' . $_POST["idPago"] . ');';
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
        <input type="hidden" id="idPago" name="idPago" value="' . $_POST["idPago"] . '" />
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
            <input type="hidden" id="idPago" name="idPago" value="' . $_POST["idPago"] . '" />
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
    $selectDatosEgresos = 'CALL sp_sistema_select_datos_egreso(' . $_POST["idPago"] . ');';
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
	    . $_POST["idPago"] . ', '
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
		. $_POST["idPago"] . ', "'
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
	    . $_SESSION["idPagoActual"] . ');';
    $elimiarDetEgrProy = consulta($deleteDetalleEgresoProy);

//ELIMINAR DETALLES DE EL EGRESO
    liberar_bd();
    $eliminarDetallesrEgreso = 'CALL sp_sistema_eliminar_detalles_egreso(' . $_SESSION["idPagoActual"] . ');';
    $eliminarDetalles = consulta($eliminarDetallesrEgreso);

//ELIMINAR PROVEEDOR DE EGRESO
    liberar_bd();
    $eliminarProveedorEgreso = 'CALL sp_sistema_eliminar_proveedor_egreso('
	    . $_SESSION["idPagoActual"] . ');';
    $eliminarProveedor = consulta($eliminarProveedorEgreso);

//ELIMINAR EL EGRESO
    liberar_bd();
    $eliminarEgreso = 'CALL sp_sistema_eliminar_egreso(' . $_SESSION["idPagoActual"] . ');';
    $eliminaEgreso = consulta($eliminarEgreso);
    return egresos_menuInicio();
}
