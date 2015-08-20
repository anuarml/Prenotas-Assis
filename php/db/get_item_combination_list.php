<?php
	include_once('../../config/config.php');
	include('global_variables.php');

	try{
		if(isset($_GET['ID']) && $_GET['ID'] != ""){
			$id = $_GET['ID'];
			
			
			$link = new PDO(   $db_url, 
		                        $user, 
		                        $password,  
		                        array(
		                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		                        ));

			//$handle = $link->prepare('SELECT '.$table_itemCombination.'.ID, '.$table_itemCombination.'.ExternalID optionEID, '.$table_optionDetail.'.Name optionDetailName, '.$table_option.'.Name optionName, '.$table_itemCombination.'.UUID  FROM '.$table_itemCombination.' ic LEFT JOIN '.$table_optionDetail.' od ON '.$table_optionDetail.'.ExternalID = '.$table_itemCombination.'.ExternalID LEFT JOIN '.$table_option.' o ON '.$table_option.'.ID = OptionID WHERE ItemID = :ID');
			//$handle = $link->prepare('SELECT ic.ID, ic.ExternalID optionEID, od.Name optionDetailName, o.Name optionName, ic.UUID  FROM '.$table_itemCombination.' ic LEFT JOIN '.$table_optionDetail.' od ON od.ExternalID = ic.ExternalID LEFT JOIN '.$table_option.' o ON o.ID = OptionID WHERE ItemID = :ID');

			$handle = $link->prepare(
				'SELECT itemCombination.ID, itemCombination.UUID, itemCombination.ExternalID combinationEID,'.
				'optionDetail1.ExternalID optionEID1, optionDetail1.Name optionDName1,'.
				'optionDetail2.ExternalID optionEID2, optionDetail2.Name optionDName2,'.
				'optionDetail3.ExternalID optionEID3, optionDetail3.Name optionDName3,'.
				'optionDetail4.ExternalID optionEID4, optionDetail4.Name optionDName4,'.
				'optionDetail5.ExternalID optionEID5, optionDetail5.Name optionDName5,'.
				'optionDetail6.ExternalID optionEID6, optionDetail6.Name optionDName6,'.
				'optionDetail7.ExternalID optionEID7, optionDetail7.Name optionDName7,'.
				'optionDetail8.ExternalID optionEID8, optionDetail8.Name optionDName8,'.
				'optionDetail9.ExternalID optionEID9, optionDetail9.Name optionDName9,'.
				'optionDetail0.ExternalID optionEID10, optionDetail0.Name optionDName10,'.
				'option1.ID optionID1,option1.Name optionName1,'.
				'option2.ID optionID2,option2.Name optionName2,'.
				'option3.ID optionID3,option3.Name optionName3,'.
				'option4.ID optionID4,option4.Name optionName4,'.
				'option5.ID optionID5,option5.Name optionName5,'.
				'option6.ID optionID6,option6.Name optionName6,'.
				'option7.ID optionID7,option7.Name optionName7,'.
				'option8.ID optionID8,option8.Name optionName8,'.
				'option9.ID optionID9,option9.Name optionName9,'.
				'option10.ID optionID10,option10.Name optionName10,'.
				'quantityOnHand'.

				' FROM '.$table_itemCombination.' itemCombination '.

				'left join '.$table_optionDetail.' optionDetail1 ON optionDetail1.ID = itemCombination.OptionDetail01ID '.
				'left join '.$table_optionDetail.' optionDetail2 ON optionDetail2.ID = itemCombination.OptionDetail02ID '.
				'left join '.$table_optionDetail.' optionDetail3 ON optionDetail3.ID = itemCombination.OptionDetail03ID '.
				'left join '.$table_optionDetail.' optionDetail4 ON optionDetail4.ID = itemCombination.OptionDetail04ID '.
				'left join '.$table_optionDetail.' optionDetail5 ON optionDetail5.ID = itemCombination.OptionDetail05ID '.
				'left join '.$table_optionDetail.' optionDetail6 ON optionDetail6.ID = itemCombination.OptionDetail06ID '.
				'left join '.$table_optionDetail.' optionDetail7 ON optionDetail7.ID = itemCombination.OptionDetail07ID '.
				'left join '.$table_optionDetail.' optionDetail8 ON optionDetail8.ID = itemCombination.OptionDetail08ID '.
				'left join '.$table_optionDetail.' optionDetail9 ON optionDetail9.ID = itemCombination.OptionDetail09ID '.
				'left join '.$table_optionDetail.' optionDetail0 ON optionDetail0.ID = itemCombination.OptionDetail10ID '.

				'left join '.$table_option.' option1 ON option1.ID = optionDetail1.OptionID '.
				'left join '.$table_option.' option2 ON option2.ID = optionDetail2.OptionID '.
				'left join '.$table_option.' option3 ON option3.ID = optionDetail3.OptionID '.
				'left join '.$table_option.' option4 ON option4.ID = optionDetail4.OptionID '.
				'left join '.$table_option.' option5 ON option5.ID = optionDetail5.OptionID '.
				'left join '.$table_option.' option6 ON option6.ID = optionDetail6.OptionID '.
				'left join '.$table_option.' option7 ON option7.ID = optionDetail7.OptionID '.
				'left join '.$table_option.' option8 ON option8.ID = optionDetail8.OptionID '.
				'left join '.$table_option.' option9 ON option9.ID = optionDetail9.OptionID '.
				'left join '.$table_option.' option10 ON option10.ID = optionDetail0.OptionID '.

				'left join '.$table_inventoryOnHand.' inventoryOnHand ON inventoryOnHand.ItemID = itemCombination.ItemID AND inventoryOnHand.ItemCombinationID = itemCombination.ID'.

				' WHERE itemCombination.ItemID = :ID'
			);

			$handle->bindParam(':ID', $id);
		 
		    $handle->execute();

			// product.optionEID = '';
			// product.optionName = '';
			// product.optionUUID = '';
			// product.optionID = 0;
			// product.QuantityOnHand = 0;

		    if(($aResult = $handle->fetchAll(PDO::FETCH_OBJ)) !== false){
		    	//getQuantityOnHand($link, $result, $id);
		    	$aResultLen = count($aResult);

		    	for($i=0; $i<$aResultLen; $i++){
		    		$result=$aResult[$i];

		    		$combination = new stdClass();
		    		$combination->ID = $result->ID;
		    		$combination->UUID = $result->UUID;
		    		$combination->EID = $result->combinationEID;
		    		$combination->quantityOnHand = $result->quantityOnHand;
		    		$combination->detail = array();

		    		for($j=1; $j<=10; $j++){
		    			if( $result['optionID'.$j] ){

		    				if( isset($combination->detail[ $result['optionID'.$j] ]) ){
		    					$combinationDetail = new stdClass();

		    					$combinationDetail->optionDEID = $result['optionEID'.$j];
					    		$combinationDetail->optionDName = $result['optionDName'.$j];

					    		$combination->detail[ $result['optionID'.$j] ] = $combinationDetail;
		    				}
		    				else{

		    				}

		    				$option = new stdClass();

				    		$option->optionID = $result['optionID'.$j];
				    		$option->optionName = $result['optionName'.$j];
				    		$option->detail

				    		$combinationDetail->optionDEID = $result['optionEID'.$j];
				    		$combinationDetail->optionDName = $result['optionDName'.$j];

				    		$combination->detail[ $result['optionID'.$j] ][] = $combinationDetail;
				    	}
		    		}
		    	}
		    	
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

	/*function getQuantityOnHand($link, $result, $itemID){
		include('../../config/config.php');
		include('global_variables.php');

		$handle = $link->prepare('SELECT QuantityOnHand FROM '.$table_inventoryOnHand.' WHERE ItemID = :itemID AND ItemCombinationID = :combinationID');

		$handle->bindParam(':itemID', $itemID);
		$handle->bindParam(':combinationID', $combinationID);

		$resultLen = count($result);

		for($i=0; $i<$resultLen; $i++){
			$combinationID = $result[$i]->ID;

			$handle->execute();

			$quantityOnHand = 0;

			$quantityInfo = $handle->fetchObject();

			if($quantityInfo){
				$quantityOnHand = $quantityInfo->QuantityOnHand;
			}

			$result[$i]->QuantityOnHand = $quantityOnHand;
		}

	}*/
?>