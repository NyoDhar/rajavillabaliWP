<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package rajavillabali
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
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
	
	<div class="container">
		<?php rajavillabali_post_thumbnail(); ?>

		<div class="entry-content">
			<?php
			the_content();

			/* wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'rajavillabali' ),
				'after'  => '</div>',
			) ); */
			?>
		</div><!-- .entry-content -->
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
