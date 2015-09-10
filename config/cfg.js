var cfg = {};
cfg.uri = 'http://sbxcentro:8088/';
//cfg.uri = 'http://192.168.96.3/LeeWs/Prenotas-Assis/';

/* Indica si se debe preguntar o no el nombre de cliente para las prenotas.
   		true: Si preguntar el nombre de cliente.
   		false: No preguntar el nombre de cliente.

   	Importante: Si se pone en false esta opción se debe configurar en config.php
   	            la opción $cfg_client_name_optional = true
*/
cfg.askClientName = false;

cfg.badge = cfg.uri+'badge.html';

cfg.itemsPerPage = 4;
