<?php

/**
 * 
 * Render Callback For Trip List
 * 
 */
function wptravel_block_trip_list_render($attributes, $content)
{

	$query_args = isset($attributes['query']) ? $attributes['query'] : array();
	$sale_trip = '';
	$featured_trip = '';
	// Legacy Block Compatibility & fixed conflict with yoast.
	if (isset($attributes['location'])) {
		$filter_term = get_term($attributes['location'], 'travel_locations');
		if (is_object($filter_term) && isset($filter_term->term_id)) {
			$selected_term                          = array(
				'count'       => $filter_term->count,
				'id'          => $filter_term->term_id,
				'description' => $filter_term->description,
				'taxonomy'    => $filter_term->taxonomy,
				'name'        => $filter_term->name,
				'slug'        => $filter_term->slug,
			);
			$query_args['selectedTripDestinations'] = array($selected_term);
		}
	}
	if (isset($attributes['tripType'])) {
		$filter_term = get_term($attributes['tripType'], 'itinerary_types');
		if (is_object($filter_term) && isset($filter_term->term_id)) {
			$selected_term                   = array(
				'count'       => $filter_term->count,
				'id'          => $filter_term->term_id,
				'description' => $filter_term->description,
				'taxonomy'    => $filter_term->taxonomy,
				'name'        => $filter_term->name,
				'slug'        => $filter_term->slug,
			);
			$query_args['selectedTripTypes'] = array($selected_term);
		}
	}

	// Options / Attributes.
	$numberposts = isset($query_args['numberOfItems']) && $query_args['numberOfItems'] ? $query_args['numberOfItems'] : 3;

	$layout_type = isset($attributes['layoutType']) ? $attributes['layoutType'] : 'default-layout';
	$card_layout = isset($attributes['cardLayout']) ? $attributes['cardLayout'] : 'grid-view';

	$args = array(
		'post_type'    => WP_TRAVEL_POST_TYPE,
		'post__not_in' => array(get_the_ID()),
	);

	if (isset($query_args['orderBy'])) {
		switch ($query_args['orderBy']) {
			case 'title':
				$args['orderby'] = 'post_title';
				break;
			case 'date':
				$args['orderby'] = 'post_date';
				break;
		}
		$args['order'] = $query_args['order'];
	}

	if ($attributes['relatedTrip']) {
		$args['posts_per_page'] = -1;


		if( $attributes['relatedTripBy'] == 'travel-locations' &&  get_the_terms( get_the_id(), 'travel_locations' ) ){
			$args['travel_locations'] = array();
			$i = 0;
			foreach( get_the_terms( get_the_id(), 'travel_locations' ) as $data ){
				$args['travel_locations'][$i] = $data->slug;
				$i++;
			}
		}

		if( $attributes['relatedTripBy'] == 'itinerary-types' && get_the_terms( get_the_id(), 'itinerary_types' ) ){
			$args['itinerary_types'] = array();
			$i = 0;
			foreach( get_the_terms( get_the_id(), 'itinerary_types' ) as $data ){
				$args['itinerary_types'][$i] = $data->slug;
				$i++;
			}
		}


		if( $attributes['relatedTripBy'] == 'travel-keywords' && get_the_terms( get_the_id(), 'travel_keywords' ) ){
			$args['travel_keywords'] = array();
			$i = 0;
			foreach( get_the_terms( get_the_id(), 'travel_keywords' ) as $data ){
				$args['travel_keywords'][$i] = $data->slug;
				$i++;
			}
		}

		if( $attributes['relatedTripBy'] == 'activity' && get_the_terms( get_the_id(), 'activity' ) ){
			$args['activity'] = array();
			$i = 0;
			foreach( get_the_terms( get_the_id(), 'activity' ) as $data ){
				$args['activity'][$i] = $data->slug;
				$i++;
			}
		}
	} else {
		$args['posts_per_page'] = $numberposts;
		if (isset($query_args['selectedTripTypes']) && !empty($query_args['selectedTripTypes'])) {
			$args['itinerary_types'] = wp_list_pluck($query_args['selectedTripTypes'], 'slug');
		}
		if (isset($query_args['selectedTripDestinations']) && !empty($query_args['selectedTripDestinations'])) {
			$args['travel_locations'] = wp_list_pluck($query_args['selectedTripDestinations'], 'slug');
		}

		if (isset($query_args['selectedTripActivities']) && !empty($query_args['selectedTripActivities'])) {
			$args['activity'] = wp_list_pluck($query_args['selectedTripActivities'], 'slug');
		}

		if (isset($query_args['selectedTripKeywords']) && !empty($query_args['selectedTripKeywords'])) {
			$args['travel_keywords'] = wp_list_pluck($query_args['selectedTripKeywords'], 'slug');
		}

		// Meta Query.
		$sale_trip     = isset($attributes['saleTrip']) ? $attributes['saleTrip'] : false;
		$featured_trip = isset($attributes['featuredTrip']) ? $attributes['featuredTrip'] : false;
		if ($sale_trip) {
			$args['sale_trip'] = $sale_trip;
		}
		if ($featured_trip) {
			$args['featured_trip'] = $featured_trip;
		}
	}

	ob_start();


	$trip_data = WpTravel_Helpers_Trips::filter_trips($args);


	if (is_array($trip_data) && isset($trip_data['code']) && 'WP_TRAVEL_FILTER_RESULTS' === $trip_data['code']) {
		$trips          = $trip_data['trip'];
		$trip_ids       = wp_list_pluck($trips, 'id');
		$col_per_row    = 3;
		if ($numberposts < 3) {
			$col_per_row = $numberposts;
		}
		$layout_version = 'v1';
		if (function_exists('wptravel_layout_version')) {
			$layout_version = wptravel_layout_version();
		}

		if ($attributes['inheritTrips']) {
?>

			<div id="wptravel-block-trips-list" class="wptravel-block-wrapper wptravel-block-trips-list wptravel-block-preview <?php echo esc_attr( $layout_type ). ' ' . 'block-id-' . hash('sha256', json_encode($attributes)); ?>">
				<div class="wp-travel-itinerary-items">
					<?php

					if (have_posts()) { ?>
						<div class="wp-travel-itinerary-items wptravel-archive-wrapper  <?php echo $layout_type == 'default-layout' ? 'grid-view' : esc_attr( $card_layout ); ?>">
							<?php while (have_posts()) {
								the_post();
								$trip_id = get_the_ID();
								$is_featured_trip = get_post_meta($trip_id, 'wp_travel_featured', true);
								$is_fixed_departure = WP_Travel_Helpers_Trip_Dates::is_fixed_departure($trip_id);
								$trip_locations = get_the_terms($trip_id, 'travel_locations');
								$location_name = '';
								$location_link = '';
								$group_size = wptravel_get_group_size($trip_id);

								if ($trip_locations && is_array($trip_locations)) {
									$first_location = array_shift($trip_locations);
									$location_name  = $first_location->name;
									$location_link  = get_term_link($first_location->term_id, 'travel_locations');
								}

								$args = $args_regular = array('trip_id' => $trip_id);
								$trip_price = WP_Travel_Helpers_Pricings::get_price($args);
								$args_regular['is_regular_price'] = true;
								$regular_price = WP_Travel_Helpers_Pricings::get_price($args_regular);
								$is_enable_sale = WP_Travel_Helpers_Trips::is_sale_enabled(
									array(
										'trip_id'                => $trip_id,
										'from_price_sale_enable' => true,
									)
								);
								$trip_url = get_the_permalink();

								if ($card_layout == 'grid-view' || $card_layout == 'slider-view') {
									/**
									 * 
									 */
									if ($layout_type == 'default-layout') {
										wptravel_get_block_template_part('v2/content', 'archive-itineraries');
									} elseif ($layout_type == 'layout-one') {
										include(WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-one.php");
									} elseif ($layout_type == 'layout-two') {
										include(WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-two.php");
									} elseif ($layout_type == 'layout-three') {
										include(WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-three.php");
									} elseif ($layout_type == 'layout-four') {
										include(WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-four.php");
									}

									/**
									 * For future 
									 */
									// wp_travel_get_template_layout( $layout_type );
								}

								if ($card_layout == 'list-view') {
									if ($layout_type == 'default-layout') {
										wptravel_get_block_template_part('v2/content', 'archive-itineraries');
									} elseif ($layout_type == 'layout-one') { ?>
										<div class="wptravel-blocks-trip-card">
											<div class="wptravel-blocks-trip-card-img-container">
												<a href="<?php echo esc_url($trip_url); ?>">
													<?php echo wptravel_get_post_thumbnail($trip_id, 'wp_travel_thumbnail'); ?>
												</a>
												<div class="wptravel-blocks-img-overlay">
													<?php if ($is_featured_trip == 'yes') { ?>
														<div class="wptravel-blocks-trip-featured">
															<i class="fas fa-crown"></i>
														</div>
													<?php } ?>
													<div class="wptravel-blocks-floating-container">
														<div class="wptravel-blocks-trip-code">
															<span class="code-hash">#</span> <?php echo wptravel_get_trip_code($trip_id) ?>
														</div>
													</div>
												</div>
											</div>
											<div class="wptravel-blocks-card-body">
												<div class="wptravel-blocks-card-body-top">
													<div class="wptravel-blocks-card-body-header">
														<a href="<?php echo esc_url($trip_url); ?>">
															<h3 class="wptravel-blocks-card-title">
																<?php the_title(); ?>
															</h3>
														</a>
														<?php do_action('wp_travel_after_archive_title', $trip_id); ?>
													</div>
													<div class="wptravel-blocks-card-content">
														<?php if ($is_fixed_departure) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class='far fa-calendar-alt'></i> <?php echo wptravel_get_fixed_departure_date($trip_id); ?>
															</div>
														<?php } else { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class='far fa-clock'></i> <?php echo wp_travel_get_trip_durations($trip_id); ?>
															</div>
														<?php } ?>
														<?php if ($trip_locations) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class="fas fa-map-marker-alt"></i>
																<a href="<?php echo esc_url($location_link); ?>">
																	<?php echo esc_html($location_name); ?>
																</a>
															</div>
														<?php } ?>
														<?php if ($group_size) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class="fas fa-users"></i> <?php echo ((int) $group_size && $group_size < 999) ?  wptravel_get_group_size($trip_id) : 'No Size Limit' ?>
															</div>
														<?php } ?>
													</div>
												</div>

												<div class="wptravel-blocks-card-footer">
													<div class="wptravel-blocks-footer-left">
														<div class="wptravel-blocks-trip-rating">
															<?php echo wptravel_single_trip_rating($trip_id); ?>
														</div>
														<div class="wptravel-blocks-trip-explore">
															<a href="<?php echo esc_url($trip_url); ?>">
																<button class="wp-block-button__link wptravel-blocks-explore-btn"><?php echo esc_html__( 'Explore', 'wp-travel-blocks' ); ?></button>
															</a>
														</div>
													</div>
													<div class="wptravel-blocks-footer-right">
														<?php if ($is_enable_sale && $regular_price > $trip_price) { ?>
															<div class="wptravel-blocks-trip-offer">
																<?php
																$save = (1 - ((int) $trip_price / (int) $regular_price)) * 100;
																$save = number_format($save, 2, '.', ',');
																echo esc_html__( 'Save ', 'wp-travel-blocks' ) . $save . "%";
																?>
															</div>
															<div class="wptravel-blocks-trip-original-price">
																<del><?php echo wptravel_get_formated_price_currency($regular_price); ?></del>
															</div>
															<div class="wptravel-blocks-trip-offer-price"><?php echo wptravel_get_formated_price_currency($trip_price); ?></div>
														<?php } else { ?>
															<div class="wptravel-blocks-trip-offer-price">
																<?php echo wptravel_get_formated_price_currency($regular_price); ?>
															</div>
														<?php } ?>
													</div>
												</div>
											</div>
										</div>
									<?php } elseif ($layout_type == 'layout-two') { ?>
										<div class="wptravel-blocks-trip-card">
											<div class="wptravel-blocks-trip-card-img-container">
												<a href="<?php echo esc_url($trip_url); ?>">
													<?php echo wptravel_get_post_thumbnail($trip_id, 'wp_travel_thumbnail'); ?>
												</a>
												<div class="wptravel-blocks-img-overlay-base">
													<div class="wptravel-blocks-img-overlay">
														<?php if ($is_featured_trip == 'yes') { ?>
															<div class="wptravel-blocks-trip-featured">
																<i class="fas fa-crown"></i> <?php echo esc_html__('Featured', 'wp-travel-blocks') ?>
															</div>
														<?php } ?>
														<?php do_action('wp_travel_after_archive_title', $trip_id); ?>
													</div>
												</div>
											</div>
											<div class="wptravel-blocks-card-body">
												<div class="wptravel-blocks-card-body-header">
													<div class="wptravel-blocks-card-body-header-left">
														<a href="<?php echo esc_url($trip_url); ?>">
															<h3 class="wptravel-blocks-card-title">
																<?php the_title(); ?>
															</h3>
														</a>
														<?php if ($trip_locations) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class="fas fa-map-marker-alt"></i>
																<a href="<?php echo esc_url($location_link); ?>">
																	<?php echo esc_html($location_name); ?>
																</a>
															</div>
														<?php } ?>
													</div>
													<div class="wptravel-blocks-trip-rating">
														<i class="fas fa-star"></i> <?php echo wptravel_get_average_rating($trip_id); ?>
													</div>
												</div>
												<div class="wptravel-blocks-card-content">
													<div class="wptravel-blocks-trip-excerpt">
														<?php the_excerpt() ?>
													</div>
													<div class="wptravel-blocks-sep"></div>
													<div class="wptravel-blocks-content-right">
														<?php if ($is_enable_sale && $regular_price > $trip_price) { ?>
															<div class="wptravel-blocks-trip-offer">
																<?php
																$save = (1 - ((int) $trip_price / (int) $regular_price)) * 100;
																$save = number_format($save, 2, '.', ',');
																echo esc_html__( 'Save ', 'wp-travel-blocks' ) . $save . "%";
																?>
															</div>
															<div class="wptravel-blocks-trip-original-price">
																<del><?php echo wptravel_get_formated_price_currency($regular_price); ?></del>
															</div>
															<div class="wptravel-blocks-trip-offer-price"><?php echo wptravel_get_formated_price_currency($trip_price); ?></div>
														<?php } else { ?>
															<div class="wptravel-blocks-trip-offer-price">
																<?php echo wptravel_get_formated_price_currency($regular_price); ?>
															</div>
														<?php } ?>
													</div>
												</div>
												<div class="wptravel-blocks-card-footer">
													<div class="wptravel-blocks-footer-left">
														<?php if ($is_fixed_departure) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class='far fa-calendar-alt'></i> <?php echo wptravel_get_fixed_departure_date($trip_id); ?>
															</div>
														<?php } else { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class='far fa-clock'></i> <?php echo wp_travel_get_trip_durations($trip_id); ?>
															</div>
														<?php } ?>
														<?php if ($group_size) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class="fas fa-users"></i> <?php echo ((int) $group_size && $group_size < 999) ?  wptravel_get_group_size($trip_id) : esc_html__( 'No Size Limit', 'wp-travel-blocks' ) ?>
															</div>
														<?php } ?>
													</div>
													<div class="wptravel-blocks-footer-right">
														<div class="wptravel-blocks-trip-explore">
															<a href="<?php echo esc_url($trip_url); ?>">
																<button class="wp-block-button__link wptravel-blocks-explore-btn"><?php echo esc_html__('Explore', 'wp-travel-blocks') ?></button>
															</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } elseif ($layout_type == 'layout-three') { ?>
										<div class="wptravel-blocks-trip-card">
											<div class="wptravel-blocks-trip-card-img-container">
												<a href="<?php echo esc_url($trip_url); ?>">
													<?php echo wptravel_get_post_thumbnail($trip_id, 'wp_travel_thumbnail'); ?>
												</a>
												<div class="wptravel-blocks-img-overlay-base">
													<div class="wptravel-blocks-img-overlay">
														<?php if ($is_featured_trip == 'yes') { ?>
															<div class="wptravel-blocks-trip-featured">
																<i class="fas fa-crown"></i>
															</div>
														<?php } ?>
													</div>
												</div>
											</div>
											<div class="wptravel-blocks-card-body">
												<div class="wptravel-blocks-card-body-top">
													<div class="wptravel-blocks-card-body-header">
														<div class="wptravel-blocks-header-left">
															<a href="<?php echo esc_url($trip_url); ?>">
																<h3 class="wptravel-blocks-card-title">
																	<?php the_title(); ?>
																</h3>
															</a>
															<div class="wptravel-blocks-trip-code">
																<span class="code-hash">#</span> <?php echo wptravel_get_trip_code($trip_id) ?>
															</div>
														</div>
														<?php do_action('wp_travel_after_archive_title', $trip_id); ?>
													</div>
													<div class="wptravel-blocks-card-content">
														<div class="wptravel-blocks-content-left">
															<div class="wptravel-blocks-trip-excerpt">
																<?php the_excerpt() ?>
															</div>
															<div class="wptravel-blocks-trip-meta-container">
																<?php if ($is_fixed_departure) { ?>
																	<div class="wptravel-blocks-trip-meta">
																		<i class='far fa-calendar-alt'></i> <?php echo wptravel_get_fixed_departure_date($trip_id); ?>
																	</div>
																<?php } else { ?>
																	<div class="wptravel-blocks-trip-meta">
																		<i class='far fa-clock'></i> <?php echo wp_travel_get_trip_durations($trip_id); ?>
																	</div>
																<?php } ?>
																<?php if ($trip_locations) { ?>
																	<div class="wptravel-blocks-trip-meta">
																		<i class="fas fa-map-marker-alt"></i>
																		<a href="<?php echo esc_url($location_link); ?>">
																			<?php echo esc_html($location_name); ?>
																		</a>
																	</div>
																<?php } ?>
																<?php if ($group_size) { ?>
																	<div class="wptravel-blocks-trip-meta">
																		<i class="fas fa-users"></i> <?php echo ((int) $group_size && $group_size < 999) ?  wptravel_get_group_size($trip_id) : 'No Size Limit' ?>
																	</div>
																<?php } ?>
															</div>
														</div>
														<div class="wptravel-blocks-content-right">
															<?php if ($is_enable_sale && $regular_price > $trip_price) { ?>
																<div class="wptravel-blocks-trip-offer">
																	<?php
																	$save = (1 - ((int) $trip_price / (int) $regular_price)) * 100;
																	$save = number_format($save, 2, '.', ',');
																	echo esc_html__( 'Save ', 'wp-travel-blocks' ) . $save . "%";
																	?>
																</div>
																<div class="wptravel-blocks-trip-original-price">
																	<del><?php echo wptravel_get_formated_price_currency($regular_price); ?></del>
																</div>
																<div class="wptravel-blocks-trip-offer-price"><?php echo wptravel_get_formated_price_currency($trip_price); ?></div>
															<?php } else { ?>
																<div class="wptravel-blocks-trip-offer-price">
																	<?php echo wptravel_get_formated_price_currency($regular_price); ?>
																</div>
															<?php } ?>
														</div>
													</div>
												</div>
												<div class="wptravel-blocks-card-footer">
													<div class="wptravel-blocks-footer-left">
														<div class="wptravel-blocks-trip-rating">
															<?php echo wptravel_single_trip_rating($trip_id); ?>
														</div>
													</div>
													<div class="wptravel-blocks-footer-right">
														<div class="wptravel-blocks-trip-explore">
															<a href="<?php echo esc_url($trip_url); ?>">
																<button class="wp-block-button__link wptravel-blocks-explore-btn"><?php echo esc_html__('Explore', 'wp-travel-blocks') ?></button>
															</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } elseif ($layout_type == 'layout-four') { ?>
										<div class="wptravel-blocks-trip-card">
											<div class="wptravel-blocks-trip-card-top">
												<div class="wptravel-blocks-trip-card-img-container">
													<a href="<?php echo esc_url($trip_url); ?>">
														<?php echo wptravel_get_post_thumbnail($trip_id, 'wp_travel_thumbnail'); ?>
													</a>
													<div class="wptravel-blocks-img-overlay">
														<?php if ($is_featured_trip == 'yes') { ?>
															<div class="wptravel-blocks-trip-featured">
																<i class="fas fa-crown"></i> <?php echo esc_html__('Featured', 'wp-travel-blocks') ?>
															</div>
														<?php } ?>
													</div>
												</div>
												<div class="wptravel-blocks-card-body">
													<div class="wptravel-blocks-card-body-top">
														<div class="wptravel-blocks-card-body-header">
															<div class="wptravel-blocks-header-left">
																<a href="<?php echo esc_url($trip_url); ?>">
																	<h3 class="wptravel-blocks-card-title">
																		<?php the_title(); ?>
																	</h3>
																</a>
																<div class="wptravel-blocks-trip-rating">
																	<?php echo wptravel_single_trip_rating($trip_id); ?>
																</div>
															</div>
															<?php do_action('wp_travel_after_archive_title', $trip_id); ?>
														</div>
														<div class="wptravel-blocks-card-content">
															<div class="wptravel-blocks-trip-excerpt">
																<?php the_excerpt() ?>
															</div>
															<div class="wptravel-blocks-trip-pricing">
																<?php if ($is_enable_sale && $regular_price > $trip_price) { ?>
																	<div class="wptravel-blocks-trip-offer">
																		<?php
																		$save = (1 - ((int) $trip_price / (int) $regular_price)) * 100;
																		$save = number_format($save, 2, '.', ',');
																		echo esc_html__( 'Save ', 'wp-travel-blocks' ) . $save . "%";
																		?>
																	</div>
																	<div class="wptravel-blocks-trip-original-price">
																		<del><?php echo wptravel_get_formated_price_currency($regular_price); ?></del>
																	</div>
																	<div class="wptravel-blocks-trip-offer-price"><?php echo wptravel_get_formated_price_currency($trip_price); ?></div>
																<?php } else { ?>
																	<div class="wptravel-blocks-trip-offer-price">
																		<?php echo wptravel_get_formated_price_currency($regular_price); ?>
																	</div>
																<?php } ?>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="wptravel-blocks-card-footer">
												<div class="wptravel-blocks-footer-left">
													<div class="wptravel-blocks-trip-meta-container">
														<?php if ($is_fixed_departure) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class='far fa-calendar-alt'></i> <?php echo wptravel_get_fixed_departure_date($trip_id); ?>
															</div>
														<?php } else { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class='far fa-clock'></i> <?php echo wp_travel_get_trip_durations($trip_id); ?>
															</div>
														<?php } ?>
														<?php if ($trip_locations) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class="fas fa-map-marker-alt"></i>
																<a href="<?php echo esc_url($location_link); ?>">
																	<?php echo esc_html($location_name); ?>
																</a>
															</div>
														<?php } ?>
														<?php if ($group_size) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class="fas fa-users"></i> <?php echo ((int) $group_size && $group_size < 999) ?  wptravel_get_group_size($trip_id) : esc_html__( 'No Size Limit', 'wp-travel-blocks' ) ?>
															</div>
														<?php } ?>
													</div>
												</div>
												<div class="wptravel-blocks-footer-right">
													<div class="wptravel-blocks-trip-explore">
														<a href="<?php echo esc_url($trip_url); ?>">
															<button class="wp-block-button__link wptravel-blocks-explore-btn"><?php echo esc_html__('Explore', 'wp-travel-blocks') ?></button>
														</a>
													</div>
												</div>
											</div>
										</div>
							<?php }
								}
							} ?>
						</div>
						<?php
						$pagination_range = apply_filters('wp_travel_pagination_range', 2);
						$max_num_pages    = apply_filters('wp_travel_max_num_pages', '');
						wptravel_pagination($pagination_range, $max_num_pages);
						?>
					<?php } else {
						echo esc_html__('No related trips found..', 'wp-travel-blocks');
					} ?>
				</div>
			</div>
		<?php
		} else { ?>
			<div id="wptravel-block-trips-list" class="wptravel-block-wrapper wptravel-block-trips-list wptravel-block-preview <?php echo esc_attr( $layout_type ). ' ' . 'block-id-' . hash('sha256', json_encode($attributes)); ?>">
				<div class="wp-travel-itinerary-items">
					<?php
					if ($sale_trip || $featured_trip) {
						$args  = array(
							'post_type' => WP_TRAVEL_POST_TYPE,
							'post__in'  => $trip_ids,
						);
					}
					$query = new WP_Query($args);
					$slider_enable = '';
					if ($card_layout == 'slider-view') {
						$slider_enable = 'grid-view slider-view';
					}

					if ($query->have_posts()) { ?>
						<div class="wp-travel-itinerary-items wptravel-archive-wrapper  <?php echo $layout_type == 'default-layout' ? 'grid-view' : esc_attr( $card_layout ); ?> <?php echo esc_attr( $slider_enable ); ?>">
							<?php while ($query->have_posts()) {
								$query->the_post();
								$trip_id = get_the_ID();
								$is_featured_trip = get_post_meta($trip_id, 'wp_travel_featured', true);
								$is_fixed_departure = WP_Travel_Helpers_Trip_Dates::is_fixed_departure($trip_id);
								$trip_locations = get_the_terms($trip_id, 'travel_locations');
								$location_name = '';
								$location_link = '';
								$group_size = wptravel_get_group_size($trip_id);
								$trip_url = get_the_permalink();

								if ($trip_locations && is_array($trip_locations)) {
									$first_location = array_shift($trip_locations);
									$location_name  = $first_location->name;
									$location_link  = get_term_link($first_location->term_id, 'travel_locations');
								}

								$args = $args_regular = array('trip_id' => $trip_id);
								$trip_price = WP_Travel_Helpers_Pricings::get_price($args);
								$args_regular['is_regular_price'] = true;
								$regular_price = WP_Travel_Helpers_Pricings::get_price($args_regular);
								$is_enable_sale = WP_Travel_Helpers_Trips::is_sale_enabled(
									array(
										'trip_id'                => $trip_id,
										'from_price_sale_enable' => true,
									)
								);

								if ($card_layout == 'grid-view' || $card_layout == 'slider-view') {
									/**
									 * 
									 */
									if ($layout_type == 'default-layout') {
										wptravel_get_block_template_part('v2/content', 'archive-itineraries');
									} elseif ($layout_type == 'layout-one') {
										include(WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-one.php");
									} elseif ($layout_type == 'layout-two') {
										include(WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-two.php");
									} elseif ($layout_type == 'layout-three') {
										include(WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-three.php");
									} elseif ($layout_type == 'layout-four') {
										include(WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-four.php");
									}

									/**
									 * For future 
									 */
									// wp_travel_get_template_layout( $layout_type );
								}

								if ($card_layout == 'list-view') {
									if ($layout_type == 'default-layout') {
										wptravel_get_block_template_part('v2/content', 'archive-itineraries');
									} elseif ($layout_type == 'layout-one') { ?>
										<div class="wptravel-blocks-trip-card">
											<div class="same-height wptravel-blocks-trip-card-img-container">
												<a href="<?php echo esc_url($trip_url); ?>">
													<?php echo wptravel_get_post_thumbnail($trip_id, 'wp_travel_thumbnail'); ?>
												</a>
												<div class="wptravel-blocks-img-overlay">
													<?php if ($is_featured_trip == 'yes') { ?>
														<div class="wptravel-blocks-trip-featured">
															<i class="fas fa-crown"></i>
														</div>
													<?php } ?>
													<div class="wptravel-blocks-floating-container">
														<div class="wptravel-blocks-trip-code">
															<span class="code-hash">#</span> <?php echo wptravel_get_trip_code($trip_id) ?>
														</div>
													</div>
												</div>
											</div>
											<div class="same-height wptravel-blocks-card-body">
												<div class="wptravel-blocks-card-body-top">
													<div class="wptravel-blocks-card-body-header">
														<a href="<?php echo esc_url($trip_url); ?>">
															<h3 class="wptravel-blocks-card-title">
																<?php the_title(); ?>
															</h3>
														</a>
														<?php do_action('wp_travel_after_archive_title', $trip_id); ?>
													</div>
													<div class="wptravel-blocks-card-content">
														<?php if ($is_fixed_departure) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class='far fa-calendar-alt'></i> <?php echo wptravel_get_fixed_departure_date($trip_id); ?>
															</div>
														<?php } else { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class='far fa-clock'></i> <?php echo wp_travel_get_trip_durations($trip_id); ?>
															</div>
														<?php } ?>
														<?php if ($trip_locations) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class="fas fa-map-marker-alt"></i>
																<a href="<?php echo esc_url($location_link); ?>">
																	<?php echo esc_html($location_name); ?>
																</a>
															</div>
														<?php } ?>
														<?php if ($group_size) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class="fas fa-users"></i> <?php echo ((int) $group_size && $group_size < 999) ?  wptravel_get_group_size($trip_id) : esc_html__( 'No Size Limit', 'wp-travel-blocks' ); ?>
															</div>
														<?php } ?>
													</div>
												</div>

												<div class="wptravel-blocks-card-footer">
													<div class="wptravel-blocks-footer-left">
														<div class="wptravel-blocks-trip-rating">
															<?php echo wptravel_single_trip_rating($trip_id); ?>
														</div>
														<div class="wptravel-blocks-trip-explore">
															<a href="<?php echo esc_url($trip_url); ?>">
																<button class="wp-block-button__link wptravel-blocks-explore-btn"><?php echo esc_html__( 'Explore', 'wp-travel-blocks' )?></button>
															</a>
														</div>
													</div>
													<div class="wptravel-blocks-footer-right">
														<?php if ($is_enable_sale && $regular_price > $trip_price) { ?>
															<div class="wptravel-blocks-trip-offer">
																<?php
																$save = (1 - ((int) $trip_price / (int) $regular_price)) * 100;
																$save = number_format($save, 2, '.', ',');
																echo esc_html__( 'Save ', 'wp-travel-blocks' ) . $save . "%";
																?>
															</div>
															<div class="wptravel-blocks-trip-original-price">
																<del><?php echo wptravel_get_formated_price_currency($regular_price); ?></del>
															</div>
															<div class="wptravel-blocks-trip-offer-price"><?php echo wptravel_get_formated_price_currency($trip_price); ?></div>
														<?php } else { ?>
															<div class="wptravel-blocks-trip-offer-price">
																<?php echo wptravel_get_formated_price_currency($regular_price); ?>
															</div>
														<?php } ?>
													</div>
												</div>
											</div>
										</div>
									<?php } elseif ($layout_type == 'layout-two') { ?>
										<div class="wptravel-blocks-trip-card">
											<div class="same-height wptravel-blocks-trip-card-img-container">
												<a href="<?php echo esc_url($trip_url); ?>">
													<?php echo wptravel_get_post_thumbnail($trip_id, 'wp_travel_thumbnail'); ?>
												</a>
												<div class="wptravel-blocks-img-overlay-base">
													<div class="wptravel-blocks-img-overlay">
														<?php if ($is_featured_trip == 'yes') { ?>
															<div class="wptravel-blocks-trip-featured">
																<i class="fas fa-crown"></i> <?php echo esc_html__('Featured', 'wp-travel-blocks') ?>
															</div>
														<?php } ?>
														<?php do_action('wp_travel_after_archive_title', $trip_id); ?>
													</div>
												</div>
											</div>
											<div class="same-height wptravel-blocks-card-body">
												<div class="wptravel-blocks-card-body-header">
													<div class="wptravel-blocks-card-body-header-left">
														<a href="<?php echo esc_url($trip_url); ?>">
															<h3 class="wptravel-blocks-card-title">
																<?php the_title(); ?>
															</h3>
														</a>
														<?php if ($trip_locations) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class="fas fa-map-marker-alt"></i>
																<a href="<?php echo esc_url($location_link); ?>">
																	<?php echo esc_html($location_name); ?>
																</a>
															</div>
														<?php } ?>
													</div>
													<div class="wptravel-blocks-trip-rating">
														<i class="fas fa-star"></i> <?php echo wptravel_get_average_rating($trip_id); ?>
													</div>
												</div>
												<div class="wptravel-blocks-card-content">
													<div class="wptravel-blocks-trip-excerpt">
														<?php the_excerpt() ?>
													</div>
													<div class="wptravel-blocks-sep"></div>
													<div class="wptravel-blocks-content-right">
														<?php if ($is_enable_sale && $regular_price > $trip_price) { ?>
															<div class="wptravel-blocks-trip-offer">
																<?php
																$save = (1 - ((int) $trip_price / (int) $regular_price)) * 100;
																$save = number_format($save, 2, '.', ',');
																echo esc_html__( 'Save ', 'wp-travel-blocks' ) . $save . "%";
																?>
															</div>
															<div class="wptravel-blocks-trip-original-price">
																<del><?php echo wptravel_get_formated_price_currency($regular_price); ?></del>
															</div>
															<div class="wptravel-blocks-trip-offer-price"><?php echo wptravel_get_formated_price_currency($trip_price); ?></div>
														<?php } else { ?>
															<div class="wptravel-blocks-trip-offer-price">
																<?php echo wptravel_get_formated_price_currency($regular_price); ?>
															</div>
														<?php } ?>
													</div>
												</div>
												<div class="wptravel-blocks-card-footer">
													<div class="wptravel-blocks-footer-left">
														<?php if ($is_fixed_departure) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class='far fa-calendar-alt'></i> <?php echo wptravel_get_fixed_departure_date($trip_id); ?>
															</div>
														<?php } else { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class='far fa-clock'></i> <?php echo wp_travel_get_trip_durations($trip_id); ?>
															</div>
														<?php } ?>
														<?php if ($group_size) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class="fas fa-users"></i> <?php echo ((int) $group_size && $group_size < 999) ?  wptravel_get_group_size($trip_id) : esc_html__( 'No Size Limit', 'wp-travel-blocks' ) ?>
															</div>
														<?php } ?>
													</div>
													<div class="wptravel-blocks-footer-right">
														<div class="wptravel-blocks-trip-explore">
															<a href="<?php echo esc_url($trip_url); ?>">
																<button class="wp-block-button__link wptravel-blocks-explore-btn"><?php echo esc_html__('Explore', 'wp-travel-blocks') ?></button>
															</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } elseif ($layout_type == 'layout-three') { ?>
										<div class="wptravel-blocks-trip-card">
											<div class="same-height wptravel-blocks-trip-card-img-container">
												<a href="<?php echo esc_url($trip_url); ?>">
													<?php echo wptravel_get_post_thumbnail($trip_id, 'wp_travel_thumbnail'); ?>
												</a>
												<div class="wptravel-blocks-img-overlay-base">
													<div class="wptravel-blocks-img-overlay">
														<?php if ($is_featured_trip == 'yes') { ?>
															<div class="wptravel-blocks-trip-featured">
																<i class="fas fa-crown"></i>
															</div>
														<?php } ?>
													</div>
												</div>
											</div>
											<div class="same-height wptravel-blocks-card-body">
												<div class="wptravel-blocks-card-body-top">
													<div class="wptravel-blocks-card-body-header">
														<div class="wptravel-blocks-header-left">
															<a href="<?php echo esc_url($trip_url); ?>">
																<h3 class="wptravel-blocks-card-title">
																	<?php the_title(); ?>
																</h3>
															</a>
															<div class="wptravel-blocks-trip-code">
																<span class="code-hash">#</span> <?php echo wptravel_get_trip_code($trip_id) ?>
															</div>
														</div>
														<?php do_action('wp_travel_after_archive_title', $trip_id); ?>
													</div>
													<div class="wptravel-blocks-card-content">
														<div class="wptravel-blocks-content-left">
															<div class="wptravel-blocks-trip-excerpt">
																<?php the_excerpt() ?>
															</div>
															<div class="wptravel-blocks-trip-meta-container">
																<?php if ($is_fixed_departure) { ?>
																	<div class="wptravel-blocks-trip-meta">
																		<i class='far fa-calendar-alt'></i> <?php echo wptravel_get_fixed_departure_date($trip_id); ?>
																	</div>
																<?php } else { ?>
																	<div class="wptravel-blocks-trip-meta">
																		<i class='far fa-clock'></i> <?php echo wp_travel_get_trip_durations($trip_id); ?>
																	</div>
																<?php } ?>
																<?php if ($trip_locations) { ?>
																	<div class="wptravel-blocks-trip-meta">
																		<i class="fas fa-map-marker-alt"></i>
																		<a href="<?php echo esc_url($location_link); ?>">
																			<?php echo esc_html($location_name); ?>
																		</a>
																	</div>
																<?php } ?>
																<?php if ($group_size) { ?>
																	<div class="wptravel-blocks-trip-meta">
																		<i class="fas fa-users"></i> <?php echo ((int) $group_size && $group_size < 999) ?  wptravel_get_group_size($trip_id) : esc_html__( 'No Size Limit', 'wp-travel-blocks' ) ?>
																	</div>
																<?php } ?>
															</div>
														</div>
														<div class="wptravel-blocks-content-right">
															<?php if ($is_enable_sale && $regular_price > $trip_price) { ?>
																<div class="wptravel-blocks-trip-offer">
																	<?php
																	$save = (1 - ((int) $trip_price / (int) $regular_price)) * 100;
																	$save = number_format($save, 2, '.', ',');
																	echo esc_html__( 'Save ', 'wp-travel-blocks' ) . $save . "%";
																	?>
																</div>
																<div class="wptravel-blocks-trip-original-price">
																	<del><?php echo wptravel_get_formated_price_currency($regular_price); ?></del>
																</div>
																<div class="wptravel-blocks-trip-offer-price"><?php echo wptravel_get_formated_price_currency($trip_price); ?></div>
															<?php } else { ?>
																<div class="wptravel-blocks-trip-offer-price">
																	<?php echo wptravel_get_formated_price_currency($regular_price); ?>
																</div>
															<?php } ?>
														</div>
													</div>
												</div>
												<div class="wptravel-blocks-card-footer">
													<div class="wptravel-blocks-footer-left">
														<div class="wptravel-blocks-trip-rating">
															<?php echo wptravel_single_trip_rating($trip_id); ?>
														</div>
													</div>
													<div class="wptravel-blocks-footer-right">
														<div class="wptravel-blocks-trip-explore">
															<a href="<?php echo esc_url($trip_url); ?>">
																<button class="wp-block-button__link wptravel-blocks-explore-btn"><?php echo esc_html__('Explore', 'wp-travel-blocks') ?></button>
															</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } elseif ($layout_type == 'layout-four') { ?>
										<div class="wptravel-blocks-trip-card">
											<div class="same-height wptravel-blocks-trip-card-top">
												<div class="wptravel-blocks-trip-card-img-container">
													<a href="<?php echo esc_url($trip_url); ?>">
														<?php echo wptravel_get_post_thumbnail($trip_id, 'wp_travel_thumbnail'); ?>
													</a>
													<div class="wptravel-blocks-img-overlay">
														<?php if ($is_featured_trip == 'yes') { ?>
															<div class="wptravel-blocks-trip-featured">
																<i class="fas fa-crown"></i> <?php echo esc_html__('Featured', 'wp-travel-blocks') ?>
															</div>
														<?php } ?>
													</div>
												</div>
												<div class="same-height wptravel-blocks-card-body">
													<div class="wptravel-blocks-card-body-top">
														<div class="wptravel-blocks-card-body-header">
															<div class="wptravel-blocks-header-left">
																<a href="<?php echo esc_url($trip_url); ?>">
																	<h3 class="wptravel-blocks-card-title">
																		<?php the_title(); ?>
																	</h3>
																</a>
																<div class="wptravel-blocks-trip-rating">
																	<?php echo wptravel_single_trip_rating($trip_id); ?>
																</div>
															</div>
															<?php do_action('wp_travel_after_archive_title', $trip_id); ?>
														</div>
														<div class="wptravel-blocks-card-content">
															<div class="wptravel-blocks-trip-excerpt">
																<?php the_excerpt() ?>
															</div>
															<div class="wptravel-blocks-trip-pricing">
																<?php if ($is_enable_sale && $regular_price > $trip_price) { ?>
																	<div class="wptravel-blocks-trip-offer">
																		<?php
																		$save = (1 - ((int) $trip_price / (int) $regular_price)) * 100;
																		$save = number_format($save, 2, '.', ',');
																		echo esc_html__( 'Save ', 'wp-travel-blocks' ) . $save . "%";
																		?>
																	</div>
																	<div class="wptravel-blocks-trip-original-price">
																		<del><?php echo wptravel_get_formated_price_currency($regular_price); ?></del>
																	</div>
																	<div class="wptravel-blocks-trip-offer-price"><?php echo wptravel_get_formated_price_currency($trip_price); ?></div>
																<?php } else { ?>
																	<div class="wptravel-blocks-trip-offer-price">
																		<?php echo wptravel_get_formated_price_currency($regular_price); ?>
																	</div>
																<?php } ?>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="wptravel-blocks-card-footer">
												<div class="wptravel-blocks-footer-left">
													<div class="wptravel-blocks-trip-meta-container">
														<?php if ($is_fixed_departure) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class='far fa-calendar-alt'></i> <?php echo wptravel_get_fixed_departure_date($trip_id); ?>
															</div>
														<?php } else { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class='far fa-clock'></i> <?php echo wp_travel_get_trip_durations($trip_id); ?>
															</div>
														<?php } ?>
														<?php if ($trip_locations) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class="fas fa-map-marker-alt"></i>
																<a href="<?php echo esc_url($location_link); ?>">
																	<?php echo esc_html($location_name); ?>
																</a>
															</div>
														<?php } ?>
														<?php if ($group_size) { ?>
															<div class="wptravel-blocks-trip-meta">
																<i class="fas fa-users"></i> <?php echo ((int) $group_size && $group_size < 999) ?  wptravel_get_group_size($trip_id) : esc_html__( 'No Size Limit', 'wp-travel-blocks' ) ?>
															</div>
														<?php } ?>
													</div>
												</div>
												<div class="wptravel-blocks-footer-right">
													<div class="wptravel-blocks-trip-explore">
														<a href="<?php echo esc_url($trip_url); ?>">
															<button class="wp-block-button__link wptravel-blocks-explore-btn"><?php echo esc_html__('Explore', 'wp-travel-blocks') ?></button>
														</a>
													</div>
												</div>
											</div>
										</div>
							<?php }
								}
							} ?>
						</div>
					<?php } else {
						echo esc_html__('No related trips found..', 'wp-travel-blocks');
					} ?>
				</div>
			</div>
		<?php }
		?>


		<script>
			jQuery(document).ready(function(n) {
				var wptravelSlider = n('#wptravel-block-trips-list');
				n('.grid-view.slider-view').slick({
					slidesToShow: 3,
					slidesToScroll: 1,
					autoplay: false,
					dots: false,
					arrows: true,
					infinite: true,
					centerPadding: '20px',
					responsive: [{
							breakpoint: 1024,
							settings: {
								slidesToShow: 3,
								slidesToScroll: 1,
								infinite: true,
								dots: false
							}
						},
						{
							breakpoint: 768,
							settings: {
								slidesToShow: 2,
								slidesToScroll: 1
							}
						},
						{
							breakpoint: 600,
							settings: {
								slidesToShow: 1,
								slidesToScroll: 1
							}
						}
					]
				});
			});
		</script>
	<?php
	} else {
		wptravel_get_block_template_part('shortcode/itinerary', 'item-none');
	}
	?>
	<style>
		.wptravel-block-trips-list.<?php echo 'block-id-' . hash('sha256', json_encode($attributes)) ?>,
		body .is-layout-constrained> :where(:not(.alignleft):not(.alignright):not(.alignfull)).wptravel-block-trips-list.<?php echo 'block-id-' . hash('sha256', json_encode($attributes)) ?> {
			<?php if (!empty($attributes['style']['color']['background'])) { ?>background-color: <?php echo esc_attr($attributes['style']['color']['background']); ?>;
			<?php } ?><?php if (!empty($attributes['style']['color']['text'])) { ?>color: <?php echo esc_attr($attributes['style']['color']['text']); ?>;
			<?php } ?><?php if (!empty($attributes['style']['spacing']['padding']['top'])) {
							$vertical_padding = str_replace('var:preset|spacing|', '', $attributes['style']['spacing']['padding']['top']);
							$vertical_padding = str_replace('px', '', $vertical_padding);
						?>padding-top: <?php echo esc_attr($vertical_padding); ?>px !important;
			padding-bottom: <?php echo esc_attr($vertical_padding); ?>px !important;
			<?php } ?><?php if (!empty($attributes['style']['spacing']['padding']['left'])) {
							$horizontal_padding = str_replace('var:preset|spacing|', '', $attributes['style']['spacing']['padding']['left']);
							$horizontal_padding = str_replace('px', '', $horizontal_padding);
						?>padding-left: <?php echo esc_attr($horizontal_padding); ?>px !important;
			padding-right: <?php echo esc_attr($horizontal_padding); ?>px !important;
			<?php } ?><?php if (!empty($attributes['style']['spacing']['margin']['top'])) {
							$vertical_margin = str_replace('var:preset|spacing|', '', $attributes['style']['spacing']['margin']['top']);
							$vertical_margin = str_replace('px', '', $vertical_margin);
						?>margin-top: <?php echo esc_attr($vertical_margin); ?>px !important;
			margin-bottom: <?php echo esc_attr($vertical_margin); ?>px !important;
			<?php } ?><?php if (!empty($attributes['style']['spacing']['margin']['left'])) {
							$horizontal_margin = str_replace('var:preset|spacing|', '', $attributes['style']['spacing']['margin']['left']);
							$horizontal_margin = str_replace('px', '', $horizontal_margin);
						?>margin-left: <?php echo esc_attr($horizontal_margin); ?>px !important;
			margin-right: <?php echo esc_attr($horizontal_margin); ?>px !important;
			<?php } ?><?php if (!empty($attributes['align']) && $attributes['align'] == 'full') { ?>max-width: 100% !important;
			<?php } ?><?php if (!empty($attributes['align']) && $attributes['align'] == 'wide') { ?>max-width: var(--wp--style--global--wide-size) !important;
			<?php } ?>
		}
	</style>
