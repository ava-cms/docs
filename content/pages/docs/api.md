---
title: API
slug: api
status: published
meta_title: Building APIs | Flat-file PHP CMS | Ava CMS
meta_description: Build custom JSON APIs with Ava CMS. Learn about routing, Request and Response helpers, and how to create headless CMS endpoints for your applications.
excerpt: Ava CMS gives you the tools to build exactly the API you need—a router, Request/Response helpers, and content access via the Repository. Perfect for headless CMS setups.
---

Ava CMS doesn't force a specific API structure on you. Instead, it gives you the building blocks to create exactly the API you need—whether that's a simple read-only JSON endpoint or a full REST API.

**What you get out of the box:**

- **Router** — Register custom routes via `$app->router()`
- **Request wrapper** — `\Ava\Http\Request` for accessing request data
- **Response builder** — `\Ava\Http\Response` for sending JSON, HTML, or redirects
- **Content access** — `\Ava\Content\Repository` and `\Ava\Content\Query` for fetching content

If you want a "headless CMS" with JSON endpoints, you typically implement it as a plugin that registers routes and returns JSON responses. This guide will show you how.

## Quick Start

Here's a minimal example to get you started. [Create a plugin](https://ava.addy.zone/docs/creating-plugins) that exposes a simple JSON endpoint:

```php
// app/plugins/my-api/plugin.php

return [
    'name' => 'My API',
    'boot' => function($app) {
        $router = $app->router();
        
        // Simple endpoint: /api/hello
        $router->addRoute('/api/hello', function($request, $params) {
            return \Ava\Http\Response::json([
                'message' => 'Hello from Ava CMS!',
                'timestamp' => date('Y-m-d H:i:s'),
            ]);
        });
        
        // List all posts: /api/posts
        $router->addRoute('/api/posts', function($request, $params) use ($app) {
            $posts = $app->repository()->publishedMeta('post');
            
            return \Ava\Http\Response::json([
                'count' => count($posts),
                'data' => array_map(fn($p) => [
                    'title' => $p->title(),
                    'slug' => $p->slug(),
                    'url' => $p->url(),
                    'date' => $p->date()?->format('Y-m-d'),
                ], $posts),
            ]);
        });
    }
];
```

Activate your plugin in your config file and visit `/api/hello` or `/api/posts` to see your API in action! The rest of this guide explains the concepts in detail.

## Routing Basics

### How matching works

The router matches requests in a specific order:

1. **Hook interception** — Plugins can intercept via `router.before_match` filter
2. **Trailing slash redirect** — Canonical URL enforcement (based on config)
3. **Redirects** — From `redirect_from` frontmatter in content
4. **System routes** — Routes registered via `addRoute(...)`
5. **Exact content routes** — From the content index (published content)
6. **Preview matching** — For draft content (requires preview token)
7. **Prefix routes** — Routes registered via `addPrefixRoute(...)`
8. **Taxonomy routes** — Taxonomy index and term pages
9. **404** — No match found

### Route handlers

Routes registered via `$router->addRoute(...)` and `$router->addPrefixRoute(...)` receive a handler function with this signature:

```php
function(\Ava\Http\Request $request, array $params): \Ava\Routing\RouteMatch|\Ava\Http\Response|null
```

**Parameters:**
- `$request` — The current HTTP request
- `$params` — URL parameters captured from route (e.g., `{slug}` becomes `$params['slug']`)

**Return values:**
- `Response` — Ava sends this directly to the client (most common for APIs)
- `RouteMatch` — Ava treats it as a normal page match and renders a template
- `null` — Continue to the next route in the matching order

For JSON API endpoints, always return a `Response` object.

## Request & Response Helpers

### Request

The `\Ava\Http\Request` class provides access to the current HTTP request. Here are the most commonly used methods in API endpoints:

**Method Information:**
- `$request->method()` — Get the HTTP method (GET, POST, etc.)
- `$request->isMethod('POST')` — Check if request uses a specific method
- `$request->path()` — Get the URL path (e.g., `/api/posts`)
- `$request->uri()` — Get the full URI including query string

**Request Data:**
- `$request->query('key', $default)` — Get a query parameter (or all with no args)
- `$request->post('key', $default)` — Get a POST parameter (or all with no args)
- `$request->body()` — Get the raw request body (lazily loaded)

