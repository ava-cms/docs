---
title: API
slug: api
status: published
meta_title: Building APIs | Flat-file PHP CMS | Ava CMS
meta_description: Build custom JSON APIs with Ava CMS. Learn about routing, Request and Response helpers, and how to create headless CMS endpoints for your applications.
excerpt: Ava gives you the tools to build exactly the API you need—a router, Request/Response helpers, and content access via the Repository. Perfect for headless CMS setups.
---

Ava doesn't force a specific API on you. Instead, it gives you the tools to build exactly the API you need.

What Ava provides out of the box:

- A router you can extend from plugins (`$app->router()`)
- A small `\Ava\Http\Request` wrapper (query params, headers, body)
- A small `\Ava\Http\Response` builder (JSON, redirects, headers)
- Content access via `\Ava\Content\Repository` and `\Ava\Content\Query`

If you want a “headless CMS” JSON API, you typically implement it as a plugin that registers routes and returns `Response::json(...)`.

## Routing Basics

### How matching works

The router matches requests in a specific order (simplified):

1. Hooks may intercept routing (`router.before_match`)
2. Trailing slash canonical redirects (based on `routing.trailing_slash`)
3. Redirects from indexed content (`redirect_from` frontmatter)
4. System routes registered via `addRoute(...)`
5. Exact content routes from the content index
6. Preview matching for draft content (requires a preview token)
7. Prefix routes registered via `addPrefixRoute(...)`
8. Taxonomy routes (taxonomy index + term pages)
9. 404

### Route handlers

Routes registered via `$router->addRoute(...)` and `$router->addPrefixRoute(...)` are invoked like this:

```php
function(\Ava\Http\Request $request, array $params): \Ava\Routing\RouteMatch|\Ava\Http\Response|null
```

Most of the time, returning a `\Ava\Http\Response` is easiest.

### Returning a Response vs a RouteMatch

- If you return a `\Ava\Http\Response`, Ava will send it directly.
- If you return a `\Ava\Routing\RouteMatch`, Ava will treat it like a normal “page match” and render a template.

For API endpoints, prefer returning `Response`.

## Request & Response Helpers

### Request

Common `\Ava\Http\Request` methods you’ll use in endpoints:

- `$request->method()` / `$request->isMethod('POST')`
- `$request->path()`
- `$request->query('key', $default)`
- `$request->header('X-Api-Key')` (header names are case-insensitive)
- `$request->body()`
- `$request->expectsJson()` (checks `Accept: application/json`)

### Response

Common `\Ava\Http\Response` helpers:

- `Response::json($data, $status = 200)`
- `Response::redirect($url, $status = 302)`
- `Response::text($string, $status = 200)`
- `Response::html($string, $status = 200)`

You can add headers immutably:

```php
return \Ava\Http\Response::json(['ok' => true])
    ->withHeader('Cache-Control', 'no-store');
```

## Building a JSON API

Since Ava is just PHP, you can easily create endpoints that return JSON. This is great if you want to use Ava as a headless CMS for a mobile app or a JavaScript frontend.

### Example: A Simple Read-Only API

You can create a plugin to expose your content as JSON.

```php
// app/plugins/json-api/plugin.php

return [
    'name' => 'JSON API',
    'boot' => function($app) {
        $router = $app->router();
        
        // Endpoint: /api/posts
        $router->addRoute('/api/posts', function($request, $params) use ($app) {
            $repo = $app->repository();
            $posts = $repo->publishedMeta('post');
            
            // Return JSON response
            return \Ava\Http\Response::json([
                'data' => array_map(fn($p) => [
                    'title' => $p->title(),
                    'slug' => $p->slug(),
                ], $posts)
            ]);
        });
    }
];
```

Now, visiting `/api/posts` will give you a clean JSON list of your blog posts.

## Building Custom Endpoints

All routes in Ava are handled through the router:

- `addRoute('/path', $handler)` registers an exact path, with optional `{param}` placeholders.
- `addPrefixRoute('/prefix/', $handler)` registers a handler for *all* paths under a prefix.

Handlers are called with:

```php
function(\Ava\Http\Request $request, array $params): \Ava\Routing\RouteMatch|\Ava\Http\Response|null
```

In most cases, the simplest thing to do is just return a `\Ava\Http\Response`.

### Basic Route

```php
$router->addRoute('/api/custom', function($request, $params) {
    return \Ava\Http\Response::json([
        'message' => 'Hello from API!'
    ]);
});
```

### Route with Parameters

```php
$router->addRoute('/api/content/{type}/{slug}', function($request, $params) use ($app) {
    $type = $params['type'];
    $slug = $params['slug'];
    
    $repo = $app->repository();
    $item = $repo->get($type, $slug);
    
    if ($item === null) {
        return \Ava\Http\Response::json(['error' => 'Not found'], 404);
    }

    return \Ava\Http\Response::json([
        'type' => $item->type(),
        'title' => $item->title(),
        'slug' => $item->slug(),
        'url' => $item->url(),
        'status' => $item->status(),
    ]);
});
```

### Query Parameters

```php
$router->addRoute('/api/search', function($request, $params) {
    $query = $request->query('q', '');
    $limit = (int) $request->query('limit', 10);
    
    // Perform search...
});
```

Notes:
- `$request->query()` returns the full query array.
- `$request->query('key', $default)` returns a single query value.

### Prefix Routes

Handle all routes under a path:

```php
$router->addPrefixRoute('/api/v2/', function($request, $params) {
    $path = $request->path();

    // For example, handle /api/v2/ping
    if ($path === '/api/v2/ping') {
        return \Ava\Http\Response::json(['ok' => true]);
    }

    return \Ava\Http\Response::json(['error' => 'Not found'], 404);
});
```

<div class="callout-info">
<strong>Routing order matters.</strong> System routes are matched before content routes, and prefix routes are matched after exact content routes and preview matching.
</div>

## Authentication

### API Key Authentication

```php
// In your plugin's boot function:
'boot' => function($app) {
    $router = $app->router();
    
    $authenticateRequest = function($request) use ($app): bool {
        $apiKey = $request->header('X-API-Key') 
               ?? $request->query('api_key');
        
        // Ava doesn't ship an API key system by default.
        // This is just one simple pattern you can implement in your own config.
        $validKeys = $app->config('api.keys', []);
        
        return in_array($apiKey, $validKeys, true);
    };

    $router->addRoute('/api/private', function($request, $params) use ($authenticateRequest) {
        if (!$authenticateRequest($request)) {
            return \Ava\Http\Response::json(['error' => 'Unauthorized'], 401);
        }
        
        // Handle authenticated request...
        return \Ava\Http\Response::json(['ok' => true]);
    });
};
```

### Config for API Keys

```php
// app/config/ava.php
return [
    // ...
    'api' => [
        'keys' => [
            'your-secret-api-key-here',
        ],
    ],
];
```

## Pagination

```php
$router->addRoute('/api/posts', function($request, $params) use ($app) {
    $page = (int) $request->query('page', 1);
    $perPage = (int) $request->query('per_page', 10);

    $query = $app->query()
        ->type('post')
        ->published()
        ->orderBy('date', 'desc')
        ->page($page)
        ->perPage($perPage);

    $items = $query->get();

    return \Ava\Http\Response::json([
        'data' => array_map(fn($p) => [
            'title' => $p->title(),
            'slug' => $p->slug(),
            'url' => $p->url(),
        ], $items),
        'pagination' => $query->pagination(),
    ]);
});
```

## Taxonomy Endpoints

