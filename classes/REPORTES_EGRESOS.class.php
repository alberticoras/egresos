<?php

    class clsReportesEgresos {
     
        public static function generateReportesMovimientosEgresos ($prov, $begin_date, $end_date, $account, $pay) {
            
            try{
		
			$response = DB::query('call sp_select_reportes_movimientos_egresos(\''.$prov.'\',\''.$begin_date.'\', \''.$end_date.'\',\''.$account.'\',\''.$pay.'\');');
			$final = array();
			while($row = $response->fetch_array(MYSQL_NUM))
			{
			    $response_array['Proveedor'] = $row[0];
			    $response_array['Fecha'] = $row[1];
                $response_array['Banco'] = $row[2];
                $response_array['Cuenta'] = $row[3];
                $response_array['Tipo'] = $row[4];
                $response_array['Monto'] = $row[5];
                $response_array['Concepto'] = $row[6];

			   	$final[] = $response_array;
		    }

			return $final;
            }catch(Exception $ex){
                return array('error php' => $ex->getMessage());   
            }
        }
        
    }

?>