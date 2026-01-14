---
title: Routing
slug: routing
status: published
meta_title: Routing | Flat-file PHP CMS | Ava CMS
meta_description: Ava CMS routing explained. Learn about hierarchical URLs, pattern-based URLs, redirects, trailing slashes, and how content structure maps to URLs.
excerpt: URLs are generated automatically based on your content structure. Choose hierarchical URLs that mirror file paths, or pattern URLs for date-based blog archives.
---

In Ava, you don't need to write complex route files. URLs are generated automatically based on your content structure and configuration.

## How It Works

Ava looks at your `content/` folder and your configuration to decide what URL each file gets.

1. **💾 You save a file** in the appropriate content directory.
2. **👀 Ava indexes it** (automatically in `auto` mode, or via `./ava rebuild`).
3. **✨ The URL works** — routing is handled for you.

All routes are compiled into a binary cache (`storage/cache/routes.bin`) for near-instant lookups.


## URL Styles

Ava supports two URL generation strategies. You configure these per content type in `app/config/content_types.php`.

### Hierarchical URLs (Folder Style)

Best for pages, documentation, and content that naturally forms a hierarchy. The URL mirrors the file path structure.

```php
'page' => [
    'url' => [
        'type' => 'hierarchical',
        'base' => '/',
    ],
]
```

**How it maps files to URLs:**

| File Path | URL |
|-----------|-----|
| `content/pages/about.md` | `/about` |
| `content/pages/about/team.md` | `/about/team` |
| `content/pages/services/web.md` | `/services/web` |
| `content/pages/index.md` | `/` (homepage) |
| `content/pages/docs/index.md` | `/docs` |

**Key points:**

- `index.md` or `_index.md` files represent the parent folder URL
- The `base` option adds a prefix (e.g., `'base' => '/docs'` would make `content/pages/about.md` → `/docs/about`)
- Setting `slug:` in frontmatter does **not** change the URL for hierarchical content — the filesystem path determines the URL
- The internal content key used for lookups is the path (e.g., `'about/team'`), not the slug

**Fetching hierarchical content in templates:**

```php
// Use the path-based key, not the slug
$item = $ava->get('page', 'about/team');
$item = $ava->get('page', 'docs/getting-started');
```

### Pattern URLs

Best for blogs, news, and date-based content where you want consistent URL structures regardless of file organization.

```php
'post' => [
    'url' => [
        'type'    => 'pattern',
        'pattern' => '/blog/{slug}',
        'archive' => '/blog',          // Optional: URL for the listing page
    ],
]
```

**Available placeholders:**

| Placeholder | Description | Example |
|-------------|-------------|---------|
| `{slug}` | Item's slug from frontmatter or filename | `my-post` |
| `{id}` | Item's unique ID (ULID or UUID7) | `01HXYZ4567ABCD` |
| `{yyyy}` | 4-digit year from `date` field | `2024` |
| `{mm}` | 2-digit month from `date` field | `03` |
| `{dd}` | 2-digit day from `date` field | `15` |

**Pattern examples:**

```php
// Simple blog URLs
'pattern' => '/blog/{slug}'                    // → /blog/my-post

// Year-based archives
'pattern' => '/blog/{yyyy}/{slug}'             // → /blog/2024/my-post

// Full date in URL
'pattern' => '/news/{yyyy}/{mm}/{dd}/{slug}'   // → /news/2024/03/15/my-post

// ID-based permalinks (rename-proof)
'pattern' => '/p/{id}'                         // → /p/01HXYZ4567ABCD
```

**Key points:**

- The `slug` frontmatter field (or filename if omitted) becomes the lookup key
- With pattern URLs, file organization inside the content directory doesn't affect URLs
- The optional `archive` setting creates a listing page at that URL

**Fetching pattern content in templates:**

```php
// For pattern URL types, the lookup key is the item's slug
$post = $ava->get('post', 'hello-world');
```


## Route Matching Order

When a request comes in, Ava checks routes in this specific order:

