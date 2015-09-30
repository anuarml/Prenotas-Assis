<?php
	include('../../config/config.php');
	include('global_variables.php');

	try{
		if( !empty ($_GET['ItemID']) ){
			$id = $_GET['ItemID'];
			$combinationID = $_GET['combinationID'];

			$query = 'SELECT ID, UUID, Quantity, SerialBatch FROM '.$table_itemSerial.' WHERE ItemID = :ItemID ORDER BY SerialBatch DESC';

			if( !empty($combinationID) ){
				$query .= ' AND ItemCombinationID = :combinationID';
			}
			
			$link = new PDO(   $db_url, 
		                        $user, 
		                        $password,  
		                        array(
		                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		                        ));

			$handle = $link->prepare($query);

			$handle->bindParam(':ItemID', $id);

			if( !empty($combinationID) ){
				$handle->bindParam(':combinationID', $combinationID);
			}
		 
		    $handle->execute();

		    if( ($result = $handle->fetchAll(PDO::FETCH_OBJ)) !== false){
		    	echo json_encode($result);
		    }
		    else echo json_encode(false);
		}
		else echo json_encode(false);
	}
	catch(PDOException $ex){
		error_log($ex->getMessage());
	    print($ex->getMessage());
	}
?>