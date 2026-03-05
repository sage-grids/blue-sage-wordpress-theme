# Blue Sage WordPress Theme — Product Requirements Document

**Version:** 1.0
**Date:** March 2026
**Status:** Approved for Development

---

## 1. Vision & Philosophy

Blue Sage is a premium WordPress theme for brands that lead, not follow. Named after the sage — a figure of wisdom and authority — the theme communicates clarity, precision, and quiet confidence. It is designed for high-end agencies, SaaS companies, consultancies, and modern businesses that need to make an unforgettable first impression.

**Core design philosophy:**

- **Restraint as a luxury signal.** What is removed is as important as what is included. White space is not emptiness — it is breathing room for ideas.
- **Typography as the primary visual element.** Large, expressive type sets the tone before any image loads.
- **Motion that serves meaning.** Every animation exists to guide attention, never to entertain for its own sake.
- **Performance is a design decision.** A slow site is a broken site. Speed is baked into every architectural choice.

**The viral formula:** Blue Sage will spread because it looks unlike anything else in the WordPress ecosystem — it borrows from the best of modern SaaS design (clean geometry, editorial type scales, purposeful blue) while remaining fully functional for real-world business use.

---

## 2. Target Users

| Primary | Secondary |
|---|---|
| Digital agencies & studios | Freelance developers reselling sites |
| SaaS & tech startups | Consultants & coaches |
| High-end service businesses | Personal brands & thought leaders |
| Creative portfolios | Nonprofits & mission-driven orgs |

**User goals:** Launch a professional, fast, high-converting site without hiring a custom developer. Customize confidently without breaking the design.

---

## 3. Design Language

### 3.1 Color System

```
Primary — Deep Navy:    #0A1628   (backgrounds, headings, authority elements)
Primary — Rich Blue:    #1E4FD8   (CTAs, links, accent borders, highlights)
Primary — Sky Blue:     #3B82F6   (hover states, subtle fills, icons)
Primary — White:        #FFFFFF   (page background, card surfaces)
Primary — Off-White:    #F8FAFF   (section alternates, subtle panels)
Primary — Near-Black:   #0D0D0D   (body text, dark sections)

Secondary — Electric Indigo: #6366F1   (sparingly: badges, tags, feature highlights)
Secondary — Sage Green:      #10B981   (success states, positive metrics — nod to name)
Secondary — Amber:           #F59E0B   (warnings, special callouts only)

Neutral — Gray-100: #F3F4F6
Neutral — Gray-300: #D1D5DB
Neutral — Gray-500: #6B7280
Neutral — Gray-700: #374151
```