1. **Hook interception** — `router.before_match` filter can intercept and return a response early
2. **Trailing slash redirect** — Enforces your canonical URL style (301 redirect)
3. **Redirects** — 301 redirects from `redirect_from` frontmatter
4. **System routes** — Custom routes registered via `$router->addRoute()`
5. **Exact routes** — Content URLs from the routes cache
6. **Preview mode** — Allows draft access with valid preview token
7. **Prefix routes** — Custom routes registered via `$router->addPrefixRoute()`
8. **Taxonomy routes** — Archives like `/category/tutorials`
9. **404** — No match found

Understanding this order helps when debugging why a route isn't matching as expected.


## Content Status and Routing

Content status affects whether items are routed:

| Status | Routing Behavior |
|--------|------------------|
| `draft` | **Not routed publicly.** Only accessible via preview mode with a valid token. Not included in the routes cache. |
| `published` | **Fully routed.** Accessible at its URL. Included in archives, listings, taxonomy indexes, and sitemaps. |
| `unlisted` | **Routed but hidden.** Accessible via direct URL (no preview token needed). Excluded from archives, listings, and taxonomy indexes. |


## Redirects

When you move or rename content, set up redirects from the old URLs:

```yaml
---
title: New Page Title
slug: new-page
redirect_from:
  - /old-page
  - /legacy/old-url
  - /renamed/from/here
---
```

Ava issues a **301 (permanent) redirect** from each old URL to the current canonical URL.

