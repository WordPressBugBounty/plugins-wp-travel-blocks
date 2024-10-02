<?php
/**
 * 
 * Render Callback For Trip Code
 * 
 */

function wptravel_block_video_button_render( $attributes, $content, $block ) {
	
	ob_start();
    ?>
	<style>		
		#wptravel-block-video-button{
			<?php if( !empty( $attributes['iconColor'] ) ): ?>
				color: <?php echo esc_attr( $attributes['iconColor'] ); ?> !important;
			<?php endif; ?>
			<?php if( !empty( $attributes['fontSize'] ) ): ?>
				font-size: <?php echo esc_attr( $attributes['fontSize'] ); ?>px;
			<?php endif; ?>
		}		
		
	</style>
	<div id="wptravel-video-button">
		<a id="wptravel-block-video-button" href="https://www.youtube.com/watch?v=<?php echo esc_attr( $attributes['videoCode'] ); ?>">
			<i class="fa fa-play-circle"></i>
		</a>
	</div>
	
	<?php
	return ob_get_clean();
}
