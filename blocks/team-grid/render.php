<?php
/**
 * Blue Sage — Team Grid: render.php
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

$eyebrow     = isset( $attributes['eyebrow'] )     ? sanitize_text_field( $attributes['eyebrow'] )  : '';
$heading     = isset( $attributes['heading'] )     ? sanitize_text_field( $attributes['heading'] )  : '';
$photo_shape = isset( $attributes['photoShape'] )  ? sanitize_key( $attributes['photoShape'] )      : 'square';
$columns     = isset( $attributes['columns'] )     ? absint( $attributes['columns'] )               : 4;
$members     = isset( $attributes['members'] ) && is_array( $attributes['members'] ) ? $attributes['members'] : [];

$columns     = in_array( $columns, [ 3, 4 ], true ) ? $columns : 4;
$photo_shape = in_array( $photo_shape, [ 'square', 'circle' ], true ) ? $photo_shape : 'square';

$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => 'bs-team' ] );

// LinkedIn SVG icon.
$linkedin_svg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>';

// Twitter/X SVG icon.
$twitter_svg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.26 5.632 5.904-5.632zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>';
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="bs-team__inner">

		<?php if ( $eyebrow || $heading ) : ?>
		<div class="bs-team__header">
			<?php if ( $eyebrow ) : ?>
				<span class="bs-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $heading ) : ?>
				<h2 class="bs-team__heading"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<div class="bs-team__grid bs-team__grid--<?php echo esc_attr( $columns ); ?>col bs-team__grid--<?php echo esc_attr( $photo_shape ); ?> js-stagger">
			<?php foreach ( $members as $member ) :
				$name      = isset( $member['name'] )     ? sanitize_text_field( $member['name'] )     : '';
				$title     = isset( $member['title'] )    ? sanitize_text_field( $member['title'] )    : '';
				$bio       = isset( $member['bio'] )      ? sanitize_text_field( $member['bio'] )      : '';
				$photo_url = isset( $member['photoUrl'] ) ? esc_url( $member['photoUrl'] )             : '';
				$photo_alt = isset( $member['photoAlt'] ) ? sanitize_text_field( $member['photoAlt'] ) : esc_attr( $name );
				$linkedin  = isset( $member['linkedin'] ) ? esc_url( $member['linkedin'] )             : '';
				$twitter   = isset( $member['twitter'] )  ? esc_url( $member['twitter'] )              : '';
				?>
			<div class="bs-team__card js-fade-up">
				<div class="bs-team__photo-wrap">
					<?php if ( $photo_url ) : ?>
						<img
							class="bs-team__photo"
							src="<?php echo $photo_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
							alt="<?php echo esc_attr( $photo_alt ); ?>"
							width="280"
							height="280"
							loading="lazy"
						>
					<?php else : ?>
						<div class="bs-team__photo bs-team__photo--placeholder" aria-hidden="true">
							<svg viewBox="0 0 280 280" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect width="280" height="280" fill="#F3F4F6"/>
								<circle cx="140" cy="110" r="40" fill="#D1D5DB"/>
								<ellipse cx="140" cy="210" rx="70" ry="50" fill="#D1D5DB"/>
							</svg>
						</div>
					<?php endif; ?>

					<?php if ( $bio || $linkedin || $twitter ) : ?>
					<div class="bs-team__overlay" aria-hidden="true">
						<?php if ( $bio ) : ?>
							<p class="bs-team__bio"><?php echo esc_html( $bio ); ?></p>
						<?php endif; ?>
						<?php if ( $linkedin || $twitter ) : ?>
						<div class="bs-team__socials">
							<?php if ( $linkedin ) : ?>
								<a href="<?php echo $linkedin; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
								   class="bs-team__social-link"
								   target="_blank"
								   rel="noopener noreferrer"
								   aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person name */ __( '%s on LinkedIn', 'blue-sage' ), $name ) ); ?>">
									<?php echo $linkedin_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</a>
							<?php endif; ?>
							<?php if ( $twitter ) : ?>
								<a href="<?php echo $twitter; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
								   class="bs-team__social-link"
								   target="_blank"
								   rel="noopener noreferrer"
								   aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person name */ __( '%s on X (Twitter)', 'blue-sage' ), $name ) ); ?>">
									<?php echo $twitter_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</a>
							<?php endif; ?>
						</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>

				<h3 class="bs-team__name"><?php echo esc_html( $name ); ?></h3>
				<?php if ( $title ) : ?>
					<p class="bs-team__title"><?php echo esc_html( $title ); ?></p>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
