<?php
/**
Template Name: Page Elementor With Heading Title
 */

get_header();
?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main">
		<?php
		while ( have_posts() ) :
			the_post();
			
				$bg = '';
				if(has_header_image()){
					$bg = 'style="background-image: url('. esc_url( get_header_image() ).');"';
				}
			?>
				<header class="entry-header bg" <?php echo $bg; ?>>
					<div class="container">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
						<?php
						if(function_exists('bcn_display') && !is_front_page()){		
							?>
							<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
								<?php bcn_display(); ?>
							</div>
							
							<?php 
						} ?>
					</div>
				</header><!-- .entry-header -->
			<?php
			
			the_content(); //get_template_part( 'template-parts/content', 'elementor' );


		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->
<?php
get_footer();
