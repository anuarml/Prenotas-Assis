<?php
	include_once('config.php');

	try{
		$itemid = $_GET['itemID'];
		
		$link = new PDO(   $db_url, 
	                        $user, 
	                        $password,  
	                        array(
	                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	                        ));	

		//$handle = $link->prepare('SELECT unitID, name, factor FROM '.$table_itemUnit.' itmUnit LEFT JOIN '.$table_unit.' unit ON UnitID = unit.ID WHERE ItemID = :itemID');
		$handle = $link->prepare('SELECT itmUnit.unitID, unit.name, itmUnit.factor FROM '.$table_itemUnit.' itmUnit LEFT JOIN '.$table_unit.' unit ON itmUnit.UnitID = unit.ID WHERE itmUnit.ItemID = :itemID');

		$handle->bindParam(':itemID', $itemid);
	 
	    $handle->execute();

	    if( ($result = $handle->fetchAll(PDO::FETCH_OBJ)) !== false){
	    	echo json_encode($result);
	    }
	    else echo json_encode(false);

	}
	catch(PDOException $ex){
		error_log($ex->getMessage());
	    print($ex->getMessage());
	}
?>