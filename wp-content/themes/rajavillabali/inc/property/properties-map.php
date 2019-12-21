<?php
add_action('wp_ajax_get_map_data', 'get_map_data');
add_action('wp_ajax_nopriv_get_map_data', 'get_map_data');
function get_map_data(){
	//echo MPHB()->postTypes()->room()->getPostType();
	$data['points'] = get_properties_for_map();
	
	ob_start();
	?>
	<div class="map-view">
		<div id="filter-map">
			<?php echo do_shortcode('[property_filter id="map"]'); ?>
		</div>
		
		<div id="pac-card">
			<input id="pac-input" class="controls" type="text" placeholder="<?php _e('Search for landmarks, addresses & more', 'rajavillabali') ?>">
		</div>
		<span id="close-map">&times;</span>
		<div id="the-map-view"></div>
		<div id="infowindow-content" class="infowindow">
		  <img src="" width="16" height="16" id="place-icon">
		  <span id="place-name"  class="title"></span><br>
		  <span id="place-address"></span>
		</div>
		
		<div id="infowindow-marker" class="infowindow">
			<div class="marker-villa-info">
				<div class="row">
					<div class="col-sm-5">
						<a href="" target="_blank" class="url">
							<img src="" id="villa-image">
						</a>
					</div>
					<div class="col-sm-7 text-info">
						<a href="" target="_blank" class="url">
							<h2 id="villa-name"></h2>
						</a>
						<span class="location">
							<i class="fa fa-map-marker" aria-hidden="true"></i>
							<span id="locations"></span>
						</span>
						<ul class="short-details">
							<li><i class="fa fa-user-circle" aria-hidden="true"></i> <span id="guest"></span></li>
							<li><i class="fa fa-bed" aria-hidden="true"></i> <span id="bedroom"></span></li>
							<li><i class="fa fa-arrows-alt" aria-hidden="true"></i> <span id="land-size"></span> sqm</li>
						</ul>
						<div id="price"></div>
					</div>
				</div>
			  </div>
		</div>
	</div>
	
	<?php
	
	$data['map_layout'] = ob_get_clean();
	
	echo json_encode( $data );
	
	wp_die();
}

add_action('wp_ajax_filter_property_map', 'filter_property_map');
add_action('wp_ajax_nopriv_filter_property_map', 'filter_property_map');
function filter_property_map(){
	$attributes = $_POST['mphb_attributes'];
	
	//Save attributes to cookie
	//if(isset($attributes)){
		setcookie('mphb_attributes', json_encode( $attributes ), time() + 3600, '/');
		$_COOKIE['mphb_attributes'] = json_encode( $attributes );
	//}
	
	$points = get_properties_for_map();
	
	echo json_encode($points);
	
	wp_die();
}

function get_properties_for_map(){
	$properties = rvb_getAvailableRoomTypes(); //\MPHB\Shortcodes\SearchResultsShortcode::getAvailableRoomTypes();
	/* $properties = get_posts(array(
						'post_type'			=> 'mphb_room_type',
						'posts_per_page'	=> -1,
						
					)); */
	//var_dump($properties);
	
	$posts = [];
	
	foreach($properties as $p){
		$pinpoint = get_post_meta($p['id'], 'pinpoint', true);
		$gallery_imgs = get_post_meta($p['id'], 'rvb_property_photos', true);
		$src = wp_get_attachment_image_src($gallery_imgs[0], 'blog-small-thumb');
		
		$price = rvb_getDefaultOrForDatesPrice( $p['id'] );
 
		
		if(!empty($pinpoint)){
			$posts[] = array(
							'point'		=> $pinpoint,
							'id'		=> $p['id'],
							'name'		=> get_the_title($p['id']),
							'url'		=> get_permalink($p['id']),
							'image_url'		=> $src[0],
							'guest'		=> get_post_meta($p['id'], 'mphb_adults_capacity', true),
							'bedroom'		=> get_post_meta($p['id'], 'rvb_bedrooms', true),
							'land_size'		=> get_post_meta($p['id'], 'mphb_size', true),
							'price'			=> $price,
							'start_at'		=> '<strong>'.__('Start at', 'rajavillabali').'</strong>',
							'locations'		=> get_the_term_list( $p['id'], 'mphb_ra_location', '', ',', '' ),
						);
		}
		
	}
	
	return $posts;
}