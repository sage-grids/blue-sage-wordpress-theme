# Code Review Task Definition

This document defines the standards and checklist for reviewing code in the **Blue Sage** WordPress theme. Use this as a guide for manual reviews or as a prompt for AI-assisted code reviews.

## Core Principles
1. **Performance First:** No heavy frameworks (Tailwind, Bootstrap, jQuery). Use native APIs and Vanilla CSS/JS.
2. **Security & Privacy:** Strictly sanitize all outputs (`esc_html`, `esc_attr`, `wp_kses`). No external CDNs; all assets (fonts, scripts) must be local.
3. **Maintainability:** Modular PHP, clear naming conventions (BEM for CSS), and consistent file structure.
4. **Gutenberg Compatibility:** Seamless integration between `theme.json` and custom block renderers.

---

## Code Review Checklist

### 1. General PHP Standards
- [ ] **PHP 8.1+ Features:** Leverage modern PHP features (constructor property promotion, enums, readonly properties) where appropriate.
- [ ] **Typing:** Use strict typing (`declare(strict_types=1);`) and type hints for function arguments and return values.
- [ ] **DocBlocks:** Every function and file should have a clear DocBlock explaining its purpose and parameters.
- [ ] **Naming:** Use `snake_case` for functions and variables, `PascalCase` for classes. Use the `blue_sage_` prefix for global functions.

### 2. WordPress & Security
- [ ] **Sanitization:** All dynamic data in `render.php` and templates must be sanitized (`esc_html()`, `esc_attr()`, `wp_kses()`, etc.).
- [ ] **Internationalization:** All strings must be translatable using `__()` or `_e()` with the `'blue-sage'` text domain.
- [ ] **Database Queries:** Use `$wpdb->prepare()` for custom queries. Prefer WP native functions (`get_posts`, `WP_Query`) over raw SQL.
- [ ] **Hooks:** Ensure `add_action` and `add_filter` are used correctly and registered in the appropriate file (e.g., `inc/setup.php` or `inc/enqueue.php`).

### 3. Custom Blocks (`/blocks/`)
- [ ] **Single Source of Truth:** Styles and attributes should be defined in `block.json`.
- [ ] **Wrapper Attributes:** Use `get_block_wrapper_attributes()` in `render.php`.
- [ ] **Server-Side Rendering:** Ensure the PHP renderer handles all block attributes correctly.
- [ ] **Asset Loading:** Verify that scripts and styles are only loaded when the block is used (via `block.json`).

### 4. CSS & Styling
- [ ] **Vanilla CSS:** No pre-processors or utility-first frameworks.
- [ ] **BEM Naming:** Use the `bs-` prefix and BEM convention (e.g., `.bs-hero__content--large`).
- [ ] **CSS Variables:** Use theme variables (from `theme.json`) for colors, typography, and spacing (e.g., `var(--wp--preset--color--rich-blue)`).
- [ ] **Responsive Design:** Use modern CSS (Flexbox, Grid, Clamp) instead of excessive media queries.

### 5. JavaScript
- [ ] **Vanilla JS:** No jQuery or external libraries unless absolutely necessary and approved.
- [ ] **Performance:** Use `IntersectionObserver` for animations and scroll-based effects.
- [ ] **Initialization:** Ensure scripts are initialized only after the DOM is ready and check for the existence of elements before acting on them.

---

## AI Reviewer Prompt

When asking an AI to perform a code review, use the following prompt:

> "Act as a Senior WordPress Developer reviewing a pull request for the 'Blue Sage' theme. 
> 
> **Context:** Blue Sage is a high-performance, hybrid theme for enterprise SaaS. It uses PHP 8.1+, Vanilla CSS/JS, and custom Gutenberg blocks with PHP renderers.
> 
> **Review Goals:**
> 1. Ensure strictly sanitized outputs (Security).
> 2. Verify adherence to Vanilla CSS and BEM naming (`bs-` prefix).
> 3. Check for modern PHP 8.1+ standards and strict typing.
> 4. Ensure no external dependencies or heavy frameworks are introduced.
> 5. Confirm that custom blocks follow the `block.json` + `render.php` architecture.
> 
> Please provide specific, actionable feedback on the following code changes:"
