<?php
/**
 * Plugin Name:       	WP Travel Gutenberg Blocks
 * Description: 		WP Travel Gutenberg blocks is a free plugin which integrates seamlessly with the WP Travel plugin and helps you display your trips and tours packages just the way you want through elegantly crafted custom blocks.
 * Requires at least: 	6.0
 * Requires Plugins:    wp-travel
 * Requires PHP:      	7.4
 * Version:         	3.9.1
 * Tested up to: 		6.8
 * Author:           	WP Travel
 * Author URI: 			http://wptravel.io
 * License:          	GPLv3
 * License URI: 		http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       	wp-travel-blocks
 *
 * @package           	wptravel-blocks
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */


 // Exit if accessed Directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



define( 'WP_TRAVEL_BLOCKS_ABS_PATH', dirname(__FILE__).'/' );

class WP_Travel_Blocks {

	/**
	 * Version
	 * 
	 * @var string 
	 */
	public $version = '3.9.1';

	/**
	 * The single instance of the class
	 * 
	 * @var WP Tarvel Block Extend
	 * @since 1.0.0
	 */
	protected static $instance = null;

	/**
	 * 
	 * Main WP Travel Block Extend Instance
	 * Ensure only one instance of WP Travel Block Extend Instance is loaded or can be loaded 
	 * 
	 * @since 1.0.0
	 * @static
	 * @return Main instance
	 * 
	 */
	public static function instance(){
		if( is_null( self::$instance ) ){
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * 
	 * WP Travel Block Extend - constructor
	 * 
	 * @since 1.0.0
	 */
	public function __construct(){

		add_action( 'wp_enqueue_scripts', array( $this, 'block_scripts' ) );

		add_action('admin_head', function() {
			?>
			<script type="text/javascript">
				var placeHolderImage = "<?php echo plugin_dir_url( __FILE__ ) . 'assets/uploads/wp-travel-placeholder.png'; ?>";
			</script>
			<?php
		});

		add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_scripts'), 10, 2 );
		add_filter( 'block_categories_all' , function( $categories ) {
			// Adding a new category.
			$categories[] = array(
				'slug'  => 'wp-travel-blocks',
				'title' => esc_html__( 'WP Travel Blocks', 'wp-travel-blocks' )
			);	
			$categories[] = array(
				'slug'  => 'wp-travel-single-trip-blocks',
				'title' => esc_html__( 'WP Travel Single Trip Blocks', 'wp-travel-blocks' )
			);	
			$categories[] = array(
				'slug'  => 'wp-travel-guide-blocks',
				'title' => esc_html__( 'WP Travel Guide Blocks', 'wp-travel-blocks' )
			);		
			$categories[] = array(
				'slug'  => 'wp-travel-trip-extra-blocks',
				'title' => esc_html__( 'WP Travel Trip Extra Blocks', 'wp-travel-blocks' )
			);	
			return $categories;
		} );


		add_action( 'init', array( $this, 'block_init' ) );

		$this->wptravel_block_include_block_render_callback();

		// add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		
		
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ));

		add_filter('body_class',  array( $this, 'wp_travel_blocks_body_classes' ));

		
		add_action('enqueue_block_assets', array( $this, 'my_enqueue_slider_assets' ));

		add_action('wp_enqueue_scripts', array( $this, 'my_enqueue_slider_assets' ));
		
	}

