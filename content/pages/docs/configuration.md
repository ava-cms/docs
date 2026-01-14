---
title: Configuration
slug: configuration
status: published
meta_title: Configuration | Flat-file PHP CMS | Ava CMS
meta_description: Configure Ava CMS using simple PHP arrays. Set up site identity, paths, caching, content types, taxonomies, and more with readable, commentable config files.
excerpt: All Ava settings live in plain PHP files—readable, commentable, and powerful. Configure your site identity, paths, caching, content types, and taxonomies.
---

Ava's configuration is simple and transparent. All settings live in `app/config/` as plain PHP files.

## Why PHP Configs?

We use PHP arrays instead of YAML or JSON because:
1. **It's Readable:** You can add comments to explain *why* you changed a setting.
2. **It's Powerful:** You can use constants, logic, or helper functions right in your config.
3. **It's Standard:** No special parsers or hidden `.env` files to debug.

## The Config Files

| File | What it controls |
|------|------------------|
| [ava.php](#main-settings-avaphp) | Main site settings (name, URL, cache, themes, plugins, security). |
| [content_types.php](#content-types-content_typesphp) | Defines your content types (Pages, Posts, etc.). See also [Content](/docs/content). |
| [taxonomies.php](#taxonomies-taxonomiesphp) | Defines how you group content (Categories, Tags). See also [Taxonomy Fields](/docs/content#taxonomy-fields). |
| `users.php` | Admin users (managed automatically by CLI). See [User Management](/docs/cli#user-management). |

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
| `site.name` | string | `'Ava Site'` | Your site's display name. Used in templates via `$site['name']`, RSS feeds, sitemaps, and the admin dashboard. |
| `site.base_url` | string | required | Full URL where your site lives (**no trailing slash**). Used for sitemaps, canonical URLs, RSS feeds, and absolute links. Example: `'https://example.com'` |
| `site.timezone` | string | `'UTC'` | Timezone for dates. Use a [PHP timezone identifier](https://www.php.net/manual/en/timezones.php) like `'America/New_York'`, `'Europe/London'`, or `'Asia/Tokyo'`. |
| `site.locale` | string | `'en_GB'` | Locale for date/number formatting. See [PHP locale codes](https://www.php.net/manual/en/function.setlocale.php#refsect1-function-setlocale-notes). |
| `site.date_format` | string | `'F j, Y'` | Default format for `$ava->date()`. Uses [PHP date() format codes](https://www.php.net/manual/en/datetime.format.php). |

<div class="callout-info">
<strong>In templates:</strong> Access site info via <code>$site['name']</code>, <code>$site['url']</code>, and <code>$site['timezone']</code>.
</div>

### Paths

Directory locations for your content, themes, plugins, and other assets.

<pre><code class="language-php">'paths' =&gt; [
    'content'  =&gt; 'content',       // Where your Markdown files live
    'themes'   =&gt; 'app/themes',    // Where theme folders live
    'plugins'  =&gt; 'app/plugins',   // Where plugin folders live
    'snippets' =&gt; 'app/snippets',  // Where PHP snippets for &#91;snippet&#93; shortcode live
    'storage'  =&gt; 'storage',       // Cache, logs, and temporary files

    'aliases' =&gt; [
        '@media<span></span>:' =&gt; '/media/',
        // '@cdn<span></span>:' =&gt; 'https://cdn.example.com/',
    ],
],
</code></pre>

<div class="callout-warning">
<strong>Important:</strong> Most people should not change these paths from the defaults. Only change them if you have a specific reason (e.g., custom project structure). Ccustom directories disable automated core updates. Aliases are safe to customise.
</div>

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `paths.content` | string | `'content'` | Directory containing your Markdown content files. |
| `paths.themes` | string | `'app/themes'` | Directory containing theme folders. |
| `paths.plugins` | string | `'app/plugins'` | Directory containing plugin folders. |
| `paths.snippets` | string | `'app/snippets'` | Directory containing PHP snippets for the <code>&#91;snippet&#93;</code> shortcode. |
| `paths.storage` | string | `'storage'` | Directory for cache files, logs, and temporary data. |
| `paths.aliases` | array | <code>['@media<span></span>:' =&gt; '/media/']</code> | Path aliases for use in content. See below. |

All paths are relative to your project root unless they start with `/`.

#### Path Aliases

Path aliases let you reference files without hard-coding URLs. Define them in `paths.aliases`:

<pre><code class="language-php">'paths' =&gt; [
    'aliases' =&gt; [
        '@media<span></span>:' =&gt; '/media/',
        '@cdn<span></span>:'   =&gt; 'https://cdn.example.com/',
    ],
],
</code></pre>

Then use them in your content Markdown:

<pre><code class="language-markdown">![Photo](@media<span></span>:images/photo.jpg)
[Download](@cdn<span></span>:files/guide.pdf)
</code></pre>

At render time, <code>@media<span></span>:</code> expands to <code>/media/</code> and <code>@cdn<span></span>:</code> expands to <code>https://cdn.example.com/</code>. This makes it easy to:

- Reorganize assets without updating every content file
- Switch to a CDN later by changing one line
- Use short, memorable paths for common asset locations

<div class="callout-info">
<strong>See:</strong> <a href="/docs/content#path-aliases">Writing Content - Path Aliases</a> for more usage examples.
</div>


### Theme

```php
'theme' => 'default',
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `theme` | string | `'default'` | The active theme folder name inside `app/themes/`. |

The theme folder must contain a `templates/` directory with at least one template file. Optionally, it can include:

- `theme.php` — Bootstrap file that runs when the theme loads (register shortcodes, hooks, etc.)
- `partials/` — Reusable template fragments
- `assets/` — CSS, JS, images (served at `/theme/...`)

**See:** [Themes](/docs/themes) for detailed theme development documentation.

### Content Index

The content index is a binary snapshot of all your content metadata—used to avoid parsing Markdown files on every request. This is the core of Ava's performance.

```php
'content_index' => [
    'mode'         => 'auto',      // When to rebuild
    'backend'      => 'array',     // Storage format
    'use_igbinary' => true,        // Use faster serialization if available
    'prerender_html' => false,     // Optional: pre-render Markdown → HTML during rebuild
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `mode` | string | `'auto'` | When to rebuild the index. See modes below. |
| `backend` | string | `'array'` | Storage backend for the index. See backends below. |
| `use_igbinary` | bool | `true` | Use igbinary extension for faster serialization if installed. |
| `prerender_html` | bool | `false` | Pre-render Markdown → HTML during rebuild (writes `html_cache.bin`). |

#### Rebuild Modes

| Mode | Behavior | Best For |
|------|----------|----------|
| `auto` | Rebuilds automatically when content files change. Checks file timestamps and counts on each request. | **Development** — see changes immediately without manual rebuilds. |
| `never` | Only rebuilds via [`./ava rebuild`](/docs/cli#rebuild) command. | **Production** — fastest possible response times, no filesystem checks. |
| `always` | Rebuilds the entire index on every single request. | **Debugging only** — extremely slow, never use in production. |

#### Storage Backends

| Backend | Description | When to Use |
|---------|-------------|-------------|
| `array` | Binary serialized PHP arrays stored in `.bin` files. **This is the default.** | Most sites. Works everywhere, no dependencies. |
| `sqlite` | SQLite database file. Requires `pdo_sqlite` PHP extension. | Large sites with 10,000+ items, or servers with limited memory. |

The `array` backend automatically uses the best available serialization method:

- **igbinary** (when available): ~5× faster serialization, ~9× smaller cache files
- **PHP serialize** (fallback): Used when igbinary extension isn't installed

You can control this with `use_igbinary`:

```php
'content_index' => [
    'backend'      => 'array',
    'use_igbinary' => true,   // Use igbinary if available (default)
    'use_igbinary' => false,  // Always use PHP serialize
],
```

Ava detects which format each cache file uses via prefix markers (`IG:` or `SZ:`), so you can switch between them safely without rebuilding.

#### Pre-rendered HTML (Optional)

If `content_index.prerender_html` is enabled, Ava generates an additional cache file during `./ava rebuild`:

- **File:** `storage/cache/html_cache.bin`
- **Purpose:** Stores Markdown → HTML output for published items so uncached requests can skip Markdown conversion work.
- **When disabled:** Any existing `html_cache.bin` is deleted during rebuild.

What’s included at rebuild time:

- Markdown conversion (League\CommonMark), using the same `markdown.configure` hook used during rendering
- Path alias expansion using `paths.aliases` (simple string replacement)

What’s deferred to runtime:

- Shortcodes (still processed on each request)

Cache keys are stored as `"<type>:<contentKey>"` (for example: `page:`, `page:about`, `post:hello-world`).

<details class="beginner-box">
<summary>Which backend should I use?</summary>
<div class="beginner-box-content">

Stick with `array` — it works great for 99% of sites. Only switch to `sqlite` if you have:
- 10,000+ content items
- Memory issues on a constrained server
- Complex queries that benefit from SQL indexing

See [Performance](/docs/performance) for detailed benchmarks.

</div>
</details>

### Webpage Cache

The webpage cache stores fully-rendered HTML for near-instant serving. This applies to all public URLs—pages, posts, archive listings, taxonomy pages, and any custom content types.

```php
'webpage_cache' => [
    'enabled' => true,
    'ttl'     => null,               // Seconds, or null = until rebuild
    'exclude' => [                   // URL patterns to never cache
        '/api/*',
        '/preview/*',
    ],
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `enabled` | bool | `true` | Enable HTML webpage caching. |
| `ttl` | int\|null | `null` | Cache lifetime in seconds. `null` means pages are cached until the next rebuild. |
| `exclude` | array | `[]` | URL patterns to never cache. Supports glob-style wildcards (`*`). |

#### How It Works

1. **First visit:** Page is rendered and saved to `storage/cache/pages/`
2. **Subsequent visits:** Cached HTML served directly (~0.1ms vs ~30ms)
3. **On `./ava rebuild`:** All cached pages are automatically cleared
4. **On content change** (with `content_index.mode = 'auto'`): Cache is cleared
5. **Logged-in admin users:** Always bypass the cache (see fresh content)
6. **Requests with query parameters:** Not cached (except UTM tracking params)

#### Fast Path (TTFB Optimisation)

On some requests, Ava can serve the cached HTML **before the application boots** (skipping plugin/theme loading and index freshness checks). This “fast path” triggers only when **all** of the following are true:

- `webpage_cache.enabled` is `true`
- Request method is `GET`
- Request path is **not** under the admin path (`admin.path`, default `/admin`)
- The URL has **no query params**, except UTM params: `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`
- No `ava_admin` cookie is present
- The URL does not match any `webpage_cache.exclude` patterns
- The cache file exists, and (if `webpage_cache.ttl` is set) the file is not expired

On a fast-path **HIT**, app boot is skipped and the cached response includes:

- `X-Page-Cache: HIT`
- `X-Cache-Age: <seconds>`

#### Exclusion Patterns

The `exclude` array supports glob-style patterns:

```php
'exclude' => [
    '/api/*',        // Exclude all API routes
    '/preview/*',    // Exclude preview routes
    '/search',       // Exclude search page
    '/cart/*',       // Exclude shopping cart
],
```

#### Per-Page Cache Override

You can disable caching for specific pages using the `cache` frontmatter field:

```yaml
---
title: My Dynamic Page
cache: false
---
```

Or force caching even if it would normally be skipped:

```yaml
---
title: Force Cached Page
cache: true
---
```

#### CLI Commands

```bash
./ava cache:stats              # View cache statistics
./ava cache:clear              # Clear all cached webpages
./ava cache:clear /blog/*      # Clear pages matching pattern
```

**See:** [Performance](/docs/performance) for detailed caching strategies and benchmarks.

### Routing

```php
'routing' => [
    'trailing_slash' => false,
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `trailing_slash` | bool | `false` | URL style preference. `false` = `/about`, `true` = `/about/`. |

When a request arrives with the "wrong" format, Ava issues a **301 redirect** to the canonical URL:

- With `trailing_slash: false`: `/about/` → 301 redirect → `/about`
- With `trailing_slash: true`: `/about` → 301 redirect → `/about/`

This ensures consistent URLs for SEO and prevents duplicate content issues.

### Content Parsing

Controls how Ava processes your Markdown content files.

```php
'content' => [
    'frontmatter' => [
        'format' => 'yaml',             // Only YAML supported currently
    ],
    'markdown' => [
        'allow_html'       => true,     // Allow raw HTML in Markdown
        'heading_ids'      => true,     // Add id attributes to headings
        'disallowed_tags'  => [         // HTML tags to strip
            'script',                   // Prevents XSS attacks
            'noscript',                 // Can contain fallback attack vectors
        ],
    ],
    'id' => [
        'type' => 'ulid',               // ulid or uuid7
    ],
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `frontmatter.format` | string | `'yaml'` | Frontmatter parser format. Currently only YAML is supported. |
| `markdown.allow_html` | bool | `true` | Allow raw HTML tags in Markdown content. Set to `false` to strip all HTML. |
| `markdown.heading_ids` | bool | `true` | Automatically add `id` attributes to headings for deep linking. |
| `markdown.disallowed_tags` | array | `['script', 'noscript']` | HTML tags to strip even when `allow_html` is true. |
| `id.type` | string | `'ulid'` | ID format for new content items: `'ulid'` or `'uuid7'`. |

#### Heading IDs

When `heading_ids` is enabled (default), all headings get auto-generated `id` attributes based on their text:

```markdown
## Installation Guide
```

Becomes:

```html
<h2 id="installation-guide">Installation Guide</h2>
```

This enables deep linking to sections (e.g., `/docs/page#installation-guide`).

#### Disallowed Tags

Even with `allow_html: true`, you can block specific HTML tags for security:

```php
'markdown' => [
    'allow_html' => true,
    'disallowed_tags' => ['script', 'iframe', 'object', 'embed'],
],
```

This allows most HTML while blocking potentially dangerous elements.

#### ID Types

Content items can have unique IDs for stable references. Ava supports two formats:

| Type | Format | Example | Description |
|------|--------|---------|-------------|
| `ulid` | 26 characters, time-sortable | `01HXYZ4567ABCDEF` | **Recommended.** Sortable by creation time, URL-safe. |
| `uuid7` | 36 characters, time-sortable | `018f6b7a-...` | Standard UUID v7 format. |

IDs are assigned when you create content with `./ava content:add` or add an `id:` field manually in frontmatter.

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
| `shortcodes.allow_php_snippets` | bool | `true` | Enable the <code>&#91;snippet name=&quot;...&quot;&#93;</code> shortcode for including PHP files. |
| `preview_token` | string\|null | `null` | Secret token for previewing draft content without logging in. |

#### PHP Snippets

When `allow_php_snippets` is `true`, content can include PHP files from the `app/snippets/` directory:

<pre><code class="language-markdown">&#91;snippet name=&quot;cta&quot; heading=&quot;Join Today&quot;&#93;
</code></pre>

This loads `app/snippets/cta.php` and passes the attributes as `$params`. Snippet names are strictly validated:
- Only letters, numbers, hyphens, and underscores allowed
- Maximum 128 characters
- Cannot contain path traversal characters

Set to `false` if you don't need this feature or want to restrict content authors.

#### Preview Token

The preview token lets you view draft content without being logged in. This is useful for:
- Sharing draft previews with clients or editors
- Testing content on devices where you're not logged in
- Previewing from headless CMS integrations

```php
'preview_token' => 'your-secure-random-token-here',
```

Then access drafts via: <code>https://example.com/path/to/draft?preview=1&amp;token=your-secure-random-token-here</code>

<div class="callout-warning">
<strong>Security:</strong> Generate a strong, random token for production:
<pre><code>php -r "echo bin2hex(random_bytes(32));"</code></pre>
Tokens under 16 characters or common words are rejected for security.
</div>

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
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'image/avif',
        ],
    ],
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `enabled` | bool | `false` | Enable the admin dashboard. |
| `path` | string | `'/admin'` | URL path for the admin area. Change to obscure the admin location. |
| `theme` | string | `'cyan'` | Color theme for the admin interface. See themes below. |

<div class="callout-warning">
<strong>Important:</strong> Create admin users with <code>./ava user:add</code> before enabling the admin dashboard.
</div>

#### Admin Themes

| Theme | Description |
|-------|-------------|
| `cyan` | Cool cyan/aqua (default) |
| `pink` | Vibrant pink/magenta |
| `purple` | Classic purple |
| `green` | Matrix green |
| `blue` | Standard blue |
| `amber` | Warm amber/orange |

#### Admin Security

The admin dashboard enforces several security measures:

- **HTTPS required:** Admin access requires HTTPS (except on localhost for development)
- **Session isolation:** Uses a separate session cookie (`ava_admin`) from any front-end sessions
- **CSRF protection:** All forms include and validate CSRF tokens
- **Rate limiting:** Login attempts are rate-limited (5 attempts, then 15-minute lockout)
- **Secure cookies:** HttpOnly, Secure (on HTTPS), SameSite=Lax

#### Media Uploads

The admin includes a secure image uploader with automatic sanitization.

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `media.enabled` | bool | `true` | Enable the media upload feature. |
| `media.path` | string | `'public/media'` | Upload directory (relative to project root). |
| `media.organize_by_date` | bool | `true` | Create `/year/month/` subfolders for uploads. |
| `media.max_file_size` | int | `10485760` | Maximum file size in bytes (10 MB default). |
| `media.allowed_types` | array | See below | Array of allowed MIME types. |

**Default allowed MIME types:**

| MIME Type | Description |
|-----------|-------------|
| `image/jpeg` | JPEG images (.jpg, .jpeg) |
| `image/png` | PNG images (.png) |
| `image/gif` | GIF images, including animated (.gif) |
| `image/webp` | WebP images (.webp) |
| `image/svg+xml` | SVG vector images (.svg) |
| `image/avif` | AVIF images (.avif) |

**Security:** All uploaded images are reprocessed through ImageMagick or GD to strip any hidden payloads, EXIF data, and malicious content before saving.

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
| `level` | string | `'errors'` | Error reporting verbosity level. |

#### Error Levels

| Level | What's Reported |
|-------|-----------------|
| `all` | All errors, warnings, notices, and deprecations. Best for development. |
| `errors` | Only fatal errors and exceptions. **Default.** Good for production. |
| `none` | Suppress all error reporting. Not recommended. |

#### Recommended Settings

**Development** — See everything:

```php
'debug' => [
    'enabled'        => true,
    'display_errors' => true,
    'log_errors'     => true,
    'level'          => 'all',
],
```

**Production** — Log only, never display:

```php
'debug' => [
    'enabled'        => false,
    'display_errors' => false,
    'log_errors'     => true,
    'level'          => 'errors',
],
```

<div class="callout-warning">
<strong>Security Warning:</strong> Never enable <code>display_errors</code> in production—it can expose sensitive information like file paths, database credentials, and stack traces to attackers.
</div>

<div class="callout-info">
<strong>Security Headers:</strong> Ava automatically adds security headers to all responses:
<ul>
<li><code>X-Content-Type-Options: nosniff</code> — Prevents MIME-sniffing attacks</li>
<li><code>X-Frame-Options: SAMEORIGIN</code> — Prevents clickjacking</li>
<li><code>Referrer-Policy: strict-origin-when-cross-origin</code> — Controls referrer information</li>
</ul>
</div>

The admin System page shows debug status, performance metrics, and recent error log entries when debug is enabled.

### Logs

Control log file size and automatic rotation to prevent disk space issues.

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

#### How Rotation Works

When a log file (e.g., `indexer.log`) exceeds `max_size`:

1. Existing rotated logs shift: `.2` → `.3`, `.1` → `.2`
2. Current log becomes `.1`
3. A fresh, empty log file starts
4. If there are more than `max_files` rotations, the oldest is deleted

**Example with defaults (10 MB, 3 files):**

```
storage/logs/
├── indexer.log       ← Current, up to 10 MB
├── indexer.log.1     ← Previous rotation
├── indexer.log.2     ← Older rotation
└── indexer.log.3     ← Oldest (deleted when .4 would be created)
```

#### Log Files

Ava creates these log files as needed:

| File | Contents |
|------|----------|
| `error.log` | PHP errors, exceptions, and warnings (when `log_errors` is true) |
| `indexer.log` | Content indexing errors and validation issues |

#### CLI Commands

```bash
./ava logs:stats              # View log file sizes and settings
./ava logs:tail indexer       # Show last 20 lines of indexer.log
./ava logs:tail indexer -n 50 # Show last 50 lines
./ava logs:clear              # Clear all logs (with confirmation)
./ava logs:clear indexer.log  # Clear specific log file
```

See [CLI - Logs](/docs/cli#logs) for more details.

### CLI Appearance

Customize the command-line interface appearance.

```php
'cli' => [
    'theme' => 'cyan',
],
```

| Theme | Description |
|-------|-------------|
| `cyan` | Cool cyan/aqua (default) |
| `pink` | Vibrant pink/magenta |
| `purple` | Classic purple |
| `green` | Matrix green |
| `blue` | Standard blue |
| `amber` | Warm amber/orange |
| `disabled` | No colors (plain text output) |

Use `disabled` for CI/CD pipelines, Docker logs, or terminals that don't support ANSI colors.

### Plugins

```php
'plugins' => [
    'sitemap',
    'feed',
    'redirects',
],
```

Array of plugin folder names to activate. Plugins load in the order listed, which matters when plugins depend on each other or modify the same hooks.

**Bundled plugins:**

| Plugin | Description |
|--------|-------------|
| `sitemap` | Generates XML sitemaps for search engines at `/sitemap.xml` |
| `feed` | Generates RSS 2.0 feeds at `/feed.xml` and per-type feeds |
| `redirects` | Manage custom URL redirects via the admin dashboard |

See [Bundled Plugins](/docs/bundled-plugins) for detailed configuration options for each plugin.

### Plugin Settings

Add plugin-specific configuration in your `ava.php`. Each plugin documents its available options.

```php
// Sitemap plugin settings
'sitemap' => [
    'enabled' => true,
],

// Feed plugin settings
'feed' => [
    'enabled'        => true,
    'items_per_feed' => 20,
    'full_content'   => false,   // true = full HTML, false = excerpt only
    'types'          => null,    // null = all types, or ['post', 'news']
],
```

### Custom Settings

Add your own site-specific configuration. Access values in templates with `$ava->config()`:

```php
// In ava.php
'analytics' => [
    'tracking_id' => 'G-XXXXXXXXXX',
    'enabled'     => true,
],

'social' => [
    'twitter'  => '@myhandle',
    'mastodon' => 'https://mastodon.social/@myhandle',
],
```

```php
// In templates
<?php if ($ava->config('analytics.enabled')): ?>
    <!-- Analytics code for <?= $ava->config('analytics.tracking_id') ?> -->
<?php endif; ?>

<a href="<?= $ava->config('social.mastodon') ?>">Mastodon</a>
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
        'search' => [
            'enabled' => true,
            'fields'  => ['title', 'body'],
        ],
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
        'taxonomies'   => ['category', 'tag'],
        'fields'       => [],
        'sorting'      => 'date_desc',
        'cache_fields' => [],
        'search' => [
            'enabled' => true,
            'fields'  => ['title', 'excerpt', 'body'],
            'weights' => [
                'title_phrase' => 80,
                'body_token'   => 2,
            ],
        ],
    ],
];
```

### Content Type Options

| Option | Type | Required | Description |
|--------|------|----------|-------------|
| `label` | string | Yes | Human-readable name shown in admin UI. |
| `content_dir` | string | Yes | Folder inside `content/` where files for this type live. |
| `url` | array | Yes | URL generation settings. See [URL Types](#url-types) below. |
| `templates` | array | Yes | Template file mappings. See [Templates](#templates) below. |
| `taxonomies` | array | No | Which taxonomies apply to this type. Default: `[]` |
| `fields` | array | No | Custom field definitions (for validation/admin). Default: `[]` |
| `sorting` | string | No | Default sort order. See [Sorting](#sorting) below. Default: `'date_desc'` |
| `cache_fields` | array | No | Extra frontmatter fields to include in archive cache. Default: `[]` |
| `search` | array | No | Search configuration. See [Search](#search-configuration) below. |

### URL Types

Ava supports two URL strategies:

#### Hierarchical URLs

URLs mirror the file path structure. Best for pages, documentation, and hierarchical content.

```php
'url' => [
    'type' => 'hierarchical',
    'base' => '/',
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `type` | string | — | Set to `'hierarchical'` for path-based URLs. |
| `base` | string | `'/'` | URL prefix. Use `'/'` for root, or `'/docs'` for a section. |

**Examples:**

```
content/pages/about.md           → /about
content/pages/about/team.md      → /about/team
content/pages/services/web.md    → /services/web
content/pages/index.md           → /              (root)
content/pages/docs/index.md      → /docs          (section root)
```

**Special files:**
- `index.md` or `_index.md` files represent the parent folder URL
- Folder structure directly determines URL hierarchy

#### Pattern URLs

URLs generated from a template pattern. Best for blogs, news, and date-based content.

```php
'url' => [
    'type'    => 'pattern',
    'pattern' => '/blog/{slug}',
    'archive' => '/blog',
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `type` | string | — | Set to `'pattern'` for template-based URLs. |
| `pattern` | string | `'/{slug}'` | URL template with placeholders. See below. |
| `archive` | string\|null | `null` | URL for the archive/listing page. Omit to disable archive. |

**Available placeholders:**

| Placeholder | Description | Example |
|-------------|-------------|---------|
| `{slug}` | Item's slug from frontmatter or filename | `my-post` |
| `{id}` | Item's unique ID | `01HXYZ4567ABCD` |
| `{yyyy}` | 4-digit year from date | `2024` |
| `{mm}` | 2-digit month from date | `03` |
| `{dd}` | 2-digit day from date | `15` |

**Pattern examples:**

```php
// Simple blog
'pattern' => '/blog/{slug}'                    // → /blog/my-post

// Year-based archives
'pattern' => '/blog/{yyyy}/{slug}'             // → /blog/2024/my-post

// Full date in URL
'pattern' => '/news/{yyyy}/{mm}/{dd}/{slug}'   // → /news/2024/03/15/my-post

// ID-based (permalink stable)
'pattern' => '/p/{id}'                         // → /p/01HXYZ4567ABCD
```

### Templates

Map content types to template files in your theme.

```php
'templates' => [
    'single'  => 'post.php',      // Single item view
    'archive' => 'archive.php',   // Archive/listing view
],
```

| Key | Description |
|-----|-------------|
| `single` | Template for viewing a single content item. |
| `archive` | Template for the archive listing page (pattern URLs only). |

Templates are looked up in `app/themes/{theme}/templates/`. If not found, Ava falls back to `single.php` then `index.php`.

**Per-item template override:** Set `template:` in frontmatter to use a different template for specific items.

### Sorting

Default sort order for listings and queries.

| Value | Description |
|-------|-------------|
| `date_desc` | Newest first (by `date` field). **Default.** |
| `date_asc` | Oldest first (by `date` field). |
| `title` | Alphabetically by title. |
| `manual` | By `order` field in frontmatter (for manual ordering). |

### Cache Fields

By default, the archive cache includes: `id`, `slug`, `title`, `date`, `status`, `excerpt`, and taxonomy terms. Add extra frontmatter fields for fast access in listings:

```php
'cache_fields' => ['author', 'featured_image', 'reading_time'],
```

These fields are then available in archive queries without loading full content files.

### Search Configuration

Control how content types are searched.

```php
'search' => [
    'enabled' => true,
    'fields'  => ['title', 'excerpt', 'body', 'author'],
    'weights' => [
        'title_phrase'     => 80,
        'title_all_tokens' => 40,
        'title_token'      => 10,
        'excerpt_phrase'   => 30,
        'excerpt_token'    => 3,
        'body_phrase'      => 20,
        'body_token'       => 2,
        'featured'         => 15,
        'field_weight'     => 5,
    ],
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `enabled` | bool | `true` | Include this type in search results. |
| `fields` | array | `['title', 'body']` | Frontmatter fields to search. |
| `weights` | array | See below | Scoring weights for different match types. |

**Default weights:**

| Weight | Default | Description |
|--------|---------|-------------|
| `title_phrase` | 80 | Exact phrase match in title |
| `title_all_tokens` | 40 | All search words appear in title |
| `title_token` | 10 | Per-word match in title |
| `title_token_max` | 30 | Maximum points for title token matches |
| `excerpt_phrase` | 30 | Exact phrase match in excerpt |
| `excerpt_token` | 3 | Per-word match in excerpt |
| `excerpt_token_max` | 15 | Maximum points for excerpt token matches |
| `body_phrase` | 20 | Exact phrase match in body |
| `body_token` | 2 | Per-word match in body |
| `body_token_max` | 10 | Maximum points for body token matches |
| `featured` | 15 | Bonus for items with `featured: true` |
| `field_weight` | 5 | Per custom field match |

**Per-query weight override:**

```php
$results = $ava->query()
    ->type('post')
    ->searchWeights([
        'title_phrase' => 100,    // Boost title matches
        'body_phrase'  => 50,     // Boost body matches
        'featured'     => 0,      // Disable featured boost
    ])
    ->search('tutorial')
    ->get();
```

## Taxonomies: `taxonomies.php`

Taxonomies organize content into groups (categories, tags, authors, etc.). Define taxonomies here, then assign them to content types in `content_types.php`.

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


### What Ava currently uses (code-backed)

In the current Ava codebase, the following taxonomy options have runtime effects:

- `public` (default `true`): if `false`, Ava will not create public taxonomy routes.
- `rewrite.base` (default `/{taxonomy}`): the URL base for taxonomy pages.
- `hierarchical` (default `false`): currently treated as **metadata** (it’s exposed in caches/admin and can be used by themes), but it does not change routing behavior by itself.

Other keys (like `rewrite.separator`, `behaviour.*`, and `ui.*`) are safe to keep for future-proofing and for admin/theme consumption, but they do not currently affect how the public router matches taxonomy URLs.

### How taxonomy URLs work

When `public` is enabled, Ava creates two kinds of routes per taxonomy:

- Taxonomy index: `rewrite.base` (e.g. `/category`) – lists all terms.
- Term archive: `rewrite.base/{term}` (e.g. `/category/php`) – lists items tagged with that term.

The `{term}` portion is the term slug, and Ava matches everything after the base as the term key.

### Term registries (`content/_taxonomies/{taxonomy}.yml`)

You can optionally create a term registry file per taxonomy. Ava reads it during indexing and merges it into the term index.

- Path: `content/_taxonomies/category.yml` (one file per taxonomy)
- Format: a YAML list of objects

Example:

```yaml
- slug: php
    name: PHP
    description: Posts about PHP

- slug: tutorials
    name: Tutorials
```

Behavior:

- If a term is used by published content, Ava adds `count` and `items` to it.
- If a term exists only in the registry, it still appears (with `count: 0`).

Themes can read these term fields via `$ava->terms('category')` and render richer taxonomy pages.
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
| `public` | bool | `true` | Create public archive pages for terms. Set `false` for internal-only taxonomies. |

### Rewrite Options

Control how taxonomy term URLs are generated.

```php
'rewrite' => [
    'base'      => '/category',
    'separator' => '/',
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `base` | string | `'/{taxonomy}'` | URL prefix for term archives. |
| `separator` | string | `'/'` | Separator for hierarchical term paths. |

**Examples:**

```
category: Tutorials         → /category/tutorials
category: Tutorials > PHP   → /category/tutorials/php  (hierarchical)
tag: php                    → /tag/php
```

### Behavior Options

Control how terms are handled.

```php
'behaviour' => [
    'allow_unknown_terms' => true,
    'hierarchy_rollup'    => true,
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `allow_unknown_terms` | bool | `true` | Auto-create terms when used in content. If `false`, only pre-defined terms are valid. |
| `hierarchy_rollup` | bool | `true` | When filtering by parent term, include content from child terms. |

### UI Options

Control taxonomy display in the admin and templates.

```php
'ui' => [
    'show_counts' => true,
    'sort_terms'  => 'name_asc',
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `show_counts` | bool | `true` | Display content count next to terms. |
| `sort_terms` | string | `'name_asc'` | Default term sort order. |

**Sort options:**

| Value | Description |
|-------|-------------|
| `name_asc` | Alphabetically A-Z |
| `name_desc` | Alphabetically Z-A |
| `count_asc` | Least content first |
| `count_desc` | Most content first |

### Using Taxonomies in Content

Assign terms in frontmatter:

```yaml
---
title: My PHP Tutorial
category: Tutorials
tag:
  - php
  - beginner
  - cms
---
```

**Single term:**
```yaml
category: Tutorials
```

**Multiple terms:**
```yaml
tag:
  - php
  - cms
  - beginner
```

**Hierarchical terms** (use `/` separator or nest):
```yaml
category: Tutorials/PHP
# or
category:
  - Tutorials
  - Tutorials/PHP
```

### Term Registry Files

Pre-define terms with additional metadata in `content/_taxonomies/{taxonomy}.yml`:

```yaml
# content/_taxonomies/category.yml
- slug: tutorials
  name: Tutorials
  description: Step-by-step guides

- slug: tutorials/php
  name: PHP
  parent: tutorials
  description: PHP-specific tutorials
```

Registry files let you:
- Define terms before any content uses them
- Add descriptions, images, or custom fields to terms
- Control term metadata separate from content

### Connecting Taxonomies to Content Types

After defining taxonomies, add them to content types in `content_types.php`:

```php
'post' => [
    // ...
    'taxonomies' => ['category', 'tag', 'author'],
],
```

Only listed taxonomies are recognized for that content type.

## Environment-Specific Config

Use PHP logic to override settings per environment:

```php
// app/config/ava.php

$config = [
    'site' => [
        'name'     => 'My Site',
        'base_url' => 'https://example.com',
    ],
    'content_index' => [
        'mode' => 'never',
    ],
    'debug' => [
        'enabled'        => false,
        'display_errors' => false,
    ],
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

// Staging overrides
if (getenv('APP_ENV') === 'staging') {
    $config['site']['base_url'] = 'https://staging.example.com';
    $config['debug']['enabled'] = true;
}

return $config;
```

Set the environment variable in your server config or `.bashrc`:

```bash
export APP_ENV=development
```

Or in your web server (Apache):
```apache
SetEnv APP_ENV production
```

Or in nginx:
```nginx
fastcgi_param APP_ENV production;
```

## Complete Production Example

A comprehensive production-ready configuration:

```php
<?php
// app/config/ava.php

return [

    // ─────────────────────────────────────────────────────────────────────────
    // SITE IDENTITY
    // ─────────────────────────────────────────────────────────────────────────
    
    'site' => [
        'name'        => 'Example Site',
        'base_url'    => 'https://example.com',
        'timezone'    => 'America/New_York',
        'locale'      => 'en_US',
        'date_format' => 'F j, Y',
    ],

    // ─────────────────────────────────────────────────────────────────────────
    // PATHS
    // ─────────────────────────────────────────────────────────────────────────
    
    'paths' => [
        'content'  => 'content',
        'themes'   => 'themes',
        'plugins'  => 'plugins',
        'snippets' => 'snippets',
        'storage'  => 'storage',
        'aliases'  => [
            '@media' . ':' => '/media/',
            '@cdn' . ':'   => 'https://cdn.example.com/',
        ],
    ],

    // ─────────────────────────────────────────────────────────────────────────
    // THEME
    // ─────────────────────────────────────────────────────────────────────────
    
    'theme' => 'default',

    // ─────────────────────────────────────────────────────────────────────────
    // PERFORMANCE
    // ─────────────────────────────────────────────────────────────────────────
    
    'content_index' => [
        'mode'         => 'never',      // Production: rebuild via CLI only
        'backend'      => 'array',
        'use_igbinary' => true,
        'prerender_html' => true,       // Optional: pre-render Markdown → HTML during rebuild
    ],

    'webpage_cache' => [
        'enabled' => true,
        'ttl'     => null,              // Cache until rebuild
        'exclude' => [
            '/api/*',
            '/preview/*',
        ],
    ],

    // ─────────────────────────────────────────────────────────────────────────
    // ROUTING & CONTENT
    // ─────────────────────────────────────────────────────────────────────────
    
    'routing' => [
        'trailing_slash' => false,
    ],

    'content' => [
        'frontmatter' => [
            'format' => 'yaml',
        ],
        'markdown' => [
            'allow_html'      => true,
            'heading_ids'     => true,
            'disallowed_tags' => ['script', 'noscript'],
        ],
        'id' => [
            'type' => 'ulid',
        ],
    ],

    // ─────────────────────────────────────────────────────────────────────────
    // SECURITY
    // ─────────────────────────────────────────────────────────────────────────
    
    'security' => [
        'shortcodes' => [
            'allow_php_snippets' => true,
        ],
        'preview_token' => getenv('PREVIEW_TOKEN') ?: null,
    ],

    // ─────────────────────────────────────────────────────────────────────────
    // ADMIN
    // ─────────────────────────────────────────────────────────────────────────
    
    'admin' => [
        'enabled' => true,
        'path'    => '/admin',
        'theme'   => 'cyan',
        'media'   => [
            'enabled'          => true,
            'path'             => 'public/media',
            'organize_by_date' => true,
            'max_file_size'    => 10 * 1024 * 1024,
            'allowed_types'    => [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'image/svg+xml',
                'image/avif',
            ],
        ],
    ],

    // ─────────────────────────────────────────────────────────────────────────
    // PLUGINS
    // ─────────────────────────────────────────────────────────────────────────
    
    'plugins' => [
        'sitemap',
        'feed',
        'redirects',
    ],

    'sitemap' => [
        'enabled' => true,
    ],

    'feed' => [
        'items_per_feed' => 20,
        'full_content'   => false,
    ],

    // ─────────────────────────────────────────────────────────────────────────
    // CLI & LOGGING
    // ─────────────────────────────────────────────────────────────────────────
    
    'cli' => [
        'theme' => 'cyan',
    ],

    'logs' => [
        'max_size'  => 10 * 1024 * 1024,
        'max_files' => 3,
    ],

    // ─────────────────────────────────────────────────────────────────────────
    // DEBUG (disabled in production)
    // ─────────────────────────────────────────────────────────────────────────
    
    'debug' => [
        'enabled'        => false,
        'display_errors' => false,
        'log_errors'     => true,
        'level'          => 'errors',
    ],

    // ─────────────────────────────────────────────────────────────────────────
    // CUSTOM SETTINGS
    // ─────────────────────────────────────────────────────────────────────────
    
    'analytics' => [
        'tracking_id' => 'G-XXXXXXXXXX',
        'enabled'     => true,
    ],

    'social' => [
        'twitter'  => '@example',
        'mastodon' => 'https://mastodon.social/@example',
        'github'   => 'https://github.com/example',
    ],

];
```

## Quick Reference

### All ava.php Options

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `site.name` | string | — | Site display name |
| `site.base_url` | string | — | Full site URL (no trailing slash) |
| `site.timezone` | string | `'UTC'` | PHP timezone identifier |
| `site.locale` | string | `'en_GB'` | PHP locale code |
| `site.date_format` | string | `'F j, Y'` | Default date format |
| `theme` | string | `'default'` | Active theme folder |
| `paths.content` | string | `'content'` | Content directory |
| `paths.themes` | string | `'app/themes'` | Themes directory |
| `paths.plugins` | string | `'app/plugins'` | Plugins directory |
| `paths.snippets` | string | `'app/snippets'` | Snippets directory |
| `paths.storage` | string | `'storage'` | Storage directory |
| `paths.aliases` | array | `[]` | Path aliases for content |
| `content_index.mode` | string | `'auto'` | `auto`, `never`, `always` |
| `content_index.backend` | string | `'array'` | `array` or `sqlite` |
| `content_index.use_igbinary` | bool | `true` | Use igbinary if available |
| `content_index.prerender_html` | bool | `false` | Pre-render Markdown → HTML during rebuild |
| `webpage_cache.enabled` | bool | `true` | Enable HTML caching |
| `webpage_cache.ttl` | int\|null | `null` | Cache lifetime (seconds) |
| `webpage_cache.exclude` | array | `[]` | URL patterns to skip |
| `routing.trailing_slash` | bool | `false` | URL trailing slash preference |
| `content.frontmatter.format` | string | `'yaml'` | Frontmatter format |
| `content.markdown.allow_html` | bool | `true` | Allow HTML in Markdown |
| `content.markdown.heading_ids` | bool | `true` | Auto-generate heading IDs |
| `content.markdown.disallowed_tags` | array | `['script', 'noscript']` | HTML tags to strip |
| `content.id.type` | string | `'ulid'` | `ulid` or `uuid7` |
| `security.shortcodes.allow_php_snippets` | bool | `true` | Enable <code>&#91;snippet&#93;</code> shortcode |
| `security.preview_token` | string\|null | `null` | Draft preview token |
| `admin.enabled` | bool | `false` | Enable admin dashboard |
| `admin.path` | string | `'/admin'` | Admin URL path |
| `admin.theme` | string | `'cyan'` | Admin color theme |
| `admin.media.enabled` | bool | `true` | Enable uploads |
| `admin.media.path` | string | `'public/media'` | Upload directory |
| `admin.media.organize_by_date` | bool | `true` | Year/month folders |
| `admin.media.max_file_size` | int | `10485760` | Max upload size (bytes) |
| `admin.media.allowed_types` | array | See above | Allowed MIME types |
| `plugins` | array | `[]` | Active plugin folders |
| `cli.theme` | string | `'cyan'` | CLI color theme |
| `logs.max_size` | int | `10485760` | Log rotation size |
| `logs.max_files` | int | `3` | Rotated logs to keep |
| `debug.enabled` | bool | `false` | Enable debug mode |
| `debug.display_errors` | bool | `false` | Show errors in browser |
| `debug.log_errors` | bool | `true` | Log errors to file |
| `debug.level` | string | `'errors'` | `all`, `errors`, `none` |
