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

	// Style option for deleting all data on plugin deletion.
	let danger = document.querySelectorAll( 'input.ninety-danger' );


	Object.keys( danger ).forEach( item => {
		let tr = danger[ item ].closest( 'tr' );
		tr.style.backgroundColor = 'rgba(204, 0, 0,.25)';
		let th = tr.querySelector( 'th' );
		th.style.backgroundColor = 'rgb(204, 0, 0)';
		th.style.color = 'rgb( 255, 255, 255 )';
		th.style.padding = '5px';
	} );

	let config = document.querySelectorAll( 'input.ninety-config' );


	Object.keys( config ).forEach( item => {
		let tr = config[ item ].closest( 'tr' );
		tr.style.backgroundColor = 'rgba(73,183,78,.25)';
		let th = tr.querySelector( 'th' );
		th.style.backgroundColor = 'rgb(73,183,78)';
		th.style.color = 'rgb( 255, 255, 255 )';
		th.style.padding = '5px';
	} );

} );
