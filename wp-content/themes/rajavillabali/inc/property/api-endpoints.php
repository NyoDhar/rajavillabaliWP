<?php
add_action('rest_api_init', function () {
	register_rest_route('rvbali/v1', '/allacomodation', array(
		'methods' => 'GET',
		'callback' => 'getAcommodations',
	));
	register_rest_route('rvbali/v1', '/hotdeals', array(
		'methods' => 'GET',
		'callback' => 'getHotDeal',
	));
	register_rest_route('rvbali/v1', '/allhotdealvilla/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'getAllHotDealVilla',
	));
	register_rest_route('rvbali/v1', '/getLocationlist', array(
		'methods' => 'GET',
		'callback' => 'getLocationList',
	));
	register_rest_route('rvbali/v1', '/login', array(
		'methods' => 'POST',
		'callback' => 'getLoginDetails',
	));
});

function getLoginDetails($request)
{
	$username = sanitize_user($request['username']);
	$password = $request['password'];
	$response['data'] = array();
	$user = get_user_by('login', $username);

	if ($user && wp_check_password($password, $user->data->user_pass, $user->ID)) {
		$data['loggedin'] = true;
		$data['user'] = $user;
		array_push($response["data"], $data);
		return $response;
	} else {
		$data['loggedin'] = false;
		$data['user'] = $user;
		array_push($response["data"], $data);
		return $response;
	}
}

function getLocationList()
{
	$arrlocation["data"] = array();
	$args = array(
		'taxonomy'		=> 'mphb_ra_location',
		'hide_empty'	=> false
	);
	$terms = get_terms($args);
	foreach ($terms as $term) {
		array_push($arrlocation["data"], $term);
	}
	return $arrlocation;
}
function getAcommodations()
{
	$villas['data'] = array();
	$args = array(
		'post_type'		    => array('mphb_room_type'),
		'posts_per_page'	=> -1

	);
	$test = array();
	$accomodation = new WP_Query($args);
	while ($accomodation->have_posts()) : $accomodation->the_post();
		$roomType = MPHB()->getRoomTypeRepository()->findById(get_the_ID());
		$amenities = $roomType->getFacilities();
		$h['amenities'] = $amenities;
		array_push($villas["data"], $h);
	endwhile;
	return $villas;
}

function getAllAccomodations()
{
	$arrvillas = array();
	$arrvillas["data"] = array();
	$args = array(
		'post_type'		    => array('mphb_room_type'),
		'posts_per_page'	=> -1

	);

	$accomodation = new WP_Query($args);
	while ($accomodation->have_posts()) : $accomodation->the_post();
		$roomType = MPHB()->getRoomTypeRepository()->findById(get_the_ID());
		$hot_deals_price = $roomType->getDefaultPrice();
		$based_price = $roomType->getDefaultPrice(false);
		$amenities = $roomType->getFacilities();
		$gallery[] = $roomType->getGalleryIds();
		$h["id"] = get_the_ID();
		$h["title"] = get_the_title();
		$h["description"] = get_the_content();
		$h['featured_image'] = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
		$h['property_email'] = get_post_meta(get_the_ID(), 'rvb_property_contact_email', TRUE);
		$h['property_phone'] = get_post_meta(get_the_ID(), 'rvb_property_contact_phone', TRUE);
		$h['bedrooms'] = get_post_meta(get_the_ID(), 'rvb_bedrooms', TRUE);
		$h['hotdeal'] = get_post_meta(get_the_ID(), 'hot_deal', TRUE);
		$h['adult_capacity'] = get_post_meta(get_the_ID(), 'mphb_adults_capacity', TRUE);
		$h['children_capacity'] = get_post_meta(get_the_ID(), 'mphb_children_capacity', TRUE);
		$h['land_size'] = get_post_meta(get_the_ID(), 'mphb_size', TRUE);

		/* find room(post_type:mphb_room) by room type id (MPHB_ROOM_TYPE) connected with post_meta : key = mphb_room_type_id, value =  MPHB_ROOM_TYPE*/
		$roomArgs = array(
			'post_type'		=>	'mphb_room',
			'meta_query'	=>	array(
				array(
					'key' => 'mphb_room_type_id',
					'value'	=>	get_the_ID(),
					'compare' => '='
				)
			)
		);
		$room = get_posts($roomArgs);
		$roomlist = array();
		foreach ($room as $r) {
			$a = $r->ID;
			array_push($roomlist, $a);
			/* Find room availability */
			$availabilityArgs = array(
				'post_type'		=>	'mphb_reserved_room',
				'meta_query'	=>	array(
					array(
						'key' => '_mphb_room_id',
						'value'	=>	$r->ID,
						'compare' => '='
					)
				)
			);
			$availability = get_posts($availabilityArgs);
			$availabilityList = array();
			foreach ($availability as $available) {
				$reservedID = $available->ID;
				$bookingID = $available->post_parent;
				$avail['checkin_date'] = get_post_meta($bookingID, 'mphb_check_in_date', TRUE);
				$avail['checkout_date'] = get_post_meta($bookingID, 'mphb_check_out_date', TRUE);
				array_push($availabilityList, $avail);
			}
		}
		$h['rooms'] = $roomlist;
		$h['availability'] = $availabilityList;
		$h["based_price"] = $based_price;
		$h["hot_deals_price"] = $hot_deals_price;
		$h["amenities"] = $amenities;
		$h["gallery"] = $gallery;
		array_push($arrvillas["data"], $h);

	endwhile;
	return $arrvillas;
}

