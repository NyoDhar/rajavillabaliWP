<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package rajavillabali
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area col-sm-4">
	<?php
		global $post;
		echo do_shortcode('[mphb_availability id="'.$post->ID.'"]');
		//dynamic_sidebar( 'sidebar-1' ); 
	?>
</aside><!-- #secondary -->
