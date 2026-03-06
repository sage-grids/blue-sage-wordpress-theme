# Phase 5 — SEO, Accessibility & Launch

**Scope:** Week 8
**Status:** Planned
**Depends on:** Phase 4 (Polish & Performance) — complete

---

## Overview

Phase 5 is the final phase before public release. It delivers the invisible infrastructure that separates a hobby theme from a professional product: structured data, self-hosted fonts, a clean WordPress.org submission package, and a starter child theme.

Phase 5 has four streams:

1. **SEO & Structured Data** — New `inc/seo.php`: JSON-LD schemas, Open Graph, Twitter Card, canonical URLs, robots.txt filter
2. **Self-hosted Fonts** — Replace Google Fonts CDN with local WOFF2 files
3. **WordPress.org Compliance & Packaging** — `readme.txt`, screenshot, Theme Check audit, translation readiness
4. **Documentation & Child Theme** — Setup guide, inline PHP doc, child theme starter package

**PRD reference:** Section 7.5 (SEO & Structured Data), Section 7.4 (Performance), Section 7.6 (Accessibility), Section 8.3 (Child Theme Support), Section 10 Phase 5.

---

## What Already Exists (do not re-implement)

| Feature | Where | Status |
|---|---|---|
| Block patterns (21 patterns) | `patterns/*.php` | Complete — exceeds 20-pattern target |
| Pattern categories registered | `inc/block-patterns.php` | Complete |
| Performance hooks (`inc/performance.php`) | `functions.php` line 22 | Complete |
| All custom blocks (11 blocks) | `blocks/*/` | Complete (Phases 2–3) |
| Hero entrance + parallax JS | `assets/js/page-entrance.js`, `parallax.js` | Complete (Phase 4) |
| Back-to-top button | `assets/js/back-to-top.js` | Complete (Phase 4) |
| Global `:focus-visible` ring | `style.css` §5 | Complete (Phase 4) |
| Font fallback `@font-face` metrics | `style.css` §1b | Complete (Phase 4) — **will be replaced** in Stream 2 |

---

## Stream 1: SEO & Structured Data

### 1.1 New File: `inc/seo.php`

The entire SEO layer lives in one file. It outputs nothing if Yoast SEO or RankMath is active — those plugins handle this territory and duplication causes errors.

**Add to `functions.php`** (after existing `require_once` calls):

```php
require_once BLUE_SAGE_DIR . '/inc/seo.php';
```

---

#### 1.1.1 Plugin detection guard

At the top of `inc/seo.php`, wrap all output hooks in a guard that silences the theme's own SEO output when a dedicated SEO plugin is active:

```php
<?php
/**
 * Blue Sage — SEO & Structured Data
 *
 * Outputs Open Graph, Twitter Card, canonical URL, and JSON-LD structured
 * data for WebSite, Organization, BreadcrumbList, Article, and FAQPage.
 *
 * Silenced entirely when Yoast SEO or RankMath is active, because those
 * plugins manage this output and duplication causes validation errors.
 *
 * @package BlueSage
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * True when a known SEO plugin is handling meta output.
 * Theme SEO functions bail when this returns true.
 */
function blue_sage_seo_plugin_active(): bool {
    return defined( 'WPSEO_VERSION' )          // Yoast SEO
        || defined( 'RANK_MATH_VERSION' )       // RankMath
        || defined( 'AIOSEO_VERSION' );         // All in One SEO
}
```

All functions below check `blue_sage_seo_plugin_active()` and return early if true.

---

#### 1.1.2 Open Graph + Twitter Card