**Headers:**
- `$request->header('X-Api-Key')` — Get a header (case-insensitive)
- `$request->headers()` — Get all headers as an array
- `$request->expectsJson()` — Check if `Accept: application/json`

**URL Information:**
- `$request->host()` — Get the host name
- `$request->fullUrl()` — Get the complete URL
- `$request->isSecure()` — Check if HTTPS
- `$request->isLocalhost()` — Check if request is from localhost

### Response

The `\Ava\Http\Response` class helps you build HTTP responses. Here are the available methods:

**Creating Responses:**
- `Response::json($data, $status = 200)` — Create a JSON response
- `Response::text($string, $status = 200)` — Create a plain text response
- `Response::html($string, $status = 200)` — Create an HTML response
- `Response::redirect($url, $status = 302)` — Create a redirect response
- `Response::notFound($content = 'Not Found')` — Create a 404 response

**Modifying Responses (immutable):**

All modification methods return a new Response instance:

- `->withHeader($name, $value)` — Add a single header
- `->withHeaders($array)` — Add multiple headers
- `->withStatus($code)` — Set the status code
- `->withContent($string)` — Set the response content

**Checking Response Status:**
- `->isSuccessful()` — Check if status is 2xx
- `->isRedirect()` — Check if status is 3xx
- `->isClientError()` — Check if status is 4xx
- `->isServerError()` — Check if status is 5xx

You can chain modifications immutably:

```php
return \Ava\Http\Response::json(['ok' => true])
    ->withHeader('Cache-Control', 'no-store')
    ->withHeader('X-Custom', 'value');
```

## Building a JSON API

Since Ava CMS is just PHP, you can easily create endpoints that return JSON. This is great if you want to use Ava CMS as a headless CMS for a mobile app or a JavaScript frontend.

### Example: A Simple Read-Only API

You can create a plugin to expose your content as JSON. This example shows how to build a simple read-only API for your blog posts.

```php
// app/plugins/json-api/plugin.php

return [
    'name' => 'JSON API',
    'boot' => function($app) {
        $router = $app->router();
        
        // Endpoint: /api/posts
        $router->addRoute('/api/posts', function($request, $params) use ($app) {
            $repo = $app->repository();
            
            // Get published posts (metadata only, no file I/O)
            // For better performance, use publishedMeta() instead of published()
            // when you don't need the full content body
            $posts = $repo->publishedMeta('post');
            
            // Return JSON response
            return \Ava\Http\Response::json([
                'data' => array_map(fn($p) => [
                    'title' => $p->title(),
                    'slug' => $p->slug(),
                    'url' => $p->url(),
                    'date' => $p->date()?->format('Y-m-d'),
                    'excerpt' => $p->excerpt(),
                ], $posts)
            ]);
        });
    }
];
```

Now, visiting `/api/posts` will give you a clean JSON list of your blog posts.

<div class="callout-info">
<strong>Performance tip:</strong> Use <code>publishedMeta()</code> instead of <code>published()</code> when you only need frontmatter data. It avoids reading content files from disk, making your API much faster.
</div>

## Building Custom Endpoints

All routes in Ava CMS are handled through the router:

- `addRoute('/path', $handler)` registers an exact path, with optional `{param}` placeholders.
- `addPrefixRoute('/prefix/', $handler)` registers a handler for *all* paths under a prefix.

Handlers are called with:

```php
function(\Ava\Http\Request $request, array $params): \Ava\Routing\RouteMatch|\Ava\Http\Response|null
```

In most cases, the simplest thing to do is just return a `\Ava\Http\Response`.

### Basic Route

The simplest API endpoint returns a static JSON response:

```php
// Register in your plugin's boot function
$router->addRoute('/api/custom', function($request, $params) {
    return \Ava\Http\Response::json([
        'message' => 'Hello from API!',
        'timestamp' => time(),
    ]);
});
```

### Route with Parameters

Use `{param}` placeholders in your route path to capture URL segments:

```php
// Route: /api/content/{type}/{slug}
// Matches: /api/content/post/hello-world
$router->addRoute('/api/content/{type}/{slug}', function($request, $params) use ($app) {
    // $params contains: ['type' => 'post', 'slug' => 'hello-world']
    $type = $params['type'];
    $slug = $params['slug'];
    
    $repo = $app->repository();
    $item = $repo->get($type, $slug);
    
    if ($item === null) {
        return \Ava\Http\Response::json(['error' => 'Not found'], 404);
    }

    // Return the content item as JSON
    return \Ava\Http\Response::json([
        'type' => $item->type(),
        'title' => $item->title(),
        'slug' => $item->slug(),
        'url' => $item->url(),
        'date' => $item->date()?->format('Y-m-d'),
        'excerpt' => $item->excerpt(),
    ]);
});
```

