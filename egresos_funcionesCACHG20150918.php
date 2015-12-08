<?php

	function egresos_menuInicio()
	{
		$pagina = '	<div id="page-heading">	
					  	<h1>Egresos</h1>
						<div class="options">
							<div class="btn-toolbar">	
								<input type="hidden" id="idEgresos" name="idEgresos" value="" />													
								<input type="hidden" name="txtIndice" />
							</div>							
						</div>										
				  	</div>										
				  	<div class="container">						
						<div class="row">
							<div class="col-sm-12">
								<div class="panel panel-danger">
									<div class="panel-heading">
										<h4></h4>
										<div class="options">   
											<a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
										</div>
									</div>
									<div class="panel-body collapse in">
										<div class="table-responsive">
											<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered " id="example">
												<thead>
													<tr>
														<th>ACCIONES</th>
													</tr>
												</thead>	
												<tbody>
													<tr>
														<td></td>
													</tr>
												</tbody>																				
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>';
							
		return $pagina;		
		
	}	
?>