<?php
    // ----------------SQL Server config------------------------
    //Configuracion de la base de datos de SBX a utilizar en prentoas.
    // SBXCENTRO
    $host = '192.168.2.15';         // Dirección del servidor.
    $user = 'sbx';                  // Nombre de usuario SQL.
    $password = 'Grupo2015!';       // Contraseña SQL.
    $database = 'SBX_Local_Pruebas';// Nombre de la base de datos.
    
    // SERVERSOPORTE
    /*$host = '192.168.96.83';     // Dirección del servidor.
    $user = 'sa';               // Nombre de usuario.
    $password = 'Tho2010';      // Contraseña
    $database = 'SBX_Local_Pruebas';// Nombre de base de datos.
    */

    $db_url = 'sqlsrv:server='.$host.';Database='.$database;

    // --------------- App Config ------------------------
    // Configuraciones a utilizar en la aplicación de prenotas.

    // Ruta del servidor donde se encuentra la aplicación de prenotas.
    //$rootPath = 'C:\inetpub\wwwroot\HolaMundoWs\LeeWs\Prenotas-Assis\\'; // Ruta base de la aplicación.
    $rootPath = 'C:\inetpub\scan_products_dev\\';
    
    //Numero de veces que se va a imprimir la prenota.
    $print_times = 1;

    // Lista de precios.
    $cfg_priceList = '(Precio Lista)';

    // Moneda.
    $cfg_currency = 'Pesos';

    // Cliente.
    $cfg_client = '5';
    
    // Sucursal
    $cfg_store = '1';

    // Caja principal, debe estar abierta para poder ingresar a la aplicación.
    $cfg_workstation = 'A';

    // Indica si se debe validar que todos los artículos que se escanean tengan rama.
    $cfg_validate_branch = true;

    //------------ Impresoras ---------------
    // Nombre de las impresoras que se van a mostrar en la aplicación de prenotas.
    // Las impresoras deben estar instaladas en el servidor y se debe utilizar el
    // nombre que tiene la impresora en el servidor.
    $cfg_printers = [
        'Piso1',
        'impresora2'
    ];
?>