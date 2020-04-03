<?php
add_action("admin_init", "rvb_add_meta_box");
function rvb_add_meta_box(){
	add_meta_box("property-details", "Property Details", "rvb_property_details", "mphb_room_type", "normal", "high");
	add_meta_box("sleeping-arrangement", "Sleeping Arrangement", "rvb_sleeping_arrangement", "mphb_room_type", "normal", "default");
	add_meta_box("property-location", "Property Location", "rvb_property_location", "mphb_room_type", "normal", "default");
	add_meta_box("property-cencel-policy", "Cancelation Policy", "rvb_property_cencel_policy", "mphb_room_type", "normal", "default");
	add_meta_box("hot-deal-meta", "Settings", "rvb_hot_deal_setting", "hot-deal", "normal", "default");
	add_meta_box("service-field-label", "Service Settings", "rvb_services_field_label", "mphb_room_service", "normal", "low");
	
	add_meta_box("payment-info", "Payment Information", "rvb_payment_information", "mphb_booking", "normal", "low");
	
	remove_meta_box( 'postcustom' , 'post' , 'normal' );
}

//Remove Service price metabox
add_action("mphb_register_mphb_room_service_metaboxes", "rvb_remove_meta_box", 100);
function rvb_remove_meta_box(){
	remove_meta_box( 'mphb_price' , 'mphb_room_service' , 'advanced' );
}

function rvb_sleeping_arrangement($post){
	$sleeping_arr = get_post_meta($post->ID, 'sleeping_arr', true);
	$bedroom = get_post_meta($post->ID, 'rvb_bedrooms', true);
	if(!empty($sleeping_arr)){
		$mphb_bed_types = get_option('mphb_bed_types');
		$i=0;
		foreach($sleeping_arr as $sa){
			?>
			<div class="field the-room">
				<label><?php printf(__('Room %d', 'rajavillabali'), ($i+1)); ?></label>
				<ul>
					<li>
						<b>Bed Type</b> :
						<select name="meta[sleeping_arr][<?php echo $i; ?>][bed_type]" class="rvb-required" required >
							<option value=""></option>
							<?php
								
								if(is_array($mphb_bed_types)){
									foreach($mphb_bed_types as $bed){
										?>
										<option value="<?php echo $bed['type'] ?>" <?php echo $bed['type'] == $sa['bed_type'] ? 'selected' : ''; ?>><?php echo $bed['type'] ?></option>
										<?php
									}
								}
								
							?>
						</select>
					</li>
					<li>
						<b>en suite bathroom</b>:
						<input type="checkbox" name="meta[sleeping_arr][<?php echo $i; ?>][ensuite_bathroom]" value="yes" <?php echo 'yes' == $sa['ensuite_bathroom'] ? 'checked' : ''; ?>>
					</li>
				</ul>
			</div>
			<?php
			$i++;
		}
	}elseif(!empty($bedroom)){
		render_sleeping_arrangement_form($bedroom, 0, 'required');
	}
}

function rvb_property_details($post){
	$metas = get_post_meta($post->ID);
	?>
	<table class="form-table">
		<tr>
			<th><label>Bedrooms</label></th>
			<td>
				<input type="number" id="rvb_bedrooms" name="rvb_bedrooms" value="<?php echo $metas['rvb_bedrooms'][0] ?>">
			</td>
		</tr>
		<tr>
			<th><label>Bathrooms</label></th>
			<td>
				<input type="number" id="rvb_bathrooms" name="rvb_bathrooms" value="<?php echo $metas['rvb_bathrooms'][0] ?>">
			</td>
		</tr>
		<tr>
			<th><label>Property Photos</label></th>
			<td>
				Click the button below to upload the images.
				<?php echo do_shortcode('[ez_wp_media_uploader wrapper_id="rvb-property-photos" field_name="rvb_property_photos" post_id="'.$post->ID.'" ]'); ?>
			</td>
		</tr>
	</table>
	
	<p class="rvb-metabox-title">Property Contact</p>
	<table class="form-table">
		<tr>
			<th><label>Customer Support Phone</label></th>
			<td>
				<input type="text" name="rvb_property_contact_phone" value="<?php echo $metas['rvb_property_contact_phone'][0] ?>" class="regular-text">
			</td>
		</tr>
		<tr>
			<th><label>Customer Support Email</label></th>
			<td>
				<input type="email" name="rvb_property_contact_email" value="<?php echo $metas['rvb_property_contact_email'][0] ?>" class="regular-text">
			</td>
		</tr>
		<tr>
			<th><label>New Booking Email Receiver</label></th>
			<td>
				<input type="email" name="rvb_property_contact_new_booking_email" value="<?php echo $metas['rvb_property_contact_new_booking_email'][0] ?>" class="regular-text">
			</td>
		</tr>
	</table>
	<?php
}

function rvb_property_location($post){
	echo do_shortcode('[blk_map post_id="'.$post->ID.'" meta_key="pinpoint" show_search="yes"]');
	?>
	<table class="form-table">
		<tr>
			<th><label>Landmark</label></th>
			<td>
				<textarea name="landmark" class="regular-text"><?php echo get_post_meta($post->ID, 'landmark', true); ?></textarea>
				<p class="description">Comma separated, i.e: Restaurant, Canggu Beach</p>
			</td>
		</tr>
	</table>
	<?php
}

