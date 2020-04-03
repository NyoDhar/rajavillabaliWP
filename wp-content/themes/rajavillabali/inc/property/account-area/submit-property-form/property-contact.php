<?php
	$current_step = 8;
	$next = $current_step + 1;
	$prev = $current_step - 1;
?>
<div id="step-<?php echo $current_step ?>" class="inner-form">
	<h2 class="step-title"><?php _e('Property Contact', 'rajavillabali'); ?></h2>
	
	<div class="row">
		<div class="col-sm-8">
			<div class="the-form">
				<div class="field">
					<label><?php _e('Customer Support Phone', 'rajavillabali') ?></label>
					<input type="text" name="meta[rvb_property_contact_phone]" id="rvb_property_contact_phone" value="<?php echo !empty($metas['rvb_property_contact_phone'][0]) ? $metas['rvb_property_contact_phone'][0] : ''; ?>">
				</div>
				<div class="field">
					<label><?php _e('Customer Support Email', 'rajavillabali') ?></label>
					<input type="text" name="meta[rvb_property_contact_email]" id="rvb_property_contact_email" value="<?php echo !empty($metas['rvb_property_contact_email'][0]) ? $metas['rvb_property_contact_email'][0] : ''; ?>">
				</div>
				<div class="field">
					<label><?php _e('New Booking Email Receiver', 'rajavillabali') ?></label>
					<input type="text" name="meta[rvb_property_contact_new_booking_email]" id="rvb_property_contact_new_booking_email" value="<?php echo !empty($metas['rvb_property_contact_new_booking_email'][0]) ? $metas['rvb_property_contact_new_booking_email'][0] : ''; ?>">
				</div>
			</div>
			
			<div class="buttons">
				<i class="fa fa-chevron-left prev" aria-hidden="true" data-step="<?php echo $prev ?>" data-current="<?php echo $current_step ?>"></i>
				<input type="button" class="button next" value="Next" data-step="<?php echo $next ?>" data-current="<?php echo $current_step ?>">
			</div>
			
		</div>
		<div class="col-sm-4">
			<div class="form-description">
				Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
			</div>
		</div>
	</div>
</div>