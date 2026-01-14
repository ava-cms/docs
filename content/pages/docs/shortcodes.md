---
title: Shortcodes
slug: shortcodes
status: published
meta_title: Shortcodes | Flat-file PHP CMS | Ava CMS
meta_description: Add dynamic content to Markdown with shortcodes. Built-in tags for dates, site info, and email obfuscation, plus custom shortcodes and PHP snippets.
excerpt: Shortcodes let you add dynamic content to your Markdown without writing raw HTML. Simple tags in square brackets get replaced when the page renders.
---

Shortcodes let you add dynamic content to your Markdown without writing raw HTML. They're simple tags in square brackets that get replaced when the page renders.

## How They Work

Shortcodes come in two forms:

<pre><code class="language-markdown">&lt;!-- Self-closing --&gt;
Copyright © &#91;&#8203;year&#93; &#91;&#8203;site_name&#93;

&lt;!-- Paired (with content between) --&gt;
&#91;&#8203;email&#93;hello@example.com&#91;/&#8203;email&#93;
</code></pre>

When rendered, <code>&#91;&#8203;year&#93;</code> becomes the current year, <code>&#91;&#8203;site_name&#93;</code> becomes your site name, and <code>&#91;&#8203;email&#93;...&#91;/&#8203;email&#93;</code> creates a spam-protected mailto link.

## Built-in Shortcodes

| Shortcode | Output |
|-----------|--------|
| <code>&#91;&#8203;year&#93;</code> | Current year |
| <code>&#91;&#8203;date format=&quot;Y-m-d&quot;&#93;</code> | Current date (optionally formatted) |
| <code>&#91;&#8203;site_name&#93;</code> | Site name from config |
| <code>&#91;&#8203;site_url&#93;</code> | Site URL from config |
| <code>&#91;&#8203;email&#93;you@example.com&#91;/&#8203;email&#93;</code> | Obfuscated mailto link |
| <code>&#91;&#8203;snippet name="file"&#93;</code> | Renders <code>app/snippets/file.php</code> |

## Creating Custom Shortcodes

Register shortcodes in your `theme.php`:

```php
<?php
// app/themes/yourtheme/theme.php

use Ava\Application;

return function (Application $app): void {
    $shortcodes = $app->shortcodes();

    // Self-closing shortcode
    $shortcodes->register('greeting', function (array $attrs, ?string $content, string $tag) {
        $name = $attrs['name'] ?? 'friend';
        return "Hello, " . htmlspecialchars($name) . "!";
    });

    // Paired shortcode (receives content)
    $shortcodes->register('highlight', function (array $attrs, ?string $content, string $tag) {
        $color = $attrs['color'] ?? 'yellow';
        $inner = $content ?? '';
        return '<mark style="background:' . htmlspecialchars($color) . '">' . $inner . '</mark>';
    });
};
```

Usage:

<pre><code class="language-markdown">&#91;&#8203;greeting name=&quot;Alice&quot;&#93;

&#91;&#8203;highlight color=&quot;#ffeeba&quot;&#93;This text is highlighted.&#91;/&#8203;highlight&#93;
</code></pre>

