<?php

/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package rajavillabali
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php
	$post_id = get_the_ID();
	$gallery_imgs = get_post_meta($post_id, 'rvb_property_photos', true);
?>
	<div class="photo-big">
		<?php
				echo wp_get_attachment_image($gallery_imgs[0], 'blog-big-thumb');
			?>
		<div class="floating-info">
			<div class="container">
				<div class="not-under-title floating">
				<?php
					if(function_exists('bcn_display') && !is_front_page()){		
						?>
						<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
							<?php bcn_display(); ?>
						</div>
						
						<?php 
					} ?>
				</div>
				<span class="total-photos">
					<i class="fa fa-file-image-o" aria-hidden="true"></i>
					<?php echo count($gallery_imgs) ?>
				</span>
			</div>
		</div>
	</div>
	<div class="container">
		
			<div class="row">
				<div class="col-lg-8">
					<header class="entry-header">
						<div class="row">
							<div class="col-sm-6">
								<div class="title-location">
									<?php
										if ( is_singular() ) :
											the_title( '<h1 class="entry-title">', '</h1>' );
										else :
											the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
										endif;
									?>
									<span class="location">
										<i class="fa fa-map-marker" aria-hidden="true"></i>
										<?php
											echo get_the_term_list( get_the_ID(), 'mphb_ra_location', '', ',', '' );
										?>
									</span>
									<?php blk_the_comment_rating_average(); ?>
								</div>
							</div>
							
							<div class="col-sm-6">
								<ul class="short-details">
									<li title="<?php _e('Guest capacity', 'rajavillabali'); ?>"><i class="fa fa-user-circle" aria-hidden="true"></i> <?php echo get_post_meta($post_id, 'mphb_adults_capacity', true) . ' '; _e('Guests', 'rajavillabali'); ?></li>
									<li title="<?php _e('Bedrooms', 'rajavillabali'); ?>"><i class="fa fa-bed" aria-hidden="true"></i> <?php echo get_post_meta($post_id, 'rvb_bedrooms', true) . ' '; _e('Bedrooms', 'rajavillabali'); ?></li>
									<!--<li><i class="fas fa-user"></i> <?php echo get_post_meta($post_id, 'mphb_children_capacity', true) ?></li>-->
									<li title="<?php _e('Land Size', 'rajavillabali'); ?>"><i class="fa fa-arrows-alt" aria-hidden="true"></i> <?php echo get_post_meta($post_id, 'mphb_size', true) . ' sqm '; _e('Land Size', 'rajavillabali'); ?></li>
								</ul>
							</div>
						</div>
					</header><!-- .entry-header -->
					
					<div class="entry-content">
						<div class="the-space white-box">
							<h2><?php _e('The Space', 'rajavillabali'); ?></h2>
							<?php
							
							the_content( sprintf(
								wp_kses(
									/* translators: %s: Name of current post. Only visible to screen readers */
									__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'rajavillabali' ),
									array(
										'span' => array(
											'class' => array(),
										),
									)
								),
								get_the_title()
							) );

							/* wp_link_pages( array(
								'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'rajavillabali' ),
								'after'  => '</div>',
							) ); */
							?>
						
							<div class="photos">
								<?php
								$show_total = 0;
								if(!empty($gallery_imgs)){
									foreach($gallery_imgs as $img){
										$src = wp_get_attachment_image_src($img, 'large');
										?>
										<a href="<?php echo $src[0]; ?>">
											<?php echo wp_get_attachment_image($img, 'blog-small-thumb'); ?>
										</a>
										<?php
										$show_total++;
										
										if($show_total == 6) break;
									}
									
									if(count($gallery_imgs) > 6){
										?>
										<p>
											<a class="button" href="<?php echo wp_get_attachment_image_src($gallery_imgs[0], 'large')[0]; ?>" >
												<?php _e('See all photos', 'rajavillabali'); ?>
											</a>
										</p>
										<?php
									}
								}
								
								?>
								
							</div>
						</div>
						
						<div class="ammenities white-box">
							<?php \MPHB\Views\SingleRoomTypeView::renderAttributes(); ?>
						</div>
						
						<div class="availability white-box">
							<?php \MPHB\Views\SingleRoomTypeView::renderCalendar(); ?>
						</div>
						
						<div class="location white-box">
							<h2><?php _e('Location', 'rajavillabali'); ?></h2>
							<?php echo do_shortcode('[blk_map post_id="'.$post_id.'" meta_key="pinpoint"]'); ?>
						</div>
						
						<?php $cancel_policy = get_post_meta($post_id, 'cancel_policy', true); 
							if(!empty($cancel_policy)){
								$the_policy = get_post($cancel_policy);
								
								?>
								<div class="cancel-policy white-box">
									<h2><?php _e('Cancellation Policy', 'rajavillabali'); ?></h2>
									<div class="read-more-box">
										<h3><?php echo apply_filters('the_title', $the_policy->post_title); ?></h3>
										<?php
											echo apply_filters('the_content', $the_policy->post_content );
										?>
									</div>
									
									<?php
										$cancel_policy_page = get_option('cancelation_policy_page');
										if(!empty($cancel_policy_page)){
											?>
											<a href="<?php echo get_page_link($cancel_policy_page) ?>" target="_blank"><?php _e('Read more about our cancellation policies'); ?></a>
											<?php
										}
									?>
									
								</div>
								<?php
							}
						?>
						
						
					</div><!-- .entry-content -->
				</div>
				
				<div class="col-lg-4">
					<div class="bookbox rvb-sticky">
						<span class="close-bookbox hidden-lg">&times;</span>
						<?php 
							\MPHB\Views\SingleRoomTypeView::renderDefaultOrForDatesPrice();
							echo do_shortcode('[mphb_availability id="'.$post_id.'"]');
						?>
					</div>
					<div class="mobile-bookbox hidden-lg">
						<?php 
							\MPHB\Views\SingleRoomTypeView::renderDefaultOrForDatesPrice();
						?>
						<a href="#" class="button open-bookbox"><?php _e('Book Now', 'rajavillabali'); ?></a>
					</div>
				</div>
			</div>
			
		
	
	
		
	</div>

	<footer class="entry-footer">
		<?php //rajavillabali_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
