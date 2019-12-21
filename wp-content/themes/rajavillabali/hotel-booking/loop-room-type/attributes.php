<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
$post_id = get_the_ID();

?>
<ul class="short-details">
	<li title="<?php _e('Guest capacity', 'rajavillabali'); ?>"><i class="fa fa-user-circle" aria-hidden="true"></i> <?php echo get_post_meta($post_id, 'mphb_adults_capacity', true) ?></li>
	<li title="<?php _e('Bedrooms', 'rajavillabali'); ?>"><i class="fa fa-bed" aria-hidden="true"></i> <?php echo get_post_meta($post_id, 'rvb_bedrooms', true) ?></li>
	<!--<li><i class="fas fa-user"></i> <?php echo get_post_meta($post_id, 'mphb_children_capacity', true) ?></li>-->
	<li title="<?php _e('Land Size', 'rajavillabali'); ?>"><i class="fa fa-arrows-alt" aria-hidden="true"></i> <?php echo get_post_meta($post_id, 'mphb_size', true) ?> sqm</li>
</ul>
<?php


/**
 * @hooked \MPHB\Views\LoopRoomTypeView::renderAttributesTitle		- 10
 * @hooked \MPHB\Views\LoopRoomTypeView::renderAttributesListOpen	- 20
 */
//do_action( 'mphb_render_loop_room_type_before_attributes' );
?>

<?php

/**
 * @hooked \MPHB\Views\LoopRoomTypeView::renderAdults			- 10
 * @hooked \MPHB\Views\LoopRoomTypeView::renderChildren			- 20
 * @hooked \MPHB\Views\LoopRoomTypeView::renderFacilities		- 30
 * @hooked \MPHB\Views\LoopRoomTypeView::renderView				- 40
 * @hooked \MPHB\Views\LoopRoomTypeView::renderSize				- 50
 * @hooked \MPHB\Views\LoopRoomTypeView::renderBedType			- 60
 * @hooked \MPHB\Views\LoopRoomTypeView::renderCategories		- 70
 * @hooked \MPHB\Views\LoopRoomTypeView::renderCustomAttributes	- 80
 */
//do_action( 'mphb_render_loop_room_type_attributes' );
?>

<?php

/**
 * @hooked \MPHB\Views\LoopRoomTypeView::renderAttributesListClose - 10
 */
//do_action( 'mphb_render_loop_room_type_after_attributes' );
?>