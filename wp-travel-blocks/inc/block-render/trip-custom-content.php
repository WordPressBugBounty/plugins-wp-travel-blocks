<?php
/**
 * 
 * Render Callback For Trip Custom Content
 * 
 */

function wptravel_block_trip_custom_content_render( $attributes ) {
	ob_start();
	// $tab_data = wptravel_get_frontend_tabs();
	// $content = is_array( $tab_data ) && isset( $tab_data['overview'] ) && isset( $tab_data['overview']['content'] ) ? $tab_data['overview']['content'] : '';
	// $align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
	// $class = sprintf( ' has-text-align-%s', $align );

	$tab_custom_contents = get_post_meta( get_the_id(), 'wp_travel_itinerary_custom_tab_cnt_' )[0];

	
	
	if( !$attributes['tripContent'] && isset(wptravel_get_settings()['wp_travel_custom_global_tabs'] ) && count( wptravel_get_settings()['wp_travel_custom_global_tabs'] ) > 0 ){
		$tab_custom_contents = wptravel_get_settings()['wp_travel_custom_global_tabs'];
	}

	$custom_contents = array();
	foreach( $tab_custom_contents as $content ){
		$custom_contents[] = $content['content'];
	}
	
	if( get_the_id()  ){ 
		if( isset( $custom_contents[ ($attributes['tabContent'] -1 ) ] ) ){
	?>
		<div id="wptravel-block-trip-custom-content" class="wptravel-block-wrapper wptravel-block-trip-custom-content">
			<?php echo wp_kses_post( $custom_contents[( $attributes['tabContent'] -1 )] ); ?>
		</div>
	<?php } }else{	
	?>
		<div id="wptravel-block-trip-custom-content" class="wptravel-block-wrapper wptravel-block-trip-custom-content <?php echo esc_attr($class); ?>">
			<p>
				<?php echo esc_html__( 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s', 'wp-travel-blocks' );?>
			</p>
			<p>
				<?php echo esc_html__( 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s.', 'wp-travel-blocks' );?>
			</p>
		</div>
	<?php
	}

	return ob_get_clean();
}
