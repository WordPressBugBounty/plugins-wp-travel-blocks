<?php
/**
 * 
 * Render Callback For Trip Gallery
 * 
 */

function wptravel_block_trip_gallery_render( $attributes ) {

	ob_start();
	$tab_data = wptravel_get_frontend_tabs();

	$content = is_array( $tab_data ) && isset( $tab_data['gallery'] ) && isset( $tab_data['gallery']['content'] ) ? $tab_data['gallery']['content'] : '';
	
	$galleryDesign     = isset( $attributes['galleryDesign'] ) ? $attributes['galleryDesign'] : 'gridStyle';
	$sliderHeight     = isset( $attributes['sliderHeight'] ) ? $attributes['sliderHeight'] : 400;
	$sliderAutoplay     = isset( $attributes['sliderAutoplay'] ) ? $attributes['sliderAutoplay'] : false;
	$sliderArrow     = isset( $attributes['sliderArrow'] ) ? $attributes['sliderArrow'] : false;
	$sliderDots     = isset( $attributes['sliderDots'] ) ? $attributes['sliderDots'] : false;
	$trips_to_show     = isset( $attributes['tripsToShow'] ) ? $attributes['tripsToShow'] : 2;

	if( $galleryDesign == 'gridStyle' ){
		if( get_the_id() ){
			echo '<div id="wptravel-block-trip-gallery" class="wptravel-block-wrapper wptravel-block-trip-gallery">'; //@phpcs:ignore
			echo wpautop( do_shortcode( $content ) ); // @phpcs:ignore
			echo '</div>'; //@phpcs:ignore
		}else{
		?>
			<div id="wptravel-block-trip-gallery" class="wptravel-block-wrapper wptravel-block-trip-gallery">
				<div class="wp-travel-advanced-gallery-items-list wp-travel-advanced-gallery-items-list-grid">
					<div class="item image"><a href="#" class="mfp-image" title="road-street-town-bicycle-bike-city-646444-pxhere-com" data-caption=""><span class="wptag__media-card"><span class="wptag__thumbnail"><img alt="" src="<?php echo esc_url( plugins_url( ) ). '/wp-travel-blocks/assets/uploads/wp-travel-placeholder.png';?>" title="wp-travel-gallery-image"></span></span></a></div>
					<div class="item image"><a href="#" class="mfp-image" title="road-street-town-bicycle-bike-city-646444-pxhere-com" data-caption=""><span class="wptag__media-card"><span class="wptag__thumbnail"><img alt="" src="<?php echo esc_url( plugins_url( ) ). '/wp-travel-blocks/assets/uploads/wp-travel-placeholder.png';?>" title="wp-travel-gallery-image"></span></span></a></div>
					<div class="item image"><a href="#" class="mfp-image" title="road-street-town-bicycle-bike-city-646444-pxhere-com" data-caption=""><span class="wptag__media-card"><span class="wptag__thumbnail"><img alt="" src="<?php echo esc_url( plugins_url( ) ). '/wp-travel-blocks/assets/uploads/wp-travel-placeholder.png';?>" title="wp-travel-gallery-image"></span></span></a></div>
					<div class="item image"><a href="#" class="mfp-image" title="road-street-town-bicycle-bike-city-646444-pxhere-com" data-caption=""><span class="wptag__media-card"><span class="wptag__thumbnail"><img alt="" src="<?php echo esc_url( plugins_url( ) ). '/wp-travel-blocks/assets/uploads/wp-travel-placeholder.png';?>" title="wp-travel-gallery-image"></span></span></a></div>
					<div class="item image"><a href="#" class="mfp-image" title="road-street-town-bicycle-bike-city-646444-pxhere-com" data-caption=""><span class="wptag__media-card"><span class="wptag__thumbnail"><img alt="" src="<?php echo esc_url( plugins_url( ) ). '/wp-travel-blocks/assets/uploads/wp-travel-placeholder.png';?>" title="wp-travel-gallery-image"></span></span></a></div>
					<div class="item image"><a href="#" class="mfp-image" title="road-street-town-bicycle-bike-city-646444-pxhere-com" data-caption=""><span class="wptag__media-card"><span class="wptag__thumbnail"><img alt="" src="<?php echo esc_url( plugins_url( ) ). '/wp-travel-blocks/assets/uploads/wp-travel-placeholder.png';?>" title="wp-travel-gallery-image"></span></span></a></div>
					<div class="item image"><a href="#" class="mfp-image" title="road-street-town-bicycle-bike-city-646444-pxhere-com" data-caption=""><span class="wptag__media-card"><span class="wptag__thumbnail"><img alt="" src="<?php echo esc_url( plugins_url( ) ). '/wp-travel-blocks/assets/uploads/wp-travel-placeholder.png';?>" title="wp-travel-gallery-image"></span></span></a></div>
					<div class="item image"><a href="#" class="mfp-image" title="road-street-town-bicycle-bike-city-646444-pxhere-com" data-caption=""><span class="wptag__media-card"><span class="wptag__thumbnail"><img alt="" src="<?php echo esc_url( plugins_url( ) ). '/wp-travel-blocks/assets/uploads/wp-travel-placeholder.png';?>" title="wp-travel-gallery-image"></span></span></a></div>
				</div>
			</div>
		<?php
		}
		
	}else{
		$gallery_image = array();

		if( class_exists( 'WP_Travel_Pro' ) ){
			if( count( get_post_meta( get_the_id(), 'wp_travel_advanced_gallery' ) ) > 0 ){
				foreach(  get_post_meta( get_the_id(), 'wp_travel_advanced_gallery' )[0]['items'] as $data ){
					array_push( $gallery_image, $data['id'] );
				}
			}else{
				$gallery_image = get_post_meta( get_the_id(), 'wp_travel_itinerary_gallery_ids' )[0];
			}
		}else{
			$gallery_image = get_post_meta( get_the_id(), 'wp_travel_itinerary_gallery_ids' )[0];
		}

	?>
		<div id="wptravel-block-trip-gallery" class="wptravel-block-wrapper wptravel-block-trip-gallery slider">
			<div class="wp-travel-advanced-gallery-items-list">
				<?php foreach( $gallery_image as $image ): ?>
					<div class="item image">
						<a href="<?php echo esc_url( wp_get_attachment_image_url($image, 'large') ); ?>" >
							<div class="wptag__media-card">
								<div class="wptag__thumbnail">
									<img decoding="async" alt="" src="<?php echo esc_url( wp_get_attachment_image_url($image, 'full') ); ?>">
								</div>
							</div>
						</a>
					</div>
				<?php endforeach; ?>
				
			</div>
			<div class="gallery-icon">
				<span class="open-image" onclick="wptravelOpenImageViwer()"><i class="far fa-images"></i>  <?php echo esc_html__( 'Gallery', 'wp-travel-blocks' ); ?></span>
				<?php if( WP_Travel_Helpers_Trips::get_trip(get_the_ID())['trip']['trip_video_code'] ): ?>
					
					<span class="open-video"><a class="trip-video" href="<?php echo esc_url(WP_Travel_Helpers_Trips::get_trip(get_the_ID())['trip']['trip_video_code']); ?>"><i class="far fa-play-circle"></i>  <?php echo esc_html__( 'Video', 'wp-travel-blocks' ); ?></a></span>
				<?php endif; ?>
			</div>
		</div>

		<style>
			@media only screen and (min-width: 993px){
				.wptravel-block-trip-gallery .wp-travel-advanced-gallery-items-list.slick-slider .wptag__thumbnail {
					height: <?php echo esc_attr($sliderHeight); ?>px;
				}

				.wptravel-block-trip-gallery .wp-travel-advanced-gallery-items-list.slick-slider .wptag__thumbnail img {
					height: 100%;
					max-height: unset;
					width: 100%;
    				object-fit: cover;
				}
			}
		</style>
		<script>
			jQuery(document).ready(function(n) {
				n('#wptravel-block-trip-gallery .wp-travel-advanced-gallery-items-list').slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					<?php 
						if( $sliderAutoplay ){
						?>
						autoplay: true,
						<?php
						}else{
						?>
						autoplay: false,
						<?php
						}
					?>
					<?php 
						if( $sliderArrow ){
						?>
						arrows: true,
						<?php
						}else{
						?>
						arrows: false,
						<?php
						}
					?>
					<?php 
						if( $sliderDots ){
						?>
						dots: true,
						<?php
						}else{
						?>
						dots: false,
						<?php
						}
					?>
					<?php 
						if( $trips_to_show ){
						?>
						slidesToShow: <?php echo absint( $trips_to_show ); ?>,
						<?php
						}else{
						?>
						slidesToShow: 1,
						<?php
						}
					?>
					infinite: false,
				});
			});
			function wptravelOpenImageViwer(){
				var firstSlideLink = document.querySelector('.slick-track .slick-slide:first-of-type a');
				firstSlideLink.click();
			}
		</script>
	<?php
	}
	return ob_get_clean();
}


