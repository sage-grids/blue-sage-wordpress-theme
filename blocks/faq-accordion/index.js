/**
 * Blue Sage — FAQ Accordion
 *
 * Accessible expand/collapse accordion. Uses max-height animation
 * driven by the element's scrollHeight so no fixed pixel values needed.
 * Loaded via viewScript — only runs on pages containing this block.
 *
 * Features:
 *   - max-height transition for smooth open/close (CSS handles easing)
 *   - aria-expanded kept in sync
 *   - Single/multi open mode via data-allow-multi attribute
 *   - Keyboard: Space / Enter on trigger (native button)
 *   - Respects prefers-reduced-motion (disables transition, keeps toggle)
 */
( function () {
	'use strict';

	const prefersReducedMotion = window.matchMedia(
		'(prefers-reduced-motion: reduce)'
	).matches;

	/**
	 * Close a single FAQ item.
	 *
	 * @param {HTMLElement} item
	 */
	function closeItem( item ) {
		const answer  = item.querySelector( '.bs-faq__answer' );
		const trigger = item.querySelector( '.bs-faq__trigger' );

		if ( ! answer || ! trigger ) return;

		item.dataset.open = 'false';
		trigger.setAttribute( 'aria-expanded', 'false' );

		if ( prefersReducedMotion ) {
			answer.style.maxHeight = '0';
		} else {
			answer.style.maxHeight = answer.scrollHeight + 'px';
			// Force reflow before collapsing so transition fires.
			answer.offsetHeight; // eslint-disable-line no-unused-expressions
			answer.style.maxHeight = '0';
		}
	}

	/**
	 * Open a single FAQ item.
	 *
	 * @param {HTMLElement} item
	 */
	function openItem( item ) {
		const answer  = item.querySelector( '.bs-faq__answer' );
		const trigger = item.querySelector( '.bs-faq__trigger' );

		if ( ! answer || ! trigger ) return;

		item.dataset.open = 'true';
		trigger.setAttribute( 'aria-expanded', 'true' );
		answer.style.maxHeight = answer.scrollHeight + 'px';
	}

	/**
	 * Initialise a single FAQ section.
	 *
	 * @param {HTMLElement} section  The <section class="bs-faq"> element.
	 */
	function initAccordion( section ) {
		const allowMulti = section.dataset.allowMulti === 'true';
		const items = Array.from( section.querySelectorAll( '.bs-faq__item' ) );

		items.forEach( function ( item ) {
			const trigger = item.querySelector( '.bs-faq__trigger' );
			if ( ! trigger ) return;

			trigger.addEventListener( 'click', function () {
				const isOpen = item.dataset.open === 'true';

				if ( ! allowMulti ) {
					// Close all other items.
					items.forEach( function ( other ) {
						if ( other !== item && other.dataset.open === 'true' ) {
							closeItem( other );
						}
					} );
				}

				if ( isOpen ) {
					closeItem( item );
				} else {
					openItem( item );
				}
			} );
		} );
	}

	function init() {
		document.querySelectorAll( '.bs-faq' ).forEach( initAccordion );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
