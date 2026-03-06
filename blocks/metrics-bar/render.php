<?php
/**
 * Blue Sage — Metrics Bar: render.php
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package BlueSage
 * @author Ilyas Serter <hello@sagegrids.com>
 * @company SAGE GRIDS LTD <https://www.sagegrids.com>
 * @link https://github.com/sage-grids/blue-sage-wordpress-theme
 */

$eyebrow = isset( $attributes['eyebrow'] ) ? sanitize_text_field( $attributes['eyebrow'] ) : '';
$dark_bg = isset( $attributes['darkBg'] ) ? (bool) $attributes['darkBg'] : true;
$items   = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : [];

$section_class = 'bs-metrics';
$section_class .= $dark_bg ? ' bs-metrics--dark' : ' bs-metrics--light';

$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => $section_class ] );
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="bs-metrics__inner">

		<?php if ( $eyebrow ) : ?>
			<span class="bs-eyebrow bs-metrics__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
		<?php endif; ?>

		<div class="bs-metrics__grid js-stagger" role="list">
				<?php foreach ( $items as $index => $item ) :
					// Support legacy "number" key used by existing pattern content.
					$value   = isset( $item['value'] )  ? sanitize_text_field( $item['value'] )  : '';
					if ( '' === $value && isset( $item['number'] ) ) {
						$value = sanitize_text_field( (string) $item['number'] );
					}
					$prefix  = isset( $item['prefix'] ) ? sanitize_text_field( $item['prefix'] ) : '';
					$suffix  = isset( $item['suffix'] ) ? sanitize_text_field( $item['suffix'] ) : '';
				$label   = isset( $item['label'] )  ? sanitize_text_field( $item['label'] )  : '';
				$numeric = (float) preg_replace( '/[^0-9.]/', '', $value );
				?>

				<?php if ( $index > 0 ) : ?>
					<div class="bs-metrics__divider" aria-hidden="true"></div>
				<?php endif; ?>

				<div class="bs-metrics__item" role="listitem">
					<span
						class="bs-metrics__number js-counter"
						data-target="<?php echo esc_attr( $numeric ); ?>"
						data-prefix="<?php echo esc_attr( $prefix ); ?>"
						data-suffix="<?php echo esc_attr( $suffix ); ?>"
						aria-label="<?php echo esc_attr( $prefix . $value . $suffix . ' ' . $label ); ?>"
					><?php echo esc_html( $prefix . $value . $suffix ); ?></span>
					<?php if ( $label ) : ?>
						<span class="bs-metrics__label"><?php echo esc_html( $label ); ?></span>
					<?php endif; ?>
				</div>

			<?php endforeach; ?>
		</div>

	</div>
</section>
