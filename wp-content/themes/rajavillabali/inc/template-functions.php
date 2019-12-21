<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package rajavillabali
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function rajavillabali_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'rajavillabali_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function rajavillabali_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'rajavillabali_pingback_header' );

function blk_pagination($max_num_pages, $paged = null){
	 $navigation = '';
	 $big = 999999999; // need an unlikely integer
	 $current = !empty( $paged ) ? $paged : get_query_var('paged');
	  $args = wp_parse_args( $args, array(
			'mid_size'           => 2,
			'end_size'     		=> 2,
			'prev_text'          => __( 'Prev' ),
			'next_text'          => __( 'Next' ),
			'screen_reader_text' => __( 'Posts navigation' ),
			'base' => str_replace( $big, '%#%',  get_pagenum_link( $big, false ) ),
			'format' => '?page=%#%',
			'current' => max( 1,  $current),
			'total' => $max_num_pages
	) );

	// Make sure we get a string back. Plain is the next best thing.
	if ( isset( $args['type'] ) && 'array' == $args['type'] ) {
			$args['type'] = 'plain';
	}

	// Set up paginated links.
	$links = paginate_links( $args );
		

	if ( $links ) {
		$navigation = _navigation_markup( $links, 'pagination', $args['screen_reader_text'] );
	}
	
	echo $navigation;
}
