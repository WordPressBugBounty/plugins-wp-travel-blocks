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

});