<?php
	include_once('config.php');
	include_once('save_products.php');
	require_once('../print_ticket/print_ticket.php');
	include_once('../class.uuid.php');
	require_once('generate_barcode.php');

	try{
		if(isset($_POST['prenote']) && $_POST['prenote'] != ""){
			$json_prenote = $_POST['prenote'];

			$prenote = json_decode($json_prenote);
			
			$saved = false;
			$printed = false;

			if(!$prenote->folio){
				$date = new DateTime();
				$prenote->date = $date->format('Y-m-d H:i:s');

				$mcs = explode('.',microtime(true));
				$ms=substr($mcs[1],0,3);
				//date format for sqlite
				//$lastUpdate = $date->format('Y-m-d H:i:s.'.$ms);
				
				//date format for sqlsrv
				$lastUpdate = $date->format('Y-m-d H:i:s.'.$ms);

				$dte = $date->format('Y-m-d');
				$tme = $date->format('H:i:s.'.$ms);
				//$code = $date->format('YmdHis'.$ms);

				$prenote_uuid = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING);
				
				$link = new PDO(   $db_url, 
			                        $user, 
			                        $password,  
			                        array(
			                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			                        ));

				

				$st = $link->query('SELECT Consecutivo FROM ThoConsecutivo WHERE Tipo = \'PRENOTA\'');

				if(!($result = $st->fetch())){
					throw new PDOException('Error al obtener el folio de la prenota.');
				}

				$code = $result['Consecutivo'];

				$link->query('UPDATE ThoConsecutivo SET Consecutivo = Consecutivo + 1 WHERE Tipo = \'PRENOTA\'');

				$terminalCode = ord($prenote->terminal);

				$code = generate_barcode(true, $code, $terminalCode);

				// Se obtiene la información del cliente configurado.
				$customer = getCustomer($link);
				
				if($customer){
					$prenote->customerUUID = $customer->uuid;
					$prenote->customerID = $customer->id;
				}

				$store = getStore($link);

				if($store){
					$prenote->store_id = $store->id;
					$prenote->store_name = $store->name;
				}


				$link->beginTransaction();

				$handle = $link->prepare('INSERT INTO '.$table_prenote.' (ID, UUID, CreationDate, LastUpdate, CreationUserID, LastUpdateUserID, Type, TypeDescription, Dte, Tme, StoreID, Workstation, DocumentStatusID, Code, CustomerID, CustomerUUID, SalesPersonUserID, Label, Total, Quantity, Reference, Comment) VALUES (:id, :UUID, :creationDate, :lastUpdate, :create_id, :update_id, 1, :type_description, :dte, :tme, :store_id, :workstation, :documentStatusID, :code, :customer_id, :customer_uuid, :id_employee, :label, :total, :narticles, :reference, :comment)');
				
				//$handle = $link->prepare( ' INSERT INTO ' .$table_prenote. ' ( [ID], [UUID], [LastUpdate], [CreationUserID], [LastUpdateUserID], [Type], [TypeDescription], [Dte], [Tme], [StoreID], [Workstation], [Code], [SalesPersonUserID], [Total], [Quantity] ) VALUES ( 0, :UUID, :lastUpdate, :create_id, :update_id, 1, :type_description, :dte, :tme, 2, :workstation, :code, :id_employee, :total, :narticles ) ' );

				
				$handle->bindParam(':UUID', $prenote_uuid);
				$handle->bindParam(':creationDate', $lastUpdate);
				$handle->bindParam(':lastUpdate', $lastUpdate);
				$handle->bindParam(':dte', $dte);
				$handle->bindParam(':tme', $tme);
				$handle->bindParam(':code', $code);
			    $handle->bindParam(':create_id', $prenote->id_employee, PDO::PARAM_INT);
			    $handle->bindParam(':update_id', $prenote->id_employee, PDO::PARAM_INT);
			    $handle->bindParam(':id_employee', $prenote->id_employee, PDO::PARAM_INT);
			    $handle->bindParam(':label', $prenote->clientName);
			    $handle->bindParam(':total', $prenote->total);
			    $handle->bindParam(':narticles', $prenote->narticles);
			    $handle->bindParam(':reference', $prenote->clientName);
			    $handle->bindParam(':comment', $prenote->cotizationNumber);

			    $handle->bindValue(':type_description', 'Venta');
			    $handle->bindParam(':workstation', $prenote->terminal);
			 	$handle->bindParam(':customer_uuid', $prenote->customerUUID);
			 	$handle->bindParam(':customer_id', $prenote->customerID);
			 	$handle->bindValue(':id', '0', PDO::PARAM_INT);
			 	$handle->bindParam(':store_id', $prenote->store_id, PDO::PARAM_INT);
			 	$handle->bindValue(':documentStatusID', '3'); // Nota Abierta (Para ver en espera)
			    $handle->execute();

			    save_products($link, $prenote_uuid, $prenote->product, $prenote->id_employee, $lastUpdate);
				
				$link->commit();

				$prenote->folio = $code;

				$saved = true;
			}
			else{
				$terminalCode = ord($prenote->terminal);
				generate_barcode(false , $prenote->folio, $terminalCode);
				$saved = true;
			}
			
			
			for($i=0;$i<$print_times;$i++){
				$isPrinted = print_ticket($prenote);
			}
			
			if($isPrinted == true){
				unlink($prenote->folio.".png");
				unlink($prenote->folio.".bmp");
				unlink($prenote->folio.".pdf");
				$printed = true;
			}
			
			echo json_encode( array( $saved, $printed, $prenote) );
		}
	}
	catch(PDOException $ex){
		error_log($ex->getMessage());
	    //print($ex->getMessage());
	    echo json_encode( array( $saved, $printed, $prenote) );
	}

	function getCustomer($link){
		include('config.php');

		$customer = null;

		$handle = $link->prepare('SELECT ID id, UUID uuid FROM '.$table_customer.' WHERE ExternalID = :externalID');

		$handle->bindParam(':externalID', $cfg_client);
		$handle->execute();

		$customer = $handle->fetch(PDO::FETCH_OBJ);

		return $customer;
	}

	function getStore($link){
		include('config.php');

		$store = null;

		$handle = $link->prepare('SELECT ID id, Name name FROM '.$table_store.' WHERE Number = :number');

		$handle->bindParam(':number', $cfg_store);
		$handle->execute();

		$store = $handle->fetch(PDO::FETCH_OBJ);

		return $store;
	}
?>