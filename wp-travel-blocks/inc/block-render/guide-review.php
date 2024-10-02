<?php
/**
 * 
 * Render Callback For Guide Review
 * 
 */

function wptravel_block_guide_review_render( $attributes ) {
    ob_start();
    $guide_data = get_user_by( 'login', get_the_title() )->data;
    $font_color = "#f9a032";
    $font_size = "16px";

    if( isset( $attributes['textColor'] ) ){
        $font_color = "var(--wp--preset--color--".$attributes['textColor'].")";
       
    }else{
        if( isset($attributes['style']['color']) ){
            $font_color = $attributes['style']['color']['text'];
        }
        
    }   
    

    if( isset( $attributes['fontSize'] ) ){
        $font_size = "var(--wp--preset--font-size--".$attributes['fontSize'].")";
    }else{
        if( isset($attributes['style']['typography']) ){
            $font_size = $attributes['style']['typography']['fontSize'];
        }
        
    }

    $guide_city        = get_user_meta( $guide_data->ID, 'city', true );
    ?>

    <p class="wptravel-guide-review-block wptravel-tourguide-aside-review">
        <?php if( $guide_data ) :?>
            <?php if ( wp_travel_travel_guide_rating( get_the_ID() )['review_count'] < 1 ) : ?>
                    <span class="wp-travel-noreviews"><?php echo esc_html__( 'No review yet.', 'wp-travel-blocks' ); ?></span>
                <?php
                else :
                    $avg_rating = wp_travel_travel_guide_rating()['avg_rating'];
                    for ( $i = 1; $i <= 5; $i++ ) {
                        $star_checked = '';
                        if ( $i <= $avg_rating ) {
                            $star_checked = 'checked';
                        }
                        ?>
                        <span class="block fa fa-star <?php echo esc_attr( $star_checked ); ?>"></span>
                    <?php } 
                        if( $attributes['enableText'] ){
                    ?>

                    <span><?php echo esc_html( wp_travel_travel_guide_rating()['review_count'] ); ?><?php echo esc_html__( ' reviews', 'wp-travel-blocks' ); ?></span>
            <?php } endif; ?>
            <?php else: ?>
                <span class="fa fa-star checked"></span>
                <span class="fa fa-star checked"></span>
                <span class="fa fa-star checked"></span>
                <span class="fa fa-star "></span>
                <span class="fa fa-star "></span>
                <?php  if( $attributes['enableText'] ){ ?>
                <span><?php echo esc_html__( '2 reviews', 'wp-travel-blocks' ); ?></span>
        <?php } endif; ?>
    </p>
    <style>
        .wptravel-guide-review-block.wptravel-tourguide-aside-review span.block.checked:before{
            font-weight: 900 !important;
        }
        .wptravel-guide-review-block.wptravel-tourguide-aside-review span.block.checked,
        .wptravel-guide-review-block.wptravel-tourguide-aside-review .block.fa-star{
            color: <?php echo esc_attr( $font_color ); ?> !important;
            font-size: <?php echo esc_attr( $font_size ); ?>;
        }

        .wptravel-guide-review-form-block #wp-travel_rate a,
        .wptravel-guide-review-form-block.wptravel-travel-guide-Review .wp-travel-average-review:before,
        .wptravel-guide-review-form-block .wp-travel-average-review span{
            color: <?php echo esc_attr( $font_color ); ?> !important;
        }
    </style>
    <?php

	return ob_get_clean();
}
