# Visual Improvement Roadmap

This document outlines the findings from the Expert Theme Review and provides actionable tasks to elevate the **Blue Sage** theme to a world-class "High-End SaaS/Enterprise" level.

---

## 1. Low Effort / High Impact (Quick Wins)

- [ ] **Interactive Depth for Hero Buttons:**
    - Currently, `.bs-btn` has a simple `translateY(-1px)`. 
    - **Improvement:** Add a subtle `scale(1.02)` and enhance the `box-shadow` on hover to make it feel more tactile.
    - *Target:* `blocks/cta-section/style.css`, `style.css`.

- [ ] **Refine Heading Letter Spacing:**
    - Large headings (H1, Display) benefit from tighter tracking.
    - **Improvement:** Ensure `letter-spacing: -0.04em` is consistently applied to all `.bs-display` and `-0.03em` to `h1`.
    - *Target:* `theme.json`, `style.css`.

- [ ] **Modernize Section Dividers:**
    - Standard `<hr>` is a bit plain.
    - **Improvement:** Create a "Gradient Rule" class that uses a fade-to-transparent linear gradient.
    - *Target:* `style.css`.

- [ ] **Smooth Image Reveals:**
    - Add a subtle scale-down effect to images when they enter the viewport.
    - **Improvement:** Update `js-fade-up` to include `scale(1.05)` in its initial state and `scale(1)` in `.is-visible`.
    - *Target:* `style.css`, `assets/js/scroll-animations.js`.

---

## 2. Design Refinements (Polishing the Details)

- [ ] **Consistent Dark Mode Tokens:**
    - Some sections (like Hero Centered) use hardcoded rgba values for overlays.
    - **Improvement:** Define a set of "Translucent White/Black" variables in `theme.json` (e.g., `--bs-white-10`, `--bs-white-60`) to ensure consistent transparency levels across the theme.
    - *Target:* `theme.json`.

- [ ] **Enhanced Card Hover States:**
    - `.bs-card` hover is good, but can be better.
    - **Improvement:** Add a subtle border-color transition to `var(--bs-rich-blue)` at 10% opacity and slightly increase the `box-shadow` spread.
    - *Target:* `style.css`.

- [ ] **Fluid Typography Audit:**
    - Check if `clamp` values are consistent across all custom blocks and match the `theme.json` fluid scale.
    - *Target:* All files in `/blocks/`.

---

## 3. New Interactive Features (The "Premium" Feel)

- [ ] **Glassmorphism Header Upgrade:**
    - The sticky header is currently a simple solid color.
    - **Improvement:** Increase the `backdrop-filter` blur and use a very subtle border-bottom with a white-to-transparent gradient to simulate a "glass edge."
    - *Target:* `style.css`.

- [ ] **Magnetic Button Effect (JS):**
    - For the primary Hero CTA, add a subtle "magnetic" pull when the cursor gets close.
    - *Target:* New JS file or `assets/js/page-entrance.js`.

- [ ] **Text Reveal Animation:**
    - For the Editorial Hero, implement a "mask" reveal where text slides up from an invisible container.
    - *Target:* `blocks/hero/style.css`, `assets/js/scroll-animations.js`.

---

## 4. Implementation Log
*Use this section to track which improvements have been applied.*

| Task | Status | Date |
| :--- | :--- | :--- |
| Create Review Roadmap | Done | 2026-03-06 |
| ... | ... | ... |
