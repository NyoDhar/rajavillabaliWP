<?php
	$current_step = $GLOBALS['submission_step']['images'];
	$next = $current_step + 1;
	$prev = $current_step - 1;
?>
<div id="step-<?php echo $current_step ?>" class="inner-form">
	<div class="container edit-inner">
		<h2 class="step-title"><?php _e('Property Photos', 'rajavillabali') ?></h2>
		
		<div class="row">
			<div class="col-sm-8">
				<div class="the-form">
					<?php
						if(!empty($metas['rvb_property_photos'][0])){
							$thumbnail_id = get_post_thumbnail_id($property->ID);
							?>
							<div class="field">
								<label><?php _e('Uploaded photos', 'rajavillabali') ?></label>
								<div class="uploaded-images">
									<?php
										//$images = unserialize($metas['rvb_property_photos'][0]);
										foreach(unserialize($metas['rvb_property_photos'][0]) as $img){
											if(!empty($img) && is_numeric($img)){
												?>
												<div class="uploaded-img">
													<?php echo wp_get_attachment_image($img, 'blog-small-thumb'); ?>
													
													<a href="#" data-imgid="<?php echo $img; ?>" class="remove-uploaded-photo"><?php _e('Remove photo', 'rajavillabali'); ?></a>
													<input type="hidden" id="img-<?php echo $img; ?>" class="images-input" name="images[]" value="<?php echo $img ?>">
													<div class="text-center">featured <input title="set as featured image" <?php echo $thumbnail_id == $img ? 'checked' : ''; ?> type="radio" name="featured-image" value="<?php echo $img ?>"></div>
												</div>
												<?php
											}
										}
									?>
								</div>
							</div>
							<?php
						}
					?>
					
					
					<div class="field">
						<label><?php _e('Upload new photos', 'rajavillabali') ?></label>
						<div id="media-uploader" class="dropzone"></div>
						<input type="hidden" id="media-ids" value="">
						<div id="images"></div>
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