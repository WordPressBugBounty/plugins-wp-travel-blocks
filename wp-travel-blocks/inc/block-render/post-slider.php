<?php
/**
 * 
 * Render Callback For Trip FAQS
 * 
 */


function wptravel_block_post_slider_render( $attributes ) {

    add_action('wp_enqueue_scripts', function() use ($attributes) {

        // Register a dummy script handle (not associated with an actual file)
        wp_register_script('wp-travel-blocks-post-slider-dynamic-script', '', [], false, true);
        
        // Enqueue the script to ensure it is loaded
        wp_enqueue_script('wp-travel-blocks-post-slider-dynamic-script');
    
        // Define the inline JavaScript with corrected syntax
        $custom_js = "
            new Splide( '#post-slider-" .esc_attr( $attributes['blockId'] ). "', {
                type       : 'loop',
                perPage    : 3,
                gap        : '30px',
                pagination : false,
                breakpoints: {
                    640: {
                    height: '6rem',
                    },
                },
            } ).mount();
        ";
    
        // Add the inline script to the registered script handle
        wp_add_inline_script('wp-travel-blocks-post-slider-dynamic-script', $custom_js);
    });
    
    $args = array();

    if( $attributes['contentType'] == 'post-ids' ){
        
        $post_ids = array_map('intval', explode(',', $attributes['postIds']));

        $args = array(
            'post_type'    => 'post',
            'post__in'      => $post_ids,
        );
    }

    if( $attributes['contentType'] == 'category-ids' ){
        $category_ids  = array_map('intval', explode(',', $attributes['categoryIds']));

        $args = array(
            'post_type'    => 'post',
            'numberposts' => $attributes['numPosts'],
            'category'   => $category_ids,
        );
    }

    if( $attributes['contentType'] == 'recent' ){

        $args = array(
            'post_type'    => 'post',
            'numberposts' => $attributes['numPosts'],
        );
    }

    

    $getPosts = get_posts($args);
	wp_reset_postdata();

	ob_start();
	?>

	<section id="post-slider-<?php echo esc_attr( $attributes['blockId'] );?>" class="post-slider splide" >
        <div class="splide__track">
            <ul class="splide__list">
                <?php foreach ($getPosts as $post) : ?>
                    <li class="splide__slide">
                        <div class='post-wrapper'>
                            <div class='featured-image'>
                                <img src="<?php echo esc_url(get_the_post_thumbnail_url($post->ID));?>"/>
                            </div>
                            <div class='entry-title'>
                                <h2 class='post-title'><?php echo esc_html(get_the_title($post->ID));?></h2>
                            </div>
                            <div class='entry-meta'>
                            <span class="posted-on">
                                <time class="entry-date"><?php echo esc_html(get_the_date('', $post->ID));?></time>
                            </span>
                            </div>
                            <div class='entry-content'>
                                <?php echo esc_html(get_the_excerpt($post->ID));?>
                            </div>											
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
	</section>
	
	<?php
	return ob_get_clean();
}