<?php
/**
 * Blue Sage — Custom Block Registration
 *
 * Registers the Blue Sage block category and all custom blocks.
 * Each block directory contains block.json which declares assets,
 * attributes, and the render.php callback.
 *
 * @package BlueSage
 * @author Ilyas Serter <hello@sagegrids.com>
 * @company SAGE GRIDS LTD <https://www.sagegrids.com>
 * @link https://github.com/sage-grids/blue-sage-wordpress-theme
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
 * Registers a shared editor script first (for Site Editor / Gutenberg support),
 * then registers each block from its directory containing block.json.
 * WordPress reads block.json to discover style, editorScript, and render.php.
 */
function blue_sage_register_blocks(): void {
	// Shared editor script: registers all custom blocks for Gutenberg / Site Editor.
	// Each block uses ServerSideRender so the editor shows a live PHP-rendered preview.
	wp_register_script(
		'blue-sage-blocks-editor',
		BLUE_SAGE_URI . '/assets/js/blocks-editor.js',
		[ 'wp-blocks', 'wp-element', 'wp-server-side-render', 'wp-block-editor' ],
		BLUE_SAGE_VERSION,
		false
	);

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
