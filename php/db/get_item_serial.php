<?php
	include('../../config/config.php');
	include('global_variables.php');

	try{
		if(isset($_GET['serial']) && $_GET['serial'] != ""){
			$serial = $_GET['serial'];
			$id = $_GET['ID'];
			
			$link = new PDO(   $db_url, 
		                        $user, 
		                        $password,  
		                        array(
		                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		                        ));

			$handle = $link->prepare('SELECT ID, Quantity, SerialBatch FROM '.$table_itemSerial.' WHERE ItemID = :ID AND SerialBatch = :serial');

			$handle->bindParam(':ID', $id);
			$handle->bindParam(':serial', $serial);
		 
		    $handle->execute();

		    if($result = $handle->fetchObject()){
		    	echo json_encode($result);
		    	error_log(json_encode($result));
		    }
		    else echo json_encode(false);
		}
		else echo json_encode(false);
	}
	catch(PDOException $ex){
		error_log($ex->getMessage());
	    print($ex->getMessage());
	}
?>