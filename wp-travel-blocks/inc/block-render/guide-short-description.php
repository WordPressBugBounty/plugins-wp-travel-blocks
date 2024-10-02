<?php
/**
 * 
 * Render Callback For Guide Short Description
 * 
 */

function wptravel_block_guide_short_description_render( $attributes ) {
    ob_start();
    $guide_data = get_user_by( 'login', get_the_title() )->data;
    $font_color = "#000";
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

    $guide_description        = get_user_meta( $guide_data->ID, 'description', true );
    ?>

    <p class="wptravel-guide-description-block">
        <?php 
            if( $guide_data ){ 
                echo esc_html( $guide_description ); 
            }else{
                echo esc_html__( 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.', 'wp-travel-blocks' );
            }
            
        ?>
    </p>
    <style>
        .wptravel-guide-description-block{
            color: <?php echo esc_attr( $font_color ); ?>;
            font-size: <?php echo esc_attr( $font_size ); ?>;
        }
    </style>
    <?php

	return ob_get_clean();
}
