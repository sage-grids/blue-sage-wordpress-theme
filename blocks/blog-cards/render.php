<?php
/**
 * Blue Sage — Blog Cards: render.php
 *
 * Renders a dynamic post list in three layouts:
 * - standard: 3-column card grid with thumbnail, category, excerpt, meta
 * - featured:  single post in a large 60/40 split card
 * - list:      minimal rows with no images
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package BlueSage
 * @author Ilyas Serter <hello@sagegrids.com>
 * @company SAGE GRIDS LTD <https://www.sagegrids.com>
 * @link https://github.com/sage-grids/blue-sage-wordpress-theme
 * @author Ilyas Serter <hello@sagegrids.com>
 * @company SAGE GRIDS LTD <https://www.sagegrids.com>
 * @link https://github.com/sage-grids/blue-sage-wordpress-theme
 */

$layout        = isset( $attributes['layout'] )       ? sanitize_key( $attributes['layout'] )            : 'standard';
$eyebrow       = isset( $attributes['eyebrow'] )      ? sanitize_text_field( $attributes['eyebrow'] )     : '';
$heading       = isset( $attributes['heading'] )      ? sanitize_text_field( $attributes['heading'] )     : '';
$posts_per_page = isset( $attributes['postsPerPage'] ) ? absint( $attributes['postsPerPage'] )             : 3;
$category_id   = isset( $attributes['categoryId'] )   ? absint( $attributes['categoryId'] )               : 0;
$show_excerpt  = isset( $attributes['showExcerpt'] )  ? (bool) $attributes['showExcerpt']                 : true;
$show_read_time = isset( $attributes['showReadTime'] ) ? (bool) $attributes['showReadTime']               : true;
$show_author   = isset( $attributes['showAuthor'] )   ? (bool) $attributes['showAuthor']                  : true;
$cta_label     = isset( $attributes['ctaLabel'] )     ? sanitize_text_field( $attributes['ctaLabel'] )    : '';
$cta_url       = isset( $attributes['ctaUrl'] )       ? esc_url( $attributes['ctaUrl'] )                  : '';

$layout = in_array( $layout, [ 'standard', 'featured', 'list' ], true ) ? $layout : 'standard';

$query_args = [
	'post_type'      => 'post',
	'posts_per_page' => ( 'featured' === $layout ) ? 1 : $posts_per_page,
	'post_status'    => 'publish',
	'no_found_rows'  => true,
];

if ( $category_id > 0 ) {
	$query_args['cat'] = $category_id;
}

$posts = new WP_Query( $query_args );

