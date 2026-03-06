<?php
/**
 * Blue Sage — SEO & Structured Data
 *
 * Outputs Open Graph, Twitter Card, canonical URL, and JSON-LD structured
 * data for WebSite, Organization, BreadcrumbList, Article, and FAQPage.
 *
 * Silenced entirely when Yoast SEO, RankMath, or AIOSEO is active.
 *
 * @package BlueSage
 * @author Ilyas Serter <hello@sagegrids.com>
 * @company SAGE GRIDS LTD <https://www.sagegrids.com>
 * @link https://www.iserter.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * True when a known SEO plugin is handling meta output.
 */
function blue_sage_seo_plugin_active(): bool {
	return defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'AIOSEO_VERSION' );
}

// ---------------------------------------------------------------------------
// Open Graph + Twitter Card
// ---------------------------------------------------------------------------

/**
 * Output Open Graph and Twitter Card meta tags.
 */
function blue_sage_social_meta(): void {
	if ( blue_sage_seo_plugin_active() ) {
		return;
	}

	$title       = wp_get_document_title();
	$description = '';
	$image       = '';
	$type        = 'website';
	$url         = get_permalink() ?: home_url( '/' );

	if ( is_singular() ) {
		global $post;
		$description = has_excerpt( $post )
			? get_the_excerpt( $post )
			: wp_trim_words( get_the_content( null, false, $post ), 30 );

		if ( has_post_thumbnail( $post ) ) {
			$img_src = wp_get_attachment_image_src(
				get_post_thumbnail_id( $post ),
				'large'
			);
			$image = $img_src ? $img_src[0] : '';
		}

		$type = 'article';
	} elseif ( is_front_page() || is_home() ) {
		$description = get_bloginfo( 'description' );
		$image       = get_theme_mod( 'blue_sage_og_image', '' );
	} else {
		$description = get_bloginfo( 'description' );
	}

	if ( ! $image ) {
		$image = get_theme_mod(
			'blue_sage_og_image',
			BLUE_SAGE_URI . '/assets/images/og-default.jpg'
		);
	}

	$site_name = get_bloginfo( 'name' );
	?>
	<meta property="og:type"        content="<?php echo esc_attr( $type ); ?>">
	<meta property="og:title"       content="<?php echo esc_attr( $title ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
	<meta property="og:url"         content="<?php echo esc_url( $url ); ?>">
	<meta property="og:site_name"   content="<?php echo esc_attr( $site_name ); ?>">
	<?php if ( $image ) : ?>
	<meta property="og:image"        content="<?php echo esc_url( $image ); ?>">
	<meta property="og:image:width"  content="1200">
	<meta property="og:image:height" content="630">
	<?php endif; ?>
	<meta name="twitter:card"        content="summary_large_image">
	<meta name="twitter:title"       content="<?php echo esc_attr( $title ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>">
	<?php if ( $image ) : ?>
	<meta name="twitter:image"       content="<?php echo esc_url( $image ); ?>">
	<?php endif; ?>
	<?php
}
add_action( 'wp_head', 'blue_sage_social_meta', 2 );

// ---------------------------------------------------------------------------
// Canonical URL
// ---------------------------------------------------------------------------

/**
 * Output a canonical URL tag.
 */
