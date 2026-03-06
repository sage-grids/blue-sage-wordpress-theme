# Phase 3 — Advanced Components

**Scope:** Week 5–6
**Status:** Complete
**Depends on:** Phase 2 (Core Components) — complete

---

## Overview

Phase 3 delivers six advanced section components that handle pricing, interactive content, social proof, and editorial layouts. These blocks are more interactive than Phase 2 equivalents — three require dedicated JS (accordion, pricing toggle, logo marquee) and one requires hover-state overlay logic (team grid).

The premium-website-guide principle of "Peak-End Rule" is central here: these components are the ones users remember. The pricing table must inspire confidence, the FAQ must reduce friction, the team grid must humanise the brand. Every interaction is a trust signal.

**Deliverables:**
1. Six custom blocks: Pricing Table, FAQ Accordion, Process/Steps, Team Grid, Blog Cards, Logo Wall
2. Six dedicated JS files (where needed — accordion, pricing toggle, marquee)
3. Ten additional block patterns (total pattern library reaches 20+)
4. CSS in `blocks/{name}/style.css` per block
5. Updated `inc/custom-blocks.php` to register new blocks
6. Three new pattern categories: `blue-sage-pricing`, `blue-sage-team`, `blue-sage-blog`

---

## Technical Architecture

Same approach as Phase 2: PHP server-side rendering via `render.php`, zero build step, vanilla JS only. Key additions for Phase 3:

- **Alpine.js is NOT used.** All accordion/toggle logic is bespoke vanilla JS (IIFE, deferred via `viewScript` in `block.json`).
- **`viewScript`** in `block.json` ensures JS only loads on pages containing that block — zero overhead on pages without it.
- **`prefers-reduced-motion`** respected in marquee (paused) and team grid hover (instant reveal instead of animated).
- Blog Cards use WordPress Query Loop inner blocks for real post data — no custom repeater attribute. This makes them dynamic and cache-friendly.

---

## Infrastructure Updates

### `inc/custom-blocks.php` — add Phase 3 blocks

Append to the `$blocks` array in `blue_sage_register_blocks()`:

```php
$blocks = [
    // Phase 2 (existing)
    'metrics-bar',
    'cta-section',
    'feature-grid',
    'hero',
    'testimonial-carousel',
    // Phase 3 (new)
    'pricing-table',
    'faq-accordion',
    'process-steps',
    'team-grid',
    'blog-cards',
    'logo-wall',
];
```

### `inc/block-patterns.php` — add new categories

```php
'blue-sage-pricing' => __( 'Blue Sage: Pricing',      'blue-sage' ),
'blue-sage-team'    => __( 'Blue Sage: Team',          'blue-sage' ),
'blue-sage-blog'    => __( 'Blue Sage: Blog',          'blue-sage' ),
```

---

## Block 1: Pricing Table (`blue-sage/pricing-table`)

**PRD reference:** Section 5.1, "Pricing Table"

### Attributes

| Attribute      | Type    | Default     | Notes                                  |
|----------------|---------|-------------|----------------------------------------|
| `eyebrow`      | string  | `''`        | —                                      |
| `heading`      | string  | `''`        | —                                      |
| `subheading`   | string  | `''`        | —                                      |
| `billingToggle`| boolean | `true`      | Show monthly/annual toggle             |
| `tiers`        | array   | `[]`        | 2–3 pricing tier objects               |

**Pricing tier object:**
```json
{
  "name": "string",
  "badge": "string",
  "monthlyPrice": "string",
  "annualPrice": "string",
  "currency": "$",
  "description": "string",
  "features": ["string"],
  "ctaLabel": "string",
  "ctaUrl": "string",
  "isPopular": false
}
```

### Inspector Controls

- **Content panel:** Eyebrow, heading, subheading
- **Billing panel:** Toggle show/hide billing period switch
- **Tiers panel:** Repeater — add/remove/reorder tiers. Per tier: name, badge text, monthly price, annual price, description, feature list (textarea, one per line), CTA label/URL, "Most Popular" checkbox
- **Style panel:** Dark section toggle (navy bg for full section)

### HTML Structure