<?php
	$html = ob_get_clean();

	return $html;
}

add_action('rest_api_init', 'wp_travel_blocks_pro_get_trips_lists_api');

function wp_travel_blocks_pro_get_trips_lists_api()
{
	register_rest_route(
		'wp-travel-block/v1',
		'/get-trip-lists',
		array(
			'methods'             => 'get',
			'permission_callback' => '__return_true',
			'callback'            => 'wp_travel_blocks_pro_trip_list',
		)
	);
}

function wp_travel_blocks_pro_trip_list(WP_REST_Request $request)
{

	$params = json_decode($request->get_param('arg'), true);
	$query_args = isset($params['query']) ? $params['query'] : array();


	// Legacy Block Compatibility & fixed conflict with yoast.
	if (isset($params['location'])) {
		$filter_term = get_term($params['location'], 'travel_locations');
		if (is_object($filter_term) && isset($filter_term->term_id)) {
			$selected_term                          = array(
				'count'       => $filter_term->count,
				'id'          => $filter_term->term_id,
				'description' => $filter_term->description,
				'taxonomy'    => $filter_term->taxonomy,
				'name'        => $filter_term->name,
				'slug'        => $filter_term->slug,
			);
			$query_args['selectedTripDestinations'] = array($selected_term);
		}
	}
	if (isset($params['tripType'])) {
		$filter_term = get_term($params['tripType'], 'itinerary_types');
		if (is_object($filter_term) && isset($filter_term->term_id)) {
			$selected_term                   = array(
				'count'       => $filter_term->count,
				'id'          => $filter_term->term_id,
				'description' => $filter_term->description,
				'taxonomy'    => $filter_term->taxonomy,
				'name'        => $filter_term->name,
				'slug'        => $filter_term->slug,
			);
			$query_args['selectedTripTypes'] = array($selected_term);
		}
	}

	// Options / params.
	$numberposts = isset($query_args['numberOfItems']) && $query_args['numberOfItems'] ? $query_args['numberOfItems'] : 3;

	$args = array(
		'post_type'    => WP_TRAVEL_POST_TYPE,
		'post__not_in' => array(get_the_ID()),
	);

	if (isset($query_args['selectedTripTypes']) && !empty($query_args['selectedTripTypes'])) {
		$args['itinerary_types'] = wp_list_pluck($query_args['selectedTripTypes'], 'slug');
	}
	if (isset($query_args['selectedTripDestinations']) && !empty($query_args['selectedTripDestinations'])) {
		$args['travel_locations'] = wp_list_pluck($query_args['selectedTripDestinations'], 'slug');
	}

	if (isset($query_args['selectedTripActivities']) && !empty($query_args['selectedTripActivities'])) {
		$args['activity'] = wp_list_pluck($query_args['selectedTripActivities'], 'slug');
	}

	if (isset($query_args['selectedTripKeywords']) && !empty($query_args['selectedTripKeywords'])) {
		$args['travel_keywords'] = wp_list_pluck($query_args['selectedTripKeywords'], 'slug');
	}

	// Meta Query.
	$sale_trip     = isset($params['saleTrip']) ? $params['saleTrip'] : false;
	$featured_trip = isset($params['featuredTrip']) ? $params['featuredTrip'] : false;
	if ($sale_trip) {
		$args['sale_trip'] = $sale_trip;
	}
	if ($featured_trip) {
		$args['featured_trip'] = $featured_trip;
	}

	$trip_data = WpTravel_Helpers_Trips::filter_trips($args);
	if (is_array($trip_data) && isset($trip_data['code']) && 'WP_TRAVEL_FILTER_RESULTS' === $trip_data['code']) {
		$trips          = $trip_data['trip'];
		$trip_ids       = wp_list_pluck($trips, 'id');

		if ($sale_trip || $featured_trip) {
			$args  = array(
				'post_type' => WP_TRAVEL_POST_TYPE,
				'post__in'  => $trip_ids,
			);
		}

		$trip_lists = array();
		$query = new WP_Query($args);

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$trip_list['id'] = get_the_id();
				$trip_list['trip_url'] = get_the_permalink();
				$trip_list['trip_title'] = get_the_title();
				$trip_list['featured_img'] = get_the_post_thumbnail_url(get_the_id(), 'post-thumbnail');
				$trip_list['is_featured_trip'] = get_post_meta(get_the_id(), 'wp_travel_featured', true);
				$trip_list['trip_code'] =  wptravel_get_trip_code(get_the_id());
				$trip_list['trip_excerpt'] =  get_the_excerpt();

				array_push($trip_lists, $trip_list);
			}
		}
		wp_reset_postdata();
	}

	return $trip_lists;
}