function rvb_hot_deal_setting($post){
	$metas = get_post_meta($post->ID); 
	?>
	<table class="form-table">
		<tr>
			<th><label>Date End</label></th>
			<td>
				<input type="text" name="rvb_hd_date_end" class="rvb-datepicker" value="<?php echo $metas['rvb_hd_date_end'][0] ?>">
			</td>
		</tr>
		<tr>
			<th><label>Discount ( % )</label></th>
			<td>
				<input type="text" name="rvb_hd_date_discount" value="<?php echo $metas['rvb_hd_date_discount'][0] ?>">
			</td>
		</tr>
		<tr>
			<th><label>Properties</label></th>
			<td>
				<input type="text" id="rvb_hd_properties_search" class="regular-text">
				<input type="hidden" id="rvb_hd_properties_search_id">
				<input type="button" class="button button-primary" id="rvb_hd_add" value="Add">
				
				<ul id="rvb_hd_properties">
					<?php
						if(!empty( $metas['rvb_hd_properties'][0] )){
							$properties = unserialize( $metas['rvb_hd_properties'][0] );
							if(!empty($properties)){
								foreach($properties as $p){
									?>
									<li>
										<input type="hidden" name="rvb_hd_properties[]" value="<?php echo $p ?>">
										<?php echo get_the_title($p) ?>
										<span class="remove">&times;</span>
									</li>
									<?php
								}
							}
						}
					?>
				</ul>
			</td>
		</tr>
	</table>
	<?php
}

function rvb_services_field_label($post){
	$service_label = get_post_meta($post->ID, 'service_label', true);
	$price = get_post_meta($post->ID, 'mphb_price', true);
	//$metas = get_post_meta($post->ID);
	
	//var_dump($metas);
	?>
	<table class="form-table">
		<tr>
			<th>
				<label for="mphb-mphb_price">Price</label>
			</th>
			<td colspan="1">
				<div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-number" data-type="number" data-inited="true">
					<input name="mphb_price" value="<?php echo $price; ?>" id="mphb-mphb_price" class=" mphb-price-text" type="number" min="0" step="0.01">
				</div>
			</td>
		</tr>
		<tr class="mphb-hide">
			<th>
				<label for="mphb-mphb_price_periodicity">Periodicity</label>
			</th>
			<td colspan="1">
				<div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-select" data-type="select" data-inited="true">
					<!--<select name="mphb_price_periodicity" id="mphb-mphb_price_periodicity">
						<option value="once">Once</option>
						<option value="per_night">Per Day</option>
						<option value="flexible" selected="selected">Guest Choice</option>
					</select>-->
					<input type="text" name="mphb_price_periodicity" id="mphb-mphb_price_periodicity" value="flexible">
					<p class="description">How many times the customer will be charged.</p>
				</div>
			</td>
		</tr>
		<tr class="mphb-hide"><th><label for="mphb-mphb_min_quantity">Minimum</label></th><td colspan="1"><div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-number" data-type="number" data-inited="true"><input name="mphb_min_quantity" value="1" id="mphb-mphb_min_quantity" class=" mphb-price-text" type="number" min="1" step="1"></div></td></tr>
		<tr class="mphb-hide"><th><label for="mphb-mphb_is_auto_limit">Maximum</label></th><td colspan="1"><div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-checkbox" data-type="checkbox" data-inited="true"><input name="mphb_is_auto_limit" value="0" id="mphb-mphb_is_auto_limit-hidden" type="hidden"><input name="mphb_is_auto_limit" value="1" id="mphb-mphb_is_auto_limit" type="checkbox" style="margin-top: 0;">&nbsp;<label for="mphb-mphb_is_auto_limit">Use the length of stay as the maximum value.</label></div></td></tr>
		<tr class="mphb-hide"><th></th><td colspan="1"><div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-number" data-type="number" data-inited="true"><input name="mphb_max_quantity" value="" id="mphb-mphb_max_quantity" class=" mphb-price-text" type="number" min="0" step="1"><p class="description">Empty means unlimited</p></div></td></tr>
		<tr class="mphb-hide">
			<th>
				<label for="mphb-mphb_price_quantity">Charge</label>
			</th>
			<td colspan="1">
				<div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-select" data-type="select" data-inited="true">
					<!--<select name="mphb_price_quantity" id="mphb-mphb_price_quantity">
						<option value="once">Per Accommodation</option>
						<option value="per_adult" selected="selected">Per Guest</option>
					</select>-->
					<input type="text" name="mphb_price_quantity" id="mphb-mphb_price_quantity" value="per_adult">
				</div>
			</td>
		</tr>
		<tr>
			<th><label>Duration/Periodicity Label</label></th>
			<td>
				<input type="text" class="rvb-ml-field" required name="service_label[duration]" value="<?php echo !empty( $service_label['duration'] ) ? $service_label['duration'] : ''; ?>">
				<p class="description">Will be shown behind the duration field, i.e: Time(s) or Day(s)</p>
			</td>
		</tr>
		<tr>
			<th><label>Quantity Label</label></th>
			<td>
				<input type="text" class="rvb-ml-field" required name="service_label[qty]" value="<?php echo !empty( $service_label['qty'] ) ? $service_label['qty'] : ''; ?>">
				<p class="description">Will be shown behind the quantity field, i.e: Person(s), Bike(s), Car(s), Chef(s)</p>
			</td>
		</tr>
	</table>
	<?php
}

