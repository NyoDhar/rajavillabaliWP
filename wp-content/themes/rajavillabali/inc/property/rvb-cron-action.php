<?php
add_action('rvb_send_email_review_request', 'send_email_review_request');
function send_email_review_request(){
	//Get confirmed bookings that its checkout date has passed current date
	$bookings = get_posts(
					array(
						'post_type'		=> 'mphb_booking',
						'post_status'	=> 'confirmed',
						'posts_per_page'=> 20,
						'meta_query'	=> array(
												array(
													'key'		=> 'email_review_sent',
													'compare'	=> 'NOT EXISTS',
												),
												array(
													'key'		=> 'mphb_check_out_date',
													'value'		=> date('Y-m-d'),
													'compare'	=> '<',
													'type'		=> 'DATE',
												)
											)
					)
				);
	
	//var_dump($bookings);
	if(!empty( $bookings )){
		foreach( $bookings as $b ){
			//echo $b->ID .' - '.get_post_meta($b->ID, 'mphb_check_out_date', true) .'<hr>';
			$status = send_asking_review_email( $b->ID );
			if($status){
				update_post_meta($b->ID, 'email_review_sent', 'Sent on '. date('Y-m-d'));
			}
		}
	}
}

function send_asking_review_email($booking_id){
	$booking = MPHB()->getBookingRepository()->findById($booking_id);
	$reservedRooms	 = $booking->getReservedRooms();
	$accomodation_id = $reservedRooms[0]->getRoomTypeId();
	$customer = $booking->getCustomer();
	$villa_name = get_the_title($accomodation_id);
	
	$to = $customer->getEmail();
	$subject = 'Still remember your stay at ' . $villa_name . '? - Raja Villa Bali';
	$email_title = 'Hi ' . $customer->getFirstName();
	
	ob_start();
	?>
		<p style="font-size: large; text-align: center;">How was your stay at <?php echo $villa_name; ?> ?</p>
		<?php
		$gallery_imgs = get_post_meta($accomodation_id, 'rvb_property_photos', true);
		
		?>
		<p style="text-align:center;">
			<?php echo wp_get_attachment_image($gallery_imgs[0], 'blog-small-thumb'); ?>
		</p>
		<p style="text-align:center;">
			<a style="display: inline-block; padding: 12px 24px; background: #31adad; font-size: 1.2em;color: #fafafa;
								text-decoration: none;"
			href="<?php echo get_page_link(3248); ?>?b=<?php echo $booking_id; ?>">I would like to share my experience</a>
		</p>
	<?php
	
	$email_content = ob_get_clean();
	//echo $email_content;
	
	$status = rvb_send_email($to, $subject, $email_title, $email_content);
	
	return $status;
}

add_action('rvb_send_email_review_request', 'check_in_reminder_email');
function check_in_reminder_email(){
	//Get confirmed bookings that its check-in date is between today and next 5 days
	$next5days = (new DateTime())->modify('+5 days');
	
	$bookings = get_posts(
					array(
						'post_type'		=> 'mphb_booking',
						'post_status'	=> 'confirmed',
						'posts_per_page'=> 20,
						'meta_key'		=> 'mphb_check_in_date',
						'meta_type'		=> 'DATE',
						'order_by'		=> 'meta_value_date',
						'order'			=> 'ASC',
						'meta_query'	=> array(
												array(
													'key'		=> 'email_check_in_reminder_sent',
													'compare'	=> 'NOT EXISTS',
												),
												array(
													'key'		=> 'mphb_check_in_date',
													'value'		=> array( date('Y-m-d'), $next5days->format('Y-m-d') ),
													'compare'	=> 'BETWEEN',
													'type'		=> 'DATE',
												)
											),
					)
				);
	
	//var_dump($bookings);
	if(!empty( $bookings )){
		foreach( $bookings as $b ){
			//echo $b->ID .' - '.get_post_meta($b->ID, 'mphb_check_out_date', true) .'<hr>';
			send_check_in_reminder_email( $b->ID );
			update_post_meta($b->ID, 'email_check_in_reminder_sent', 'Sent on '. date('Y-m-d'));
		}
	}
}

function send_check_in_reminder_email($booking_id){
	$booking = MPHB()->getBookingRepository()->findById($booking_id);
	$reservedRooms	 = $booking->getReservedRooms();
	$accomodation_id = $reservedRooms[0]->getRoomTypeId();
	$customer = $booking->getCustomer();
	//$villa_name = get_the_title($accomodation_id);
	
	
	
	ob_start();
	\MPHB\Views\BookingView::renderCheckInDateWPFormatted( $booking );
	$check_in = ob_get_clean();
	
	ob_start();
	\MPHB\Views\BookingView::renderCheckOutDateWPFormatted( $booking );
	$check_out = ob_get_clean();
	
	
	$total_price = $booking->getTotalPrice();
	
	$fee_percentage = get_option('rvb_company_fee');
	$fee = $total_price * $fee_percentage / 100;
	$potential_earn = $total_price - $fee;
	
	$email_content_core = "<h4>Details of booking</h4>
						Booking ID: #{$booking->getId()}<br>
						Check-in: {$check_in}<br>
						Check-out: {$check_out}<br>";
	
	//$reservedRooms	 = $booking->getReservedRooms();
	//$accomodation_id = $reservedRooms[0]->getRoomTypeId();
	$roomType	 = MPHB()->getRoomTypeRepository()->findById( $accomodation_id );
					//$roomType	 = apply_filters( '_mphb_translate_room_type', $roomType, $this->booking->getLanguage() );
					//$replaceText = ( $roomType ) ? $roomType->getTitle() : '';
	$email_content_core .= "<h4>Accommodation</h4>
						Guest: {$reservedRooms[0]->getAdults()}<br>
						Accommodation: {$roomType->getTitle()}<br>";
	
	$email_content_core .= "<h4>Customer Info</h4>
						Name: ".$booking->getCustomer()->getFirstName()." ".$booking->getCustomer()->getLastName()."<br>
						Email: ".$booking->getCustomer()->getEmail()."<br>
						Phone: ".$booking->getCustomer()->getPhone()."<br>
						Note: <br>".$booking->getNote();
	
	$to = $customer->getEmail();
	$subject = sprintf( __('Hooray! your holliday is comming - %s', 'rajavillabali'), get_bloginfo('name') );
	$email_title = __('Your holliday is comming', 'rajavillabali');
	$email_content_guest = "<p>".sprintf( __("Hooray! your holliday is comming, can't wait your arrival at %s", 'rajavillabali'), $roomType->getTitle() )."</p>" . $email_content_core;
	
	$villa_owner_email = get_post_meta($accomodation_id, 'rvb_property_contact_new_booking_email', true);
	$subject_owner = sprintf( __('Your guest is comming - %s', 'rajavillabali'), get_bloginfo('name') );
	$email_title_owner = __('Your guest is comming', 'rajavillabali');
	$email_content_owner = "<p>".sprintf( __("Your guest is comming at %s", 'rajavillabali'), $roomType->getTitle() )."</p>" . $email_content_core;
	
	rvb_send_email($to, $subject, $email_title, $email_content_guest);
	rvb_send_email($villa_owner_email, $subject_owner, $email_title_owner, $email_content_owner);

}