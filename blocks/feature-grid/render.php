<?php
/**
 * Blue Sage — Feature Grid: render.php
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package BlueSage
 */

$layout     = isset( $attributes['layout'] )     ? sanitize_text_field( $attributes['layout'] )     : 'icon-grid';
$eyebrow    = isset( $attributes['eyebrow'] )    ? sanitize_text_field( $attributes['eyebrow'] )    : '';
$heading    = isset( $attributes['heading'] )    ? sanitize_text_field( $attributes['heading'] )    : '';
$subheading = isset( $attributes['subheading'] ) ? sanitize_text_field( $attributes['subheading'] ) : '';
$columns    = isset( $attributes['columns'] )    ? (int) $attributes['columns']                      : 3;
$alt_bg     = isset( $attributes['altBg'] )      ? (bool) $attributes['altBg']                      : false;
$items      = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : [];

// Icon SVG library — hardcoded to prevent XSS via user input.
$icons = [
	'lightning' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>',
	'shield'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
	'chart'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg>',
	'puzzle'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>',
	'headset'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3z"/><path d="M3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg>',
	'sliders'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>',
	'star'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
	'globe'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
	'check'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>',
	'zap'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
];

// Helper: render icon safely.
$get_icon = function( string $key ) use ( $icons ): string {
	return isset( $icons[ $key ] ) ? $icons[ $key ] : $icons['star'];
};

$section_class  = 'bs-features bs-features--' . sanitize_html_class( $layout );
$section_class .= $alt_bg ? ' bs-features--alt-bg' : '';
if ( 'icon-grid' === $layout ) {
	$section_class .= ' bs-features--cols-' . $columns;
}

$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => $section_class ] );
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="bs-features__inner">

		<?php if ( $eyebrow || $heading || $subheading ) : ?>
			<div class="bs-features__header js-fade-up">
				<?php if ( $eyebrow ) : ?>
					<span class="bs-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>
				<?php if ( $heading ) : ?>
					<h2 class="bs-features__heading"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
				<?php if ( $subheading ) : ?>
					<p class="bs-features__subheading"><?php echo esc_html( $subheading ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( 'icon-grid' === $layout ) : ?>

			<div class="bs-features__grid js-stagger">
				<?php foreach ( $items as $item ) :
					$icon_key = isset( $item['icon'] ) ? sanitize_key( $item['icon'] ) : 'star';
					$title    = isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : '';
					$body     = isset( $item['body'] )  ? sanitize_text_field( $item['body'] )  : '';
					?>
					<div class="bs-features__item">
						<div class="bs-features__item-icon" aria-hidden="true">
							<?php echo $get_icon( $icon_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<?php if ( $title ) : ?>
							<h3 class="bs-features__item-title"><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>
						<?php if ( $body ) : ?>
							<p class="bs-features__item-body"><?php echo esc_html( $body ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

		<?php elseif ( 'alternating' === $layout ) : ?>

			<div class="bs-features__rows">
				<?php foreach ( $items as $index => $item ) :
					$icon_key  = isset( $item['icon'] )      ? sanitize_key( $item['icon'] )                   : 'star';
					$title     = isset( $item['title'] )     ? sanitize_text_field( $item['title'] )            : '';
					$body      = isset( $item['body'] )      ? sanitize_text_field( $item['body'] )             : '';
					$eyebrow_i = isset( $item['eyebrow'] )   ? sanitize_text_field( $item['eyebrow'] )          : '';
					$img_url   = isset( $item['mediaUrl'] )  ? esc_url( $item['mediaUrl'] )                     : '';
					$img_alt   = isset( $item['mediaAlt'] )  ? sanitize_text_field( $item['mediaAlt'] )         : $title;
					$is_even   = ( 0 === $index % 2 );
					$row_class = 'bs-features__row js-fade-up' . ( $is_even ? '' : ' bs-features__row--reverse' );
					?>
					<div class="<?php echo esc_attr( $row_class ); ?>">

						<div class="bs-features__row-media">
							<?php if ( $img_url ) : ?>
								<img
									src="<?php echo $img_url; ?>"
									alt="<?php echo esc_attr( $img_alt ); ?>"
									loading="lazy"
									decoding="async"
									class="bs-features__row-image"
								>
							<?php else : ?>
								<div class="bs-features__row-placeholder" aria-hidden="true">
									<?php echo $get_icon( $icon_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="bs-features__row-text">
							<?php if ( $eyebrow_i ) : ?>
								<span class="bs-eyebrow"><?php echo esc_html( $eyebrow_i ); ?></span>
							<?php endif; ?>
							<?php if ( $title ) : ?>
								<h3 class="bs-features__item-title"><?php echo esc_html( $title ); ?></h3>
							<?php endif; ?>
							<?php if ( $body ) : ?>
								<p class="bs-features__item-body"><?php echo esc_html( $body ); ?></p>
							<?php endif; ?>
						</div>

					</div>
				<?php endforeach; ?>
			</div>

		<?php elseif ( 'large-cards' === $layout ) : ?>

			<div class="bs-features__cards js-stagger">
				<?php foreach ( array_slice( $items, 0, 4 ) as $index => $item ) :
					$icon_key  = isset( $item['icon'] )  ? sanitize_key( $item['icon'] )          : 'star';
					$title     = isset( $item['title'] ) ? sanitize_text_field( $item['title'] )  : '';
					$body      = isset( $item['body'] )  ? sanitize_text_field( $item['body'] )   : '';
					$is_blue   = ( 0 === $index % 2 );
					$card_class = 'bs-features__large-card' . ( $is_blue ? ' bs-features__large-card--blue' : '' );
					?>
					<div class="<?php echo esc_attr( $card_class ); ?>">
						<div class="bs-features__large-card-icon" aria-hidden="true">
							<?php echo $get_icon( $icon_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<?php if ( $title ) : ?>
							<h3 class="bs-features__item-title"><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>
						<?php if ( $body ) : ?>
							<p class="bs-features__item-body"><?php echo esc_html( $body ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

		<?php endif; ?>

	</div>
</section>
