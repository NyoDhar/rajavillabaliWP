<?php
/**
 * rajavillabali functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package rajavillabali
 */
 
 include_once('inc/property/metabox.php');
 include_once('inc/property/rvb-functions.php');

if ( ! function_exists( 'rajavillabali_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function rajavillabali_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on rajavillabali, use a find and replace
		 * to change 'rajavillabali' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'rajavillabali', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'rajavillabali' ),
			'menu-top' => esc_html__( 'Top', 'rajavillabali' ),
			'account-menu' => esc_html__( 'Account Menu', 'rajavillabali' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'rajavillabali_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
		
		add_image_size( 'blog-big-thumb', 1500, 612, true );
		add_image_size( 'blog-small-thumb', 364, 243, true );
		add_image_size( 'property-thumb', 555, 370, true );
	}
endif;
add_action( 'after_setup_theme', 'rajavillabali_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function rajavillabali_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'rajavillabali_content_width', 640 );
}
add_action( 'after_setup_theme', 'rajavillabali_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function rajavillabali_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'rajavillabali' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'rajavillabali' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'rajavillabali_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function rajavillabali_scripts() {
	wp_enqueue_style( 'boostrap-grid-only', get_template_directory_uri().'/assets/bootstrap-grid-only.css' );
	wp_enqueue_style('font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
	//wp_enqueue_style('source-san-pro', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400&display=swap');
	wp_enqueue_style('gfonts', 'https://fonts.googleapis.com/css?family=Noto+Sans|Source+Sans+Pro:600&display=swap');
	
	
	
	

	wp_enqueue_script( 'rajavillabali-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'rajavillabali-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );
	

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	
	if ( is_singular('mphb_room_type') || is_page('Submit Review') ) {
		wp_enqueue_style( 'magnific-style', get_stylesheet_directory_uri(). '/assets/magnific/style.css' );
		wp_enqueue_script( 'magnific-js', get_template_directory_uri() . '/assets/magnific/magnific.js', array('jquery'), null, true );
		
	}
	
	if(!wp_script_is('jquery-ui-datepicker')){
		wp_enqueue_script('jquery-ui-datepicker');
	}
	
	if(is_page('booking-confirmation') || is_page('my-bookings')){
		wp_enqueue_style( 'jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css', null, '1.0');
	}
	
	wp_enqueue_script( 'map-view-js', get_template_directory_uri() . '/js/map.js', array('jquery'), null, true );
	
	wp_enqueue_script( 'sticky-js', get_template_directory_uri() . '/js/jquery.sticky.js', array('jquery'), null, true );
	
	if(is_page( get_option('rvb_submit_listings_page')) || ( is_page( get_option('rvb_my_bookings_page')) && !empty($_GET['action']) && $_GET['action'] == 'cancel-booking') ){
		wp_enqueue_style( 'dropzone-style', get_template_directory_uri().'/assets/dropzone/style.css' );
		wp_enqueue_script('dropzone',get_stylesheet_directory_uri(). '/assets/dropzone/dropzone.js', array('jquery'));
		wp_enqueue_script('drag-drop-uploader', get_stylesheet_directory_uri().'/js/blk-drag-drop-uploader.js',array('jquery','dropzone'));
		$drop_param = array(
		  'upload'		=>admin_url( 'admin-ajax.php?action=handle_dropped_media' ),
		  'delete'		=>admin_url( 'admin-ajax.php?action=handle_deleted_media' ),
		  'remove_text'	=> __('Remove Photo', 'rajavillabali'),
		  'file_too_big'=> __('File size is larger than 2MB', 'rajavillabali'),
		  'obj'			=> false,
		);
		wp_localize_script('drag-drop-uploader','blkddu', $drop_param);
		
		if(!wp_script_is('accounting', 'enqueued')){
			wp_enqueue_script( 'accounting', get_template_directory_uri() . '/assets/accountingjs/accounting.min.js', array('jquery'), '1.0', true);
		}
	}
	
	wp_enqueue_style( 'rajavillabali-style', get_stylesheet_uri() );
	wp_enqueue_style( 'responsive-style', get_template_directory_uri().'/responsive.css' );
	wp_enqueue_script( 'rajavillabali-js', get_template_directory_uri() . '/js/custom.js', array('jquery'), null, true );
	
	if(is_page( get_option('rvb_submit_listings_page') )){
		$rvb_params = array(
						'fee'	=> get_option('rvb_company_fee'),
						'submitFormChanged'	=> false,
					);
		
		wp_localize_script('rajavillabali-js','rvbparams', $rvb_params);
		
		$review_params = array(
							'propertyDetail'	=> __('#bedrooms bedroom(s) for up to #guest guests, land size #land_size sqm', 'rajavillabali'),
							'photos'			=> __('#photos photos uploaded', 'rajavillabali'),
							'contact_phone'		=> __('Customer Support Phone', 'rajavillabali'),
							'booking_email'		=> __('New Booking Email Receiver', 'rajavillabali'),
							'contact_email'		=> __('Customer Support Email', 'rajavillabali'),
						);
		
		wp_localize_script('rajavillabali-js','reviewText', $review_params);
	}
	
	
}
add_action( 'wp_enqueue_scripts', 'rajavillabali_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}