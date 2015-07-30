<?php
if (function_exists('printer_list')) {

    $printerList = printer_list(PRINTER_ENUM_LOCAL,"",5);
    foreach($printerList as $printer){
	echo $printer['PRINTERNAME'].'<br>';
    }
    //var_dump($printerList[1]['PRINTERNAME']);
} else {
    echo "Las funciones de IMAP NO están disponibles.<br />\n";
}
    //$printerList = printer_list(PRINTER_ENUM_LOCAL);
    //var_dump($printerList[0]['NAME']);
?>