<?php

namespace MPHB\Emails\Templaters;

use \MPHB\Views;

class EmailTemplater extends AbstractTemplater {

	private $tagGroups = array();

	/**
	 *
	 * @var \MPHB\Entities\Booking
	 */
	private $booking;

	/**
	 *
	 * @var \MPHB\Entities\Payment
	 */
	private $payment;

	/**
	 *
	 * @param array $tagGroups
	 * @param bool $tagGroups['global'] Global site tags. Default TRUE.
	 * @param bool $tagGroups['booking'] Booking tags. Default FALSE.
	 * @param bool $tagGroups['user_confirmation'] User confirmation tags. Default FALSE.
	 * @param bool $tagGroups['user_cancellation'] User cancellation tags. Default FALSE.
	 * @param bool $tagGroups['payment'] Payment details tags. Default FALSE.
	 */
	public static function create( $tagGroups = array() ){

		$templater = new static();

		$templater->setTagGroups( $tagGroups );

		return $templater;
	}

	public function setTagGroups( $tagGroups ){
		$defaultTagGroups = array(
			'global'			 => true,
			'booking'			 => false,
			'user_confirmation'	 => false,
			'user_cancellation'	 => false,
			'payment'			 => false
		);

		$this->tagGroups = array_merge( $defaultTagGroups, $tagGroups );
	}

	/**
	 *
	 * @param array $tagGroups
	 */
	public function setupTags(){

		$tags = array();

		if ( $this->tagGroups['global'] ) {
			$this->_fillGlobalTags( $tags );
		}

		if ( $this->tagGroups['booking'] ) {
			$this->_fillBookingTags( $tags );
		}

		if ( $this->tagGroups['user_confirmation'] ) {
			$this->_fillUserConfirmationTags( $tags );
		}

		if ( $this->tagGroups['user_cancellation'] ) {
			$this->_fillUserCancellationTags( $tags );
		}

		if ( $this->tagGroups['payment'] ) {
			$this->_fillPaymentTags( $tags );
		}

		$tags = apply_filters( 'mphb_email_tags', $tags );

		foreach ( $tags as $tag ) {
			$this->addTag( $tag['name'], $tag['description'], $tag );
		}
	}

	private function _fillGlobalTags( &$tags ){
		$globalTags = array(
			array(
				'name'			 => 'site_title',
				'description'	 => __( 'Site title (set in Settings > General)', 'motopress-hotel-booking' ),
			)
		);

        $globalTags = apply_filters( 'mphb_email_global_tags', $globalTags );

		$tags = array_merge( $tags, $globalTags );
	}

