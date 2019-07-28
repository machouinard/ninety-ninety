<?php
//* TODO: build template
get_header();

?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<article id="post-<?php get_the_ID(); ?>" class="<?php post_class(); ?>">
				<header class="entry-header">
					<?php the_title( '<h1>', '</h1>' ); ?>
				</header>
				<div class="entry-content">
					<?php ninety_meeting_entry_content(); ?>
				</div>
				<?php ninety_single_post_nav(); ?>
			</article>
		</main>
	</div>

<?php

get_footer();
