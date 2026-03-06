# Admin Branding Guide

Blue Sage is a white-labeled, high-end theme that includes custom branding for **SAGE GRIDS LTD**. This document outlines how branding is integrated into the WordPress admin panel and where to make changes if necessary.

## 1. PHP Docblocks
Every PHP file in the theme must include a standardized header docblock. This credits the author and company and provides relevant links for developers.

**Standard Docblock:**
```php
/**
 * [File Description]
 *
 * @package BlueSage
 * @author Ilyas Serter <hello@sagegrids.com>
 * @company SAGE GRIDS LTD <https://www.sagegrids.com>
 * @link https://github.com/sage-grids/blue-sage-wordpress-theme
 */
```

## 2. Theme Admin Page
The theme registers a custom admin page under **Appearance > Blue Sage Theme**. This page serves as a branded dashboard for site owners and administrators.

- **File Location:** `inc/admin.php`
- **Hook:** `admin_menu`
- **Capabilities:** `edit_theme_options`

### Content of the Admin Page:
- **Branding:** "AI-Powered Systems. Built with Expertise."
- **About SAGE GRIDS:** Information about the development company.
- **Social & Support:** Direct links to LinkedIn, X (Twitter), and business e-mail for support and collaboration.

## 3. Footer Copyright
The front-end footer includes a "Designed by SAGE GRIDS" link within the copyright notice. This is implemented directly in the block template parts.

- **Files:** `parts/footer.html` and `parts/footer-minimal.html`
- **Markup:**
```html
<p>&copy; 2026 Blue Sage. All rights reserved. Designed by <a href="https://www.sagegrids.com" target="_blank" rel="noopener">SAGE GRIDS</a></p>
```

## 4. Customizing Branding
To update the branding (e.g., if the company name or links change):
1. **Admin Page:** Edit `inc/admin.php`.
2. **Footer:** Edit the HTML in `parts/footer.html` and `parts/footer-minimal.html`.
3. **Docblocks:** Use a global search-and-replace for the `@author`, `@company`, and `@link` tags in all `.php` files.
