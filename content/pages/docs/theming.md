---
title: Theming
status: published
meta_title: Theming | Flat-file PHP CMS | Ava CMS
meta_description: Create Ava CMS themes with HTML and PHP. No custom templating language—just familiar HTML with PHP helpers for dynamic content, assets, and layouts.
excerpt: Ava CMS themes are HTML-first templates with PHP available when you need it. No custom templating language, no build step—just save and refresh.
---

Ava CMS themes are HTML-first templates with PHP available when you need it. Start with normal HTML, then sprinkle in `<?= ?>` to output data or call helpers. There's no custom templating language, no build step, and no new syntax to learn.

```php
<!-- Output a title -->
<h1><?= $content->title() ?></h1>

<!-- Render the Markdown content as HTML -->
<div class="content">
    <?= $ava->body($content) ?>
</div>

<!-- Link to a stylesheet in your theme's assets folder -->
<link rel="stylesheet" href="<?= $ava->asset('style.css') ?>">

<!-- Loop through recent posts -->
<?php foreach ($ava->recent('post', 5) as $entry): ?>
    <article>
        <h2><a href="<?= $ava->url('post', $entry->slug()) ?>"><?= $entry->title() ?></a></h2>
        <time><?= $ava->date($entry->date()) ?></time>
    </article>
<?php endforeach; ?>
```

You decide how much custom PHP to use: none for simple pages, or more for dynamic layouts. The helpers are there when you want them, but HTML remains the core.

<details class="beginner-box">
<summary>Why HTML + PHP (and not a custom templating language)?</summary>
<div class="beginner-box-content">

**What you gain**
- **Familiar building blocks** — If you know HTML, you can start immediately. Output is just `<?= $variable ?>` and the `$ava` helper.
- **No build pipeline** — Save the file, refresh the browser. No extra compilers, watchers or dependencies needed.
- **Full power available** — Need a loop, conditional, or a custom helper? Use plain PHP. No special template language or custom syntax.
- **Easy to debug** — Standard PHP errors, standard stack traces. Nothing is hidden behind a template engine.

**Who this suits**
- Designers comfortable with HTML/CSS who want minimal new concepts
- Developers who want flexibility without adopting a custom template language or dealing with additional tooling
- Beginners who want to learn web fundamentals instead of framework-specific magic
- Teams that prefer transparency and portability

</div>
</details>

<details class="beginner-box">
<summary>What is `&lt;?= ?&gt;`?</summary>
<div class="beginner-box-content">

### What is `<?= ?>`?

`<?= ?>` is a short way to output a value in PHP. It’s exactly the same as writing `<?php echo ?>`, just shorter and easier to read.

It’s called a **short echo tag** and is **always enabled in modern PHP**.

⚠️ It does **not** escape output automatically. Use `htmlspecialchars()` or `$ava->e()` when outputting user-provided data to prevent XSS attacks.

**Don’t confuse it with `<? ?>`:**  
That older shorthand (without the `=`) is discouraged and disabled by default.  As long as you include the `=`, you’re using the correct syntax.

