<?php
/**
 * Blue Sage — Performance Optimisations
 *
 * Removes unused WordPress head outputs, disables emojis,
 * and handles LCP image prioritisation.
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
 * Remove the WordPress version query string from enqueued asset URLs.
 * Allows CDN/proxy caches to hold assets longer.
 */
function blue_sage_remove_version_strings( string $src ): string {
	if ( strpos( $src, '?ver=' ) !== false ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}
add_filter( 'style_loader_src',  'blue_sage_remove_version_strings' );
add_filter( 'script_loader_src', 'blue_sage_remove_version_strings' );

/**
 * Remove unused WordPress head outputs.
 */
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

/**
 * Disable emoji scripts and styles — not used in this theme.
 */
function blue_sage_disable_emojis(): void {
	remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script'    );
	remove_action( 'wp_print_styles',     'print_emoji_styles'              );
	remove_action( 'admin_print_styles',  'print_emoji_styles'              );
	remove_filter( 'the_content_feed',    'wp_staticize_emoji'              );
	remove_filter( 'comment_text_rss',    'wp_staticize_emoji'              );
	remove_filter( 'wp_mail',             'wp_staticize_emoji_for_email'    );
}
add_action( 'init', 'blue_sage_disable_emojis' );

/**
 * Add fetchpriority="high" and loading="eager" to the first content image
 * on singular pages. WordPress 6.3+ does this for featured images automatically,
 * but block content images still need it.
 *
 * Runs after the_content filters so it catches block-rendered output.
 *
 * @param  string $content Post content HTML.
 * @return string
 */
function blue_sage_prioritise_lcp_image( string $content ): string {
	if ( is_admin() || ! is_singular() ) {
		return $content;
	}

	// Already handled — do not double-apply.
	if ( strpos( $content, 'fetchpriority' ) !== false ) {
		return $content;
	}

	// Swap loading="lazy" → loading="eager" and add fetchpriority on first <img>.
	$content = preg_replace(
		'/(<img\b[^>]*?)(\s+loading=["\']lazy["\'])([^>]*?>)/i',
		'$1 loading="eager" fetchpriority="high"$3',
		$content,
		1
	);

	return $content;
}
add_filter( 'the_content', 'blue_sage_prioritise_lcp_image', 20 );
