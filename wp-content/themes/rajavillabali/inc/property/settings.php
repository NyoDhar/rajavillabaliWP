<?php
/*******************************
ADD ADMIN STYLE AND SCRIPT
*******************************/
function rvb_addAdminScript(){
	//Style
	//wp_enqueue_style( 'to-admin-style', get_template_directory_uri() . '/admin/theme-options/css/style.css');
	wp_enqueue_style('font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
	
	if(in_array( $_GET['page'],array('booking-link', 'reports', 'performance'))){
		if(!wp_script_is('jquery-ui-autocomplete')){
			wp_enqueue_script('jquery-ui-autocomplete');
		}
		
		wp_enqueue_style( 'boostrap-grid-only', get_template_directory_uri().'/assets/bootstrap-grid-only.css' );
	}
	
	if($_GET['page'] == 'graphs'){
		wp_enqueue_script( 'moment-js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js', array('jquery'), '1.0', true);
		wp_enqueue_script( 'chart-bundle-js', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.min.js', array('jquery'), '1.0', true);
		wp_enqueue_script( 'chart-js', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.js', array('jquery'), '1.0', true);
		
		if(!wp_script_is('accounting', 'enqueued')){
			wp_enqueue_script( 'accounting', get_template_directory_uri() . '/assets/accountingjs/accounting.min.js', array('jquery'), '1.0', true);
		}
	}
	
	if(!wp_script_is('jquery-ui-datepicker')){
		wp_enqueue_script('jquery-ui-datepicker');
	}
	
	if(!empty($_GET['page']) && in_array($_GET['page'], array('rvb-email-blast'))){
		wp_enqueue_script('jquery-ui-progressbar');
	}
	
	wp_enqueue_style( 'jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css', null, '1.0');
	
	wp_enqueue_style( 'admin-style', get_template_directory_uri().'/admin-style.css', null, '1.0');
	wp_enqueue_script( 'admin-custom-js', get_template_directory_uri() . '/js/admin-custom.js', array('jquery'));
}
add_action( 'admin_enqueue_scripts', 'rvb_addAdminScript');

function rvb_settings(){
	add_menu_page('Web Options', 'Web Options', 'manage_categories', 'web-setting', 'rvbto_web_setting_menu' );
	add_menu_page('Booking Link', 'Booking Link', 'manage_categories', 'booking-link', 'rvbto_booking_link' );
	add_menu_page('Email Blast', 'Email Blast', 'manage_categories', 'rvb-email-blast', 'rvb_email_blast' );
}
add_action('admin_menu','rvb_settings');

add_action( 'admin_notices', 'rvb_admin_notice' );
function rvb_admin_notice() {
	
	if($_POST['rvb-save-web-settings']){
		update_option('wa_number',$_POST['wa_number']);
		update_option('rvb_company_fee',$_POST['rvb_company_fee']);
		update_option('cancelation_policy_page',$_POST['cancel-policy-page']);
		
		//Account area options
		update_option('rvb_my_account_page',$_POST['rvb_my_account_page']);
		update_option('rvb_my_booking_page',$_POST['rvb_my_booking_page']);
		update_option('rvb_my_listings_page',$_POST['rvb_my_listings_page']);
		update_option('rvb_submit_listings_page',$_POST['rvb_submit_listings_page']);
		update_option('rvb_homeowner_page',$_POST['rvb_homeowner_page']);
		
		
		//Property Submission by owner options
		update_option('strength-point-owner',$_POST['strength-point-owner']);
		
		
		
		?>
			<div class="notice notice-success is-dismissible">
				<p><?php _e( 'Web options updated!', 'ump' ); ?></p>
			</div>
		<?php
	}
	
    
}

function rvbto_web_setting_menu(){
	?>
	
	<div class="wrap">
		<h1>Web Setting</h1>
		<form method="post">
			<table class="form-table">
				<tr>
					<th scope="row">WhatsApp Number</th>
					<td><input name="wa_number" type="text" value="<?php echo get_option('wa_number') ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row">Company Fee ( % )</th>
					<td>
						<input name="rvb_company_fee" type="number" value="<?php echo get_option('rvb_company_fee') ?>" class="regular-text">
						<p class="description">A percentage of fee from booking amount, is used to calculate company revenue</p>
					</td>
				</tr>
				<tr>
					<th scope="row">Cacelation Policy Page</th>
					<td>
						<select name="cancel-policy-page">
							<option value=""></option>
							<?php
								$pages = get_pages();
								$cancelation_policy_page = get_option('cancelation_policy_page');
								if( !empty($pages) ){
									foreach($pages as $page){
										?>
										<option value="<?php echo $page->ID ?>" <?php echo $cancelation_policy_page == $page->ID ? 'selected' : ''; ?> ><?php echo $page->post_title; ?></option>
										<?php
									}
								}
							?>
						</select>
					</td>
				</tr>
			</table>
			
			<h2>User Account</h2>
			<?php
				$pages = get_pages();
			?>
			<table class="form-table">
				<tr>
					<th scope="row">My Account Page</th>
					<td>
						<?php
							$args = array(
										'name'		=> 'rvb_my_account_page',
										'selected'	=> get_option('rvb_my_account_page'),
									);
							rvb_pages_list_dropdown($args);
						?>
					</td>
				</tr>
				<tr>
					<th scope="row">My Booking Page</th>
					<td>
						<?php
							$args = array(
										'name'		=> 'rvb_my_booking_page',
										'selected'	=> get_option('rvb_my_booking_page'),
									);
							rvb_pages_list_dropdown($args);
						?>
					</td>
				</tr>
				<tr>
					<th scope="row">My Listings Page</th>
					<td>
						<?php
							$args = array(
										'name'		=> 'rvb_my_listings_page',
										'selected'	=> get_option('rvb_my_listings_page'),
									);
							rvb_pages_list_dropdown($args);
						?>
					</td>
				</tr>
				<tr>
					<th scope="row">Submit Listings Page</th>
					<td>
						<?php
							$args = array(
										'name'		=> 'rvb_submit_listings_page',
										'selected'	=> get_option('rvb_submit_listings_page'),
									);
							rvb_pages_list_dropdown($args);
						?>
					</td>
				</tr>
				<tr>
					<th scope="row">Homeowner Page</th>
					<td>
						<?php
							$args = array(
										'name'		=> 'rvb_homeowner_page',
										'selected'	=> get_option('rvb_homeowner_page'),
									);
							rvb_pages_list_dropdown($args);
						?>
					</td>
				</tr>
			</table>
			
			<h2>Property Submission</h2>
			<table class="form-table">
				<tr>
					<th scope="row">Strength Point For Property Owner</th>
					<td>
						<textarea cols="50" rows="10" name="strength-point-owner" class="rvb-ml-field"><?php echo get_option('strength-point-owner'); ?></textarea>
						<p class="description">one per line</p>
					</td>
				</tr>
				
			</table>
			
			<p class="submit"><input type="submit" name="rvb-save-web-settings" id="submit" class="button button-primary" value="Save Changes"></p>
		</form>
	</div>
	<?php
}


function rvbto_booking_link(){
	?>
	<div class="wrap">
		<h1>Booking Link</h1>
		<form id="send-booking-link" method="post">
			<table class="form-table">
				<tr>
					<th scope="row">Customer Email</th>
					<td><input name="c_email" type="text" value="" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row">Customer Name</th>
					<td><input name="c_name" type="text" value="" class="regular-text"></td>
				</tr>
				
				<tr>
					<th scope="row">Check-in</th>
					<td><input name="check-in" class="rvb-datepicker" type="text" value="" class="regular-text" autocomplete="off"></td>
				</tr>
				<tr>
					<th scope="row">Check-out</th>
					<td><input name="check-out" class="rvb-datepicker" type="text" value="" class="regular-text" autocomplete="off"></td>
				</tr>
				<tr>
					<th scope="row">Accomodation</th>
					<td><input name="accomodation" id="find-accomodation" type="text" value="" class="regular-text">
						<input name="accomodation_id" type="hidden" value="" class="regular-text">
					</td>
				</tr>
			</table>
			<p class="submit"><input type="submit" name="sendlink" id="submit" class="button button-primary" value="Send Booking Link"></p>
		</form>
	</div>
	<?php
}

function rvb_email_blast(){
	$wp_editor_settings = array(
							'media_buttons'	=> false,
							'editor_height'	=> 250,
							'editor_class'		=> 'rvb-field',
						);
	?>
	<div class="wrap">
		<h1>Email Blast</h1>
		<form id="send-email-blast" method="post">
			<table class="form-table">
				<tr>
					<th scope="row">Send Test Email To</th>
					<td>
						<input name="test-email" id="test-email" type="email" value="" class="regular-text">
						<p class="description">Fill in this field with an email address to send test email blast</p>
					</td>
				</tr>
				<tr>
					<th scope="row">Hot Deals to send</th>
					<td>
						<ul class="hot-deals">
						<?php
							$valid_hds = get_posts(array(
									'post_type'			=> 'hot-deal',
									'posts_per_page'	=> -1,
									'meta_query'		=> array(
															'key'		=> 'rvb_hd_date_end',
															'compare'	=> '>',
															'type'		=> 'DATE',
															'value'		=> date('Y-m-d'),
														)
								));
							
							foreach($valid_hds as $hd){
								?>
								<li>
									<input id="hd-<?php echo $hd->ID; ?>" class="hot-deals" type="checkbox" name="hot-deals[]" value="<?php echo $hd->ID ?>">
									<label for="hd-<?php echo $hd->ID; ?>"><?php echo $hd->post_title; ?></label>
								</li>
								<?php
							}
						?>
						</ul>
					</td>
				</tr>
				<tr>
					<th scope="row">Email Subject</th>
					<td>
						<input name="email_subject" type="text" value="Hot Deals!!" class="regular-text">
					</td>
				</tr>
				
				<tr>
					<th scope="row">Email Title</th>
					<td>
						<input name="email_title" type="text" value="Hot Deals!!" class="regular-text">
					</td>
				</tr>
				
				<tr>
					<th scope="row">Email Text</th>
					<td>
						<?php wp_editor('','email_text',$wp_editor_settings); ?>
					</td>
				</tr>
				
			</table>
			<p class="submit"><input type="submit" name="send-email-blast" id="submit-email-blast" class="button button-primary" value="Send"></p>
			<div id="progressbar" class="tmp-hide"><div class="progress-label">Loading...</div></div>
		</form>
	</div>
	<?php
}