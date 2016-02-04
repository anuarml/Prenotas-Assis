<?php
	
	function save_products($link, $prenote_uuid, $product, $id_employee, $lastUpdate){
		include('../../config/config.php');
		include('global_variables.php');
		
		$query = 'INSERT INTO '.$table_prenoteDetails.' (ID, UUID, CreationDate, CreationUserID, LastUpdate,  LastUpdateUserID,  OperationOnHoldUUID, Line, ItemUUID, ItemCombinationID, ItemCombinationUUID, InventoryOnHandDetailID, InventoryOnHandDetailUUID, ItemBarcode, UnitID, Quantity, UnitPrice, SalesPersonUserID, ParentID, RecordStatusID, Serial, Batch) VALUES (0, NEWID(), :creationDate, :create_id, :last_update,  :update_id, :prenote_uuid, :line, :ItemUUID, :combinationID, :combinationUUID, :serialID, :serialUUID, :barcode, :unitID, :Quantity, :Price, :id_employee, 0, :recordStatusID, :serial, :batch)';
		
		$handle = $link->prepare($query);
				
		//$handle->bindParam(':UUID', $prenoteDetails_uuid);
		$handle->bindParam(':creationDate', $lastUpdate);
		$handle->bindParam(':create_id', $id_employee, PDO::PARAM_INT);
		$handle->bindParam(':last_update', $lastUpdate);
		$handle->bindParam(':update_id', $id_employee, PDO::PARAM_INT);
		$handle->bindParam(':prenote_uuid', $prenote_uuid);
		$handle->bindParam(':ItemUUID', $UUID);
		$handle->bindParam(':combinationID', $combinationID);
		$handle->bindParam(':combinationUUID', $combinationUUID);
		$handle->bindParam(':serialID', $serialID);
		$handle->bindParam(':serialUUID', $serialUUID);
		$handle->bindParam(':barcode', $barcode);
		$handle->bindParam(':unitID', $UnitID, PDO::PARAM_INT);
		$handle->bindParam(':Quantity', $Quantity);
		$handle->bindParam(':Price', $Price);
		$handle->bindParam(':id_employee', $id_employee, PDO::PARAM_INT);
		//$handle->bindParam(':itemSerialBatch', $itemSerialBatch); //Se cambia por Serial y Batch
		$handle->bindParam(':line', $line);
		$handle->bindParam(':serial', $serial);
		$handle->bindParam(':batch', $batch);

		$handle->bindValue(':recordStatusID', '1');
		
	 	$lenght = count($product);

		for($i=0;$i<$lenght;$i++){
			//$prenoteDetails_uuid = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING);
			$serial= null;
			$batch = null;

			$UUID = $product[$i]->UUID;
			$Quantity = $product[$i]->Quantity;
			$Price = $product[$i]->Price;
			$UnitID = $product[$i]->UnitID;
			$serialID = $product[$i]->SerialID;
			$serialUUID = $product[$i]->SerialUUID;
			$barcode = $product[$i]->barcode;
			$combinationID = $product[$i]->optionID;
			$combinationUUID = $product[$i]->optionUUID;
			$itemSerialBatch = $product[$i]->serialBatch;
			$line = $i + 1;

			//if($product[$i]->isSerialInformative == 1)
			//	$serialID = -1;

			if($itemSerialBatch == '')
				$itemSerialBatch = null;

			if($product[$i]->useSerial == 1){
				$serial = $itemSerialBatch;
			}

			if($product[$i]->useBatch == 1){
				$batch = $itemSerialBatch;
			}

			$handle->execute();
		}
	}

?>