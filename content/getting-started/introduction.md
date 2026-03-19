---
title: Introduction
order: 1
---

# Introduction

Welcome to your new documentation site, powered by **Zephyrus Leaf**.

## What is Leaf?

Leaf is a static content site template built on the [Zephyrus](https://github.com/dadajuice/zephyrus2) PHP framework. It gives you:

- **Markdown-powered pages** with front matter, GFM tables, and syntax highlighting
- **Live-reload** during development — edit a file, see changes instantly
- **One-command static builds** — run `composer build` to generate a `dist/` folder ready for GitHub Pages
- **Full-text search** — client-side fuzzy search across all your pages
- **Dark theme** — a clean, professional design out of the box

## Quick start

Edit this file at `content/getting-started/introduction.md` to start writing your own docs.

### Adding pages

Create a new `.md` file in any section folder under `content/`:

```
content/
  getting-started/
    introduction.md    <- you are here
    installation.md    <- add more pages
  guides/
    deployment.md
```

Each file needs front matter with at least a `title` and `order`:

```yaml
---
title: Installation
order: 2
---
```

### Adding sections

Add new sections in `config.yml` under the `leaf.sections` key:

```yaml
leaf:
  sections:
    getting-started: "Getting Started"
    guides: "Guides"
```

The order in the YAML file determines the sidebar order.

## Next steps

- Edit `config.yml` to set your project name, version, and GitHub URL
- Add your logo to `public/assets/images/` and update `app/Views/partials/nav.latte`
- Run `composer build` to generate the static site
