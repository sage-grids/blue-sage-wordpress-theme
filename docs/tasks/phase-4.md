# Phase 4 — Polish & Performance

**Scope:** Week 7
**Status:** Planned
**Depends on:** Phase 3 (Advanced Components) — complete

---

## Overview

Phase 4 is not about new blocks. It is about making every existing block and interaction feel inevitable and effortless. The target: **Lighthouse Performance 95+** on the default homepage demo and **zero jank** across all breakpoints and browsers.

Phase 4 has three distinct streams that can be worked in parallel:

1. **Animation completion** — Hero entrance, parallax, counter decimal fix, `will-change` lifecycle
2. **Micro-interaction layer** — Gaps in the PRD interaction spec not yet implemented
3. **Performance hardening** — Asset loading, image attributes, critical CSS, font strategy

**PRD reference:** Section 4 (Motion & Interaction Design), Section 7.4 (Performance Targets)

---

## What Already Exists (do not re-implement)

Understanding what is already built prevents duplication:

| Feature | Where | Status |
|---|---|---|
| Fade-up / fade-in / stagger scroll animations | `assets/js/scroll-animations.js` + `style.css` §12 | Complete |
| Counter animation (`js-counter`) | `assets/js/scroll-animations.js` | Complete — **decimal values broken** (see §1.1) |
| Sticky header frosted glass | `assets/js/navigation.js` + `style.css` §6 | Complete |
| Active nav link detection | `assets/js/navigation.js` | Complete |
| Reading progress bar | `assets/js/navigation.js` | Complete |
| Button hover `translateY(-1px)` + active reset | `style.css` §8 | Complete |
| Button primary / outline / ghost variants | `style.css` §8 | Complete |
| Nav link underline (slide from left) | `style.css` §7 | Complete |
| Dropdown submenu fade + slide | `style.css` §7 | Complete |
| Mobile overlay slide-in | `style.css` §7 | Complete |
| Form input focus ring (blue border + shadow) | `style.css` §9 | Complete |
| Card hover `translateY(-4px)` | `style.css` §13 | Complete |
| Team grid overlay (CSS hover) | `blocks/team-grid/style.css` | Complete |
| Blog card image zoom on hover | `blocks/blog-cards/style.css` | Complete |
| Logo wall marquee | `blocks/logo-wall/style.css` | Complete |
| Timeline dot ripple | `blocks/process-steps/style.css` | Complete |
| `prefers-reduced-motion` in JS (bail-out) | `scroll-animations.js`, `testimonial-carousel/index.js` | Complete |
| `prefers-reduced-motion` in CSS (wrapped media query) | `style.css` §12, block CSS files | Complete |

---

## Stream 1: Animation System Completion

### 1.1 Fix: Counter Animation Decimal Support

**Problem:** `animateCounter()` in `scroll-animations.js` calls `Math.round()` unconditionally, which renders `4.9` as `5`. The Metrics Bar block stores values like `"4.9"` and `"99.9"`.

**Fix location:** `assets/js/scroll-animations.js`, `animateCounter()` function.

Detect whether `el.dataset.target` contains a decimal point. If so, render each animation frame with one decimal place:

```javascript
function animateCounter( el ) {
    var targetStr = el.dataset.target;
    var target    = parseFloat( targetStr );
    var isDecimal = targetStr.indexOf( '.' ) !== -1;
    var prefix    = el.dataset.prefix || '';
    var suffix    = el.dataset.suffix || '';
    var duration  = 1500;
    var startTime = null;

    if ( isNaN( target ) ) return;

    function step( timestamp ) {
        if ( ! startTime ) startTime = timestamp;

        var elapsed  = timestamp - startTime;
        var progress = Math.min( elapsed / duration, 1 );
        var eased    = 1 - Math.pow( 1 - progress, 3 );
        var current  = eased * target;

        el.textContent = prefix
            + ( isDecimal
                ? current.toFixed( 1 )
                : Math.round( current ).toLocaleString() )
            + suffix;

        if ( progress < 1 ) {
            window.requestAnimationFrame( step );
        }
    }

    window.requestAnimationFrame( step );
}
```

---

### 1.2 Fix: `will-change` Lifecycle Management

**Problem:** `will-change: transform` should be applied before an animation begins and removed immediately after. Setting it permanently on all `.js-fade-up` elements wastes GPU memory.

