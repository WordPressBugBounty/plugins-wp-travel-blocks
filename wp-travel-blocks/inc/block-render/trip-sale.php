<?php
/**
 *
 * Render Callback For Trip Sale
 *
 */
function wptravel_block_trip_sale_render( $attributes ) {
	// Options / Attributes
	$trip_id = get_the_ID();
	$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';

	$sale_price = WP_Travel_Helpers_Pricings::get_price( [ 'trip_id' => $trip_id ] );
	$regular_price = WP_Travel_Helpers_Pricings::get_price( [ 'trip_id' => $trip_id, 'is_regular_price' => true ] );
	$is_sale = WP_Travel_Helpers_Trips::is_sale_enabled([ 'trip_id' => $trip_id, 'from_price_sale_enable' => true ]);

	ob_start();
	
	if( !get_the_ID() ) { ?>
		<div id="wptravel-block-trip-sale" data-align="<?php echo esc_attr( $align ); ?>" class="wptravel-block-wrapper">
			<span><?php echo '10' . __( ' % off', 'wp-travel-blocks' ); ?></span>
		</div>
	<?php } else {
		if( get_post()->post_type == 'itineraries' ) {
			if( count( WpTravel_Helpers_Trips::get_trip( $trip_id )['trip']['pricings'] ) > 0 && $is_sale ) {
				$discount = ( (float)$regular_price - (float)$sale_price )/ (float)$regular_price * 100; 
				if( $discount ) {
				?>
				<div id="wptravel-block-trip-sale" data-align="<?php echo esc_attr( $align ); ?>" class="wptravel-block-wrapper">
					<span>
						<?php 
							echo esc_html( round($discount, 2) ) . esc_html__( ' % off', 'wp-travel-blocks' );
						 ?>
					</span>
				</div>
			<?php } }
		}
	}	
	
	$html = ob_get_clean();
	return $html;
}