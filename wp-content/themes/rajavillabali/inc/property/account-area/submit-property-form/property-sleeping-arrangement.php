<?php
	$current_step = 2;
	$next = $current_step + 1;
	$prev = $current_step - 1;
?>

<div id="step-<?php echo $current_step ?>" class="inner-form sleeping-arrangement">
	<h2 class="step-title"><?php _e('Sleeping Arrangement', 'rajavillabali') ?></h2>
	
	<div class="row">
		<div class="col-sm-8">
			<div class="the-form">
				<?php
					if(!empty($metas['sleeping_arr'][0])){
						$slepping_arrangemnt = unserialize($metas['sleeping_arr'][0]);
						$mphb_bed_types = get_option('mphb_bed_types');
						$i=0;
						foreach($slepping_arrangemnt as $sa){
							?>
							<div class="field the-room">
								<label><?php printf(__('Room %d', 'rajavillabali'), ($i+1)); ?></label>
								<ul>
									<li>
										<b>Bed Type</b> :
										<select name="meta[sleeping_arr][<?php echo $i; ?>][bed_type]" class="rvb-required">
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
					}elseif(!empty($metas['rvb_bedrooms'][0])){
						render_sleeping_arrangement_form($metas['rvb_bedrooms'][0], 0);
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