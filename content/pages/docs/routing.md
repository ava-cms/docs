---
title: Routing
slug: routing
status: published
meta_title: Routing | Flat-file PHP CMS | Ava CMS
meta_description: Ava CMS routing explained. Learn about hierarchical URLs, pattern-based URLs, redirects, trailing slashes, and how content structure maps to URLs.
excerpt: URLs are generated automatically based on your content structure. Choose hierarchical URLs that mirror file paths, or pattern URLs for date-based blog archives.
---

**Routing** is how your CMS decides which content to show when someone visits a URL on your site — like matching `/about` to your about page.

In Ava CMS, you don't write route files. URLs are generated automatically based on your content structure and configuration.

## How It Works

1. **💾 Save a file** in your content directory
2. **👀 Ava indexes it** (automatically in `auto` [index mode](/docs/configuration#content-content-index) or via `./ava rebuild`)
3. **✨ The URL works** — routing is handled for you

Routes are compiled into a binary cache for instant lookups.


## URL Styles

Choose how URLs are generated per content type in `app/config/content_types.php`.

### Hierarchical URLs

Best for most simple content types that aren't organised by date. URLs mirror your file structure.

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

- `index.md` files represent the parent folder URL
- `base` adds a URL prefix (e.g., `'base' => '/docs'` makes `about.md` → `/docs/about`)
- The file path determines the URL — `slug:` in frontmatter is ignored
- Use path-based keys for lookups: `$ava->get('page', 'about/team')`

### Pattern URLs

Best for blogs and news. URLs follow a specified pattern regardless of file organization.

```php
'post' => [
    'url' => [
        'type'    => 'pattern',
        'pattern' => '/blog/{slug}',
        'archive' => '/blog',  // Optional: listing page URL
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

**Examples:**

```php
'/blog/{slug}'                        // → /blog/my-post
'/blog/{yyyy}/{slug}'                 // → /blog/2024/my-post
'/news/{yyyy}/{mm}/{dd}/{slug}'       // → /news/2024/03/15/my-post
'/p/{id}'                             // → /p/01HXYZ4567ABCD (rename-proof)
```

**Key points:**

- Use the `slug` field for lookups: `$ava->get('post', 'hello-world')`
- File organization doesn't affect URLs
- `archive` creates a listing page

## Content Status and Routing

Content files **must** have a status in their frontmatter to determine routing behavior:

```yaml
---
title: Sample Post
status: published  # draft, published, unlisted
---
```

Depending on the status, routing behaves as follows:


| Status | Routing Behavior |
|--------|------------------|
| `draft` | Not routed publicly. Only accessible via [preview mode](#preview-mode). |
| `published` | Fully accessible. Appears in listings and sitemaps. |
| `unlisted` | Accessible via direct URL but excluded from listings. |


## Preview Mode

View draft content before publishing by adding `?preview=1&token=...` to the URL:

```
https://example.com/blog/my-draft?preview=1&token=YOUR_SECRET_TOKEN
```

**Configure in `ava.php`:**

```php
'security' => [
    'preview_token' => 'your-secure-random-token',
]
```

**Generate a secure token:**
```bash
php -r "echo bin2hex(random_bytes(32));"
```

Both `preview=1` and `token` parameters are required. Without a configured token, preview mode is disabled.


## Redirects

Redirect old URLs when you move or rename content:

```yaml
---
title: New Page Title
redirect_from:
  - /old-page
  - /legacy/old-url
---
```

Ava issues **301 (permanent) redirects** from old URLs to the current URL. For external redirects or arbitrary mappings, use the [Redirects plugin](/docs/bundled-plugins#redirects).


## Trailing Slash

Enforce consistent URL format in `ava.php`:

```php
'routing' => [
    'trailing_slash' => false,  // /about (recommended)
]
```

Ava issues **301 redirects** to enforce your choice:
- `false`: `/about/` → `/about`
- `true`: `/about` → `/about/`

The root `/` is always valid.


## Taxonomy Routes

Taxonomies with `public: true` automatically get routes:

```php
'category' => [
    'label'   => 'Categories',
    'public'  => true,         // Enable public routes
    'rewrite' => [
        'base' => '/category', // URL prefix
    ],
],
```

**Routes created:**
- `/category` — Taxonomy index (all terms)
- `/category/tutorials` — Term archive (items with this term)

**Template variables:**
- `$tax['name']` — Taxonomy name
- `$tax['term']` — Term data (term archives)
- `$tax['terms']` — All terms (index)
- `$query` — Pre-filtered Query object

**See:** [Taxonomies](/docs/taxonomies) for full documentation on configuration, term storage, and template examples.


## Adding Custom Routes

Register custom routes in your `theme.php` for APIs or special pages.

### Exact Routes

```php
return function (\Ava\Application $app): void {
    $router = $app->router();
    
    $router->addRoute('/api/search', function ($request) use ($app) {
        $results = $app->query()
            ->published()
            ->search($request->query('q', ''))
            ->perPage(10)
            ->get();
        
        return \Ava\Http\Response::json([
            'results' => array_map(fn($item) => [
                'title' => $item->title(),
                'url' => $app->router()->urlFor($item->type(), $item->slug()),
            ], $results),
        ]);
    });
};
```

### Routes with Parameters

```php
$router->addRoute('/api/posts/{id}', function ($request, $params) use ($app) {
    $item = $app->repository()->getById($params['id']);
    
    if (!$item) {
        return \Ava\Http\Response::json(['error' => 'Not found'], 404);
    }
    
    return \Ava\Http\Response::json(['title' => $item->title()]);
});

### Prefix Routes

```php
$router->addPrefixRoute('/api/', function ($request) use ($app) {
    // Handles all /api/* requests
    return \Ava\Http\Response::json(['path' => $request->path()]);
});
```

Prefix routes are checked after exact and content routes.

### Using Hooks

Intercept routing with the `router.before_match` filter (checked first):

```php
use Ava\Plugins\Hooks;
use Ava\Http\Response;

Hooks::addFilter('router.before_match', function ($match, $request) use ($app) {
    if ($request->path() === '/custom-page') {
        return Response::html($app->render('custom-template'));
    }
    return $match;
});
```

See [Understanding Hooks](/docs/creating-plugins#content-understanding-hooks) for details.


## Route Caching

Routes are compiled to binary cache files for instant lookups:
- `storage/cache/routes.bin` — All route mappings
- `storage/cache/slug_lookup.bin` — Content key lookups

**Rebuilding:**
- `content_index.mode = 'auto'` — Automatic when content changes
- `content_index.mode = 'never'` — Run `./ava rebuild` manually

See [Performance](/docs/performance) for details.


## Generating URLs in Templates

Use `$ava` helper methods:

```php
<?= $ava->url('post', 'hello-world') ?>       // Content: /blog/hello-world
<?= $ava->url('page', 'about/team') ?>        // Hierarchical: /about/team
<?= $ava->termUrl('category', 'tutorials') ?> // Term: /category/tutorials
<?= $ava->fullUrl('/about') ?>                // Full: https://example.com/about
<?= $ava->baseUrl() ?>                        // Base: https://example.com
```
## Route Matching Order

When a request comes in, Ava CMS checks routes in this specific order:

1. **Hook interception** — `router.before_match` filter can intercept and return a response early
2. **Trailing slash redirect** — Enforces your [canonical URL style](/docs/configuration#:~:text=trailing%20slash) (301 redirect)
3. **Redirects** — 301 redirects from `redirect_from` frontmatter
4. **System routes** — Custom routes registered via `$router->addRoute()`
5. **Exact routes** — Content URLs from the routes cache
6. **Preview mode** — Allows draft access with valid preview token
7. **Prefix routes** — Custom routes registered via `$router->addPrefixRoute()`
8. **Taxonomy routes** — Archives like `/category/tutorials`
9. **404** — No match found

Understanding this order helps when debugging why a route isn't matching as expected.

## Debugging Routes

**Inspect route cache:**

```php
$routes = $app->repository()->routes();
var_dump($routes['exact']['/my-path'] ?? 'not found');
```

**Common issues:**

| Issue | Solution |
|-------|----------|
| 404 for new content | Run `./ava rebuild` or set `content_index.mode = 'auto'` |
| Wrong hierarchical URL | Use path-based key: `$ava->get('page', 'about/team')` |
| Redirect loop | Check for circular `redirect_from` |
| Preview not working | Verify `security.preview_token` in config |

<div class="related-docs">
<h2>Related Documentation</h2>
<ul>
<li><a href="/docs/configuration#content-content-types-content_typesphp">Configuration: Content Types</a> — URL patterns and settings</li>
<li><a href="/docs/taxonomies#content-routing">Taxonomies: Routing</a> — Taxonomy URL generation</li>
<li><a href="/docs/theming">Theming</a> — Template resolution</li>
<li><a href="/docs/api">API</a> — Building custom endpoints</li>
<li><a href="/docs/creating-plugins#content-frontend-routes">Creating Plugins: Frontend Routes</a> — Plugin routes</li>
</ul>
</div>
