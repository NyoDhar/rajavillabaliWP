<?php

namespace MPHB\Views;

use \MPHB\Entities;

class ReservedRoomView {

	/**
	 *
	 * @param \MPHB\Entities\ReservedRoom $reservedRoom
	 */
	public static function renderServicesList( Entities\ReservedRoom $reservedRoom ){
		$reservedServices = $reservedRoom->getReservedServices();
		if ( !empty( $reservedServices ) ) {
			?>
			<p>
				<?php
				foreach ( $reservedServices as $reservedService ) {
					$reservedService = apply_filters( '_mphb_translate_reserved_service', $reservedService );
					$id = $reservedService->getOriginalId();
					$service_label = get_post_meta($id, 'service_label', true);
					
					echo $reservedService->getTitle();
					if ( $reservedService->isPayPerAdult() ) {
						echo ' <em>';
						printf( _n( 'x %d '.$service_label['qty'], 'x %d '.$service_label['qty'], $reservedService->getAdults(), 'motopress-hotel-booking' ), $reservedService->getAdults() );
						echo '</em>';
					}
                    if ($reservedService->isFlexiblePay()) {
                        echo ' <em>';
                        printf(_n('x %d '.$service_label['duration'], 'x %d '.$service_label['duration'], $reservedService->getQuantity(), 'motopress-hotel-booking'), $reservedService->getQuantity());
                        echo '</em>';
                    }
					echo '<br />';
				}
				?>
			</p>
			<?php
		} else {
			echo "&#8212;";
		}
	}

}
