<?php
	$current_step = 6;
	$next = $current_step + 1;
	$prev = $current_step - 1;
?>
<div id="step-<?php echo $current_step ?>" class="inner-form">
	<h2 class="step-title"><?php _e('Set House Rule & Cancelation Policy', 'rajavillabali'); ?></h2>
	
	<div class="row">
		<div class="col-sm-8">
			<div class="the-form">
				<?php
					$terms = get_terms(array(
											'taxonomy'		=> 'house-rule',
											'hide_empty'	=> false,
										));
					
					if(!empty($terms) && !is_wp_error($terms)){
						$selected_house_rule = array();
						if(!empty($property)){
							$selected_house_rule = wp_get_post_terms($property->ID, 'house-rule', array( 'fields' => 'ids' ));
						}
					?>
						<div class="field">
							<label><?php _e('House Rule', 'rajavillabali') ?></label>
							<ul class="att-inputs house-rules">
								<?php
									foreach($terms as $term){
										?>
										<li>
											<input id="term-<?php echo 'house-rule'.$term->term_id; ?>" type="checkbox" name="terms[house-rule][]" value="<?php echo $term->term_id; ?>" <?php echo in_array($term->term_id, $selected_house_rule) ? 'checked' : ''; ?>>
											<label for="term-<?php echo 'house-rule'.$term->term_id; ?>"><?php echo $term->name; ?></label>
										</li>
										<?php
									}
								?>
							</ul>
						</div>
					<?php
					}
				?>
				
				<?php
					//$cancel_policy = get_post_meta($post->ID, 'cancel_policy', true);
					$cancellation_policies = get_posts(array(
												'post_type'			=> 'cancel-policy',
												'posts_per_page'	=> -1,
											));
					if(!empty($cancellation_policies)){
						?>
						<div class="field">
							<label><?php _e('Cancellation Policy', 'rajavillabali') ?></label>
							<ul class="cancel-policies">
								<?php
								foreach($cancellation_policies as $cp){
									?>
									<li>
										<input id="cp-<?php echo $cp->ID ?>" name="meta[cancel_policy]" type="radio" value="<?php echo $cp->ID ?>" <?php echo !empty($metas['cancel_policy'][0]) && $metas['cancel_policy'][0] == $cp->ID ? 'checked' : ''; ?> >
										<label for="cp-<?php echo $cp->ID ?>"><?php echo apply_filters('the_title', $cp->post_title); ?></label>
									</li>
									<?php
								}
								
								?>
							</ul>
						</div>
						<?php
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