$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => 'bs-blog bs-blog--' . $layout ] );
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="bs-blog__inner">

		<?php if ( $eyebrow || $heading ) : ?>
		<div class="bs-blog__header">
			<?php if ( $eyebrow ) : ?>
				<span class="bs-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $heading ) : ?>
				<h2 class="bs-blog__heading"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<?php if ( ! $posts->have_posts() ) : ?>
			<p class="bs-blog__empty"><?php esc_html_e( 'No posts found.', 'blue-sage' ); ?></p>
		<?php else : ?>

			<?php if ( 'featured' === $layout ) : ?>

			<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
			<article class="bs-blog__featured js-fade-up">
				<?php
				$thumb_id = get_post_thumbnail_id();
				if ( $thumb_id ) :
				?>
				<div class="bs-blog__featured-media">
					<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
						<?php echo wp_get_attachment_image( $thumb_id, 'large', false, [
							'class'   => 'bs-blog__featured-img',
							'loading' => 'lazy',
						] ); ?>
					</a>
				</div>
				<?php endif; ?>
				<div class="bs-blog__featured-content">
					<?php
					$categories = get_the_category();
					if ( $categories ) :
						$cat = $categories[0];
						?>
						<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
						   class="bs-blog__category"><?php echo esc_html( $cat->name ); ?></a>
					<?php endif; ?>
					<h2 class="bs-blog__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h2>
					<?php if ( $show_excerpt ) : ?>
						<p class="bs-blog__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
					<?php endif; ?>
					<div class="bs-blog__meta">
						<?php if ( $show_author ) : ?>
							<?php echo get_avatar( get_the_author_meta( 'ID' ), 32, '', '', [ 'class' => 'bs-blog__avatar' ] ); ?>
							<span class="bs-blog__author-name"><?php the_author(); ?></span>
							<span class="bs-blog__meta-dot" aria-hidden="true">&middot;</span>
						<?php endif; ?>
						<time class="bs-blog__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
							<?php echo blue_sage_post_date(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</time>
						<?php if ( $show_read_time ) : ?>
							<span class="bs-blog__meta-dot" aria-hidden="true">&middot;</span>
							<span class="bs-blog__read-time"><?php blue_sage_reading_time(); ?></span>
						<?php endif; ?>
					</div>
					<a href="<?php the_permalink(); ?>" class="bs-blog__read-more">
						<?php esc_html_e( 'Read article', 'blue-sage' ); ?> &rarr;
					</a>
				</div>
			</article>
			<?php endwhile; ?>

			<?php elseif ( 'list' === $layout ) : ?>

			<ul class="bs-blog__list" role="list">
				<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
				<li class="bs-blog__list-row js-fade-up" role="listitem">
					<?php
					$categories = get_the_category();
					if ( $categories ) :
						$cat = $categories[0];
						?>
						<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
						   class="bs-blog__category"><?php echo esc_html( $cat->name ); ?></a>
					<?php endif; ?>
					<h3 class="bs-blog__list-title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
					<time class="bs-blog__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
						<?php echo blue_sage_post_date(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</time>
				</li>
				<?php endwhile; ?>
			</ul>

			<?php else : // standard ?>

			<div class="bs-blog__grid js-stagger">
				<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
				<article class="bs-blog__card js-fade-up">
					<?php
					$thumb_id = get_post_thumbnail_id();
					if ( $thumb_id ) :
					?>
					<div class="bs-blog__thumb">
						<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<?php echo wp_get_attachment_image( $thumb_id, 'medium_large', false, [
								'class'   => 'bs-blog__thumb-img',
								'loading' => 'lazy',
							] ); ?>
						</a>
					</div>
					<?php endif; ?>
					<div class="bs-blog__card-body">
						<?php
						$categories = get_the_category();
						if ( $categories ) :
							$cat = $categories[0];
							?>
							<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
							   class="bs-blog__category"><?php echo esc_html( $cat->name ); ?></a>
						<?php endif; ?>
						<h3 class="bs-blog__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
						<?php if ( $show_excerpt ) : ?>
							<p class="bs-blog__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
						<?php endif; ?>
						<div class="bs-blog__meta">
							<?php if ( $show_author ) : ?>
								<?php echo get_avatar( get_the_author_meta( 'ID' ), 32, '', '', [ 'class' => 'bs-blog__avatar' ] ); ?>
								<span class="bs-blog__author-name"><?php the_author(); ?></span>
								<span class="bs-blog__meta-dot" aria-hidden="true">&middot;</span>
							<?php endif; ?>
							<time class="bs-blog__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
								<?php echo blue_sage_post_date(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</time>
							<?php if ( $show_read_time ) : ?>
								<span class="bs-blog__meta-dot" aria-hidden="true">&middot;</span>
								<span class="bs-blog__read-time"><?php blue_sage_reading_time(); ?></span>
							<?php endif; ?>
						</div>
					</div>
				</article>
				<?php endwhile; ?>
			</div>

			<?php endif; ?>

		<?php endif; wp_reset_postdata(); ?>

		<?php if ( $cta_label && $cta_url ) : ?>
		<div class="bs-blog__cta-wrap">
			<a href="<?php echo $cta_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" class="bs-blog__cta">
				<?php echo esc_html( $cta_label ); ?> &rarr;
			</a>
		</div>
		<?php endif; ?>

	</div>
</section>
