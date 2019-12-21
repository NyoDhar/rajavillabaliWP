<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package rajavillabali
 */

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main page">
			
			<?php if ( have_posts() ) : ?>
			<?php
				$bg = '';
				$post_id = get_the_ID();
				
				if(has_post_thumbnail()){
					$src = get_the_post_thumbnail_url($post_id, 'blog-big-thumb');
					$bg = 'style="background-image: url('. esc_url( $src ).');"';
				}else{
					if(has_header_image()){
						$bg = 'style="background-image: url('. esc_url( get_header_image() ).');"';
					}
				}
			?>
			<header class="page-header entry-header bg big" <?php echo $bg; ?>>
				
				<div class="floating-bot">
					<div class="container">
						<?php
						the_title( '<h1 class="page-title entry-title">', '</h1>' );
						
						if(function_exists('bcn_display') && !is_front_page()){		
							?>
							<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
								<?php bcn_display(); ?>
							</div>
							
							<?php 
						}
						?>
					</div>
				</div>
			</header><!-- .page-header -->
			
			<article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
				<div class="container">
						<?php
							$date_end = get_post_meta($post_id, 'rvb_hd_date_end', true);
							if($date_end >= date('Y-m-d')){
								$properties = get_post_meta($post_id, 'rvb_hd_properties', true);
								if(!empty($properties)){
									echo do_shortcode('[mphb_rooms ids="'.implode(',', $properties).'"]');
								}else{
									echo 'There is no properties selected in the hot deal program';
								}
							}else{
								?>
								<div class="text-center">
									<h2><?php echo sprintf(__('%s program has ended', 'rajavillabali'), get_the_title()); ?></h2>
									<a class="button" href="<?php echo get_page_link(3235) ?>"><?php _e('See new hot deals', 'rajavillabali'); ?></a>
								</div>
								<?php
							}
						?>
				</div>
				
				<?php
				endif;
				?>
			</article>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
//get_sidebar();
get_footer();