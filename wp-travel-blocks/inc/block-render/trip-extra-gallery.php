<?php
/**
 * 
 * Render Callback For Trip Extra Gallery
 * 
 */

function wptravel_block_trip_extra_gallery_render( $attributes ) {
    ob_start();

    $post_id = get_the_id();
    $gallery_ids = get_post_meta( $post_id, 'wp_travel_extras_gallery_ids', true );

?>
    <div class="wptravel-trip-extra-gallery-block wp-tour-extras-detail">
        <?php if( $gallery_ids ){ ?>
            <?php if ( ! empty( $gallery_ids ) && is_array( $gallery_ids ) ) : ?>
                <div class="wp-tour-extras-detail-slider-wrap">
                    <div class="wp-tour-extras-detail-slider">

                        <?php foreach( $gallery_ids as $key => $id ) :
                        $img_url = wp_get_attachment_image_url( $id, 'large' );
                        $img_url_large = wp_get_attachment_image_url( $id, 'large' );
                        ?>
                            <div><figure class="wp-tour-extras-detail-thumb"><a href="<?php echo esc_url( $img_url_large ); ?>"><img src="<?php echo esc_url( $img_url ); ?>" alt=""></a></figure></div>

                        <?php endforeach; ?>


                    </div>
                    <div class="wp-tour-extras-detail-slider-nav">
                        <?php foreach( $gallery_ids as $key => $id ) :
                        $img_url = wp_get_attachment_image_url( $id, 'medium' );
                        ?>
                            <div><figure class="wp-tour-extras-detail-thumb"><img src="<?php echo esc_url( $img_url ); ?>" alt=""></figure></div>

                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php
        }else{
            if( !get_the_id() ):
            ?>
            <div class="editor-note">
                Trip Extras Gallery block is only visible in frontend.
            </div>
            <?php
            endif;
        }
           
        ?>
    </div>

    <?php

	return ob_get_clean();
}
