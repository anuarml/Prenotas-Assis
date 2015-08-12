<?php
    // ----------------SQL Server config------------------------
    // SBXCENTRO
    /*$host = '192.168.2.15';     // Dirección del servidor.
    $user = 'sbx';               // Nombre de usuario.
    $password = 'Grupo2015!';      // Contraseña
    $database = 'SBX_Local_Pruebas';// Nombre de base de datos.
    */
    // SERVERSOPORTE
    $host = '192.168.96.83';     // Dirección del servidor.
    $user = 'sa';               // Nombre de usuario.
    $password = 'Tho2010';      // Contraseña
    $database = 'SBX_Local_Pruebas';// Nombre de base de datos.


    // --------------- App Config ------------------------
    $rootPath = 'C:\inetpub\wwwroot\HolaMundoWs\LeeWs\Prenotas-Assis\\'; // Ruta base de la aplicación.
    
    //Numero de veces que se va a imprimir la prenota
    $print_times = 1;

    $cfg_priceList = '(Precio Lista)';  // Lista de precios
    $cfg_currency = 'Pesos';            // Moneda
    $cfg_client = '5';                  // Cliente
    $cfg_store = '1';                   // Sucursal
    $cfg_workstation = 'A';             // Estación principal, debe estar abierta.

    $cfg_validate_branch = true;        // Indica si se debe validar que todos los artículos que se escanean tengan rama.

    //------------ Impresoras ---------------
    $cfg_printers = [
        'Piso1',
    ];

    //----------- Constantes Globales ------------

    $CONST_ITEM_STATUS_ACTIVE = 'ALTA';
    $CONST_ITEM_STATUS_INACTIVE = 'BAJA';

    $CONST_ITEM_TYPE_KIT = 'Juego';

    // ----------  Nombre de tablas  -----------------
	$table_prenote = 'OperationOnHold';
	$table_prenoteDetails = 'OperationOnHoldDetail';

	$table_item = 'Item';
	$table_itembarcode = 'ItemBarcode';
    $table_itemSerial = 'ItemSerial';
	$table_itemCombination = 'ItemCombination';
    $table_itemOption = '[ItemOption]';
    $table_itemType = '[ItemType]';
    $table_itemStatus = '[ItemStatus]';

	$table_optionDetail = 'OptionDetail';
	$table_option = '[Option]';

    $table_optionListDetail = '[OptionListDetail]';

    $table_unit = 'Unit';
    $table_itemUnit = 'ItemUnit';
	
	$table_inventoryOnHand = 'InventoryOnHand';
	
	$table_user = '[User]';
	$table_person = 'Person';

    $table_priceList = '[PriceList]';
    $table_priceListDetail = '[PriceListDetail]';

    $table_currency = '[Currency]';

    $table_customer = '[Customer]';
    $table_store = '[Store]';

    $table_register = '[Register]';
	// --------------------------------------
    
    $db_url = 'sqlsrv:server='.$host.';Database='.$database;
	
?>