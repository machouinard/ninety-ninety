<?php

require_once dirname( __FILE__ ) . '/fpdf/fpdf.php';

/**
 * Class NinetyPDF
 */
class NinetyPDF extends FPDF {

	/**
	 * PDF Header
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function Header() {

		// Get PDF title set in options page, with default.
		$header_text = ninety_ninety()->get_option( 'ninety_pdf_title' );
		if ( empty( $header_text ) ) {
			$header_text = 'Meetings List';
		}

		// Arial bold 15.
		$this->SetFont( 'Arial', 'B', 15 );
		// Title.
		$this->Cell( 0, 10, $header_text, 1, 0, 'C' );
		// Line break.
		$this->Ln( 20 );
	}

	/**
	 * PDF Footer
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function Footer() {

		// Position at 1.5 cm from bottom.
		$this->SetY( - 15 );
		// Arial italic 8.
		$this->SetFont( 'Arial', 'I', 8 );
		// Page number.
		$this->Cell( 0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C' );
	}

	/**
	 * Convert array of lines into multidimensional array for fPDF
	 *
	 * @param array $lines array of lines for fPDF.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function LoadData( $lines = array() ) {

		$data = array();
		foreach ( $lines as $line ) {
			$data[] = explode( ';', trim( $line ) );
		}

		return $data;
	}

	/**
	 * Basic Table
	 *
	 * @param array $header Header cells.
	 * @param array $data   PDF Data.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function BasicTable( $header, $data ) {

		// Header.
		foreach ( $header as $col ) {
			$this->Cell( 40, 7, $col, 1 );
		}
		$this->Ln();
		// Data.
		foreach ( $data as $row ) {
			foreach ( $row as $col ) {
				$this->Cell( 40, 6, $col, 1 );
			}
			$this->Ln();
		}
	}

	/**
	 * Improved Table
	 *
	 * @param array $header Header cells.
	 * @param array $data   PDF Data.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function ImprovedTable( $header, $data ) {

		// Column widths.
		$w = array( 40, 70, 40, 20 );
		// Header.
		$count = count( $header );
		for ( $i = 0; $i < $count; $i ++ ) {
			$this->Cell( $w[ $i ], 7, $header[ $i ], 1, 0, 'C' );
		}
		$this->Ln();
		// Data.
		foreach ( $data as $row ) {
			$this->Cell( $w[0], 6, $row[0], 'LR' );
			$this->Cell( $w[1], 6, $row[1], 'LR' );
			$this->Cell( $w[2], 6, $row[2], 'LR', 0, 'R' );
			$this->Cell( $w[3], 6, $row[3], 'LR', 0, 'R' );
			$this->Ln();
		}
		// Closing line.
		$this->Cell( array_sum( $w ), 0, '', 'T' );
	}

	/**
	 * Fancy Table
	 *
	 * @param array $header Header cells.
	 * @param array $data   PDF Data.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function FancyTable( $header, $data ) {

		// Colors, line width and bold font.
		$this->SetFillColor( 47, 79, 79 );
		$this->SetTextColor( 255 );
		$this->SetFont( '', 'B' );
		// Header.
		$w     = array( 40, 100, 30, 20 );
		$count = count( $header );
		for ( $i = 0; $i < $count; $i ++ ) {
			$this->Cell( $w[ $i ], 7, $header[ $i ], 1, 0, 'C', true );
		}
		$this->Ln();
		// Color and font restoration.
		$this->SetFillColor( 224, 235, 255 );
		$this->SetTextColor( 0 );
		$this->SetFont( '' );
		// Data.
		$fill = false;
		foreach ( $data as $row ) {
			$this->Cell( $w[0], 6, $row[0], 'LR', 0, 'L', $fill );
			$this->Cell( $w[1], 6, $row[1], 'LR', 0, 'C', $fill );
			$this->Cell( $w[2], 6, $row[2], 'LR', 0, 'R', $fill );
			$this->Cell( $w[3], 6, $row[3], 'LR', 0, 'R', $fill );
			$this->Ln();
			$fill = ! $fill;
		}
		// Closing line.
		$this->Cell( array_sum( $w ), 0, '', 'T' );
	}
}
