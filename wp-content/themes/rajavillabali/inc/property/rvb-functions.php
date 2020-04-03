<?php
include_once('settings.php');
include_once('api-endpoints.php');
include_once('reports.php');
include_once('properties-map.php');
include_once('account-area/shortcodes.php');
include_once('rvb-cron-action.php');

//add_action('admin_head', 'hide_some_admin');
function hide_some_admin(){
	?>
	<style>
		.postbox-container #rooms,
		.postbox-container #reviews,
		.postbox-container #mphb_gallery,
		.postbox-container #mphb_other,
		.postbox-container #mphb_services,
		.postbox-container #heateor_sss_meta,
		#menu-posts-mphb_room_type ul.wp-submenu li:nth-child(10),
		#menu-posts-mphb_room_type ul.wp-submenu li:nth-child(11),
		#menu-posts-mphb_room_type ul.wp-submenu li:nth-child(12){
			display: none;
		}
	</style>
	<?php
}

add_action('wp_footer', 'rvb_ajax_vars');
function rvb_ajax_vars(){
	?>
	<script>
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>',
			marker_icon = '<?php echo get_template_directory_uri(); ?>/images/pinpoint.png';
	</script>
	<?php
}

add_action('admin_footer', 'rvb_admin_js_vars');
function rvb_admin_js_vars(){
	?>
	<script>
		var progressPath = "<?php echo get_template_directory_uri().'/progress.json'; ?>";
		console.log(progressPath);
	</script>
	<?php
}

/*******************************
Add Custom Post type
*******************************/
add_action( 'init', 'addCustomPostType' );
function addCustomPostType() {
	$theme_text_domain='insti'; /*** Remember to adjust **/
	
	/*** Define custom post type properties in array */
	$posts_type=array(
		'hot-deal' => array(
								'singular_name' => 'Hot Deal',
								'plural_name'	=> 'Hot Deals',
								'public'		=> true,
								'supports'		=> array( 'title', 'thumbnail' ), //Posible value: false (for no support), title, editor, author, thumbnail, excerpt, trackbacks, custom-fields, comments, revisions, page-attributes, post-formats
							),
		'cancel-policy'	=> array(
								'singular_name' => 'Cancellation Policy',
								'plural_name'	=> 'Cancellation Policies',
								'public'		=> false,
								'supports'		=> array( 'title', 'editor' ), //Posible value: false (for no support), title, editor, author, thumbnail, excerpt, trackbacks, custom-fields, comments, revisions, page-attributes, post-formats
							),
	);

	/*** Register custom post type in loop based on $posts_type value */
	foreach($posts_type as $key=>$val){
		$labels = array(
			'name'               => _x( $val['plural_name'], 'post type general name', $theme_text_domain ),
			'singular_name'      => _x( $val['singular_name'], 'post type singular name', $theme_text_domain ),
			'add_new'            => _x( 'Add New', $val['singular_name'], $theme_text_domain ),
			'add_new_item'       => __( 'Add New '.$val['singular_name'], $theme_text_domain ),
			'new_item'           => __( 'New '.$val['singular_name'], $theme_text_domain ),
			'edit_item'          => __( 'Edit '.$val['singular_name'], $theme_text_domain ),
			'view_item'          => __( 'View '.$val['singular_name'], $theme_text_domain ),
			'all_items'          => __( 'All '.$val['singular_name'], $theme_text_domain ),
			'search_items'       => __( 'Search '.$val['singular_name'], $theme_text_domain ),
			'parent_item_colon'  => __( 'Parent '.$val['singular_name'].':', $theme_text_domain ),
			'not_found'          => __( 'No '.$val['plural_name'].' found.', $theme_text_domain ),
			'not_found_in_trash' => __( 'No '.$val['plural_name'].' found in Trash.', $theme_text_domain )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => $val['public'],
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => $key ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'supports'           => $val['supports']
		);

		register_post_type( $key, $args );
	}
}

/*******************************
Register Taxonomy (Category)
*******************************/
add_action( 'init', 'rvb_addCategory', 0 );
function rvb_addCategory() {
	$cats=array(
			'house-rule' => array(
									'singular_name'	=> 'House Rule',
									'plural_name'	=> 'House Rules',
									'posts'			=> array('mphb_room_type')
								)
		);
	
	foreach ($cats as $taxonomy=>$cat){
		$labels = array(
			'name'              => _x( $cat['plural_name'], 'taxonomy general name' ),
			'singular_name'     => _x( $cat['singular_name'], 'taxonomy singular name' ),
			'search_items'      => __( 'Search '.$cat['plural_name'] ),
			'all_items'         => __( 'All '.$cat['plural_name'] ),
			'parent_item'       => __( 'Parent '.$cat['singular_name'] ),
			'parent_item_colon' => __( 'Parent '.$cat['singular_name'].':' ),
			'edit_item'         => __( 'Edit '.$cat['singular_name'] ),
			'update_item'       => __( 'Update '.$cat['singular_name'] ),
			'add_new_item'      => __( 'Add New '.$cat['singular_name'] ),
			'new_item_name'     => __( 'New '.$cat['singular_name'].' Name' ),
			'menu_name'         => __( $cat['singular_name'] ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => $taxonomy ),
		);

		register_taxonomy( $taxonomy, $cat['posts'], $args );
	}
}

add_action('mphb_sc_search_results_before_loop', 'add_villa_search_form', 10, 1);
add_action('mphb_sc_search_results_before_loop_empty', 'add_villa_search_form', 10, 1);
add_action('blk_before_archieve_property_loop', 'add_villa_search_form', 10, 1);
function add_villa_search_form($post_count){
	?>
		<div class="search-buttons-act">
			<?php
			$show = true;
			if(is_numeric($post_count) && $post_count==0){
				$show = false;
			}
			
			if($show){
				?>
				<a href="#" class="button open-filter hidden-lg"><i class="fa fa-filter" aria-hidden="true"></i> <?php _e('Filter', 'rajavillabali') ?></a>
				<a href="#" class="button open-map-view"><i class="fa fa-map-pin" aria-hidden="true"></i> <?php _e('View Map', 'rajavillabali') ?></a>
				<?php
			}
			?>
			<a href="#" class="button open-change-search" data-text="<?php _e('Change Search', 'rajavillabali'); ?>" data-cancel="<?php _e('Cancel Change', 'rajavillabali'); ?>">
				<i class="fa fa-search" aria-hidden="true"></i> <span><?php _e('Change Search', 'rajavillabali'); ?></span>
			</a>
		</div>
		<div class="change-search tmp-hide">
	<?php
			echo do_shortcode('[mphb_availability_search attributes="location"]');
	?>
		</div>
	<?php
}

add_action('mphb_sc_search_results_before_loop', 'add_property_list_wrapper_open');
add_action('mphb_sc_rooms_before_loop', 'add_property_list_wrapper_open');
function add_property_list_wrapper_open(){
	echo '<div class="rvb-properties row">';
}

add_action('mphb_sc_search_results_after_loop', 'add_property_list_wrapper_close');
add_action('mphb_sc_rooms_after_loop', 'add_property_list_wrapper_close');
function add_property_list_wrapper_close(){
	echo '</div>';
}

/* function custom_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function excerpt_readmore($more) {
    return '...';
}
add_filter('excerpt_more', 'excerpt_readmore'); */

add_action('wp_loaded', 'remove_hooks');
function remove_hooks(){
	remove_action('mphb_render_loop_room_type_before_featured_image', '\MPHB\Views\LoopRoomTypeView::_renderFeaturedImageParagraphOpen', 10);
	remove_action('mphb_render_loop_room_type_after_featured_image', '\MPHB\Views\LoopRoomTypeView::_renderFeaturedImageParagraphClose', 10);
	remove_action('mphb_render_single_room_type_metas', '\MPHB\Views\SingleRoomTypeView::renderDefaultOrForDatesPrice', 30);
	remove_action('mphb_render_single_room_type_metas', '\MPHB\Views\SingleRoomTypeView::renderReservationForm', 50);
	remove_action('mphb_render_single_room_type_metas', '\MPHB\Views\SingleRoomTypeView::renderAttributes', 20);
	remove_action('mphb_render_single_room_type_metas', '\MPHB\Views\SingleRoomTypeView::renderCalendar', 40);
	
	/*
		Chekout Page
	*/
	remove_action('mphb_sc_checkout_form', '\MPHB\Views\Shortcodes\CheckoutView::renderCustomerDetails', 40);
	//add_action('mphb_cb_checkout_room_details', '\MPHB\Views\Shortcodes\CheckoutView::renderCustomerDetails', 21);
	add_action( 'mphb_sc_checkout_room_details', array( '\MPHB\Views\Shortcodes\CheckoutView', 'renderCustomerDetails' ), 21 );

	//add_action('mphb_render_single_room_type_metas', '\MPHB\Views\SingleRoomTypeView::renderDefaultOrForDatesPrice', 10);
}

add_action('mphb_sc_checkout_form', 'included_services', 31);
function included_services(){
	echo '<i>Price are include onetime airport pickup.</i>';
}

add_action('mphb_render_loop_room_type_before_featured_image', 'property_thumb_open_tag');
function property_thumb_open_tag(){
	echo '<div class="post-thumbnail mphb-loop-room-thumbnail">';
}

add_action('mphb_render_loop_room_type_after_featured_image', 'property_thumb_open_close');
function property_thumb_open_close(){
	echo '</div>';
}

add_filter('get_the_archive_title', 'tax_title_filter');
function tax_title_filter($title){
	if(is_tax('mphb_ra_location')){
		//$tax = get_taxonomy( get_queried_object()->taxonomy );
		return single_term_title( '', false );
	}
	
	return $title;
}

function arphabet_widgets_init() {
	
	register_sidebar( array(
		'name'          => 'Footer Left',
		'id'            => 'footer_left',
		'before_widget' => '<div class="footer-widget footer-left">',
		'after_widget'  => '</div>',
		'before_title'  => '<span class="widget-title">',
		'after_title'   => '</span>',
	) );
	
	register_sidebar( array(
		'name'          => 'Footer Middle',
		'id'            => 'footer_middle',
		'before_widget' => '<div class="footer-widget footer-middle">',
		'after_widget'  => '</div>',
		'before_title'  => '<span class="widget-title">',
		'after_title'   => '</span>',
	) );
	
	register_sidebar( array(
		'name'          => 'Footer Right',
		'id'            => 'footer_right',
		'before_widget' => '<div class="footer-widget footer-right">',
		'after_widget'  => '</div>',
		'before_title'  => '<span class="widget-title">',
		'after_title'   => '</span>',
	) );

}
add_action( 'widgets_init', 'arphabet_widgets_init' );

add_filter( 'comments_open', 'rvb_comments_open', 10, 2 );
function rvb_comments_open( $open, $post_id ) {
	$post = get_post( $post_id );

	if ( 'mphb_room_type' == $post->post_type )
		$open = true;

	return $open;
}

function rvb_move_comment_field_to_bottom( $fields ) {
	$comment_field = $fields['comment'];
	unset( $fields['comment'] );
	$fields['comment'] = $comment_field;
	return $fields;
}
add_filter( 'comment_form_fields', 'rvb_move_comment_field_to_bottom');

// disable for posts
add_filter('use_block_editor_for_post', '__return_false', 10);

// disable for post types
add_filter('use_block_editor_for_post_type', '__return_false', 10);

add_shortcode('get_latest_blog', 'get_latest_blog');
function get_latest_blog($atts){
	
	$atts = shortcode_atts( array(
		'posts_per_page'	=> 3,
		'related_to'		=> '',
		'post_type'			=> 'post'
	), $atts, 'bartag' );
	
	$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;

	$args = array(
		'posts_per_page' => $atts['posts_per_page'],
		'post_type' => $atts['post_type'],
	);
	
	if(!empty($atts['related_to'])){
		$cats = wp_get_post_categories($atts['related_to']);
		if(!is_wp_error($cats)){
			$args['category__in'] = $cats;
			$args['post__not_in'] = array($atts['related_to']);
		}
	}

	$the_query = new WP_Query( $args );
	
	ob_start();
	?>
	<div class="blog-list">
		<?php
		if($the_query->have_posts()){
			while($the_query->have_posts()){
				$the_query->the_post();
				
				get_template_part( 'template-parts/content', 'relatedpost' );
			}
			
			wp_reset_postdata();
			
		}
		?>
	</div>
	<?php
	
	return ob_get_clean();
}

function get_other_services($exclude){
	$args = array(
				'post_type'			=> 'mphb_room_service',
				'posts_per_page'	=> 5,
				'post__not_in'		=> array($exclude)
			);
	
	$the_query = new WP_Query( $args );
	

	?>
	<div class="blog-list">
		<?php
		if($the_query->have_posts()){
			while($the_query->have_posts()){
				$the_query->the_post();
				
				get_template_part( 'template-parts/content', 'relatedpost' );
			}
			
			wp_reset_postdata();
			
		}
		?>
	</div>
	<?php
}

function modify_read_more_link() {
    return '<br><a class="more-link button" href="' . get_permalink() . '">'.__('Continue reading', 'rajavillabali').'</a>';
}
//add_filter( 'the_content_more_link', 'modify_read_more_link' );

function theme_slug_excerpt_length( $length ) {
        /* if ( is_admin() ) {
                return $length;
        } */
		global $post;
		
		if($post->post_type == 'post'){
			//return 65 is the current page is Magazine
			if(is_home()){
				return 65;
			}else{
				return 17;
			}
		}else{
			return 25;
		}
        
}
add_filter( 'excerpt_length', 'theme_slug_excerpt_length', 999 );

