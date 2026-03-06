# Custom Blocks Guide

Blue Sage includes a set of custom, highly-performant Gutenberg blocks. These blocks are built using the **WordPress Block API (v3)** and rely on PHP for rendering to ensure dynamic data (like blog posts) is always up-to-date.

## 1. Block Structure
Every custom block is located in the `/blocks/` directory and follows this structure:

```text
blocks/my-new-block/
├── block.json      # Metadata, attributes, and asset definitions
├── render.php      # PHP template for front-end & editor rendering
├── style.css       # Block-specific styles (editor & front-end)
└── index.js        # (Optional) Interactivity logic (viewScript)
```

## 2. The `block.json` File
This is the single source of truth for the block. We use it to define:
- **Attributes:** The data schema for the block (strings, integers, booleans).
- **Supports:** Built-in WordPress features (alignment, spacing).
- **Assets:** Enqueuing CSS (`style`) and JS (`viewScript`).
- **Render:** Pointing to the PHP renderer (`render.php`).

Example:
```json
{
    "apiVersion": 3,
    "name": "blue-sage/my-block",
    "title": "BS — My Block",
    "render": "file:./render.php",
    "style": "file:./style.css"
}
```

## 3. PHP Rendering (`render.php`)
We use PHP renderers to maintain a "Single Template" approach. The same PHP file renders the block in both the Gutenberg editor (via the ServerSideRender component) and the front-end.

Inside `render.php`, you have access to:
- `$attributes`: The values saved by the user.
- `$content`: Inner blocks (if applicable).
- `$block`: The block instance (useful for `get_block_wrapper_attributes()`).

**Standard Pattern:**
```php
<?php
/**
 * Blue Sage — My Block: render.php
 * @package BlueSage
 */

$heading = $attributes['heading'] ?? '';
$wrapper_attributes = get_block_wrapper_attributes(['class' => 'my-custom-class']);
?>
<div <?php echo $wrapper_attributes; ?>>
    <h2><?php echo esc_html($heading); ?></h2>
</div>
```

## 4. Block Registration
Blocks are automatically registered in `inc/custom-blocks.php`. If you add a new block folder, ensure it is added to the registration loop if it doesn't follow the automatic pattern.

## 5. Interactivity (`index.js`)
For blocks that require client-side logic (like accordions or carousels), we use `viewScript` in `block.json`.
- These scripts are **only loaded** when the block is present on the page.
- We use Vanilla JavaScript (no jQuery) for maximum performance.
- Use `DOMContentLoaded` or check `document.readyState` to initialize.

## 6. Best Practices
- **Sanitize Everything:** Always use `esc_html()`, `esc_attr()`, or `wp_kses()` in your PHP renderers.
- **BEM Naming:** Use the `bs-` prefix and BEM (Block Element Modifier) for CSS classes (e.g., `.bs-faq__trigger--active`).
- **CSS Variables:** Leverage the theme's CSS variables for colors and spacing to ensure consistency with `theme.json`.