```html
<section class="bs-pricing">
  <div class="bs-pricing__header">
    <span class="bs-eyebrow">...</span>
    <h2>...</h2>
    <p>...</p>
    <div class="bs-pricing__toggle" role="group" aria-label="Billing period">
      <span class="bs-pricing__toggle-label">Monthly</span>
      <button class="bs-pricing__toggle-btn is-active" aria-pressed="true"
              data-period="monthly">Monthly</button>
      <button class="bs-pricing__toggle-btn" aria-pressed="false"
              data-period="annual">Annual <span class="bs-badge bs-badge--sage">Save 20%</span></button>
      <span class="bs-pricing__toggle-label">Annual</span>
    </div>
  </div>
  <div class="bs-pricing__grid">
    <div class="bs-pricing__tier" data-popular="false">
      <div class="bs-pricing__tier-header">
        <h3 class="bs-pricing__tier-name">Starter</h3>
        <div class="bs-pricing__price">
          <span class="bs-pricing__currency">$</span>
          <span class="bs-pricing__amount js-price" data-monthly="29" data-annual="19">29</span>
          <span class="bs-pricing__period">/mo</span>
        </div>
        <p class="bs-pricing__description">...</p>
      </div>
      <ul class="bs-pricing__features">
        <li class="bs-pricing__feature">
          <svg class="bs-pricing__check" ...><!-- checkmark --></svg>
          Feature text
        </li>
      </ul>
      <a class="wp-block-button__link" href="...">Get started</a>
    </div>
    <div class="bs-pricing__tier bs-pricing__tier--popular" data-popular="true">
      <!-- Most Popular badge + elevated card -->
    </div>
  </div>
</section>
```

### Layout Spec

- **3-column grid** on desktop, 1-column stacked on mobile.
- Middle "Most Popular" card: `has-rich-blue-background-color`, white text, `transform: translateY(-8px)`, box-shadow with blue at 25% opacity.
- Non-featured cards: white bg, `1px solid var(--wp--preset--color--gray-100)`, `border-radius: 16px`.
- Feature checkmarks: Sage Green SVG icon (`#10B981`).
- Toggle switch: pill-shaped button group, active period highlighted in Rich Blue.

### CSS Classes

```
.bs-pricing                    ← section wrapper
.bs-pricing__header            ← centered header area
.bs-pricing__toggle            ← billing period toggle group
.bs-pricing__toggle-btn        ← individual period button
.bs-pricing__toggle-btn.is-active
.bs-pricing__grid              ← 3-col card grid
.bs-pricing__tier              ← single pricing card
.bs-pricing__tier--popular     ← elevated popular card
.bs-pricing__tier-header
.bs-pricing__price             ← currency + amount + period row
.bs-pricing__currency
.bs-pricing__amount            ← js-price target
.bs-pricing__period
.bs-pricing__features          ← feature list (ul)
.bs-pricing__feature           ← feature list item
.bs-pricing__check             ← sage green checkmark SVG
```

### JS (`blocks/pricing-table/index.js`)

**Behavior:** Toggle between monthly/annual prices when billing period buttons are clicked. No animation — price numbers swap instantly.

```javascript
(function () {
  'use strict';

  document.querySelectorAll('.bs-pricing').forEach(function (section) {
    const btns   = section.querySelectorAll('.bs-pricing__toggle-btn');
    const prices = section.querySelectorAll('.js-price');

    btns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const period = btn.dataset.period; // 'monthly' | 'annual'

        btns.forEach(function (b) {
          b.classList.toggle('is-active', b === btn);
          b.setAttribute('aria-pressed', String(b === btn));
        });

        prices.forEach(function (el) {
          el.textContent = el.dataset[period];
        });

        section.dataset.activePeriod = period;
      });
    });
  });
})();
```

### Files to create

```
blocks/pricing-table/
  block.json
  render.php
  style.css
  index.js
```

---

## Block 2: FAQ Accordion (`blue-sage/faq-accordion`)

**PRD reference:** Section 5.1, "FAQ Accordion"

### Attributes

| Attribute    | Type    | Default | Notes                                      |
|--------------|---------|---------|--------------------------------------------|
| `eyebrow`    | string  | `''`    | —                                          |
| `heading`    | string  | `''`    | —                                          |
| `items`      | array   | `[]`    | FAQ item objects                           |
| `allowMulti` | boolean | `false` | If false, opening one closes others        |

**FAQ item object:**
```json
{
  "question": "string",
  "answer": "string"
}
```

### Inspector Controls

- **Content panel:** Eyebrow, heading
- **Items panel:** Repeater — add/remove/reorder FAQ items. Each item: question (text input), answer (rich text area)
- **Behavior panel:** "Allow multiple open" toggle

### HTML Structure