```php
/**
 * Output Open Graph and Twitter Card meta tags.
 * Fired on wp_head priority 2 (before title, before scripts).
 */
function blue_sage_social_meta(): void {
    if ( blue_sage_seo_plugin_active() ) {
        return;
    }

    // Resolve values.
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

    // Fallback image: theme default OG image.
    if ( ! $image ) {
        $image = get_theme_mod(
            'blue_sage_og_image',
            BLUE_SAGE_URI . '/assets/images/og-default.jpg'
        );
    }

    $site_name = get_bloginfo( 'name' );

    // Output.
    ?>
    <meta property="og:type"        content="<?php echo esc_attr( $type ); ?>">
    <meta property="og:title"       content="<?php echo esc_attr( $title ); ?>">
    <meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
    <meta property="og:url"         content="<?php echo esc_url( $url ); ?>">
    <meta property="og:site_name"   content="<?php echo esc_attr( $site_name ); ?>">
    <?php if ( $image ) : ?>
    <meta property="og:image"       content="<?php echo esc_url( $image ); ?>">
    <meta property="og:image:width" content="1200">
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
```

---

#### 1.1.3 Canonical URL

```php
/**
 * Output a canonical URL tag.
 * WordPress core does not output canonical by default (only Yoast/RankMath do).
 */
function blue_sage_canonical(): void {
    if ( blue_sage_seo_plugin_active() ) {
        return;
    }

    $canonical = '';

    if ( is_singular() ) {
        $canonical = get_permalink();
    } elseif ( is_front_page() ) {
        $canonical = home_url( '/' );
    } elseif ( is_home() ) {
        $posts_page_id = get_option( 'page_for_posts' );
        $canonical     = $posts_page_id
            ? get_permalink( $posts_page_id )
            : home_url( '/' );
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $canonical = get_term_link( get_queried_object() );
        if ( is_wp_error( $canonical ) ) {
            $canonical = '';
        }
    } elseif ( is_author() ) {
        $canonical = get_author_posts_url( get_queried_object_id() );
    }

    // Pagination.
    if ( $canonical && get_query_var( 'paged' ) > 1 ) {
        $canonical = trailingslashit( $canonical )
            . 'page/' . get_query_var( 'paged' ) . '/';
    }

    if ( $canonical ) {
        echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'blue_sage_canonical', 3 );
```

---

#### 1.1.4 JSON-LD: WebSite + Organization

Output on every page. Organization data pulled from Customizer settings (see §1.2).

```php
/**
 * JSON-LD: WebSite and Organization schemas.
 * Output on every public page.
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
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            'name'     => $site_name,
            'url'      => $site_url,
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

    // Organization only when logo is configured.
    if ( $logo_url ) {
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => $site_name,
            'url'      => $site_url,
            'logo'     => [
                '@type' => 'ImageObject',
                'url'   => $logo_url,
            ],
        ];
    }

    foreach ( $schemas as $schema ) {
        // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
        echo '<script type="application/ld+json">'
            . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT )
            . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'blue_sage_jsonld_global', 10 );
```

---

#### 1.1.5 JSON-LD: BreadcrumbList

Output on all pages except the front page. Works with WordPress's native navigation hierarchy.

```php
/**
 * JSON-LD: BreadcrumbList.
 * Skipped on front page (no breadcrumb needed there).
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
        // Category breadcrumb for single posts.
        if ( is_single() ) {
            $cats = get_the_category();
            if ( $cats ) {
                $cat      = $cats[0];
                $items[]  = [
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
            'item'     => get_permalink(),
        ];
    } elseif ( is_category() ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => esc_html( single_cat_title( '', false ) ),
            'item'     => get_category_link( get_queried_object_id() ),
        ];
    } elseif ( is_search() ) {
        /* translators: %s: search term */
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => sprintf( __( 'Search: %s', 'blue-sage' ), get_search_query() ),
        ];
    }

    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];

    // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
    echo '<script type="application/ld+json">'
        . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT )
        . '</script>' . "\n";
}
add_action( 'wp_head', 'blue_sage_jsonld_breadcrumb', 11 );
```

---

#### 1.1.6 JSON-LD: Article

Output only on `single` posts.

