/**
 * Blue Sage — Hero Parallax
 *
 * Moves hero background images at 10% of scroll speed.
 * Only runs on elements with data-parallax="true".
 * Disabled for prefers-reduced-motion.
 * Uses passive scroll listener + requestAnimationFrame for performance.
 */
( function () {
	'use strict';

	if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	var heroes  = [];
	var ticking = false;

	function collectHeroes() {
		heroes = Array.from( document.querySelectorAll( '.bs-hero[data-parallax]' ) );
	}

	function updateParallax() {
		var scrollY = window.scrollY;

		heroes.forEach( function ( hero ) {
			var rect = hero.getBoundingClientRect();
			// Only calculate for heroes near or in the viewport.
			if ( rect.bottom < 0 || rect.top > window.innerHeight ) return;
			var offset = ( rect.top + scrollY ) * 0.10;
			hero.style.backgroundPositionY = ( -offset ) + 'px';
		} );

		ticking = false;
	}

	window.addEventListener( 'scroll', function () {
		if ( ! ticking ) {
			window.requestAnimationFrame( updateParallax );
			ticking = true;
		}
	}, { passive: true } );

	function init() {
		collectHeroes();
		if ( heroes.length ) {
			updateParallax();
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
