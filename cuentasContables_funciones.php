<?php

	function cuentas_menuInicio()
	{
		$btnEdita = false;
		$btnAlta = false;
		$btnElimina = false;
		$btnAgregar = false;
		
		//PREMISOS DE ACCIONES
		liberar_bd();
		$selectPermisosAcciones = 'CALL sp_sistema_select_permisos_acciones_modulo('.$_SESSION["idPerfil"].', '.$_SESSION["mod"].');';
		$permisosAcciones = consulta($selectPermisosAcciones);
		while($acciones = siguiente_registro($permisosAcciones))
		{
			switch(utf8_encode($acciones["accion"]))
			{
				case 'Alta':
					$btnAlta = true;					
				break;
				case 'Agregar subtipo':
					$btnAgregar = true;					
				break;
				case 'Modificación':
					$btnEdita = true;					
				break;
				case 'Eliminación':
					$btnElimina = true;
				break;
				
			}
		}
		
		//CUENTAS DE ACTIVO
		liberar_bd();
		$selectCuentasContablesActivo = "	SELECT
												ctaCont.id_ctas_contable AS id,
												ctaCont.nivel_ctas_contable AS nivel,
												ctaCont.padre_ctas_contable AS padre,
												ctaCont.nombre_ctas_contable AS nombre,
												ctaCont.estatus_ctas_contable AS estatus,
												ctaCont.codigoMayor_ctas_contable AS codigo,
												ctaCont.tipo_ctas_contable AS tipo
											FROM
												ctas_contable AS ctaCont
											WHERE
												ctaCont.estatus_ctas_contable = 1
											AND ctaCont.clasificacion_ctas_contable = 1
											AND ctaCont.nivel_ctas_contable = 0
											ORDER BY
												tipo";
							  
		$cuentasContablesActivo = consulta($selectCuentasContablesActivo);
		while($ctasContActi =  siguiente_registro($cuentasContablesActivo))
		{
			$listaCtasAct = '	<li class="dd-item dd3-item" data-id="'.$ctasContActi["id"].'">
									<div class="dd-handle dd3-handle fa fa-th"></div>
									<div class="dd3-content">
										('.$ctasContActi["codigo"].')'.utf8_encode($ctasContActi["nombre"]).'
										<div class="btn-group btnsCuentas">	';
										
										if($btnAgregar)
											$listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(1,'.$ctasContActi["id"].');" style="cursor:pointer;"></i>';		
										if($btnEdita)
											$listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$ctasContActi["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
										if($btnElimina)
											$listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$ctasContActi["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
											
					$listaCtasAct.= '	</div>									
									</div>'; 
								
			//CHECAMOS SI TIENE HIJOS DE NIVEL 1
			liberar_bd();
			$selectHijosNivel1 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$ctasContActi["id"].',1);';
			$hijosNivel1 = consulta($selectHijosNivel1);
			$ctaHijosNivel1 = cuenta_registros($hijosNivel1);
			if($ctaHijosNivel1 != 0)
			{
				$listaCtasAct .='<ol class="dd-list">';
				while($nivel1 = siguiente_registro($hijosNivel1))
				{
					$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel1["id"].'">
										  <div class="dd-handle dd3-handle fa fa-th"></div>
											  <div class="dd3-content">
												  ('.$nivel1["codigo"].')'.utf8_encode($nivel1["nombre"]).'
												  <div class="btn-group btnsCuentas">	';
												  
												  if($btnAgregar)
													  $listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(1,'.$nivel1["id"].');" style="cursor:pointer;"></i>';		
												  if($btnEdita)
													  $listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel1["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
												  if($btnElimina)
													  $listaCtasAct.= '	 <i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel1["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
													  
							  $listaCtasAct.= '		</div>																					  
												</div>';
										
						//CHECAMOS SI TIENE HIJOS DE NIVEL 2
						liberar_bd();
						$selectHijosNivel2 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel1["id"].',2);';
						$hijosNivel2 = consulta($selectHijosNivel2);
						$ctaHijosNivel2 = cuenta_registros($hijosNivel2);
						if($ctaHijosNivel2 != 0)
						{
							$listaCtasAct .='<ol class="dd-list">';
							while($nivel2 = siguiente_registro($hijosNivel2))
							{
								$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel2["id"].'">
													  <div class="dd-handle dd3-handle fa fa-th"></div>
													  <div class="dd3-content">
														  ('.$nivel2["codigo"].')'.utf8_encode($nivel2["nombre"]).'
														   <div class="btn-group btnsCuentas">	';
															  if($btnAgregar)
																  $listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(1,'.$nivel2["id"].');" style="cursor:pointer;"></i>';		
															  if($btnEdita)
																  $listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel2["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
															  if($btnElimina)
																  $listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel2["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
									$listaCtasAct.= '	   </div>													  
													  </div>';
												  
									//CHECAMOS SI TIENE HIJOS DE NIVEL 3
									liberar_bd();
									$selectHijosNivel3 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel2["id"].',3);';
									$hijosNivel3 = consulta($selectHijosNivel3);
									$ctaHijosNivel3 = cuenta_registros($hijosNivel3);
									if($ctaHijosNivel3 != 0)
									{
										$listaCtasAct .='<ol class="dd-list">';
										while($nivel3 = siguiente_registro($hijosNivel3))
										{
											$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel3["id"].'">
																  <div class="dd-handle dd3-handle fa fa-th"></div>
																  <div class="dd3-content">
																	  ('.$nivel3["codigo"].')'.utf8_encode($nivel3["nombre"]).'
																	   <div class="btn-group btnsCuentas">	';
																		  if($btnAgregar)
																			  $listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(1,'.$nivel3["id"].');" style="cursor:pointer;"></i>';		
																		  if($btnEdita)
																			  $listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel3["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																		  if($btnElimina)
																			  $listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel3["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
												$listaCtasAct.= '		</div>																  
																  </div>';
																  
												//CHECAMOS SI TIENE HIJOS DE NIVEL 4
												liberar_bd();
												$selectHijosNivel4 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel3["id"].',4);';
												$hijosNivel4 = consulta($selectHijosNivel4);
												$ctaHijosNivel4 = cuenta_registros($hijosNivel4);
												if($ctaHijosNivel4 != 0)
												{
													$listaCtasAct .='<ol class="dd-list">';
													while($nivel4 = siguiente_registro($hijosNivel4))
													{
														$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel4["id"].'">
																			  <div class="dd-handle dd3-handle fa fa-th"></div>
																			  <div class="dd3-content">
																				  ('.$nivel4["codigo"].')'.utf8_encode($nivel4["nombre"]).'
																				   <div class="btn-group btnsCuentas">	';
																					  if($btnAgregar)
																						  $listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(1,'.$nivel4["id"].');" style="cursor:pointer;"></i>';		
																					  if($btnEdita)
																						  $listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel4["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																					  if($btnElimina)
																						  $listaCtasAct.= '	 <i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel4["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																  $listaCtasAct.= '	</div>																			  
																			  </div>';
																			  
															//CHECAMOS SI TIENE HIJOS DE NIVEL 5
															liberar_bd();
															$selectHijosNivel5 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel4["id"].',5);';
															$hijosNivel5 = consulta($selectHijosNivel5);
															$ctaHijosNivel5 = cuenta_registros($hijosNivel5);
															if($ctaHijosNivel5 != 0)
															{
																$listaCtasAct .='<ol class="dd-list">';
																while($nivel5 = siguiente_registro($hijosNivel5))
																{
																	$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel5["id"].'">
																						  <div class="dd-handle dd3-handle fa fa-th"></div>
																						  <div class="dd3-content">
																							  ('.$nivel5["codigo"].')'.utf8_encode($nivel5["nombre"]).'
																							   <div class="btn-group btnsCuentas">	';
																									if($btnAgregar)
																										$listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(1,'.$nivel5["id"].');" style="cursor:pointer;"></i>';		
																									if($btnEdita)
																										$listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel5["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																									if($btnElimina)
																										$listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel5["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																			$listaCtasAct.= '	</div>	
																						  </div>';
																					  
																	//CHECAMOS SI TIENE HIJOS DE NIVEL 6
																	liberar_bd();
																	$selectHijosNivel6 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel5["id"].',6);';
																	$hijosNivel6 = consulta($selectHijosNivel6);
																	$ctaHijosNivel6 = cuenta_registros($hijosNivel6);
																	if($ctaHijosNivel6 != 0)
																	{
																		$listaCtasAct .='<ol class="dd-list">';
																		while($nivel6 = siguiente_registro($hijosNivel6))
																		{
																			$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel6["id"].'">
																								  <div class="dd-handle dd3-handle fa fa-th"></div>
																								  <div class="dd3-content">
																									  ('.$nivel6["codigo"].')'.utf8_encode($nivel6["nombre"]).'
																									   <div class="btn-group btnsCuentas">	';
																											if($btnAgregar)
																												$listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(1,'.$nivel6["id"].');" style="cursor:pointer;"></i>';		
																											if($btnEdita)
																												$listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel6["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																											if($btnElimina)
																												$listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel6["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																					$listaCtasAct.= '	</div>	
																								  </div>';
																			//CHECAMOS SI TIENE HIJOS DE NIVEL 7
																			liberar_bd();
																			$selectHijosNivel7 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel6["id"].',7);';
																			$hijosNivel7 = consulta($selectHijosNivel7);
																			$ctaHijosNivel7 = cuenta_registros($hijosNivel7);
																			if($ctaHijosNivel7 != 0)
																			{
																				$listaCtasAct .='<ol class="dd-list">';
																				while($nivel7 = siguiente_registro($hijosNivel7))
																				{
																					$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel7["id"].'">
																									  <div class="dd-handle dd3-handle fa fa-th"></div>
																									  <div class="dd3-content">
																									  ('.$nivel7["codigo"].')'.utf8_encode($nivel7["nombre"]).'
																									   <div class="btn-group btnsCuentas">	';
																											if($btnAgregar)
																												$listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(1,'.$nivel7["id"].');" style="cursor:pointer;"></i>';		
																											if($btnEdita)
																												$listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel7["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																											if($btnElimina)
																												$listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel7["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																					  $listaCtasAct.= '	</div>
																									 </div>';
																									 
																					//CHECAMOS SI TIENE HIJOS DE NIVEL 8
																					liberar_bd();
																					$selectHijosNivel8 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel7["id"].',8);';
																					$hijosNivel8 = consulta($selectHijosNivel8);
																					$ctaHijosNivel8 = cuenta_registros($hijosNivel8);
																					if($ctaHijosNivel8 != 0)
																					{
																						$listaCtasAct .='<ol class="dd-list">';
																						while($nivel8 = siguiente_registro($hijosNivel8))
																						{
																							$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel8["id"].'">
																												  <div class="dd-handle dd3-handle fa fa-th"></div>
																												  <div class="dd3-content">
																													  ('.$nivel8["codigo"].')'.utf8_encode($nivel8["nombre"]).'
																													   <div class="btn-group btnsCuentas">	';
																															if($btnAgregar)
																																$listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(1,'.$nivel8["id"].');" style="cursor:pointer;"></i>';		
																															if($btnEdita)
																																$listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel8["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																															if($btnElimina)
																																$listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel8["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																									$listaCtasAct.= '	</div>	
																											  		</div>';
																													
																							//CHECAMOS SI TIENE HIJOS DE NIVEL 9
																							liberar_bd();
																							$selectHijosNivel9 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel8["id"].',9);';
																							$hijosNivel9 = consulta($selectHijosNivel9);
																							$ctaHijosNivel9 = cuenta_registros($hijosNivel9);
																							if($ctaHijosNivel9 != 0)
																							{
																								$listaCtasAct .='<ol class="dd-list">';
																								while($nivel9 = siguiente_registro($hijosNivel9))
																								{
																									$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel9["id"].'">
																														  <div class="dd-handle dd3-handle fa fa-th"></div>
																														  <div class="dd3-content">
																															  ('.$nivel9["codigo"].')'.utf8_encode($nivel9["nombre"]).'
																															   <div class="btn-group btnsCuentas">	';
																																   if($btnEdita)
																																		$listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel9["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																																	if($btnElimina)
																																		$listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel9["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																											$listaCtasAct.= '	</div>																															
																													  		</div>';																						
																									$listaCtasAct .= '</li>';
																								}
																								$listaCtasAct .='</ol>';				
																							}
																							$listaCtasAct .= '</li>';
																						}
																						$listaCtasAct .='</ol>';				
																					}
																					$listaCtasAct .= '</li>';
																				}
																				$listaCtasAct .='</ol>';				
																			}
																			$listaCtasAct .= '</li>';
																		}
																		$listaCtasAct .='</ol>';				
																	}
																	$listaCtasAct .= '</li>';
																}
																$listaCtasAct .='</ol>';				
															}
														$listaCtasAct .= '</li>';
													}
													$listaCtasAct .='</ol>';				
												}
											$listaCtasAct .= '</li>';
										}
										$listaCtasAct .='</ol>';				
									}
								$listaCtasAct .= '</li>';
							}
							$listaCtasAct .='</ol>';				
						}			
					$listaCtasAct .= '</li>';
				}
				$listaCtasAct .='</ol>';				
			}			
			$listaCtasAct .= '</li>';	
			
			switch($ctasContActi["tipo"])
			{
				case 1:
					$listaContActCirc.= $listaCtasAct; 	
				break;
				case 2:
					$listaContActFij.= $listaCtasAct; 	
				break;
				case 3:
					$listaContActDif.= $listaCtasAct; 	
				break;
			}
		}
		
		//CUENTAS DE PASIVO
		liberar_bd();
		$selectCuentasContablesPasivo = "	SELECT
												ctaCont.id_ctas_contable AS id,
												ctaCont.nivel_ctas_contable AS nivel,
												ctaCont.padre_ctas_contable AS padre,
												ctaCont.nombre_ctas_contable AS nombre,
												ctaCont.estatus_ctas_contable AS estatus,
												ctaCont.tipo_ctas_contable AS tipo
											FROM
												ctas_contable AS ctaCont
											WHERE
												ctaCont.estatus_ctas_contable = 1
											AND ctaCont.clasificacion_ctas_contable = 2
											AND ctaCont.nivel_ctas_contable = 0";
							  
		$cuentasContablesPasivo = consulta($selectCuentasContablesPasivo);
		while($ctasContPas =  siguiente_registro($cuentasContablesPasivo))
		{
			$listaCtasPas = '	<li class="dd-item dd3-item" data-id="'.$ctasContPas["id"].'">
									<div class="dd-handle dd3-handle fa fa-th"></div>
									<div class="dd3-content">
										'.utf8_encode($ctasContPas["nombre"]).'
										<div class="btn-group btnsCuentas">	';
										
										if($btnAgregar)
											$listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(2,'.$ctasContPas["id"].');" style="cursor:pointer;"></i>';		
										if($btnEdita)
											$listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$ctasContPas["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
										if($btnElimina)
											$listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$ctasContPas["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
											
					$listaCtasPas.= '	</div>									
									</div>'; 
								
			//CHECAMOS SI TIENE HIJOS DE NIVEL 1
			liberar_bd();
			$selectHijosNivel1 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$ctasContPas["id"].',1);';
			$hijosNivel1 = consulta($selectHijosNivel1);
			$ctaHijosNivel1 = cuenta_registros($hijosNivel1);
			if($ctaHijosNivel1 != 0)
			{
				$listaCtasPas .='<ol class="dd-list">';
				while($nivel1 = siguiente_registro($hijosNivel1))
				{
					$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel1["id"].'">
										  <div class="dd-handle dd3-handle fa fa-th"></div>
											  <div class="dd3-content">
												  '.utf8_encode($nivel1["nombre"]).'
												  <div class="btn-group btnsCuentas">	';
												  
												  if($btnAgregar)
													  $listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(2,'.$nivel1["id"].');" style="cursor:pointer;"></i>';		
												  if($btnEdita)
													  $listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel1["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
												  if($btnElimina)
													  $listaCtasPas.= '	 <i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel1["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
													  
							  $listaCtasPas.= '		</div>																					  
												</div>';
										
						//CHECAMOS SI TIENE HIJOS DE NIVEL 2
						liberar_bd();
						$selectHijosNivel2 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel1["id"].',2);';
						$hijosNivel2 = consulta($selectHijosNivel2);
						$ctaHijosNivel2 = cuenta_registros($hijosNivel2);
						if($ctaHijosNivel2 != 0)
						{
							$listaCtasPas .='<ol class="dd-list">';
							while($nivel2 = siguiente_registro($hijosNivel2))
							{
								$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel2["id"].'">
													  <div class="dd-handle dd3-handle fa fa-th"></div>
													  <div class="dd3-content">
														  '.utf8_encode($nivel2["nombre"]).'
														   <div class="btn-group btnsCuentas">	';
															  if($btnAgregar)
																  $listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(2,'.$nivel2["id"].');" style="cursor:pointer;"></i>';		
															  if($btnEdita)
																  $listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel2["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
															  if($btnElimina)
																  $listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel2["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
									$listaCtasPas.= '	   </div>													  
													  </div>';
												  
									//CHECAMOS SI TIENE HIJOS DE NIVEL 3
									liberar_bd();
									$selectHijosNivel3 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel2["id"].',3);';
									$hijosNivel3 = consulta($selectHijosNivel3);
									$ctaHijosNivel3 = cuenta_registros($hijosNivel3);
									if($ctaHijosNivel3 != 0)
									{
										$listaCtasPas .='<ol class="dd-list">';
										while($nivel3 = siguiente_registro($hijosNivel3))
										{
											$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel3["id"].'">
																  <div class="dd-handle dd3-handle fa fa-th"></div>
																  <div class="dd3-content">
																	  '.utf8_encode($nivel3["nombre"]).'
																	   <div class="btn-group btnsCuentas">	';
																		  if($btnAgregar)
																			  $listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(2,'.$nivel3["id"].');" style="cursor:pointer;"></i>';		
																		  if($btnEdita)
																			  $listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel3["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																		  if($btnElimina)
																			  $listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel3["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
												$listaCtasPas.= '		</div>																  
																  </div>';
																  
												//CHECAMOS SI TIENE HIJOS DE NIVEL 4
												liberar_bd();
												$selectHijosNivel4 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel3["id"].',4);';
												$hijosNivel4 = consulta($selectHijosNivel4);
												$ctaHijosNivel4 = cuenta_registros($hijosNivel4);
												if($ctaHijosNivel4 != 0)
												{
													$listaCtasPas .='<ol class="dd-list">';
													while($nivel4 = siguiente_registro($hijosNivel4))
													{
														$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel4["id"].'">
																			  <div class="dd-handle dd3-handle fa fa-th"></div>
																			  <div class="dd3-content">
																				  '.utf8_encode($nivel4["nombre"]).'
																				   <div class="btn-group btnsCuentas">	';
																					  if($btnAgregar)
																						  $listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(2,'.$nivel4["id"].');" style="cursor:pointer;"></i>';		
																					  if($btnEdita)
																						  $listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel4["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																					  if($btnElimina)
																						  $listaCtasPas.= '	 <i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel4["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																  $listaCtasPas.= '	</div>																			  
																			  </div>';
																			  
															//CHECAMOS SI TIENE HIJOS DE NIVEL 5
															liberar_bd();
															$selectHijosNivel5 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel4["id"].',5);';
															$hijosNivel5 = consulta($selectHijosNivel5);
															$ctaHijosNivel5 = cuenta_registros($hijosNivel5);
															if($ctaHijosNivel5 != 0)
															{
																$listaCtasPas .='<ol class="dd-list">';
																while($nivel5 = siguiente_registro($hijosNivel5))
																{
																	$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel5["id"].'">
																						  <div class="dd-handle dd3-handle fa fa-th"></div>
																						  <div class="dd3-content">
																							  '.utf8_encode($nivel5["nombre"]).'
																							   <div class="btn-group btnsCuentas">	';
																									if($btnAgregar)
																										$listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(2,'.$nivel5["id"].');" style="cursor:pointer;"></i>';		
																									if($btnEdita)
																										$listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel5["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																									if($btnElimina)
																										$listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel5["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																			$listaCtasPas.= '	</div>	
																						  </div>';
																					  
																	//CHECAMOS SI TIENE HIJOS DE NIVEL 6
																	liberar_bd();
																	$selectHijosNivel6 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel5["id"].',6);';
																	$hijosNivel6 = consulta($selectHijosNivel6);
																	$ctaHijosNivel6 = cuenta_registros($hijosNivel6);
																	if($ctaHijosNivel6 != 0)
																	{
																		$listaCtasPas .='<ol class="dd-list">';
																		while($nivel6 = siguiente_registro($hijosNivel6))
																		{
																			$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel6["id"].'">
																								  <div class="dd-handle dd3-handle fa fa-th"></div>
																								  <div class="dd3-content">
																									  '.utf8_encode($nivel6["nombre"]).'
																									   <div class="btn-group btnsCuentas">	';
																											if($btnAgregar)
																												$listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(2,'.$nivel6["id"].');" style="cursor:pointer;"></i>';		
																											if($btnEdita)
																												$listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel6["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																											if($btnElimina)
																												$listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel6["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																					$listaCtasPas.= '	</div>	
																								  </div>';
																			//CHECAMOS SI TIENE HIJOS DE NIVEL 7
																			liberar_bd();
																			$selectHijosNivel7 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel6["id"].',7);';
																			$hijosNivel7 = consulta($selectHijosNivel7);
																			$ctaHijosNivel7 = cuenta_registros($hijosNivel7);
																			if($ctaHijosNivel7 != 0)
																			{
																				$listaCtasPas .='<ol class="dd-list">';
																				while($nivel7 = siguiente_registro($hijosNivel7))
																				{
																					$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel7["id"].'">
																									  <div class="dd-handle dd3-handle fa fa-th"></div>
																									  <div class="dd3-content">
																									  '.utf8_encode($nivel7["nombre"]).'
																									   <div class="btn-group btnsCuentas">	';
																											if($btnAgregar)
																												$listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(2,'.$nivel7["id"].');" style="cursor:pointer;"></i>';		
																											if($btnEdita)
																												$listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel7["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																											if($btnElimina)
																												$listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel7["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																					  $listaCtasPas.= '	</div>
																									 </div>';
																									 
																					//CHECAMOS SI TIENE HIJOS DE NIVEL 8
																					liberar_bd();
																					$selectHijosNivel8 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel7["id"].',8);';
																					$hijosNivel8 = consulta($selectHijosNivel8);
																					$ctaHijosNivel8 = cuenta_registros($hijosNivel8);
																					if($ctaHijosNivel8 != 0)
																					{
																						$listaCtasPas .='<ol class="dd-list">';
																						while($nivel8 = siguiente_registro($hijosNivel8))
																						{
																							$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel8["id"].'">
																												  <div class="dd-handle dd3-handle fa fa-th"></div>
																												  <div class="dd3-content">
																													  '.utf8_encode($nivel8["nombre"]).'
																													   <div class="btn-group btnsCuentas">	';
																															if($btnAgregar)
																																$listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(2,'.$nivel8["id"].');" style="cursor:pointer;"></i>';		
																															if($btnEdita)
																																$listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel8["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																															if($btnElimina)
																																$listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel8["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																									$listaCtasPas.= '	</div>	
																											  		</div>';
																													
																							//CHECAMOS SI TIENE HIJOS DE NIVEL 9
																							liberar_bd();
																							$selectHijosNivel9 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel8["id"].',9);';
																							$hijosNivel9 = consulta($selectHijosNivel9);
																							$ctaHijosNivel9 = cuenta_registros($hijosNivel9);
																							if($ctaHijosNivel9 != 0)
																							{
																								$listaCtasPas .='<ol class="dd-list">';
																								while($nivel9 = siguiente_registro($hijosNivel9))
																								{
																									$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel9["id"].'">
																														  <div class="dd-handle dd3-handle fa fa-th"></div>
																														  <div class="dd3-content">
																															  '.utf8_encode($nivel9["nombre"]).'
																															   <div class="btn-group btnsCuentas">	';
																																   if($btnEdita)
																																		$listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="agregarSubCuenta(2,'.$nivel9["id"].');" onClick="document.frmSistema.idCuenta.value='.$nivel9["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																																	if($btnElimina)
																																		$listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel9["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																											$listaCtasPas.= '	</div>																															
																													  		</div>';																						
																									$listaCtasPas .= '</li>';
																								}
																								$listaCtasPas .='</ol>';				
																							}
																							$listaCtasPas .= '</li>';
																						}
																						$listaCtasPas .='</ol>';				
																					}
																					$listaCtasPas .= '</li>';
																				}
																				$listaCtasPas .='</ol>';				
																			}
																			$listaCtasPas .= '</li>';
																		}
																		$listaCtasPas .='</ol>';				
																	}
																	$listaCtasPas .= '</li>';
																}
																$listaCtasPas .='</ol>';				
															}
														$listaCtasPas .= '</li>';
													}
													$listaCtasPas .='</ol>';				
												}
											$listaCtasPas .= '</li>';
										}
										$listaCtasPas .='</ol>';				
									}
								$listaCtasPas .= '</li>';
							}
							$listaCtasPas .='</ol>';				
						}			
					$listaCtasPas .= '</li>';
				}
				$listaCtasPas .='</ol>';				
			}			
			$listaCtasPas .= '</li>';
			
			switch($ctasContPas["tipo"])
			{
				case 1:
					$listaCtasPasCirc.= $listaCtasPas; 	
				break;
				case 2:
					$listaCtasPasFij.= $listaCtasPas; 	
				break;
				case 3:
					$listaCtasPasDif.= $listaCtasPas; 	
				break;
			}			
		}
		
		//CUENTAS DE CAPITAL
		liberar_bd();
		$selectCuentasContablesCapital = "	SELECT
												ctaCont.id_ctas_contable AS id,
												ctaCont.nivel_ctas_contable AS nivel,
												ctaCont.padre_ctas_contable AS padre,
												ctaCont.nombre_ctas_contable AS nombre,
												ctaCont.estatus_ctas_contable AS estatus
											FROM
												ctas_contable AS ctaCont
											WHERE
												ctaCont.estatus_ctas_contable = 1
											AND ctaCont.clasificacion_ctas_contable = 3
											AND ctaCont.nivel_ctas_contable = 0";
							  
		$cuentasContablesCap = consulta($selectCuentasContablesCapital);
		while($ctasContCap =  siguiente_registro($cuentasContablesCap))
		{
			$listaCtasCap .= '	<li class="dd-item dd3-item" data-id="'.$ctasContCap["id"].'">
									<div class="dd-handle dd3-handle fa fa-th"></div>
									<div class="dd3-content">
										'.utf8_encode($ctasContCap["nombre"]).'
										<div class="btn-group btnsCuentas">	';
										
										if($btnAgregar)
											$listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(3,'.$ctasContCap["id"].');" style="cursor:pointer;"></i>';		
										if($btnEdita)
											$listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$ctasContCap["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
										if($btnElimina)
											$listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$ctasContCap["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
											
					$listaCtasCap.= '	</div>									
									</div>'; 
								
			//CHECAMOS SI TIENE HIJOS DE NIVEL 1
			liberar_bd();
			$selectHijosNivel1 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$ctasContCap["id"].',1);';
			$hijosNivel1 = consulta($selectHijosNivel1);
			$ctaHijosNivel1 = cuenta_registros($hijosNivel1);
			if($ctaHijosNivel1 != 0)
			{
				$listaCtasCap .='<ol class="dd-list">';
				while($nivel1 = siguiente_registro($hijosNivel1))
				{
					$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel1["id"].'">
										  <div class="dd-handle dd3-handle fa fa-th"></div>
											  <div class="dd3-content">
												  '.utf8_encode($nivel1["nombre"]).'
												  <div class="btn-group btnsCuentas">	';
												  
												  if($btnAgregar)
													  $listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(3,'.$nivel1["id"].');" style="cursor:pointer;"></i>';		
												  if($btnEdita)
													  $listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel1["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
												  if($btnElimina)
													  $listaCtasCap.= '	 <i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel1["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
													  
							  $listaCtasCap.= '		</div>																					  
												</div>';
										
						//CHECAMOS SI TIENE HIJOS DE NIVEL 2
						liberar_bd();
						$selectHijosNivel2 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel1["id"].',2);';
						$hijosNivel2 = consulta($selectHijosNivel2);
						$ctaHijosNivel2 = cuenta_registros($hijosNivel2);
						if($ctaHijosNivel2 != 0)
						{
							$listaCtasCap .='<ol class="dd-list">';
							while($nivel2 = siguiente_registro($hijosNivel2))
							{
								$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel2["id"].'">
													  <div class="dd-handle dd3-handle fa fa-th"></div>
													  <div class="dd3-content">
														  '.utf8_encode($nivel2["nombre"]).'
														   <div class="btn-group btnsCuentas">	';
															  if($btnAgregar)
																  $listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(3,'.$nivel2["id"].');" style="cursor:pointer;"></i>';		
															  if($btnEdita)
																  $listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel2["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
															  if($btnElimina)
																  $listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel2["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
									$listaCtasCap.= '	   </div>													  
													  </div>';
												  
									//CHECAMOS SI TIENE HIJOS DE NIVEL 3
									liberar_bd();
									$selectHijosNivel3 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel2["id"].',3);';
									$hijosNivel3 = consulta($selectHijosNivel3);
									$ctaHijosNivel3 = cuenta_registros($hijosNivel3);
									if($ctaHijosNivel3 != 0)
									{
										$listaCtasCap .='<ol class="dd-list">';
										while($nivel3 = siguiente_registro($hijosNivel3))
										{
											$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel3["id"].'">
																  <div class="dd-handle dd3-handle fa fa-th"></div>
																  <div class="dd3-content">
																	  '.utf8_encode($nivel3["nombre"]).'
																	   <div class="btn-group btnsCuentas">	';
																		  if($btnAgregar)
																			  $listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(3,'.$nivel3["id"].');" style="cursor:pointer;"></i>';		
																		  if($btnEdita)
																			  $listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel3["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																		  if($btnElimina)
																			  $listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel3["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
												$listaCtasCap.= '		</div>																  
																  </div>';
																  
												//CHECAMOS SI TIENE HIJOS DE NIVEL 4
												liberar_bd();
												$selectHijosNivel4 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel3["id"].',4);';
												$hijosNivel4 = consulta($selectHijosNivel4);
												$ctaHijosNivel4 = cuenta_registros($hijosNivel4);
												if($ctaHijosNivel4 != 0)
												{
													$listaCtasCap .='<ol class="dd-list">';
													while($nivel4 = siguiente_registro($hijosNivel4))
													{
														$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel4["id"].'">
																			  <div class="dd-handle dd3-handle fa fa-th"></div>
																			  <div class="dd3-content">
																				  '.utf8_encode($nivel4["nombre"]).'
																				   <div class="btn-group btnsCuentas">	';
																					  if($btnAgregar)
																						  $listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(3,'.$nivel4["id"].');" style="cursor:pointer;"></i>';		
																					  if($btnEdita)
																						  $listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel4["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																					  if($btnElimina)
																						  $listaCtasCap.= '	 <i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel4["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																  $listaCtasCap.= '	</div>																			  
																			  </div>';
																			  
															//CHECAMOS SI TIENE HIJOS DE NIVEL 5
															liberar_bd();
															$selectHijosNivel5 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel4["id"].',5);';
															$hijosNivel5 = consulta($selectHijosNivel5);
															$ctaHijosNivel5 = cuenta_registros($hijosNivel5);
															if($ctaHijosNivel5 != 0)
															{
																$listaCtasCap .='<ol class="dd-list">';
																while($nivel5 = siguiente_registro($hijosNivel5))
																{
																	$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel5["id"].'">
																						  <div class="dd-handle dd3-handle fa fa-th"></div>
																						  <div class="dd3-content">
																							  '.utf8_encode($nivel5["nombre"]).'
																							   <div class="btn-group btnsCuentas">	';
																									if($btnAgregar)
																										$listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(3,'.$nivel5["id"].');" style="cursor:pointer;"></i>';		
																									if($btnEdita)
																										$listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel5["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																									if($btnElimina)
																										$listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel5["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																			$listaCtasCap.= '	</div>	
																						  </div>';
																					  
																	//CHECAMOS SI TIENE HIJOS DE NIVEL 6
																	liberar_bd();
																	$selectHijosNivel6 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel5["id"].',6);';
																	$hijosNivel6 = consulta($selectHijosNivel6);
																	$ctaHijosNivel6 = cuenta_registros($hijosNivel6);
																	if($ctaHijosNivel6 != 0)
																	{
																		$listaCtasCap .='<ol class="dd-list">';
																		while($nivel6 = siguiente_registro($hijosNivel6))
																		{
																			$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel6["id"].'">
																								  <div class="dd-handle dd3-handle fa fa-th"></div>
																								  <div class="dd3-content">
																									  '.utf8_encode($nivel6["nombre"]).'
																									   <div class="btn-group btnsCuentas">	';
																											if($btnAgregar)
																												$listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(3,'.$nivel6["id"].');" style="cursor:pointer;"></i>';		
																											if($btnEdita)
																												$listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel6["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																											if($btnElimina)
																												$listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel6["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																					$listaCtasCap.= '	</div>	
																								  </div>';
																			//CHECAMOS SI TIENE HIJOS DE NIVEL 7
																			liberar_bd();
																			$selectHijosNivel7 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel6["id"].',7);';
																			$hijosNivel7 = consulta($selectHijosNivel7);
																			$ctaHijosNivel7 = cuenta_registros($hijosNivel7);
																			if($ctaHijosNivel7 != 0)
																			{
																				$listaCtasCap .='<ol class="dd-list">';
																				while($nivel7 = siguiente_registro($hijosNivel7))
																				{
																					$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel7["id"].'">
																									  <div class="dd-handle dd3-handle fa fa-th"></div>
																									  <div class="dd3-content">
																									  '.utf8_encode($nivel7["nombre"]).'
																									   <div class="btn-group btnsCuentas">	';
																											if($btnAgregar)
																												$listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(3,'.$nivel7["id"].');" style="cursor:pointer;"></i>';		
																											if($btnEdita)
																												$listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel7["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																											if($btnElimina)
																												$listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel7["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																					  $listaCtasCap.= '	</div>
																									 </div>';
																									 
																					//CHECAMOS SI TIENE HIJOS DE NIVEL 8
																					liberar_bd();
																					$selectHijosNivel8 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel7["id"].',8);';
																					$hijosNivel8 = consulta($selectHijosNivel8);
																					$ctaHijosNivel8 = cuenta_registros($hijosNivel8);
																					if($ctaHijosNivel8 != 0)
																					{
																						$listaCtasCap .='<ol class="dd-list">';
																						while($nivel8 = siguiente_registro($hijosNivel8))
																						{
																							$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel8["id"].'">
																												  <div class="dd-handle dd3-handle fa fa-th"></div>
																												  <div class="dd3-content">
																													  '.utf8_encode($nivel8["nombre"]).'
																													   <div class="btn-group btnsCuentas">	';
																															if($btnAgregar)
																																$listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="agregarSubCuenta(3,'.$nivel8["id"].');" style="cursor:pointer;"></i>';		
																															if($btnEdita)
																																$listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel8["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																															if($btnElimina)
																																$listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel8["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																									$listaCtasCap.= '	</div>	
																											  		</div>';
																													
																							//CHECAMOS SI TIENE HIJOS DE NIVEL 9
																							liberar_bd();
																							$selectHijosNivel9 = 'CALL sp_sistema_select_hijosNivel_ctasContables('.$nivel8["id"].',9);';
																							$hijosNivel9 = consulta($selectHijosNivel9);
																							$ctaHijosNivel9 = cuenta_registros($hijosNivel9);
																							if($ctaHijosNivel9 != 0)
																							{
																								$listaCtasCap .='<ol class="dd-list">';
																								while($nivel9 = siguiente_registro($hijosNivel9))
																								{
																									$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel9["id"].'">
																														  <div class="dd-handle dd3-handle fa fa-th"></div>
																														  <div class="dd3-content">
																															  '.utf8_encode($nivel9["nombre"]).'
																															   <div class="btn-group btnsCuentas">	';
																																   if($btnEdita)
																																		$listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel9["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																																	if($btnElimina)
																																		$listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta contable\')){document.frmSistema.idCuenta.value='.$nivel9["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																											$listaCtasCap.= '	</div>																															
																													  		</div>';																						
																									$listaCtasCap .= '</li>';
																								}
																								$listaCtasCap .='</ol>';				
																							}
																							$listaCtasCap .= '</li>';
																						}
																						$listaCtasCap .='</ol>';				
																					}
																					$listaCtasCap .= '</li>';
																				}
																				$listaCtasCap .='</ol>';				
																			}
																			$listaCtasCap .= '</li>';
																		}
																		$listaCtasCap .='</ol>';				
																	}
																	$listaCtasCap .= '</li>';
																}
																$listaCtasCap .='</ol>';				
															}
														$listaCtasCap .= '</li>';
													}
													$listaCtasCap .='</ol>';				
												}
											$listaCtasCap .= '</li>';
										}
										$listaCtasCap .='</ol>';				
									}
								$listaCtasCap .= '</li>';
							}
							$listaCtasCap .='</ol>';				
						}			
					$listaCtasCap .= '</li>';
				}
				$listaCtasCap .='</ol>';				
			}			
			$listaCtasCap .= '</li>';			
		}
		
		$pagina = linktag('assets/plugins/form-nestable/jquery.nestable.css').
				'	<div id="page-heading">	
					  	<ol class="breadcrumb">
							<li><a href="javascript:navegar_modulo(0);">Tablero</a></li>    
							<li class="active">
								'.$_SESSION["moduloPadreActual"].'
							</li>
						</ol>
						<h1>'.$_SESSION["moduloPadreActual"].'</h1>
						<div class="options">
							<div class="btn-toolbar">
								<input readonly="readonly" type="hidden" id="idCuenta" name="idCuenta" value="" />
								<input readonly="readonly" type="hidden" id="clasCuenta" name="clasCuenta" value="" />';
								if($btnAlta)
									$pagina.= '	<i title="Nuevo cuenta" style="cursor:pointer;" onclick="navegar(\'Nuevo\')" class="btn btn-warning" >
														Nuevo cuenta
												   </i>';				
		$pagina.= '			</div>
						</div>										
				  	</div>									
				  	<div class="container">						
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-danger">
									<div class="panel-heading">
										<h4></h4>
										<div class="options">   
											<a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down" style="cursor:pointer;"></i></a>
										</div>
									</div>
									<div class="panel-body collapse in">
										<div class="row">
											<div class="col-md-12">
												<div class="panel">
													<div class="panel-body" style="background-color: #f7f8fa;">
														<div class="row">
															<div class="col-md-12">
																<h3>Activo</h3>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="panel">
																	<div class="panel-body">
																		<h4>Circulante</h4>
																		<div class="dd" id="nestable_list_1">
																			<ol class="dd-list">
																				'.$listaContActCirc.'
																			</ol>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="panel">
																	<div class="panel-body">
																		<h4>Fijo</h4>
																		<div class="dd" id="nestable_list_1">
																			<ol class="dd-list">
																				'.$listaContActFij.'
																			</ol>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="panel">
																	<div class="panel-body">
																		<h4>Diferido</h4>
																		<div class="dd" id="nestable_list_1">
																			<ol class="dd-list">
																				'.$listaContActDif.'
																			</ol>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<div class="panel">
													<div class="panel-body" style="background-color: #f7f8fa;">
														<div class="row">
															<div class="col-md-12">
																<h3>Pasivo</h3>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="panel">
																	<div class="panel-body">
																		<h4>Circulante</h4>
																		<div class="dd" id="nestable_list_1">
																			<ol class="dd-list">
																				'.$listaCtasPasCirc.'
																			</ol>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="panel">
																	<div class="panel-body">
																		<h4>Fijo</h4>
																		<div class="dd" id="nestable_list_1">
																			<ol class="dd-list">
																				'.$listaCtasPasFij.'
																			</ol>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="panel">
																	<div class="panel-body">
																		<h4>Diferido</h4>
																		<div class="dd" id="nestable_list_1">
																			<ol class="dd-list">
																				'.$listaCtasPasDif.'
																			</ol>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<div class="panel">
													<div class="panel-body" style="background-color: #f7f8fa;">
														<div class="row">
															<div class="col-md-12">
																<h3>Capital</h3>
															</div>
														</div>
														<div class="dd" id="nestable_list_3">
															<ol class="dd-list">
																'.$listaCtasCap.'
															</ol>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>'.
					scripttag("assets/plugins/form-nestable/jquery.nestable.min.js").
					scripttag("assets/plugins/form-nestable/app.min.js").
					scripttag("assets/demo/demo-nestable.min.js");
							
		return $pagina;
	}
	
	function cuentas_formularioNuevo()
	{
		$pagina = '		<div id="page-heading">	
							 <ol class="breadcrumb">
								  <li><a href="javascript:navegar_modulo(0);">Tablero</a></li> 
								  <li><a href="javascript:navegar_modulo('.$_SESSION["mod"].');">'.$_SESSION["moduloPadreActual"].'</a></li>    
								  <li class="active">
									  '.$_SESSION["moduloHijoActual"].'
								  </li>
							 </ol>  
							 <h1>'.$_SESSION["moduloHijoActual"].'</h1>
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
											<h4></h4>
										</div>
										<div class="panel-body" style="border-radius: 0px;">
											<div class="form-horizontal">
												<div class="form-group">
													<label for="codigoCta" class="col-sm-3 control-label">C&oacute;digo de mayor:</label>
													<div class="col-sm-6">
														<input type="text" class="form-control" id="codigoCta" name="codigoCta" maxlength="10"/>
													</div>
												</div>
												<div class="form-group">
													<label for="nombreCta" class="col-sm-3 control-label">Nombre:</label>
													<div class="col-sm-6">
														<input type="text" class="form-control" id="nombreCta" name="nombreCta" maxlength="100"/>
													</div>
												</div>
												<div class="form-group">
													<label for="clasCta" class="col-sm-3 control-label">Clasificaci&oacute;n:</label>
													<div class="col-sm-6">
														<select id="clasCta" name="clasCta" style="width:100% !important" class="selectSerch">
															<option value="1">Activo</option>
															<option value="2">Pasivo</option>
															<option value="3">Capital</option>
														</select>
													</div>
												</div> 
												<div class="form-group" id="divTipoCta">
													<label for="tipoCta" class="col-sm-3 control-label">Tipo:</label>
													<div class="col-sm-6">
														<select id="tipoCta" name="tipoCta" style="width:100% !important" class="selectSerch">
															<option value="1">Circulante</option>
															<option value="2">Fijo</option>
															<option value="3">Diferido</option>
														</select>
													</div>
												</div>                             
												<div class="form-group">
													<label for="descCta" class="col-sm-3 control-label">Descripci&oacute;n:</label>
													<div class="col-sm-6">
														<textarea class="form-control" id="descCta" name="descCta"></textarea>
													</div>
												</div>                              
											</div>											
										</div>
										<div class="panel-footer">
											<div class="row">
												<div class="col-sm-12">
													<div class="btn-toolbar btnsGuarCan">
														<i class="btn-danger btn" onclick="navegar();">Cancelar</i>
														<i class="btn-success btn" onclick="nuevaCtaContable(\'Guardar\');">Guardar</i>
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
	
	function cuentas_guardar()
	{	
		//CHECAMOS QUE NO EXITAN DOS IGUALES							
		liberar_bd();
		$selectCuenta =  "CALL sp_sistema_select_cuenta_contable_nombre('".utf8_decode($_POST["codigoCta"])."', 
																		'".utf8_decode($_POST["nombreCta"])."');";
		$cuenta = consulta($selectCuenta);
		$ctaCuenta = cuenta_registros($cuenta);
		if($ctaCuenta == 0)
		{
			if($_POST["clasCta"] != 3)
				$tipoCta = $_POST["tipoCta"];
			else
				$tipoCta = 0;
			
			//INSERTAMOS LA CUENTA CONTABLE	
			liberar_bd();
			$insertCuenta = " CALL sp_sistema_insert_cuenta_contable_registro('".$_POST["codigoCta"]."',
																			  '".utf8_decode($_POST["nombreCta"])."',
																			  '".utf8_decode($_POST["descCta"])."',
																			  ".$_POST["clasCta"].",
																			  ".$tipoCta.",
																			  ".$_SESSION[$varIdUser].");";								  
			$insert = consulta($insertCuenta);
			
			if($insert)
				$res= $msj.cuentas_menuInicio();					
			else
			{
				$error='No se ha podido guardar la cuenta contable.';
				$msj = sistema_mensaje("error",$error);
				$pagina = cuentas_nuevo_error();
				$res= $msj.$pagina;									
			}
		}
		else
		{
			$error='Ya existe un tipo de egreso con este nombre.';
			$msj = sistema_mensaje("error",$error);
			$pagina = cuentas_nuevo_error();
			$res= $msj.$pagina;
		}
		
		return $res;
	}
	
	function cuentas_formularioNuevaSub()
	{
		$pagina = '<div id="page-heading">	
					   <ol class="breadcrumb">
							<li><a href="javascript:navegar_modulo(0);">Tablero</a></li> 
							<li><a href="javascript:navegar_modulo('.$_SESSION["mod"].');">'.$_SESSION["moduloPadreActual"].'</a></li>    
							<li class="active">
								'.$_SESSION["moduloHijoActual"].'
							</li>
					   </ol>  
					   <h1>'.$_SESSION["moduloHijoActual"].'</h1>
					   <div class="options">
						   <div class="btn-toolbar"> 
							  <input type="hidden" id="idCuenta" name="idCuenta" value="'.$_POST["idCuenta"].'" />
							  <input type="hidden" id="clasCuenta" name="clasCuenta" value="'.$_POST["clasCuenta"].'" />	
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
											  <label for="nombreCta" class="col-sm-3 control-label">C&oacute;digo de mayor:</label>
											  <div class="col-sm-6">
												  <input type="text" class="form-control" id="codigoCta" name="codigoCta" maxlength="10"/>
											  </div>
										  </div>
										  <div class="form-group">
											  <label for="nombreCta" class="col-sm-3 control-label">Nombre:</label>
											  <div class="col-sm-6">
												  <input type="text" class="form-control" id="nombreCta" name="nombreCta" maxlength="100"/>
											  </div>
										  </div>										                             
										  <div class="form-group">
											  <label for="descCta" class="col-sm-3 control-label">Descripci&oacute;n:</label>
											  <div class="col-sm-6">
												  <textarea class="form-control" id="descCta" name="descCta"></textarea>
											  </div>
										  </div>                            
									  </div>											
								  </div>
								  <div class="panel-footer">
									  <div class="row">
										  <div class="col-sm-12">
											  <div class="btn-toolbar btnsGuarCan">
												  <i class="btn-danger btn" onclick="navegar();">Cancelar</i>
												  <i class="btn-success btn" onclick="nuevaCtaContable(\'GuardarAgregar\');">Guardar</i>
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
	
	function cuentas_guardarsubCuenta()
	{	
		liberar_bd();
		$selectCuenta =  "CALL sp_sistema_select_cuenta_contable_nombre('".utf8_decode($_POST["codigoCta"])."', 
																		'".utf8_decode($_POST["nombreCta"])."');";
		$cuenta = consulta($selectCuenta);
		$ctaCuenta = cuenta_registros($cuenta);		  
		if($ctaCuenta == 0)
		{
			//DATOS DE LA CUENTA PADRE
			liberar_bd();
			$selectDatosCuenta = 'CALL sp_sistema_select_datos_cuenta_contableId('.$_POST["idCuenta"].');';
			$datosCuenta = consulta($selectDatosCuenta);
			$datCuent = siguiente_registro($datosCuenta);
			$nivel = $datCuent["nivel"] + 1;			 
			
			//INSERTAMOS LA SUBCUENTA
			liberar_bd();
			$insertCuenta = " CALL sp_sistema_insert_subcuenta_contable(".$nivel.",
																		".$_POST["idCuenta"].", 
																		'".$_POST["codigoCta"]."',
																		'".utf8_decode($_POST["nombreCta"])."',
																		'".utf8_decode($_POST["descCta"])."',
																		".$datCuent["clasif"].",
																		".$datCuent["tipo"].",
																		".$_SESSION[$varIdUser].");";								  
			$insert = consulta($insertCuenta);
			
			if($insert)
			{
				$ctaNumeroHijos = $datCuent["numHijos"] + 1;
				
				//ACTUALIZAMOS NUMERO DE HIJOS DE LA CUENTA PADRE
				liberar_bd();
				$updateNumeroHijos = 'CALL sp_sistema_update_numeroHijos_cuenta_contableId('.$_POST["idCuenta"].', '.$ctaNumeroHijos.');';
				$upNumHijos = consulta($updateNumeroHijos);
				$res= $msj.cuentas_menuInicio();					
			}
			else
			{
				$error='No se ha podido guardar la subcuenta contable.';
				$msj = sistema_mensaje("error",$error);
				$pagina = cuentas_nuevo_subcuenta_error();
				$res= $msj.$pagina;									
			}
		}
		else
		{
			$error='Ya existe una cuenta con este nombre.';
			$msj = sistema_mensaje("error",$error);
			$pagina = cuentas_nuevo_subcuenta_error();							
			$res= $msj.$pagina;
		}
		
		return $res;
	}
	
	function cuentas_formularioEditar()
	{
		//DATOS DE LA CUENTA
		liberar_bd();
		$selectDatosCuenta = 'CALL sp_sistema_select_datos_cuenta_contable('.$_POST["idCuenta"].');';
		$datosCuenta = consulta($selectDatosCuenta);
		$cuen = siguiente_registro($datosCuenta);
		
		$pagina = '	   <div id="page-heading">	
						   <ol class="breadcrumb">
								<li><a href="javascript:navegar_modulo(0);">Tablero</a></li> 
								<li><a href="javascript:navegar_modulo('.$_SESSION["mod"].');">'.$_SESSION["moduloPadreActual"].'</a></li>    
								<li class="active">
									'.$_SESSION["moduloHijoActual"].'
								</li>
						   </ol>  
						   <h1>'.$_SESSION["moduloHijoActual"].'</h1>
						   <div class="options">
							  <div class="btn-toolbar"> 
								  <input type="hidden" id="idCuenta" name="idCuenta" value="'.$_POST["idCuenta"].'" />
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
												  <label for="codigoCta" class="col-sm-3 control-label">C&oacute;digo de mayor:</label>
												  <div class="col-sm-6">
													  <input type="text" class="form-control" id="codigoCta" name="codigoCta" maxlength="10" value="'.utf8_encode($cuen["codigo"]).'"/>
												  </div>
											  </div>
											  <div class="form-group">
												  <label for="nombreCta" class="col-sm-3 control-label">Nombre:</label>
												  <div class="col-sm-6">
													  <input type="text" class="form-control" id="nombreCta" name="nombreCta" maxlength="100" value="'.utf8_encode($cuen["nombre"]).'"/>
												  </div>
											  </div>                          
											  <div class="form-group">
												  <label for="descCta" class="col-sm-3 control-label">Descripci&oacute;n:</label>
												  <div class="col-sm-6">
													  <textarea class="form-control" id="descCta" name="descCta">'.utf8_encode($cuen["txt"]).'</textarea>
												  </div>
											  </div>                              
										  </div>											
									  </div>
									  <div class="panel-footer">
										  <div class="row">
											  <div class="col-sm-12">
												  <div class="btn-toolbar btnsGuarCan">
													  <i class="btn-danger btn" onclick="navegar();">Cancelar</i>
													  <i class="btn-success btn" onclick="nuevaCtaContable(\'GuardarEdit\');">Guardar</i>
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
	
	function cuentas_editarCuenta()
	{	
		liberar_bd();
		$selectCuenta =  "CALL sp_sistema_select_cuenta_contable_nombreId(	".$_POST["idCuenta"].", 
																			'".utf8_decode($_POST["codigoCta"])."', 
																			'".utf8_decode($_POST["nombreCta"])."');";
		$cuenta = consulta($selectCuenta);
		$ctaCuenta = cuenta_registros($cuenta);		  
		if($ctaCuenta == 0)
		{
			//ACTUALIZAMOS LA SUBCUENTA
			liberar_bd();
			$updateCuenta = " CALL sp_sistema_update_subcuenta_contable(".$_POST["idCuenta"].", 
																		'".$_POST["codigoCta"]."',
																		'".utf8_decode($_POST["nombreCta"])."',
																		'".utf8_decode($_POST["descCta"])."',
																		".$_SESSION[$varIdUser].");";								  
			$update = consulta($updateCuenta);
			
			if($update)
				$res= $msj.cuentas_menuInicio();
			else
			{
				$error='No se ha podido editar la cuenta contable.';
				$msj = sistema_mensaje("error",$error);
				$pagina = cuentas_editar_subcuenta_error();
				$res= $msj.$pagina;									
			}
		}
		else
		{
			$error='Ya existe una cuenta con este nombre.';
			$msj = sistema_mensaje("error",$error);
			$pagina = cuentas_editar_subcuenta_error();							
			$res= $msj.$pagina;
		}
		
		return $res;
	}
	
	function cuentas_eliminarCuenta()
	{
		//CHECAMOS SI LA CUENTA TIENE SUBCUENTAS
		liberar_bd();
		$selectSubcuentasCuentas = 'CALL sp_sistema_select_subcuentas_cuentas_contables('.$_POST["idCuenta"].');';
		$subCueCuen = consulta($selectSubcuentasCuentas);
		$ctaSubCueCue = cuenta_registros($subCueCuen);
		if($ctaSubCueCue == 0)
		{
			liberar_bd();
			$deleteCtaContable = "CALL sp_sistema_delete_cuenta_contable(".$_POST["idCuenta"].", ".$_SESSION[$varIdUser].");";
			$delete = consulta($deleteCtaContable);
			if($delete)
			{
				//DATOS DE LA CUENTA
			  	liberar_bd();
			  	$selectDatosCuenta = 'CALL sp_sistema_select_datos_cuenta_contableId('.$_POST["idCuenta"].');';
			  	$datosCuenta = consulta($selectDatosCuenta);
			  	$cuen = siguiente_registro($datosCuenta);
				
				//NUMERO DE HIJOS DE LA CUENTA PADRE
				liberar_bd();
				$selectNumeroHijos = 'CALL sp_sistema_select_datos_cuenta_contableId('.$cuen["padre"].');';
				$numeroHijos = consulta($selectNumeroHijos);
				$hijos = siguiente_registro($numeroHijos);
				$ctaNumeroHijos = $hijos["numHijos"] - 1;				
				
				//ACTUALIZAMOS NUMERO DE HIJOS DE LA CUENTA PADRE
				liberar_bd();
				$updateNumeroHijos = 'CALL sp_sistema_update_numeroHijos_cuenta_contableId('.$cuen["padre"].', '.$ctaNumeroHijos.');';
				$upNumHijos = consulta($updateNumeroHijos);
				
				$res= $msj.cuentas_menuInicio();				
			}
			else
			{
				$error='No se ha podido eliminar la categoría.';
				$msj = sistema_mensaje("error",$error);
				$res= $msj.cuentas_menuInicio();
			}
		}
		else
		{
			$error='Esta categoría tiene subcategorias activas.';
			$msj = sistema_mensaje("error",$error);
			$res= $msj.cuentas_menuInicio();
		}
		
		return $res.$updateNumeroHijos;
	}
	
	function cuentas_nuevo_error()
	{
		switch($_POST["clasCta"])
		{
			case 1:
				$opt1 = 'checked';
			break;
			case 2:
				$opt2 = 'checked';
			break;
			case 3:
				$opt3 = 'checked';
			break;
		}
		
		switch($_POST["tipoCta"])
		{
			case 1:
				$tipo1 = 'checked';
			break;
			case 2:
				$tipo2 = 'checked';
			break;
			case 3:
				$tipo3 = 'checked';
			break;
		}
		
		$formNuevo = '	<div id="page-heading">	
							 <ol class="breadcrumb">
								  <li><a href="javascript:navegar_modulo(0);">Tablero</a></li> 
								  <li><a href="javascript:navegar_modulo('.$_SESSION["mod"].');">'.$_SESSION["moduloPadreActual"].'</a></li>    
								  <li class="active">
									  '.$_SESSION["moduloHijoActual"].'
								  </li>
							 </ol>  
							 <h1>'.$_SESSION["moduloHijoActual"].'</h1>
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
											<h4></h4>
										</div>
										<div class="panel-body" style="border-radius: 0px;">
											<div class="form-horizontal">
												<div class="form-group">
													<label for="codigoCta" class="col-sm-3 control-label">C&oacute;digo de mayor:</label>
													<div class="col-sm-6">
														<input type="text" class="form-control" id="codigoCta" name="codigoCta" maxlength="10" value="'.$_POST["codigoCta"].'"/>
													</div>
												</div>
												<div class="form-group">
													<label for="nombreCta" class="col-sm-3 control-label">Nombre:</label>
													<div class="col-sm-6">
														<input type="text" class="form-control" id="nombreCta" name="nombreCta" maxlength="100" value="'.$_POST["nombreCta"].'"/>
													</div>
												</div>
												<div class="form-group">
													<label for="clasCta" class="col-sm-3 control-label">Clasificaci&oacute;n:</label>
													<div class="col-sm-6">
														<select id="clasCta" name="clasCta" style="width:100% !important" class="selectSerch">
															<option value="1" '.$opt1.'>Activo</option>
															<option value="2" '.$opt1.'>Pasivo</option>
															<option value="3" '.$opt1.'>Capital</option>
														</select>
													</div>
												</div> 
												<div class="form-group">
													<label for="tipoCta" class="col-sm-3 control-label">Tipo:</label>
													<div class="col-sm-6">
														<select id="tipoCta" name="tipoCta" style="width:100% !important" class="selectSerch">
															<option value="1" '.$tipo1.'>Circulante</option>
															<option value="2" '.$tipo2.'>Fijo</option>
															<option value="3" '.$tipo3.'>Diferido</option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label for="descCta" class="col-sm-3 control-label">Descripci&oacute;n:</label>
													<div class="col-sm-6">
														<textarea class="form-control" id="descCta" name="descCta">'.$_POST["descCta"].'</textarea>
													</div>
												</div>                          
											</div>											
										</div>
										<div class="panel-footer">
											<div class="row">
												<div class="col-sm-12">
													<div class="btn-toolbar btnsGuarCan">
														<i class="btn-danger btn" onclick="navegar();">Cancelar</i>
														<i class="btn-success btn" onclick="nuevaCtaContable(\'Guardar\');">Guardar</i>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>';
						
		return $formNuevo;
	}
	
	function cuentas_nuevo_subcuenta_error()
	{
		$formNuevo = '	<div id="page-heading">	
							 <ol class="breadcrumb">
								  <li><a href="javascript:navegar_modulo(0);">Tablero</a></li> 
								  <li><a href="javascript:navegar_modulo('.$_SESSION["mod"].');">'.$_SESSION["moduloPadreActual"].'</a></li>    
								  <li class="active">
									  '.$_SESSION["moduloHijoActual"].'
								  </li>
							 </ol>  
							 <h1>'.$_SESSION["moduloHijoActual"].'</h1>
							 <div class="options">
								<div class="btn-toolbar"> 
									<input type="hidden" id="idCuenta" name="idCuenta" value="'.$_POST["idCuenta"].'" />
									<input type="hidden" id="clasCuenta" name="clasCuenta" value="'.$_POST["clasCuenta"].'" />
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
													<label for="codigoCta" class="col-sm-3 control-label">C&oacute;digo de mayor:</label>
													<div class="col-sm-6">
														<input type="text" class="form-control" id="codigoCta" name="codigoCta" maxlength="10" value="'.$_POST["codigoCta"].'"/>
													</div>
												</div>
												<div class="form-group">
													<label for="nombreCta" class="col-sm-3 control-label">Nombre:</label>
													<div class="col-sm-6">
														<input type="text" class="form-control" id="nombreCta" name="nombreCta" maxlength="100" value="'.$_POST["nombreCta"].'"/>
													</div>
												</div>
												<div class="form-group">
													<label for="descCta" class="col-sm-3 control-label">Descripci&oacute;n:</label>
													<div class="col-sm-6">
														<textarea class="form-control" id="descCta" name="descCta">'.$_POST["descCta"].'</textarea>
													</div>
												</div>                        
											</div>											
										</div>
										<div class="panel-footer">
											<div class="row">
												<div class="col-sm-12">
													<div class="btn-toolbar btnsGuarCan">
														<i class="btn-danger btn" onclick="navegar();">Cancelar</i>
														<i class="btn-success btn" onclick="nuevaCtaContable(\'Guardar\');">Guardar</i>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>';
						
		return $formNuevo;
	}
	
	function cuentas_editar_subcuenta_error()
	{
		$formNuevo = '	<div id="page-heading">	
							 <ol class="breadcrumb">
								  <li><a href="javascript:navegar_modulo(0);">Tablero</a></li> 
								  <li><a href="javascript:navegar_modulo('.$_SESSION["mod"].');">'.$_SESSION["moduloPadreActual"].'</a></li>    
								  <li class="active">
									  '.$_SESSION["moduloHijoActual"].'
								  </li>
							 </ol>  
							 <h1>'.$_SESSION["moduloHijoActual"].'</h1>
							 <div class="options">
								<div class="btn-toolbar"> 
									<input type="hidden" id="idCuenta" name="idCuenta" value="'.$_POST["idCuenta"].'" />
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
													<label for="codigoCta" class="col-sm-3 control-label">C&oacute;digo de mayor:</label>
													<div class="col-sm-6">
														<input type="text" class="form-control" id="codigoCta" name="codigoCta" maxlength="10" value="'.$_POST["codigoCta"].'"/>
													</div>
												</div>
												<div class="form-group">
													<label for="nombreCta" class="col-sm-3 control-label">Nombre:</label>
													<div class="col-sm-6">
														<input type="text" class="form-control" id="nombreCta" name="nombreCta" maxlength="100" value="'.$_POST["nombreCta"].'"/>
													</div>
												</div>
												<div class="form-group">
													<label for="descCta" class="col-sm-3 control-label">Descripci&oacute;n:</label>
													<div class="col-sm-6">
														<textarea class="form-control" id="descCta" name="descCta">'.$_POST["descCta"].'</textarea>
													</div>
												</div>                        
											</div>											
										</div>
										<div class="panel-footer">
											<div class="row">
												<div class="col-sm-12">
													<div class="btn-toolbar btnsGuarCan">
														<i class="btn-danger btn" onclick="navegar();">Cancelar</i>
														<i class="btn-success btn" onclick="nuevaCtaContable(\'GuardarEdit\');">Guardar</i>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>';
						
		return $formNuevo;
	}

?>