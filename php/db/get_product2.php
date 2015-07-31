<?php
	include_once('config.php');

	try{
		if(isset($_GET['code']) && $_GET['code'] != ""){
			$code = $_GET['code'];
			
			$link = new PDO(	$db_url, 
		                        $user, 
		                        $password,  
		                        array(
		                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		                        ));
			
			$item = null;

			// Se busca el código en la tabla de códigos de barras.
			$itemBarcode = getItemBarcode($link, $code);

			if ($itemBarcode) {
				$item = getItemByID($link, $itemBarcode->ItemID);

				if ($item) {
					$item->basePrice = $item->Price;
					$item->barcode = $code;

					if ($itemBarcode->unitID) {
						$item->UnitID = $itemBarcode->unitID;
					}

					if ($itemBarcode->itemCombinationID) {

						$combinationInfo = getCombinationInfo($link, $item->ID, $itemBarcode->itemCombinationID);

						if($combinationInfo){
							$quantityOnHand = getQuantityOnHand($link, $item->ID, $itemBarcode->itemCombinationID);

							$item->optionID = $combinationInfo->ID;
							$item->optionUUID = $combinationInfo->UUID;
							$item->optionEID = $combinationInfo->ExternalID;
							$item->optionName = $combinationInfo->Name;
							$item->QuantityOnHand = $quantityOnHand;
						}
					} else {
						$quantityOnHand = getQuantityOnHand($link, $item->ID, null);
						$item->QuantityOnHand = $quantityOnHand;
					}

					$unitInfo = getUnitInfo($link, $item->UnitID);

					if($unitInfo){
						$item->unitName = $unitInfo->name;
						$item->unitDecimals = $unitInfo->decimals;
					}

					/*$itemOptions = getItemOptions($link, $item->ID);

					error_log(json_encode($itemOptions));

					if($itemOptions){
						$item->options = $itemOptions;
					}*/

					//error_log(json_encode($item));

					echo json_encode($item);

				} else {
					echo json_encode(false);
				}

			} else { // Se busca el código en la tabla de items.
				$item = getItemByCode($link, $code);

				if ($item) {
					$item->basePrice = $item->Price;
					$unitInfo = getUnitInfo($link, $item->UnitID);

					if($unitInfo){
						$item->unitName = $unitInfo->name;
						$item->unitDecimals = $unitInfo->decimals;
					}


					if ($item->UseCombination == 1) {
						$itemOptions = getItemOptions($link, $item->ID);

						if($itemOptions){
							$item->options = $itemOptions;
						}

					} else {
						$quantityOnHand = getQuantityOnHand($link, $item->ID, null);
						$item->QuantityOnHand = $quantityOnHand;
					}


					//error_log(json_encode($item));

					echo json_encode($item);

				} else {
					echo json_encode(false);
				}
			}
		} else {
			echo json_encode(false);
		}
	}
	catch(PDOException $ex){
		error_log($ex->getMessage());
	    echo $ex->getMessage();
	}

	function getItemBarcode($link, $code){
		include('config.php');

		$handle = $link->prepare('SELECT ItemID, itemCombinationID, unitID FROM '.$table_itembarcode.' WHERE Barcode = :code');
		$handle->bindParam(':code', $code);
	    $handle->execute();

	    $itemBarcode = $handle->fetchObject();

	    return $itemBarcode;
	}

	function getItemByID($link, $id){
		include('config.php');

		//$handle = $link->prepare('SELECT '.$table_item.'.ID, '.$table_item.'.UUID, '.$table_item.'.Code, Description, ItemTypeID, Price, UnitID, QuantityOnHand, UseCombination FROM '.$table_item.' LEFT JOIN '.$table_inventoryOnHand.' ON '.$table_inventoryOnHand.'.ItemID = '.$table_item.'.ID WHERE '.$table_item.'.ID = :ID');
		$handle = $link->prepare('SELECT ID, UUID, Code, Description, ItemTypeID, Price, UnitID, UseCombination, serialInfoOptional isSerialInformative FROM '.$table_item.' WHERE '.$table_item.'.ID = :ID');
		$handle->bindParam(':ID', $id);
		$handle->execute();

		$aItem = $handle->fetchAll(PDO::FETCH_OBJ);
		//error_log(json_encode($aItem));
		
		$item = $aItem[0];
		//$item = $handle->fetchObject();

		return $item;
	}

	function getItemByCode($link, $code){
		include('config.php');

		//$handle = $link->prepare('SELECT '.$table_item.'.ID, '.$table_item.'.UUID, '.$table_item.'.Code, Description, ItemTypeID, Price, UnitID, QuantityOnHand, UseCombination FROM '.$table_item.' LEFT JOIN '.$table_inventoryOnHand.' ON '.$table_inventoryOnHand.'.ItemID = '.$table_item.'.ID WHERE code = :code');
		$handle = $link->prepare('SELECT ID, UUID, Code, Description, ItemTypeID, Price, UnitID, UseCombination, serialInfoOptional isSerialInformative FROM '.$table_item.' WHERE code = :code');
		$handle->bindParam(':code', $code);
		$handle->execute();

		$item = $handle->fetchObject();

		return $item;
	}

	function getUnitInfo($link, $unitID){
		include('config.php');

		$handle = $link->prepare('SELECT name, decimals FROM '.$table_unit.' WHERE ID = :unitID');
		$handle->bindParam(':unitID', $unitID);
		$handle->execute();

		$unitInfo = $handle->fetchObject();

		return $unitInfo;
	}

	function getCombinationInfo($link, $itemID, $combinationID){
		include('config.php');

		$handle = $link->prepare('SELECT itmC.ID, itmC.UUID, itmC.ExternalID, Name FROM '.$table_itemCombination.' itmC LEFT JOIN '.$table_optionDetail.' optD ON optD.ExternalID = itmC.ExternalID WHERE ItemID = :ID AND itmC.ID = :combination');

		$handle->bindParam(':ID', $itemID);
		$handle->bindParam(':combination', $combinationID);

		$handle->execute();

		$combinationInfo = $handle->fetchObject();

		return $combinationInfo;
	}

	function getQuantityOnHand($link, $itemID, $combinationID){
		include('config.php');

		$query = 'SELECT QuantityOnHand FROM '.$table_inventoryOnHand.' WHERE ItemID = :itemID';

		if ($combinationID) {
			$query .= ' AND ItemCombinationID = :combinationID';
		}

		$handle = $link->prepare($query);

		$handle->bindParam(':itemID', $itemID);

		if ($combinationID) {
			$handle->bindParam(':combinationID', $combinationID);
		}

		$handle->execute();

		$quantityOnHand = 0;

		$quantityInfo = $handle->fetchObject();

		if($quantityInfo){
			$quantityOnHand = $quantityInfo->QuantityOnHand;
		}

		return $quantityOnHand;
	}

	function getItemOptions($link, $itemID){
		include('config.php');

		$handle = $link->prepare('SELECT'.
			' o.name, ito.optionID, ito.mandatory, ito.OptionListID optionListID'.

			' FROM '.$table_itemOption.' ito'.

			' JOIN '.$table_option.' o ON ito.optionID=o.ID'.

			' WHERE ItemID = :ID'.

			' ORDER BY o.externalID'
		);

		$handle->bindParam(':ID', $itemID);
	 
	    $handle->execute();

	    if( ($itemOptions = $handle->fetchAll(PDO::FETCH_OBJ)) == false){
	    	$itemOptions = null;
	    }

	    return $itemOptions;
	}
?>