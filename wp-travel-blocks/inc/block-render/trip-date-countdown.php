<?php
/**
 * 
 * Render Callback For Trip Custom Content
 * 
 */

function wptravel_block_trip_date_countdown_render( $attributes ) {
	ob_start();


	$fixed_departure = WP_Travel_Helpers_Trip_Dates::is_fixed_departure( get_the_id() );
    $available_dates = wptravel_get_trip_available_dates( get_the_id() );

	// Check if we have at least one date
	if ( ! empty( $available_dates ) ) {
		$today = date( 'Y-m-d' ); // Today's date in Y-m-d format
		$first_date = $available_dates[0]['start_date'];

		// If the first date is today and a second date exists, use the second one
		if ( $first_date === $today && isset( $available_dates[1] ) ) {
			$date_to_use = $available_dates[1]['start_date'];
		} else {
			$date_to_use = $first_date;
		}
	}
    if( $fixed_departure ):
	?>
		<div class="wptravel-tour-date-countdown" style="margin-top: 20px">
			<span><?php echo esc_html($attributes['tourCountDownLabel']); ?></span>
			<div class="tour-date-countdown" data-date="<?php echo date_i18n( 'Y-m-d', strtotime( $date_to_use ) ); ?>">
				<div class="time-unit"><span class="time-number" id="days">00</span><span class="time-label">Days</span></div>
				<div class="time-unit"><span class="time-number" id="hours">00</span><span class="time-label">Hours</span></div>
				<div class="time-unit"><span class="time-number" id="minutes">00</span><span class="time-label">Minutes</span></div>
				<div class="time-unit"><span class="time-number" id="seconds">00</span><span class="time-label">Seconds</span></div>
			</div>
		</div>
	<?php endif;
	

	return ob_get_clean();
}