**Fix location:** `assets/js/scroll-animations.js`.

In `initFadeAnimations()`, set `will-change` on each element before observing it, then remove it in the `transitionend` handler after `.is-visible` is applied:

```javascript
// Set before observing.
elements.forEach( function ( el ) {
    el.style.willChange = 'opacity, transform';
    observer.observe( el );
} );

// In the intersection callback:
entry.target.addEventListener( 'transitionend', function cleanup() {
    entry.target.style.willChange = 'auto';
    entry.target.removeEventListener( 'transitionend', cleanup );
}, { once: true } );
entry.target.classList.add( 'is-visible' );
observer.unobserve( entry.target );
```

Apply the same pattern to stagger children in `initStaggerAnimations()`.

---

### 1.3 New: Hero Entrance Animation

**Problem:** The hero section is already in the viewport on page load, so `IntersectionObserver` never fires for it. The PRD specifies a 600ms page-level entrance. Currently hero elements appear with no animation.

**Solution:** A new JS file adds `.is-page-loaded` to `<html>` after `DOMContentLoaded`. Hero child elements use CSS `transition` keyed to this class, with staggered `transition-delay` values.

**New file: `assets/js/page-entrance.js`**

```javascript
/**
 * Blue Sage — Page Entrance
 *
 * Adds .is-page-loaded to <html> after DOMContentLoaded.
 * Hero children use CSS transitions keyed to this class.
 * Adds the class immediately (no animation) for prefers-reduced-motion.
 */
( function () {
    'use strict';

    function onReady() {
        requestAnimationFrame( function () {
            document.documentElement.classList.add( 'is-page-loaded' );
        } );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', onReady );
    } else {
        onReady();
    }
} )();
```

The script is the same regardless of `prefers-reduced-motion` — the CSS handles the no-animation path by simply not defining any transition for that media query.

**CSS additions to `style.css` — append to Section 12 (Scroll Animations):**

```css
/* ---- Hero entrance (triggered by .is-page-loaded on <html>) ---- */

@media (prefers-reduced-motion: no-preference) {
    .bs-hero__eyebrow,
    .bs-hero__heading,
    .bs-hero__subheading,
    .bs-hero__ctas,
    .bs-hero__media {
        opacity: 0;
        transform: translateY(20px);
        transition:
            opacity   600ms cubic-bezier(0, 0, 0.2, 1),
            transform 600ms cubic-bezier(0, 0, 0.2, 1);
    }

    .is-page-loaded .bs-hero__eyebrow    { opacity: 1; transform: none; transition-delay:  80ms; }
    .is-page-loaded .bs-hero__heading    { opacity: 1; transform: none; transition-delay: 160ms; }
    .is-page-loaded .bs-hero__subheading { opacity: 1; transform: none; transition-delay: 240ms; }
    .is-page-loaded .bs-hero__ctas       { opacity: 1; transform: none; transition-delay: 320ms; }
    .is-page-loaded .bs-hero__media      { opacity: 1; transform: none; transition-delay: 440ms; }
}
```

**Enqueue in `inc/enqueue.php`:**

```php
wp_enqueue_script(
    'blue-sage-page-entrance',
    BLUE_SAGE_URI . '/assets/js/page-entrance.js',
    [],
    BLUE_SAGE_VERSION,
    [ 'strategy' => 'defer', 'in_footer' => false ]
);
```

`'in_footer' => false` is intentional: the script must reach the browser before the first paint so `.is-page-loaded` is applied before any CSS transition can fire. `defer` keeps it non-blocking.

---

### 1.4 New: Hero Parallax

**PRD reference:** Section 4.3 — "Parallax: Subtle 10% parallax on hero background only. Disabled if `prefers-reduced-motion`."

**New file: `assets/js/parallax.js`**