[Learn more about PHP tags →](https://www.php.net/manual/en/language.basic-syntax.phptags.php)

</div>
</details>

## Theme Structure

A theme is just a folder in `app/themes/`. Here's a typical layout:

```
app/themes/
└── default/
    ├── templates/        # Your page layouts
    │   ├── index.php     # The default layout
    │   ├── page.php      # For standard pages
    │   ├── post.php      # For blog posts
    │   └── 404.php       # "Page not found" error
    ├── partials/         # Reusable template fragments
    │   ├── header.php
    │   └── footer.php
    ├── assets/           # CSS, JS, images
    │   ├── style.css
    │   └── script.js
    └── theme.php         # Optional setup code
```

## Using Assets

Ava CMS makes it easy to include your CSS and JS files. It even handles cache-busting automatically, so your visitors always see the latest version based on the files modified time.

```php
<!-- Just ask $ava for the asset URL -->
<link rel="stylesheet" href="<?= $ava->asset('style.css') ?>">
<script src="<?= $ava->asset('script.js') ?>"></script>
```

<div class="callout-info">
This outputs a URL like <code>/theme/style.css?v=123456</code>, ensuring instant updates when you change the file without worrying about browser or CDN caching.
</div>

<div class="callout-warning"><strong>Theme Assets Security</strong>:
The `assets/` folder inside your theme is served publicly via the `/theme/` URL route. Only common static file types are allowed: CSS, JavaScript, JSON, images (PNG, JPG, GIF, WebP, SVG, ICO), and fonts (WOFF, WOFF2, TTF, EOT). Any other file types—including PHP, HTML, and hidden files (dotfiles) may return a 404 Not Found response by default.
<br><br>
<strong>Treat your theme's `assets/` folder as a public directory</strong>: never place sensitive files, configuration, or executable code there. For private theme files like PHP templates, use the `templates/` or `partials/` folders which are not web-accessible.

</div>

## Template Basics

In your template files (like `page.php`), you have access to your content and helper variables.

```php
<!-- templates/post.php -->
<?= $ava->partial('header', ['title' => $content->title()]) ?>

<article>
    <h1><?= $content->title() ?></h1>
    
    <div class="content">
        <?= $ava->body($content) ?>
    </div>
    
    <?php if ($content->date()): ?>
        <time><?= $ava->date($content->date()) ?></time>
    <?php endif; ?>
</article>

<?= $ava->partial('footer') ?>
```

It's just HTML with simple tags to show your data.

## Quick Reference

This section gives you a complete overview of what's available in templates.

### Template Variables

These variables are available in your templates:

| Variable | Type | Description |
|----------|------|-------------|
| `$content` | `Item` | The current content item (single post/page templates only) |
| `$query` | `Query` | Query object for archive/listing templates |
| `$tax` | `array` | Taxonomy context (taxonomy templates only). See details below. |
| `$site` | `array` | Site config: `name`, `url`, `timezone` |
| `$theme` | `array` | Theme info: `name`, `path`, `url` |
| `$request` | `Request` | Current HTTP request (path, query params, etc.) |
| `$route` | `RouteMatch` | Matched route information (type, params, template) |
| `$ava` | `TemplateHelpers` | Helper methods for rendering, URLs, queries, and more |

#### The `$tax` Variable

The `$tax` array is available in taxonomy templates and has different contents depending on the route type:

**In `taxonomy.php` (term archive):**

| Key | Type | Description |
|-----|------|-------------|
| `$tax['name']` | `string` | Taxonomy name (e.g., `'category'`) |
| `$tax['term']` | `array` | Term data: `slug`, `name`, `count`, `items`, plus any custom fields from registry |

**In `taxonomy-index.php` (all terms listing):**

| Key | Type | Description |
|-----|------|-------------|
| `$tax['name']` | `string` | Taxonomy name (e.g., `'category'`) |
| `$tax['terms']` | `array` | All terms with their data (keyed by slug) |

<div class="callout-info">
Not all variables are present in every template. For example, <code>$content</code> only exists on single content pages, while <code>$query</code> is for archives.
</div>

### The `$content` Object — All Properties

When displaying a single piece of content (a page, post, etc.), use `$content` to access its data:

| Method | Returns | Description |
|--------|---------|-------------|
| **Identity** | | |
| `id()` | `string\|null` | Unique identifier (ULID) |
| `title()` | `string` | Title from frontmatter |
| `slug()` | `string` | URL-friendly identifier |
| `type()` | `string` | Content type (`page`, `post`, etc.) |
| `status()` | `string` | `draft`, `published`, or `unlisted` |
| **Status Checks** | | |
| `isPublished()` | `bool` | Is status "published"? |
| `isDraft()` | `bool` | Is status "draft"? |
| `isUnlisted()` | `bool` | Is status "unlisted"? |
| **Dates** | | |
| `date()` | `DateTimeImmutable\|null` | Publication date (and time if specified) |
| `updated()` | `DateTimeImmutable\|null` | Last updated (falls back to `date()`) |
| **Content** | | |
| `rawContent()` | `string` | Raw Markdown body (before rendering) |
| `excerpt()` | `string\|null` | Excerpt from frontmatter |
| `rawHtml()` | `bool` | Whether to render body as raw HTML (skips Markdown) |
| **Taxonomies** | | |
| `terms()` | `array` | All taxonomy terms |
| `terms('category')` | `array` | Terms for a specific taxonomy |
| **SEO** | | |
| `metaTitle()` | `string\|null` | Custom meta title |
| `metaDescription()` | `string\|null` | Meta description |
| `noindex()` | `bool` | Should search engines skip this? |
| `canonical()` | `string\|null` | Canonical URL |
| `ogImage()` | `string\|null` | Open Graph image URL |
| **Custom Fields** | | |
| `get('field')` | `mixed` | Get any frontmatter field |
| `get('field', 'default')` | `mixed` | Get field with default value |
| `has('field')` | `bool` | Check if field exists |
| **Assets & Structure** | | |
| `css()` | `array` | Per-item CSS files |
| `js()` | `array` | Per-item JS files |
| `template()` | `string\|null` | Custom template name |
| `parent()` | `string\|null` | Parent page slug |
| `order()` | `int` | Manual sort order |
| `redirectFrom()` | `array` | Old URLs that redirect here |
| `filePath()` | `string` | Path to the Markdown file |
| `html()` | `string\|null` | Pre-rendered HTML (if available) |
| `frontmatter()` | `array` | All frontmatter fields as an array |

### The `$ava` Helper — All Methods

The `$ava` object provides helper methods for common tasks:

| Method | Description |
|--------|-------------|
| **Rendering** | |
| `body($content)` | Render content's Markdown body to HTML |
| `markdown($string)` | Render a Markdown string to HTML |
| `partial($name, $data)` | Render a partial template |
| `expand($path)` | Expand path aliases (e.g., <code>@media<span></span>:</code>) |
| **URLs** | |
| `url($type, $slug)` | URL for a content item |
| `termUrl($taxonomy, $term)` | URL for a taxonomy term page |
| `baseUrl()` | Get the site base URL (from config) |
| `asset($path)` | Theme asset URL with cache-busting |
| `fullUrl($path)` | Full absolute URL from a path |
| **Queries** | |
| `query()` | Start a new content query |
| `recent($type, $count)` | Get recent items (shortcut) |
| `get($type, $slug)` | Get a specific item by slug |
| `terms($taxonomy)` | Get all terms for a taxonomy |
| `termName($taxonomy, $slug)` | Get display name for a term |
| **Dates** | |
| `date($date, $format)` | Format a date (uses site timezone) |
| `ago($date)` | Relative time ("2 days ago") |
| **HTML** | |
| `metaTags($content)` | Output SEO meta tags |
| `itemAssets($content)` | Output per-content CSS/JS |
| `pagination($query, $path)` | Render pagination links |
| **Utilities** | |
| `e($value)` | Escape HTML (for user input) |
| `excerpt($text, $words)` | Truncate text to word count |
| `config($key)` | Get a config value |

## Detailed Guide

### Rendering Content

Display a page or post's main content with `$ava->body($content)`:

```php
<div class="content">
    <?= $ava->body($content) ?>
</div>
```

<details class="beginner-box">
<summary>Why `$ava->body($content)` instead of `$content->body()`?</summary>
<div class="beginner-box-content">

The `$content` object holds your raw Markdown text, but `$ava->body()` does the processing:

1. Converts Markdown → HTML
2. Processes shortcodes like <code>&#91;button&#93;</code>
3. Expands path aliases like <code>@media<span></span>:</code>
4. Applies plugin filters
5. Uses pre-rendered cache when available for better performance

Think of `$content` as the ingredients, and `$ava` as the kitchen that prepares the final dish.

</div>
</details>

### Working with Dates

Format dates using `$ava->date()`, which automatically converts to your site's timezone:

```php
<!-- Uses site's default format from config -->
<?= $ava->date($content->date()) ?>

<!-- Or specify a custom format -->
<?= $ava->date($content->date(), 'F j, Y') ?>        // December 31, 2025
<?= $ava->date($content->date(), 'M j, g:ia') ?>     // Dec 31, 2:30pm
```

Dates in frontmatter can include times:

```yaml
date: 2025-12-31           # Date only
date: 2025-12-31 14:30     # Date with time
date: 2025-12-31T14:30:00  # ISO 8601 format
```

For relative times, use `$ava->ago()`:

```php
<?= $ava->ago($content->date()) ?>  // "2 hours ago", "3 days ago"
```

<div class="callout-info">
Date formats use <a href="https://www.php.net/manual/en/datetime.format.php">PHP's date() format codes</a>. Common codes: <code>Y</code> (year), <code>m</code> (month), <code>d</code> (day), <code>F</code> (full month name), <code>M</code> (short month), <code>j</code> (day without zero), <code>g</code> (12-hour), <code>H</code> (24-hour), <code>i</code> (minutes), <code>a</code> (am/pm).
</div>

### Escaping HTML

Use `$ava->e()` to escape HTML characters in user-submitted data:

```php
<!-- User input from URL - escape it! -->
<p>Search results for: <?= $ava->e($request->query('q')) ?></p>
```

<details class="beginner-box">
<summary>When do you need `$ava->e()`?</summary>
<div class="beginner-box-content">

You only need it for **user-submitted data** like search queries or form input. Your own content from Markdown files is safe to output directly—you control it, so there's no security risk.

</div>
</details>

### Custom Fields

Access any frontmatter field via `$content->get()`:

```php
<p>Role: <?= $content->get('role', 'Unknown') ?></p>

<?php if ($content->get('featured')): ?>
    <span class="badge">Featured</span>
<?php endif; ?>
```

### Taxonomies

Display categories, tags, or any taxonomy terms:

```php
<!-- Terms for the current content item -->
<?php foreach ($content->terms('category') as $term): ?>
    <a href="<?= $ava->termUrl('category', $term) ?>"><?= $term ?></a>
<?php endforeach; ?>

<!-- All terms across your site -->
<?php foreach ($ava->terms('category') as $slug => $info): ?>
    <a href="<?= $ava->termUrl('category', $slug) ?>"><?= $info['name'] ?></a>
    <span>(<?= $info['count'] ?>)</span>
<?php endforeach; ?>
```

### Querying Content

The `$ava->query()` method returns a fluent query builder:

```php
// Get the 5 most recent published posts
$posts = $ava->query()
    ->type('post')
    ->published()
    ->orderBy('date', 'desc')
    ->perPage(5)
    ->get();

foreach ($posts as $entry) {
    echo $entry->title();
}
```

#### Building Queries

| Method | Description | Example |
|--------|-------------|---------|
| `type($type)` | Filter by content type | `->type('post')` |
| `status($status)` | Filter by status | `->status('published')` |
| `published()` | Shortcut for published status | `->published()` |
| `whereTax($tax, $term)` | Filter by taxonomy term | `->whereTax('category', 'tutorials')` |
| `where($field, $value, $op)` | Filter by field value (default `$op` is `=`) | `->where('featured', true)` |
| `orderBy($field, $dir)` | Sort results | `->orderBy('date', 'desc')` |
| `perPage($count)` | Items per page (max 100) | `->perPage(10)` |
| `page($num)` | Current page number | `->page(2)` |
| `search($query)` | Full-text search | `->search('php tutorial')` |
| `searchWeights($weights)` | Customize search scoring | `->searchWeights(['title_phrase' => 100])` |
| `fromParams($params)` | Build query from array | `->fromParams($request->query())` |

**Comparison operators for `where()`:**

The third parameter accepts: `=`, `!=`, `>`, `>=`, `<`, `<=`, `in`, `not_in`, `like`

```php
// Comparison operators
->where('price', 100, '>')       // Greater than
->where('status', ['a', 'b'], 'in')  // Value in array
->where('title', 'php', 'like')      // Contains (case-insensitive)
```

#### Result Methods

| Method | Returns | Description |
|--------|---------|-------------|
| `get()` | `Item[]` | Execute and get items |
| `first()` | `Item\|null` | Get first match |
| `count()` | `int` | Total count (before pagination) |
| `totalPages()` | `int` | Number of pages |
| `currentPage()` | `int` | Current page number |
| `hasMore()` | `bool` | Are there more pages? |
| `hasPrevious()` | `bool` | Are there previous pages? |
| `pagination()` | `array` | Full pagination info |
| `isEmpty()` | `bool` | No results? |

**The `pagination()` method returns:**

```php
$info = $query->pagination();
// [
//     'current_page' => 1,
//     'per_page' => 10,
//     'total' => 42,
//     'total_pages' => 5,
//     'has_more' => true,
//     'has_previous' => false,
// ]
```

#### Shortcuts

```php
// Recent items
$posts = $ava->recent('post', 5);

// Get specific item
$about = $ava->get('page', 'about');
```

### Using Partials

Partials are reusable template fragments in `app/themes/{theme}/partials/`:

```php
<!-- Render a partial -->
<?= $ava->partial('header') ?>

<!-- Pass data to it -->
<?= $ava->partial('header', ['title' => $content->title()]) ?>
```

Inside partials, passed data becomes variables:

```php
<!-- partials/header.php -->
<header>
    <h1><?= $title ?? $site['name'] ?></h1>
</header>
```

Partials automatically inherit `$site`, `$theme`, `$request`, and `$ava`.

### URLs

```php
<?= $ava->url('post', 'hello-world') ?>                // /blog/hello-world
<?= $ava->termUrl('category', 'tutorials') ?>           // /category/tutorials
<?= $ava->asset('style.css') ?>                         // /theme/style.css?v=123456
<?= $ava->fullUrl('/about') ?>                          // https://example.com/about
```

### SEO and Meta Tags

Output all SEO meta tags for a content item:

```php
<head>
    <?= $ava->metaTags($content) ?>
</head>
```

This outputs meta description, Open Graph tags, canonical URL, and noindex if set.

### Per-content CSS/JS Assets

If your content frontmatter defines per-item assets (CSS/JS), output them in your page `<head>`:

```php
<?= $ava->itemAssets($content) ?>
```

This helper outputs the appropriate `<link>` and `<script>` tags for that item.

## Template Resolution

When a content item is requested, Ava CMS looks for a template in this order:

1. **Frontmatter `template` field** — If the item specifies `template: landing`, use `templates/landing.php`
2. **Content type's template** — From `content_types.php`, e.g., posts use `post.php`
3. **`single.php` fallback** — A generic single-item template
4. **`index.php` fallback** — The ultimate default

**For archives and taxonomy pages:**

| Route Type | Primary Template | Fallback |
|------------|------------------|----------|
| Content type archive | `archive.php` | `index.php` |
| Taxonomy term archive | `taxonomy.php` | `index.php` |
| Taxonomy index (all terms) | `taxonomy-index.php` | `index.php` |

**For error pages:**

| Error | Template | Built-in Fallback |
|-------|----------|-------------------|
| 404 Not Found | `404.php` | Ava CMS's built-in 404 page (if theme doesn't provide one) |
| 500 Server Error | `500.php` | Ava CMS's built-in error page |

