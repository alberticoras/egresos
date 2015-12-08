<?php

    session_start();
    $dbCredentials = array(
	
		'db_host' => 'localhost',
	
		'db_user' => //'eleven11_sicDev',
					'root',
		
	
		'db_pass' => //'51Cr3SaD3v2oi5',
					'',
		
	
		'db_name' => //'eleven11_sicresaHD'
					'eleven11_sicresahd'
	
	);
	
	require('classes/DB.class.php');
	require('classes/DETALLES_DOCUMENTOS.class.php');
    require('classes/DOCUMENTOS_PROVEEDOR.class.php');
    require('classes/PAGOS.class.php');
    require('classes/REPORTES_EGRESOS.class.php');
	
	DB::init($dbCredentials);
	
	$response = array();
	
	try
	{

		switch ($_GET['action']) {
			//Operaciones de Documentos de Proveedor
            case 'createDocument':
                $response = clsDocumentosProveedor::createDocument($_POST['status'], $_POST['concepto'], $_POST['fecha'], $_POST['monto'], $_POST['idProveedor'], $_POST['recurrencia'], $_POST['tiempo'], $_POST['duracion'], $_POST['tipo'], '1',$_POST['folio'], $_POST['compromiso'], $_POST['observaciones']);
//                  $response = conexionPrueba();
				break;
            case 'cancelDocument':
                $response = clsDocumentosProveedor::cancelDocument($_POST['id'],$_POST['id_user'],$_POST['motive']);
                break;
            case 'updateDocument':
                $response = clsDocumentosProveedor::updateDocument($_POST['status'],$_POST['monto'],$_POST['fecha'],$_POST['id']);
                break;
            case 'updateRecurrencyDocument':
                $response = clsDocumentosProveedor::updateRecurrencyDocument($_POST['id'],$_POST['flag'],$_POST['interval'],$_POST['last_time']);
                break;
            case 'getDocuments':
                $response = clsDocumentosProveedor::getDocuments($_POST['status']);
                break;
            case 'getCancelledDocuments':
                $response = clsDocumentosProveedor::getCancelledDocuments();
                break;
            case 'getDebtForDocument':
                $response = clsDocumentosProveedor::getDebtForDocument($_POST['id']);
                break;
            case 'getDocumentForId':
                $response = clsDocumentosProveedor::getDocumentForId($_POST['id']);
                break;
            case 'verifyFolio':
                $response = clsDocumentosProveedor::verifyFolio($_POST['folio']);
                break;
            case 'getDocumentsForProvider':
            	$response = clsDocumentosProveedor::getDocumentsForProvider($_POST['id']);
            	break; 
            
            //Operaciones de Pagos
            case 'getPayments':
                $response = clsPagos::getPayments($_POST['status']);
                break;
            case 'getPaymentsForDocument':
                $response = clsPagos::getPayments($_POST['status'],$_POST['idDocument']);
                break;
            case 'getCancelledPayments':
                $response = clsPagos::getCancelledPayments();
                break;
            case 'getCancelledPaymentsForDocument':
                $response = clsPagos::getCancelledPaymentsForDocument($_POST['idDocument']);
                break;
            case 'createPayment':
                $response = clsPagos::createPayment($_POST['fecha_pago'], $_POST['monto'], $_POST['status'], $_POST['user'], $_POST['tipo'], $_POST['id_sucursal'], $_POST['id_cuenta'], $_POST['id_documento']);
				break;
            case 'cancelPayment':
                $response = clsPagos::cancelPayment($_POST['id'],$_SESSION["login"],$_POST['motive']);
                break;
            case 'getPaymentsForSpecificPeriod':
                $response = clsPagos::getPaymentsForSpecificPeriod($_POST['begin'],$_POST['end']);
                break;
            case 'createPaymentForDocuments':
                $response = clsPagos::createPaymentForDocuments($_POST['fecha_pago'], $_POST['monto'], $_POST['status'], $_POST['user'], $_POST['tipo'], $_POST['id_sucursal'], $_POST['id_cuenta'], $_POST['referencia']);
                break;
            case 'linkPaymentToDocuments':
                $response = clsPagos::linkPaymentToDocuments($_POST['idDoc'], $_POST['idPay'], $_POST['Monto']);
                break;
            case 'proyectarPagos':
                $response = clsPagos::createPlannedPayment($_POST['fecha'], $_POST['monto'], $_POST['id_doc']);
                break;
            
            //Operaciones de Detalles de Documentos
            case 'getPayments':
                $response = clsDetallesDocumentos::getDetailForDocument($_POST['idDocument']);
                break;
            case 'createDetails':
                $response = clsDetallesDocumentos::createDetails($_POST['descripcion'],$_POST['cantidad'],$_POST['precio_u'],$_POST['inicio_periodo'],$_POST['fin_periodo'],$_POST['idDocumento'],$_POST['idSucursal'], $_POST['idConcepto']);
                break;
            case 'updateDetails':
                $response = clsDetallesDocumentos::updateDetails($_POST['descripcion'],$_POST['cantidad'],$_POST['precio_u'],$_POST['inicio_periodo'],$_POST['fin_periodo'],$_POST['iva'],$_POST['idConcepto'],$_POST['idDetalle']);
                break;
            //Obtener conceptos de detalles
            case 'getConceptsForDetails':
                $response = clsDetallesDocumentos::getConceptsForDetails();
                break;
            //Obtener contenidos de un archivo XML
            case 'loadXML':
                $response = getContentsOfXML($_POST['URL']);
                break;
            case 'rename_file':
                $response = renameXMLFile($_POST['rfc'], $_POST['location'],$_POST['file'], $_POST['folio']);
                break;
            case 'displayDetailsForConcept':
            	$response = clsDetallesDocumentos::displayDetailsForConcept($_POST['id']);
            	break;
            
            //Obtener cuentas y mas datos de los combos
            case 'getCuentas':
                $response = clsPagos::getCuentas();
                break;
            case 'getSucursales':
                $response = clsPagos::getSucursales();
                break;
            case 'getSaldo':
                $response = clsPagos::getSaldoForAccount($_POST['id']);
                break;
            
            //Agregar un nuevo proveedor
            case 'crearNuevoProveedor':
                $response = clsDocumentosProveedor::crearNuevoProveedor($_POST['nombre'],$_POST['calle'],$_POST['numInt'],$_POST['numExt'],$_POST['colonia'],$_POST['cp'],$_POST['ciudad'],$_POST['user']);
                break;
            case 'getProveedores':
                $response = clsDocumentosProveedor::getProveedores();
                break;
            
            //Reportes de egresos
            case 'outgoingReport':
                $response = clsDetallesDocumentos::outgoingReportTotal();
                break;
            case 'getTotals':
                $response = clsDetallesDocumentos::getTotalForConceptId($_POST['id'], $_POST['inicio'], $_POST['fin']);
                break;
            case 'generateReportesMovimientosEgresos':
                $response = clsReportesEgresos::generateReportesMovimientosEgresos($_POST['proveedor'], $_POST["fechaInicio"], $_POST["fechaFin"], $_POST["cuenta"], $_POST["tipo_pago"]);
                break;
            
		}
		
		echo json_encode($response);
	
	}
	catch (Exception $e) 
	{
		die(json_encode(array('error en ajax' => $e->getMessage())));	
	}
	

    function getContentsOfXML ($url) {
        
        try
        {
            
            $xml = file_get_contents('http://localhost/sicresa_real.com/sistemaHD/'.$url, true);
    
            $response = array();
            $xml_array = simplexml_load_string($xml) or die("Error: Cannot create object");
            
            $header_array = array();
            $header_array[0] = $xml_array["fecha"];
            $header_array[1] = $xml_array["folio"];
            $header_array[2] = $xml_array["total"];
            $header_array[3] = $xml_array["subTotal"];
            $response[0] = $header_array;
            $x = 1;
            
            $ns = $xml_array->getNamespaces(true);
            if (array_key_exists('tfd', $ns)) {
                $xml_array->registerXPathNamespace('t', $ns['tfd']);
                
                foreach ($xml_array->xpath('//t:TimbreFiscalDigital') as $Complemento){

                    $concepts_array['UUID'] = $Complemento['UUID'];

                    $response[$x] = $concepts_array;
                    $x++;
                }
            }
            
            foreach ($xml_array->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $Concepto){ 
                
                $concepts_array['cantidad'] = $Concepto['cantidad'];
                $concepts_array['unidad'] = $Concepto['unidad'];
                $concepts_array['concepto'] = $Concepto['descripcion'];
                $concepts_array['precio_uni'] = $Concepto['valorUnitario'];
                $concepts_array['total'] = $Concepto['importe'];
                
                $response[$x] = $concepts_array;
                $x++;
            }
            
            //Leer los impuestos retenidos en caso de existir
            foreach ($xml_array->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Retenciones//cfdi:Retencion') as $Impuesto){
                $tax_array['nombre'] = $Impuesto['impuesto'];
                $tax_array['importe'] = $Impuesto['importe'];
                $tax_array['retencion'] = 'yes';
                
                $response[$x] = $tax_array;
                $x++;
            }
            //leer los impuestos trasladados en caso de existir
            foreach ($xml_array->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslados//cfdi:Traslado') as $Impuesto){
                $tax_array2['nombre'] = $Impuesto['impuesto'];
                $tax_array2['importe'] = $Impuesto['importe'];
                $tax_array2['tasa'] = $Impuesto['tasa'];
                $tax_array2['traslado'] = 'yes';
                
                $response[$x] = $tax_array2;
                $x++;
            }
            
            foreach ($xml_array->xpath('//cfdi:Comprobante//cfdi:Emisor') as $RFC){
                $rfc_array['rfc'] = $RFC['rfc'];
                $response[$x] = $rfc_array;
            }
            
            return $response;
            
        }catch(Exception $ex){
            return array('error' => $ex->getMessage());   
        }
    }

    function renameXMLFile ($rfc, $path, $file,$folio) {
        
        try {
            if(!file_exists('handling/upload/'.$rfc.'_'.$folio.'.xml')){
                $myfile = fopen('handling/upload/'.$rfc.'_'.$folio.'.xml', "w") or die("Unable to open file!");
                $xml = file_get_contents('http://localhost/sicresa_real.com/sistemaHD/'.$path.$file, true);
                $xml_array = simplexml_load_string($xml) or die("Error: Cannot create object");
                fwrite($myfile, $xml_array->asXML());
                fclose($myfile);
                unlink('handling/upload/'.$file);
            }

            return true;
            
        }catch (Exception $ex){
            
        }
    }

?>