```html
<section class="bs-faq">
  <div class="bs-faq__header">
    <span class="bs-eyebrow">...</span>
    <h2>...</h2>
  </div>
  <div class="bs-faq__list">
    <div class="bs-faq__item" data-open="false">
      <button class="bs-faq__trigger" aria-expanded="false"
              aria-controls="faq-answer-1" id="faq-trigger-1">
        <span class="bs-faq__question">How does it work?</span>
        <span class="bs-faq__icon" aria-hidden="true">
          <svg class="bs-faq__icon-plus"><!-- + --></svg>
          <svg class="bs-faq__icon-close"><!-- × --></svg>
        </span>
      </button>
      <div class="bs-faq__answer" id="faq-answer-1"
           role="region" aria-labelledby="faq-trigger-1"
           style="max-height: 0; overflow: hidden;">
        <div class="bs-faq__answer-inner">
          <p>...</p>
        </div>
      </div>
    </div>
  </div>
</section>
```

### Expand/Collapse Spec

- **Open state:** `max-height` transitions from `0` to the element's `scrollHeight`. Duration: 350ms, easing: `cubic-bezier(0.4, 0, 0.2, 1)`.
- **Icon:** `+` SVG rotates/fades to `×` SVG (150ms crossfade).
- **Keyboard:** Space/Enter on trigger button toggles. Focus ring: `2px solid var(--bs-rich-blue)` offset 2px.
- **`aria-expanded`** and **`data-open`** toggled in sync.
- **`allowMulti=false`:** Before opening any item, iterate all others and close them.

### CSS Classes

```
.bs-faq                    ← section wrapper
.bs-faq__header
.bs-faq__list
.bs-faq__item              ← single Q&A pair
.bs-faq__item[data-open="true"]
.bs-faq__trigger           ← the button (full-width row)
.bs-faq__question          ← question text
.bs-faq__icon              ← icon area (right side)
.bs-faq__icon-plus         ← + SVG
.bs-faq__icon-close        ← × SVG
.bs-faq__answer            ← collapsible region (overflow: hidden)
.bs-faq__answer-inner      ← padding wrapper inside answer
```

Style: borderless. Each item separated by a `1px solid var(--wp--preset--color--gray-100)` bottom border. No card backgrounds. Typography: question is H4 weight (600), answer is body (16px, gray-700).

### JS (`blocks/faq-accordion/index.js`)

**Key functions:**
```javascript
initAccordion(section, allowMulti)
openItem(item)
closeItem(item)
closeAll(section)
```

`prefers-reduced-motion`: disables the `max-height` transition but preserves open/close toggle behavior.

### Files to create

```
blocks/faq-accordion/
  block.json
  render.php
  style.css
  index.js
```

---

## Block 3: Process / Steps (`blue-sage/process-steps`)

**PRD reference:** Section 5.1, "Process / Steps"

### Attributes

| Attribute    | Type    | Default         | Options                          |
|--------------|---------|-----------------|----------------------------------|
| `layout`     | string  | `'numbered'`    | `'numbered'`, `'timeline'`       |
| `eyebrow`    | string  | `''`            | —                                |
| `heading`    | string  | `''`            | —                                |
| `subheading` | string  | `''`            | —                                |
| `items`      | array   | `[]`            | Step objects                     |

**Step object:**
```json
{
  "number": "string",
  "title": "string",
  "body": "string",
  "mediaId": 0,
  "mediaUrl": "string",
  "mediaAlt": "string"
}
```

`number` is stored as a string to support custom labels ("01", "Step 1", "A").

### Layout A — Numbered Steps

```
[Section — off-white bg]
  [Section header — centered]
  [Steps container — horizontal flex on desktop, vertical on mobile]
    [Step]
      [Oversized number — 80px, font-weight 800, color rgba(30,79,216,0.1)]
      [Title — H4, overlaid on/below number]
      [Body — 15px, gray-700]
    [Connector line — thin 1px, gray-300, between steps, hidden on mobile]
    [Step]
    ...
```

The oversized number is `position: absolute`, slightly behind the title, creating a layered depth effect. The connector line uses `::after` pseudo on the step container.

CSS class: `.bs-steps--numbered`

### Layout B — Timeline

```
[Section — white bg]
  [Section header]
  [Timeline track — left-bordered vertical line (2px, gray-200)]
    [Timeline item — alternating left/right on desktop]
      [Dot — 12px, Rich Blue, border-ring glow animation on scroll entry]
      [Content card — white, shadow-sm, 8px radius]
        [Date/label — sage green, uppercase, 12px]
        [Title — H4]
        [Body]
```

On mobile: timeline collapses to a single left-side track with all items right-aligned.

Dot ripple animation (triggered by `.is-visible` class from `scroll-animations.js`):
```css
.bs-timeline__dot.is-visible::before {
  animation: bs-ripple 600ms cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
@keyframes bs-ripple {
  from { transform: scale(0.4); opacity: 0.6; }
  to   { transform: scale(2.2); opacity: 0; }
}
```

