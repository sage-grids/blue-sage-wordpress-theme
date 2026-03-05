<?php
/**
 * Blue Sage — Theme Setup
 *
 * Registers WordPress feature support, image sizes, menus,
 * and custom block styles.
 *
 * @package BlueSage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets up theme defaults and registers support for WordPress features.
 */
function blue_sage_setup(): void {
	// Make the theme available for translation.
	load_theme_textdomain( 'blue-sage', BLUE_SAGE_DIR . '/languages' );

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title tag.
	add_theme_support( 'title-tag' );

	// Enable featured images on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Register custom image sizes.
	add_image_size( 'blue-sage-card',   800, 450, true  );
	add_image_size( 'blue-sage-hero',  1920, 960, false );
	add_image_size( 'blue-sage-thumb',  600, 400, true  );
	add_image_size( 'blue-sage-avatar',  96,  96, true  );

	// Use HTML5 markup throughout.
	add_theme_support( 'html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	] );

	// Load editor styles so the block editor matches the front end.
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	// Wide and full alignment for blocks.
	add_theme_support( 'align-wide' );

	// Responsive embeds (YouTube, Vimeo, etc.).
	add_theme_support( 'responsive-embeds' );

	// Allow themes to override block editor color palette from theme.json.
	add_theme_support( 'wp-block-styles' );

	// Custom logo configuration.
	add_theme_support( 'custom-logo', [
		'height'      => 48,
		'width'       => 160,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => [ 'site-title', 'site-description' ],
	] );

	// Register navigation menus (classic fallback / plugin compatibility).
	register_nav_menus( [
		'primary' => __( 'Primary Navigation', 'blue-sage' ),
		'footer'  => __( 'Footer Navigation',  'blue-sage' ),
	] );
}
add_action( 'after_setup_theme', 'blue_sage_setup' );

/**
 * Set the content width in pixels for embedded content.
 */
function blue_sage_content_width(): void {
	$GLOBALS['content_width'] = 800;
}
add_action( 'after_setup_theme', 'blue_sage_content_width', 0 );

/**
 * Register custom block styles.
 */
function blue_sage_register_block_styles(): void {
	// Ghost button — transparent with neutral border.
	register_block_style( 'core/button', [
		'name'  => 'ghost',
		'label' => __( 'Ghost', 'blue-sage' ),
	] );
}
add_action( 'init', 'blue_sage_register_block_styles' );

/**
 * Add contextual body classes.
 *
 * @param  string[] $classes Existing body classes.
 * @return string[]
 */
function blue_sage_body_classes( array $classes ): array {
	if ( is_singular() ) {
		$classes[] = 'is-singular';
	}

	if ( is_front_page() ) {
		$classes[] = 'is-front-page';
	}

	if ( is_single() ) {
		$classes[] = 'is-single-post';
	}

	return $classes;
}
add_filter( 'body_class', 'blue_sage_body_classes' );
