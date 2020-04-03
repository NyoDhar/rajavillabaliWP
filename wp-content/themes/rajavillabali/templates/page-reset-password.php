<?php
/**
Template Name: Page Reset Password
 */

get_header();
?>
	<div id="primary" class="content-area account-area">
		<main id="main" class="site-main">

		<div class="container">
			<div class="row">
				<?php
					if(have_posts()){
						the_post();
						?>
						<div class="col-sm-12">
							<?php
								the_content();
							?>
						</div>
					<?php
					}
				?>
			</div>
		</div>
		</main><!-- #main -->
	</div><!-- #primary -->
<?php
get_footer();