CSS class: `.bs-steps--timeline`

### CSS Classes

```
.bs-steps                      ← section wrapper
.bs-steps--numbered
.bs-steps--timeline
.bs-steps__header
.bs-steps__track               ← numbered: flex row; timeline: vertical container
.bs-steps__item
.bs-steps__number              ← oversized background numeral (numbered layout)
.bs-steps__connector           ← horizontal line between numbered steps
.bs-timeline__line             ← vertical track line (timeline layout)
.bs-timeline__dot              ← milestone dot
.bs-timeline__card             ← content card (timeline layout)
.bs-steps__title
.bs-steps__body
```

No dedicated JS file — scroll entry handled by existing `scroll-animations.js` (`.js-fade-up`, `.js-stagger`). The ripple animation triggers via CSS when `.is-visible` is added.

### Files to create

```
blocks/process-steps/
  block.json
  render.php
  style.css
```

---

## Block 4: Team Grid (`blue-sage/team-grid`)

**PRD reference:** Section 5.1, "Team Grid"

### Attributes

| Attribute    | Type    | Default      | Options                        |
|--------------|---------|--------------|--------------------------------|
| `eyebrow`    | string  | `''`         | —                              |
| `heading`    | string  | `''`         | —                              |
| `photoShape` | string  | `'square'`   | `'square'`, `'circle'`         |
| `columns`    | integer | `4`          | `3`, `4`                       |
| `members`    | array   | `[]`         | Member objects                 |

**Member object:**
```json
{
  "name": "string",
  "title": "string",
  "bio": "string",
  "photoId": 0,
  "photoUrl": "string",
  "photoAlt": "string",
  "linkedin": "string",
  "twitter": "string"
}
```

### Inspector Controls

- **Content panel:** Eyebrow, heading
- **Style panel:** Photo shape (square / circle), columns (3 / 4)
- **Members panel:** Repeater — name, title, bio, photo picker, LinkedIn URL, Twitter URL

### HTML Structure

```html
<section class="bs-team">
  <div class="bs-team__header">
    <span class="bs-eyebrow">...</span>
    <h2>...</h2>
  </div>
  <div class="bs-team__grid bs-team__grid--4col js-stagger">
    <div class="bs-team__card js-fade-up">
      <div class="bs-team__photo-wrap">
        <img class="bs-team__photo" src="..." alt="..." loading="lazy"
             width="280" height="280">
        <div class="bs-team__overlay" aria-hidden="true">
          <p class="bs-team__bio">...</p>
          <div class="bs-team__socials">
            <a href="..." class="bs-team__social-link" target="_blank" rel="noopener noreferrer"
               aria-label="Jane Smith on LinkedIn">
              <!-- LinkedIn SVG -->
            </a>
          </div>
        </div>
      </div>
      <h3 class="bs-team__name">Jane Smith</h3>
      <p class="bs-team__title">Head of Design</p>
    </div>
  </div>
</section>
```

### Hover Overlay Spec

- Overlay: `position: absolute`, full cover, `background: rgba(10, 22, 40, 0.88)` (deep navy at 88%).
- Transition: `opacity 0→1` over 250ms on card hover.
- Bio text: white, 14px, centered.
- Social links: white icon SVGs, `24px`, gap 12px.
- `prefers-reduced-motion`: overlay appears instantly (no opacity transition).
- Photo shape: `square` = `border-radius: 8px`; `circle` = `border-radius: 50%`.

No dedicated JS needed — pure CSS hover. The `.bs-team__overlay` is always in the DOM, just hidden via opacity/pointer-events.

### CSS Classes

```
.bs-team
.bs-team__header
.bs-team__grid
.bs-team__grid--3col
.bs-team__grid--4col
.bs-team__card
.bs-team__photo-wrap        ← position: relative, overflow: hidden
.bs-team__photo
.bs-team__overlay           ← hover reveal (CSS only)
.bs-team__bio
.bs-team__socials
.bs-team__social-link
.bs-team__name
.bs-team__title
```

### Files to create

```
blocks/team-grid/
  block.json
  render.php
  style.css
```

---

## Block 5: Blog Cards (`blue-sage/blog-cards`)

**PRD reference:** Section 5.1, "Blog / Content Cards"

### Strategy: WordPress Query Integration

Unlike the repeater-based blocks in Phase 2 and 3, Blog Cards use **WordPress's native query system**. The block renders a `WP_Query` using attributes as arguments — this gives real dynamic post data, respects pagination, and is cache-friendly.

