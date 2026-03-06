# Phase 2 — Core Components

**Scope:** Week 3–4
**Status:** Planned
**Depends on:** Phase 1 (Foundation) — complete

---

## Overview

Phase 2 delivers the six core section components that make up 80% of a typical Blue Sage page. Every component is built as a native WordPress block with PHP server-side rendering — no JavaScript build step required. Components are also registered as curated block patterns so users can insert fully-styled sections in one click.

**Deliverables:**
1. Five custom blocks: Hero, Feature Grid, CTA Section, Testimonial Carousel, Metrics Bar
2. Supporting infrastructure: `inc/custom-blocks.php`, `inc/block-patterns.php`
3. Ten block patterns using the new blocks (+ core blocks for pattern variety)
4. CSS for all block variants in `assets/css/components/`
5. Carousel JS for the Testimonial block

---

## Technical Architecture Decision

**Approach: PHP server-side rendering with inner blocks**

Each block uses `render.php` (server-side render callback) — no JS build step, no webpack, no node_modules. The block editor renders an editable inner block template; the front end uses the PHP callback for clean, cacheable output.

Why not patterns-only:
- Blocks expose Inspector Controls (layout switcher, color options, column count) that patterns cannot
- Blocks can be updated globally; patterns fork on insertion
- The PRD explicitly requires `register_block_type()`

Why not JS-compiled blocks:
- Zero build tooling = faster development, no CI complexity
- PHP rendering is fully SEO-friendly and works without JS
- The only exceptions are the Testimonial carousel and Metrics counter, which ship a small vanilla JS file per block

**Block structure per component:**
```
blocks/
  {block-name}/
    block.json       ← block metadata, attributes, supports
    render.php       ← server-side render callback
    style.css        ← front-end block styles (auto-enqueued)
    editor.css       ← editor-only overrides (optional)
    index.js         ← only for carousel + counter blocks
```

**Naming convention:** `blue-sage/{block-name}` (e.g. `blue-sage/hero`)

---

## Infrastructure: New Files

### `inc/custom-blocks.php`

Registers all custom blocks and enqueues per-block assets.

```php
function blue_sage_register_blocks(): void {
    $blocks = [
        'hero',
        'feature-grid',
        'cta-section',
        'testimonial-carousel',
        'metrics-bar',
    ];

    foreach ( $blocks as $block ) {
        register_block_type( BLUE_SAGE_DIR . '/blocks/' . $block );
    }
}
add_action( 'init', 'blue_sage_register_blocks' );
```

Add `require_once BLUE_SAGE_DIR . '/inc/custom-blocks.php';` to `functions.php`.

### `inc/block-patterns.php`

Registers all block patterns and pattern categories.

```php
function blue_sage_register_pattern_categories(): void {
    $categories = [
        'blue-sage-heroes'       => __( 'Blue Sage: Heroes',       'blue-sage' ),
        'blue-sage-features'     => __( 'Blue Sage: Features',     'blue-sage' ),
        'blue-sage-social-proof' => __( 'Blue Sage: Social Proof', 'blue-sage' ),
        'blue-sage-cta'          => __( 'Blue Sage: CTA',          'blue-sage' ),
    ];

    foreach ( $categories as $slug => $label ) {
        register_block_pattern_category( $slug, [ 'label' => $label ] );
    }
}
add_action( 'init', 'blue_sage_register_pattern_categories' );

function blue_sage_register_patterns(): void {
    $patterns = glob( BLUE_SAGE_DIR . '/patterns/*.php' );
    foreach ( $patterns as $file ) {
        require $file;
    }
}
add_action( 'init', 'blue_sage_register_patterns' );
```

Add `require_once BLUE_SAGE_DIR . '/inc/block-patterns.php';` to `functions.php`.

---

## Block 1: Hero (`blue-sage/hero`)

**PRD reference:** Section 5.1, "Hero (3 layouts)"

### Attributes

| Attribute     | Type    | Default      | Options                          |
|---------------|---------|--------------|----------------------------------|
| `layout`      | string  | `'centered'` | `'centered'`, `'split'`, `'editorial'` |
| `darkBg`      | boolean | `true`       | —                                |
| `eyebrow`     | string  | `''`         | —                                |
| `heading`     | string  | `''`         | —                                |
| `subheading`  | string  | `''`         | —                                |
| `primaryCta`  | object  | `{}`         | `{ label, url, newTab }`         |
| `secondaryCta`| object  | `{}`         | `{ label, url, newTab }`         |
| `mediaId`     | integer | `0`          | Image attachment ID (split layout) |
| `mediaUrl`    | string  | `''`         | —                                |
| `mediaAlt`    | string  | `''`         | —                                |

