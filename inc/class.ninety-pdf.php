<?php

require_once dirname( __FILE__ ) . '/fpdf/fpdf.php';

class NinetyPDF extends FPDF {
	
	// Page header
	function Header() {
		
		//* Get PDF title set in options page, with default.
		$header_text = ninety_ninety()->get_option( 'ninety_pdf_title' );
		if ( empty( $header_text ) ) {
			$header_text = 'Meetings List';
		}
		
		// Arial bold 15
		$this->SetFont( 'Arial', 'B', 15 );
		// Title
		$this->Cell( 0, 10, $header_text, 1, 0, 'C' );
		// Line break
		$this->Ln( 20 );
	}
	
	// Page footer
	function Footer() {
		
		// Position at 1.5 cm from bottom
		$this->SetY( - 15 );
		// Arial italic 8
		$this->SetFont( 'Arial', 'I', 8 );
		// Page number
		$this->Cell( 0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C' );
	}
	
	// Load data
	function LoadData( $file ) {
		
		// Read file lines
		$lines = file( $file );
		$data  = array();
		foreach ( $lines as $line ) {
			$data[] = explode( ';', trim( $line ) );
		}
		
		return $data;
	}
	
	// Simple table
	function BasicTable( $header, $data ) {
		
		// Header
		foreach ( $header as $col ) {
			$this->Cell( 40, 7, $col, 1 );
		}
		$this->Ln();
		// Data
		foreach ( $data as $row ) {
			foreach ( $row as $col ) {
				$this->Cell( 40, 6, $col, 1 );
			}
			$this->Ln();
		}
	}
	
	// Better table
	function ImprovedTable( $header, $data ) {
		
		// Column widths
		$w = array( 40, 70, 40, 20 );
		// Header
		for ( $i = 0; $i < count( $header ); $i ++ ) {
			$this->Cell( $w[ $i ], 7, $header[ $i ], 1, 0, 'C' );
		}
		$this->Ln();
		// Data
		foreach ( $data as $row ) {
			$this->Cell( $w[0], 6, $row[0], 'LR' );
			$this->Cell( $w[1], 6, $row[1], 'LR' );
			$this->Cell( $w[2], 6, $row[2], 'LR', 0, 'R' );
			$this->Cell( $w[3], 6, $row[3], 'LR', 0, 'R' );
			$this->Ln();
		}
		// Closing line
		$this->Cell( array_sum( $w ), 0, '', 'T' );
	}
	
	// Colored table
	function FancyTable( $header, $data ) {
		
		// Colors, line width and bold font
		$this->SetFillColor( 47, 79, 79 );
		$this->SetTextColor( 255 );
		//				$this->SetDrawColor(128,0,0);
		//				$this->SetLineWidth(.3);
		$this->SetFont( '', 'B' );
		// Header
		$w = array( 40, 100, 30, 20 );
		for ( $i = 0; $i < count( $header ); $i ++ ) {
			$this->Cell( $w[ $i ], 7, $header[ $i ], 1, 0, 'C', true );
		}
		$this->Ln();
		// Color and font restoration
		$this->SetFillColor( 224, 235, 255 );
		$this->SetTextColor( 0 );
		$this->SetFont( '' );
		// Data
		$fill = false;
		foreach ( $data as $row ) {
			$this->Cell( $w[0], 6, $row[0], 'LR', 0, 'L', $fill );
			$this->Cell( $w[1], 6, $row[1], 'LR', 0, 'C', $fill );
			$this->Cell( $w[2], 6, $row[2], 'LR', 0, 'R', $fill );
			$this->Cell( $w[3], 6, $row[3], 'LR', 0, 'R', $fill );
			$this->Ln();
			$fill = ! $fill;
		}
		// Closing line
		$this->Cell( array_sum( $w ), 0, '', 'T' );
	}
}
