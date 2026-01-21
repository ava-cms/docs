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

Ava CMS is a flat-file PHP CMS (PHP 8.3+) requiring no database. Content is Markdown with YAML frontmatter. Configuration is PHP arrays. No build step—edit, refresh, done.

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

**Requirements:** PHP 8.3+, extensions: `mbstring`, `json`, `ctype`. Optional: `pdo_sqlite`, `igbinary`, `opcache`, `imagick`/`gd` (media uploads).


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
    'path'    => '/ava-admin',
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
        'fields'       => [/* See Fields section */],
        'search' => [
            'enabled' => true,
            'fields'  => ['title', 'excerpt', 'body'],
            'weights' => [
                'title_phrase'   => 80,
                'title_token'    => 10,
                'excerpt_phrase' => 30,
                'body_phrase'    => 20,
                'body_token'     => 2,
                'featured'       => 15,
            ],
        ],
    ],
];
```

**Content Type Options:**

| Option | Required | Description |
|--------|----------|-------------|
| `label` | Yes | Human-readable name shown in admin UI |
| `content_dir` | Yes | Folder inside `content/` for this type |
| `url` | Yes | URL generation settings (see URL Types) |
| `templates` | Yes | Template file mappings (`single`, `archive`) |
| `taxonomies` | No | Which taxonomies apply. Default: `[]` |
| `fields` | No | Custom field definitions (see Fields section) |
| `sorting` | No | Default sort: `date_desc`, `date_asc`, `title`, `manual` |
| `cache_fields` | No | Extra frontmatter fields for archive cache |
| `search` | No | Search config (enabled, fields, weights) |

**URL Types:**
- `hierarchical` — URLs mirror file paths. `content/pages/about/team.md` → `/about/team`
- `pattern` — Template-based. Placeholders: `{slug}`, `{id}`, `{yyyy}`, `{mm}`, `{dd}`

### Taxonomies (`taxonomies.php`)

```php
return [
    'category' => [
        'label'        => 'Categories',
        'hierarchical' => true,
        'public'       => true,
        'rewrite'      => ['base' => '/category'],
        'behaviour'    => ['allow_unknown_terms' => true, 'hierarchy_rollup' => true],
        'ui'           => ['show_counts' => true, 'sort_terms' => 'name_asc'],
    ],
];
```

| Option | Default | Description |
|--------|---------|-------------|
| `label` | Required | Human-readable name |
| `hierarchical` | `false` | Support parent/child relationships |
| `public` | `true` | Create public archive pages |
| `rewrite.base` | `'/{taxonomy}'` | URL prefix for archives |
| `behaviour.allow_unknown_terms` | `true` | Auto-create terms when used |
| `ui.sort_terms` | `'name_asc'` | Sort: `name_asc`, `name_desc`, `count_asc`, `count_desc` |

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

**Custom Fields:** Any field is accessible via `$item->get('field_name')`. See Fields section below.

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

**Validation:** `./ava lint` — Checks YAML, required fields, duplicate slugs/IDs, custom field constraints

## Fields

**Full docs:** https://ava.addy.zone/docs/fields

Define typed fields in `content_types.php` for admin UI rendering, CLI linting, and validation.

### Defining Fields

```php
'post' => [
    'fields' => [
        'author' => [
            'type' => 'text',
            'label' => 'Author Name',
            'required' => true,
        ],
        'featured' => [
            'type' => 'checkbox',
            'label' => 'Featured Post',
        ],
        'publish_date' => [
            'type' => 'date',
            'label' => 'Publish Date',
            'includeTime' => true,
        ],
    ],
],
```

### Common Field Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `type` | string | required | Field type identifier |
| `label` | string | from field name | Human-readable label |
| `description` | string | — | Help text below field |
| `required` | bool | `false` | Whether value is required |
| `default` | mixed | varies | Default value for new content |
| `placeholder` | string | — | Placeholder text |
| `group` | string | — | Group name for organizing fields |

### Field Types

| Type | Description | Key Options |
|------|-------------|-------------|
| `text` | Single-line text | `minLength`, `maxLength`, `pattern` |
| `textarea` | Multi-line text | `minLength`, `maxLength`, `rows` |
| `number` | Integer or decimal | `min`, `max`, `step`, `numberType` (`int`/`float`) |
| `checkbox` | Boolean toggle | `checkboxLabel` |
| `select` | Dropdown | `options`, `multiple`, `emptyOption` |
| `date` | Date/datetime picker | `includeTime`, `min`, `max` |
| `color` | Color picker | `format` (`hex`/`rgb`/`rgba`/`hsl`), `alpha` |
| `image` | Image picker | `allowedTypes`, `basePath`, `showPreview` |
| `file` | File picker | `accept`, `basePath` |
| `gallery` | Multiple images | `allowedTypes`, `minItems`, `maxItems` |
| `array` | Dynamic list/key-value | `associative`, `minItems`, `maxItems` |
| `content` | Reference to content | `contentType`, `multiple`, `valueField` |
| `taxonomy` | Taxonomy term selector | `taxonomy`, `multiple`, `allowNew` |
| `status` | Status toggle | — |
| `template` | Template selector | `defaultTemplate` |

### Field Storage in Frontmatter

```yaml
---
title: My Post
author: Jane Smith           # text
featured: true               # checkbox
publish_date: 2025-01-15     # date
accent_color: "#3b82f6"      # color
ingredients:                 # array (simple list)
  - "500g flour"
  - "350g water"
