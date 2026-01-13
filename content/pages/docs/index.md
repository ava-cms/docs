---
title: Introduction
slug: index
status: published
---

<div class="badges">

[![Release](https://img.shields.io/github/v/release/avacms/ava)](https://github.com/avacms/ava/releases)
[![Issues](https://img.shields.io/github/issues/avacms/ava)](https://github.com/avacms/ava/issues)
[![Code size](https://img.shields.io/github/languages/code-size/avacms/ava)](https://github.com/avacms/ava)
[![Discord](https://img.shields.io/discord/1028357262189801563)](https://discord.gg/fZwW4jBVh5)
[![GitHub Repo stars](https://img.shields.io/github/stars/avacms/ava)](https://github.com/avacms/ava)

</div>

Ava is a blazing fast and very flexible flat-file CMS. Your content lives as Markdown (`.md`) files on disk, not rows in a database. Create a file, write in Markdown or HTML, and you have a page. Edit it, refresh your browser, and it’s live.

The best part is how *normal* everything feels: your whole site is readable, portable, and yours. Put it in Git. Edit in VS Code, Obsidian, vim—whatever you like. Back it up by copying a folder. Move hosts by uploading files. There's no build step, no complex deployment process, and no proprietary format to learn.

When you want more dynamic or advanced features, Ava is still right there with you: PHP-powered themes, custom content types, taxonomies, search, and caching—without hiding the underlying structure. There’s an optional admin dashboard for quick edits and status monitoring, a friendly intuitive CLI for power users, and a plugin system with extensive hooks for adding extras. But the core idea always stays simple: files in, website out.

If you want your own place on the web—something you can understand, move, back up, and keep for the long haul—Ava is built for that. And if you’re still learning HTML/CSS (or PHP), that’s fine too: Ava stays approachable and grows with you.

## Your Site, On Disk

Ava projects are intentionally simple: your content is text files, your theme is a collection of HTML/PHP templates, and your configuration is plain PHP arrays. No magic, no hidden layers.

Here’s what a typical Ava site looks like:

<pre><code class="language-text">mysite/
├── app/
│   └── config/          # Configuration files
│       ├── ava.php      # Main config (site, paths, caching)
│       ├── content_types.php
│       ├── taxonomies.php
│       └── users.php    # Admin users (managed by CLI)
├── content/
│   ├── pages/           # Page content (hierarchical URLs)
│   ├── posts/           # Blog posts (/blog/{slug})
│   └── _taxonomies/     # Term registries
├── themes/
│   └── default/         # Theme templates
│       ├── templates/
│       └── assets/
├── plugins/             # Optional plugins
├── snippets/            # Safe PHP snippets for [snippet] shortcode
├── public/              # Web root
│   ├── media/           # Downloads referenced via @media: alias
│   └── index.php        # Entry point
├── storage/cache/       # Content index and page cache (gitignored)
└── ava                  # CLI tool
</code></pre>

The big idea: you can look at the folder tree and immediately understand where things live. That makes Ava great for long-term ownership, collaboration, and version control.

## How It Works

1. **[Write](/docs/content)** — Create Markdown files in your `content/` folder.
2. **[Index](/docs/performance)** — Ava automatically scans your files and builds a fast index.
3. **[Render](/docs/theming)** — Your theme turns that content into beautiful HTML.

The system handles the plumbing: routing, sorting, pagination, and search. You focus on content and design.

## Editing Content: Pick Your Style

Ava is flexible about *how* you work. There’s no “correct” workflow—use whatever fits your setup:

- **Edit directly on your server** — SFTP, SSH, or your host’s file manager. Changes appear instantly.
- **Work locally** — Edit on your computer and upload when ready. Great for bigger changes.
- **Use Git** — Version control with GitHub, GitLab, etc. Perfect for collaboration and history.
- **Mix and match** — Quick fixes on the server, bigger projects locally. Whatever works for you.

If you want some beginner-friendly background on the tools involved:

- Learn the basics of running commands in [CLI](/docs/cli)
- Learn what Markdown is (and what editors are great) in [Content](/docs/content)

## Is Ava for You?

Ava is a great fit if:

- You want a site you can fully own, understand, and move between hosts.
- You like writing in a real editor instead of a web form.
- You know some HTML and CSS (or want to learn), and you’re happy to customise a theme.
- You want a fast site without build pipelines, deploy queues, or complicated tooling.
- You’d rather keep things simple: files first, database optional.

It won’t be a good fit if you need a drag-and-drop page builder or a huge marketplace of third‑party themes and plugins.

## Philosophy

Ava is built for people who like understanding how their site works. It keeps the “just files” simplicity of a static workflow, while giving you dynamic tools when you need them.

<div class="philosophy-grid">
<div class="philosophy-grid-item">

**📂 Your Files, Your Rules** Content is Markdown with YAML frontmatter (plus optional HTML) and extensible PHP shortcodes. Configuration is plain, readable PHP. Your files are the source of truth.

</div>
<div class="philosophy-grid-item">

**✍️ Bring Your Own Editor** Write in your favourite text editor, IDE, or even the terminal. Use standard HTML and CSS for themes, and sprinkle in PHP when you want dynamic bits.

</div>
<div class="philosophy-grid-item">

**🚀 No Database Required** Run happily with no database at all. If your site grows huge, SQLite is available as a single lightweight file to handle big collections while keeping memory usage low.

</div>
<div class="philosophy-grid-item">

**⚡ Edit Live** Edit a file, refresh your browser, and see the change immediately. No build step, no deploy queue, and no waiting for regeneration.

</div>
<div class="philosophy-grid-item">

**🎨 Bespoke by Design** Model your site the way you think: blogs, portfolios, recipe collections, changelogs—whatever fits. Custom content types are a first-class feature, not a workaround.

</div>
<div class="philosophy-grid-item">

**🤖 LLM Friendly** The clean file structure, integrated docs, and straightforward CLI make it easy to pair with AI assistants when building themes and extensions.

</div>
</div>

## Core Features

| Feature | What it does for you |
|---------|-------------|
| **Content&nbsp;Types** | [Define](/docs/configuration#content-types) exactly what you're publishing (Pages, Posts, Projects, etc.). |
| **Taxonomies** | [Organise](/docs/configuration#taxonomies) content your way with custom categories, tags, or collections. |
| **Smart&nbsp;Routing** | URLs are generated [automatically](/docs/routing) based on your content structure. |
| **Themes** | Write standard HTML and CSS however you prefer, use PHP and Ava's [helpers](/docs/theming) only where you need dynamic data. |
| **Shortcodes** | Embed [dynamic content](/docs/shortcodes) and reusable snippets in your Markdown. |
| **Plugins** | Add [functionality](/docs/creating-plugins) like sitemaps and feeds without bloat. |
| **Speed** | Built-in page [caching](/docs/performance) makes your site load instantly, even on cheap hosting. |
| **Search** | Full-text search across your content with [configurable](/docs/configuration#search-configuration) weights. |
| **CLI Tool** | Manage your site from the [command line](/docs/cli): clear caches, create users, run tests, and more. |
| **Admin Dashboard** | Optional [web UI](/docs/admin) for editing content, managing taxonomies, viewing logs, and system diagnostics. |

## Performance

Ava is designed to be fast by default, whether you have 100 posts or 100,000.

- **Instant Publishing:** No build step. Edit a file, refresh your browser, see it live.
- **Smart Caching:** A [tiered caching system](/docs/performance) keeps page generation extremely fast. Even without page caching, posts compile quickly, and large content updates can be indexed almost immediately for responsive search and sorting.
- **Scalable Backends:** Start with the default Array backend for raw speed, or switch to [SQLite](/docs/performance#sqlite-backend) for constant memory usage at scale.
- **Static Speed:** Enable [full page caching](/docs/performance#page-caching) to serve static HTML files, bypassing the application entirely for most visitors.

[See full benchmarks and scaling guide →](/docs/performance)

## Command Line Interface

Ava includes a friendly CLI for managing your site. Run commands from your project root to check status, rebuild indexes, create content, and more.

```bash
./ava status
```

<pre><samp><span class="t-cyan">   ▄▄▄  ▄▄ ▄▄  ▄▄▄     ▄▄▄▄ ▄▄   ▄▄  ▄▄▄▄
  ██▀██ ██▄██ ██▀██   ██▀▀▀ ██▀▄▀██ ███▄▄
  ██▀██  ▀█▀  ██▀██   ▀████ ██   ██ ▄▄██▀</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Site</span> <span class="t-dim">──────────────────────────────────────────────</span>

  <span class="t-dim">Name:</span>       <span class="t-white">My Site</span>
  <span class="t-dim">URL:</span>        <span class="t-cyan">https://example.com</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Content</span> <span class="t-dim">───────────────────────────────────────────</span>

  <span class="t-cyan">◆ Page:</span> <span class="t-white">5 published</span>
  <span class="t-cyan">◆ Post:</span> <span class="t-white">38 published</span> <span class="t-yellow">(4 drafts)</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Page Cache</span> <span class="t-dim">────────────────────────────────────────</span>

  <span class="t-dim">Status:</span>     <span class="t-green">● Enabled</span>
  <span class="t-dim">Cached:</span>     <span class="t-white">42 pages</span></samp></pre>

[See full CLI reference →](/docs/cli)

## Admin Dashboard

Ava includes a web-based admin panel for monitoring and managing your site. It's completely optional—everything can be done via the CLI or direct file editing—but it's handy for quick edits and common tasks.

<a href="@media:admin-dashboard.webp" target="_blank" rel="noopener">
  <img src="@media:admin-dashboard.webp" alt="Ava Admin Dashboard" />
</a>

The dashboard lets you:

- **Edit content** — Create and edit Markdown files with frontmatter generation
- **Browse content** — See what exists, its status, and where it lives on disk
- **Manage taxonomies** — Create and delete terms via a file-backed registry
- **Upload media** — Add images to your media folder
- **Run diagnostics** — Lint content, view logs, check system health
- **Maintenance** — Rebuild indexes and clear cached pages

[See admin documentation →](/docs/admin)

## Requirements

<img src="https://addy.zip/ava/i-love-php.webp" alt="I love PHP" style="float: right; width: 180px; margin: 0 0 1rem 1.5rem;" />

Ava requires **PHP 8.3** or later and **SSH access** for CLI commands. Most good hosts include this.

**Required Extensions:**

- `mbstring` — UTF-8 text handling
- `json` — Config and API responses
- `ctype` — String validation

These are bundled with most PHP installations.

**Optional Extensions:**

- `pdo_sqlite` — SQLite backend for large sites (10k+ items, constant memory)
- `igbinary` — Faster content indexing and smaller cache files
- `opcache` — Opcode caching for production

If `igbinary` isn't available, Ava falls back to PHP's built-in `serialize`. Both work fine, `igbinary` is just [faster](/docs/performance).

## Quick Start

Getting started with Ava is simple and the default set-up can be put live in just a minute. Here are a few options:

### Download and Upload

The simplest approach—no special tools required:

[![Release](https://img.shields.io/github/v/release/avacms/ava)](https://github.com/avacms/ava/releases)

1. Download the latest release from [GitHub Releases](https://github.com/avacms/ava/releases)
2. Extract the ZIP file
3. Upload to your web host (via SFTP, your host's file manager, or however you prefer)
4. Run `composer install` to install dependencies
5. [Configure](/docs/configuration) your site by editing `app/config/ava.php`
6. Visit your site!

### Clone with Git

If you're comfortable with Git and want version control from the start:

1. Clone the repo in your website's root directory (above the `public` folder):
```bash
git clone https://github.com/avacms/ava.git
```
2. Install dependencies:
```bash
composer install
```
3. [Configure](/docs/configuration) your site by editing `app/config/ava.php`
4. Visit your site!

### Local Development (Optional)

If you want to preview your site on your own computer before going live:

```bash
php -S localhost:8000 -t public
```

Then visit [http://localhost:8000](http://localhost:8000) in your browser.

<details class="beginner-box">
<summary>Ready for Production?</summary>
<div class="beginner-box-content">

### Ready for Production?

See the [Hosting Guide](/docs/hosting) for shared hosting, VPS options, and deployment tips.

</div>
</details>

### Default Site

By default, Ava comes with a simple example site. You can replace the content in the `content/` folder and your theme in the `themes/default/` folder to start building your site.

<img src="@media:default.webp" alt="Default theme preview" />

The default theme provides a clean, minimal starting point for your site. Customise it with your own styles, scripts and templates to match your vibe or [build something entirely new](/docs/theming).

## Next Steps

- [Configuration](/docs/configuration) — Site settings, content types, and taxonomies
- [Content](/docs/content) — Writing pages and posts
- [Hosting](/docs/hosting) — Getting your site live
- [Theming](/docs/theming) — Creating templates
- [Admin](/docs/admin) — Optional dashboard
- [CLI](/docs/cli) — Command-line tools
- [Showcase](/showcase) — Community sites, themes, and plugins

## License

Ava CMS is free and open-source software licensed under the [MIT License](https://github.com/avacms/ava/blob/main/LICENSE).

In plain English, that means you can:

- Use Ava for personal or commercial projects.
- Modify it to fit your site (and keep your changes private if you want).
- Share it, fork it, and redistribute it.

The main thing the license asks is that you keep the MIT license text and copyright notice with the software.

Also worth knowing: the MIT license comes with a standard "no warranty" clause. Ava is provided as-is, so you're responsible for how you deploy and run it.

## Contributing

Ava is still fairly early and moving quickly, so I'm not looking for undiscussed pull requests or additional contributors just yet.

That said, I'd genuinely love your feedback:

- If you run into a bug, get stuck, or have a "this could be nicer" moment, please [open an issue](https://github.com/avacms/ava/issues).
- Feature requests, ideas, and suggestions are very welcome.

If you prefer a more conversational place to ask questions and share ideas, join the [Discord community](https://discord.gg/fZwW4jBVh5).

## Community

See what others are building with Ava:

- [Community Plugins](/plugins) — Extend Ava with plugins shared by the community
- [Community Themes](/themes) — Ready-to-use themes for your site
- [Sites Built with Ava](/showcase) — Get inspired by what others have created
