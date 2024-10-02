<?php
/**
 * 
 * Render Callback For Trip Calendar
 * 
 */

function wptravel_block_trip_calendar_render( $attributes, $content ) {

	$settings        = wptravel_get_settings();
	$trip_id         = get_the_id();
	$trip_id         = apply_filters( 'wp_travel_booking_tab_custom_trip_id', $trip_id );
	$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
	$wrapper_id      = isset( $tab_key ) ? $tab_key . '-booking-form' : 'booking-form'; // temp fixes.
	if ( wptravel_is_react_version_enabled() ) {
		$wrapper_id = isset( $tab_key ) ? $tab_key : 'booking';
	}

	$settings_listing = $settings['trip_date_listing'];
	$fixed_departure  = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
	$wrapper_class    = 'dates' === $settings_listing && 'yes' === $fixed_departure ? 'wp-travel-list-view' : 'wp-travel-calendar-view';

	$tooltip      = isset( $attributes['showTooltip'] ) ? $attributes['showTooltip'] : true;
	$tooltip_text = isset( $attributes['tooltipText'] ) ? $attributes['tooltipText'] : esc_html__( 'Select a Date to view available pricings and other options.', 'wp-travel-blocks' );
	ob_start();
	?>
	<style>
		#booking .wp-travel-booking__datepicker-wrapper{
			justify-content: <?php echo esc_attr($attributes['textAlign']) ?> !important;
		}
		#booking p{
			<?php if( !empty( $attributes['tooltipColor'] ) ) { ?>
				color: <?php echo esc_attr($attributes['tooltipColor']) ?> !important;
			<?php } ?>
			<?php if( !empty( $attributes['tooltipBgColor'] ) ) { ?>
				background-color: <?php echo esc_attr($attributes['tooltipBgColor']) ?> !important;
			<?php } ?>
			<?php if( !empty( $attributes['tooltipBorderColor'] ) ) { ?>
				border: 1px solid <?php echo esc_attr($attributes['tooltipBorderColor']) ?> !important;
			<?php } ?>
		}
		<?php if( !empty( $attributes['tooltipBgColor'] ) ) { ?>
			.wp-travel-calendar-view .wp-travel-booking__datepicker-wrapper p::after{
				border-right: 8px solid <?php echo esc_attr($attributes['tooltipBgColor']) ?> !important;
			}
		<?php } ?>
		<?php if( !empty( $attributes['tooltipBorderColor'] ) ) { ?>
			.wp-travel-calendar-view .wp-travel-booking__datepicker-wrapper p::before{
				border-right: 8px solid <?php echo esc_attr($attributes['tooltipBorderColor']) ?> !important;
			}
		<?php } ?>
		.wp-travel-calendar-view .react-datepicker__input-container button{
			<?php if( !empty( $attributes['btnBackgroundColor'] ) ) { ?>
				background-color: <?php echo esc_attr($attributes['btnBackgroundColor']) ?> !important;
			<?php } ?>
			<?php if( !empty( $attributes['btnTextColor'] ) ) { ?>
				color: <?php echo esc_attr($attributes['btnTextColor']) ?> !important;
			<?php } ?>
			<?php if( !empty( $attributes['btnBordercolor'] ) ) { ?>
				border: 1px solid <?php echo esc_attr($attributes['btnBordercolor']) ?> !important;
			<?php } ?>
			<?php if( !empty( $attributes['btnBorderRadius'] ) ) { ?>
				border-radius: <?php echo esc_attr($attributes['btnBorderRadius']) ?>px !important;
			<?php } ?>
		}
		.wp-travel-calendar-view .react-datepicker__input-container button span{
			<?php if( !empty( $attributes['btnIconBackgroundColor'] ) ) { ?>
				background-color: <?php echo esc_attr($attributes['btnIconBackgroundColor']) ?> !important;
			<?php } ?>
			<?php if( !empty( $attributes['iconColor'] ) ) { ?>
				color: <?php echo esc_attr($attributes['iconColor']) ?> !important;
			<?php } ?>
		}
	</style>
	
	<?php if( !is_single() ): ?>
		<div id="booking" class="wptravel-block-wrapper wptravel-block-trip-calendar wp-travel-calendar-view">
				<div class="wp-travel-booking__datepicker-wrapper">
					<div class="react-datepicker-wrapper">
						<div class="react-datepicker__input-container">
							<button class="wp-travel-date-picker-btn"><?php echo esc_html__( 'Select a Date', 'wp-travel-blocks' ); ?><span><i class="far fa-calendar-alt"></i></span></button>
						</div>
					</div>
					
					<?php if( $attributes['showTooltip'] ){ ?>
						<p><?php echo esc_html( $tooltip_text ); ?></p>
					<?php } ?>
				</div>
				
			</div>
		<?php else: ?>
			<div id="booking" data-tooltip="<?php echo esc_attr( $tooltip ); ?>" data-tooltip_text="<?php echo esc_attr( $tooltip_text ); ?>" class="wptravel-block-wrapper wptravel-block-trip-calendar <?php echo esc_attr( $wrapper_class ); ?>">
		
			</div>
	<?php endif; ?>
	
	<?php
	$html = ob_get_clean();

	return $html;
}