<div class="callout-info">
Ava CMS includes built-in error page templates as fallbacks, so your site will always show a reasonable error page even if your theme doesn't include error templates.
</div>

## Taxonomy Templates

Taxonomy pages require specific templates to display term archives and term listings.

### Term Archive Template (`taxonomy.php`)

Displays content tagged with a specific term:

```php
<!-- templates/taxonomy.php -->
<?= $ava->partial('header', ['title' => $tax['term']['name']]) ?>

<h1><?= $ava->e($tax['term']['name']) ?></h1>

<?php if (!empty($tax['term']['description'])): ?>
    <p class="term-description"><?= $ava->e($tax['term']['description']) ?></p>
<?php endif; ?>

<p><?= $tax['term']['count'] ?> items in this <?= $tax['name'] ?></p>

<?php foreach ($query->get() as $item): ?>
    <article>
        <h2>
            <a href="<?= $ava->url($item->type(), $item->slug()) ?>">
                <?= $item->title() ?>
            </a>
        </h2>
        <?php if ($item->excerpt()): ?>
            <p><?= $item->excerpt() ?></p>
        <?php endif; ?>
    </article>
<?php endforeach; ?>

<?= $ava->pagination($query, $request->path()) ?>

<?= $ava->partial('footer') ?>
```

### Taxonomy Index Template (`taxonomy-index.php`)

