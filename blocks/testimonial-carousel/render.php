<?php
/**
 * Blue Sage — Testimonial Carousel: render.php
 *
 * Crossfade carousel with dot navigation and optional autoplay.
 * JS is loaded only on pages containing this block (viewScript in block.json).
 * Fully accessible: keyboard navigation, ARIA roles, reduced-motion support.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package BlueSage
 * @author Ilyas Serter <hello@sagegrids.com>
 * @company SAGE GRIDS LTD <https://www.sagegrids.com>
 * @link https://www.iserter.com
 */

$eyebrow       = isset( $attributes['eyebrow'] )       ? sanitize_text_field( $attributes['eyebrow'] )  : '';
$heading       = isset( $attributes['heading'] )       ? sanitize_text_field( $attributes['heading'] )  : '';
$dark_bg       = isset( $attributes['darkBg'] )        ? (bool) $attributes['darkBg']                   : false;
$autoplay      = isset( $attributes['autoplay'] )      ? (bool) $attributes['autoplay']                 : true;
$autoplay_delay = isset( $attributes['autoplayDelay'] ) ? (int) $attributes['autoplayDelay']             : 5000;
$items         = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : [];

if ( empty( $items ) ) {
	return;
}

$section_class  = 'bs-testimonials';
$section_class .= $dark_bg ? ' bs-testimonials--dark' : '';

$wrapper_attributes = get_block_wrapper_attributes( [
	'class'            => $section_class,
	'data-autoplay'    => $autoplay ? 'true' : 'false',
	'data-delay'       => $autoplay_delay,
	'aria-roledescription' => 'carousel',
	'aria-label'       => __( 'Customer testimonials', 'blue-sage' ),
] );

// Large decorative quote mark SVG.
$quote_mark = '<svg class="bs-testimonial__mark" viewBox="0 0 48 36" fill="currentColor" aria-hidden="true"><path d="M0 36V22.5C0 9 7.5 2.25 22.5 0l3 5.25C14.25 7.5 10.5 11.625 10.5 18H18V36H0Zm27 0V22.5C27 9 34.5 2.25 49.5 0l3 5.25C41.25 7.5 37.5 11.625 37.5 18H45V36H27Z"/></svg>';
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<?php if ( $eyebrow || $heading ) : ?>
		<div class="bs-testimonials__header js-fade-up">
			<?php if ( $eyebrow ) : ?>
				<span class="bs-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $heading ) : ?>
				<h2 class="bs-testimonials__heading"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div
		class="bs-testimonials__track"
		role="list"
		aria-live="polite"
		aria-atomic="true"
	>
		<?php foreach ( $items as $index => $item ) :
			$quote   = isset( $item['quote'] )         ? wp_kses_post( $item['quote'] )                    : '';
			$name    = isset( $item['authorName'] )    ? sanitize_text_field( $item['authorName'] )        : '';
			$title   = isset( $item['authorTitle'] )   ? sanitize_text_field( $item['authorTitle'] )       : '';
			$company = isset( $item['authorCompany'] ) ? sanitize_text_field( $item['authorCompany'] )     : '';
			$avatar  = isset( $item['avatarUrl'] )     ? esc_url( $item['avatarUrl'] )                     : '';
			$logo    = isset( $item['logoUrl'] )       ? esc_url( $item['logoUrl'] )                       : '';
			$logo_alt = isset( $item['logoAlt'] )      ? sanitize_text_field( $item['logoAlt'] )           : $company;
			$is_active = ( 0 === $index );
			?>
			<div
				class="bs-testimonial<?php echo $is_active ? ' is-active' : ''; ?>"
				role="listitem"
				aria-label="<?php echo esc_attr( sprintf( __( 'Testimonial %d of %d', 'blue-sage' ), $index + 1, count( $items ) ) ); ?>"
			>
				<blockquote class="bs-testimonial__quote">

					<?php echo $quote_mark; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

					<p class="bs-testimonial__text"><?php echo wp_kses_post( $quote ); ?></p>

					<footer class="bs-testimonial__author">

						<?php if ( $avatar ) : ?>
							<img
								src="<?php echo $avatar; ?>"
								alt="<?php echo esc_attr( $name ); ?>"
								class="bs-testimonial__avatar"
								width="56"
								height="56"
								loading="lazy"
								decoding="async"
							>
						<?php else : ?>
							<div class="bs-testimonial__avatar bs-testimonial__avatar--initials" aria-hidden="true">
								<?php echo esc_html( mb_substr( $name, 0, 1 ) ); ?>
							</div>
						<?php endif; ?>

						<div class="bs-testimonial__info">
							<?php if ( $name ) : ?>
								<cite class="bs-testimonial__name"><?php echo esc_html( $name ); ?></cite>
							<?php endif; ?>
							<?php if ( $title || $company ) : ?>
								<span class="bs-testimonial__role">
									<?php echo esc_html( implode( ', ', array_filter( [ $title, $company ] ) ) ); ?>
								</span>
							<?php endif; ?>
						</div>

						<?php if ( $logo ) : ?>
							<img
								src="<?php echo $logo; ?>"
								alt="<?php echo esc_attr( $logo_alt ); ?>"
								class="bs-testimonial__logo"
								loading="lazy"
								decoding="async"
							>
						<?php endif; ?>

					</footer>
				</blockquote>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( count( $items ) > 1 ) : ?>
		<div class="bs-testimonials__dots" role="tablist" aria-label="<?php esc_attr_e( 'Testimonials navigation', 'blue-sage' ); ?>">
			<?php foreach ( $items as $index => $item ) :
				$name = isset( $item['authorName'] ) ? sanitize_text_field( $item['authorName'] ) : '';
				?>
				<button
					class="bs-testimonials__dot<?php echo ( 0 === $index ) ? ' is-active' : ''; ?>"
					role="tab"
					aria-selected="<?php echo ( 0 === $index ) ? 'true' : 'false'; ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'Go to testimonial by %s', 'blue-sage' ), $name ) ); ?>"
					data-index="<?php echo esc_attr( $index ); ?>"
				></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

</section>
