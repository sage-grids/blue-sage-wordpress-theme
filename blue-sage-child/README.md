# Blue Sage Child Theme

Starter child theme for Blue Sage.

## Getting Started

1. Copy the `blue-sage-child` folder to `wp-content/themes/`
2. Ensure the parent **Blue Sage** theme is also installed
3. Activate via Appearance > Themes
4. Add CSS overrides to `style.css`
5. Add PHP customizations to `functions.php`

## Available Hook Points

| Hook | Type | When it fires | Arguments |
|---|---|---|---|
| `blue_sage_before_hero` | Action | Before hero block renders | `$attributes` (array) |
| `blue_sage_after_hero` | Action | After hero block renders | `$attributes` (array) |
| `blue_sage_jsonld_organization` | Filter | Before Organization JSON-LD is encoded | `$schema` (array) |
| `robots_txt` | Filter (WP core) | When virtual robots.txt is generated | `$output` (string) |

## Examples

See `functions.php` for commented examples of each hook.

## License

GPLv2 or later — https://www.gnu.org/licenses/gpl-2.0.html