metadata:                    # array (associative)
  author: "John"
  version: "1.0"
photos:                      # gallery
  - "@media:photo1.jpg"
  - "@media:photo2.jpg"
related_posts:               # content (multiple)
  - pizza-recipe
  - bread-guide
---
```

### Field Groups

Organize fields into collapsible panels:

```php
'fields' => [
    'author' => ['type' => 'text', 'group' => 'Meta'],
    'publish_date' => ['type' => 'date', 'group' => 'Meta'],
    'featured_image' => ['type' => 'image', 'group' => 'Media'],
],
```

### Accessing Fields in Templates

```php
$content->get('author')          // Get field value
$content->get('author', 'Anon')  // With default
$content->has('featured_image')  // Check if exists
$content->terms('category')      // Taxonomy terms
```

## CLI

**Full docs:** https://ava.addy.zone/docs/cli

Run from project root: `./ava <command> [options]`

| Command | Description |
|---------|-------------|
| `status` | Site overview and health check |
| `rebuild [--keep-webcache]` | Rebuild content index |
| `lint` | Validate all content files |
| `make <type> "Title"` | Create new content with scaffolding |
| `user:add <email> <pass> [name]` | Create admin user |
| `user:password <email> <pass>` | Update password |
| `user:remove <email>` | Remove user |
| `user:list` | List all users |
| `cache:stats` | Webpage cache statistics |
| `cache:clear [pattern]` | Clear cached pages |
| `logs:stats` | Log file statistics |
| `logs:tail [name] [-n N]` | Show last N lines of log |
| `update:check [--force]` | Check for updates |
| `update:apply [-y] [--dev]` | Apply update |
| `update:stale` | Detect stale files from older releases |

**Plugin Commands:** `sitemap:stats`, `feed:stats`, `redirects:list`, `redirects:add <from> <to> [code]`, `redirects:remove <from>`
| `stress:clean <type>` | Remove test content |

### Plugin Commands

## Admin Dashboard

**Full docs:** https://ava.addy.zone/docs/admin

Optional web-based admin for content management. Files remain the source of truth.

**Enabling:**
```php
'admin' => ['enabled' => true, 'path' => '/ava-admin', 'theme' => 'cyan'],
```

Create users via CLI: `./ava user:add email@example.com password "Name"`

**Features:** Content editor with custom field support, media library, taxonomy management, content linting, cache/index management, system info, admin logs.

**Content Safety:** Blocks high-risk HTML (`<script>`, `<iframe>`, `on*=` handlers). Edit files directly for advanced HTML.

**Security:** HTTPS required (production), bcrypt passwords, rate limiting (5 failures → 15min lockout), HttpOnly/SameSite cookies, CSRF protection.

**Media Config:**
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
$content->isPublished()     // Status check helpers
$content->date()            // DateTimeImmutable|null
$content->updated()         // DateTimeImmutable|null (falls back to date)
$content->excerpt()         // Excerpt string
$content->rawContent()      // Raw Markdown body
$content->terms('category') // Taxonomy terms array
$content->get('field')      // Custom frontmatter field
$content->get('field', 'default') // With default
$content->has('field')      // Check field exists
$content->metaTitle()       // SEO title
$content->metaDescription() // SEO description
$content->noindex()         // Should search engines skip?
$content->frontmatter()     // All frontmatter as array
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

```php
<?php
return [
    'name' => 'My Plugin',
    'version' => '1.0.0',
    'boot' => function($app) {
        // Routes, hooks, etc.
    },
    'commands' => [
        ['name' => 'myplugin:task', 'description' => 'Do something', 
         'handler' => function($args, $cli, $app) { return 0; }],
    ],
];
```

Enable in `ava.php`: `'plugins' => ['sitemap', 'feed', 'my-plugin']`

### Hooks

**Filters** — Modify and return data:
```php
use Ava\Plugins\Hooks;
Hooks::addFilter('render.context', fn($ctx) => [...$ctx, 'custom' => 'value'], priority: 10);
```

**Actions** — React to events:
```php
Hooks::addAction('indexer.rebuild', function($app) { /* sync content */ });
```

| Hook | Type | Description |
|------|------|-------------|
| `router.before_match` | Filter | Intercept routing |
| `content.loaded` | Filter | Modify loaded content item |
| `render.context` | Filter | Add template variables |
| `render.output` | Filter | Modify final HTML |
| `markdown.configure` | Action | Configure CommonMark |
| `admin.register_pages` | Filter | Add admin pages |
| `indexer.rebuild` | Action | After content index rebuild |
| `cli.rebuild` | Action | After CLI rebuild command |

### Admin Pages

```php
Hooks::addFilter('admin.register_pages', function($pages) {
    $pages['my-plugin'] = [
        'label' => 'My Plugin', 'icon' => 'extension',
        'handler' => function($request, $app, $controller) {
            return $controller->renderPluginPage(['title' => 'My Plugin', 'activePage' => 'my-plugin'], '<div>...</div>');
        },
    ];
    return $pages;
});
```

## Bundled Plugins

**Full docs:** https://ava.addy.zone/docs/bundled-plugins

### Sitemap

Generates `/sitemap.xml` for search engines.

```php
'sitemap' => [
    'enabled' => true,
],
```

Exclude pages with `noindex: true` in frontmatter. CLI: `./ava sitemap:stats`

### RSS Feed

Generates `/feed.xml` for RSS readers.

```php
'feed' => ['enabled' => true, 'items_per_feed' => 20, 'full_content' => false, 'types' => null],
```

Add to theme: `<link rel="alternate" type="application/rss+xml" href="/feed.xml">`

### Redirects

Manage URL redirects via admin or CLI. Stored in `storage/redirects.json`.

**Status codes:** 301, 302, 307, 308 (redirects), 410, 451, 503 (status-only)

**Alternative:** Use `redirect_from:` in content frontmatter for content-based redirects.

## Performance

**Full docs:** https://ava.addy.zone/docs/performance

Two-layer system: Content Indexing + Webpage Caching.

### Content Index

Pre-built binary index of content metadata. Avoids parsing Markdown on every request.

**Cache files:** `storage/cache/` — `recent_cache.bin`, `slug_lookup.bin`, `content_index.bin`, `tax_index.bin`, `routes.bin`

| Tier | Cache | Use Case | Speed |
|------|-------|----------|-------|
| 1 | Recent | Homepage, RSS | ~0.2ms |
| 2 | Slug Lookup | Single item | ~1-15ms |
| 3 | Full Index | Search, pagination | ~15-300ms |

**Backends:** `array` (default, fastest) or `sqlite` (10k+ items, lower memory)

| Mode | Behavior |
|------|----------|
| `auto` | Rebuild on file changes (development) |
| `never` | Only via `./ava rebuild` (production) |
| `always` | Every request (debugging only) |

### Webpage Cache

Stores fully-rendered HTML.

```php
'webpage_cache' => ['enabled' => true, 'ttl' => null, 'exclude' => ['/api/*']],
```

**Speed:** Uncached ~5ms → Cached ~0.02ms (250× faster)

**Per-page control:** `cache: false` in frontmatter

**Not cached:** Admin pages, POST requests, query strings, logged-in admins

**Invalidation:** `./ava rebuild` clears both caches (use `--keep-webcache` to preserve webpage cache)

**Recommendations:** Development: `mode: auto`, cache disabled. Production: `mode: never`, cache enabled, `./ava rebuild` after deploys.

## Hosting

**Full docs:** https://ava.addy.zone/docs/hosting

**Requirements:** PHP 8.3+, Composer, SSH access recommended.

**Structure:** Only `public/` should be web-accessible. Keep `app/`, `content/`, `core/`, `storage/` above web root.

**Local Development:** `php -S localhost:8000 -t public`

**Nginx:**
```nginx
server {
    root /path/to/public;
    location / { try_files $uri $uri/ /index.php?$query_string; }
    location ~ \.php$ { fastcgi_pass unix:/var/run/php/php8.3-fpm.sock; include fastcgi_params; }
}
```

**Deployment:** `git pull && composer install --no-dev && ./ava rebuild`

**Pre-Launch:** PHP 8.3+, HTTPS enabled, `./ava rebuild` run, debug disabled, cache enabled.

## Updates

**Full docs:** https://ava.addy.zone/docs/updating

```bash
./ava update:check    # Check for updates
./ava update:apply    # Apply update (requires ZipArchive)
```

**Updated:** `core/`, `ava`, `bootstrap.php`, bundled plugins. **Preserved:** `content/`, `app/`, `vendor/`, `storage/`

**After updating:** `composer install && ./ava rebuild`

## API

**Full docs:** https://ava.addy.zone/docs/api

Building blocks for custom APIs (no predefined structure).

### Request/Response

```php
$request->method()                    // GET, POST, etc.
$request->query('key', $default)      // Query parameter
$request->header('X-Api-Key')         // Header
$request->body()                      // Request body

