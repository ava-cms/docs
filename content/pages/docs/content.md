---
title: Writing Content
slug: content
status: published
meta_title: Writing Content | Flat-file PHP CMS | Ava CMS
meta_description: Write content in Markdown with YAML frontmatter. Learn about content types, taxonomies, drafts, and how to structure your flat-file CMS content.
excerpt: Content in Ava is just text—write in Markdown, save as a file, and you have a page. No database to manage, your files are your content.
---

Content in Ava is just text. You write in [Markdown](https://www.markdownguide.org/basic-syntax/), which is a simple way to format text, and save it as a file. There's no database to manage—your files are your content. 

Ava can handle any combination of Markdown and standard HTML, even within the same file. You can also embed safe and reusable PHP snippets using [Shortcodes](/docs/shortcodes) for absolute flexibility.

<details class="beginner-box">
<summary>What is Markdown?</summary>
<div class="beginner-box-content">

## What is Markdown?

Markdown is a lightweight way to format text using plain characters.

- You write readable text.
- You sprinkle in simple symbols for headings, links, lists, and code.
- Mix in your own custom HTML if required for advanced styling.
- Ava (and your theme) turns it into HTML.

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

[View full Markdown reference](https://www.markdownguide.org/basic-syntax/)

### Markdown Editors (use what you like)

You can write Ava content in almost anything:

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

You can keep drafts forever. Set `status: draft` while writing, then switch to `published` when you’re happy.

## Creating Content

### Manually

Create a `.md` file in the appropriate directory:

```bash
# content/posts/my-new-post.md
```

Add frontmatter and content, then save. If cache mode is `auto`, the site updates immediately.

### Via the Admin Dashboard

If you have the admin dashboard enabled, you can create, edit, and delete content files directly in the browser. Ava writes changes back to your Markdown files (files remain the source of truth).

See [Admin Dashboard](/docs/admin).

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
<strong>Beginner's need not worry:</strong> the CLI isn't "advanced mode", it's just a helper that saves you from remembering boilerplate and file naming. It's a great way to dip your toes in to command-line tools without getting overwhelmed.
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

These fields are used by Ava to manage and display your content.

| Field | Required | Description |
|-------|----------|-------------|
| `title` | No* | Display title. If omitted, Ava derives one from `slug` (e.g. `my-post` → `My Post`). |
| `slug` | No* | A URL-safe identifier. If omitted, Ava sets it from the **filename** (not the title). Must match: lowercase letters/numbers + hyphens only (`^[a-z0-9-]+$`). |
| `status` | No | `draft`, `published`, or `unlisted`. Defaults to `draft`. |
| `id` | No | Optional unique identifier. Useful for stable references and ID-based URLs (see below). |
| `date` | No | Optional date. Ava accepts common date strings (and some other types); invalid values become `null`. |
| `updated` | No | Optional updated timestamp. If omitted (or invalid), Ava falls back to `date`. |
| `excerpt` | No | Optional short summary for listings/search/etc. |
| `template` | No | Optional template override (e.g., `landing.php`). |

\*Ava’s linter validates that the computed `title` and `slug` are non-empty, but Ava also supplies defaults:

- `slug` defaults to the filename (e.g. `hello-world.md` → `hello-world`)
- `title` defaults to a title-cased version of `slug`

In other words: you *can* omit `title`/`slug` in frontmatter, but your **filename still needs to be a valid slug** or `./ava lint` will fail.

#### Slugs and URLs (pattern vs hierarchical)

Ava supports two URL styles per content type (configured in `app/config/content_types.php`):

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

- Ava can fetch an item by ID via the repository index.
- You can create stable, rename-proof permalinks by using `{id}` in a pattern URL (e.g. `'/p/{id}'`).
- Ava detects duplicate IDs during indexing (helpful when merging content).

If you don’t need any of that, you can omit `id:` entirely.

### Taxonomy Fields

Assign content to categories, tags, or any taxonomy defined in [taxonomies.php](/docs/configuration#taxonomies).

```yaml
category:
  - tutorials
  - php
tag:
  - getting-started
  - beginner
```

You can use either a single value or a list. Ava normalizes both into an array when reading.

**How taxonomy indexing works:**

- Only taxonomies defined in `app/config/taxonomies.php` are indexed and routed
- Taxonomy indexes only include **published** content (not drafts or unlisted items)
- Terms do not need to be "pre-created" — if a published item references a term slug, the term appears automatically
- Term display names are auto-generated from slugs (e.g., `php-tutorials` → `Php Tutorials`) unless defined in a registry file
- Term slugs should be lowercase alphanumeric with hyphens

#### Single vs Multiple Terms

```yaml
# Single term (string)
category: tutorials

# Multiple terms (array)
category:
  - tutorials
  - php
  - cms
```

Both formats work — Ava normalizes single values into arrays internally.

#### Alternative format: `tax:` map

If you prefer to group all taxonomies under a single key:

```yaml
tax:
  category: [tutorials, php]
  tag: beginner
```

This keeps frontmatter tidy when you have many taxonomies.

#### Accessing Terms in Templates

```php
// Get terms for a specific taxonomy on an item
<?php foreach ($content->terms('category') as $term): ?>
    <a href="<?= $ava->termUrl('category', $term) ?>">
        <?= $ava->termName('category', $term) ?>
    </a>
<?php endforeach; ?>

// Get all terms for a taxonomy across the site
<?php foreach ($ava->terms('category') as $slug => $info): ?>
    <li>
        <a href="<?= $ava->termUrl('category', $slug) ?>">
            <?= $ava->e($info['name']) ?>
        </a>
        <span class="count">(<?= $info['count'] ?>)</span>
    </li>
<?php endforeach; ?>
```

#### Term registries (`content/_taxonomies/*.yml`)

You can optionally define a "term registry" file per taxonomy to add metadata:

- `content/_taxonomies/category.yml`
- `content/_taxonomies/tag.yml`

Registry files let you:

- Define term display names (instead of auto-generated)
- Add descriptions, images, or custom fields to terms
- Pre-create terms before any content uses them

**Registry file format:**

```yaml
# content/_taxonomies/category.yml
- slug: tutorials
  name: Tutorials
  description: Step-by-step guides and how-tos
  icon: book

- slug: php
  name: PHP
  description: PHP-specific content

- slug: reference
  name: Reference
  description: API and syntax reference
```

**How registry merging works:**

- Terms used in published content get their `count` and `items` from indexing, plus any extra fields from the registry
- Terms that exist only in the registry appear with `count: 0`
- Registry fields are available in templates via `$ava->terms('category')[$slug]['description']` etc.

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

Fine-tune how Ava handles this specific piece of content.

| Field | Description |
|-------|-------------|
| `cache` | Set to `false` to disable page caching for this URL. Set to `true` to force caching. |
| `redirect_from` | Array of old URLs that should 301 redirect here. |
| `template` | Override the default template for this content type (e.g., `landing.php`). |

### Per-Item Assets

Load CSS or JS only on specific pages:

<pre><code class="language-yaml">assets:
  css:
    - "@media<span></span>:css/custom-post.css"
  js:
    - "@media<span></span>:js/interactive.js"
</code></pre>

### Custom Fields

You can add **any custom fields** you like! They're accessible in your templates via `$item->get('field_name')`.

```yaml
---
title: Team Member
slug: jane-doe
status: published
# Custom fields:
role: Lead Developer
website: "https://janedoe.com"
featured: true
---
```

In your template:
```php
<h1><?= $ava->e($item->title()) ?></h1>
<p>Role: <?= $ava->e($item->get('role')) ?></p>
<?php if ($item->get('featured')): ?>
    <span class="badge">Featured</span>
<?php endif; ?>
```

You can also define expected fields for a content type in [content_types.php](/docs/configuration#content-types) using the `fields` option—useful for documentation and validation.

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

## Where to Put Images and Media

<details class="beginner-box">
<summary>Start here</summary>
<div class="beginner-box-content">

- Put user-facing images and files in <code>public/media/</code> (use the <code>@media<span></span>:</code> alias).
- If the admin dashboard is enabled, you can upload files via the media uploader (uploads go into <code>public/media/</code>).
- Theme assets are handled by your theme’s asset pipeline/helpers; don’t drop theme CSS/JS here.
- Keep content (Markdown) in <code>content/</code>; reference media via aliases so paths stay clean.

Project snapshot:

```text
public/
  media/         # your images, PDFs, downloads
  index.php
content/         # your Markdown pages/posts
```

Example Markdown:

<pre><code class="language-markdown">![Team photo](@media<span></span>:team/group.jpg)

[Download PDF](@media<span></span>:docs/guide.pdf)
</code></pre>

</div>
</details>

## Path Aliases

Use aliases instead of hard-coded URLs. These are configured in [`ava.php`](/docs/configuration#path-aliases) and expanded at render time:

| Alias | Default Expansion | Use For |
|-------|-------------------|--------|
| <code>@media<span></span>:</code> | <code>/media/</code> | Images, downloads, per-post CSS/JS |

You can add custom aliases in your config (e.g., <code>@cdn<span></span>:</code> for a CDN URL).

Use in your Markdown:

<pre><code class="language-markdown">![Hero image](@media<span></span>:hero.jpg)

[Download PDF](@media<span></span>:docs/guide.pdf)
</code></pre>

This makes it easy to change asset locations or add a CDN later without updating every content file.

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

Ava is flexible—you can edit directly on a live server, work locally and deploy, or any combination that suits your workflow.

<details class="beginner-box">
<summary>Choosing Your Workflow</summary>
<div class="beginner-box-content">

## Choosing Your Workflow

There's no single "right" way to work with Ava. Here are some common approaches:

### Option 1: Edit Directly on Your Server

The simplest approach—just edit files on your web server.

**How:** Use SFTP (FileZilla, Cyberduck, WinSCP), your host's file manager, or SSH.

**Pros:**
- No extra setup—changes are live immediately
- Great for quick fixes and content updates
- Works from any computer

**Cons:**
- No undo if you break something (make backups!)
- Slower if you're making lots of changes

### Option 2: Work Locally, Then Upload

Edit on your own computer, preview locally, then upload when ready.

**How:** Run PHP's built-in server (see below), edit in your favourite editor, upload via SFTP when happy.

**Pros:**
- Fast feedback loop—save, refresh, repeat
- Can work offline
- Safer—test changes before they go live

**Cons:**
- Need PHP installed on your computer
- Extra step to upload changes

### Option 3: Use Git + Remote Repository

Track all changes with version control and sync via GitHub, GitLab, or similar.

**How:** Commit changes locally, push to a remote repository, pull or deploy on your server.

**Pros:**
- Full history of every change (easy to undo mistakes)
- Great for collaboration
- Can automate deployments

**Cons:**
- Steeper learning curve if you're new to Git
- More setup involved

### Mix and Match

Many people combine approaches: quick content fixes directly on the server, bigger design changes locally with Git. Do what works for you!

</div>
</details>

### Local Preview

If you're working on your own machine, PHP's built-in server is the quickest way to preview:

```bash
php -S localhost:8000 -t public
```

Then open `http://localhost:8000` in your browser.

<div class="callout-warning">
This is a development server, not for public use. See the <a href="hosting.md">Hosting Guide</a> for production options.
</div>

### Live Editing

Editing files directly on your server works great too! Ava's auto-rebuild mode (the default) means changes appear immediately—just save and refresh.

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
