<?php
/**
 * 
 * Render Callback For Trip Facts
 * 
 */

function wptravel_block_trip_facts_render( $attributes ) {
	ob_start();
	$trip_id   = get_the_ID();

	$background_color = 'transparent';
	if ( isset( $attributes['backgroundColor'] ) && ! empty( $attributes['backgroundColor'] ) ) {
		$background_color = $attributes['backgroundColor'];
	}
	?>
	<style>
		.wptravel-block-trip-fact .tour-info .tour-info-box{
			border:none;
			background: <?php echo esc_attr( $background_color ); ?>
		}

		.wptravel-block-trip-fact .tour-info .tour-info-box .tour-info-item{
			color: <?php echo esc_attr( $attributes['textColor'] ); ?>;
		}

		.wptravel-block-trip-fact .tour-info .tour-info-box .tour-info-item strong,
		.wptravel-block-trip-fact .tour-info .tour-info-box .tour-info-item i{
			color: <?php echo esc_attr( $attributes['labelColor'] ); ?>;
		}
	</style>
	<?php if( get_the_id() ): ?>
		<div id="wptravel-block-trip-fact" class="wptravel-block-wrapper wptravel-block-trip-fact">
			<?php wptravel_frontend_trip_facts( $trip_id ); ?>
		</div>
		<?php else: ?>
			<div id="wptravel-block-trip-fact" class="wptravel-block-wrapper wptravel-block-trip-fact">
				<div class="tour-info">
					<div class="tour-info-box clearfix">
						<div class="tour-info-column ">
							<span class="tour-info-item tour-info-type">
								<span class="trip-facts-label">
									<i class="fas fa-bus" aria-hidden="true"></i>
									<strong><?php echo esc_html__( 'Transportation', 'wp-travel-blocks' ); ?></strong>
								</span>
								<span class="trip-facts-value">
									<span>:</span>
									<?php echo esc_html__( 'Bus,Jeep', 'wp-travel-blocks' ); ?>							
								</span>
							</span>
							<span class="tour-info-item tour-info-type">
								<span class="trip-facts-label">
									<i class="fas fa-bus" aria-hidden="true"></i>
									<strong><?php echo esc_html__( 'Accomodation', 'wp-travel-blocks' ); ?></strong>
								</span>
								<span class="trip-facts-value">
									<span>:</span>
									<?php echo esc_html__( 'Home Stay', 'wp-travel-blocks' ); ?>							
								</span>
							</span>
							<span class="tour-info-item tour-info-type">
								<span class="trip-facts-label">
									<i class="fas fa-bus" aria-hidden="true"></i>
									<strong><?php echo esc_html__( 'Maximum Altitude', 'wp-travel-blocks' ); ?></strong>
								</span>
								<span class="trip-facts-value">
									<span>:</span>
									<?php echo esc_html__( '3000 meters', 'wp-travel-blocks' ); ?>							
								</span>
							</span>
							<span class="tour-info-item tour-info-type">
								<span class="trip-facts-label">
									<i class="fas fa-bus" aria-hidden="true"></i>
									<strong><?php echo esc_html__( 'Best Season', 'wp-travel-blocks' ); ?></strong>
								</span>
								<span class="trip-facts-value">
									<span>:</span>
									<?php echo esc_html__( 'feb,mar,apr', 'wp-travel-blocks' ); ?>							
								</span>
							</span>
							<span class="tour-info-item tour-info-type">
								<span class="trip-facts-label">
									<i class="fas fa-bus" aria-hidden="true"></i>
									<strong><?php echo esc_html__( 'Meals', 'wp-travel-blocks' ); ?></strong>
								</span>
								<span class="trip-facts-value">
									<span>:</span>
									<?php echo esc_html__( 'Veg,Non Veg', 'wp-travel-blocks' ); ?>						
								</span>
							</span>
							<span class="tour-info-item tour-info-type">
								<span class="trip-facts-label">
									<i class="fas fa-bus" aria-hidden="true"></i>
									<strong><?php echo esc_html__( 'Tour Guide', 'wp-travel-blocks' ); ?></strong>
								</span>
								<span class="trip-facts-value">
									<span>:</span>
									<?php echo esc_html__( 'Available', 'wp-travel-blocks' ); ?>						
								</span>
							</span>
							<span class="tour-info-item tour-info-type">
								<span class="trip-facts-label">
									<i class="fas fa-bus" aria-hidden="true"></i>
									<strong><?php echo esc_html__( 'Language', 'wp-travel-blocks' ); ?></strong>
								</span>
								<span class="trip-facts-value">
									<span>:</span>
									<?php echo esc_html__( 'Enghish,Chinese,French', 'wp-travel-blocks' ); ?>				
								</span>
							</span>
							<span class="tour-info-item tour-info-type">
								<span class="trip-facts-label">
									<i class="fas fa-bus" aria-hidden="true"></i>
									<strong><?php echo esc_html__( 'Departure From', 'wp-travel-blocks' ); ?></strong>
								</span>
								<span class="trip-facts-value">
									<span>:</span>
									<?php echo esc_html__( 'Pokhara', 'wp-travel-blocks' ); ?>					
								</span>
							</span>
						</div>
					</div>
					</div>
			</div>
	<?php endif; ?>
	<?php
	return ob_get_clean();
}