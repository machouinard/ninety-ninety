<?php

/**
 * Meeting Search widget class
 * Heavily based on
 * https://github.com/thingsym/custom-post-type-widgets/blob/master/inc/widget-custom-post-type-search.php.
 *
 * @since   1.0.0
 * @package Ninety in Ninety
 */
class Ninety_Meeting_Search extends WP_Widget {

	/**
	 * Ninety_Meeting_Search constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_search',
			'description' => __( 'A search form for your site.', 'ninety-ninety' ),
		);
		parent::__construct( 'ninety-meeting-search', __( 'Search ( Meetings )', 'ninety-ninety' ), $widget_ops );
		$this->alt_option_name = 'widget_ninety_meeting_search';

	}

	/**
	 * Filter search form post type
	 *
	 * @param string $form
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function add_form_input_post_type( $form ) {
		$posttype = 'ninety_meeting';
		$insert   = '<input type="hidden" name="post_type" value="' . $posttype . '">';

		$form = str_replace( '</form>', $insert . '</form>', $form );

		return $form;
	}

	/**
	 * Output Meeting post type search form
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Search', 'ninety-ninety' ) : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		add_filter( 'get_search_form', array( $this, 'add_form_input_post_type' ), 10, 1 );
		get_search_form();
		remove_filter( 'get_search_form', array( $this, 'add_form_input_post_type' ) );

		echo $args['after_widget'];
	}

	/**
	 * Update search form title
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array|mixed
	 * @since 1.0.0
	 */
	public function update( $new_instance, $old_instance ) {
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Widget title field
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 * @since 1.0.0
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'posttype' => 'post' ) );
		$title    = isset( $instance['title'] ) ? strip_tags( $instance['title'] ) : '';
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'ninety-ninety' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
				   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
				   value="<?php echo esc_attr( $title ); ?>"/></p>

		<?php
	}

	/**
	 * Provide array of ACF fields to include in search query
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function list_searchable_acf() {
		$list_searchable_acf = array(
			'ninety_meeting_speaker',
			'ninety_meeting_topic',
			'ninety_meeting_notes',
		);

		return $list_searchable_acf;
	}

	/**
	 * Update 'where' in search query
	 *
	 * Hooked to `posts_search` in ninety-ninety.php
	 *
	 * @param string $where the initial "where" part of the search query.
	 * @param object $wp_query
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function advanced_custom_search( $where, $wp_query ) {
		global $wpdb;

		if ( empty( $where ) ) {
			return $where;
		}

		// get search expression.
		$terms = $wp_query->query_vars['s'];

		// explode search expression to get search terms.
		$exploded = explode( ' ', $terms );
		if ( false === $exploded || 0 === count( $exploded ) ) {
			$exploded = array( 0 => $terms );
		}

		// reset search in order to rebuilt it as we wish.
		$where = '';

		// get searchable_acf, a list of advanced custom fields you want to search content in.
		$list_searchable_acf = self::list_searchable_acf();
		foreach ( $exploded as $tag ) :
			$where .= " 
		  AND (
			(wp_posts.post_title LIKE '%$tag%')
			OR (wp_posts.post_content LIKE '%$tag%')
			OR EXISTS (
			  SELECT * FROM wp_postmeta
				  WHERE post_id = wp_posts.ID
					AND (";
			foreach ( $list_searchable_acf as $searchable_acf ) :
				if ( $searchable_acf == $list_searchable_acf[0] ) :
					$where .= " (meta_key LIKE '%" . $searchable_acf . "%' AND meta_value LIKE '%$tag%') ";
				else :
					$where .= " OR (meta_key LIKE '%" . $searchable_acf . "%' AND meta_value LIKE '%$tag%') ";
				endif;
			endforeach;
			$where .= ")
			)
			OR EXISTS (
			  SELECT * FROM wp_comments
			  WHERE comment_post_ID = wp_posts.ID
				AND comment_content LIKE '%$tag%'
			)
			OR EXISTS (
			  SELECT * FROM wp_terms
			  INNER JOIN wp_term_taxonomy
				ON wp_term_taxonomy.term_id = wp_terms.term_id
			  INNER JOIN wp_term_relationships
				ON wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id
			  WHERE (
				taxonomy = 'post_tag'
					OR taxonomy = 'category'                
					OR taxonomy = 'myCustomTax'
				)
				AND object_id = wp_posts.ID
				AND wp_terms.name LIKE '%$tag%'
			)
		)";
		endforeach;

		return $where;
	}

}
