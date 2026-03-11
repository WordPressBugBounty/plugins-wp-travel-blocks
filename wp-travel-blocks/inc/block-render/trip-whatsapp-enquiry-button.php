<?php
/**
 * 
 * Render Callback For Trip Custom Content
 * 
 */

function wptravel_block_trip_whatsapp_enquiry_button_render( $attributes ) {
	ob_start();
	$settings = wptravel_get_settings();

	if ( $settings['enable_whatsapp_btn'] === 'no' ) {
        return '';
    }

	$trip_id = get_the_ID();
	$trip_name = get_the_title( );
	$trip_link = get_permalink( );

	$raw_message = sprintf(
		$settings['whatsapp_initial_message'] . "\n%s - %s",
		$trip_name,
		$trip_link
	);

	// Apply filter so developers can modify it
	$message = apply_filters( 'wptravel_whatsapp_message', $raw_message, $trip_id, $trip_name, $trip_link, $settings,  );

	// Encode for WhatsApp URL
	$encoded_message = urlencode( $message );

	// Filterable WhatsApp number
	$phone_number = $settings['whatsapp_number'];

	// Final WhatsApp link
	$whatsapp_link = apply_filters(
		'wp_travel_trip_whatsapp_link',
		"https://api.whatsapp.com/send/?phone={$phone_number}&text={$encoded_message}&type=phone_number&app_absent=0"
	);

    // if( $fixed_departure ):
	?>
		<a id="wp-travel-send-message-whatsapp" class="wp-travel-message-whatsapp" data-effect="mfp-move-from-top" href="<?php echo $whatsapp_link ?>" target="_blank">
			<span class="wp-travel-booking-enquiry-message">
				<span class="wp-travel-whatsapp-icon">
					<i class="fab fa-whatsapp" style="color: <?php echo esc_attr($attributes['whatsappIconColor']); ?>"></i>
				</span>
				<span  style="color: <?php echo esc_attr($attributes['whatsappLinkTextColor']); ?>">
					<?php echo esc_html($attributes['buttonLabel']); ?>
				</span>
			</span>
		</a>
	<?php 
	// endif;	

	return ob_get_clean();
}