function getHotDeal()
{
	$arrhotdeals = array();
	$arrhotdeals["data"] = array();
	$args = array(
		'post_type'		    => array('hot-deal'),
		'posts_per_page'	=> -1
	);

	$hotdeal = new WP_Query($args);
	while ($hotdeal->have_posts()) : $hotdeal->the_post();
		$h["id"] = get_the_ID();
		$h['title'] = get_the_title();
		$h['featured_image'] = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
		array_push($arrhotdeals["data"], $h);

	endwhile;
	return $arrhotdeals;
}

function getAllHotDealVilla($hotdealId)
{
	$arrvillas = array();
	$arrvillas["data"] = array();
	$args = array(
		'post_type'		    => array('mphb_room_type'),
		'posts_per_page'	=> -1,
		'meta_query'	=>	array(
			array(
				'key' => 'hot_deal',
				'value'	=>	$hotdealId['id'],
				'compare' => '='
			)
		)
	);

	$accomodation = new WP_Query($args);
	while ($accomodation->have_posts()) : $accomodation->the_post();
		$roomType = MPHB()->getRoomTypeRepository()->findById(get_the_ID());
		$hot_deals_price = $roomType->getDefaultPrice();
		$based_price = $roomType->getDefaultPrice(false);
		$amenities = $roomType->getFacilities();
		$gallery[] = $roomType->getGalleryIds();
		$h["id"] = get_the_ID();
		$h["title"] = get_the_title();
		$h["description"] = get_the_content();
		$h['featured_image'] = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
		$h['property_email'] = get_post_meta(get_the_ID(), 'rvb_property_contact_email', TRUE);
		$h['property_phone'] = get_post_meta(get_the_ID(), 'rvb_property_contact_phone', TRUE);
		$h['bedrooms'] = get_post_meta(get_the_ID(), 'rvb_bedrooms', TRUE);
		$h['hotdeal'] = get_post_meta(get_the_ID(), 'hot_deal', TRUE);
		$h['adult_capacity'] = get_post_meta(get_the_ID(), 'mphb_adults_capacity', TRUE);
		$h['children_capacity'] = get_post_meta(get_the_ID(), 'mphb_children_capacity', TRUE);
		$h['land_size'] = get_post_meta(get_the_ID(), 'mphb_size', TRUE);

		/* find room(post_type:mphb_room) by room type id (MPHB_ROOM_TYPE) connected with post_meta : key = mphb_room_type_id, value =  MPHB_ROOM_TYPE*/
		$roomArgs = array(
			'post_type'		=>	'mphb_room',
			'meta_query'	=>	array(
				array(
					'key' => 'mphb_room_type_id',
					'value'	=>	get_the_ID(),
					'compare' => '='
				)
			)
		);
		$room = get_posts($roomArgs);
		$roomlist = array();
		foreach ($room as $r) {
			$a = $r->ID;
			array_push($roomlist, $a);
			/* Find room availability */
			$availabilityArgs = array(
				'post_type'		=>	'mphb_reserved_room',
				'meta_query'	=>	array(
					array(
						'key' => '_mphb_room_id',
						'value'	=>	$r->ID,
						'compare' => '='
					)
				)
			);
			$availability = get_posts($availabilityArgs);
			$availabilityList = array();
			foreach ($availability as $available) {
				$reservedID = $available->ID;
				$bookingID = $available->post_parent;
				$avail['checkin_date'] = get_post_meta($bookingID, 'mphb_check_in_date', TRUE);
				$avail['checkout_date'] = get_post_meta($bookingID, 'mphb_check_out_date', TRUE);
				array_push($availabilityList, $avail);
			}
		}
		$h['rooms'] = $roomlist;
		$h['availability'] = $availabilityList;
		$h["based_price"] = $based_price;
		$h["hot_deals_price"] = $hot_deals_price;
		$h["amenities"] = $amenities;
		$h["gallery"] = $gallery;
		array_push($arrvillas["data"], $h);

	endwhile;
	return $arrvillas;
}
