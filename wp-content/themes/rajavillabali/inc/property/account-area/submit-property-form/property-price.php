<?php
	$current_step = 5;
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
	<h2 class="step-title"><?php _e('Property Price', 'rajavillabali') ?></h2>
	
	<div class="row">
		<div class="col-sm-8">
			<div class="the-form">
				<div class="field">
					<label><?php _e('Earning per night including taxes - High Season', 'rajavillabali') ?></label>
					<span class="nempel-kiri">USD</span>
					<input type="text" id="price-high-season-input" class="has-nempel property-price-input" data-season="high" name="mphb_season_prices[0][base_price][prices][]" value="<?php echo !empty($rates[0]['base_price']['prices'][0]) ? $rates[0]['base_price']['prices'][0] : ''; ?>">
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
					<label><?php _e('Earning per night including taxes - Low Season', 'rajavillabali') ?></label>
					<span class="nempel-kiri">USD</span>
					<input type="text" id="price-low-season-input" class="has-nempel property-price-input" name="mphb_season_prices[2][base_price][prices][]" data-season="low" value="<?php echo !empty($rates[2]['base_price']['prices'][0]) ? $rates[2]['base_price']['prices'][0] : '';  ?>">
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
						<?php _e('Total price charged to your guest per night - High Season', 'rajavillabali'); ?>
					</b>
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