add_action( 'rest_api_init', 'wp_travel_blocks_pro_get_trips_meta' );

function wp_travel_blocks_pro_get_trips_meta(){
	register_rest_route(
		'wp-travel-block/v1',
		'/get-trip-gallery',
		array(
			'methods'             => 'get',
			'permission_callback' => '__return_true',
			'callback'            => 'wp_travel_blocks_pro_trip_gallery',
		)
	);
}

function wp_travel_blocks_pro_trip_gallery( WP_REST_Request $request ){
	$set_images = array();
	$gallery_images = array();

	if( class_exists( 'WP_Travel_Pro' ) ){
		if( count( get_post_meta( $request->get_param('id'), 'wp_travel_advanced_gallery' ) ) > 0 ){
			foreach(  get_post_meta( $request->get_param('id'), 'wp_travel_advanced_gallery' )[0]['items'] as $data ){
				array_push( $gallery_images, $data['id'] );
			}
		}else{
			$gallery_images = get_post_meta( $request->get_param('id'), 'wp_travel_itinerary_gallery_ids' )[0];
		}
	}else{
		$gallery_images = get_post_meta( $request->get_param('id'), 'wp_travel_itinerary_gallery_ids' )[0];
	}

	foreach( $gallery_images as $image ){
		$data['url'] = wp_get_attachment_image_url($image, 'full', true);
		array_push( $set_images, $data );
	}

	return $set_images;
}



