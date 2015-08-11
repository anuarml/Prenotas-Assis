<?php
	include('config.php');

	try{
		if(isset($_GET['itemCombinationID']) && $_GET['itemCombinationID'] != ""){

			$itemCombinationID = $_GET['itemCombinationID'];

			$link = new PDO(
				$db_url, 
		        $user, 
		        $password,  
		        array(
		            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		    ));

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
						'WHERE ID = :itemCombinationID '.
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

			$handle->bindParam(':itemCombinationID', $itemCombinationID);

			$handle->execute();

			$optionDescList = $handle->fetchAll(PDO::FETCH_OBJ);

			echo json_encode(array('status'=>'success','data'=>$optionDescList));
		}
		else throw new PDOException('No se tienen opciones');
		
	}
	catch(PDOException $ex){
		error_log($ex->getMessage());
		echo json_encode(array('status'=>'error','data'=>$ex->getMessage()));
	}