	private function _fillBookingTags( &$tags ){
		$bookingTags	 = array(
			// Booking
			array(
				'name'			 => 'booking_id',
				'description'	 => __( 'Booking ID', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'booking_edit_link',
				'description'	 => __( 'Booking Edit Link', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'booking_total_price',
				'description'	 => __( 'Booking Total Price', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'booking_fee',
				'description'	 => __( 'Company fee amount', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'booking_owner_revenue',
				'description'	 => __( 'Owner potential earning', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'check_in_date',
				'description'	 => __( 'Check-in Date', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'check_out_date',
				'description'	 => __( 'Check-out Date', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'check_in_time',
				'description'	 => __( 'Check-in Time', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'check_out_time',
				'description'	 => __( 'Check-out Time', 'motopress-hotel-booking' ),
			),
			// Customer
			array(
				'name'			 => 'customer_first_name',
				'description'	 => __( 'Customer First Name', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'customer_last_name',
				'description'	 => __( 'Customer Last Name', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'customer_email',
				'description'	 => __( 'Customer Email', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'customer_phone',
				'description'	 => __( 'Customer Phone', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'customer_note',
				'description'	 => __( 'Customer Note', 'motopress-hotel-booking' ),
			),
			// Room Type
			array(
				'name'			 => 'reserved_rooms_details',
				'description'	 => __( 'Reserved Accommodations Details', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'property_contact',
				'description'	 => __( 'Show property contact', 'motopress-hotel-booking' ),
			),
			array(
				'name'			 => 'rvb_booking_link',
				'description'	 => __( 'Show booking link', 'motopress-hotel-booking' ),
			),
			/* array(
				'name'			 => 'rvb_user_login_info',
				'description'	 => __( 'Show user login info', 'motopress-hotel-booking' ),
			), */
		);

        $bookingTags = apply_filters( 'mphb_email_booking_tags', $bookingTags );

		$tags = array_merge( $tags, $bookingTags );
	}

	private function _fillUserConfirmationTags( &$tags ){
		$userConfirmationTags = array(
			array(
				'name'			 => 'user_confirm_link',
				'description'	 => __( 'Confirmation Link', 'motopress-hotel-booking' )
			),
			array(
				'name'			 => 'user_confirm_link_expire',
				'description'	 => __( 'Confirmation Link Expiration Time ( UTC )', 'motopress-hotel-booking' )
			)
		);

        $userConfirmationTags = apply_filters( 'mphb_email_user_confirmation_tags', $userConfirmationTags );

		$tags = array_merge( $tags, $userConfirmationTags );
	}

	private function _fillUserCancellationTags( &$tags ){
		$userCancellationTags	 = array(
			array(
				'name'			 => 'cancellation_details',
				'description'	 => __( 'Cancellation Details (if enabled)', 'motopress-hotel-booking' ),
			),
		);

        $userCancellationTags = apply_filters( 'mphb_email_user_cancellation_tags', $userCancellationTags );

		$tags = array_merge( $tags, $userCancellationTags );
	}

	private function _fillPaymentTags( &$tags ){
		$paymentTags = array(
			array(
				'name'			 => 'payment_amount',
				'description'	 => __( 'The total price of payment', 'motopress-hotel-booking' )
			),
			array(
				'name'			 => 'payment_id',
				'description'	 => __( 'The unique ID of payment', 'motopress-hotel-booking' )
			),
			array(
				'name'			 => 'payment_method',
				'description'	 => __( 'The method of payment', 'motopress-hotel-booking' )
			),
		);

        $paymentTags = apply_filters( 'mphb_email_payment_tags', $paymentTags );

		$tags = array_merge( $tags, $paymentTags );
	}

	/**
	 *
	 * @param \MPHB\Entities\Booking $booking
	 */
	public function setupBooking( $booking ){
		$this->booking = $booking;
	}

	/**
	 *
	 * @param \MPHB\Entities\Payment $payment
	 */
	public function setupPayment( $payment ){
		$this->payment = $payment;
	}

	/**
	 *
	 * @param array $match
	 * @param string $match[0] Tag
	 *
	 * @return string
	 */
	public function replaceTag( $match ){

		$tag = str_replace( '%', '', $match[0] );

		$replaceText = '';

		switch ( $tag ) {

			// Global
			case 'site_title':
				$replaceText = get_bloginfo( 'name' );
				break;
			case 'check_in_time':
				$replaceText = MPHB()->settings()->dateTime()->getCheckInTimeWPFormatted();
				break;
			case 'check_out_time':
				$replaceText = MPHB()->settings()->dateTime()->getCheckOutTimeWPFormatted();
				break;

			// Booking
			case 'booking_id':
				if ( isset( $this->booking ) ) {
					$replaceText = $this->booking->getId();
				}
				break;
			case 'booking_edit_link':
				if ( isset( $this->booking ) ) {
					$replaceText = mphb_get_edit_post_link_for_everyone( $this->booking->getId() );
				}
				break;
			case 'booking_total_price':
				if ( isset( $this->booking ) ) {
					ob_start();
					Views\BookingView::renderTotalPriceHTML( $this->booking );
					$replaceText = ob_get_clean();
				}
				break;
			case 'booking_fee':
				if ( isset( $this->booking ) ) {
					$total_price = $this->booking->getTotalPrice();
	
					$fee_percentage = get_option('rvb_company_fee');
					$fee = $total_price * $fee_percentage / 100;
					$replaceText = mphb_format_price( $fee );
				}
				break;
			case 'booking_owner_revenue':
				if ( isset( $this->booking ) ) {
					$total_price = $this->booking->getTotalPrice();
	
					$fee_percentage = get_option('rvb_company_fee');
					$replaceText = $total_price * $fee_percentage / 100;
					$revenue = $total_price - $fee;
					$replaceText = mphb_format_price( $revenue );
				}
				break;
			case 'check_in_date':
				if ( isset( $this->booking ) ) {
					ob_start();
					Views\BookingView::renderCheckInDateWPFormatted( $this->booking );
					$replaceText = ob_get_clean();
				}
				break;
			case 'check_out_date':
				if ( isset( $this->booking ) ) {
					ob_start();
					Views\BookingView::renderCheckOutDateWPFormatted( $this->booking );
					$replaceText = ob_get_clean();
				}
				break;
			case 'reserved_rooms_details':
				if ( isset( $this->booking ) ) {
					$replaceText = MPHB()->emails()->getReservedRoomsTemplater()->process( $this->booking );
				}
				break;

			// Customer
			case 'customer_first_name':
				if ( isset( $this->booking ) ) {
					$replaceText = $this->booking->getCustomer()->getFirstName();
				}
				break;
			case 'customer_last_name':
				if ( isset( $this->booking ) ) {
					$replaceText = $this->booking->getCustomer()->getLastName();
				}
				break;
			case 'customer_email':
				if ( isset( $this->booking ) ) {
					$replaceText = $this->booking->getCustomer()->getEmail();
				}
				break;
			case 'customer_phone';
				if ( isset( $this->booking ) ) {
					$replaceText = $this->booking->getCustomer()->getPhone();
				}
				break;
			case 'customer_note':
				if ( isset( $this->booking ) ) {
					$replaceText = $this->booking->getNote();
				}
				break;
			case 'user_confirm_link':
				if ( isset( $this->booking ) ) {
					$replaceText = MPHB()->userActions()->getBookingConfirmationAction()->generateLink( $this->booking );
				}
				break;
			case 'user_confirm_link_expire':
				if ( isset( $this->booking ) ) {
					$expireTime	 = $this->booking->retrieveExpiration( 'user' );
					$replaceText = date_i18n( MPHB()->settings()->dateTime()->getDateTimeFormatWP(), $expireTime );
				}
				break;
			case 'cancellation_details':
				if ( isset( $this->booking ) && MPHB()->settings()->main()->canUserCancelBooking() ) {
					$replaceText = MPHB()->emails()->getCancellationTemplater()->process( $this->booking );
				}
				break;

			// Payment
			case 'payment_amount':
				if ( isset( $this->payment ) ) {
					$amountAtts	 = array(
						'currency_symbol' => MPHB()->settings()->currency()->getBundle()->getSymbol( $this->payment->getCurrency() )
					);
					$replaceText = mphb_format_price( $this->payment->getAmount(), $amountAtts );
				}
				break;
			case 'payment_id':
				if ( isset( $this->payment ) ) {
					$replaceText = $this->payment->getId();
				}
				break;
			case 'payment_method':
				if ( isset( $this->payment ) ) {
					$gateway	 = MPHB()->gatewayManager()->getGateway( $this->payment->getGatewayId() );
					$replaceText = $gateway ? $gateway->getTitle() : '';
				}
				break;
			
			case 'property_contact':
				if ( isset( $this->booking ) ) {
					$replaceText = rvb_get_property_contact( $this->booking->getReservedRooms() );
				}
				break;
				
			case 'rvb_booking_link':
				if ( isset( $this->booking ) ) {
					$booking_key = get_post_meta($this->booking->getId(), 'mphb_key', true);
					$replaceText = get_home_url().'/?jlm='.$this->booking->getId().'&key='.str_replace('booking_','',$booking_key);
				}
				break;
			
			/* case 'rvb_user_login_info':
				if ( isset( $this->booking ) ) {
					$user_id = get_post_meta($this->booking->getId(), 'mphb_key', true);
					$replaceText = get_home_url().'/?jlm='.$this->booking->getId().'&key='.str_replace('booking_','',$booking_key);
				}
				break; */
				
				
			// Deprecated
		}

        /** @since 3.0.3 Has 3rd and 4th arguments - booking and payment. */
		$replaceText = apply_filters( 'mphb_email_replace_tag', $replaceText, $tag, $this->booking, $this->payment );

		return $replaceText;
	}

}
