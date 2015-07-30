<?php

	function generate_pdf($prenote){
		require_once('fpdf17/fpdf.php');

		$pdf=new FPDF();
		
		$length = count($prenote->product);
		
		$pdf->Open();
		$pdf->AddPage('P',array(50,49 + $length * 8));
		$pdf->SetFont('Arial', '', 9);
		$pdf->Image( $prenote->folio.'.png' , 3 ,2, 35 , 15,'PNG');
		$pdf->Text(7, 20, $prenote->folio);
		$pdf->Text(5, 24, $prenote->clientName);
		$pdf->Text(5,26,'------------------------------');
		
		$product = $prenote->product;
		$c = 0;
		
		for($i=0;$i<$length;$i++){
			
			$pdf->Text(5, 29 +$c, utf8_decode($product[$i]->Description));
			$pdf->Text(5, 33 + $c, $product[$i]->scanCode);
			$pdf->Text(20, 33 + $c, $product[$i]->Quantity);
			$precio_cantidad = ($product[$i]->Quantity) * ($product[$i]->Price);
			$pdf->Text(28, 33 + $c, '$' . $precio_cantidad);

			$c += 8;	
		}
		$c -= 8;
		$pdf->Text(5,35 + $c,'------------------------------');	
		$pdf->Text(25, 38 + $c, $prenote->narticles);
		$pdf->Text(5, 38 + $c, '$' . $prenote->total);
		$pdf->Text(5, 42 + $c, $prenote->id_employee);
		$pdf->Text(10, 42 + $c, $prenote->employeeName);
		$pdf->Text(5, 46 + $c, $prenote->store_id);
		$pdf->Text(10, 46 + $c, $prenote->store_name);
		$pdf->Text(5, 50 + $c, $prenote->cotizationNumber);
		$pdf->Text(5, 54 + $c, $prenote->date);
		$pdf->Output( $prenote->folio.'.pdf','F');
	}

	/*function print_prenote3($prenote) {
    var textFile = null,
    makeTextFile = function (text) {
    var data = new Blob([text], {type: 'text/plain'});

    // If we are replacing a previously generated file we need to
    // manually revoke the object URL to avoid memory leaks.
    if (textFile !== null) {
      window.URL.revokeObjectURL(textFile);
    }

    textFile = window.URL.createObjectURL(data);

    return textFile;
  };


  /*var create = document.getElementById('create'),
    textbox = document.getElementById('textbox');*/

  /*create.addEventListener('click', function () {
    var link = document.getElementById('downloadlink');
    var TextOfTicket = $prenote->folio + "/t" + $prenote->clientName;
    //var TextOfTicket = 095089 + "/t" + anuar;
    link.href = makeTextFile(TextOfTicket);
    link.style.display = 'block';
  }, false);
}*/
?>