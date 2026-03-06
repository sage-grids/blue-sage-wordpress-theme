<?php
/**
 * Blue Sage — Template Helper Functions
 *
 * Reusable utilities for templates and template parts.
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
 * Output a reading time estimate for the current post.
 *
 * Uses a 200 words-per-minute reading speed.
 */
function blue_sage_reading_time(): void {
	$content    = get_post_field( 'post_content', get_the_ID() );
	$word_count = str_word_count( wp_strip_all_tags( $content ) );
	$minutes    = (int) ceil( $word_count / 200 );

	printf(
		/* translators: %d: estimated reading time in minutes */
		esc_html( _n( '%d min read', '%d min read', $minutes, 'blue-sage' ) ),
		$minutes
	);
}

/**
 * Return a formatted post date.
 *
 * @param  int|null $post_id Optional post ID. Defaults to current post.
 * @return string            Formatted date string.
 */
function blue_sage_post_date( ?int $post_id = null ): string {
	return esc_html( get_the_date( 'M j, Y', $post_id ) );
}

/**
 * Check whether the reading progress bar should be shown on this page.
 *
 * @return bool
 */
function blue_sage_show_reading_progress(): bool {
	return is_single() && ! is_admin();
}

/**
 * Output an inline SVG icon from assets/images/icons/.
 *
 * @param string  $name  Icon filename without the .svg extension.
 * @param array   $attrs Optional HTML attributes to add to the wrapper <span>.
 */
function blue_sage_icon( string $name, array $attrs = [] ): void {
	$file = BLUE_SAGE_DIR . '/assets/images/icons/' . sanitize_file_name( $name ) . '.svg';

	if ( ! file_exists( $file ) ) {
		return;
	}

	$defaults = [
		'aria-hidden' => 'true',
		'focusable'   => 'false',
		'class'       => 'bs-icon',
	];

	$attrs  = wp_parse_args( $attrs, $defaults );
	$output = '';

	foreach ( $attrs as $key => $value ) {
		$output .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
	}

	echo '<span' . $output . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	include $file; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</span>';
}

/**
 * Truncate a string to a given word count, appending an ellipsis.
 *
 * @param  string $text      The input string.
 * @param  int    $max_words Maximum number of words.
 * @param  string $more      Suffix appended when the text is truncated.
 * @return string
 */
function blue_sage_truncate( string $text, int $max_words = 20, string $more = '&hellip;' ): string {
	$words = explode( ' ', wp_strip_all_tags( $text ) );

	if ( count( $words ) <= $max_words ) {
		return esc_html( $text );
	}

	return esc_html( implode( ' ', array_slice( $words, 0, $max_words ) ) ) . $more;
}
