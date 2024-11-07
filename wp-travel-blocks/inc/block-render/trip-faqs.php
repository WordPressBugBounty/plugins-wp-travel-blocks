<?php
/**
 * 
 * Render Callback For Trip FAQS
 * 
 */


 function wptravel_block_trip_faqs_render( $attributes ) {
	// Handle all attributes with defaults to avoid errors
    $btnBorderColor = isset( $attributes['btnBorderColor'] ) ? esc_attr( $attributes['btnBorderColor'] ) : '';
    $btnBorderRadius = isset( $attributes['btnBorderRadius'] ) ? intval( $attributes['btnBorderRadius'] ) : 0;
    $btnTextColor = isset( $attributes['btnTextColor'] ) ? esc_attr( $attributes['btnTextColor'] ) : '';
    $btnBackgroundColor = isset( $attributes['btnBackgroundColor'] ) ? esc_attr( $attributes['btnBackgroundColor'] ) : '';
    $questionBackgroundColor = isset( $attributes['questionBackgroundColor'] ) ? esc_attr( $attributes['questionBackgroundColor'] ) : '';
    $questionTextColor = isset( $attributes['questionTextColor'] ) ? esc_attr( $attributes['questionTextColor'] ) : '';
    $answerBackgroundColor = isset( $attributes['answerBackgroundColor'] ) ? esc_attr( $attributes['answerBackgroundColor'] ) : '';
    $answerTextColor = isset( $attributes['answerTextColor'] ) ? esc_attr( $attributes['answerTextColor'] ) : '';
    $layout = isset( $attributes['layout'] ) ? esc_attr( $attributes['layout'] ) : 'first-design';

	ob_start();
	?>
	<style>
		.wptravel-block-trip-faqs .open-all-link {
			border-color: <?php echo $btnBorderColor; ?> !important;
			border-radius: <?php echo intval( $btnBorderRadius ); ?>px !important;
			color: <?php echo $btnTextColor; ?> !important;
			background-color: <?php echo $btnBackgroundColor; ?> !important;
		}

		.wptravel-block-trip-faqs .panel-title a {
			background-color: <?php echo $questionBackgroundColor; ?> !important;
			color: <?php echo $questionTextColor; ?> !important;
		}
		
		.wptravel-block-trip-faqs .panel-collapse {
			background-color: <?php echo $answerBackgroundColor; ?> !important;
			color: <?php echo $answerTextColor; ?> !important;
		}
	</style>
	<div id="faq" class="wptravel-block-wrapper wptravel-block-trip-faqs faq <?php echo $layout; ?>">
		<!-- FAQ content goes here -->
		<div class="wp-collapse-open clearfix">
			<a href="#" class="open-all-faq-link"><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel-blocks' ); ?></span></a>
			<a href="#" class="close-all-faq-link" style="display:none;"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel-blocks' ); ?></span></a>
		</div>
		<div class="panel-group" id="accordion">
			<?php
			$faqs = wptravel_get_faqs( get_the_ID() );
			if ( ! empty( $faqs ) && is_array( $faqs ) ) {
				foreach ( $faqs as $k => $faq ) : ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo esc_attr( $k + 1 ); ?>">
									<?php echo esc_html( $faq['question'] ); ?>
									<span class="collapse-icon"></span>
								</a>
							</h4>
						</div>
						<div id="collapse<?php echo esc_attr( $k + 1 ); ?>" class="panel-collapse collapse">
							<div class="panel-body">
								<?php echo wp_kses_post( wpautop( $faq['answer'] ) ); ?>
							</div>
						</div>
					</div>
				<?php endforeach;
			} else {
				// Default placeholder content
				?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="false">
								<?php echo esc_html__( 'Sample Question?', 'wp-travel-blocks' ); ?>
								<span class="collapse-icon"></span>
							</a>
						</h4>
					</div>
					<div id="collapse1" class="panel-collapse collapse">
						<div class="panel-body">
							<p><?php echo esc_html__( 'This is a sample answer.', 'wp-travel-blocks' ); ?></p>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="false">
								<?php echo esc_html__( 'Sample Question?', 'wp-travel-blocks' ); ?>
								<span class="collapse-icon"></span>
							</a>
						</h4>
					</div>
					<div id="collapse1" class="panel-collapse collapse">
						<div class="panel-body">
							<p><?php echo esc_html__( 'This is a sample answer.', 'wp-travel-blocks' ); ?></p>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
