<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package rajavillabali
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer">
		<div class="container">
			<!--<div class="row text-center">
				<h3 class="footer-title">Contact</h3>
				<div class="col-sm-4">
					<i class="fa fa-phone" aria-hidden="true"></i>
					<?php if ( is_active_sidebar( 'footer_left' ) ) : ?>
						<?php dynamic_sidebar( 'footer_left' ); ?>
					<?php endif; ?>
				</div>
				<div class="col-sm-4">
					<i class="fa fa-map-pin" aria-hidden="true"></i>
					<?php if ( is_active_sidebar( 'footer_middle' ) ) : ?>
						<?php dynamic_sidebar( 'footer_middle' ); ?>
					<?php endif; ?>
				</div>
				<div class="col-sm-4">
					<i class="fa fa-envelope-o" aria-hidden="true"></i>
					<?php if ( is_active_sidebar( 'footer_right' ) ) : ?>
						<?php dynamic_sidebar( 'footer_right' ); ?>
					<?php endif; ?>
				</div>
					
			</div>-->
			<div class="credits">
				<div class="row">
					<div class="col-sm-6">
						&copy; <?php bloginfo('name'); ?> <?php echo date('Y'); ?>, <?php _e('All Rights Reserved', 'rajavillabali');  ?>.
					</div>
					<div class="col-sm-6 text-right">
						<?php //_e('Developed by Wayan Bali Web', 'rajavillabali');  ?>
						<ul class="social-icons">
							<li><a href="#"><i class="fa fa-facebook"></i></a></li>
							<li><a href="#"><i class="fa fa-instagram"></i></a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
