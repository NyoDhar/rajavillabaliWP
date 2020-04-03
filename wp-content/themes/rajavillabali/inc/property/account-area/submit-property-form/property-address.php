<?php
	$current_step = $GLOBALS['submission_step']['location'];
	$next = $current_step + 1;
	$prev = $current_step - 1;
	
?>
<div id="step-<?php echo $current_step ?>" class="inner-form">
	<div class="container edit-inner">
		<h2 class="step-title"><?php _e('Property Location', 'rajavillabali') ?></h2>
		
		<div class="row">
			<div class="col-sm-8">
				<div class="the-form">
					<div class="field">
						<label><?php _e('Property Full Address', 'rajavillabali') ?> <sup>*</sup></label>
						<input type="text" id="property-address" class="rvb-required" value="<?php echo !empty($metas['rvb_property_address'][0]) ? $metas['rvb_property_address'][0] : ''; ?>" name="meta[rvb_property_address]" >
					</div>
					<div class="field">
						<label><?php _e('Area', 'rajavillabali') ?> <sup>*</sup></label>
						<?php
							$args = array(
										'hide_empty'	=> false,
										'hierarchical'	=> true,
										'option_none_value'	=> ' ',
										/* 'id'			=> 'area',
										'name'			=> 'terms[mphb_ra_location]', */
										'taxonomy'		=> 'mphb_ra_location',
										'show_option_all'	=> ' ',
										'echo'			=> 0,
									);
							
							$pinpoint = '';
							
							if(!empty($property)){
								$locations = wp_get_post_terms($property->ID, 'mphb_ra_location');
								$args['selected'] = $locations[0]->term_id;
								
								if(!empty($metas['pinpoint'][0])){
									$pinpoint = ' pinpoint="'.$metas['pinpoint'][0].'" ';
								}
							}
							
							$select = wp_dropdown_categories($args);
						?>
						<?php $replace = "<select name='terms[mphb_ra_location]' id='area' class='rvb-required' >"; ?>
						<?php $select  = preg_replace( '#<select([^>]*)>#', $replace, $select ); 
							echo $select;
						?>
					</div>
					
					<div class="field">
						
						<label><?php _e('Please select your property location below', 'rajavillabali'); ?> <sup>*</sup></label>
						<?php echo do_shortcode('[blk_map show_search="yes" '.$pinpoint.' marker="pin" ]'); ?>
					</div>
					
					<div class="field">
						<label><?php _e('Nearby Landmark', 'rajavillabali'); ?></label>
						<textarea id="property-landmark" name="meta[landmark]"><?php echo !empty($metas['landmark'][0]) ? $metas['landmark'][0] : ''; ?></textarea>
						<small><?php _e('Comma separated, i.e: Restaurant, Beach', 'rajavillabali'); ?></small>
					</div>
					
					<div class="field">
						<label><?php _e('Views', 'rajavillabali'); ?></label>
						<?php
							$rvb_property_views = get_terms( array(
										'taxonomy' => 'mphb_ra_view',
										'hide_empty' => false,
									) );
							if(!empty($rvb_property_views)){
								
								$selected_views = array();
								if(!empty($property)){
									$selected_views = wp_get_post_terms($property->ID, 'mphb_ra_view', array( 'fields' => 'ids' ));
								}
								
								?>
								<ul id="property-views" class="att-inputs">
									<?php
									foreach($rvb_property_views as $view){
										?>
										<li>
											<input type="checkbox" id="pviews-<?php echo $view->term_id; ?>" name="terms[mphb_ra_view][]" value="<?php echo $view->term_id ?>" <?php echo in_array($view->term_id, $selected_views) ? 'checked' : ''; ?> >
											<label for="pviews-<?php echo $view->term_id; ?>"><?php echo $view->name ?></label>
										</li>
										<?php
									}
									?>
								</ul>
								<?php
							}
						?>
					</div>
				</div>
				
				<div class="buttons">
					<i class="fa fa-chevron-left prev" aria-hidden="true" data-step="<?php echo $prev ?>" data-current="<?php echo $current_step ?>"></i>
					<input type="button" class="button next" value="Next" data-step="<?php echo $next ?>" data-current="<?php echo $current_step ?>">
				</div>
				
				<div class="buttons-editing">
					<input type="button" class="button save-edit" value="Save" data-current="<?php echo $current_step ?>">
					<input type="button" class="button cancel-edit" value="Cancel" data-current="<?php echo $current_step ?>">
				</div>
				
			</div>
			<div class="col-sm-4">
				<div class="form-description">
					Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
				</div>
			</div>
		</div>
	</div>
</div>