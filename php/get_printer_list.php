<?php
include('../config/config.php');
$printers = array();
if (function_exists('printer_list')) {
    
    $printerList = printer_list(PRINTER_ENUM_LOCAL,"",5);
    foreach($printerList as $printer){
	//echo $printer['PRINTERNAME'].'<br>';
    	$printerName = $printer['PRINTERNAME'];

	//error_log(json_encode($cfg_printers));
    	if(in_array($printerName,$cfg_printers))
        	$printers[] = array('name' => $printer['PRINTERNAME'],'ip'=>'');
    }

    
}
echo json_encode($printers);
?>