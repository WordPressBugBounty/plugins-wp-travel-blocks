<?php
/**
 * 
 * Render Callback For Trip Map
 * 
 */

function wptravel_block_trip_pickup_points_render( $attributes ) {

	$trip_id     = get_the_id();

	$pickupLocationLabel       = $attributes['pickupPointLabel'];

	ob_start();
	if($trip_id ){
		if( get_post_meta( $trip_id , 'wp_travel_trip_pickup_points', true ) ){
			?>
			<div id="wp-travel-trip-pickup-location" class="wp-travel-trip-pickup-location-block">
		
				<span><?php echo esc_html( $pickupLocationLabel ) . get_post_meta( $trip_id , 'wp_travel_trip_pickup_points', true )[0]; ?></span>
				<ul class="location-lists">
					<?php foreach( get_post_meta( $trip_id , 'wp_travel_trip_pickup_points', true ) as $location ): ?>
						<li class="location">
							<?php echo esc_html($location) ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php }
	}else{
		$pickupLocations = array( 'Location One', 'Location Two', 'Location Three', 'Location Four', 'Location Five' )
		?>
		<div id="wp-travel-trip-pickup-location" class="wp-travel-trip-pickup-location-block">
		
			<span><?php echo esc_html( $pickupLocationLabel ) . $pickupLocations[0]; ?></span>
			<ul class="location-lists">
				<?php foreach( $pickupLocations as $location ): ?>
					<li class="location">
						<?php echo esc_html($location) ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	return ob_get_clean();
}