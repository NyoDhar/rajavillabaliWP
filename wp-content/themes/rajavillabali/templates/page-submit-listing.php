<?php
/**
Template Name: Page Submit Listing
 */

get_header();
?>
	<div id="primary" class="content-area account-area submit-listing">
		<main id="main" class="site-main">

		<div class="container">
			<div class="row">
				<?php
					if(have_posts()){
						the_post();
						?>
						<h1 class="text-center account-title"><?php the_title() ?></h1>
						
						<?php
							the_content();
						
					}
				?>
			</div>
		</div>
		</main><!-- #main -->
	</div><!-- #primary -->
<?php
get_footer();