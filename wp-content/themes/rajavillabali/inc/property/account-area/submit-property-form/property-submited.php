<?php
	$current_step = $GLOBALS['submission_step']['submited'];
	$next = $current_step + 1;
	$prev = $current_step - 1;
?>
<div id="step-<?php echo $current_step ?>" class="inner-form">
	<h2 class="step-title"></h2>
	
	<div class="row">
		<div class="col-sm-12">
			<div class="the-form submitted">
				<div class="field">
					<p id="content-msg"></p>
					<?php
						//_e('Thank you, your property has submitted and are now under review, you will get notified when your property is published.');
						
						$my_listing_page = get_option('rvb_my_listings_page');
					?>
					
					<p><?php _e('See all my listings', 'rajavillabali') ?></p>
					<a class="button" href="<?php echo get_page_link($my_listing_page) ?>"><?php _e('My Listings', 'rajavillabali'); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>