### Inspector Controls

- **Layout panel:** Radio/SegmentedControl: Centered / Split / Editorial
- **Background panel:** Toggle "Dark Background" (shows Deep Navy vs White)
- **Content panel:** Eyebrow text input, gradient text toggle on heading
- **Media panel** (split only): Image picker via MediaUpload
- **CTA panel:** Primary label + URL, secondary label + URL

### Layout A — Centered Statement

```
[Full-width section — Deep Navy bg]
  [Container — max 800px text, centered]
    [eyebrow label — sage green, uppercase, 12px]
    [H1 — white, display size, gradient on key word optional]
    [subheading — 18px, rgba(255,255,255,0.75), max 2 lines]
    [CTA group — primary blue btn + ghost btn, gap 12px]
    [optional: abstract geometric SVG or image below, fade-up]
```

CSS class: `.bs-hero--centered`
Dark bg applied via `has-deep-navy-background-color` on the wrapper group.

### Layout B — Split

```
[Full-width section — White bg]
  [Container — 7/5 col grid]
    [Left col — 7/12]
      [eyebrow]
      [H1 — near-black]
      [subheading]
      [CTA group]
    [Right col — 5/12]
      [Image — rounded 16px, shadow-blue]
```

CSS class: `.bs-hero--split`
On mobile: stacks vertically, image moves below text.

### Layout C — Editorial

```
[Full-width section — White bg]
  [Container — full-width, no max]
    [Large eyebrow — sage green, 14px, left-aligned]
    [H1 — MASSIVE, clamp(56px,9vw,120px), tracks full width]
      [Line 2 — indented 80px, creates staircase effect]
    [Bottom row — thin rule + 18px subtext left, CTA right]
```

CSS class: `.bs-hero--editorial`
Typography: `font-size: clamp(56px, 9vw, 120px)`, `letter-spacing: -0.05em`, `line-height: 0.95`

### Files to create

```
blocks/hero/
  block.json
  render.php
  style.css         ← all 3 layout variants
  editor.css        ← layout preview in editor
```

### CSS classes

```
.bs-hero                    ← base wrapper
.bs-hero--centered          ← layout A
.bs-hero--split             ← layout B
.bs-hero--editorial         ← layout C
.bs-hero__eyebrow           ← eyebrow label
.bs-hero__heading           ← main heading
.bs-hero__subheading        ← supporting text
.bs-hero__ctas              ← button group
.bs-hero__media             ← image wrapper (split)
.bs-hero__editorial-line    ← each line in editorial layout
```

---

## Block 2: Feature Grid (`blue-sage/feature-grid`)

**PRD reference:** Section 5.1, "Feature Grid (3 layouts)"

### Attributes

| Attribute     | Type    | Default       | Options                                       |
|---------------|---------|---------------|-----------------------------------------------|
| `layout`      | string  | `'icon-grid'` | `'icon-grid'`, `'alternating'`, `'large-cards'` |
| `eyebrow`     | string  | `''`          | —                                             |
| `heading`     | string  | `''`          | —                                             |
| `subheading`  | string  | `''`          | —                                             |
| `items`       | array   | `[]`          | Array of feature item objects (see below)     |
| `columns`     | integer | `3`           | `2`, `3` (icon-grid only)                     |

**Feature item object:**
```json
{
  "icon": "string (SVG markup or icon name)",
  "title": "string",
  "body": "string",
  "mediaId": 0,
  "mediaUrl": "string",
  "mediaAlt": "string",
  "accent": "blue"
}
```

### Inspector Controls

- **Layout panel:** SegmentedControl: Icon Grid / Alternating / Large Cards
- **Section header panel:** Eyebrow, heading, subheading text fields
- **Columns panel** (icon-grid only): 2 or 3 columns
- **Items panel:** Repeater for adding/removing/reordering feature items

### Layout A — Icon Grid

```
[Section — off-white or white bg]
  [Section header — centered, eyebrow + H2 + subheading]
  [Grid — 3col desktop / 2col tablet / 1col mobile]
    [Feature item]
      [Icon box — 48px, blue-tinted bg, rounded-md]
      [Title — H4]
      [Body — 15px, gray-700]
```

