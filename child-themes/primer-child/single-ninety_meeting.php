<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Primer
 * @since   1.0.0
 */

// **Only link to meetings at same location when necessary
add_filter( 'primer_post_nav_default_args', 'primer_child_post_nav_default_args' );

get_header(); ?>

<div id="primary" class="content-area">

	<main id="main" class="site-main" role="main">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'content-ninety_meeting' ); ?>

		<?php primer_post_nav(); ?>

		<?php if ( comments_open() || get_comments_number() ) : ?>

			<?php comments_template(); ?>

		<?php endif; ?>

	<?php endwhile; ?>

	</main><!-- #main -->

</div><!-- #primary -->

<?php get_sidebar(); ?>

<?php get_sidebar( 'tertiary' ); ?>

<?php get_footer(); ?>
