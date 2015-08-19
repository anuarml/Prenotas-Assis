<?php
	include_once('config.php');
	include_once('get_register.php');
	include_once('get_store.php');

	try{
		$link = new PDO(	
			$db_url, 
            $user, 
            $password, 
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ));

		// Se valida que la caja principal que fue configurada este abierta.
		$store = getStore($link);

		if(!$store){
			throw new PDOException('No se encontró la tienda configurada');
		}

		$register = getRegister($link, $store->id);

		if(!$register){
			throw new PDOException('No se encuentra abierta la caja principal.');
		}

		// Validación del usuario.
		if(isset($_GET['login']) && $_GET['login'] != ""){
			$login = $_GET['login'];
			
			$handle = $link->prepare('SELECT usr.ID, usr.Login, usr.Name, usr.Active, usr.SalesPersonExternalID salesPersonID FROM '.$table_user.' usr WHERE usr.Login = :login');

			$handle->bindParam(':login', $login);
		
		    $handle->execute();

		    if($user = $handle->fetchObject()){

		    	/*if($user->Active != 1){
		    		throw new PDOException('Usuario inactivo.');
		    	}*/

		    	if(!$user->salesPersonID){
		    		throw new PDOException('El usuario no tiene configurado un ID de vendedor.');
		    	}

		    	echo json_encode( array('status' => 'success', 'data' => $user) );
		    }
		    else
		    	throw new PDOException('Usuario no registrado.');
		}
		else
			throw new PDOException('Ingresa un usuario.');
	}
	catch(PDOException $ex){
		error_log($ex->getMessage());
	    echo json_encode( array('status' => 'error', 'data' => $ex->getMessage()));
	}
?>