	public function my_enqueue_slider_assets() {
		// Enqueue Swiper.js for both frontend and editor
		wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), null, true);
		wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), null);
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'wp-travel-blocks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	public function wp_travel_blocks_body_classes( $classes ) {

		$classes[] = 'wp-travel-blocks-frontend';
	
		return $classes;
	}
	
	
	// public function admin_scripts(){

	// 	wp_enqueue_style( 
	// 		'wp-travel-blocks-admin-demo-style', 
	// 		plugin_dir_url( __FILE__ ) . 'assets/css/admin-style.css', 
	// 	);

	// 	wp_enqueue_script( 
	// 		'wp-travel-blocks-admin-demo-script', 
	// 		plugin_dir_url( __FILE__ ) . 'build/demo/index.js' , 
	// 		array( 'jquery' ), 
	// 		'', 
	// 		true 
	// 	);

	// }
	
	public function block_editor_scripts(){

		wp_enqueue_style( 
			'wp-travel-blocks-editor-style', 
			plugin_dir_url( __FILE__ ) . '/assets/css/editor-style.css', 
		);

	}

	public function block_scripts(){

		wp_enqueue_style( 
			'magnific-popup-css', 
			plugin_dir_url( __FILE__ ) . 'assets/css/magnific-popup.css', 
		);

		wp_enqueue_style( 
			'splide-css', 
			plugin_dir_url( __FILE__ ) . 'assets/css/splide.css', 
		);

		wp_enqueue_style( 
			'wp-travel-blocks-front-css', 
			plugin_dir_url( __FILE__ ) . 'assets/css/frontend.css', 
		);

		wp_enqueue_script( 'theia-sticky-js', plugin_dir_url( __FILE__ ) . 'assets/js/theia-sticky-sidebar.js' , array( 'jquery' ), '', true );
		wp_enqueue_script( 'magnific-popup-js', plugin_dir_url( __FILE__ ) . 'assets/js/magnific-popup.js' , array( 'jquery' ), '', true );
		wp_enqueue_script( 'matchheight-js', plugin_dir_url( __FILE__ ) . 'assets/js/matchheight.js' , array( 'jquery' ), '', true );
		wp_enqueue_script( 'splide-js', plugin_dir_url( __FILE__ ) . 'assets/js/splide.js' , array( 'jquery' ), '', true );
		wp_enqueue_script( 'slick-js', plugin_dir_url( __FILE__ ) . 'assets/js/slick.js' , array( 'jquery' ), '', true );
		wp_enqueue_style( 'slick-css', plugin_dir_url( __FILE__ ) . 'assets/slick/slick.css');
		wp_enqueue_style( 'slick-theme-css', plugin_dir_url( __FILE__ ) . 'assets/slick/slick-theme.css');

		wp_enqueue_script( 'wp-travel-blocks-custom-js', plugin_dir_url( __FILE__ ) . 'assets/js/custom.js' , array( 'jquery' ), '', true );
	}

	public function block_init() {

		wp_register_script(
			'wp-travel-blocks-slider-script', 
			plugin_dir_url( __FILE__ ) . 'assets/js/slider-front.js', 
			array(), 
			'',
			true
		);

		register_block_type( __DIR__ . '/build/slider', array(
			'render_callback' => 'wptravel_block_slider_render'
		) );
		register_block_type( __DIR__ . '/build/slides' );

		register_block_type( __DIR__ . '/build/trip-search', array(
			'render_callback' => 'wptravel_block_trip_search_render'
		) );

		register_block_type( __DIR__ . '/build/trip-list', array(
			'render_callback' => 'wptravel_block_trip_list_render'
		) );

		register_block_type( __DIR__ . '/build/trip-calendar', array(
			'render_callback' => 'wptravel_block_trip_calendar_render'
		) );

		register_block_type( __DIR__ . '/build/trip-categories', array(
			'render_callback' => 'wptravel_block_trip_category_render'
		) );

		register_block_type( __DIR__ . '/build/trip-code', array(
			'render_callback' => 'wptravel_block_trip_code_render',
		) );

		register_block_type( __DIR__ . '/build/trip-duration', array(
			'render_callback' => 'wptravel_block_trip_duration_render'
		) );

		register_block_type( __DIR__ . '/build/trip-enquiry', array(
			'render_callback' => 'wptravel_block_trip_enquiry_render'
		) );

		register_block_type( __DIR__ . '/build/trip-excludes', array(
			'render_callback' => 'wptravel_block_trip_excludes_render'
		) );

		register_block_type( __DIR__ . '/build/trip-facts', array(
			'render_callback' => 'wptravel_block_trip_facts_render'
		) );

		register_block_type( __DIR__ . '/build/trip-faqs', array(
			'render_callback' => 'wptravel_block_trip_faqs_render'
		) );

		register_block_type( __DIR__ . '/build/trip-filters', array(
			'render_callback' => 'wptravel_block_trip_filters_render'
		) );

		register_block_type( __DIR__ . '/build/trip-gallery', array(
			'render_callback' => 'wptravel_block_trip_gallery_render'
		) );

		register_block_type( __DIR__ . '/build/trip-group-size', array(
			'render_callback' => 'wptravel_block_trip_group_size_render'
		) );

		register_block_type( __DIR__ . '/build/trip-includes', array(
			'render_callback' => 'wptravel_block_trip_includes_render'
		) );

		register_block_type( __DIR__ . '/build/trip-map', array(
			'render_callback' => 'wptravel_block_trip_map_render'
		) );

		register_block_type( __DIR__ . '/build/trip-pickup-points', array(
			'render_callback' => 'wptravel_block_trip_pickup_points_render'
		) );

		register_block_type( __DIR__ . '/build/trip-outline', array(
			'render_callback' => 'wptravel_block_trip_outline_render'
		) );

		register_block_type( __DIR__ . '/build/trip-overview', array(
			'render_callback' => 'wptravel_block_trip_overview_render'
		) );

		register_block_type( __DIR__ . '/build/trip-price', array(
			'render_callback' => 'wptravel_block_trip_price_render'
		) );

		register_block_type( __DIR__ . '/build/trip-rating', array(
			'render_callback' => 'wptravel_block_trip_rating_render'
		) );

		register_block_type( __DIR__ . '/build/trip-review', array(
			'render_callback' => 'wptravel_block_trip_review_render'
		) );

		register_block_type( __DIR__ . '/build/trip-review-list', array(
			'render_callback' => 'wptravel_block_trip_review_list_render'
		) );

		register_block_type( __DIR__ . '/build/trip-tabs', array(
			'render_callback' => 'wptravel_block_trip_tabs_render'
		) );

		register_block_type( __DIR__ . '/build/trip-timespan', array(
			'render_callback' => 'wptravel_block_trip_timespan_render'
		) );

		register_block_type( __DIR__ . '/build/trip-wishlists', array(
			'render_callback' => 'wptravel_block_trip_wishlists_render'
		) );

		register_block_type( __DIR__ . '/build/trip-sale', array(
			'render_callback' => 'wptravel_block_trip_sale_render'
		) );

		register_block_type( __DIR__ . '/build/trip-departure', array(
			'render_callback' => 'wptravel_block_trip_departure_render'
		) );

		register_block_type( __DIR__ . '/build/trip-downloads', array(
			'render_callback' => 'wptravel_block_trip_downloads_render'
		) );

		register_block_type( __DIR__ . '/build/breadcrumb', array(
			'render_callback' => 'wptravel_block_breadcrumb_render'
		) );

		register_block_type( __DIR__ . '/build/trip-slider', array(
			'render_callback' => 'wptravel_block_trip_slider_render'
		) );

		register_block_type( __DIR__ . '/build/trip-button', array(
			'render_callback' => 'wptravel_block_trip_button_render'
		) );

		register_block_type( __DIR__ . '/build/trip-featured-category', array(
			'render_callback' => 'wptravel_block_trip_featured_category_render'
		) );

		register_block_type( __DIR__ . '/build/filterable-trips', array(
			'render_callback' => 'wptravel_block_filterable_trips_render'
		) );

		register_block_type( __DIR__ . '/build/book-button', array(
			'render_callback' => 'wptravel_block_book_button_render'
		) );

		register_block_type( __DIR__ . '/build/trip-guide-list', array(
			'render_callback' => 'wptravel_block_trip_guide_list_render'
		) );

		
		// if( class_exists( 'WP_Travel_Pro' ) ){
		// 	register_block_type( __DIR__ . '/build/templates' );
		// }
	
		if( function_exists('wptravel_get_settings') && isset( wptravel_get_settings()['modules']['show_wp_travel_tour_guide']['value'] ) && wptravel_get_settings()['modules']['show_wp_travel_tour_guide']['value'] == 'yes' ){
			register_block_type( __DIR__ . '/build/guide-image', array(
				'render_callback' => 'wptravel_block_guide_image_render'
			) );

			register_block_type( __DIR__ . '/build/guide-fullname', array(
				'render_callback' => 'wptravel_block_guide_fullname_render'
			) );

			register_block_type( __DIR__ . '/build/guide-contact-number', array(
				'render_callback' => 'wptravel_block_guide_contact_number_render'
			) );

			register_block_type( __DIR__ . '/build/guide-email', array(
				'render_callback' => 'wptravel_block_guide_email_render'
			) );

			register_block_type( __DIR__ . '/build/guide-country', array(
				'render_callback' => 'wptravel_block_guide_country_render'
			) );

			register_block_type( __DIR__ . '/build/guide-city', array(
				'render_callback' => 'wptravel_block_guide_city_render'
			) );

			register_block_type( __DIR__ . '/build/guide-age', array(
				'render_callback' => 'wptravel_block_guide_age_render'
			) );

			register_block_type( __DIR__ . '/build/guide-join-year', array(
				'render_callback' => 'wptravel_block_guide_join_year_render'
			) );

			register_block_type( __DIR__ . '/build/guide-language', array(
				'render_callback' => 'wptravel_block_guide_language_render'
			) );

			register_block_type( __DIR__ . '/build/guide-gender', array(
				'render_callback' => 'wptravel_block_guide_gender_render'
			) );

			register_block_type( __DIR__ . '/build/guide-social-link', array(
				'render_callback' => 'wptravel_block_guide_social_link_render'
			) );

			register_block_type( __DIR__ . '/build/guide-slogan', array(
				'render_callback' => 'wptravel_block_guide_slogan_render'
			) );

			register_block_type( __DIR__ . '/build/guide-short-description', array(
				'render_callback' => 'wptravel_block_guide_short_description_render'
			) );

			register_block_type( __DIR__ . '/build/guide-biography', array(
				'render_callback' => 'wptravel_block_guide_biography_render'
			) );

			register_block_type( __DIR__ . '/build/guide-featured-trip', array(
				'render_callback' => 'wptravel_block_guide_featured_trip_render'
			) );

			register_block_type( __DIR__ . '/build/guide-review', array(
				'render_callback' => 'wptravel_block_guide_review_render'
			) );

			register_block_type( __DIR__ . '/build/guide-review-form', array(
				'render_callback' => 'wptravel_block_guide_review_form_render'
			) );

			register_block_type( __DIR__ . '/build/guide-code', array(
				'render_callback' => 'wptravel_block_guide_code_render'
			) );
		}

		if( function_exists('wptravel_get_settings') && isset( wptravel_get_settings()['modules']['show_wp_travel_tour_extras']['value'] ) && wptravel_get_settings()['modules']['show_wp_travel_tour_extras']['value'] == 'yes' ){
			register_block_type( __DIR__ . '/build/trip-extra-description', array(
				'render_callback' => 'wptravel_block_trip_extra_description_render'
			) );

			register_block_type( __DIR__ . '/build/trip-extra-price', array(
				'render_callback' => 'wptravel_block_trip_extra_price_render'
			) );

			register_block_type( __DIR__ . '/build/trip-extra-gallery', array(
				'render_callback' => 'wptravel_block_trip_extra_gallery_render'
			) );
		}
		

		/**
		 * Enable Cart Button block only if multi-cart option is enabled in WP Travel
		 * 
		 * @since 3.3.0
		 */
		if( function_exists('wp_travel_add_to_cart_system') && wp_travel_add_to_cart_system() == true ) {
			register_block_type( __DIR__ . '/build/cart-button', array(
				'render_callback' => 'wptravel_block_cart_button_render'
			) );
		}

		register_block_type( __DIR__ . '/build/icon-picker' );

		// register_block_type( __DIR__ . '/build/modal' );

		// register_block_type( __DIR__ . '/build/post-slider', array(
		// 	'render_callback' => 'wptravel_block_post_slider_render'
		// ) );

	}

	public function wptravel_block_include_block_render_callback(){

		$theme = wp_get_theme();

		$theme_slug = $theme->get_stylesheet();

		include sprintf( '%s/inc/breadcrumb-class.php', dirname( __FILE__ ) );
		// include sprintf( '%s/inc/class-rest.php', dirname( __FILE__ ) );
		// include sprintf( '%s/inc/class-templates.php', dirname( __FILE__ ) );

		// if( $theme_slug !== 'themepalace-fse-pro' ){
		// 	include sprintf( '%s/inc/importer/importer.php', dirname( __FILE__ ) );
		// 	include sprintf( '%s/inc/demo.php', dirname( __FILE__ ) );
		// }

		include sprintf( '%s/inc/post-slider-rest.php', dirname( __FILE__ ) );
		// include sprintf( '%s/inc/migration.php', dirname( __FILE__ ) );

		//render block 
		include sprintf( '%s/inc/block-render/trip-search.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-list.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-calendar.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-category.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-code.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-duration.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-enquiry.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-excludes.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-facts.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-faqs.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-filters.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-gallery.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-group-size.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-includes.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-map.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-pickup-points.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-outline.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-overview.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-price.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-rating.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-review.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-review-list.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-tabs.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-timespan.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-wishlists.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-sale.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-departure.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-downloads.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/breadcrumb.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-slider.php', dirname( __FILE__ ) );
		// include sprintf( '%s/inc/block-render/video-button.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-button.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/slider.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-featured-category.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/filterable-trips.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/cart-button.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/book-button.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/post-slider.php', dirname( __FILE__ ) );
		include sprintf( '%s/inc/block-render/trip-guide-list.php', dirname( __FILE__ ) );

		// Guide Render Blocks

		// if( isset( wptravel_get_settings()['modules']['show_wp_travel_tour_guide']['value'] ) && wptravel_get_settings()['modules']['show_wp_travel_tour_guide']['value'] == 'yes' ){
			include sprintf( '%s/inc/block-render/guide-image.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-fullname.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-contact-number.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-email.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-country.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-city.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-age.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-join-year.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-language.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-gender.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-social-link.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-slogan.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-short-description.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-biography.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-featured-trip.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-review.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-review-form.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/guide-code.php', dirname( __FILE__ ) );

			include sprintf( '%s/inc/block-render/trip-extra-description.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/trip-extra-price.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/block-render/trip-extra-gallery.php', dirname( __FILE__ ) );
		// }
		
	}
}

function wptravel_blocks(){
	//return WP_Travel_Blocks::instance();
	return new WP_Travel_Blocks();
}


if( class_exists( 'WP_Travel' ) ) {
	wptravel_blocks();
}
