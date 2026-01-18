---
title: Creating Plugins
slug: creating-plugins
status: published
meta_title: Creating Plugins | Flat-file PHP CMS | Ava CMS
meta_description: Extend Ava CMS with custom plugins. Learn how to create reusable functionality, register hooks, add CLI commands, and build admin dashboard pages.
excerpt: Plugins let you extend Ava CMS with reusable, shareable functionality that lives outside your theme. Add routes, hooks, CLI commands, and more.
---

Plugins let you extend Ava CMS with reusable, shareable functionality that lives outside your theme.

## Plugins vs theme.php

You might wonder: *"Can't I just put everything in [theme.php](/docs/theming#themephp)?"*

Yes! For simple sites, `theme.php` is often all you need. But plugins are better when:

| Use theme.php | Use a Plugin |
|---------------|--------------|
| Theme-specific features | Features that work with any theme |
| Site customisations | Code you want to share with others |
| Simple hooks and shortcodes | Admin dashboard pages |
| Quick, one-off additions | CLI commands |

**Think of it this way:** If you switch themes, anything in `theme.php` disappears. Plugins survive theme changes because they live in a separate folder.

The [bundled plugins](/docs/bundled-plugins) (sitemap, feed, redirects) are good examples — they work regardless of which theme you use.

## Your First Plugin

1. Create a folder: `app/plugins/my-plugin/`
2. Create a file: `app/plugins/my-plugin/plugin.php`

```php
<?php
return [
    'name' => 'My First Plugin',
    'version' => '1.0',
    
    'boot' => function($app) {
        // Your code runs here when the plugin loads
        
        // Example: Add a custom route
        $app->router()->addRoute('/hello', function() {
            return new \Ava\Http\Response('Hello World!');
        });
    }
];
```

3. Enable it in `app/config/ava.php`:

```php
'plugins' => [
    'sitemap',
    'feed',
    'my-plugin',  // Add your plugin here
],
```

That's it! Visit `/hello` and see your plugin in action.

<details class="beginner-box">
<summary>What is <code>$app</code>?</summary>
<div class="beginner-box-content">

The `$app` object is your gateway to everything in Ava CMS. It's passed to your plugin's `boot` function:

| Method | Returns |
|--------|---------|
| `$app->router()` | Router — add custom routes |
| `$app->repository()` | Content repository — fetch pages and posts |
| `$app->query()` | Content query builder with filtering/pagination |
| `$app->config('key')` | Configuration values |
| `$app->path('relative')` | Absolute file paths |
| `$app->configPath('key')` | Paths from config (e.g., `'storage'`, `'plugins'`) |
| `$app->renderer()` | Template rendering engine |
| `$app->shortcodes()` | Shortcode registration |

See the [API documentation](/docs/api) for detailed usage.

</div>
</details>

## Plugin Structure

```php
<?php
// app/plugins/my-plugin/plugin.php

return [
    // Required
    'name' => 'My Plugin',
    'boot' => function($app) { ... },

    // Recommended metadata
    'version' => '1.0.0',
    'description' => 'What this plugin does',
    'author' => 'Your Name',

    // Optional
    'url' => 'https://example.com/plugin',
    'license' => 'GPLv3',

    // Optional: CLI commands
    'commands' => [ ... ],
];
```

Plugins load in the order listed in `app/config/ava.php`. If plugin B depends on hooks from plugin A, list A first.

## Hooks

Hooks let your code run at specific moments — when content loads, templates render, or the admin builds.

### Filters vs Actions

**Filters** modify data and must return it:

```php
use Ava\Plugins\Hooks;

Hooks::addFilter('render.context', function($context) {
    $context['year'] = date('Y');
    return $context;  // Must return!
});
```

**Actions** react to events without returning data:

```php
Hooks::addAction('indexer.rebuild', function($app) {
    file_put_contents('rebuild.log', date('c') . "\n", FILE_APPEND);
});
```

### Priority

Multiple callbacks run in priority order (lower first, default 10):

```php
Hooks::addFilter('render.context', $earlyCallback, 5);   // Runs first
Hooks::addFilter('render.context', $normalCallback);      // Priority 10
Hooks::addFilter('render.context', $lateCallback, 100);   // Runs last
```

### Available Hooks