```php
/**
 * JSON-LD: Article schema for single blog posts.
 */
function blue_sage_jsonld_article(): void {
    if ( blue_sage_seo_plugin_active() || ! is_single() ) {
        return;
    }

    global $post;

    $image_url   = '';
    $thumb_id    = get_post_thumbnail_id( $post );
    if ( $thumb_id ) {
        $img_src   = wp_get_attachment_image_src( $thumb_id, 'large' );
        $image_url = $img_src ? $img_src[0] : '';
    }

    $schema = [
        '@context'         => 'https://schema.org',
        '@type'            => 'Article',
        'headline'         => get_the_title( $post ),
        'datePublished'    => get_the_date( 'c', $post ),
        'dateModified'     => get_the_modified_date( 'c', $post ),
        'author'           => [
            '@type' => 'Person',
            'name'  => get_the_author_meta( 'display_name', (int) $post->post_author ),
        ],
        'publisher'        => [
            '@type' => 'Organization',
            'name'  => get_bloginfo( 'name' ),
        ],
        'url'              => get_permalink( $post ),
        'description'      => has_excerpt( $post )
            ? get_the_excerpt( $post )
            : wp_trim_words( get_the_content( null, false, $post ), 30 ),
    ];

    if ( $image_url ) {
        $schema['image'] = $image_url;
    }

    // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
    echo '<script type="application/ld+json">'
        . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT )
        . '</script>' . "\n";
}
add_action( 'wp_head', 'blue_sage_jsonld_article', 12 );
```

---

#### 1.1.7 JSON-LD: FAQPage

Automatically generated from any page containing the FAQ Accordion block. Parses `$post->post_content` for the `blue-sage/faq-accordion` block and extracts Q&A pairs from block attributes.

```php
/**
 * JSON-LD: FAQPage schema.
 * Auto-generated from any singular page containing the faq-accordion block.
 */
function blue_sage_jsonld_faq(): void {
    if ( blue_sage_seo_plugin_active() || ! is_singular() ) {
        return;
    }

    global $post;

    if ( ! has_block( 'blue-sage/faq-accordion', $post ) ) {
        return;
    }

    $blocks   = parse_blocks( $post->post_content );
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

    // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
    echo '<script type="application/ld+json">'
        . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT )
        . '</script>' . "\n";
}
add_action( 'wp_head', 'blue_sage_jsonld_faq', 13 );
```

---

### 1.2 robots.txt Filter

The PRD specifies a `robots.txt` template that allowlists `OAI-SearchBot` and `PerplexityBot` while blocking `GPTBot` and `CCBot` by default.

WordPress generates `robots.txt` dynamically. Use the `robots_txt` filter — **do not create a physical robots.txt file** (WordPress ignores it if `WP_DEBUG` is on or permalink structure is plain).

Add this to the bottom of `inc/seo.php`:

```php
/**
 * Modify the virtual robots.txt to reflect the PRD bot policy:
 * - Block: GPTBot, CCBot (AI training scrapers)
 * - Allow: OAI-SearchBot, PerplexityBot (AI search/citation bots)
 *
 * @param  string $output The current robots.txt content.
 * @return string
 */
function blue_sage_robots_txt( string $output ): string {
    $additions = "\n"
        . "# AI crawler policy — Blue Sage theme default\n"
        . "# Edit via the blue_sage_robots_txt filter or a plugin.\n\n"
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
```

---

### 1.3 Customizer Settings for SEO

Add minimal Customizer settings to `inc/seo.php` so users can configure the Organization logo and default OG image without editing PHP:

```php
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

    // Default OG image.
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

    // Organization logo.
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
```

---

## Stream 2: Self-hosted Fonts

The PRD specifies self-hosted WOFF2 font files (§7.4). Phase 4 added font fallback `@font-face` metrics as a CLS mitigation. Self-hosting eliminates the CDN dependency entirely and removes the need for those fallback blocks.

### 2.1 Font Files Required

Download WOFF2 subsets from Google Fonts (Latin subset) and place in `assets/fonts/`:

```
assets/fonts/
├── plus-jakarta-sans-400.woff2
├── plus-jakarta-sans-600.woff2
├── plus-jakarta-sans-700.woff2
├── plus-jakarta-sans-800.woff2
├── inter-400.woff2
├── inter-500.woff2
├── jetbrains-mono-400.woff2
└── jetbrains-mono-500.woff2
```

**Download tool:** Use `google-webfonts-helper` (self-hosted font downloader) at the URL below to get correctly subsetted WOFF2 files. Download Latin subset only.

