<?php

function print_prenote2($prenote){
	include('../db/config.php');
	//$printerList = printer_list(PRINTER_ENUM_LOCAL);

	if($printerHandler = printer_open($prenote->printer->name)){
		printer_set_option($printerHandler, PRINTER_MODE, "raw");
		
		printer_start_doc($printerHandler, "Prenote ".$prenote->folio);
		printer_start_page($printerHandler);

		$fontHeight = 21;
		$fontWidth = 10;
		$fontWeight = 250;
		$firstLine = 80;
		$lineNumber = 0;
		$image = $prenote->folio.'.bmp';
		$filename = $rootPath."php\db\\".$image;
		$font = printer_create_font("Arial", $fontHeight, $fontWidth, $fontWeight, false, false, false, 0);

		$fontHeight = 20;
		$fontWidth = 9;
		$fontWeight = 130;
		$lightfont = printer_create_font("Arial", $fontHeight, $fontWidth, $fontWeight, false, false, false, 0);

		printer_select_font($printerHandler, $font);


		printer_draw_bmp($printerHandler, $filename, 150, 1);
		printer_draw_text($printerHandler, $prenote->folio, 168, calculateNextLine($lineNumber++));

		printer_draw_text($printerHandler, $prenote->clientName, 1, calculateNextLine($lineNumber++));
		printer_draw_text($printerHandler, "----------------------------------------------------------------------------------------------------------", 1, calculateNextLine($lineNumber++));
		
		$product = $prenote->product;
		$length = count($product);
		for($i=0;$i<$length;$i++){
			printer_select_font($printerHandler, $font);

			printer_draw_text($printerHandler, utf8_decode($product[$i]->Description), 1, calculateNextLine($lineNumber++));

			printer_select_font($printerHandler, $lightfont);

			printer_draw_text($printerHandler, $product[$i]->optionDesc, 1, calculateNextLine($lineNumber));
			printer_draw_text($printerHandler, $product[$i]->serialBatch, 350, calculateNextLine($lineNumber++));

			printer_draw_text($printerHandler, $product[$i]->scanCode, 1, calculateNextLine($lineNumber));
			printer_draw_text($printerHandler, $product[$i]->unitName, 151, calculateNextLine($lineNumber));
			printer_draw_text($printerHandler, $product[$i]->Quantity, 351, calculateNextLine($lineNumber));
			$precio_cantidad = ($product[$i]->Quantity) * ($product[$i]->Price);
			$precio_cantidad = round($precio_cantidad, 2);
			$precio_cantidad = number_format($precio_cantidad, 2, '.', ',');

			printer_draw_text($printerHandler, formatText('$' . $precio_cantidad ,'right', 11), 451, calculateNextLine($lineNumber++));
		}

		printer_draw_text($printerHandler, "----------------------------------------------------------------------------------------------------------", 1, calculateNextLine($lineNumber++));
		
		printer_draw_text($printerHandler, formatText( $prenote->narticles, 'right', 11 ), 151, calculateNextLine($lineNumber));

		$totalAmount = number_format( $prenote->total, 2, '.', ',');

		printer_draw_text($printerHandler, formatText('$' . $totalAmount,'right', 15), 351, calculateNextLine($lineNumber++));
		printer_draw_text($printerHandler, $prenote->employeeLogin, 1, calculateNextLine($lineNumber));
		printer_draw_text($printerHandler, $prenote->employeeName, 101, calculateNextLine($lineNumber++));
		printer_draw_text($printerHandler, $prenote->store_num, 1, calculateNextLine($lineNumber));
		printer_draw_text($printerHandler, $prenote->store_name, 101, calculateNextLine($lineNumber++));
		printer_draw_text($printerHandler, $prenote->cotizationNumber, 1, calculateNextLine($lineNumber++));
		printer_draw_text($printerHandler, $prenote->date, 1, calculateNextLine($lineNumber));
		
		printer_end_page($printerHandler);
		printer_end_doc($printerHandler);

		printer_close($printerHandler);

		return true;
	}
	else{
		return false;
	}
}
	
function calculateNextLine($lineNumber){
	$fontHeight = 20;
	$firstLine = 85;

	return $firstLine + $fontHeight * $lineNumber;
}

function formatText($text ,$align, $width){

	$textFormatted = '';
	$textLength = strlen($text);

	if($textLength == $width){
		return $text;
	}

	if($width < $textLength){
		$truncateChars = $width - $textLength;
		$textFormatted = substr( $text, 0, $truncateChars);
		return $textFormatted;
	}

	if($width > $textLength){
		$blankSpaces = $width - $textLength;

		if($align == 'right'){

			for ($i=0; $i < $blankSpaces; $i++) { 
				$textFormatted .= '  ';
			}

			$textFormatted .= $text;
		}
	}

	return $textFormatted;
}

?>