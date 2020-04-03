<?php
	$current_step = $GLOBALS['submission_step']['review'];
	$next = $current_step + 1;
	$prev = $current_step - 1;
?>
<div id="step-<?php echo $current_step ?>" class="inner-form">
	<h2 class="step-title"><?php _e('Review your property details', 'rajavillabali'); ?></h2>
	
	<div class="row">
		<div class="col-sm-8">
			<div class="the-form review">
				<div class="field">
					<label><?php _e('Property Name', 'rajavillabali') ?></label>
					<div id="property-name-review"></div>
				</div>
				
				<div class="field">
					<label><?php _e('Number of bedrooms, bathrooms, max guest, land size, and home area', 'rajavillabali') ?></label>
					<div id="property-detail-review"></div>
				</div>
				
				<div class="field ammenities">
					<label><p><?php _e('Sleeping Arrangement', 'rajavillabali') ?></p></label>
					<div id="property-sa-review"></div>
				</div>
				
				<div class="field ammenities">
					<label><p><?php _e('Ammenities', 'rajavillabali'); ?></p></label>
					<div id="ammenties-misc-review"></div>
					<div id="ammenties-review"></div>
				</div>
				
				<div class="field">
					<label><?php _e('Property Description', 'rajavillabali'); ?></label>
					<div id="description-review"></div>
				</div>
				
				<div class="field">
					<label><?php _e('Property Contact', 'rajavillabali'); ?></label>
					<div id="contact-review"></div>
				</div>
				
				<div class="field">
					<label><?php _e('Bank Account', 'rajavillabali'); ?></label>
					<div class="info-box">
						<div class="info-body">
							<p><?php _e('This information will be used to wire you the payment', 'rajavillabali'); ?></p>
							<ul>
								<li>
									<label><?php _e('Bank Name', 'rajavillabali'); ?></label> : 
									<?php echo !empty($bank_info['name']) ? $bank_info['name'] : '' ?>
								</li>
								<li>
									<label><?php _e('Bank Address', 'rajavillabali'); ?></label> : 
									<?php echo !empty($bank_info['address']) ? $bank_info['address'] : '' ?>
								</li>
								<li>
									<label><?php _e('Account Name', 'rajavillabali'); ?></label> : 
									<?php echo !empty($bank_info['account_name']) ? $bank_info['account_name'] : '' ?>
								</li>
								<li>
									<label><?php _e('Account Number', 'rajavillabali'); ?></label> : 
									<?php echo !empty($bank_info['account_number']) ? $bank_info['account_number'] : '' ?>
								</li>
								<li>
									<label><?php _e('Account Currency', 'rajavillabali'); ?></label> : 
									<?php echo !empty($bank_info['account_currency']) ? $bank_info['account_currency'] : '' ?>
								</li>
								<li>
									<label><?php _e('Swift Code', 'rajavillabali'); ?></label> : 
									<?php echo !empty($bank_info['swift_code']) ? $bank_info['swift_code'] : '' ?>
								</li>
							<ul>
						</div>
					</div>
				</div>
				
				<div class="text-right">
					<a href="#" class="edit-info prev button" data-step="<?php echo $GLOBALS['submission_step']['info'] ?>">edit</a>
				</div>
			</div>
			
			
			<h2 class="step-title"><?php _e('Property Location', 'rajavillabali'); ?></h2>
			<div class="the-form review">
				<div class="field">
					<label><?php _e('Property Full Address', 'rajavillabali') ?></label>
					<div id="address-review"></div>
				</div>
				
				<div class="field">
					<label><?php _e('Area', 'rajavillabali') ?></label>
					<div id="area-review"></div>
				</div>
				
				<div class="field">
					<label><?php _e('Location', 'rajavillabali') ?></label>
					<div id="map-review" class="blk-map"></div>
				</div>
				
				<div class="field">
					<label><?php _e('Nearby Landmark', 'rajavillabali') ?></label>
					<div id="landmark-review"></div>
				</div>
				
				<div class="field">
					<label><?php _e('Views', 'rajavillabali') ?></label>
					<div id="views-review"></div>
				</div>
				
				<div class="field">
					<input type="checkbox" id="confirm-info"> 
					<span id="confirm-info-text">
						<?php _e('i confirm that my property location and property name are correct', 'rajavillabali') ?>
					</span>
				</div>
				
				<div class="text-right">
					<a href="#" class="edit-info prev button" data-step="<?php echo $GLOBALS['submission_step']['location'] ?>">edit</a>
				</div>
			</div>
			
			<h2 class="step-title"><?php _e('Property Photos', 'rajavillabali'); ?></h2>
			<div class="the-form review">
				
				<div class="field">
					<div id="photos-review"></div>
					
				</div>
				
				<div class="text-right">
					<a href="#" class="edit-info prev button" data-step="<?php echo $GLOBALS['submission_step']['images'] ?>">edit</a>
				</div>
			</div>
			
			
			<h2 class="step-title"><?php _e('Property Price & Availability', 'rajavillabali'); ?></h2>
			<div class="the-form review">
				
				<div class="field">
					<label><?php _e('What your guest pay', 'rajavillabali'); ?></label>
					<div id="price-review"></div>
					
				</div>
				
				<div class="field review">
					<label><?php _e('Availability Synchronization', 'rajavillabali'); ?></label>
					<p><?php _e('Your property availability will be synchronized with the following calendar', 'rajavillabali'); ?></p>
					<div id="ical-review"></div>
				</div>
				
				<div class="text-right">
					<a href="#" class="edit-info prev button" data-step="<?php echo $GLOBALS['submission_step']['price'] ?>">edit</a>
				</div>
			</div>
			
			<h2 class="step-title"><?php _e('House Rules & Cancelation Policy', 'rajavillabali'); ?></h2>
			<div class="the-form review">
				
				<div class="field">
					<label><?php _e('House Rules', 'rajavillabali'); ?></label>
					<div id="house-rules-review"></div>
				</div>
				
				<div class="field">
					<label><?php _e('Check-in & Check-out', 'rajavillabali'); ?></label>
					<div id="check-inout-review"></div>
				</div>
				
				<div class="field">
					<label><?php _e('Cancellation Policy', 'rajavillabali'); ?></label>
					<div id="cancel-policy-review"></div>
				</div>
				
				<div class="text-right">
					<a href="#" class="edit-info prev button" data-step="<?php echo $GLOBALS['submission_step']['house_rule'] ?>">edit</a>
				</div>
			</div>
			
			<div class="buttons">
				<?php
					if( !empty($property) && $property->post_status != 'draft' ){
						$button_text = __('Save Property', 'rajavillabali');
					}else{
						$button_text = __('Submit Property', 'rajavillabali');
					}
					
				?>
				<input type="button" class="button" id="submit-it" value="<?php echo $button_text; ?>">
			</div>
			
		</div>
		<div class="col-sm-4">
			<div class="form-description">
				Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
			</div>
		</div>
	</div>
</div>