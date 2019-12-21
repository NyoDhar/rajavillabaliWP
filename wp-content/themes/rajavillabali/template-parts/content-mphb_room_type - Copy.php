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
?>
	<div class="container">
		<header class="entry-header">
			<div class="row">
				<div class="col-sm-8">
					<div class="owl-carousel property-gallery-preview-owl owl-theme owl-loaded" data-smallmedium="1" data-extrasmall="1" data-items="1" data-carousel="owl" data-pagination="true" data-nav="true" data-margin="1" data-loop="true">
					<?php 
						//rajavillabali_post_thumbnail();
						$gallery_imgs = get_post_meta($post_id, 'rvb_property_photos', true);
						
						if(!empty($gallery_imgs)){
							foreach($gallery_imgs as $img_id){
								//echo '<div>';
								echo wp_get_attachment_image($img_id, 'full');
								//echo '</div>';
							}
						}
					?>
					</div>
					
					<div class="owl-carousel property-gallery-index owl-theme owl-loaded" data-smallmedium="6" data-extrasmall="3" data-items="6" data-carousel="owl" data-pagination="false" data-nav="false" data-margin="1">
					<?php 
						if(!empty($gallery_imgs)){
							foreach($gallery_imgs as $img_id){
								echo '<div class="thumb-link">';
								echo wp_get_attachment_image($img_id, 'thumbnail');
								echo '</div>';
							}
						}
					?>
					</div>
					
					
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
				</div>
				
				<div class="col-sm-4">
					<div class="bookbox rvb-sticky">
						<?php 
							\MPHB\Views\SingleRoomTypeView::renderDefaultOrForDatesPrice();
							echo do_shortcode('[mphb_availability id="'.$post_id.'"]');
						?>
					</div>
				</div>
			</div>
			
		</header><!-- .entry-header -->
	
	
		<div class="row">
			<div class="entry-content col-sm-8">
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
				
			</div><!-- .entry-content -->
		</div>
	</div>

	<footer class="entry-footer">
		<?php //rajavillabali_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
