<?php

 //CONDICIONAR LA UNIDAD Y LA OPCION CUANDO ES NULL CAMBAR POR IS NULL
	include('config.php');

	$response = (object) array('status'=>null,'msg'=>null);

	try{
		if(isset($_GET['ItemID']) && $_GET['ItemID'] != ""){
			
			$itemID = $_GET['ItemID'];
			$itemCombinationID = isset($_GET['itemCombinationID']) || null;
			$unitID = isset($_GET['unitID']) || null;
			
			$link = new PDO(
				$db_url, 
		        $user, 
		        $password,  
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ));

			$handle = $link->prepare(
				'SELECT'.
				' PriceListDetail.Price'.
				' FROM '.$table_priceListDetail.' PriceListDetail'.
				' JOIN '.$table_priceList.' PriceList'.
				' ON PriceListDetail.PriceListID = PriceList.ID'.
				' JOIN '.$table_currency.' Currency'.
				' ON PriceListDetail.CurrencyID = Currency.ID'.
				' WHERE PriceListDetail.ItemID = :ItemID'.
				' AND PriceList.Name = :priceList'.
				' AND PriceListDetail.ItemCombinationID = :itemCombinationID'.
				' AND PriceListDetail.UnitID = :unitID'.
				' AND Currency.Name = :currency'
			);

			$handle->bindParam(':ItemID', $itemID);
			$handle->bindParam(':priceList', $cfg_priceList);
			$handle->bindParam(':itemCombinationID', $itemCombinationID);
			$handle->bindParam(':unitID', $unitID);
			$handle->bindParam(':currency', $cfg_currency);
		 
		    $handle->execute();

		    $itemPriceList = null;

		    if( ($itemPriceList = $handle->fetchAll(PDO::FETCH_OBJ)) == false){
		    	$itemPriceList = null;
		    }

		    $response->status = 'success';
			$response->msg = $itemPriceList;

			$responseEncoded = json_encode($response);
			
			echo $responseEncoded;
			exit(0);
		}
		else {
			$response->status = 'error';
			$response->msg = 'Faltó especificar un artículo.';

			$responseEncoded = json_encode($response);
			
			error_log($responseEncoded);
			echo $responseEncoded;
		}
    }
	catch(PDOException $ex){
	    $response->status = 'error';
		$response->msg = $ex->getMessage();

		$responseEncoded = json_encode($response);
		
		error_log($responseEncoded);
		echo $responseEncoded;
	}
?>