Response::json($data, 200)            // JSON response
Response::redirect($url, 302)         // Redirect
Response::json(['ok' => true])->withHeader('Cache-Control', 'no-store')
```

### JSON API Example

```php
$router->addRoute('/api/posts', function($request, $params) use ($app) {
    $posts = $app->query()->type('post')->published()
        ->orderBy('date', 'desc')->perPage(10)->get();
    return \Ava\Http\Response::json([
        'data' => array_map(fn($p) => ['title' => $p->title(), 'slug' => $p->slug()], $posts),
    ]);
});
```

### Authentication Pattern

```php
$apiKey = $request->header('X-API-Key') ?? $request->query('api_key');
$validKeys = $app->config('api.keys', []);
if (!in_array($apiKey, $validKeys, true)) {
    return Response::json(['error' => 'Unauthorized'], 401);
}
```

Store keys: `'api' => ['keys' => ['your-secret-key']]` in `ava.php`

---

## License

Ava CMS is released under the [GNU General Public License v3.0](https://github.com/avacms/ava/blob/main/LICENSE) (GPL-3.0). This means you are free to use, modify, and distribute it, but any derivative works must also be released under the GPL. When using AI assistants to generate code for Ava CMS projects, please ensure the generated code respects the GPL license terms.

---

<div class="callout-warning">
Ava CMS is provided as <a href="https://github.com/avacms/ava/blob/main/LICENSE">free, open-source software without warranty</a>. You are responsible for reviewing, testing, and securing any deployment.
</div>
