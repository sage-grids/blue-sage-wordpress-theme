# Task: Fix Block Validation Errors Across All Templates

**Priority:** High
**Status:** Open
**Affects:** WordPress Site Editor canvas — "Block contains unexpected or invalid content" warnings

When WordPress's block editor (Gutenberg / Site Editor) loads a template, it validates every static core block's stored HTML against the block's `save()` function output. Mismatches produce "Block contains unexpected or invalid content" warnings that require users to dismiss or attempt recovery, and prevent clean template editing.

All custom Blue Sage blocks (`blue-sage/*`) use server-side rendering (`save: () => null`) so they are exempt. Only **core WordPress blocks** need attention here.

---

## Issue 1 — Missing `anchor` attribute on every `wp:group` main wrapper

**Root cause:** Every template stores `id="main-content"` in the `<main>` element's HTML, but the block comment does not declare `"anchor":"main-content"`. The group block's `save()` only emits an `id` attribute when an `anchor` is present in the block attributes. Without it, the stored HTML does not match the save output → validation error.

**Fix:** Add `"anchor":"main-content"` to the block comment attributes for the outer `wp:group` in every template, and remove the bare `id="main-content"` from the stored HTML (WordPress will output it via the anchor attribute at render time).

**Before:**
```html
<!-- wp:group {"tagName":"main","className":"site-main","layout":{"type":"constrained"}} -->
<main class="wp-block-group site-main" id="main-content">
```

**After:**
```html
<!-- wp:group {"tagName":"main","className":"site-main","anchor":"main-content","layout":{"type":"constrained"}} -->
<main class="wp-block-group site-main" id="main-content">
```

**Affected files (8):**
- `templates/front-page.html` — line 3
- `templates/page.html` — line 3
- `templates/single.html` — line 3
- `templates/archive.html` — line 3
- `templates/404.html` — line 3
- `templates/index.html` — line 3
- `templates/page-full-width.html` — line 3
- `templates/page-landing.html` — line 3

---

## Issue 2 — `wp:separator` missing `has-alpha-channel-opacity` class

**Root cause:** The separator block's `save()` has included `has-alpha-channel-opacity` as a default class since WordPress 5.6. Any separator stored without it will fail validation.

**Fix:** Add `has-alpha-channel-opacity` to the `<hr>` element's class list, and remove the space before `/>` (WordPress save output uses self-closing without a space: `<hr .../>`).

**Before:**
```html
<!-- wp:separator {"style":{...}} -->
<hr class="wp-block-separator" />
<!-- /wp:separator -->
```

**After:**
```html
<!-- wp:separator {"style":{...}} -->
<hr class="wp-block-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->
```

**Affected files (1):**
- `templates/single.html` — line 36–38 (the post divider above comments)

> `parts/footer.html` — already fixed (both separators corrected in previous pass).

---

## Issue 3 — `wp:buttons` missing `is-content-justification-center` class

**Root cause:** The buttons block's `save()` emits a justification class (`is-content-justification-*`) when the layout's `justifyContent` is set. In `404.html` the layout has `"justifyContent":"center"` but the stored HTML omits `is-content-justification-center`.

**Fix:** Add the justification class to the stored `<div>` HTML.

**Before:**
```html
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap","columnGap":"12px"}} -->
<div class="wp-block-buttons">
```

**After:**
```html
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap","columnGap":"12px"}} -->
<div class="wp-block-buttons is-content-justification-center">
```

**Affected files (1):**
- `templates/404.html` — line 26–27

---

## Issue 4 — `wp:group` article cards missing `is-layout-flex` orientation class

**Root cause:** Groups with `"layout":{"type":"flex","flexDirection":"column"}` emit `is-vertical` in some WordPress versions as part of the save output (not just via PHP render). The stored HTML for the blog card groups in archive/index templates uses only `wp-block-group bs-card`, potentially causing deprecation warnings.

**Fix:** Add `is-vertical` to the stored `<article>` HTML for flex-column groups.

**Before:**
```html
<!-- wp:group {"className":"bs-card","tagName":"article","layout":{"type":"flex","flexWrap":"wrap","flexDirection":"column","rowGap":"16px"}} -->
<article class="wp-block-group bs-card">
```

**After:**
```html
<!-- wp:group {"className":"bs-card","tagName":"article","layout":{"type":"flex","flexWrap":"wrap","flexDirection":"column","rowGap":"16px"}} -->
<article class="wp-block-group bs-card is-vertical">
```

**Affected files (2):**
- `templates/archive.html` — line 20–21
- `templates/index.html` — line 19–20

> **Note:** Verify in the Site Editor first — if no error shows for these, skip this fix. The `is-vertical` class may be PHP-injected in the current WP version.

---

## Issue 5 — `wp:group` entry-meta and entry-header missing flex justification classes

**Root cause:** Groups using `"layout":{"type":"flex"}` with explicit `alignItems` or `justifyContent` may emit corresponding classes (`is-nowrap`, `is-content-justification-*`) in their save output. The stored HTML for these groups is bare.

**Fix:** Audit and add only the classes that WordPress's save function emits for flex groups. These can be confirmed by inserting the equivalent block in the Site Editor and inspecting what HTML it generates.

**Affected files (1):**
- `templates/single.html` — lines 17–22 (entry-meta group) and lines 7–28 (entry-header group)

> **Note:** Low risk — these groups render correctly on the frontend. Prioritise Issues 1–3 first.

---

## Verification Steps

After applying fixes, verify in the Site Editor:

1. Open each template: **wp-admin → Site Editor → Templates → Blue Sage**
2. Confirm zero "Block contains unexpected or invalid content" banners
3. Check frontend at `http://localhost:8808/` — all pages must still render identically

**Quick smoke test (CLI):**
```bash
curl -s http://localhost:8808/ | grep -c "bs-hero--centered"
# Should return 1

curl -s http://localhost:8808/?page_id=2 | grep "entry-title"
# Should return the page title block markup
```

---

## Completion Checklist

- [ ] Issue 1 — Add `anchor` to all 8 main `wp:group` wrappers
- [ ] Issue 2 — Fix separator in `single.html`
- [ ] Issue 3 — Fix buttons block in `404.html`
- [ ] Issue 4 — Verify/fix article card groups in `archive.html` and `index.html`
- [ ] Issue 5 — Audit single.html flex groups (low priority)
- [ ] Run Site Editor verification on all 8 templates
