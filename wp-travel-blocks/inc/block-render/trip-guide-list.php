<?php
/**
 * 
 * Render Callback For Guide Featured Trip
 * 
 */

function wptravel_block_trip_guide_list_render( $attributes ) {
    ob_start();

    $layout_type = isset( $attributes['layoutType'] ) ? $attributes['layoutType'] : 'default-layout' ;

    $pattern_slug = $attributes['patternSlug'];
    $pattern = '';
    if($pattern_slug){
        $args = array(
            'name'        => $pattern_slug,
            'post_type'   => 'wp_block',
            'post_status' => 'publish',
            'numberposts' => 1
        );
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $pattern = get_the_content();
            }
        }
        wp_reset_postdata();
    }
    global $post;
    $post_id = isset( $post->ID ) ? $post->ID : '';	
    $guides_ids = get_post_meta( $post_id, 'selected_guides', true );
    $trip_guides = array();

    foreach ( $guides_ids as $user_id ) { 
        $user = get_user_by( 'id', $user_id )->data;
        
        $trip_guides[] = get_page_by_path( get_user_meta($user->ID, 'nickname', true), OBJECT, 'travel-guide' )->ID;
    }
    if( !get_the_id() ){
            echo __( 'Guide Featured Trip block only visible in frontend.', 'wp-travel-pro' );
    }else{
    ?>
        <div class="wptravel-tg-list-view-row grid-layout block" id="list-grid">
            <?php 
            $args = array( );

            if( $trip_guides ){
                $args = array(
                    'post_type' => 'travel-guide',
                    'post__in' => $trip_guides,
                    'orderby' => 'post__in'
                );
            }

            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                $guide_data = get_user_by( 'login', get_the_title() )->data;
                $guide_name = $guide_data->user_nicename;
                $guide_fullname = $guide_data->display_name;
                $guide_url = get_the_permalink();
                $guide_image = !empty(get_user_meta($guide_data->ID, 'profile_picture', true)) ? get_user_meta($guide_data->ID, 'profile_picture', true)['url'] : WP_Travel_Travel_Guide_Core_URL . 'assets/wp-travel-placeholder.png';
                $guide_experience = !empty(get_user_meta($guide_data->ID, 'join_year', true)) ? date('Y') - get_user_meta($guide_data->ID, 'join_year', true) : 0;

                if ($guide_experience > 0) {
                    $guide_experience = $guide_experience . __('years', 'wp-travel-pro');
                } else {
                    $guide_experience = __('Less than a year', 'wp-travel-pro');
                }

                $guide_location = !empty(get_user_meta($guide_data->ID, 'city', true)) ? get_user_meta($guide_data->ID, 'city', true) : '';

                $guide_language = !empty(get_user_meta($guide_data->ID, 'language', true)) ? get_user_meta($guide_data->ID, 'language', true) : '';

                $guide_description = !empty(get_user_meta($guide_data->ID, 'description', true)) ? get_user_meta($guide_data->ID, 'description', true) : '';

            ?>
            
                <?php if( $layout_type == 'custom' ): ?>
                    <?php echo do_blocks( $pattern); ?>
                <?php else:?>
                    <div class="wptravel-tg-column">
                    <div class="wptravel-tg-wrapper guide-list">
                        <div class="wptravel-tg-gallary">
                            <!-- guide image -->
                            <div class="wptravel-tg-guideimage">
                                <img width="100%" height="100%" src="<?php echo esc_url($guide_image); ?>" alt="">
                                <!-- guide rating display:absolute -->
                                <?php if (wp_travel_travel_guide_rating(get_posts(array('name' => $guide_name, 'post_type' => 'travel-guide',))[0]->ID)['review_count'] > 0) :
                                    $avg_rating = wp_travel_travel_guide_rating(get_posts(array('name' => $guide_name, 'post_type' => 'travel-guide',))[0]->ID)['avg_rating'];
                                ?>
                                    <div class="wptravel-tg-rating">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            $star_checked = '';
                                            if ($i <= $avg_rating) {
                                                $star_checked = 'checked';
                                            }
                                        ?>
                                            <span class="fa fa-star <?php echo esc_attr($star_checked); ?>"></span>
                                        <?php } ?>
                                    </div>
                                    <?php else: ?>
                                        <div class="wptravel-tg-rating">
                                            <span class="fa fa-star"></span>
                                            <span class="fa fa-star"></span>
                                            <span class="fa fa-star"></span>
                                            <span class="fa fa-star"></span>
                                            <span class="fa fa-star"></span>
                                        </div>
                                <?php endif ?>
                            </div>

                            <div class="entry-container">
                                <!-- guide name -->
                                <header class="wptravel-tg-guidename">
                                    <h2 class="entry-title">
                                        <a class="heading-link" href="<?php echo esc_url($guide_url); ?>">
                                            <?php echo esc_html($guide_fullname) ?>
                                        </a>
                                    </h2>
                                </header>
                                <!-- experiance -->
                                <?php if (!empty($guide_experience)) : ?>
                                    <div class="wptravel-tg-guide-experiance">
                                        <span><?php echo esc_html__('Experience: ', 'wp-travel-pro') ?> </span><?php echo esc_html($guide_experience); ?>
                                    </div>
                                <?php endif ?>
                                <!-- address -->
                                <?php if (!empty($guide_location)) : ?>
                                    <div class="wptravel-tg-address">
                                        <span><?php echo esc_html__('From: ', 'wp-travel-pro') ?></span><?php echo esc_html($guide_location); ?>
                                    </div>
                                <?php endif ?>
                                <!-- language -->
                                <?php if (!empty($guide_language)) : ?>
                                    <div class="wptravel-tg-guide-language">
                                        <span><?php echo esc_html__('Speaks: ', 'wp-travel-pro') ?></span>
                                        <?php echo esc_html($guide_language); ?>
                                    </div>
                                <?php endif ?>

                                <?php if (!empty(get_user_meta($guide_data->ID, 'facebook_link', true)) || !empty(get_user_meta($guide_data->ID, 'twitter_link', true)) || !empty(get_user_meta($guide_data->ID, 'instagram_link', true)) || !empty(get_user_meta($guide_data->ID, 'tiktok_link', true)) || !empty(get_user_meta($guide_data->ID, 'youtube_link', true))) : ?>
                                    <!-- social links -->
                                    <ul class="wp-social">
                                        <?php if (!empty(get_user_meta($guide_data->ID, 'facebook_link', true))) : ?>
                                            <li>
                                                <a href="<?php echo esc_url(get_user_meta($guide_data->ID, 'facebook_link', true)); ?>" alt="fb-link">
                                                    <svg width="25" height="25">
                                                        <path d="M12,2C6.477,2,2,6.477,2,12c0,5.013,3.693,9.153,8.505,9.876V14.65H8.031v-2.629h2.474v-1.749 c0-2.896,1.411-4.167,3.818-4.167c1.153,0,1.762,0.085,2.051,0.124v2.294h-1.642c-1.022,0-1.379,0.969-1.379,2.061v1.437h2.995 l-0.406,2.629h-2.588v7.247C18.235,21.236,22,17.062,22,12C22,6.477,17.523,2,12,2z" />
                                                    </svg>
                                                </a>
                                            </li>
                                        <?php endif ?>

                                        <?php if (!empty(get_user_meta($guide_data->ID, 'twitter_link', true))) : ?>
                                            <li>
                                                <a href="<?php echo esc_url(get_user_meta($guide_data->ID, 'twitter_link', true)); ?>" alt="twitter-link">
                                                <svg viewBox="0 0 48 48" width="25px" height="25px"><path fill="#03A9F4" d="M42,12.429c-1.323,0.586-2.746,0.977-4.247,1.162c1.526-0.906,2.7-2.351,3.251-4.058c-1.428,0.837-3.01,1.452-4.693,1.776C34.967,9.884,33.05,9,30.926,9c-4.08,0-7.387,3.278-7.387,7.32c0,0.572,0.067,1.129,0.193,1.67c-6.138-0.308-11.582-3.226-15.224-7.654c-0.64,1.082-1,2.349-1,3.686c0,2.541,1.301,4.778,3.285,6.096c-1.211-0.037-2.351-0.374-3.349-0.914c0,0.022,0,0.055,0,0.086c0,3.551,2.547,6.508,5.923,7.181c-0.617,0.169-1.269,0.263-1.941,0.263c-0.477,0-0.942-0.054-1.392-0.135c0.94,2.902,3.667,5.023,6.898,5.086c-2.528,1.96-5.712,3.134-9.174,3.134c-0.598,0-1.183-0.034-1.761-0.104C9.268,36.786,13.152,38,17.321,38c13.585,0,21.017-11.156,21.017-20.834c0-0.317-0.01-0.633-0.025-0.945C39.763,15.197,41.013,13.905,42,12.429"/></svg>
                                                                        
                                                </a>
                                            </li>
                                        <?php endif ?>

                                        <?php if (!empty(get_user_meta($guide_data->ID, 'instagram_link', true))) : ?>
                                            <li>
                                                <a href="<?php echo esc_url(get_user_meta($guide_data->ID, 'instagram_link', true)); ?>" alt="insta-link">
                                                    <svg width="25" height="25">
                                                        <path d="M 8 3 C 5.239 3 3 5.239 3 8 L 3 16 C 3 18.761 5.239 21 8 21 L 16 21 C 18.761 21 21 18.761 21 16 L 21 8 C 21 5.239 18.761 3 16 3 L 8 3 z M 18 5 C 18.552 5 19 5.448 19 6 C 19 6.552 18.552 7 18 7 C 17.448 7 17 6.552 17 6 C 17 5.448 17.448 5 18 5 z M 12 7 C 14.761 7 17 9.239 17 12 C 17 14.761 14.761 17 12 17 C 9.239 17 7 14.761 7 12 C 7 9.239 9.239 7 12 7 z M 12 9 A 3 3 0 0 0 9 12 A 3 3 0 0 0 12 15 A 3 3 0 0 0 15 12 A 3 3 0 0 0 12 9 z" />
                                                    </svg>
                                                </a>
                                            </li>
                                        <?php endif ?>

                                        <?php if (!empty(get_user_meta($guide_data->ID, 'linkedin_link', true))) : ?>
                                            <li>
                                                <a href="<?php echo esc_url(get_user_meta($guide_data->ID, 'linkedin_link', true)); ?>" alt="linkedin-link">
                                                    <svg width="25" height="25">
                                                        <path d="M19,3H5C3.895,3,3,3.895,3,5v14c0,1.105,0.895,2,2,2h14c1.105,0,2-0.895,2-2V5C21,3.895,20.105,3,19,3z M9,17H6.477v-7H9 V17z M7.694,8.717c-0.771,0-1.286-0.514-1.286-1.2s0.514-1.2,1.371-1.2c0.771,0,1.286,0.514,1.286,1.2S8.551,8.717,7.694,8.717z M18,17h-2.442v-3.826c0-1.058-0.651-1.302-0.895-1.302s-1.058,0.163-1.058,1.302c0,0.163,0,3.826,0,3.826h-2.523v-7h2.523v0.977 C13.93,10.407,14.581,10,15.802,10C17.023,10,18,10.977,18,13.174V17z" />
                                                    </svg>
                                                </a>
                                            </li>
                                        <?php endif ?>

                                        <?php if (!empty(get_user_meta($guide_data->ID, 'tiktok_link', true))) : ?>
                                            <li>
                                                <a href="<?php echo esc_url(get_user_meta($guide_data->ID, 'tiktok_link', true)); ?>" alt="tiktok-link">
                                                    <svg width="25" height="25">
                                                        <path d="M 6 3 C 4.3550302 3 3 4.3550302 3 6 L 3 18 C 3 19.64497 4.3550302 21 6 21 L 18 21 C 19.64497 21 21 19.64497 21 18 L 21 6 C 21 4.3550302 19.64497 3 18 3 L 6 3 z M 12 7 L 14 7 C 14 8.005 15.471 9 16 9 L 16 11 C 15.395 11 14.668 10.734156 14 10.285156 L 14 14 C 14 15.654 12.654 17 11 17 C 9.346 17 8 15.654 8 14 C 8 12.346 9.346 11 11 11 L 11 13 C 10.448 13 10 13.449 10 14 C 10 14.551 10.448 15 11 15 C 11.552 15 12 14.551 12 14 L 12 7 z" />
                                                    </svg>
                                                </a>
                                            </li>
                                        <?php endif ?>

                                        <?php if (!empty(get_user_meta($guide_data->ID, 'youtube_link', true))) : ?>
                                            <li>
                                                <a href="<?php echo esc_url(get_user_meta($guide_data->ID, 'youtube_link', true)); ?>" alt="youtube-link">
                                                <svg viewBox="0 0 48 48" width="25px" height="25px"><path fill="#FF3D00" d="M43.2,33.9c-0.4,2.1-2.1,3.7-4.2,4c-3.3,0.5-8.8,1.1-15,1.1c-6.1,0-11.6-0.6-15-1.1c-2.1-0.3-3.8-1.9-4.2-4C4.4,31.6,4,28.2,4,24c0-4.2,0.4-7.6,0.8-9.9c0.4-2.1,2.1-3.7,4.2-4C12.3,9.6,17.8,9,24,9c6.2,0,11.6,0.6,15,1.1c2.1,0.3,3.8,1.9,4.2,4c0.4,2.3,0.9,5.7,0.9,9.9C44,28.2,43.6,31.6,43.2,33.9z"/><path fill="#FFF" d="M20 31L20 17 32 24z"/></svg>
                                                                        
                                                </a>
                                            </li>
                                        <?php endif ?>

                                    </ul>
                                <?php endif ?>
                            </div>

                        </div>
                    </div>   
                    </div>   
                <?php endif;?>        
            
            <?php endwhile; wp_reset_postdata(); else : ?>
                <?php echo esc_html('No Guide Found..', 'wp-travel-pro') ?>
            <?php endif; ?>
        </div>
    <?php
    }
	return ob_get_clean();
}
