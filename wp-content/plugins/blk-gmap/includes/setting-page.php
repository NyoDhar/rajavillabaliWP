<?php
function blk_gmap_add_page(){
	add_menu_page('BLK Gmap', 'BLK Gmap', 'manage_categories', 'blk-gmap', 'blk_gmap_setting_page' );
}
add_action('admin_menu','blk_gmap_add_page');

function blk_gmap_setting_page(){
	if(!empty($_POST['blk-save-gmail-settings'])){
		update_option('gmap-api-key', $_POST['gmap-api-key']);
		update_option('gmap-custom-marker', $_POST['gmap-custom-marker']);
		update_option('blk-gmap-circle-marker', $_POST['blk-gmap-circle-marker']);
	}
	
	$blk_gmap_circle_marker = get_option('blk-gmap-circle-marker');
	?>
	<div class="wrap">
		<h1>BLK Gmap Settings</h1>
		<form method="post">
			<table class="form-table">
				<tr>
					<th><label>Gmap API Key</label></th>
					<td><input name="gmap-api-key" type="text" value="<?php echo get_option('gmap-api-key'); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th><label>Gmap Custom Marker</label></th>
					<td>
						<?php echo do_shortcode('[ez_wp_media_uploader wrapper_id="gmap-custom-marker" field_name="gmap-custom-marker" wp_option="gmap-custom-marker" multiple="false"]'); ?>
					</td>
				</tr>
				<tr>
					<th><label>Use Circle instead of Marker</label></th>
					<td>
						<input type="checkbox" name="blk-gmap-circle-marker" <?php echo ($blk_gmap_circle_marker == 'on') ? 'checked' : ''; ?>>
					</td>
				</tr>
			</table>
			<p class="submit"><input type="submit" name="blk-save-gmail-settings" id="submit" class="button button-primary" value="Save Changes"></p>
		</form>
	</div>
	<?php
}