# Theme Review & Visual Excellence Task Definition

This document defines the standards and process for reviewing the **Blue Sage** theme from a creative design and high-end development perspective. The goal is to ensure the theme maintains a "Premium SaaS/Enterprise" aesthetic.

## Core Design Philosophy
1. **Sophisticated Minimalism:** Clean layouts with purposeful white space. Avoid clutter.
2. **Typography as Structure:** High-contrast typographic hierarchy using *Plus Jakarta Sans* for impact and *Inter* for readability.
3. **Intentional Color:** A palette that feels "Tech-Forward" (Deep Navies, Electric Indigos, Sage Greens) with subtle gradients and depth.
4. **Fluid Motion:** Smooth, non-intrusive transitions and micro-interactions that make the site feel "alive."
5. **Precision:** Consistent spacing, border-radii, and alignment across all blocks and templates.

---

## Visual Review Checklist

### 1. Typography & Hierarchy
- [ ] **Scale:** Are the font sizes (Display through Small) proportionally balanced? Is the `Display` size impactful on large screens?
- [ ] **Rhythm:** Is the line-height (1.7 for body, 1.1-1.4 for headers) providing optimal readability?
- [ ] **Contrast:** Is there enough weight contrast between headings and body text?
- [ ] **Letter Spacing:** Are negative letter-spacings (e.g., `-0.03em` on H1) applied correctly to large headings for a high-end feel?

### 2. Color & Depth
- [ ] **Palette Usage:** Are `Deep Navy` and `Rich Blue` used effectively to create depth?
- [ ] **Gradients:** Are gradients subtle and modern, or do they feel dated?
- [ ] **Elevation:** Are the custom shadows (`sm`, `md`, `lg`, `blue`) used to create a clear sense of layering and hierarchy?
- [ ] **Dark Mode Prep:** Does the current color system allow for a future dark mode implementation?

### 3. Spacing & Composition
- [ ] **Consistency:** Are blocks utilizing the 11-step spacing scale from `theme.json`?
- [ ] **White Space:** Is there enough "breathing room" between major sections (e.g., using `spacing--10` or `11`)?
- [ ] **Grid Alignment:** Do custom blocks align perfectly with the `contentSize` (800px) and `wideSize` (1280px) constraints?

### 4. Interactive Elements
- [ ] **Buttons:** Do buttons feel tactile? Are the hover states (`background` change + `spring` transition) smooth?
- [ ] **Micro-interactions:** Are there subtle hover effects on links, cards, and icons?
- [ ] **Loading/Entrance:** Do sections "fade in" or "slide up" elegantly as the user scrolls?

### 5. Custom Block Polish
- [ ] **BEM Logic:** Is the CSS clean and modular?
- [ ] **Component Integrity:** Does each block look good in both the Editor and the Front-end?
- [ ] **Responsive Refinement:** Do blocks stack gracefully on mobile without losing their "high-end" character?

---

## AI Creative Director Prompt

When asking an AI to perform a visual and structural theme review, use the following prompt:

> "Act as an Expert WordPress Developer and High-End Creative Director. Your goal is to review the 'Blue Sage' theme and identify opportunities to elevate its visual quality to a world-class SaaS/Enterprise level.
> 
> **Context:** Blue Sage uses a 'Plus Jakarta Sans' / 'Inter' typographic pair, a Deep Navy/Rich Blue palette, and a custom block-based architecture. It avoids Tailwind/Bootstrap for a leaner, bespoke feel.
> 
> **Instructions:**
> 1. Analyze the `theme.json` and CSS files for consistency in spacing, typography, and color.
> 2. Review the `/blocks/` renderers and styles to identify 'visual gaps' (e.g., lack of hover states, poor mobile stacking, or insufficient white space).
> 3. Suggest specific CSS or `theme.json` tweaks to improve the 'high-end' feel (e.g., better shadows, more fluid transitions, refined typography).
> 4. **Output Format:** You MUST write your findings and specific actionable tasks into `docs/tasks/improve-theme.md`. Organize them by 'Low Effort/High Impact', 'Design Refinements', and 'New Interactive Features'.
> 
> Focus on making the theme feel 'expensive', 'performant', and 'polished'."
