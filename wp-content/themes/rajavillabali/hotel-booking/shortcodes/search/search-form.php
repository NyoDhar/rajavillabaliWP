<?php
/**
 * Available variables
 * - string $uniqid
 * - string $action Action for search form
 * - string $checkInDate
 * - string $checkOutDate
 * - int $adults
 * - int $children
 * - array $adultsList
 * - array $childrenList
 * - array $attributes [%Attribute name% => [%Term ID% => %Term title%]]
 */
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form method="GET" class="mphb_sc_search-form" action="<?php echo esc_attr( $action ); ?>">

	<?php
	/**
	 * @hooked \MPHB\Shortcodes\SearchShortcode::renderHiddenInputs - 10
	 */
	do_action( 'mphb_sc_search_render_form_top' );
	?>

	<p class="mphb_sc_search-check-in-date">
		<label for="<?php echo esc_attr( 'mphb_check_in_date-' . $uniqid ); ?>">
			<?php _e( 'Check-in', 'motopress-hotel-booking' ); ?>
			<abbr title="<?php printf( _x( 'Formatted as %s', 'Date format tip', 'motopress-hotel-booking' ), MPHB()->settings()->dateTime()->getDateFormatJS() ); ?>">*</abbr>
		</label>
		<br />
		<input
			id="<?php echo esc_attr( 'mphb_check_in_date-' . $uniqid ); ?>"
			data-datepick-group="<?php echo esc_attr( $uniqid ); ?>"
			value="<?php echo esc_attr( $checkInDate ); ?>"
			placeholder="<?php _e( 'Check-in Date', 'motopress-hotel-booking' ); ?>"
			required="required"
			type="text"
			name="mphb_check_in_date"
			class="mphb-datepick"
			autocomplete="off"
			/>
	</p>

	<p class="mphb_sc_search-check-out-date">
		<label for="<?php echo esc_attr( 'mphb_check_out_date-' . $uniqid ); ?>">
			<?php _e( 'Check-out', 'motopress-hotel-booking' ); ?>
			<abbr title="<?php printf( _x( 'Formatted as %s', 'Date format tip', 'motopress-hotel-booking' ), MPHB()->settings()->dateTime()->getDateFormatJS() ); ?>">*</abbr>
		</label>
		<br />
		<input
			id="<?php echo esc_attr( 'mphb_check_out_date-' . $uniqid ); ?>"
			data-datepick-group="<?php echo esc_attr( $uniqid ); ?>"
			value="<?php echo esc_attr( $checkOutDate ); ?>"
			placeholder="<?php esc_attr_e( 'Check-out Date', 'motopress-hotel-booking' ); ?>"
			required="required"
			type="text"
			name="mphb_check_out_date"
			class="mphb-datepick"
			autocomplete="off"
			/>
	</p>

	<?php if ( MPHB()->settings()->main()->isAdultsDisabledOrHidden() ) { ?>
		<input type="hidden" id="<?php echo esc_attr( 'mphb_adults-' . $uniqid ); ?>" name="mphb_adults" value="<?php echo esc_attr( MPHB()->settings()->main()->getMinAdults() ); ?>" />
	<?php } else { ?>
		<p class="mphb_sc_search-adults">
			<label for="<?php echo esc_attr( 'mphb_adults-' . $uniqid ); ?>">
				<?php
					if ( MPHB()->settings()->main()->isChildrenAllowed() ) {
						_e( 'Adults', 'motopress-hotel-booking' );
					} else {
						_e( 'Guests', 'motopress-hotel-booking' );
					}
				?>
			</label>
			<br />
			<select id="<?php echo esc_attr( 'mphb_adults-' . $uniqid ); ?>" name="mphb_adults" >
				<?php foreach ( $adultsList as $value ) { ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $adults, $value ); ?>><?php echo esc_html( $value ); ?></option>
				<?php } ?>
			</select>
		</p>
	<?php } ?>

	<?php if ( MPHB()->settings()->main()->isChildrenDisabledOrHidden() ) { ?>
		<input type="hidden" id="<?php echo esc_attr( 'mphb_children-' . $uniqid ); ?>" name="mphb_children" value="<?php echo esc_attr( MPHB()->settings()->main()->getMinChildren() ); ?>" />
	<?php } else { ?>
		<p class="mphb_sc_search-children">
			<label for="<?php echo esc_attr( 'mphb_children-' . $uniqid ); ?>">
				<?php
					$childrenAge = MPHB()->settings()->main()->getChildrenAgeText();
					if ( empty( $childrenAge ) ) {
						_e( 'Children', 'motopress-hotel-booking' );
					} else {
						printf( __( 'Children %s', 'motopress-hotel-booking' ), $childrenAge );
					}
				?>
			</label>
			<br />
			<select id="<?php echo esc_attr( 'mphb_children-' . $uniqid ); ?>" name="mphb_children">
				<?php foreach ( $childrenList as $value ) { ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $children, $value ); ?>><?php echo esc_html( $value ); ?></option>
				<?php } ?>
			</select>
		</p>
	<?php } ?>

	<?php do_action( 'mphb_sc_search_form_before_attributes' ); ?>

	<?php foreach ( $attributes as $attributeName => $terms ) { ?>
		<p class="<?php echo esc_attr( 'mphb_sc_search-' . $attributeName ); ?>">
			<label for="<?php echo esc_attr( 'mphb_' . $attributeName . '-' . $uniqid ); ?>">
				<?php echo esc_html( mphb_attribute_title( $attributeName ) ); ?>
			</label>
			<br />
			<!--<select id="<?php echo esc_attr( 'mphb_' . $attributeName . '-' . $uniqid ); ?>" name="<?php echo esc_attr( 'mphb_attributes[' . $attributeName . ']' ); ?>">
				<option value=""><?php echo mphb_attribute_default_text( $attributeName ); ?></option>
				<?php foreach ( $terms as $termId => $termLabel ) { ?>
					<option value="<?php echo esc_attr( $termId ); ?>"><?php echo esc_html( $termLabel ); ?></option>
				<?php } ?>
			</select>-->
			<?php
				$the_attributes = !empty($_COOKIE['mphb_attributes']) ? json_decode( stripslashes( $_COOKIE['mphb_attributes'] ), true ) : array();
				$locations_selected = !empty($the_attributes['location']) ? $the_attributes['location'] : array();
				
				/* $args = array(
					'taxonomy'		=> 'mphb_ra_'.$attributeName,
					'hide_empty'	=> false,
					'hierarchical'	=> true,
					//'name'			=> 'mphb_attributes['.$attributeName.']',
					//'id'				=> 'mphb_' . $attributeName . '-' . $uniqid,
					//'show_option_all'	=> '&nbsp;',
					'selected'			=> $location,
					'echo'				=> 0,
				);
				//wp_dropdown_categories($args);
				$select = wp_dropdown_categories($args);
				
				$replace = "<select name='mphb_attributes[$attributeName]' id='mphb_$attributeName-$uniqid' multiple='multiple' data-text='Location' >";
				$select  = preg_replace( '#<select([^>]*)>#', $replace, $select ); 
				echo $select; */
				
			?>
			
			<select name="mphb_attributes[<?php echo $attributeName; ?>]" id="mphb_<?php echo $attributeName .'-'. $uniqid; ?>" multiple="multiple" >

				<?php $locations = get_terms( array(
					'taxonomy'		=> 'mphb_ra_'.$attributeName,
					'hide_empty' 	=> false,
					'parent'		=> 0,
				) ); ?>

				<?php if ( is_array( $locations ) ) : ?>
					<?php foreach ( $locations as $location ) : ?>
						<option value="<?php echo esc_attr( $location->term_id ); ?>" <?php echo in_array($location->term_id, $locations_selected) ? 'selected' : ''; ?>><?php echo esc_html( $location->name ); ?></option>

						<?php $sublocations = get_terms( array(
							'taxonomy'		=> 'mphb_ra_'.$attributeName,
							'hide_empty'    => false,
							'parent'        => $location->term_id,
						) ); ?>

						<?php if ( is_array( $sublocations ) ) : ?>
							<?php foreach ( $sublocations as $sublocation ) : ?>
								<option value="<?php echo esc_attr( $sublocation->term_id ); ?>" <?php echo in_array($location->term_id, $locations_selected) ? 'selected' : ''; ?>>
									&raquo;&nbsp; <?php echo esc_html( $sublocation->name ); ?>
								</option>

								<?php $subsublocations = get_terms( array(
									'taxonomy'		=> 'mphb_ra_'.$attributeName,
									'hide_empty' 	=> false,
									'parent' 		=> $sublocation->term_id,
								) ); ?>

								<?php if ( is_array( $subsublocations ) ) : ?>
									<?php foreach ( $subsublocations as $subsublocation ) : ?>
										<option value="<?php echo esc_attr( $subsublocation->term_id ); ?>" <?php echo in_array($location->term_id, $locations_selected) ? 'selected' : ''; ?>>
											&nbsp;&nbsp;&nbsp;&raquo;&nbsp; <?php echo esc_html( $subsublocation->name ); ?>
										</option>
									<?php endforeach; ?>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</p>
	<?php } ?>

	<?php do_action( 'mphb_sc_search_form_before_submit_btn' ); ?>

	<p class="mphb_sc_search-submit-button-wrapper">
		<input type="submit" class="button" value="<?php _e( 'Search', 'motopress-hotel-booking' ); ?>"/>
	</p>

	<?php do_action( 'mphb_sc_search_form_bottom' ); ?>

</form>