# Zephyrus Leaf

A static documentation site generator powered by [Zephyrus 2](https://github.com/dadajuice/zephyrus2). Write your docs in Markdown, develop with live-reload, and build to static HTML with a single command.

## Features

- **Markdown content** -- YAML frontmatter, GFM tables, fenced code blocks, heading permalinks
- **Automatic navigation** -- Sidebar and table of contents generated from your content structure
- **Full-text search** -- Client-side fuzzy search across all pages, no server required
- **Syntax highlighting** -- Prism.js with support for PHP, JavaScript, TypeScript, Bash, YAML, JSON, SQL, and Solidity
- **Dark theme** -- Electric violet on deep black. Single theme, no toggle needed
- **One-command build** -- `composer build` generates a `dist/` folder ready for GitHub Pages, Netlify, or any static host
- **Live-reload** -- Edit a file, see changes instantly during development
- **Mobile responsive** -- Hamburger menu with slide-in sidebar drawer on small screens

## Requirements

- PHP 8.4+
- Composer
- `ext-intl` and `ext-mbstring`

## Quick Start

```bash
# Clone or create a new project
composer create-project dadajuice/zephyrus-leaf my-docs

cd my-docs

# Start the dev server with live-reload
composer dev
```

Open [http://localhost:8080](http://localhost:8080) in your browser.

## Project Structure

```
my-docs/
├── config.yml              # Site identity, sections, and build settings
├── content/                # Markdown documentation pages
│   └── getting-started/
│       └── introduction.md
├── app/
│   ├── Controllers/        # Route handlers
│   ├── Models/Core/        # Application bootstrap
│   └── Views/              # Latte templates
│       ├── docs/page.latte
│       ├── layouts/
│       └── partials/       # Nav, sidebar, footer, search, TOC
├── public/
│   ├── assets/
│   │   ├── css/app.css     # Full theme CSS
│   │   ├── js/             # Copy-code, sidebar, search scripts
│   │   └── images/         # Your logo and assets
│   └── index.php
├── src/                    # Leaf library classes
├── bin/
│   ├── router.php          # Dev server entry point
│   └── build.php           # Static site builder
└── dist/                   # Built static output (after composer build)
```

## Writing Content

### Pages

Create Markdown files in `content/{section}/{page}.md`. Each file needs YAML frontmatter:

```yaml
---
title: Installation
order: 2
---

# Installation

Your content here...
```

The `title` appears in the sidebar. The `order` controls the sort order within a section (lower numbers come first).

### Sections

Define sections and their display names in `config.yml`:

```yaml
leaf:
  sections:
    getting-started: "Getting Started"
    guides: "Guides"
    api-reference: "API Reference"
```

The order in the YAML file determines the sidebar order. Create a matching directory under `content/` for each section.

### Cross-references

Link between pages using absolute paths:

```markdown
See the [Installation](/getting-started/installation) guide.
```

## Configuration

All site settings live in the `leaf:` section of `config.yml`:

```yaml
leaf:
  name: "My Project"             # Displayed in the navbar
  version: "1.0.0"               # Version badge in the navbar
  description: "A short tagline" # Used in meta tags
  github_url: ""                 # GitHub link in the navbar (leave empty to hide)
  content_path: "content"        # Where markdown files live
  sections:                      # Sidebar sections (order matters)
    getting-started: "Getting Started"
  output_path: "dist"            # Static build output directory
  base_url: ""                   # Base URL for production (e.g. "/docs")
  author: "Your Name"            # Displayed in the sidebar footer
  author_url: ""                 # Author link
  license: "MIT"                 # License badge in the footer
```

## Customization

### Logo

Replace the default SVG icon in `app/Views/partials/nav.latte` with your own logo. For an image logo, use:

```html
<a href="/" class="nav-logo">
    <img src="/assets/images/your-logo.svg" alt="My Project" class="nav-logo-img">
</a>
```

### Theme

The design system is defined as CSS custom properties in `public/assets/css/app.css`:

| Token | Default | Usage |
|---|---|---|
| `--bg-deep` | `#08080d` | Page background |
| `--bg-surface` | `#11111a` | Sidebar, cards, code blocks |
| `--bg-elevated` | `#1a1a27` | Hover states |
| `--accent` | `#8b5cf6` | Primary accent (violet) |
| `--accent-light` | `#a78bfa` | Hover accent |
| `--text` | `#d4d4d8` | Body text |
| `--text-bright` | `#fafafa` | Headings |
| `--font-body` | Inter | Body font |
| `--font-mono` | JetBrains Mono | Code font |

### Syntax Highlighting

Additional Prism.js languages can be added in `app/Views/layouts/docs.latte`:

```html
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-rust.min.js"></script>
```

## Commands

```bash
composer dev     # Start dev server with live-reload (localhost:8080)
composer build   # Generate static HTML to dist/
composer test    # Run tests
```

## Deployment

After running `composer build`, the `dist/` directory contains a fully self-contained static site. Deploy it to any static hosting:

**GitHub Pages** -- Push `dist/` to a `gh-pages` branch or configure GitHub Pages to serve from `dist/` on `main`.

**Netlify / Vercel** -- Point the build output directory to `dist/`.

**Manual** -- Upload the contents of `dist/` to any web server.

The build also generates a `404.html` at the root of `dist/` for GitHub Pages compatibility.

## License

MIT
