<?php
/**
 * Blue Sage — Pricing Table: render.php
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package BlueSage
 */

$eyebrow        = isset( $attributes['eyebrow'] )        ? sanitize_text_field( $attributes['eyebrow'] )        : '';
$heading        = isset( $attributes['heading'] )        ? sanitize_text_field( $attributes['heading'] )        : '';
$subheading     = isset( $attributes['subheading'] )     ? sanitize_text_field( $attributes['subheading'] )     : '';
$billing_toggle = isset( $attributes['billingToggle'] )  ? (bool) $attributes['billingToggle']                  : true;
$tiers          = isset( $attributes['tiers'] ) && is_array( $attributes['tiers'] ) ? $attributes['tiers']     : [];

$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => 'bs-pricing' ] );

// Inline checkmark SVG used per feature row.
$check_svg = '<svg class="bs-pricing__check" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false"><circle cx="8" cy="8" r="8" fill="#10B981" fill-opacity="0.12"/><path d="M5 8l2 2 4-4" stroke="#10B981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="bs-pricing__inner">

		<?php if ( $eyebrow || $heading || $subheading ) : ?>
		<div class="bs-pricing__header">
			<?php if ( $eyebrow ) : ?>
				<span class="bs-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $heading ) : ?>
				<h2 class="bs-pricing__heading"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $subheading ) : ?>
				<p class="bs-pricing__subheading"><?php echo esc_html( $subheading ); ?></p>
			<?php endif; ?>

			<?php if ( $billing_toggle ) : ?>
			<div class="bs-pricing__toggle" role="group" aria-label="<?php esc_attr_e( 'Billing period', 'blue-sage' ); ?>">
				<button class="bs-pricing__toggle-btn is-active" data-period="monthly" aria-pressed="true">
					<?php esc_html_e( 'Monthly', 'blue-sage' ); ?>
				</button>
				<button class="bs-pricing__toggle-btn" data-period="annual" aria-pressed="false">
					<?php esc_html_e( 'Annual', 'blue-sage' ); ?>
					<span class="bs-badge bs-badge--sage"><?php esc_html_e( 'Save 20%', 'blue-sage' ); ?></span>
				</button>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<div class="bs-pricing__grid">
			<?php foreach ( $tiers as $tier ) :
				$name          = isset( $tier['name'] )         ? sanitize_text_field( $tier['name'] )         : '';
				$badge         = isset( $tier['badge'] )        ? sanitize_text_field( $tier['badge'] )        : '';
				$monthly_price = isset( $tier['monthlyPrice'] ) ? sanitize_text_field( $tier['monthlyPrice'] ) : '0';
				$annual_price  = isset( $tier['annualPrice'] )  ? sanitize_text_field( $tier['annualPrice'] )  : '0';
				$currency      = isset( $tier['currency'] )     ? sanitize_text_field( $tier['currency'] )     : '$';
				$description   = isset( $tier['description'] )  ? sanitize_text_field( $tier['description'] )  : '';
				$features      = isset( $tier['features'] ) && is_array( $tier['features'] ) ? $tier['features'] : [];
				$cta_label     = isset( $tier['ctaLabel'] )     ? sanitize_text_field( $tier['ctaLabel'] )     : '';
				$cta_url       = isset( $tier['ctaUrl'] )       ? esc_url( $tier['ctaUrl'] )                   : '#';
				$is_popular    = ! empty( $tier['isPopular'] );

				$card_class = 'bs-pricing__tier js-fade-up';
				if ( $is_popular ) {
					$card_class .= ' bs-pricing__tier--popular';
				}
				?>
			<div class="<?php echo esc_attr( $card_class ); ?>">
				<?php if ( $badge ) : ?>
					<div class="bs-pricing__badge"><?php echo esc_html( $badge ); ?></div>
				<?php endif; ?>

				<div class="bs-pricing__tier-header">
					<h3 class="bs-pricing__tier-name"><?php echo esc_html( $name ); ?></h3>
					<div class="bs-pricing__price">
						<span class="bs-pricing__currency"><?php echo esc_html( $currency ); ?></span>
						<span class="bs-pricing__amount js-price"
							data-monthly="<?php echo esc_attr( $monthly_price ); ?>"
							data-annual="<?php echo esc_attr( $annual_price ); ?>"
						><?php echo esc_html( $monthly_price ); ?></span>
						<span class="bs-pricing__period"><?php esc_html_e( '/mo', 'blue-sage' ); ?></span>
					</div>
					<?php if ( $description ) : ?>
						<p class="bs-pricing__description"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>
				</div>

				<?php if ( $features ) : ?>
				<ul class="bs-pricing__features">
					<?php foreach ( $features as $feature ) : ?>
					<li class="bs-pricing__feature">
						<?php echo $check_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><?php echo esc_html( sanitize_text_field( $feature ) ); ?></span>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>

				<?php if ( $cta_label ) : ?>
				<a href="<?php echo $cta_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
				   class="bs-pricing__cta wp-block-button__link">
					<?php echo esc_html( $cta_label ); ?>
				</a>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