### Query Parameters

Access query string parameters using `$request->query()`:

```php
// Route: /api/search
// URL: /api/search?q=hello&limit=20
$router->addRoute('/api/search', function($request, $params) {
    // Get query parameters with defaults
    $query = $request->query('q', '');
    $limit = (int) $request->query('limit', 10);
    
    // Validate and sanitize
    $limit = max(1, min(100, $limit)); // Cap between 1 and 100
    
    // Perform search...
    return \Ava\Http\Response::json([
        'query' => $query,
        'limit' => $limit,
        'results' => [], // Your search results here
    ]);
});
```

**Tips:**
- `$request->query()` with no arguments returns all query parameters as an array
- Always provide sensible defaults
- Validate and sanitize user input

### Prefix Routes

Prefix routes match all URLs under a specific path. This is useful for creating API versioning or catching multiple related endpoints:

```php
// Handle all routes under /api/v2/
$router->addPrefixRoute('/api/v2/', function($request, $params) {
    $path = $request->path();

    // Match specific endpoints
    if ($path === '/api/v2/ping') {
        return \Ava\Http\Response::json(['ok' => true]);
    }
    
    if ($path === '/api/v2/status') {
        return \Ava\Http\Response::json([
            'status' => 'healthy',
            'version' => '2.0',
        ]);
    }

    // Default 404 for unmatched paths
    return \Ava\Http\Response::json(['error' => 'Not found'], 404);
});
```

<div class="callout-info">
<strong>When to use prefix routes:</strong> Use <code>addPrefixRoute()</code> when you have multiple related endpoints under a common path. For single endpoints, use <code>addRoute()</code> instead.
</div>

## Authentication

Ava CMS doesn't include built-in API authentication, giving you flexibility to implement the approach that fits your needs. Here are common patterns:

### API Key Authentication

API keys are the simplest approach for machine-to-machine authentication:

```php
// In your plugin's boot function:
'boot' => function($app) {
    $router = $app->router();
    
    // Helper function to authenticate requests
    $authenticateRequest = function($request) use ($app): bool {
        // Check for API key in header or query string
        $apiKey = $request->header('X-API-Key') 
               ?? $request->query('api_key');
        
        // Get valid keys from config
        $validKeys = $app->config('api.keys', []);
        
        return in_array($apiKey, $validKeys, true);
    };

    $router->addRoute('/api/private', function($request, $params) use ($authenticateRequest) {
        // Check authentication
        if (!$authenticateRequest($request)) {
            return \Ava\Http\Response::json([
                'error' => 'Unauthorized',
                'message' => 'Valid API key required'
            ], 401);
        }
        
        // Handle authenticated request...
        return \Ava\Http\Response::json(['ok' => true]);
    });
};
```

### Config for API Keys

Store your API keys in `app/config/ava.php`:

```php
// app/config/ava.php
return [
    // ...other config
    'api' => [
        'keys' => [
            'your-secret-api-key-here',
            // Generate with: bin2hex(random_bytes(32))
        ],
    ],
];
```

<div class="callout-warning">
<strong>Security note:</strong> Never commit API keys to version control. Use environment variables or a separate config file that's in your <code>.gitignore</code>.
</div>

### Other Authentication Options

- **Bearer tokens** — Use `Authorization: Bearer <token>` header
- **Basic Auth** — Use `Authorization: Basic <credentials>` header  
- **JWT tokens** — For stateless authentication with expiry
- **OAuth** — For third-party integrations

The implementation pattern is similar—extract credentials from the request and validate them before processing.

## Pagination

Ava's Query builder includes built-in pagination. Here's a complete example:

```php
$router->addRoute('/api/posts', function($request, $params) use ($app) {
    // Get pagination parameters from query string
    $page = (int) $request->query('page', 1);
    $perPage = (int) $request->query('per_page', 10);

    // Build and execute query
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
            'date' => $p->date()?->format('Y-m-d'),
        ], $items),
        'pagination' => $query->pagination(),
    ]);
});
```

