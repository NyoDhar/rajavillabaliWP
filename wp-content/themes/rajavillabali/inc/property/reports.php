<?php
function rvb_reports(){
	add_menu_page('Reports', 'Reports', 'manage_categories', 'reports', 'financial_report' );
	add_submenu_page( 'reports', 'Financial Report', 'Financial Report', 'manage_categories', 'reports');
	add_submenu_page( 'reports', 'Rental Occupancy', 'Rental Occupancy', 'manage_categories', 'performance', 'rvb_performance');
	add_submenu_page( 'reports', 'Graphs', 'Graphs', 'manage_categories', 'graphs', 'rvb_graphs');
	/* add_menu_page('Booking Link', 'Booking Link', 'manage_categories', 'booking-link', 'rvbto_booking_link' );
	add_menu_page('Email Blast', 'Email Blast', 'manage_categories', 'rvb-email-blast', 'rvb_email_blast' ); */
}
add_action('admin_menu','rvb_reports');

function get_revenue($args = array()){
	$defaults = array(
					'revenues'			=> array('bookings', 'revenue_total'),
					'paged_bookings'	=> true,
					'date-from'			=> ( !empty($_GET['date-from']) ?  $_GET['date-from'] : ''),
					'date-until'		=> ( !empty($_GET['date-until']) ?  $_GET['date-until'] : ''),
					'accomodation_id'	=> ( !empty($_GET['accomodation_id']) ?  $_GET['accomodation_id'] : ''),
				);
	
	$args = wp_parse_args( $args, $defaults );
	$return = array();
	
	global $wpdb;
	$fee_percentage = get_option('rvb_company_fee');
	
	//SQL uses in both requests bookings revenue list and revenue total
	$join = ' INNER JOIN '.$wpdb->postmeta.' price ON ( price.post_id = booking.ID AND price.meta_key="mphb_total_price" ) ';
	$where = ' WHERE booking.post_type = "mphb_booking" AND booking.post_status = "confirmed" AND price.meta_value >= 1 ';
	
	
	if(!empty($args['date-from']) && !empty($args['date-until'])){
		$join .= ' INNER JOIN '.$wpdb->postmeta.' bdate ON ( bdate.post_id = booking.ID AND bdate.meta_key="mphb_check_in_date" ) ';
		$where .= ' AND CAST( bdate.meta_value as DATE ) BETWEEN CAST("'.$args['date-from'].'" as DATE ) AND  CAST("'.$args['date-until'].'" as DATE )';
	}
	
	if(!empty( $args['accomodation_id'] )){
		$join .= ' INNER JOIN '.$wpdb->posts.' reservedroom ON ( reservedroom.post_parent = booking.ID AND reservedroom.post_type="mphb_reserved_room" )
					INNER JOIN '.$wpdb->postmeta.' room ON ( room.post_id = reservedroom.ID AND room.meta_key="_mphb_room_id" )
					INNER JOIN '.$wpdb->postmeta.' accomodation ON ( accomodation.post_id = room.meta_value AND accomodation.meta_key="mphb_room_type_id" )';
		$where .= ' AND accomodation.meta_value = '.$args['accomodation_id'];
	}
	
	$wpdb->show_errors(); 
	
	//if bookings list with revenues are requested
	if( in_array('bookings', $args['revenues']) ){
		
		
		$select = 'SELECT booking.ID
					from '.$wpdb->posts.' booking';
		
		$select_count = 'SELECT count( booking.ID )
					from '.$wpdb->posts.' booking ';
					
		$limit = '';
		
		if($args['paged_bookings']){
			$paged = ( $_GET[ 'paged' ] ) ? absint( $_GET[ 'paged' ] ) : 1;
			$posts_per_page = get_option('posts_per_page');
			$offset = ( $paged - 1 ) * $posts_per_page;

			$limit = ' LIMIT '.$offset.', '.$posts_per_page;
			
			$sql_count = $select_count . $join . $where;
			$total_bookings = $wpdb->get_var($sql_count);
			$return['max_pages'] = ceil( $total_bookings / $posts_per_page );
			$return['paged'] = $paged;
		}
		
		$sql = $select . $join . $where . $limit;
		$return['bookings'] = $wpdb->get_results($sql);
		
	}
	
	//if revenue total are requested
	if( in_array('revenue_total', $args['revenues']) ){
		//Collecting total revenue
		$select_total_price = 'SELECT SUM( price.meta_value )
							FROM '.$wpdb->posts.' booking ';
		
		$join_paid_to_owner = ' INNER JOIN '.$wpdb->postmeta .' haspaid ON ( haspaid.post_id = booking.ID AND haspaid.meta_key = "owner_paid" ) ';
		$where_paid_to_owner = ' AND haspaid.meta_value="paid"';
		
		$join_owner_balance = ' LEFT JOIN '.$wpdb->postmeta .' haspaid ON ( haspaid.post_id = booking.ID AND haspaid.meta_key = "owner_paid" ) ';
		$where_owner_balance = ' AND haspaid.meta_value IS NULL';
		
		$gross_revenue = $wpdb->get_var( $select_total_price . $join . $where );
		$nett_revenue = $gross_revenue * $fee_percentage / 100;
		$owner_revenue = $gross_revenue - $nett_revenue;
		
		$paid_to_owner_gross = $wpdb->get_var( $select_total_price . $join .$join_paid_to_owner . $where .$where_paid_to_owner );
		$paid_to_owner_fee = $paid_to_owner_gross * $fee_percentage / 100;
		$paid_to_owner = $paid_to_owner_gross - $paid_to_owner_fee;
		
		$owner_balance_gross = $wpdb->get_var( $select_total_price . $join .$join_owner_balance . $where .$where_owner_balance );
		$owner_balance_fee = $owner_balance_gross * $fee_percentage / 100;
		$owner_balance = $owner_balance_gross - $owner_balance_fee;
		
		$return['gross_revenue'] = $gross_revenue;
		$return['nett_revenue'] = $nett_revenue;
		$return['owner_revenue'] = $owner_revenue;
		$return['paid_to_owner'] = $paid_to_owner;
		$return['owner_balance'] = $owner_balance;
		
	}
	
	if($wpdb->last_error !== '') :
		$wpdb->print_error();
	endif;
	
	return $return;
}

