/**
 * Blue Sage — Back to Top
 *
 * Injects a fixed scroll-to-top button that appears after 400px of scroll.
 * Smooth scrolls to top on click. Respects prefers-reduced-motion.
 */
( function () {
	'use strict';

	var SHOW_AT = 400;
	var reduced  = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var ticking  = false;

	function createButton() {
		var btn = document.createElement( 'button' );
		btn.className = 'bs-back-to-top';
		btn.type      = 'button';
		btn.setAttribute(
			'aria-label',
			( window.BlueSageL10n && window.BlueSageL10n.backToTop )
				? window.BlueSageL10n.backToTop
				: 'Back to top'
		);
		btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" '
			+ 'aria-hidden="true" focusable="false">'
			+ '<path d="M10 15V5M5 10l5-5 5 5" stroke="currentColor" '
			+ 'stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>'
			+ '</svg>';

		btn.addEventListener( 'click', function () {
			window.scrollTo( { top: 0, behavior: reduced ? 'auto' : 'smooth' } );
		} );

		document.body.appendChild( btn );
		return btn;
	}

	function init() {
		var btn = createButton();

		window.addEventListener( 'scroll', function () {
			if ( ! ticking ) {
				window.requestAnimationFrame( function () {
					btn.classList.toggle( 'is-visible', window.scrollY > SHOW_AT );
					ticking = false;
				} );
				ticking = true;
			}
		}, { passive: true } );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