function wpdocs_excerpt_more( $more ) {
	global $post;
		
	if($post->post_type == 'post'){
		return sprintf( '...<br><a href="%1$s" class="more-link button">%2$s</a>',
			  esc_url( get_permalink( get_the_ID() ) ),
			  sprintf( __( 'Continue reading %s', 'rajavillabali' ), '<span class="screen-reader-text">' . get_the_title( get_the_ID() ) . '</span>' )
		);
	}else{
		return '...';
	}
}
add_filter( 'excerpt_more', 'wpdocs_excerpt_more' );


function rvb_comment($comment, $args, $depth) {
    if ( 'div' === $args['style'] ) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }?>
    <<?php echo $tag; ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID() ?>"><?php 
    if ( 'div' != $args['style'] ) { ?>
        <div id="div-comment-<?php comment_ID() ?>" class="comment-body"><?php
    } ?>
        <div class="comment-author vcard"><?php 
            if ( $args['avatar_size'] != 0 ) {
                echo get_avatar( $comment, $args['avatar_size'] ); 
            } 
            printf( __( '<cite class="fn">%s</cite>' ), get_comment_author_link() ); ?>
			<?php do_action('after_comment_author'); ?>
			<span class="comment-meta commentmetadata">
				<?php
					/* translators: 1: date, 2: time */
					printf( 
						__('%1$s at %2$s'), 
						get_comment_date(),  
						get_comment_time() 
					); ?>
				<?php 
				//edit_comment_link( __( '(Edit)' ), '  ', '' ); ?>
			</span>
        </div><?php 
        if ( $comment->comment_approved == '0' ) { ?>
            <em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></em><br/><?php 
        } ?>
        

        <?php comment_text(); ?>

        <!--<div class="reply"><?php 
                comment_reply_link( 
                    array_merge( 
                        $args, 
                        array( 
                            'add_below' => $add_below, 
                            'depth'     => $depth, 
                            'max_depth' => $args['max_depth'] 
                        ) 
                    ) 
                ); ?>
        </div>--><?php 
    if ( 'div' != $args['style'] ) : ?>
        </div><?php 
    endif;
}

add_action( 'mphb_ra_location_edit_form_fields', 'rvba_taxonomy_custom_fields', 10, 2 );
add_action( 'mphb_ra_location_add_form_fields', 'rvba_taxonomy_custom_fields', 10, 2 ); 
function rvba_taxonomy_custom_fields($tag) {  
   // Check for existing taxonomy meta for the term you're editing  
    $t_id = $tag->term_id; // Get the ID of the term you're editing  
    $term_meta = get_option( "tax_meta_$t_id" ); // Do the check  
	//var_dump($term_meta);
?>  
  
<tr class="form-field">  
    <th scope="row" valign="top">  
        <label for="tax_id"><?php _e('Image'); ?></label>  
    </th>  
    <td>  
        <?php echo do_shortcode('[ez_wp_media_uploader wrapper_id="rvb-tax-image" field_name="term_meta{image}" image_id="'.$term_meta['image'].'" multiple="false" is_array_field_name="true"]'); ?> 
    </td>  
</tr>  
  
<?php  
} 

add_action( 'edited_mphb_ra_location', 'save_taxonomy_custom_fields', 10, 2 ); 
add_action( 'created_mphb_ra_location', 'save_taxonomy_custom_fields', 10, 2 );  
function save_taxonomy_custom_fields( $term_id ) {  
    if ( isset( $_POST['term_meta'] ) ) {  
        $t_id = $term_id;  
        $term_meta = get_option( "tax_meta_$t_id" );  
        $cat_keys = array_keys( $_POST['term_meta'] );  
            foreach ( $cat_keys as $key ){  
            if ( isset( $_POST['term_meta'][$key] ) ){  
                $term_meta[$key] = $_POST['term_meta'][$key];  
            }  
        }  
        //save the option array  
        update_option( "tax_meta_$t_id", $term_meta );  
    }  
}

add_action('wp_footer', 'floating_whatsapp');
function floating_whatsapp(){
	$wa_number = get_option('wa_number');
	if(!empty($wa_number)){
	?>
		<a class="floating-wa" target="_blank" href="https://api.whatsapp.com/send?phone=<?php echo $wa_number; ?>"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>
	<?php
	}
}

add_shortcode('property_filter', 'property_filter');
function property_filter($atts){
	$atts = shortcode_atts( array(
		'id' => '',
	), $atts, 'bartag' );
	
	//$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	ob_start();
	
	$attributes = !empty($_COOKIE['mphb_attributes']) ? json_decode( stripcslashes( $_COOKIE['mphb_attributes'] ), true ) : array();
	
	$filters = get_posts(array(
				'post_type'			=> 'mphb_room_attribute',
				'posts_per_page'	=> -1,
				'meta_query'		=> array(
										array(
											'key'	=> 'mphb_filter',
											'value'	=> '1'
										)
									)
			));
	
	if(empty($filters)) return '';
	
	
	
	
	//var_dump($_GET);
	
	$today = new DateTime();
	
	$check_in = !empty($_GET['mphb_check_in_date']) ? $_GET['mphb_check_in_date'] : $today->format('Y-m-d');
	$today->add(new DateInterval('P1D'));
	$check_out = !empty($_GET['mphb_check_out_date']) ? $_GET['mphb_check_out_date'] : $today->format('Y-m-d');
	
	if(is_tax('mphb_ra_location')){
		$location = get_queried_object()->term_id;
	}else{
		$location = !empty($attributes) ? $attributes['location'] : '';//$_GET['mphb_attributes']['location'];
	}
	
	?>
	<div class="property-filter">
		<span class="h2"><i class="fa fa-filter" aria-hidden="true"></i> <?php _e('Filters', 'rajavillabali') ?> <span class="close-filter hidden-lg">&times;</span></span>
		<form action="<?php echo get_page_link(2786); ?>">
			<input type="hidden" name="mphb_check_in_date" value="<?php echo $check_in; ?>">
			<input type="hidden" name="mphb_check_out_date" value="<?php echo $check_out; ?>">
			<input type="hidden" name="mphb_adults" value="<?php echo !empty( $_GET['mphb_adults'] ) ? $_GET['mphb_adults'] : 1; ?>">
			<input type="hidden" name="mphb_children" value="<?php echo !empty( $_GET['mphb_children'] ) ? $_GET['mphb_children'] : 0; ?>">
			
			<?php
				if(is_array($location)){
					$i = 0;
					foreach($location as $loc){
						?>
						<input type="hidden" name="mphb_attributes[location][<?php echo $i; ?>]" value="<?php echo $loc; ?>">
						<?php
						$i++;
					}
				}else{
					?>
					<input type="hidden" name="mphb_attributes[location]" value="<?php echo $location; ?>">
					<?php
				}
			?>
			
			
			<?php
				foreach($filters as $filter){
					$tax = 'mphb_ra_'.$filter->post_name;
					$inclusions = get_terms(array(
							'taxonomy' => $tax,
						));
						
					if(!empty($inclusions)){
						$inclusion_selected = !empty($attributes[$tax]) ? $attributes[$tax] : array(); //!empty($_GET['mphb_attributes']['inclusion']) ? $_GET['mphb_attributes']['inclusion'] : array();
						?>
						<div class="filter-section">
							<span class="filter-title"><?php echo apply_filters('the_title', $filter->post_title ); ?></span>
							<ul class="filter-list">
								<?php
								foreach($inclusions as $inc){
									?>
									<li>
										<input id="inclusion-<?php echo $inc->term_id; echo $atts['id'];?>" type="checkbox" name="mphb_attributes[<?php echo $tax; ?>][]" value="<?php echo $inc->term_id ?>" <?php echo in_array( $inc->term_id, $inclusion_selected ) ? 'checked' : ''; ?> >
										<label for="inclusion-<?php echo $inc->term_id; echo $atts['id']; ?>"><?php echo $inc->name ?></label>
									</li>
									<?php
								}
								?>
							</ul>
						</div>	
						<?php
					}
				}
			?>
			
			<input type="submit" value="Filter" name="filter">
		</form>
	</div>
	<?php
	return ob_get_clean();
}

add_action('wp_footer', 'insert_inqyiry_form');
function insert_inqyiry_form(){
	if(is_singular('mphb_room_type')){
		global $post;
		?>
		<div id="inquiry-form" class="submit-property tmp-hide">
			<?php
				echo do_shortcode('[contact-form-7 id="3184" title="Inquiry Form"]');
			?>
		</div>
		<script>
			var inqVillaName = '<?php echo $post->post_title; ?>',
				inqVillaLink = '<?php echo get_permalink($post->ID); ?>';
		</script>
		<?php
	}
}

//email will be sent when customer send inquiry using the inquiry form
add_action('wp_ajax_inquiry_thankyou_email', 'inquiry_thankyou_email');
add_action('wp_ajax_nopriv_inquiry_thankyou_email', 'inquiry_thankyou_email');
function inquiry_thankyou_email(){
	$to = sanitize_email($_POST['to']);
	$name = sanitize_text_field($_POST['name']);
	$phone = sanitize_text_field( $_POST['phone'] );
	$msg = sanitize_textarea_field( $_POST['message'] );
	$villa_name	= sanitize_text_field( $_POST['villa-name'] );
	$villa_link	= sanitize_text_field( $_POST['villa-link'] );
	$check_in	= sanitize_text_field( $_POST['check-in'] );
	$check_out	= sanitize_text_field( $_POST['check-out'] );
	
	$subject = __('Thanks for your inquiry - Raja Villa Bali', 'rajavillabali');
	$message = '<p>'. sprintf( __('Thank you %s, <br> we have received your inquiry, we will get back to you as soon as possible', 'rajavillabali') , $name). '</p>';
	
	$message .= "<p><b>". __('Your inquiry details', 'rajavillabali'). "</b></p>
						<b>From</b>: {$name} ( {$to} )<br>
						<b>Phone</b>: {$phone}<br>
						<br>
						<b>Villa</b>: <a href='{$villa_link}'>{$villa_name}</a><br>
						<b>Check-in</b>: {$check_in}<br>
						<b>Check-out</b>: {$check_out}<br>
						<br>
						<b>Message</b>:<br>
						{$msg}";
	
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';

	// Additional headers
	//$headers[] = 'To: Mary <mary@example.com>, Kelly <kelly@example.com>';
	$headers[] = 'From: Raja Villa Bali <info@rajavillabali.com>';

	// Mail it
	$status = wp_mail($to, $subject, $message, $headers);
	
	if($status){
		echo 'Terkirim';
	}else{
		echo 'Tidak Terkirim';
	}
	
	wp_die();
}

add_action('wp_ajax_find_accomodation', 'find_accomodation');
function find_accomodation(){
	$search = $_GET['q'];
	
	$accomodations = get_posts(array(
				'post_type'		=> 'mphb_room_type',
				's'				=> $search, 
			));
	
	$items = array();
	foreach($accomodations as $g){
		$items[] = array(
						'value' => $g->post_title,
						'id'	=> $g->ID,
					);
	}

	header('Content-type: application/x-javascript');
	echo $_GET['callback']."(".json_encode($items).")";
	
	wp_die();
}

add_action('wp_ajax_find_hd_accomodation', 'find_hd_accomodation');
function find_hd_accomodation(){
	$search = $_GET['q'];
	
	$hds = get_posts(array(
				'post_type'			=> 'hot-deal',
				'posts_per_page'	=> -1,
				'post_status'		=> 'any',
				'meta_query'		=> array(
										array(
											'key'		=> 'rvb_hd_date_end',
											'compare'	=> '>=',
											'type'		=> 'DATE',
											'value'		=> date('Y-m-d'),
										)
									)
			));
	
	
	$args = array(
				'post_type'		=> 'mphb_room_type',
				's'				=> $search, 
			);
			
	if(!empty( $hds )){
		$exclude_properties = array();
		
		foreach($hds as $hd){
			$properties = get_post_meta($hd->ID, 'rvb_hd_properties', true);
			if(!empty($properties)){
				$exclude_properties = array_merge($exclude_properties, $properties);
			}
		}
		
		if(!empty($exclude_properties)){
			$args['post__not_in'] = $exclude_properties;
		}
	}
	
	$accomodations = get_posts($args);
	
	$items = array();
	foreach($accomodations as $g){
		$items[] = array(
						'value' => $g->post_title,
						'id'	=> $g->ID,
					);
	}

	header('Content-type: application/x-javascript');
	echo $_GET['callback']."(".json_encode($items).")";
	
	wp_die();
}

