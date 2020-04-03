<?php
	$current_step = $GLOBALS['submission_step']['info'];
	$next = $current_step + 1;
?>

<div id="step-<?php echo $current_step ?>" class="inner-form">
	<div class="container edit-inner">
		<h2 class="step-title"><?php _e('Property Information', 'rajavillabali') ?></h2>
		
		<div class="row">
			<div class="col-sm-8">
				<div class="the-form">
					<div class="field">
						<label><?php _e('Property Name', 'rajavillabali') ?> <sup>*</sup></label>
						<input type="text" id="name" class="rvb-required" value="<?php echo !empty($property) ? $property->post_title : ''; ?>" name="property_name" >
					</div>
					
					<div class="field">
						<label>Bedrooms <sup>*</sup></label>
						<input type="number" id="bedrooms" class="rvb-required" value="<?php echo !empty($metas['rvb_bedrooms'][0]) ? $metas['rvb_bedrooms'][0] : ''; ?>" name="meta[rvb_bedrooms]" >
					</div>
					
					<div class="field">
						<label>Bathrooms <sup>*</sup></label>
						<input type="number" id="bathrooms" class="rvb-required" value="<?php echo !empty($metas['rvb_bathrooms'][0]) ? $metas['rvb_bathrooms'][0] : ''; ?>" name="meta[rvb_bathrooms]" >
					</div>
					
					<div class="field">
						<label>Guest Capacity <sup>*</sup></label>
						<input type="number" id="capacity" class="rvb-required" value="<?php echo !empty($metas['mphb_adults_capacity'][0]) ? $metas['mphb_adults_capacity'][0] : ''; ?>" name="meta[mphb_adults_capacity]" >
					</div>
					
					<div class="field">
						<label><?php _e('Land Size', 'rajavillabali') ?> ( sqm )</label>
						<input type="number" id="land-size" value="<?php echo !empty($metas['mphb_size'][0]) ? $metas['mphb_size'][0] : ''; ?>" name="meta[mphb_size]" >
					</div>
					
					<div class="field">
						<label><?php _e('Home Area', 'rajavillabali') ?> ( sqm )</label>
						<input type="number" id="home-area" value="<?php echo !empty($metas['rvb_home_area'][0]) ? $metas['rvb_home_area'][0] : ''; ?>" name="meta[rvb_home_area]" >
					</div>
					
					<div class="field">
						<label><?php _e('Sleeping Arrangement', 'rajavillabali') ?> <sup>*</sup></label>
						<div class="sleeping-arrangement">
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
												<b><?php _e('is en suite bathroom?', 'rajavillabali'); ?></b>:
												<input type="checkbox" name="meta[sleeping_arr][<?php echo $i; ?>][ensuite_bathroom]" value="yes" <?php echo 'yes' == $sa['ensuite_bathroom'] ? 'checked' : ''; ?>>
											</li>
										</ul>
									</div>
									<?php
									$i++;
								}
							}elseif(!empty($metas['rvb_bedrooms'][0])){
								render_sleeping_arrangement_form($metas['rvb_bedrooms'][0], 0);
							}else{
								 _e('Please fill in the bedrooms field to set sleeping arrangement', 'rajavillabali');
							}
							?>
						</div>
					</div>
					
					<div class="field">
						<div class="row">
							<div class="col-sm-6">
								<label><b><?php _e('Pool Size', 'rajavillabali') ?> ( i.e: 3x5 Meters )</b></label>
								<input type="text" id="pool-size" value="<?php echo !empty($metas['rvb_pool_size'][0]) ? $metas['rvb_pool_size'][0] : ''; ?>" name="meta[rvb_pool_size]" >
								
							</div>
							<div class="col-sm-6">
								<label><b><?php _e('Pool', 'rajavillabali') ?></b></label><br>
								<select id="pool-type" name="meta[rvb_pool_type]">
									<option value=""></option>
									<option value="Private" <?php echo !empty($metas['rvb_pool_type'][0]) && $metas['rvb_pool_type'][0] == 'Private' ? 'selected' : ''; ?> >Private</option>
									<option value="Shared" <?php echo !empty($metas['rvb_pool_type'][0]) && $metas['rvb_pool_type'][0] == 'Shared' ? 'selected' : ''; ?> >Shared</option>
								</select>
							</div>
						</div>
					</div>
					
					<div class="field">
						<ul class="att-inputs ammenities" id="misc-ammenities">
							<li>
								<label for="garage"><?php _e('Garage', 'rajavillabali') ?></label>
								<input id="garage" type="checkbox" <?php echo !empty($metas['rvb_garage'][0]) && $metas['rvb_garage'][0] == 'on' ? 'checked' : '' ; ?> name="meta[rvb_garage]" >
							</li>
							<li>
								<label for="carport"><?php _e('Carports', 'rajavillabali') ?></label>
								<input id="carport" type="checkbox" <?php echo !empty($metas['rvb_carports'][0]) && $metas['rvb_carports'][0] == 'on' ? 'checked' : '' ; ?> name="meta[rvb_carports]" >
							</li>
							<li>
								<label for="garden"><?php _e('Garden', 'rajavillabali') ?></label>
								<input id="garden" type="checkbox" <?php echo !empty($metas['rvb_garden'][0]) && $metas['rvb_garden'][0] == 'on' ? 'checked' : '' ; ?> name="meta[rvb_garden]" >
							</li>
						</ul>
						
					</div>
					
					<?php
						$att_input_settings = get_option('ammenities_input');
						$attributes = get_posts(array(
										'post_type'			=> 'mphb_room_attribute',
										'posts_per_page'	=> -1,
										'post__in'			=> $att_input_settings,
										/* 'meta_query'		=> array(
																array(
																	'key'	=> 'mphb_visible',
																	'value'	=> '1'
																)
															) */
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
									<div class="field ammenities-inputs">
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
				
				<h2 class="step-title"><?php _e('Property Contact', 'rajavillabali'); ?></h2>
				
				<div class="the-form">
					
					<div class="field">
						<label><?php _e('Title', 'rajavillabali'); ?> <sup>*</sup></label>
						<select name="meta[rvb_property_contact_title]" id="rvb_property_contact_title" class="rvb-required">
							<option value=""></option>
							<option value="Homeowner" <?php echo !empty($metas['rvb_property_contact_title'][0]) && $metas['rvb_property_contact_title'][0] == 'Homeowner' ? 'selected' : '' ?> >Homeowner</option>
							<option value="Villa Manager/Management" <?php echo !empty($metas['rvb_property_contact_title'][0]) && $metas['rvb_property_contact_title'][0] == 'Villa Manager/Management' ? 'selected' : '' ?>>Villa Manager/Management</option>
							<option value="Subleaser" <?php echo !empty($metas['rvb_property_contact_title'][0]) && $metas['rvb_property_contact_title'][0] == 'Subleaser' ? 'selected' : '' ?>>Subleaser</option>
						</select>
					</div>
					<div class="field">
						<label><?php _e('Name', 'rajavillabali') ?> <sup>*</sup></label>
						<input type="text" name="meta[rvb_property_contact_name]" id="rvb_property_contact_name" class="rvb-required" value="<?php echo !empty($metas['rvb_property_contact_name'][0]) ? $metas['rvb_property_contact_name'][0] : ''; ?>">
					</div>
					<div class="field">
						<label><?php _e('Phone', 'rajavillabali') ?> <sup>*</sup></label>
						<input type="text" name="meta[rvb_property_contact_phone]" id="rvb_property_contact_phone" class="rvb-required" value="<?php echo !empty($metas['rvb_property_contact_phone'][0]) ? $metas['rvb_property_contact_phone'][0] : ''; ?>">
					</div>
					<div class="field">
						<label><?php _e('Email', 'rajavillabali') ?> <sup>*</sup></label>
						<input type="text" name="meta[rvb_property_contact_email]" id="rvb_property_contact_email" class="rvb-required" value="<?php echo !empty($metas['rvb_property_contact_email'][0]) ? $metas['rvb_property_contact_email'][0] : ''; ?>">
					</div>
					<div class="field">
						<label><?php _e('New Booking Email Receiver', 'rajavillabali') ?> <sup>*</sup></label>
						<input type="text" name="meta[rvb_property_contact_new_booking_email]" id="rvb_property_contact_new_booking_email" class="rvb-required" value="<?php echo !empty($metas['rvb_property_contact_new_booking_email'][0]) ? $metas['rvb_property_contact_new_booking_email'][0] : ''; ?>">
					</div>
					
					<div class="field" id="legal-docs">
						<label><?php _e('Legal Documents', 'rajavillabali') ?> <sup>*</sup></label>
						<?php
						//echo $metas['rvb_property_legal_document'][0];
							$docs = unserialize($metas['rvb_property_legal_document'][0]);
							if(!empty($docs) && is_array($docs)){
							?>
								<div class="uploaded-images">
									<?php
									foreach( $docs as $img ){
										?>
										<div class="uploaded-img">
											<?php 
											$type =  get_post_mime_type($img);
											if($type == 'application/pdf'){
												$title = get_the_title($img);
												?>
												<div class="row">
													<div class="col-sm-12">
														<a href="<?php echo wp_get_attachment_url($img); ?>" target="_blank" title="<?php echo $title; ?>">
															
															<i class="fa fa-file-pdf-o fa-5x fa-pull-left" aria-hidden="true"></i>
															<?php echo wp_trim_words( $title, 7, null ); ?><br>
														</a>
													</div>
												</div>
												<?php
											}else{
												echo wp_get_attachment_image($img, 'blog-small-thumb');
											}
											
											?>
											<a href="#" data-imgid="<?php echo $img; ?>" class="remove-uploaded-photo"><?php _e('Remove', 'rajavillabali'); ?></a>
											<input type="hidden" id="img-<?php echo $img; ?>" class="legal-docs-input" name="meta[rvb_property_legal_document][]" value="<?php echo $img ?>">
											
										</div>
										<?php
									}
									?>
								</div>
							<?php
							}
						?>
						
					</div>
					
					<div class="field">
						<!--<input type="file" name="meta[rvb_property_legal_document]">-->
						
						<div id="file-uploader" class="dropzone"></div>
						<div id="files"></div>
						
						<p class="description">
							
							<?php
							echo '(*) '; _e('Mandatoy', 'rajavillabali'); echo '<br>';
							_e('<b>Homeowner :</b> Ownership doc(*), ID Card(*), Tax Number(optional)<br>
								<b>Villa Manager/Management :</b> Management Aggreement(*), ID Owner(*), NPWP perusahaan(optional)<br>
								<b>Subleaser :</b> Lease Agreement(*), ID Subleaser(*), ID Owner(*)', 'rajavillabali');
							?>
						</p>
					</div>
				</div>
				
				<h2 class="step-title"><?php _e('Bank Account', 'rajavillabali'); ?></h2>
				
				<div class="the-form">
					<?php
						$user = rvb_get_current_user();
						$bank_info = get_user_meta($user->ID, 'bankinfo', true);
					?>
					
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
				
				
				<div class="buttons">
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