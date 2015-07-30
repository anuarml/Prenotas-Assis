<?php
	include_once('config.php');

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
				'SELECT ic.ID, ic.UUID, ic.ExternalID combinationEID,'.
				'od1.ExternalID optionEID1, od1.Name optionDName1,'.
				'od2.ExternalID optionEID2, od2.Name optionDName2,'.
				'od3.ExternalID optionEID3, od3.Name optionDName3,'.
				'od4.ExternalID optionEID4, od4.Name optionDName4,'.
				'od5.ExternalID optionEID5, od5.Name optionDName5,'.
				'od6.ExternalID optionEID6, od6.Name optionDName6,'.
				'od7.ExternalID optionEID7, od7.Name optionDName7,'.
				'od8.ExternalID optionEID8, od8.Name optionDName8,'.
				'od9.ExternalID optionEID9, od9.Name optionDName9,'.
				'od10.ExternalID optionEID10, od10.Name optionDName10,'.
				'o1.ID optionID1,o1.Name optionName1,'.
				'o2.ID optionID2,o2.Name optionName2,'.
				'o3.ID optionID3,o3.Name optionName3,'.
				'o4.ID optionID4,o4.Name optionName4,'.
				'o5.ID optionID5,o5.Name optionName5,'.
				'o6.ID optionID6,o6.Name optionName6,'.
				'o7.ID optionID7,o7.Name optionName7,'.
				'o8.ID optionID8,o8.Name optionName8,'.
				'o9.ID optionID9,o9.Name optionName9,'.
				'o10.ID optionID10,o10.Name optionName10,'.
				'quantityOnHand'.

				' FROM '.$table_itemCombination.' ic '.

				'left join '.$table_optionDetail.' od1 ON od1.ID = ic.OptionDetail01ID '.
				'left join '.$table_optionDetail.' od2 ON od2.ID = ic.OptionDetail02ID '.
				'left join '.$table_optionDetail.' od3 ON od3.ID = ic.OptionDetail03ID '.
				'left join '.$table_optionDetail.' od4 ON od4.ID = ic.OptionDetail04ID '.
				'left join '.$table_optionDetail.' od5 ON od5.ID = ic.OptionDetail05ID '.
				'left join '.$table_optionDetail.' od6 ON od6.ID = ic.OptionDetail06ID '.
				'left join '.$table_optionDetail.' od7 ON od7.ID = ic.OptionDetail07ID '.
				'left join '.$table_optionDetail.' od8 ON od8.ID = ic.OptionDetail08ID '.
				'left join '.$table_optionDetail.' od9 ON od9.ID = ic.OptionDetail09ID '.
				'left join '.$table_optionDetail.' od10 ON od10.ID = ic.OptionDetail10ID '.

				'left join '.$table_option.' o1 ON o1.ID = od1.OptionID '.
				'left join '.$table_option.' o2 ON o2.ID = od2.OptionID '.
				'left join '.$table_option.' o3 ON o3.ID = od3.OptionID '.
				'left join '.$table_option.' o4 ON o4.ID = od4.OptionID '.
				'left join '.$table_option.' o5 ON o5.ID = od5.OptionID '.
				'left join '.$table_option.' o6 ON o6.ID = od6.OptionID '.
				'left join '.$table_option.' o7 ON o7.ID = od7.OptionID '.
				'left join '.$table_option.' o8 ON o8.ID = od8.OptionID '.
				'left join '.$table_option.' o9 ON o9.ID = od9.OptionID '.
				'left join '.$table_option.' o10 ON o10.ID = od10.OptionID '.

				'left join '.$table_inventoryOnHand.' ih ON ih.ItemID = ic.ItemID AND ih.ItemCombinationID = ic.ID'.

				' WHERE ic.ItemID = :ID'
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
		include('config.php');

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