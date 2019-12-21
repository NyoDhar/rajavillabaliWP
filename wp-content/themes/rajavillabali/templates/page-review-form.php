<?php
/**
Template Name: Page Review Form
 */

get_header();
?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main">
		<?php
		if ( have_posts() ) :
			the_post();
			
			$booking_id = $_GET['b']; //3240
			$booking = MPHB()->getBookingRepository()->findById($booking_id);
			$reservedRooms	 = $booking->getReservedRooms();
			$accomodation_id = $reservedRooms[0]->getRoomTypeId();
			$customer = $booking->getCustomer();
			
			$villa_name = get_the_title($accomodation_id);
			//var_dump($customer);
			//$accomodation_id = 2815;
		?>
		<?php
			$bg = '';
				if(has_header_image()){
					$bg = 'style="background-image: url('. esc_url( get_header_image() ).');"';
				}
			?>
		<header class="entry-header bg" <?php echo $bg; ?>>
			<div class="container">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				<?php
				if(function_exists('bcn_display') && !is_front_page()){		
					?>
					<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
						<?php bcn_display(); ?>
					</div>
					
					<?php 
				} ?>
			</div>
		</header><!-- .entry-header -->
		
		<article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
			<div class="container rvb-small-col">
				<div class="row">
					<div class="col-sm-12 text-center review-hello">
						<h1>Hi <?php echo $customer->getFirstName(); ?></h1>
						<p><?php echo sprintf( __('How was your stay at %s ?', 'rajavillabali'), $villa_name ); ?></p>
						
						<span class="rvb-divider-separator"></span>
						
					</div>
					<div class="col-sm-12">
						<div class="photos">
							<?php
							$show_total = 0;
							$gallery_imgs = get_post_meta($accomodation_id, 'rvb_property_photos', true);
							if(!empty($gallery_imgs)){
								foreach($gallery_imgs as $img){
									$src = wp_get_attachment_image_src($img, 'large');
									?>
									<a href="<?php echo $src[0]; ?>">
										<?php echo wp_get_attachment_image($img, 'blog-small-thumb'); ?>
									</a>
									<?php
									$show_total++;
									
									if($show_total == 6) break;
								}
								
								if(count($gallery_imgs) > 6){
									?>
									<p>
										<a class="button" href="<?php echo wp_get_attachment_image_src($gallery_imgs[0], 'large')[0]; ?>" >
											<?php _e('See all photos', 'rajavillabali'); ?>
										</a>
									</p>
									<?php
								}
							}
							
							?>
							
						</div>
					</div>
					<div class="col-sm-12">
						<div class="the-review-form" id="comments">
							<input type="hidden" id="accomodation-id" value="<?php $accomodation_id ?>">
							<?php
								$fields =  array(
									  'author' =>
										'<p class="comment-form-author"><label for="author">' . __( 'Name', 'rajavillabali' ) .
										'<span class="required">*</span></label> ' .
										'<input id="author" name="author" type="text" value="' . $customer->getFirstName() .' '. $customer->getLastName() .
										'" size="30" /></p>',

									  'email' =>
										'<p class="comment-form-email"><label for="email">' . __( 'Email', 'rajavillabali' ) .
										'<span class="required">*</span></label> ' .
										'<input id="email" name="email" type="text" value="' . $customer->getEmail() .
										'" size="30" /></p>',
									);
								$comments_args = array(
										// change the title of send button 
										'label_submit'=>'Send',
										// change the title of the reply section
										'title_reply'=>'',
										// remove "Text or HTML to be displayed after the set of comment fields"
										'comment_notes_after' => '',
										// redefine your own textarea (the comment body)
										'comment_field' => '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label><br /><textarea rows="5" id="comment" name="comment" aria-required="true"></textarea></p>',
										 'fields' => apply_filters( 'comment_form_default_fields', $fields ),
								);

								comment_form($comments_args, $accomodation_id);
							?>
							<div id="rvb-comment-notice"></div>
						</div>
					</div>
					
				</div>
			</div>
		</article>
		<?php endif; ?>
		</main><!-- #main -->
	</div><!-- #primary -->
<?php
get_footer();
