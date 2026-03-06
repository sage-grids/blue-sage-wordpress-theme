/* global wp */
/**
 * Blue Sage — Block Editor Registration
 *
 * Registers all custom Blue Sage blocks for the WordPress Site Editor / Gutenberg.
 * Each block renders a live server-side preview in the editor canvas.
 *
 * No build step required — uses wp.* globals provided by WordPress core.
 */
( function () {
	'use strict';

	var blockNames = [
		'blue-sage/hero',
		'blue-sage/feature-grid',
		'blue-sage/cta-section',
		'blue-sage/metrics-bar',
		'blue-sage/testimonial-carousel',
		'blue-sage/pricing-table',
		'blue-sage/faq-accordion',
		'blue-sage/process-steps',
		'blue-sage/team-grid',
		'blue-sage/blog-cards',
		'blue-sage/logo-wall',
	];

	blockNames.forEach( function ( blockName ) {
		wp.blocks.registerBlockType( blockName, {
			edit: function ( props ) {
				return wp.element.createElement(
					'div',
					wp.blockEditor.useBlockProps(),
					wp.element.createElement( wp.serverSideRender, {
						block:      blockName,
						attributes: props.attributes,
					} )
				);
			},
			save: function () {
				return null;
			},
		} );
	} );
}() );