Thin `1px solid gray-100` separator lines between rows (not between columns).
CSS class: `.bs-features--icon-grid`

### Layout B — Alternating Split

```
[Section — white bg]
  [Section header]
  [Feature rows — alternate image/text sides]
    [Feature row — even: image left, text right]
      [Image — 5/12 col, rounded-md, shadow-md]
      [Text — 5/12 col, with 4px left blue accent bar]
        [eyebrow label]
        [H3]
        [body]
        [optional CTA link]
    [Feature row — odd: text left, image right]
      (mirrored)
```

Blue vertical accent bar: `border-left: 4px solid var(--bs-rich-blue); padding-left: 24px`
CSS class: `.bs-features--alternating`

### Layout C — Large Card Grid

```
[Section — white bg]
  [Section header]
  [2-col grid, equal height]
    [Card A — blue bg (#1E4FD8), white text, large]
      [Icon — white]
      [H3 — white]
      [body — rgba(255,255,255,0.8)]
    [Card B — white bg, dark text, subtle border]
      [Icon — blue]
      [H3]
      [body]
```

Cards are tall and visually heavy. One blue, one white — creates contrast-driven hierarchy.
CSS class: `.bs-features--large-cards`

### Files to create

```
blocks/feature-grid/
  block.json
  render.php
  style.css
```

### CSS classes

```
.bs-features
.bs-features--icon-grid
.bs-features--alternating
.bs-features--large-cards
.bs-features__header
.bs-features__grid
.bs-features__item
.bs-features__item-icon
.bs-features__item-title
.bs-features__item-body
.bs-features__row          ← alternating layout
.bs-features__row-media
.bs-features__row-text
.bs-features__large-card
.bs-features__large-card--blue
```

---

## Block 3: CTA Section (`blue-sage/cta-section`)

**PRD reference:** Section 5.1, "CTA Section (2 layouts)"

### Attributes

| Attribute      | Type    | Default       | Options                      |
|----------------|---------|---------------|------------------------------|
| `layout`       | string  | `'full-dark'` | `'full-dark'`, `'inline-box'` |
| `eyebrow`      | string  | `''`          | —                            |
| `heading`      | string  | `''`          | —                            |
| `subtext`      | string  | `''`          | —                            |
| `primaryCta`   | object  | `{}`          | `{ label, url }`             |
| `secondaryCta` | object  | `{}`          | `{ label, url }`             |
| `glowEffect`   | boolean | `false`       | Full-dark layout only        |

### Inspector Controls

- **Layout panel:** Radio: Full-width Dark / Inline Box
- **Content panel:** Eyebrow, heading, subtext
- **CTAs panel:** Primary + secondary label/URL
- **Effects panel** (full-dark only): Toggle radial glow behind heading

### Layout A — Full-width Dark

```
[Full-width — Deep Navy bg, optional radial glow]
  [Container — centered, max 680px]
    [eyebrow — sage green]
    [H2 — white, large]
    [subtext — rgba white 0.7]
    [CTA row — primary white-on-blue + ghost white-border]
```

Glow effect: `radial-gradient(ellipse 60% 40% at 50% 0%, rgba(30,79,216,0.3) 0%, transparent 70%)` as pseudo-element behind heading.
CSS class: `.bs-cta--full-dark`

### Layout B — Inline Box

```
[Section — white bg]
  [Container]
    [Box — white, border: 1px solid rgba(30,79,216,0.15), border-radius 16px, padding 48px]
      [Two-column: text left (8/12), CTAs right (4/12, flex-end, centered)]
        [Left: eyebrow + H3 + subtext]
        [Right: stacked primary + ghost buttons]
```

On mobile: stacks vertically, CTAs full-width below text.
CSS class: `.bs-cta--inline-box`

### Files to create

```
blocks/cta-section/
  block.json
  render.php
  style.css
```

---

## Block 4: Testimonial Carousel (`blue-sage/testimonial-carousel`)

**PRD reference:** Section 5.1, "Testimonial Carousel"

### Attributes

