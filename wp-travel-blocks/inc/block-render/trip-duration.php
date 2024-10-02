<?php
/**
 * 
 * Render Callback For Trip Duration
 * 
 */

function wptravel_block_trip_duration_render( $attributes ) {

	$trip_id = get_the_ID();
	$fixed_departure = WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );
	$type            = $fixed_departure ? 'type-fixed-departure' : 'type-trip-duartion';

	$trip_duration       = get_post_meta( $trip_id, 'wp_travel_trip_duration', true );
	$trip_duration       = ( $trip_duration ) ? $trip_duration : 0;
	$trip_duration_night = get_post_meta( $trip_id, 'wp_travel_trip_duration_night', true );
	$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;

	$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
	$duration_format = isset( $attributes['durationFormat'] ) ? $attributes['durationFormat'] : 'day-night';
	$days_placeholder_text = ! empty( $attributes['daysPlaceholderText'] ) ? $attributes['daysPlaceholderText'] : 'Days';
	$nights_placeholder_text = ! empty( $attributes['nightsPlaceholderText'] ) ? $attributes['nightsPlaceholderText'] : 'Nights';
	$hours_placeholder_text = ! empty( $attributes['hourPlaceholderText'] ) ? $attributes['hourPlaceholderText'] : 'Hours';
	$minutes_placeholder_text = ! empty( $attributes['minutePlaceholderText'] ) ? $attributes['minutePlaceholderText'] : 'Minutes';
	$class = sprintf( ' has-text-align-%s', $align );
	$extra_class = $attributes['extraClass'];


	ob_start();
	
	if( !empty( $attributes['textColor'] )
	&& ( ( $duration_format == 'day_night' && ( isset( $trip['trip_duration']['days'] ) && ! empty( $trip['trip_duration']['days'] ) ) || ( isset( $trip['trip_duration']['nights'] ) && ! empty( $trip['trip_duration']['nights'] ) ) )
		|| ( $duration_format == 'hour_minute' && ( isset( $trip['trip_duration']['hours'] ) && ! empty( $trip['trip_duration']['hours'] ) ) || ( isset( $trip['trip_duration']['minutes'] ) && ! empty( $trip['trip_duration']['minutes'] ) ) ) ) ): ?>
		<style>
			.wptravel-block-<?php echo esc_attr( $extra_class ); ?> .dropbtn,
			.wptravel-block-<?php echo esc_attr( $extra_class ); ?>{
				<?php if( $attributes['textColor'] ): ?>
					color: <?php echo esc_attr( $attributes['textColor'] ); ?>!important;
				<?php endif; ?>
			}
			.wptravel-block-<?php echo esc_attr( $extra_class ); ?> .fixed-date-dropdown .dropbtn::after {
				color: <?php echo esc_attr( $attributes['textColor'] ); ?>!important;
			}
		</style>
	<?php
	endif;
	
	if( !get_the_ID() ){ ?>
		<div id="wptravel-block-trip-duration" class="wptravel-block-wrapper wptravel-block-trip-duration-date">
			<div class="travel-info trip-duration">
				<span class="value">
					<?php printf( __( '%1$s Day(s) %2$s Night(s)', 'wp-travel-blocks' ), 3, 2 ); ?>
				</span>
			</div>
		</div>
	<?php } else {
		if( get_post()->post_type == 'itineraries' ) {
			if ( $fixed_departure ) {
				$dates = wptravel_get_fixed_departure_date( $trip_id );
				if ( $dates ) {
					?>
					<div id="wptravel-block-trip-duration" class="wptravel-block-wrapper wptravel-block-<?php echo esc_attr( $extra_class ); ?> wptravel-block-trip-duration-date <?php echo esc_attr( $class ); ?>">
						<span class="value">
							<?php echo $dates; // @phpcs:ignore ?>
						</span>
					</div>
					<?php
				}
			} else {
				if ( ( $trip_duration || $trip_duration_night ) ) {
					?>
					<div id="wptravel-block-trip-duration" class="wptravel-block-wrapper wptravel-block-<?php echo esc_attr( $extra_class ); ?> wptravel-block-trip-duration-date <?php echo esc_attr( $class ); ?>">
						<span class="value">
							<?php echo wp_travel_get_trip_durations( $trip_id ); ?>
						</span>
					</div>
				<?php } else { ?>
					<div id="wptravel-block-trip-duration" class="wptravel-block-wrapper wptravel-block-<?php echo esc_attr( $extra_class ); ?> wptravel-block-trip-duration-date <?php echo esc_attr( $class ); ?>">
						<span class="value">
							<?php printf( __( 'N/A', 'wp-travel-blocks' ) ); ?>
						</span>
					</div>
				<?php }
			}
		} else { ?>
			<div class="travel-info trip-duration">
				<span class="value">
					<?php printf( __( '%1$s Day(s) %2$s Night(s)', 'wp-travel-blocks' ), 3, 2 ); ?>
				</span>
			</div>
		<?php }
	}
	
	$html = ob_get_clean();

	return $html;
}