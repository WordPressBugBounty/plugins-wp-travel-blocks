<?php
/**
 * 
 * Render Callback For Guide Full Name 
 * 
 */

function wptravel_block_guide_fullname_render( $attributes ) {
    ob_start();
    $enable_link     = isset( $attributes['enableLink'] ) ? $attributes['enableLink'] : true; 
    $guide_data = get_user_by( 'login', get_the_title() )->data;
    $font_color = "#000";
    $font_size = "24px";

    if( isset( $attributes['textColor'] ) ){
        $font_color = "var(--wp--preset--color--".$attributes['textColor'].")";
       
    }else{
        if( isset( $attributes['style']['color'] ) ){ 
            $font_color = $attributes['style']['color']['text'];
        }
    }   
    

    if( isset( $attributes['fontSize'] ) ){
        $font_size = "var(--wp--preset--font-size--".$attributes['fontSize'].")";
    }else{
        if( isset( $attributes['style']['typography'] ) ){ 
            $font_size = $attributes['style']['typography']['fontSize'];
        }
    }

    ?>

    <h2 class="wptravel-guide-fullname-block">
        <?php 
            if( $guide_data ){ 

                if( $enable_link ){
                    echo "<a href='".esc_url(get_the_permalink())."'>" .esc_html( $guide_data->display_name ) ."</a>";
                }else{
                    echo esc_html( $guide_data->display_name );
                }

            }else{
                echo esc_html__( 'Guide Full Name', 'wp-travel-blocks' );
            }
            
        ?>
    </h2>
    <style>
        .wptravel-guide-fullname-block a{
            text-decoration: none;
        }
        .wptravel-guide-fullname-block{
            color: <?php echo esc_attr( $font_color ); ?>;
            font-size: <?php echo esc_attr( $font_size ); ?>;
        }
    </style>
    <?php

	return ob_get_clean();
}