//Send Booking link to customer by admin
add_action('wp_ajax_send_booking_link', 'send_booking_link');
function send_booking_link(){
	
	$c_email = sanitize_email($_POST['c_email']);
	$c_name = sanitize_text_field($_POST['c_name']);
	$check_in = sanitize_text_field($_POST['check-in']);
	$check_out = sanitize_text_field($_POST['check-out']);
	$accomodation_id = sanitize_text_field($_POST['accomodation_id']);
	$villa_name = get_the_title($accomodation_id);
	
	$email_template = file_get_contents(get_template_directory().'/email-templates/default.html');
	$subject = sprintf( __('Book %1$s now - %2$s'), $villa_name, get_bloginfo('name'));
	
	$email_content = "<p>Hi {$c_name},<br><br> Your dates selection are available, you can continue to book the accomodation using the button below.</p>";
	$email_content .= "<p><b style='font-size: 1.2em;
							margin-bottom: 10px;
							display: inline-block;
							margin-top: 30px;'>Booking Details</b><br>
							<b>Accommodation</b>: <a href='".get_permalink($accomodation_id)."'>{$villa_name}</a><br>
							<b>Check-in</b>: {$check_in}<br>
							<b>Check-out</b>: {$check_out}
						</p>
						<a style='display: inline-block; padding: 12px 24px; background: #31adad; font-size: 1.2em;color: #fafafa;
								text-decoration: none;' href='".get_page_link(3187)."?vid={$accomodation_id}&ciid={$check_in}&coid={$check_out}'>Book Now</a>";
	$email_title = sprintf( __('%s is available', 'rajavillabali'), $villa_name);
	
	$search = array('{email_title}', '{email_content}');
	$replace = array($email_title, $email_content);
	$email = str_replace($search, $replace, $email_template);
	
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';

	// Additional headers
	//$headers[] = 'To: Mary <mary@example.com>, Kelly <kelly@example.com>';
	$headers[] = 'From: Raja Villa Bali <info@rajavillabali.com>';

	// Mail it
	$status = wp_mail($c_email, $subject, $email, $headers);
	
	if($status){
		echo '<div class="notice notice-success is-dismissible">
				<p>Booking link has been sent successfully</p>
			</div>';
	}else{
		echo '<div class="notice notice-warning is-dismissible">
				<p>Failed to send booking link, please try again later or contact you administrator.</p>
			</div>';
	}
	
	wp_die();
}

function rvb_get_property_contact($rooms){
	//return get_post_meta();
	//foreach($rooms as $room){
	$accomodation_id = $rooms[0]->getRoomTypeId();
	$phone = get_post_meta($accomodation_id, 'rvb_property_contact_phone', true);
	$email = get_post_meta($accomodation_id, 'rvb_property_contact_email', true);
	$return = $phone .'<br>'.$email;
	//}
	return $return; // print_r($rooms, true);
}

add_filter('mphb_email_booking_tags', 'rvb_add_mphb_email_tags', 10, 1);
function rvb_add_mphb_email_tags($bookingTags){
	$bookingTags[] = array(
				'name'			 => 'property_contact',
				'description'	 => __( 'Show property contact', 'motopress-hotel-booking' ),
			);
	exit();
	return $bookingTags;
}

add_action('mphb_booking_confirmed_with_payment', 'send_booking_confirmed_to_property_owner', 10, 1);
//add_action('wp_loaded', 'send_booking_confirmed_to_property_owner');
function send_booking_confirmed_to_property_owner($booking){
	//if(empty($_GET['test_mail'])) return;
		
	/* $booking = MPHB()->getBookingRepository()->findById( 3215 );

	if ( !$booking ) {
		echo 'booking sing ade';
		exit();
	} */
		
	$email_template = file_get_contents(get_template_directory().'/email-templates/default.html');
	//$subject = __('New Booking - Raja Villa Bali', 'rajavillabali');
	$subject = sprintf( __('New Booking - %s', 'rajavillabali'), get_bloginfo('name') );
	
	$email_content = "<p>".sprintf( __('Congratulation, you have new booking from %s', 'rajavillabali'), get_bloginfo('name') )."</p>";
	ob_start();
	\MPHB\Views\BookingView::renderCheckInDateWPFormatted( $booking );
	$check_in = ob_get_clean();
	
	ob_start();
	\MPHB\Views\BookingView::renderCheckOutDateWPFormatted( $booking );
	$check_out = ob_get_clean();
	
	$email_content .= "<h4>Details of booking</h4>
						Booking ID: #{$booking->getId()}<br>
						Check-in: {$check_in}<br>
						Check-out: {$check_out}<br>";
	
	$reservedRooms	 = $booking->getReservedRooms();
	
	//Get total additional services price then deduct it from total price
	$reservedServices = $reservedRooms[0]->getReservedServices();
	$total_service_price = 0;
	
	foreach ( $reservedServices as $reservedService ) {
		/* $reservedService = apply_filters( '_mphb_translate_reserved_service', $reservedService );
		$id = $reservedService->getOriginalId();
		$service_label = get_post_meta($id, 'service_label', true); */
		
		$total_service_price += $reservedService->calcPrice($booking->getCheckInDate(), $booking->getCheckOutDate());
	}
	
	
	$total_price = $booking->getTotalPrice() - $total_service_price;
	
	$fee_percentage = get_option('rvb_company_fee');
	$fee = $total_price * $fee_percentage / 100;
	$potential_earn = $total_price - $fee;
	
	
	$accomodation_id = $reservedRooms[0]->getRoomTypeId();
	$roomType	 = MPHB()->getRoomTypeRepository()->findById( $accomodation_id );
					//$roomType	 = apply_filters( '_mphb_translate_room_type', $roomType, $this->booking->getLanguage() );
					//$replaceText = ( $roomType ) ? $roomType->getTitle() : '';
	$email_content .= "<h4>Accommodation</h4>
						Guest: {$reservedRooms[0]->getAdults()}<br>
						Accommodation: {$roomType->getTitle()}<br>";
	
	$email_content .= "<h4>Customer Info</h4>
						Name: ".$booking->getCustomer()->getFirstName()." ".$booking->getCustomer()->getLastName()."<br>
						Email: ".$booking->getCustomer()->getEmail()."<br>
						Phone: ".$booking->getCustomer()->getPhone()."<br>
						Note: <br>".$booking->getNote();
	
	$email_content .= "<h4>Revenue</h4>
						Gross Revenue: ".mphb_format_price( $total_price )."<br>
						Fee: ".mphb_format_price( $fee )."<br>
						Potential Earning: ". mphb_format_price( $potential_earn );
	
	$email_title = __('New Booking', 'rajavillabali');
	
	$search = array('{email_title}', '{email_content}');
	$replace = array($email_title, $email_content);
	$email = str_replace($search, $replace, $email_template);
	
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';
	
	$villa_owner_email = get_post_meta($accomodation_id, 'rvb_property_contact_new_booking_email', true);
	// Additional headers
	//$headers[] = 'To: Mary <mary@example.com>, Kelly <kelly@example.com>';
	$headers[] = 'From: Raja Villa Bali <info@rajavillabali.com>';
	
	
	// Mail it
	$status = $status = wp_mail($villa_owner_email, $subject, $email, $headers);
}

add_action('mphb_booking_confirmed_with_payment', 'accociate_booking_with_user');
function accociate_booking_with_user($booking){
	$user = rvb_get_current_user();
	
	if($user === false){
		$customer = $booking->getCustomer();
		$user_email = $customer->getEmail();
		
		$user = get_user_by('email', $user_email);
		
		if(!$user){
			$pass = wp_generate_password();
			$new_user_id = wp_create_user($customer->getFirstName(), $pass, $user_email );
			$user = new WP_User($new_user_id);
			$user->set_role('common_user');
			update_user_meta( $new_user_id, 'rvb_user_pass', $pass );
		}
	}
	
	update_post_meta( $booking->getId(), 'rvb_user_id', $user->ID );
}

add_action('wp_loaded', 'check_things');
function check_things(){
	if(!empty($_GET['check_comment'])){
		rvb_add_new_review();
		exit();
	}
	
	if(!empty($_GET['check_hd'])){
		$hds = get_posts(array(
				'post_type'			=> 'hot-deal',
				'posts_per_page'	=> -1,
				'post_status'		=> 'any',
				'meta_query'		=> array(
										'key'		=> 'rvb_hd_date_end',
										'compare'	=> '>',
										'type'		=> 'DATE',
										'value'		=> date('Y-m-d'),
									)
			));
	
			var_dump($hds);
			
			$args = array(
						'post_type'		=> 'mphb_room_type',
						's'				=> $search, 
					);
					
			if(!empty( $hds )){
				$exclude_properties = array();
				
				foreach($hds as $hd){
					$properties = get_post_meta($hd->ID, 'rvb_hd_properties', true);
					var_dump($properties);
					if(!empty($properties)){
						$exclude_properties = array_merge($exclude_properties, $properties);
					}
				}
				
				if(!empty($exclude_properties)){
					$args['post__not_in'] = $exclude_properties;
				}
				
			}
		
			
		exit();
	}
	
	if(!empty($_GET['check_emails'])){
		$emails = get_email_blast_receivers();
		var_dump($emails);
		exit();
	}
	
	if(!empty($_GET['check_review_email'])){
		$status = send_asking_review_email(3237);
		var_dump($status);
		exit();
	}
	
	if(!empty($_GET['check_send_review_email'])){
		send_email_review_request();
		exit();
	}
	
	if(!empty($_GET['check_in_reminder_email'])){
		check_in_reminder_email();
		exit();
	}
	
	if(!empty($_GET['check_performance'])){
		get_rental_occupancy(2813, '2019-08-01', '2019-08-31');
		exit();
	}
	
	if(!empty($_GET['check_popular_area'])){
		get_popular_area_data(array());
		exit();
	}
	
	if(!empty($_GET['check_popular_villa'])){
		get_popular_property_data(array());
		exit();
	}
	
	if(!empty($_GET['check_achievment'])){
		get_sales_achievement_graph_data(array());
		exit();
	}
	
	
	if(!empty($_GET['check_prepare_map_data'])){
		prepare_map_data();
		exit();
	}
	
	if(!empty($_GET['check_price'])){
		echo rvb_getDefaultOrForDatesPrice(2792);
	}
	
	if(!empty($_GET['check_term'])){
		$args = array(
					'taxonomy'		=> 'mphb_ra_location',
					'hide_empty'	=> false,
					'hierarchical'	=> true,
					'name'			=> 'mphb_attributes[location]',
					'id'			=> 'mphb_location-mphb-search-form-5d9301059f322',
				);
		//$terms = get_terms($args);
		wp_dropdown_categories($args);
		//var_dump($terms);
		
		exit();
	}
	
	if(!empty($_GET['check_ratep'])){
		$id = get_property_rate(3395);
		var_dump($id);
		
		exit();
	}
	
	if(!empty($_GET['get_currency_rate'])){
		echo get_currency_rate('USD', 'IDR', 320);
		
		exit();
	}
	
	if(!empty($_GET['check_service'])){
		
		$booking = MPHB()->getBookingRepository()->findById( 3590 );
		
		send_booking_confirmed_to_property_owner($booking);
		
		$reservedRooms	 = $booking->getReservedRooms();
		$reservedServices = $reservedRooms[0]->getReservedServices();
		$total_service_price = 0;
		
		foreach ( $reservedServices as $reservedService ) {
			/* $reservedService = apply_filters( '_mphb_translate_reserved_service', $reservedService );
			$id = $reservedService->getOriginalId();
			$service_label = get_post_meta($id, 'service_label', true); */
			
			$total_service_price += $reservedService->calcPrice($booking->getCheckInDate(), $booking->getCheckOutDate());
		}
		
		$total_price = $booking->getTotalPrice() - $total_service_price;
	
		$fee_percentage = get_option('rvb_company_fee');
		$fee = $total_price * $fee_percentage / 100;
		$potential_earn = $total_price - $fee;
		
		echo $total_price .'<br>';
		echo $fee .'<br>';
		echo $potential_earn .'<br>';
	
		echo $total_service_price;
		exit();
	}
	
	if(!empty($_GET['update_season'])){
		rvb_update_seasons();
		exit();
	}
	
	if(!empty($_GET['check_c'])){
		test_get_c();
		
		exit();
	}
	
}

add_shortcode('get_hot_deals', 'get_hot_deals');
function get_hot_deals(){
	$hds = get_posts(array(
				'post_type'			=> 'hot-deal',
				'posts_per_page'	=> -1,
				'meta_query'		=> array(
										array(
											'key'		=> 'rvb_hd_date_end',
											'compare'	=> '>=',
											'type'		=> 'DATE',
											'value'		=> date('Y-m-d'),
										)
									)
			));
	
	if(!empty($hds)){
		ob_start();
		if(count($hds) > 1){
			?>
			<div class="hot-deals-list">
				<div class="row">
					<?php
					foreach($hds as $hd){
						?>
						<div class="col-sm-6">
							<a class="post-thumbnail" href="<?php echo get_the_permalink($hd->ID); ?>" aria-hidden="true" tabindex="-1">
								<?php
									echo get_the_post_thumbnail($hd->ID, 'property-thumb');
								?>
							</a>
						</div>
						<?php
					}
				?>
				</div>
			</div>
			<?php
		}else{
			$properties = get_post_meta($hds[0]->ID, 'rvb_hd_properties', true);
			if(!empty($properties)){
				echo do_shortcode('[mphb_rooms ids="'.implode(',', $properties).'"]');
			}else{
				echo 'There is no properties selected in the hot deal program';
			}
		}
		return ob_get_clean();
	}
	
	return 'There is no hot deal program at the moment';
}

