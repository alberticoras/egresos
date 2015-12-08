<?php

    class clsDetallesDocumentos 
    {
    
        public static function getDetailForDocument ($idDocument) {
            
            try{
		
			$response = DB::query('call sp_get_detalle_documento(\''.$idDocument.'\')');
			$final = array();
			while($row = $response->fetch_array(MYSQL_NUM))
			{
			    $response_array['id'] = $row[0];
			    $response_array['Decripcion'] = $row[1];
			    $response_array['Cantidad'] = $row[2];
                $response_array['Precio_U'] = $row[3];
                $response_array['Precio_T'] = $row[4];
                $response_array['Periodo_Ini'] = $row[5];
                $response_array['Periodo_Fin'] = $row[6];
                $response_array['nombre_sucursal'] = $row[7];
                $response_array['idSucursal'] = $row[8];

			   	$final[] = $response_array;
		    }

			return $final;
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
        }
        
        public static function createDetails ($_descr, $_cantidad, $_precio_u, $_inicio_periodo, $_fin_periodo, $_id_doc, $_id_suc, $_idConcepto) {
         
            try { 
                
                $response = DB::query('call sp_alta_detalle_documento(\''.$_descr.'\', \''.$_cantidad.'\', \''.$_precio_u.'\',\''.$_inicio_periodo.'\', \''.$_fin_periodo.'\', \''.$_id_doc.'\', \''.$_id_suc.'\', \''.$_idConcepto.'\')');
                return array('completion' => 'true');
                
            }catch(Exception $ex) {
                return array('error php' => $ex->getMessage());   
            }
        }
        
        public static function updateDetails ($_descr, $_cantidad, $_precio_u, $_periodo_i, $_periodo_f, $_iva, $_concepto, $_id_detalle) {
            
            try{ 
                
                $response = DB::query('call sp_update_detalle_documento(\''.$_descr.'\',\''.$_cantidad.'\', \''.$_precio_u.'\', \''.$_periodo_i.'\', \''.$_periodo_f.'\', \''.$_iva.'\', \''.$_concepto.'\', \''.$_id_detalle.'\');');
                return array('completion' => 'true');
                
            }catch(EXception $ex){
                return array('error php' => $ex->getMessage());   
            }
            
        }
    
        public static function getConceptsForDetails () {
            
            try{
		
			$response = DB::query('CALL sp_sistema_lista_cuentas_hijo()');
			$final = array();
			while($row = $response->fetch_array(MYSQL_NUM))
			{
			    $response_array['id'] = $row[0];
			    $response_array['Nombre'] = $row[1];

			   	$final[] = $response_array;
		    }

			return $final;
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
        }
        
        public static function outgoingReportTotal () {
            
            try {
             
                $response = DB::query('SELECT DISTINCT idConcepto FROM detalle_documento;');
                $final = array();
                while($row = $response->fetch_array(MYSQL_NUM))
                {
                    $response_array['id'] = $row[0];
                    $final[] = $response_array;
                }
                return $final;
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
        }
        
        public static function getTotalForConceptId ($id, $inicio, $fin) {
            
            try {    
                $new_response = DB::query('call sp_get_total_for_concepts_details(\''.$id.'\', \''.$inicio.'\', \''.$fin.'\');');
                $final = array();
                while($row_total = $new_response->fetch_array(MYSQL_NUM)){
                    $sum_array['id'] = $row_total[0];
                    $sum_array['total'] = $row_total[1];
                    $final[] = $sum_array;
                }
                return $final;
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
        }
        
        public static function displayDetailsForConcept ($id) {
	        
	        try {    
                $new_response = DB::query('call sp_get_details_for_concepts(\''.$id.'\');');
                $final = array();
                while($row_total = $new_response->fetch_array(MYSQL_NUM)){
                    $sum_array['descripcion'] = $row_total[0];
                    $sum_array['cantidad'] = $row_total[1];
                    $sum_array['precio_u'] = $row_total[2];
                    $sum_array['precio_t'] = $row_total[3];
                    $final[] = $sum_array;
                }
                return $final;
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
        }
    }

?>