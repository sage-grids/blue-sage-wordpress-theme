<?php
/**
 * Blue Sage — Block Pattern Registration
 *
 * Registers pattern categories for theme patterns in /patterns/.
 * Pattern files are auto-discovered by WordPress core.
 *
 * @package BlueSage
 * @author Ilyas Serter <hello@sagegrids.com>
 * @company SAGE GRIDS LTD <https://www.sagegrids.com>
 * @link https://www.iserter.com
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
