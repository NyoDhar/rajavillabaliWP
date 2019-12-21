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
	<div class="row">
		<div class="col-sm-4">
			<?php rajavillabali_post_thumbnail('blog-small-thumb', true); ?>
		</div>
		<div class="col-sm-8">
			<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
			<div class="entry-meta">
				<?php
				rajavillabali_posted_on();
				?>
			</div>
			<?php
			/* the_content( sprintf(
				wp_kses(
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'rajavillabali' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			) ); */
			the_excerpt();
			?>
		</div>
	</div>
</article>