**Usage rules:**
- White background dominates. Blue is the hero, not the wallpaper.
- Never use more than 2 accent colors in a single section.
- Dark sections (#0A1628 bg) are used for dramatic emphasis — hero, pricing, CTA. Maximum 2 per page.
- The Sage Green accent is used sparsely and intentionally — it is the signature detail that makes people stop scrolling.

### 3.2 Typography

**Display / Headings:** `Plus Jakarta Sans` — geometric, authoritative, highly legible at large sizes. Weights: 400, 600, 700, 800.

**Body:** `Inter` — the gold standard for UI text. Clean, neutral, screen-optimized. Weights: 400, 500.

**Monospace (code, tags, labels):** `JetBrains Mono` — used for code blocks, labels, version numbers.

**Type Scale (8px base unit × golden ratio approximation):**

```
Display:   80px / line-height 1.05 / letter-spacing -0.04em
H1:        56px / line-height 1.1  / letter-spacing -0.03em
H2:        40px / line-height 1.2  / letter-spacing -0.02em
H3:        28px / line-height 1.3  / letter-spacing -0.01em
H4:        20px / line-height 1.4
Body LG:   18px / line-height 1.7
Body:      16px / line-height 1.7
Small:     14px / line-height 1.6
Label:     12px / line-height 1.5 / letter-spacing 0.08em / UPPERCASE
```

**Mobile scaling:** All display/H1 sizes reduce by ~35% on mobile via fluid `clamp()` values.

### 3.3 Spacing System

Strict 8px grid. All spacing values: `4, 8, 12, 16, 24, 32, 48, 64, 96, 128, 192px`. No arbitrary values.

Section vertical padding: `96px` desktop / `64px` tablet / `48px` mobile.

### 3.4 Grid & Layout

- **Container max-width:** 1280px, centered, with 24px side padding on mobile, 48px on tablet, 80px on desktop.
- **Grid system:** 12-column. Components use 12, 8, 6, 4, or 3 column spans.
- **Breakpoints:** 375px (mobile), 768px (tablet), 1024px (laptop), 1280px (desktop), 1536px (wide).

### 3.5 Visual Details

- **Border radius:** 4px (buttons, inputs), 8px (cards), 16px (large cards, modals), 999px (pills/badges).
- **Shadows:** Elevation-based. No generic `box-shadow: 0 4px 6px rgba(0,0,0,0.1)`. Colored shadows using brand blue at low opacity for interactive states.
- **Borders:** `1px solid` using `Gray-100` or `rgba(30, 79, 216, 0.12)` for blue-tinted borders on featured elements.
- **Gradients:** Used sparingly. Blue-to-indigo (`#1E4FD8` → `#6366F1`) for gradient text on display headings. Never on backgrounds unless it's a dark hero section.

---

## 4. Motion & Interaction Design

Every interaction is choreographed, not random. The guiding principle: **motion should feel inevitable, not decorative.**

### 4.1 Animation Tokens

```
Duration — Fast:    150ms   (hover color, border changes)
Duration — Base:    250ms   (button states, icon transforms)
Duration — Slow:    400ms   (cards, modals, dropdowns)
Duration — Page:    600ms   (hero entrance, section reveals)

Easing — Standard:  cubic-bezier(0.4, 0, 0.2, 1)   (most UI transitions)
Easing — Enter:     cubic-bezier(0, 0, 0.2, 1)       (elements entering viewport)
Easing — Exit:      cubic-bezier(0.4, 0, 1, 1)       (elements leaving)
Easing — Spring:    cubic-bezier(0.34, 1.56, 0.64, 1) (playful, for badges/icons)
```

### 4.2 Micro-interactions

- **Buttons:** Background shifts on hover (150ms), subtle `translateY(-1px)` on press with shadow.
- **Cards:** `translateY(-4px)` + shadow deepening on hover (250ms).
- **Links:** Underline animates from left to right using a pseudo-element.
- **Navigation:** Active item slides a thin blue indicator bar (not just color change).
- **Form inputs:** Border color transitions to Rich Blue on focus; label floats up.

### 4.3 Scroll Animations

Implemented via `IntersectionObserver` (no GSAP dependency for base theme):
- **Fade up:** Default for most section content. `opacity: 0 → 1`, `translateY(24px → 0)`.
- **Stagger:** Card grids stagger children by `80ms` delay each.
- **Counter animation:** Stat numbers count up when entering viewport.
- **Parallax:** Subtle `10%` parallax on hero background only. Disabled if `prefers-reduced-motion`.

All animations respect `prefers-reduced-motion: reduce` — they are stripped entirely, not slowed.

---

## 5. Component Library

All components are built as native WordPress blocks registered via `register_block_type()` and configurable through the Block Editor's Inspector Controls. They also work as standalone PHP template parts.

### 5.1 Section Components

#### Hero (3 layouts)
- **Layout A — Centered Statement:** Full-width dark (#0A1628) section. Eyebrow label (uppercase, Sage Green). Display-size heading with gradient blue text on key words. Subheading (max 2 lines). Primary + Secondary CTA. Optional floating UI mockup or abstract geometric element below.
- **Layout B — Split:** Left-aligned headline + CTAs on 7-col, right 5-col visual (image, device mockup, illustration). Clean white background.
- **Layout C — Editorial:** Oversized H1 (runs across full width, possibly breaking lines dramatically). Minimal CTA below. Inspired by editorial magazine design.

#### Feature Grid (3 layouts)
- **Icon Grid:** 3-col (desktop) / 2-col (tablet) / 1-col (mobile). Icon in a subtle blue-tinted square, title, body. Thin separator lines between items.
- **Alternating Split:** Feature highlights with alternating image-left/text-right and text-left/image-right rows. Blue vertical accent bar on text side.
- **Large Card Grid:** 2-col large cards with a full bleed colored (blue tinted) background on one card, white on the other. Used for primary feature highlights.

#### Social Proof
- **Testimonial Carousel:** Single testimonial at a time. Large quote mark in blue. Author photo (circular, 56px), name, title, company logo. Auto-advances, dots indicator, keyboard accessible.
- **Logo Wall:** Grayscale client/partner logos on hover animate to color. Two rows, infinite scroll marquee on desktop. Static grid on mobile.
- **Metrics Bar:** Full-width dark section. 3–4 stats side by side. Large numerals (H1 size) in white or blue. Counter animation on scroll. Thin dividers between stats.

#### Pricing Table
- **3-column** layout. Middle card ("Most Popular") elevated with blue background, white text, and `translateY(-8px)`. Toggle for monthly/annual billing. Feature list with checkmarks (Sage Green). CTA button per tier. Subtle animation on plan switch.

#### CTA Section (2 layouts)
- **Full-width Dark:** Navy background. Centered heading + subtext + CTA. Optional subtle radial blue glow behind heading.
- **Inline Box:** White background section. Contained card with a thin blue border, heading, body, and side-by-side CTAs. Used mid-page between content sections.

#### FAQ Accordion
- Clean, borderless. Each item has a `+` / `×` icon (animates between states). Answer expands with `max-height` animation. Optional: grouped by category with tab navigation.

#### Process / Steps
- **Numbered Steps:** Large numerals (80px, 10% opacity blue) behind step title. Horizontal connector line on desktop, vertical on mobile.
- **Timeline:** Left-bordered vertical timeline with alternating left/right content blocks. Blue dot at each milestone with a ripple animation on scroll entry.

#### Team Grid
- Circular or square (8px radius) photo. Name, title, optional social links. On hover: card lifts, semi-transparent overlay with bio text appears.

#### Blog / Content Cards
- **Standard Card:** Image (16:9), category label (uppercase, colored badge), title (H4), excerpt, author avatar + date + read time.
- **Featured Card:** Full-width card variant for homepage latest post. Image left (60%), text right (40%).
- **Minimal List:** No image. Just category, title, date in a clean row. Used for sidebar or archive lists.

### 5.2 Navigation Components

#### Primary Navigation
- Sticky header. Logo left. Nav links center (or right). CTA button rightmost.
- On scroll: slim border-bottom appears, background gains a `backdrop-filter: blur(12px)` frosted glass effect at 98% white opacity.
- Hover state: underline slides in (200ms) from left.
- Active state: thin blue line below link.

#### Mega Menu (optional)
- Dropdown panel (full container width). Categorized links in a 3-column grid. Featured link card on far right (blue bg, white text, icon, arrow).

#### Mobile Navigation
- Full-screen slide-in overlay from right. Logo + close button. Large navigation links (H3 size). Accordion sub-menus. Social links at bottom.

#### Breadcrumbs
- Small, `Gray-500` text. Separator: `/` or `›`. Current page: `Gray-700`. Schema.org breadcrumb markup included.

### 5.3 Footer

**3-zone layout:**
1. **Top:** Logo + tagline (left) and newsletter signup input + button (right).
2. **Middle:** 4-column link grid (Company, Product, Resources, Legal).
3. **Bottom:** Copyright + social icons + optional back-to-top button.

Dark variant (Navy bg) and Light variant (Off-White bg) both included as options.

### 5.4 Utility Blocks

- **Divider:** Thin line, optional label in center, optional gradient.
- **Badge / Tag:** Pill shape, 3 variants (blue, sage green, neutral).
- **Callout Box:** Bordered box with left accent bar. 4 variants: info (blue), success (green), warning (amber), note (neutral).
- **Button:** Primary (blue, filled), Secondary (blue, outlined), Ghost (transparent), Destructive (red). All sizes: sm / md / lg. Icon left/right support.
- **Icon Box:** SVG icon + title + body. Used inline in content.
- **Code Block:** Syntax highlighted. Copy-to-clipboard button. Language label.

---

## 6. Page Templates

### 6.1 Core Templates

| Template | Purpose |
|---|---|
| `front-page.php` | Customizable homepage with Hero, Features, Social Proof, CTA, Blog preview |
| `page.php` | Default page. Content-only, no sidebar |
| `page-full-width.php` | No sidebar, full container |
| `page-landing.php` | No header/footer nav. For conversion pages |
| `single.php` | Single post. Reading progress bar, author bio, related posts |
| `archive.php` | Blog index. Filterable by category. Masonry or grid toggle |
| `search.php` | Search results. Highlighted query terms |
| `404.php` | Custom 404. Large "404" heading, search field, suggested links |

### 6.2 WooCommerce Templates (optional integration)

If WooCommerce is active, Blue Sage provides styled overrides for: shop archive, single product, cart, checkout, account pages. Minimal, consistent with theme aesthetic.

---

## 7. Technical Architecture

### 7.1 WordPress Standards

- **Minimum WordPress:** 6.4
- **Minimum PHP:** 8.1
- **Full Site Editing (FSE):** Theme built as a block theme with `theme.json` as the single source of truth for design tokens (colors, typography, spacing, shadows).
- **`theme.json`:** Defines all color palette, typography scale, spacing scale, border radius, and shadow tokens. No inline CSS where `theme.json` can handle it.
- **Template Hierarchy:** Full support for WordPress template hierarchy. Templates in `/templates/`, template parts in `/parts/`.
- **Block Patterns:** All major section combinations registered as curated block patterns. User can insert a full hero section in one click.

### 7.2 CSS Architecture

- **Methodology:** BEM naming convention for custom component styles.
- **Approach:** CSS custom properties for all design tokens (synced from `theme.json`). No Sass or PostCSS build step required for end-users — but a `/src/` directory with Sass is provided for developers.
- **No utility-class frameworks** (no Tailwind). Custom, minimal CSS only. This keeps the theme lean and customizable.
- **Critical CSS:** Above-the-fold styles are inlined. Everything else is deferred.

### 7.3 JavaScript

- **Vanilla JS only** for the base theme. Zero jQuery. Zero external runtime dependencies.
- **Alpine.js** included optionally (single script, 15kB gzipped) for interactive components (accordion, tabs, dropdown, carousel).
- **All JS is modular:** Each component's JS lives in its own file, only enqueued when the block is present on the page (`wp_enqueue_script` with block dependency detection).
- **No render-blocking scripts.** All scripts load with `defer` or `async`.

### 7.4 Performance Targets

| Metric | Target |
|---|---|
| Lighthouse Performance | 95+ |
| LCP | < 2.0s |
| INP | < 150ms |
| CLS | < 0.05 |
| FCP | < 1.2s |
| TTFB | < 600ms |
| Total page weight (homepage) | < 400kB transferred |

Implementation methods:
- AVIF/WebP images with `<picture>` fallback. Explicit `width` and `height` on all `<img>` tags.
- Hero image: `fetchpriority="high"` + `loading="eager"`. All other images: `loading="lazy"`.
- Google Fonts loaded via `font-display: swap` with local fallback metrics to prevent layout shift.
- `will-change: transform` applied only to actively animating elements, removed after animation.

### 7.5 SEO & Structured Data

- **JSON-LD** blocks registered: `WebSite`, `Organization`, `BreadcrumbList`, `Article`, `FAQPage`, `Product`.
- **Open Graph + Twitter Card** meta tags built in, with per-page override.
- **Canonical URLs** automatically generated.
- **XML Sitemap** compatible with Yoast, RankMath, and WordPress core sitemap.
- **robots.txt template** includes allowlist for `OAI-SearchBot`, `PerplexityBot`. Blocks `GPTBot`, `CCBot` by default (configurable).
- BLUF-structured content guidance provided in the theme documentation.

### 7.6 Accessibility

- WCAG 2.1 AA compliant.
- All interactive elements keyboard accessible. Visible focus rings (blue outline, not browser default).
- ARIA labels on all icon-only buttons and navigation landmarks.
- Skip-to-content link at top of every page.
- Color contrast minimum 4.5:1 for body text, 3:1 for large text and UI elements.
- Screen reader tested on VoiceOver (macOS/iOS) and NVDA (Windows).

### 7.7 File Structure

```
blue-sage/
├── assets/
│   ├── css/
│   │   ├── style.css              # Theme main stylesheet (WordPress header)
│   │   ├── editor.css             # Block editor styles
│   │   └── components/            # Per-component CSS files
│   ├── js/
│   │   ├── navigation.js
│   │   ├── scroll-animations.js
│   │   └── components/            # Per-component JS files
│   ├── fonts/                     # Self-hosted font files (WOFF2)
│   └── images/                    # Theme default images / placeholders
├── blocks/                        # Custom registered blocks
│   ├── hero/
│   ├── feature-grid/
│   ├── testimonial/
│   ├── pricing-table/
│   └── ...
├── inc/
│   ├── setup.php                  # Theme setup, supports, image sizes
│   ├── enqueue.php                # Scripts & styles
│   ├── block-patterns.php         # Pattern registration
│   ├── custom-blocks.php          # Block registration
│   ├── seo.php                    # Meta tags, JSON-LD, OG
│   ├── woocommerce.php            # WooCommerce support (conditional)
│   └── helpers.php                # Reusable template helper functions
├── parts/
│   ├── header.html
│   ├── footer.html
│   ├── header-landing.html
│   └── footer-minimal.html
├── patterns/
│   ├── hero-centered.php
│   ├── hero-split.php
│   ├── features-icon-grid.php
│   └── ...                        # 20+ curated patterns
├── templates/
│   ├── front-page.html
│   ├── page.html
│   ├── single.html
│   ├── archive.html
│   ├── search.html
│   └── 404.html
├── theme.json                     # Design tokens (single source of truth)
├── functions.php                  # Entry point, loads /inc/ files
├── style.css                      # WordPress theme header
└── screenshot.png                 # 1200×900px theme screenshot
```

---

## 8. Customization System

### 8.1 WordPress Customizer / Global Styles

Users configure the theme via **Site Editor > Styles** (FSE Global Styles panel):
- Switch between 3 header layout variants
- Toggle sticky header on/off
- Choose footer variant (dark / light)
- Select accent color (defaults to Rich Blue; 5 preset options)
- Toggle scroll animations on/off

### 8.2 Block Patterns Library

20+ curated patterns covering every common section type. Organized into categories in the Block Inserter:
- `Blue Sage: Heroes`
- `Blue Sage: Features`
- `Blue Sage: Social Proof`
- `Blue Sage: Pricing`
- `Blue Sage: CTA`
- `Blue Sage: Contact`
- `Blue Sage: Footer`

### 8.3 Child Theme Support

Full child theme support. A starter child theme is included in the package with documented hooks, filters, and action points.

---

## 9. Plugin Compatibility

| Plugin | Support Level |
|---|---|
| WooCommerce | Full styled templates |
| Contact Form 7 | Styled form inputs |
| WPForms | Styled form inputs |
| Yoast SEO | Compatible, deduplicates meta |
| RankMath | Compatible, deduplicates meta |
| WP Rocket | Compatible, no conflicts |
| LiteSpeed Cache | Compatible |
| Elementor | Not supported (FSE theme, not needed) |
| ACF (Advanced Custom Fields) | Compatible, used in custom blocks |

---

## 10. Development Phases

### Phase 1 — Foundation (Week 1–2)
- `theme.json` design token system
- Typography, color, spacing tokens
- Base CSS reset and utility styles
- Header + Footer templates (dark + light variants)
- Navigation component (desktop + mobile)
- Core template files: `front-page`, `page`, `single`, `archive`, `404`

### Phase 2 — Core Components (Week 3–4)
- Hero blocks (all 3 layouts)
- Feature Grid block (all 3 layouts)
- Button block (all variants)
- CTA Section blocks
- Testimonial block
- Metrics / Stats bar

### Phase 3 — Advanced Components (Week 5–6)
- Pricing Table block
- FAQ Accordion block
- Process / Steps block
- Team Grid block
- Blog Card block (standard + featured + list)
- Logo Wall / Marquee block

### Phase 4 — Polish & Performance (Week 7)
- Scroll animations system (IntersectionObserver)
- Micro-interaction layer
- Performance audit (Lighthouse 95+)
- Cross-browser testing (Chrome, Firefox, Safari, Edge)
- Mobile device testing (iOS Safari, Android Chrome)

### Phase 5 — SEO, Accessibility & Launch (Week 8)
- JSON-LD structured data implementation
- WCAG 2.1 AA audit and remediation
- Block patterns library (20+ patterns)
- Theme screenshot + WordPress.org compliance check
- Documentation and setup guide
- Child theme starter

---

## 11. Success Metrics

The theme is considered successful when:

- Lighthouse Performance score: **95+** on the default homepage demo
- Lighthouse Accessibility score: **100**
- WordPress Theme Check: **0 errors, 0 warnings**
- Renders correctly on: Chrome, Firefox, Safari, Edge (latest 2 versions each)
- Renders correctly on: iPhone 14 (375px), iPad (768px), 1440px desktop
- First external download within **48 hours** of public release
- Featured in at least one major WordPress community showcase within **30 days**

---

## 12. Out of Scope (v1.0)

- Multilingual / RTL support (planned for v1.1)
- Dark mode toggle (planned for v1.2)
- WooCommerce advanced features (subscriptions, bundles)
- Custom post types beyond standard WordPress types
- Membership / gating functionality
- Email builder integration
