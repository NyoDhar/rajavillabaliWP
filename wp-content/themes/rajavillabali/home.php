<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package rajavillabali
 */

get_header();

$bg = '';
if(has_header_image()){
	$bg = 'style="background-image: url('. esc_url( get_header_image() ).');"';
}
				
?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main">
		
			<header class="page-header entry-header bg" <?php echo $bg; ?>>
				<div class="container">
					<h1 class="page-title entry-title"><?php single_post_title(); ?></h1>
					<?php
					
					if(function_exists('bcn_display') && !is_front_page()){		
						?>
						<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
							<?php bcn_display(); ?>
						</div>
						
						<?php 
					}
					?>
				</div>
			</header><!-- .page-header -->
			
			<div class="container">
				<div class="col-sm-12">
				<?php
				if ( have_posts() ) :

					if ( is_home() && ! is_front_page() ) :
						?>
						<header>
							<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
						</header>
						<?php
					endif;
					
					
						
					?>
					
					<?php

					/* Start the Loop */
					while ( have_posts() ) :
						the_post();

						/*
						 * Include the Post-Type-specific template for the content.
						 * If you want to override this in a child theme, then include a file
						 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
						 */
						get_template_part( 'template-parts/content', get_post_type() );

					endwhile;

					//the_posts_navigation();
					the_posts_pagination(array(
								'prev_text' => __( 'Prev', 'rajavillabali' ),
								'next_text' => __( 'Next', 'rajavillabali' ),
							));

				else :

					get_template_part( 'template-parts/content', 'none' );

				endif;
				?>
				</div>
			</div>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
//get_sidebar();
get_footer();
