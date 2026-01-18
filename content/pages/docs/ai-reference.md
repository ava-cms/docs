---
title: AI Reference
slug: ai-reference
status: published
meta_title: AI Reference | Flat-file PHP CMS | Ava CMS
meta_description: Condensed technical reference for AI assistants working with Ava CMS. Essential framework details, conventions, and patterns for building themes, plugins, and content.
excerpt: A condensed technical reference for AI assistants working with Ava CMS, containing essential framework details, conventions, and patterns for building themes, plugins, and content.
---

<div class="callout-info">This is a condensed technical reference for AI assistants working with Ava CMS. It contains the essential framework details, conventions, and patterns needed to help users build themes, plugins, and content.</div>

**For raw Markdown (ideal for AI context):** [View on GitHub](https://raw.githubusercontent.com/avacms/docs/refs/heads/main/content/pages/docs/ai-reference.md)

## Overview

**Full docs:** https://ava.addy.zone/docs

Ava CMS is a flat-file PHP CMS (PHP 8.3+) that requires no database. Content is Markdown files with YAML frontmatter. Configuration is PHP arrays. There's no build step—edit a file, refresh, see changes.

**Core Philosophy:**
- Files are the source of truth (content, config, themes)
- No WYSIWYG—users write Markdown in their preferred editor
- No database required (SQLite optional for 10k+ items)
- Immediate publishing—no build/deploy step
- Bespoke by design—any content type without plugins

**Project Structure:**
```
mysite/
├── app/                  # Your code
│   ├── config/           # Configuration (ava.php, content_types.php, taxonomies.php)
│   ├── plugins/          # Plugin folders
│   ├── snippets/         # PHP snippets for [snippet] shortcode
│   └── themes/{name}/    # Theme templates, assets, partials
├── content/              # Markdown content files
│   ├── pages/            # Hierarchical pages
│   ├── posts/            # Blog posts
│   └── _taxonomies/      # Term registries
├── public/               # Web root (index.php, media/, assets/)
├── storage/cache/        # Index and page cache
└── ava                   # CLI tool
```

**Requirements:** PHP 8.3+, extensions: `mbstring`, `json`, `ctype`. Optional: `pdo_sqlite`, `igbinary`, `opcache`.


## Configuration

**Full docs:** https://ava.addy.zone/docs/configuration

All settings in `app/config/` as PHP arrays. Three main files:

- `ava.php` — Main settings (site, paths, cache, themes, plugins, security, debug)
- `content_types.php` — Content type definitions
- `taxonomies.php` — Taxonomy definitions

### Main Settings (`ava.php`)

**Site Identity:**
```php
'site' => [
    'name'        => 'My Site',
    'base_url'    => 'https://example.com',  // No trailing slash
    'timezone'    => 'Europe/London',
    'locale'      => 'en_GB',
    'date_format' => 'F j, Y',
],
```

**Paths:**
```php
'paths' => [
    'content'  => 'content',
    'themes'   => 'app/themes',
    'plugins'  => 'app/plugins',
    'snippets' => 'app/snippets',
    'storage'  => 'storage',
    'aliases'  => [
        '@media:' => '/media/',
    ],
],
```

**Content Index (performance-critical):**
```php
'content_index' => [
    'mode'         => 'auto',   // 'auto' (dev), 'never' (prod), 'always' (debug)
    'backend'      => 'array',  // 'array' or 'sqlite' (for 10k+ items)
    'use_igbinary' => true,
    'prerender_html' => false,  // Optional: pre-render Markdown → HTML during rebuild
],
```

**Webpage Cache:**
```php
'webpage_cache' => [
    'enabled' => true,
    'ttl'     => null,           // null = until rebuild
    'exclude' => ['/api/*'],
],
```

**Content Parsing:**
```php
'content' => [
    'markdown' => [
        'allow_html'      => true,
        'heading_ids'     => true,
        'disallowed_tags' => ['script', 'noscript'],
    ],
    'id' => ['type' => 'ulid'],  // 'ulid' or 'uuid7'
],
```

**Security:**
```php
'security' => [
    'shortcodes' => ['allow_php_snippets' => true],
    'preview_token' => 'secure-random-token',
],
```

**Admin:**
```php
'admin' => [
    'enabled' => true,
    'path'    => '/admin',
    'theme'   => 'cyan',  // cyan, pink, purple, green, blue, amber
    'media'   => [
        'enabled'          => true,
        'path'             => 'public/media',
        'organize_by_date' => true,
        'max_file_size'    => 10 * 1024 * 1024,
        'allowed_types'    => ['image/jpeg', 'image/png', 'image/webp', ...],
    ],
],
```

**Plugins:** `'plugins' => ['sitemap', 'feed', 'redirects'],`

**Debug:**
```php
'debug' => [
    'enabled'        => false,
    'display_errors' => false,  // Never true in production
    'log_errors'     => true,
    'level'          => 'errors',  // 'all', 'errors', 'none'
],
```

### Content Types (`content_types.php`)

```php
return [
    'post' => [
        'label'       => 'Posts',
        'content_dir' => 'posts',
        'url' => [
            'type'    => 'pattern',
            'pattern' => '/blog/{slug}',
            'archive' => '/blog',
        ],
        'templates' => [
            'single'  => 'post.php',
            'archive' => 'archive.php',
        ],
        'taxonomies'   => ['category', 'tag'],
        'sorting'      => 'date_desc',
        'cache_fields' => ['author', 'featured_image'],
        'search' => [
            'enabled' => true,
            'fields'  => ['title', 'excerpt', 'body'],
            'weights' => [
                'title_phrase'   => 80,   // Exact phrase in title
                'title_token'    => 10,   // Per-word match in title
                'excerpt_phrase' => 30,   // Exact phrase in excerpt
                'body_phrase'    => 20,   // Exact phrase in body
                'body_token'     => 2,    // Per-word match in body
                'featured'       => 15,   // Bonus for featured:true
            ],
        ],
    ],
];
```

**Search Weights:** Configure relevance scoring per content type. Higher weights = higher ranking.

| Weight | Default | Description |
|--------|---------|-------------|
| `title_phrase` | 80 | Exact phrase match in title |
| `title_all_tokens` | 40 | All search words in title |
| `title_token` | 10 | Per-word match in title (max 30) |
| `excerpt_phrase` | 30 | Exact phrase in excerpt |
| `excerpt_token` | 3 | Per-word match in excerpt (max 15) |
| `body_phrase` | 20 | Exact phrase in body |
| `body_token` | 2 | Per-word match in body (max 10) |
| `featured` | 15 | Bonus for `featured: true` items |
| `field_weight` | 5 | Per custom field match |

**Per-query weight override:**
```php
$results = $ava->query()
    ->type('post')
    ->search('tutorial')
    ->searchWeights(['title_phrase' => 100, 'featured' => 0])
    ->get();
```

**URL Types:**
- `hierarchical` — URLs mirror file paths. `content/pages/about/team.md` → `/about/team`
- `pattern` — Template-based. Placeholders: `{slug}`, `{id}`, `{yyyy}`, `{mm}`, `{dd}`

**Sorting options:** `date_desc`, `date_asc`, `title`, `manual` (by `order` field)

### Taxonomies (`taxonomies.php`)

```php
return [
    'category' => [
        'label'        => 'Categories',
        'hierarchical' => true,
        'public'       => true,
        'rewrite'      => ['base' => '/category'],
    ],
    'tag' => [
        'label'        => 'Tags',
        'hierarchical' => false,
        'public'       => true,
        'rewrite'      => ['base' => '/tag'],
    ],
];
```

Term registries: `content/_taxonomies/{taxonomy}.yml` — Pre-define terms with metadata.

### Environment-Specific Config

```php
$config = [...];
if (getenv('APP_ENV') === 'development') {
    $config['content_index']['mode'] = 'auto';
    $config['debug']['enabled'] = true;
}
return $config;
```

## Content

**Full docs:** https://ava.addy.zone/docs/content

Content files are Markdown with YAML frontmatter. Located in `content/` folder, organized by content type.

### File Structure

```
content/
├── pages/           # Hierarchical pages
│   ├── index.md     # Homepage (/)
│   ├── about.md     # /about
│   └── docs/
│       └── index.md # /docs
├── posts/           # Pattern-based posts
│   └── hello.md     # /blog/hello (if pattern is /blog/{slug})
└── _taxonomies/     # Term registries
    └── category.yml
```

### Frontmatter

```yaml
---
id: 01JGMK0000POST0000000001
title: My Post
slug: my-post
status: published
date: 2024-12-28
updated: 2024-12-30
excerpt: Short summary for listings
template: custom.php
order: 10
category:
  - tutorials
tag:
  - php
meta_title: SEO Title
meta_description: SEO description
canonical: https://example.com/post
og_image: "@media:social.jpg"
noindex: false
cache: true
redirect_from:
  - /old-url
assets:
  css:
    - "@media:css/custom.css"
  js:
    - "@media:js/script.js"
---
```

**Core Fields:**
- `title` — Display title (defaults to slugified filename)
- `slug` — URL identifier. For hierarchical types, URL comes from file path, not slug
- `status` — `draft`, `published`, `unlisted`
- `id` — Optional ULID/UUID7 for stable references
- `date`, `updated` — Timestamps
- `excerpt` — Summary for listings/search
- `template` — Override default template

**Taxonomy Fields:**
```yaml
category: tutorials           # Single term
category:                     # Multiple terms
  - tutorials
  - php
tax:                          # Alternative grouped format
  category: [tutorials, php]
  tag: beginner
```

**SEO Fields:** `meta_title`, `meta_description`, `canonical`, `noindex`, `og_image`

**Behavior Fields:**
- `cache: false` — Disable caching for this page
- `redirect_from: [/old-url]` — 301 redirects from old URLs
- `order` — Manual sort order (use with `sorting: 'manual'`)

**Custom Fields:** Any field is accessible via `$item->get('field_name')`

### Content Status

| Status | Behavior |
|--------|----------|
| `draft` | Not publicly routed. Viewable via preview token. |
| `published` | Public. In listings, archives, taxonomy indexes. |
| `unlisted` | Public via direct URL. Excluded from listings/archives/taxonomies. |

### Path Aliases

Defined in `ava.php`, expanded at render time:
```markdown
![Image](@media:photo.jpg)  →  /media/photo.jpg
```

### Creating Content

**CLI:** `./ava make post "Title"` — Creates file with ULID, slug, date, draft status

**Validation:** `./ava lint` — Checks YAML, required fields, duplicate slugs/IDs

## CLI

**Full docs:** https://ava.addy.zone/docs/cli

Run from project root: `./ava <command> [options]`

### Core Commands

| Command | Description |
|---------|-------------|
| `status` | Site overview and health check |
| `rebuild` | Rebuild content index |
| `lint` | Validate all content files |
| `make <type> "Title"` | Create new content with scaffolding |
| `prefix <add\|remove> [type]` | Toggle date prefixes on filenames |

### User Management

| Command | Description |
|---------|-------------|
| `user:add <email> <pass> [name]` | Create admin user |
| `user:password <email> <pass>` | Update password |
| `user:remove <email>` | Remove user |
| `user:list` | List all users |

### Cache & Logs

| Command | Description |
|---------|-------------|
| `cache:stats` | Webpage cache statistics |
| `cache:clear [pattern]` | Clear cached pages |
| `logs:stats` | Log file statistics |
| `logs:tail [name] [-n N]` | Show last N lines of log |
| `logs:clear [name]` | Clear logs |

### Updates

| Command | Description |
|---------|-------------|
| `update:check [--force]` | Check for updates |
| `update:apply [-y] [--dev]` | Apply update |

### Testing & Benchmarking

| Command | Description |
|---------|-------------|
| `test [filter] [-q]` | Run test suite |
| `benchmark [--compare]` | Benchmark content index |
| `stress:generate <type> <n>` | Generate test content |
| `stress:clean <type>` | Remove test content |

### Plugin Commands

Plugins can register commands. Bundled plugins provide:
- `sitemap:stats`, `feed:stats`
- `redirects:list`, `redirects:add <from> <to> [code]`, `redirects:remove <from>`

## Admin Dashboard

**Full docs:** https://ava.addy.zone/docs/admin

Optional web-based admin for quick edits and monitoring. Files remain the source of truth.

### Enabling

```php
'admin' => [
    'enabled' => true,
    'path'    => '/admin',
    'theme'   => 'cyan',
],
```

Create users via CLI: `./ava user:add email@example.com password "Name"`

### Features

- **Content**: Browse, create, edit, delete Markdown files
- **Validation**: Run content linter
- **Maintenance**: Rebuild index, clear cache
- **Taxonomies**: Manage terms via file-backed registry (`content/_taxonomies/*.yml`)
- **Media**: Upload images (optional, sanitized through ImageMagick/GD)
- **Logs**: View admin activity and system info

### Content Safety

Admin editor blocks high-risk HTML (`<script>`, `<iframe>`, `on*=` handlers, `javascript:` URLs). For advanced HTML, edit files directly.

### Security

- **HTTPS required** in production (localhost excepted)
- **Passwords**: bcrypt hashed (cost 12), never stored plain
- **Rate limiting**: 5 failed logins → 15-minute lockout
- **Session**: HttpOnly cookies, SameSite=Lax, regenerated on login/logout
- **CSRF**: Tokens on all forms, timing-safe verification

### Media Upload Config

```php
'admin' => [
    'media' => [
        'enabled'          => true,
        'path'             => 'public/media',
        'organize_by_date' => true,
        'max_file_size'    => 10 * 1024 * 1024,
        'allowed_types'    => ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml', 'image/avif'],
    ],
],
```

## Theming

**Full docs:** https://ava.addy.zone/docs/theming

Themes are HTML + PHP templates. No build step, no custom templating language.

### Structure

```
app/themes/mytheme/
├── templates/        # Page layouts
│   ├── index.php     # Default fallback
│   ├── page.php      # Pages
│   ├── post.php      # Posts
│   ├── archive.php   # Listings
│   ├── taxonomy.php  # Term archives
│   └── 404.php       # Not found
├── partials/         # Reusable fragments
├── assets/           # CSS, JS, images
└── theme.php         # Bootstrap (optional)
```

### Template Variables

| Variable | Type | Description |
|----------|------|-------------|
| `$content` | `Item` | Current content item (single pages) |
| `$query` | `Query` | Query for archives/listings |
| `$tax` | `array` | Taxonomy context (taxonomy pages) |
| `$site` | `array` | Site config: `name`, `url`, `timezone` |
| `$request` | `Request` | Current HTTP request |
| `$ava` | `TemplateHelpers` | Helper methods |

### Content Item (`$content`) Methods

```php
$content->id()              // ULID
$content->title()           // Title
$content->slug()            // URL slug
$content->type()            // 'page', 'post', etc.
$content->status()          // 'draft', 'published', 'unlisted'
$content->date()            // DateTimeImmutable|null
$content->updated()         // DateTimeImmutable|null
$content->excerpt()         // Excerpt string
$content->terms('category') // Taxonomy terms array
$content->get('field')      // Custom frontmatter field
$content->has('field')      // Check field exists
$content->metaTitle()       // SEO title
$content->metaDescription() // SEO description
```

### Template Helpers (`$ava`) Methods

**Rendering:**
```php
$ava->body($content)              // Render content body (uses pre-render cache when enabled)
$ava->markdown($string)           // Render Markdown string
$ava->partial('header', $data)    // Include partial with data
```

**URLs:**
```php
$ava->url('post', 'my-slug')      // Content URL
$ava->termUrl('category', 'php')  // Term archive URL
$ava->asset('style.css')          // Theme asset with cache-bust
$ava->baseUrl()                   // Site base URL
```

**Queries:**
```php
$ava->query()                     // New query builder
$ava->recent('post', 5)           // Recent items shortcut
$ava->get('page', 'about')        // Get specific item
$ava->terms('category')           // All terms for taxonomy
```

**Utilities:**
```php
$ava->date($date, 'F j, Y')       // Format date
$ava->ago($date)                  // "2 days ago"
$ava->e($value)                   // HTML escape
$ava->metaTags($content)          // Output SEO meta tags
$ava->itemAssets($content)        // Output per-item CSS/JS
$ava->pagination($query, $path)   // Render pagination
$ava->config('key.subkey')        // Get config value
```

### Query Builder

```php
$posts = $ava->query()
    ->type('post')
    ->published()
    ->whereTax('category', 'tutorials')
    ->where('featured', true)
    ->orderBy('date', 'desc')
    ->perPage(10)
    ->page(1)
    ->search('query')
    ->get();

// Result methods
$query->count()        // Total items
$query->totalPages()   // Page count
$query->hasMore()      // More pages?
$query->pagination()   // Full pagination info
```

**Where operators:** `=`, `!=`, `>`, `>=`, `<`, `<=`, `in`, `not_in`, `like`

### Template Resolution

1. Frontmatter `template: landing` → `templates/landing.php`
2. Content type's configured template
3. `single.php` fallback
4. `index.php` fallback

### Theme Bootstrap (`theme.php`)

```php
<?php
return function (\Ava\Application $app): void {
    // Register shortcodes
    $app->shortcodes()->register('mycode', fn() => 'output');
    
    // Add to template context
    Hooks::addFilter('render.context', function ($ctx) {
        $ctx['custom'] = 'value';
        return $ctx;
    });
    
    // Custom routes
    $app->router()->addRoute('/search', function ($request) use ($app) {
        // ...
    });
};
```

## Routing

**Full docs:** https://ava.addy.zone/docs/routing

URLs are generated automatically from content structure and configuration. Routes are cached in binary files for instant lookups.

### URL Types

**Hierarchical** — URLs mirror file paths:
```php
'page' => [
    'url' => ['type' => 'hierarchical', 'base' => '/'],
]
// content/pages/about/team.md → /about/team
// content/pages/index.md → /
```

**Pattern** — Template-based URLs:
```php
'post' => [
    'url' => [
        'type'    => 'pattern',
        'pattern' => '/blog/{slug}',
        'archive' => '/blog',
    ],
]
```

**Placeholders:** `{slug}`, `{id}`, `{yyyy}`, `{mm}`, `{dd}`

### Route Matching Order

1. Hook interception (`router.before_match`)
2. Trailing slash redirect (301)
3. Redirects from `redirect_from` frontmatter
4. Custom routes (`$router->addRoute()`)
5. Content routes (from cache)
6. Preview mode (drafts with token)
7. Prefix routes (`$router->addPrefixRoute()`)
8. Taxonomy routes
9. 404

### Custom Routes

```php
// In theme.php or plugin.php
$router->addRoute('/api/search', function ($request) use ($app) {
    return \Ava\Http\Response::json(['results' => []]);
});

// With parameters
$router->addRoute('/api/posts/{id}', function ($request, $params) {
    $id = $params['id'];
    // ...
});

// Prefix routes (matches /api/*)
$router->addPrefixRoute('/api/', function ($request) {
    // ...
});
```

### Taxonomy Routes

When `public: true` in taxonomy config:
- `/category` → `taxonomy-index.php`
- `/category/tutorials` → `taxonomy.php`

### Preview Mode

Access drafts: `?preview=1&token=YOUR_TOKEN`

Configure: `'security' => ['preview_token' => 'random-token']`

### Generating URLs

```php
$ava->url('post', 'my-slug')         // /blog/my-slug
$ava->url('page', 'about/team')      // /about/team (hierarchical path)
$ava->termUrl('category', 'php')     // /category/php
$ava->fullUrl('/about')              // https://example.com/about
```

## Shortcodes

**Full docs:** https://ava.addy.zone/docs/shortcodes

Dynamic content in Markdown via `[tag]` syntax. Processed after Markdown conversion.

### Built-in Shortcodes

| Shortcode | Output |
|-----------|--------|
| `[year]` | Current year |
| `[date format="Y-m-d"]` | Formatted current date |
| `[site_name]` | Site name from config |
| `[site_url]` | Site URL from config |
| `[email]you@example.com[/email]` | Obfuscated mailto link |
| `[snippet name="file"]` | Renders `app/snippets/file.php` |

### Creating Shortcodes

```php
// In theme.php
$app->shortcodes()->register('greeting', function ($attrs, $content, $tag) {
    $name = $attrs['name'] ?? 'friend';
    return "Hello, " . htmlspecialchars($name) . "!";
});
```

**Callback parameters:**
- `$attrs` — Array of attributes
- `$content` — Content between tags (null if self-closing)
- `$tag` — Shortcode name (lowercase)

### Snippets

PHP files in `app/snippets/` folder, invoked via `[snippet name="file"]`.

**Variables available:**
- `$params` — Attributes array
- `$content` — Content between tags
- `$ava` — Rendering engine
- `$app` — Application instance

```php
<?php // app/snippets/cta.php ?>
<?php $heading = $params['heading'] ?? 'Ready?'; ?>
<div class="cta">
    <h3><?= htmlspecialchars($heading) ?></h3>
    <?= $content ?>
</div>
```

**Security:** Snippet names can't contain `..` or `/`. Disable with `security.shortcodes.allow_php_snippets = false`.

### Limitations

- No nested shortcodes
- Paired content stops at next `[` character

## Plugins

**Full docs:** https://ava.addy.zone/docs/creating-plugins

Reusable extensions in `app/plugins/{name}/plugin.php`. Survive theme changes.

### Structure

```php
<?php
return [
    'name' => 'My Plugin',
    'version' => '1.0.0',
    'boot' => function($app) {
        // Initialization code
    },
    'commands' => [
        [
            'name' => 'myplugin:status',
            'description' => 'Show status',
            'handler' => function($args, $cli, $app) {
                $cli->success('Working!');
                return 0;
            },
        ],
    ],
];
```

Enable in `ava.php`: `'plugins' => ['sitemap', 'feed', 'my-plugin']`

### Hooks

**Filters** — Modify and return data:
```php
use Ava\Plugins\Hooks;

Hooks::addFilter('render.context', function($context) {
    $context['custom'] = 'value';
    return $context;
}, priority: 10);
```

**Actions** — React to events:
```php
Hooks::addAction('markdown.configure', function($environment) {
    $environment->addExtension(new TableExtension());
});
```

### Available Hooks

| Hook | Type | Description |
|------|------|-------------|
| `router.before_match` | Filter | Intercept routing |
| `content.loaded` | Filter | Modify loaded content item |
| `render.context` | Filter | Add template variables |
| `render.output` | Filter | Modify final HTML |
| `markdown.configure` | Action | Configure CommonMark |
| `admin.register_pages` | Filter | Add admin pages |
| `admin.sidebar_items` | Filter | Add sidebar items |
| `indexer.rebuild` | Action | Fires when the content index is rebuilt (CLI, auto, admin). Preferred for content-syncing plugins. |
| `cli.rebuild` | Action | Runs after a CLI-initiated rebuild, before the command exits (useful for CLI-specific output). |

### Adding Routes

```php
$app->router()->addRoute('/api/data', function($request) use ($app) {
    return \Ava\Http\Response::json(['data' => 'value']);
});

// With parameters
$app->router()->addRoute('/api/posts/{slug}', function($request, $params) {
    $slug = $params['slug'];
    // ...
});
```

### Admin Pages

```php
Hooks::addFilter('admin.register_pages', function($pages) {
    $pages['my-plugin'] = [
        'label' => 'My Plugin',
        'icon' => 'extension',
        'section' => 'Plugins',
        'handler' => function($request, $app, $controller) {
            $content = '<div class="card">...</div>';
            return $controller->renderPluginPage([
                'title' => 'My Plugin',
                'icon' => 'extension',
                'activePage' => 'my-plugin',
            ], $content);
        },
    ];
    return $pages;
});
```

### CLI Commands

```php
'commands' => [
    [
        'name' => 'myplugin:task',
        'description' => 'Do something',
        'handler' => function($args, $cli, $app) {
            $cli->header('Task');
            $cli->info('Processing...');
            $cli->success('Done!');
            return 0;  // Exit code
        },
    ],
],
```

**CLI methods:** `header()`, `info()`, `success()`, `warning()`, `error()`, `writeln()`, `table()`

## Bundled Plugins

**Full docs:** https://ava.addy.zone/docs/bundled-plugins

### Sitemap

Generates `/sitemap.xml` for search engines.

```php
'sitemap' => [
    'enabled' => true,
],
```

Exclude pages with `noindex: true` in frontmatter.

CLI: `./ava sitemap:stats`

### RSS Feed

Generates `/feed.xml` for RSS readers.

```php
'feed' => [
    'enabled' => true,
    'items_per_feed' => 20,
    'full_content' => false,
    'types' => null,  // null = all, or ['post']
],
```

Add to theme: `<link rel="alternate" type="application/rss+xml" href="/feed.xml">`

CLI: `./ava feed:stats`

### Redirects

Manage URL redirects via admin or CLI. Stored in `storage/redirects.json`.

```bash
./ava redirects:list
./ava redirects:add /old /new [301]
./ava redirects:remove /old
```

**Status codes:** 301, 302, 307, 308 (redirects), 410, 451, 503 (status-only)

**Alternative:** Use `redirect_from:` in content frontmatter for content-based redirects.

## Performance

**Full docs:** https://ava.addy.zone/docs/performance

Two-layer system: Content Indexing + Webpage Caching.

### Content Index

Pre-built binary index of content metadata. Avoids parsing Markdown on every request.

**Cache files in `storage/cache/`:**
- `recent_cache.bin` — Top 200 items per type (instant archives)
- `slug_lookup.bin` — Slug → file path map (fast single lookups)
- `content_index.bin` — Full metadata (deep queries, search)
- `tax_index.bin` — Taxonomy terms
- `routes.bin` — URL routing map

**Tiered lookups:**
| Tier | Cache | Use Case | Speed |
|------|-------|----------|-------|
| 1 | Recent Cache | Homepage, RSS, pages 1-20 | ~0.2ms |
| 2 | Slug Lookup | Single post/page | ~1-15ms |
| 3 | Full Index | Search, deep pagination | ~15-300ms |

### Backends

**Array (default):**
```php
'content_index' => [
    'backend' => 'array',
    'use_igbinary' => true,
],
```
- Fastest for most sites
- Memory scales with content size
- igbinary: ~2× faster, smaller files

**SQLite:**
```php
'content_index' => [
    'backend' => 'sqlite',
],
```
- Minimal memory per request
- Best for 10k+ items or memory-constrained servers
- Requires `pdo_sqlite`

### Index Modes

| Mode | Behavior |
|------|----------|
| `auto` | Rebuild on file changes (development) |
| `never` | Only via `./ava rebuild` (production) |
| `always` | Every request (debugging only) |

### Webpage Cache

Stores fully-rendered HTML. Most visitors get static files.

```php
'webpage_cache' => [
    'enabled' => true,
    'ttl'     => null,       // null = until rebuild
    'exclude' => ['/api/*'],
],
```

**Speed:** Uncached ~5ms → Cached ~0.02ms (250× faster)

**Per-page control:** `cache: false` in frontmatter

**Not cached:** Admin pages, POST requests, query strings, logged-in admins

**Invalidation:** `./ava rebuild` clears both index and page cache unless the `--keep-webpage-cache` flag (alias `--keep-webcache`) is used to preserve cached pages during the rebuild.

### CLI

```bash
./ava status              # View index and cache status
./ava rebuild             # Rebuild index, clear cache
./ava rebuild --keep-webpage-cache  # Rebuild index but keep the webpage cache (alias: --keep-webcache)
./ava cache:stats         # Page cache statistics
./ava cache:clear         # Clear page cache
./ava benchmark --compare # Compare backends
```

### Recommendations

**Development:** `mode: auto`, `webpage_cache.enabled: false`

**Production:** `mode: never`, `webpage_cache.enabled: true`, run `./ava rebuild` after deploys

## Hosting

**Full docs:** https://ava.addy.zone/docs/hosting

**Requirements:** PHP 8.3+, Composer, SSH access recommended.

### File Structure

```
/home/user/
├── public_html/       # Web root (public/ contents)
│   └── index.php
└── ava/               # Ava CMS installation (above web root)
    ├── app/
    ├── content/
    ├── core/
    └── storage/       # Must be writable
```

Only `public/` should be web-accessible.

### Local Development

```bash
php -S localhost:8000 -t public
```

### Shared Hosting Setup

1. Upload Ava CMS above web root
2. Move/symlink `public/` contents to `public_html/`
3. Update paths in `public/index.php`
4. Run `composer install`
5. Visit site (auto-builds index)

### VPS Setup

Configure Nginx/Apache to serve `public/` as document root.

**Nginx:**
```nginx
server {
    root /home/user/ava/public;
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        include fastcgi_params;
    }
}
```

### Deployment

```bash
# After uploading content changes
./ava rebuild
```

For CI/CD, automate: `git pull && composer install --no-dev && ./ava rebuild`

### Pre-Launch Checklist

- PHP 8.3+ with required extensions
- HTTPS enabled (required for admin)
- `./ava rebuild` run
- Debug mode disabled
- Webpage cache enabled
- Admin user created

## Updates

**Full docs:** https://ava.addy.zone/docs/updates

### CLI Commands

```bash
./ava update:check [--force]  # Check for updates
./ava update:apply [--yes]    # Apply update
./ava update:apply --dev      # Apply from latest main branch
```

Requires `ZipArchive` PHP extension.

### What Gets Updated

**Updated:** `core/`, `ava`, `bootstrap.php`, `composer.json`, `public/index.php`, `public/assets/admin.css`, bundled plugins (`sitemap`, `feed`, `redirects`)

**Preserved:** `content/`, `app/` (config, themes, plugins, snippets), `vendor/`, `storage/`

### After Updating

```bash
composer install
./ava rebuild
```

### Backup Before Updates

Essential folders:
- `content/` — All content
- `app/config/` — Settings
- `app/themes/` — Custom themes

## API

**Full docs:** https://ava.addy.zone/docs/api

Ava CMS provides building blocks for creating custom APIs rather than shipping a predefined API structure.

### Core Components

- `$app->router()` — Route registration
- `\Ava\Http\Request` — Query params, headers, body
- `\Ava\Http\Response` — JSON, redirects, headers
- `\Ava\Content\Repository` / `\Ava\Content\Query` — Content access

### Request Methods

```php
$request->method()              // GET, POST, etc.
$request->isMethod('POST')      // Check method
$request->path()                // URL path
$request->query('key', $default)// Query parameter
$request->header('X-Api-Key')   // Header (case-insensitive)
$request->body()                // Request body
$request->expectsJson()         // Accept: application/json
```

### Response Helpers

```php
Response::json($data, $status = 200)
Response::redirect($url, $status = 302)
Response::text($string, $status = 200)
Response::html($string, $status = 200)

// Add headers immutably
Response::json(['ok' => true])->withHeader('Cache-Control', 'no-store');
```

### Route Registration

```php
// Exact route with parameters
$router->addRoute('/api/content/{type}/{slug}', function($request, $params) {
    $type = $params['type'];
    $slug = $params['slug'];
    return Response::json(['type' => $type, 'slug' => $slug]);
});

// Prefix route (handles all paths under prefix)
$router->addPrefixRoute('/api/v2/', function($request, $params) {
    // Match all /api/v2/* paths
});
```

### Handler Signature

```php
function(\Ava\Http\Request $request, array $params): RouteMatch|Response|null
```

Return `Response` for API endpoints (sent directly). Return `RouteMatch` to trigger template rendering.

### JSON API Plugin Example

```php
// app/plugins/json-api/plugin.php
return [
    'name' => 'JSON API',
    'boot' => function($app) {
        $router = $app->router();
        
        $router->addRoute('/api/posts', function($request, $params) use ($app) {
            $posts = $app->query()
                ->type('post')
                ->published()
                ->orderBy('date', 'desc')
                ->page((int) $request->query('page', 1))
                ->perPage(10)
                ->get();
            
            return \Ava\Http\Response::json([
                'data' => array_map(fn($p) => [
                    'title' => $p->title(),
                    'slug' => $p->slug(),
                    'url' => $p->url(),
                ], $posts),
            ]);
        });
    }
];
```

### Authentication Pattern

```php
$authenticateRequest = function($request) use ($app): bool {
    $apiKey = $request->header('X-API-Key') ?? $request->query('api_key');
    $validKeys = $app->config('api.keys', []);
    return in_array($apiKey, $validKeys, true);
};

$router->addRoute('/api/private', function($request, $params) use ($authenticateRequest) {
    if (!$authenticateRequest($request)) {
        return Response::json(['error' => 'Unauthorized'], 401);
    }
    return Response::json(['ok' => true]);
});
```

Store keys in config:

```php
// app/config/ava.php
'api' => ['keys' => ['your-secret-api-key-here']],
```

### Search Endpoint

```php
$router->addRoute('/api/search', function($request, $params) use ($app) {
    $query = trim($request->query('q', ''));
    
    $results = $app->query()
        ->type('post')
        ->published()
        ->search($query)
        ->perPage(20)
        ->get();
    
    return Response::json([
        'results' => array_map(fn($item) => [
            'title' => $item->title(),
            'excerpt' => $item->excerpt(),
        ], $results),
    ]);
});
```

### CORS Headers

```php
$withCors = function (Response $response): Response {
    return $response->withHeaders([
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, X-API-Key',
    ]);
};

// Handle OPTIONS preflight
if ($request->isMethod('OPTIONS')) {
    return $withCors(new Response('', 204));
}
```

### Route Matching Order

1. `router.before_match` hook interception
2. Trailing slash redirects
3. `redirect_from` frontmatter redirects
4. System routes (`addRoute`)
5. Exact content routes
6. Preview matching (draft content)
7. Prefix routes (`addPrefixRoute`)
8. Taxonomy routes
9. 404
