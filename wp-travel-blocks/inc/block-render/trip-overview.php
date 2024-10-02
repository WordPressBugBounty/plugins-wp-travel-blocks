<?php
/**
 * 
 * Render Callback For Trip Overview
 * 
 */

function wptravel_block_trip_overview_render( $attributes ) {
	ob_start();
	$tab_data = wptravel_get_frontend_tabs();
	$content = is_array( $tab_data ) && isset( $tab_data['overview'] ) && isset( $tab_data['overview']['content'] ) ? $tab_data['overview']['content'] : '';
	$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
	$class = sprintf( ' has-text-align-%s', $align );
	
	if( get_the_id() ){
		echo '<div id="wptravel-block-trip-overview" class="wptravel-block-wrapper wptravel-block-trip-overview '.esc_attr($class).'">'; 
		echo wpautop( do_shortcode( $content ) ); 
		echo '</div>';
	}else{	
	?>
		<div id="wptravel-block-trip-overview" class="wptravel-block-wrapper wptravel-block-trip-overview <?php echo esc_attr($class); ?>">
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
