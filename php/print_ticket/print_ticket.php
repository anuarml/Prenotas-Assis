<?php
	function print_ticket($prenote){
		require_once('print.php');
		require_once('generate_pdf.php');
		require_once('convertImage.php');
		require_once('print_local.php');

		if ( !($prenote && $prenote->printer) ) {
			return false;
		}

		$flag = true;

		if($prenote->printer->ip){
			// Imprimir prenota por una impresora con ip, generando un pdf.
			generate_pdf($prenote);

			if(print_prenote($prenote->folio, $prenote->printer->ip)== false){
				$flag=false;
			}

			unlink($prenote->folio.".pdf");

		} else {
			// Imprimir prenota en una impresora conectada al servidor.
			convertPngToBmp($prenote->folio);

			$flag = print_prenote2($prenote);

			unlink($prenote->folio.".bmp");
		}

		return $flag;
	}
?>