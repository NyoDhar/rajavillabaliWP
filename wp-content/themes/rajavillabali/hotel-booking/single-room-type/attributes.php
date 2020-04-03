<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2 class="rvb-ammenities"><?php _e('Ammenities', 'rajavillabali'); ?></h2>
<?php

/**
 * @hooked \MPHB\Views\SingleRoomTypeView::renderAttributesTitle	- 10
 * @hooked \MPHB\Views\SingleRoomTypeView::renderAttributesListOpen	- 20
 */
//do_action( 'mphb_render_single_room_type_before_attributes' );
?>

<?php

/**
 * @hooked \MPHB\Views\SingleRoomTypeView::renderAdults				- 10
 * @hooked \MPHB\Views\SingleRoomTypeView::renderChildren			- 20
 * @hooked \MPHB\Views\SingleRoomTypeView::renderFacilities			- 30
 * @hooked \MPHB\Views\SingleRoomTypeView::renderView				- 40
 * @hooked \MPHB\Views\SingleRoomTypeView::renderSize				- 50
 * @hooked \MPHB\Views\SingleRoomTypeView::renderBedType			- 60
 * @hooked \MPHB\Views\SingleRoomTypeView::renderCategories			- 70
 * @hooked \MPHB\Views\SingleRoomTypeView::renderCustomAttributes	- 80
 */
//do_action( 'mphb_render_single_room_type_attributes' );
?>

<?php

/**
 * @hooked \MPHB\Views\SingleRoomTypeView::renderAttributesListClose - 10
 */
//do_action( 'mphb_render_single_room_type_after_attributes' );

//echo get_the_term_list( get_the_ID(), 'mphb_room_type_facility', '<ul class="mphb_room_type_facility"><li>', '</li><li>', '</li></ul>' );

$attributes = get_posts(array(
					'post_type'			=> 'mphb_room_attribute',
					'posts_per_page'	=> -1,
					'meta_query'		=> array(
											array(
												'key'	=> 'mphb_visible',
												'value'	=> '1'
											)
										)
				));

if(!empty($attributes)){
	?>
	<div class="atts">
		<div class="row">
			<?php
			foreach($attributes as $at){
				
				$terms = wp_get_post_terms(get_the_ID(), 'mphb_ra_'.$at->post_name);
				
				if(!is_wp_error($terms) && !empty($terms)){
					$remain = count($terms) - 10;
					?>
						<div class="col-sm-4">
							<span class="att-title"><?php echo apply_filters('the_title', $at->post_title ); ?></span>
							<ul class="mphb_room_type_facility">
							<?php
							foreach($terms as $term){
								?>
								<li><i class="fa fa-check-circle-o" aria-hidden="true"></i> <?php echo $term->name; ?></li>
								<?php
							}
							?>
							</ul>
							<?php
								if( $remain > 0 ){
								?>
									<a href="#" class="expand-att"><?php echo sprintf(__('Show %d more', 'rajavillabali'), $remain); ?></a>
									<a href="#" class="collaps-att tmp-hide"><?php _e('Show Less', 'rajavillabali'); ?></a>
								<?php
								}
							?>
							
						</div>
						<?php
					}
			}
			?>
		</div>
	</div>
	<?php
}