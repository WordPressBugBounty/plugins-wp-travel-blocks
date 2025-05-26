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