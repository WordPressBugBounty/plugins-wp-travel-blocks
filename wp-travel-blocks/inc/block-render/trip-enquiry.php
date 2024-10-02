<?php
/**
 * 
 * Render Callback For Trip Enquiry
 * 
 */

function wptravel_block_trip_enquiry_render( $attributes) {
	global $post;
	$show_trip_dropdown = "itineraries" === $post->post_type ? false : true;
	ob_start();
	?>
	<style>
		#wp-travel-enquiries .wp-travel-form-field label{
			font-size: <?php echo esc_attr( $attributes['fontSize'] ); ?>px;
		}

		#wp-travel-enquiry-submit,
		#wp-travel-send-enquiries-btn{
			padding: <?php echo esc_attr( $attributes['verticalPadding'] ); ?>px <?php echo esc_attr( $attributes['horizontalPadding'] ); ?>px !important;
			border: <?php echo esc_attr( $attributes['borderWidth'] ); ?>px <?php echo esc_attr( $attributes['borderColor'] ); ?> solid;
			border-radius: <?php echo esc_attr( $attributes['borderRadius'] ); ?>px;
			width: <?php echo esc_attr( $attributes['buttonWidth'] ); ?>px !important;
			background-color: <?php echo esc_attr( $attributes['backgroundColor'] ); ?>;
		}

		#wp-travel-enquiry-submit,
		#wp-travel-send-enquiries-btn a{
			text-decoration: none;
			color: <?php echo esc_attr( $attributes['textColor'] ); ?>;
			font-size: <?php echo esc_attr( $attributes['fontSize'] ); ?>px;
		}
		
	</style>
	<div class="wptravel-block-trip-enquiry <?php echo !$attributes['enableModal'] ? 'modal-disable': '';?>">
		<?php if( $attributes['enableModal'] ): ?>
		<button id="wp-travel-send-enquiries-btn">
			<a id="wp-travel-send-enquiries" class="wp-travel-send-enquiries" data-effect="mfp-move-from-top" href="#wp-travel-enquiries">
				<?php echo esc_attr( $attributes['textEnquiry'] ); ?>
			</a>
		</button>
		<?php endif; ?>
	<?php
	echo '<div id="wptravel-block-trip-enquiry" class="wptravel-block-wrapper">';
	if ( function_exists( 'wptravel_get_enquiries_form' ) ) {
		wptravel_get_enquiries_form( $show_trip_dropdown );
	} else {
		wp_travel_get_enquiries_form( $show_trip_dropdown );
	}
	echo '</div>';

	echo "</div>";
	$html = ob_get_clean();

	return $html;
}
