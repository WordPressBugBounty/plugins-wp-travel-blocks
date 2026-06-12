<?php
/**
 * 
 * Render Callback For Trip Custom Content
 * 
 */

function wptravel_block_trip_booking_count_render( $attributes ) {
	ob_start();
	$settings = wptravel_get_settings();
	$text_color = isset( $attributes['textColor'] ) ? $attributes['textColor'] : '';

	$trip_id = get_the_ID();  

	if ( $settings['enable_trip_book_count'] == 'yes' ) :

		// Default booking count from DB
		$booking_count = absint( get_post_meta( $trip_id, 'wp_travel_booking_count', true ) );

		if ( $settings['enable_custom_booking_count'] == 'yes' ){
			// Allow override via filter
			$custom_counts = apply_filters( 'wp_travel_custom_trip_booking_count', [] );

			if ( isset( $custom_counts[ $trip_id ] ) ) {
				$booking_count = absint( $custom_counts[ $trip_id ] );
			}
		}

		$booking_count_label = $settings['book_count_label'];
		$booking_count_label_non_booked = $settings['book_count_label_with_zero_booking'];
	?>
		<div class="wp-travel-booking-count-block" style="<?php echo esc_attr( $text_color ? 'color:' . $text_color . ';' : '' ); ?>">
			<?php if ( $booking_count > 0 ) : 

				echo str_replace(
					['{count}'],
					[$booking_count],
					$booking_count_label
				);

			else : 

				echo $booking_count_label_non_booked;

			endif; ?>
		</div>
	<?php endif; 
	
	return ob_get_clean();
}
