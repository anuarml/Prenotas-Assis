<?php

function createItemCombination($link, $userID, $itemID, $details, $combExternalID){
	include('config.php');
	require('create_sync.php');

	$uuid = null;
	$lastUpdate = null;
	$itemPrice = 0;

	$statement = $link->query('SELECT NEWID() AS uuid, GETDATE() AS lastDate');

	$result = $statement->fetchObject();

	if(!$result || !($uuid = $result->uuid) || !($lastUpdate = $result->lastDate)){
		throw new PDOException('Error al generar el id y/o fecha de la combinación.');
	}

	$handle = $link->prepare('SELECT Item.Price price FROM '.$table_item.' Item WHERE Item.ID = :itemID');
	$handle->bindParam(':itemID',$itemID);
	$handle->execute();
	
	$result = $handle->fetchObject();

	if( $result && $result->price ){
		$itemPrice = $result->price;
	}

	$query =
		'INSERT INTO '.$table_itemCombination.' '.
		'(ID, UUID, CreationUserID, LastUpdate, LastUpdateUserID, ItemID, LastCost, AverageCost, FirstPrice, PreviousPrice, ReferencePrice, Price, PriceStatusID, ExternalID, OptionDetail01ID, OptionDetail02ID, OptionDetail03ID, OptionDetail04ID, OptionDetail05ID, OptionDetail06ID, OptionDetail07ID, OptionDetail08ID, OptionDetail09ID, OptionDetail10ID, RecordStatusID) '.
		'VALUES '.
		'(0, :UUID, :createUserID, :lastUpdate, :updateUserID, :itemID, 0, 0, 0, 0, 0, :itemPrice, 0, :externalID, :optionDetail01ID, :optionDetail02ID, :optionDetail03ID, :optionDetail04ID, :optionDetail05ID, :optionDetail06ID, :optionDetail07ID, :optionDetail08ID, :optionDetail09ID, :optionDetail10ID, 1)';

	$handle = $link->prepare($query);

	$handle->bindParam(':UUID', $uuid);
	$handle->bindParam(':createUserID', $userID);
	$handle->bindParam(':lastUpdate', $lastUpdate);
	$handle->bindParam(':updateUserID', $userID);
	$handle->bindParam(':itemID', $itemID);
	$handle->bindParam(':itemPrice', $itemPrice);
	$handle->bindParam(':externalID', $combExternalID);

	$detailsLen = count($details);

	for ($optionNum=0; $optionNum < $MAX_ITEM_COMBINATION_OPTIONS; $optionNum++) { 
		
		$sDetailNum = ''.($optionNum + 1);
		if ( strlen($sDetailNum) == 1 ) {
			$sDetailNum =  '0'.$sDetailNum;
		}

		if($optionNum < $detailsLen){
			$handle->bindParam(':optionDetail'.$sDetailNum.'ID', $details[$optionNum]);
		}
		else{
			$handle->bindValue(':optionDetail'.$sDetailNum.'ID', '0');
		}
	}

	/*$handle->bindParam(':optionDetail01ID', $optionDetail01ID);
	$handle->bindParam(':optionDetail02ID', $optionDetail02ID);
	$handle->bindParam(':optionDetail03ID', $optionDetail03ID);
	$handle->bindParam(':optionDetail04ID', $optionDetail04ID);
	$handle->bindParam(':optionDetail05ID', $optionDetail05ID);
	$handle->bindParam(':optionDetail06ID', $optionDetail06ID);
	$handle->bindParam(':optionDetail07ID', $optionDetail07ID);
	$handle->bindParam(':optionDetail08ID', $optionDetail08ID);
	$handle->bindParam(':optionDetail09ID', $optionDetail09ID);
	$handle->bindParam(':optionDetail10ID', $optionDetail10ID);*/

	$handle->execute();

	// Indica a la base de datos que tiene que sincronizar la tabla con la BD central.
	createSync($link, $uuid, $lastUpdate, strtoupper($table_itemCombination), 'F');
}