<?php
/**
 *
 * Template Name: 19 Woobizz Standard: +Headers +Breadcrumb +Title -Footers
 *
 * @package storefront
 */
 
//DISPLAT TEMPLATE
get_header();
 ?>	

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post();
			
				woobizz_standard_19_template_css();
				do_action( 'storefront_page_before' );
				get_template_part( 'content', 'page' );

				/**
				 * Functions hooked in to storefront_page_after action
				 *
				 * @hooked storefront_display_comments - 10
				 */
				do_action( 'storefront_page_after' );

			endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
do_action( 'storefront_sidebar' );
get_footer();