<?php
/**
 * 
 * Render Callback For Guide Featured Trip
 * 
 */

function wptravel_block_guide_featured_trip_render( $attributes ) {
    ob_start();
    $guide_data = get_user_by( 'login', get_the_title() )->data;
    
    $layout_type = isset( $attributes['layoutType'] ) ? $attributes['layoutType'] : 'default-layout' ;
	$card_layout = isset( $attributes['cardLayout'] ) ? $attributes['cardLayout'] : 'grid-view' ;

    ?>

    <div id="wptravel-block-trips-list" class="wptravel-block-wrapper wptravel-guide-featured-trips wptravel-block-preview <?php echo esc_attr( $layout_type ).' '.'block-id-'.hash( 'sha256', json_encode($attributes) ); ?>">
        <div class="wp-travel-itinerary-items">
            <?php

            if ( !$guide_data && empty( get_user_meta( $guide_data->ID, 'trip_list', true ) ) ) {
                echo __( 'Guide Featured Trip block only visible in frontend.', 'wp-travel-pro' );
            } else {
                $args = array(
                    'post_type' => 'itineraries',
                    'post__in'  => get_user_meta( $guide_data->ID, 'trip_list', true ),
                );

                $query = new WP_Query( $args );

                if( $query->have_posts() ) { ?>
                    <div class="wp-travel-itinerary-items wptravel-archive-wrapper  <?php echo $layout_type == 'default-layout' ? 'grid-view' : esc_attr( $card_layout ); ?>">
                    <?php while( $query->have_posts() ) {
                        $query->the_post();
                        $trip_id = get_the_ID();
                        $is_featured_trip = get_post_meta( $trip_id, 'wp_travel_featured', true );
                        $is_fixed_departure = WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );
                        $trip_locations = get_the_terms( $trip_id, 'travel_locations' );
                        $location_name = '';
                        $location_link = '';
                        $group_size = wptravel_get_group_size( $trip_id );
                        $trip_url = get_the_permalink();

                        if ( $trip_locations && is_array( $trip_locations ) ) {
                            $first_location = array_shift( $trip_locations );
                            $location_name  = $first_location->name;
                            $location_link  = get_term_link( $first_location->term_id, 'travel_locations' );
                        }

                        $args = $args_regular = array( 'trip_id' => $trip_id );
                        $trip_price = WP_Travel_Helpers_Pricings::get_price( $args );
                        $args_regular['is_regular_price'] = true;
                        $regular_price = WP_Travel_Helpers_Pricings::get_price( $args_regular );
                        $is_enable_sale = WP_Travel_Helpers_Trips::is_sale_enabled(
                            array(
                                'trip_id'                => $trip_id,
                                'from_price_sale_enable' => true,
                            )
                        );

                        if( $card_layout == 'grid-view' ) {
                            /**
                             * 
                             */
                            if( $layout_type == 'default-layout' ) {
                                wptravel_get_block_template_part( 'v2/content', 'archive-itineraries' );
                            } elseif( $layout_type == 'layout-one' ) {
                                include( WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-one.php" );
                            } elseif( $layout_type == 'layout-two' ) {
                                include( WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-two.php" );
                            } elseif( $layout_type == 'layout-three' ) {
                                include( WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-three.php" );
                            } elseif( $layout_type == 'layout-four' ) {
                                include( WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-four.php" );
                            }

                            /**
                             * For future 
                             */
                            // wp_travel_get_template_layout( $layout_type );
                        }

                        if( $card_layout == 'list-view' ) {
                            if( $layout_type == 'default-layout' ) {
                                wptravel_get_block_template_part( 'v2/content', 'archive-itineraries' );
                            } elseif( $layout_type == 'layout-one' ) { ?>
                                <div class="wptravel-blocks-trip-card">
                                    <div class="same-height wptravel-blocks-trip-card-img-container">
                                        <a href="<?php echo esc_url( $trip_url ); ?>">
                                            <?php echo wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ); ?>
                                        </a>
                                        <div class="wptravel-blocks-img-overlay">
                                            <?php if( $is_featured_trip == 'yes' ) { ?>
                                            <div class="wptravel-blocks-trip-featured">
                                                <i class="fas fa-crown"></i>
                                            </div>
                                            <?php } ?>
                                            <div class="wptravel-blocks-floating-container">
                                                <div class="wptravel-blocks-trip-code">
                                                    <span class="code-hash">#</span> <?php echo wptravel_get_trip_code( $trip_id ) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="same-height wptravel-blocks-card-body">
                                        <div class="wptravel-blocks-card-body-top">
                                            <div class="wptravel-blocks-card-body-header">
                                                <a href="<?php echo esc_url( $trip_url ); ?>">
                                                    <h3 class="wptravel-blocks-card-title">
                                                        <?php the_title(); ?>
                                                    </h3>
                                                </a>
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
                                                <?php } ?>
                                                <?php if ( $trip_locations ) { ?>
                                                <div class="wptravel-blocks-trip-meta">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <a href="<?php echo esc_url( $location_link ); ?>">
                                                        <?php echo esc_html( $location_name ); ?>
                                                    </a>
                                                </div>
                                                <?php } ?>
                                                <?php if( $group_size ) { ?>
                                                <div class="wptravel-blocks-trip-meta">
                                                    <i class="fas fa-users"></i> <?php echo ( (int) $group_size && $group_size < 999 ) ?  wptravel_get_group_size( $trip_id ) : esc_html__( 'No Size Limit', 'wp-travel-blocks' );?>
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
                                                        <button class="wp-block-button__link wptravel-blocks-explore-btn"><?php esc_html__( 'Explore', 'wp-travel-blocks' ); ?></button>
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
                            <?php } elseif( $layout_type == 'layout-two' ) { ?>
                                <div class="wptravel-blocks-trip-card">
                                    <div class="same-height wptravel-blocks-trip-card-img-container">
                                        <a href="<?php echo esc_url( $trip_url ); ?>">
                                            <?php echo wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ); ?>
                                        </a>
                                        <div class="wptravel-blocks-img-overlay-base">
                                            <div class="wptravel-blocks-img-overlay">
                                                <?php if( $is_featured_trip == 'yes' ) { ?>
                                                <div class="wptravel-blocks-trip-featured">
                                                    <i class="fas fa-crown"></i> <?php echo __( 'Featured', 'wp-travel-blocks' ) ?>
                                                </div>
                                                <?php } ?>
                                                <?php do_action( 'wp_travel_after_archive_title', $trip_id ); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="same-height wptravel-blocks-card-body">
                                        <div class="wptravel-blocks-card-body-header">
                                            <div class="wptravel-blocks-card-body-header-left">
                                                <a href="<?php echo esc_url( $trip_url ); ?>">
                                                    <h3 class="wptravel-blocks-card-title">
                                                        <?php the_title(); ?>
                                                    </h3>
                                                </a>
                                                <?php if ( $trip_locations ) { ?>
                                                <div class="wptravel-blocks-trip-meta">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <a href="<?php echo esc_url( $location_link ); ?>">
                                                        <?php echo esc_html( $location_name ); ?>
                                                    </a>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <div class="wptravel-blocks-trip-rating">
                                                <i class="fas fa-star"></i> <?php echo wptravel_get_average_rating( $trip_id ); ?>
                                            </div>
                                        </div>
                                        <div class="wptravel-blocks-card-content">
                                            <div class="wptravel-blocks-trip-excerpt">
                                                <?php the_excerpt(); ?>
                                            </div>
                                            <div class="wptravel-blocks-sep"></div>
                                            <div class="wptravel-blocks-content-right">
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
                                                    <i class="fas fa-users"></i> <?php echo ( (int) $group_size && $group_size < 999 ) ?  wptravel_get_group_size( $trip_id ) : esc_html__( 'No Size Limit', 'wp-travel-blocks' ); ?>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <div class="wptravel-blocks-footer-right">
                                                <div class="wptravel-blocks-trip-explore">
                                                    <a href="<?php echo esc_url( $trip_url ); ?>">
                                                        <button class="wp-block-button__link wptravel-blocks-explore-btn"><?php echo esc_html__( 'Explore', 'wp-travel-blocks' ) ?></button>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } elseif( $layout_type == 'layout-three' ) { ?>
                                <div class="wptravel-blocks-trip-card">
                                    <div class="same-height wptravel-blocks-trip-card-img-container">
                                        <a href="<?php echo esc_url( $trip_url ); ?>">
                                            <?php echo wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ); ?>
                                        </a>
                                        <div class="wptravel-blocks-img-overlay-base">
                                            <div class="wptravel-blocks-img-overlay">
                                                <?php if( $is_featured_trip == 'yes' ) { ?>
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
                                                        <a href="<?php echo esc_url( $trip_url ); ?>">
                                                            <h3 class="wptravel-blocks-card-title">
                                                                <?php the_title(); ?>
                                                            </h3>
                                                        </a>
                                                        <div class="wptravel-blocks-trip-code">
                                                            <span class="code-hash">#</span> <?php echo wptravel_get_trip_code( $trip_id ) ?>
                                                        </div>
                                                    </div>
                                                    <?php do_action( 'wp_travel_after_archive_title', $trip_id ); ?>
                                                </div>
                                            <div class="wptravel-blocks-card-content">
                                                <div class="wptravel-blocks-content-left">
                                                    <div class="wptravel-blocks-trip-excerpt">
                                                        <?php the_excerpt(); ?>
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
                                                        <?php } ?>
                                                        <?php if ( $trip_locations ) { ?>
                                                        <div class="wptravel-blocks-trip-meta">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                            <a href="<?php echo esc_url( $location_link ); ?>">
                                                                <?php echo esc_html( $location_name ); ?>
                                                            </a>
                                                        </div>
                                                        <?php } ?>
                                                        <?php if( $group_size ) { ?>
                                                        <div class="wptravel-blocks-trip-meta">
                                                            <i class="fas fa-users"></i> <?php echo ( (int) $group_size && $group_size < 999 ) ?  wptravel_get_group_size( $trip_id ) : esc_html__( 'No Size Limit', 'wp-travel-blocks' ); ?>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="wptravel-blocks-content-right">
                                                    <?php if( $is_enable_sale && $regular_price > $trip_price ) { ?>
                                                    <div class="wptravel-blocks-trip-offer">
                                                        <?php
                                                            $save = ( 1 - ( (int) $trip_price / (int) $regular_price ) ) * 100;
                                                            $save = number_format( $save, 2, '.', ',' );
                                                            echo esc_html__( 'Save ', 'wp-travel-blocks' ) . $save . "%";
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
                                        <div class="wptravel-blocks-card-footer">
                                            <div class="wptravel-blocks-footer-left">
                                                <div class="wptravel-blocks-trip-rating">
                                                    <?php echo wptravel_single_trip_rating( $trip_id ); ?>
                                                </div>
                                            </div>
                                            <div class="wptravel-blocks-footer-right">
                                                <div class="wptravel-blocks-trip-explore">
                                                    <a href="<?php echo esc_url( $trip_url ); ?>">
                                                        <button class="wp-block-button__link wptravel-blocks-explore-btn"><?php echo esc_html__( 'Explore', 'wp-travel-blocks' ) ?></button>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } elseif( $layout_type == 'layout-four' ) { ?>
                                <div class="wptravel-blocks-trip-card">
                                    <div class="wptravel-blocks-trip-card-top">
                                        <div class="same-height wptravel-blocks-trip-card-img-container">
                                            <a href="<?php echo esc_url( $trip_url ); ?>">
                                                <?php echo wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ); ?>
                                            </a>
                                            <div class="wptravel-blocks-img-overlay">
                                                <?php if( $is_featured_trip == 'yes' ) { ?>
                                                <div class="wptravel-blocks-trip-featured">
                                                    <i class="fas fa-crown"></i> <?php echo esc_html__( 'Featured', 'wp-travel-blocks' ); ?>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="same-height wptravel-blocks-card-body">
                                            <div class="wptravel-blocks-card-body-top">
                                                <div class="wptravel-blocks-card-body-header">
                                                        <div class="wptravel-blocks-header-left">
                                                            <a href="<?php echo esc_url( $trip_url ); ?>">
                                                                <h3 class="wptravel-blocks-card-title">
                                                                    <?php the_title(); ?>
                                                                </h3>
                                                            </a>
                                                            <div class="wptravel-blocks-trip-rating">
                                                                <?php echo wptravel_single_trip_rating( $trip_id ); ?>
                                                            </div>
                                                        </div>
                                                        <?php do_action( 'wp_travel_after_archive_title', $trip_id ); ?>
                                                    </div>
                                                <div class="wptravel-blocks-card-content">
                                                    <div class="wptravel-blocks-trip-excerpt">
                                                        <?php the_excerpt(); ?>
                                                    </div>
                                                    <div class="wptravel-blocks-trip-pricing">
                                                        <?php if( $is_enable_sale && $regular_price > $trip_price ) { ?>
                                                        <div class="wptravel-blocks-trip-offer">
                                                            <?php
                                                                $save = ( 1 - ( (int) $trip_price / (int) $regular_price ) ) * 100;
                                                                $save = number_format( $save, 2, '.', ',' );
                                                                echo esc_html__( 'Save ', 'wp-travel-blocks' ) . $save . "%";
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
                                    </div>
                                    <div class="wptravel-blocks-card-footer">
                                        <div class="wptravel-blocks-footer-left">
                                            <div class="wptravel-blocks-trip-meta-container">
                                                <?php if( $is_fixed_departure ) { ?>
                                                <div class="wptravel-blocks-trip-meta">
                                                    <i class='far fa-calendar-alt'></i> <?php echo wptravel_get_fixed_departure_date( $trip_id ); ?>
                                                </div>
                                                <?php } else { ?>
                                                    <div class="wptravel-blocks-trip-meta">
                                                    <i class='far fa-clock'></i> <?php echo wp_travel_get_trip_durations( $trip_id ); ?>
                                                </div>
                                                <?php } ?>
                                                <?php if ( $trip_locations ) { ?>
                                                <div class="wptravel-blocks-trip-meta">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <a href="<?php echo esc_url( $location_link ); ?>">
                                                        <?php echo esc_html( $location_name ); ?>
                                                    </a>
                                                </div>
                                                <?php } ?>
                                                <?php if( $group_size ) { ?>
                                                <div class="wptravel-blocks-trip-meta">
                                                    <i class="fas fa-users"></i> <?php echo ( (int) $group_size && $group_size < 999 ) ?  wptravel_get_group_size( $trip_id ) : esc_html__( 'No Size Limit', 'wp-travel-blocks' ) ?>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="wptravel-blocks-footer-right">
                                            <div class="wptravel-blocks-trip-explore">
                                                <a href="<?php echo esc_url( $trip_url ); ?>">
                                                    <button class="wp-block-button__link wptravel-blocks-explore-btn"><?php echo esc_html__( 'Explore', 'wp-travel-blocks' ) ?></button>
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
                wp_reset_postdata();    
                }else{
                    echo esc_html__( 'No featured trips found..', 'wp-travel-blocks' );
                } }?>
            
        </div>
    </div>
    <style>
        .wptravel-guide-featured-trips.<?php echo 'block-id-'.hash( 'sha256', json_encode($attributes) )?>,
        body .is-layout-constrained > :where(:not(.alignleft):not(.alignright):not(.alignfull)).wptravel-guide-featured-trips.<?php echo 'block-id-'.hash( 'sha256', json_encode($attributes) )?> {

            <?php if( !empty( $attributes['align'] ) && $attributes['align'] == 'full' ){ ?>
                max-width: 100% !important;
            <?php } ?>

            <?php if( !empty( $attributes['align'] ) && $attributes['align'] == 'wide' ){ ?>
                max-width: var(--wp--style--global--wide-size) !important;
            <?php } ?>
        }
    </style>
    <?php

	return ob_get_clean();
}