```php
// List all categories
$router->addRoute('/api/categories', function($request, $params) use ($app) {
    $repo = $app->repository();
    $terms = $repo->terms('category');
    
    return \Ava\Http\Response::json(array_map(fn($term) => [
        'name' => $term,
        'slug' => \Ava\Support\Str::slug($term),
        'url' => '/category/' . \Ava\Support\Str::slug($term),
    ], $terms));
});

// Posts by category
$router->addRoute('/api/categories/{slug}/posts', function($request, $params) use ($app) {
    $posts = $app->query()
        ->type('post')
        ->published()
        ->whereTax('category', $params['slug'])
        ->get();
    
    return \Ava\Http\Response::json(array_map(fn($p) => [
        'title' => $p->title(),
        'slug' => $p->slug(),
    ], $posts));
});
```

## Search Endpoint

The `\Ava\Content\Query` class has built-in search with relevance scoring:

```php
$router->addRoute('/api/search', function($request, $params) use ($app) {
    $query = trim($request->query('q', ''));
    
    if (strlen($query) < 2) {
        return \Ava\Http\Response::json([
            'results' => [],
            'message' => 'Query too short',
        ]);
    }
    
    // Search posts with built-in relevance scoring
    $searchQuery = $app->query()
        ->type('post')
        ->published()
        ->search($query)
        ->perPage(20);
    
    $results = $searchQuery->get();
    
    return \Ava\Http\Response::json([
        'query' => $query,
        'count' => $searchQuery->count(),
        'results' => array_map(fn($item) => [
            'type' => $item->type(),
            'title' => $item->title(),
            'slug' => $item->slug(),
            'excerpt' => $item->excerpt(),
        ], $results),
        'pagination' => $searchQuery->pagination(),
    ]);
});
```

Search automatically scores results by:
- Title matches (phrase and individual words)
- Excerpt matches
- Body content matches
- Featured item boost

Configure per content type in `content_types.php`:
```php
'search' => [
    'fields' => ['title', 'excerpt', 'body', 'author'],
    'weights' => ['title_phrase' => 80, 'body_phrase' => 20],
],
```

Or override per query:
```php
->searchWeights(['title_phrase' => 100, 'featured' => 0])
```

## CORS Headers

For cross-origin requests, add CORS headers:

```php
// A tiny helper to apply CORS headers to any Response
$withCors = function (\Ava\Http\Response $response): \Ava\Http\Response {
    return $response->withHeaders([
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, X-API-Key',
    ]);
};

$router->addRoute('/api/posts', function($request, $params) use ($withCors, $app) {
    // Handle OPTIONS preflight
    if ($request->isMethod('OPTIONS')) {
        return $withCors(new \Ava\Http\Response('', 204));
    }

    $posts = $app->repository()->publishedMeta('post');

    return $withCors(\Ava\Http\Response::json([
        'data' => array_map(fn($p) => [
            'title' => $p->title(),
            'slug' => $p->slug(),
        ], $posts),
    ]));
});
```

## Response Helper

Add this helper function to your plugin:

```php
function jsonResponse(mixed $data, int $status = 200): \Ava\Http\Response {
    return \Ava\Http\Response::json($data, $status);
}
```

## Application Instance

The `\Ava\Application` class is the heart of the framework. It acts as a service container and configuration provider. It is typically passed as `$app` to plugin boot closures.

### Core Methods

| Method | Description |
|--------|-------------|
| `config(string $key, $default)` | Get a config value using dot notation (e.g. `site.name`). |
| `path(string $relative)` | Get an absolute filesystem path from the project root. |
| `router()` | Get the `\Ava\Routing\Router` instance. |
| `repository()` | Get the `\Ava\Content\Repository` instance. |
| `query()` | Create a new `\Ava\Content\Query` instance. |
| `loadPlugins()` | Manually load plugins and register their hooks. Useful for custom CLI scripts or external integrations designated to run outside the normal request lifecycle. Note: the CLI calls `loadPlugins()` before running `rebuild` so plugin hooks (including `cli.rebuild`) are registered during CLI rebuilds. |

