<?php
/**
 * Blue Sage — Block Pattern Registration
 *
 * Registers pattern categories and loads all pattern files from /patterns/.
 * Each pattern file calls register_block_pattern() independently.
 *
 * @package BlueSage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Blue Sage pattern categories in the block inserter.
 */
function blue_sage_register_pattern_categories(): void {
	$categories = [
		'blue-sage-heroes'       => __( 'Blue Sage: Heroes',       'blue-sage' ),
		'blue-sage-features'     => __( 'Blue Sage: Features',     'blue-sage' ),
		'blue-sage-social-proof' => __( 'Blue Sage: Social Proof', 'blue-sage' ),
		'blue-sage-cta'          => __( 'Blue Sage: CTA',          'blue-sage' ),
		'blue-sage-pricing'      => __( 'Blue Sage: Pricing',      'blue-sage' ),
		'blue-sage-team'         => __( 'Blue Sage: Team',         'blue-sage' ),
		'blue-sage-blog'         => __( 'Blue Sage: Blog',         'blue-sage' ),
	];

	foreach ( $categories as $slug => $label ) {
		register_block_pattern_category( $slug, [ 'label' => $label ] );
	}
}
add_action( 'init', 'blue_sage_register_pattern_categories' );

/**
 * Load and register all pattern files.
 */
function blue_sage_register_patterns(): void {
	$pattern_files = glob( BLUE_SAGE_DIR . '/patterns/*.php' );

	if ( ! $pattern_files ) {
		return;
	}

	foreach ( $pattern_files as $file ) {
		require_once $file;
	}
}
add_action( 'init', 'blue_sage_register_patterns' );
