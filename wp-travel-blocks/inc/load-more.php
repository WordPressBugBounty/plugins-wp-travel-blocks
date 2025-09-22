<?php 

add_action('init', function () {
    add_action('wp_ajax_block_trip_load_more', 'wptravel_block_handle_block_trip_load_more' );
    add_action('wp_ajax_nopriv_block_trip_load_more', 'wptravel_block_handle_block_trip_load_more' );
});

function wptravel_block_handle_block_trip_load_more() {
    check_ajax_referer('block_trip_load_more_nonce', 'nonce');

    $attributes = ( $_POST['settings'] );

    $pattern_slug = $attributes['patternSlug'];
    $pattern = '';
    if($pattern_slug){
        $args = array(
            'name'        => $pattern_slug,
            'post_type'   => 'wp_block',
            'post_status' => 'publish',
            'numberposts' => 1
        );
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $pattern = get_the_content();
            }
        }
        wp_reset_postdata();
    }

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

    $args['offset'] = intval($_POST['offset']);

    $query = new WP_Query($args);
    

    if ($query->have_posts()) {
        ob_start();
        while ($query->have_posts()) {
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
            
            if( $layout_type == 'default-layout' ){
            ?>
                <div class="view-box">
                    <div class="view-image">
                        <a href="<?php echo esc_url( $trip_url ); ?>" class="image-thumb">
                            <div class="image-overlay"></div>
                            <?php echo wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ); ?>
                        </a> 
                        <div class="offer">
                            <span>#<?php echo wptravel_get_trip_code( $trip_id ) ?></span>
                        </div>
                    </div>

                    <div class="view-content">
                        <div class="left-content">
                            <header>
                                <?php do_action( 'wp_travel_before_archive_content_title', $trip_id ); ?>
                                <h2 class="entry-title" style="font-size:18px; line-height:25px">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php esc_html( the_title() ); ?>
                                    </a>
                                </h2>
                                <?php do_action( 'wp_travel_after_archive_title', $trip_id ); ?>
                            </header>
                            <div class="trip-icons">
                                <?php wptravel_get_trip_duration( $trip_id ); ?>
                                <div class="trip-location">
                                    <?php echo wp_kses_post( apply_filters( 'wp_travel_archive_page_location_icon', '<i class="fas fa-map-marker-alt"></i>' ) ); ?>
                                    <span>
                                        <?php if ( $location_name ) : ?>
                                            <a href="<?php echo esc_url( $location_link ); ?>" ><?php echo esc_html( $location_name ); ?></a>
                                                <?php if( count( $trip_locations ) > 0 ): ?>
                                                    <i class="fas fa-angle-down"></i>
                                                    <ul>
                                                        <?php foreach( $trip_locations as $location ): ?>
                                                            <li><a href="<?php echo esc_url( get_term_link( $location->term_id, 'travel_locations' ) ); ?>" ><?php echo esc_html( $location->name ); ?></a></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            <?php
                                        else :
                                            echo esc_html( apply_filters( 'wp_travel_archives_page_trip_location', __( 'N/A', 'wp-travel' ), $trip_id) );
                                        endif;
                                        ?>
                                    </span>
                                </div>
                                <div class="group-size">
                                    <?php echo wp_kses_post( apply_filters( 'wp_travel_archive_page_group_size_icon', '<i class="fas fa-users"></i>' ) ); ?>
                                    <span><?php echo ( (int) $group_size && $group_size < 999 ) ?  wptravel_get_group_size( $trip_id ) : 'No Size Limit' ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="right-content">
                            <div class="footer-wrapper">
                                <div class="trip-price">
                                    <?php apply_filters( 'wp_trave_archives_page_trip_save_offer', wptravel_save_offer( $trip_id ), $trip_id ); ?>
                                    <?php if ( $trip_price > 0 ) : ?>
                                        <span class="price-here">
                                            <?php echo apply_filters('wp_travel_archives_page_trip_price', wptravel_get_formated_price_currency( $trip_price ), $trip_id ); //phpcs:ignore ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ( $is_enable_sale && $trip_price < $regular_price ) : ?>
                                        <del><?php echo apply_filters('wp_travel_archives_page_trip_price_sale', wptravel_get_formated_price_currency( $regular_price, true ), $trip_id ); //phpcs:ignore ?></del>
                                    <?php endif; ?>
                                </div>
                                <div class="trip-rating">
                                    <?php $reviewed = apply_filters( 'wp_travel_trip_archive_list_review', wptravel_tab_show_in_menu( 'reviews' ) ); if ( $reviewed ) : ?>
                                        <div class="wp-travel-average-review">
                                            <?php wptravel_trip_rating( $trip_id ); ?>
                                            <?php $count = (int) wptravel_get_review_count(); ?>
                                        </div>
                                        <span class="wp-travel-review-text"> (<?php echo esc_html( $count ) . esc_html__( ' Reviews', 'wp-travel' ); ?>)</span>
                                    <?php endif; ?>
                                </div>
                            </div>        
                            <a class="wp-block-button__link explore-btn" href="<?php the_permalink(); ?>"><span><?php echo esc_html( apply_filters( 'wp_travel_archives_page_trip_explore_btn', __( 'Explore', 'wp-travel' ), $trip_id ) ); ?></span></a>
                        </div>
                    </div>
                </div>
            <?php
            }

            if( $layout_type == 'layout-one' ){
            ?>  
                <?php if( $card_layout == 'grid-view' ): ?>
                    <div class="wptravel-blocks-trip-card">
                        <div class="wptravel-blocks-trip-card-img-container">
                            <a href="<?php echo esc_url( $trip_url ); ?>">
                                <?php echo wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ); ?>
                            </a>
                            <div class="wptravel-blocks-img-overlay">
                                <?php if( $is_featured_trip == 'yes' ) { ?>
                                <div class="wptravel-blocks-trip-featured">
                                    <i class="fas fa-crown"></i> <?php esc_html__( "Featured", "wp-travel-blocks" ); ?>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="wptravel-blocks-card-body">
                            <div class="wptravel-blocks-card-body-top">
                                <div class="wptravel-blocks-floating-container">
                                    <div class="wptravel-blocks-trip-code">
                                        <span class="code-hash">#</span> <?php echo wptravel_get_trip_code( $trip_id ) ?>
                                    </div>
                                </div>
                                <div class="wptravel-blocks-card-body-header">
                                    <h3 class="wptravel-blocks-card-title wp-block-heading">
                                        <a href="<?php echo esc_url( $trip_url ); ?>">
                                            <?php esc_html( the_title() ); ?>
                                        </a>
                                    </h3>
                                    <?php do_action( 'wp_travel_after_archive_title', $trip_id ); ?>
                                </div>
                                <div class="wptravel-blocks-card-content">
                                    <?php if( $is_fixed_departure ) { ?>
                                    <div class="wptravel-blocks-trip-meta">
                                        <i class='far fa-calendar-alt'></i> <?php echo wptravel_get_fixed_departure_date( $trip_id ); ?>
                                    </div>
                                    <?php } else { ?>
                                        <div class="wptravel-blocks-trip-meta">
                                        <i class='far fa-clock'></i> <?php echo wp_travel_get_trip_durations( $trip_id ); ?>
                                    </div>
                                    <?php }
                                    if ( $location_name ) { ?>
                                        <div class="wptravel-blocks-trip-meta">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <a href="<?php echo esc_url( $location_link ); ?>">
                                                <?php echo esc_html( $location_name ); ?>
                                            </a>
                                            <?php if( count( $trip_locations ) > 0 ): ?>
                                                <i class="fas fa-angle-right"></i>
                                                <ul class="wptravel-blocks-locations-dropdown">
                                                    <?php foreach( $trip_locations as $location ): ?>
                                                        <li><a href="<?php echo esc_url( get_term_link( $location->term_id, 'travel_locations' ) ); ?>" ><?php echo esc_html( $location->name ); ?></a></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    <?php } ?>
                                    <?php if( $group_size ) { ?>
                                    <div class="wptravel-blocks-trip-meta">
                                        <i class="fas fa-users"></i> <?php echo ( (int) $group_size && $group_size < 999 ) ?  wptravel_get_group_size( $trip_id ) : 'No Size Limit' ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="wptravel-blocks-card-footer">
                                <div class="wptravel-blocks-footer-left">
                                    <div class="wptravel-blocks-trip-rating">
                                        <?php echo wptravel_single_trip_rating( $trip_id ); ?>
                                    </div>
                                    <div class="wptravel-blocks-trip-explore">
                                        <a href="<?php echo esc_url( $trip_url ); ?>">
                                            <button class="wp-block-button__link wptravel-blocks-explore-btn"><?php echo esc_html__( 'Explore', 'wp-travel-blocks' ); ?></button>
                                        </a>
                                    </div>
                                </div>
                                <div class="wptravel-blocks-footer-right">
                                    <?php if( $is_enable_sale && $regular_price > $trip_price ) { ?>
                                    <div class="wptravel-blocks-trip-offer">
                                        <?php
                                            $save = ( 1 - ( (int) $trip_price / (int) $regular_price ) ) * 100;
                                            $save = number_format( $save, 2, '.', ',' );
                                            echo "Save " . $save . "%";
                                        ?>
                                    </div>
                                    <div class="wptravel-blocks-trip-original-price">
                                        <del><?php echo wptravel_get_formated_price_currency( $regular_price ); ?></del>
                                    </div>
                                    <div class="wptravel-blocks-trip-offer-price"><?php echo wptravel_get_formated_price_currency( $trip_price ); ?></div>
                                    <?php } else { ?>
                                    <div class="wptravel-blocks-trip-offer-price">
                                        <?php echo wptravel_get_formated_price_currency( $regular_price ); ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if( $card_layout == 'list-view' ): ?>
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
                <?php endif; ?>
            <?php
            }

            if( $layout_type == 'layout-two' ){
            ?>
                <?php if( $card_layout == 'grid-view' ): ?>
                    <div class="wptravel-blocks-trip-card">
                        <div class="wptravel-blocks-trip-card-img-container">
                            <a href="<?php echo esc_url( $trip_url ); ?>">
                                <?php echo wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ); ?>
                            </a>
                            <div class="wptravel-blocks-img-overlay-base">
                                <div class="wptravel-blocks-img-overlay">
                                    <?php if( $is_featured_trip == 'yes' ) { ?>
                                    <div class="wptravel-blocks-trip-featured">
                                        <i class="fas fa-crown"></i> <?php echo __( "Featured", "wptravel-blocks"); ?>
                                    </div>
                                    <?php } ?>
                                    <?php do_action( 'wp_travel_after_archive_title', $trip_id ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="wptravel-blocks-card-body">
                            <div class="wptravel-blocks-card-body-top">
                                <?php if ( $location_name ) { ?>
                                    <div class="wptravel-blocks-floating-container">
                                        <div class="wptravel-blocks-trip-meta-float">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <a href="<?php echo esc_url( $location_link ); ?>">
                                                <?php echo esc_html( $location_name ); ?>
                                            </a>
                                            <?php if( count( $trip_locations ) > 0 ): ?>
                                                <i class="fas fa-angle-right"></i>
                                                <ul class="wptravel-blocks-locations-dropdown">
                                                    <?php foreach( $trip_locations as $location ): ?>
                                                        <li><a href="<?php echo esc_url( get_term_link( $location->term_id, 'travel_locations' ) ); ?>" ><?php echo esc_html( $location->name ); ?></a></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="wptravel-blocks-card-body-header">
                                    <h3 class="wptravel-blocks-card-title wp-block-heading">
                                        <a href="<?php echo esc_url( $trip_url ); ?>">
                                            <?php esc_html( the_title() ); ?>
                                        </a>
                                    </h3>
                                    <div class="wptravel-blocks-trip-rating">
                                        <i class="fas fa-star"></i> <?php echo wptravel_get_average_rating( $trip_id ); ?>
                                    </div>
                                </div>
                                <div class="wptravel-blocks-card-content">
                                    <div class="wptravel-blocks-trip-excerpt">
                                        <?php echo esc_html( the_excerpt() ) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="wptravel-blocks-card-footer">
                                <div class="wptravel-blocks-footer-left">
                                    <?php if( $is_fixed_departure ) { ?>
                                    <div class="wptravel-blocks-trip-meta">
                                        <i class='far fa-calendar-alt'></i> <?php echo wptravel_get_fixed_departure_date( $trip_id ); ?>
                                    </div>
                                    <?php } else { ?>
                                        <div class="wptravel-blocks-trip-meta">
                                        <i class='far fa-clock'></i> <?php echo wp_travel_get_trip_durations( $trip_id ); ?>
                                    </div>
                                    <?php } ?>
                                    <?php if( $group_size ) { ?>
                                    <div class="wptravel-blocks-trip-meta">
                                        <i class="fas fa-users"></i> <?php echo ( (int) $group_size && $group_size < 999 ) ?  wptravel_get_group_size( $trip_id ) : 'No Size Limit' ?>
                                    </div>
                                    <?php } ?>
                                    <div class="wptravel-blocks-trip-explore">
                                        <a href="<?php echo esc_url( $trip_url ); ?>">
                                            <button class="wp-block-button__link wptravel-blocks-explore-btn"><?php echo esc_html__( 'Explore', 'wp-travel-blocks' ); ?></button>
                                        </a>
                                    </div>
                                </div>
                                <div class="wptravel-blocks-footer-right">
                                    <?php if( $is_enable_sale && $regular_price > $trip_price ) { ?>
                                    <div class="wptravel-blocks-trip-offer">
                                        <?php
                                            $save = ( 1 - ( (int) $trip_price / (int) $regular_price ) ) * 100;
                                            $save = number_format( $save, 2, '.', ',' );
                                            echo "Save " . $save . "%";
                                        ?>
                                    </div>
                                    <div class="wptravel-blocks-trip-original-price">
                                        <del><?php echo wptravel_get_formated_price_currency( $regular_price ); ?></del>
                                    </div>
                                    <div class="wptravel-blocks-trip-offer-price"><?php echo wptravel_get_formated_price_currency( $trip_price ); ?></div>
                                    <?php } else { ?>
                                    <div class="wptravel-blocks-trip-offer-price">
                                        <?php echo wptravel_get_formated_price_currency( $regular_price ); ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if( $card_layout == 'list-view' ): ?>
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
                <?php endif; ?>
                
            <?php
            }

            if( $layout_type == 'layout-three' ){
            ?>
                <?php if( $card_layout == 'grid-view' ): ?>
                    <div class="wptravel-blocks-trip-card">
                        <div class="wptravel-blocks-trip-card-img-container">
                            <a href="<?php echo esc_url( $trip_url ); ?>">
                                <?php echo wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ); ?>
                            </a>
                            <div class="wptravel-blocks-img-overlay-base">
                                <div class="wptravel-blocks-img-overlay">
                                    <?php if( $is_featured_trip == 'yes' ) { ?>
                                    <div class="wptravel-blocks-trip-featured">
                                        <i class="fas fa-crown"></i> <?php esc_html__( "Featured", "wp-travel-blocks" ); ?>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="wptravel-blocks-trip-meta-container">
                                    <?php if ( $location_name ) { ?>
                                        <div class="wptravel-blocks-trip-meta">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <a href="<?php echo esc_url( $location_link ); ?>">
                                                <?php echo esc_html( $location_name ); ?>
                                            </a>
                                            <?php if( count( $trip_locations ) > 0 ): ?>
                                                <i class="fas fa-angle-right"></i>
                                            <?php endif; ?>
                                        </div>
                                    <?php } ?>
                                    <?php if( $is_fixed_departure ) { ?>
                                    <div class="wptravel-blocks-trip-meta">
                                        <i class='far fa-calendar-alt'></i> <?php echo wptravel_get_fixed_departure_date( $trip_id ); ?>
                                    </div>
                                    <?php } else { ?>
                                        <div class="wptravel-blocks-trip-meta">
                                        <i class='far fa-clock'></i> <?php echo wp_travel_get_trip_durations( $trip_id ); ?>
                                    </div>
                                    <?php } ?>
                                    <?php if( $group_size ) { ?>
                                    <div class="wptravel-blocks-trip-meta">
                                        <i class="fas fa-users"></i> <?php echo ( (int) $group_size && $group_size < 999 ) ?  wptravel_get_group_size( $trip_id ) : 'No Size Limit' ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="wptravel-blocks-card-body">
                            <div class="wptravel-blocks-card-body-top">
                                <div class="wptravel-blocks-card-body-header">
                                    <h3 class="wptravel-blocks-card-title wp-block-heading">
                                        <a href="<?php echo esc_url( $trip_url ); ?>">
                                            <?php esc_html( the_title() ); ?>
                                        </a>
                                    </h3>
                                    <?php do_action( 'wp_travel_after_archive_title', $trip_id ); ?>
                                </div>
                                <div class="wptravel-blocks-card-content">
                                    <div class="wptravel-blocks-trip-excerpt">
                                        <?php echo esc_html( the_excerpt() ); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="wptravel-blocks-card-footer">
                                <div class="wptravel-blocks-footer-left">
                                    <div class="wptravel-blocks-trip-rating">
                                        <?php echo wptravel_single_trip_rating( $trip_id ); ?>
                                    </div>
                                    <div class="wptravel-blocks-trip-explore">
                                        <a href="<?php echo esc_url( $trip_url ); ?>">
                                            <button class="wp-block-button__link wptravel-blocks-explore-btn"><?php echo esc_html__( 'Explore', 'wp-travel-blocks' ); ?></button>
                                        </a>
                                    </div>
                                </div>
                                <div class="wptravel-blocks-footer-right">
                                    <?php if( $is_enable_sale && $regular_price > $trip_price ) { ?>
                                    <div class="wptravel-blocks-trip-offer">
                                        <?php
                                            $save = ( 1 - ( (int) $trip_price / (int) $regular_price ) ) * 100;
                                            $save = number_format( $save, 2, '.', ',' );
                                            echo "Save " . $save . "%";
                                        ?>
                                    </div>
                                    <div class="wptravel-blocks-trip-original-price">
                                        <del><?php echo wptravel_get_formated_price_currency( $regular_price ); ?></del>
                                    </div>
                                    <div class="wptravel-blocks-trip-offer-price"><?php echo wptravel_get_formated_price_currency( $trip_price ); ?></div>
                                    <?php } else { ?>
                                    <div class="wptravel-blocks-trip-offer-price">
                                        <?php echo wptravel_get_formated_price_currency( $regular_price ); ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if( $card_layout == 'list-view' ): ?>
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
                <?php endif; ?>
                
            <?php
            }

            if( $layout_type == 'layout-four' ){
            ?>
                <?php if( $card_layout == 'grid-view' ): ?>
                    <div class="wptravel-blocks-trip-card">
                        <div class="wptravel-blocks-trip-card-img-container">
                            <a href="<?php echo esc_url( $trip_url ); ?>">
                                <?php echo wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ); ?>
                            </a>
                            <div class="wptravel-blocks-img-overlay-base">
                                <div class="wptravel-blocks-img-overlay">
                                    <?php if( $is_enable_sale && $regular_price > $trip_price ) { ?>
                                    <div class="wptravel-blocks-trip-offer">
                                        <?php
                                            $save = ( 1 - ( (int) $trip_price / (int) $regular_price ) ) * 100;
                                            $save = number_format( $save, 2, '.', ',' );
                                            echo "Save " . $save . "%";
                                        ?>
                                    </div>
                                    <?php } 
                                    if( $is_featured_trip == 'yes' ) { ?>
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
                                        <h3 class="wptravel-blocks-card-title wp-block-heading">
                                            <a href="<?php echo esc_url( $trip_url ); ?>">
                                                <?php esc_html( the_title() ); ?>
                                            </a>
                                        </h3>
                                        <div class="wptravel-blocks-trip-code">
                                            <span class="code-hash">#</span> <?php echo wptravel_get_trip_code( $trip_id ) ?>
                                        </div>
                                    </div>
                                    <?php do_action( 'wp_travel_after_archive_title', $trip_id ); ?>
                                </div>
                                <div class="wptravel-blocks-card-content">
                                    <div class="wptravel-blocks-trip-excerpt">
                                        <?php echo esc_html( the_excerpt() ) ?>
                                    </div>
                                    <div class="wptravel-blocks-trip-meta-container">
                                        <?php if( $is_fixed_departure ) { ?>
                                        <div class="wptravel-blocks-trip-meta">
                                            <i class='far fa-calendar-alt'></i> <?php echo wptravel_get_fixed_departure_date( $trip_id ); ?>
                                        </div>
                                        <?php } else { ?>
                                            <div class="wptravel-blocks-trip-meta">
                                            <i class='far fa-clock'></i> <?php echo wp_travel_get_trip_durations( $trip_id ); ?>
                                        </div>
                                        <?php }
                                        if ( $location_name ) { ?>
                                            <div class="wptravel-blocks-trip-meta">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <a href="<?php echo esc_url( $location_link ); ?>">
                                                    <?php echo esc_html( $location_name ); ?>
                                                </a>
                                                <?php if( count( $trip_locations ) > 0 ): ?>
                                                    <i class="fas fa-angle-right"></i>
                                                    <ul class="wptravel-blocks-locations-dropdown">
                                                        <?php foreach( $trip_locations as $location ): ?>
                                                            <li><a href="<?php echo esc_url( get_term_link( $location->term_id, 'travel_locations' ) ); ?>" ><?php echo esc_html( $location->name ); ?></a></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </div>
                                        <?php }
                                        if( $group_size ) { ?>
                                        <div class="wptravel-blocks-trip-meta">
                                            <i class="fas fa-users"></i> <?php echo ( (int) $group_size && $group_size < 999 ) ?  wptravel_get_group_size( $trip_id ) : 'No Size Limit' ?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="wptravel-blocks-card-footer">
                                <div class="wptravel-blocks-footer-left">
                                    <div class="wptravel-blocks-trip-rating">
                                        <?php echo wptravel_single_trip_rating( $trip_id ); ?>
                                    </div>
                                </div>
                                <div class="wptravel-blocks-footer-right">
                                    <?php if( $is_enable_sale && $regular_price > $trip_price ) { ?>
                                    <div class="wptravel-blocks-trip-original-price">
                                        <del><?php echo wptravel_get_formated_price_currency( $regular_price ); ?></del>
                                    </div>
                                    <div class="wptravel-blocks-trip-offer-price"><?php echo wptravel_get_formated_price_currency( $trip_price ); ?></div>
                                    <?php } else { ?>
                                    <div class="wptravel-blocks-trip-offer-price">
                                        <?php echo wptravel_get_formated_price_currency( $regular_price ); ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if( $card_layout == 'list-view' ): ?>
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
                <?php endif; ?>
                
            <?php
            } 

            if ( $layout_type == 'custom' ){
                echo do_blocks( $pattern);
            }
        }
    
        wp_reset_postdata();
        echo ob_get_clean();
    }
    
    wp_die();
}