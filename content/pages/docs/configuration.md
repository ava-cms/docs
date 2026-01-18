---
title: Configuration
slug: configuration
status: published
meta_title: Configuration | Flat-file PHP CMS | Ava CMS
meta_description: Configure Ava CMS using simple PHP arrays. Set up site identity, paths, caching, content types, taxonomies, and more with readable, commentable config files.
excerpt: All Ava CMS settings live in plain PHP files—readable, commentable, and powerful. Configure your site identity, paths, caching, content types, and taxonomies.
---

Ava CMS's configuration is simple and transparent. All settings live in `app/config/` as plain PHP files.

<details class="beginner-box">
<summary>Quick Start: The settings most people change first</summary>
<div class="beginner-box-content">

Open `app/config/ava.php` and update these:

```php
'site' => [
    'name'        => 'My Awesome Site',
    'base_url'    => 'https://example.com',    // Full URL, no trailing slash
    'timezone'    => 'Europe/London',          // php.net/timezones
    'locale'      => 'en_GB',                  // php.net/setlocale
    'date_format' => 'F j, Y',                 // php.net/datetime.format
],
```

See [Site Identity](#content-site-identity) below for details on each option, or the [PHP date format reference](https://www.php.net/manual/en/datetime.format.php) for date formatting.

</div>
</details>

## Why PHP Configs?

We use PHP arrays instead of YAML or JSON because:
1. **It's Readable:** You can add comments to explain *why* you changed a setting.
2. **It's Powerful:** You can use constants, logic, or helper functions right in your config.
3. **It's Standard:** No special parsers or hidden `.env` files to debug.

## The Config Files

| File | What it controls |
|------|------------------|
| [ava.php](#content-main-settings-avaphp) | Main site settings (name, URL, cache, themes, plugins, security). |
| [content_types.php](#content-content-types-content_typesphp) | Defines your content types (Pages, Posts, etc.). See also [Writing Content](/docs/content). |
| [taxonomies.php](#content-taxonomies-taxonomiesphp) | Defines how you group content (Categories, Tags). See also [Taxonomy Fields](/docs/content#content-taxonomy-fields). |
| `users.php` | Admin users (managed automatically by CLI). See [User Management](/docs/cli#content-user-management). |

## Main Settings (`ava.php`)

This is where you set up your site's identity and behavior.

### Site Identity

```php
return [
    'site' => [
        'name'        => 'My Awesome Site',
        'base_url'    => 'https://example.com',    // Full URL, no trailing slash
        'timezone'    => 'Europe/London',          // php.net/timezones
        'locale'      => 'en_GB',                  // php.net/setlocale
        'date_format' => 'F j, Y',                 // php.net/datetime.format
    ],
    // ...
];
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `site.name` | string | `'Ava CMS Site'` | Your site's display name. Used in templates, RSS feeds, sitemaps, and admin. |
| `site.base_url` | string | required | Full URL (**no trailing slash**). Used for sitemaps, canonical URLs, and absolute links. |
| `site.timezone` | string | `'UTC'` | Timezone for dates. Use a [PHP timezone identifier](https://www.php.net/manual/en/timezones.php). |
| `site.locale` | string | `'en_GB'` | Locale for date/number formatting. See [PHP locale codes](https://www.php.net/manual/en/function.setlocale.php). |
| `site.date_format` | string | `'F j, Y'` | Default format for `$ava->date()`. Uses [PHP date() format codes](https://www.php.net/manual/en/datetime.format.php). |

**In templates:** Access site info via `$site['name']`, `$site['url']`, and `$site['timezone']`. See [Theming - Template Variables](/docs/theming#content-template-variables).

### Paths

Directory locations for your content, themes, plugins, and other assets.

<div class="callout-warning">
<strong>Important:</strong> Most people should not change these paths from the defaults. Only change them if you have a specific reason. Aliases are safe to customize.
</div>

<pre><code class="language-php">'paths' =&gt; [
    'content'  =&gt; 'content',       // Where your Markdown files live
    'themes'   =&gt; 'app/themes',    // Where theme folders live
    'plugins'  =&gt; 'app/plugins',   // Where plugin folders live
    'snippets' =&gt; 'app/snippets',  // Snippets for &#91;snippet&#93; shortcode
    'storage'  =&gt; 'storage',       // Cache, logs, and temporary files

    'aliases' =&gt; [
        '@media‎:' =&gt; '/media/',
    ],
],
</code></pre>

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `paths.content` | string | `'content'` | Directory containing your Markdown content files. |
| `paths.themes` | string | `'app/themes'` | Directory containing theme folders. |
| `paths.plugins` | string | `'app/plugins'` | Directory containing plugin folders. |
| `paths.snippets` | string | `'app/snippets'` | Directory for PHP snippets. See [Shortcodes - Snippets](/docs/shortcodes#content-snippets-reusable-php-components). |
| `paths.storage` | string | `'storage'` | Directory for cache files, logs, and temporary data. |
| `paths.aliases` | array | `['@media‎:' => '/media/']` | Path aliases for use in content. See [Writing Content - Images and Media](/docs/content#content-images-and-media). |

All paths are relative to your project root unless they start with `/`.

### Theme

```php
'theme' => 'default',
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `theme` | string | `'default'` | The active theme folder name inside `app/themes/`. |

**See:** [Theming](/docs/theming) for theme structure, template variables, and development documentation.

### Content Index

The content index is a binary snapshot of your content metadata—used to avoid parsing Markdown files on every request.

```php
'content_index' => [
    'mode'           => 'auto',   // When to rebuild: auto, never, always
    'backend'        => 'array',  // Storage: array or sqlite
    'use_igbinary'   => true,     // Faster serialization if available
    'prerender_html' => false,    // Pre-render Markdown during rebuild
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `mode` | string | `'auto'` | `auto` = rebuild when files change, `never` = CLI only (production), `always` = every request (debug only). |
| `backend` | string | `'array'` | `array` = binary PHP arrays (default), `sqlite` = SQLite database (for 10k+ items). |
| `use_igbinary` | bool | `true` | Use igbinary extension for faster serialization if installed. |
| `prerender_html` | bool | `false` | Pre-render Markdown → HTML during rebuild to speed up uncached requests. |

**See:** [Performance - Content Indexing](/docs/performance#content-content-indexing) for detailed explanations of modes, backends, tiered caching, and benchmarks.

### Webpage Cache

The webpage cache stores fully-rendered HTML for near-instant serving.

```php
'webpage_cache' => [
    'enabled' => true,
    'ttl'     => null,       // Seconds, or null = until rebuild
    'exclude' => ['/api/*'], // URL patterns to never cache
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `enabled` | bool | `false` | Enable HTML webpage caching. Recommended `true` for production. |
| `ttl` | int\|null | `null` | Cache lifetime in seconds. `null` = cached until next rebuild. |
| `exclude` | array | `[]` | URL patterns to never cache. Supports glob wildcards (`*`). |

**See:** [Performance - Webpage Caching](/docs/performance#content-webpage-caching) for how it works, fast-path optimization, and cache management.

### Routing

```php
'routing' => [
    'trailing_slash' => false,
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `trailing_slash` | bool | `false` | URL style: `false` = `/about`, `true` = `/about/`. Mismatches trigger 301 redirects. |

**See:** [Routing](/docs/routing) for URL styles, custom routes, and taxonomy URLs.

### Content Parsing

Controls how Ava CMS processes your Markdown content files.

```php
'content' => [
    'frontmatter' => [
        'format' => 'yaml',
    ],
    'markdown' => [
        'allow_html'       => true,
        'heading_ids'      => true,
        'disallowed_tags'  => ['script', 'noscript'],
    ],
    'id' => [
        'type' => 'ulid',
    ],
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `frontmatter.format` | string | `'yaml'` | Frontmatter parser format. Currently only YAML is supported. |
| `markdown.allow_html` | bool | `true` | Allow raw HTML tags in Markdown content. |
| `markdown.heading_ids` | bool | `true` | Add `id` attributes to headings for deep linking. |
| `markdown.disallowed_tags` | array | `[]` | HTML tags to strip even when `allow_html` is true. |
| `id.type` | string | `'ulid'` | ID format for new content: `'ulid'` (recommended) or `'uuid7'`. |

**See:** [Writing Content](/docs/content) for frontmatter fields and Markdown syntax.

### Security

```php
'security' => [
    'shortcodes' => [
        'allow_php_snippets' => true,
    ],
    'preview_token' => null,
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `shortcodes.allow_php_snippets` | bool | `true` | Enable the `[snippet]` shortcode for including PHP files from `app/snippets/`. |
| `preview_token` | string\|null | `null` | Secret token for previewing draft content without logging in. |

**Preview token usage:** Access drafts via `https://example.com/path?preview=1&token=your-token`

**See:** [Shortcodes - Snippets](/docs/shortcodes#content-snippets-reusable-php-components) and [Routing - Preview Mode](/docs/routing#content-preview-mode).

### Admin Dashboard

The admin dashboard provides a web-based interface for managing your site.

```php
'admin' => [
    'enabled' => false,
    'path'    => '/admin',
    'theme'   => 'cyan',

    'media' => [
        'enabled'          => true,
        'path'             => 'public/media',
        'organize_by_date' => true,
        'max_file_size'    => 10 * 1024 * 1024,   // 10 MB
        'allowed_types'    => [
            'image/jpeg', 'image/png', 'image/gif',
            'image/webp', 'image/svg+xml', 'image/avif',
        ],
    ],
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `enabled` | bool | `false` | Enable the admin dashboard. |
| `path` | string | `'/admin'` | URL path for the admin area. Change to obscure admin location. |
| `theme` | string | `'cyan'` | Color theme: `cyan`, `pink`, `purple`, `green`, `blue`, or `amber`. |
| `media.enabled` | bool | `true` | Enable the media upload feature. |
| `media.path` | string | `'public/media'` | Upload directory (relative to project root). |
| `media.organize_by_date` | bool | `true` | Create `/year/month/` subfolders for uploads. |
| `media.max_file_size` | int | `10485760` | Maximum file size in bytes (10 MB default). |
| `media.allowed_types` | array | See code | Array of allowed MIME types for uploads. |

<div class="callout-warning">
<strong>Important:</strong> Create admin users with <code>./ava user:add</code> before enabling the admin dashboard.
</div>

**See:** [Admin Dashboard](/docs/admin) for features, security, and user management.

### Debug Mode

Control error visibility and logging for development and troubleshooting.

```php
'debug' => [
    'enabled'        => false,
    'display_errors' => false,
    'log_errors'     => true,
    'level'          => 'errors',
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `enabled` | bool | `false` | Master switch for debug features. |
| `display_errors` | bool | `false` | Show PHP errors in browser output. **Never enable in production!** |
| `log_errors` | bool | `true` | Write errors to `storage/logs/error.log`. |
| `level` | string | `'errors'` | Error reporting: `all` (dev), `errors` (prod), or `none`. |

<div class="callout-warning">
<strong>Security Warning:</strong> Never enable <code>display_errors</code> in production—it can expose sensitive information.
</div>

### Logs

Control log file size and automatic rotation.

```php
'logs' => [
    'max_size'  => 10 * 1024 * 1024,   // 10 MB
    'max_files' => 3,
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `max_size` | int | `10485760` | Maximum log file size in bytes before rotation (10 MB). |
| `max_files` | int | `3` | Number of rotated log files to keep. |

**See:** [CLI - Logs](/docs/cli#content-logs) for log viewing and management commands.

### CLI Appearance

```php
'cli' => [
    'theme' => 'cyan',
],
```

| Theme | Description |
|-------|-------------|
| `cyan` | Cool cyan/aqua (default) |
| `pink`, `purple`, `green`, `blue`, `amber` | Alternative colors |
| `disabled` | No colors (for CI/CD or non-ANSI terminals) |

### Plugins

```php
'plugins' => [
    'sitemap',
    'feed',
    'redirects',
],
```

Array of plugin folder names to activate. Plugins load in the order listed.

**See:** [Bundled Plugins](/docs/bundled-plugins) for sitemap, RSS feed, and redirects configuration.

### Custom Settings

Add your own site-specific configuration. Access values in templates with `$ava->config()`:

```php
// In ava.php
'analytics' => [
    'tracking_id' => 'G-XXXXXXXXXX',
    'enabled'     => true,
],
```

```php
// In templates
<?php if ($ava->config('analytics.enabled')): ?>
    <!-- Analytics code for <?= $ava->config('analytics.tracking_id') ?> -->
<?php endif; ?>
```

## Content Types: `content_types.php`

Define what kinds of content your site has. Each content type specifies where files live, how URLs are generated, and which templates to use.

```php
<?php

return [
    'page' => [
        'label'       => 'Pages',
        'content_dir' => 'pages',
        'url' => [
            'type' => 'hierarchical',
            'base' => '/',
        ],
        'templates' => [
            'single' => 'page.php',
        ],
        'taxonomies' => [],
        'fields'     => [],
        'sorting'    => 'manual',
    ],

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
        'taxonomies' => ['category', 'tag'],
        'sorting'    => 'date_desc',
    ],
];
```

### Content Type Options

| Option | Type | Required | Description |
|--------|------|----------|-------------|
| `label` | string | Yes | Human-readable name shown in admin UI. |
| `content_dir` | string | Yes | Folder inside `content/` where files for this type live. |
| `url` | array | Yes | URL generation settings. See [Routing - URL Styles](/docs/routing#content-url-styles). |
| `templates` | array | Yes | Template file mappings (`single`, `archive`). |
| `taxonomies` | array | No | Which taxonomies apply to this type. Default: `[]` |
| `fields` | array | No | Custom field definitions for validation and admin UI. See [Fields](/docs/fields). |
| `sorting` | string | No | Default sort: `date_desc`, `date_asc`, `title`, or `manual`. |
| `cache_fields` | array | No | Extra frontmatter fields to include in archive cache for fast access. |
| `search` | array | No | Search configuration (enabled, fields, weights). |

### Example with Custom Fields

Here's a more complete example showing custom fields for a blog post type:

```php
'post' => [
    'label'       => 'Blog Posts',
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
    'taxonomies' => ['category', 'tag'],
    'sorting'    => 'date_desc',
    
    // Custom fields for validation and admin UI
    'fields' => [
        'author' => [
            'type'     => 'text',
            'label'    => 'Author Name',
            'required' => true,
        ],
        'featured_image' => [
            'type'  => 'text',
            'label' => 'Featured Image URL',
        ],
        'reading_time' => [
            'type'    => 'number',
            'label'   => 'Reading Time (minutes)',
            'min'     => 1,
            'max'     => 60,
            'default' => 5,
        ],
        'featured' => [
            'type'    => 'boolean',
            'label'   => 'Featured Post',
            'default' => false,
        ],
    ],
    
    // Include these fields in archive cache for fast listing access
    'cache_fields' => ['author', 'featured_image', 'reading_time', 'featured'],
],
```

**See:** [Fields](/docs/fields) for all available field types (text, number, boolean, select, date, etc.) and validation options.

## Taxonomies: `taxonomies.php`

Taxonomies organize content into groups (categories, tags, authors, etc.).

```php
<?php

return [
    'category' => [
        'label'        => 'Categories',
        'hierarchical' => true,
        'public'       => true,

        'rewrite' => [
            'base'      => '/category',
            'separator' => '/',
        ],

        'behaviour' => [
            'allow_unknown_terms' => true,
            'hierarchy_rollup'    => true,
        ],

        'ui' => [
            'show_counts' => true,
            'sort_terms'  => 'name_asc',
        ],
    ],

    'tag' => [
        'label'        => 'Tags',
        'hierarchical' => false,
        'public'       => true,

        'rewrite' => [
            'base' => '/tag',
        ],

        'behaviour' => [
            'allow_unknown_terms' => true,
        ],

        'ui' => [
            'show_counts' => true,
            'sort_terms'  => 'count_desc',
        ],
    ],
];
```

### Taxonomy Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `label` | string | Required | Human-readable name for the taxonomy. |
| `hierarchical` | bool | `false` | Support parent/child term relationships. |
| `public` | bool | `true` | Create public archive pages for terms. |
| `rewrite.base` | string | `'/{taxonomy}'` | URL prefix for term archives. |
| `rewrite.separator` | string | `'/'` | Separator for hierarchical term paths. |
| `behaviour.allow_unknown_terms` | bool | `true` | Auto-create terms when used in content. |
| `behaviour.hierarchy_rollup` | bool | `true` | Include child terms when filtering by parent. |
| `ui.show_counts` | bool | `true` | Display content count next to terms. |
| `ui.sort_terms` | string | `'name_asc'` | Default sort: `name_asc`, `name_desc`, `count_asc`, `count_desc`. |

**Using taxonomies in content:**

```yaml
---
title: My PHP Tutorial
category: Tutorials
tag:
  - php
  - beginner
---
```

**See:** [Writing Content - Taxonomy Fields](/docs/content#content-taxonomy-fields) for usage examples and [Routing - Taxonomy Routes](/docs/routing#content-taxonomy-routes) for URL generation.

## Environment-Specific Config

Use PHP logic to override settings per environment:

```php
// app/config/ava.php

$config = [
    'site' => [
        'name'     => 'My Site',
        'base_url' => 'https://example.com',
    ],
    'content_index' => ['mode' => 'never'],
    'debug' => ['enabled' => false],
];

// Development overrides
if (getenv('APP_ENV') === 'development') {
    $config['site']['base_url'] = 'http://localhost:8000';
    $config['content_index']['mode'] = 'auto';
    $config['admin']['enabled'] = true;
    $config['debug'] = [
        'enabled'        => true,
        'display_errors' => true,
        'level'          => 'all',
    ];
}

return $config;
```

Set the environment variable in your server config:

```bash
export APP_ENV=development
```
---

<div class="callout-warning">
Ava CMS is provided as <a href="https://github.com/avacms/ava/blob/main/LICENSE">free, open-source software without warranty</a>. You are responsible for reviewing, testing, and securing any deployment.
</div>