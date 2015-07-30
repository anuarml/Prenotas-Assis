<?php
 // 192.168.96.139\EPSONTM-T20II
	function print_prenote($folio, $ip){
		require_once( 'printIPP/PrintIPP.php' );

   		try{
		    $ipp = new PrintIPP();
		    
		    $ipp->setHost($ip);
		    $ipp->setPrinterURI($ip);
			$ipp->setCharset("UTF-8");
		    $ipp->setData( $folio.".pdf" );
		    
		    if( ($status = $ipp->printJob( $folio ) ) == "successfull-ok"){
				return true;
			}else {
				error_log($status);
				return false;
			}
		}
		catch(Exception $ex){
			error_log('ex...'+$ex->getErrorFormatted());
			return false;
		}
	}
?> 