function rvb_property_cencel_policy($post){
	$cancel_policy = get_post_meta($post->ID, 'cancel_policy', true);
	$cancellation_policies = get_posts(array(
								'post_type'			=> 'cancel-policy',
								'posts_per_page'	=> -1,
							));
	if(!empty($cancellation_policies)){
		?>
		<table class="form-table">
			<?php
			foreach($cancellation_policies as $cp){
				?>
				<tr>
					<th>
						<input id="cp-<?php echo $cp->ID ?>" <?php echo $cancel_policy == $cp->ID ? 'checked' : ''; ?> name="cancel_policy" type="radio" value="<?php echo $cp->ID ?>">
						<label for="cp-<?php echo $cp->ID ?>"><?php echo apply_filters('the_title', $cp->post_title); ?></label>
					</th>
				</tr>
				<?php
			}
			
			?>
		</table>
		<?php
	}
}

function rvb_payment_information($post){
	?>
	<table class="form-table">
		<tr>
			<th>Amount Paid</th>
			<td><?php 
				$amount_paid = get_post_meta($post->ID, 'rvb_amount_paid', true);
				$amount_to_pay = get_post_meta($post->ID, 'rvb_amount_to_pay_idr', true);
				$amount = !empty($amount_paid) ? $amount_paid : $amount_to_pay;
				
				echo get_post_meta($post->ID, 'rvb_currency_to_pay', true) .' ';
				echo number_format($amount, 0, ',', '.');
			?></td>
		</tr>
		<tr>
			<th>Rate</th>
			<td><?php 
				echo get_post_meta($post->ID, 'rvb_rate', true);
				//echo number_format($rate, 0, ',', '.');
			?></td>
		</tr>
	</table>
	<?php
}

add_action( 'save_post', 'rvb_save_meta_fields_value', 10, 2 );
function rvb_save_meta_fields_value($post_id, $post){
	global $pagenow;
	if ( 'post.php' != $pagenow ) return $post_id;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if($_POST['post_type']=='mphb_room_type'){
		update_post_meta($post_id,'rvb_bedrooms',$_POST['rvb_bedrooms']);
		update_post_meta($post_id,'rvb_bathrooms',$_POST['rvb_bathrooms']);
		update_post_meta($post_id,'rvb_property_photos',$_POST['rvb_property_photos']);
		update_post_meta($post_id,'pinpoint',$_POST['pinpoint']);
		update_post_meta($post_id,'rvb_property_contact_email',$_POST['rvb_property_contact_email']);
		update_post_meta($post_id,'rvb_property_contact_phone',$_POST['rvb_property_contact_phone']);
		update_post_meta($post_id,'rvb_property_contact_new_booking_email',$_POST['rvb_property_contact_new_booking_email']);
		update_post_meta($post_id,'cancel_policy',$_POST['cancel_policy']);
		update_post_meta($post_id,'sleeping_arr',$_POST['meta']['sleeping_arr']);
		update_post_meta($post_id,'landmark',$_POST['landmark']);
		set_post_thumbnail($post_id, $_POST['rvb_property_photos'][0]);
	}
	
	if($_POST['post_type']=='hot-deal'){
		update_post_meta($post_id,'rvb_hd_date_end',$_POST['rvb_hd_date_end']);
		update_post_meta($post_id,'rvb_hd_date_discount',$_POST['rvb_hd_date_discount']);
		update_post_meta($post_id,'rvb_hd_properties',$_POST['rvb_hd_properties']);
		
		if($post->post_status == 'publish'){
			if(!empty($_POST['rvb_hd_properties'])){
				foreach($_POST['rvb_hd_properties'] as $p){
					update_post_meta($p, 'hot_deal', $post_id );
				}
			}
			
		}
	}
	
	if($_POST['post_type']=='mphb_room_service'){
		update_post_meta($post_id,'service_label',$_POST['service_label']);
		
		update_post_meta($post_id,'mphb_price',$_POST['mphb_price']);
		update_post_meta($post_id,'mphb_price_periodicity',$_POST['mphb_price_periodicity']);
		update_post_meta($post_id,'mphb_min_quantity',$_POST['mphb_min_quantity']);
		update_post_meta($post_id,'mphb_is_auto_limit',$_POST['mphb_is_auto_limit']);
		update_post_meta($post_id,'mphb_price_quantity',$_POST['mphb_price_quantity']);
		update_post_meta($post_id,'mphb_max_quantity',$_POST['mphb_max_quantity']);
	}
}

