/**
 * Blue Sage — Testimonial Carousel
 *
 * Crossfade carousel with dot navigation and optional autoplay.
 * Loaded via viewScript — only runs on pages containing this block.
 *
 * Features:
 *   - Crossfade transitions (CSS opacity)
 *   - Dot navigation (pill indicator for active)
 *   - Optional autoplay with pause-on-hover
 *   - Keyboard navigation (← →, Home, End)
 *   - Reduced-motion: disables autoplay, removes transitions via CSS
 *   - ARIA: aria-selected on dots, aria-live on track
 */
( function () {
	'use strict';

	const prefersReducedMotion = window.matchMedia(
		'(prefers-reduced-motion: reduce)'
	).matches;

	/**
	 * Initialise a single carousel root element.
	 *
	 * @param {HTMLElement} root  The <section> element with data-autoplay / data-delay.
	 */
	function initCarousel( root ) {
		const track = root.querySelector( '.bs-testimonials__track' );
		const slides = Array.from(
			root.querySelectorAll( '.bs-testimonial' )
		);
		const dots = Array.from(
			root.querySelectorAll( '.bs-testimonials__dot' )
		);

		if ( ! track || slides.length < 2 ) return;

		let current = 0;
		let timer = null;

		const autoplay = root.dataset.autoplay === 'true' && ! prefersReducedMotion;
		const delay = parseInt( root.dataset.delay, 10 ) || 5000;

		// Set container height to tallest slide so layout doesn't jump.
		function setTrackHeight() {
			// Temporarily make all slides visible to measure.
			slides.forEach( ( s ) => {
				s.style.position = 'relative';
				s.style.opacity = '1';
			} );
			const tallest = Math.max( ...slides.map( ( s ) => s.offsetHeight ) );
			slides.forEach( ( s ) => {
				s.style.position = '';
				s.style.opacity = '';
			} );
			track.style.minHeight = tallest + 'px';
		}

		/**
		 * Transition to slide at given index.
		 *
		 * @param {number} index
		 */
		function goTo( index ) {
			const prev = current;
			current = ( ( index % slides.length ) + slides.length ) % slides.length;

			// Slides.
			slides[ prev ].classList.remove( 'is-active' );
			slides[ current ].classList.add( 'is-active' );

			// Dots.
			dots[ prev ].classList.remove( 'is-active' );
			dots[ prev ].setAttribute( 'aria-selected', 'false' );
			dots[ current ].classList.add( 'is-active' );
			dots[ current ].setAttribute( 'aria-selected', 'true' );
		}

		function next() {
			goTo( current + 1 );
		}

		function startAutoplay() {
			if ( ! autoplay ) return;
			timer = setInterval( next, delay );
		}

		function stopAutoplay() {
			clearInterval( timer );
		}

		// Dot click.
		dots.forEach( ( dot, i ) => {
			dot.addEventListener( 'click', () => {
				stopAutoplay();
				goTo( i );
				startAutoplay();
			} );
		} );

		// Keyboard navigation on the track.
		root.addEventListener( 'keydown', ( e ) => {
			if ( e.key === 'ArrowLeft' ) {
				stopAutoplay();
				goTo( current - 1 );
				startAutoplay();
				e.preventDefault();
			} else if ( e.key === 'ArrowRight' ) {
				stopAutoplay();
				goTo( current + 1 );
				startAutoplay();
				e.preventDefault();
			} else if ( e.key === 'Home' ) {
				stopAutoplay();
				goTo( 0 );
				startAutoplay();
				e.preventDefault();
			} else if ( e.key === 'End' ) {
				stopAutoplay();
				goTo( slides.length - 1 );
				startAutoplay();
				e.preventDefault();
			}
		} );

		// Pause autoplay on hover / focus inside.
		root.addEventListener( 'mouseenter', stopAutoplay );
		root.addEventListener( 'focusin', stopAutoplay );
		root.addEventListener( 'mouseleave', startAutoplay );
		root.addEventListener( 'focusout', startAutoplay );

		// Initialise.
		setTrackHeight();
		window.addEventListener( 'resize', setTrackHeight );
		startAutoplay();
	}

	// Boot all carousels on the page.
	function init() {
		const carousels = document.querySelectorAll(
			'.bs-testimonials[data-autoplay]'
		);
		carousels.forEach( initCarousel );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
