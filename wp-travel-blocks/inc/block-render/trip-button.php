<?php
/**
 * 
 * Render Callback For Trip Code
 * 
 */

function wptravel_block_trip_button_render( $attributes, $content, $block ) {
	
	$trip_button_text = isset( $attributes['tripButtonText'] ) ? $attributes['tripButtonText'] : esc_html__( "Explore", "wp-travel-blocks" );

	ob_start();
    ?>
	
	<a id="wptravel-block-trip-button" href="<?php echo esc_url( get_the_permalink( get_the_id() ) ); ?>" class="wp-block-button__link">
		<?php echo esc_html( $trip_button_text ); ?>
    </a>
	<?php
	return ob_get_clean();
}
