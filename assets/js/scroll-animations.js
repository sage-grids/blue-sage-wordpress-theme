/**
 * Blue Sage — Scroll Animations
 *
 * Provides IntersectionObserver-based entrance animations.
 * Automatically disabled if the user prefers reduced motion.
 *
 * Usage in markup:
 *   .js-fade-up       — Element fades in and slides up 24px.
 *   .js-fade-in       — Element fades in only (no movement).
 *   .js-stagger       — Direct children animate in sequence (80ms stagger).
 *   .js-counter       — Numeric text counts up when entering viewport.
 *                       Requires: data-target="1500"
 *                       Optional: data-prefix="$", data-suffix="+"
 */
( function () {
	'use strict';

	// Bail immediately if the user has requested reduced motion.
	if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	var BASE_OPTIONS = {
		threshold:  0.10,
		rootMargin: '0px 0px -48px 0px',
	};

	// ----------------------------------------------------------------
	// Fade animations (.js-fade-up, .js-fade-in)
	// CSS handles the initial hidden state and transition.
	// JS adds .is-visible to trigger the transition.
	// ----------------------------------------------------------------

	function initFadeAnimations() {
		var elements = document.querySelectorAll( '.js-fade-up, .js-fade-in' );
		if ( ! elements.length ) return;

		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-visible' );
					observer.unobserve( entry.target );
				}
			} );
		}, BASE_OPTIONS );

		elements.forEach( function ( el ) {
			observer.observe( el );
		} );
	}

	// ----------------------------------------------------------------
	// Stagger animations (.js-stagger)
	// Each direct child receives a staggered transition-delay.
	// ----------------------------------------------------------------

	function initStaggerAnimations() {
		var grids = document.querySelectorAll( '.js-stagger' );
		if ( ! grids.length ) return;

		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					var children = Array.from( entry.target.children );

					children.forEach( function ( child, index ) {
						child.style.transitionDelay = ( index * 80 ) + 'ms';
						child.classList.add( 'is-visible' );
					} );

					observer.unobserve( entry.target );
				}
			} );
		}, { threshold: 0.05, rootMargin: '0px 0px -32px 0px' } );

		grids.forEach( function ( el ) {
			observer.observe( el );
		} );
	}

	// ----------------------------------------------------------------
	// Counter animations (.js-counter)
	// Counts from 0 to data-target with an ease-out cubic curve.
	// ----------------------------------------------------------------

	function initCounterAnimations() {
		var counters = document.querySelectorAll( '.js-counter' );
		if ( ! counters.length ) return;

		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					animateCounter( entry.target );
					observer.unobserve( entry.target );
				}
			} );
		}, { threshold: 0.50 } );

		counters.forEach( function ( el ) {
			// Store the target value before we overwrite the text.
			if ( ! el.dataset.target ) {
				el.dataset.target = el.textContent.replace( /[^0-9.]/g, '' );
			}
			observer.observe( el );
		} );
	}

	function animateCounter( el ) {
		var target    = parseFloat( el.dataset.target );
		var prefix    = el.dataset.prefix || '';
		var suffix    = el.dataset.suffix || '';
		var duration  = 1500;
		var startTime = null;

		if ( isNaN( target ) ) return;

		function step( timestamp ) {
			if ( ! startTime ) startTime = timestamp;

			var elapsed  = timestamp - startTime;
			var progress = Math.min( elapsed / duration, 1 );

			// Ease-out cubic: decelerates toward the target.
			var eased   = 1 - Math.pow( 1 - progress, 3 );
			var current = Math.round( eased * target );

			el.textContent = prefix + current.toLocaleString() + suffix;

			if ( progress < 1 ) {
				window.requestAnimationFrame( step );
			}
		}

		window.requestAnimationFrame( step );
	}

	// ----------------------------------------------------------------
	// Boot
	// ----------------------------------------------------------------

	function init() {
		initFadeAnimations();
		initStaggerAnimations();
		initCounterAnimations();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