function render_revenue_table( $revenue_data ){
	?>
	<table class="wp-list-table widefat fixed striped posts finance-report">
		<thead>
			<tr>
				<th>Booking</th>
				<th>Guest</th>
				<th>Stay</th>
				<th>Accomodation</th>
				<th>Gross Revenue</th>
				<th>Nett Revenue</th>
				<th>Owner Revenue</th>
				<th>Paid to Owner</th>
			</tr>
		</thead>
		<?php
		
		if( !empty( $revenue_data['bookings'] ) ){
			$fee_percentage = get_option('rvb_company_fee');
			
			foreach( $revenue_data['bookings'] as $b ){
				//$bookings->the_post();
				$booking_id = $b->ID; //get_the_ID();
				
				$booking = MPHB()->getBookingRepository()->findById($booking_id);
				$reservedRooms	 = $booking->getReservedRooms();
				$accomodation_id = $reservedRooms[0]->getRoomTypeId();
				
				ob_start();
				\MPHB\Views\BookingView::renderCheckInDateWPFormatted( $booking );
				$check_in = ob_get_clean();
				
				ob_start();
				\MPHB\Views\BookingView::renderCheckOutDateWPFormatted( $booking );
				$check_out = ob_get_clean();
				
				
				$total_price = $booking->getTotalPrice();
				$fee = $total_price * $fee_percentage / 100;
				$potential_earn = $total_price - $fee;
				
				$is_paid = get_post_meta($booking_id, 'owner_paid', true);
		
				?>
				<tr>
					<td><a href="<?php echo get_edit_post_link( $booking_id ); ?>" target="_blank">#<?php echo $booking_id; ?></a></td>
					<td><?php echo $booking->getCustomer()->getFirstName()." ".$booking->getCustomer()->getLastName(); ?></td>
					<td><?php echo $check_in .' - '. $check_out; ?></td>
					<td><?php echo get_the_title( $accomodation_id ); ?></td>
					<td><?php echo mphb_format_price( $total_price ); ?></td>
					<td><?php echo mphb_format_price( $fee ); ?></td>
					<td><?php echo mphb_format_price( $potential_earn ); ?></td>
					<td>
						<?php 
							
							if($is_paid == 'paid'){
								?>
								<i class="fa fa-check paid" title="have been paid to owner"></i>
								<i class="fa fa-undo undo-owner-paid" data-bookingid="<?php echo $booking_id ?>" aria-hidden="true" title="Set back as not paid"></i>
								<?php
							}else{
								?>
									<input type="button" value="Set Paid" class="button primary set-owner-paid" data-bookingid="<?php echo $booking_id ?>">
								<?php
							}
						?>
					</td>
				</tr>
				<?php
			}
		}
		?>
	</table>
	<?php
		if(!empty($revenue_data['max_pages'])){
			//$_SERVER['REQUEST_URI'] = 'http://localhost/rajavillabali/wp-admin/admin.php?filter_report=Filter&page=reports';
			blk_pagination( $revenue_data['max_pages'] , $revenue_data['paged']);
		}
	?>
	<table class="finance-report total-view">
		<tr>
			<th>Gross Revenue</th>
			<td><?php echo mphb_format_price($revenue_data['gross_revenue']); ?></td>
		</tr>
		<tr>
			<th>Nett Revenue</th>
			<td><?php echo mphb_format_price($revenue_data['nett_revenue']); ?></td>
		</tr>
		<tr>
			<th>Owner Revenue</th>
			<td><?php echo mphb_format_price($revenue_data['owner_revenue']); ?></td>
		</tr>
		<tr>
			<th>Paid to Owner</th>
			<td><?php echo mphb_format_price($revenue_data['paid_to_owner']); ?></td>
		</tr>
		<tr>
			<th>Owner Balance</th>
			<td><?php echo mphb_format_price($revenue_data['owner_balance']); ?></td>
		</tr>
	</table>
	<?php
}

