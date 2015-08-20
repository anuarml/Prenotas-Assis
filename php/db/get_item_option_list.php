<?php
	include_once('../../config/config.php');
	include('global_variables.php');

	try{
		if(isset($_GET['optionID']) && $_GET['optionID'] != ""){
			$optionID = $_GET['optionID'];
			$itemId = $_GET['itemId'];
			$optionListID = isset($_GET['optionListID'])? $_GET['optionListID'] : null;


			$aOptionDetails = json_decode($_GET['aOptionDetails']);
			
			$link = new PDO(   $db_url, 
		                        $user, 
		                        $password,  
		                        array(
		                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		                        ));


			if($optionListID && $optionListID != 'null'){
				$query =
					'SELECT OptionDetail.ID id, OptionDetail.ExternalID externalID, OptionDetail.Name name '.
					'FROM '.$table_optionDetail.' OptionDetail '.
					'JOIN '.$table_optionListDetail.' OptionListDetail ON OptionDetail.ID = OptionListDetail.OptionDetailID '.
					'JOIN '.$table_itemOption.' ItemOption ON OptionListDetail.OptionListID = ItemOption.OptionListID '.
					'WHERE ItemOption.OptionListID = :optionListID '.
					'ORDER BY OptionDetail.Name DESC';
			}
			else{
				$query =
					'SELECT OptionDetail.ID id, OptionDetail.ExternalID externalID, OptionDetail.Name name '.
					'FROM '.$table_optionDetail.' OptionDetail '.
					'WHERE OptionDetail.OptionID = :optionID '.
					'ORDER BY OptionDetail.Name DESC';
			}


			$handle = $link->prepare( $query );


			if($optionListID && $optionListID != 'null'){
				$handle->bindParam(':optionListID', $optionListID);
			}
			else{
				$handle->bindParam(':optionID', $optionID);
			}


		    $handle->execute();


		    if(($aResult = $handle->fetchAll(PDO::FETCH_OBJ)) !== false){
				for($numberOfRow = 0; $numberOfRow < count($aResult); $numberOfRow++){
					$quantity = getQuantityOnHand($aOptionDetails, $itemId, $aResult[$numberOfRow]->id, $link);
					$aResult[$numberOfRow]->quantity = $quantity;
				}
				//error_log(json_encode($aResult));
		    	echo json_encode($aResult);
		    }
		    else echo json_encode(false);
		}
		else echo json_encode(false);
	}
	catch(PDOException $ex){
		error_log($ex->getMessage());
	    print($ex->getMessage());
	}
	
	function getQuantityOnHand($aOptionDetails, $itemId, $optionDetailId, $link){
		include('../../config/config.php');
		include('global_variables.php');
		
		$query = 'SELECT SUM(QuantityOnHand) quantity FROM '.$table_inventoryOnHand.' ih JOIN 
		'.$table_itemCombination.' itemCombination ON ItemCombinationID = itemCombination.ID where ih.ItemID = :itemId';
		
		for($numberOfOptions = 0; $numberOfOptions < count($aOptionDetails) + 1; $numberOfOptions++){
			if($numberOfOptions < 9){
				$query.= ' AND OptionDetail0'.($numberOfOptions + 1).'ID = :optionDetail0'.($numberOfOptions + 1).'ID' ;
				
			}else if($numberOfOptions == 9){
				$query.= ' AND OptionDetail10ID = :optionDetail10ID' ;
			}
		}
		
		$handle = $link->prepare($query);
		
		for($numberOfOptions = 0; $numberOfOptions < count($aOptionDetails); $numberOfOptions++){
			if($numberOfOptions < 9){
				$handle->bindParam(':optionDetail0'.($numberOfOptions + 1).'ID', $aOptionDetails[$numberOfOptions]);
			}else if($numberOfOptions == 9){
				$handle->bindParam(':optionDetail10ID', $aOptionDetails[$numberOfOptions]);
			}
		}
		
		if($numberOfOptions < 9){
			$handle->bindParam(':optionDetail0'.($numberOfOptions + 1).'ID', $optionDetailId);
		}else if($numberOfOptions == 9){
			$handle->bindParam(':optionDetail10ID', $optionDetailId);
		}
		
		$handle->bindParam(':itemId', $itemId);                                                     
		$handle->execute();
		$quantity = 0;
		
		if(($aResultOfQuantity = $handle->fetch(PDO::FETCH_OBJ)) !== false){
			$quantity =  $aResultOfQuantity->quantity;
		}
		
		return $quantity;
		
		/*select SUM(QuantityOnHand) quantity from InventoryOnHand ih 

		join ItemCombination ic on ItemCombinationID=ic.ID join OptionDetail od on OptionDetail01ID=od.ID


		where ih.ItemID= 203 and OptionDetail01ID = 13 and OptionDetail02ID = 14 and OptionDetail03ID = 6 */
		
		
	}
?>