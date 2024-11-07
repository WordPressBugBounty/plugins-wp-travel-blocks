<?php
/**
 * 
 * Render Callback For Trip FAQS
 * 
 */


function wptravel_block_countdown_render( $attributes ) {

	add_action('wp_enqueue_scripts', function() use ($attributes) {

		wp_register_script('wp-travel-blocks-countdown-dynamic-script', false, [], false, true); 
	

		wp_enqueue_script('wp-travel-blocks-countdown-dynamic-script');
	
		$custom_js = "
			var countDownDate = new Date('".esc_attr( $attributes['endDate'] )."').getTime();

			var x = setInterval(function() {

			var now = new Date().getTime();

			var distance = countDownDate - now;
				
			var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((distance % (1000 * 60)) / 1000);

			document.querySelector('#countdown-".esc_attr( $attributes['blockId'] )." #days span').innerHTML = days;
			document.querySelector('#countdown-".esc_attr( $attributes['blockId'] )." #hours span').innerHTML = hours;
			document.querySelector('#countdown-".esc_attr( $attributes['blockId'] )." #minutes span').innerHTML = minutes;
			document.querySelector('#countdown-".esc_attr( $attributes['blockId'] )." #seconds span').innerHTML = seconds;
			
			document.querySelector('#countdown-".esc_attr( $attributes['blockId'] )."' ).style.visibility = 'visible';

			if (distance < 0) {
				clearInterval(x);
				document.querySelector('#countdown-".esc_attr( $attributes['blockId'] )."').innerHTML = '".esc_html__( 'EXPIRED', 'wp-travel-blocks')."';
			}
			}, 1000);
		";
	
		// Add the inline script
		wp_add_inline_script('wp-travel-blocks-countdown-dynamic-script', $custom_js);
	});
	ob_start();
	?>

	<div id="countdown-<?php echo esc_attr( $attributes['blockId'] );?>" class="wptravel-block-wrapper wptravel-block-countdown <?php echo !is_admin() ? 'front' : '';?>">
		<p> 
			<span id="days"><span> 2</span> <?php echo esc_html( $attributes['daysLabel'] );?></span>
			<span id="hours"><span> 2</span> <?php echo esc_html( $attributes['hoursLabel'] );?></span>
			<span id="minutes"><span> 30</span> <?php echo esc_html( $attributes['minutesLabel'] );?></span>
			<span id="seconds"><span> 59</span> <?php echo esc_html( $attributes['secondsLabel'] );?></span>
		</p>
	</div>
	
	<?php
	return ob_get_clean();
}