<?php
/**
 * 
 * Render Callback For Trip Search
 * 
 */

function wptravel_block_trip_search_render( $attributes ){

	$show_input     = isset( $attributes['showInput'] ) ? $attributes['showInput'] : true; 
	$show_trip_type = isset( $attributes['showTripType'] ) ? $attributes['showTripType'] : true; 
	$show_location  = isset( $attributes['showLocation'] ) ? $attributes['showLocation'] : true; 
	$search_label	= isset( $attributes['searchButtonLabel'] ) ? $attributes['searchButtonLabel'] : "Search";
	$border_props	= isset( $attributes['inputBorder'] ) ? $attributes['inputBorder'] : json_decode('
		{
			"color": "var(--wp--preset--color--primary)",
			"style": "solid",
			"width": "1px"
		}
	');
	$search_border_radius = isset( $attributes['searchBorderRadius'] ) ? $attributes['searchBorderRadius'] : true;

	// echo "<pre>"; print_r($border_props);

	$args = array(
		'show_input'     => $show_input,
		'show_trip_type' => $show_trip_type,
		'show_location'  => $show_location,
	);

	$submission_get = WP_Travel::get_sanitize_request();

	$label_string = apply_filters(
		'wp_travel_search_filter_label_strings',
		array(
			'search'        => __( 'Search:', 'wp-travel' ),
			'trip_type'     => __( 'Trip Type:', 'wp-travel' ),
			'location'      => __( 'Location:', 'wp-travel' ),
		)
	);

	$search_string        = ! empty( $label_string['search'] ) ? $label_string['search'] : '';
	$trip_type_string     = ! empty( $label_string['trip_type'] ) ? $label_string['trip_type'] : '';
	$location_string      = ! empty( $label_string['location'] ) ? $label_string['location'] : '';

	ob_start(); 

	?>

	<style>
		.wp-travel-search #wp-travel-search.button.wp-block-button__link.button-primary {
			<?php 
				if(! empty( $attributes['buttonBackgroundColor'] ) ){
			?>
				background-color: <?php echo esc_attr( $attributes['buttonBackgroundColor'] ); ?>;
			<?php }	if(! empty( $attributes['buttonTextColor'] ) ){
			?>
				color: <?php echo esc_attr( $attributes['buttonTextColor'] ); ?>;
			<?php }	if(! empty( $attributes['searchBorderRadius'] ) ){
			?>
				border-radius: <?php echo esc_attr( $attributes['searchBorderRadius'] ); ?>px;
			<?php }	?>
			
		}
		.wptravel-block-trip-search,
		body .is-layout-constrained > :where(:not(.alignleft):not(.alignright):not(.alignfull)).wptravel-block-trip-search {
			<?php 
				if( isset( $attributes['backgroundColor'] ) ){
			?>
					background-color: var(--wp--preset--color--<?php echo esc_attr( $attributes['backgroundColor'] ); ?>);
			<?php   
				}else{
					if( isset( $attributes['style']['color'] ) ){
				?>
					background-color: <?php echo esc_attr( $attributes['style']['color']['background'] ); ?>;
				<?php
					}
				} 
			?>

			<?php 
				if( isset( $attributes['textColor'] ) ){
			?>
					color: var(--wp--preset--color--<?php echo esc_attr( $attributes['textColor'] )?>);
			<?php   
				}else{
					if( isset( $attributes['style']['color'] ) ){
				?>
					color: <?php echo esc_attr( $attributes['style']['color']['text'] ); ?>;
				<?php
					}
				} 
			?>
			
			<?php if( !empty( $attributes['style']['spacing']['padding']['top'] ) ){ 
				$vertical_padding = str_replace( 'var:preset|spacing|', '', $attributes['style']['spacing']['padding']['top'] );
				$vertical_padding = str_replace( 'px', '', $vertical_padding );
			?>
				padding-top: <?php echo esc_attr( $vertical_padding ); ?>px !important;
				padding-bottom: <?php echo esc_attr( $vertical_padding ); ?>px !important;
			<?php } ?>

			<?php if( !empty( $attributes['style']['spacing']['padding']['left'] ) ){ 
				$horizontal_padding = str_replace( 'var:preset|spacing|', '', $attributes['style']['spacing']['padding']['left'] );
				$horizontal_padding = str_replace( 'px', '', $horizontal_padding );
			?>
				padding-left: <?php echo esc_attr( $horizontal_padding ); ?>px !important;
				padding-right: <?php echo esc_attr( $horizontal_padding ); ?>px !important;
			<?php } ?>

			<?php if( !empty( $attributes['style']['spacing']['margin']['top'] ) ){ 
				$vertical_margin = str_replace( 'var:preset|spacing|', '', $attributes['style']['spacing']['margin']['top'] );
				$vertical_margin = str_replace( 'px', '', $vertical_margin );
			?>
				margin-top: <?php echo esc_attr( $vertical_margin ); ?>px !important;
				margin-bottom: <?php echo esc_attr( $vertical_margin ); ?>px !important;
			<?php } ?>

			<?php if( !empty( $attributes['style']['spacing']['margin']['left'] ) ){ 
				$horizontal_margin = str_replace( 'var:preset|spacing|', '', $attributes['style']['spacing']['margin']['left'] );
				$horizontal_margin = str_replace( 'px', '', $horizontal_margin );
			?>
				margin-left: <?php echo esc_attr( $horizontal_margin ); ?>px !important;
				margin-right: <?php echo esc_attr( $horizontal_margin ); ?>px !important;
			<?php } ?>

			<?php if( !empty( $attributes['align'] ) && $attributes['align'] == 'full' ){ ?>
				max-width: 100% !important;
			<?php } ?>

			<?php if( !empty( $attributes['align'] ) && $attributes['align'] == 'wide' ){ ?>
				max-width: var(--wp--style--global--wide-size) !important;
			<?php } ?>
		}


		#wptravel-block-trip-search.wptravel-block-trip-search .wp-travel-search form p input:not(#wp-travel-search), #wptravel-block-trip-search.wptravel-block-trip-search form select.wp-travel-taxonomy {
			<?php 
				if(! empty( $attributes['inputBorder']['color'] ) ){
			?>
				border-color: <?php echo esc_attr( $attributes['inputBorder']['color'] ); ?>;
			<?php }	?>
			<?php 
				if(! empty( $attributes['inputBorder']['style'] ) ){
			?>
				border-style: <?php echo esc_attr( $attributes['inputBorder']['style'] ); ?>;
			<?php }	?>
			<?php 
				if(! empty( $attributes['inputBorder']['width'] ) ){
			?>
				border-width: <?php echo esc_attr( $attributes['inputBorder']['width'] ); ?>;
			<?php }	if(! empty( $attributes['inputBorderRadius'] ) ){
			?>
				border-radius: <?php echo esc_attr( $attributes['inputBorderRadius'] ); ?>px;
			<?php }	?>
		}

		.wptravel-layout-v2 #wptravel-block-trip-search.wptravel-block-trip-search .wp-travel-search form p input, .wptravel-layout-v2 #wptravel-block-trip-search form select.wp-travel-taxonomy {
			<?php 
				if(! empty( $attributes['inputBorderRadius'] ) ){
			?>
				border-radius: <?php echo esc_attr( $attributes['inputBorderRadius'] ); ?>;
			<?php }	?>
		}
	</style>

	<div id="wptravel-block-trip-search" class="wptravel-block-wrapper wptravel-block-trip-search wptravel-block-preview">
		<?php 
			// wptravel_search_form( $args );

		?>
		<div class="wp-travel-search">
			<form method="get" name="wp-travel_search" action="<?php echo esc_url( home_url( '/' ) ); ?>" >
				<input type="hidden" name="post_type" value="<?php echo esc_attr( WP_TRAVEL_POST_TYPE ); ?>" />
				<?php if ( $show_input ) : ?>
				<p>
					<label><?php echo esc_html( $search_string ); ?></label>
					<?php $placeholder = __( 'Ex: Trekking', 'wp-travel' ); ?>
					<input type="text" name="wts" id="wts" value="<?php the_search_query(); ?>" placeholder="<?php echo esc_attr( apply_filters( 'wp_travel_search_placeholder', $placeholder ) ); ?>">
				</p>
				<?php endif; ?>
				<?php if ( $show_trip_type ) : ?>
				<p>
					<label><?php echo esc_html( $trip_type_string ); ?></label>
					<?php
					$taxonomy = 'itinerary_types';
					$args     = array(
						'show_option_all' => __( 'All', 'wp-travel' ),
						'hide_empty'      => 0,
						'selected'        => 1,
						'hierarchical'    => 1,
						'name'            => $taxonomy,
						'class'           => 'wp-travel-taxonomy',
						'taxonomy'        => $taxonomy,
						'selected'        => ( isset( $submission_get[ $taxonomy ] ) ) ? esc_textarea( $submission_get[ $taxonomy ] ) : 0,
						'value_field'     => 'slug',
						'order'           => 'asc',
						'orderby'         => 'title',
					);

					wp_dropdown_categories( $args, $taxonomy );
					?>
				</p>
				<?php endif; ?>
				<?php if ( $show_location ) : ?>
				<p>
					<label><?php echo esc_html( $location_string ); ?></label>
					<?php
					$taxonomy = 'travel_locations';
					$args     = array(
						'show_option_all' => __( 'All', 'wp-travel' ),
						'hide_empty'      => 0,
						'selected'        => 1,
						'hierarchical'    => 1,
						'name'            => $taxonomy,
						'class'           => 'wp-travel-taxonomy',
						'taxonomy'        => $taxonomy,
						'selected'        => ( isset( $submission_get[ $taxonomy ] ) ) ? esc_textarea( $submission_get[ $taxonomy ] ) : 0,
						'value_field'     => 'slug',
					);

					wp_dropdown_categories( $args, $taxonomy );
					?>
				</p>
				<?php endif; ?>
				<?php WP_Travel::create_nonce_field(); ?>

				<p class="wp-travel-search"><input type="submit" name="wp-travel_search" id="wp-travel-search" class="button wp-block-button__link button-primary" value="<?php echo esc_attr( $search_label ); ?>"  /></p>
			</form>
		</div>
	</div>
	<?php
	$html = ob_get_clean();

	return $html;

}