```javascript
/**
 * Blue Sage — Hero Parallax
 *
 * Moves hero background images at 10% of scroll speed.
 * Only runs on elements with data-parallax="true".
 * Disabled for prefers-reduced-motion.
 * Uses passive scroll listener + requestAnimationFrame for performance.
 */
( function () {
    'use strict';

    if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
        return;
    }

    var heroes  = [];
    var ticking = false;

    function collectHeroes() {
        heroes = Array.from( document.querySelectorAll( '.bs-hero[data-parallax]' ) );
    }

    function updateParallax() {
        var scrollY = window.scrollY;

        heroes.forEach( function ( hero ) {
            var rect   = hero.getBoundingClientRect();
            // Only calculate for heroes near or in the viewport.
            if ( rect.bottom < 0 || rect.top > window.innerHeight ) return;
            var offset = ( rect.top + scrollY ) * 0.10;
            hero.style.backgroundPositionY = ( -offset ) + 'px';
        } );

        ticking = false;
    }

    window.addEventListener( 'scroll', function () {
        if ( ! ticking ) {
            window.requestAnimationFrame( updateParallax );
            ticking = true;
        }
    }, { passive: true } );

    function init() {
        collectHeroes();
        if ( heroes.length ) {
            updateParallax();
        }
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }
} )();
```

**Hero block update — `blocks/hero/render.php`:**

Add `data-parallax="true"` to the section wrapper for centered and editorial layouts. Not split layout — that layout uses an inline `<img>`, not a CSS background.

```php
// In the $wrapper_attributes call, add data-parallax for applicable layouts:
$extra = ( 'split' !== $layout ) ? [ 'data-parallax' => 'true' ] : [];
$wrapper_attributes = get_block_wrapper_attributes( array_merge(
    [ 'class' => $section_class ],
    $extra
) );
```

**`blocks/hero/style.css` addition:**

```css
/* Parallax hero: CSS background used for centered + editorial layouts */
.bs-hero--centered[data-parallax],
.bs-hero--editorial[data-parallax] {
    will-change: background-position;
}
```

**Enqueue in `inc/enqueue.php`:**

```php
wp_enqueue_script(
    'blue-sage-parallax',
    BLUE_SAGE_URI . '/assets/js/parallax.js',
    [],
    BLUE_SAGE_VERSION,
    [ 'strategy' => 'defer', 'in_footer' => true ]
);
```

---

## Stream 2: Micro-interaction Layer

### 2.1 Body Content Link Underline

**Problem:** The PRD specifies "Links: underline animates from left to right using a pseudo-element" (Section 4.2). This exists on nav links but not on inline body text links.

**CSS additions to `style.css` — Section 14 (Content Styles):**

```css
/* Animated underline for inline body links */
.entry-content a:not(.wp-block-button__link):not([class*="bs-"]),
.bs-blog__list-title a,
.bs-blog__title a {
    text-decoration: none;
    background-image: linear-gradient(var(--bs-rich-blue), var(--bs-rich-blue));
    background-size: 0% 1px;
    background-repeat: no-repeat;
    background-position: left bottom;
    transition: background-size var(--bs-t-base), color var(--bs-t-fast);
}

.entry-content a:not(.wp-block-button__link):not([class*="bs-"]):hover,
.bs-blog__list-title a:hover,
.bs-blog__title a:hover {
    background-size: 100% 1px;
    color: var(--bs-rich-blue);
}
```

---

### 2.2 Global Focus-Visible Ring

**Problem:** The PRD requires "Visible focus rings (blue outline, not browser default)" for all interactive elements (Section 7.6). Individual blocks define their own `:focus-visible` rules, but there is no global catch-all for elements not yet styled.

**CSS additions to `style.css` — after the existing `.skip-link` block in Section 5:**

```css
/* Global focus-visible: blue ring, not browser default chrome */
:focus-visible {
    outline: 2px solid var(--bs-rich-blue);
    outline-offset: 3px;
    border-radius: 2px;
}

/* Suppress outline for pointer/touch users */
:focus:not(:focus-visible) {
    outline: none;
}

/* Form fields handle focus via border + shadow — suppress double ring */
input:focus-visible,
textarea:focus-visible,
select:focus-visible {
    outline: none;
}
```

---

### 2.3 Back-to-Top Button

**PRD reference:** Section 5.3 Footer — "optional back-to-top button."

**New file: `assets/js/back-to-top.js`**

