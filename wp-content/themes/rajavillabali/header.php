<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package rajavillabali
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<!--<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'rajavillabali' ); ?></a>-->

	<header id="masthead" class="site-header rvb-sticky">
		<div class="top-bar">
			<div class="container">
				<div class="col-sm-6"></div>
				<div class="col-sm-6">
					<nav id="top-navigation" class="main-navigation">
						<?php
						wp_nav_menu( array(
							'theme_location' => 'menu-top',
							'menu_id'        => 'top-menu',
						) );
						?>
					</nav>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-lg-3">
					<div class="site-branding">
						<?php
						the_custom_logo();
						if ( is_front_page() && is_home() ) :
							?>
							<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
							<?php
						else :
							?>
							<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
							<?php
						endif;
						$rajavillabali_description = get_bloginfo( 'description', 'display' );
						if ( $rajavillabali_description || is_customize_preview() ) :
							?>
							<p class="site-description"><?php echo $rajavillabali_description; /* WPCS: xss ok. */ ?></p>
						<?php endif; ?>
					</div><!-- .site-branding -->
					<i class="fa fa-bars mobile-menu-open hidden-lg" aria-hidden="true"></i>
				</div>
				
				<div class="col-lg-9">
					<nav id="site-navigation" class="main-navigation">
						<!--<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'rajavillabali' ); ?></button> -->
						<div class="mobile-menu-wrapper">
							<div class="mobile-menu-header hidden-lg">
								<img src="<?php echo get_site_icon_url() ?>" class="mobile-logo">
								
								<span class="mobile-close-menu">&times;</span>
							</div>
							<?php
							wp_nav_menu( array(
								'theme_location' => 'menu-1',
								'menu_id'        => 'primary-menu',
							) );
							?>
						</div>
					</nav><!-- #site-navigation -->
				</div>
			</div>
		</div>
	</header><!-- #masthead -->

	<div id="content" class="site-content">
