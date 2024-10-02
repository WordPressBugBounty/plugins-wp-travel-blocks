<?php
/**
 * 
 * Render Callback For Guide Image 
 * 
 */

function wptravel_block_guide_image_render( $attributes ) {
	ob_start();
    $guide_data = get_user_by( 'login', get_the_title() )->data;
    $guide_profile_img = ! empty( get_user_meta( $guide_data->ID, 'profile_picture', true ) ) ? get_user_meta( $guide_data->ID, 'profile_picture', true )['url'] : plugin_dir_url( __FILE__ ) . 'assets/uploads/wp-travel-placeholder.png';

    $image_width = 300;
    $image_height = 300;
    if( isset( $attributes['imageWidth'] ) ){
        $image_width = $attributes['imageWidth'];
    }
    if( isset( $attributes['imageHeight'] ) ){
        $image_height = $attributes['imageHeight'];
    }
    ?>
    
    <img class="wptravel-guide-image-block" src="<?php echo esc_url($guide_profile_img); ?>">
    <style>
        .wptravel-guide-image-block{
            height: <?php echo esc_attr( $image_height ); ?>px;
            width: <?php echo esc_attr( $image_width ); ?>px;
            object-fit: cover;
        }
    </style>
    <?php

	return ob_get_clean();
}