function blue_sage_canonical(): void {
	if ( blue_sage_seo_plugin_active() ) {
		return;
	}

	$canonical = '';

	if ( is_singular() ) {
		$canonical = (string) get_permalink();
	} elseif ( is_front_page() ) {
		$canonical = home_url( '/' );
	} elseif ( is_home() ) {
		$posts_page_id = (int) get_option( 'page_for_posts' );
		$canonical     = $posts_page_id
			? (string) get_permalink( $posts_page_id )
			: home_url( '/' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$link = get_term_link( get_queried_object() );
		if ( ! is_wp_error( $link ) ) {
			$canonical = $link;
		}
	} elseif ( is_author() ) {
		$canonical = (string) get_author_posts_url( get_queried_object_id() );
	}

	if ( $canonical && get_query_var( 'paged' ) > 1 ) {
		$canonical = trailingslashit( $canonical ) . 'page/' . get_query_var( 'paged' ) . '/';
	}

	if ( $canonical ) {
		echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'blue_sage_canonical', 3 );

// ---------------------------------------------------------------------------
// JSON-LD: WebSite + Organization
// ---------------------------------------------------------------------------

/**
 * JSON-LD: WebSite and Organization schemas output on every public page.
 */
function blue_sage_jsonld_global(): void {
	if ( blue_sage_seo_plugin_active() ) {
		return;
	}

	$site_name = get_bloginfo( 'name' );
	$site_url  = home_url( '/' );
	$logo_url  = get_theme_mod( 'blue_sage_org_logo', '' );

	$schemas = [
		[
			'@context'        => 'https://schema.org',
			'@type'           => 'WebSite',
			'name'            => $site_name,
			'url'             => $site_url,
			'potentialAction' => [
				'@type'       => 'SearchAction',
				'target'      => [
					'@type'       => 'EntryPoint',
					'urlTemplate' => home_url( '/?s={search_term_string}' ),
				],
				'query-input' => 'required name=search_term_string',
			],
		],
	];

	if ( $logo_url ) {
		$org_schema = [
			'@context' => 'https://schema.org',
			'@type'    => 'Organization',
			'name'     => $site_name,
			'url'      => $site_url,
			'logo'     => [
				'@type' => 'ImageObject',
				'url'   => $logo_url,
			],
		];

		/** @param array $org_schema The Organization JSON-LD array. */
		$org_schema = apply_filters( 'blue_sage_jsonld_organization', $org_schema );
		$schemas[]  = $org_schema;
	}

	foreach ( $schemas as $schema ) {
		echo '<script type="application/ld+json">'
			. wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT )
			. '</script>' . "\n";
	}
}
add_action( 'wp_head', 'blue_sage_jsonld_global', 10 );

// ---------------------------------------------------------------------------
// JSON-LD: BreadcrumbList
// ---------------------------------------------------------------------------

/**
 * JSON-LD: BreadcrumbList — output on all pages except the front page.
 */
function blue_sage_jsonld_breadcrumb(): void {
	if ( blue_sage_seo_plugin_active() || is_front_page() ) {
		return;
	}

	$items = [
		[
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => __( 'Home', 'blue-sage' ),
			'item'     => home_url( '/' ),
		],
	];

	$position = 2;

	if ( is_singular() ) {
		if ( is_single() ) {
			$cats = get_the_category();
			if ( $cats ) {
				$cat     = $cats[0];
				$items[] = [
					'@type'    => 'ListItem',
					'position' => $position++,
					'name'     => esc_html( $cat->name ),
					'item'     => get_category_link( $cat->term_id ),
				];
			}
		}

		$items[] = [
			'@type'    => 'ListItem',
			'position' => $position,
			'name'     => esc_html( get_the_title() ),
			'item'     => (string) get_permalink(),
		];
	} elseif ( is_category() ) {
		$items[] = [
			'@type'    => 'ListItem',
			'position' => $position,
			'name'     => esc_html( single_cat_title( '', false ) ),
			'item'     => (string) get_category_link( get_queried_object_id() ),
		];
	} elseif ( is_search() ) {
		$items[] = [
			'@type'    => 'ListItem',
			'position' => $position,
			/* translators: %s: search query */
			'name'     => sprintf( __( 'Search: %s', 'blue-sage' ), get_search_query() ),
		];
	}

	$schema = [
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	];

	echo '<script type="application/ld+json">'
		. wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT )
		. '</script>' . "\n";
}
add_action( 'wp_head', 'blue_sage_jsonld_breadcrumb', 11 );

// ---------------------------------------------------------------------------
// JSON-LD: Article
// ---------------------------------------------------------------------------

/**
 * JSON-LD: Article schema for single blog posts.
 */
