<?php

/**
 * 
 * Render Callback For Trip List
 * 
 */
function wptravel_block_filterable_trips_render( $attributes, $content ){

    $trip_to_show = $attributes['query']['numberOfItems'];
    $order_by = $attributes['query']['orderBy'];
    $order = $attributes['query']['order'];
    $selected_trip_types = isset( $attributes['query']['selectedTripTypes'] ) ? $attributes['query']['selectedTripTypes'] : [];
    $selected_trip_destinations = isset( $attributes['query']['selectedTripDestinations'] ) ? $attributes['query']['selectedTripDestinations'] : [];
    $selected_trip_activities = isset( $attributes['query']['selectedTripActivities'] ) ? $attributes['query']['selectedTripActivities'] : [];
    $layout_type = $attributes['layoutType'];
    $filter_type = $attributes['filterType'];
    $card_layout = $attributes['cardLayout'];

    $trip_term_slugs = [];
    $active_terms = [];
    
    if( $filter_type == "itinerary_types" ) {
        $active_terms = $selected_trip_types;
    } else if ( $filter_type == "travel_locations" ) {
        $active_terms = $selected_trip_destinations;
    } else if ( $filter_type == "activity" ) {
        $active_terms = $selected_trip_activities;
    }

    foreach( $active_terms as $term ) {
        array_push($trip_term_slugs, $term["slug"]);
    }

	ob_start();

    if( !empty( $active_terms ) ) :
    ?>
    <div id="wptravel-blocks-filterable-trips" class="wptravel-blocks-filterable-trips <?php echo 'block-id-'.hash( 'sha256', json_encode($attributes) )?>">

        <!-- HTML for the filter buttons -->
        <div class="wptravel-filterable-trips-filters">
            <?php $i = 1; foreach( $active_terms as $term ) { ?>
                <button class="filter-btn <?php echo $i == 1 ? 'active': '' ;?>" data-filter="<?php echo esc_attr( $term["slug"] ); ?>"><?php echo esc_html( $term["name"] ); ?></button>
            <?php $i++; } ?>
        </div>

        <?php 
        /**
         * Count $i and apply active class to first 
         */
        $j = 1; foreach( $active_terms as $term ) { ?>
            <div id="category-<?php echo esc_attr( $term["slug"] ); ?>" class="wptravel-filterable-trips-grid wp-travel-itinerary-items wptravel-archive-wrapper <?php echo esc_attr( $layout_type ); ?> grid-view <?php echo $j == 1 ? 'active': '' ;?>">
                <?php 
                    $query = new WP_Query([
                        'post_type'         =>  'itineraries',
                        'order'             =>  sanitize_text_field( $order ),
                        'order_by'          =>  sanitize_text_field( $order_by ),
                        'posts_per_page'    =>  absint( $trip_to_show ),
                        'post__not_in'      =>  array( get_the_ID() ),
                        'tax_query'         =>  [
                            [
                                'taxonomy'  => sanitize_text_field( $filter_type ),
                                'field'     => 'slug',
                                'terms'     => $term["slug"]
                            ]
                        ]
                    ]);
        
                    if ($query->have_posts()) :
                        while ($query->have_posts()) :
                            $query->the_post();
                            
                            $trip_id = get_the_ID();
                            $trip_locations = get_the_terms( $trip_id, 'travel_locations' );
                            $is_featured_trip = get_post_meta( $trip_id, 'wp_travel_featured', true );
                            $is_fixed_departure = WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );
                            $location_name = '';
                            $location_link = '';
                            $group_size = wptravel_get_group_size( $trip_id );

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
        
                            if( $attributes['layoutType'] == 'default-layout' ) {
                                wptravel_get_block_template_part( 'v2/content', 'archive-itineraries' );
                            } else if( $attributes['layoutType'] == 'layout-one' ) {
                                include( WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-one.php" );
                            } else if( $attributes['layoutType'] == 'layout-two' ) {
                                include( WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-two.php" );
                            } else if( $attributes['layoutType'] == 'layout-three' ) {
                                include( WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-three.php" );
                            } else if( $attributes['layoutType'] == 'layout-four' ) {
                                include( WP_TRAVEL_BLOCKS_ABS_PATH . "inc/layouts/grid-layouts/layout-four.php" );
                            }
        
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo 'No trips found.';
                    endif;
                ?>
            </div>
            
        <?php $j++; } ?>

        
    </div>
    <style>
        <?php if( !empty( $attributes['style']['color']['text'] ) ){ ?>
            #wptravel-blocks-filterable-trips .wptravel-filterable-trips-filters .filter-btn{
                color: <?php echo esc_attr( $attributes['style']['color']['text'] ); ?>;
            }            
        <?php } ?>
       
        .wptravel-blocks-filterable-trips.<?php echo 'block-id-'.hash( 'sha256', json_encode($attributes) )?>,
        body .is-layout-constrained > :where(:not(.alignleft):not(.alignright):not(.alignfull)).wptravel-blocks-filterable-trips.<?php echo 'block-id-'.hash( 'sha256', json_encode($attributes) )?> {
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
    <!-- jQuery for filtering trips -->
    <script>
        jQuery(document).ready(function ($) {
            $('#wptravel-blocks-filterable-trips .wptravel-filterable-trips-filters button').on('click', function () {
                var filter = $(this).data('filter');
                $(this).siblings("button").removeClass(" active").end().addClass(" active");

                $(".wptravel-filterable-trips-grid").removeClass(" active").end();
                $('#category-' + filter).addClass(" active");
            });
        });
    </script>

    <?php
    endif;

	$html = ob_get_clean();

	return $html;
}