function get_hot_deal_discount($accomodation_id = null){
	if(empty($accomodation_id)){
		global $post;
		$accomodation_id = $post->ID;
	}
	
	
	$hot_deal_id = get_post_meta($accomodation_id, 'hot_deal', true);
	//var_dump($hot_deal_id);
	//Check if hot deal program still valid, not expired
	$valid_hd = get_posts(array(
				'post_type'			=> 'hot-deal',
				'posts_per_page'	=> 1,
				'p'					=> $hot_deal_id,
				'meta_query'		=> array(
										array(
											'key'		=> 'rvb_hd_date_end',
											'compare'	=> '>=',
											'type'		=> 'DATE',
											'value'		=> date('Y-m-d'),
										)
									)
			));
	//var_dump($valid_hd);
	if(!empty($valid_hd)){
		$properties = get_post_meta( $valid_hd[0]->ID, 'rvb_hd_properties', true );
		if(in_array($accomodation_id, $properties)){
			$discount = get_post_meta($valid_hd[0]->ID, 'rvb_hd_date_discount', true);
			return $discount;
		}
	}
	
	return false;
}

function rvb_send_email($to, $subject, $email_title, $email_content){
	
	$email_template = file_get_contents(get_template_directory().'/email-templates/default.html');
	$search = array('{email_title}', '{email_content}');
	$replace = array($email_title, $email_content);
	$email = str_replace($search, $replace, $email_template);
	
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';
	// Additional headers
	//$headers[] = 'To: Mary <mary@example.com>, Kelly <kelly@example.com>';
	$headers[] = 'From: Raja Villa Bali <info@rajavillabali.com>';
	
	
	// Mail it
	$status = wp_mail($to, $subject, $email, $headers);
	
	return $status;
}

function get_email_blast_receivers(){
	global $wpdb;
	$sql = "select e.meta_value as email from ".$wpdb->posts." b 
				INNER JOIN ".$wpdb->postmeta." e ON ( e.post_id = b.ID AND e.meta_key='mphb_email' )
			WHERE b.post_type='mphb_booking' AND b.post_status='confirmed' AND e.meta_value <> ''
			GROUP BY e.meta_value";
	
	$emails = $wpdb->get_results($sql, 'ARRAY_A' );
	
	return array_values($emails);
}

add_action('wp_ajax_send_email_blast_ajax', 'rvb_send_email_blast');
function rvb_send_email_blast(){
	
	set_time_limit(0);
	
	
	$template_path = get_template_directory();
	$emails = get_email_blast_receivers();
	
	$email_title = $_POST['email_title'];
	$email_text	= $_POST['email_text'];
	$subject	= $_POST['subject'];
	$hot_deals	= $_POST['hot_deals'];
	$test_email	= $_POST['test_email'];
	
	$separator = '<p style="text-align:center; margin-bottom: 60px;"><span style="border-top-style: dotted;
								border-top-width: 5px;
								border-top-color: #d7ad3f;
								width: 10%;
								display: inline-block;">
				</span></p>';
	$email_content = 'masuk email';
	if(!empty($email_text)){
		$email_content = '<p style="text-align:center;">'.$email_text.'</p>' . $separator;
	}
	
	
	foreach($hot_deals as $hd){
		$link = get_permalink($hd);
		$img_url = get_the_post_thumbnail_url($hd, 'blog-small-thumb');
		$email_content .= '<p align="center" style="margin-bottom: 60px;"><a href="'.$link.'"><img src="'.$img_url.'"></a><br>
								<a style="display: inline-block; padding: 12px 24px; background: #31adad; font-size: 1.2em;color: #fafafa;
								text-decoration: none;" href="'.$link.'">See Offer</a>
							</p>';
	}
	
	$email_result = array();
	
	if(empty($test_email)){
		$total_email = count($emails);
		$progress_step = 100 / $total_email;
		$progress = 0;
		
		foreach($emails as $email){
			
			$email_result[$email] = rvb_send_email($email, $subject, $email_title, $email_content);
			
			$progress += $progress_step;
			file_put_contents($template_path . '/progress.json', json_encode(array('progress'=> round($progress))));
		}
	}else{
		//send test email
		$email_title .= ' - Test';
		$subject .= '- Test';
		$email_result[$test_email] = rvb_send_email($test_email, $subject, $email_title, $email_content);
		file_put_contents($template_path . '/progress.json', json_encode(array('progress'=> round(100))));
	}
	
	print_r($email_result);
	echo $email_content;
	wp_die();
}

add_action('wp_ajax_reset_progress', 'reset_progress');
function reset_progress(){
	file_put_contents(get_template_directory() . '/progress.json', json_encode(array('progress'=>0)));
	
	wp_die();
}

//add Review/Comment using ajax
add_action('wp_ajax_nopriv_rvb_add_new_review', 'rvb_add_new_review');
add_action('wp_ajax_rvb_add_new_review', 'rvb_add_new_review');
function rvb_add_new_review(){
	//global $post, $current_user; //for this example only :)

	$commentdata = array(
		'comment_post_ID' => $_POST['accomodation_id'], // to which post the comment will show up
		'comment_author' => $_POST['customer_name'], //fixed value - can be dynamic 
		'comment_author_email' => $_POST['email'], //fixed value - can be dynamic 
		'comment_author_url' => '', //fixed value - can be dynamic 
		'comment_content' => $_POST['comment'], //fixed value - can be dynamic 
		'comment_type' => '', //empty for regular comments, 'pingback' for pingbacks, 'trackback' for trackbacks
		'comment_parent' => 0, //0 if it's not a reply to another comment; if it's a reply, mention the parent comment ID here
		//'user_id' => $current_user->ID, //passing current user ID or any predefined as per the demand
	);

	//Insert new comment and get the comment ID
	$comment_id = wp_new_comment( $commentdata, true );
	
	if(!is_wp_error($comment_id)){
		$rating = intval( $_POST['rating'] );
		add_comment_meta( $comment_id, 'rating', $rating );
		
		echo '<div class="notice success">'.__('Thank You, your review has been sent successfully.', 'rajavillabali').'</div>';
	}else{
		echo '<div class="notice error">'.__('We apologize, your review was not sent successfully, please try again later.', 'rajavillabali').'</div>';
	}
	
	//var_dump($comment_id);
	
	wp_die();
}

add_shortcode('get_cancellation_policies','get_cancellation_policies');
function get_cancellation_policies($atts){
	/* $atts = shortcode_atts( array(
		'posts_per_page'	=> 3,
		'related_to'		=> '',
		'post_type'			=> 'post'
	), $atts, 'bartag' ); */
	
	$policies = get_posts(array(
							'post_type'			=> 'cancel-policy',
							'posts_per_page'	=> -1,
						));
	
	ob_start();
	if(!empty($policies)){
		foreach($policies as $p){
			?>
			<div class="cancel-policy list">
				<h2 id="#cp-<?php echo $p->post_name; ?>"><?php echo apply_filters('the_title', $p->post_title); ?></h2>
				<?php
					echo apply_filters('the_content', $p->post_content );
				?>
			</div>
			<?php
			
		}
	}
	
	return ob_get_clean();
}

add_filter( 'wp_nav_menu_items', 'add_logout_link', 10, 2);

/**
 * Add a login link to the members navigation
 */
function add_logout_link( $items, $args )
{
	$is_rvb_user_logged_in = rvb_is_user_logged_in();
	$is_homeowner_logged_in = rvb_is_user_logged_in('homeowner');
		
    if($args->theme_location == 'menu-top')
    {
		
        if($is_rvb_user_logged_in || $is_homeowner_logged_in)
        {
			//$my_booking_page = get_option('rvb_my_booking_page');
            $items .= '<li>
							<a href="'.get_page_link( get_option('rvb_my_account_page') ).'">'.__('My Account', 'rajavillabali').'</a>
							<ul class="sub-menu">';
							if($is_rvb_user_logged_in){
								$items .= '<li><a href="'.get_page_link( get_option('rvb_my_booking_page') ).'">'.__('My Bookings', 'rajavillabali').'</a></li>';
							}
							if($is_homeowner_logged_in){
								$items .= '<li><a href="'.get_page_link( get_option('rvb_my_listings_page') ).'">'.__('Listings', 'rajavillabali').'</a></li>';
							}
								$items .= '<li><a href="#" id="logout">'.__('Logout', 'rajavillabali').'</a></li>
							</ul>
						</li>';
        } else {
            $items .= '<li><a href="#" id="open-login-register">'.__('Log In / Register', 'rajavillabali').'</a></li>';
        }
    }
	
	if($args->theme_location == 'account-menu')
    {
		if($is_rvb_user_logged_in){
			$items .= '<li><a href="'.get_page_link( get_option('rvb_my_booking_page') ).'">'.__('My Bookings', 'rajavillabali').'</a></li>';
		}
		
		if($is_homeowner_logged_in){
			$items .= '<li><a href="'.get_page_link( get_option('rvb_my_listings_page') ).'">'.__('Listings', 'rajavillabali').'</a></li>
			<li><a href="'.get_page_link( get_option('rvb_owner_income_page') ).'">'.__('Income', 'rajavillabali').'</a></li>';
		}
					
	}

    return $items;
}

//Remove menu item on user account area if the user are not authorized to access it
//add_filter('wp_nav_menu_objects', 'rvb_filter_menu', 10, 2);
function rvb_filter_menu($sorted_menu_objects, $args) {

    // check for the right menu to remove the menu item from
    // here we check for theme location of 'secondary-menu'
    // alternatively you can check for menu name ($args->menu == 'menu_name')
    if ($args->theme_location != 'account-menu')  
        return $sorted_menu_objects;

    // remove the menu item that has a title of 'Uncategorized'
    /* foreach ($sorted_menu_objects as $key => $menu_object) {

        // can also check for $menu_object->url for example
        // see all properties to test against:
        // print_r($menu_object); die();
        if ($menu_object->title == 'Uncategorized') {
            unset($sorted_menu_objects[$key]);
            break;
        }
    } */
	
	//var_dump($sorted_menu_objects);
	
	$user = rvb_get_current_user();
	
	//remove my booking link if user cannot make booking, the array key of $sorted_menu_objects is depend on the menu order in wordpress menu, array key start from 1
	if(!rvb_user_can($user, 'can_booking')){
		unset($sorted_menu_objects[2]);
	}
	
	//remove my listing link if user cannot submit listing, the array key of $sorted_menu_objects is depend on the menu order in wordpress menu, array key start from 1
	if(!rvb_user_can($user, 'can_listing')){
		unset($sorted_menu_objects[3]);
	}

    return $sorted_menu_objects;
}

