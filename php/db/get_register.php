<?php

function getRegister($link, $storeID){

	include('config.php');

	$query = 'SELECT TOP 1 ID id, UUID uuid, Workstation workstation '.
			 'FROM '.$table_register.' Register '.
			 'WHERE Closed = 0 AND StoreID = :storeID AND CreationDate >= CONVERT(DATE,GETDATE())';

	$handle = $link->prepare($query);

	$handle->bindParam(':storeID', $storeID);
	$handle->execute();

	$register = $handle->fetchObject();

	return $register;
}

?>