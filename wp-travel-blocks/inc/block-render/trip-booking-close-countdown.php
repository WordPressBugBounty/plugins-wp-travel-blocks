<?php
/**
 * 
 * Render Callback For Trip Custom Content
 * 
 */

function wptravel_block_trip_booking_close_countdown_render( $attributes ) {
	ob_start();

	$settings = wptravel_get_settings();
	$trip_id = get_the_id();

	$fixed_departure = WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );
	$available_dates = wptravel_get_trip_available_dates( $trip_id );

	// Check if we have at least one date
	if ( ! empty( $available_dates ) ) {
		$today = date( 'Y-m-d' ); // Today's date in Y-m-d format
		
		if( isset($available_dates[0]['start_date']) ){
			$first_date = $available_dates[0]['start_date'];
		}else{
			$first_date = $available_dates[0];
		}

		// If the first date is today and a second date exists, use the second one
		if ( $first_date === $today && isset( $available_dates[1] ) ) {
			$date_to_use = $available_dates[1]['start_date'];
		} else {
			$date_to_use = $first_date;
		}
	}
	
	// Trip start date
	$trip_start_date = $date_to_use;

	// Cutoff time in hours before trip start (can be decimal or integer)
	$cutOffTime = get_post_meta( $trip_id, 'cuttOffTime', true );

	if ( empty( $trip_start_date ) ) return;

	// Convert cutoff time to seconds
	$cutOffSeconds = intval($cutOffTime) * 3600;

	// Get timestamp for trip start date at midnight
	$trip_start_timestamp = strtotime( $trip_start_date . ' 00:00:00' );

	// Booking close timestamp
	$booking_close_timestamp = $trip_start_timestamp - $cutOffSeconds;

	// Format date for countdown (Y-m-d H:i:s)
	$booking_close_date = date_i18n( 'Y-m-d H:i:s', $booking_close_timestamp );
    if( $fixed_departure ):
	?>
		<div class="wptravel-booking-countdown" style="margin-top: 20px">
			<span><?php echo esc_html($attributes['tourBookingCountDownLabel']); ?></span>
			<div class="booking-countdown" data-date="<?php echo esc_attr( $booking_close_date ); ?>">
				<div class="time-unit"><span class="time-number" id="booking-days">00</span><span class="time-label">Days</span></div>
				<div class="time-unit"><span class="time-number" id="booking-hours">00</span><span class="time-label">Hours</span></div>
				<div class="time-unit"><span class="time-number" id="booking-minutes">00</span><span class="time-label">Minutes</span></div>
				<div class="time-unit"><span class="time-number" id="booking-seconds">00</span><span class="time-label">Seconds</span></div>
			</div>		
		</div>
		<style>
			<?php if( $attributes['labelTextColor'] ): ?>
				.wptravel-booking-countdown > span{
					color: <?php echo esc_attr($attributes['labelTextColor']); ?>
				}
			<?php endif; ?>
			
			<?php if( $attributes['counterBgColor'] ): ?>
				.wptravel-booking-countdown .time-unit{
					background-color: <?php echo esc_attr($attributes['counterBgColor']); ?>
				}
			<?php endif; ?>
			
			<?php if( $attributes['counterTextColor'] ): ?>
				.wptravel-booking-countdown .time-unit span{
					color: <?php echo esc_attr($attributes['counterTextColor']); ?>
				}
			<?php endif; ?>
		</style>
	<?php 
	endif;	

	return ob_get_clean();
}
