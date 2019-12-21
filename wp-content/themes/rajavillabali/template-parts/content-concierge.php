<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package rajavillabali
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php rajavillabali_post_thumbnail('blog-big-thumb'); ?>
	<div class="container">
		<div class="entry-content">
			<header class="entry-header text-center">
				<?php
				if ( is_singular() ) :
					the_title( '<h1 class="entry-title">', '</h1>' );
				else :
					the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
				endif;

				if ( 'post' === get_post_type() ) :
					?>
					<div class="entry-meta">
						<?php
						rajavillabali_posted_on();
						?>
					</div><!-- .entry-meta -->
				<?php endif; ?>
				
			</header>
			
			<?php
			the_content( sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'rajavillabali' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			) );

			/* wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'rajavillabali' ),
				'after'  => '</div>',
			) ); */
			?>
			
			<!--<div class="share-post">
				<h3 class="text-center"><?php _e('Spread the love', 'rajavillabali') ?></h3>
				<?php echo do_shortcode('[Sassy_Social_Share]'); ?>
			</div>-->
			
			<div class="related-posts">
				<h2><?php _e('Other Services', 'rajavillabali'); ?></h2>
				<?php get_other_services(get_the_ID()); ?>
			</div>
		</div><!-- .entry-content -->
		
		
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
