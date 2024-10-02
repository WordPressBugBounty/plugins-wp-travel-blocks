<?php
/** 
 * Callback function to render Trip Timespan
 */
function wptravel_block_trip_timespan_render( $attributes, $content ){
    $trip_id = get_the_ID();
	$is_fixed_departure = WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );
	$type = $is_fixed_departure ? 'type-fixed-departure' : 'type-trip-duration';

    $timespan_title_style = "color:".$attributes['titleColor'].";background:".$attributes['titleBackgroundColor'];
    $timespan_icon_style = "color:".$attributes['iconColor'].";background:".$attributes['iconBackgroundColor'];
    $timespan_value_style = "color:".$attributes['timespanColor'].";background:".$attributes['timespanBackgroundColor'];

    ob_start();

	if( !get_the_ID() ){ ?>
		<div id="wptravel-blocks-trip-timespan" class="<?php echo esc_attr( $type ); ?>">
            <?php if( isset( $attributes['showTitle'] ) && $attributes['showTitle'] ) { ?>
                <div class="wptravel-blocks-timespan-title" style="<?php echo esc_attr( $timespan_title_style ); ?>">
                    <?php echo esc_html__("Departure", "wp-travel-blocks"); ?>
                </div>
            <?php } ?>
            <div class="wptravel-blocks-timespan-content">
                <?php if( isset( $attributes['showIcon'] ) && $attributes['showIcon'] ) { ?>
                    <i class='far fa-clock' style="<?php echo esc_attr( $timespan_icon_style ) ?>"></i>
                <?php }
                if( isset( $attributes['showTimespan'] ) && $attributes['showTimespan'] ) { ?>
                    <span class="wptravel-blocks-timespan-value" style="<?php echo esc_attr( $timespan_value_style ) ?>">
                        <?php echo esc_html__( "4 Day(s) 3 Night(s)", "wp-travel-blocks"); ?>
                    </span>
                <?php } ?>
            </div>
        </div>
	<?php } else {
        if( get_post()->post_type == 'itineraries' ) {
            if( $is_fixed_departure ) { ?>
                <div id="wptravel-blocks-trip-timespan" class="<?php echo esc_attr( $type ); ?>">
                    <?php if( isset( $attributes['showTitle'] ) && $attributes['showTitle'] ) { ?>
                        <div class="wptravel-blocks-timespan-title" style="<?php echo esc_attr( $timespan_title_style ) ?>">
                            <?php echo esc_html__("Departure", "wp-travel-blocks"); ?>
                        </div>
                    <?php } ?>
                    <div class="wptravel-blocks-timespan-content">
                        <?php if( isset( $attributes['showIcon'] ) && $attributes['showIcon'] ) { ?>
                            <i class='far fa-calendar-alt' style="<?php echo esc_attr( $timespan_icon_style ) ?>"></i>
                        <?php }
                        if( isset( $attributes['showTimespan'] ) && $attributes['showTimespan'] ) { ?>
                        <span class="wptravel-blocks-timespan-value" style="<?php echo esc_attr( $timespan_value_style ) ?>">
                            <?php echo trim( wptravel_get_fixed_departure_date( $trip_id ) ) ? wptravel_get_fixed_departure_date( $trip_id ) : "N/A"; ?>
                        </span>
                        <?php } ?>
                    </div>
                </div>
            <?php } else { ?>
                <div id="wptravel-blocks-trip-timespan" class="<?php echo $type; ?>">
                    <?php if( isset( $attributes['showTitle'] ) && $attributes['showTitle'] ) { ?>
                        <div class="wptravel-blocks-timespan-title" style="<?php echo esc_attr( $timespan_title_style ) ?>">
                            <?php echo esc_html__("Duration", "wp-travel-blocks"); ?>
                        </div>
                    <?php } ?>
                    <div class="wptravel-blocks-timespan-content">
                        <?php if( isset( $attributes['showIcon'] ) && $attributes['showIcon'] ) { ?>
                            <i class='far fa-clock' style="<?php echo esc_attr( $timespan_icon_style ) ?>"></i>
                        <?php }
                        if( isset( $attributes['showTimespan'] ) && $attributes['showTimespan'] ) { ?>
                        <span class="wptravel-blocks-timespan-value" style="<?php echo esc_attr( $timespan_value_style ) ?>">
                            <?php echo trim( wp_travel_get_trip_durations( $trip_id ) ) ? wp_travel_get_trip_durations( $trip_id ) : "N/A"; ?>
                        </span>
                        <?php } ?>
                    </div>
                </div>
            <?php }
        } else { ?>
            <div id="wptravel-blocks-trip-timespan" class="<?php echo esc_attr( $type ); ?>">
                <?php if( isset( $attributes['showTitle'] ) && $attributes['showTitle'] ) { ?>
                    <div class="wptravel-blocks-timespan-title" style="<?php echo esc_attr( $timespan_title_style ) ?>">
                        <?php echo esc_html__("Departure", "wp-travel-blocks"); ?>
                    </div>
                <?php } ?>
                <div class="wptravel-blocks-timespan-content">
                    <?php if( isset( $attributes['showIcon'] ) && $attributes['showIcon'] ) { ?>
                        <i class='far fa-clock' style="<?php echo esc_attr( $timespan_icon_style ) ?>"></i>
                    <?php }
                    
                    if( isset( $attributes['showTimespan'] ) && $attributes['showTimespan'] ) { ?>
                    <span class="wptravel-blocks-timespan-value" style="<?php echo esc_attr( $timespan_value_style ) ?>">
                        <?php echo esc_html__( "3 Day(s) 2 Night(s)", "wp-travel-blocks"); ?>
                    </span>
                    <?php } ?>
                </div>
            </div>
        <?php }
    }

    $html = ob_get_clean();
    return $html;
}