- Plus Jakarta Sans: weights 400, 600, 700, 800
- Inter: weights 400, 500
- JetBrains Mono: weights 400, 500

Total estimated size: ~180 kB across all 8 files (well within the 400 kB page budget).

---

### 2.2 Remove Google Fonts from `inc/enqueue.php`

Find and **remove** the following block from `inc/enqueue.php` (the Google Fonts `wp_enqueue_style()` call):

```php
// REMOVE this block in Phase 5:
wp_enqueue_style(
    'blue-sage-google-fonts',
    'https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=JetBrains+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap',
    [],
    null
);
```

Replace with `preconnect` removal — no replacement needed, since `@font-face` in `style.css` now serves fonts locally.

Also remove any `<link rel="preconnect" href="https://fonts.googleapis.com">` and `<link rel="preconnect" href="https://fonts.gstatic.com">` that may be output via `wp_head` actions.

---

### 2.3 `@font-face` Declarations in `style.css`

**Replace** the Phase 4 `§1b FONT FALLBACK METRICS` block with the full self-hosted declarations:

```css
/* ============================================================
   1b. SELF-HOSTED FONTS
   ============================================================ */

/* Plus Jakarta Sans */
@font-face {
    font-family: 'Plus Jakarta Sans';
    src: url('assets/fonts/plus-jakarta-sans-400.woff2') format('woff2');
    font-weight: 400;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'Plus Jakarta Sans';
    src: url('assets/fonts/plus-jakarta-sans-600.woff2') format('woff2');
    font-weight: 600;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'Plus Jakarta Sans';
    src: url('assets/fonts/plus-jakarta-sans-700.woff2') format('woff2');
    font-weight: 700;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'Plus Jakarta Sans';
    src: url('assets/fonts/plus-jakarta-sans-800.woff2') format('woff2');
    font-weight: 800;
    font-style: normal;
    font-display: swap;
}

/* Inter */
@font-face {
    font-family: 'Inter';
    src: url('assets/fonts/inter-400.woff2') format('woff2');
    font-weight: 400;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'Inter';
    src: url('assets/fonts/inter-500.woff2') format('woff2');
    font-weight: 500;
    font-style: normal;
    font-display: swap;
}

/* JetBrains Mono */
@font-face {
    font-family: 'JetBrains Mono';
    src: url('assets/fonts/jetbrains-mono-400.woff2') format('woff2');
    font-weight: 400;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'JetBrains Mono';
    src: url('assets/fonts/jetbrains-mono-500.woff2') format('woff2');
    font-weight: 500;
    font-style: normal;
    font-display: swap;
}
```

**Update the font stacks in `:root`** — remove the `-Fallback` variants (no longer needed since fonts load locally):

```css
--bs-font-display: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
--bs-font-body:    'Inter', system-ui, -apple-system, sans-serif;
--bs-font-mono:    'JetBrains Mono', 'Fira Code', monospace;
```

**Font preload:** Add to `inc/enqueue.php` to preload the two most critical font files (body 400 and display 700, which are above-the-fold):

```php
/**
 * Preload critical WOFF2 font files for LCP reduction.
 * Only the two heaviest-used weights are preloaded.
 */
function blue_sage_preload_fonts(): void {
    $fonts = [
        'plus-jakarta-sans-700.woff2',
        'inter-400.woff2',
    ];

    foreach ( $fonts as $font ) {
        echo '<link rel="preload" href="'
            . esc_url( BLUE_SAGE_URI . '/assets/fonts/' . $font )
            . '" as="font" type="font/woff2" crossorigin="anonymous">' . "\n";
    }
}
add_action( 'wp_head', 'blue_sage_preload_fonts', 1 );
```

---

## Stream 3: WordPress.org Compliance & Packaging

### 3.1 `readme.txt`

WordPress.org requires a `readme.txt` (not `README.md`) in a specific format. Create `readme.txt` in the theme root:

