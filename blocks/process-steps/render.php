<?php
/**
 * Blue Sage — Process / Steps: render.php
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

$layout     = isset( $attributes['layout'] )     ? sanitize_key( $attributes['layout'] )          : 'numbered';
$eyebrow    = isset( $attributes['eyebrow'] )    ? sanitize_text_field( $attributes['eyebrow'] )   : '';
$heading    = isset( $attributes['heading'] )    ? sanitize_text_field( $attributes['heading'] )   : '';
$subheading = isset( $attributes['subheading'] ) ? sanitize_text_field( $attributes['subheading'] ): '';
$items      = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : [];

$section_class = 'bs-steps bs-steps--' . ( 'timeline' === $layout ? 'timeline' : 'numbered' );
$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => $section_class ] );
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="bs-steps__inner">

		<?php if ( $eyebrow || $heading || $subheading ) : ?>
		<div class="bs-steps__header">
			<?php if ( $eyebrow ) : ?>
				<span class="bs-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $heading ) : ?>
				<h2 class="bs-steps__heading"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $subheading ) : ?>
				<p class="bs-steps__subheading"><?php echo esc_html( $subheading ); ?></p>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<?php if ( 'numbered' === $layout ) : ?>
		<ol class="bs-steps__track js-stagger" aria-label="<?php esc_attr_e( 'Process steps', 'blue-sage' ); ?>">
			<?php foreach ( $items as $index => $item ) :
				$number = isset( $item['number'] ) ? sanitize_text_field( $item['number'] ) : (string) ( $index + 1 );
				$title  = isset( $item['title'] )  ? sanitize_text_field( $item['title'] )  : '';
				$body   = isset( $item['body'] )   ? sanitize_text_field( $item['body'] )   : '';
				?>
			<li class="bs-steps__item js-fade-up">
				<div class="bs-steps__item-inner">
					<span class="bs-steps__number" aria-hidden="true"><?php echo esc_html( $number ); ?></span>
					<h3 class="bs-steps__title"><?php echo esc_html( $title ); ?></h3>
					<?php if ( $body ) : ?>
						<p class="bs-steps__body"><?php echo esc_html( $body ); ?></p>
					<?php endif; ?>
				</div>
				<?php if ( $index < count( $items ) - 1 ) : ?>
					<div class="bs-steps__connector" aria-hidden="true"></div>
				<?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ol>

		<?php else : ?>

		<div class="bs-steps__timeline" role="list">
			<div class="bs-timeline__line" aria-hidden="true"></div>
			<?php foreach ( $items as $index => $item ) :
				$number = isset( $item['number'] ) ? sanitize_text_field( $item['number'] ) : (string) ( $index + 1 );
				$title  = isset( $item['title'] )  ? sanitize_text_field( $item['title'] )  : '';
				$body   = isset( $item['body'] )   ? sanitize_text_field( $item['body'] )   : '';
				$side   = 0 === $index % 2 ? 'left' : 'right';
				?>
			<div class="bs-timeline__item bs-timeline__item--<?php echo esc_attr( $side ); ?> js-fade-up" role="listitem">
				<div class="bs-timeline__dot js-fade-in" aria-hidden="true"></div>
				<div class="bs-timeline__card">
					<?php if ( $number ) : ?>
						<span class="bs-timeline__label bs-eyebrow"><?php echo esc_html( $number ); ?></span>
					<?php endif; ?>
					<h3 class="bs-timeline__title"><?php echo esc_html( $title ); ?></h3>
					<?php if ( $body ) : ?>
						<p class="bs-timeline__body"><?php echo esc_html( $body ); ?></p>
					<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>

		<?php endif; ?>

	</div>
</section>
