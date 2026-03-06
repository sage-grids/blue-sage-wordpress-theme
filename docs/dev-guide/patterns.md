# Block Patterns Guide

Block patterns are pre-defined layouts of blocks that users can easily insert into any page. In Blue Sage, patterns are the primary way to build consistent, high-end layouts quickly.

## 1. How Patterns Work
In Blue Sage, block patterns are stored in the `/patterns/` directory. Each PHP file in this folder represents a single pattern. 

WordPress automatically registers these patterns based on a special comment header at the top of each file.

## 2. Pattern Registration Category
Blue Sage registers several custom categories for patterns:
- `blue-sage-heroes`
- `blue-sage-features`
- `blue-sage-social-proof`
- `blue-sage-cta`
- `blue-sage-pricing`
- `blue-sage-team`
- `blue-sage-blog`

## 3. Creating a New Pattern
To create a new pattern, follow these steps:
1. Create a new file in `/patterns/` (e.g., `my-new-pattern.php`).
2. Add the required comment header.
3. Add the block markup below the header.

**Example File Structure:**
```php
<?php
/**
 * Title: My New Pattern
 * Slug: blue-sage/my-new-pattern
 * Categories: blue-sage-features
 * Description: A brief description of the pattern.
 * Keywords: feature, layout, custom
 */
?>
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- Insert blocks here -->
</div>
<!-- /wp:group -->
```

## 4. Automatic Loading
The loading logic is handled in `inc/block-patterns.php`. It uses `glob()` to find all PHP files in the `/patterns/` folder and requires them on the `init` hook. This means you do not need to manually register your new pattern in `functions.php`.

## 5. Syncing with Site Editor
If you modify a pattern in the WordPress Site Editor and want to save it back to the theme:
1. Open the pattern in the editor.
2. Click the three dots (options) and select **"Copy block"**.
3. Paste the markup into your PHP file in the `/patterns/` directory.
4. Ensure the metadata (Title, Slug, etc.) remains intact.

## 6. Best Practices
- **Use Theme Tokens:** Always use the theme's colors and spacing presets within the block attributes.
- **Keep it Simple:** Patterns should provide a great starting point but remain flexible enough for users to customize.
- **Descriptive Titles:** Use clear titles that help users find exactly what they need in the Block Inserter.
