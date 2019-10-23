<?php

class NinetyRest {

	/**
	 * NinetyRest constructor.
	 */
	public function __construct() {
		add_filter( 'rest_ninety_meeting_query', [ $this, 'limit_per_page' ], 2, 10 );
		add_filter( 'rest_ninety_meeting_location_query', [ $this, 'limit_per_page' ], 2, 10 );
		add_filter( 'rest_prepare_ninety_meeting', [ $this, 'meeting_prepare_rest' ], 10, 3 );
		add_filter( 'rest_prepare_ninety_meeting_location', [ $this, 'location_prepare_rest' ], 10, 3 );
	}

	/**
	 * Limit per page to 100
	 *
	 * @param $args
	 * @param $req
	 *
	 * @return array
	 * @since 0.1.2
	 */
	public function limit_per_page( $args, $req ) {

		$args['posts_per_page'] = 100;

		return $args;
	}

	/**
	 * Add ACF fields to meeting for REST API
	 *
	 * @param $data
	 * @param $meeting
	 * @param $req
	 *
	 * @return mixed
	 * @since 0.1.2
	 */
	public function meeting_prepare_rest( $data, $meeting, $req ) {
		$_data  = $data->data;
		$fields = get_fields( $meeting->ID );

		$_data['acf'] = $fields;

		$data->data = $_data;

		return $data;

	}

	/**
	 * Add ACF fields to location for REST API
	 *
	 * @param $data
	 * @param $location
	 * @param $req
	 *
	 * @return mixed
	 * @since 0.1.2
	 */
	public function location_prepare_rest( $data, $location, $req ) {
		$_data            = $data->data;
		$address          = get_field( 'ninety_location_address', $location );
		$coords           = get_field( 'ninety_location_coords', $location );
		$_data['address'] = $address;
		$_data['coords']  = $coords;
		$data->data       = $_data;

		return $data;
	}

}

new NinetyRest();
