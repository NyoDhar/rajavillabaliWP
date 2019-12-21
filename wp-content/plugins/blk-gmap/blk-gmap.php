<?php
/*
Plugin Name: BLK GMap
Description: Give an ability to use google map API.
Author: Blauk Blonk
Version: 1.0
*/

include_once('includes/setting-page.php');

add_action( 'admin_enqueue_scripts', 'blk_gmap_addscripts' );
function blk_gmap_addscripts(){
	//Style
	/* wp_enqueue_style( 'owl-carousel-css', plugins_url('assets/owl-carousel/assets/owl.carousel.min.css', __FILE__));
	wp_enqueue_style( 'blk-testimonial-css', plugins_url('css/blk-testimonial.css', __FILE__)); */
	
	wp_enqueue_style( 'blk-gmap-css', plugins_url('style.css', __FILE__));

	//Script
	//wp_enqueue_script( 'owl-carousel-js', plugins_url('assets/owl-carousel/owl.carousel.min.js', __FILE__), array('jquery'));
	$gmap_api_key = get_option('gmap-api-key');
	if(!empty($gmap_api_key)){
		wp_enqueue_script( 'gmap-api', 'https://maps.googleapis.com/maps/api/js?key='.$gmap_api_key.'&libraries=places&sensor=false');
	}
	
	wp_enqueue_script( 'blk-gmap-js', plugins_url('js/gmap.js', __FILE__), array('jquery'));
}

add_action( 'wp_enqueue_scripts', 'blk_gmap_add_front_scripts' );
function blk_gmap_add_front_scripts(){
	//Style
	/* wp_enqueue_style( 'owl-carousel-css', plugins_url('assets/owl-carousel/assets/owl.carousel.min.css', __FILE__));
	wp_enqueue_style( 'blk-testimonial-css', plugins_url('css/blk-testimonial.css', __FILE__)); */
	
	wp_enqueue_style( 'blk-gmap-css', plugins_url('style.css', __FILE__));

	//Script
	//wp_enqueue_script( 'owl-carousel-js', plugins_url('assets/owl-carousel/owl.carousel.min.js', __FILE__), array('jquery'));
	$gmap_api_key = get_option('gmap-api-key');
	if(!empty($gmap_api_key)){
		wp_enqueue_script( 'gmap-api', 'https://maps.googleapis.com/maps/api/js?key='.$gmap_api_key.'&libraries=places');
	}
	
	wp_enqueue_script( 'blk-gmap-js', plugins_url('js/gmap-front.js', __FILE__), array('jquery'));
}

//can be used in backend and frontend
add_shortcode('blk_map', 'blk_admin_map');
function blk_admin_map($atts){
	$a = shortcode_atts( array(
		'pinpoint'	=> '',
		'post_id'	=> '',
		'meta_key'	=> '',
		'wp_option'	=> '',
		'show_search'	=> 'no',
    ), $atts );
	
	$pinpoint_field_name = 'pinpoint';
	
	if(empty($a['pinpoint'])){
		if(!empty($a['post_id']) && !empty($a['meta_key'])){
			$a['pinpoint'] = get_post_meta($a['post_id'], $a['meta_key'], true);
			$pinpoint_field_name = $a['meta_key'];
		}else if(!empty($a['wp_option'])){
			$a['pinpoint'] = get_option($a['wp_option'], '');
			$pinpoint_field_name = $a['wp_option'];
		}
	}
	
	ob_start();
	?>
	<div id="blk-map-wrapper">
		<input type="hidden" id="pinpoint" name="<?php echo $pinpoint_field_name; ?>" value="<?php echo $a['pinpoint']; ?>">
		<?php 
		if( $a['show_search'] == 'yes' ){
			?>
			<input id="pac-input" class="controls" style="margin-top: 10px; padding: 5px 10px; width: 300px;" type="text" placeholder="<?php _e('Search for landmarks, addresses, or place', ''); ?>">
			<?php
		}
		?>
		
		<div id="blk-map" style="width:100%; height:450px;"></div>
	</div>
	<?php
	return ob_get_clean();
}

add_action('wp_footer', 'set_js_option_var');
add_action('admin_footer', 'set_js_option_var');
function set_js_option_var(){
	$blk_gmap_circle_marker = get_option('blk-gmap-circle-marker');
	?>
	<script>
		var useCircleMarker = "<?php echo $blk_gmap_circle_marker ?>";
	</script>
	<?php
}