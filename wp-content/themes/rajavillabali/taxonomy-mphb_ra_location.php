<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package rajavillabali
 */

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main page">
			
			<?php if ( have_posts() ) : ?>
			<?php
				$bg = '';
				$term_id = get_queried_object()->term_id;
				$tax_meta = get_option('tax_meta_'.$term_id);
				
				if(!empty($tax_meta['image'])){
					$src = wp_get_attachment_image_src($tax_meta['image'], 'blog-big-thumb');
					$bg = 'style="background-image: url('. esc_url( $src[0] ).');"';
				}else{
					if(has_header_image()){
						$bg = 'style="background-image: url('. esc_url( get_header_image() ).');"';
					}
				}
			?>
			<header class="page-header entry-header bg big" <?php echo $bg; ?>>
				
				<div class="floating-bot">
					<div class="container">
						<?php
						the_archive_title( '<h1 class="page-title entry-title">', '</h1>' );
						
						if(function_exists('bcn_display') && !is_front_page()){		
							?>
							<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
								<?php bcn_display(); ?>
							</div>
							
							<?php 
						}
						
						the_archive_description( '<div class="archive-description">', '</div>' );
						?>
					</div>
				</div>
			</header><!-- .page-header -->
			
			<div class="container-fluid">
					<div class="row">
						<div class="col-sm-3">
							<?php echo do_shortcode('[property_filter]'); ?>
						</div>
						<div class="col-sm-9">
							<div class="rvb-properties row">
								<div class="col-sm-12">
									<p class="mphb_sc_search_results-info">
										<?php
										global $wp_query;
										$total_post = $wp_query->found_posts;
										printf( _n( '%s accommodation found', '%s accommodations found', $total_post, 'rajavillabali' ), $total_post );
										?>
									</p>
									<?php
										do_action('blk_before_archieve_property_loop', $total_post);
									?>
								</div>
								<?php
								
								/* Start the Loop */
								//do_action( 'mphb_sc_rooms_before_loop' );
								while ( have_posts() ) :
									the_post();

									/*
									 * Include the Post-Type-specific template for the content.
									 * If you want to override this in a child theme, then include a file
									 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
									 */
									//get_template_part( 'template-parts/content', get_post_type() );
									$templateAtts = array(
									'isShowGallery'		 => false,
									'isShowImage'		 => true,
									'isShowTitle'		 => true,
									'isShowExcerpt'		 => true,
									'isShowDetails'		 => true,
									'isShowPrice'		 => true,
									'isShowViewButton'	 => true,
									'isShowBookButton'	 => false
								);
								
								do_action( 'mphb_sc_rooms_before_item' );
									mphb_get_template_part( 'shortcodes/rooms/room-content', $templateAtts );
								do_action( 'mphb_sc_rooms_after_item' );

								endwhile;
								//do_action( 'mphb_sc_rooms_after_loop' );
								?>
							</div>
							<?php
							
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
			</div>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
//get_sidebar();
get_footer();
