jQuery(document).ready(function($) {
	

	if ($('#sticky-trip-tab').length) {
		// Element exists in the DOM
		$(window).on('scroll', function() {
			var tabElement = $('#sticky-trip-tab'); // Replace with your tabElement's ID or selector
			var tabElementTop = tabElement.offset().top;
			var tabElementHeight = tabElement.outerHeight();
			var viewportTop = $(window).scrollTop();
			var viewportHeight = $(window).height();
			var viewportMiddle = viewportTop + (viewportHeight / 2);
			var tabElementMiddle = tabElementTop + (tabElementHeight / 2);
			
	
			if (Math.abs(viewportMiddle - tabElementMiddle) < tabElementHeight / 2) {
				tabElement.addClass( 'fixed-trip-tab' );
			}
	
			var tabContent = $('#trip-tab-content');
			var tabContentTop = tabContent.offset().top;
			var scrollTop = $(window).scrollTop();

			if (tabContentTop <= scrollTop) {
				tabElement.removeClass( 'fixed-trip-tab' );
			}else{
				tabElement.addClass( 'fixed-trip-tab' );
			}

			var tabContentStart = $('#trip-tab-content-start');
			var windowTop = $(window).scrollTop();
			var windowBottom = windowTop + $(window).height();
			var tabContentStartTop = tabContentStart.offset().top;
			var tabContentStartBottom = tabContentStartTop + tabContentStart.outerHeight();

			if ((tabContentStartTop >= windowTop && tabContentStartTop < windowBottom) ||
				(tabContentStartBottom > windowTop && tabContentStartBottom <= windowBottom) ||
				(tabContentStartTop < windowTop && tabContentStartBottom > windowBottom)) {
					tabElement.removeClass( 'fixed-trip-tab' );
			} 
			
		});
	
	}
	
	// $('.wp-travel-itinerary-items').hide();
	$('.same-height').matchHeight();

	var getHeight = document.querySelectorAll('.same-height');
	for (var i = 0; i < getHeight.length; ++i) {

		if( getHeight[i].children[0].children[0].tagName == 'IMG' ){
			getHeight[i].children[0].children[0].style.maxHeight = getHeight[i].style.cssText.replace( "height: ", "" ).replace( ";", "" );
			getHeight[i].children[0].children[0].style.height = "100%";
			
		}
	}

    $( '.wp-travel-sticky-content').theiaStickySidebar({
        additionalMarginTop: 30
    });

	$('#wptravel-block-video-button').magnificPopup({
		type: 'iframe',
		mainClass: 'mfp-fade',
		preloader: true,
	});

	$('.wptravel-block-trip-gallery').magnificPopup({
        delegate: 'a', // child items selector, by clicking on it popup will open
        type: 'image',
        // other options
        gallery: {
            enabled: true
        }
    });
	

    $('.open-all-itinerary-link').click(function (e) {
		e.preventDefault();
		var parent = '#wptravel-block-trip-outline';
		$(parent + ' .panel-title a').removeClass('collapsed').attr({ 'aria-expanded': 'true' });
		$(parent + ' .panel-collapse').addClass('collapse in').css('height', 'auto');
		$(this).hide();
		$(parent + ' .close-all-itinerary-link').show();
		$(parent + ' #tab-accordion .panel-collapse').css('height', 'auto');
	});

	// Close All accordion.
	$('.close-all-itinerary-link').click(function (e) {
		var parent = '#wptravel-block-trip-outline';
		e.preventDefault();
		$(parent + ' .panel-title a').addClass('collapsed').attr({ 'aria-expanded': 'false' });
		$(parent + ' .panel-collapse').removeClass('in').addClass('collapse');
		$(this).hide();
		$(parent + ' .open-all-itinerary-link').show();
	});

    $('.open-all-faq-link').click(function (e) {
		e.preventDefault();
		var parent = '#faq.faq';
		$(parent + ' .panel-title a').removeClass('collapsed').attr({ 'aria-expanded': 'true' });
		$(parent + ' .panel-collapse').addClass('collapse in').css('height', 'auto');
		$(this).hide();
		$(parent + ' .close-all-faq-link').show();
		$(parent + ' #tab-accordion .panel-collapse').css('height', 'auto');
	});

	// Close All accordion.
	$('.close-all-faq-link').click(function (e) {
		var parent = '#faq.faq';
		e.preventDefault();
		$(parent + ' .panel-title a').addClass('collapsed').attr({ 'aria-expanded': 'false' });
		$(parent + ' .panel-collapse').removeClass('in').addClass('collapse');
		$(this).hide();
		$(parent + ' .open-all-faq-link').show();
	});


            
	var element = document.querySelector(".wptravel-book-your-trips.wp-travel-booknow-btns");
	
	if( element !== null ){
		element.classList.add("wp-block-button__link");
	}

	const counter = $('.counter-item');

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

	const barCounter = $('.progressbar-item');

	if (barCounter.length > 0) {
		barCounter.each(function() {
			let $item = $(this);

			// Check the bar type and initialize the appropriate progress bar
			if ($item.attr('bar-type') === 'circle') {
				new ProgressBar.Circle($item[0], {
					strokeWidth: 5,
					easing: 'easeInOut',
					duration: 1400,
					color: $item.attr('bar-color'),
					trailColor: $item.attr('bar-path-color'),
					trailWidth: 3,
					svgStyle: { width: $item.attr('bar-width'), height: $item.attr('bar-width') }
				}).animate($item.attr('bar-progress-count') / 100);
			} else {
				new ProgressBar.Line($item[0], {
					strokeWidth: 3,
					easing: 'easeInOut',
					duration: 1400,
					color: $item.attr('bar-color'),
					trailColor: $item.attr('bar-path-color'),
					trailWidth: 2
				}).animate($item.attr('bar-progress-count') / 100);
			}
		});
	}

	//modal code 
	$('.wp-travel-blocks-main-modal svg').click(function() {
		$(this).closest('.wp-travel-blocks-main-modal').hide();
	});

	$('.wp-travel-blocks-modal-btn button').click(function() {
		$(this).closest('.wp-travel-blocks-modal-wrapper').find('.wp-travel-blocks-main-modal').show();
	});

	function showModal() {
        $('.wp-travel-blocks-main-modal.autoModalShow').show();
        localStorage.setItem('modalShown', 'true');
    }

    // Check if modal has been shown before
    if (!localStorage.getItem('modalShown')) {
        showModal();
    }

	let resetTimeout;

	document.addEventListener('visibilitychange', function() {
		if (document.visibilityState === 'hidden') {
			// Start a timer for 1 hour (3600000 milliseconds)
			resetTimeout = setTimeout(() => {
				localStorage.removeItem('modalShown');
			}, 3600000);
		} else if (document.visibilityState === 'visible') {
			// User has returned to the page, cancel the timeout
			if (resetTimeout) {
				clearTimeout(resetTimeout);
			}
		}
	});
	//end modal code

	const checkoutElement = document.getElementById('wp-travel-one-page-checkout-enables');

	if (checkoutElement) {
		const btnLabel = checkoutElement.getAttribute('data-btn-label');
		document.querySelector('.wptravel-book-your-trips').textContent = btnLabel;
	}
});



document.addEventListener('DOMContentLoaded', () => {
    // Initialize Accordion for each container
    const accordions = document.querySelectorAll('.accordion-container');
    accordions.forEach(container => {
        new Accordion(container);
    });
});