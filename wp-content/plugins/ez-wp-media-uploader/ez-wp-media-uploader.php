<?php
/*
Plugin Name: Ez WP Media Uploader
Description: Provide shortcode for easy wp media uploader integration in any of wordpress pages.
Author: Blauk Blonk
Version: 1.0
*/

add_shortcode('ez_wp_media_uploader','init_ez_wp_media_uploader');
function init_ez_wp_media_uploader($atts){
	$error = array();
	$options = shortcode_atts( array(
        'title'			=> 'Select or Upload Media Of Your Chosen Persuasion',
        'multiple'		=> 'true',
		'field_name'	=> 'ez-wp-media-uploader-field',
		'wrapper_id'	=> '',
		'meta_key'		=> '',
		'post_id'		=> '',
		'wp_option'		=> '',
		'image_id'		=> '',
		'force_array_field_name' => '',
		'is_array_field_name'	=> '',
    ), $atts );
	
	if(empty($options['wrapper_id'])) $error[] = 'wrapper_id undefined';
	
	if( !empty($options['post_id']) && empty($options['meta_key']) ) {
		$options['meta_key'] = $options['field_name'];
	}
	
	if( !empty($options['post_id']) ){
		$images = get_post_meta($options['post_id'], $options['meta_key'], true);
	}elseif( !empty($options['image_id']) ){
		$images = $options['image_id'];
	}elseif( !empty($options['wp_option']) ){
		$images = get_option($options['wp_option']);
	}else{
		$error[] = 'Please specify the post_id or wp_option';
	}
	
	if($options['is_array_field_name']=='true'){
		$options["field_name"] = str_replace('{','[',$options["field_name"]);
		$options["field_name"] = str_replace('}',']',$options["field_name"]);
	}
	
	//$sortable = $options['multiple'] != 'false' ? '' : '';
	
	$form = '<div class="ez-wp-uploader" id="'.$options['wrapper_id'].'">';
	
	$img_data = array(
				'field_name'	=> $options['field_name'],
				'multiple'		=> $options['multiple'],
				'force_array_field_name' => $options['force_array_field_name'],
			);
					
	if($options['multiple']!='false'){
		$form .='<input type="button" value="Upload" class="ez_upload_button button button-primary button-large">
			<div id="thumbs" class="sortable">';
				
				if(is_array($images)){
					foreach($images as $img){
						$img_data['src'] = wp_get_attachment_image_src($img,'thumbnail');
						$img_data['attachment_id'] = $img;
						$form .= ez_wp_media_uploader_generate_fields($img_data);
					}
				}
			$form .='</div>';
	}
	
	if($options['multiple']=='false'){
		$form .='<div class="ez_upload_button single-uploader">';
			if(!empty($images)){
				$img_data['src'] = wp_get_attachment_image_src($images,'thumbnail');
				$img_data['attachment_id'] = $images;
				$form .= ez_wp_media_uploader_generate_fields($img_data);
			}
		$form .='</div>';
	}
	
	$form .='</div>';
	bind_ez_uploader($options);
	return $form;
}

function ez_wp_media_uploader_generate_fields($atts){
	$multiple = '';
	if($atts['multiple'] != 'false' || $atts['force_array_field_name']=='true'){
		$multiple = '[]';
	}
	
	$remove = '';
	if($atts['multiple'] == 'true'){
		$remove = '<span class="remove">x</span>';
	}
	return '<div class="thumb">
		'.$remove.'
		<img src="'.$atts['src'][0].'">
		<input type="hidden" type="text" name="'.$atts["field_name"].$multiple.'" value="'.$atts["attachment_id"].'">
	</div>';
}

function bind_ez_uploader($atts){
	?>
	<script>
		jQuery(document).ready(function(){
			if(jQuery('#<?php echo $atts['wrapper_id']; ?>').length>0){
				var multiple = '<?php echo $atts['multiple']; ?>';
				var force_array_field_name = '<?php echo $atts['force_array_field_name']; ?>';
				var is_array_field_name = '<?php echo $atts['is_array_field_name']; ?>';
				jQuery('#<?php echo $atts['wrapper_id']; ?>').wpmediauploader({
					title		: '<?php echo $atts['title'] ?>',
					multiple	: (multiple == 'true') ? true : false,
					fieldsName	: '<?php echo $atts['field_name']; ?>',
					force_array_field_name : (force_array_field_name == 'true') ? true : false,
				});
				
				jQuery('.ez-wp-media-uploader #thumbs').sortable();
			}
		});
	</script>
	<?php
}

add_action( 'admin_enqueue_scripts', 'add_ez_uploader_scripts');
function add_ez_uploader_scripts(){
	$plugin_dir = plugins_url('',__FILE__);
	
	wp_enqueue_style( 'ez-wp-media-uploader-js', $plugin_dir . '/css/style.css');
	wp_enqueue_media();
	wp_enqueue_script( 'ez-wp-media-uploader-js', $plugin_dir .'/js/wpmediauploader.js', array('jquery'));
}