Displays all terms in a taxonomy:

```php
<!-- templates/taxonomy-index.php -->
<?= $ava->partial('header', ['title' => ucfirst($tax['name'])]) ?>

<h1>All <?= ucfirst($tax['name']) ?>s</h1>

<ul class="term-list">
    <?php foreach ($tax['terms'] as $slug => $term): ?>
        <li>
            <a href="<?= $ava->termUrl($tax['name'], $slug) ?>">
                <?= $ava->e($term['name']) ?>
            </a>
            <span class="count">(<?= $term['count'] ?>)</span>
            <?php if (!empty($term['description'])): ?>
                <p><?= $ava->e($term['description']) ?></p>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>

<?= $ava->partial('footer') ?>
```

## Complete Examples

### Header Partial

```php
<!-- partials/header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? $site['name'] ?></title>
    <?php if (isset($content)): ?>
        <?= $ava->metaTags($content) ?>
        <?= $ava->itemAssets($content) ?>
    <?php endif; ?>
    <link rel="stylesheet" href="<?= $ava->asset('style.css') ?>">
</head>
<body>
    <header class="site-header">
        <a href="/" class="logo"><?= $site['name'] ?></a>
        <nav>
            <a href="/">Home</a>
            <a href="/about">About</a>
            <a href="/blog">Blog</a>
        </nav>
    </header>
    <main>
```