| Attribute      | Type    | Default | Notes                          |
|----------------|---------|---------|-------------------------------|
| `eyebrow`      | string  | `''`    | —                              |
| `heading`      | string  | `''`    | —                              |
| `items`        | array   | `[]`    | Array of testimonial objects   |
| `autoplay`     | boolean | `true`  | Auto-advances every 5s         |
| `autoplayDelay`| integer | `5000`  | ms between slides              |
| `darkBg`       | boolean | `false` | Navy background variant        |

**Testimonial item object:**
```json
{
  "quote": "string",
  "authorName": "string",
  "authorTitle": "string",
  "authorCompany": "string",
  "avatarId": 0,
  "avatarUrl": "string",
  "logoId": 0,
  "logoUrl": "string",
  "logoAlt": "string"
}
```

### Inspector Controls

- **Content panel:** Eyebrow, heading
- **Items panel:** Repeater — add/remove/reorder testimonials. Each item: quote textarea, name, title, company, avatar picker, logo picker
- **Behavior panel:** Autoplay toggle, delay input (1000–10000ms)
- **Style panel:** Light / Dark background toggle

### HTML Structure (rendered by PHP)

```html
<section class="bs-testimonials" data-autoplay="true" data-delay="5000">
  <div class="bs-testimonials__header">
    <span class="bs-eyebrow">...</span>
    <h2>...</h2>
  </div>
  <div class="bs-testimonials__track" role="list">
    <div class="bs-testimonial is-active" role="listitem">
      <blockquote class="bs-testimonial__quote">
        <svg class="bs-testimonial__mark" ...><!-- quote SVG --></svg>
        <p>...</p>
        <footer class="bs-testimonial__author">
          <img class="bs-testimonial__avatar" ...>
          <div class="bs-testimonial__info">
            <cite class="bs-testimonial__name">...</cite>
            <span class="bs-testimonial__role">Title, Company</span>
          </div>
          <img class="bs-testimonial__logo" ...>
        </footer>
      </blockquote>
    </div>
    <!-- additional testimonials -->
  </div>
  <div class="bs-testimonials__dots" role="tablist" aria-label="Testimonials">
    <button class="bs-testimonials__dot is-active" role="tab" ...></button>
  </div>
</section>
```

### Carousel JS (`blocks/testimonial-carousel/index.js`)

**Behavior:**
- Class toggling: `.is-active` on current slide, `.is-leaving` on outgoing
- Transition: crossfade (`opacity` 0→1) over 400ms
- Dot indicators: clicking a dot jumps to that slide
- Keyboard: Left/Right arrow navigation when focused
- Autoplay: `setInterval`, paused on hover/focus (`mouseenter`, `focusin`)
- Reduced motion: disables crossfade, keeps dot navigation

**Key functions:**
```javascript
initCarousel(el)        // Set up one carousel instance
goTo(index)             // Navigate to slide by index
startAutoplay()         // setInterval wrapper
stopAutoplay()          // clearInterval wrapper
updateDots(index)       // Sync dot states
handleKeydown(e)        // Arrow key navigation
```

### Files to create

```
blocks/testimonial-carousel/
  block.json
  render.php
  style.css
  index.js
```

---

## Block 5: Metrics Bar (`blue-sage/metrics-bar`)

**PRD reference:** Section 5.1, "Metrics Bar"

### Attributes

| Attribute   | Type    | Default | Notes                       |
|-------------|---------|---------|------------------------------|
| `eyebrow`   | string  | `''`    | Optional, above the stats    |
| `items`     | array   | `[]`    | 2–4 stat objects             |
| `darkBg`    | boolean | `true`  | Navy bg is the default look  |

**Stat item object:**
```json
{
  "value": "string",
  "prefix": "string",
  "suffix": "string",
  "label": "string"
}
```

Notes on `value`: Store as string to support decimals ("4.9") and large numbers ("10000"). The JS counter reads `data-target` and animates from 0.

### Inspector Controls

- **Content panel:** Eyebrow text
- **Items panel:** Repeater — add/remove stat items (value, prefix, suffix, label). Max 4.
- **Style panel:** Dark / Light background toggle

### HTML Structure

```html
<section class="bs-metrics has-deep-navy-background-color">
  <div class="bs-metrics__inner">
    <div class="bs-metrics__grid js-stagger">
      <div class="bs-metrics__item">
        <span class="bs-metrics__number js-counter" data-target="10000" data-prefix="" data-suffix="+">10,000+</span>
        <span class="bs-metrics__label">Happy Customers</span>
      </div>
      <!-- divider between items -->
      <div class="bs-metrics__divider" aria-hidden="true"></div>
      <!-- ... -->
    </div>
  </div>
</section>
```

