<?php
/**
 * 
 * Render Callback For Guide Review Form
 * 
 */

function wptravel_block_guide_review_form_render( $attributes ) {
    ob_start();
    $guide_data = get_user_by( 'login', get_the_title() )->data;
    ?>

    <div class="wptravel-guide-review-form-block wptravel-travel-guide-Review" id="wptravel-travel-guide-Review">
        <h3 class="section-title"> <?php echo esc_html__( 'RECENT REVIEW', 'wp-travel-pro' ); ?></h3>
        <?php comments_template(); ?>
    </div>

    <style>
        .wptravel-guide-review-form-block .comment-reply-link,
        .wptravel-guide-review-form-block #reply-title,
        .wptravel-guide-review-form-block .section-title,
        .wptravel-guide-review-form-block{
            color: <?php echo esc_attr( $font_color ); ?> !important;
        }

        .wptravel-guide-review-form-block textarea#comment{
            display: block;
        }

        #wptravel-travel-guide-Review.wptravel-guide-review-form-block .form-submit input {
            color: var(--wp--preset--color--bright);
            background-color: var(--wp--preset--color--primary);
        }
    </style>
    <?php

	return ob_get_clean();
}
