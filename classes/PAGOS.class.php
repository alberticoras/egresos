<?php 

    class clsPagos {
    
        public static function getPayments ($status) {
            
            try{
		
			$response = DB::query('call sp_get_pagos(\''.$status.'\', \'0\')');
			$final = array();
			while($row = $response->fetch_array(MYSQL_NUM))
			{
			    $response_array['id'] = $row[0];
			    $response_array['Fecha'] = $row[1];
			    $response_array['Monto'] = $row[2];
                $response_array['status'] = $row[3];
                $response_array['usuario'] = $row[4];
                $response_array['tipo'] = $row[5];
                $response_array['id_sucursal'] = $row[6];
                $response_array['id_cuenta'] = $row[7];
                $response_array['Fecha_Captura'] = $row[8];

			   	$final[] = $response_array;
		    }

			return $final;
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }   
        }
        
        public static function getPaymentsForSpecificPeriod($begin, $end){
               
            try{
                
                $response = DB::query('call sp_get_payments_dates(\''.$begin.'\', \''.$end.'\')');
                $final = array();
                while($row = $response->fetch_array(MYSQL_NUM))
                {
                    $response_array['id'] = $row[0];
                    $response_array['Fecha'] = $row[1];
                    $response_array['Monto'] = $row[2];
                    $response_array['status'] = $row[3];
                    $response_array['usuario'] = $row[4];
                    $response_array['tipo'] = $row[5];
                    $response_array['id_sucursal'] = $row[6];
                    $response_array['id_cuenta'] = $row[7];
                    $response_array['Fecha_Captura'] = $row[8];

                    $final[] = $response_array;
                }

			    return $final;
                
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());
            }
        }
        
        public static function getPaymentsForDocument($status, $idDocument) 
        {
            try
            {
                
                $response = DB::query('call sp_get_pagos(\''.$status.'\', \''.$idDocument.'\')');
                $final = array();
                while($row = $response->fetch_array(MYSQL_NUM))
                {
                    $response_array['id'] = $row[0];
                    $response_array['Fecha'] = $row[1];
                    $response_array['Monto'] = $row[2];
                    $response_array['usuario'] = $row[3];
                    $response_array['tipo'] = $row[4];
                    $response_array['id_sucursal'] = $row[5];
                    $response_array['id_cuenta'] = $row[6];

                    $final[] = $response_array;
                }

                return $final;
                   
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
        }
        
        public static function getCancelledPayments () {
            
            try{
		
			$response = DB::query('call sp_get_pagos(0, 0)');
			$final = array();
			/*while($row = $response->fetch_array(MYSQL_NUM))
			{
			    $response_array['concepto'] = $row[0];
			    $response_array['fecha_pago'] = $row[1];
                $response_array['Monto'] = $row[2];
                $response_array['usuario'] = $row[3];
                $response_array['fecha_cancel'] = $row[4];
                $response_array['motivo'] = $row[5];

			   	$final[] = $response_array;
		    }*/

			return $response;
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
        }
        
        public static function getCancelledPaymentsForDocument ($idDocument) {
            
            try{
		
			$response = DB::query('call sp_get_pagos(0, \''.$idDocument.'\')');
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
        
        public static function createPayment ($_fecha_pago, $_monto, $_estatus, $_user, $_tipo, $_id_suc, $_id_cuenta, $_id_documento_ref, $_reference)
        {
         
            try { 
                
                $response = DB::query('call sp_alta_pago(\''.$_fecha_pago.'\', \''.$_monto.'\', \''.$_estatus.'\',\''.$_user.'\', \''.$_tipo.'\', \''.$_id_suc.'\', \''.$_id_cuenta.'\', \''.$_id_documento_ref.'\', \''.$_reference.'\');');
                return array('completion' => 'true');
                
            }catch(Exception $ex) {
                return array('error php' => $ex->getMessage());
            }
        }
        
        public static function cancelPayment ($idPayment, $idUser, $motive) {
            
            try{
                
                $response = DB::query('call sp_cancelacion_pago(\''.$idPayment.'\',\''.$idUser.'\', \''.$motive.'\');');
                return array('completion' => 'true');
                
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
            
        }
        
        public static function getCuentas () {
         
            try{
                
                $response = DB::query('call sp_sistema_lista_ctas_bancos();');
                $final = array();
                while($row = $response->fetch_array(MYSQL_NUM))
                {
                    $response_array['id'] = $row[0];
                    $response_array['nombre'] = $row[1];
                    $response_array['numero'] = $row[2];

                    $final[] = $response_array;
                }

			    return $final;
                
            }catch(Exception $ex){
                
            }
        }
        
        public static function getSaldoForAccount($id) {
            
            try {
                
                $response = DB::query('call sp_get_saldo_for_count(\''.$id.'\');');
                    
                $final = array();
                $row = $response->fetch_array(MYSQL_NUM);
                $response_array['saldo'] = $row[0];
                $final[] = $response_array;
                
                return $final;
                
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
        }
        
        public static function getSucursales () {
         
            try{
                
                $response = DB::query('call sp_sistema_select_lista_sucursales()');
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
        
        public static function createPaymentForDocuments ($_fecha_pago, $_monto, $_estatus, $_user, $_tipo, $_id_suc, $_id_cuenta, $_reference) {
            
            try{
                $response = DB::query('call sp_alta_pago_various(\''.$_fecha_pago.'\', \''.$_monto.'\', \''.$_estatus.'\',\''.$_user.'\', \''.$_tipo.'\', \''.$_id_suc.'\', \''.$_id_cuenta.'\', \''.$_reference.'\');');
                    
                $final = array();
                $row = $response->fetch_array(MYSQL_NUM);
                $response_array['id'] = $row[0];
                $final[] = $response_array;
                
                return $final;
            }catch(Exception $ex){
                
            }
        }
        
        public static function linkPaymentToDocuments ($_id_doc, $_id_pay, $_monto) {
            
            try {
                $response = DB::query('call sp_link_payments_to_docs(\''.$_id_doc.'\', \''.$_id_pay.'\',\''.$_monto.'\')');
                return array('completion' => 'true');
                
            }catch(Exception $ex){
                return array('error' => $ex->getMessage());
            }
            
        }
        
        public static function createPlannedPayment ($_date, $_amount, $_doc) {
            
            try {
                
                $response = DB::query('call sp_create_planned_payment(\''.$_monto.'\', \''.$_date.'\',\''.$_id_doc.'\')');
                return array('completion' => 'true');
                
            }catch(Exception $ex){
                return array('error' => $ex->getMessage());
            }
        }
        
    }

?>