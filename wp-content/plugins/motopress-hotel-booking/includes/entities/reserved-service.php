<?php

namespace MPHB\Entities;

use MPHB\Utils\ValidateUtils;

class ReservedService extends Service {

	/**
	 *
	 * @var int
	 */
	private $adults;

    protected $quantity;
	
	//BLK edit
	protected $date_expected;

	/**
	 *
	 * @param array $atts
	 * @param int	$atts['id'] Id of service
	 * @param int	$atts['adults'] Number of adults reserved service. For service per room equal 1.
	 */
	protected function __construct( $atts ){
		parent::__construct( $atts );
		$this->adults = $atts['adults'];
        $this->quantity = isset($atts['quantity']) ? absint($atts['quantity']) : 1;
		//BLK edit
		$this->date_expected = $atts['date_expected'];
	}

	/**
	 *
	 * @param array $atts
	 * @return ReservedService|null
	 */
	public static function create( $atts ){

		if ( !isset( $atts['id'], $atts['adults'] ) ) {
			return null;
		}

		$service = MPHB()->getServiceRepository()->findById( $atts['id'] );
		if ( !$service ) {
			return null;
		}

        if ($service->isFlexiblePay()) {
            if (!isset($atts['quantity'])) {
                return null;
            }

            if ($service->isAutoLimit() || $service->isUnlimited()) {
                // With autolimit we don't know the max quantity (nights count);
                // with unlimited - we don't have the max quantity
                $quantity = ValidateUtils::validateInt($atts['quantity'], $service->getMinQuantity());
            } else {
                // Fix max quantity if max < min
                $maxQuantity = max($service->getMinQuantity(), $service->getMaxQuantityNumber());
                $quantity = ValidateUtils::validateInt($atts['quantity'], $service->getMinQuantity(), $maxQuantity);
            }

            if ($quantity === false) {
                return null;
            }
        }

		$serviceAtts = array(
			'original_id'	 => $service->getOriginalId(),
			'title'			 => $service->getTitle(),
			'description'	 => $service->getDescription(),
			'periodicity'	 => $service->getPeriodicity(),
            'min_quantity'   => $service->getMinQuantity(),
            'max_quantity'   => $service->getMaxQuantity(),
            'is_auto_limit'  => $service->isAutoLimit(),
			'repeat'		 => $service->getRepeatability(),
			'price'			 => $service->getPrice(),
		);

		$atts = array_merge( $serviceAtts, $atts );

		return new self( $atts );
	}

	/**
	 *
	 * @return int
	 */
	public function getAdults(){
		return $this->adults;
	}

    public function getQuantity()
    {
        return $this->quantity;
    }
	
	//BLK edit
	public function getDateExpected()
    {
        return $this->date_expected;
    }

	/**
	 *
	 * @param \DateTime $checkInDate
	 * @param \DateTime $checkOutDate
	 * @return float
	 */
	public function calcPrice( $checkInDate, $checkOutDate ){
		$multiplier = 1;
		if ( $this->isPayPerNight() ) {
			$nights		 = \MPHB\Utils\DateUtils::calcNights( $checkInDate, $checkOutDate );
			$multiplier	 = $multiplier * $nights;
		} else if ($this->isFlexiblePay()) {
            $multiplier *= $this->quantity;
        }

		if ( $this->isPayPerAdult() ) {
			$multiplier = $multiplier * $this->adults;
		}

		return $multiplier * $this->getPrice();
	}

	/**
	 * @param \DateTime $checkInDate
	 * @param \DateTime $checkOutDate
	 * @param string [$language=null]
	 *
	 * @return array
	 */
	public function getPriceBreakdown( $checkInDate, $checkOutDate, $language = null ){
		if ( !$language ) {
			$language = MPHB()->translation()->getCurrentLanguage();
		}
		$serviceId	 = apply_filters( '_mphb_translate_post_id', $this->getId(), $language );
		$service	 = MPHB()->getServiceRepository()->findById( $serviceId );

		return array(
			'title'		 => $service->getTitle(),
			'details'	 => $this->generatePriceDetailsString( $checkInDate, $checkOutDate ),
			'total'		 => $this->calcPrice( $checkInDate, $checkOutDate ),
		);
	}

	/**
	 *
	 * @param \DateTime $checkInDate
	 * @param \DateTime $checkOutDate
	 * @return string
	 */
	public function generatePriceDetailsString( $checkInDate, $checkOutDate ){

		$priceDetails = mphb_format_price( $this->getPrice() );
		$id = $this->getOriginalId();
		
		$service_label = get_post_meta($id, 'service_label', true);

		if ( $this->isPayPerNight() ) {
			$nights = \MPHB\Utils\DateUtils::calcNights( $checkInDate, $checkOutDate );
			$priceDetails .= sprintf( _n( ' &#215; %d night', ' &#215; %d nights', $nights, 'motopress-hotel-booking' ), $nights );
		}

		if ( $this->isPayPerAdult() ) {
			//BLK edit
			//$priceDetails .= sprintf( _n( ' &#215; %d adult', ' &#215; %d adults', $this->adults, 'motopress-hotel-booking' ), $this->adults );
			$priceDetails .= sprintf( _n( ' &#215; %d '.$service_label['qty'], ' &#215; %d '.$service_label['qty'], $this->adults, 'motopress-hotel-booking' ), $this->adults );
		}

        if ($this->isFlexiblePay()) {
            //BLK edit
			//$priceDetails .= sprintf( _n( ' &#215; %d time', ' &#215; %d times', $this->quantity, 'motopress-hotel-booking' ), $this->quantity );
			$priceDetails .= sprintf( _n( ' &#215; %d '.$service_label['duration'], ' &#215; %d '.$service_label['duration'], $this->quantity, 'motopress-hotel-booking' ), $this->quantity );
			
        }
		
		//BLK edit
		$priceDetails .= '<br>' . __('Expected on', 'motopress-hotel-booking').' '.$this->date_expected;

		return $priceDetails;
	}

}