The `js-counter` class hooks into the existing `scroll-animations.js` counter system — no new JS file needed.

### Files to create

```
blocks/metrics-bar/
  block.json
  render.php
  style.css
```

### CSS

```
.bs-metrics                ← full-width wrapper
.bs-metrics__inner         ← max-width container
.bs-metrics__grid          ← flex row, space-between
.bs-metrics__item          ← text-center stat
.bs-metrics__number        ← display-size numeral (white or blue)
.bs-metrics__label         ← small descriptor text
.bs-metrics__divider       ← thin vertical separator line between items
```

Responsive: grid collapses to 2×2 on tablet, 1-col on mobile.

---

## Block Patterns

Ten patterns to register in `patterns/`. Each file calls `register_block_pattern()`.

### Pattern: Hero Centered Dark (`blue-sage-heroes/hero-centered`)

- Uses `blue-sage/hero` with `layout=centered`, `darkBg=true`
- Pre-filled with demo copy: "Build something that matters", sage green eyebrow "New in 2026"
- Two CTAs: "Get Started Free" + "See the Demo"

### Pattern: Hero Split Light (`blue-sage-heroes/hero-split`)

- Uses `blue-sage/hero` with `layout=split`, `darkBg=false`
- Pre-filled with placeholder image, split layout demo copy

### Pattern: Hero Editorial (`blue-sage-heroes/hero-editorial`)

- Uses `blue-sage/hero` with `layout=editorial`
- Demo copy: oversized headline across 2 lines

### Pattern: Features Icon Grid (`blue-sage-features/icon-grid`)

- Uses `blue-sage/feature-grid` with `layout=icon-grid`, 3 columns
- 6 pre-filled feature items with SVG icons (speed, security, analytics, integrations, support, customization)

### Pattern: Features Alternating (`blue-sage-features/alternating`)

- Uses `blue-sage/feature-grid` with `layout=alternating`
- 3 alternating rows with placeholder images

### Pattern: Features Large Cards (`blue-sage-features/large-cards`)

- Uses `blue-sage/feature-grid` with `layout=large-cards`
- 2 cards: one blue "Core Feature", one white "Secondary Feature"

### Pattern: CTA Full Dark (`blue-sage-cta/full-dark`)

- Uses `blue-sage/cta-section` with `layout=full-dark`, `glowEffect=true`
- Demo copy: "Ready to get started?"

### Pattern: CTA Inline Box (`blue-sage-cta/inline-box`)

- Uses `blue-sage/cta-section` with `layout=inline-box`
- Used as mid-page break pattern

### Pattern: Testimonials (`blue-sage-social-proof/testimonials`)

- Uses `blue-sage/testimonial-carousel` with 3 pre-filled testimonials

### Pattern: Metrics Bar (`blue-sage-social-proof/metrics`)

- Uses `blue-sage/metrics-bar` with 4 sample stats: customers, uptime, integrations, NPS

---

## File Structure After Phase 2

```
blocks/
  hero/
    block.json
    render.php
    style.css
    editor.css
  feature-grid/
    block.json
    render.php
    style.css
  cta-section/
    block.json
    render.php
    style.css
  testimonial-carousel/
    block.json
    render.php
    style.css
    index.js
  metrics-bar/
    block.json
    render.php
    style.css

inc/
  custom-blocks.php      ← NEW
  block-patterns.php     ← NEW
  setup.php
  enqueue.php
  helpers.php

patterns/
  hero-centered.php      ← NEW
  hero-split.php         ← NEW
  hero-editorial.php     ← NEW
  features-icon-grid.php ← NEW
  features-alternating.php ← NEW
  features-large-cards.php ← NEW
  cta-dark.php           ← NEW
  cta-inline.php         ← NEW
  testimonials.php       ← NEW
  metrics.php            ← NEW
```

---

## Implementation Order

Build in this sequence to ship the most impactful components first and unblock pattern creation:

1. **Infrastructure** — `inc/custom-blocks.php` + `inc/block-patterns.php` + `functions.php` updates
2. **Metrics Bar** — simplest block, hooks into existing counter JS, validates the block registration pattern
3. **CTA Section** — second simplest, no JS, 2 layout variants
4. **Feature Grid** — medium complexity, 3 layouts, repeater items
5. **Hero** — most complex layout CSS, do after patterns are confirmed working
6. **Testimonial Carousel** — last, has its own JS file which needs testing

