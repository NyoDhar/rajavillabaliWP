<?php
/**
Template Name: Page Account Area
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
							<h1 class="text-center account-title"><?php the_title() ?></h1>
						</div>
						<div class="col-sm-3">
							<div class="account-menu">
								<?php
									wp_nav_menu( array(
										'theme_location' => 'account-menu',
										'menu_id'        => 'account-menu',
									) );
								?>
							</div>
						</div>
						<div class="col-sm-9">
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