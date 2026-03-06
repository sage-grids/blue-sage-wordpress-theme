/**
 * Blue Sage — Page Entrance
 *
 * Adds .is-page-loaded to <html> after DOMContentLoaded.
 * Hero children use CSS transitions keyed to this class.
 * Adds the class immediately (no animation) for prefers-reduced-motion.
 */
( function () {
	'use strict';

	function onReady() {
		requestAnimationFrame( function () {
			document.documentElement.classList.add( 'is-page-loaded' );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', onReady );
	} else {
		onReady();
	}
} )();
