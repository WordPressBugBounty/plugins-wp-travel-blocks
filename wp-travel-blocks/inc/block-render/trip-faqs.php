<?php
/**
 * 
 * Render Callback For Trip FAQS
 * 
 */


function wptravel_block_trip_faqs_render( $attributes ) {
	ob_start();
	$trip_id   = get_the_ID();
	?>
	<style>
		.wptravel-block-trip-faqs .open-all-link{
			border-color: <?php echo esc_attr( $attributes['btnBorderColor'] ); ?> !important;
			border-radius: <?php echo esc_attr( $attributes['btnBorderRadius'] ); ?>px !important;
			color: <?php echo esc_attr( $attributes['btnTextColor'] ); ?> !important;
			background-color: <?php echo esc_attr( $attributes['btnBackgroundColor'] ); ?> !important;
		}

		.wptravel-block-trip-faqs .panel-title a{
			background-color: <?php echo esc_attr( $attributes['questionBackgroundColor'] ); ?> !important;
			color: <?php echo esc_attr( $attributes['questionTextColor'] ); ?> !important;
		}
		
		.wptravel-block-trip-faqs .panel-collapse{
			background-color: <?php echo esc_attr( $attributes['answerBackgroundColor'] ); ?> !important;
			color: <?php echo esc_attr( $attributes['answerTextColor'] ); ?> !important;
		}
	</style>
	<div id="faq" class="wptravel-block-wrapper wptravel-block-trip-faqs faq <?php echo esc_attr($attributes['layout']);?>">
		<div class="wp-collapse-open clearfix">
			<a href="#" class="open-all-faq-link"><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel-blocks' ); ?></span></a>
			<a href="#" class="close-all-faq-link" style="display:none;"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel-blocks' ); ?></span></a>
		</div>
		<div class="panel-group" id="accordion">
			<?php
			$faqs = wptravel_get_faqs( $trip_id );
			if ( is_array( $faqs ) && count( $faqs ) > 0 ) {
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
				<?php
			endforeach;
		} else {
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
				<h4 class="panel-title">
					<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="false">
					<?php echo esc_html__( 'What should I pack?', 'wp-travel-blocks' ); ?>							
					<span class="collapse-icon"></span>
					</a>
				</h4>
				</div>
				<div id="collapse1" class="panel-collapse collapse" aria-expanded="false" style="height: 95px;">
				<div class="panel-body">
					<p><?php echo esc_html__( 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s', 'wp-travel-blocks' ); ?></p>
				</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
				<h4 class="panel-title">
					<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="false">
					<?php echo esc_html__( 'How do I get around once I arrive?', 'wp-travel-blocks' ); ?>						
					<span class="collapse-icon"></span>
					</a>
				</h4>
				</div>
				<div id="collapse1" class="panel-collapse collapse" aria-expanded="false" style="height: 0px; display:none;">
				<div class="panel-body">
					<p><?php echo esc_html__( 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s', 'wp-travel-blocks' ); ?></p>
				</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
				<h4 class="panel-title">
					<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="false">
					<?php echo esc_html__( 'What are the must-see attractions and activities?', 'wp-travel-blocks' ); ?>			
					<span class="collapse-icon"></span>
					</a>
				</h4>
				</div>
				<div id="collapse1" class="panel-collapse collapse" aria-expanded="false" style="height: 0px; display:none;">
				<div class="panel-body">
					<p><?php echo esc_html__( 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s', 'wp-travel-blocks' ); ?></p>
				</div>
				</div>
			</div>
			
		<?php } ?>
		</div>
	</div>
	
	<?php
	return ob_get_clean();
}