### Attributes

| Attribute       | Type    | Default      | Options                                    |
|-----------------|---------|--------------|--------------------------------------------|
| `layout`        | string  | `'standard'` | `'standard'`, `'featured'`, `'list'`       |
| `eyebrow`       | string  | `''`         | —                                          |
| `heading`       | string  | `''`         | —                                          |
| `postsPerPage`  | integer | `3`          | 2–6 for standard, 1 for featured           |
| `categoryId`    | integer | `0`          | 0 = all categories                         |
| `showExcerpt`   | boolean | `true`       | Show/hide excerpt                          |
| `showReadTime`  | boolean | `true`       | Uses helpers.php `reading_time()`          |
| `showAuthor`    | boolean | `true`       | Show author avatar + name                  |
| `ctaLabel`      | string  | `''`         | "View all posts" link label (optional)     |
| `ctaUrl`        | string  | `''`         | Destination for CTA link                   |

### Inspector Controls

- **Layout panel:** Standard Card Grid / Featured Card / Minimal List
- **Query panel:** Posts per page (number), Category filter (dropdown from registered categories), Show excerpt, Show read time, Show author
- **Section panel:** Eyebrow, heading
- **CTA panel:** Label + URL for "view all" link below grid

### Layout A — Standard Card Grid

```
[Section — white or off-white bg]
  [Section header]
  [3-col grid (desktop) / 2-col (tablet) / 1-col (mobile)]
    [Post card — white bg, 8px radius, shadow-sm, hover: translateY(-4px)]
      [Thumbnail — 16:9, rounded top, loading="lazy"]
      [Category badge — colored pill]
      [Title — H4, 2-line clamp]
      [Excerpt — 3-line clamp, gray-700, 15px]
      [Footer — author avatar (32px, circle) + name + dot + date + dot + read time]
```

CSS class: `.bs-blog--standard`

### Layout B — Featured Card

```
[Full-width card — white bg, 8px radius, shadow-md]
  [2-column: Image left (60%) / Content right (40%)]
    [Image — full height, object-fit: cover]
    [Content — padding 48px]
      [Category badge]
      [Title — H2 size, large]
      [Excerpt — 4-line clamp]
      [Footer row]
      [CTA link — "Read article →"]
```

Used for the homepage "latest post" showcase. `postsPerPage=1` in this layout.

CSS class: `.bs-blog--featured`

### Layout C — Minimal List

```
[Section]
  [Section header]
  [List — borderless rows]
    [Row — flex, gap 24px]
      [Category — 80px min-width, colored text, uppercase, 12px]
      [Title — H4, flex-1, hover: text color shifts to Rich Blue]
      [Date — gray-500, 14px, no-shrink]
```

No images. Compact. Used in sidebars, archive pages, or "more posts" sections.

CSS class: `.bs-blog--list`

### `render.php` Query

```php
$query = new WP_Query([
    'post_type'      => 'post',
    'posts_per_page' => absint( $attributes['postsPerPage'] ?? 3 ),
    'cat'            => absint( $attributes['categoryId'] ?? 0 ) ?: null,
    'post_status'    => 'publish',
    'no_found_rows'  => true,
]);
```

Always reset with `wp_reset_postdata()` after the loop.

### CSS Classes

```
.bs-blog                       ← section wrapper
.bs-blog--standard
.bs-blog--featured
.bs-blog--list
.bs-blog__header
.bs-blog__grid                 ← card grid (standard)
.bs-blog__card                 ← individual post card
.bs-blog__thumb                ← thumbnail wrapper
.bs-blog__thumb img
.bs-blog__category             ← category badge/label
.bs-blog__title
.bs-blog__excerpt
.bs-blog__meta                 ← author + date + read time row
.bs-blog__avatar
.bs-blog__author-name
.bs-blog__date
.bs-blog__read-time
.bs-blog__featured             ← featured layout wrapper
.bs-blog__featured-media       ← image column
.bs-blog__featured-content     ← text column
.bs-blog__list-row             ← list layout row
.bs-blog__cta                  ← "view all" link below grid
```

### Files to create

```
blocks/blog-cards/
  block.json
  render.php
  style.css
```

---

## Block 6: Logo Wall (`blue-sage/logo-wall`)

**PRD reference:** Section 5.1, "Logo Wall"

### Attributes

