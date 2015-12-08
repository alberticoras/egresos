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
			switch($acciones["accion"])
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
		
		//CONCEPTOS
		liberar_bd();
		$selectCuentasActivo = "	SELECT ctasReg.id_ctas_registro AS id
										 , ctasReg.nivel_ctas_registro AS nivel
										 , ctasReg.padre_ctas_registro AS padre
										 , ctasReg.nombre_ctas_registro AS nombre
										 , ctasReg.estatus_ctas_registro AS estatus
									FROM
									  ctas_registro ctasReg
									WHERE
									  ctasReg.estatus_ctas_registro <> 0
									  AND ctasReg.nivel_ctas_registro = 0";
							  
		$cuentasActivo = consulta($selectCuentasActivo);
		while($ctasAct =  siguiente_registro($cuentasActivo))
		{
			$listaCtasAct .= '	<li class="dd-item dd3-item" data-id="'.$ctasAct["id"].'">
									<div class="dd-handle dd3-handle fa fa-th"></div>
									<div class="dd3-content">'.utf8_encode($ctasAct["nombre"]).'
										<div class="btn-group btnsCuentas">	';
										
										if($btnAgregar)
											$listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$ctasAct["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
										if($btnEdita)
											$listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$ctasAct["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
										if($btnElimina)
											$listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar este tipo de egreso\')){document.frmSistema.idCuenta.value='.$ctasAct["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
											
					$listaCtasAct.= '	</div>									
									</div>'; 
								
			//CHECAMOS SI TIENE HIJOS DE NIVEL 1
			liberar_bd();
			$selectHijosNivel1 = 'CALL sp_sistema_select_hijosNivel('.$ctasAct["id"].',1);';
			$hijosNivel1 = consulta($selectHijosNivel1);
			$ctaHijosNivel1 = cuenta_registros($hijosNivel1);
			if($ctaHijosNivel1 != 0)
			{
				$listaCtasAct .='<ol class="dd-list">';
				while($nivel1 = siguiente_registro($hijosNivel1))
				{
					$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel1["id"].'">
										  <div class="dd-handle dd3-handle fa fa-th"></div>
											  <div class="dd3-content">'.utf8_encode($nivel1["nombre"]).'
												  <div class="btn-group btnsCuentas">	';												  
												  if($btnAgregar)
													  $listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel1["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
												  if($btnEdita)
													  $listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel1["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
												  if($btnElimina)
													  $listaCtasAct.= '	 <i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel1["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
													  
							  $listaCtasAct.= '		</div>																					  
												</div>';
										
						//CHECAMOS SI TIENE HIJOS DE NIVEL 2
						liberar_bd();
						$selectHijosNivel2 = 'CALL sp_sistema_select_hijosNivel('.$nivel1["id"].',2);';
						$hijosNivel2 = consulta($selectHijosNivel2);
						$ctaHijosNivel2 = cuenta_registros($hijosNivel2);
						if($ctaHijosNivel2 != 0)
						{
							$listaCtasAct .='<ol class="dd-list">';
							while($nivel2 = siguiente_registro($hijosNivel2))
							{
								$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel2["id"].'">
													  <div class="dd-handle dd3-handle fa fa-th"></div>
													  <div class="dd3-content">'.utf8_encode($nivel2["nombre"]).'
														   <div class="btn-group btnsCuentas">	';
															  if($btnAgregar)
																  $listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel2["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
															  if($btnEdita)
																  $listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel2["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
															  if($btnElimina)
																  $listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel2["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
									$listaCtasAct.= '	   </div>													  
													  </div>';
												  
									//CHECAMOS SI TIENE HIJOS DE NIVEL 3
									liberar_bd();
									$selectHijosNivel3 = 'CALL sp_sistema_select_hijosNivel('.$nivel2["id"].',3);';
									$hijosNivel3 = consulta($selectHijosNivel3);
									$ctaHijosNivel3 = cuenta_registros($hijosNivel3);
									if($ctaHijosNivel3 != 0)
									{
										$listaCtasAct .='<ol class="dd-list">';
										while($nivel3 = siguiente_registro($hijosNivel3))
										{
											$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel3["id"].'">
																  <div class="dd-handle dd3-handle fa fa-th"></div>
																  <div class="dd3-content">'.utf8_encode($nivel3["nombre"]).'
																	   <div class="btn-group btnsCuentas">	';
																		  if($btnAgregar)
																			  $listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel3["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
																		  if($btnEdita)
																			  $listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel3["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																		  if($btnElimina)
																			  $listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel3["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
												$listaCtasAct.= '		</div>																  
																  </div>';
																  
												//CHECAMOS SI TIENE HIJOS DE NIVEL 4
												liberar_bd();
												$selectHijosNivel4 = 'CALL sp_sistema_select_hijosNivel('.$nivel3["id"].',4);';
												$hijosNivel4 = consulta($selectHijosNivel4);
												$ctaHijosNivel4 = cuenta_registros($hijosNivel4);
												if($ctaHijosNivel4 != 0)
												{
													$listaCtasAct .='<ol class="dd-list">';
													while($nivel4 = siguiente_registro($hijosNivel4))
													{
														$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel4["id"].'">
																			  <div class="dd-handle dd3-handle fa fa-th"></div>
																			  <div class="dd3-content">'.utf8_encode($nivel4["nombre"]).'
																				   <div class="btn-group btnsCuentas">	';
																					  if($btnAgregar)
																						  $listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel4["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
																					  if($btnEdita)
																						  $listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel4["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																					  if($btnElimina)
																						  $listaCtasAct.= '	 <i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel4["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																  $listaCtasAct.= '	</div>																			  
																			  </div>';
																			  
															//CHECAMOS SI TIENE HIJOS DE NIVEL 5
															liberar_bd();
															$selectHijosNivel5 = 'CALL sp_sistema_select_hijosNivel('.$nivel4["id"].',5);';
															$hijosNivel5 = consulta($selectHijosNivel5);
															$ctaHijosNivel5 = cuenta_registros($hijosNivel5);
															if($ctaHijosNivel5 != 0)
															{
																$listaCtasAct .='<ol class="dd-list">';
																while($nivel5 = siguiente_registro($hijosNivel5))
																{
																	$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel5["id"].'">
																						  <div class="dd-handle dd3-handle fa fa-th"></div>
																						  <div class="dd3-content">'.utf8_encode($nivel5["nombre"]).'
																							   <div class="btn-group btnsCuentas">	';
																									if($btnAgregar)
																										$listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel5["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
																									if($btnEdita)
																										$listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel5["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																									if($btnElimina)
																										$listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel5["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																			$listaCtasAct.= '	</div>	
																						  </div>';
																					  
																	//CHECAMOS SI TIENE HIJOS DE NIVEL 6
																	liberar_bd();
																	$selectHijosNivel6 = 'CALL sp_sistema_select_hijosNivel('.$nivel5["id"].',6);';
																	$hijosNivel6 = consulta($selectHijosNivel6);
																	$ctaHijosNivel6 = cuenta_registros($hijosNivel6);
																	if($ctaHijosNivel6 != 0)
																	{
																		$listaCtasAct .='<ol class="dd-list">';
																		while($nivel6 = siguiente_registro($hijosNivel6))
																		{
																			$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel6["id"].'">
																								  <div class="dd-handle dd3-handle fa fa-th"></div>
																								  <div class="dd3-content">'.utf8_encode($nivel6["nombre"]).'
																									   <div class="btn-group btnsCuentas">	';
																											if($btnAgregar)
																												$listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel6["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
																											if($btnEdita)
																												$listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel6["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																											if($btnElimina)
																												$listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel6["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																					$listaCtasAct.= '	</div>	
																								  </div>';
																			//CHECAMOS SI TIENE HIJOS DE NIVEL 7
																			liberar_bd();
																			$selectHijosNivel7 = 'CALL sp_sistema_select_hijosNivel('.$nivel6["id"].',7);';
																			$hijosNivel7 = consulta($selectHijosNivel7);
																			$ctaHijosNivel7 = cuenta_registros($hijosNivel7);
																			if($ctaHijosNivel7 != 0)
																			{
																				$listaCtasAct .='<ol class="dd-list">';
																				while($nivel7 = siguiente_registro($hijosNivel7))
																				{
																					$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel7["id"].'">
																									  <div class="dd-handle dd3-handle fa fa-th"></div>
																									  <div class="dd3-content">'.utf8_encode($nivel7["nombre"]).'
																									   <div class="btn-group btnsCuentas">	';
																											if($btnAgregar)
																												$listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel7["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
																											if($btnEdita)
																												$listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel7["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																											if($btnElimina)
																												$listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel7["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																					  $listaCtasAct.= '	</div>
																									 </div>';
																									 
																					//CHECAMOS SI TIENE HIJOS DE NIVEL 8
																					liberar_bd();
																					$selectHijosNivel8 = 'CALL sp_sistema_select_hijosNivel('.$nivel7["id"].',8);';
																					$hijosNivel8 = consulta($selectHijosNivel8);
																					$ctaHijosNivel8 = cuenta_registros($hijosNivel8);
																					if($ctaHijosNivel8 != 0)
																					{
																						$listaCtasAct .='<ol class="dd-list">';
																						while($nivel8 = siguiente_registro($hijosNivel8))
																						{
																							$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel8["id"].'">
																												  <div class="dd-handle dd3-handle fa fa-th"></div>
																												  <div class="dd3-content">'.utf8_encode($nivel8["nombre"]).'
																													   <div class="btn-group btnsCuentas">	';
																															if($btnAgregar)
																																$listaCtasAct.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel8["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
																															if($btnEdita)
																																$listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel8["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																															if($btnElimina)
																																$listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel8["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
																									$listaCtasAct.= '	</div>	
																											  		</div>';
																													
																							//CHECAMOS SI TIENE HIJOS DE NIVEL 9
																							liberar_bd();
																							$selectHijosNivel9 = 'CALL sp_sistema_select_hijosNivel('.$nivel8["id"].',9);';
																							$hijosNivel9 = consulta($selectHijosNivel9);
																							$ctaHijosNivel9 = cuenta_registros($hijosNivel9);
																							if($ctaHijosNivel9 != 0)
																							{
																								$listaCtasAct .='<ol class="dd-list">';
																								while($nivel9 = siguiente_registro($hijosNivel9))
																								{
																									$listaCtasAct .= '<li class="dd-item dd3-item" data-id="'.$nivel9["id"].'">
																														  <div class="dd-handle dd3-handle fa fa-th"></div>
																														  <div class="dd3-content">'.utf8_encode($nivel9["nombre"]).'
																															   <div class="btn-group btnsCuentas">	';
																																   if($btnEdita)
																																		$listaCtasAct.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel9["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
																																	if($btnElimina)
																																		$listaCtasAct.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel9["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
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
		}
		
		////CUENTAS DE PASIVO
//		liberar_bd();
//		$selectCuentasPasivo = "	SELECT ctasReg.id_ctas_registro AS id
//										 , ctasReg.nivel_ctas_registro AS nivel
//										 , ctasReg.padre_ctas_registro AS padre
//										 , ctasReg.nombre_ctas_registro AS nombre
//										 , ctasReg.estatus_ctas_registro AS estatus
//									FROM
//									  ctas_registro ctasReg
//									WHERE
//									  ctasReg.estatus_ctas_registro <> 0
//									  AND ctasReg.tipo_ctas_registro = 2
//									  AND ctasReg.nivel_ctas_registro = 0";
//							  
//		$cuentasPasivo = consulta($selectCuentasPasivo);
//		while($ctasPas =  siguiente_registro($cuentasPasivo))
//		{
//			$listaCtasPas .= '	<li class="dd-item dd3-item" data-id="'.$ctasPas["id"].'">
//									<div class="dd-handle dd3-handle fa fa-th"></div>
//									<div class="dd3-content">
//										'.utf8_encode($ctasPas["nombre"]).'
//										<div class="btn-group btnsCuentas">	';
//										
//										if($btnAgregar)
//											$listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$ctasPas["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//										if($btnEdita)
//											$listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$ctasPas["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//										if($btnElimina)
//											$listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar este tipo de egreso\')){document.frmSistema.idCuenta.value='.$ctasPas["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//											
//					$listaCtasPas.= '	</div>									
//									</div>'; 
//								
//			//CHECAMOS SI TIENE HIJOS DE NIVEL 1
//			liberar_bd();
//			$selectHijosNivel1 = 'CALL sp_sistema_select_hijosNivel('.$ctasPas["id"].',1);';
//			$hijosNivel1 = consulta($selectHijosNivel1);
//			$ctaHijosNivel1 = cuenta_registros($hijosNivel1);
//			if($ctaHijosNivel1 != 0)
//			{
//				$listaCtasPas .='<ol class="dd-list">';
//				while($nivel1 = siguiente_registro($hijosNivel1))
//				{
//					$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel1["id"].'">
//										  <div class="dd-handle dd3-handle fa fa-th"></div>
//											  <div class="dd3-content">
//												  '.utf8_encode($nivel1["nombre"]).'
//												  <div class="btn-group btnsCuentas">	';
//												  
//												  if($btnAgregar)
//													  $listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel1["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//												  if($btnEdita)
//													  $listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel1["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//												  if($btnElimina)
//													  $listaCtasPas.= '	 <i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel1["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//													  
//							  $listaCtasPas.= '		</div>																					  
//												</div>';
//										
//						//CHECAMOS SI TIENE HIJOS DE NIVEL 2
//						liberar_bd();
//						$selectHijosNivel2 = 'CALL sp_sistema_select_hijosNivel('.$nivel1["id"].',2);';
//						$hijosNivel2 = consulta($selectHijosNivel2);
//						$ctaHijosNivel2 = cuenta_registros($hijosNivel2);
//						if($ctaHijosNivel2 != 0)
//						{
//							$listaCtasPas .='<ol class="dd-list">';
//							while($nivel2 = siguiente_registro($hijosNivel2))
//							{
//								$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel2["id"].'">
//													  <div class="dd-handle dd3-handle fa fa-th"></div>
//													  <div class="dd3-content">
//														  '.utf8_encode($nivel2["nombre"]).'
//														   <div class="btn-group btnsCuentas">	';
//															  if($btnAgregar)
//																  $listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel2["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//															  if($btnEdita)
//																  $listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel2["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//															  if($btnElimina)
//																  $listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel2["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//									$listaCtasPas.= '	   </div>													  
//													  </div>';
//												  
//									//CHECAMOS SI TIENE HIJOS DE NIVEL 3
//									liberar_bd();
//									$selectHijosNivel3 = 'CALL sp_sistema_select_hijosNivel('.$nivel2["id"].',3);';
//									$hijosNivel3 = consulta($selectHijosNivel3);
//									$ctaHijosNivel3 = cuenta_registros($hijosNivel3);
//									if($ctaHijosNivel3 != 0)
//									{
//										$listaCtasPas .='<ol class="dd-list">';
//										while($nivel3 = siguiente_registro($hijosNivel3))
//										{
//											$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel3["id"].'">
//																  <div class="dd-handle dd3-handle fa fa-th"></div>
//																  <div class="dd3-content">
//																	  '.utf8_encode($nivel3["nombre"]).'
//																	   <div class="btn-group btnsCuentas">	';
//																		  if($btnAgregar)
//																			  $listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel3["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//																		  if($btnEdita)
//																			  $listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel3["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//																		  if($btnElimina)
//																			  $listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel3["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//												$listaCtasPas.= '		</div>																  
//																  </div>';
//																  
//												//CHECAMOS SI TIENE HIJOS DE NIVEL 4
//												liberar_bd();
//												$selectHijosNivel4 = 'CALL sp_sistema_select_hijosNivel('.$nivel3["id"].',4);';
//												$hijosNivel4 = consulta($selectHijosNivel4);
//												$ctaHijosNivel4 = cuenta_registros($hijosNivel4);
//												if($ctaHijosNivel4 != 0)
//												{
//													$listaCtasPas .='<ol class="dd-list">';
//													while($nivel4 = siguiente_registro($hijosNivel4))
//													{
//														$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel4["id"].'">
//																			  <div class="dd-handle dd3-handle fa fa-th"></div>
//																			  <div class="dd3-content">
//																				  '.utf8_encode($nivel4["nombre"]).'
//																				   <div class="btn-group btnsCuentas">	';
//																					  if($btnAgregar)
//																						  $listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel4["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//																					  if($btnEdita)
//																						  $listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel4["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//																					  if($btnElimina)
//																						  $listaCtasPas.= '	 <i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel4["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//																  $listaCtasPas.= '	</div>																			  
//																			  </div>';
//																			  
//															//CHECAMOS SI TIENE HIJOS DE NIVEL 5
//															liberar_bd();
//															$selectHijosNivel5 = 'CALL sp_sistema_select_hijosNivel('.$nivel4["id"].',5);';
//															$hijosNivel5 = consulta($selectHijosNivel5);
//															$ctaHijosNivel5 = cuenta_registros($hijosNivel5);
//															if($ctaHijosNivel5 != 0)
//															{
//																$listaCtasPas .='<ol class="dd-list">';
//																while($nivel5 = siguiente_registro($hijosNivel5))
//																{
//																	$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel5["id"].'">
//																						  <div class="dd-handle dd3-handle fa fa-th"></div>
//																						  <div class="dd3-content">
//																							  '.utf8_encode($nivel5["nombre"]).'
//																							   <div class="btn-group btnsCuentas">	';
//																									if($btnAgregar)
//																										$listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel5["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//																									if($btnEdita)
//																										$listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel5["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//																									if($btnElimina)
//																										$listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel5["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//																			$listaCtasPas.= '	</div>	
//																						  </div>';
//																					  
//																	//CHECAMOS SI TIENE HIJOS DE NIVEL 6
//																	liberar_bd();
//																	$selectHijosNivel6 = 'CALL sp_sistema_select_hijosNivel('.$nivel5["id"].',6);';
//																	$hijosNivel6 = consulta($selectHijosNivel6);
//																	$ctaHijosNivel6 = cuenta_registros($hijosNivel6);
//																	if($ctaHijosNivel6 != 0)
//																	{
//																		$listaCtasPas .='<ol class="dd-list">';
//																		while($nivel6 = siguiente_registro($hijosNivel6))
//																		{
//																			$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel6["id"].'">
//																								  <div class="dd-handle dd3-handle fa fa-th"></div>
//																								  <div class="dd3-content">
//																									  '.utf8_encode($nivel6["nombre"]).'
//																									   <div class="btn-group btnsCuentas">	';
//																											if($btnAgregar)
//																												$listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel6["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//																											if($btnEdita)
//																												$listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel6["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//																											if($btnElimina)
//																												$listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel6["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//																					$listaCtasPas.= '	</div>	
//																								  </div>';
//																			//CHECAMOS SI TIENE HIJOS DE NIVEL 7
//																			liberar_bd();
//																			$selectHijosNivel7 = 'CALL sp_sistema_select_hijosNivel('.$nivel6["id"].',7);';
//																			$hijosNivel7 = consulta($selectHijosNivel7);
//																			$ctaHijosNivel7 = cuenta_registros($hijosNivel7);
//																			if($ctaHijosNivel7 != 0)
//																			{
//																				$listaCtasPas .='<ol class="dd-list">';
//																				while($nivel7 = siguiente_registro($hijosNivel7))
//																				{
//																					$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel7["id"].'">
//																									  <div class="dd-handle dd3-handle fa fa-th"></div>
//																									  <div class="dd3-content">
//																									  '.utf8_encode($nivel7["nombre"]).'
//																									   <div class="btn-group btnsCuentas">	';
//																											if($btnAgregar)
//																												$listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel7["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//																											if($btnEdita)
//																												$listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel7["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//																											if($btnElimina)
//																												$listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel7["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//																					  $listaCtasPas.= '	</div>
//																									 </div>';
//																									 
//																					//CHECAMOS SI TIENE HIJOS DE NIVEL 8
//																					liberar_bd();
//																					$selectHijosNivel8 = 'CALL sp_sistema_select_hijosNivel('.$nivel7["id"].',8);';
//																					$hijosNivel8 = consulta($selectHijosNivel8);
//																					$ctaHijosNivel8 = cuenta_registros($hijosNivel8);
//																					if($ctaHijosNivel8 != 0)
//																					{
//																						$listaCtasPas .='<ol class="dd-list">';
//																						while($nivel8 = siguiente_registro($hijosNivel8))
//																						{
//																							$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel8["id"].'">
//																												  <div class="dd-handle dd3-handle fa fa-th"></div>
//																												  <div class="dd3-content">
//																													  '.utf8_encode($nivel8["nombre"]).'
//																													   <div class="btn-group btnsCuentas">	';
//																															if($btnAgregar)
//																																$listaCtasPas.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel8["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//																															if($btnEdita)
//																																$listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel8["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//																															if($btnElimina)
//																																$listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel8["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//																									$listaCtasPas.= '	</div>	
//																											  		</div>';
//																													
//																							//CHECAMOS SI TIENE HIJOS DE NIVEL 9
//																							liberar_bd();
//																							$selectHijosNivel9 = 'CALL sp_sistema_select_hijosNivel('.$nivel8["id"].',9);';
//																							$hijosNivel9 = consulta($selectHijosNivel9);
//																							$ctaHijosNivel9 = cuenta_registros($hijosNivel9);
//																							if($ctaHijosNivel9 != 0)
//																							{
//																								$listaCtasPas .='<ol class="dd-list">';
//																								while($nivel9 = siguiente_registro($hijosNivel9))
//																								{
//																									$listaCtasPas .= '<li class="dd-item dd3-item" data-id="'.$nivel9["id"].'">
//																														  <div class="dd-handle dd3-handle fa fa-th"></div>
//																														  <div class="dd3-content">
//																															  '.utf8_encode($nivel9["nombre"]).'
//																															   <div class="btn-group btnsCuentas">	';
//																																   if($btnEdita)
//																																		$listaCtasPas.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel9["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//																																	if($btnElimina)
//																																		$listaCtasPas.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel9["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//																											$listaCtasPas.= '	</div>																															
//																													  		</div>';																						
//																									$listaCtasPas .= '</li>';
//																								}
//																								$listaCtasPas .='</ol>';				
//																							}
//																							$listaCtasPas .= '</li>';
//																						}
//																						$listaCtasPas .='</ol>';				
//																					}
//																					$listaCtasPas .= '</li>';
//																				}
//																				$listaCtasPas .='</ol>';				
//																			}
//																			$listaCtasPas .= '</li>';
//																		}
//																		$listaCtasPas .='</ol>';				
//																	}
//																	$listaCtasPas .= '</li>';
//																}
//																$listaCtasPas .='</ol>';				
//															}
//														$listaCtasPas .= '</li>';
//													}
//													$listaCtasPas .='</ol>';				
//												}
//											$listaCtasPas .= '</li>';
//										}
//										$listaCtasPas .='</ol>';				
//									}
//								$listaCtasPas .= '</li>';
//							}
//							$listaCtasPas .='</ol>';				
//						}			
//					$listaCtasPas .= '</li>';
//				}
//				$listaCtasPas .='</ol>';				
//			}			
//			$listaCtasPas .= '</li>';			
//		}
//		
//		//CUENTAS DE CAPITAL
//		liberar_bd();
//		$selectCuentasCapital = "	SELECT ctasReg.id_ctas_registro AS id
//										 , ctasReg.nivel_ctas_registro AS nivel
//										 , ctasReg.padre_ctas_registro AS padre
//										 , ctasReg.nombre_ctas_registro AS nombre
//										 , ctasReg.estatus_ctas_registro AS estatus
//									FROM
//									  ctas_registro ctasReg
//									WHERE
//									  ctasReg.estatus_ctas_registro <> 0
//									  AND ctasReg.tipo_ctas_registro = 3
//									  AND ctasReg.nivel_ctas_registro = 0";
//							  
//		$cuentasCapital = consulta($selectCuentasCapital);
//		while($ctasCap =  siguiente_registro($cuentasCapital))
//		{
//			$listaCtasCap .= '	<li class="dd-item dd3-item" data-id="'.$ctasCap["id"].'">
//									<div class="dd-handle dd3-handle fa fa-th"></div>
//									<div class="dd3-content">
//										'.utf8_encode($ctasCap["nombre"]).'
//										<div class="btn-group btnsCuentas">	';
//										
//										if($btnAgregar)
//											$listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$ctasCap["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//										if($btnEdita)
//											$listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$ctasCap["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//										if($btnElimina)
//											$listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar este tipo de egreso\')){document.frmSistema.idCuenta.value='.$ctasCap["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//											
//					$listaCtasCap.= '	</div>									
//									</div>'; 
//								
//			//CHECAMOS SI TIENE HIJOS DE NIVEL 1
//			liberar_bd();
//			$selectHijosNivel1 = 'CALL sp_sistema_select_hijosNivel('.$ctasCap["id"].',1);';
//			$hijosNivel1 = consulta($selectHijosNivel1);
//			$ctaHijosNivel1 = cuenta_registros($hijosNivel1);
//			if($ctaHijosNivel1 != 0)
//			{
//				$listaCtasCap .='<ol class="dd-list">';
//				while($nivel1 = siguiente_registro($hijosNivel1))
//				{
//					$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel1["id"].'">
//										  <div class="dd-handle dd3-handle fa fa-th"></div>
//											  <div class="dd3-content">
//												  '.utf8_encode($nivel1["nombre"]).'
//												  <div class="btn-group btnsCuentas">	';
//												  
//												  if($btnAgregar)
//													  $listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel1["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//												  if($btnEdita)
//													  $listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel1["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//												  if($btnElimina)
//													  $listaCtasCap.= '	 <i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel1["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//													  
//							  $listaCtasCap.= '		</div>																					  
//												</div>';
//										
//						//CHECAMOS SI TIENE HIJOS DE NIVEL 2
//						liberar_bd();
//						$selectHijosNivel2 = 'CALL sp_sistema_select_hijosNivel('.$nivel1["id"].',2);';
//						$hijosNivel2 = consulta($selectHijosNivel2);
//						$ctaHijosNivel2 = cuenta_registros($hijosNivel2);
//						if($ctaHijosNivel2 != 0)
//						{
//							$listaCtasCap .='<ol class="dd-list">';
//							while($nivel2 = siguiente_registro($hijosNivel2))
//							{
//								$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel2["id"].'">
//													  <div class="dd-handle dd3-handle fa fa-th"></div>
//													  <div class="dd3-content">
//														  '.utf8_encode($nivel2["nombre"]).'
//														   <div class="btn-group btnsCuentas">	';
//															  if($btnAgregar)
//																  $listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel2["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//															  if($btnEdita)
//																  $listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel2["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//															  if($btnElimina)
//																  $listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel2["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//									$listaCtasCap.= '	   </div>													  
//													  </div>';
//												  
//									//CHECAMOS SI TIENE HIJOS DE NIVEL 3
//									liberar_bd();
//									$selectHijosNivel3 = 'CALL sp_sistema_select_hijosNivel('.$nivel2["id"].',3);';
//									$hijosNivel3 = consulta($selectHijosNivel3);
//									$ctaHijosNivel3 = cuenta_registros($hijosNivel3);
//									if($ctaHijosNivel3 != 0)
//									{
//										$listaCtasCap .='<ol class="dd-list">';
//										while($nivel3 = siguiente_registro($hijosNivel3))
//										{
//											$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel3["id"].'">
//																  <div class="dd-handle dd3-handle fa fa-th"></div>
//																  <div class="dd3-content">
//																	  '.utf8_encode($nivel3["nombre"]).'
//																	   <div class="btn-group btnsCuentas">	';
//																		  if($btnAgregar)
//																			  $listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel3["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//																		  if($btnEdita)
//																			  $listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel3["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//																		  if($btnElimina)
//																			  $listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel3["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//												$listaCtasCap.= '		</div>																  
//																  </div>';
//																  
//												//CHECAMOS SI TIENE HIJOS DE NIVEL 4
//												liberar_bd();
//												$selectHijosNivel4 = 'CALL sp_sistema_select_hijosNivel('.$nivel3["id"].',4);';
//												$hijosNivel4 = consulta($selectHijosNivel4);
//												$ctaHijosNivel4 = cuenta_registros($hijosNivel4);
//												if($ctaHijosNivel4 != 0)
//												{
//													$listaCtasCap .='<ol class="dd-list">';
//													while($nivel4 = siguiente_registro($hijosNivel4))
//													{
//														$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel4["id"].'">
//																			  <div class="dd-handle dd3-handle fa fa-th"></div>
//																			  <div class="dd3-content">
//																				  '.utf8_encode($nivel4["nombre"]).'
//																				   <div class="btn-group btnsCuentas">	';
//																					  if($btnAgregar)
//																						  $listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel4["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//																					  if($btnEdita)
//																						  $listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel4["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//																					  if($btnElimina)
//																						  $listaCtasCap.= '	 <i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel4["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//																  $listaCtasCap.= '	</div>																			  
//																			  </div>';
//																			  
//															//CHECAMOS SI TIENE HIJOS DE NIVEL 5
//															liberar_bd();
//															$selectHijosNivel5 = 'CALL sp_sistema_select_hijosNivel('.$nivel4["id"].',5);';
//															$hijosNivel5 = consulta($selectHijosNivel5);
//															$ctaHijosNivel5 = cuenta_registros($hijosNivel5);
//															if($ctaHijosNivel5 != 0)
//															{
//																$listaCtasCap .='<ol class="dd-list">';
//																while($nivel5 = siguiente_registro($hijosNivel5))
//																{
//																	$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel5["id"].'">
//																						  <div class="dd-handle dd3-handle fa fa-th"></div>
//																						  <div class="dd3-content">
//																							  '.utf8_encode($nivel5["nombre"]).'
//																							   <div class="btn-group btnsCuentas">	';
//																									if($btnAgregar)
//																										$listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel5["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//																									if($btnEdita)
//																										$listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel5["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//																									if($btnElimina)
//																										$listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel5["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//																			$listaCtasCap.= '	</div>	
//																						  </div>';
//																					  
//																	//CHECAMOS SI TIENE HIJOS DE NIVEL 6
//																	liberar_bd();
//																	$selectHijosNivel6 = 'CALL sp_sistema_select_hijosNivel('.$nivel5["id"].',6);';
//																	$hijosNivel6 = consulta($selectHijosNivel6);
//																	$ctaHijosNivel6 = cuenta_registros($hijosNivel6);
//																	if($ctaHijosNivel6 != 0)
//																	{
//																		$listaCtasCap .='<ol class="dd-list">';
//																		while($nivel6 = siguiente_registro($hijosNivel6))
//																		{
//																			$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel6["id"].'">
//																								  <div class="dd-handle dd3-handle fa fa-th"></div>
//																								  <div class="dd3-content">
//																									  '.utf8_encode($nivel6["nombre"]).'
//																									   <div class="btn-group btnsCuentas">	';
//																											if($btnAgregar)
//																												$listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel6["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//																											if($btnEdita)
//																												$listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel6["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//																											if($btnElimina)
//																												$listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel6["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//																					$listaCtasCap.= '	</div>	
//																								  </div>';
//																			//CHECAMOS SI TIENE HIJOS DE NIVEL 7
//																			liberar_bd();
//																			$selectHijosNivel7 = 'CALL sp_sistema_select_hijosNivel('.$nivel6["id"].',7);';
//																			$hijosNivel7 = consulta($selectHijosNivel7);
//																			$ctaHijosNivel7 = cuenta_registros($hijosNivel7);
//																			if($ctaHijosNivel7 != 0)
//																			{
//																				$listaCtasCap .='<ol class="dd-list">';
//																				while($nivel7 = siguiente_registro($hijosNivel7))
//																				{
//																					$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel7["id"].'">
//																									  <div class="dd-handle dd3-handle fa fa-th"></div>
//																									  <div class="dd3-content">
//																									  '.utf8_encode($nivel7["nombre"]).'
//																									   <div class="btn-group btnsCuentas">	';
//																											if($btnAgregar)
//																												$listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel7["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//																											if($btnEdita)
//																												$listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel7["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//																											if($btnElimina)
//																												$listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel7["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//																					  $listaCtasCap.= '	</div>
//																									 </div>';
//																									 
//																					//CHECAMOS SI TIENE HIJOS DE NIVEL 8
//																					liberar_bd();
//																					$selectHijosNivel8 = 'CALL sp_sistema_select_hijosNivel('.$nivel7["id"].',8);';
//																					$hijosNivel8 = consulta($selectHijosNivel8);
//																					$ctaHijosNivel8 = cuenta_registros($hijosNivel8);
//																					if($ctaHijosNivel8 != 0)
//																					{
//																						$listaCtasCap .='<ol class="dd-list">';
//																						while($nivel8 = siguiente_registro($hijosNivel8))
//																						{
//																							$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel8["id"].'">
//																												  <div class="dd-handle dd3-handle fa fa-th"></div>
//																												  <div class="dd3-content">
//																													  '.utf8_encode($nivel8["nombre"]).'
//																													   <div class="btn-group btnsCuentas">	';
//																															if($btnAgregar)
//																																$listaCtasCap.= '	<i title="Agregar" class="fa fa-plus-circle" onClick="document.frmSistema.idCuenta.value='.$nivel8["id"].';navegar(\'Agregar\');" style="cursor:pointer;"></i>';		
//																															if($btnEdita)
//																																$listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel8["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//																															if($btnElimina)
//																																$listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel8["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//																									$listaCtasCap.= '	</div>	
//																											  		</div>';
//																													
//																							//CHECAMOS SI TIENE HIJOS DE NIVEL 9
//																							liberar_bd();
//																							$selectHijosNivel9 = 'CALL sp_sistema_select_hijosNivel('.$nivel8["id"].',9);';
//																							$hijosNivel9 = consulta($selectHijosNivel9);
//																							$ctaHijosNivel9 = cuenta_registros($hijosNivel9);
//																							if($ctaHijosNivel9 != 0)
//																							{
//																								$listaCtasCap .='<ol class="dd-list">';
//																								while($nivel9 = siguiente_registro($hijosNivel9))
//																								{
//																									$listaCtasCap .= '<li class="dd-item dd3-item" data-id="'.$nivel9["id"].'">
//																														  <div class="dd-handle dd3-handle fa fa-th"></div>
//																														  <div class="dd3-content">
//																															  '.utf8_encode($nivel9["nombre"]).'
//																															   <div class="btn-group btnsCuentas">	';
//																																   if($btnEdita)
//																																		$listaCtasCap.= '	<i title="Editar" class="fa fa-pencil" onClick="document.frmSistema.idCuenta.value='.$nivel9["id"].';navegar(\'Editar\');" style="cursor:pointer;"></i>';		
//																																	if($btnElimina)
//																																		$listaCtasCap.= '	<i title="Eliminar" class="fa fa-times-circle" onclick="if(confirm(\'Desea eliminar esta cuenta\')){document.frmSistema.idCuenta.value='.$nivel9["id"].'; navegar(\'Eliminar\')}" style="cursor:pointer;"></i>';	
//																											$listaCtasCap.= '	</div>																															
//																													  		</div>';																						
//																									$listaCtasCap .= '</li>';
//																								}
//																								$listaCtasCap .='</ol>';				
//																							}
//																							$listaCtasCap .= '</li>';
//																						}
//																						$listaCtasCap .='</ol>';				
//																					}
//																					$listaCtasCap .= '</li>';
//																				}
//																				$listaCtasCap .='</ol>';				
//																			}
//																			$listaCtasCap .= '</li>';
//																		}
//																		$listaCtasCap .='</ol>';				
//																	}
//																	$listaCtasCap .= '</li>';
//																}
//																$listaCtasCap .='</ol>';				
//															}
//														$listaCtasCap .= '</li>';
//													}
//													$listaCtasCap .='</ol>';				
//												}
//											$listaCtasCap .= '</li>';
//										}
//										$listaCtasCap .='</ol>';				
//									}
//								$listaCtasCap .= '</li>';
//							}
//							$listaCtasCap .='</ol>';				
//						}			
//					$listaCtasCap .= '</li>';
//				}
//				$listaCtasCap .='</ol>';				
//			}			
//			$listaCtasCap .= '</li>';			
//		}
		
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
								<input type="hidden" id="idCuenta" name="idCuenta" value="" />
								<input type="hidden" name="txtIndice" />';
								if($btnAlta)
									$pagina.= '	<i title="Nuevo tipo de egreso" style="cursor:pointer;" onclick="navegar(\'Nuevo\')" class="btn btn-warning" >
														Nuevo tipo de egreso
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
										<div class="dd" id="nestable_list_1">
											<ol class="dd-list">
												'.$listaCtasAct.'
											</ol>
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
											<h3>Datos generales</h3>
											<div class="form-horizontal">
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
											<hr class="divider"/>
											<h3>Datos extras</h3>	
											<div class="form-horizontal">	
												<div class="form-group">
													<label class="col-sm-3 control-label">Periodo</label>
													<div class="col-sm-6">
														<label class="radio-inline">
															<input id="periCta" type="radio" value="0" checked="checked" name="periCta">Sin captura
														</label>
														<label class="radio-inline">
															<input id="periCta" type="radio" value="1" name="periCta">Obligatorio
														</label>
														<label class="radio-inline">
															<input id="periCta" type="radio" value="2" name="periCta">No obligatorio
														</label>
													</div>
												</div>
												<div class="form-group" style="display:none;">
													<label class="col-sm-3 control-label">Factor Iva</label>
													<div class="col-sm-6">
														<div class="input-group">
															<input class="form-control" type="text" name="factorCta" id="factorCta" placeholder="0.00" value="0.00">
															<span class="input-group-addon">%</span>
														</div>
													</div>
												</div>
											</div>											
										</div>
										<div class="panel-footer">
											<div class="row">
												<div class="col-sm-12">
													<div class="btn-toolbar btnsGuarCan">
														<i class="btn-danger btn" onclick="navegar();">Cancelar</i>
														<i class="btn-success btn" onclick="nuevoConcepto(\'Guardar\');">Guardar</i>
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
	
	function cuentas_formularioNuevaSub()
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
											<h3>Datos generales</h3>
											<div class="form-horizontal">
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
											<hr class="divider"/>
											<h3>Datos extras</h3>	
											<div class="form-horizontal">												
												<div class="form-group">
													<label class="col-sm-3 control-label">Periodo</label>
													<div class="col-sm-6">
														<label class="radio-inline">
															<input id="periCta" type="radio" value="0" checked="checked" name="periCta">Sin captura
														</label>
														<label class="radio-inline">
															<input id="periCta" type="radio" value="1" name="periCta">Obligatorio
														</label>
														<label class="radio-inline">
															<input id="periCta" type="radio" value="2" name="periCta">No obligatorio
														</label>
													</div>
												</div>
												<div class="form-group" style="display:none">
													<label class="col-sm-3 control-label">Factor Iva</label>
													<div class="col-sm-6">
														<div class="input-group">
															<input class="form-control" type="text" name="factorCta" id="factorCta" placeholder="0.00" value="0.00">
															<span class="input-group-addon">%</span>
														</div>
													</div>
												</div>                            
											</div>											
										</div>
										<div class="panel-footer">
											<div class="row">
												<div class="col-sm-12">
													<div class="btn-toolbar btnsGuarCan">
														<i class="btn-danger btn" onclick="navegar();">Cancelar</i>
														<i class="btn-success btn" onclick="nuevoConcepto(\'GuardarAgregar\');">Guardar</i>
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
		  liberar_bd();
		  $selectCuenta =  "CALL sp_sistema_select_cuenta_nombre('".utf8_decode($_POST["nombreCta"])."');";
		  $cuenta = consulta($selectCuenta);
		  $ctaCuenta = cuenta_registros($cuenta);
		  if($ctaCuenta == 0)
		  {
			  if($_POST["factorCta"] == "")
			  	$factorCta = 0.00;
			  else
			  	$factorCta = $_POST["factorCta"];
				
			  liberar_bd();
			  $insertCuenta = " CALL sp_sistema_insert_cuenta_registro(	'".utf8_decode($_POST["nombreCta"])."',
			  															'".utf8_decode($_POST["descCta"])."',
																		'".$_POST["periCta"]."',
																		".$factorCta.",
			  															'".$_POST["saldoCta"]."',
			  															'".$_POST["saldoCta2"]."',
																		".$_SESSION[$varIdUser].");";
			  $insert = consulta($insertCuenta);

			  if($insert)
			  {
				  //ULTIMO CONCEPTO INSERTADO
				  liberar_bd();
				  $selectUltimoConcepto = 'CALL sp_sistema_select_ultimo_concepto('.$_SESSION[$varIdUser].');';
				  $ultimoConcepto = consulta($selectUltimoConcepto);
				  $ultConcep = siguiente_registro($ultimoConcepto);
				  				  
				  //INSERTAMOS UN REGISTRO EL EL ARBOL 
				  liberar_bd();
			  	  $insertArbolReg = 'CALL sp_sistema_insert_reg_arbol('.$ultConcep["id"].', '.$_SESSION[$varIdUser].');';
				  $arbolReg = consulta($insertArbolReg);
			  
			  	  $res= $msj.cuentas_menuInicio();
			  }
			  else
			  {
                  $error='No se ha podido guardar el tipo de egreso.';
				  $msj = sistema_mensaje("error",$error);
				  $pagina = cuentas_error_nuevo();				  							
			  	  $res= $msj.$pagina;									
			  }
		  }
		  else
		  {
			  $error='Ya existe un tipo de egreso con este nombre.';
			  $msj = sistema_mensaje("error",$error);
			  $pagina = cuentas_error_nuevo();							
			  $res= $msj.$pagina;
		  }
		
		return $res;
	}
	
	function cuentas_guardarsubCuenta()
	{	
		liberar_bd();
		$selectCuenta =  "CALL sp_sistema_select_cuenta_nombre('".utf8_decode($_POST["nombreCta"])."');";
		$cuenta = consulta($selectCuenta);
		$ctaCuenta = cuenta_registros($cuenta);		  
		if($ctaCuenta == 0)
		{
			 if($_POST["factorCta"] == "")
			  	$factorCta = 0.00;
			  else
			  	$factorCta = $_POST["factorCta"];
				
			//DATOS DE LA SUBCUENTA
			liberar_bd();
			$selectDatosCuenta = 'CALL sp_sistema_select_datos_subcuenta('.$_POST["idCuenta"].');';
			$datosCuenta = consulta($selectDatosCuenta);
			$cuen = siguiente_registro($datosCuenta);
			$nivel = $cuen["nivel"] + 1;
			
			//INSERTAMOS LA SUBCUENTA
			liberar_bd();
			$insertCuenta = " CALL sp_sistema_insert_subcuenta(	'".utf8_decode($_POST["nombreCta"])."',
																'".utf8_decode($_POST["descCta"])."',
																'".$_POST["periCta"]."',
																".$factorCta.",
																".$_POST["idCuenta"].", 
																".$nivel.",
																".$_SESSION[$varIdUser].");";								  
			$insert = consulta($insertCuenta);
			
			if($insert)
			{
				//ULTIMO CONCEPTO INSERTADO
				liberar_bd();
				$selectUltimoConcepto = 'CALL sp_sistema_select_ultimo_concepto('.$_SESSION[$varIdUser].');';
				$ultimoConcepto = consulta($selectUltimoConcepto);
				$ultConcep = siguiente_registro($ultimoConcepto);
				
				//NUMERO DE HIJOS DE LA CUENTA PADRE
				liberar_bd();
				$selectNumeroHijos = 'CALL sp_sistema_select_numHijos_cuenta('.$_POST["idCuenta"].');';
				$numeroHijos = consulta($selectNumeroHijos);
				$hijos = siguiente_registro($numeroHijos);
				$ctaNumeroHijos = $hijos["hijos"] + 1;
				
				//ACTUALIZAMOS NUMERO DE HIJOS DE LA CUENTA PADRE
				liberar_bd();
				$updateNumeroHijos = 'CALL sp_sistema_update_numeroHijos_cuenta('.$_POST["idCuenta"].', '.$ctaNumeroHijos.');';
				$upNumHijos = consulta($updateNumeroHijos);
								
				//DATOS DEL ARBOL DEL PADRE
				liberar_bd();
				$selectDatosArbol = 'CALL sp_sistema_select_datos_arbol_cuentaId('.$_POST["idCuenta"].');';
				$datosArbol = consulta($selectDatosArbol);
				$datAr = siguiente_registro($datosArbol);
				
				if($cuen["nivel"] == 0)
					$n1 = $_POST["idCuenta"];
				else
					$n1 = $datAr["n1"];
					
				if($cuen["nivel"] == 1)
					$n2 = $_POST["idCuenta"];
				else
					$n2 = $datAr["n2"];
				
				if($cuen["nivel"] == 2)
					$n3 = $_POST["idCuenta"];
				else
					$n3 = $datAr["n3"];
					
				if($cuen["nivel"] == 3)
					$n4 = $_POST["idCuenta"];
				else
					$n4 = $datAr["n4"];
					
				if($cuen["nivel"] == 4)
					$n5 = $_POST["idCuenta"];
				else
					$n5 = $datAr["n5"];
					
				if($cuen["nivel"] == 5)
					$n6 = $_POST["idCuenta"];
				else
					$n6 = $datAr["n6"];
				
				if($cuen["nivel"] == 6)
					$n7 = $_POST["idCuenta"];
				else
					$n7 = $datAr["n7"];
					
				if($cuen["nivel"] == 7)
					$n8 = $_POST["idCuenta"];
				else
					$n8 = $datAr["n8"];
					
				if($cuen["nivel"] == 8)
					$n9 = $_POST["idCuenta"];
				else
					$n9 = $datAr["n9"];
				
				//INSERTAMOS HIJO ARBOL CTAS
				liberar_bd();
				$insertHijoArbol = 'CALL sp_sistema_insert_hijo_reg_arbol(	'.$ultConcep["id"].', 
																			"'.$n1.'",
																			"'.$n2.'",
																			"'.$n3.'",
																			"'.$n4.'",
																			"'.$n5.'",
																			"'.$n6.'",
																			"'.$n7.'",
																			"'.$n8.'",
																			"'.$n9.'",
																			'.$_SESSION[$varIdUser].');';
				
				$insertHijo = consulta($insertHijoArbol);
				
				$res= $msj.cuentas_menuInicio();					
			}
			else
			{
				$error='No se ha podido guardar el concepto.';
				$msj = sistema_mensaje("error",$error);
				$pagina = cuentas_error_nuevoSub();
				$res= $msj.$pagina;									
			}
		}
		else
		{
			$error='Ya existe un concepto de egreso con este nombre.';
			$msj = sistema_mensaje("error",$error);
			$pagina = cuentas_error_nuevoSub();							
			$res= $msj.$pagina;
		}
		
		return $res;
	}
	
	function cuentas_formularioEditar()
	{
		//DATOS DE LA CUENTA
		liberar_bd();
		$selectDatosCuenta = 'CALL sp_sistema_select_datos_subcuenta('.$_POST["idCuenta"].');';
		$datosCuenta = consulta($selectDatosCuenta);
		$cuen = siguiente_registro($datosCuenta);
		
		//REVISAMOS VALIDEZ PERIODO
		switch($cuen["peri"])
		{ 
			case 0:
				$chkNoAplica = 'checked="checked"';
			break;
			case 1:
				$chkObliga = 'checked="checked"';
			break;
			case 2:
				$chkNoObliga = 'checked="checked"';
			break;
		}
		
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
											<h3>Datos generales</h3>
											<div class="form-horizontal">
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
											<hr class="divider"/>
											<h3>Datos extras</h3>	
											<div class="form-horizontal">	
												<div class="form-group">
													<label class="col-sm-3 control-label">Periodo</label>
													<div class="col-sm-6">
														<label class="radio-inline">
															<input id="periCta" type="radio" value="0" name="periCta" '.$chkNoAplica.'>Sin captura
														</label>
														<label class="radio-inline">
															<input id="periCta" type="radio" value="1" name="periCta" '.$chkObliga.'>Obligatorio
														</label>
														<label class="radio-inline">
															<input id="periCta" type="radio" value="2" name="periCta" '.$chkNoObliga.'>No obligatorio
														</label>
													</div>
												</div>
												<div class="form-group" style="display:none">
													<label class="col-sm-3 control-label">Factor Iva</label>
													<div class="col-sm-6">
														<div class="input-group">
															<input class="form-control" type="text" name="factorCta" id="factorCta" placeholder="0.00" value="0.00" value="'.$cuen["factor"].'">
															<span class="input-group-addon">%</span>
														</div>
													</div>
												</div>
											</div>																						
										</div>
										<div class="panel-footer">
											<div class="row">
												<div class="col-sm-12">
													<div class="btn-toolbar btnsGuarCan">
														<i class="btn-danger btn" onclick="navegar();">Cancelar</i>
														<i class="btn-success btn" onclick="nuevoConcepto(\'GuardarEdit\');">Guardar</i>
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
		$selectLinea =  "CALL sp_sistema_select_cuenta_nombreId(".$_POST["idCuenta"].", '".utf8_decode($_POST["nombreCta"])."');";
		$linea = consulta($selectLinea);
		$ctaLinea = cuenta_registros($linea);
		if($ctaLinea == 0)
		{
			if($_POST["factorCta"] == "")
			  	$factorCta = 0.00;
			  else
			  	$factorCta = $_POST["factorCta"];
				
			liberar_bd();
			$updateCuenta = " CALL sp_sistema_update_cuenta_registro(".$_POST["idCuenta"].", 
																	 '".utf8_decode($_POST["nombreCta"])."',
																	 '".utf8_decode($_POST["descCta"])."',
																	 '".$_POST["periCta"]."',
																	 ".$factorCta.",
																	 ".$_SESSION[$varIdUser].");";								  
			$update = consulta($updateCuenta);
			
			if($update)
				  $res= $msj.cuentas_menuInicio();	
			else
			{
				$error='No se ha podido editar el tipo de egreso.';
				$msj = sistema_mensaje("error",$error);
				$pagina = cuentas_error_edita();
				$res= $msj.$pagina;					
			}			  
		}
		else
		{
			$error='Ya existe un tipo de egreso con este nombre.';
			$msj = sistema_mensaje("error",$error);
			$pagina = cuentas_error_edita();
			$res= $msj.$pagina;
		}		  
		
		return $res;
	}
	
	function cuentas_eliminarCuenta()
	{
		//CHECAMOS SI LA CUENTA TIENE SUBCUENTAS
		liberar_bd();
		$selectSubcuentasCuentas = 'CALL sp_sistema_select_subcuentas_cuentas('.$_POST["idCuenta"].');';
		$subCueCuen = consulta($selectSubcuentasCuentas);
		$ctaSubCueCue = cuenta_registros($subCueCuen);
		if($ctaSubCueCue == 0)
		{
			liberar_bd();
			$deleteLinea = "CALL sp_sistema_delete_cuenta_registro('".$_POST["idCuenta"]."');";
			$delete = consulta($deleteLinea);
			if($delete)
			{
				//DATOS DE LA SUBCUENTA
			  	liberar_bd();
			  	$selectDatosCuenta = 'CALL sp_sistema_select_datos_subcuenta('.$_POST["idCuenta"].');';
			  	$datosCuenta = consulta($selectDatosCuenta);
			  	$cuen = siguiente_registro($datosCuenta);
				
				//NUMERO DE HIJOS DE LA CUENTA PADRE
				liberar_bd();
				$selectNumeroHijos = 'CALL sp_sistema_select_numHijos_cuenta('.$cuen["padre"].');';
				$numeroHijos = consulta($selectNumeroHijos);
				$hijos = siguiente_registro($numeroHijos);
				$ctaNumeroHijos = $hijos["hijos"] - 1;				
				
				//ACTUALIZAMOS NUMERO DE HIJOS DE LA CUENTA PADRE
				liberar_bd();
				$updateNumeroHijos = 'CALL sp_sistema_update_numeroHijos_cuenta('.$cuen["padre"].', '.$ctaNumeroHijos.');';
				$upNumHijos = consulta($updateNumeroHijos);
				
				$res= $msj.cuentas_menuInicio();				
			}
			else
			{
				$error='No se ha podido eliminar la subcategoría.';
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
		
		return $res;
	}
	
	function cuentas_error_nuevo()
	{
		//REVISAMOS VALIDEZ PERIODO 
		switch($_POST["periCta"])
		{ 
			case 0:
				$chkNoAplica = 'checked="checked"';
			break;
			case 1:
				$chkObliga = 'checked="checked"';
			break;
			case 2:
				$chkNoObliga = 'checked="checked"';
			break;
		}
				
		$nuevoError = '	<div id="page-heading">	
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
											<h3>Datos generales</h3>
											<div class="form-horizontal">
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
											<hr class="divider"/>
											<h3>Datos extras</h3>	
											<div class="form-horizontal">	
												<div class="form-group">
													<label class="col-sm-3 control-label">Periodo</label>
													<div class="col-sm-6">
														<label class="radio-inline">
															<input id="periCta" type="radio" value="0" name="periCta" '.$chkNoAplica.'>Sin captura
														</label>
														<label class="radio-inline">
															<input id="periCta" type="radio" value="1" name="periCta" '.$chkObliga.'>Obligatorio
														</label>
														<label class="radio-inline">
															<input id="periCta" type="radio" value="2" name="periCta" '.$chkNoObliga.'>No obligatorio
														</label>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Factor Iva</label>
													<div class="col-sm-6">
														<div class="input-group">
															<input class="form-control" type="text" name="factorCta" id="factorCta" placeholder="0.00" value="0.00" value="'.$_POST["factorCta"].'">
															<span class="input-group-addon">%</span>
														</div>
													</div>
												</div>
											</div>										
										</div>
										<div class="panel-footer">
											<div class="row">
												<div class="col-sm-12">
													<div class="btn-toolbar btnsGuarCan">
														<i class="btn-danger btn" onclick="navegar();">Cancelar</i>
														<i class="btn-success btn" onclick="nuevoConcepto(\'Guardar\');">Guardar</i>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>';
		
		return $nuevoError;
	}
	
	function cuentas_error_edita()
	{
		//REVISAMOS VALIDEZ PERIODO 
		switch($_POST["periCta"])
		{ 
			case 0:
				$chkNoAplica = 'checked="checked"';
			break;
			case 1:
				$chkObliga = 'checked="checked"';
			break;
			case 2:
				$chkNoObliga = 'checked="checked"';
			break;
		}
			
		$nuevoError = '	  <div id="page-heading">	
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
										  	  <h3>Datos generales</h3>
											  <div class="form-horizontal">
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
											  <hr class="divider"/>
											  <h3>Datos extras</h3>	
											  <div class="form-horizontal">	
												  <div class="form-group">
													  <label class="col-sm-3 control-label">Periodo</label>
													  <div class="col-sm-6">
														  <label class="radio-inline">
															  <input id="periCta" type="radio" value="0" name="periCta" '.$chkNoAplica.'>Sin captura
														  </label>
														  <label class="radio-inline">
															  <input id="periCta" type="radio" value="1" name="periCta" '.$chkObliga.'>Obligatorio
														  </label>
														  <label class="radio-inline">
															  <input id="periCta" type="radio" value="2" name="periCta" '.$chkNoObliga.'>No obligatorio
														  </label>
													  </div>
												  </div>
												  <div class="form-group">
													  <label class="col-sm-3 control-label">Factor Iva</label>
													  <div class="col-sm-6">
														  <div class="input-group">
															  <input class="form-control" type="text" name="factorCta" id="factorCta" placeholder="0.00" value="0.00" value="'.$_POST["factorCta"].'">
															  <span class="input-group-addon">%</span>
														  </div>
													  </div>
												  </div>
											  </div>									
										  </div>
										  <div class="panel-footer">
											  <div class="row">
												  <div class="col-sm-12">
													  <div class="btn-toolbar btnsGuarCan">
														  <i class="btn-danger btn" onclick="navegar();">Cancelar</i>
														  <i class="btn-success btn" onclick="nuevoConcepto(\'GuardarEdit\');">Guardar</i>
													  </div>
												  </div>
											  </div>
										  </div>
									  </div>
								  </div>
							  </div>
						  </div>';
								
		return $nuevoError;
	}
	
	function cuentas_error_nuevoSub()
	{
		//REVISAMOS VALIDEZ PERIODO 
		switch($_POST["periCta"])
		{ 
			case 0:
				$chkNoAplica = 'checked="checked"';
			break;
			case 1:
				$chkObliga = 'checked="checked"';
			break;
			case 2:
				$chkNoObliga = 'checked="checked"';
			break;
		}
			
		$cuentaError = ' <div id="page-heading">	
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
											<h3>Datos generales</h3>
											<div class="form-horizontal">
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
											<hr class="divider"/>
											<h3>Datos extras</h3>	
											<div class="form-horizontal">	
												<div class="form-group">
													<label class="col-sm-3 control-label">Periodo</label>
													<div class="col-sm-6">
														<label class="radio-inline">
															<input id="periCta" type="radio" value="0" name="periCta" '.$chkNoAplica.'>Sin captura
														</label>
														<label class="radio-inline">
															<input id="periCta" type="radio" value="1" name="periCta" '.$chkObliga.'>Obligatorio
														</label>
														<label class="radio-inline">
															<input id="periCta" type="radio" value="2" name="periCta" '.$chkNoObliga.'>No obligatorio
														</label>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Factor Iva</label>
													<div class="col-sm-6">
														<div class="input-group">
															<input class="form-control" type="text" name="factorCta" id="factorCta" placeholder="0.00" value="0.00" value="'.$_POST["factorCta"].'">
															<span class="input-group-addon">%</span>
														</div>
													</div>
												</div>
											</div>											
										</div>
										<div class="panel-footer">
											<div class="row">
												<div class="col-sm-12">
													<div class="btn-toolbar btnsGuarCan">
														<i class="btn-danger btn" onclick="navegar();">Cancelar</i>
														<i class="btn-success btn" onclick="nuevoConcepto(\'GuardarAgregar\');">Guardar</i>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>';
		
		return $cuentaError;
	}

?>