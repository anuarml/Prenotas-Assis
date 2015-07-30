<?php
    // ----------------SQL Server config------------------------
    $host = '192.168.2.15';     // Dirección del servidor.
    $user = 'sbx';               // Nombre de usuario.
    $password = 'Grupo2015!';      // Contraseña
    $database = 'SBX_Local_Pruebas';// Nombre de base de datos.


    // --------------- App Config ------------------------
    $rootPath = 'C:\inetpub\scan_products\\'; // Ruta base de la aplicación.
    
    //Numero de veces que se va a imprimir la prenota
    $print_times = 1;


    // ----------  Nombre de tablas  -----------------
	$table_prenote = 'OperationOnHold';
	$table_prenoteDetails = 'OperationOnHoldDetail';

	$table_item = 'Item';
	$table_itembarcode = 'ItemBarcode';
    $table_itemSerial = 'ItemSerial';
	$table_itemCombination = 'ItemCombination';
    $table_itemOption = 'ItemOption';
	$table_optionDetail = 'OptionDetail';
	$table_option = '[Option]';

    $table_unit = 'Unit';
    $table_itemUnit = 'ItemUnit';
	
	$table_inventoryOnHand = 'InventoryOnHand';
	
	$table_user = '[User]';
	$table_person = 'Person';	
	// --------------------------------------
    
    $db_url = 'sqlsrv:server='.$host.';Database='.$database;
	
?>