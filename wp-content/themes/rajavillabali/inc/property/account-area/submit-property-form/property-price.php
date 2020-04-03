<?php
	$current_step = $GLOBALS['submission_step']['price'];
	$next = $current_step + 1;
	$prev = $current_step - 1;
	
	$low_season_fee = 0;
	$high_season_fee = 0;
	$low_season_charge = 0;
	$high_season_charge = 0;
	
	if(!empty($property)){
		$rate_id = get_property_rate($property->ID);
		$fee = get_option('rvb_company_fee');
		
		if($rate_id){
			$rates = get_post_meta($rate_id, 'mphb_season_prices', true);
			
			if(!empty($rates[0]['base_price']['prices'][0])){
				$high_season_fee = $rates[0]['base_price']['prices'][0] * $fee / 100;
			}
			
			if(!empty($rates[2]['base_price']['prices'][0])){
				$low_season_fee = $rates[2]['base_price']['prices'][0] * $fee / 100;
			}
		}
	}
?>
<div id="step-<?php echo $current_step ?>" class="inner-form">
	<div class="container edit-inner">
		<h2 class="step-title"><?php _e('Property Price', 'rajavillabali') ?></h2>
		
		<div class="row">
			<div class="col-sm-8">
				<div class="the-form">
					<div class="field">
						<label><?php _e('Earning per night including taxes - High Season', 'rajavillabali') ?> <sup>*</sup></label>
						<span class="nempel-kiri">USD</span>
						<input type="text" id="price-high-season-input" class="has-nempel property-price-input rvb-required" data-season="high" name="mphb_season_prices[0][base_price][prices][]" value="<?php echo !empty($rates[0]['base_price']['prices'][0]) ? $rates[0]['base_price']['prices'][0] : ''; ?>">
						<?php
							//High season id is 2799
						?>
						<input type="hidden" id="property-price-high" name="mphb_season_prices[0][price][prices][]" value="<?php echo !empty($rates[0]['price']['prices'][0]) ? $rates[0]['price']['prices'][0] : ''; ?>">
						<input type="hidden" name="mphb_season_prices[0][season]" value="2799">
						<input type="hidden" name="mphb_season_prices[0][price][periods][]" value="1">
						<input name="mphb_season_prices[0][price][enable_variations]" value="" type="hidden">
						<input type="hidden" name="mphb_season_prices[0][price][variations]" value="">
						
						
						<?php
							//Christmast season id is 2801
						?>
						<input type="hidden" id="property-price-christmast-season" name="mphb_season_prices[1][price][prices][]" value="<?php echo !empty($rates[1]['price']['prices'][0]) ? $rates[1]['price']['prices'][0] : ''; ?>">
						<input type="hidden" name="mphb_season_prices[1][season]" value="2801">
						<input type="hidden" name="mphb_season_prices[1][price][periods][]" value="1">
						<input name="mphb_season_prices[1][price][enable_variations]" value="" type="hidden">
						<input type="hidden" name="mphb_season_prices[1][price][variations]" value="">
						
						
					</div>
					
					<div class="field">
						<label><?php _e('Earning per night including taxes - Low Season', 'rajavillabali') ?> <sup>*</sup></label>
						<span class="nempel-kiri">USD</span>
						<input type="text" id="price-low-season-input" class="has-nempel property-price-input rvb-required" name="mphb_season_prices[2][base_price][prices][]" data-season="low" value="<?php echo !empty($rates[2]['base_price']['prices'][0]) ? $rates[2]['base_price']['prices'][0] : '';  ?>">
						<?php
							//Low season id is 2800
						?>
						
						<input type="hidden" id="property-price-low" name="mphb_season_prices[2][price][prices][]" value="<?php echo !empty($rates[2]['price']['prices'][0]) ? $rates[2]['price']['prices'][0] : ''; ?>">
						<input type="hidden" name="mphb_season_prices[2][season]" value="2800">
						<input type="hidden" name="mphb_season_prices[2][price][periods][]" value="1">
						<input name="mphb_season_prices[2][price][enable_variations]" value="" type="hidden">
						<input type="hidden" name="mphb_season_prices[2][price][variations]" value="">
						
					</div>
					
					<div class="field">
						
						<b>
							<?php _e('Raja Villa Bali commission and charges', 'rajavillabali'); ?>
						</b>
						
						<div id="ota-earning-high" class="price-calc">
							<span class="the-price">
								USD <span><?php echo $high_season_fee; ?></span> 
							</span>
							
							<?php _e('on high season', 'rajavillabali'); ?>
						</div>
						
						<div id="ota-earning-low" class="price-calc">
							<span class="the-price">
								USD <span><?php echo $low_season_fee; ?></span> 
							</span>
							
							<?php _e('on low season', 'rajavillabali'); ?>
						</div>
						<br>
						<?php
							$strength_points = get_option('strength-point-owner');
							if(!empty($strength_points)){
								?>
								<ul class="osp">
									<?php
									foreach(explode(PHP_EOL, $strength_points) as $strength){
										?>
										<li><?php echo $strength ?></li>
										<?php
									}
									?>
								</ul>
								<?php
							}
						?>
						
						
						<b>
							<?php _e('Total price charged to your guest per night', 'rajavillabali'); ?>
						</b>
						<div id="price-charge">
							<div id="ota-charge-high" class="price-calc">
								<span class="the-price">
									USD <span><?php echo !empty($rates[0]['price']['prices'][0]) ? $rates[0]['price']['prices'][0] : '0';  ?></span> 
								</span>
								<?php _e('on high season', 'rajavillabali'); ?>
							</div>
							
							<div id="ota-charge-low" class="price-calc">
								<span class="the-price">
									USD <span><?php echo !empty($rates[2]['price']['prices'][0]) ? $rates[2]['price']['prices'][0] : '0';  ?></span> 
								</span>
								<?php _e('on low season', 'rajavillabali'); ?>
							</div>
						</div>
					</div>
					
					<div class="field">
						<label><?php _e('Breakfast', 'rajavillabali') ?> <sup>*</sup></label>
						<ul id="breakfast" class="inline-ul">
							<li>
								<input id="bf-yes" class="breakfast" type="radio" value="yes" <?php echo !empty($metas['rvb_breakfast'][0]) && $metas['rvb_breakfast'][0] == 'yes' ? 'checked' : ''; ?> name="meta[rvb_breakfast]" > 
								<label for="bf-yes"><?php _e('Included','rajavillabali'); ?></label>
							</li>
							<li>
								<input id="bf-no" class="breakfast" type="radio" value="no" <?php echo !empty($metas['rvb_breakfast'][0]) && $metas['rvb_breakfast'][0] == 'no' ? 'checked' : ''; ?> name="meta[rvb_breakfast]" >
								<label for="bf-no"><?php _e('Not Included'); ?></label>
							</li>
						</ul>
						
					</div>
					
					<div class="field bf-cost <?php echo $metas['rvb_breakfast'][0] == 'no' ? '' : 'tmp-hide'; ?>">
						<label><?php _e('Additional cost for breakfast per pax', 'rajavillabali') ?></label>
						<span class="nempel-kiri">USD</span>
						<input id="bf-extra-cost" type="text" class="has-nempel" value="<?php echo !empty($metas['rvb_bf_additional_cost'][0]) ? $metas['rvb_bf_additional_cost'][0] : ''; ?>" name="meta[rvb_bf_additional_cost]" >
						<p class="description"><?php _e('Additional cost for breakfast if you would like to provide it as a paid service. Your guest will have this as an option to include when they make a booking.', 'rajavillabali'); ?></p>
					</div>
					
				</div>
				
				<h2 class="step-title"><?php _e('Availability', 'rajavillabali') ?></h2>
				<div class="the-form">
					<div class="field">
						<p><?php _e('Syncronize your property availability if you have your property listed on other OTA', 'rajavillabali'); ?> </p>
						<input type="url" id="ical-url" size="40" placeholder="iCal URL">
						<input type="button" class="button medium" id="add-ical" value="Add">
						<p class="description"><?php echo sprintf(__('Learn more about iCal in <a href="%s" target="_blank">this page</a>', 'rajavillabali'), get_page_link(get_option('rvb_ical_help_page')) ); ?></p>
					</div>
					<div class="field" id="ical-sync">
						<ul id="icals" class="ical-list">
							<?php
							if(!empty($property)){
								$room_id = get_property_room($property->ID);
								$room   = MPHB()->getRoomRepository()->findById( $room_id );
								$urls   = array();

								// updateUrls() in $room->setSyncUrls() will remove all duplicates
								// in the room. Load the real list of URLs
								$urls = $room->getSyncUrls();

								// Prepare for complex field
								$urls = array_map( function( $url ) {
									return array( 'url' => $url );
								}, $urls );

								// Get rid of sync_id's in keys
								$urls = array_values( $urls );
								
								//var_dump($urls);
								if(!empty($urls)){
									$index = 0;
									foreach($urls as $u){
										?>
										<li data-index="<?php echo $index ?>">
											<?php echo $u['url'] ?>
											<input type="hidden" name="mphb_sync_urls[<?php echo $index ?>][url]" value="<?php echo $u['url'] ?>">
											<span class="delete">&times;</span>
										</li>
										<?php
										$index++;
									}
								}
							}
							?>
						</ul>
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
