---
title: Writing Content
slug: content
status: published
meta_title: Writing Content | Flat-file PHP CMS | Ava CMS
meta_description: Write content in Markdown with YAML frontmatter. Learn about content types, taxonomies, drafts, and how to structure your flat-file CMS content.
excerpt: Content in Ava CMS is just text—write in Markdown, save as a file, and you have a page. No database to manage, your files are your content.
---

Content in Ava CMS is just text. You write in [Markdown](https://ava.addy.zone/docs/markdown-reference), which is a simple way to format text, and save it as a file. There's no database to manage—your files are your content. 

Ava CMS can handle any combination of Markdown and standard HTML, even within the same file. You can also embed safe and reusable PHP snippets using [Shortcodes](/docs/shortcodes) for absolute flexibility.

<details class="beginner-box">
<summary>What is Markdown?</summary>
<div class="beginner-box-content">

## What is Markdown?

Markdown is a lightweight way to format text using plain characters.

- You write readable text.
- You sprinkle in simple symbols for headings, links, lists, and code.
- Mix in your own custom HTML if required for advanced styling.
- Ava CMS (and your theme) turns it into HTML.

### A tiny Markdown cheat-sheet

```markdown
# Heading 1
## Heading 2

**bold** and *italic*

- bullet item
1. numbered item

[a link](https://example.com)

`inline code`

```php
// a code block
echo 'Hello';
```‎ 
```

[View full Markdown reference →](https://ava.addy.zone/docs/markdown-reference)

### Markdown Editors (use what you like)

You can write Ava CMS content in almost anything:

- **Code editors:** VS Code, Sublime Text, PhpStorm
- **Markdown-focused apps:** Obsidian, Typora, MarkText, iA Writer, Zettlr
- **In the browser:** StackEdit, your web host's file manager, or GitHub's editor if you're using Git

There's no "correct" editor. If you like writing in a notes app and uploading later, that works. If you like editing on the server over SSH, that works too.

### Frontmatter vs Markdown (two different things)

Each content file has:

- **Frontmatter (YAML)** between `---` lines: structured metadata
- **Body (Markdown)**: the actual writing

**Note:** YAML is sensitive to indentation. If something breaks, it's often a missing space or an unclosed quote in frontmatter. Running [`./ava lint`](/docs/cli#lint) is the fastest way to get a clear error message.

</div>
</details>

## Content Types

Content types define what kinds of content your site has. They're configured in `app/config/content_types.php`:

```php
return [
    'page' => [
        'label'       => 'Pages',
        'content_dir' => 'pages',
        'url'         => ['type' => 'hierarchical', 'base' => '/'],
    ],
    'post' => [
        'label'       => 'Posts',
        'content_dir' => 'posts',
        'url'         => ['type' => 'pattern', 'pattern' => '/blog/{slug}'],
        'taxonomies'  => ['category', 'tag'],
    ],
];
```

For complete configuration options (fields, templates, sorting, archives), see [Configuration: Content Types](/docs/configuration#content-content-types-content_typesphp).

## The Basics

Every piece of content is a `.md` file with two parts:

1. **💌 Frontmatter** — Metadata about the content (like title, date, status) at the top. Think of it like the address on an envelope.
2. **📝 Body** — The actual content, written in Markdown.

```markdown
---
title: My First Post
slug: my-first-post
status: published
date: 2024-12-28
---

# Hello World

This is my first post. I can use **bold**, *italics*, and [links](https://example.com).
```

Set `status: draft` while writing, then switch to `published` when you’re happy.

## Creating Content

### Manually

Create a `.md` file in the appropriate content directory:

```
content/
└── pages/
    └── my-new-page.md   ← create your file here
```

Add frontmatter and content, then save. If cache mode is `auto`, the site updates immediately.

### Via the Admin Dashboard

If you have the admin dashboard enabled, you can create, edit, and delete content files directly in the browser. Ava CMS writes changes back to your Markdown files (files remain the source of truth).

<div class="screenshot-window">
<a href="@media:admin-dashboard.webp" target="_blank" rel="noopener">
    <img src="@media:admin-dashboard.webp" alt="Ava CMS admin dashboard" />
</a>
</div>

See [Admin Dashboard](/docs/admin) for setup and usage.

### Via CLI

Use the [`make`](/docs/cli#make) command:

```bash
./ava make <type> "Title"
```

Examples:

```bash
./ava make page "About Us"
./ava make post "Hello World"
```

<pre><samp>  <span class="t-green">╭───────────────────────────╮
  │  Created new post!        │
  ╰───────────────────────────╯</span>

  <span class="t-dim">File:</span>       <span class="t-white">content/posts/hello-world.md</span>
  <span class="t-dim">ID:</span>         <span class="t-white">01JGHK8M3Q4R5S6T7U8V9WXYZ</span>
  <span class="t-dim">Slug:</span>       <span class="t-cyan">hello-world</span>
  <span class="t-dim">Status:</span>     <span class="t-yellow">draft</span>

  <span class="t-yellow">💡 Tip:</span> Edit your content, then set status: published when ready</samp></pre>

Run without arguments to see available types:

```bash
./ava make
```

<pre><samp>  <span class="t-red">✗</span> Usage: ./ava make &lt;type&gt; "Title"

  <span class="t-bold">Available types:</span>

    <span class="t-cyan">▸ page</span> <span class="t-dim">— Pages</span>
    <span class="t-cyan">▸ post</span> <span class="t-dim">— Posts</span>

  <span class="t-bold">Example:</span>
    <span class="t-dim">./ava make post "My New Post"</span></samp></pre>

This creates a properly formatted file with:
- Generated ULID
- Slugified filename
- Date (for dated types)
- Draft status

<div class="callout-info">
<strong>Beginners need not worry:</strong> the CLI isn't "advanced mode"—it's just a helper that saves you from remembering boilerplate and file naming. It's a great way to dip your toes into command-line tools without getting overwhelmed.
</div>

## Organising Your Files

Content lives in the `content/` folder. You can organise it however you like, but typically it looks like this:

```
content/
├── pages/           # Standard pages like About or Contact
│   ├── index.md     # Your homepage (or _index.md, if you prefer)
│   ├── about.md     # /about
│   └── services/
│       ├── index.md # /services
│       └── web.md   # /services/web
├── posts/           # Example of a pattern-based type (only if you define it)
│   └── hello.md
└── _taxonomies/     # Optional term registries (only if you use taxonomies)
  ├── category.yml
  └── tag.yml
```

For hierarchical types (like the default `pages`), folder structure maps cleanly to URLs. For example `content/pages/services/web.md` becomes `/services/web`.

## Frontmatter Reference

Frontmatter is metadata about your content, written in [YAML](https://yaml.org/) between two `---` lines at the top of your file. Think of it as the "settings" for each page.

### Full Example

Here's a complete example showing all the common fields (but you can keep it much simpler if you prefer):

<pre><code class="language-yaml">---
id: 01JGMK0000POST0000000000001
title: My Blog Post
slug: my-blog-post
status: published
date: 2024-12-28
updated: 2024-12-30
excerpt: A short summary for listings and search results.
template: custom-post.php
order: 10
category:
  - tutorials
  - php
tag:
  - beginner
meta_title: SEO-Optimised Title
meta_description: Description for search engines.
canonical: https://example.com/blog/my-blog-post
og_image: "@media<span></span>:2024/social-card.jpg"
noindex: false
cache: true
redirect_from:
  - /old-url
  - /another-old-url
assets:
  css:
    - "@media<span></span>:css/custom-post.css"
  js:
    - "@media<span></span>:js/interactive.js"
---

Your Markdown content goes here...
</code></pre>

### Core Fields

These fields are used by Ava CMS to manage and display your content.

| Field | Required | Description |
|-------|----------|-------------|
| `title` | No* | Display title. If omitted, Ava CMS derives one from `slug` (e.g. `my-post` → `My Post`). |
| `slug` | No* | A URL-safe identifier. If omitted, Ava CMS sets it from the **filename** (not the title). Must match: lowercase letters/numbers + hyphens only (`^[a-z0-9-]+$`). |
| `status` | No | `draft`, `published`, or `unlisted`. Defaults to `draft`. |
| `id` | No | Optional unique identifier. Useful for stable references and ID-based URLs (see below). |
| `date` | No | Optional date. Ava CMS accepts common date strings (and some other types); invalid values become `null`. |
| `updated` | No | Optional updated timestamp. If omitted (or invalid), Ava CMS falls back to `date`. |
| `excerpt` | No | Optional short summary for listings/search/etc. |
| `template` | No | Optional template override (e.g., `landing.php`). |

\*Ava CMS’s linter validates that the computed `title` and `slug` are non-empty, but Ava CMS also supplies defaults:

- `slug` defaults to the filename (e.g. `hello-world.md` → `hello-world`)
- `title` defaults to a title-cased version of `slug`

In other words: you *can* omit `title`/`slug` in frontmatter, but your **filename still needs to be a valid slug** or `./ava lint` will fail.

#### Slugs and URLs (pattern vs hierarchical)

Ava CMS supports two URL styles per content type (configured in `app/config/content_types.php`):

- **Pattern URLs** (`url.type = pattern`): the item’s `slug` is used in the URL pattern (e.g. `/blog/{slug}`), and as the lookup key.
- **Hierarchical URLs** (`url.type = hierarchical`): the URL is derived from the **file path**, not the item’s `slug`.

For hierarchical types:

- `content/pages/about/team.md` becomes `/about/team`.
- `index.md` or `_index.md` represent the folder URL (e.g. `content/pages/docs/index.md` → `/docs`).
- The internal lookup key is the path (e.g. `about/team`). If you fetch items manually in templates, use that key:

```php
$item = $ava->get('page', 'about/team');
```

Setting `slug:` in frontmatter does **not** change the URL for hierarchical content (the filesystem path wins).

#### IDs: optional, but powerful

The `id` field is optional.

Benefits of setting/keeping IDs:

- Ava CMS can fetch an item by ID via the repository index.
- You can create stable, rename-proof permalinks by using `{id}` in a pattern URL (e.g. `'/p/{id}'`).
- Ava CMS detects duplicate IDs during indexing (helpful when merging content).

If you don’t need any of that, you can omit `id:` entirely.

### Taxonomy Fields

Assign content to categories, tags, or any taxonomy defined in [taxonomies.php](/docs/configuration#content-taxonomies-taxonomiesphp).

```yaml
category:
  - tutorials
  - php
tag:
  - getting-started
  - beginner
```

You can use either a single value or a list. Ava normalizes both into an array.

#### Alternative format: `tax:` map

Group all taxonomies under a single key:

```yaml
tax:
  category: [tutorials, php]
  tag: beginner
```

#### Accessing Terms in Templates

```php
<?php foreach ($content->terms('category') as $term): ?>
    <a href="<?= $ava->termUrl('category', $term) ?>">
        <?= $ava->termName('category', $term) ?>
    </a>
<?php endforeach; ?>
```

**See:** [Taxonomies](/docs/taxonomies) for full documentation on term storage, registry files, hierarchical terms, and template helpers.

### SEO Fields

Control how your content appears in search engines and social media.

| Field | Description |
|-------|-------------|
| `meta_title` | Custom title for search engines. Defaults to `title`. |
| `meta_description` | Description shown in search results. |
| `canonical` | Explicit canonical URL. Use when content exists at multiple URLs or to point to the original source. |
| `noindex` | Set to `true` to hide from search engines (adds `<meta name="robots" content="noindex">`). |
| `og_image` | Image URL for social media sharing (Open Graph). Supports path aliases like `@media:`. |

Your theme must output SEO meta tags in the page `<head>` (for example by including `<?= $ava->metaTags($content) ?>`).

### Hierarchy & Ordering Fields

Control content structure and manual sorting.

| Field | Description |
|-------|-------------|
| `parent` | Parent page slug (for building navigation trees or breadcrumbs). Not used for URL generation in hierarchical types — URLs come from the filesystem. |
| `order` | Integer for manual sorting (e.g., `order: 10`). Lower values appear first. Default is `0`. Use with `sorting: 'manual'` in content type config. |

**Example using order for manual sorting:**

```yaml
---
title: Getting Started
order: 1
---
```

```yaml
---
title: Advanced Usage
order: 2
---
```

In templates, sort by order:

```php
$items = $ava->query()
    ->type('page')
    ->published()
    ->orderBy('order', 'asc')
    ->get();
```

### Behaviour Fields

Fine-tune how Ava CMS handles this specific piece of content.

| Field | Description |
|-------|-------------|
| `cache` | Set to `false` to disable page caching for this URL. Set to `true` to force caching. |
| `redirect_from` | Array of old URLs that should 301 redirect here. See [Redirects](#redirects). |
| `template` | Override the default template for this content type (e.g., `landing.php`). See [Theming](/docs/theming). |
| `raw_html` | Set to `true` to skip Markdown parsing. Shortcodes and path aliases are still processed. Useful for pages with custom HTML layouts. |

### Per-Item Assets

Load CSS or JS only on specific pages (for "art-directed" posts). See [Per-Item Assets](#per-item-assets-art-directed-posts) for details.

### Custom Fields

You can add **any custom fields** you like—they're just YAML keys in your frontmatter:

```yaml
---
title: Team Member
slug: jane-doe
status: published
role: Lead Developer
website: "https://janedoe.com"
featured: true
---
```

Access them in templates with `$item->get('field_name')`:

```php
<p>Role: <?= $ava->e($item->get('role')) ?></p>
<?php if ($item->get('featured')): ?>
    <span class="badge">Featured</span>
<?php endif; ?>
```

**Want validation and admin UI for your custom fields?** Define them in your content type configuration to get proper form inputs, type validation, and linting. See [Fields](/docs/fields) for the complete guide to typed fields.

## Redirects

When you move or rename content, set up redirects in the new file:

```yaml
redirect_from:
  - /old-url
  - /another-old-url
```

Requests to the old URLs will 301 redirect to the new location.

## Per-Item Assets (Art-Directed Posts)

For art-directed blogging, you can load custom CSS or JS on specific pages. Put your files in `public/media/` and reference them:

<pre><code class="language-yaml">assets:
  css:
    - "@media<span></span>:css/my-styled-post.css"
  js:
    - "@media<span></span>:js/interactive-chart.js"
</code></pre>

Your theme must include `<?= $ava->itemAssets($item) ?>` in the `<head>` for these to load (the default theme already does this).

**See also:** [Theming - Per-Item Assets](/docs/theming) for how to implement this in your theme.

## Images and Media

Store images, PDFs, and other files in `public/media/`. Reference them using the <code>@media<span></span>:</code> path alias:

<pre><code class="language-markdown">![Team photo](@media<span></span>:team/group.jpg)

[Download PDF](@media<span></span>:docs/guide.pdf)
</code></pre>

The <code>@media<span></span>:</code> alias expands to `/media/` at render time. You can add custom aliases (like <code>@cdn<span></span>:</code>) in [`ava.php`](/docs/configuration#path-aliases)—this makes it easy to change asset locations later without updating every content file.

**Uploading files:**
- **Manually** — Drop files into `public/media/` via SFTP or your file manager
- **Admin Dashboard** — Use the built-in media uploader (see [Admin Dashboard](/docs/admin))

<div class="callout-info">
<strong>Tip:</strong> Theme assets (CSS/JS for your site design) go in your theme folder, not <code>public/media/</code>. The media folder is for content-related files like images, downloads, and per-post assets.
</div>

## Shortcodes

Embed dynamic content using shortcodes:

<pre><code class="language-markdown">Current year: &#91;year&#93;

Site name: &#91;site_name&#93;

Include snippet: &#91;snippet name=&quot;cta&quot; heading=&quot;Join Us&quot;&#93;
</code></pre>

See [Shortcodes](/docs/shortcodes) for the full reference.

## Content Status

| Status | Visibility |
|--------|------------|
| `draft` | Not routed publicly. Viewable via preview token (if configured). |
| `published` | Publicly routed. Included in listings/archives and taxonomy indexes. |
| `unlisted` | Publicly routed (accessible via direct URL, no preview token required). Excluded from published-only listings (e.g. `$ava->recent()`), archives, and taxonomy indexes. |

## Previewing Your Site

### Local Development

Run PHP's built-in server to preview locally:

```bash
composer install                    # First time only
php ava rebuild                     # Build content index
php -S localhost:8000 -t public     # Start dev server
```

Then open `http://localhost:8000` in your browser.

<div class="callout-warning">
This is a development server, not for public use. See the <a href="/docs/hosting">Hosting Guide</a> for production options.
</div>

<div class="callout-info">
<strong>New to local development?</strong> See the <a href="/docs/hosting#local-development">Hosting Guide</a> for detailed setup instructions, including Windows-specific guidance for installing PHP and Composer.
</div>

### Live Editing

Editing files directly on your server works great too! Ava CMS's auto-rebuild mode (the default) means changes appear immediately—just save and refresh.

<details class="beginner-box">
<summary>Workflow Options</summary>
<div class="beginner-box-content">

There's no single "right" way to work with Ava CMS:

- **Edit on server** — Use SFTP, your host's file manager, or SSH. Changes are live immediately.
- **Work locally** — Edit with your favourite tools, preview with PHP's dev server, upload when ready.
- **Use Git** — Track changes with version control, sync via GitHub/GitLab, automate deployments.

Many people combine approaches: quick fixes directly on the server, bigger changes locally with Git. Do what works for you!

</div>
</details>

## Validation

Run the linter to check all content:

```bash
./ava lint
```

This catches:
- Invalid YAML syntax
- Missing required fields
- Invalid status values
- Malformed slugs
- Duplicate content keys (slug for pattern types; path key for hierarchical types)
- Duplicate IDs (when IDs are present)

See [CLI Reference - Lint](/docs/cli#lint) for more details.

<div class="related-docs">
<h2>Related Documentation</h2>
<ul>
<li><a href="/docs/configuration#content-content-types-content_typesphp">Configuration: Content Types</a> — Full content type options</li>
<li><a href="/docs/fields">Fields</a> — All field types and validation</li>
<li><a href="/docs/taxonomies">Taxonomies</a> — Organizing content with categories and tags</li>
<li><a href="/docs/shortcodes">Shortcodes</a> — Embedding dynamic content</li>
<li><a href="/docs/markdown-reference">Markdown Reference</a> — Complete Markdown syntax</li>
</ul>
</div>