| Attribute    | Type    | Default | Notes                                      |
|--------------|---------|---------|--------------------------------------------|
| `eyebrow`    | string  | `''`    | e.g. "Trusted by teams at"                 |
| `logos`      | array   | `[]`    | Logo objects (image + alt + optional URL)  |
| `marquee`    | boolean | `true`  | Infinite scroll on desktop, static on mobile |
| `speed`      | integer | `40`    | Scroll animation duration in seconds       |
| `colorize`   | boolean | `false` | If true: full-color logos; false: grayscale hover-to-color |

**Logo object:**
```json
{
  "mediaId": 0,
  "mediaUrl": "string",
  "mediaAlt": "string",
  "linkUrl": "string"
}
```

### Inspector Controls

- **Content panel:** Eyebrow text
- **Logos panel:** Repeater — add/remove logos (image picker, alt text, optional link URL)
- **Behavior panel:** Enable marquee toggle, speed slider (20–80s)
- **Style panel:** Grayscale/color mode toggle

### HTML Structure

Marquee uses CSS `@keyframes` scroll — no JS position calculation. The logo list is duplicated in PHP to create seamless looping.

```html
<section class="bs-logos">
  <div class="bs-logos__inner">
    <p class="bs-eyebrow bs-logos__eyebrow">Trusted by teams at</p>
    <div class="bs-logos__track-wrap" aria-label="Partner logos">
      <div class="bs-logos__track" style="--bs-marquee-speed: 40s;">
        <!-- First set -->
        <div class="bs-logos__set" aria-hidden="false">
          <div class="bs-logos__item">
            <a href="https://..." class="bs-logos__link" target="_blank"
               rel="noopener noreferrer" aria-label="Acme Corp">
              <img class="bs-logos__img" src="..." alt="Acme Corp"
                   loading="lazy" width="120" height="48">
            </a>
          </div>
          <!-- ... more logos -->
        </div>
        <!-- Duplicate set for seamless loop (aria-hidden="true") -->
        <div class="bs-logos__set" aria-hidden="true">
          <!-- identical content -->
        </div>
      </div>
    </div>
  </div>
</section>
```

### Marquee CSS

```css
.bs-logos__track {
  display: flex;
  gap: 64px;
  width: max-content;
  animation: bs-marquee var(--bs-marquee-speed, 40s) linear infinite;
}

@keyframes bs-marquee {
  from { transform: translateX(0); }
  to   { transform: translateX(-50%); }   /* -50% = one full set width */
}

/* Pause on hover / reduced-motion */
.bs-logos__track:hover,
.bs-logos__track:focus-within {
  animation-play-state: paused;
}

@media (prefers-reduced-motion: reduce) {
  .bs-logos__track {
    animation: none;
    flex-wrap: wrap;
    width: 100%;
    justify-content: center;
  }
  /* Hide duplicate set when not animating */
  .bs-logos__set[aria-hidden="true"] {
    display: none;
  }
}

/* Mobile: static grid, no marquee */
@media (max-width: 767px) {
  .bs-logos__track {
    animation: none;
    flex-wrap: wrap;
    width: 100%;
    justify-content: center;
    gap: 32px;
  }
  .bs-logos__set[aria-hidden="true"] { display: none; }
}
```

### Grayscale/Color Spec

Default (grayscale):
```css
.bs-logos__img {
  filter: grayscale(100%);
  opacity: 0.6;
  transition: filter 250ms ease, opacity 250ms ease;
}
.bs-logos__item:hover .bs-logos__img {
  filter: grayscale(0%);
  opacity: 1;
}
```

When `colorize=true`: no filter applied at all (`filter: none`).

### No JS Required

The marquee is pure CSS animation. `prefers-reduced-motion` is handled entirely in CSS. No `index.js` needed for this block.

### CSS Classes

```
.bs-logos                    ← section wrapper
.bs-logos__inner             ← max-width container
.bs-logos__eyebrow
.bs-logos__track-wrap        ← overflow: hidden mask
.bs-logos__track             ← animating flex row
.bs-logos__set               ← one complete set of logos
.bs-logos__item              ← individual logo wrapper
.bs-logos__link              ← optional anchor
.bs-logos__img               ← logo image
```

### Files to create

```
blocks/logo-wall/
  block.json
  render.php
  style.css
```

---

## Block Patterns (Phase 3 — 10 new patterns)

Total after Phase 3: 20 patterns.

### Pricing Patterns

#### `blue-sage-pricing/three-tier` — Three-Tier Pricing
- Uses `blue-sage/pricing-table` with 3 tiers: Starter, Pro (popular), Enterprise
- Pre-filled with realistic SaaS pricing copy and features
- Billing toggle enabled, annual prices show 20% savings

#### `blue-sage-pricing/two-tier` — Two-Tier Simple
- 2 tiers: Free vs Pro. No billing toggle. Simpler for service businesses.

