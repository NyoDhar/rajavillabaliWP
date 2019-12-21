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
	$class = '';
	$atts=$_GET['mphb_attributes'];
	//var_dump($atts);
	$term = '';
	if(!empty($atts['location'])){
		$tax_meta = get_option('tax_meta_'.$atts['location']);
		$term = get_term($atts['location'], 'mphb_ra_location');	
		if(!empty($tax_meta['image'])){
			$src = wp_get_attachment_image_src($tax_meta['image'], 'blog-big-thumb');
			$bg = 'style="background-image: url('. esc_url( $src[0] ).');"';
			$class='big';
		}
	}
	
	if(empty($bg)){
		if(has_header_image()){
			$bg = 'style="background-image: url('. esc_url( get_header_image() ).');"';
		}
	}
?>
	<header class="entry-header bg <?php echo $class; ?>" <?php echo $bg; ?>>
		<div class="floating-bot">
			<div class="container">
				<?php 
					if($term && !is_wp_error($term)){
						echo '<h1 class="entry-title">' . $term->name . '</h1>';
					}else{
						the_title( '<h1 class="entry-title">', '</h1>' );
					}
				?>
				<?php
				if(function_exists('bcn_display') && !is_front_page()){		
					?>
					<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
						<?php bcn_display(); ?>
					</div>
					
					<?php 
				} ?>
				
				<?php 
					if($term && !is_wp_error($term)){
						echo '<p>' . $term->description . '</p>';
					}
				?>
			</div>
		</div>
	</header><!-- .entry-header -->
	
	<div class="container-fluid">
		<?php //rajavillabali_post_thumbnail(); ?>
		<div class="row">
			<div class="col-lg-3">
				<?php echo do_shortcode('[property_filter]'); ?>
			</div>
			<div class="col-lg-9">
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
		</div>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
