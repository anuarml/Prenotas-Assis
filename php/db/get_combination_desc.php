<?php

function get_combination_desc($link, $itemCombinationUUID){
	include('config.php');

	//try{
		//if(isset($_GET['itemCombinationUUID']) && $_GET['itemCombinationUUID'] != ""){
		$optionsDesc = '';

		if($itemCombinationUUID){

			//$itemCombinationUUID = $_GET['itemCombinationUUID'];

			//$link = new PDO(
			//	$db_url, 
		    //    $user, 
		    //    $password,  
		    //    array(
		    //        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		    //));

			$query = 		    
				'SELECT o.Name optionType, OptionDetail.Name optionName '.
				'FROM OptionDetail '.
				'JOIN [Option] o ON optionID = o.ID '.
				'WHERE OptionDetail.ID IN ('.
					'SELECT OptionDetailID FROM ('.
						'SELECT OptionDetail01ID,'.
							   'OptionDetail02ID,'.
							   'OptionDetail03ID,'.
							   'OptionDetail04ID,'.
							   'OptionDetail05ID,'.
							   'OptionDetail06ID,'.
							   'OptionDetail07ID,'.
							   'OptionDetail08ID,'.
							   'OptionDetail09ID,'.
							   'OptionDetail10ID '.
						'FROM ItemCombination '.
						'WHERE UUID = :itemCombinationUUID '.
					') AS SourceTable '.
					'UNPIVOT ('.
						'OptionDetailID FOR ID IN ('.
							'OptionDetail01ID,'.
							'OptionDetail02ID,'.
							'OptionDetail03ID,'.
							'OptionDetail04ID,'.
							'OptionDetail05ID,'.
							'OptionDetail06ID,'.
							'OptionDetail07ID,'.
							'OptionDetail08ID,'.
							'OptionDetail09ID,'.
							'OptionDetail10ID'.
						')'.
					') AS PivotTable'.
				')';
			
			$handle = $link->prepare($query);

			$handle->bindParam(':itemCombinationUUID', $itemCombinationUUID);

			$handle->execute();

			if( $optionDescList = $handle->fetchAll(PDO::FETCH_OBJ) ){

				$optionListLen = count($optionDescList);

				for($i = 0; $i < $optionListLen; $i++){
	    			
	    			if($i > 0){
	    				$optionsDesc .= ' | ';
	    			}
	    			$optionsDesc .= $optionDescList[$i]->optionName;
				}
			}

			//echo json_encode(array('status'=>'success','data'=>$optionDescList));
		}
		else throw new PDOException('No se tienen opciones');

		return $optionsDesc;
		
	//}
	//catch(PDOException $ex){
	//	error_log($ex->getMessage());
	//	echo json_encode(array('status'=>'error','data'=>$ex->getMessage()));
	//}
}