### Team Patterns

#### `blue-sage-team/grid-four` — Team Grid (4-col)
- Uses `blue-sage/team-grid` with 4 columns, square photos, 8 sample members

#### `blue-sage-team/grid-three` — Team Grid (3-col)
- Uses `blue-sage/team-grid` with 3 columns, circle photos, 6 sample members

### FAQ Patterns

#### `blue-sage-faq/standard` — FAQ Section
- Uses `blue-sage/faq-accordion` with 6 pre-filled questions
- Eyebrow "FAQ", heading "Everything you need to know"
- `allowMulti=false` — professional single-open behavior

#### `blue-sage-faq/with-cta` — FAQ + CTA Stack
- Core group block: `blue-sage/faq-accordion` (5 items) stacked above `blue-sage/cta-section` (inline-box layout)
- Common pattern: answer questions, then convert

### Process Patterns

#### `blue-sage-process/numbered` — How It Works (Numbered)
- Uses `blue-sage/process-steps` with `layout=numbered`, 4 steps
- Demo copy: Discover → Design → Build → Launch

#### `blue-sage-process/timeline` — Company Timeline
- Uses `blue-sage/process-steps` with `layout=timeline`, 5 milestone entries with years

### Blog Patterns

#### `blue-sage-blog/homepage-preview` — Blog Preview (Homepage)
- Core group: `blue-sage/blog-cards` featured layout (1 post) stacked with standard 3-card grid
- Common homepage bottom section: big feature card, then 3 more posts

#### `blue-sage-blog/minimal-archive` — Minimal Post List
- Uses `blue-sage/blog-cards` with `layout=list`, `postsPerPage=8`

### Logo Patterns

#### `blue-sage-social-proof/logo-wall` — Logo Wall / Marquee
- Uses `blue-sage/logo-wall` with `marquee=true`, 8 placeholder logos
- Eyebrow: "Trusted by 10,000+ teams worldwide"

---

## File Structure After Phase 3

```
blocks/
  pricing-table/
    block.json
    render.php
    style.css
    index.js           ← billing toggle
  faq-accordion/
    block.json
    render.php
    style.css
    index.js           ← expand/collapse
  process-steps/
    block.json
    render.php
    style.css
  team-grid/
    block.json
    render.php
    style.css
  blog-cards/
    block.json
    render.php
    style.css
  logo-wall/
    block.json
    render.php
    style.css

patterns/
  pricing-three-tier.php       ← NEW
  pricing-two-tier.php         ← NEW
  team-grid-four.php           ← NEW
  team-grid-three.php          ← NEW
  faq-standard.php             ← NEW
  faq-with-cta.php             ← NEW
  process-numbered.php         ← NEW
  process-timeline.php         ← NEW
  blog-homepage-preview.php    ← NEW
  blog-minimal-archive.php     ← NEW
  logo-wall.php                ← NEW
```

---

## Implementation Order

Build in this sequence — interactive blocks first (validates JS patterns), layout blocks last:

1. **`inc/custom-blocks.php`** — Add 6 new block slugs to `$blocks` array
2. **`inc/block-patterns.php`** — Add 3 new pattern categories
3. **Logo Wall** — CSS-only, no JS, validates the block registration pattern for Phase 3
4. **Process Steps** — No JS, hooks into existing scroll-animation system
5. **Team Grid** — CSS hover only, validates photo shape + grid layout
6. **FAQ Accordion** — First Phase 3 block with its own JS; validate JS enqueue via `viewScript`
7. **Pricing Table** — Second JS block; more complex (billing toggle, repeater items)
8. **Blog Cards** — WP_Query integration; validate dynamic rendering + all 3 layouts
9. **Patterns** — Write all 11 patterns after blocks are verified

For each block:
```
block.json → render.php → style.css → (index.js if needed) → verify in editor + front end → write patterns
```

---

## `block.json` Notes for Phase 3 Blocks

All blocks inherit the same base structure from Phase 2. Phase 3 additions:

**Blocks with `viewScript`:** `pricing-table`, `faq-accordion`
```json
"viewScript": "file:./index.js"
```

**Blog Cards — dynamic flag:**
```json
"usesContext": ["postId", "postType"],
"render": "file:./render.php"
```
The `render` attribute automatically flags the block as dynamic (server-rendered). No `"dynamic": true` flag needed in `apiVersion: 3`.

**Logo Wall — `supports` override:**
```json
"supports": {
  "html": false,
  "align": ["wide", "full"],
  "spacing": { "margin": true, "padding": true }
}
```

---

