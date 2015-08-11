<?php
include_once('db/config.php');
$printers = array();
if (function_exists('printer_list')) {
    
    $printerList = printer_list(PRINTER_ENUM_LOCAL,"",5);
    foreach($printerList as $printer){
	//echo $printer['PRINTERNAME'].'<br>';
    	$printerName = $printer['PRINTERNAME'];

    	if(in_array($printerName,$cfg_printers))
        	$printers[] = array('name' => $printer['PRINTERNAME'],'ip'=>'');
    }

    
}
echo json_encode($printers);
?>