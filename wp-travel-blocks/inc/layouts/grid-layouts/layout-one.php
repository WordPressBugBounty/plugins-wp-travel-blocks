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