<?php
	include('config.php');
	try{
		if(isset($_GET['ItemID']) && $_GET['ItemID'] != ""){

			$itemID = $_GET['ItemID'];
			$itemCombinationID = isset($_GET['itemCombinationID']) ? $_GET['itemCombinationID'] : null;
			$unitID = isset($_GET['unitID']) ? $_GET['unitID'] : null;
			
			$price = 0;

			$link = new PDO(
				$db_url, 
		        $user, 
		        $password,  
		        array(
		            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		    ));

			//Opcion y Unidad
			if($unitID && $itemCombinationID){
				
				$price = getPriceListPrice($link, $itemID, $unitID, $itemCombinationID);

				if($price != 0){
					echo createResponse('success', $price);
					exit(0);
				}
			}

			//Unidad
			if($unitID){
				$price = getPriceListPrice($link, $itemID, $unitID, null);

				if($price != 0){
					echo createResponse('success', $price);
					exit(0);
				}
			}

			// Opciones
			if($itemCombinationID){
				$price = getPriceListPrice($link, $itemID, null, $itemCombinationID);

				if($price != 0){
					echo createResponse('success', $price);
					exit(0);
				}
			}

			//Lista
			$price = getPriceListPrice($link, $itemID, null, null);

			if($price != 0){
				echo createResponse('success', $price);
				exit(0);
			}

			//Articulo
			$price = getItemPrice($link, $itemID);

			echo createResponse('success', $price);

		}
		else {
			echo createResponse('error', 'Faltó especificar un artículo.');
		}
    }
	catch(PDOException $ex){
		echo createResponse('error', $ex->getMessage());
	}


function getPriceListPrice($link, $itemID, $unitID, $itemCombinationID){
	include('config.php');

    $query = 'SELECT'.
		' PriceListDetail.Price price'.
		' FROM '.$table_priceListDetail.' PriceListDetail'.
		' JOIN '.$table_priceList.' PriceList'.
		' ON PriceListDetail.PriceListID = PriceList.ID'.
		' JOIN '.$table_currency.' Currency'.
		' ON PriceListDetail.CurrencyID = Currency.ID'.
		' WHERE PriceListDetail.ItemID = :ItemID'.
		' AND PriceList.Name = :priceList'.
		' AND Currency.Name = :currency';

	if($itemCombinationID != null){
		$query .= ' AND PriceListDetail.ItemCombinationID = :itemCombinationID';
	}
	else{
		$query .= ' AND PriceListDetail.ItemCombinationID IS NULL';
	}

	if($unitID != null){
		$query .= ' AND PriceListDetail.UnitID = :unitID';
	}
	else{
		$query .= ' AND PriceListDetail.UnitID IS NULL';
	}

	$handle = $link->prepare($query);

	$handle->bindParam(':ItemID', $itemID);
	$handle->bindParam(':priceList', $cfg_priceList);
	$handle->bindParam(':currency', $cfg_currency);

	if($itemCombinationID != null){
		$handle->bindParam(':itemCombinationID', $itemCombinationID);
	}

	if($unitID != null){
		$handle->bindParam(':unitID', $unitID);
	}
 
    $handle->execute();

    $price = 0;

    if( $itemPriceList = $handle->fetch(PDO::FETCH_OBJ) ){
    	$price += $itemPriceList->price;
    }
	
	return $price;
}


function getItemPrice($link, $itemID){
	include('config.php');

    $query = 'SELECT'.
		' Price price'.
		' FROM '.$table_item.
		' WHERE ID = :ItemID';

	$handle = $link->prepare($query);

	$handle->bindParam(':ItemID', $itemID);
 
    $handle->execute();

    $price = 0;

    if( $item = $handle->fetch(PDO::FETCH_OBJ) ){
    	$price += $item->price;
    }
	
	return $price;
}


function createResponse($status, $msg){
	$response = (object) array('status'=>$status,'msg'=> $msg);

	$responseEncoded = json_encode($response);
	
	error_log($responseEncoded);
	return $responseEncoded;
}


?>