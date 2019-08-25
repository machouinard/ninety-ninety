<?php

get_header();
// TODO: build template.
?>

	<div class="wrap">
		<section id="primary" class="content-area">
			<main id="main" class="site-main" role="main">

				<?php if ( have_posts() ) : ?>

				<article <?php post_class(); ?>>
					<header class="entry-header">
						<?php
						the_archive_title( '<h1 class="page-title">', '</h1>' );
						?>
					</header><!-- .page-header -->

					<div class="entry-content">
						<?php
						// Start the Loop.
						while ( have_posts() ) :
							the_post();

							printf( '<a href="%s"><h3>%s</h3></a>', get_the_permalink(), get_the_title() );

							$loc = get_field( 'ninety_meeting_location', get_the_ID() );
							if ( ! $loc ) {
								continue;
							}
							echo esc_attr( $loc->name );

							// End the loop.
						endwhile;
						endif;
						?>
					</div>
				</article>

				<div class="navigation pagination">
					<?php posts_nav_link(); ?>
				</div>
			</main><!-- #main -->

		</section><!-- #primary -->

	</div>

<?php
get_footer();
