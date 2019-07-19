<?php

/**
 * Ninety Meeting Archives widget class
 * Heavily based on
 * https://github.com/thingsym/custom-post-type-widgets/blob/master/inc/widget-custom-post-type-archive.php
 *
 * @since   1.0.0
 * @package Ninety in Ninety
 */
class Ninety_Meeting_Archives extends WP_Widget {

	/**
	 * Ninety_Meeting_Archives constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_archive',
			'description' => __( 'A monthly archive of your Meetings.', 'ninety-ninety' ),
		);
		parent::__construct( 'ninety-meeting-archives', __( 'Archives (Meetings)', 'ninety-ninety' ), $widget_ops );
	}

	/**
	 * Widget
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function widget( $args, $instance ) {
		$posttype = 'ninety_meeting';
		$c        = ! empty( $instance['count'] ) ? '1' : '0';
		$d        = ! empty( $instance['dropdown'] ) ? '1' : '0';
		$title    = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Archives', 'ninety-ninety' ) : $instance['title'], $instance, $this->id_base );

		add_filter( 'month_link', array( $this, 'get_month_link_meetings' ), 10, 3 );
		add_filter( 'get_archives_link', array( $this, 'trim_post_type' ), 10, 1 );

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		if ( $d ) {
			?>
			<select name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'>
				<option value=""><?php echo esc_attr( __( 'Select Month', 'ninety-ninety' ) ); ?></option>
				<?php
				wp_get_archives( apply_filters( 'widget_archives_dropdown_args', array(
					'post_type'       => $posttype,
					'type'            => 'monthly',
					'format'          => 'option',
					'show_post_count' => $c,
				) ) );
				?>
			</select>
			<?php
		} else {
			?>
			<ul>
				<?php
				wp_get_archives( apply_filters( 'widget_archives_args', array(
					'post_type'       => $posttype,
					'type'            => 'monthly',
					'show_post_count' => $c,
				) ) );
				?>
			</ul>
			<?php
		}

		remove_filter( 'month_link', array( $this, 'get_month_link_meetings' ) );
		remove_filter( 'get_archives_link', array( $this, 'trim_post_type' ) );

		echo $args['after_widget'];
	}

	/**
	 * Update
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$new_instance         = wp_parse_args( (array) $new_instance, array(
			'title'    => '',
			'posttype' => 'post',
			'count'    => 0,
			'dropdown' => '',
		) );
		$instance['title']    = sanitize_text_field( $new_instance['title'] );
		$instance['posttype'] = strip_tags( $new_instance['posttype'] );
		$instance['count']    = $new_instance['count'] ? 1 : 0;
		$instance['dropdown'] = $new_instance['dropdown'] ? 1 : 0;

		return $instance;
	}

	/**
	 * Form
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 * @since 1.0.0
	 */
	public function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title'    => '',
				'posttype' => 'post',
				'count'    => 0,
				'dropdown' => '',
			)
		);
		$title    = isset( $instance['title'] ) ? sanitize_text_field( $instance['title'] ) : '';
		$count    = $instance['count'] ? 'checked="checked"' : '';
		$dropdown = $instance['dropdown'] ? 'checked="checked"' : '';
		?>
		<p>
			<label for="<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'ninety-ninety' ); ?></label>
			<input class="widefat" id="<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>"
				   name="<?php echo esc_html( $this->get_field_name( 'title' ) ); ?>" type="text"
				   value="<?php echo esc_attr( $title ); ?>"/></p>

		?>

		<p><input class="checkbox" type="checkbox" <?php echo $dropdown; ?>
				  id="<?php echo $this->get_field_id( 'dropdown' ); ?>"
				  name="<?php echo $this->get_field_name( 'dropdown' ); ?>"/> <label
					for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Display as dropdown', 'ninety-ninety' ); ?></label><br>
			<input class="checkbox" type="checkbox" <?php echo $count; ?>
				   id="<?php echo $this->get_field_id( 'count' ); ?>"
				   name="<?php echo $this->get_field_name( 'count' ); ?>"/> <label
					for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show post counts', 'ninety-ninety' ); ?></label>
		</p>
		<?php
	}

	/**
	 * Get Meetings Month Link
	 *
	 * @param $monthlink
	 * @param $year
	 * @param $month
	 *
	 * @return false|mixed|string|void
	 * @since 1.0.0
	 */
	public function get_month_link_meetings( $monthlink, $year, $month ) {
		global $wp_rewrite;

		$posttype = 'ninety_meeting';

		if ( ! $year ) {
			$year = gmdate( 'Y', current_time( 'timestamp' ) );
		}
		if ( ! $month ) {
			$month = gmdate( 'm', current_time( 'timestamp' ) );
		}

		$monthlink = $wp_rewrite->get_month_permastruct();

		if ( ! empty( $monthlink ) ) {
			$front = preg_replace( '/\/$/', '', $wp_rewrite->front );

			$monthlink = str_replace( '%year%', $year, $monthlink );
			$monthlink = str_replace( '%monthnum%', zeroise( intval( $month ), 2 ), $monthlink );

			$type_obj     = get_post_type_object( $posttype );
			$archive_name = ! empty( $type_obj->rewrite['slug'] ) ? $type_obj->rewrite['slug'] : $posttype;
			if ( $front ) {
				$new_front = $type_obj->rewrite['with_front'] ? $front : '';
				$monthlink = str_replace( $front, $new_front . '/' . $archive_name, $monthlink );
				$monthlink = home_url( user_trailingslashit( $monthlink, 'month' ) );
			} else {
				$monthlink = home_url( user_trailingslashit( $archive_name . $monthlink, 'month' ) );
			}
		} else {
			$monthlink = home_url( '?post_type=' . $posttype . '&m=' . $year . zeroise( $month, 2 ) );
		}

		return $monthlink;
	}

	/**
	 * Strip post_type from $link_html
	 * Don't need it with our rewrites
	 *
	 * @param string $link_html
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function trim_post_type( $link_html ) {
		global $wp_rewrite;

		if ( ! $wp_rewrite->permalink_structure ) {
			return $link_html;
		}

		$posttype = 'ninety_meeting';

		$link_html = str_replace( '?post_type=' . $posttype, '', $link_html );

		return $link_html;
	}
}
