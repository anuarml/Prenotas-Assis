<?php
$printers = array();
if (function_exists('printer_list')) {
    
    $printerList = printer_list(PRINTER_ENUM_LOCAL,"",5);
    foreach($printerList as $printer){
	//echo $printer['PRINTERNAME'].'<br>';
        $printers[] = array('name' => $printer['PRINTERNAME'],'ip'=>'');
    }

    
}
echo json_encode($printers);
?>