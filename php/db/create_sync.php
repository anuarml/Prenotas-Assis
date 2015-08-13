<?php

function createSync($link, $uuid, $lastUpdate, $tableName, $deleted){
	include('config.php');

	$tableName = strtoupper($tableName);

	$query = 
	'INSERT INTO '. $table_sync.
	'(UUID, LastUpdate, TableName, Deleted) '.
	'VALUES(:uuid, :lastUpdate, :tableName, :deleted)';

	$handle = $link->prepare($query);

	$handle->bindParam(':uuid', $uuid);
	$handle->bindParam(':lastUpdate', $lastUpdate);
	$handle->bindParam(':tableName', $tableName);
	$handle->bindParam(':deleted', $deleted);

	$handle->execute();
	
}