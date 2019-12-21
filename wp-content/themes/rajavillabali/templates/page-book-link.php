<?php
/**
Template Name: Page Book Link
 */

get_header();
?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main">

		<div class="container">
			<div class="booking-prepare text-center">
				<h1><?php _e('Your booking page are being prepared') ?></h1>
				<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
			</div>
			
			<?php
				if(!empty($_GET['vid']) && !empty($_GET['ciid']) && !empty($_GET['coid'])){
					$isDirectBooking = MPHB()->settings()->main()->isDirectBooking();
					$actionUrl		 = MPHB()->settings()->pages()->getSearchResultsPageUrl();
					$formMethod		 = 'GET';
					if ( $isDirectBooking ) {
						$actionUrl	 = MPHB()->settings()->pages()->getCheckoutPageUrl();
						$formMethod	 = 'POST';
					}
					
					?>
					<form id="book-link" class="tmp-hide" method="<?php echo $formMethod ?>" action="<?php echo $actionUrl ?>">
						<?php wp_nonce_field( \MPHB\Shortcodes\CheckoutShortcode::NONCE_ACTION_CHECKOUT, \MPHB\Shortcodes\CheckoutShortcode::NONCE_NAME ); ?>
						<input type="hidden" name="mphb_room_type_id" value="<?php echo esc_attr( $_GET['vid'] ); ?>" />
						<input type="hidden" name="mphb_check_in_date" value="<?php echo esc_attr( $_GET['ciid'] ); ?>" />
						<input type="hidden" name="mphb_check_out_date" value="<?php echo esc_attr( $_GET['coid'] ); ?>" />
						<select class="mphb-rooms-quantity" name="mphb_rooms_details[<?php echo esc_attr( $_GET['vid'] ); ?>]"><option value="1">1</option></select>
					</form>
					<?php
				}
			?>
		</div>
		<script>
			jQuery(document).ready(function($){
				if($('form#book-link').length){
					$('form#book-link').submit();
				}
			});
		</script>
		</main><!-- #main -->
	</div><!-- #primary -->
<?php
get_footer();