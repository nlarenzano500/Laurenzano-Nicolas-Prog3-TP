<?php
require_once './models/Pdf.php';
require_once './../vendor/fpdf.php';
// require('fpdf/fpdf.php');


class PDF extends FPDF {

	// Page header
	function Header() {

		// Add logo to page
		$this->Image('recursos/logo.png',10,8,33);

		// Set font family to Arial bold
		$this->SetFont('Arial','B',20);

		// Move to the right
		$this->Cell(80);

		// Header
		$this->Cell(50,10,'Pedidos',1,0,'C');

		// Line break
		$this->Ln(40);
	}

	// Page footer
	function Footer() {

		// Position at 1.5 cm from bottom
		$this->SetY(-15);

		// Arial italic 8
		$this->SetFont('Arial','I',8);

		// Page number
		$this->Cell(0,10,'Pagina ' .
		$this->PageNo() . '/{nb}',0,0,'C');
	}
}

?>