For each block, the sequence is:
```
block.json → render.php → style.css → verify in editor → write pattern
```

---

## `block.json` Template

All blocks follow this base structure:

```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "blue-sage/BLOCK-NAME",
  "version": "1.0.0",
  "title": "BS — TITLE",
  "category": "blue-sage",
  "description": "...",
  "keywords": ["blue-sage", "..."],
  "textdomain": "blue-sage",
  "attributes": { ... },
  "supports": {
    "html": false,
    "align": ["wide", "full"],
    "spacing": {
      "margin": true,
      "padding": true,
      "blockGap": false
    },
    "color": false
  },
  "style":    "file:./style.css",
  "editorStyle": "file:./editor.css",
  "render":   "file:./render.php"
}
```

Note: `"category": "blue-sage"` requires registering a custom block category. Add to `inc/custom-blocks.php`:

```php
function blue_sage_block_category( array $categories ): array {
    return array_merge(
        [ [ 'slug' => 'blue-sage', 'title' => 'Blue Sage', 'icon' => 'star-filled' ] ],
        $categories
    );
}
add_filter( 'block_categories_all', 'blue_sage_block_category', 10, 1 );
```

---

## CSS Component File Strategy

Rather than appending all block CSS to `style.css`, each block ships its own `style.css` inside its `blocks/` directory. WordPress auto-enqueues these when the block is present on a page (zero unused CSS loaded).

Shared component variables (colors, spacing tokens) are already in `:root` via `style.css` from Phase 1 — block CSS files can reference `var(--bs-rich-blue)` etc. directly.

---

## Testimonial Carousel JS: `blocks/testimonial-carousel/index.js`

Enqueued via `block.json`'s `"viewScript": "file:./index.js"` — only loads on pages containing the block.

```javascript
// Minimal footprint — no dependencies, IIFE pattern
( function () {
  'use strict';

  if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
    // Skip crossfade, keep dot navigation only
  }

  document.querySelectorAll( '.bs-testimonials' ).forEach( initCarousel );

  function initCarousel( el ) { ... }
  function goTo( el, index, items, dots ) { ... }
  function startAutoplay( el, delay, items, dots ) { ... }
  function stopAutoplay( el ) { ... }
} )();
```

---

## Definition of Done

Phase 2 is complete when all of the following are true:

- [ ] All 5 blocks activate without PHP errors
- [ ] All 5 blocks render correctly in the block editor (no editor crashes, correct preview)
- [ ] All 5 blocks render correctly on the front end across Chrome, Firefox, Safari
- [ ] All 3 hero layout variants render at 375px, 768px, 1280px
- [ ] All 3 feature grid layout variants render at all breakpoints
- [ ] Testimonial carousel keyboard-navigates with arrow keys and dots
- [ ] Testimonial carousel pauses autoplay on hover and focus
- [ ] Metrics Bar counters trigger on scroll entry, not on page load
- [ ] All 10 patterns appear in the Block Inserter under the correct categories
- [ ] Patterns insert without validation errors in the block editor
- [ ] All blocks pass WordPress block validation (no block recovery needed on reload)
- [ ] `prefers-reduced-motion` respected in carousel and metrics counter
- [ ] No render-blocking JS introduced (all scripts load deferred or as `viewScript`)
- [ ] Zero `console.error` output in browser dev tools
- [ ] `WP_DEBUG` shows no PHP warnings or notices

---

## Notes for Implementation

- The `render.php` for each block receives `$attributes`, `$content`, and `$block` as arguments. Use `$attributes` for all configurable data. Always sanitize output with `esc_html()`, `esc_url()`, `wp_kses_post()`.
- For `items` repeater attributes stored as arrays, validate each item defensively (array_key_exists, esc_html per field).
- SVG icons in the Hero and Feature Grid blocks: store as hardcoded strings inside `render.php`, not as user input. This prevents XSS via SVG injection.
- The Testimonial and Feature Grid `items` arrays can be pre-populated via `block.json` `"default"` values to give users an immediate preview in the editor.
- Pattern PHP files use `register_block_pattern()` with block markup strings. Keep pattern markup clean — run through the block editor once to generate canonical markup, then paste the serialized output.
