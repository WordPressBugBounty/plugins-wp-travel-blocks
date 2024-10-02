jQuery(document).ready(function($) {

	const counter = $('.counter-item').hide();

	if( counter.length > 0 ){

		counter.each(function() {
			let $item = $(this);
			let counterInnerText = parseInt($item.text(), 10);

			let count = 1;

			function counterUp() {
				$item.text(count++);
				if (counterInnerText < count) {
					clearInterval(stop);
				}
			}

			const stop = setInterval(() => {
				counterUp();
			}, 10);
		});

	}


	// var bar = new ProgressBar.Circle('.progressbar-item', {
	// 	strokeWidth: 5,
	// 	easing: 'easeInOut',
	// 	duration: 1400,
	// 	color: '#FFEA82',
	// 	trailColor: '#eee',
	// 	trailWidth: 3,
	//   });
	  
	//   bar.animate(0.5);
	
});