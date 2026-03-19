# CLAUDE.md — Zephyrus Leaf

## What Is This

`dadajuice/zephyrus-leaf` is a **static content site template** powered by the Zephyrus 2 framework. It provides everything needed to build documentation sites, blogs, and landing pages — with live-reload during development and one-command static builds. Users scaffold a new site via `composer create-project dadajuice/zephyrus-leaf my-docs`.

**Package:** `dadajuice/zephyrus-leaf` (type: project)
**Branch:** `main`
**License:** MIT
**PHP:** `^8.4`
**Tests:** 58 tests, 130 assertions (PHPUnit 11)

## Ecosystem

| Repo | Path | Purpose |
|---|---|---|
| **zephyrus2** | `/Users/dtucker/www/zephyrus2` | Core framework library (dependency) |
| **zephyrus2-framework** | `/Users/dtucker/www/zephyrus2-framework` | Application template |
| **zephyrus-docs** | `/Users/dtucker/www/zephyrus-docs` | Documentation website (uses leaf as dependency) |
| **zephyrus-leaf** (this) | `/Users/dtucker/www/zephyrus-leaf` | Static content site template |

## Architecture

### Two Layers

1. **Library (`src/` — namespace `Leaf\`)** — Reusable classes that `zephyrus-docs` imports as a dependency.
2. **Application (`app/` — namespaces `App\Controllers\`, `App\Models\`)** — Controllers, kernel, views that ship as the template starter.

### Library Classes (`src/`)

| Class | Purpose |
|---|---|
| `StaticSiteBuilder` | Renders all routes to static HTML in `dist/`. Drives `composer build`. |
| `StaticBuildResult` | Value object holding build stats (pages, errors, duration). |
| `DevRouter` | Serves static files + live-reload endpoint for PHP's built-in server. |
| `FileWatcher` | Scans directories for mtime changes, returns MD5 hash for live-reload. |
| `LeafLatteExtension` | Injects `$leafName`, `$leafVersion`, `$leafGithubUrl`, `$leafAuthor`, `$leafAuthorUrl`, `$leafLicense`, `$leafDescription` into every Latte template. |
| `Config/LeafConfig` | Typed config section extending `ConfigSection` for the `leaf:` YAML block. |
| `Content/ContentLoader` | Reads markdown content from `content/{section}/{page}.md`, builds sidebar navigation and page data. Sections ordered by `LeafConfig->sections`. |
| `Content/MarkdownParser` | Converts Markdown to HTML via league/commonmark with heading permalinks, TOC extraction, and auto-ID generation. |
| `Content/ParsedMarkdown` | Value object: `html`, `title`, `tableOfContents` (array of `{id, text, level}`). |
| `Content/SearchIndexBuilder` | Builds a JSON search index from all content pages for client-side search. |

### Application Layer (`app/`)

| File | Purpose |
|---|---|
| `Controllers/DocsController.php` | `GET /` (redirect to first page), `GET /{section}/{slug}` (page), `GET /search.json` (index), `GET /404` (error page) |
| `Models/Core/Kernel.php` | Abstract bootstrap: loads config, creates Latte engine with `LeafLatteExtension`, wires controllers with dependency injection, registers 404 handler |
| `Models/Core/Application.php` | Concrete `Kernel` subclass (empty — relies on parent defaults) |

### Templates (`app/Views/`)

```
app/Views/
├── 404.latte                 # Themed 404 page
├── docs/page.latte           # Documentation page template
├── layouts/
│   ├── docs.latte            # Docs layout (sidebar + content + TOC)
│   └── landing.latte         # Landing layout (full-width)
└── partials/
    ├── head.latte            # <head> tag, fonts, Tailwind CDN, Prism.js, live-reload script
    ├── nav.latte             # Top navigation bar
    ├── footer.latte          # Site footer
    ├── sidebar.latte         # Docs sidebar navigation
    ├── toc.latte             # Table of contents (right rail)
    └── search-modal.latte    # Search overlay modal
```

### Content

Markdown files live in `content/{section}/{page}.md`. Sections are defined in `config.yml`:

```yaml
leaf:
  sections:
    getting-started: "Getting Started"
```

Each markdown file has YAML frontmatter with `title` and `order` fields.

### Config (`config.yml`)

The `leaf:` section drives site identity, navigation, sidebar, build paths, and footer:

```yaml
leaf:
  name: "My Project"
  version: "1.0.0"
  description: "A short description."
  github_url: "https://github.com/user/repo"
  content_path: "content"
  sections:
    getting-started: "Getting Started"
  output_path: "dist"
  base_url: ""
  author: "Your Name"
  author_url: "https://yoursite.com"
  license: "MIT"
```

`LeafConfig extends ConfigSection` normalizes keys to camelCase, but section slugs with hyphens (like `getting-started`) need raw access via `$this->values['sections']` to preserve hyphens.

### Design System (Dark Theme Only)

| Token | Value | Usage |
|---|---|---|
| `--bg-deep` | `#08080d` | Page background |
| `--bg-surface` | `#11111a` | Cards, sidebar, code blocks |
| `--bg-elevated` | `#1a1a27` | Hover states, active items |
| `--border` | `#1e1e2e` | Subtle borders |
| `--accent` | `#8b5cf6` | Primary violet |
| `--accent-light` | `#a78bfa` | Hover/lighter violet |
| `--text` | `#d4d4d8` | Body text |
| `--text-bright` | `#fafafa` | Headings |

Fonts: Inter (body) + JetBrains Mono (code) via Google Fonts. Tailwind CSS CDN. Prism.js for syntax highlighting.

## Dependencies

**Runtime:** `dadajuice/zephyrus2`, `league/commonmark ^2.8`, `league/config ^1.2`, `ext-intl`, `ext-mbstring`
**Dev:** `phpunit/phpunit ^11.0`, `symfony/var-dumper`

The `zephyrus2` dependency is currently a local path repo (`../zephyrus2`).

## Commands

```bash
composer dev     # Start PHP dev server with live-reload (localhost:8080)
composer build   # Generate static HTML to dist/
composer test    # Run PHPUnit (58 tests)
```

## Key Files

| File | Purpose |
|---|---|
| `bin/router.php` | Dev server entry — defines `DEV_SERVER` constant, delegates to `DevRouter`. **Must `return false` at top-level scope** for static file serving. |
| `bin/build.php` | Static build entry — boots Application, uses `StaticSiteBuilder`, generates search index, creates root redirect and GitHub Pages 404. |
| `public/index.php` | Web entry point (production). |
| `public/assets/css/app.css` | 937-line dark theme CSS. |
| `public/assets/js/copy-code.js` | Code block copy button. |
| `public/assets/js/sidebar.js` | Sidebar expand/collapse + active state. |
| `public/assets/js/search.js` | Client-side full-text search with modal. |
| `docker-compose.yml` | PHP 8.4 Pulsar image, maps port 8080, mounts sibling zephyrus2. |

## Important Discoveries

- **PHP built-in server needs `return false` at top-level scope** — `DevRouter::handle()` returns `false` for static files, but that return value from a method call is ignored by PHP. The router script MUST have `if ((new DevRouter(...))->handle() === false) { return false; }`.
- **`DEV_SERVER` constant gating** — Only defined in `bin/router.php`. Templates check `{if defined('DEV_SERVER')}` to inject live-reload. Static builds never define it.
- **Latte 3 parses `{` inside templates** — Code examples need `n:syntax="off"` on `<pre>` elements. Latte 3 does NOT parse `{` inside `<script>` tags.
- **Latte 3 `$params` is protected** — `LeafLatteExtension` uses `Closure::bind()` in `beforeRender()` to inject template variables.
- **ConfigSection normalizes keys to camelCase** — `github_url` → `githubUrl`. Section slugs with hyphens need raw `$this->values['sections']` access.

## Conventions

- **Commit format:** `feat: Message` or `fix: Message`
- **No co-author** in commits
- **Tests for every change**
- **`dist/` committed** to repo (no CI build needed, deploy direct to GitHub Pages)
- **No light theme** — single dark theme only
- **No landing page** — `/` redirects to first documentation page. The site is docs-only.
- **No `/docs` prefix** — URLs are `/{section}/{slug}` directly