For frontmatter details, see [Redirects](/docs/content#content-redirects).

**Key points:**

- Redirects are processed before content routes in the matching order
- You can have multiple old URLs redirect to the same new page
- The redirect happens server-side, so search engines update their indexes

For managing redirects outside of content files (e.g., arbitrary URL mappings), see the [Redirects plugin](/docs/bundled-plugins#redirects).


## Trailing Slash

Configure your preferred URL style in `ava.php`:

```php
'routing' => [
    'trailing_slash' => false,  // /about (recommended)
    // 'trailing_slash' => true,   // /about/
]
```

When a request arrives with the "wrong" format, Ava issues a **301 redirect** to the canonical URL:

- With `trailing_slash: false`: `/about/` → 301 → `/about`
- With `trailing_slash: true`: `/about` → 301 → `/about/`

This ensures consistent URLs for SEO and prevents duplicate content issues.

<div class="callout-info">
The root path <code>/</code> is always valid regardless of this setting.
</div>


## Taxonomy Routes

Taxonomies (categories, tags, etc.) automatically get archive routes when `public: true` in their configuration.

**Route types created:**

| Route Type | Example | Template |
|------------|---------|----------|
| Taxonomy index | `/category` | `taxonomy-index.php` |
| Term archive | `/category/tutorials` | `taxonomy.php` |

**Configuration in `taxonomies.php`:**

```php
'category' => [
    'label'   => 'Categories',
    'public'  => true,              // Creates public routes
    'rewrite' => [
        'base' => '/category',      // URL prefix for term archives
    ],
],
```

Set `'public' => false` for internal-only taxonomies that shouldn't have public archive pages.

**Template variables for taxonomy routes:**

In `taxonomy.php` or `taxonomy-index.php`, you have access to:

```php
$tax['name']   // Taxonomy name (e.g., 'category')
$tax['term']   // Term data array (for term archives)
$tax['terms']  // All terms (for taxonomy index)
$query         // Pre-built Query filtered to this term
```


## Preview Mode

Preview mode allows viewing draft content without publishing it. This is useful for:

- Reviewing drafts before publishing
- Sharing previews with clients or editors
- Testing on devices where you're not logged in

**Access a draft via preview:**

```
https://example.com/blog/my-draft?preview=1&token=YOUR_SECRET_TOKEN
```

**Configure in `ava.php`:**

```php
'security' => [
    'preview_token' => 'your-secure-random-token',
]
```

**Security considerations:**

- Generate a strong, random token: `php -r "echo bin2hex(random_bytes(32));"`
- The token is validated using timing-safe comparison (`hash_equals`)
- Both `preview=1` and `token=...` query parameters are required
- If no token is configured, preview mode is disabled
- Preview mode works for content that matches URL patterns but isn't in the routes cache (drafts)


## Adding Custom Routes

You can register custom routes for API endpoints, special pages, or dynamic functionality.

### Exact Routes

Match a specific URL path:

```php
// In theme.php
return function (\Ava\Application $app): void {
    $router = $app->router();
    
    $router->addRoute('/api/search', function ($request) use ($app) {
        $query = $request->query('q', '');
        
        $results = $app->query()
            ->published()
            ->search($query)
            ->perPage(10)
            ->get();
        
        return \Ava\Http\Response::json([
            'query' => $query,
            'results' => array_map(fn($item) => [
                'title' => $item->title(),
                'url' => $app->router()->urlFor($item->type(), $item->slug()),
            ], $results),
        ]);
    });
};
```

### Routes with Parameters

Use `{param}` placeholders for dynamic segments:

```php
$router->addRoute('/api/posts/{id}', function ($request, $params) use ($app) {
    $id = $params['id'];
    $item = $app->repository()->getById($id);
    
    if (!$item) {
        return \Ava\Http\Response::json(['error' => 'Not found'], 404);
    }
    
    return \Ava\Http\Response::json([
        'title' => $item->title(),
        'content' => $app->render('partials/post-body', ['content' => $item]),
    ]);
});
```

### Prefix Routes

Match all URLs starting with a prefix:

```php
$router->addPrefixRoute('/api/', function ($request) use ($app) {
    // Handles all /api/* requests
    $path = $request->path();
    
    // Your API routing logic here
    return \Ava\Http\Response::json(['path' => $path]);
});
```

**Order matters:** Prefix routes are checked after exact routes and content routes, so they won't override content URLs.

### Using Hooks for Routes

You can also intercept routing using the `router.before_match` filter:

```php
use Ava\Plugins\Hooks;
use Ava\Http\Response;

Hooks::addFilter('router.before_match', function ($match, $request) use ($app) {
    if ($request->path() === '/custom-page') {
        return Response::html(
            $app->render('custom-template', ['request' => $request])
        );
    }
    return $match; // Let normal routing continue
});
```

This is checked first in the route matching order, so it can override any other route.

For hook basics (filters vs actions, priorities, etc.), see [Understanding Hooks](/docs/creating-plugins#content-understanding-hooks).


## Route Caching

Routes are compiled into binary cache files for instant lookups:

| File | Contents |
|------|----------|
| `storage/cache/routes.bin` | All route mappings |
| `storage/cache/slug_lookup.bin` | Quick content key → file lookups |

**Cache structure:**

```php
[
    'redirects' => [
        '/old-url' => ['to' => '/new-url', 'code' => 301],
    ],
    'exact' => [
        '/about' => [
            'type' => 'single',
            'content_type' => 'page',
            'slug' => 'about',
            'file' => 'pages/about.md',
            'template' => 'page.php',
        ],
    ],
    'taxonomy' => [
        'category' => [
            'base' => '/category',
            'hierarchical' => false,
        ],
    ],
]
```

**Rebuilding:**

- With `content_index.mode = 'auto'`: Routes rebuild automatically when content changes
- With `content_index.mode = 'never'`: Use `./ava rebuild` to update routes

For more on caching and performance, see [Performance](/docs/performance).


## Generating URLs in Templates

Use the `$ava` helper to generate URLs:

```php
// Content URL (pattern-based types use slug, hierarchical use path)
<?= $ava->url('post', 'hello-world') ?>       // /blog/hello-world
<?= $ava->url('page', 'about/team') ?>        // /about/team

// Taxonomy term URL
<?= $ava->termUrl('category', 'tutorials') ?> // /category/tutorials

// Full absolute URL
<?= $ava->fullUrl('/about') ?>                // https://example.com/about

// Site base URL
<?= $ava->baseUrl() ?>                        // https://example.com
```


## Debugging Routes

**Check if a route exists:**

```php
$router = $app->router();
$routes = $app->repository()->routes();

// Inspect the routes cache
var_dump($routes['exact']['/my-path'] ?? 'not found');
```

**Common issues:**

| Issue | Cause | Solution |
|-------|-------|----------|
| 404 for new content | Cache not rebuilt | Run `./ava rebuild` or set `content_index.mode = 'auto'` |
| Wrong URL for hierarchical content | Using slug instead of path | Use path-based key: `$ava->get('page', 'about/team')` |
| Redirect loop | Conflicting `redirect_from` | Check for circular redirects in frontmatter |
| Custom route not matching | Route order conflict | Check if content route matches first |
| Preview not working | Missing or invalid token | Verify `security.preview_token` in config |
