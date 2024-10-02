<?php
/**
 * 
 * Render Callback For Cart Button
 * 
 */

function wptravel_block_book_button_render( $attributes, $content, $block ) {

	ob_start();
?>
        <style>
            <?php if( isset( $attributes["textColor"] ) ) { ?>
                #wptravel-blocks-book-button a{
                    color: <?php echo esc_attr( $attributes["textColor"] ); ?>;
                }
            <?php } if( isset( $attributes["backgroundColor"] ) ) { ?>
                #wptravel-blocks-book-button button{
                    background-color: <?php echo esc_attr( $attributes["backgroundColor"] ); ?>;
                }
            <?php } if( isset( $attributes["textColorHover"] ) ) { ?>
                #wptravel-blocks-book-button button:hover {
                    color: <?php echo esc_attr( $attributes["textColorHover"] ); ?>;
                }
            <?php } if( isset( $attributes["backgroundColorHover"] ) ) { ?>
                #wptravel-blocks-book-button button:hover {
                    background-color: <?php echo esc_attr( $attributes["backgroundColorHover"] ); ?>;
                }
            <?php } if( isset( $attributes["borderRadius"] ) ) { ?>
                #wptravel-blocks-book-button button{
                    border-radius: <?php echo esc_attr( $attributes["borderRadius"]."px" ); ?>;
                    transition: 0.25s ease-in-out border-radius;
                }
            <?php } if( isset( $attributes["borderRadiusHover"] ) ) { ?>
                #wptravel-blocks-book-button button:hover {
                    border-radius: <?php echo esc_attr( $attributes["borderRadiusHover"]."px" ); ?>;
                }
            <?php } ?>
        </style>
        
        <?php if(  isset( wptravel_get_settings()['enable_one_page_booking'] ) && wptravel_get_settings()['enable_one_page_booking'] !== '1' ): ?>
            <div id="wptravel-blocks-book-button">
                <a class="wptravel-blocks-book-btn editor-styles-wrapper wp-block-button" id="trip-booking" href="#booking" rel="noopener noreferrer">
                    <button class="wptravel-blocks-single-trip-book-button wp-block-button__link">
                        <?php echo esc_html( $attributes["buttonLabel"] ); ?>
                    </button>
                </a>
            </div>
            <?php else: ?>
                <div id="wp-travel-one-page-checkout-enables">
                </div>
        <?php endif; ?>

    
	<?php
	
	$html = ob_get_clean();

	return $html;
}
