<?php
/**
 * Blue Sage — Scripts & Styles
 *
 * Enqueues the main stylesheet, navigation JS, and scroll animation JS.
 * Fonts are self-hosted via @font-face in style.css — no CDN requests.
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
 * Preload critical WOFF2 font files to reduce LCP.
 * Only the two highest-impact weights are preloaded.
 */
function blue_sage_preload_fonts(): void {
	$fonts = [
		'plus-jakarta-sans-700.woff2',
		'inter-400.woff2',
	];

	foreach ( $fonts as $font ) {
		echo '<link rel="preload" href="'
			. esc_url( BLUE_SAGE_URI . '/assets/fonts/' . $font )
			. '" as="font" type="font/woff2" crossorigin="anonymous">' . "\n";
	}
}
add_action( 'wp_head', 'blue_sage_preload_fonts', 1 );

/**
 * Enqueue front-end assets.
 */
function blue_sage_enqueue_assets(): void {
	// Main theme stylesheet (fonts are declared via @font-face inside style.css).
	wp_enqueue_style(
		'blue-sage-style',
		get_stylesheet_uri(),
		[],
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

	// Page entrance animation (in <head>, non-blocking).
	wp_enqueue_script(
		'blue-sage-page-entrance',
		BLUE_SAGE_URI . '/assets/js/page-entrance.js',
		[],
		BLUE_SAGE_VERSION,
		[ 'strategy' => 'defer', 'in_footer' => false ]
	);

	// Hero parallax.
	wp_enqueue_script(
		'blue-sage-parallax',
		BLUE_SAGE_URI . '/assets/js/parallax.js',
		[],
		BLUE_SAGE_VERSION,
		[ 'strategy' => 'defer', 'in_footer' => true ]
	);

	// Back to top.
	wp_enqueue_script(
		'blue-sage-back-to-top',
		BLUE_SAGE_URI . '/assets/js/back-to-top.js',
		[],
		BLUE_SAGE_VERSION,
		[ 'strategy' => 'defer', 'in_footer' => true ]
	);

	wp_localize_script(
		'blue-sage-back-to-top',
		'BlueSageL10n',
		[
			'backToTop' => __( 'Back to top', 'blue-sage' ),
		]
	);

	// Comment reply threading script.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'blue_sage_enqueue_assets' );