### Footer Partial

```php
<!-- partials/footer.php -->
    </main>
    <footer class="site-footer">
        <p>&copy; <?= date('Y') ?> <?= $site['name'] ?></p>
    </footer>
    <script src="<?= $ava->asset('script.js') ?>"></script>
</body>
</html>
```

### Page Template

```php
<!-- templates/page.php -->
<?= $ava->partial('header', ['title' => $content->title(), 'content' => $content]) ?>

<article class="page">
    <h1><?= $content->title() ?></h1>
    
    <div class="content">
        <?= $ava->body($content) ?>
    </div>
</article>

<?= $ava->partial('footer') ?>
```

### Post Template

```php
<!-- templates/post.php -->
<?= $ava->partial('header', ['title' => $content->title(), 'content' => $content]) ?>

<article class="post">
    <header class="post-header">
        <h1><?= $content->title() ?></h1>
        
        <?php if ($content->date()): ?>
            <time datetime="<?= $content->date()->format('c') ?>">
                <?= $ava->date($content->date()) ?>
            </time>
        <?php endif; ?>
        
        <?php if ($categories = $content->terms('category')): ?>
            <div class="categories">
                <?php foreach ($categories as $term): ?>
                    <a href="<?= $ava->termUrl('category', $term) ?>"><?= $term ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </header>
    
    <div class="content">
        <?= $ava->body($content) ?>
    </div>
</article>

<?= $ava->partial('footer') ?>
```

