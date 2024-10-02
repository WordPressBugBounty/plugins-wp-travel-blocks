<?php
/**
 * 
 * Render Callback For Cart Button
 * 
 */

function wptravel_block_cart_button_render( $attributes, $content, $block ) {
	
    global $wp_travel_itinerary;
	global $wt_cart;
	$trip_items     = $wt_cart->getItems();

	ob_start();

    // echo "<pre>"; print_r($attributes); die;

    if ( wp_travel_add_to_cart_system() == true ) { ?>
        <style>
            <?php if( isset( $attributes["textColor"] ) ) { ?>
                #wptravel-blocks-cart-button .editor-styles-wrapper.wp-block-button .wp-block-button__link,
                #wptravel-blocks-cart-button .wp-block-button .wp-block-button__link {
                    color: <?php echo esc_attr( $attributes["textColor"] ); ?>;
                }
            <?php } if( isset( $attributes["backgroundColor"] ) ) { ?>
                #wptravel-blocks-cart-button .editor-styles-wrapper.wp-block-button .wp-block-button__link,
                #wptravel-blocks-cart-button .wp-block-button .wp-block-button__link  {
                    background-color: <?php echo esc_attr( $attributes["backgroundColor"] ); ?>;
                }
            <?php } if( isset( $attributes["textColorHover"] ) ) { ?>
                #wptravel-blocks-cart-button .editor-styles-wrapper.wp-block-button .wp-block-button__link:hover,
                #wptravel-blocks-cart-button .wp-block-button .wp-block-button__link:hover  {
                    color: <?php echo esc_attr( $attributes["textColorHover"] ); ?> !important;
                }
            <?php } if( isset( $attributes["backgroundColorHover"] ) ) { ?>
                #wptravel-blocks-cart-button .editor-styles-wrapper.wp-block-button .wp-block-button__link:hover,
                #wptravel-blocks-cart-button .wp-block-button .wp-block-button__link:hover,
                #wptravel-blocks-cart-button .wp-block-button .wp-block-button__link:focus{
                    background-color: <?php echo esc_attr( $attributes["backgroundColorHover"] ); ?> !important;
                }
            <?php } if( isset( $attributes["borderRadius"] ) ) { ?>
                #wptravel-blocks-cart-button .editor-styles-wrapper.wp-block-button .wp-block-button__link,
                #wptravel-blocks-cart-button .wp-block-button .wp-block-button__link  {
                    border-radius: <?php echo esc_attr( $attributes["borderRadius"]."px" ); ?> !important;
                    transition: 0.25s ease-in-out border-radius;
                }
            <?php } if( isset( $attributes["borderRadiusHover"] ) ) { ?>
                #wptravel-blocks-cart-button .editor-styles-wrapper.wp-block-button .wp-block-button__link:hover,
                #wptravel-blocks-cart-button .wp-block-button .wp-block-button__link:hover{
                    border-radius: <?php echo esc_attr( $attributes["borderRadiusHover"]."px" ); ?> !important;
                }
            <?php } ?>
        </style>
    
        <div id="wptravel-blocks-cart-button">
            <div id="wp-travel__add-to-cart_notice"></div>

            <a class="wptravel-blocks-cart-btn editor-styles-wrapper wp-block-button" href="<?php echo function_exists('wptravel_get_checkout_url') ? esc_url( wptravel_get_checkout_url() ) : "#"; ?>" target="_blank" rel="noopener noreferrer">
                <button class="wptravel-blocks-single-trip-cart-button wp-block-button__link">
                    <span id="wptravel-blocks-cart-cart_item_show">
                        <span class="btn-icon-label">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                            <?php if( isset( $attributes["buttonLabel"] ) ) { ?>
                                <span class="btn-label"><?php echo esc_html( $attributes["buttonLabel"] );?></span>
                            <?php } ?>
                        </span>
                        <span class="wp-travel-cart-items-number <?php echo ( !empty( $trip_items ) && count( $trip_items ) > 0 ) ? 'active' : '' ?>"><?php if( count( $trip_items ) > 0 ) echo count( $trip_items ); ?></span>
                    </span>
                </button>
            </a>
        </div>
		
	<?php }
	
	$html = ob_get_clean();

	return $html;
}
