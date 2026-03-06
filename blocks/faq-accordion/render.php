<?php
/**
 * Blue Sage — FAQ Accordion: render.php
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

$eyebrow     = isset( $attributes['eyebrow'] )    ? sanitize_text_field( $attributes['eyebrow'] )  : '';
$heading     = isset( $attributes['heading'] )    ? sanitize_text_field( $attributes['heading'] )  : '';
$allow_multi = isset( $attributes['allowMulti'] ) ? (bool) $attributes['allowMulti']               : false;
$items       = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : [];

$wrapper_attributes = get_block_wrapper_attributes( [
	'class'           => 'bs-faq',
	'data-allow-multi' => $allow_multi ? 'true' : 'false',
] );
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="bs-faq__inner">

		<?php if ( $eyebrow || $heading ) : ?>
		<div class="bs-faq__header">
			<?php if ( $eyebrow ) : ?>
				<span class="bs-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $heading ) : ?>
				<h2 class="bs-faq__heading"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<div class="bs-faq__list">
			<?php foreach ( $items as $index => $item ) :
				$question = isset( $item['question'] ) ? sanitize_text_field( $item['question'] ) : '';
				$answer   = isset( $item['answer'] )   ? wp_kses_post( $item['answer'] )           : '';
				$item_id  = 'faq-' . $index;
				?>
			<div class="bs-faq__item" data-open="false">
				<button
					class="bs-faq__trigger"
					aria-expanded="false"
					aria-controls="<?php echo esc_attr( $item_id . '-answer' ); ?>"
					id="<?php echo esc_attr( $item_id . '-trigger' ); ?>"
					type="button"
				>
					<span class="bs-faq__question"><?php echo esc_html( $question ); ?></span>
					<span class="bs-faq__icon" aria-hidden="true">
						<svg class="bs-faq__icon-plus" width="20" height="20" viewBox="0 0 20 20" fill="none" focusable="false">
							<path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
						</svg>
						<svg class="bs-faq__icon-close" width="20" height="20" viewBox="0 0 20 20" fill="none" focusable="false">
							<path d="M5 5l10 10M15 5L5 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
						</svg>
					</span>
				</button>
				<div
					class="bs-faq__answer"
					id="<?php echo esc_attr( $item_id . '-answer' ); ?>"
					role="region"
					aria-labelledby="<?php echo esc_attr( $item_id . '-trigger' ); ?>"
				>
					<div class="bs-faq__answer-inner">
						<?php echo wp_kses_post( $answer ); ?>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
