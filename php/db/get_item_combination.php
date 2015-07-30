<?php
	include_once('config.php');

	try{
		if(isset($_GET['combination']) && $_GET['combination'] != ""){
			$combination = $_GET['combination'];
			$id = $_GET['ID'];
			
			$link = new PDO(   $db_url, 
		                        $user, 
		                        $password,  
		                        array(
		                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		                        ));	

			$handle = $link->prepare('SELECT '.$table_itemCombination.'.ID, '.$table_itemCombination.'.UUID, '.$table_itemCombination.'.ExternalID, Name FROM '.$table_itemCombination.' LEFT JOIN '.$table_optionDetail.' ON '.$table_optionDetail.'.ExternalID = '.$table_itemCombination.'.ExternalID WHERE ItemID = :ID AND '.$table_itemCombination.'.ExternalID = :combination');

			$handle->bindParam(':ID', $id);
			$handle->bindParam(':combination', $combination);
		 
		    $handle->execute();

		    if($result = $handle->fetchObject()){

		    	$result->QuantityOnHand = getQuantityOnHand($link, $id, $result->ID);

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