<?php

namespace MPHB\Persistences;

class ReservedRoomPersistence extends CPTPersistence {

	/**
	 * @param array $atts Optional.
	 * @param int $atts['booking_id'] Optional. Retrieve reserved rooms for booking.
	 *
	 * @return WP_Post[]|int[] List of posts.
	 */
	public function getPosts( $atts = array() ){
		return parent::getPosts( $atts );
	}

	protected function getDefaultQueryAtts(){
		$defaultAtts = array(
			'post_status' => 'publish',
		);

		return array_merge( parent::getDefaultQueryAtts(), $defaultAtts );
	}

	protected function modifyQueryAtts( $atts ){
		$atts = parent::modifyQueryAtts( $atts );

		$atts = $this->_addBookingCriteria( $atts );
		$atts = $this->_addRoomCriteria( $atts );

		return $atts;
	}

	private function _addBookingCriteria( $atts ){

		if ( empty( $atts['booking_id'] ) ) {
			return $atts;
		}

		if ( !is_array( $atts['booking_id'] ) ) {
			$atts['post_parent'] = $atts['booking_id'];
		} else {
			$atts['post_parent__in'] = $atts['booking_id'];
		}

		unset( $atts['booking_id'] );

		return $atts;
	}

	private function _addRoomCriteria( $atts ){
		if ( empty( $atts['room_id'] ) ) {
			return $atts;
		}

		if ( is_array( $atts['room_id'] ) ) {
			$queryPart = array(
				'key'		 => '_mphb_room_id',
				'value'		 => $atts['room_id'],
				'compare'	 => 'IN'
			);
		} else {
			$queryPart = array(
				'key'		 => '_mphb_room_id',
				'value'		 => $atts['room_id'],
				'compare'	 => '='
			);
		}

		$atts['meta_query'] = mphb_add_to_meta_query( $queryPart, isset( $atts['meta_query'] ) ? $atts['meta_query'] : null  );

		unset( $atts['room_id'] );

		return $atts;
	}

}