```
=== Blue Sage ===
Contributors: (your WordPress.org username)
Tags: blog, portfolio, grid-layout, one-column, two-columns, custom-colors, custom-logo, custom-menu, editor-style, featured-images, full-site-editing, block-patterns, rtl-language-support, sticky-post, threaded-comments, translation-ready, wide-blocks
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A premium Full Site Editing block theme for high-end agencies, SaaS companies, and modern businesses.

== Description ==

Blue Sage is a premium WordPress FSE block theme built for brands that lead, not follow. Named after the sage — a figure of wisdom and authority — the theme communicates clarity, precision, and quiet confidence.

Features:
* Full Site Editing (FSE) with block patterns and global styles
* 11 custom blocks: Hero (3 layouts), Feature Grid, Metrics Bar, Testimonial Carousel, Pricing Table, CTA Section, FAQ Accordion, Process Steps, Team Grid, Logo Wall, Blog Cards
* 21 curated block patterns covering every common section type
* Lighthouse Performance 95+ out of the box
* Self-hosted fonts (no external CDN requests)
* JSON-LD structured data: WebSite, Organization, BreadcrumbList, Article, FAQPage
* WCAG 2.1 AA accessible
* Vanilla JS only — zero jQuery, zero build step required

== Installation ==

1. Upload the `blue-sage` folder to `/wp-content/themes/`
2. Activate the theme via Appearance > Themes
3. Visit Site Editor to configure global styles, header, and footer
4. Use the Block Inserter to add Blue Sage patterns to any page

== Frequently Asked Questions ==

= Does Blue Sage support WooCommerce? =
WooCommerce basic styling is included. Advanced WooCommerce templates are planned for v1.1.

= Can I use a page builder? =
Blue Sage is a Full Site Editing (FSE) theme and works exclusively with the WordPress Block Editor. Elementor and similar page builders are not supported or needed.

= Does it support child themes? =
Yes. A starter child theme is included in the package.

== Changelog ==

= 1.0.0 =
* Initial release

== Copyright ==

Blue Sage WordPress Theme, Copyright (C) 2026 (your name/company)
Blue Sage is distributed under the terms of the GNU GPL

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

Fonts: Plus Jakarta Sans, licensed SIL Open Font License 1.1
Fonts: Inter, licensed SIL Open Font License 1.1
Fonts: JetBrains Mono, licensed SIL Open Font License 1.1
```

---

### 3.2 `screenshot.png`

The theme screenshot is displayed in Appearance > Themes. Requirements:
- **Dimensions:** exactly 1200 × 900 px
- **Format:** PNG
- **Location:** `screenshot.png` in the theme root
- **Content:** A styled composite of the homepage hero section over the full width, with realistic placeholder content. Must look intentional, not like a blank mockup.

Recommended composition:
- Deep navy hero (`#0A1628` background) with the centered layout
- Eyebrow in Sage Green, headline in white with blue gradient on key words
- Two CTA buttons visible
- Footer partially visible at bottom of canvas

This file must be created as a design asset (Figma, Sketch, or screenshot of a live WordPress install with demo content). It cannot be generated by code.

---

### 3.3 WordPress.org Theme Check Audit

Run the **Theme Check** plugin (`wordpress.org/plugins/theme-check`) and resolve all errors and warnings before submission. The target is **0 errors, 0 warnings**.

Known areas to verify:

| Check | Where | Action |
|---|---|---|
| All `echo` output uses `esc_html_e()` / `esc_url()` / `wp_kses_post()` | All `blocks/*/render.php`, `inc/seo.php` | Audit each file |
| All user-facing strings wrapped in `__()` / `_e()` | All PHP files | Audit — especially error messages and button labels |
| No `call_user_func_array()` with untrusted input | N/A | Confirm |
| `add_theme_support('title-tag')` present | `inc/setup.php` | Already done |
| `wp_head()` and `wp_footer()` present in all templates | `parts/header.html`, `parts/footer.html` | Confirm — FSE templates handled by core |
| No hardcoded `http://` references | All files | Search and replace |
| License headers in all PHP files | All `inc/` and `blocks/` files | Add if missing |
| `Text Domain` in `style.css` matches `'blue-sage'` | `style.css` | Already correct |
| No calls to deprecated WordPress functions | All PHP | Verify against WP 6.7 changelog |