**Pagination info structure:**

The `$query->pagination()` method returns:

```php
[
    'current_page' => 1,      // Current page number
    'per_page' => 10,         // Items per page
    'total' => 50,            // Total items (before pagination)
    'total_pages' => 5,       // Total number of pages
    'has_more' => true,       // Whether there are more pages
    'has_previous' => false,  // Whether there are previous pages
]
```

## Taxonomy Endpoints

Expose your taxonomies (categories, tags, etc.) via API endpoints:

```php
// List all categories
$router->addRoute('/api/categories', function($request, $params) use ($app) {
    $repo = $app->repository();
    $terms = $repo->terms('category');
    
    return \Ava\Http\Response::json([
        'data' => array_map(fn($term) => [
            'name' => $term,
            'slug' => \Ava\Support\Str::slug($term),
            'url' => '/category/' . \Ava\Support\Str::slug($term),
        ], $terms)
    ]);
});

// Get posts by category
$router->addRoute('/api/categories/{slug}/posts', function($request, $params) use ($app) {
    $slug = $params['slug'];
    
    $posts = $app->query()
        ->type('post')
        ->published()
        ->whereTax('category', $slug)
        ->orderBy('date', 'desc')
        ->get();
    
    if (empty($posts)) {
        return \Ava\Http\Response::json([
            'error' => 'Category not found or has no posts',
        ], 404);
    }
    
    return \Ava\Http\Response::json([
        'category' => $slug,
        'data' => array_map(fn($p) => [
            'title' => $p->title(),
            'slug' => $p->slug(),
            'url' => $p->url(),
            'date' => $p->date()?->format('Y-m-d'),
        ], $posts)
    ]);
});
```

## Search Endpoint

The `\Ava\Content\Query` class includes built-in full-text search with relevance scoring:

```php
$router->addRoute('/api/search', function($request, $params) use ($app) {
    $query = trim($request->query('q', ''));
    
    // Validate minimum query length
    if (strlen($query) < 2) {
        return \Ava\Http\Response::json([
            'results' => [],
            'message' => 'Query too short (minimum 2 characters)',
            'count' => 0,
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
            'url' => $item->url(),
            'excerpt' => $item->excerpt(),
            'date' => $item->date()?->format('Y-m-d'),
        ], $results),
        'pagination' => $searchQuery->pagination(),
    ]);
});
```

### Search Configuration

Search automatically scores results by matching in:
- Title (phrase and individual words)
- Excerpt  
- Body content
- Featured item boost

Configure per content type in `app/config/content_types.php`:

```php
'post' => [
    // ...other config
    'search' => [
        'fields' => ['title', 'excerpt', 'body', 'author'],
        'weights' => [
            'title_phrase' => 80,  // Exact phrase in title
            'body_phrase' => 20,   // Exact phrase in body
            'featured' => 15,      // Boost for featured items
        ],
    ],
],
```

Or override weights per query:

```php
->searchWeights([
    'title_phrase' => 100,
    'excerpt_phrase' => 50,
    'featured' => 0,
])
```

## CORS for Cross-Origin Requests

If your API needs to be accessed from a different domain (e.g., a JavaScript frontend), you'll need to add CORS headers. Here's a complete example with preflight handling:

```php
// Helper to apply CORS headers
$withCors = function (\Ava\Http\Response $response): \Ava\Http\Response {
    return $response->withHeaders([
        // Be specific about allowed origins in production
        'Access-Control-Allow-Origin' => 'https://yourdomain.com',
        'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, X-API-Key',
    ]);
};

$router->addRoute('/api/posts', function($request, $params) use ($withCors, $app) {
    // Handle OPTIONS preflight request
    if ($request->isMethod('OPTIONS')) {
        return $withCors(new \Ava\Http\Response('', 204));
    }

    $posts = $app->repository()->publishedMeta('post');

    return $withCors(\Ava\Http\Response::json([
        'data' => array_map(fn($p) => [
            'title' => $p->title(),
            'slug' => $p->slug(),
            'url' => $p->url(),
        ], $posts),
    ]));
});
```

<div class="callout-warning">
<strong>Security tip:</strong> In production, never use <code>'Access-Control-Allow-Origin' => '*'</code>. Always specify the exact origin(s) that should have access to your API.
</div>

## Error Handling

Proper error handling makes your API more reliable and easier to debug. Here are some best practices:

### Consistent Error Format

Use a consistent JSON structure for all errors:

```php
function errorResponse(string $message, int $status = 400, ?string $code = null): \Ava\Http\Response {
    $error = ['error' => $message];
    
    if ($code !== null) {
        $error['code'] = $code;
    }
    
    return \Ava\Http\Response::json($error, $status);
}
```

### Validation Errors

Provide specific validation feedback:

```php
$router->addRoute('/api/posts', function($request, $params) use ($app) {
    if (!$request->isMethod('POST')) {
        return errorResponse('Method not allowed', 405);
    }
    
    // Validate required fields
    $title = $request->post('title');
    if (empty($title)) {
        return \Ava\Http\Response::json([
            'error' => 'Validation failed',
            'errors' => [
                'title' => 'Title is required'
            ]
        ], 422);
    }
    
    // Process the request...
});
```

### Handling Exceptions

Wrap potentially failing operations in try-catch blocks:

```php
$router->addRoute('/api/data', function($request, $params) {
    try {
        // Your code that might throw exceptions
        $data = someRiskyOperation();
        
        return \Ava\Http\Response::json(['data' => $data]);
    } catch (\Exception $e) {
        // Log the error for debugging
        error_log('API error: ' . $e->getMessage());
        
        // Return a safe error message to the client
        return \Ava\Http\Response::json([
            'error' => 'Internal server error',
            'message' => 'An unexpected error occurred'
        ], 500);
    }
});
```

### Common HTTP Status Codes

Use appropriate status codes:

- `200` — Success
- `201` — Created (for POST requests that create resources)
- `400` — Bad request (invalid input)
- `401` — Unauthorized (authentication required)
- `403` — Forbidden (authenticated but not permitted)
- `404` — Not found
- `422` — Unprocessable entity (validation errors)
- `429` — Too many requests (rate limiting)
- `500` — Internal server error

## Best Practices

### 1. Use Descriptive Endpoints

Make your API intuitive:

```php
// Good
/api/posts
/api/posts/{slug}
/api/categories/{slug}/posts

// Avoid
/api/p
/api/get-post
/api/data?type=post
```

### 2. Version Your API

Plan for future changes:

```php
// Use prefix routes for versioning
$router->addPrefixRoute('/api/v1/', function($request, $params) {
    // v1 endpoints
});

$router->addPrefixRoute('/api/v2/', function($request, $params) {
    // v2 endpoints with breaking changes
});
```

### 3. Optimize Performance

- Use `publishedMeta()` instead of `published()` when you don't need content bodies
- Implement caching for expensive queries
- Set reasonable pagination limits
- Consider adding rate limiting for public APIs

### 4. Document Your API

Add API documentation to help users:

- List all available endpoints
- Document request/response formats
- Provide example requests
- Explain authentication requirements
- Note any rate limits or restrictions

### 5. Add CORS Correctly

Only enable CORS for endpoints that need it:

```php
// Apply CORS only to specific API routes
$withCors = function (\Ava\Http\Response $response): \Ava\Http\Response {
    return $response->withHeaders([
        'Access-Control-Allow-Origin' => 'https://yourdomain.com', // Be specific!
        'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, X-API-Key',
    ]);
};

// Apply to specific routes
$router->addRoute('/api/public', function($request, $params) use ($withCors) {
    $response = \Ava\Http\Response::json(['data' => 'public']);
    return $withCors($response);
});
```

## Application Instance

The `\Ava\Application` class is the heart of the framework. It acts as a service container and configuration provider. It is typically passed as `$app` to plugin boot closures.

### Core Methods

| Method | Description |
|--------|-------------|
| `config(string $key, $default)` | Get a config value using dot notation (e.g., `'site.name'`). Returns the default if not found. |
| `allConfig()` | Get the full configuration array. |
| `path(string $relative)` | Get an absolute filesystem path from the project root. |
| `configPath(string $key)` | Get a configured path (from `paths.*` in config) as an absolute path. |
| `router()` | Get the `\Ava\Routing\Router` instance for registering routes. |
| `repository()` | Get the `\Ava\Content\Repository` instance for accessing content. |
| `query()` | Create a new `\Ava\Content\Query` instance for querying content. |
| `loadPlugins()` | Manually load plugins and register their hooks. Useful for custom CLI scripts or external integrations that run outside the normal request lifecycle. |

