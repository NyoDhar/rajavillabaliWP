<?php
	$current_step = 8;
	$next = $current_step + 1;
	$prev = $current_step - 1;
?>
<div id="step-<?php echo $current_step ?>" class="inner-form">
	<h2 class="step-title"><?php _e('Review your property details', 'rajavillabali'); ?></h2>
	
	<div class="row">
		<div class="col-sm-8">
			<div class="the-form review">
				<div class="field">
				
					<label><?php _e('Area', 'rajavillabali') ?></label>
					<div id="area-review"></div>
					
					<label><?php _e('Location', 'rajavillabali') ?></label>
					<div id="map-review"></div>
					
					<a href="#" class="edit-info prev" data-step="2">edit</a>
				</div>
				
				<div class="field">
					<label><?php _e('Property Name', 'rajavillabali') ?></label>
					<div id="property-name-review"></div>
					
					<a href="#" class="edit-info prev" data-step="1">edit</a>
				</div>
				
				<div class="field">
					<input type="checkbox" id="confirm-info"> 
					<span id="confirm-info-text">
						<?php _e('i confirm that my property location and property name are correct', 'rajavillabali') ?>
					</span>
				</div>
				
				<div class="field">
					<label><?php _e('Number of bedrooms, max guest, and land size', 'rajavillabali') ?></label>
					<div id="property-detail-review"></div>
					
					<a href="#" class="edit-info prev" data-step="1">edit</a>
				</div>
				
				<div class="field">
					<label><?php _e('Photos', 'rajavillabali'); ?></label>
					<div id="photos-review"></div>
					
					<a href="#" class="edit-info prev" data-step="3">edit</a>
				</div>
				
				<div class="field">
					<label><?php _e('Ammenities', 'rajavillabali'); ?></label>
					<div id="ammenties-review"></div>
					
					<a href="#" class="edit-info prev" data-step="4">edit</a>
				</div>
				
				<div class="field">
					<label><?php _e('What your guest pay', 'rajavillabali'); ?></label>
					<div id="price-review"></div>
					
					<a href="#" class="edit-info prev" data-step="5">edit</a>
				</div>
				
				<div class="field">
					<label><?php _e('House Rules', 'rajavillabali'); ?></label>
					<div id="house-rules-review"></div>
					
					<a href="#" class="edit-info prev" data-step="6">edit</a>
				</div>
				
				<div class="field">
					<label><?php _e('Cancellation Policy', 'rajavillabali'); ?></label>
					<div id="cancel-policy-review"></div>
					
					<a href="#" class="edit-info prev" data-step="6">edit</a>
				</div>
				
				<div class="field">
					<label><?php _e('Property Contact', 'rajavillabali'); ?></label>
					<div id="contact-review"></div>
					
					<a href="#" class="edit-info prev" data-step="7">edit</a>
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