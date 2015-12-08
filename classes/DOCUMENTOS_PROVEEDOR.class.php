<?php

    class clsDocumentosProveedor {
        
        public static function getDocuments ($status) {
            
            try{
		
			$response = DB::query('call sp_get_documento_proveedor(\''.$status.'\')');
			$final = array();
			while($row = $response->fetch_array(MYSQL_NUM))
			{
			    $response_array['id'] = $row[0];
			    $response_array['Status'] = $row[2];
			    $response_array['Fecha'] = $row[4];
                $response_array['Monto'] = $row[5];
                $response_array['idProveedor'] = $row[6];
                $response_array['Recurrencia'] = $row[7];
                $response_array['Tipo'] = $row[8];
                $response_array['Usuario'] = $row[9];
                $response_array['Fecha_Captura'] = $row[10];
                $response_array['Saldo'] = $row[1];
                $response_array['Folio'] = $row[11];
                $response_array['Proveedor'] = $row[12];

			   	$final[] = $response_array;
		    }

			return $final;
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
        }
        
        public static function getDocumentsForProvider ($id) {
            
            try{
		
			$response = DB::query('call sp_get_document_for_provider(\''.$id.'\')');
			$final = array();
			while($row = $response->fetch_array(MYSQL_NUM))
			{
			    $response_array['id'] = $row[0];
			    $response_array['Folio'] = $row[1];
			    $response_array['Fecha'] = $row[2];
                $response_array['Saldo'] = $row[3];

			   	$final[] = $response_array;
		    }

			return $final;
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
        }
        
        public static function getDocumentForId ($id) {
            
            try{
		
			$response = DB::query('call sp_get_documento_proveedor_for_id (\''.$id.'\')');
			$final = array();
			
            $row = $response->fetch_array(MYSQL_NUM);
            $response_array['id'] = $row[0];
            $response_array['Status'] = $row[1];
            $response_array['Concepto'] = $row[2];
            $response_array['Fecha'] = $row[3];
            $response_array['Monto'] = $row[4];
            $response_array['Folio'] = $row[5];
            $response_array['Tipo'] = $row[6];
            $response_array['Saldo'] = $row[7];
            $response_array['Proveedor'] = $row[8];
            $response_array['Observaciones'] = $row[9];

            $final[] = $response_array;

			return $final;
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
        }
        
        public static function getCancelledDocuments () {
            
            try{
		
			$response = DB::query('call sp_get_documento_proveedor(0)');
			$final = array();
			while($row = $response->fetch_array(MYSQL_NUM))
			{
			    $response_array['concepto'] = $row[0];
			    $response_array['fecha_pago'] = $row[1];
                $response_array['Monto'] = $row[2];
                $response_array['usuario'] = $row[3];
                $response_array['fecha_cancel'] = $row[4];
                $response_array['motivo'] = $row[5];

			   	$final[] = $response_array;
		    }

			return $final;
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
        }
        
        public static function verifyFolio ($folio) {
            
            try{
		
			$response = DB::query('call sp_verify_existing_folio(\''.$folio.'\');');
			$final = array();
            while($row = $response->fetch_array(MYSQL_NUM))
            {
                $response_array['count'] = $row[0];

                $final[] = $response_array;
            }
			return $final;
                
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
        }
        
        public static function createDocument ($_status, $_concepto, $_fecha, $_monto, $_idProveedor, $_recurrencia, $_tiempo_recurrencia, $_duracion_recurrencia, $_tipo_documento, $_user, $folio, $compromiso, $observaciones) {
         
            try { 
                
                $response = DB::query('call sp_alta_documento_proveedor(\''.$_status.'\', \''.$_concepto.'\', \''.$_fecha.'\',\''.$_monto.'\', \''.$_idProveedor.'\', \''.$_recurrencia.'\', \''.$_tiempo_recurrencia.'\', \''.$_duracion_recurrencia.'\', \''.$_tipo_documento.'\', \''.$_user.'\',\''.$folio.'\', \''.$compromiso.'\', \''.$observaciones.'\')');
                
                $final = array();
                while($row = $response->fetch_array(MYSQL_NUM))
                {
                    $response_array['id'] = $row[0];

                    $final[] = $response_array;
                }

                return $final;
                
            }catch(Exception $ex) {
                return array('error php' => $ex->getMessage());   
            }
        }
        
        public static function cancelDocument ($idDocument, $idUser, $motive) {
            
            try{
                
                $response = DB::query('call sp_cancelacion_documento_proveedor(\''.$idDocument.'\',\''.$idUser.'\', \''.$motive.'\');');
                return array('completion' => 'true');
                
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
            
        }
        
        public static function updateDocument ($_estatus, $_monto, $_fecha, $_id_documento) {
            
            try{ 
                
                $response = DB::query('call sp_update_documento_proveedor(\''.$_estatus.'\',\''.$_monto.'\', \''.$_fecha.'\', \''.$_id_documento.'\');');
                return array('completion' => 'true');
                
            }catch(EXception $ex){
                return array('error php' => $ex->getMessage());   
            }
            
        }
        
        public static function updateRecurrencyDocument ($_id_documento, $_recurrencia, $_intervalo, $_duracion) {
            
            try{ 
                
                $response = DB::query('call sp_update_recurrencia_documento_proveedor(\''.$_id_documento.'\',\''.$_recurrencia.'\', \''.$_intervalo.'\', \''.$_duracion.'\');');
                return array('completion' => 'true');
                
            }catch(EXception $ex){
                return array('error php' => $ex->getMessage());   
            }
            
        }
        
        public static function getDebtForDocument ($id) {
            
            try {
             
                $response = DB::query('call sp_get_saldo_for_document(\''.$id.'\')');
                $final = array();
                while($row = $response->fetch_array(MYSQL_NUM))
                {
                    $response_array['saldo'] = $row[0];

                    $final[] = $response_array;
                }

                return $final;
                
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
        }
        
        public static function crearNuevoProveedor ($nombre, $calle, $numInt, $numExt, $colonia, $codigo, $ciudad, $user) {
         
            try {
                
                $response = DB::query('call sp_sistema_insert_proveedores_datos_grales(\''.$nombre.'\', \''.$calle.'\', \''.$numInt.'\',\''.$numExt.'\', \''.$colonia.'\', \''.$codigo.'\', \''.$ciudad.'\', \''.$user.'\');');
                

                return $final;
                
            }catch(Exception $ex){
                return array('error proveedor' => $ex->getMessage());
            }
        }
        
        public static function getProveedores () {
            
            try {
                
                $response = DB::query('call sp_sistema_lista_proveedores()');
                $final = array();
                while($row = $response->fetch_array(MYSQL_NUM))
                {
                    $response_array['id'] = $row[0];
                    $response_array['nombre'] = $row[1];

                    $final[] = $response_array;
                }

                return $final;
                
            }catch(Exception $ex){
                
            }
        }
    }

?>