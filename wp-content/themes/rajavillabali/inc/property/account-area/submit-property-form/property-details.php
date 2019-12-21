<?php
	$current_step = 1;
	$next = $current_step + 1;
?>

<div id="step-<?php echo $current_step ?>" class="inner-form">
	<h2 class="step-title"><?php _e('Property Information', 'rajavillabali') ?></h2>
	
	<div class="row">
		<div class="col-sm-8">
			<div class="the-form">
				<div class="field">
					<label><?php _e('Property Name', 'rajavillabali') ?></label>
					<input type="text" id="name" value="<?php echo !empty($property) ? $property->post_title : ''; ?>" name="property_name" >
				</div>
				
				<div class="field">
					<label>Bedrooms</label>
					<input type="number" id="bedrooms" value="<?php echo !empty($metas['rvb_bedrooms'][0]) ? $metas['rvb_bedrooms'][0] : ''; ?>" name="meta[rvb_bedrooms]" >
				</div>
				
				<div class="field">
					<label>Guest Capacity</label>
					<input type="number" id="capacity" value="<?php echo !empty($metas['mphb_adults_capacity'][0]) ? $metas['mphb_adults_capacity'][0] : ''; ?>" name="meta[mphb_adults_capacity]" >
				</div>
				
				<div class="field">
					<label>Property Land Size ( sqm )</label>
					<input type="number" id="land-size" value="<?php echo !empty($metas['mphb_size'][0]) ? $metas['mphb_size'][0] : ''; ?>" name="meta[mphb_size]" >
				</div>
				
				<div class="field">
					<label>Description</label>
					<?php 
						$settings = array(
										'quicktags'=>false,
										 'tinymce' => array(
													'init_instance_callback' => 'function(editor) {
																editor.on("change", function(){
																	rvbparams.submitFormChanged = true;
															});
														}'
													),
									);
						
						$content = !empty($property) ? $property->post_content : '';
						wp_editor($content , 'description', $settings ); ?>
				</div>
				
			</div>
			
			<div class="buttons">
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