| Hook | Type | When it fires |
|------|------|---------------|
| `router.before_match` | Filter | Before routing — return Response to intercept |
| `content.loaded` | Filter | After content item loads from repository |
| `render.context` | Filter | Before template rendering — add template variables |
| `render.output` | Filter | After rendering — modify final HTML |
| `markdown.configure` | Action | When Markdown parser initializes |
| `admin.register_pages` | Filter | When admin pages are registered |
| `indexer.rebuild` | Action | After any content index rebuild |
| `cli.rebuild` | Action | After CLI rebuild command only |

### Hook Examples

**Intercept routing:**
```php
use Ava\Http\Response;

Hooks::addFilter('router.before_match', function($match, $request, $router) {
    if ($request->path() === '/old-page') {
        return Response::redirect('/new-page', 301);
    }
    return $match;
});
```

**Add template variables:**
```php
Hooks::addFilter('render.context', function($context) {
    if (isset($context['content'])) {
        $words = str_word_count(strip_tags($context['content']->rawContent()));
        $context['reading_time'] = max(1, (int) ceil($words / 200));
    }
    return $context;
});
```

**Modify final HTML:**
```php
Hooks::addFilter('render.output', function($output, $templatePath, $context) {
    return str_replace('</body>', '<script src="/tracking.js"></script></body>', $output);
});
```

**Add Markdown extensions:**
```php
use League\CommonMark\Extension\Table\TableExtension;

Hooks::addAction('markdown.configure', function($environment) {
    $environment->addExtension(new TableExtension());
});
```

## Frontend Routes

Create custom public URLs for APIs, landing pages, or dynamic content.

### Basic Route

```php
use Ava\Http\Request;
use Ava\Http\Response;

'boot' => function($app) {
    $app->router()->addRoute('/api/posts', function(Request $request) use ($app) {
        $posts = $app->query()->type('post')->published()->get();
        
        return Response::json([
            'count' => count($posts),
            'posts' => array_map(fn($p) => [
                'title' => $p->title(),
                'slug' => $p->slug(),
                'excerpt' => $p->excerpt(),
            ], $posts),
        ]);
    });
}
```

### URL Parameters

Use `{param}` placeholders:

```php
$router->addRoute('/api/posts/{slug}', function(Request $request, array $params) use ($app) {
    $post = $app->repository()->get('post', $params['slug']);
    
    if (!$post) {
        return Response::json(['error' => 'Not found'], 404);
    }
    
    return Response::json([
        'title' => $post->title(),
        'content' => $post->html(),
    ]);
});
```

### Form Handling

```php
$router->addRoute('/contact', function(Request $request) use ($app) {
    if ($request->isMethod('POST')) {
        $name = $request->post('name', '');
        $email = $request->post('email', '');
        
        if (empty($name) || empty($email)) {
            return Response::json(['error' => 'Name and email required'], 400);
        }
        
        // Save, send email, etc.
        return Response::json(['success' => true]);
    }
    
    return $app->renderer()->render('contact');
});
```

### Prefix Routes

Match any URL starting with a path (checked after exact routes):

```php
$router->addPrefixRoute('/api/', function(Request $request) {
    return Response::json(['error' => 'Endpoint not found'], 404);
});
```

### Response Methods

| Method | Description |
|--------|-------------|
| `Response::json($data, $status)` | JSON response |
| `Response::html($html, $status)` | HTML response |
| `Response::redirect($url, $status)` | Redirect (301, 302, etc.) |
| `Response::text($text, $status)` | Plain text |
| `Response::notFound($message)` | 404 response |

## Admin Pages

Add pages to the admin dashboard. They're automatically protected by authentication.

### Register a Page

```php
use Ava\Plugins\Hooks;
use Ava\Http\Request;
use Ava\Application;

Hooks::addFilter('admin.register_pages', function(array $pages) {
    $pages['my-plugin'] = [
        'label' => 'My Plugin',     // Sidebar text
        'icon' => 'extension',      // Material Symbols icon
        'handler' => function(Request $request, Application $app, $controller) {
            $content = '<div class="card">
                <div class="card-body">Your content here</div>
            </div>';
            
            return $controller->renderPluginPage([
                'title' => 'My Plugin',
                'activePage' => 'my-plugin',
            ], $content);
        },
    ];
    return $pages;
});
```

Plugin pages appear in the "Plugins" section of the sidebar.

