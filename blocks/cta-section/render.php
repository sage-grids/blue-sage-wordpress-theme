<?php
/**
 * Blue Sage — CTA Section: render.php
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

$layout        = isset( $attributes['layout'] )      ? sanitize_text_field( $attributes['layout'] )  : 'full-dark';
$eyebrow       = isset( $attributes['eyebrow'] )     ? sanitize_text_field( $attributes['eyebrow'] ) : '';
$heading       = isset( $attributes['heading'] )     ? sanitize_text_field( $attributes['heading'] ) : '';
$subtext       = isset( $attributes['subtext'] )     ? sanitize_text_field( $attributes['subtext'] ) : '';
$glow_effect   = isset( $attributes['glowEffect'] )  ? (bool) $attributes['glowEffect']              : true;
$primary_cta   = isset( $attributes['primaryCta'] )  && is_array( $attributes['primaryCta'] )  ? $attributes['primaryCta']  : [];
$secondary_cta = isset( $attributes['secondaryCta'] ) && is_array( $attributes['secondaryCta'] ) ? $attributes['secondaryCta'] : [];

// Sanitise CTA data.
$p_label  = isset( $primary_cta['label'] )  ? sanitize_text_field( $primary_cta['label'] )  : '';
$p_url    = isset( $primary_cta['url'] )    ? esc_url( $primary_cta['url'] )                : '#';
$p_target = ! empty( $primary_cta['newTab'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';

$s_label  = isset( $secondary_cta['label'] )  ? sanitize_text_field( $secondary_cta['label'] )  : '';
$s_url    = isset( $secondary_cta['url'] )    ? esc_url( $secondary_cta['url'] )                : '#';
$s_target = ! empty( $secondary_cta['newTab'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';

$section_class  = 'bs-cta bs-cta--' . sanitize_html_class( $layout );
$section_class .= ( 'full-dark' === $layout && $glow_effect ) ? ' bs-cta--glow' : '';

$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => $section_class ] );
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<?php if ( 'full-dark' === $layout ) : ?>

		<div class="bs-cta__inner">

			<?php if ( $eyebrow ) : ?>
				<span class="bs-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<?php if ( $heading ) : ?>
				<h2 class="bs-cta__heading"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>

			<?php if ( $subtext ) : ?>
				<p class="bs-cta__subtext"><?php echo esc_html( $subtext ); ?></p>
			<?php endif; ?>

			<?php if ( $p_label || $s_label ) : ?>
				<div class="bs-cta__actions">
					<?php if ( $p_label ) : ?>
						<a href="<?php echo $p_url; ?>" class="bs-btn bs-btn--primary"<?php echo $p_target; // phpcs:ignore ?>>
							<?php echo esc_html( $p_label ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $s_label ) : ?>
						<a href="<?php echo $s_url; ?>" class="bs-btn bs-btn--ghost-light"<?php echo $s_target; // phpcs:ignore ?>>
							<?php echo esc_html( $s_label ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>

	<?php else : // inline-box ?>

		<div class="bs-cta__container">
			<div class="bs-cta__box">

				<div class="bs-cta__box-text">
					<?php if ( $eyebrow ) : ?>
						<span class="bs-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
					<?php endif; ?>
					<?php if ( $heading ) : ?>
						<h3 class="bs-cta__heading"><?php echo esc_html( $heading ); ?></h3>
					<?php endif; ?>
					<?php if ( $subtext ) : ?>
						<p class="bs-cta__subtext"><?php echo esc_html( $subtext ); ?></p>
					<?php endif; ?>
				</div>

				<?php if ( $p_label || $s_label ) : ?>
					<div class="bs-cta__box-actions">
						<?php if ( $p_label ) : ?>
							<a href="<?php echo $p_url; ?>" class="bs-btn bs-btn--primary"<?php echo $p_target; // phpcs:ignore ?>>
								<?php echo esc_html( $p_label ); ?>
							</a>
						<?php endif; ?>
						<?php if ( $s_label ) : ?>
							<a href="<?php echo $s_url; ?>" class="bs-btn bs-btn--ghost"<?php echo $s_target; // phpcs:ignore ?>>
								<?php echo esc_html( $s_label ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

			</div>
		</div>

	<?php endif; ?>

</section>
