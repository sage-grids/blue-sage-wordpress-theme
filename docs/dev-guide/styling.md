# Styling & CSS Architecture

Blue Sage uses a performance-optimized approach to styling, relying on **Vanilla CSS** and **WordPress Global Settings (theme.json)**. We avoid heavy CSS frameworks to ensure minimal page weight and maximum flexibility.

## 1. The `theme.json` File
This is the core of the theme's design system. It defines:
- **Color Palette:** Named colors (e.g., `rich-blue`, `deep-navy`) that appear in the block editor.
- **Typography:** Font families, font sizes, and line heights.
- **Spacing:** Preset spacing steps (1 through 11).
- **Shadows:** Custom box-shadow presets.

WordPress automatically converts these settings into CSS variables that you can use in your stylesheets:
- Colors: `--wp--preset--color--[slug]`
- Fonts: `--wp--preset--font-family--[slug]`
- Spacing: `--wp--preset--spacing--[slug]`

## 2. Design Tokens (`style.css`)
We map WordPress presets to internal theme variables in the `:root` section of `style.css`. This provides a more semantic way to reference tokens and allows for easier overrides.

Example:
```css
:root {
    --bs-rich-blue: var(--wp--preset--color--rich-blue);
    --bs-font-display: var(--wp--preset--font-family--display);
    --bs-radius-md: 8px;
}
```

## 3. CSS Organization
- **Global Styles (`style.css`):** Contains typography resets, base layout rules, and common utility classes.
- **Block Styles (`/blocks/[name]/style.css`):** Contains styles specific to a custom block. These are only loaded when the block is used.
- **Editor Styles (`assets/css/editor.css`):** Customizes the look of the Gutenberg editor to match the front-end (e.g., adding padding to the canvas).

## 4. BEM Methodology
For custom blocks and components, we follow the **BEM (Block Element Modifier)** naming convention with a `bs-` prefix:

```css
.bs-card { /* Block */ }
.bs-card__image { /* Element */ }
.bs-card__title--featured { /* Modifier */ }
```

## 5. Responsive Design
We use modern CSS techniques for responsiveness:
- **Fluid Typography:** Using `clamp()` for font sizes that scale with the viewport.
- **CSS Grid & Flexbox:** Preferred over fixed widths or percentage floats.
- **Clamp-based Spacing:** Viewport-aware margins and paddings.

## 6. Self-Hosted Fonts
Fonts are served directly from the `assets/fonts/` directory. No external calls to Google Fonts or Typekit are allowed. This improves performance and ensures GDPR compliance.

## 7. Best Practices
- **Prefer CSS Variables:** Never hardcode HEX or RGB values; always use the provided `--bs-` or `--wp--preset--` variables.
- **Keep it Modular:** If a style is only used by one block, keep it in that block's `style.css`.
- **Avoid `!important`:** Use proper CSS specificity or the WordPress `theme.json` "styles" section to override core block behaviors.
