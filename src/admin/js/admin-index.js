jQuery( document ).ready( function ( $ ) {

	$( '.ninety-color-field' ).wpColorPicker();
	$( '.ninety-datepicker' ).datepicker();

	// Handle click on "x" by datepicker fields to clear value.
	$( '.pdf-clear' ).click(
		e => {
			// (e.target).previousSibling is the date input field.
			( ( e.target ).previousSibling ).value = '';
		}
	);

} );

