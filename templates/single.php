<?php
//* TODO: build template
get_header();

?>

	<div class="wrap">
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">
				<?php
				if ( have_posts() ) :
					while ( have_posts() ) :
						the_post();
						?>
						<article id="post-<?php get_the_ID(); ?>" class="<?php post_class(); ?>">
							<header class="entry-header">
								<?php the_title( '<h1>', '</h1>' ); ?>
								<div class="entry-meta">

								</div>
							</header>
							<div class="entry-content">
								<?php ninety_meeting_entry_content(); ?>
								<?php the_content(); ?>
							</div>
							<?php ninety_single_post_nav(); ?>
						</article>
					<?php
					endwhile;
				endif;

				?>
			</main>
		</div>
	</div>

<?php

get_footer();