function financial_report(){
	/* $paged = ( $_GET[ 'paged' ] ) ? absint( $_GET[ 'paged' ] ) : 1;
	$posts_per_page = get_option('posts_per_page');
	$fee_percentage = get_option('rvb_company_fee');
	$offset = ( $paged - 1 ) * $posts_per_page;
	
	global $wpdb;
	$wpdb->show_errors(); 
	$select = 'SELECT booking.ID
				from '.$wpdb->posts.' booking';
	
	$select_count = 'SELECT count( booking.ID )
				from '.$wpdb->posts.' booking ';
				
	$join = ' INNER JOIN '.$wpdb->postmeta.' price ON ( price.post_id = booking.ID AND price.meta_key="mphb_total_price" ) ';
	
	$where = ' WHERE booking.post_type = "mphb_booking" AND booking.post_status = "confirmed" AND price.meta_value >= 1 ';
	
	$limit = ' LIMIT '.$offset.', '.$posts_per_page;
	
	
	if(!empty($_GET['date-from']) && !empty($_GET['date-until'])){
		$join .= ' INNER JOIN '.$wpdb->postmeta.' bdate ON ( bdate.post_id = booking.ID AND bdate.meta_key="mphb_check_in_date" ) ';
		$where .= ' AND CAST( bdate.meta_value as DATE ) BETWEEN CAST("'.$_GET['date-from'].'" as DATE ) AND  CAST("'.$_GET['date-until'].'" as DATE )';
	}
	
	if(!empty( $_GET['accomodation_id'] )){
		$join .= ' INNER JOIN '.$wpdb->posts.' reservedroom ON ( reservedroom.post_parent = booking.ID AND reservedroom.post_type="mphb_reserved_room" )
					INNER JOIN '.$wpdb->postmeta.' room ON ( room.post_id = reservedroom.ID AND room.meta_key="_mphb_room_id" )
					INNER JOIN '.$wpdb->postmeta.' accomodation ON ( accomodation.post_id = room.meta_value AND accomodation.meta_key="mphb_room_type_id" )';
		$where .= ' AND accomodation.meta_value = '.$_GET['accomodation_id'];
	}
		
	$sql = $select . $join . $where . $limit;
	$sql_count = $select_count . $join . $where;
	
	$bookings = $wpdb->get_results($sql);
	$total_bookings = $wpdb->get_var($sql_count);
	$max_pages = $total_bookings / $posts_per_page;


	//Collecting total revenue
	$select_total_price = 'SELECT SUM( price.meta_value )
						FROM '.$wpdb->posts.' booking ';
	
	$join_paid_to_owner = ' INNER JOIN '.$wpdb->postmeta .' haspaid ON ( haspaid.post_id = booking.ID AND haspaid.meta_key = "owner_paid" ) ';
	$where_paid_to_owner = ' AND haspaid.meta_value="paid"';
	
	$join_owner_balance = ' LEFT JOIN '.$wpdb->postmeta .' haspaid ON ( haspaid.post_id = booking.ID AND haspaid.meta_key = "owner_paid" ) ';
	$where_owner_balance = ' AND haspaid.meta_value IS NULL';
	
	$gross_revenue = $wpdb->get_var( $select_total_price . $join . $where );
	$nett_revenue = $gross_revenue * $fee_percentage / 100;
	$owner_revenue = $gross_revenue - $nett_revenue;
	
	$paid_to_owner_gross = $wpdb->get_var( $select_total_price . $join .$join_paid_to_owner . $where .$where_paid_to_owner );
	$paid_to_owner_fee = $paid_to_owner_gross * $fee_percentage / 100;
	$paid_to_owner = $paid_to_owner_gross - $paid_to_owner_fee;
	
	$owner_balance_gross = $wpdb->get_var( $select_total_price . $join .$join_owner_balance . $where .$where_owner_balance );
	$owner_balance_fee = $owner_balance_gross * $fee_percentage / 100;
	$owner_balance = $owner_balance_gross - $owner_balance_fee;
	
	if($wpdb->last_error !== '') :
		$wpdb->print_error();
	endif; */
	
	
	
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	?>
	<div class="wrap financial-report">
		<h1>Financial Report</h1>
		<div class="filters-export">
			<div class="row">
				<div class="col-sm-9">
					<form id="report-filter" method="GET" action="<?php echo $actual_link; ?>">
						<input name="accomodation" autocomplete="off" id="find-accomodation" type="text" value="<?php echo !empty($_GET['accomodation']) ? $_GET['accomodation'] : '' ?>" placeholder="All Accomodation">
						<input name="accomodation_id" type="hidden" value="<?php echo !empty($_GET['accomodation_id']) ? $_GET['accomodation_id'] : '' ?>">
						
						<input name="date-from" autocomplete="off" class="rvb-datepicker" type="text" value="<?php echo !empty($_GET['date-from']) ? $_GET['date-from'] : '' ?>" placeholder="Date From">
						<input name="date-until" autocomplete="off" class="rvb-datepicker" type="text" value="<?php echo !empty($_GET['date-until']) ? $_GET['date-until'] : '' ?>" placeholder="Date Until">
						
						<input type="submit" name="filter-report" id="submit" class="button button-primary" value="Filter">
						<input type="hidden" name="page" value="reports">
						<?php
						if($paged>1){
							?>
							<input type="hidden" name="paged" value="<?php echo $paged; ?>">
							<?php
						}
						?>
					</form>
				</div>
				<div class="col-sm-3">
					<form id="export" target="_blank" class="text-right">
						<input type="hidden" name="act" value="export-report">
						<input name="accomodation_id" type="hidden" value="<?php echo !empty($_GET['accomodation_id']) ? $_GET['accomodation_id'] : '' ?>">
						<input name="date-from" type="hidden" value="<?php echo !empty($_GET['date-from']) ? $_GET['date-from'] : '' ?>" >
						<input name="date-until" type="hidden" value="<?php echo !empty($_GET['date-until']) ? $_GET['date-until'] : '' ?>" >
						<input type="submit" class="button button-primary" value="export">
					</form>
				</div>
			</div>
		</div>
		<?php
			$revenue_data = get_revenue();
			render_revenue_table( $revenue_data );
		?>
		
	</div>
	<?php
}