<div class="callout-info">
<strong>Heads up:</strong> Ava processes shortcodes after Markdown, so shortcodes can run even inside code blocks/tables. This page inserts an invisible character after <code>[</code> in examples so they display correctly without executing.
</div>

### Shortcode Callback Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$attrs` | `array` | All attributes passed to the shortcode |
| `$content` | `?string` | Content between opening/closing tags (null when not provided) |
| `$tag` | `string` | Normalized shortcode tag name (lowercase) |

If your callback only needs `$attrs`, you can still declare fewer parameters — PHP will ignore the extra arguments Ava passes.

### Attributes

Attribute parsing supports:

- `key="value"`
- `key='value'`
- `key=value`
- Boolean flags: `key` (sets `$attrs['key'] = true`)

### Best Practices

- **Escape output** — Always use `htmlspecialchars()` for user-provided values to prevent XSS
- **Return strings** — Return a string (or `null`, which becomes an empty string)
- **Keep it simple** — Complex shortcodes are better as snippets (see below)
- **Name carefully** — Shortcode names are case-insensitive, use underscores for multi-word names

## Snippets: Reusable PHP Components

For more complex components, use snippets. A snippet is a PHP file in your `app/snippets/` folder that you invoke with the <code>&#91;&#8203;snippet&#93;</code> shortcode.

**When to use snippets vs shortcodes:**

| Use Shortcodes | Use Snippets |
|----------------|--------------|
| Simple text replacements | Complex HTML structures |
| No external files needed | Reusable across sites |
| 1-5 lines of code | Need full PHP file with logic |
| Site-specific utilities | Component libraries |

### Creating a Snippet

```php
<?php // app/snippets/cta.php ?>
<?php
$heading = $params['heading'] ?? 'Ready to get started?';
$button = $params['button'] ?? 'Learn More';
$url = $params['url'] ?? '/contact';
?>
<div class="cta-box">
    <h3><?= htmlspecialchars($heading) ?></h3>
    <p><?= $content ?></p>
    <a href="<?= htmlspecialchars($url) ?>" class="button">
        <?= htmlspecialchars($button) ?>
    </a>
</div>
```

### Using a Snippet

<pre><code class="language-markdown">&#91;&#8203;snippet name=&quot;cta&quot; heading=&quot;Join Our Newsletter&quot; button=&quot;Subscribe&quot; url=&quot;/subscribe&quot;&#93;
Get weekly tips delivered to your inbox.
&#91;/&#8203;snippet&#93;
</code></pre>

### Variables in Snippets

| Variable | Description |
|----------|-------------|
| `$content` | Text between opening/closing tags |
| `$params` | Array of all attributes (e.g., `$params['heading']`) |
| `$ava` | Rendering engine instance (`Ava\Rendering\Engine`) |
| `$app` | Application instance |

### Example Snippets

**YouTube Embed:**
```php
<?php // app/snippets/youtube.php ?>
<?php $id = $params['id'] ?? ''; ?>
<div class="video-embed" style="aspect-ratio: 16/9;">
    <iframe src="https://www.youtube.com/embed/<?= htmlspecialchars($id) ?>" 
            frameborder="0" allowfullscreen style="width:100%;height:100%;"></iframe>
</div>
```

Usage: <code>&#91;&#8203;snippet name=&quot;youtube&quot; id=&quot;dQw4w9WgXcQ&quot;&#93;</code>

**Notice Box:**
```php
<?php // app/snippets/notice.php ?>
<?php
$type = $params['type'] ?? 'info';
$icons = ['info' => '💡', 'warning' => '⚠️', 'success' => '✅', 'error' => '❌'];
$icon = $icons[$type] ?? '💡';
?>
<div class="notice notice-<?= htmlspecialchars($type) ?>">
    <span><?= $icon ?></span>
    <div><?= $content ?></div>
</div>
```

Usage:
<pre><code class="language-markdown">&#91;&#8203;snippet name=&quot;notice&quot; type=&quot;warning&quot;&#93;
This feature is experimental.
&#91;/&#8203;snippet&#93;
</code></pre>

**Pricing Card:**
```php
<?php // app/snippets/pricing.php ?>
<?php
$plan = $params['plan'] ?? 'Plan';
$price = $params['price'] ?? '$0';
$features = $params['features'] ?? '';
$url = $params['url'] ?? '#';
?>
<div class="pricing-card">
    <h3><?= htmlspecialchars($plan) ?></h3>
    <div class="price"><?= htmlspecialchars($price) ?><span>/month</span></div>
    <ul>
        <?php foreach (explode(',', $features) as $feature): ?>
            <li><?= htmlspecialchars(trim($feature)) ?></li>
        <?php endforeach; ?>
    </ul>
    <a href="<?= htmlspecialchars($url) ?>" class="button">Get Started</a>
</div>
```

Usage: <code>&#91;&#8203;snippet name=&quot;pricing&quot; plan=&quot;Pro&quot; price=&quot;$29&quot; features=&quot;Unlimited projects, Priority support, API access&quot;&#93;</code>

## How Processing Works

1. Markdown is converted to HTML
2. Shortcodes are processed in the HTML output
3. Path aliases are expanded
4. Result is sent to the browser

Since shortcodes run after Markdown processing, they can safely output raw HTML.

## Limitations (v1)

- **No nested shortcodes** — shortcodes are not processed inside other shortcodes.
- **Paired content cannot contain `[`** — the current parser stops paired shortcode content at the next `[` character. If you need rich/nested markup, prefer a snippet.

## Security

- **Path safety:** Snippet names can't contain `..` or `/` — no directory traversal
- **Disable snippets:** Set `security.shortcodes.allow_php_snippets` to `false`
- **Unknown shortcodes:** Left as-is in output (no errors)
- **Errors:** Exceptions are logged and replaced with an HTML comment
- **Escaping:** Always use `htmlspecialchars()` for user values
