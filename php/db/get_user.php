<?php
	include_once('config.php');

	try{
		if(isset($_GET['login']) && $_GET['login'] != ""){
			$login = $_GET['login'];
			
			$link = new PDO(   $db_url, 
		                        $user, 
		                        $password,  
		                        array(
		                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		                        ));

			$handle = $link->prepare('SELECT user.ID, user.Name FROM '.$table_user.' user WHERE user.Login = :login');

			$handle->bindParam(':login', $login);
		 
		    $handle->execute();

		    if($user = $handle->fetchObject()){
		    	echo json_encode($user);
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