add_action('wp_footer', 'login_register_form');
function login_register_form(){
	if(!rvb_is_user_logged_in()){
	?>
		<div id="login-register" class="popup-window">
			<div class="inner-window">
				<div class="window-head"><?php _e('Sign in / Register', 'rajavillabali'); ?> <span id="close-window">&times;</span></div>
				<div class="window-content">
					<div class="login-register">
						<div class="errors"></div>
						<form id="login-register-form" data-act="login">
							<div class="field register">
								<a href="<?php echo get_page_link( get_option('rvb_homeowner_page') ) ?>">
									<?php _e('Are you homeowner? apply an account here', 'rajavillabali'); ?>
								</a>
							</div>
							<div class="field">
								<label>Username</label>
								<input type="text" name="username">
							</div>
							<div class="field register">
								<label>Email</label>
								<input type="email" name="email">
							</div>
							<div class="field">
								<label>Password</label>
								<input type="password" name="password">
							</div>
							<div class="field register">
								<label><?php _e('Confirm Password', 'rajavillabali'); ?></label>
								<input type="password" name="confirm-password">
							</div>
							
							<div class="text-right buttons">
									<input type="submit" class="login" name="login" value="<?php _e('Sign in', 'rajavillabali') ?>">
									<input type="submit" class="register" name="register" value="<?php _e('Register', 'rajavillabali') ?>">
							</div>
							<div class="other-acts">
									<a href="<?php echo get_page_link( get_option('rvb_reset_password_page') ); ?>" id="forget-password" class="other-act login">Forget your password?</a>
									<a href="#" id="create-account" class="other-act login"><?php _e("Don't have an account yet? Register now", 'rajavillabali'); ?></a>
									<a href="#" id="sign-in" class="other-act register"><?php _e("Already have an account? Sign in now", 'rajavillabali'); ?></a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
}

add_action('wp_footer', 'login_register_form_homeowner');
function login_register_form_homeowner(){
	if(!rvb_is_user_logged_in('homeowner')){
	?>
		<div id="login-register-homeowner" class="popup-window">
			<div class="inner-window">
				<div class="window-head"><?php _e('Register', 'rajavillabali'); ?> <span id="close-window">&times;</span></div>
				<div class="window-content">
					<div class="login-register">
						<div class="errors"></div>
						<form id="login-register-form-homeowner" data-act="login" data-type="homwowner">
							<div class="field">
								<label>Name</label>
								<input type="text" name="name">
							</div>
							<div class="field">
								<label>Phone</label>
								<input type="text" name="phone">
							</div>
							<div class="field">
								<label>Username</label>
								<input type="text" name="username">
							</div>
							<div class="field">
								<label>Email</label>
								<input type="email" name="email">
							</div>
							<div class="field">
								<label>Password</label>
								<input type="password" name="password">
							</div>
							<div class="field">
								<label><?php _e('Confirm Password', 'rajavillabali'); ?></label>
								<input type="password" name="confirm-password">
							</div>
							
							<div class="text-right buttons">
								<input type="submit" name="register" value="<?php _e('Register', 'rajavillabali') ?>">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
}

add_action('wp_ajax_rvb_account_action_register', 'rvb_account_action_register');
add_action('wp_ajax_nopriv_rvb_account_action_register', 'rvb_account_action_register');
function rvb_account_action_register(){
	$errors = [];
	
	if($_POST['type'] == 'owner'){
		if(!empty($_POST['name'])){
			$homwowner_name = sanitize_text_field($_POST['name']);
		}else{
			$errors[] = __('Please fill in your name', 'rajavillabali');
		}
		
		if(!empty($_POST['phone'])){
			$homwowner_phone = sanitize_text_field($_POST['phone']);
		}else{
			$errors[] = __('Please fill in your phone number', 'rajavillabali');
		}
	}
	
	if(!empty($_POST['username']) && validate_username($_POST['username'])){
		$username = sanitize_user($_POST['username']);
	}else{
		$errors[] = __('Invalid Username, only alphabet, number, and these caracters ( _, space, ., -, *, and @ ) are allowed', 'rajavillabali');
	}
	
	if(!empty($_POST['email']) && is_email($_POST['email'])){
		$email = sanitize_email($_POST['email']);
	}else{
		$errors[] = __('Invalid Email', 'rajavillabali');
	}
	
	
	$valid = blk_password_meter($_POST['password']);
	if($valid === true){
		if(!empty($_POST['password']) && !empty($_POST['confirm_password'])){
			
			if($_POST['password'] == $_POST['confirm_password']){
				$password = $_POST['password'];
			}else{
				$errors[] = __('Password and confirm password does not match', 'rajavillabali');
			}
			
		}else{
			$errors[] = __('Please fill in both password and confirm password', 'rajavillabali');
		}
	}else{
		$errors[] = $valid;
	}
	
	
	$user_id = username_exists( $username );
	if ( $user_id || email_exists($email) ) {
		
		$errors[] = __('Username or email are already registered.', 'rajavillabali');
	}
	
	if(empty($errors)){
		$redirect_to = get_page_link( get_option('rvb_my_account_page') );
		
		$args = array(
				'user_login'	=> $username,
				'user_email'	=> $email,
				'user_pass'		=> $password,
			);
		
		if($_POST['type'] == 'user'){
			$args['role'] = 'common_user';
		}elseif($_POST['type'] == 'owner'){
			$args['role'] = 'homeowner';
			$args['first_name'] = $homwowner_name;
			
			
		}
		
		$new_user_id = wp_insert_user($args);
		
		if(isset($homwowner_phone)){
			update_user_meta($new_user_id, 'rvb_phone', $homwowner_phone);
		}
		
		/* $new_user_id = wp_create_user( $username, $password, $email );
		
		$user = new WP_User($new_user_id);
		$user->set_role('common_user'); */
		rvb_signin_user($username);
		
		echo 'success|<p class="success">You are registered successfully, logging you in!</p>|'.$redirect_to;
	}else{
		echo 'error|<ul><li>' . implode('</li><li>',$errors) . '</li></ul>';
	}
	
	wp_die();
	
}

function blk_password_meter($pass){
	$valid = true;
	
	if(strlen($pass) < 8 || 1 !== preg_match('/[A-Z]/', $pass) || 1 !== preg_match('/[a-z]/', $pass) || 1 !== preg_match('/[^a-zA-Z]+/', $pass)){
		$valid = false;
	}
	
	if(!$valid){
		return __('Your password must be at least 8 characters. <br> It must contain a mixture of upper and lower case letters, and at least one number or symbol.', 'rajavillabali');
	}
	
	return $valid;
}

add_action('wp_ajax_rvb_account_action_login', 'rvb_account_action_login');
add_action('wp_ajax_nopriv_rvb_account_action_login', 'rvb_account_action_login');
function rvb_account_action_login(){
	$username = sanitize_user($_POST['username']);
	$password = $_POST['password'];
	
	$result = rvb_check_user_password($username, $password);
	
	if( $result !== false ){
		$redirect_to = get_page_link( get_option('rvb_my_account_page') );
		echo 'success|<p class="success">Sign in successfully, please wait a moment</p>|'.$redirect_to;
	}else{
		echo 'error|<ul><li>Username & password does not match</li></ul>';
	}
	
	wp_die();
}

function rvb_check_user_password($username, $pass){
	$user = get_user_by( 'login', $username );
	if ( $user && wp_check_password( $pass, $user->data->user_pass, $user->ID) ){
		rvb_signin_user($username);
		return $user->ID;
	}else{
	   return false;
	}
}

function rvb_signin_user($username){
	setcookie('rvb_user_loggedin', $username, time()+60*60*24*360, '/' );
}

function rvb_is_user_logged_in($role = 'common_user'){
	if(!empty($_COOKIE['rvb_user_loggedin'])){
		$user = get_user_by( 'login', $_COOKIE['rvb_user_loggedin'] );
		if($user && in_array($role, $user->roles )){
			return true;
		}
	}
	
	return false;
}

function rvb_get_current_user(){
	if(!empty($_COOKIE['rvb_user_loggedin'])){
		$user = get_user_by( 'login', $_COOKIE['rvb_user_loggedin'] );
		if($user && ( in_array('common_user', $user->roles) || in_array('homeowner', $user->roles ) )){
			// returning user object
			return $user;
		}
	}
	
	return false;
}

function rvb_user_can($user, $cap){
	//var_dump($user->allcaps);
	if($user->allcaps[$cap]) return true;
	
	return false;
}

add_action('wp_ajax_rvb_account_action_logout', 'rvb_account_action_logout');
add_action('wp_ajax_nopriv_rvb_account_action_logout', 'rvb_account_action_logout');
function rvb_account_action_logout(){
	rvb_logout_user();
	echo get_home_url();
	wp_die();
}

function rvb_logout_user(){
	setcookie('rvb_user_loggedin', '', time()-3600, '/' );
}

add_action('init', 'set_property_filter_cookie');
function set_property_filter_cookie(){
	if(isset($_GET['mphb_attributes'])){
		setcookie('mphb_attributes', json_encode($_GET['mphb_attributes']), time() + 3600, '/');
		$_COOKIE['mphb_attributes'] = json_encode($_GET['mphb_attributes']);
	}
}

//MPHB Function Copy & Modif
function rvb_getAvailableRoomTypes(){
	global $wpdb;
	
	$storedParameters = MPHB()->searchParametersStorage()->get();
	
	//var_dump( $storedParameters );
	
	$checkInDateObj	 = \DateTime::createFromFormat( MPHB()->settings()->dateTime()->getDateTransferFormat(), $storedParameters['mphb_check_in_date'] );
	$checkOutDateObj = \MPHB\Utils\DateUtils::createCheckOutDate( MPHB()->settings()->dateTime()->getDateTransferFormat(), $storedParameters['mphb_check_out_date'] );
	
	$roomsAtts = array(
		'availability'	 => 'locked',
		'from_date'		 => $checkInDateObj,
		'to_date'		 => $checkOutDateObj,
	);

	$lockedRooms	 = MPHB()->getRoomPersistence()->searchRooms( $roomsAtts );
	$lockedRoomsStr	 = join( ',', $lockedRooms );

	$query = "SELECT DISTINCT room_types.ID AS id, COUNT(DISTINCT rooms.ID) AS count"
		. " FROM {$wpdb->posts} AS rooms";

	$join = " INNER JOIN {$wpdb->postmeta} AS room_type_ids"
		. " ON rooms.ID = room_type_ids.post_id AND room_type_ids.meta_key = 'mphb_room_type_id'"
		. " INNER JOIN {$wpdb->posts} AS room_types"
		. " ON room_type_ids.meta_value = room_types.ID";

	$where = " WHERE 1=1"
		. " AND rooms.post_type = '" . MPHB()->postTypes()->room()->getPostType() . "'"
		. " AND rooms.post_status = 'publish'"
		. ( !empty( $lockedRoomsStr ) ? " AND rooms.ID NOT IN ({$lockedRoomsStr})" : "" )

		. " AND room_type_ids.meta_value IS NOT NULL"
		. " AND room_type_ids.meta_value != ''"

		. " AND room_types.post_type = '" . MPHB()->postTypes()->roomType()->getPostType() . "'"
		. " AND room_types.post_status = 'publish'";

	$order = " GROUP BY room_type_ids.meta_value DESC";
	
	//Filter By Guest Capacity - BLK Edit
	if(!empty($storedParameters['mphb_adults'])){
		$join .= " INNER JOIN {$wpdb->postmeta} guest ON ( guest.post_id = room_types.ID AND guest.meta_key='mphb_adults_capacity' )";
		$where .= " AND guest.meta_value >= ".$storedParameters['mphb_adults'];
	}
	
	//Filter by attributes
	$attributes = !empty($_COOKIE['mphb_attributes']) ? rvb_parseAttributes( json_decode( stripcslashes( $_COOKIE['mphb_attributes'] ), true ) ) : '';
	
	if ( !empty( $attributes ) ) {
		// Add attributes to the query. At the moment the relation between
		// attributes is OR. Later we need to check, that every room type
		// have each required term (change relation to AND)

		/*$inTerms	 = MPHB()->translation()->translateAttributes( $this->attributes, MPHB()->translation()->getDefaultLanguage() );
		$inTerms	 = array_unique( $inTerms );
		$inTermsStr	 = join( ',', $inTerms ); */
		
		//var_dump($this->attributes);
		//var_dump($inTerms);
		//var_dump($inTermsStr);
		
		// "object_id" can differ from "term_taxonomy_id"; see issue [MB-935]
		/*$join .= " INNER JOIN {$wpdb->term_relationships} AS room_relationships"
			. " ON room_types.ID = room_relationships.object_id"
			 . " INNER JOIN {$wpdb->term_taxonomy} AS room_attributes"
			. " ON room_relationships.term_taxonomy_id = room_attributes.term_taxonomy_id"; */
		
		//Modif to regards the property filters ( filter on the side )
		foreach($attributes as $key=>$val){
			if(is_array($val)){
				if($key == 'location'){
					$v = implode(',', array_map('absint', $val));
					$join .= " 
						INNER JOIN {$wpdb->term_relationships} as {$key} ON ( room_types.ID = {$key}.object_id  AND {$key}.term_taxonomy_id IN ({$v}) )
						";
				}else{
					foreach($val as $v){
						$join .= " 
						INNER JOIN {$wpdb->term_relationships} as {$key}_{$v} ON ( room_types.ID = {$key}_{$v}.object_id  AND {$key}_{$v}.term_taxonomy_id = {$v} )
						";
						
					}
				}
			}else{
				$join .= " 
				INNER JOIN {$wpdb->term_relationships} as {$key} ON ( room_types.ID = {$key}.object_id  AND {$key}.term_taxonomy_id = {$val})
				";
			}
			
		}
		
		//$where .= " AND room_attributes.term_id IN ({$inTermsStr})"; // Here term ID can be any from the required list
	}
	
	//echo '<pre>' . $query . $join . $where . $order . '</pre>';
	$roomTypeDetails = $wpdb->get_results( $query . $join . $where . $order, ARRAY_A );

	return $roomTypeDetails;
}

function rvb_parseAttributes( $attributes ){
	$the_attributes = [];
	foreach ( $attributes as $attributeName => $id ) {
		if ( empty( $id ) ) {
			continue;
		}

		$attributeName = mphb_sanitize_attribute_name( $attributeName );
		/* if(is_array($id)){
			$id = implode(',', array_map('absint', $id));
		}else{
			$id = absint( $id );
		} */

		$the_attributes[$attributeName] = $id;
	}
	
	return $the_attributes;
}

//original function name renderDefaultOrForDatesPrice()
function rvb_getDefaultOrForDatesPrice($id){
	$searchParameters = MPHB()->searchParametersStorage()->get();

	$hasRates = false;

	if ( $searchParameters['mphb_check_in_date'] && $searchParameters['mphb_check_out_date'] ) {
		$rateAtts = array(
			'check_in_date'	 => \DateTime::createFromFormat( 'Y-m-d', $searchParameters['mphb_check_in_date'] ),
			'check_out_date' => \DateTime::createFromFormat( 'Y-m-d', $searchParameters['mphb_check_out_date'] )
		);

		if ( MPHB()->getRateRepository()->isExistsForRoomType( $id, $rateAtts ) ) {
			$hasRates = true;
		}
	}

	if ( $hasRates ) {
		$checkInDate	 = \MPHB\Utils\DateUtils::createCheckInDate( MPHB()->settings()->dateTime()->getDateTransferFormat(), $searchParameters['mphb_check_in_date'] );
		$checkOutDate	 = \MPHB\Utils\DateUtils::createCheckOutDate( MPHB()->settings()->dateTime()->getDateTransferFormat(), $searchParameters['mphb_check_out_date'] );
		ob_start();
		mphb_tmpl_the_room_type_price_for_dates( $checkInDate, $checkOutDate, $id );
		return ob_get_clean();

	} else {
		ob_start();
		mphb_tmpl_the_room_type_default_price($id);
		return ob_get_clean();
	}
}
//MPHB Function Copy & Modif End

function rvb_pages_list_dropdown($args){
	/*
		$args : array(
					'name' 		=> '',
					'selected'	=> '',
				)
	*/
	$pages = get_pages();
	
	?>
	<select name="<?php echo $args['name'] ?>">
		<option value=""></option>
		<?php
			if( !empty($pages) ){
				foreach($pages as $page){
					?>
					<option value="<?php echo $page->ID ?>" <?php echo $args['selected'] == $page->ID ? 'selected' : ''; ?> ><?php echo $page->post_title; ?></option>
					<?php
				}
			}
		?>
	</select>
	<?php
}


//Drag & Drop File Uploader

add_action( 'wp_ajax_handle_dropped_media', 'handle_dropped_media' );
// if you want to allow your visitors of your website to upload files, be cautious.
add_action( 'wp_ajax_nopriv_handle_dropped_media', 'handle_dropped_media' );
function handle_dropped_media() {
    status_header(200);

    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['path'] . DIRECTORY_SEPARATOR;
    $num_files = count($_FILES['file']['tmp_name']);

    $newupload = 0;

    if ( !empty($_FILES) ) {
        $files = $_FILES;
        foreach($files as $file) {
            $newfile = array (
                    'name' => $file['name'],
                    'type' => $file['type'],
                    'tmp_name' => $file['tmp_name'],
                    'error' => $file['error'],
                    'size' => $file['size']
            );

            $_FILES = array('upload'=>$newfile);
            foreach($_FILES as $file => $array) {
                $newupload = media_handle_upload( $file, 0 );
            }
        }
    }

    echo $newupload;    
    die();
}

add_action( 'wp_ajax_handle_deleted_media', 'handle_deleted_media' );
add_action( 'wp_ajax_nopriv_handle_deleted_media', 'handle_deleted_media' );
function handle_deleted_media(){

    if( isset($_REQUEST['media_id']) ){
        $post_id = absint( $_REQUEST['media_id'] );

        $status = wp_delete_attachment($post_id, true);

        if( $status )
            echo json_encode(array('status' => 'OK'));
        else
            echo json_encode(array('status' => 'FAILED'));
    }

    die();
}

add_action('wp_ajax_nopriv_rvb_save_property', 'rvb_save_property');
add_action('wp_ajax_rvb_save_property', 'rvb_save_property');
function rvb_save_property(){
	$post_data = $_POST;
	$post_id = (int)$post_data['post_id'];
	$post_title = sanitize_text_field( $post_data['property_name'] );
	$user = rvb_get_current_user();
	
	$args = array(
				'post_author'	=> $user->ID,
				'post_title'	=> $post_title,
				'post_type'		=> 'mphb_room_type',
				'post_content'	=> $_POST['description'],
			);
	
	if(!empty($post_id)){
		$args['ID'] = $post_id;
	}
	
	/* if(!empty($post_data['status'])){
		$args['post_status'] = $post_data['status'];
	} */
	
	//Fetching meta input
	$meta_input = array();
	
	if(!empty($post_data['meta'])){
		$meta_input = $post_data['meta'];
	}
	
	if(!empty($post_data['pinpoint'])){
		$meta_input['pinpoint'] = $post_data['pinpoint'];
	}
	
	if(!empty($post_data['images'])){
		$meta_input['rvb_property_photos'] = $post_data['images'];
		$meta_input['_thumbnail_id'] = $post_data['featured-image'];
	}
	
	if(!empty($meta_input)){
		$args['meta_input'] = $meta_input;
	}
	
	/* echo '<pre>';
	var_dump($args['tax_input']);
	echo '</pre>';
	
	wp_die(); */

	
	$id = wp_insert_post($args);
	
	if($id && !is_wp_error($id)){
		
		//Fetching taxonomies input
		if(!empty($post_data['terms'])){
			$taxonomies = convert_to_int( $post_data['terms'] );
			
			foreach($taxonomies as $tax_name=>$tags){
				wp_set_post_terms($id, $tags, $tax_name);
			}
		}
		
		//Fetching Price - do it here because property ID is needed - affect availability
		if(!empty($post_data['mphb_season_prices'][0]['price']['prices'][0])){
			$rate_id = get_property_rate($id);
			
			if(!empty($rate_id)){
				update_post_meta($rate_id, 'mphb_season_prices', $post_data['mphb_season_prices']);
			}else{
				$price_args = array(
							'post_author'	=> $user->ID,
							'post_title'	=> $post_title,
							'post_type'		=> 'mphb_rate',
							'post_status'	=> 'publish',
							'meta_input'	=> array(
													'mphb_season_prices'	=> $post_data['mphb_season_prices'],
													'mphb_room_type_id'		=> $id,
													'mphb_description'		=> '',
												),
						);
			
				$price_id = wp_insert_post($price_args);
			}
		}
		
		//Create property room if there is no one yet - affect availability
		$room_id = get_property_room($id);
		if(empty($room_id)){
			$room_args = array(
						'post_author'	=> $user->ID,
						'post_title'	=> $post_title,
						'post_type'		=> 'mphb_room',
						'post_status'	=> 'publish',
						'meta_input'	=> array(
												'mphb_room_type_id'		=> $id,
											),
					);
		
			$room_id = wp_insert_post($room_args);
		}
		
		//Save iCal Urls
		$room   = MPHB()->getRoomRepository()->findById( $room_id );
		// Save new list of URLs
		$newUrls = is_array( $_POST['mphb_sync_urls'] ) ? $_POST['mphb_sync_urls'] : array();
		$newUrls = wp_list_pluck( $newUrls, 'url' );

		$room->setSyncUrls( $newUrls );
		
		
		echo 'success|'.$id;
		
	}else{
		echo 'error|post creation error';
	}
	
	wp_die();
}

function convert_to_int($array){
	foreach($array as $key=>$val){
		if(is_array($val)){
			$array[$key] = array_map('intval', $val);
		}else{
			$array[$key] = (int)$val;
		}
	}
	
	return $array;
}

function get_property_rate($property_id){
	global $wpdb;
	
	$sql = 'SELECT ID
				FROM '.$wpdb->posts.' rate
			INNER JOIN '.$wpdb->postmeta.' pm ON (pm.post_id=rate.ID AND pm.meta_key="mphb_room_type_id")
			WHERE pm.meta_value = '.$property_id.' AND rate.post_type="mphb_rate" AND rate.post_status="publish"
			limit 1
		';
		
	$rate_id = $wpdb->get_var($sql);
	
	return $rate_id;
}

function get_property_room($property_id){
	global $wpdb;
	
	$sql = 'SELECT ID
				FROM '.$wpdb->posts.' room
			INNER JOIN '.$wpdb->postmeta.' pm ON (pm.post_id=room.ID AND pm.meta_key="mphb_room_type_id")
			WHERE pm.meta_value = '.$property_id.' AND room.post_type="mphb_room" AND room.post_status="publish"
			limit 1
		';
		
	$room_id = $wpdb->get_var($sql);
	
	return $room_id;
}

add_action('wp_ajax_submit_property_for_review', 'submit_property_for_review');
add_action('wp_ajax_nopriv_submit_property_for_review', 'submit_property_for_review');
function submit_property_for_review(){
	$post_id = (int)$_POST['post_id'];
	
	$args = array(
				'ID' =>  $post_id,
			);
	
	if(!empty($_POST['post_status']) && $_POST['post_status'] != 'draft' ){
		$args['post_status'] = $_POST['post_status'];
	}else{
		$args['post_status'] = 'pending';
	}
	
	$status = wp_update_post($args);
	 
	if($status){
		if( !empty($_POST['post_status']) && $_POST['post_status'] != 'draft' ){
			$title_msg = __('Your changes have been saved successfully', 'rajavillabali');
			$content_msg = __('Your changes have been saved successfully and now are shown publicly on our website.', 'rajavillabali');
		}else{
			$title_msg = __('Your property has been submitted successfully', 'rajavillabali');
			$content_msg = __('Thank you, your property has been submitted and are now under review, you will be notified when your property is published.', 'rajavillabali');
			
			$subject = __( sprintf('%s - Your Property Submitted Successfully', get_bloginfo('name') ), 'rajavillabali' );
			$email_title = __('Your Property Is Under Review', 'rajavillabali');
			
			$email_content = '<p>'.__(sprintf('Thank you, your property " %s " has been submitted and are now under review, you will be notified when your property is published.', get_the_title($post_id)), 'rajavillabali') . '</p>';
			
			$user = rvb_get_current_user();
			
			rvb_send_email($user->user_email, $subject, $email_title, $email_content);
		}
		
		echo 'success|'.$title_msg.'|'.$content_msg;
		
	}else{
		if( !empty($_POST['post_status']) && $_POST['post_status'] != 'draft' ){
			$error_msg = __('cannot saving your changes at the moment', 'rajavillabali');
			echo 'error|'.$error_msg;
		}else{
			$error_msg = __('cannot submit your property at the moment', 'rajavillabali');
			echo 'error|'.$error_msg;
		}
	}
	
	wp_die();
}

add_action('wp_ajax_remove_property_photo', 'remove_property_photo');
add_action('wp_ajax_nopriv_remove_property_photo', 'remove_property_photo');
function remove_property_photo(){
	if(!empty($_POST['image_id']) && is_numeric($_POST['image_id'])){
		if( false === wp_delete_attachment( $_POST['image_id'] ) ){
			echo 'error|cannot delete the image at the moment';
		}else{
			echo 'success|';
		}
	}else{
		echo 'error|image not found';
	}
	
	wp_die();
}

add_action('wp_ajax_rvb_do_change_user_password', 'rvb_do_change_user_password');
add_action('wp_ajax_nopriv_rvb_do_change_user_password', 'rvb_do_change_user_password');
function rvb_do_change_user_password(){
	$user = rvb_get_current_user();
	
	//$result = rvb_check_user_password($user, $_POST['current-password']);
	
	if ( $user && wp_check_password( $_POST['current-password'], $user->data->user_pass, $user->ID) ){
		wp_set_password($_POST['new-password'], $user->ID);
		echo 'success|'.__('Your password changed successfully', 'rajavillabali');
	}else{
		echo 'error|'.__('Your current password does not match','rajavillabali');
	}
	
	wp_die();
}

add_action('wp_ajax_rvb_do_change_user_info', 'rvb_do_change_user_info');
add_action('wp_ajax_nopriv_rvb_do_change_user_info', 'rvb_do_change_user_info');
function rvb_do_change_user_info(){
	$user = rvb_get_current_user();
	
	//if new email have been used by other user
	if(email_exists($_POST['email']) && $user->user_email != $_POST['email']){
		echo 'error|'.__('Cannot save your changes, your new email have been used','rajavillabali');
		wp_die();
	}
	
	$args = array(
				'ID'			=> $user->ID,
				'first_name'	=> $_POST['name'],
				'user_email'	=> $_POST['email'],
			);
	
	$user_id = wp_update_user($args);
	
	if ( $user_id && !is_wp_error($user_id) ){
		update_user_meta($user_id, 'rvb_phone', $_POST['phone']);
		echo 'success|'.__('Your information changed successfully', 'rajavillabali');
	}else{
		echo 'error|'.__('Could not change your information at the moment','rajavillabali');
	}
	
	wp_die();
}


//Login user automatically when they click "booking link" on the booking confirmation email they received
add_action('wp_loaded', 'auto_login_from_booking_email');
function auto_login_from_booking_email(){
	if(!empty($_GET['jlm']) && !empty($_GET['key'])){
		$booking = get_post($_GET['jlm']);
		if(!empty($booking)){
			$key = get_post_meta($booking->ID, 'mphb_key', true);
			
			if($key == 'booking_'.$_GET['key']){
				$user_id = get_post_meta($booking->ID, 'rvb_user_id', true);
				$user = get_user_by('ID', $user_id);
				if($user){
					rvb_signin_user($user->user_login);
				}
			}
		}
		
		wp_redirect(get_page_link(get_option('rvb_my_account_page')));
		exit();
	}
}

add_action('wp_ajax_rvb_cancel_the_booking', 'rvb_cancel_the_booking');
add_action('wp_ajax_nopriv_rvb_cancel_the_booking', 'rvb_cancel_the_booking');
function rvb_cancel_the_booking(){
	if(!empty($_POST['booking_id']) && is_numeric($_POST['booking_id'])){
		$booking = get_post($_POST['booking_id']);
		
		if($booking){
			//update_post_meta($booking->ID, 'cancel_reason', $_POST['reason']);
			//update_post_meta($booking->ID, 'cancel_file', $_POST['images']);
			
			$booking = MPHB()->getBookingRepository()->findById( $booking->ID );
			$booking->setStatus( \MPHB\PostTypes\BookingCPT\Statuses::STATUS_CANCELLED );
			$isSaved = MPHB()->getBookingRepository()->save( $booking );
			
			if($isSaved){
				do_action( 'mphb_customer_cancelled_booking', $booking );
				
				wp_send_json_success(array(
					'msg'			=> __('Booking cancelled successfully', 'rajavillabali'),
					'redirect_to'	=> get_page_link( get_option('rvb_my_booking_page') ),
				));

			}else{
				wp_send_json_error(array(
					'msg'	=> __('Cannot proccess your cancellation request at the moment', 'rajavillabali'),
				));
			}
			
		}else{
			wp_send_json_error(array(
				'msg'	=> __('Cannot proccess you request at the moment', 'rajavillabali'),
			));
		}
		
	}else{
		wp_send_json_error(array(
				'msg'	=> __('Cannot proccess you request at the moment', 'rajavillabali'),
			));
	}
	
	
	wp_die();
}

add_action('wp_ajax_rvb_do_change_booking', 'rvb_do_change_booking');
add_action('wp_ajax_nopriv_rvb_do_change_booking', 'rvb_do_change_booking');
function rvb_do_change_booking(){
	if(!empty($_POST['booking_id']) && is_numeric($_POST['booking_id'])){
		$booking = get_post($_POST['booking_id']);
		
		if($booking){
			
			$new_check_in = DateTime::createFromFormat('j F Y', $_POST['check-in']);
			$check_in = DateTime::createFromFormat('Y-m-d', get_post_meta($_POST['booking_id'], 'mphb_check_in_date', true));
			
			$diff = $check_in->diff($new_check_in)->format("%R%a day");
			
			$check_out = DateTime::createFromFormat('Y-m-d', get_post_meta($_POST['booking_id'], 'mphb_check_out_date', true));
			$new_check_out = DateTime::createFromFormat('Y-m-d', $check_out->format('Y-m-d'));
			$new_check_out->modify($diff);
			
			$booking_object = MPHB()->getBookingRepository()->findById( $booking->ID );
			$reservedRooms	 = $booking_object->getReservedRooms();
			$accomodation_id = $reservedRooms[0]->getRoomTypeId();
			$is_available = check_single_property_availability($accomodation_id, $new_check_in, $new_check_out);
			
			if($is_available){
				update_post_meta($booking->ID, 'prev_check_in_date', $check_in->format('Y-m-d'));
				update_post_meta($booking->ID, 'prev_check_out_date', $check_out->format('Y-m-d'));
				
				update_post_meta($booking->ID, 'mphb_check_in_date', $new_check_in->format('Y-m-d'));
				update_post_meta($booking->ID, 'mphb_check_out_date', $new_check_out->format('Y-m-d'));
				
				//$booking_object = MPHB()->getBookingRepository()->findById($booking->ID);
				send_booking_date_changes($booking_object);
				
				echo 'success|'.__('Booking dates changed successfully', 'rajavillabali');
				
			}else{
				
				echo 'error|'.__('New dates is not available', 'rajavillabali');
				
			}
		
		}else{
			echo 'error|'.__('Error : Booking not found', 'rajavillabali');
		}
	}else{
		echo 'error|'.__('Error : Booking not found', 'rajavillabali'); 
	}
	
	wp_die();
}

add_action('wp_ajax_rvb_get_check_out_date_change', 'rvb_get_check_out_date_change');
add_action('wp_ajax_nopriv_rvb_get_check_out_date_change', 'rvb_get_check_out_date_change');
function rvb_get_check_out_date_change(){
	if(!empty($_POST['booking_id']) && is_numeric($_POST['booking_id'])){
		$new_check_in = DateTime::createFromFormat('j F Y', $_POST['check-in']);
		$check_in = DateTime::createFromFormat('Y-m-d', get_post_meta($_POST['booking_id'], 'mphb_check_in_date', true));
		
		$diff = $check_in->diff($new_check_in)->format("%R%a day");
		//echo $diff .' | ';
		
		$check_out = DateTime::createFromFormat('Y-m-d', get_post_meta($_POST['booking_id'], 'mphb_check_out_date', true));
		$check_out->modify($diff);
		
		$booking = MPHB()->getBookingRepository()->findById( $_POST['booking_id'] );
		$reservedRooms	 = $booking->getReservedRooms();
		$accomodation_id = $reservedRooms[0]->getRoomTypeId();
		$is_available = check_single_property_availability($accomodation_id, $new_check_in, $check_out);
		
		if($is_available){
			wp_send_json_success( array( 
									'checkout' => $check_out->format('j F Y'),
								) );
		}else{
			wp_send_json_error( array( 
									'checkout'	=> $check_out->format('j F Y'),
									'message'	=> __('New dates is not available', 'rajavillabali'),
								) );
		}
		
		//echo $check_out->format('j F Y');
	}else{
		wp_send_json_error( array( 
									'message'	=> __('Forbiden', 'rajavillabali'),
								) );
	}
	
	wp_die();
}

function check_single_property_availability($property_id, $checkIn, $checkOut){

	$roomType	 = MPHB()->getRoomTypeRepository()->findById( $property_id );

	if ( !MPHB()->getRulesChecker()->reservationRules()->verify( $checkIn, $checkOut, $property_id ) ) {
		return false;
	}

	$availableRooms = MPHB()->getRoomPersistence()->searchRooms( array(
		'availability'	 => 'free',
		'from_date'		 => $checkIn,
		'to_date'		 => $checkOut,
		'room_type_id'	 => $property_id
	) );
	$unavailableRooms = MPHB()->getRulesChecker()->customRules()->getUnavailableRooms( $checkIn, $checkOut, $roomType->getOriginalId() );
	$unavailableRooms = array_intersect( $availableRooms, $unavailableRooms ); // Filter not available rooms
	$freeCount = count( $availableRooms ) - count( $unavailableRooms );

	if ( $freeCount > 0 ) {
		return true;
	} else {
		return false;
	}
}

//Send email to admin and homewowner when customer change booking date
function send_booking_date_changes($booking){
	//if(empty($_GET['test_mail'])) return;
		
	/* $booking = MPHB()->getBookingRepository()->findById( 3215 );

	if ( !$booking ) {
		echo 'booking sing ade';
		exit();
	} */
		
	$email_template = file_get_contents(get_template_directory().'/email-templates/default.html');
	//$subject = __('Booking Date Changed - Raja Villa Bali', 'rajavillabali');
	$subject = sprintf( __('%s - Booking #%s Date Changed', 'rajavillabali'), get_bloginfo('name'), $booking->getId() );
	
	$email_content = "<p>" . __('The dates of the following booking has changed by the guest', 'rajavillabali') . "</p>";
	ob_start();
	\MPHB\Views\BookingView::renderCheckInDateWPFormatted( $booking );
	$check_in = ob_get_clean();
	
	ob_start();
	\MPHB\Views\BookingView::renderCheckOutDateWPFormatted( $booking );
	$check_out = ob_get_clean();
	
	$prev_check_in = DateTime::createFromFormat('Y-m-d', get_post_meta($booking->getId(), 'prev_check_in_date', true));
	$prev_check_out = DateTime::createFromFormat('Y-m-d', get_post_meta($booking->getId(), 'prev_check_out_date', true));
	
	$total_price = $booking->getTotalPrice();
	
	$fee_percentage = get_option('rvb_company_fee');
	$fee = $total_price * $fee_percentage / 100;
	$potential_earn = $total_price - $fee;
	
	$email_content .= "<h4>Details of booking</h4>
						Booking ID: #{$booking->getId()}<br><br>
						
						<b>Previous Dates:</b><br>
						Check-in: {$prev_check_in->format('F j, Y')}<br>
						Check-out: {$prev_check_out->format('F j, Y')}<br><br>
						
						<b>Changed to:</b><br>
						Check-in: {$check_in}<br>
						Check-out: {$check_out}<br>";
	
	$reservedRooms	 = $booking->getReservedRooms();
	$accomodation_id = $reservedRooms[0]->getRoomTypeId();
	$roomType	 = MPHB()->getRoomTypeRepository()->findById( $accomodation_id );
					//$roomType	 = apply_filters( '_mphb_translate_room_type', $roomType, $this->booking->getLanguage() );
					//$replaceText = ( $roomType ) ? $roomType->getTitle() : '';
	$email_content .= "<h4>Accommodation</h4>
						Guest: {$reservedRooms[0]->getAdults()}<br>
						Accommodation: {$roomType->getTitle()}<br>";
	
	$email_content .= "<h4>Customer Info</h4>
						Name: ".$booking->getCustomer()->getFirstName()." ".$booking->getCustomer()->getLastName()."<br>
						Email: ".$booking->getCustomer()->getEmail()."<br>
						Phone: ".$booking->getCustomer()->getPhone()."<br>
						Note: <br>".$booking->getNote();
	
	$email_content .= "<h4>Revenue</h4>
						Gross Revenue: ".mphb_format_price( $total_price )."<br>
						Fee: ".mphb_format_price( $fee )."<br>
						Potential Earning: ". mphb_format_price( $potential_earn );
	
	$email_title = __('Booking date has changed', 'rajavillabali');
	
	$search = array('{email_title}', '{email_content}');
	$replace = array($email_title, $email_content);
	$email = str_replace($search, $replace, $email_template);
	
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';
	
	$villa_owner_email = get_post_meta($accomodation_id, 'rvb_property_contact_new_booking_email', true);
	// Additional headers
	//$headers[] = 'To: Mary <mary@example.com>, Kelly <kelly@example.com>';
	$headers[] = 'From: Raja Villa Bali <info@rajavillabali.com>';
	
	
	// Mail it to owner
	$status = wp_mail($villa_owner_email, $subject, $email, $headers);
	
	$admin_email = get_option('mphb_email_admin_payment_confirmed_booking_recipients');
	if( empty($admin_email) ) $admin_email = get_option('admin_email');
	
	//Mail it to admin
	wp_mail($admin_email, $subject, $email, $headers);
	
}

add_action('wp_ajax_rvb_get_reset_password_link', 'rvb_get_reset_password_link');
add_action('wp_ajax_nopriv_rvb_get_reset_password_link', 'rvb_get_reset_password_link');
function rvb_get_reset_password_link(){
	if(!empty($_POST['login-info'])){
		if (filter_var($_POST['login-info'], FILTER_VALIDATE_EMAIL)) {
			$user_id = email_exists($_POST['login-info']);
		} else {
			$user_id = username_exists($_POST['login-info']);
		}
		
		if($user_id){
			$user = get_user_by('ID', $user_id);
			
			$salt = wp_generate_password(20); // 20 character "random" string
			$key = sha1($salt . $user->user_email . uniqid(time(), true));
			
			update_user_meta( $user->ID, 'reset_password_key', $key );
			
			$link = get_page_link( get_option('rvb_reset_password_page') ) . '?login='.$user->user_login.'&key='.$key;
			
			$site_name = get_bloginfo('name');
			
			$subject = __( sprintf('%s - Reset Password', $site_name ), 'rajavillabali' );
			$email_title = __('Reset Password', 'rajavillabali');
			
			$email_content = '<p>Someone has requested a password reset for the following account:</p>

								Site Name: '.$site_name.'<br>

								Username: '.$user->user_login.'<br>

								<p>
									If this was a mistake, just ignore this email and nothing will happen.<br>
									To reset your password, visit the following address:<br><br>
									'.$link.'
								</p>
								';
			
			rvb_send_email($user->user_email, $subject, $email_title, $email_content);
			
			wp_send_json_success(array(
				'msg'	=> __('We have sent a reset password link to your email', 'rajavillabali'),
			));
			
		}else{
			wp_send_json_error(array(
				'msg'	=> __('ERROR: There is no account with that username or email address.', 'rajavillabali'),
			));
		}
	}else{
		wp_send_json_error(array(
				'msg'	=> __('ERROR: There is no account with that username or email address.', 'rajavillabali'),
			));
	}
	
	wp_die();
}

add_action('wp_ajax_rvb_do_reset_password_link', 'rvb_do_reset_password_link');
add_action('wp_ajax_nopriv_rvb_do_reset_password_link', 'rvb_do_reset_password_link');
function rvb_do_reset_password_link(){
	
	if(!empty($_POST['uid']) && is_numeric($_POST['uid'])){
		$user = get_user_by('ID', $_POST['uid']);
		wp_set_password($_POST['new-password'], $user->ID);
		
		wp_send_json_success(array(
			'msg'	=> __('Your password changed successfully', 'rajavillabali')
		));
	}else{
		wp_send_json_error(array(
			'msg'	=> __('Invalid request', 'rajavillabali')
		));
	}
	
	//$result = rvb_check_user_password($user, $_POST['current-password']);
	
	/* if ( $user && wp_check_password( $_POST['current-password'], $user->data->user_pass, $user->ID) ){
		wp_set_password($_POST['new-password'], $user->ID);
		echo 'success|'.__('Your password changed successfully', 'rajavillabali');
	}else{
		echo 'error|'.__('Your current password does not match','rajavillabali');
	} */
	
	wp_die();
}

add_action('publish_mphb_room_type', 'send_property_published_email', 10, 2);
function send_property_published_email($id, $post){
	$user = get_user_by('ID',$post->post_author);
	$subject = sprintf( __('Congratulation, your property has been published - %s', 'rajavillabali'), get_bloginfo('name') );
	$email_title = __('Congratulation, your property has been published', 'rajavillabali');
	$email_content = '<p>'. sprintf( __('Your property %s has been published and now is showing publicly in our website', 'rajavillabali'), apply_filters('the_title', $post->post_title ) ) .'</p>
						<a style="display: inline-block; padding: 12px 24px; background: #31adad; font-size: 1.2em;color: #fafafa;
								text-decoration: none;" href="'.get_permalink($id).'">See my property</a>';
	
	rvb_send_email($user->user_email, $subject, $email_title, $email_content);
}

//add_action('wp', 'verify_mc_payment');
add_shortcode('verify_mc_payment', 'verify_mc_payment');
function verify_mc_payment(){
	
	if(!empty($_GET['payment_id']) && !empty($_POST['tranID'])){
		if($_POST['status'] == '00'){
			
			//amount is empty when using sandbox mode
			if ( !empty( $_POST['amount'] ) ) {
				$booking_id = (int) get_post_meta( $_GET['payment_id'], '_mphb_booking_id', true );
				if($booking_id){
					update_post_meta( $booking_id, 'rvb_amount_paid', $_POST['amount'] );
				}
			}
			
			$payment = MPHB()->getPaymentRepository()->findById( $_GET['payment_id'], true );
			
			if ( !MPHB()->paymentManager()->canBeCompleted( $payment ) ) {
				return false;
			}

			

			/* if ( $completeLog ) {
				$this->payment->addLog( $completeLog );
			} */

			//$this->updatePaymentMeta();
			

			$payment->setTransactionId( mphb_clean( $_POST['tranID'] ) );

			$completed = MPHB()->paymentManager()->completePayment( $payment, '', true );
			
			return __('Thank you for your payment. Your transaction has been completed and a receipt for your payment has been emailed to you.', 'rajavillabali');
		}else{
			ob_start();
			
			_e('Unfortunately, your transaction cannot be completed at this time. Please try again or contact us.', 'rajavillabali');
			
			if(!empty($_POST['error_desc'])){
				echo '<p>Fail reason: '.$_POST['error_desc'].'</p>';
			}
			
			return ob_get_clean();
		}
		
		/* var_dump($completed);
		
		exit(); */
	}
}

//currency converter using google finance
function get_currency_rate($from_Currency, $to_Currency, $amount=1) {
	$amount = urlencode($amount);
    $from_Currency = urlencode($from_Currency);
    $to_Currency = urlencode($to_Currency);
    //$url = "https://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";
	//$url = "https://finance.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";
	//$url = "http://www.countrycurrencyrates.com/en/convert/IDR/USD/200000000";
	//$url = "https://www.xe.com/currencyconverter/convert/?Amount=$amount&From=$from_Currency&To=$to_Currency";
	//$url = 'https://www.google.com/search?q=1+usd+%3D+EUR&oq=1+usd+%3D+EUR';
	//$url = 'https://transferwise.com/gb/currency-converter/'.$from_Currency.'-to-'.$to_Currency.'-rate?amount=1';
	
	//$access_key = 'fe6af77be16be81ef7700cd97caadfc4';
	//$url = 'http://data.fixer.io/api/convert?access_key='.$access_key.'&from='.$from_Currency.'&to='.$to_Currency.'&amount='.$amount;
	
	
	//API from https://exchangeratesapi.io/
	$url = 'https://api.exchangeratesapi.io/latest?base='.$from_Currency.'&symbols='.$to_Currency;
	
	//return $url;
	
    $ch = curl_init();
    $timeout = 0;
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_USERAGENT,
                 "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36");
	curl_setopt($ch, CURLOPT_REFERER, 'https://www.google.com/');
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $rawdata = curl_exec($ch);
    
	
	if(!$rawdata){
		return array(
					'error' => curl_error($ch)
				);
	}
	
	curl_close($ch);
	//return $rawdata;
	 //<span class="text-success" data-rate="EUR">0.63037 EUR</span>
	 
	/* $data = explode('<span class="text-success">', $rawdata);
	return $data; */
	$data = json_decode($rawdata, true);
	
	//return $data;
	if(empty($data['rates'])){
		$msg = 'File path: ' . __FILE__;
		wp_mail('blaukblonk04@gmail.com', 'Currency rate checker does not work', $msg);
		return array(
					'error'	=> 'Unexpected data returned.',
				);
	}
	
    //$data = explode(" EUR</span>", $data[1]);
    //return str_replace(',','',$data[0]);//round(floatval($data[0]),2);
	return $data['rates'][$to_Currency];
}

add_action('wp_ajax_nopriv_render_sleeping_arrangement_form', 'render_sleeping_arrangement_form_ajax');
add_action('wp_ajax_render_sleeping_arrangement_form', 'render_sleeping_arrangement_form_ajax');
function render_sleeping_arrangement_form_ajax(){
	$attr = !empty($_POST['attr']) ? $_POST['attr'] : '';
	render_sleeping_arrangement_form($_POST['bedrooms'], $_POST['start_from'], $attr);
	wp_die();
}

function render_sleeping_arrangement_form($bedrooms = 0, $start_from = 0, $attr = ''){
	/* $bedrooms = !empty($_POST['bedrooms']) ? $_POST['bedrooms'] : '';
	$start_from = !empty($_POST['start_from']) ? $_POST['start_from'] : ''; */
	
	$mphb_bed_types = get_option('mphb_bed_types');
	
	for($i=$start_from; $i<$bedrooms; $i++){
		?>
		<div class="field the-room">
			<label><?php printf(__('Room %d', 'rajavillabali'), ($i+1)); ?></label>
			<ul>
				<li>
					<b>Bed Type</b> :
					<select name="meta[sleeping_arr][<?php echo $i; ?>][bed_type]" class="rvb-required" <?php echo $attr; ?>>
						<option value=""></option>
						<?php
							
							if(is_array($mphb_bed_types)){
								foreach($mphb_bed_types as $bed){
									?>
									<option value="<?php echo $bed['type'] ?>"><?php echo $bed['type'] ?></option>
									<?php
								}
							}
							
						?>
					</select>
				</li>
				<li>
					<b><?php _e('is en suite bathroom?', 'rajavillabali'); ?></b>:
					<input type="checkbox" name="meta[sleeping_arr][<?php echo $i; ?>][ensuite_bathroom]" value="yes">
				</li>
			</ul>
		</div>
		<?php
	}
}

add_action('wp_ajax_rvb_save_owner_bank_info', 'rvb_save_owner_bank_info');
add_action('wp_ajax_nopriv_rvb_save_owner_bank_info', 'rvb_save_owner_bank_info');
function rvb_save_owner_bank_info(){
	$user = rvb_get_current_user();
	if($_POST['uid'] == $user->ID){
		if(!empty($_POST['verification'])){
			if(is_valid_otp('change_bank_info', $_POST['verification'])){
				update_user_meta($user->ID, 'bankinfo', $_POST['bankinfo']);
				setcookie(md5('change_bank_info'), '', time() - 3600, '/');
				wp_send_json_success(array(
						'saved'	=> true,
						'msg'	=> __('Bank information updated successfully', 'rajavillabali')
					));
			}else{
				wp_send_json_error(array(
					'msg'	=> __('Verification code is incorrect', 'rajavillabali')
				));
			}
		}else{
			//Generate OTP here
			send_bank_change_otp_code($user->user_email);
			
			wp_send_json_success(array(
						'saved'	=> false,
					));
		}
	}else{
		wp_send_json_error(array(
				'msg'	=> __('Invalid request', 'rajavillabali')
			));
	}
	
	wp_die();
}

add_action('wp_ajax_resend_bank_change_otp', 'resend_bank_change_otp');
add_action('wp_ajax_nopriv_resend_bank_change_otp', 'resend_bank_change_otp');
function resend_bank_change_otp(){
	$user = rvb_get_current_user();
	
	if(send_bank_change_otp_code($user->user_email)){
		wp_send_json_success(array(
						'msg'	=> __('Verification code resent successfully, the verification code will expired in 10 minutes', 'rajavillabali')
					));
	}else{
		wp_send_json_error(array(
				'msg'	=> __('We having difficulty in sending the verification code, try again later', 'rajavillabali')
			));
	}
}

function send_bank_change_otp_code($email){
	$otp = rand(100000,999999);
	
	$subject = __(sprintf('Verification Code - %s', get_bloginfo('name')), 'rajavillabali');
	$email_title = __('Verification Code', 'rajavillabali');
	
	$email_content = '<p>'.__(sprintf('You recently attempt to change your bank account information on %s website, use the following code to continue:', get_bloginfo('name')), 'rajavillabali').'</p>';
	$email_content .= '<h1 style="text-align="center">'.$otp.'</h1>';
	
	$status = rvb_send_email($email, $subject, $email_title, $email_content);
	
	if($status){
		setcookie(md5('change_bank_info'), md5($otp), time() + 60 * 10, '/');
	}
	
	return $status;
}

function is_valid_otp($name, $otp){
	$cookie_name = md5($name);
	$cookie_otp = md5($otp);
	
	if(!empty($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] == $cookie_otp){
		return true;
	}else{
		return false;
	}
}

//Upload id_card when the field is changed
add_action('wp_ajax_rvb_upload_photo_profile', 'rvb_upload_photo_profile');
add_action('wp_ajax_nopriv_rvb_upload_photo_profile', 'rvb_upload_photo_profile');
function rvb_upload_photo_profile(){
	if ( !empty($_FILES['pp-input']) ){
		if($_FILES['pp-input']['size'] > 2000000){
			wp_send_json_error(array(
				'msg'			=> 'File size is too big, maximum is 2mb',
			));
			
			wp_die();
		}
	   
		$img_id = media_handle_upload('pp-input', $order_id );
		if($img_id && !is_wp_error($img_id)){
			$user = rvb_get_current_user();
			update_user_meta($user->ID, 'rvb_pp', $img_id);
			wp_send_json_success(array(
				'img_url'			=> wp_get_attachment_image_url($img_id, 'medium'),
			));
		}else{
			wp_send_json_error(array(
				'msg'			=> $img_id->get_error_message(),
			));
		}
	}else{
		wp_send_json_error(array(
				'msg'			=> 'No File',
			));
	}
	
	wp_die();
}

add_action('wp_ajax_get_villa_photos_view', 'get_villa_photos_view');
add_action('wp_ajax_nopriv_get_villa_photos_view', 'get_villa_photos_view');
function get_villa_photos_view(){
	$imgs = get_post_meta($_POST['post_id'], 'rvb_property_photos', true);
	
	?>
	<div class="uploaded-images">
		<?php
			if(!empty($imgs)){
				$thumbnail_id = get_post_thumbnail_id($_POST['post_id']);
				foreach($imgs as $img){
					if(!empty($img) && is_numeric($img)){
						?>
						<div class="uploaded-img">
							<?php echo wp_get_attachment_image($img, 'blog-small-thumb'); ?>
							<?php echo $thumbnail_id == $img ? '<i class="fa fa-star-o featured" title="'.__('Featured image', 'rajavillabali').'" aria-hidden="true"></i>' : ''; ?>
						</div>
						<?php
					}
				}
			}
		?>
	</div>
	<?php
	
	wp_die();
}

add_action('wp_ajax_get_villa_legal_docs_view', 'get_villa_legal_docs_view');
add_action('wp_ajax_nopriv_get_villa_legal_docs_view', 'get_villa_legal_docs_view');
function get_villa_legal_docs_view(){
	$imgs = get_post_meta($_POST['post_id'], 'rvb_property_legal_document', true);
	
	?>
	<div class="uploaded-images">
		<?php
			if(!empty($imgs)){
				$thumbnail_id = get_post_thumbnail_id($_POST['post_id']);
				foreach($imgs as $img){
					if(!empty($img) && is_numeric($img)){
						?>
						<div class="uploaded-img">
							<?php 
							$type =  get_post_mime_type($img);
							if($type == 'application/pdf'){
								$title = get_the_title($img);
								?>
								<div class="row">
									<div class="col-sm-12">
										<a href="<?php echo wp_get_attachment_url($img); ?>" target="_blank" title="<?php echo $title; ?>">
											
											<i class="fa fa-file-pdf-o fa-5x fa-pull-left" aria-hidden="true"></i>
											<?php echo wp_trim_words( $title, 7, null ); ?><br>
										</a>
									</div>
								</div>
								<?php
							}else{
								echo wp_get_attachment_image($img, 'blog-small-thumb');
							}
							
							?>
						</div>
						<?php
					}
				}
			}
		?>
	</div>
	<?php
	
	wp_die();
}

add_action('rvb_auto_update_season', 'rvb_update_seasons');
function rvb_update_seasons(){
	$seasons = get_posts(array(
					'post_type'		=> 'mphb_season',
					'meta_query'	=> array(
										array(
											'key'		=> 'mphb_end_date',
											'value'		=> date('Y-m-d'),
											'compare'	=> '<',
											'type'		=> 'DATE',
										)
									)
				));
	
	$log = '';
	$action = !empty($_GET['update_season']) ? 'manual' : 'auto';
	$log = date('Y-m-d H:i:s') .' Season '.$action.' updated : ';
		
	if(!empty($seasons)){
		$updated_seasons = array();
		
		foreach($seasons as $s){
			$start_date = DateTime::createFromFormat('Y-m-d', get_post_meta($s->ID, 'mphb_start_date', true) );
			$end_date = DateTime::createFromFormat('Y-m-d', get_post_meta($s->ID, 'mphb_end_date', true) );
			
			$start_date->modify('+1 year');
			$end_date->modify('+1 year');
			
			update_post_meta($s->ID, 'mphb_start_date', $start_date->format('Y-m-d'));
			update_post_meta($s->ID, 'mphb_end_date', $end_date->format('Y-m-d'));
			
			$updated_seasons[] = $s->post_title;
		}
		
		//$action = !empty($_GET['update_season']) ? 'manual' : 'auto';
		$log .= implode(', ', $updated_seasons)."\r\n";
		echo $log;
	}else{
		$log .= 'No season updated'."\r\n";
	}
	
	file_put_contents(get_template_directory() . '/logs/season_update.txt', $log, FILE_APPEND);
}

add_filter( 'cron_schedules', 'cron_add_monthly' );
function cron_add_monthly( $schedules ) {
	// Adds once a month to the existing schedules.
	$schedules['monthly'] = array(
		'interval' => 2592000,
		'display' => __( 'Once a Month' )
	);
	return $schedules;
}

function test_get_c(){
	$posts = get_posts(array(
					'post_type'			=> 'mphb_room_type',
					'posts_per_page'	=> 2,
				));
	
	
	$p = array();
	if($posts){
		foreach($posts as $p){
			$p[] = array(
						'ID'		=> $p->ID,
						'content'	=> $p->post_content
					);
		}
	}
	
}