add_action('wp_ajax_set_owner_paid', 'set_owner_paid');
function set_owner_paid(){
	$booking_id = $_POST['booking_id'];
	
	update_post_meta($booking_id, 'owner_paid', 'paid');
	
	?>
		<i class="fa fa-check paid" title="have been paid to owner"></i>
		<i class="fa fa-undo undo-owner-paid" data-bookingid="<?php echo $booking_id ?>" aria-hidden="true" title="Set back as not paid"></i>
	<?php
	
	
	wp_die();
}

add_action('wp_ajax_set_back_owner_not_paid', 'set_back_owner_not_paid');
function set_back_owner_not_paid(){
	$booking_id = $_POST['booking_id'];
	
	delete_post_meta($booking_id, 'owner_paid', 'paid');
	
	?>
		<input type="button" value="Set Paid" class="button primary set-owner-paid" data-bookingid="<?php echo $booking_id ?>">
	<?php
	
	
	wp_die();
}

add_action('wp_loaded', 'export_finance_report');
function export_finance_report(){
	if(!is_admin()) return;
	
	if(!empty($_GET['act']) && $_GET['act'] == 'export-report'){
		/* global $wpdb;
		$fee_percentage = get_option('rvb_company_fee');
		
		$select = 'SELECT booking.ID
				from '.$wpdb->posts.' booking';
	
		$select_count = 'SELECT count( booking.ID )
					from '.$wpdb->posts.' booking ';
					
		$join = ' INNER JOIN '.$wpdb->postmeta.' price ON ( price.post_id = booking.ID AND price.meta_key="mphb_total_price" ) ';
		
		$where = ' WHERE booking.post_type = "mphb_booking" AND booking.post_status = "confirmed" AND price.meta_value >= 1 ';

		
		
		if(!empty($_GET['date-from']) && !empty($_GET['date-until'])){
			$join .= ' INNER JOIN '.$wpdb->postmeta.' bdate ON ( bdate.post_id = booking.ID AND bdate.meta_key="mphb_check_in_date" ) ';
			$where .= ' AND CAST( bdate.meta_value as DATE ) BETWEEN CAST("'.$_GET['date-from'].'" as DATE ) AND  CAST("'.$_GET['date-until'].'" as DATE )';
		}
		
		if(!empty( $_GET['accomodation_id'] )){
			$join .= ' INNER JOIN '.$wpdb->posts.' reservedroom ON ( reservedroom.post_parent = booking.ID AND reservedroom.post_type="mphb_reserved_room" )
						INNER JOIN '.$wpdb->postmeta.' room ON ( room.post_id = reservedroom.ID AND room.meta_key="_mphb_room_id" )
						INNER JOIN '.$wpdb->postmeta.' accomodation ON ( accomodation.post_id = room.meta_value AND accomodation.meta_key="mphb_room_type_id" )';
			$where .= ' AND accomodation.meta_value = '.$_GET['accomodation_id'];
		}
		
		$bookings = $wpdb->get_results( $select . $join . $where );
		
		//Collecting total revenue
		$select_total_price = 'SELECT SUM( price.meta_value )
							FROM '.$wpdb->posts.' booking ';
		
		$join_paid_to_owner = ' INNER JOIN '.$wpdb->postmeta .' haspaid ON ( haspaid.post_id = booking.ID AND haspaid.meta_key = "owner_paid" ) ';
		$where_paid_to_owner = ' AND haspaid.meta_value="paid"';
		
		$join_owner_balance = ' LEFT JOIN '.$wpdb->postmeta .' haspaid ON ( haspaid.post_id = booking.ID AND haspaid.meta_key = "owner_paid" ) ';
		$where_owner_balance = ' AND haspaid.meta_value IS NULL';
		
		$gross_revenue = $wpdb->get_var( $select_total_price . $join . $where );
		$nett_revenue = $gross_revenue * $fee_percentage / 100;
		$owner_revenue = $gross_revenue - $nett_revenue;
		
		$paid_to_owner_gross = $wpdb->get_var( $select_total_price . $join .$join_paid_to_owner . $where .$where_paid_to_owner );
		$paid_to_owner_fee = $paid_to_owner_gross * $fee_percentage / 100;
		$paid_to_owner = $paid_to_owner_gross - $paid_to_owner_fee;
		
		$owner_balance_gross = $wpdb->get_var( $select_total_price . $join .$join_owner_balance . $where .$where_owner_balance );
		$owner_balance_fee = $owner_balance_gross * $fee_percentage / 100;
		$owner_balance = $owner_balance_gross - $owner_balance_fee; */
		
		$revenue_data = get_revenue(array(
										'paged_bookings'	=> false,
									));
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=Finance Report.xls");
		
		?>
		<table class="wp-list-table widefat fixed striped posts finance-report" border="1">
			<thead>
				<tr>
					<th>Booking</th>
					<th>Guest</th>
					<th>Stay</th>
					<th>Accomodation</th>
					<th>Gross Revenue</th>
					<th>Nett Revenue</th>
					<th>Owner Revenue</th>
					<th>Paid to Owner</th>
				</tr>
			</thead>
			<?php
			
			if( !empty( $revenue_data['bookings'] ) ){
				$fee_percentage = get_option('rvb_company_fee');
				
				foreach( $revenue_data['bookings'] as $b ){
					//$bookings->the_post();
					$booking_id = $b->ID; //get_the_ID();
					
					$booking = MPHB()->getBookingRepository()->findById($booking_id);
					$reservedRooms	 = $booking->getReservedRooms();
					$accomodation_id = $reservedRooms[0]->getRoomTypeId();
					
					ob_start();
					\MPHB\Views\BookingView::renderCheckInDateWPFormatted( $booking );
					$check_in = ob_get_clean();
					
					ob_start();
					\MPHB\Views\BookingView::renderCheckOutDateWPFormatted( $booking );
					$check_out = ob_get_clean();
					
					$total_price = $booking->getTotalPrice();
					$fee = $total_price * $fee_percentage / 100;
					$potential_earn = $total_price - $fee;
					
					$is_paid = get_post_meta($booking_id, 'owner_paid', true);
					
			
					?>
					<tr>
						<td>#<?php echo $booking_id; ?></td>
						<td><?php echo $booking->getCustomer()->getFirstName()." ".$booking->getCustomer()->getLastName(); ?></td>
						<td><?php echo $check_in .' - '. $check_out; ?></td>
						<td><?php echo get_the_title( $accomodation_id ); ?></td>
						<td><?php echo mphb_format_price( $total_price ); ?></td>
						<td><?php echo mphb_format_price( $fee ); ?></td>
						<td><?php echo mphb_format_price( $potential_earn ); ?></td>
						<td>
							<?php
								if($is_paid == 'paid'){
									echo 'Paid';
								}
							?>
						</td>
					</tr>
					<?php
				}
			}
			?>
		</table>
<br>
		<table class="finance-report total-view" border="1">
			<tr>
				<th>Gross Revenue</th>
				<td><?php echo mphb_format_price($revenue_data['gross_revenue']); ?></td>
			</tr>
			<tr>
				<th>Nett Revenue</th>
				<td><?php echo mphb_format_price($revenue_data['nett_revenue']); ?></td>
			</tr>
			<tr>
				<th>Owner Revenue</th>
				<td><?php echo mphb_format_price($revenue_data['owner_revenue']); ?></td>
			</tr>
			<tr>
				<th>Paid to Owner</th>
				<td><?php echo mphb_format_price($revenue_data['paid_to_owner']); ?></td>
			</tr>
			<tr>
				<th>Owner Balance</th>
				<td><?php echo mphb_format_price($revenue_data['owner_balance']); ?></td>
			</tr>
		</table>
		<?php
		
		exit();
	}
}

