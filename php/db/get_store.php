<?php
	function getStore($link){
		include('config.php');

		$store = null;

		$handle = $link->prepare('SELECT ID id, Name name, [Number] num FROM '.$table_store.' WHERE [Number] = :number');

		$handle->bindParam(':number', $cfg_store);
		$handle->execute();

		$store = $handle->fetch(PDO::FETCH_OBJ);

		return $store;
	}
?>