```javascript
/**
 * Blue Sage — Back to Top
 *
 * Injects a fixed scroll-to-top button that appears after 400px of scroll.
 * Smooth scrolls to top on click. Respects prefers-reduced-motion.
 */
( function () {
    'use strict';

    var SHOW_AT      = 400;
    var reduced      = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
    var ticking      = false;

    function createButton() {
        var btn = document.createElement( 'button' );
        btn.className = 'bs-back-to-top';
        btn.type      = 'button';
        btn.setAttribute( 'aria-label', 'Back to top' );
        btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" '
            + 'aria-hidden="true" focusable="false">'
            + '<path d="M10 15V5M5 10l5-5 5 5" stroke="currentColor" '
            + 'stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>'
            + '</svg>';

        btn.addEventListener( 'click', function () {
            window.scrollTo( { top: 0, behavior: reduced ? 'instant' : 'smooth' } );
        } );

        document.body.appendChild( btn );
        return btn;
    }

    function init() {
        var btn = createButton();

        window.addEventListener( 'scroll', function () {
            if ( ! ticking ) {
                window.requestAnimationFrame( function () {
                    btn.classList.toggle( 'is-visible', window.scrollY > SHOW_AT );
                    ticking = false;
                } );
                ticking = true;
            }
        }, { passive: true } );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }
} )();
```

**CSS additions to `style.css` — new Section 16:**

```css
/* ============================================================
   16. BACK TO TOP
   ============================================================ */

.bs-back-to-top {
    position: fixed;
    bottom: 32px;
    right: 32px;
    z-index: 90;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 1.5px solid var(--bs-gray-200);
    background-color: var(--bs-white);
    color: var(--bs-gray-700);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transform: translateY(12px);
    pointer-events: none;
    transition:
        opacity      var(--bs-t-base),
        transform    var(--bs-t-base),
        background   var(--bs-t-fast),
        border-color var(--bs-t-fast),
        color        var(--bs-t-fast);
    box-shadow: var(--bs-shadow-sm);
}

.bs-back-to-top.is-visible {
    opacity: 1;
    transform: none;
    pointer-events: auto;
}

.bs-back-to-top:hover {
    background-color: var(--bs-rich-blue);
    border-color: var(--bs-rich-blue);
    color: var(--bs-white);
    box-shadow: var(--bs-shadow-blue-sm);
}

.bs-back-to-top:focus-visible {
    outline: 2px solid var(--bs-rich-blue);
    outline-offset: 3px;
}

@media (max-width: 767px) {
    .bs-back-to-top {
        bottom: 20px;
        right: 20px;
        width: 40px;
        height: 40px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .bs-back-to-top {
        transition: opacity var(--bs-t-fast);
        transform: none;
    }
}
```

**Enqueue in `inc/enqueue.php`:**

```php
wp_enqueue_script(
    'blue-sage-back-to-top',
    BLUE_SAGE_URI . '/assets/js/back-to-top.js',
    [],
    BLUE_SAGE_VERSION,
    [ 'strategy' => 'defer', 'in_footer' => true ]
);
```

---

### 2.4 Accessibility Utility Classes

Add to `style.css` as a new Section 15 (before Section 16 back-to-top):

```css
/* ============================================================
   15. ACCESSIBILITY
   ============================================================ */

/* Visually hidden but available to screen readers */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.sr-only-focusable:focus {
    position: static;
    width: auto;
    height: auto;
    overflow: visible;
    clip: auto;
    white-space: normal;
}
```

---

## Stream 3: Performance Hardening

### 3.1 New File: `inc/performance.php`

Centralises all WordPress performance hooks. Add `require_once BLUE_SAGE_DIR . '/inc/performance.php';` to `functions.php`.