function rvb_performance(){
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$date_from = $_GET['date-from'];
	$date_until = $_GET['date-until'];
	
	if(empty($date_from) && empty($date_until)){
		$date_now = new DateTime();
		$date_until = $date_now->format('Y-m-d');
		
		$date_now->sub(new DateInterval('P30D'));
		$date_from = $date_now->format('Y-m-d');
		
	}

	?>
	<div class="wrap performance-report">
		<h1>Rental Occupancy</h1>
		<div class="filters-export">
			<div class="row">
				<div class="col-sm-12">
					<form id="report-filter" method="GET" action="<?php echo $actual_link; ?>">
						<input name="accomodation" autocomplete="off" id="find-accomodation" type="text" value="<?php echo !empty($_GET['accomodation']) ? $_GET['accomodation'] : '' ?>" placeholder="All Accomodation">
						<input name="accomodation_id" type="hidden" value="<?php echo !empty($_GET['accomodation_id']) ? $_GET['accomodation_id'] : '' ?>">
						
						<input name="date-from" autocomplete="off" class="rvb-datepicker" type="text" value="<?php echo $date_from ?>" placeholder="Date From">
						<input name="date-until" autocomplete="off" class="rvb-datepicker" type="text" value="<?php echo $date_until; ?>" placeholder="Date Until">
						
						<input type="submit" name="filter-report" id="submit" class="button button-primary" value="Filter">
						<input type="hidden" name="page" value="performance">
						<?php
						if($paged>1){
							?>
							<input type="hidden" name="paged" value="<?php echo $paged; ?>">
							<?php
						}
						?>
					</form>
				</div>
				
			</div>
		</div>
		
		<?php
		//global $wpdb;
		//$wpdb->show_errors();
		$paged = ( $_GET[ 'paged' ] ) ? absint( $_GET[ 'paged' ] ) : 1;
		$args = array(
					'post_type'			=> 'mphb_room_type',
					'posts_per_page'	=> get_option('posts_per_page'),
					'paged'				=> $paged,
				);
		
		if(!empty($_GET['accomodation_id'])){
			$args['p'] = $_GET['accomodation_id'];
		}
		
		$accomodations = new WP_Query($args);
		
		?>
		<table class="wp-list-table widefat fixed striped posts performance-report">
			<thead>
				<tr>
					<th>Accomodation</th>
					<th>Rental Occupancy</th>
					<th>Nights booked</th>
				</tr>
			</thead>
			<?php
			
			if($accomodations->have_posts()){
				while($accomodations->have_posts()){
					$accomodations->the_post();
					$accomodation_id = get_the_ID();
					$occupancy = get_rental_occupancy($accomodation_id, $_GET['date-from'], $_GET['date-until']);
					?>
					<tr>
						<td><a href="<?php echo get_edit_post_link($accomodation_id) ?>" target="_blank"><?php  ?><?php the_title() ?></a></td>
						<td><?php echo $occupancy['occupancy_rate']; ?>%</td>
						<td><?php echo $occupancy['nights_booked']; ?></td>
						
					</tr>
					<?php
				}
				
				
			}
			?>
		</table>
		<?php
			blk_pagination($accomodations->max_num_pages, $paged);
			wp_reset_query();
		?>
	</div>
	<?php
	/* if($wpdb->last_error !== '') :
		$wpdb->print_error();
	endif; */
}

