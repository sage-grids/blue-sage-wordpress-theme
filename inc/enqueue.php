<?php
/**
 * Blue Sage — Scripts & Styles
 *
 * Enqueues Google Fonts, the main stylesheet, navigation JS,
 * and scroll animation JS. Adds preconnect hints for performance.
 *
 * @package BlueSage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue front-end assets.
 */
function blue_sage_enqueue_assets(): void {
	// Google Fonts: Plus Jakarta Sans, Inter, JetBrains Mono.
	// Phase 5: replace with self-hosted WOFF2 files in assets/fonts/ for
	// better LCP and privacy. See theme.json fontFace stubs.
	wp_enqueue_style(
		'blue-sage-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=JetBrains+Mono:wght@400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
		[],
		null
	);

	// Main theme stylesheet.
	wp_enqueue_style(
		'blue-sage-style',
		get_stylesheet_uri(),
		[ 'blue-sage-fonts' ],
		BLUE_SAGE_VERSION
	);

	// Navigation: sticky header, active links, reading progress bar.
	wp_enqueue_script(
		'blue-sage-navigation',
		BLUE_SAGE_URI . '/assets/js/navigation.js',
		[],
		BLUE_SAGE_VERSION,
		[ 'strategy' => 'defer', 'in_footer' => true ]
	);

	// Scroll animations: IntersectionObserver fade/stagger/counter.
	wp_enqueue_script(
		'blue-sage-animations',
		BLUE_SAGE_URI . '/assets/js/scroll-animations.js',
		[],
		BLUE_SAGE_VERSION,
		[ 'strategy' => 'defer', 'in_footer' => true ]
	);

	// Comment reply threading script.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'blue_sage_enqueue_assets' );

/**
 * Preconnect to Google Fonts origins for faster DNS resolution.
 *
 * @param  array  $hints         Existing resource hints.
 * @param  string $relation_type The relation type (preconnect, dns-prefetch, etc.).
 * @return array
 */
function blue_sage_resource_hints( array $hints, string $relation_type ): array {
	if ( 'preconnect' === $relation_type ) {
		$hints[] = [ 'href' => 'https://fonts.googleapis.com', 'crossorigin' => 'anonymous' ];
		$hints[] = [ 'href' => 'https://fonts.gstatic.com',    'crossorigin' => 'anonymous' ];
	}

	return $hints;
}
add_filter( 'wp_resource_hints', 'blue_sage_resource_hints', 10, 2 );
