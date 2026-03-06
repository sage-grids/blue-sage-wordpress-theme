<?php
/**
 * Blue Sage — Logo Wall: render.php
 *
 * Renders client/partner logos with an optional CSS marquee animation.
 * The logo list is duplicated to create a seamless infinite scroll loop.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package BlueSage
 */

$eyebrow  = isset( $attributes['eyebrow'] )  ? sanitize_text_field( $attributes['eyebrow'] )  : '';
$marquee  = isset( $attributes['marquee'] )  ? (bool) $attributes['marquee']                   : true;
$speed    = isset( $attributes['speed'] )    ? absint( $attributes['speed'] )                  : 40;
$colorize = isset( $attributes['colorize'] ) ? (bool) $attributes['colorize']                  : false;
$logos    = isset( $attributes['logos'] ) && is_array( $attributes['logos'] ) ? $attributes['logos'] : [];

// Clamp speed to a reasonable range.
$speed = max( 10, min( 120, $speed ) );

$section_class = 'bs-logos';
if ( $colorize ) {
	$section_class .= ' bs-logos--colorize';
}

$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => $section_class ] );

/**
 * Output a single set of logo items.
 *
 * @param array $logos     Logo attribute objects.
 * @param bool  $aria_hidden Whether to mark this set as aria-hidden (duplicate set).
 */
function blue_sage_render_logo_set( array $logos, bool $aria_hidden = false ): void {
	$hidden_attr = $aria_hidden ? ' aria-hidden="true"' : '';
	echo '<div class="bs-logos__set"' . $hidden_attr . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	foreach ( $logos as $logo ) {
		$media_url = isset( $logo['mediaUrl'] ) ? esc_url( $logo['mediaUrl'] )             : '';
		$media_alt = isset( $logo['mediaAlt'] ) ? sanitize_text_field( $logo['mediaAlt'] ) : '';
		$link_url  = isset( $logo['linkUrl'] )  ? esc_url( $logo['linkUrl'] )              : '';

		if ( ! $media_url ) {
			continue;
		}

		echo '<div class="bs-logos__item">';
		if ( $link_url ) {
			echo '<a href="' . $link_url . '" class="bs-logos__link" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $media_alt ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '<img class="bs-logos__img" src="' . $media_url . '" alt="' . esc_attr( $media_alt ) . '" loading="lazy" height="40" width="140">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( $link_url ) {
			echo '</a>';
		}
		echo '</div>';
	}
	echo '</div>';
}
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="bs-logos__inner">

		<?php if ( $eyebrow ) : ?>
			<p class="bs-eyebrow bs-logos__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>

		<?php if ( $logos ) : ?>
		<div class="bs-logos__track-wrap">
			<div class="bs-logos__track<?php echo $marquee ? ' bs-logos__track--marquee' : ''; ?>"
				 style="<?php echo $marquee ? '--bs-marquee-speed: ' . esc_attr( $speed ) . 's' : ''; ?>"
				 <?php echo $marquee ? 'aria-label="' . esc_attr__( 'Partner logos', 'blue-sage' ) . '"' : ''; ?>>
				<?php blue_sage_render_logo_set( $logos, false ); ?>
				<?php if ( $marquee ) : ?>
					<?php blue_sage_render_logo_set( $logos, true ); ?>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>

	</div>
</section>