function get_rental_occupancy($accomodation_id, $date_from = '', $date_until=''){
	global $wpdb;
	
	if(empty($accomodation_id)) return 0;
	
	if(empty($date_from) && empty($date_until)){
		$date_now = new DateTime();
		$date_until = $date_now->format('Y-m-d');
		
		$date_now->sub(new DateInterval('P30D'));
		$date_from = $date_now->format('Y-m-d');
	}
	
	
	$select = 'SELECT checkin.meta_value as check_in, checkout.meta_value as check_out
				FROM '.$wpdb->posts.' booking';
				
	$join = ' INNER JOIN '.$wpdb->postmeta.' price ON ( price.post_id = booking.ID AND price.meta_key="mphb_total_price" ) 
				INNER JOIN '.$wpdb->postmeta .' checkin ON ( checkin.post_id = booking.ID AND checkin.meta_key="mphb_check_in_date" )
				INNER JOIN '.$wpdb->postmeta .' checkout ON ( checkout.post_id = booking.ID AND checkout.meta_key="mphb_check_out_date" )
				
				INNER JOIN '.$wpdb->posts.' reservedroom ON ( reservedroom.post_parent = booking.ID AND reservedroom.post_type="mphb_reserved_room" )
				INNER JOIN '.$wpdb->postmeta.' room ON ( room.post_id = reservedroom.ID AND room.meta_key="_mphb_room_id" )
				INNER JOIN '.$wpdb->postmeta.' accomodation ON ( accomodation.post_id = room.meta_value AND accomodation.meta_key="mphb_room_type_id" )
			';
				
	$where = ' WHERE booking.post_type = "mphb_booking" AND booking.post_status = "confirmed" AND price.meta_value >= 1 
				AND 
				(
					CAST( checkin.meta_value as DATE ) BETWEEN CAST("'.$date_from.'" as DATE ) AND  CAST("'.$date_until.'" as DATE )
					OR
					CAST( checkout.meta_value as DATE ) BETWEEN CAST("'.$date_from.'" as DATE ) AND  CAST("'.$date_until.'" as DATE )
				)
				AND accomodation.meta_value = '.$accomodation_id;
				
	$wpdb->show_errors();
	
	$booked_dates = $wpdb->get_results( $select . $join . $where );
	
	if($wpdb->last_error !== '') :
		$wpdb->print_error();
	endif;
	
	$nights_booked = 0;
	
	$date1=date_create($date_from);
	$date2=date_create($date_until);
	$diff=date_diff($date1,$date2);
	$total_nights = $diff->format("%a");
	
	foreach($booked_dates as $bd){
		$date_start = $date_from > $bd->check_in ? $date_from : $bd->check_in;
		$date_end = $date_until < $bd->check_out ? $date_until : $bd->check_out;
		
		$diff=date_diff( date_create( $date_start ) , date_create( $date_end ) );
		/* $period = new DatePeriod(
				 new DateTime( $date_start ),
				 new DateInterval('P1D'),
				 new DateTime( $date_end )
			); */
		
		$nights_booked += $diff->format("%a");
	}
	
	$occupancy_rate = $nights_booked / $total_nights * 100;
	
	/* echo 'Total Nights: ' . $total_nights .'<br>';
	echo 'Nights Booked: ' . $nights_booked .'<br>';
	echo 'occupancy_rate: ' . $occupancy_rate .'<br>'; */
	
	return array(
				'nights_booked'		=> $nights_booked,
				'occupancy_rate'	=> round( $occupancy_rate ),
			);
}

function rvb_graphs(){
	?>
	<div class="wrap graph-report">
		
		<div id="popular-area-wrapper" class="report-container">
			<div class="report-head">
				<h2>Area Popularity</h2>
				<?php
					$until = new DateTime('now');
					$from = new DateTime('now');
					$from->sub(new DateInterval('P3M'));

				?>
				<div class="report-control">
					<span class="date-box">
						<input type="text" class="rvb-datepicker" value="<?php echo $from->format('Y-m-d') ?>" id="pa-from">
					</span>
					to
					<span class="date-box">
						<input type="text" class="rvb-datepicker" value="<?php echo $until->format('Y-m-d') ?>" id="pa-until">
					</span>
					
					<span class="button button-primary" id="popular-area-stat-submit">Submit</span>
				</div>
			</div>
			<div class="graph-container">
				<canvas id="popular-area-bar"></canvas>
			</div>
		</div>
		
		<div id="popular-property-wrapper" class="report-container">
			<div class="report-head">
				<h2>Property Popularity</h2>
				<?php
					$until = new DateTime('now');
					$from = new DateTime('now');
					$from->sub(new DateInterval('P3M'));

				?>
				<div class="report-control">
					<span class="date-box">
						<input type="text" class="rvb-datepicker" value="<?php echo $from->format('Y-m-d') ?>" id="pp-from">
					</span>
					to
					<span class="date-box">
						<input type="text" class="rvb-datepicker" value="<?php echo $until->format('Y-m-d') ?>" id="pp-until">
					</span>
					
					<span class="button button-primary" id="popular-property-stat-submit">Submit</span>
				</div>
			</div>
			<div class="graph-container">
				<canvas id="popular-property-bar"></canvas>
			</div>
		</div>
		
		<div id="sales-achievement-wrapper" class="report-container">
			<div class="report-head">
				<h2>Revenue</h2>
				
				<div class="report-control">
					<select id="sa-year">
						<?php
							for($i=date('Y'); $i>=2018; $i--){
								?>
								<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
								<?php
							}
						?>
					</select>
					<select id="sa-type">
						<option value="gross">Gross</option>
						<option value="nett">Nett</option>
					</select>
					<span class="button button-primary" id="sales-achievement-submit">Submit</span>
				</div>
			</div>
			<div class="graph-container">
				<canvas id="sales-achievement-graph"></canvas>
			</div>
		</div>
	</div>
	<?php
}

