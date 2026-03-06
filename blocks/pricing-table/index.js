/**
 * Blue Sage — Pricing Table
 *
 * Switches displayed prices when the billing period toggle is clicked.
 * Loaded via viewScript — only runs on pages containing this block.
 *
 * Features:
 *   - Instant price swap (monthly ↔ annual) via data-monthly / data-annual
 *   - aria-pressed state kept in sync with active button
 *   - Keyboard accessible (toggle buttons are native <button> elements)
 */
( function () {
	'use strict';

	/**
	 * Initialise one pricing table instance.
	 *
	 * @param {HTMLElement} section  The <section class="bs-pricing"> element.
	 */
	function initPricingTable( section ) {
		const btns   = Array.from( section.querySelectorAll( '.bs-pricing__toggle-btn' ) );
		const prices = Array.from( section.querySelectorAll( '.js-price' ) );

		if ( ! btns.length || ! prices.length ) return;

		btns.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				const period = btn.dataset.period; // 'monthly' | 'annual'

				// Update button states.
				btns.forEach( function ( b ) {
					const active = b === btn;
					b.classList.toggle( 'is-active', active );
					b.setAttribute( 'aria-pressed', String( active ) );
				} );

				// Swap prices.
				prices.forEach( function ( el ) {
					const value = el.dataset[ period ];
					if ( value !== undefined ) {
						el.textContent = value;
					}
				} );

				// Store active period on section for potential external hooks.
				section.dataset.activePeriod = period;
			} );
		} );
	}

	function init() {
		document.querySelectorAll( '.bs-pricing' ).forEach( initPricingTable );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