```php
<?php
/**
 * Blue Sage — Performance Optimisations
 *
 * Removes unused WordPress head outputs, disables emojis,
 * and handles LCP image prioritisation.
 *
 * @package BlueSage
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Remove the WordPress version query string from enqueued asset URLs.
 * Allows CDN/proxy caches to hold assets longer.
 */
function blue_sage_remove_version_strings( string $src ): string {
    if ( strpos( $src, '?ver=' ) !== false ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}
add_filter( 'style_loader_src',  'blue_sage_remove_version_strings' );
add_filter( 'script_loader_src', 'blue_sage_remove_version_strings' );

/**
 * Remove unused WordPress head outputs.
 */
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

/**
 * Disable emoji scripts and styles — not used in this theme.
 */
function blue_sage_disable_emojis(): void {
    remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script'    );
    remove_action( 'wp_print_styles',     'print_emoji_styles'              );
    remove_action( 'admin_print_styles',  'print_emoji_styles'              );
    remove_filter( 'the_content_feed',    'wp_staticize_emoji'              );
    remove_filter( 'comment_text_rss',    'wp_staticize_emoji'              );
    remove_filter( 'wp_mail',             'wp_staticize_emoji_for_email'    );
}
add_action( 'init', 'blue_sage_disable_emojis' );

/**
 * Add fetchpriority="high" and loading="eager" to the first content image
 * on singular pages. WordPress 6.3+ does this for featured images automatically,
 * but block content images still need it.
 *
 * Runs after the_content filters so it catches block-rendered output.
 *
 * @param  string $content Post content HTML.
 * @return string
 */
function blue_sage_prioritise_lcp_image( string $content ): string {
    if ( is_admin() || ! is_singular() ) {
        return $content;
    }

    // Already handled — do not double-apply.
    if ( strpos( $content, 'fetchpriority' ) !== false ) {
        return $content;
    }

    // Swap loading="lazy" → loading="eager" and add fetchpriority on first <img>.
    $content = preg_replace(
        '/(<img\b[^>]*?)(\s+loading=["\']lazy["\'])([^>]*?>)/i',
        '$1 loading="eager" fetchpriority="high"$3',
        $content,
        1
    );

    return $content;
}
add_filter( 'the_content', 'blue_sage_prioritise_lcp_image', 20 );
```

---

### 3.2 Hero Block: LCP Image Priority

**File: `blocks/hero/render.php`**

The hero split layout image is almost always the LCP element on the front page. Mark it with `fetchpriority="high"` and `loading="eager"` when rendering on the front page.

In the render.php, before the split layout `<img>` tag:

```php
$is_lcp   = is_front_page();
$img_load = $is_lcp ? 'eager' : 'lazy';
$img_fp   = $is_lcp ? ' fetchpriority="high"' : '';
```

Then in the img tag output:
```php
<img
    class="bs-hero__img"
    src="<?php echo esc_url( $media_url ); ?>"
    alt="<?php echo esc_attr( $media_alt ); ?>"
    loading="<?php echo esc_attr( $img_load ); ?>"
    width="800"
    height="600"
    <?php echo $img_fp; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
>
```

---

### 3.3 Blog Cards: Explicit Image Dimensions

**Problem:** `the_post_thumbnail()` does not include `width` and `height` attributes unless WordPress can resolve them from the database. Missing dimensions cause CLS.

**Fix in `blocks/blog-cards/render.php`:** Replace `the_post_thumbnail()` calls with `wp_get_attachment_image()`, which always includes dimensions and `srcset`:

```php
// Standard card:
$thumb_id = get_post_thumbnail_id();
if ( $thumb_id ) {
    echo wp_get_attachment_image( $thumb_id, 'medium_large', false, [
        'class'   => 'bs-blog__thumb-img',
        'loading' => 'lazy',
    ] );
}

// Featured card:
$thumb_id = get_post_thumbnail_id();
if ( $thumb_id ) {
    echo wp_get_attachment_image( $thumb_id, 'large', false, [
        'class'   => 'bs-blog__featured-img',
        'loading' => $is_lcp ? 'eager' : 'lazy',
    ] );
}
```

---

### 3.4 Font Fallback Metrics

**Problem:** When Google Fonts loads, the browser swaps the web font for the system fallback. If the metrics differ, visible layout shift occurs (CLS). `font-display: swap` is already in the Google Fonts URL but no local fallback metrics are defined.

**CSS additions to `style.css` — insert between `:root {}` and `/* 2. RESET */`:**

```css
/* ============================================================
   1b. FONT FALLBACK METRICS
   Reduces CLS during web font swap by sizing the local fallback
   to approximate the web font's metrics.
   Source: https://screenspan.net/fallback
   Phase 5: remove when fonts are self-hosted.
   ============================================================ */

@font-face {
    font-family: 'Inter-Fallback';
    src: local('Arial');
    ascent-override:   90.49%;
    descent-override:  22.56%;
    line-gap-override:  0.00%;
    size-adjust:       107.64%;
}

@font-face {
    font-family: 'Plus-Jakarta-Sans-Fallback';
    src: local('Arial');
    ascent-override:   96.52%;
    descent-override:  22.44%;
    line-gap-override:  0.00%;
    size-adjust:       102.11%;
}
```

**Update font stacks in `:root`:**