function get_popular_area_data($atts){
	global $wpdb;
	
	$amont_ago = new DateTime();
	$amont_ago->sub(new DateInterval('P3M'));
	
	$args = shortcode_atts( array(
			'from' 			=> $amont_ago->format('Y-m-d'),
			'until' 		=> date('Y-m-d'),
		), $atts );
		
	//$wpdb->show_errors();
	
	$areas = $wpdb->get_results('SELECT term.name as name, term.term_id
								FROM '.$wpdb->term_taxonomy.' cat
								INNER JOIN '.$wpdb->terms.' term ON (term.term_id = cat.term_id)
								WHERE cat.taxonomy="mphb_ra_location"
							');
	
	$popular_areas = array();
	$data_check = array();
	
	if($areas){
		foreach($areas as $area){
			$color[0] = rand(0, 255);
			$color[1] = rand(0, 255);
			$color[2] = rand(0, 255);
			
			$sql = 'SELECT count( booking.ID )
					from '.$wpdb->posts.' booking
					
					INNER JOIN '.$wpdb->postmeta.' price ON ( price.post_id = booking.ID AND price.meta_key="mphb_total_price" ) 
					INNER JOIN '.$wpdb->postmeta.' bdate ON ( bdate.post_id = booking.ID AND bdate.meta_key="mphb_check_in_date" )
					
					INNER JOIN '.$wpdb->posts.' reservedroom ON ( reservedroom.post_parent = booking.ID AND reservedroom.post_type="mphb_reserved_room" )
					INNER JOIN '.$wpdb->postmeta.' room ON ( room.post_id = reservedroom.ID AND room.meta_key="_mphb_room_id" )
					INNER JOIN '.$wpdb->postmeta.' accomodation ON ( accomodation.post_id = room.meta_value AND accomodation.meta_key="mphb_room_type_id" )
					
					INNER JOIN '.$wpdb->term_relationships.' tr ON ( tr.object_id = accomodation.meta_value AND tr.term_taxonomy_id = '.$area->term_id.' )
					
					WHERE booking.post_type = "mphb_booking" AND booking.post_status = "confirmed" AND price.meta_value >= 1 
						AND CAST( bdate.meta_value as DATE ) BETWEEN CAST("'.$args['from'].'" as DATE ) AND  CAST("'.$args['until'].'" as DATE )';
					
					
			$count = $wpdb->get_var($sql);
			
			
			if($count>0){
				$popular_areas['labels'][]	= $area->name;
				$popular_areas['count'][]	= $count;
				$popular_areas['bgcolor'][]	= 'rgba('.$color[0].', '.$color[1].', '.$color[2].', 0.5)';
				$popular_areas['bordercolor'][]	= 'rgba('.$color[0].', '.$color[1].', '.$color[2].', 0.5)';
				$data_check[$area->name] = $count;
			}
		}
	}
	
	/* if($wpdb->last_error !== ''){
		$wpdb->print_error();
	} */
	
	//var_dump( $data_check );
	
	array_multisort($popular_areas['count'],SORT_DESC,SORT_NUMERIC, $popular_areas['labels'], SORT_ASC, $popular_areas['bgcolor'], SORT_ASC, $popular_areas['bordercolor'], SORT_ASC);
	
	$data['labels'] = !empty($popular_areas['labels']) ? $popular_areas['labels'] : array();
	$data['dataset'][] = array(
						'label'				=> 'Area Popularity',
						'backgroundColor'	=> $popular_areas['bgcolor'],
						'borderColor'		=> $popular_areas['bordercolor'],
						'borderWidth'		=> 1,
						'data'				=> !empty($popular_areas['count']) ? $popular_areas['count'] : array(),
					);
	
	//arsort($data_check);
	//$popular_areas['data_check'] = $data_check;
	return $data;
}

add_action('wp_ajax_get_popular_area_stat_data', 'get_popular_area_data_ajax');
function get_popular_area_data_ajax(){
	$args = array();
	
	if(!empty($_POST['from'])){
		$args['from'] = $_POST['from'];
	}
	
	if(!empty($_POST['until'])){
		$args['until'] = $_POST['until'];
	}

		
	$data = get_popular_area_data($args);
	if($data){
		echo json_encode($data);
	}else{
		echo 0;
	}
	
	wp_die();
}

function get_popular_property_data($atts){
	global $wpdb;
	
	$amont_ago = new DateTime();
	$amont_ago->sub(new DateInterval('P3M'));
	
	$args = shortcode_atts( array(
			'from' 			=> $amont_ago->format('Y-m-d'),
			'until' 		=> date('Y-m-d'),
		), $atts );
		
	//$wpdb->show_errors();
	
	$popular_areas = array();
	$data_check = array();
		
	$sql = 'SELECT villa.post_title, ( SELECT count( booking.ID )
						from '.$wpdb->posts.' booking
						
						INNER JOIN '.$wpdb->postmeta.' price ON ( price.post_id = booking.ID AND price.meta_key="mphb_total_price" ) 
						INNER JOIN '.$wpdb->postmeta.' bdate ON ( bdate.post_id = booking.ID AND bdate.meta_key="mphb_check_in_date" )
						
						INNER JOIN '.$wpdb->posts.' reservedroom ON ( reservedroom.post_parent = booking.ID AND reservedroom.post_type="mphb_reserved_room" )
						INNER JOIN '.$wpdb->postmeta.' room ON ( room.post_id = reservedroom.ID AND room.meta_key="_mphb_room_id" )
						INNER JOIN '.$wpdb->postmeta.' accomodation ON ( accomodation.post_id = room.meta_value AND accomodation.meta_key="mphb_room_type_id" )
						
						WHERE booking.post_type = "mphb_booking" AND booking.post_status = "confirmed" AND price.meta_value >= 1 
							AND CAST( bdate.meta_value as DATE ) BETWEEN CAST("'.$args['from'].'" as DATE ) AND  CAST("'.$args['until'].'" as DATE )
							AND accomodation.meta_value = villa.ID ) as count
			FROM '.$wpdb->posts.' villa
			WHERE villa.post_type="mphb_room_type" and villa.post_status="publish"
			LIMIT 10 ';
			
	$results = $wpdb->get_results($sql);

	
	foreach($results as $r){
		$color[0] = rand(0, 255);
		$color[1] = rand(0, 255);
		$color[2] = rand(0, 255);
		
		
		if($r->count > 0){
			$popular_areas['labels'][]	= $r->post_title;
			$popular_areas['count'][]	= $r->count;
			$popular_areas['bgcolor'][]	= 'rgba('.$color[0].', '.$color[1].', '.$color[2].', 0.5)';
			$popular_areas['bordercolor'][]	= 'rgba('.$color[0].', '.$color[1].', '.$color[2].', 0.5)';
			$data_check[$area->name] = $r->count;
		}
	}
	
	/* if($wpdb->last_error !== ''){
		$wpdb->print_error();
	} */
	
	//var_dump( $data_check );
	
	array_multisort($popular_areas['count'],SORT_DESC,SORT_NUMERIC, $popular_areas['labels'], SORT_ASC, $popular_areas['bgcolor'], SORT_ASC, $popular_areas['bordercolor'], SORT_ASC);
	
	$data['labels'] = !empty($popular_areas['labels']) ? $popular_areas['labels'] : array();
	$data['dataset'][] = array(
						'label'				=> 'Property Popularity',
						'backgroundColor'	=> $popular_areas['bgcolor'],
						'borderColor'		=> $popular_areas['bordercolor'],
						'borderWidth'		=> 1,
						'data'				=> !empty($popular_areas['count']) ? $popular_areas['count'] : array(),
					);
	
	//arsort($data_check);
	//$popular_areas['data_check'] = $data_check;
	return $data;
}

