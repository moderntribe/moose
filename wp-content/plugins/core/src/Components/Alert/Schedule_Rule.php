<?php

namespace Tribe\Plugin\Components\Alert;

class Schedule_Rule implements Rule_Interface {

	public function passes( \WP_Post $alert, array $context ): bool {
		$current_time = $context['current_time'] ?? current_time( 'timestamp' );

		// Check start date
		$start_date = get_post_meta( $alert->ID, '_alert_start_date', true );
		$start_time = get_post_meta( $alert->ID, '_alert_start_time', true );

		if ( $start_date ) {
			$start_datetime = strtotime( $start_date . ' ' . ( $start_time ?: '00:00:00' ) );

			if ( $current_time < $start_datetime ) {
				return false;
			}
		}

		// Check end date
		$end_date = get_post_meta( $alert->ID, '_alert_end_date', true );
		$end_time = get_post_meta( $alert->ID, '_alert_end_time', true );

		if ( $end_date ) {
			$end_datetime = strtotime( $end_date . ' ' . ( $end_time ?: '23:59:59' ) );

			if ( $current_time > $end_datetime ) {
				return false;
			}
		}

		return true;
	}

	public function get_name(): string {
		return 'schedule';
	}
}