**Command to search for unescaped echo statements (run in project root):**

```bash
grep -rn "echo \$" --include="*.php" blocks/ inc/ patterns/
```

Any bare `echo $variable` must use an appropriate escape wrapper.

---

### 3.4 Translation Readiness

Every user-visible string must use a translation function with the `'blue-sage'` text domain. Audit the following:

**Strings likely missing translation wrapping:**

- Block attribute placeholders in `render.php` files (button labels, default eyebrow text)
- Error fallback messages
- Screen reader text in JS-generated HTML (back-to-top button aria-label is already in JS — this is acceptable, or add a `wp_localize_script()` call to make it filterable)

**Add `wp_localize_script()` for JS strings** to `inc/enqueue.php`:

```php
wp_localize_script(
    'blue-sage-back-to-top',
    'BlueSageL10n',
    [
        'backToTop' => __( 'Back to top', 'blue-sage' ),
    ]
);
```

Then update `assets/js/back-to-top.js` to use:

```javascript
btn.setAttribute(
    'aria-label',
    ( window.BlueSageL10n && window.BlueSageL10n.backToTop )
        ? window.BlueSageL10n.backToTop
        : 'Back to top'
);
```

---

## Stream 4: Documentation & Child Theme

### 4.1 Child Theme Starter

The PRD specifies "a starter child theme included in the package with documented hooks, filters, and action points" (§8.3).

Create a `blue-sage-child/` directory at the same level as the theme root (it ships as a separate folder in the distribution ZIP):

**`blue-sage-child/style.css`:**

```css
/*
Theme Name:   Blue Sage Child
Description:  Child theme for Blue Sage. Add your customizations here.
Template:     blue-sage
Version:      1.0.0
License:      GNU General Public License v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  blue-sage-child
*/

/*
  Add your custom CSS overrides below.
  Parent theme styles load automatically — no need to re-import them.
*/
```

**`blue-sage-child/functions.php`:**

```php
<?php
/**
 * Blue Sage Child — Functions
 *
 * This file is the entry point for all child theme customizations.
 * The parent theme (Blue Sage) loads automatically by WordPress.
 *
 * Documented hook points:
 *
 * blue_sage_before_hero        — Action: fires before hero block render
 * blue_sage_after_hero         — Action: fires after hero block render
 * blue_sage_og_image           — Filter: override the default OG image URL
 * blue_sage_jsonld_organization — Filter: modify the Organization JSON-LD array
 * blue_sage_robots_txt         — Filter (WordPress core): extend robots.txt
 *
 * @package BlueSageChild
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue child theme stylesheet.
 * The parent stylesheet is already enqueued by the parent theme.
 * This only adds the child's style.css on top.
 */
function blue_sage_child_enqueue_styles(): void {
    $parent_version = wp_get_theme( 'blue-sage' )->get( 'Version' );

    wp_enqueue_style(
        'blue-sage-child-style',
        get_stylesheet_uri(),
        [ 'blue-sage-style' ],
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'wp_enqueue_scripts', 'blue_sage_child_enqueue_styles' );

/*
 * -----------------------------------------------------------------
 * EXAMPLE CUSTOMIZATIONS
 * Uncomment and adapt these as needed.
 * -----------------------------------------------------------------
 */

/*
 * Example: Override the default OG image.
 *
add_filter( 'blue_sage_og_image', function( string $url ): string {
    return 'https://example.com/my-og-image.jpg';
} );
*/

/*
 * Example: Add a company address to the Organization JSON-LD.
 *
add_filter( 'blue_sage_jsonld_organization', function( array $schema ): array {
    $schema['address'] = [
        '@type'           => 'PostalAddress',
        'streetAddress'   => '123 Main Street',
        'addressLocality' => 'San Francisco',
        'addressRegion'   => 'CA',
        'postalCode'      => '94105',
        'addressCountry'  => 'US',
    ];
    return $schema;
} );
*/
```

**`blue-sage-child/README.md`:**