## Key CSS Patterns for Phase 3

### Pricing card elevation (popular tier)
```css
.bs-pricing__tier--popular {
  background-color: var(--wp--preset--color--rich-blue);
  color: #fff;
  transform: translateY(-8px);
  box-shadow: 0 24px 48px rgba(30, 79, 216, 0.25);
  z-index: 1;
}
```

### FAQ answer transition
```css
.bs-faq__answer {
  max-height: 0;
  overflow: hidden;
  transition: max-height 350ms cubic-bezier(0.4, 0, 0.2, 1);
}
.bs-faq__item[data-open="true"] .bs-faq__answer {
  max-height: var(--bs-faq-answer-height); /* set by JS: el.style.setProperty() */
}
```

### Timeline ripple (no new JS — CSS only, triggered by scroll-animations.js)
```css
.bs-timeline__dot {
  position: relative;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: var(--bs-rich-blue);
}
.bs-timeline__dot::before {
  content: '';
  position: absolute;
  inset: -4px;
  border-radius: 50%;
  background: var(--bs-rich-blue);
  opacity: 0;
}
.bs-timeline__dot.is-visible::before {
  animation: bs-ripple 600ms cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
@keyframes bs-ripple {
  from { transform: scale(0.4); opacity: 0.5; }
  to   { transform: scale(2.5); opacity: 0;   }
}
```

### Team hover overlay (CSS only)
```css
.bs-team__overlay {
  position: absolute;
  inset: 0;
  background: rgba(10, 22, 40, 0.88);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 24px;
  opacity: 0;
  transition: opacity 250ms ease;
  border-radius: inherit;
}
.bs-team__card:hover .bs-team__overlay,
.bs-team__card:focus-within .bs-team__overlay {
  opacity: 1;
}
@media (prefers-reduced-motion: reduce) {
  .bs-team__overlay { transition: none; }
}
```

---

## Accessibility Checklist (per block)

Following WCAG 2.1 AA and the premium-website-guide mandate for non-negotiable a11y:

| Block          | Key Requirements                                                                         |
|----------------|------------------------------------------------------------------------------------------|
| Pricing Table  | `role="group"` on toggle, `aria-pressed` on buttons, feature lists are semantic `<ul>`  |
| FAQ Accordion  | `aria-expanded`, `aria-controls`, `role="region"`, keyboard: Space/Enter/Escape          |
| Process Steps  | Semantic `<ol>` for numbered steps, `aria-hidden="true"` on decorative numbers          |
| Team Grid      | Overlay has `aria-hidden="true"` (decorative), social links have `aria-label` per person |
| Blog Cards     | Explicit `width`/`height` on all images, author info in `<address>` or semantic span     |
| Logo Wall      | Duplicate marquee set has `aria-hidden="true"`, individual logos have descriptive alt    |

---

## Definition of Done

Phase 3 is complete when all of the following are true:

- [ ] All 6 blocks activate without PHP errors or WordPress notices
- [ ] All 6 blocks render without crashes in the block editor
- [ ] All 6 blocks render correctly on the front end: Chrome, Firefox, Safari, Edge
- [ ] Pricing table billing toggle switches prices correctly; `aria-pressed` state updates
- [ ] FAQ accordion opens/closes with keyboard (Space/Enter) and mouse
- [ ] FAQ accordion `allowMulti=false` closes others when a new item opens
- [ ] Process Steps: numbered layout renders horizontal connector lines on desktop, stacks on mobile
- [ ] Process Steps: timeline dot ripple triggers on scroll entry (via `scroll-animations.js`)
- [ ] Team grid hover overlay appears on hover and on keyboard focus (`:focus-within`)
- [ ] Blog Cards: all 3 layouts render real post data from `WP_Query`
- [ ] Blog Cards: `reading_time()` helper output is correct and visible when enabled
- [ ] Logo Wall marquee animates infinitely, pauses on hover, stops on mobile
- [ ] Logo Wall grayscale mode: logos render in grayscale, animate to full color on hover
- [ ] `prefers-reduced-motion`: FAQ transitions off, marquee static, team overlay instant
- [ ] All 11 patterns appear in the Block Inserter under the correct categories
- [ ] Pattern library total reaches 20+ (10 from Phase 2 + 11 from Phase 3)
- [ ] No `console.error` in browser dev tools on any block
- [ ] `WP_DEBUG` shows no PHP warnings on any page using Phase 3 blocks
- [ ] All blocks pass WCAG 2.1 AA: keyboard accessible, ARIA correct, contrast ratios met
- [ ] All images include explicit `width` and `height` attributes (CLS prevention)
