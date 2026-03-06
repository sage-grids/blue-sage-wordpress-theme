# Developer Overview

Welcome to the **Blue Sage** developer guide. This document provides a high-level overview of the theme's architecture, philosophy, and core technologies.

## 1. Theme Philosophy
Blue Sage is a **Premium Hybrid WordPress Theme** designed for AI-powered SaaS and enterprise websites. It balances the power of the Block Editor (Gutenberg) with the performance and control of a custom PHP-based backend.

- **Performance First:** No heavy frameworks (like Tailwind or Bootstrap). We use Vanilla CSS and modern CSS variables.
- **Privacy & Security:** Self-hosted fonts, no external CDN dependencies, and strictly sanitized data.
- **Developer Experience:** Clean, modular PHP functions, structured block registration, and automated deployment workflows.

## 2. Tech Stack
- **WordPress 6.4+:** Built on the latest block-based standards.
- **PHP 8.1+:** Strongly typed where possible, leveraging modern PHP features.
- **Vanilla CSS:** Custom properties (CSS variables) mapped to `theme.json`.
- **Vanilla JS:** Native browser APIs for animations and interactivity (Intersection Observer, etc.).

## 3. Project Structure
```text
blue-sage-wordpress-theme/
├── assets/             # Global CSS, JS, and Fonts
├── blocks/             # Custom Gutenberg blocks (JSON + PHP Renderers)
├── inc/                # Core PHP logic (Setup, SEO, Admin, Helpers)
├── parts/              # Block template parts (Header, Footer)
├── patterns/           # Pre-built block patterns
├── templates/          # Block templates for pages and posts
├── functions.php       # Theme entry point
├── style.css           # Global styles and theme metadata
└── theme.json          # Global settings and styles (Gutenberg)
```

## 4. Key Concepts

### Hybrid Architecture
While Blue Sage uses `theme.json` for global styles and `templates/` for layouts, it relies on custom PHP renderers for complex blocks (found in `/blocks/`). This allows for dynamic logic that pure HTML blocks cannot provide.

### The `theme.json` Bridge
Most visual styling (colors, typography, spacing) is defined in `theme.json`. These values are exposed as CSS variables (e.g., `--wp--preset--color--rich-blue`) which are then used in both the editor and the front-end.

### Block-First Workflow
Every page in Blue Sage is built using blocks. We provide both **Custom Blocks** (unique logic) and **Block Patterns** (curated layouts of core blocks).

## 5. Getting Started
To begin developing on Blue Sage:
1. Ensure you have a local WordPress environment (LocalWP, DevKinsta, or Docker).
2. Clone this repository into `wp-content/themes/`.
3. Activate the theme in the WordPress Admin.
4. Review the [Setup Guide](../SETUP.md) for detailed configuration.

## 6. Recommended Reading
To dive deeper into specific parts of the theme, please consult the following guides:

- [**Custom Blocks Guide**](blocks.md) - Learn how to build and maintain the theme's core functionality.
- [**Styling & CSS Architecture**](styling.md) - Understand the bridge between `theme.json` and CSS.
- [**Block Patterns Guide**](patterns.md) - See how to create and organize pre-built layouts.
- [**Admin Branding Guide**](admin-branding.md) - Details on how the theme's branding is implemented.
