<?php

/**
 * 
 * Render Callback For Trip List
 * 
 */
function wptravel_block_trip_featured_category_render( $attributes, $content ){

    $selected_trip_types = isset( $attributes['query']['selectedTripTypes'] ) ? $attributes['query']['selectedTripTypes'] : [];
    $selected_trip_destinations = isset( $attributes['query']['selectedTripDestinations'] ) ? $attributes['query']['selectedTripDestinations'] : [];
    $selected_trip_activities = isset( $attributes['query']['selectedTripActivities'] ) ? $attributes['query']['selectedTripActivities'] : [];
    $layout = isset( $attributes['layout'] ) ? $attributes['layout'] : 'layout-one';

    $trip_term_slugs = [];
    $active_terms = [];
    
    if( $attributes['tripTax'] == 'itinerary_types' ) {
        $active_terms = $selected_trip_types;
    } else if ( $attributes['tripTax'] == "travel_locations" ) {
        $active_terms = $selected_trip_destinations;
    } else if ( $attributes['tripTax'] == "activity" ) {
        $active_terms = $selected_trip_activities;
    }

    foreach( $active_terms as $term ) {
        array_push($trip_term_slugs, $term["slug"]);
    }
    
	ob_start();

    if( !empty( $active_terms ) ) : ?>
        <div id="wptravel-blocks-featured-category-container" class="wptravel-blocks-featured-category-container <?php echo 'block-id-'.hash( 'sha256', json_encode($attributes) )?>">
        <?php foreach( $active_terms as $term ) {
            if( $layout == "layout-one" ) { ?>
                <div id="wp-travel-blocks-trip-featured-category" class="<?php echo esc_attr( $layout ); ?>">
                    <div class="layout-handler">
                        <a href="<?php echo esc_url($term['link']); ?>">
                            <div class="wp-travel-blocks-trip-featured-category-img-container">
                                <img src="<?php echo esc_url( wp_get_attachment_url( get_term_meta( $term['id'], 'wp_travel_trip_type_image_id', true) ) ) ?>" alt="">
                            </div>
                        </a>
                        <div class="wp-travel-blocks-trip-featured-category-footer">
                            
                                <div class="wp-travel-blocks-trip-featured-category-left-info">
                                    <span><?php echo esc_html($term['name']); ?></span>
                                    <i class="fa fa-arrow-right"></i>
                                </div>
                            
                            <div class="wp-travel-blocks-trip-featured-category-right-info">
                                <i class="fas fa-suitcase-rolling"></i>
                                <span><?php echo esc_html($term['count']) . ' ' . esc_html__( 'Trips Available', 'wp-travel-blocks' ) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } elseif ( $layout == "layout-two" ) { ?>
                <div id="wp-travel-blocks-trip-featured-category" class="<?php echo esc_attr( $layout ); ?>">
                    <a href="<?php echo esc_url($term['link']); ?>">
                        <div class="wp-travel-blocks-trip-featured-category-img-container">
                            <img src="<?php echo esc_url( wp_get_attachment_url( get_term_meta( $term['id'], 'wp_travel_trip_type_image_id', true) ) ) ?>" alt="">
                            <div class="wp-travel-blocks-trip-featured-category-img-overlay-trip">
                                <i class="fas fa-suitcase-rolling"></i>
                                <span><?php echo esc_html($term['count']) . ' ' . esc_html__( 'Trips Available', 'wp-travel-blocks' ) ?></span>
                            </div>
                        </div>
                        <div class="wp-travel-blocks-trip-featured-category-footer">
                            <div class="wp-travel-blocks-trip-featured-category-left-info">
                                <span><?php echo esc_html($term['name']); ?></span>
                                <i class="fa fa-arrow-right"></i>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } elseif ( $layout == "layout-three" ) { ?>
                <div id="wp-travel-blocks-trip-featured-category" class="<?php echo esc_attr( $layout ); ?>">
                    <a href="<?php echo esc_url($term['link']); ?>">
                        <div class="wp-travel-blocks-trip-featured-category-img-container">
                            <img src="<?php echo esc_url( wp_get_attachment_url( get_term_meta( $term['id'], 'wp_travel_trip_type_image_id', true) ) ) ?>" alt="">
                        </div>
                        <div class="wp-travel-blocks-trip-featured-category-img-overlay-trip">
                            <div class="wp-travel-blocks-trip-featured-category-footer">
                                <div class="wp-travel-blocks-trip-info-container">
                                    <div class="wp-travel-blocks-trip-featured-category-left-info">
                                        <div class="wp-travel-blocks-trip-destination">
                                            <span><?php echo esc_html($term['name']); ?></span>
                                        </div>
                                        <i class="fas fa-suitcase-rolling"></i>
                                        <span class="wp-travel-blocks-trip-count"><?php echo esc_html($term['count']) . ' ' . esc_html__( 'Trips Available', 'wp-travel-blocks' ) ?></span> 
                                    </div>
                                    <div class="wp-travel-blocks-trip-featured-category-right-info">
                                        <i class="fa fa-arrow-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } elseif ( $layout == "layout-four" ) { ?>
                <div id="wp-travel-blocks-trip-featured-category" class="<?php echo esc_attr( $layout ); ?>">
                    <a href="<?php echo esc_url($term['link']); ?>">
                        <div class="wp-travel-blocks-trip-featured-category-img-container">
                            <img src="<?php echo esc_url( wp_get_attachment_url( get_term_meta( $term['id'], 'wp_travel_trip_type_image_id', true) ) ) ?>" alt="">
                        </div>
                        <div class="wp-travel-blocks-trip-featured-category-img-overlay-trip">
                            <div class="wp-travel-blocks-trip-featured-category-footer">
                                <div class="wp-travel-blocks-trip-info-container">
                                    <div class="wp-travel-blocks-trip-featured-category-left-info">
                                        <div class="wp-travel-blocks-trip-destination">
                                            <span><?php echo esc_html($term['name']); ?></span>
                                        </div>
                                        <i class="fas fa-suitcase-rolling"></i>
                                        <span class="wp-travel-blocks-trip-count"><?php echo esc_html($term['count']) . ' ' . esc_html__( 'Trips Available', 'wp-travel-blocks' ) ?></span> 
                                    </div>
                                </div>
                                <div class="wp-travel-blocks-trip-featured-category-bottom">
                                    <div class="wp-travel-blocks-trip-featured-category-arrow">
                                        <i class="fa fa-arrow-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php }
        } ?>
        </div>
        <style>
            .wptravel-blocks-featured-category-container.<?php echo 'block-id-'.hash( 'sha256', json_encode($attributes) )?>,
            body .is-layout-constrained > :where(:not(.alignleft):not(.alignright):not(.alignfull)).wptravel-blocks-featured-category-container.<?php echo 'block-id-'.hash( 'sha256', json_encode($attributes) )?> {
                <?php if( !empty( $attributes['style']['color']['background'] ) ){ ?>
                    background-color: <?php echo esc_attr( $attributes['style']['color']['background'] ); ?>;
                <?php } ?>

                <?php if( !empty( $attributes['style']['color']['text'] ) ){ ?>
                    color: <?php echo esc_attr( $attributes['style']['color']['text'] ); ?>;
                <?php } ?>

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
        </style>
    <?php endif;

	$html = ob_get_clean();

	return $html;
}