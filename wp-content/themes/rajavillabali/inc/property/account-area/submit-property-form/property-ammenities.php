<?php
	$current_step = 4;
	$next = $current_step + 1;
	$prev = $current_step - 1;
?>
<div id="step-<?php echo $current_step ?>" class="inner-form">
	<h2 class="step-title"><?php _e('Property Ammenities', 'rajavillabali') ?></h2>
	
	<div class="row">
		<div class="col-sm-8">
			<div class="the-form">
				<?php
					$attributes = get_posts(array(
									'post_type'			=> 'mphb_room_attribute',
									'posts_per_page'	=> -1,
									'meta_query'		=> array(
															array(
																'key'	=> 'mphb_visible',
																'value'	=> '1'
															)
														)
								));
					
					if(!empty($attributes)){
						
						foreach($attributes as $att){
							
							$taxonomy = 'mphb_ra_'.$att->post_name;
							$terms = get_terms(array(
													'taxonomy'		=> $taxonomy,
													'hide_empty'	=> false,
												));
							
							if(!empty($terms) && !is_wp_error($terms)){
								
								$selected_attributes = array();
								if(!empty($property)){
									$selected_attributes = wp_get_post_terms($property->ID, $taxonomy, array( 'fields' => 'ids' ));
								}
								
								$ammenities_name = apply_filters('the_title', $att->post_title);
							?>
								<div class="field">
									<label><?php echo $ammenities_name; ?></label>
									<ul class="att-inputs ammenities" data-name="<?php echo $ammenities_name; ?>">
										<?php
											foreach($terms as $term){
												?>
												<li>
													<input id="term-<?php echo $taxonomy.$term->term_id; ?>" type="checkbox" name="terms[<?php echo $taxonomy ?>][]" value="<?php echo $term->term_id; ?>" <?php echo in_array($term->term_id, $selected_attributes) ? 'checked' : ''; ?> >
													<label for="term-<?php echo $taxonomy.$term->term_id; ?>"><?php echo $term->name; ?></label>
												</li>
												<?php
											}
										?>
									</ul>
								</div>
							<?php
							}
						}
					}
				?>
				
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