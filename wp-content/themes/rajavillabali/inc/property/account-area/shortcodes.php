<?php
add_shortcode('rvb_my_bookings', 'rvb_my_bookings');
function rvb_my_bookings(){
	$user = rvb_get_current_user();
	if($user === false || !rvb_user_can($user, 'can_booking')) return _e('You are not authorized to access this page', 'rajavillabali');
	
	ob_start();
	
	if(!empty($_GET['action'])){
		if($_GET['action'] == 'change-booking'){
			change_booking();
		}
		
		if($_GET['action'] == 'cancel-booking'){
			cancel_booking();
		}
		
	}else{
		get_booking_list($user);
	}
	
	return ob_get_clean();
}

function change_booking(){
	//$booking = get_post($_GET['bid']);
	$check_in = DateTime::createFromFormat('Y-m-d', get_post_meta($_GET['bid'], 'mphb_check_in_date', true));
	$check_out = DateTime::createFromFormat('Y-m-d', get_post_meta($_GET['bid'], 'mphb_check_out_date', true));
	
	$today = new DateTime();
	
	if( $check_in > $today ){
		?>
		<div id="change-booking">
			<form data-bid="<?php echo $_GET['bid']; ?>">
				<div class="info-box">
					<div class="info-head">
						<span class="title">Change Booking Dates</span>
					</div>
					<div class="info-body">
						<ul>
							<li>
								<label>Check-in</label> :
								<input type="text" class="rvb-datepicker" id="check-in-date-input" value="<?php echo $check_in->format('j F Y'); ?>" name="check-in" required >
							</li>
							<li>
								<label>Check-out</label> :
								<span id="checkout-date"><?php echo $check_out->format('j F Y'); ?></span>
								<!--<input type="text" class="rvb-datepicker" value="<?php echo $check_out->format('j F Y'); ?>" name="check-out" required >-->
							</li>
						</ul>
					</div>
					<div class="info-footer">
						<input type="submit" class="button" value="Save Change" disabled >
					</div>
				</div>
			</form>
		</div>
		<?php
	}else{
		_e( sprintf('Booking #%s has been passed and cannot be changed', $_GET['bid']), 'rajavillabali' );
	}
}

function cancel_booking(){
	$booking = MPHB()->getBookingRepository()->findById( $_GET['bid'] );

	$reservedRooms	 = $booking->getReservedRooms();
	$accomodation_id = $reservedRooms[0]->getRoomTypeId();
	$roomType	 = MPHB()->getRoomTypeRepository()->findById( $accomodation_id );
	
	$check_in = DateTime::createFromFormat('Y-m-d', get_post_meta($_GET['bid'], 'mphb_check_in_date', true));
	$check_out = DateTime::createFromFormat('Y-m-d', get_post_meta($_GET['bid'], 'mphb_check_out_date', true));
	
	$today = new DateTime();
	
	if( $check_in > $today ){
		?>
		<div id="cancel-booking">
			<form data-bid="<?php echo $_GET['bid']; ?>">
				<div class="info-box">
					<div class="info-head">
						<span class="title"><?php _e('Cancel Booking', 'rajavillabali'); ?></span>
					</div>
					<div class="info-body">
						<ul>
							<li>
								<label><?php _e('Booking', 'rajavillabali') ?></label> :
								#<?php echo $_GET['bid']; ?>
							</li>
							<li>
								<label><?php _e('Accomodation', 'rajavillabali') ?></label> :
								<?php echo get_the_title($accomodation_id); ?>
							</li>
							<li>
								<label><?php _e('Check-in', 'rajavillabali') ?></label> :
								<?php echo $check_in->format('j F Y'); ?>
							</li>
							<li>
								<label><?php _e('Check-out', 'rajavillabali') ?></label> :
								<?php echo $check_out->format('j F Y'); ?>
							</li>
							<li>
								<label><?php _e('Cancellation policy', 'rajavillabali') ?></label> :
								<?php
									$cancel_policy = get_post_meta($accomodation_id, 'cancel_policy', true);
									if($cancel_policy){
										$villa = get_post($cancel_policy);
										$title = apply_filters('the_title', $villa->post_title);
										?>
										
										<a href="#" id="see-cancel-policy"><?php echo $title; ?> </a>
										<div id="cancel-policy-content" class="popup-window">
											<div class="inner-window">
												<div class="window-head"><?php echo $title; ?> <span id="close-window">&times;</span></div>
												<div class="window-content">
													<?php
														
														echo apply_filters( 'the_content', $villa->post_content );
													?>
												</div>
											</div>
										</div>
										<?php
										
									}
								?>
							</li>
							
							<li>
								<label><?php _e('Reason to cancel', 'rajavillabali') ?></label>
								<textarea name="reason" rows="10" required ></textarea>
							</li>
							<li>
								<label><?php _e('File', 'rajavillabali') ?></label>
								<div id="media-uploader" class="dropzone"></div>
								<input type="hidden" id="media-ids" value="">
								<div id="images"></div>
								<small><?php _e('any files need to be sent regarding the cancelation if any, only image are accepted', 'rajavillabali'); ?></small>
							</li>
						</ul>
						
					</div>
					<div class="info-footer">
						<input type="submit" class="button" value="<?php _e('Cancel Booking', 'rajavillabali'); ?>" >
					</div>
				</div>
			</form>
		</div>
		<?php
	}else{
		_e( sprintf('Booking #%s has been passed and cannot be changed', $_GET['bid']), 'rajavillabali' );
	}
}

