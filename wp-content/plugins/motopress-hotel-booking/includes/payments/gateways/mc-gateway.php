<?php

namespace MPHB\Payments\Gateways;

use \MPHB\Admin\Groups;
use \MPHB\Admin\Fields;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class McGateway extends Gateway {
	
	protected $merchant_id;
	protected $verify_key;
	
	protected function setupProperties(){
		parent::setupProperties();
		$this->adminTitle = __( 'MC Payment', 'motopress-hotel-booking' );
	}

	protected function initDefaultOptions(){
		$defaults = array(
			'title'			 => __( 'Mc Payment', 'motopress-hotel-booking' ),
			'description'	 => '',
			'enabled'		 => false,
		);
		return array_merge( parent::initDefaultOptions(), $defaults );
	}

	protected function initId(){
		return 'mcpayment';
	}

	public function processPayment( \MPHB\Entities\Booking $booking, \MPHB\Entities\Payment $payment ){
		$url = $this->getPaymentUrl( $booking, $payment );

		// Redirect to paypal checkout
		wp_redirect( $url );
		exit;
	}
	
	public function getPaymentUrl( $booking, $payment ){
		$paypalArgs = http_build_query( $this->getRequestArgs( $booking, $payment ), '', '&' );

		if ( $this->isSandbox ) {
			$url = 'https://mcbill.sandbox.id.mcpayment.net/pay/'.$this->merchant_id.'/?' . $paypalArgs;
		} else {
			$url = 'https://mcbill.mcpayment.co.id/pay/'.$this->merchant_id.'/?' . $paypalArgs;
		}

		return $url;
	}

	/**
	 *
	 * @param \MPHB\Entities\Booking $booking
	 * @param \MPHB\Entities\Payment $payment
	 * @return string
	 */
	public function getRequestArgs( $booking, $payment ){
		
		$vcode = md5( $booking->calcDepositAmount() & $this->merchant_id & $payment->getKey() & $this->verify_key );
		
		$args = array(
			'returnurl'		 => esc_url_raw( MPHB()->settings()->pages()->getPaymentSuccessPageUrl( $booking ) ),
			//'cancel_return'	 => esc_url_raw( MPHB()->settings()->pages()->getPaymentFailedPageUrl( $booking ) ),
			//'bn'			 => 'MPHB_BuyNow', //  build notation
			'orderid'		 => $payment->getKey(),
			//'custom'		 => $payment->getId(),
			//'cbt'			 => get_bloginfo( 'name' ), // Return to Merchant button text
			//'no_shipping'	 => '1', // Do not prompt buyers for a shipping address.
			//'no_note'		 => '1', // Do not prompt buyers to include a note // Deprecated
			'bill_desc'		=> '',
			'vcode'			=> $vcode,
			'cur'			=> $payment->getCurrency(),
			'langcode'		=> 'en',
		);

		$args = array_merge( $args, $this->getBillingInfoArgs( $booking ), $this->getItemArgs( $booking ) );

		return $args;
	}

	/**
	 *
	 * @param \MPHB\Entities\Booking $booking
	 * @return array
	 */
	private function getBillingInfoArgs( $booking ){

		$fields = array(
			'country'	 => $booking->getCustomer()->getCountry(), // needs 2-character IS0-3166-1 country codes not free field
//			'state'		 => $booking->getCustomer()->getState(), // needs 2-character state codes
			//'city'		 => $booking->getCustomer()->getCity(),
			//'address1'	 => $booking->getCustomer()->getAddress1(),
			//'zip'		 => $booking->getCustomer()->getZip(),
			'bill_email'		 => $booking->getCustomer()->getEmail(),
			'bill_name' => $booking->getCustomer()->getFirstName() . ' ' . $booking->getCustomer()->getLastName(),
			'bill_mobile'	=> '',
			//'last_name'	 => $booking->getCustomer()->getLastName(),
		);

		// remove empty fields
		$fields = array_filter( $fields );

		return $fields;
	}

	/**
	 *
	 * @param \MPHB\Entities\Booking $booking
	 * @return array
	 */
	public function getItemArgs( $booking ){
		$itemName = $this->generateItemName( $booking );

		return array(
			//'item_name'	 => $itemName,
			'amount'	 => $booking->calcDepositAmount()
		);
	}
	
	public function registerOptionsFields( &$subTab ){
		parent::registerOptionsFields( $subTab );
		$group = new Groups\SettingsGroup( "mphb_payments_{$this->id}_group2", '', $subTab->getOptionGroupName() );

		$groupFields = array(
			Fields\FieldFactory::create( "mphb_payment_gateway_{$this->id}_merchant_id", array(
				'type'		 => 'text',
				'label'		 => __( 'MC Merchant ID', 'motopress-hotel-booking' ),
				'default'	 => $this->getDefaultOption( 'merchant_id' )
			) ),
			Fields\FieldFactory::create( "mphb_payment_gateway_{$this->id}_verify_key", array(
				'type'		 => 'text',
				'label'		 => __( 'MC Verify Key', 'motopress-hotel-booking' ),
				'default'	 => $this->getDefaultOption( 'verify_key' )
			) ),
			Fields\FieldFactory::create( "mphb_payment_gateway_{$this->id}_live_api_key", array(
				'type'		 => 'text',
				'label'		 => __( 'MC Live API Key', 'motopress-hotel-booking' ),
				'default'	 => $this->getDefaultOption( 'live_api_key' )
			) ),
			Fields\FieldFactory::create( "mphb_payment_gateway_{$this->id}_test_api_key", array(
				'type'		 => 'text',
				'label'		 => __( 'MC Test API Key', 'motopress-hotel-booking' ),
				'default'	 => $this->getDefaultOption( 'test_api_key' )
			) ),
		);

		$group->addFields( $groupFields );

		$subTab->addGroup( $group );
	}
	
	public function getMerchantID(){
		return $this->merchant_id;
	}
	
	public function getVerifyKey(){
		return $this->verify_key;
	}

}