```markdown
# Blue Sage Child Theme

Starter child theme for [Blue Sage](https://example.com/blue-sage).

## Getting Started

1. Copy the `blue-sage-child` folder to `wp-content/themes/`
2. Activate via Appearance > Themes (Blue Sage parent must also be installed)
3. Add CSS overrides to `style.css`
4. Add PHP customizations to `functions.php`

## Available Hook Points

| Hook | Type | Description |
|---|---|---|
| `blue_sage_before_hero` | Action | Fires before hero block render |
| `blue_sage_after_hero` | Action | Fires after hero block render |
| `blue_sage_og_image` | Filter | Override the default OG image URL |
| `blue_sage_jsonld_organization` | Filter | Modify the Organization JSON-LD array |

## License

GPLv2 or later — https://www.gnu.org/licenses/gpl-2.0.html
```

---

### 4.2 Hook Points in Block Render Files

For the child theme hooks to be useful, the parent theme must actually fire them. Add `do_action()` calls to key blocks:

**`blocks/hero/render.php`** — wrap the entire block output:

```php
do_action( 'blue_sage_before_hero', $attributes );
// ... existing output ...
do_action( 'blue_sage_after_hero', $attributes );
```

Apply `blue_sage_jsonld_organization` filter in `inc/seo.php` before encoding:

```php
// In blue_sage_jsonld_global(), before json_encode:
if ( $logo_url ) {
    $org_schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => $site_name,
        'url'      => $site_url,
        'logo'     => [ '@type' => 'ImageObject', 'url' => $logo_url ],
    ];

    /** @param array $org_schema The Organization JSON-LD array. */
    $org_schema = apply_filters( 'blue_sage_jsonld_organization', $org_schema );
    $schemas[]  = $org_schema;
}
```

---

### 4.3 Setup Documentation

Create `docs/SETUP.md` as the primary user-facing setup guide. Cover:

1. **Installation** (FTP upload, WordPress admin upload, WP-CLI)
2. **First-run checklist** (Site Editor > Styles, set site identity, configure SEO Customizer)
3. **Using block patterns** (how to insert a full hero section in one click)
4. **Customizing colors and fonts** (Global Styles panel)
5. **Header variants** (how to switch between 3 layouts via Site Editor)
6. **Custom blocks reference** (one paragraph per block, what attributes it accepts)
7. **Performance tips** (image optimization, caching plugin recommendations)
8. **Child theme usage** (link to child theme README)
9. **Plugin compatibility notes**

---

## Final WCAG 2.1 AA Audit

This is a verification sweep, not new development. Phase 4 resolved most accessibility issues. Phase 5 confirms completeness before launch.

### Automated checks

Run **axe DevTools** browser extension on the following pages:
- Homepage (front-page template)
- Single blog post
- Archive/blog index
- 404 page
- A page with FAQ Accordion block
- A page with Pricing Table block

Target: **0 violations** at WCAG 2.1 AA level.

### Manual checks

| Check | Pass criteria |
|---|---|
| Tab order on homepage | Logical top-to-bottom, no focus traps |
| All modal/overlay elements | Trap focus when open, return on close |
| FAQ accordion | `aria-expanded` toggles correctly; `aria-controls` points to panel |
| Pricing toggle | `role="switch"` or `aria-pressed` reflects state |
| Testimonial carousel | Pause on keyboard focus; dots announce current slide |
| Logo wall marquee | `aria-hidden="true"` on marquee container; static accessible list available |
| Mobile nav overlay | Focus locked to overlay when open; `Escape` closes it |
| Form inputs (newsletter in footer) | Label associated with input; error messages announced |
| Skip-to-content link | Appears on first Tab press; jumps to `#main-content` |
| Colour contrast (spot check) | Body on white ≥ 4.5:1; sage eyebrow on white ≥ 3:1 |

---

## New and Updated Files

