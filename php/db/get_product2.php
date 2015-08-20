<?php
	include('../../config/config.php');
	include('global_variables.php');

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
					// Si está activado en la configuración, se valida que el artículo tenga rama.
					if($cfg_validate_branch && !$item->CategoryBranchID){
						throw new PDOException('El artículo no tiene rama.');
					}

					// Se valida que el estatus del artículo no sea 'BAJA'.
					if($item->itemStatus == $CONST_ITEM_STATUS_INACTIVE){
						throw new PDOException('Artículo dado de baja.');
					}

					// Se valida que el artículo no sea de tipo 'Juego'.
					if($item->itemType == $CONST_ITEM_TYPE_KIT){
						throw new PDOException('No se permiten artículos tipo: Juego.');
					}

					$item->basePrice = $item->Price;
					$item->barcode = $itemBarcode->Barcode;

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
					//json_encode($item);
					echo createResponse(true, $item);

				} else {
					echo createResponse(false, 'Artículo no registrado.');
				}

			} else { // Se busca el código en la tabla de items.
				$item = getItemByCode($link, $code);

				if ($item) {

					// Si está activado en la configuración, se valida que el artículo tenga rama.
					if($cfg_validate_branch && !$item->CategoryBranchID){
						throw new PDOException('El artículo no tiene rama.');
					}

					// Se valida que el estatus del artículo no sea 'BAJA'.
					if($item->itemStatus == $CONST_ITEM_STATUS_INACTIVE){
						throw new PDOException('Artículo dado de baja.');
					}

					// Se valida que el artículo no sea de tipo 'Juego'.
					if($item->itemType == $CONST_ITEM_TYPE_KIT){
						throw new PDOException('No se permiten artículos tipo: Juego.');
					}

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

					echo createResponse(true, $item);

				} else {
					echo createResponse(false, 'Artículo no registrado.');
				}
			}
		} else {
			echo createResponse(false, 'Artículo no registrado.');
		}
	}
	catch(PDOException $ex){
		error_log($ex->getMessage());
	    echo createResponse(false, $ex->getMessage());
	}

	function getItemBarcode($link, $code){
		include('../../config/config.php');
		include('global_variables.php');

		$handle = $link->prepare('SELECT Barcode, ItemID, itemCombinationID, unitID FROM '.$table_itembarcode.' WHERE Barcode = :code');
		$handle->bindParam(':code', $code);
	    $handle->execute();

	    $itemBarcode = $handle->fetchObject();

	    return $itemBarcode;
	}

	function getItemByID($link, $id){
		include('../../config/config.php');
		include('global_variables.php');

		$query =
			'SELECT TOP 1 Item.ID, Item.UUID, Item.Code, '.
			'Item.Description, Item.ItemTypeID, Item.Price, '.
			'Item.UnitID, Item.UseCombination, Item.SerialInfoOptional isSerialInformative, '.
			'Item.CategoryBranchID, '.
			'ItemType.Name itemType, ItemStatus.Name itemStatus '.
			'FROM '.$table_item.' Item '.
			'JOIN '.$table_itemType.' ItemType '.
			'ON ItemType.ID = Item.ItemTypeID '.
			'JOIN '.$table_itemStatus.' ItemStatus '.
			'ON ItemStatus.ID = Item.ItemStatusID '.
			'WHERE Item.ID = :ID';

		//$handle = $link->prepare('SELECT '.$table_item.'.ID, '.$table_item.'.UUID, '.$table_item.'.Code, Description, ItemTypeID, Price, UnitID, QuantityOnHand, UseCombination FROM '.$table_item.' LEFT JOIN '.$table_inventoryOnHand.' ON '.$table_inventoryOnHand.'.ItemID = '.$table_item.'.ID WHERE '.$table_item.'.ID = :ID');
		$handle = $link->prepare($query);
		$handle->bindParam(':ID', $id);
		$handle->execute();

		//$aItem = $handle->fetchAll(PDO::FETCH_OBJ);
		//error_log(json_encode($aItem));
		
		//$item = $aItem[0];
		$item = $handle->fetchObject();

		return $item;
	}

	function getItemByCode($link, $code){
		include('../../config/config.php');
		include('global_variables.php');

		$query =
			'SELECT TOP 1 Item.ID, Item.UUID, Item.Code, '.
			'Item.Description, Item.ItemTypeID, Item.Price, '.
			'Item.UnitID, Item.UseCombination, Item.SerialInfoOptional isSerialInformative, '.
			'Item.CategoryBranchID, '.
			'ItemType.Name itemType, ItemStatus.Name itemStatus '.
			'FROM '.$table_item.' Item '.
			'JOIN '.$table_itemType.' ItemType '.
			'ON ItemType.ID = Item.ItemTypeID '.
			'JOIN '.$table_itemStatus.' ItemStatus '.
			'ON ItemStatus.ID = Item.ItemStatusID '.
			'WHERE Item.Code = :code';
		//$handle = $link->prepare('SELECT '.$table_item.'.ID, '.$table_item.'.UUID, '.$table_item.'.Code, Description, ItemTypeID, Price, UnitID, QuantityOnHand, UseCombination FROM '.$table_item.' LEFT JOIN '.$table_inventoryOnHand.' ON '.$table_inventoryOnHand.'.ItemID = '.$table_item.'.ID WHERE code = :code');
		$handle = $link->prepare($query);
		$handle->bindParam(':code', $code);
		$handle->execute();

		$item = $handle->fetchObject();

		return $item;
	}

	function getUnitInfo($link, $unitID){
		include('../../config/config.php');
		include('global_variables.php');

		$handle = $link->prepare('SELECT name, decimals FROM '.$table_unit.' WHERE ID = :unitID');
		$handle->bindParam(':unitID', $unitID);
		$handle->execute();

		$unitInfo = $handle->fetchObject();

		return $unitInfo;
	}

	function getCombinationInfo($link, $itemID, $combinationID){
		include('../../config/config.php');
		include('global_variables.php');

		$handle = $link->prepare('SELECT itemCombination.ID, itemCombination.UUID, itemCombination.ExternalID, Name FROM '.$table_itemCombination.' itemCombination LEFT JOIN '.$table_optionDetail.' optD ON optD.ExternalID = itemCombination.ExternalID WHERE ItemID = :ID AND itemCombination.ID = :combination');

		$handle->bindParam(':ID', $itemID);
		$handle->bindParam(':combination', $combinationID);

		$handle->execute();

		$combinationInfo = $handle->fetchObject();

		return $combinationInfo;
	}

	function getQuantityOnHand($link, $itemID, $combinationID){
		include('../../config/config.php');
		include('global_variables.php');

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
		include('../../config/config.php');
		include('global_variables.php');

		$handle = $link->prepare('SELECT'.
			' opt.name, itemOption.optionID, itemOption.mandatory, itemOption.OptionListID optionListID'.

			' FROM '.$table_itemOption.' itemOption'.

			' JOIN '.$table_option.' opt ON itemOption.optionID=opt.ID'.

			' WHERE ItemID = :ID'.

			' ORDER BY opt.externalID'
		);

		$handle->bindParam(':ID', $itemID);
	 
	    $handle->execute();

	    if( ($itemOptions = $handle->fetchAll(PDO::FETCH_OBJ)) == false){
	    	$itemOptions = null;
	    }

	    return $itemOptions;
	}

	function createResponse($status, $data){

		$response = array('status' => $status, 'data' => $data);

		return json_encode($response);
	}
?>