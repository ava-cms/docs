---
title: Introduction
slug: index
status: published
meta_title: Introduction | Flat-file PHP CMS | Ava CMS
meta_description: Ava CMS is a blazing fast flat-file CMS. Your content lives as Markdown files, not database rows. No build step, no complex deployment—just files in, website out.
excerpt: Ava CMS is a blazing fast and flexible flat-file CMS where your content lives as Markdown files on disk. Edit in any editor, back up by copying a folder, and see changes instantly.
---

<div class="badges">

[![Release](https://img.shields.io/github/v/release/avacms/ava)](https://github.com/avacms/ava/releases)
[![Issues](https://img.shields.io/github/issues/avacms/ava)](https://github.com/avacms/ava/issues)
[![Code size](https://img.shields.io/github/languages/code-size/avacms/ava)](https://github.com/avacms/ava)
[![Discord](https://img.shields.io/discord/1028357262189801563)](https://discord.gg/fZwW4jBVh5)
[![GitHub Repo stars](https://img.shields.io/github/stars/avacms/ava)](https://github.com/avacms/ava)

</div>

Ava (Addy’s very adaptable) CMS (Content Management System) is a fast, flexible, file-based PHP CMS for bespoke websites. Create a file, write in Markdown (with optional HTML), and you have a page. Edit it, refresh, and it’s live — no build step.

Content is plain text files you can read, move, and version. Use the optional admin dashboard when it’s convenient, or stick to your favourite editor and tools — Ava CMS fits your workflow.

When you need more power, Ava CMS adds it without hiding the structure: PHP themes, custom content types and fields, taxonomies, search, caching, plugins, and a friendly CLI. The idea stays simple: files in, website out.

## Philosophy

Ava CMS is for people who like owning and understanding how their site works. It keeps the “just files” simplicity, while giving you dynamic tools when you need them.

<div class="philosophy-grid">
<div class="philosophy-grid-item">

**📝 Markdown & HTML** Write in Markdown, drop to HTML when you need to, and keep metadata clean with YAML frontmatter. Your content stays portable, readable, and truly yours.

</div>
<div class="philosophy-grid-item">

**⚡ Instant Feedback** Edit a file and refresh — no build step, no deploy queue. Auto-indexing mode keeps routing, lists, and search up to date as you work.

</div>
<div class="philosophy-grid-item">

**🎨 Design Freedom** No templating language to learn. Themes are plain PHP templates with your own HTML, CSS, and JavaScript — Ava stays out of the way.

</div>
<div class="philosophy-grid-item">

**🧩 Model Anything** Define your own content types, fields, and taxonomies for projects, posts, events, recipes — whatever fits. Structure and validation are first-class, not a hack.

</div>
<div class="philosophy-grid-item">

**🚀 Blazing Fast** Dual cache layers and optimised code paths keep pages quick by default — from tiny personal sites to busy production builds.

</div>
<div class="philosophy-grid-item">

**🗄️ No Database Required** Run happily with just files. If your site grows massive, switch to SQLite with a single config line — without changing how you write content.

</div>
<div class="philosophy-grid-item">

**🧰 Built for Builders** Extend with plugins, hooks, and shortcodes — plus a robust CLI for linting, indexing, and cache management. Use the tools you already know.

</div>
<div class="philosophy-grid-item">

**🤖 LLM Friendly** The clean file structure, integrated docs, and straightforward CLI make it easy to pair with AI assistants when building themes and extensions.

</div>
</div>

## Core Features

| Feature | What it does for you |
|---------|-------------|
| **Content&nbsp;Types** | [Define](/docs/configuration) exactly what you're publishing (Pages, Posts, Projects, etc.). |
| **Taxonomies** | [Organise](/docs/configuration) content your way with custom categories, tags, or collections. |
| **Smart&nbsp;Routing** | URLs are generated [automatically](/docs/routing) based on your content structure. |
| **Themes** | Write standard HTML and CSS, use PHP and Ava CMS [helpers](/docs/theming) only where you need to. |
| **Shortcodes** | Embed [dynamic content](/docs/shortcodes) and reusable snippets in your Markdown. |
| **Plugins** | Add [functionality](/docs/creating-plugins) like sitemaps and feeds without bloat. |
| **Speed** | Built-in page [caching](/docs/performance) makes your site load instantly, even on cheap hosting. |
| **Search** | Full-text search across your content with [configurable](/docs/configuration) weights. |
| **CLI Tool** | Manage your site from the [command line](/docs/cli): clear caches, create users, run tests, and more. |
| **Admin&nbsp;Dashboard** | Optional [web UI](/docs/admin) for editing content, managing taxonomies, viewing logs, and system diagnostics. |

## Files in, website out

Ava CMS projects are intentionally simple: your content is text files, your theme is a collection of HTML/PHP templates, and your configuration is plain PHP arrays. No magic, no hidden layers.

Here’s what a typical Ava CMS site looks like:

<pre><code class="language-text">mysite/
├── app/                 # Your code
│   ├── config/          # Configuration files
│   │   ├── ava.php      # Main config (site, paths, caching)
│   │   ├── content_types.php
│   │   ├── taxonomies.php
│   │   └── users.php    # Admin users (managed by CLI)
│   ├── plugins/         # Enabled plugins
│   ├── snippets/        # Reusable PHP content blocks
│   └── themes/          # Your HTML/PHP templates
│       └── default/
│           ├── templates/
│           └── assets/
├── content/
│   ├── pages/           # Page content (hierarchical URLs)
│   ├── posts/           # Blog posts (/blog/{slug})
│   └── _taxonomies/     # Term registries
├── public/              # Web root
│   ├── media/           # Downloads referenced via @media: alias
│   └── index.php        # Entry point
├── storage/cache/       # Content index and page cache (gitignored)
└── ava                  # CLI tool
</code></pre>

## How It Works

1. **[Write](/docs/content)** — Create Markdown files in your `content/` folder using your own editor or the admin dashboard.
2. **[Index](/docs/performance)** — Ava CMS scans your files and builds a fast index.
3. **[Render](/docs/theming)** — Your theme turns that content into beautiful HTML.

Routing, sorting, pagination, and search are handled for you so you can focus on content and design.

## Admin Dashboard

Ava CMS includes a fully featured admin dashboard designed for real content work. It’s optional — you can still edit files directly — but for teams or active sites it’s a major productivity boost.

<div class="screenshot-window">
<a href="@media:admin-dashboard.webp" target="_blank" rel="noopener">
  <img src="@media:admin-dashboard.webp" alt="Ava CMS Admin Dashboard" />
</a>
</div>

The admin is especially strong with [custom fields](/docs/fields): complex schemas become clean, validated forms instead of manual YAML. You can create, edit, and organise content quickly without losing the transparency of file-based storage.

Key capabilities:

- **Edit content** — Create and edit Markdown with frontmatter generation
- **Browse content** — See what exists, its status, and where it lives on disk
- **Manage taxonomies** — Create and delete terms via a file-backed registry
- **Upload media** — Add images to your media folder
- **Run diagnostics** — Lint content, view logs, check system health
- **Maintenance** — Rebuild indexes and clear cached pages

[See admin documentation →](/docs/admin)

## Is Ava CMS for You?

Ava CMS is a great fit if:

- You want a site you can fully own, understand, and move between hosts.
- You like writing in a real editor instead of a web form.
- You know some HTML and CSS (or want to learn), and you’re happy to customise a theme.
- You want a fast site without build pipelines, deploy queues, or complicated tooling.
- You’d rather keep things simple: files first, database optional.

It won’t be a good fit if you need a drag-and-drop page builder or a huge marketplace of third‑party themes and plugins.

## Performance

Ava CMS is designed to be fast by default, whether you have 100 posts or 100,000.

- **Instant Publishing:** No build step. Edit a file, refresh your browser, see it live.
- **Smart Caching:** A [tiered caching system](/docs/performance) keeps page generation extremely fast.
- **Scalable Backends:** Start with the default Array backend for raw speed, or switch to [SQLite](/docs/performance) for constant memory usage at scale.
- **Static Speed:** Enable [full page caching](/docs/performance) to serve static HTML files, bypassing the application entirely for most visitors.

[See full benchmarks and scaling guide →](/docs/performance)

## Command Line Interface

Ava CMS includes a friendly CLI for managing your site. Run commands from your project root to check status, rebuild indexes, create content, and more.

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

## Requirements

<img src="https://addy.zip/ava/i-love-php.webp" alt="I love PHP" style="float: right; width: 180px; margin: 0 0 1rem 1.5rem;" />

Ava CMS requires **PHP 8.3** or later and **SSH access** for CLI commands. Most good hosts include this.

**Required Extensions:**

- `mbstring` — UTF-8 text handling
- `json` — Config and API responses
- `ctype` — String validation

These are bundled with most PHP installations.

**Optional Extensions:**

- `pdo_sqlite` — SQLite backend for large sites (10k+ items, constant memory)
- `igbinary` — Faster content indexing and smaller cache files
- `opcache` — Opcode caching for production

If `igbinary` isn't available, Ava CMS falls back to PHP's built-in `serialize`. Both work fine, `igbinary` is just [faster](/docs/performance).

## Quick Start

Getting started with Ava CMS is simple and the default set-up can be put live in just a minute. Here are a few options:

### Download and Upload

The simplest approach—no special tools required:

[![Release](https://img.shields.io/github/v/release/avacms/ava)](https://github.com/avacms/ava/releases)

1. Download the latest release from [GitHub Releases](https://github.com/avacms/ava/releases)
2. Extract the ZIP file
3. Upload to your web host (via SFTP, your host's file manager, or however you prefer)
4. Run `composer install` to install dependencies
5. [Configure](/docs/configuration) your site by editing `app/config/ava.php`
6. Visit your site!

### Download Latest Release (Command Line)

If you have SSH access and prefer the terminal, downloading the latest release is the cleanest approach (no Git required).

**Option A: GitHub CLI (`gh`)**

```bash
mkdir -p /tmp/ava-release && cd /tmp/ava-release
gh release download -R avacms/ava --pattern '*.zip' --clobber
unzip -q ./*.zip -d /path/to/your/site
cd /path/to/your/site
composer install
```

**Option B: `curl` (uses PHP to pick the right ZIP asset)**

```bash
REPO='avacms/ava'
URL="$(
  curl -fsSL "https://api.github.com/repos/$REPO/releases/latest" |
  php -r '$r=json_decode(stream_get_contents(STDIN), true);
    foreach (($r["assets"] ?? []) as $a) {
      if (str_ends_with($a["name"], ".zip")) { echo $a["browser_download_url"]; exit; }
    }
    fwrite(STDERR, "No .zip asset found on latest release\n");
    exit(1);'
)"

mkdir -p /tmp/ava-release && cd /tmp/ava-release
curl -fL "$URL" -o ava.zip
unzip -q ava.zip -d /path/to/your/site
cd /path/to/your/site
composer install
```

Then [configure](/docs/configuration) your site by editing `app/config/ava.php` and visit your site.

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

By default, Ava CMS comes with a simple example site. You can replace the content in the `content/` folder and your theme in the `app/themes/default/` folder to start building your site.

<div class="screenshot-window">
<a href="@media:admin-dashboard.webp" target="_blank" rel="noopener">
  <img src="@media:default.webp" alt="Default theme preview" />
</a>
</div>

The default theme provides a clean, minimal starting point for your site. Customise it with your own styles, scripts and templates to match your vibe or [build something entirely new](/docs/theming).

## Next Steps

- [Configuration](/docs/configuration) — Site settings, content types, and taxonomies
- [Content](/docs/content) — Writing pages and posts
- [Hosting](/docs/hosting) — Getting your site live
- [Theming](/docs/theming) — Creating templates
- [Admin](/docs/admin) — Optional dashboard
- [CLI](/docs/cli) — Command-line tools
- [Showcase](/showcase) — Community sites, themes, and plugins

## Community

See what others are building with Ava CMS:

<div class="callout-warning"><strong>Security warning:</strong> Community themes/plugins are provided by third parties and are <strong>not vetted or audited</strong> by the Ava CMS project. Installing them may run third-party PHP/JS with the same permissions as your site. Only install code you trust, review it before deploying, and test in a staging environment.</div>

- [Community Plugins](/plugins) — Extend Ava CMS with plugins shared by the community
- [Community Themes](/themes) — Ready-to-use themes for your site
- [Sites Built with Ava CMS](/showcase) — Get inspired by what others have created

## Contributing

Ava CMS is still fairly early and moving quickly, so I'm not looking for undiscussed pull requests or additional contributors just yet.

That said, I'd genuinely love your feedback:

- If you run into a bug, get stuck, or have a "this could be nicer" moment, please [open an issue](https://github.com/avacms/ava/issues).
- Feature requests, ideas, and suggestions are very welcome.

If you prefer a more conversational place to ask questions and share ideas, join the [Discord community](https://discord.gg/fZwW4jBVh5).

## License

Ava CMS is free software: you can redistribute it and/or modify it under the terms of the [GNU General Public License](https://github.com/avacms/ava/blob/main/LICENSE) as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should receive a copy of the GNU General Public License along with the program. If not, see https://www.gnu.org/licenses/.