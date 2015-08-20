<?php

function getRegister($link, $storeID){

	include('../../config/config.php');
	include('global_variables.php');

	$query = 'SELECT TOP 1 ID id, UUID uuid, Workstation workstation '.
			 'FROM '.$table_register.' Register '.
			 'WHERE Closed = 0 '.
			 'AND StoreID = :storeID '.
			 //'AND CreationDate >= CONVERT(DATE,GETDATE())'
			 'AND Workstation = :workstation';

	$handle = $link->prepare($query);

	$handle->bindParam(':storeID', $storeID);
	$handle->bindParam(':workstation', $cfg_workstation);
	$handle->execute();

	$register = $handle->fetchObject();

	return $register;
}

?>