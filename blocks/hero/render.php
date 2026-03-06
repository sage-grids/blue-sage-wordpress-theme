<?php
/**
 * Blue Sage — Hero: render.php
 *
 * Three layout variants:
 *   centered  — Full-width dark section. Display-size heading, centered.
 *   split     — 7/5 grid. Text left, image right. White background.
 *   editorial — Oversized type, staircase indent. Editorial magazine aesthetic.
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

$layout           = isset( $attributes['layout'] )          ? sanitize_text_field( $attributes['layout'] )          : 'centered';
$eyebrow          = isset( $attributes['eyebrow'] )         ? sanitize_text_field( $attributes['eyebrow'] )         : '';
$heading          = isset( $attributes['heading'] )         ? sanitize_text_field( $attributes['heading'] )         : '';
$subheading       = isset( $attributes['subheading'] )      ? sanitize_text_field( $attributes['subheading'] )      : '';
$description      = isset( $attributes['description'] )     ? sanitize_text_field( $attributes['description'] )     : '';
$gradient_heading = isset( $attributes['gradientHeading'] ) ? (bool) $attributes['gradientHeading']                 : false;
$media_url        = isset( $attributes['mediaUrl'] )        ? esc_url( $attributes['mediaUrl'] )                    : '';
$media_alt        = isset( $attributes['mediaAlt'] )        ? sanitize_text_field( $attributes['mediaAlt'] )        : '';
$primary_cta      = isset( $attributes['primaryCta'] )  && is_array( $attributes['primaryCta'] )  ? $attributes['primaryCta']  : [];
$secondary_cta    = isset( $attributes['secondaryCta'] ) && is_array( $attributes['secondaryCta'] ) ? $attributes['secondaryCta'] : [];

// Sanitise CTAs.
$p_label  = isset( $primary_cta['label'] )   ? sanitize_text_field( $primary_cta['label'] )   : '';
$p_url    = isset( $primary_cta['url'] )     ? esc_url( $primary_cta['url'] )                 : '#';
$p_target = ! empty( $primary_cta['newTab'] ) ? ' target="_blank" rel="noopener noreferrer"'  : '';

$s_label  = isset( $secondary_cta['label'] )   ? sanitize_text_field( $secondary_cta['label'] )   : '';
$s_url    = isset( $secondary_cta['url'] )     ? esc_url( $secondary_cta['url'] )                 : '#';
$s_target = ! empty( $secondary_cta['newTab'] ) ? ' target="_blank" rel="noopener noreferrer"'    : '';

// Heading class — optional gradient treatment.
$heading_class = 'bs-hero__heading' . ( $gradient_heading ? ' bs-gradient-text' : '' );

$section_class = 'bs-hero bs-hero--' . sanitize_html_class( $layout );
$extra         = ( 'split' !== $layout ) ? [ 'data-parallax' => 'true' ] : [];
$wrapper_attributes = get_block_wrapper_attributes( array_merge(
	[ 'class' => $section_class ],
	$extra
) );

// LCP image priority for split layout on front page.
$is_lcp   = ( 'split' === $layout ) && is_front_page();
$img_load = $is_lcp ? 'eager' : 'lazy';
$img_fp   = $is_lcp ? ' fetchpriority="high"' : '';

// Helper: render CTA buttons.
$render_ctas = static function () use ( $layout, $p_label, $p_url, $p_target, $s_label, $s_url, $s_target ) {
	if ( ! $p_label && ! $s_label ) return;
	echo '<div class="bs-hero__ctas">';
	if ( $p_label ) {
		printf(
			'<a href="%s" class="bs-btn bs-btn--primary"%s>%s</a>',
			$p_url,
			$p_target, // phpcs:ignore
			esc_html( $p_label )
		);
	}
	if ( $s_label ) {
		$ghost_class = ( 'centered' === $layout ) ? 'bs-btn--ghost-light' : 'bs-btn--ghost';
		printf(
			'<a href="%s" class="bs-btn %s"%s>%s</a>',
			$s_url,
			esc_attr( $ghost_class ),
			$s_target, // phpcs:ignore
			esc_html( $s_label )
		);
	}
	echo '</div>';
};

do_action( 'blue_sage_before_hero', $attributes );
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<?php if ( 'centered' === $layout ) : ?>

		<div class="bs-hero__inner bs-hero__inner--centered">
			<?php if ( $eyebrow ) : ?>
				<span class="bs-eyebrow bs-hero__eyebrow js-fade-in"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<?php if ( $heading ) : ?>
				<h1 class="<?php echo esc_attr( $heading_class ); ?> js-fade-up">
					<?php echo esc_html( $heading ); ?>
				</h1>
			<?php endif; ?>

			<?php if ( $subheading ) : ?>
				<p class="bs-hero__subheading js-fade-up"><?php echo esc_html( $subheading ); ?></p>
			<?php endif; ?>

			<?php $render_ctas(); ?>
		</div>

	<?php elseif ( 'split' === $layout ) : ?>

		<div class="bs-hero__inner bs-hero__inner--split">

			<div class="bs-hero__text js-fade-up">
				<?php if ( $eyebrow ) : ?>
					<span class="bs-eyebrow bs-hero__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>
				<?php if ( $heading ) : ?>
					<h1 class="<?php echo esc_attr( $heading_class ); ?>"><?php echo esc_html( $heading ); ?></h1>
				<?php endif; ?>
				<?php if ( $subheading ) : ?>
					<p class="bs-hero__subheading"><?php echo esc_html( $subheading ); ?></p>
				<?php endif; ?>
				<?php $render_ctas(); ?>
			</div>

			<div class="bs-hero__media js-fade-up">
				<?php if ( $media_url ) : ?>
					<img
						class="bs-hero__img"
						src="<?php echo esc_url( $media_url ); ?>"
						alt="<?php echo esc_attr( $media_alt ); ?>"
						loading="<?php echo esc_attr( $img_load ); ?>"
						width="800"
						height="600"
						decoding="async"
						<?php echo $img_fp; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					>
				<?php else : ?>
					<div class="bs-hero__media-placeholder" aria-hidden="true"></div>
				<?php endif; ?>
			</div>

		</div>

	<?php elseif ( 'editorial' === $layout ) : ?>

		<div class="bs-hero__inner bs-hero__inner--editorial">

			<?php if ( $eyebrow ) : ?>
				<span class="bs-eyebrow bs-hero__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<?php if ( $heading ) : ?>
				<h1 class="bs-hero__heading bs-hero__heading--editorial">
					<span class="bs-hero__editorial-line1"><?php echo esc_html( $heading ); ?></span>
					<?php if ( $subheading ) : ?>
						<span class="bs-hero__editorial-line2"><?php echo esc_html( $subheading ); ?></span>
					<?php endif; ?>
				</h1>
			<?php endif; ?>

			<div class="bs-hero__editorial-footer">
				<hr class="bs-hero__editorial-rule" aria-hidden="true">
				<?php if ( $description ) : ?>
					<p class="bs-hero__editorial-desc"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
				<?php if ( $p_label ) : ?>
					<a href="<?php echo $p_url; ?>" class="bs-btn bs-btn--primary bs-hero__editorial-cta"<?php echo $p_target; // phpcs:ignore ?>>
						<?php echo esc_html( $p_label ); ?>
					</a>
				<?php endif; ?>
			</div>

		</div>

	<?php endif; ?>

</section>
<?php
do_action( 'blue_sage_after_hero', $attributes );
?>