function get_booking_list($user){
	
	
	$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
	
	$bookings = get_posts(array(
						'post_type'			=> 'mphb_booking',
						'posts_per_page'	=> 10,
						'paged'				=> $paged,
						'post_status'		=> array('confirmed', 'cancelled'),
						'meta_query'		=> array(
												array(
													'key'	=> 'rvb_user_id',
													'value'	=> $user->ID,
												),
												array(
													'key'		=> 'mphb_check_in_date',
													'value'		=> date('Y-m-d'),
													'compare'	=> '>',
													'type'		=> 'DATE',
												)
											)
						
					));
	
	$bookings_past = get_posts(array(
						'post_type'			=> 'mphb_booking',
						'posts_per_page'	=> 10,
						'paged'				=> $paged,
						'post_status'		=> array('confirmed', 'cancelled'),
						'meta_query'		=> array(
												array(
													'key'	=> 'rvb_user_id',
													'value'	=> $user->ID,
												),
												array(
													'key'		=> 'mphb_check_in_date',
													'value'		=> date('Y-m-d'),
													'compare'	=> '<=',
													'type'		=> 'DATE',
												)
											)
						
					));
	
	//var_dump($bookings);
	
	
	
	if(!empty($bookings) && !empty($bookings_past)){
		
		if(!empty($bookings)){
			$my_booking_link = get_page_link(get_option('rvb_my_booking_page'));
			
			?>
			<div class="bookings-wrapper">
				<h2><?php _e('Incoming  Bookings', 'rajavillabali'); ?></h2>
				<ul class="bookings-list">
					<?php
					foreach($bookings as $b){
						$booking = MPHB()->getBookingRepository()->findById( $b->ID );
						
						$reservedRooms	 = $booking->getReservedRooms();
						$accomodation_id = $reservedRooms[0]->getRoomTypeId();
						$roomType	 = MPHB()->getRoomTypeRepository()->findById( $accomodation_id );
						?>
						<li>
							<div class="booking-head">
								<?php _e('Booking ID', 'rajavillabali') ?> <span class="booking-id">#<?php echo $b->ID ?></span>
								<?php
									if($b->post_status == 'confirmed'){
									?>
									<ul class="menu booking-action">
										<li>
											<i class="fa fa-ellipsis-v" aria-hidden="true"></i>
											<ul class="sub-menu">
												<li><a href="<?php echo $my_booking_link ?>?action=change-booking&bid=<?php echo $b->ID; ?>">Change Booking</a></li>
												<li><a href="<?php echo $my_booking_link ?>?action=cancel-booking&bid=<?php echo $b->ID; ?>">Cancel Booking</a></li>
												
											</ul>
										</li>
									</ul>
									<?php
								}
								?>
								
								<?php //echo MPHB()->userActions()->getBookingCancellationAction()->generateLink( $booking ); ?>
								
							</div>
							<div class="booking-content">
								<a target="_blank" href="<?php echo get_permalink($roomType->getId()) ?>"><i class="fa fa-home" aria-hidden="true"></i> <?php echo $roomType->getTitle(); ?></a>
								<ul class="details">
									<li><?php echo __('Check-in', 'rajavillabali') . ': '; \MPHB\Views\BookingView::renderCheckInDateWPFormatted( $booking ) ?></li>
									<li><?php echo __('Check-out', 'rajavillabali') . ': '; \MPHB\Views\BookingView::renderCheckOutDateWPFormatted( $booking ); ?></li>
									<li><?php _e('Guest', 'rajavillabali') ?> : <?php echo $reservedRooms[0]->getAdults(); ?></li>
								</ul>
							</div>
							<div class="booking-footer">
								<div class="row">
									<div class="col-sm-6">
										<span class="total-price">
											<b><?php _e('Total', 'rajavillabali'); ?> : </b> <?php echo mphb_format_price( $booking->getTotalPrice() ); ?>
										</span>
									</div>
									<div class="col-sm-6 text-right">
										<span class="booking-status <?php echo $b->post_status ?>">
											<b><?php _e('Status', 'rajavillabali'); ?> : </b> 
											<span class="status">
												<?php echo $b->post_status == 'confirmed' ? __('Approved', 'rajavillabali') : __('Cancelled', 'rajavillabali') ?>
											</span>
										</span>
									</div>
								</div>
							</div>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
			<?php
		}
		
		//Past Bookings
		if(!empty($bookings_past)){
			//$my_booking_link = get_page_link(get_option('rvb_my_booking_page'));
			
			?>
			<div class="bookings-wrapper">
				<h2><?php _e('Past Bookings', 'rajavillabali'); ?></h2>
				<ul class="bookings-list">
					<?php
					foreach($bookings_past as $b){
						$booking = MPHB()->getBookingRepository()->findById( $b->ID );
						
						$reservedRooms	 = $booking->getReservedRooms();
						$accomodation_id = $reservedRooms[0]->getRoomTypeId();
						$roomType	 = MPHB()->getRoomTypeRepository()->findById( $accomodation_id );
						?>
						<li>
							<div class="booking-head">
								<?php _e('Booking ID', 'rajavillabali') ?> <span class="booking-id">#<?php echo $b->ID ?></span>
							</div>
							<div class="booking-content">
								<a target="_blank" href="<?php echo get_permalink($roomType->getId()) ?>"><i class="fa fa-home" aria-hidden="true"></i> <?php echo $roomType->getTitle(); ?></a>
								<ul class="details">
									<li><?php echo __('Check-in', 'rajavillabali') . ': '; \MPHB\Views\BookingView::renderCheckInDateWPFormatted( $booking ) ?></li>
									<li><?php echo __('Check-out', 'rajavillabali') . ': '; \MPHB\Views\BookingView::renderCheckOutDateWPFormatted( $booking ); ?></li>
									<li><?php _e('Guest', 'rajavillabali') ?> : <?php echo $reservedRooms[0]->getAdults(); ?></li>
								</ul>
							</div>
							<div class="booking-footer">
								<div class="row">
									<div class="col-sm-6">
										<span class="total-price">
											<b><?php _e('Total', 'rajavillabali'); ?> : </b> <?php echo mphb_format_price( $booking->getTotalPrice() ); ?>
										</span>
									</div>
									<div class="col-sm-6 text-right">
										<span class="booking-status <?php echo $b->post_status ?>">
											<b><?php _e('Status', 'rajavillabali'); ?> : </b> 
											<span class="status">
												<?php echo $b->post_status == 'confirmed' ? __('Approved', 'rajavillabali') : __('Cancelled', 'rajavillabali') ?>
											</span>
										</span>
									</div>
								</div>
							</div>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
			<?php
		}
	}else{
		_e("You haven't made any booking yet", 'rajavillabali');
	}
	
	
}

add_shortcode('rvb_my_listing', 'rvb_my_listing_page');
function rvb_my_listing_page(){
	$user = rvb_get_current_user();
	if($user === false || !rvb_user_can($user, 'can_listing')) return _e('You are not authorized to access this page', 'rajavillabali');
	
	ob_start();
	
	$submit_link = get_page_link( get_option('rvb_submit_listings_page') );
	?>
	
	<div class="listings">
		<div class="button-acts">
			<a class="button" href="<?php echo $submit_link; ?>"><?php _e('Add new listing', 'rajavillabali'); ?></a>
		</div>
		
		<?php
			
			$properties = get_posts(array(
									'post_type'			=> 'mphb_room_type',
									'posts_per_page'	=> 10,
									'author'			=> $user->ID,
									'post_status'		=> array('publish', 'draft', 'pending'),
								));
			
			if(!empty($properties)){
				foreach($properties as $p){
					?>
					<div class="the-listing">
						<div class="thebody">
							<div class="row">
								<div class="col-sm-4">
									<?php
									$gallery_imgs = get_post_meta($p->ID, 'rvb_property_photos', true);
									$src = wp_get_attachment_image_src($gallery_imgs[0], 'blog-small-thumb');
									
										if(!empty($src[0])){
											$img_url = $src[0];
										}else{
											$img_url = get_template_directory_uri().'/images/dummy.jpg';
										}
									?>
									<img src="<?php echo $img_url ?>">
								</div>
								<div class="col-sm-8">
									<div class="details">
										<h2><?php echo apply_filters('the_title', $p->post_title); ?></h2>
										<?php
											$price = rvb_getDefaultOrForDatesPrice( $p->ID );
											$guest		= get_post_meta($p->ID, 'mphb_adults_capacity', true);
											$bedroom	= get_post_meta($p->ID, 'rvb_bedrooms', true);
											$land_size	= get_post_meta($p->ID, 'mphb_size', true);
											$location = get_the_term_list( $p->ID, 'mphb_ra_location', '', ',', '' );
											
										?>
										<span class="location">
											<i class="fa fa-map-marker" aria-hidden="true"></i>
											<?php echo $location; ?>
										</span>
										
										<ul class="short-details">
											<li><i class="fa fa-user-circle" aria-hidden="true"></i> <?php echo $guest; ?></li>
											<li><i class="fa fa-bed" aria-hidden="true"></i> <?php echo $bedroom; ?></li>
											<li><i class="fa fa-arrows-alt" aria-hidden="true"></i> <?php echo $land_size; ?> sqm</li>
										</ul>
										<div class="price"><?php echo '<strong>'.__('Start at', 'rajavillabali').'</strong> ' . $price; ?></div>
									</div>
								</div>
							</div>
						</div>
						<div class="thefooter">
							<div class="row">
								<div class="col-sm-6">
									<b><?php
										_e('Status', 'rajavillabali');
									?> :</b> <span class="status <?php echo $p->post_status; ?>"><?php echo $p->post_status; ?></span>
								</div>
								
								<div class="col-sm-6 text-right">
									<?php
										if($p->post_status != 'pending' ){
											$text = $p->post_status == 'draft' ? __('Continue', 'rajavillabali') : __('Edit', 'rajavillabali');
											?>
												<a href="<?php echo $submit_link ?>?pid=<?php echo $p->ID; ?>" class="button"><?php echo $text; ?></a>
											<?php
										}
									?>
									
								</div>
							</div>
						</div>
					</div>
					<?php
				}
			}else{
				_e("You haven't submit any listing yet", 'rajavillabali');
			}
		?>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode('submit_listing_form', 'rvb_submit_listing');
function rvb_submit_listing(){
	$user = rvb_get_current_user();
	if($user === false || !rvb_user_can($user, 'can_listing')) return _e('You are not authorized to access this page', 'rajavillabali');
	
	ob_start();
	if(!empty($_GET['pid']) && is_numeric($_GET['pid'])){
		$property = get_post($_GET['pid']);
		$metas = get_post_meta($_GET['pid']);
	}
	
	?>
	<div id="submit-listing">
		<form id="submit-property" data-poststatus="<?php echo !empty($property) ? $property->post_status : ''; ?>" data-postid="<?php echo !empty($property) ? $property->ID : ''; ?>" >
		<?php
			include_once('submit-property-form/property-details.php');
			include_once('submit-property-form/property-address.php');
			include_once('submit-property-form/property-images.php');
			include_once('submit-property-form/property-ammenities.php');
			include_once('submit-property-form/property-price.php');
			include_once('submit-property-form/property-house-rule.php');
			include_once('submit-property-form/property-contact.php');
			include_once('submit-property-form/property-review.php');
			include_once('submit-property-form/property-submited.php');
		?>
		</form>
	</div>
	<?php
	
	return ob_get_clean();
}

add_shortcode('rvb_my_account', 'rvb_my_account');
function rvb_my_account(){
	$user = rvb_get_current_user();
	if($user === false) return _e('You are not authorized to access this page', 'rajavillabali');
	
	ob_start();
	
	if(!empty($_GET['action'])){
		if($_GET['action'] == 'change-password'){
			form_change_user_password($user);
		}
		
		if($_GET['action'] == 'edit-info'){
			form_change_user_info($user);
		}
		
	}else{
		render_my_acount_page($user);
	}
	
	return ob_get_clean();
}

function render_my_acount_page($user){
	
	$my_account_link = get_page_link(get_option('rvb_my_account_page'));
	$is_homeowner_logged_in = rvb_is_user_logged_in('homeowner');
	
	?>
	<div id="my-account">
		<div id="user-info" class="info-box">
			<div class="info-head">
				<span class="title">Your Information</span>
			</div>
			<div class="info-body">
				<ul>
					<?php
					if( $is_homeowner_logged_in ){
						?>
						<li>
							<label><?php _e('Name', 'rajavillabali'); ?></label> :
							<?php echo $user->first_name; ?>
						</li>
						<li>
							<label><?php _e('Phone', 'rajavillabali'); ?></label> :
							<?php echo get_user_meta($user->ID, 'rvb_phone', true); ?>
						</li>
						<?php
					}
					?>
					<li>
						<label><?php _e('Username', 'rajavillabali'); ?></label> :
						<?php echo $user->user_login ?>
					</li>
					<li>
						<label><?php _e('Email', 'rajavillabali'); ?></label> :
						<?php echo $user->user_email ?>
					</li>
				</ul>
			</div>
			<div class="info-footer">
				<a href="<?php echo $my_account_link ?>?action=change-password" class="button"><?php _e('Change Password', 'rajavillabali') ?></a>
				<?php
				if($is_homeowner_logged_in){
					?>
					<a href="<?php echo $my_account_link ?>?action=edit-info" class="button"><?php _e('Edit info', 'rajavillabali') ?></a>
					<?php
				}
				?>
			</div>
		</div>
		
		<?php
		if(rvb_is_user_logged_in()){
			?>
			<div id="user-bookings" class="info-box">
				<div class="info-head">
					<span class="title">Last 5 Bookings</span>
				</div>
				<div class="info-body">
					<?php
						$bookings = get_posts(array(
							'post_type'			=> 'mphb_booking',
							'posts_per_page'	=> 5,
							'post_status'		=> array('confirmed', 'cancelled'),
							'meta_query'		=> array(
													array(
														'key'	=> 'rvb_user_id',
														'value'	=> $user->ID,
													)
												)
							
						));
						
						if(!empty($bookings)){
							?>
							<ul>
								<?php
								foreach($bookings as $b){
									$booking = MPHB()->getBookingRepository()->findById( $b->ID );
						
									$reservedRooms	 = $booking->getReservedRooms();
									$accomodation_id = $reservedRooms[0]->getRoomTypeId();
									$roomType	 = MPHB()->getRoomTypeRepository()->findById( $accomodation_id );
									?>
									<li>
										<b>#<?php echo $b->ID  ?></b> 
										- <a target="_blank" href="<?php echo get_permalink($roomType->getId()) ?>"><i class="fa fa-home" aria-hidden="true"></i> <?php echo $roomType->getTitle(); ?></a>
										( <b> <?php echo mphb_format_price( $booking->getTotalPrice() ); ?> </b> )
									</li>
									<?php
								}
								?>
								
							</ul>
							<?php
						}else{
							_e("You haven't made any booking yet", 'rajavillabali');
						}
					?>
				</div>
				<div class="info-footer">
					<a href="<?php echo get_page_link(get_option('rvb_my_booking_page')); ?>" class="button"><?php _e('See All Bookings', 'rajavillabali') ?></a>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php
}

function form_change_user_password($user){
	?>
	<div id="form-edit-user-info">
		<div class="info-box">
			<form id="change-password">
				<div class="info-head">
					<span class="title">Change Password</span>
				</div>
				<div class="info-body">
					
					<ul>
						<li>
							<label>Current Password</label> : 
							<input type="password" name="current-password" required >
						</li>
						<li>
							<label>New Password</label> : 
							<input type="password" name="new-password" required >
						</li>
						<li>
							<label>Confirm New Password</label> : 
							<input type="password" name="confirm-password" id="confirm-password" required>
						</li>
					<ul>
				</div>
				<div class="info-footer">
					<input type="submit" value="Change Password">
				</div>
			</form>
		</div>
	</div>
	<?php
}

function form_change_user_info($user){
	?>
	<div id="form-edit-user-info">
		<div class="info-box">
			<form id="change-user-info">
				<div class="info-head">
					<span class="title">Change Info</span>
				</div>
				<div class="info-body">
					
					<ul>
						<li>
							<label>Name</label> : 
							<input type="text" name="name" required value="<?php echo $user->first_name ?>">
						</li>
						<li>
							<label>Phone</label> : 
							<input type="text" name="phone" required value="<?php echo get_user_meta($user->ID, 'rvb_phone', true); ?>">
						</li>
						<li>
							<label>Email</label> : 
							<input type="email" name="email" required value="<?php echo $user->user_email; ?>">
						</li>
					<ul>
				</div>
				<div class="info-footer">
					<input type="submit" value="Save Changes">
				</div>
			</form>
		</div>
	</div>
	<?php
}