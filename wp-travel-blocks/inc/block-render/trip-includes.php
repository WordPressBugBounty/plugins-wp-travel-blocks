<?php
/**
 * 
 * Render Callback For Trip Includes
 * 
 */

function wptravel_block_trip_includes_render( $attributes ) {
	ob_start();
	?>
	<style>
		#wptravel-block-trip-includes ul{
			list-style: <?php echo esc_attr( $attributes['listStyle'] ); ?>;
		}
	
		#wptravel-block-trip-includes ul li{
			margin-bottom: <?php echo esc_attr( $attributes['listGap'] ); ?>px;
		}
	</style>
	<?php
		if( get_the_ID() ){
			$tab_data = wptravel_get_frontend_tabs();
			$content = is_array( $tab_data ) && isset( $tab_data['trip_includes'] ) && isset( $tab_data['trip_includes']['content'] ) ? $tab_data['trip_includes']['content'] : '';
			$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
			$class = sprintf( ' has-text-align-%s', $align );
			echo '<div id="wptravel-block-trip-includes" class="wptravel-block-wrapper wptravel-block-trip-includes '.esc_attr( $class ).' '.esc_attr( $attributes['listStyle'] ).'">'; //@phpcs:ignore
			echo wpautop( do_shortcode( $content ) ); // @phpcs:ignore
			echo '</div>';
		}else{
			$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
			$class = sprintf( ' has-text-align-%s', $align );
			echo '<div id="wptravel-block-trip-includes" class="wptravel-block-wrapper wptravel-block-trip-includes '.esc_attr($class).' '.esc_attr( $attributes['listStyle'] ).'">'; //@phpcs:ignore
			echo '<ul>';
			echo '<li>This is include one</li>';
			echo '<li>This is include two</li>';
			echo '<li>This is include three</li>';
			echo '<li>This is include four</li>';
			echo '<li>This is include five</li>';
			echo '</ul>';
			echo '</div>';
		}

	return ob_get_clean();
}
