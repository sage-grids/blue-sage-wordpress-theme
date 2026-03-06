<?php
/**
 * Blue Sage — Custom Block Registration
 *
 * Registers the Blue Sage block category and all custom blocks.
 * Each block directory contains block.json which declares assets,
 * attributes, and the render.php callback.
 *
 * @package BlueSage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add a "Blue Sage" category to the block inserter.
 *
 * @param  array $categories Existing block categories.
 * @return array
 */
function blue_sage_block_category( array $categories ): array {
	return array_merge(
		[
			[
				'slug'  => 'blue-sage',
				'title' => __( 'Blue Sage', 'blue-sage' ),
				'icon'  => 'star-filled',
			],
		],
		$categories
	);
}
add_filter( 'block_categories_all', 'blue_sage_block_category', 10, 1 );

/**
 * Register all custom blocks.
 *
 * Each entry maps to a directory under /blocks/ containing block.json.
 * WordPress reads block.json to discover style, viewScript, and render.php.
 */
function blue_sage_register_blocks(): void {
	$blocks = [
		// Phase 2
		'metrics-bar',
		'cta-section',
		'feature-grid',
		'hero',
		'testimonial-carousel',
		// Phase 3
		'pricing-table',
		'faq-accordion',
		'process-steps',
		'team-grid',
		'blog-cards',
		'logo-wall',
	];

	foreach ( $blocks as $block ) {
		$path = BLUE_SAGE_DIR . '/blocks/' . $block;

		if ( is_dir( $path ) ) {
			register_block_type( $path );
		}
	}
}
add_action( 'init', 'blue_sage_register_blocks' );