**Finding icons:** Browse [Material Symbols](https://fonts.google.com/icons) for icon names.

### renderPluginPage() Options

| Option | Description |
|--------|-------------|
| `title` | Browser tab title |
| `heading` | Page heading (defaults to title) |
| `icon` | Header icon |
| `activePage` | Sidebar item to highlight |
| `headerActions` | HTML for header buttons |
| `alertSuccess` | Green success message |
| `alertError` | Red error message |
| `alertWarning` | Yellow warning message |
| `scripts` | Additional JavaScript |

### Handling Forms

```php
'handler' => function(Request $request, Application $app, $controller) {
    $configFile = $app->path('storage/my-plugin.json');
    $config = file_exists($configFile) 
        ? json_decode(file_get_contents($configFile), true) 
        : [];
    
    $success = null;
    $error = null;
    
    if ($request->isMethod('POST')) {
        if (!$controller->auth()->verifyCsrf($request->post('_csrf', ''))) {
            $error = 'Invalid request.';
        } else {
            $config['api_key'] = $request->post('api_key', '');
            file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
            $success = 'Settings saved!';
            $controller->auth()->regenerateCsrf();
        }
    }
    
    $csrf = $controller->auth()->csrfToken();
    
    $content = '<div class="card">
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="_csrf" value="' . $csrf . '">
                <div class="form-group">
                    <label>API Key</label>
                    <input type="text" name="api_key" value="' . htmlspecialchars($config['api_key'] ?? '') . '" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>';
    
    return $controller->renderPluginPage([
        'title' => 'Settings',
        'activePage' => 'my-plugin',
        'alertSuccess' => $success,
        'alertError' => $error,
    ], $content);
},
```

### Available CSS Classes

| Class | Use for |
|-------|---------|
| `.card`, `.card-header`, `.card-body` | Content cards |
| `.stat-grid`, `.stat-card`, `.stat-label`, `.stat-value` | Statistics display |
| `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-sm` | Buttons |
| `.badge`, `.badge-success`, `.badge-warning`, `.badge-muted` | Status indicators |
| `.alert`, `.alert-success`, `.alert-danger`, `.alert-warning` | Messages |
| `.table`, `.table-wrap` | Data tables |
| `.form-group`, `.form-control` | Form elements |

## CLI Commands

Add commands that appear in `./ava help`.

```php
return [
    'name' => 'My Plugin',
    
    'commands' => [
        [
            'name' => 'myplugin:status',
            'description' => 'Show plugin status',
            'handler' => function(array $args, $cli, \Ava\Application $app) {
                $cli->header('My Plugin Status');
                $cli->success('Everything is working!');
                return 0;
            },
        ],
    ],
];
```

### Handler Parameters

| Parameter | Description |
|-----------|-------------|
| `$args` | Arguments after command name |
| `$cli` | CLI output helper |
| `$app` | Application instance |

### Output Methods

```php
$cli->header('Section Title');      // Bold header
$cli->info('Note');                 // ℹ Cyan
$cli->success('Done!');             // ✓ Green
$cli->warning('Careful');           // ⚠ Yellow
$cli->error('Failed');              // ✗ Red
$cli->writeln('Plain text');
$cli->table(['Col1', 'Col2'], [['a', 'b'], ['c', 'd']]);

// Text formatting (returns string)
$cli->bold('text');
$cli->dim('text');
$cli->green('text');
$cli->yellow('text');
$cli->red('text');
```

### Return Values

Return `0` for success, `1` or higher for errors.

## Plugin Assets

### Admin Assets

Serve CSS/JS for admin pages via `/admin-assets/{plugin-name}/{file}`:

```php
'handler' => function($request, $app, $controller) {
    $pluginPath = $app->configPath('plugins') . '/my-plugin/assets';
    $cssFile = $pluginPath . '/styles.css';
    $cssUrl = '/admin-assets/my-plugin/styles.css';
    if (file_exists($cssFile)) {
        $cssUrl .= '?v=' . filemtime($cssFile);
    }
    
    $content = '<link rel="stylesheet" href="' . $cssUrl . '">
        <div class="card">...</div>';
    
    return $controller->renderPluginPage([...], $content);
},
```

Assets are cached with `max-age=31536000`, so always use `?v={timestamp}` for cache busting.

**Supported types:** CSS, JS, JSON, images (SVG, PNG, JPG, GIF, WebP, ICO), fonts (WOFF, WOFF2, TTF, EOT).

### Frontend Assets

Add to template context, then include in your theme's `<head>`:

```php
// In plugin
Hooks::addFilter('render.context', function($context) {
    $context['plugin_assets'][] = '/app/plugins/my-plugin/assets/style.css';
    return $context;
});
```

```php
<!-- In theme layout -->
<?php foreach ($plugin_assets ?? [] as $asset): ?>
    <?php if (str_ends_with($asset, '.css')): ?>
        <link rel="stylesheet" href="<?= $asset ?>">
    <?php else: ?>
        <script src="<?= $asset ?>"></script>
    <?php endif; ?>
<?php endforeach; ?>
```

## Shortcodes

Plugins can register custom [shortcodes](/docs/shortcodes):

```php
$app->shortcodes()->register('greeting', function(array $attrs, ?string $content) {
    $name = $attrs['name'] ?? 'friend';
    return "Hello, " . htmlspecialchars($name) . "!";
});
```

See the [Shortcodes documentation](/docs/shortcodes) for full details.

## Complete Example

A plugin with admin page, CLI command, and frontend route:

```php
<?php
// app/plugins/link-checker/plugin.php

use Ava\Plugins\Hooks;
use Ava\Http\Request;
use Ava\Http\Response;
use Ava\Application;

return [
    'name' => 'Link Checker',
    'version' => '1.0.0',
    'description' => 'Scans content for broken internal links',
    
    'boot' => function($app) {
        // Admin page
        Hooks::addFilter('admin.register_pages', function(array $pages) {
            $pages['link-checker'] = [
                'label' => 'Link Checker',
                'icon' => 'link_off',
                'handler' => function(Request $request, Application $app, $controller) {
                    $broken = findBrokenLinks($app);
                    
                    if (empty($broken)) {
                        $content = '<div class="card"><div class="card-body" style="text-align:center;padding:3rem">
                            <span class="material-symbols-rounded" style="font-size:3rem;color:var(--success)">check_circle</span>
                            <h3>No Broken Links</h3>
                        </div></div>';
                    } else {
                        $rows = implode('', array_map(fn($l) => 
                            '<tr><td>' . htmlspecialchars($l['page']) . '</td><td><code>' . htmlspecialchars($l['url']) . '</code></td></tr>', 
                            $broken
                        ));
                        $content = '<div class="card"><div class="table-wrap">
                            <table class="table"><thead><tr><th>Page</th><th>Broken Link</th></tr></thead>
                            <tbody>' . $rows . '</tbody></table>
                        </div></div>';
                    }
                    
                    return $controller->renderPluginPage([
                        'title' => 'Link Checker',
                        'icon' => 'link_off',
                        'activePage' => 'link-checker',
                    ], $content);
                },
            ];
            return $pages;
        });
        
        // JSON API endpoint
        $app->router()->addRoute('/api/broken-links', function() use ($app) {
            return Response::json(findBrokenLinks($app));
        });
    },
    
    'commands' => [
        [
            'name' => 'links:check',
            'description' => 'Check for broken internal links',
            'handler' => function($args, $cli, $app) {
                $cli->header('Checking Links');
                $broken = findBrokenLinks($app);
                
                foreach ($broken as $link) {
                    $cli->writeln('  ' . $cli->red('✗') . ' ' . $link['page'] . ': ' . $link['url']);
                }
                
                if (empty($broken)) {
                    $cli->success('All links valid!');
                    return 0;
                }
                
                $cli->error('Found ' . count($broken) . ' broken link(s)');
                return 1;
            },
        ],
    ],
];

function findBrokenLinks($app): array {
    $repo = $app->repository();
    $validPaths = array_keys($repo->routes()['exact'] ?? []);
    $broken = [];
    
    foreach ($repo->types() as $type) {
        foreach ($repo->published($type) as $item) {
            preg_match_all('/\[([^\]]+)\]\(([^)]+)\)/', $item->rawContent(), $matches);
            foreach ($matches[2] as $url) {
                if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
                    $path = strtok($url, '#?');
                    if (!in_array($path, $validPaths) && $path !== '/') {
                        $broken[] = ['page' => $item->title(), 'url' => $url];
                    }
                }
            }
        }
    }
    return $broken;
}
```

## Next Steps

- Browse [bundled plugins](/docs/bundled-plugins) for real examples
- See the [API reference](/docs/api) for all available methods
- Read the [CLI reference](/docs/cli) for command details
- Learn about [shortcodes](/docs/shortcodes) for content extensions
