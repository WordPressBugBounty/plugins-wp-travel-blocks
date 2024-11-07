<?php

class WPTravel_Block_Post_Rest{

    public function __construct(){
        add_action( 'rest_api_init', array( $this, 'wp_travel_blocks_post_rest' ) );
    }

    public function wp_travel_blocks_post_rest(){
        register_rest_route(
            'wp-travel-block/v1',
            '/get-trips',
            array(
                'methods'             => 'get',
                'permission_callback' => '__return_true',
                'callback'            => array( $this, 'wp_travel_blocks_get_trips' ),
            )
        );
    }

    public function wp_travel_blocks_get_trips( WP_REST_Request $request ){
        
        $post_lists = array();

        $args = array(
            'numberposts' => $request->numPost, // Number of posts to retrieve
        );
    
        $getPosts = get_posts($args);
        $i = 0; // Start from index 0
        foreach ($getPosts as $post) {
            $post_lists[$i]['title'] = get_the_title($post->ID);
            $post_lists[$i]['content'] = get_the_content($post->ID);
            $post_lists[$i]['excerpt'] = get_the_excerpt($post->ID);
            $post_lists[$i]['date'] = get_the_date('', $post->ID); // Pass empty string for format
            $post_lists[$i]['featured_image'] = get_the_post_thumbnail_url($post->ID, 'medium');
            $post_lists[$i]['url'] = get_the_permalink($post->ID);
            $i++;
        }
    
        wp_reset_postdata();

        return new WP_REST_Response( $request, 200 );

    }

}

new WPTravel_Block_Post_Rest();