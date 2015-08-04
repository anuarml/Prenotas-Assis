<?php
	
	function save_products($link, $prenote_uuid, $product, $id_employee, $lastUpdate){
		include('config.php');
		include_once('../class.uuid.php');
		
		
		
		$handle = $link->prepare('INSERT INTO '.$table_prenoteDetails.' (ID, UUID, CreationDate, LastUpdate, CreationUserID, LastUpdateUserID,  OperationOnHoldUUID, ItemUUID, ItemCombinationID, ItemCombinationUUID, ItemSerialID, ItemBarcode, UnitID, Quantity, UnitPrice, SalesPersonUserID, ParentID) VALUES (0, :UUID, :creationDate, :last_update, :create_id, :update_id, :prenote_uuid, :ItemUUID, :combinationID, :combinationUUID, :serialID, :barcode, :unitID, :Quantity, :Price, :id_employee, 0)');
				
		$handle->bindParam(':UUID', $prenoteDetails_uuid);
		$handle->bindParam(':creationDate', $lastUpdate);
		$handle->bindParam(':last_update', $lastUpdate);
		$handle->bindParam(':id_employee', $id_employee, PDO::PARAM_INT);
		$handle->bindParam(':create_id', $id_employee, PDO::PARAM_INT);
		$handle->bindParam(':update_id', $id_employee, PDO::PARAM_INT);
		$handle->bindParam(':prenote_uuid', $prenote_uuid);
				
		$handle->bindParam(':ItemUUID', $UUID);
		$handle->bindParam(':unitID', $UnitID, PDO::PARAM_INT);
		$handle->bindParam(':Quantity', $Quantity);
		$handle->bindParam(':Price', $Price);
		$handle->bindParam(':serialID', $serialID);
		$handle->bindParam(':barcode', $barcode);
		$handle->bindParam(':combinationID', $combinationID);
		$handle->bindParam(':combinationUUID', $combinationUUID);
		
		
	 	$lenght = count($product);

		for($i=0;$i<$lenght;$i++){
			$prenoteDetails_uuid = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING);

			$UUID = $product[$i]->UUID;
			$Quantity = $product[$i]->Quantity;
			$Price = $product[$i]->Price;
			$UnitID = $product[$i]->UnitID;
			$serialID = $product[$i]->SerialID;
			$barcode = $product[$i]->barcode;
			$combinationID = $product[$i]->optionID;
			$combinationUUID = $product[$i]->optionUUID;

			$handle->execute();
		}
	}

?>