```
inc/
  seo.php                       NEW   OG, Twitter Card, canonical, JSON-LD, robots.txt filter
  enqueue.php                   UPDATE  Remove Google Fonts; add font preload; wp_localize_script

assets/
  fonts/                        NEW   8 WOFF2 font files
  js/back-to-top.js             UPDATE  Use BlueSageL10n for aria-label

blocks/
  hero/render.php               UPDATE  do_action hooks before/after

style.css                       UPDATE  §1b: Replace fallback @font-face with self-hosted;
                                        Update :root font stacks

readme.txt                      NEW   WordPress.org submission file

blue-sage-child/
  style.css                     NEW   Child theme header
  functions.php                 NEW   Child theme entry point with documented hooks
  README.md                     NEW   Child theme setup guide

docs/
  SETUP.md                      NEW   End-user setup documentation
```

---

## Implementation Order

Build in this sequence — SEO is highest user value, fonts unlock CDN removal, compliance blocks release:

1. **`inc/seo.php`** — OG + canonical + JSON-LD + robots.txt filter. Add `require_once` to `functions.php`.
2. **Customizer settings** — OG image + org logo in `inc/seo.php`.
3. **Font files** — Download WOFF2 files into `assets/fonts/`.
4. **Self-hosted `@font-face`** — Replace §1b in `style.css`. Remove Google Fonts from `enqueue.php`. Add `blue_sage_preload_fonts()`.
5. **`wp_localize_script()` for back-to-top** — Update `inc/enqueue.php` + `assets/js/back-to-top.js`.
6. **Child theme hooks** — `do_action()` in `blocks/hero/render.php`; `apply_filters()` in `inc/seo.php`.
7. **`blue-sage-child/`** — Create child theme starter package.
8. **`readme.txt`** — Create in theme root.
9. **`screenshot.png`** — Design and export at 1200×900.
10. **Theme Check audit** — Install plugin, resolve all errors and warnings.
11. **Translation audit** — Search bare `echo $var`; wrap strings.
12. **WCAG final audit** — axe DevTools pass + manual checklist.
13. **`docs/SETUP.md`** — Write setup guide.
14. **Package ZIP** — `blue-sage.zip` (theme) + `blue-sage-child.zip` (child starter).

---

## Definition of Done

Phase 5 is complete when all of the following are true:

- [ ] `inc/seo.php` loaded; OG tags present in page `<head>` on singular and front page
- [ ] Canonical `<link>` present on all paginated and singular URLs
- [ ] JSON-LD `WebSite` schema present on every page (validate at schema.org/validator)
- [ ] JSON-LD `Article` schema present on single posts
- [ ] JSON-LD `FAQPage` schema auto-generated on any page with FAQ Accordion block
- [ ] `blue_sage_seo_plugin_active()` suppresses all theme SEO output when Yoast is active
- [ ] robots.txt (via `/?robots=1`) blocks `GPTBot` and `CCBot`; allows `OAI-SearchBot` and `PerplexityBot`
- [ ] No Google Fonts CDN request in page source or Network tab
- [ ] 8 WOFF2 files present in `assets/fonts/`; font preload in `<head>`
- [ ] `font-display: swap` present on all `@font-face` rules
- [ ] WordPress Theme Check plugin: **0 errors, 0 warnings**
- [ ] All user-facing strings wrapped in `__()` or `_e()` with `'blue-sage'` text domain
- [ ] All `echo` output uses correct escape functions (`esc_html`, `esc_url`, `wp_kses_post`)
- [ ] `readme.txt` present in theme root with all required fields
- [ ] `screenshot.png` present at exactly 1200×900 px
- [ ] Child theme starter (`blue-sage-child/`) activates cleanly with no PHP errors
- [ ] Child theme `blue_sage_jsonld_organization` filter is exercisable (confirmed with test snippet)
- [ ] axe DevTools: 0 WCAG 2.1 AA violations on homepage, single post, FAQ page
- [ ] All manual accessibility checks pass (see table above)
- [ ] Lighthouse Accessibility = 100 (re-run after SEO additions to confirm no regression)
- [ ] Lighthouse SEO = 100
- [ ] `docs/SETUP.md` covers all 9 setup topics
- [ ] No PHP notices or warnings with `WP_DEBUG=true` and `WP_DEBUG_LOG=true`
- [ ] Distribution ZIP validated against WordPress.org theme review handbook checklist
