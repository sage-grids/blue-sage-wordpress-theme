/**
 * Blue Sage — Navigation
 *
 * Handles:
 *  - Sticky header frosted-glass effect on scroll
 *  - Active navigation link detection via URL matching
 *  - Reading progress bar on single posts
 */
( function () {
	'use strict';

	var SCROLL_THRESHOLD = 20;
	var lastScrollY      = 0;
	var ticking          = false;

	// ----------------------------------------------------------------
	// Init
	// ----------------------------------------------------------------

	function init() {
		initStickyHeader();
		initActiveNavLinks();

		if (
			document.body.classList.contains( 'is-single-post' ) ||
			document.body.classList.contains( 'single' )
		) {
			initReadingProgress();
		}
	}

	// ----------------------------------------------------------------
	// Sticky header: toggle .is-scrolled for the frosted glass effect
	// ----------------------------------------------------------------

	function initStickyHeader() {
		var header = document.querySelector( '.site-header' );
		if ( ! header ) return;

		function updateHeader() {
			if ( lastScrollY > SCROLL_THRESHOLD ) {
				header.classList.add( 'is-scrolled' );
			} else {
				header.classList.remove( 'is-scrolled' );
			}
			ticking = false;
		}

		window.addEventListener( 'scroll', function () {
			lastScrollY = window.scrollY;
			if ( ! ticking ) {
				window.requestAnimationFrame( updateHeader );
				ticking = true;
			}
		}, { passive: true } );

		// Run once on load in case the page is already scrolled.
		lastScrollY = window.scrollY;
		updateHeader();
	}

	// ----------------------------------------------------------------
	// Active nav link detection — marks the current page link with
	// aria-current="page" for both CSS styling and screen readers.
	// ----------------------------------------------------------------

	function initActiveNavLinks() {
		var currentPath = window.location.pathname;
		var navLinks    = document.querySelectorAll( '.site-nav a[href]' );

		navLinks.forEach( function ( link ) {
			try {
				var linkPath = new URL( link.href, window.location.origin ).pathname;

				if ( linkPath === currentPath ) {
					link.setAttribute( 'aria-current', 'page' );
					var li = link.closest( 'li' );
					if ( li ) {
						li.classList.add( 'current-menu-item' );
					}
				}
			} catch ( e ) {
				// Skip invalid URLs.
			}
		} );
	}

	// ----------------------------------------------------------------
	// Reading progress bar — injected dynamically on single posts.
	// Uses a gradient from Rich Blue to Electric Indigo.
	// ----------------------------------------------------------------

	function initReadingProgress() {
		var bar = document.createElement( 'div' );
		bar.className = 'reading-progress';
		bar.setAttribute( 'role', 'progressbar' );
		bar.setAttribute( 'aria-label', 'Reading progress' );
		bar.setAttribute( 'aria-valuemin', '0' );
		bar.setAttribute( 'aria-valuemax', '100' );
		bar.setAttribute( 'aria-valuenow', '0' );
		document.body.prepend( bar );

		var progressTicking = false;

		window.addEventListener( 'scroll', function () {
			if ( ! progressTicking ) {
				window.requestAnimationFrame( function () {
					updateProgress( bar );
					progressTicking = false;
				} );
				progressTicking = true;
			}
		}, { passive: true } );
	}

	function updateProgress( bar ) {
		var scrollTop  = window.scrollY;
		var docHeight  = document.documentElement.scrollHeight - window.innerHeight;
		var progress   = docHeight > 0 ? Math.min( ( scrollTop / docHeight ) * 100, 100 ) : 0;

		bar.style.width = progress + '%';
		bar.setAttribute( 'aria-valuenow', Math.round( progress ) );
	}

	// ----------------------------------------------------------------
	// Boot
	// ----------------------------------------------------------------

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