```css
--bs-font-display: 'Plus Jakarta Sans', 'Plus-Jakarta-Sans-Fallback', system-ui, -apple-system, sans-serif;
--bs-font-body:    'Inter', 'Inter-Fallback', system-ui, -apple-system, sans-serif;
```

---

### 3.5 Logo Wall: Numeric Image Width

**Problem:** `blocks/logo-wall/render.php` outputs `width="auto"` on logo images. `auto` is not a valid HTML attribute value for `width` — it must be a number or omitted entirely. Omitting it causes CLS.

**Fix in `blocks/logo-wall/render.php`:** Change `width="auto"` to `width="140"`.

---

## Updated `inc/enqueue.php` — New Scripts

After all Stream 1 and Stream 2 additions, three new `wp_enqueue_script()` calls are added:

```php
// Page entrance animation (in <head>, non-blocking).
wp_enqueue_script(
    'blue-sage-page-entrance',
    BLUE_SAGE_URI . '/assets/js/page-entrance.js',
    [],
    BLUE_SAGE_VERSION,
    [ 'strategy' => 'defer', 'in_footer' => false ]
);

// Hero parallax.
wp_enqueue_script(
    'blue-sage-parallax',
    BLUE_SAGE_URI . '/assets/js/parallax.js',
    [],
    BLUE_SAGE_VERSION,
    [ 'strategy' => 'defer', 'in_footer' => true ]
);

// Back to top.
wp_enqueue_script(
    'blue-sage-back-to-top',
    BLUE_SAGE_URI . '/assets/js/back-to-top.js',
    [],
    BLUE_SAGE_VERSION,
    [ 'strategy' => 'defer', 'in_footer' => true ]
);
```

---

## New and Updated Files

```
assets/js/
  page-entrance.js          NEW   Hero entrance animation trigger
  parallax.js               NEW   10% hero background parallax
  back-to-top.js            NEW   Scroll-to-top button

inc/
  performance.php           NEW   WP head cleanup, emoji removal, LCP filter
  functions.php             UPDATE  require_once performance.php

blocks/
  hero/render.php           UPDATE  data-parallax attr + fetchpriority on split image
  blog-cards/render.php     UPDATE  wp_get_attachment_image() with dimensions
  logo-wall/render.php      UPDATE  width="140" (not "auto") on logo imgs

assets/js/
  scroll-animations.js      UPDATE  decimal counter fix + will-change lifecycle

style.css                   UPDATE  font fallback @font-face, font stack in :root,
                                    hero entrance CSS, body link underline,
                                    global focus-visible rule, §15 accessibility,
                                    §16 back-to-top

inc/enqueue.php             UPDATE  three new wp_enqueue_script() calls
```

---

## Implementation Order

Build in this sequence to maximise early Lighthouse wins:

1. **`inc/performance.php`** — Largest single Lighthouse gain. Emoji removal, head cleanup, LCP image filter. Update `functions.php` to require it.
2. **Font fallback `@font-face`** — CLS fix. Add to `style.css`, update `:root` font stacks.
3. **Blog cards `wp_get_attachment_image()`** — CLS fix. Update `render.php`.
4. **Logo wall width fix** — Minor CLS fix.
5. **Hero `fetchpriority`** — LCP fix. Update `blocks/hero/render.php`.
6. **Counter decimal fix** — Correctness. Update `scroll-animations.js`.
7. **`will-change` lifecycle** — GPU memory. Update `scroll-animations.js`.
8. **`page-entrance.js`** — New file. Add hero entrance CSS to `style.css`. Update `enqueue.php`.
9. **`parallax.js`** — New file. Update hero `render.php` for `data-parallax`. Update `enqueue.php`.
10. **Body link underline** — CSS only. Add to `style.css` §14.
11. **Global `:focus-visible`** — CSS only. Add to `style.css` §5.
12. **Accessibility classes** — CSS only. Add `style.css` §15.
13. **`back-to-top.js`** — New file + CSS. Update `enqueue.php`.
14. **Run full testing checklist.**

---

## Cross-Browser & Mobile Testing Checklist

### Browsers (latest 2 versions)

