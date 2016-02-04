<?php
	include('../../config/config.php');
	include('global_variables.php');

	try{
		if( !empty ($_GET['ItemID']) ){
			$id = $_GET['ItemID'];
			$combinationID = $_GET['combinationID'];
			$useSerial = $_GET['useSerial'];
			$useBatch = $_GET['useBatch'];

			//$query = 'SELECT ID, UUID, Quantity, SerialBatch FROM '.$table_itemSerial.' WHERE ItemID = :ItemID ';
			// Se cambia el campo SerialBatch por Serial y Batch. Y la tabla ItemSerial por InventoryOnHandDetail
			$query = 'SELECT InventoryOnHandD.ID, InventoryOnHandD.UUID, InventoryOnHandD.Quantity, InventoryOnHandD.Serial, InventoryOnHandD.Batch FROM '.$table_inventoryOnHand.' InventoryOnHand '.
							 'JOIN '.$table_inventoryOnHandDetail.' InventoryOnHandD ON InventoryOnHandD.InventoryOnHandUUID = InventoryOnHand.UUID '.
							 'WHERE InventoryOnHand.ItemID = :ItemID ';

			if( !empty($combinationID) ){
				$query .= ' AND InventoryOnHand.ItemCombinationID = :combinationID';
			}

			$query .= ' ORDER BY InventoryOnHandD.Serial DESC, InventoryOnHandD.Batch DESC';
			
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

		    if( ($itemSerials = $handle->fetchAll(PDO::FETCH_OBJ)) !== false){

		    	$resLen = count($itemSerials);

		    	for ($i=0; $i < $resLen; $i++) { 
		    		$itemSerial = $itemSerials[$i];

		    		if($useSerial){
		    			$itemSerial->SerialBatch = $itemSerial->Serial;
		    		}
		    		else if($useBatch){
		    			$itemSerial->SerialBatch = $itemSerial->Batch;
		    		}
		    	}

		    	echo json_encode($itemSerials);
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