<?php
	$current_step = 2;
	$next = $current_step + 1;
	$prev = $current_step - 1;
	
?>
<div id="step-<?php echo $current_step ?>" class="inner-form">
	<h2 class="step-title"><?php _e('Property Location', 'rajavillabali') ?></h2>
	
	<div class="row">
		<div class="col-sm-8">
			<div class="the-form">
				<div class="field">
					<label><?php _e('Area', 'rajavillabali') ?></label>
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
					<?php $replace = "<select name='terms[mphb_ra_location]' id='area' required >"; ?>
					<?php $select  = preg_replace( '#<select([^>]*)>#', $replace, $select ); 
						echo $select;
					?>
				</div>
				
				<div class="field">
					<!--<div id="pac-card">
						<input id="address" class="controls" type="text" placeholder="<?php _e('Find Your Address', 'rajavillabali') ?>">
					</div>
					<input type="hidden" id="pinpoint" value="">
					<div id="submit-property-map"></div>-->
					<label><?php _e('Please select your property location below', 'rajavillabali'); ?></label>
					<?php echo do_shortcode('[blk_map show_search="yes" '.$pinpoint.' ]'); ?>
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