### Archive Template

```php
<!-- templates/archive.php -->
<?= $ava->partial('header', ['title' => 'Blog']) ?>

<h1>Blog</h1>

<?php foreach ($query->get() as $entry): ?>
    <article class="post-summary">
        <h2><a href="<?= $ava->url('post', $entry->slug()) ?>"><?= $entry->title() ?></a></h2>
        <time><?= $ava->date($entry->date()) ?></time>
        <?php if ($entry->excerpt()): ?>
            <p><?= $entry->excerpt() ?></p>
        <?php endif; ?>
    </article>
<?php endforeach; ?>

<?= $ava->pagination($query, $request->path()) ?>

<?= $ava->partial('footer') ?>
```

## Search

Ava CMS includes full-text search. Add `->search()` to any query to search titles, excerpts, and content:

```php
$results = $ava->query()
    ->published()
    ->search('your query')
    ->perPage(10)
    ->get();
```

Results are automatically scored and ranked by relevance.

### Tuning Search Relevance

By default, exact phrase matches in titles score highest. Customize weights:

- **Globally** — configure `search.weights` in [content type config](/docs/configuration#content-search-configuration)
- **Per query** — override with `->searchWeights()`:

```php
$results = $ava->query()
    ->published()
    ->searchWeights(['title_phrase' => 120, 'body_phrase' => 5])
    ->search($q)
    ->get();
```

### Adding Search to Your Theme

Register a route in `theme.php` to handle search requests:

```php
$app->router()->addRoute('/search', function ($request) use ($app) {
    $q = trim($request->query('q', ''));
    $results = $q ? $app->query()->published()->search($q)->perPage(10)->get() : [];
    
    return $app->render('search', [
        'query' => $q,
        'results' => $results,
    ]);
});
```

### Working examples

- **Default theme** (server-rendered search page): [theme.php](https://github.com/avacms/ava/blob/main/app/themes/default/theme.php) and [search.php template](https://github.com/avacms/ava/blob/main/app/themes/default/templates/search.php)
- **Docs theme** (AJAX popup): [theme.php](https://github.com/avacms/docs/blob/main/app/themes/docs/theme.php) — registers `/search` and `/search.json` routes; the front-end JS renders results in a modal


## Theme Bootstrap

`theme.php` runs when your theme loads. It should return a function that receives the application instance. Use it for hooks, shortcodes, and custom routes:

```php
<?php
// app/themes/yourtheme/theme.php

use Ava\Application;
use Ava\Plugins\Hooks;

return function (Application $app): void {
    // Register shortcodes
    $app->shortcodes()->register('theme_version', fn() => '1.0.0');

    // Add data to all templates
    Hooks::addFilter('render.context', function (array $context) {
        $context['social_links'] = [
            'twitter' => 'https://twitter.com/yoursite',
            'github' => 'https://github.com/yoursite',
        ];
        return $context;
    });

    // Custom route
    $app->router()->addRoute('/search', function ($request) use ($app) {
        // Handle search...
    });
};
```

### Organizing Larger Themes

Split a large `theme.php` into multiple files:

```php
// app/themes/yourtheme/theme.php
return function (\Ava\Application $app): void {
    (require __DIR__ . '/inc/shortcodes.php')($app);
    (require __DIR__ . '/inc/routes.php')($app);
};
```

Each file returns a function receiving `$app`—everything stays portable with your theme folder.

## Community Themes

Looking for ready-made themes? Check out the [Community Themes](/themes) page for themes shared by other Ava CMS users.

<div class="callout-warning"><strong>Security warning:</strong> Community themes are third-party code and are <strong>not vetted or audited</strong> by the Ava CMS project. Themes can include PHP (e.g. <code>theme.php</code>), templates, and assets that execute on your server/in your users’ browsers. Only install themes from authors you trust, review the source, and test in staging before deploying.</div>

Built a theme you'd like to share? [Submit it to the community gallery!](/themes#content-submit-your-theme)


