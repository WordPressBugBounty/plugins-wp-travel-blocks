<?php
/**
 * 
 * Render Callback For Trip Excludes
 * 
 */

function wptravel_block_trip_excludes_render( $attributes ) {
	ob_start();
?>
<style>
	#wptravel-block-trip-excludes ul{
		list-style: <?php echo esc_attr( $attributes['listStyle'] ); ?>;
	}

	#wptravel-block-trip-excludes ul li{
		margin-bottom: <?php echo esc_attr( $attributes['listGap'] ); ?>px;
	}
</style>
<?php
	if( get_the_ID() ){
		$tab_data = wptravel_get_frontend_tabs();
		$content = is_array( $tab_data ) && isset( $tab_data['trip_excludes'] ) && isset( $tab_data['trip_excludes']['content'] ) ? $tab_data['trip_excludes']['content'] : '';
		$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
		$class = sprintf( ' has-text-align-%s', $align );
		echo '<div id="wptravel-block-trip-excludes" class="wptravel-block-wrapper wptravel-block-trip-excludes '.esc_attr( $class ).' '.esc_attr( $attributes['listStyle'] ).'">'; //@phpcs:ignore
		echo wpautop( do_shortcode( $content ) ); // @phpcs:ignore
		echo '</div>';
	}else{
		$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
		$class = sprintf( ' has-text-align-%s', $align );
		echo '<div id="wptravel-block-trip-excludes" class="wptravel-block-wrapper wptravel-block-trip-excludes '.esc_attr( $class ).' '.esc_attr( $attributes['listStyle'] ).'">'; //@phpcs:ignore
		echo '<ul>';
		echo '<li>This is exclude one</li>';
		echo '<li>This is exclude two</li>';
		echo '<li>This is exclude three</li>';
		echo '<li>This is exclude four</li>';
		echo '<li>This is exclude five</li>';
		echo '</ul>';
		echo '</div>';
	}

	return ob_get_clean();
}