function blue_sage_jsonld_article(): void {
	if ( blue_sage_seo_plugin_active() || ! is_single() ) {
		return;
	}

	global $post;

	$image_url = '';
	$thumb_id  = get_post_thumbnail_id( $post );
	if ( $thumb_id ) {
		$img_src   = wp_get_attachment_image_src( $thumb_id, 'large' );
		$image_url = $img_src ? $img_src[0] : '';
	}

	$schema = [
		'@context'      => 'https://schema.org',
		'@type'         => 'Article',
		'headline'      => get_the_title( $post ),
		'datePublished' => get_the_date( 'c', $post ),
		'dateModified'  => get_the_modified_date( 'c', $post ),
		'author'        => [
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', (int) $post->post_author ),
		],
		'publisher'     => [
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
		],
		'url'           => (string) get_permalink( $post ),
		'description'   => has_excerpt( $post )
			? get_the_excerpt( $post )
			: wp_trim_words( get_the_content( null, false, $post ), 30 ),
	];

	if ( $image_url ) {
		$schema['image'] = $image_url;
	}

	echo '<script type="application/ld+json">'
		. wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT )
		. '</script>' . "\n";
}
add_action( 'wp_head', 'blue_sage_jsonld_article', 12 );

// ---------------------------------------------------------------------------
// JSON-LD: FAQPage
// ---------------------------------------------------------------------------

/**
 * JSON-LD: FAQPage schema — auto-generated from the FAQ Accordion block.
 */
function blue_sage_jsonld_faq(): void {
	if ( blue_sage_seo_plugin_active() || ! is_singular() ) {
		return;
	}

	global $post;

	if ( ! has_block( 'blue-sage/faq-accordion', $post ) ) {
		return;
	}

	$blocks    = parse_blocks( $post->post_content );
	$faq_items = [];

	foreach ( $blocks as $block ) {
		if ( 'blue-sage/faq-accordion' !== $block['blockName'] ) {
			continue;
		}

		$items = $block['attrs']['items'] ?? [];
		foreach ( $items as $item ) {
			$question = sanitize_text_field( $item['question'] ?? '' );
			$answer   = wp_kses_post( $item['answer'] ?? '' );

			if ( $question && $answer ) {
				$faq_items[] = [
					'@type'          => 'Question',
					'name'           => $question,
					'acceptedAnswer' => [
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( $answer ),
					],
				];
			}
		}
	}

	if ( empty( $faq_items ) ) {
		return;
	}

	$schema = [
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $faq_items,
	];

	echo '<script type="application/ld+json">'
		. wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT )
		. '</script>' . "\n";
}
add_action( 'wp_head', 'blue_sage_jsonld_faq', 13 );

// ---------------------------------------------------------------------------
// robots.txt
// ---------------------------------------------------------------------------

/**
 * Modify virtual robots.txt: block AI training crawlers, allow AI search bots.
 *
 * @param  string $output Current robots.txt content.
 * @return string
 */
function blue_sage_robots_txt( string $output ): string {
	$additions = "\n"
		. "# AI crawler policy\n"
		. "User-agent: GPTBot\n"
		. "Disallow: /\n\n"
		. "User-agent: CCBot\n"
		. "Disallow: /\n\n"
		. "User-agent: OAI-SearchBot\n"
		. "Allow: /\n\n"
		. "User-agent: PerplexityBot\n"
		. "Allow: /\n";

	return $output . $additions;
}
add_filter( 'robots_txt', 'blue_sage_robots_txt' );

// ---------------------------------------------------------------------------
// Customizer settings
// ---------------------------------------------------------------------------

/**
 * Register Customizer settings for SEO fields.
 *
 * @param WP_Customize_Manager $wp_customize
 */
function blue_sage_seo_customizer( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section( 'blue_sage_seo', [
		'title'    => __( 'SEO & Social', 'blue-sage' ),
		'priority' => 120,
	] );

	$wp_customize->add_setting( 'blue_sage_og_image', [
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	] );
	$wp_customize->add_control( new WP_Customize_Image_Control(
		$wp_customize,
		'blue_sage_og_image',
		[
			'label'   => __( 'Default Social Share Image (1200×630)', 'blue-sage' ),
			'section' => 'blue_sage_seo',
		]
	) );

	$wp_customize->add_setting( 'blue_sage_org_logo', [
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	] );
	$wp_customize->add_control( new WP_Customize_Image_Control(
		$wp_customize,
		'blue_sage_org_logo',
		[
			'label'   => __( 'Organization Logo (for JSON-LD)', 'blue-sage' ),
			'section' => 'blue_sage_seo',
		]
	) );
}
add_action( 'customize_register', 'blue_sage_seo_customizer' );
