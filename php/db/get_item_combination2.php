<?php
	include_once('config.php');
	include_once('get_item_combination2.php');

	try{
		if( isset($_GET['ID']) && $_GET['ID'] != "" && 
			isset($_GET['Detail0']) && $_GET['Detail0'] != "" &&
			isset($_GET['combExternalID']) && $_GET['combExternalID'] != ""
		){

			$lastOption = 0;
			$details = array();

			$itemID = $_GET['ID'];
			$details[] = $_GET['Detail0'];
			$combExternalID = $_GET['combExternalID'];

			$query = 'SELECT ID, UUID, ExternalID FROM '.$table_itemCombination.' WHERE ItemID = :ID AND optionDetail01ID = :detail0';

			for ($i = 1; isset($_GET['Detail'.$i]); $i++) {
				$details[] = $_GET['Detail'.$i];

				$sDetailNum = ''.($i + 1);
				if ( strlen($sDetailNum) == 1 ) {
					$sDetailNum =  '0'.$sDetailNum;
				}

				$query .= ' AND optionDetail'.$sDetailNum.'ID = :detail'.$i;

				$lastOption = $i;
			}

			// Se agrega el siguiente optionDetailID en 0 para que no tome una opción parecida. ej: C1 y C1T2
			if( $lastOption >= 0 && $lastOption < $MAX_ITEM_COMBINATION_OPTIONS - 1 ){
				$nextOption = $lastOption + 1;

				$detailNum = $nextOption + 1;
				$sDetailNum = ($detailNum<10?'0':'').$detailNum;

				$query .= ' AND optionDetail'.$sDetailNum.'ID = 0';
			}


			$link = new PDO(   $db_url, 
		                        $user, 
		                        $password,  
		                        array(
		                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		                        ));	

			$handle = $link->prepare($query);

			$handle->bindParam(':ID', $itemID);
			$handle->bindParam(':detail0', $details[0]);

			$detailsLen = count($details);
			for ($i = 1; $i < $detailsLen; $i++) {
				$handle->bindParam(':detail'.$i, $details[$i]);
			}
		 
		    $handle->execute();

		    if($combination = $handle->fetchObject()){

		    	$combination->QuantityOnHand = getQuantityOnHand($link, $itemID, $combination->ID);

		    	echo json_encode($combination);
		    }
		    else{ //echo json_encode(false);
		    	$combination = createItemCombination($link, $userID, $itemID, $details, $combExternalID);

		    	echo json_encode($combination);
		    }
		}
		else echo json_encode(false);
	}
	catch(PDOException $ex){
		error_log($ex->getMessage());
	    print($ex->getMessage());
	}

	function getQuantityOnHand($link, $itemID, $combinationID){
		include('config.php');

		$handle = $link->prepare('SELECT QuantityOnHand FROM '.$table_inventoryOnHand.' WHERE ItemID = :itemID AND ItemCombinationID = :combinationID');

		$handle->bindParam(':itemID', $itemID);
		$handle->bindParam(':combinationID', $combinationID);

		$handle->execute();

		$quantityOnHand = 0;

		$quantityInfo = $handle->fetchObject();

		if($quantityInfo){
			$quantityOnHand = $quantityInfo->QuantityOnHand;
		}

		return $quantityOnHand;
	}
?>