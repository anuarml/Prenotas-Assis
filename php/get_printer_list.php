<?php
require('../config/config.php');

$avail_printers = array();
$installed_printer_names = array();
if (function_exists('printer_list')) {
    
    $installed_printers = printer_list(PRINTER_ENUM_LOCAL,"",5);

    foreach($installed_printers as $installed_printer){
	$installed_printer_names[] = $installed_printer['PRINTERNAME'];
    }

    $avail_printer_names = array_intersect($installed_printer_names,$cfg_printers);

    foreach($avail_printer_names as $avail_printer_name){
        $avail_printers[] = array('name' => $avail_printer_name,'ip'=>'');
    }
}
echo json_encode($avail_printers);
?>