<?php
/**
 * 
 * Render Callback For Trip Extra Price
 * 
 */

function wptravel_block_trip_extra_price_render( $attributes ) {
    ob_start();

    $post_id = get_the_id();
    $trip_extras_data = get_post_meta( $post_id, 'wp_travel_tour_extras_metas', true );
    
    $description      = isset( $trip_extras_data['extras_item_description'] ) && ! empty( $trip_extras_data['extras_item_description'] ) ? $trip_extras_data['extras_item_description'] : false;
    $price            = isset( $trip_extras_data['extras_item_price'] ) && ! empty( $trip_extras_data['extras_item_price'] ) ? $trip_extras_data['extras_item_price'] : false;
    $sale_price       = isset( $trip_extras_data['extras_item_sale_price'] ) && ! empty( $trip_extras_data['extras_item_sale_price'] ) ? $trip_extras_data['extras_item_sale_price'] : false;
    $unit             = isset( $trip_extras_data['extras_item_unit'] ) && ! empty( $trip_extras_data['extras_item_unit'] ) ? $trip_extras_data['extras_item_unit'] : false;
    
    
    // Multiple Currency Support.
    $price      = apply_filters( 'wp_travel_multiple_currency', $price );
    $sale_price = apply_filters( 'wp_travel_multiple_currency', $sale_price );

    ?>

    <div class="wptravel-trip-extra-price-block">
        <?php

            if( $trip_extras_data  ){
            ?>
                <div class="wp-tour-extras-detail-pricing">
                    <?php if( $price || $sale_price ) : ?>
                        <?php if( $sale_price ) : ?>
                            <del>
                                <?php echo wp_kses_post( wp_travel_tour_extras_formated_price( $price ) ); ?>
                            </del>
                            <ins>
                                <?php echo wp_kses_post( wp_travel_tour_extras_formated_price( $sale_price ) ); ?> / <?php echo esc_html( $unit ); ?>
                            </ins>
                        <?php else : ?>
                            <ins> 
                                <?php echo wp_kses_post( wp_travel_tour_extras_formated_price( $price ) ); ?> / <?php echo esc_html( $unit ); ?>
                            </ins>
                        <?php endif; ?>
                    
                    <?php endif; ?>
                </div>
            <?php
            }else{
               ?>
               <div class="wptravel-trip-extra-price-block">
                    <div class="wp-tour-extras-detail-pricing">
                        <del>$ 200.00</del>
                        <ins>$ 180.00 / unit</ins>                                            
                    </div>
                </div>
               <?php
            }
           
        ?>
    </div>

    <?php

	return ob_get_clean();
}