| Test | Chrome | Firefox | Safari | Edge |
|---|---|---|---|---|
| Hero entrance animation fires on page load | | | | |
| Hero entrance skipped with `prefers-reduced-motion` | | | | |
| Parallax animates smoothly, no jank at 60fps | | | | |
| Parallax off with `prefers-reduced-motion` | | | | |
| Sticky header backdrop-filter renders (note: requires `-webkit-` prefix on Safari — already in §3.6 above) | | | | |
| FAQ accordion opens/closes (keyboard + click) | | | | |
| Pricing toggle switches prices | | | | |
| Logo wall marquee animates and pauses on hover | | | | |
| Counter counts integers correctly (`10,000+`) | | | | |
| Counter counts decimal correctly (`4.9/5`) | | | | |
| Back-to-top appears after scroll, smooth-scrolls to top | | | | |
| Focus ring visible on buttons, links, inputs, accordion triggers | | | | |
| Blog card hover lift with no layout shift | | | | |
| Body link underline animates left-to-right on hover | | | | |

### Viewports

| Viewport | Test |
|---|---|
| 375px iPhone | No horizontal scroll; tap targets ≥ 44px; mobile nav slide-in works |
| 390px iPhone 15 Pro | Same as above |
| 768px iPad | Pricing 1-col; feature grids 2-col; footer 2-col |
| 1024px laptop | Pricing grid correct; back-to-top visible |
| 1280px desktop | Full design renders as designed |
| 1440px+ | Content stays within max-width; no excess white space |

### Lighthouse Targets

Run in Chrome DevTools Incognito, simulated throttling (4G), on the default homepage demo.

| Metric | Target |
|---|---|
| Performance | ≥ 95 |
| Accessibility | 100 |
| Best Practices | ≥ 95 |
| SEO | 100 |
| LCP | < 2.0s |
| INP | < 150ms |
| CLS | < 0.05 |
| FCP | < 1.2s |
| Total page weight (transferred) | < 400 kB |

### Accessibility Spot Checks

- [ ] Tab through homepage without mouse — every interactive element reachable and visibly focused
- [ ] All non-decorative images have descriptive `alt` text; decorative images have `alt=""`
- [ ] Body text on white: contrast ≥ 4.5:1
- [ ] White text on Rich Blue button: contrast ≥ 4.5:1
- [ ] Off-white text on Deep Navy sections: contrast ≥ 4.5:1
- [ ] Sage green eyebrow on white background: contrast ≥ 3:1
- [ ] `prefers-reduced-motion`: no scroll animations, no entrance animation, no parallax, no marquee animation
- [ ] VoiceOver (macOS): hero reads eyebrow → heading → subheading → CTA
- [ ] VoiceOver: FAQ accordion announces `expanded` / `collapsed` state change
- [ ] VoiceOver: pricing toggle announces `aria-pressed` state change
- [ ] Back-to-top button announces label "Back to top" to screen reader

---

## Definition of Done

Phase 4 is complete when all of the following are true:

- [ ] Lighthouse Performance ≥ 95 on homepage demo (Chrome, throttled 4G, Incognito)
- [ ] Lighthouse Accessibility = 100
- [ ] Lighthouse Best Practices ≥ 95, SEO = 100
- [ ] LCP < 2.0s, CLS < 0.05, FCP < 1.2s on homepage
- [ ] Counter renders `4.9` not `5` on Metrics Bar with decimal values
- [ ] Hero elements animate in on page load; skipped with `prefers-reduced-motion`
- [ ] Parallax moves hero background at ~10% of scroll; disabled with `prefers-reduced-motion`
- [ ] Back-to-top appears after 400px scroll, smooth-scrolls to top
- [ ] Body text links in `.entry-content` have left-to-right underline on hover
- [ ] All interactive elements show a blue `:focus-visible` ring
- [ ] Font fallback `@font-face` metrics defined; no visible font-swap layout shift
- [ ] Hero split layout image has `fetchpriority="high"` on front page
- [ ] Blog card images output via `wp_get_attachment_image()` with dimensions
- [ ] Logo wall images have numeric `width` attribute
- [ ] `inc/performance.php` loaded; emoji scripts absent from page source
- [ ] No `console.error` on any page
- [ ] No PHP warnings with `WP_DEBUG` enabled
- [ ] No horizontal overflow at 375px
- [ ] All tap targets ≥ 44px on mobile
- [ ] Full cross-browser matrix passes: Chrome, Firefox, Safari, Edge
