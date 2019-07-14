<?php

/**
 * Class NinetyNinety_CPT
 */
class NinetyNinety_CPT {
	
	/**
	 * NinetyNinety_CPT constructor.
	 */
	function __construct() {
		
		//* Register Meeting post type
		add_action( 'init', [ $this, 'register_post_type' ], 0 );
		
		//* Register custom taxonomies
		add_action( 'init', [ $this, 'register_taxonomies' ], 1 );
		
		//* Add rewrite rules
		add_action( 'generate_rewrite_rules', [ $this, 'generate_rewrite_rules' ] );
		
		//* If option is set to keep Meetings private, run the redirect function
		$private = ninety_ninety()->get_option( 'ninety_keep_private' );
		if ( $private ) {
			add_action( 'wp', [ $this, 'redirect_anon_users' ] );
		}
		
	}
	
	/**
	 * Add Meeting CPT
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	function register_post_type() {
		
		$plan_labels = [
			'name'                  => 'Meetings',
			'singular_name'         => 'Meeting',
			'add_new'               => 'Add New',
			'add_new_item'          => 'Add New Meeting',
			'edit_item'             => 'Edit Meeting',
			'new_item'              => 'New Meeting',
			'all_items'             => 'All Meetings',
			'view_item'             => 'View Meeting',
			'update_item'           => 'Update Meeting',
			'search_items'          => 'Search Meetings',
			'not_found'             => 'No meetings found',
			'not_found_in_trash'    => 'No meetings found in trash',
			'parent_item_colon'     => 'Parent Meeting:',
			'menu_name'             => 'Meetings',
			'insert_into_item'      => 'Insert into meeting',
			'uploaded_to_this_item' => 'Uploaded to this meeting',
			'items_list'            => 'Meetings list',
			'items_list_navigation' => 'Meetings list navigation',
			'filter_items_list'     => 'Filter meetings list',
		];
		
		$plan_args = [
			'labels'              => $plan_labels,
			'description'         => 'AA Meetings',
			'public'              => true,
			'menu_icon'           => 'dashicons-groups',
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menu'    => false,
			'show_in_admin_bar'   => true,
			'query_var'           => true,
			'capability_type'     => 'post',
			'rewrite'             => [
				'slug' => 'meetings',
			],
			'can_export'          => true,
			'has_archive'         => true,
			'hierarchical'        => false,
			'menu_position'       => 25,
			'supports'            => [ 'permalink', 'genesis-cpt-archives-settings' ],
			'exclude_from_search' => true,
			'taxonomies'          => [ 'ninety_meeting_location', 'ninety_meeting_type' ],
		];
		
		register_post_type( 'ninety_meeting', $plan_args );
		
	}
	
	/**
	 * Add Meeting Type & Location taxonomies
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	function register_taxonomies() {
		
		// Meeting Locations
		$labels = [
			'name'                       => 'Meeting Locations',
			'singular_name'              => 'Meeting Location',
			'menu_name'                  => 'Meeting Locations',
			'all_items'                  => 'All Meeting Locations',
			'parent_item'                => 'Parent Meeting Location',
			'parent_item_colon'          => 'Parent Meeting Location:',
			'new_item_name'              => 'New Meeting Location',
			'add_new_item'               => 'Add New Meeting Location',
			'edit_item'                  => 'Edit Meeting Location',
			'back_to_items'              => 'Back to Meeting Locations',
			'view_item'                  => 'View Meeting Location',
			'update_item'                => 'Update Meeting Location',
			'popular_items'              => 'Popular Meeting Locations',
			'separate_items_with_commas' => 'Separate meeting locations with commas',
			'search_items'               => 'Search meeting locations',
			'not_found'                  => 'No meeting locations found',
			'add_or_remove_items'        => 'Add or remove meeting locations',
			'choose_from_most_used'      => 'Choose from the most used meeting locations',
		];
		
		$args = [
			'labels'             => $labels,
			'hierarchical'       => false,
			'meta_box_cb'        => false,
			'public'             => true,
			'show_ui'            => true,
			'show_in_quick_edit' => false,
			'show_admin_column'  => true,
			'show_in_nav_menus'  => true,
			'show_tagcloud'      => true,
		];
		
		register_taxonomy( 'ninety_meeting_location', 'ninety_meeting', $args );
		
		// Meeting Types
		$labels = [
			'name'                       => 'Meeting Types',
			'singular_name'              => 'Meeting Type',
			'menu_name'                  => 'Meeting Types',
			'all_items'                  => 'All Meeting Types',
			'parent_item'                => 'Parent Meeting Type',
			'parent_item_colon'          => 'Parent Meeting Type:',
			'new_item_name'              => 'New Meeting Type',
			'add_new_item'               => 'Add New Meeting Type',
			'edit_item'                  => 'Edit Meeting Type',
			'back_to_items'              => 'Back to Meeting Types',
			'view_item'                  => 'View Meeting Type',
			'update_item'                => 'Update Meeting Type',
			'popular_items'              => 'Popular Meeting Types',
			'separate_items_with_commas' => 'Separate meeting types with commas',
			'search_items'               => 'Search meeting types',
			'not_found'                  => 'No meeting types found',
			'add_or_remove_items'        => 'Add or remove meeting types',
			'choose_from_most_used'      => 'Choose from the most used meeting types',
		];
		
		$args = [
			'labels'             => $labels,
			'hierarchical'       => false,
			//				'meta_box_cb'       => 'post_categories_meta_box',
			'meta_box_cb'        => false,
			'public'             => true,
			'rewrite'            => false,
			'show_ui'            => true,
			'show_in_quick_edit' => false,
			'show_admin_column'  => true,
			'show_in_nav_menus'  => true,
			'show_tagcloud'      => true,
		];
		
		register_taxonomy( 'ninety_meeting_type', 'ninety_meeting', $args );
		
	}
	
	/**
	 * Add rewrite rules for Locations
	 *
	 * @param $wp_rewrite
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	function generate_rewrite_rules( $wp_rewrite ) {
		
		$rules      = [];
		$post_types = get_post_types( [
			'name'     => 'ninety_meeting',
			'public'   => true,
			'_builtin' => false,
		], 'objects' );
		$taxonomies = get_taxonomies( [
			'name'     => 'ninety_meeting_location',
			'public'   => true,
			'_builtin' => false,
		], 'objects' );
		
		foreach ( $post_types as $post_type ) {
			$post_type_name = $post_type->name;
			$post_type_slug = $post_type->rewrite['slug'];
			
			foreach ( $taxonomies as $taxonomy ) {
				if ( $taxonomy->object_type[0] == $post_type_name ) {
					$terms = get_categories( [
						'type'       => $post_type_name,
						'taxonomy'   => $taxonomy->name,
						'hide_empty' => 0,
					] );
					foreach ( $terms as $term ) {
						$rules[ $post_type_slug . '/' . $term->slug . '/page/([0-9]{1,})/?' ] = 'index.php?post_type=' . $post_type_name . '&' . $term->taxonomy . '=' . $term->slug . '&paged=$matches[1]';
						$rules[ $post_type_slug . '/' . $term->slug . '/([^/]+)/?$' ]         = 'index.php?post_type=' . $post_type_name . '&' . $post_type_name . '=$matches[1]&' . $term->taxonomy . '=' . $term->slug;
						$rules[ $post_type_slug . '/' . $term->slug . '/?$' ]                 = 'index.php?post_type=' . $post_type_name . '&' . $term->taxonomy . '=' . $term->slug;
					}
				}
			}
		}
		
		$wp_rewrite->rules = $rules + $wp_rewrite->rules;
		
	}
	
	/**
	 * Redirect not-logged-in users to home page from Meetings pages
	 *
	 * @param $wp  WP instance (passed by reference)
	 *
	 * @return mixed
	 * @since 1.0.0
	 *
	 */
	public function redirect_anon_users( $wp ) {
		
		if ( is_admin() ) {
			return $wp;
		}
		
		global $post;
		
		//* No $post object on 404 page...
		if ( ! $post ) {
			return $wp;
		}
		
		//* Get array of page templates used for Meetings
		$templates = ninety_ninety()->get_setting( 'page_templates' );
		
		if ( ( 'ninety_meeting' == $post->post_type || is_post_type_archive( 'ninety_meeting' ) || is_tax( 'ninety_meeting_location' ) || is_page_template( $templates ) ) && ! is_user_logged_in() ) {
			wp_redirect( get_home_url() );
			exit;
		}
		
		return $wp;
		
	}
	
	/**
	 * Create some default Meeting Types on activation
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public static function activate() {
		
		$self = new NinetyNinety_CPT();
		
		$meeting_types = [
			'Regular',
			'Speaker',
			'Business',
			'12 & 12',
			'Big Book',
			'Fireball',
		];
		
		$self->register_taxonomies();
		
		$self->register_post_type();
		
		foreach ( apply_filters( 'ninety_meeting_types', $meeting_types ) as $type ) {
			
			wp_insert_term( $type, 'ninety_meeting_type' );
			
		}
		
		flush_rewrite_rules();
		
	}
	
}
