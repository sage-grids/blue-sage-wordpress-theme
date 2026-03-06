# Blue Sage — Setup Guide

**Version:** 1.0.0
**Requires:** WordPress 6.4+, PHP 8.1+

---

## 1. Installation

### Via WordPress Admin

1. Download `blue-sage.zip`
2. Go to **Appearance > Themes > Add New > Upload Theme**
3. Upload the ZIP and click **Install Now**
4. Click **Activate**

### Via FTP

1. Unzip `blue-sage.zip`
2. Upload the `blue-sage` folder to `/wp-content/themes/`
3. Activate via **Appearance > Themes**

### Via WP-CLI

```bash
wp theme install blue-sage.zip --activate
```

---

## 2. First-Run Checklist

After activating, complete these steps in order:

- [ ] **Set site identity** — Appearance > Customize > Site Identity. Upload your logo and set the site title.
- [ ] **Configure SEO & Social** — Appearance > Customize > SEO & Social. Upload your default social share image (1200×630 px) and organization logo.
- [ ] **Build your homepage** — Pages > Add New. Assign it as your static front page via Settings > Reading.
- [ ] **Configure navigation** — Appearance > Editor > Navigation. Assign your primary menu.
- [ ] **Choose header variant** — Appearance > Editor > Template Parts > Header. Three layouts available.
- [ ] **Choose footer variant** — Appearance > Editor > Template Parts > Footer. Dark (navy) or Light (off-white).
- [ ] **Add font files** — Place WOFF2 files in `assets/fonts/` (see §7 below).

---

## 3. Using Block Patterns

Block patterns let you insert complete, pre-styled page sections in a single click.

1. Open the Block Editor on any page or post
2. Click the **+** inserter (top left or between blocks)
3. Switch to the **Patterns** tab
4. Browse the **Blue Sage** categories:
   - Blue Sage: Heroes
   - Blue Sage: Features
   - Blue Sage: Social Proof
   - Blue Sage: CTA
   - Blue Sage: Pricing
   - Blue Sage: Team
   - Blue Sage: Blog
5. Click any pattern to insert it. Edit the placeholder text and images.

21 patterns are included covering every common section type.

---

## 4. Customizing Colors and Fonts

Blue Sage uses WordPress Global Styles as the single source of truth for design tokens.

1. Go to **Appearance > Editor**
2. Click the **Styles** icon (half-circle, top right)
3. Edit colors, typography, and spacing

All design decisions cascade from here to every block. Do not override colors via block-level controls unless you intend a one-off exception.

**Color palette (defaults):**

| Name | Hex | Usage |
|---|---|---|
| Deep Navy | `#0A1628` | Dark section backgrounds, headings |
| Rich Blue | `#1E4FD8` | CTAs, links, active states |
| Sky Blue | `#3B82F6` | Hover states, icons |
| Sage Green | `#10B981` | Eyebrow labels, success states |
| White | `#FFFFFF` | Page background, card surfaces |
| Off-White | `#F8FAFF` | Section alternates |

---

## 5. Header Variants

Three header layouts are available. To switch:

1. Go to **Appearance > Editor > Template Parts**
2. Click **Header**
3. In the block list, find the Navigation block and adjust alignment:
   - **Centered** — Logo left, nav centered, CTA right (default)
   - **Right-aligned** — Logo left, all nav items right
   - **Minimal** — Logo only, no nav (use `header-landing` part for landing pages)

The header is sticky and gains a frosted-glass backdrop on scroll automatically.

---

## 6. Custom Blocks Reference

All blocks are inserted via the Block Editor inserter under the **Blue Sage** category.

| Block | Key attributes |
|---|---|
| **Hero** | `layout` (centered/split/editorial), `eyebrow`, `heading`, `subheading`, `primaryCta`, `secondaryCta`, `mediaUrl` |
| **Feature Grid** | `layout` (icon-grid/alternating/large-cards), `features` (array of title/body/icon) |
| **Metrics Bar** | `metrics` (array of value/prefix/suffix/label) — counter animates on scroll |
| **Testimonial Carousel** | `testimonials` (array of quote/author/title/company/photo) |
| **Pricing Table** | `plans` (array), `billingToggle` (boolean), `currency` |
| **CTA Section** | `layout` (dark/inline), `heading`, `subheading`, `primaryCta`, `secondaryCta` |
| **FAQ Accordion** | `items` (array of question/answer) — auto-generates FAQPage JSON-LD |
| **Process Steps** | `layout` (numbered/timeline), `steps` (array of title/body) |
| **Team Grid** | `members` (array of name/title/photo/bio/social) |
| **Logo Wall** | `logos` (array of imageUrl/alt/href), `marquee` (boolean) |
| **Blog Cards** | `layout` (standard/featured/list), `postsPerPage`, `category` |

---

## 7. Self-hosted Fonts

Blue Sage ships with `@font-face` declarations ready — you just need to add the WOFF2 files.

**Required files** (place in `assets/fonts/`):

```
plus-jakarta-sans-400.woff2
plus-jakarta-sans-600.woff2
plus-jakarta-sans-700.woff2
plus-jakarta-sans-800.woff2
inter-400.woff2
inter-500.woff2
jetbrains-mono-400.woff2
jetbrains-mono-500.woff2
```

**Download:** Use [google-webfonts-helper](https://gwfh.mranftl.com/fonts) to download Latin-subset WOFF2 files for:
- Plus Jakarta Sans (weights: 400, 600, 700, 800)
- Inter (weights: 400, 500)
- JetBrains Mono (weights: 400, 500)

Once placed in `assets/fonts/`, fonts load locally with no Google CDN request.

---

## 8. Performance Tips

Blue Sage targets Lighthouse Performance 95+ out of the box. To maintain that score:

**Images**
- Use AVIF or WebP format (WordPress 6.1+ converts on upload)
- Always fill the "Alt text" field
- The hero image on the front page gets `fetchpriority="high"` automatically — do not manually add `loading="lazy"` to it

**Caching**
- Compatible with WP Rocket, LiteSpeed Cache, and W3 Total Cache
- No special exclusions required

**Third-party scripts**
- Each external script you add (chat widgets, analytics, etc.) costs Lighthouse points
- Load analytics via `wp_enqueue_script` with `defer` or `async`

**Font loading**
- The two critical WOFF2 files (`plus-jakarta-sans-700.woff2`, `inter-400.woff2`) are preloaded automatically
- Do not add more preloads without measuring impact

---

## 9. Child Theme Usage

A starter child theme is included in the `blue-sage-child/` folder.

1. Copy `blue-sage-child/` to `wp-content/themes/`
2. Activate via **Appearance > Themes** (parent theme must also be installed)
3. Add CSS overrides to `style.css`
4. Add PHP customizations to `functions.php`

See `blue-sage-child/README.md` for documented hook points and examples.

---

## 10. Plugin Compatibility

| Plugin | Status |
|---|---|
| Yoast SEO | Compatible — theme SEO output disabled automatically |
| RankMath | Compatible — theme SEO output disabled automatically |
| WooCommerce | Basic styling included |
| Contact Form 7 | Styled inputs |
| WPForms | Styled inputs |
| WP Rocket | Compatible, no exclusions needed |
| LiteSpeed Cache | Compatible |
| ACF | Compatible, used internally by custom blocks |
| Elementor | Not supported (FSE theme) |
