<?php
	include_once('config.php');

	try{
		if(isset($_GET['ID']) && $_GET['ID'] != "" && isset($_GET['Detail0']) && $_GET['Detail0'] != ""){
			$details = array();

			$id = $_GET['ID'];
			$details[] = $_GET['Detail0'];

			$query = 'SELECT ID, UUID, ExternalID FROM '.$table_itemCombination.' WHERE ItemID = :ID AND optionDetail01ID = :detail0';

			for ($i = 1; isset($_GET['Detail'.$i]); $i++) {
				$details[] = $_GET['Detail'.$i];

				$sDetailNum = ''.($i + 1);
				if ( strlen($sDetailNum) == 1 ) {
					$sDetailNum =  '0'.$sDetailNum;
				}

				$query .= ' AND optionDetail'.$sDetailNum.'ID = :detail'.$i;
			}


			$link = new PDO(   $db_url, 
		                        $user, 
		                        $password,  
		                        array(
		                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		                        ));	

			$handle = $link->prepare($query);

			$handle->bindParam(':ID', $id);
			$handle->bindParam(':detail0', $details[0]);

			$detailsLen = count($details);
			for ($i = 1; $i < $detailsLen; $i++) {
				$handle->bindParam(':detail'.$i, $details[$i]);
			}
		 
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