add_action('wp_ajax_get_popular_property_stat_data', 'get_popular_property_data_ajax');
function get_popular_property_data_ajax(){
	$args = array();

	
	if(!empty($_POST['from'])){
		$args['from'] = $_POST['from'];
	}
	
	if(!empty($_POST['until'])){
		$args['until'] = $_POST['until'];
	}

		
	$data = get_popular_property_data($args);
	if($data){
		echo json_encode($data);
	}else{
		echo 0;
	}
	
	wp_die();
}

function get_sales_achievement_graph_data($year=''){
	$year = !empty($year) ? $year : date('Y');
	global $wpdb;
	//$wpdb->show_errors();

	$data = array();
	
	$data['labels'] = array('Jan','Feb','Mar','Apr','Mei','Jun','July','Aug','Sept','Oct','Nov','Dec');
	$total_month = date('n');

		/* $sql = 'SELECT SUM( amount_paid * rate_to_idr ) as received, MONTH(payment_date) as month
				FROM '.$wpdb->prefix.'payment_plan pp
				INNER JOIN '.$wpdb->posts.' inquiry ON (inquiry.ID=pp.inquiry_id)
				WHERE YEAR(payment_date) = '.$year.'
				GROUP BY month
			'; */
		$sql = 'SELECT SUM( price.meta_value ) as received, MONTH( booking.post_date ) as month
					from '.$wpdb->posts.' booking
					
					INNER JOIN '.$wpdb->postmeta.' price ON ( price.post_id = booking.ID AND price.meta_key="mphb_total_price" )
					
					WHERE booking.post_type = "mphb_booking" AND booking.post_status = "confirmed" AND price.meta_value >= 1 
						AND YEAR( booking.post_date ) = '.$year.'
					GROUP BY month';

		$result = $wpdb->get_results($sql);
		
		//var_dump($result);
		
		$the_data = array();
		for($i=count($the_data); $i<$total_month; $i++){
			$the_data[] = 0;
		}
		
		$fee = 1;
		//jika tipe revenue yg di request adalah nett, maka tampilkan total Fee
		if($_POST['type'] == 'nett'){
			$fee = get_option('rvb_company_fee') / 100;
		}
		
		foreach($result as $r){
			$the_data[($r->month - 1)] = $r->received * $fee;
		}
		
		
		$color[0] = rand(0, 255);
		$color[1] = rand(0, 255);
		$color[2] = rand(0, 255);
		
		$data['dataset'][] = array(
						'label'				=> 'Revenue',
						'backgroundColor'	=> 'rgba('.$color[0].', '.$color[1].', '.$color[2].', 0.5)',
						'borderColor'		=> 'rgb('.$color[0].', '.$color[1].', '.$color[2].')',
						'borderWidth'		=> 1,
						'data'				=> $the_data,
						'fill'				=> false,
					);

	return $data;
}

add_action('wp_ajax_get_sales_achievement_graph_data', 'get_sales_achievement_graph_data_ajax');
function get_sales_achievement_graph_data_ajax(){
	$result = get_sales_achievement_graph_data($_POST['year']);
	
	if($result){
		echo json_encode($result);
	}else{
		echo 0;
	}
	
	wp_die();
}