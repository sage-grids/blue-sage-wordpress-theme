<?php
/**
 * Blue Sage Child — Functions
 *
 * Entry point for all child theme customizations.
 * The parent theme (Blue Sage) loads automatically by WordPress.
 *
 * Available filter/action hooks from the parent theme:
 *
 *   blue_sage_before_hero            Action  Fires before hero block render. Passes $attributes array.
 *   blue_sage_after_hero             Action  Fires after hero block render. Passes $attributes array.
 *   blue_sage_jsonld_organization    Filter  Modify the Organization JSON-LD array before output.
 *   blue_sage_robots_txt             Filter  Extend the robots.txt content (via WordPress core).
 *
 * @package BlueSageChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue child theme stylesheet.
 * The parent stylesheet is already enqueued by the parent theme.
 */
function blue_sage_child_enqueue_styles(): void {
	wp_enqueue_style(
		'blue-sage-child-style',
		get_stylesheet_uri(),
		[ 'blue-sage-style' ],
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'blue_sage_child_enqueue_styles' );

/*
 * -----------------------------------------------------------------
 * EXAMPLE CUSTOMIZATIONS — uncomment and adapt as needed
 * -----------------------------------------------------------------
 */

/*
 * Example: Override the default OG image for all pages.
 *
add_filter( 'theme_mod_blue_sage_og_image', function( string $url ): string {
	return 'https://example.com/my-og-image.jpg';
} );
*/

/*
 * Example: Add a postal address to the Organization JSON-LD schema.
 *
add_filter( 'blue_sage_jsonld_organization', function( array $schema ): array {
	$schema['address'] = [
		'@type'           => 'PostalAddress',
		'streetAddress'   => '123 Main Street',
		'addressLocality' => 'San Francisco',
		'addressRegion'   => 'CA',
		'postalCode'      => '94105',
		'addressCountry'  => 'US',
	];
	return $schema;
} );
*/

/*
 * Example: Inject content before every hero block.
 *
add_action( 'blue_sage_before_hero', function( array $attributes ): void {
	if ( 'centered' === ( $attributes['layout'] ?? '' ) ) {
		echo '<div class="my-announcement-bar">Free shipping on all orders</div>';
	}
} );
*/
