---
title: Bundled Plugins
slug: bundled-plugins
status: published
meta_title: Bundled Plugins | Flat-file PHP CMS | Ava CMS
meta_description: Ava CMS comes with sitemap, RSS feed, and redirects plugins included. Learn how to configure and use these bundled plugins for SEO and content management.
excerpt: Ava CMS comes with helpful plugins for common tasks—sitemap generation for SEO, RSS feeds for subscribers, and redirects for URL management. Enable or disable them in your config.
---

Ava CMS comes with a few helpful plugins to handle common tasks. They're installed by default but you can enable or disable them in your config.

These plugins also serve as good examples and code references if you want to build your own — see [Creating Plugins](/docs/creating-plugins).

## Sitemap

Automatically generates an XML sitemap for search engines like Google.

<div class="screenshot-window">
<a href="@media:plugin-sitemap.webp" target="_blank" rel="noopener">
  <img src="@media:plugin-sitemap.webp" alt="Sitemap plugin screen" />
</a>
</div>

- **What it does:** Creates a `sitemap.xml` index that links to individual sitemaps for each content type (e.g., `/sitemap-page.xml`).
- **How to use:** Just enable it in `app/config/ava.php`.
- **Customisation:** You can exclude pages by adding `noindex: true` to their frontmatter.
- **Automation:** Automatically adds the Sitemap URL to your `robots.txt` when you rebuild the site.
- **Admin Interface:** View sitemap statistics and status directly in the Admin Panel under **Plugins > Sitemap**.

### Behavior

- **Robots handling:** On every index rebuild the plugin will check `public/robots.txt`:
  - Create `robots.txt` if it doesn't exist (with sensible defaults).
  - Add or update the `Sitemap: <url>` line to match your `site.base_url` config.
  - Preserve existing `User-agent` and `Allow` rules when updating.

### Configuration

First, ensure `'sitemap'` is in your `plugins` array:

```php
// app/config/ava.php
'plugins' => [
    'sitemap',
    // ... other plugins
],
```

### CLI Commands

#### `sitemap:stats`

Show sitemap statistics including URL counts per content type.

```bash
./ava sitemap:stats
```

<pre><samp>  <span class="t-dim">───</span> <span class="t-bold">Sitemap Statistics</span> <span class="t-dim">──────────────────────────────</span>

  <span class="t-dim">Content Type</span>  <span class="t-dim">Indexable</span>  <span class="t-dim">Noindex</span>  <span class="t-dim">Sitemap File</span>       
  <span class="t-dim">─────────────────────────────────────────────────────</span>
  <span class="t-cyan">page</span>          <span class="t-white">5</span>          <span class="t-yellow">1</span>        <span class="t-dim">/sitemap-page.xml</span>  
  <span class="t-cyan">post</span>          <span class="t-white">12</span>         <span class="t-dim">0</span>        <span class="t-dim">/sitemap-post.xml</span>  

  <span class="t-cyan">ℹ</span> Total URLs in sitemap: <span class="t-white">17</span>
  <span class="t-cyan">ℹ</span> Main sitemap: <span class="t-cyan">https://example.com/sitemap.xml</span></samp></pre>


## RSS Feed

Lets people subscribe to your blog using an RSS reader.

<div class="screenshot-window">
<a href="@media:plugin-feeds.webp" target="_blank" rel="noopener">
  <img src="@media:plugin-feeds.webp" alt="Feeds plugin screen" />
</a>
</div>

- **What it does:** Creates `feed.xml` with your latest posts, plus per-content-type feeds (e.g., `/feed/page.xml`).
- **How to use:** Enable it in `app/config/ava.php`.
- **Customisation:** Choose which content types to include and whether to show full content or excerpts.

### Configuration

First, ensure `'feed'` is in your `plugins` array:

```php
// app/config/ava.php
'plugins' => [
    'feed',
    // ... other plugins
],
```

Then optionally configure:

```php
'feed' => [
    'enabled' => true,
    'items_per_feed' => 20,
    'full_content' => false,  // true = full HTML, false = excerpt only
    'types' => null,          // null = all types, or ['post'] for specific types
],
```

| Option | Default | Description |
|--------|---------|-------------|
| `enabled` | `true` | Enable or disable feed generation |
| `items_per_feed` | `20` | Maximum items per feed |
| `full_content` | `false` | `true` = full rendered HTML, `false` = excerpt only |
| `types` | `null` | Which content types to include (`null` = all, or array like `['post']`) |

### Adding to Your Theme

Add the feed link to your theme's `<head>`:

```html
<link rel="alternate" type="application/rss+xml" 
      title="My Site" 
      href="/feed.xml">
```


### CLI Commands

#### `feed:stats`

Show RSS feed statistics and configuration.

```bash
./ava feed:stats
```

<pre><samp>  <span class="t-dim">───</span> <span class="t-bold">RSS Feed Statistics</span> <span class="t-dim">─────────────────────────────</span>

  <span class="t-dim">Content Type</span>  <span class="t-dim">Total Items</span>  <span class="t-dim">In Feed</span>  <span class="t-dim">Feed URL</span>        
  <span class="t-dim">────────────────────────────────────────────────────</span>
  <span class="t-cyan">page</span>          <span class="t-white">5</span>            <span class="t-white">5</span>        <span class="t-dim">/feed/page.xml</span>  
  <span class="t-cyan">post</span>          <span class="t-white">12</span>           <span class="t-white">12</span>       <span class="t-dim">/feed/post.xml</span>  

  <span class="t-cyan">ℹ</span> Items per feed: <span class="t-white">20</span>
  <span class="t-cyan">ℹ</span> Content mode: <span class="t-white">Excerpt only</span>
  <span class="t-cyan">ℹ</span> Main feed: <span class="t-cyan">https://example.com/feed.xml</span></samp></pre>

### Output Example

```xml
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
  <title>My Ava CMS Site</title>
  <link>https://example.com</link>
  <description>Latest content from My Ava CMS Site</description>
  <atom:link href="https://example.com/feed.xml" rel="self" type="application/rss+xml"/>
  <item>
    <title>My Latest Post</title>
    <link>https://example.com/blog/my-latest-post</link>
    <guid isPermaLink="true">https://example.com/blog/my-latest-post</guid>
    <pubDate>Mon, 20 Jan 2025 12:00:00 +0000</pubDate>
    <description>Post excerpt or full content...</description>
  </item>
</channel>
</rss>
```

## Redirects

Manage custom URL redirects and status responses through the admin UI or CLI.

<div class="screenshot-window">
<a href="@media:plugin-redirects.webp" target="_blank" rel="noopener">
  <img src="@media:plugin-redirects.webp" alt="Redirects plugin screen" />
</a>
</div>

- **What it does:** Redirects old URLs to new ones, or returns status-only responses (like 410 Gone).
- **How to use:** Enable the plugin, then use the admin dashboard or CLI commands.
- **Storage:** Redirects are saved to `storage/redirects.json`.

### Features

- **CLI management** — Add and remove redirects via command line
- **Multiple status codes** — Supports redirects (301, 302, 307, 308) and status-only responses (410, 418, 451, 503)
- **High priority** — Processed before content routing
- **Security validation** — Blocks redirects from admin paths and existing content
- **Admin page** — Manage redirects under Plugins → Redirects

### Enabling

Add `'redirects'` to your `plugins` array:

```php
// app/config/ava.php
'plugins' => [
    'redirects',
    // ... other plugins
],
```

This plugin has no additional configuration options—just enable it and use the admin UI or CLI to manage redirects.

### When to Use

| Redirect Type | Use Case |
|---------------|----------|
| **301 Permanent** | Content moved permanently, SEO-friendly |
| **302 Temporary** | Temporary redirect, not cached |

### Comparison with Content Redirects

Ava CMS supports two ways to redirect:

| Method | Best For |
|--------|----------|
| **Redirects Plugin** | External URLs, legacy paths, quick fixes |
| **`redirect_from` frontmatter** | Content that's been moved/renamed |

Using `redirect_from` in content:

```yaml
---
title: New Page Location
redirect_from:
  - /old-url
  - /another-old-url
---
```


### CLI Commands

#### `redirects:list`

List all configured redirects.

```bash
./ava redirects:list
```

**With redirects configured:**

<pre><samp>  <span class="t-dim">───</span> <span class="t-bold">Configured Redirects</span> <span class="t-dim">────────────────────────────</span>

  <span class="t-dim">From</span>          <span class="t-dim">To</span>             <span class="t-dim">Code</span>  <span class="t-dim">Type</span>              
  <span class="t-dim">────────────────────────────────────────────────────</span>
  <span class="t-cyan">/old-page</span>     <span class="t-white">/new-page</span>      <span class="t-green">301</span>   <span class="t-dim">Moved Permanently</span> 
  <span class="t-cyan">/legacy</span>       <span class="t-white">/modern</span>        <span class="t-yellow">302</span>   <span class="t-dim">Found (Temporary)</span> 

  <span class="t-cyan">ℹ</span> Total: <span class="t-white">2</span> redirects</samp></pre>

**No redirects configured:**

<pre><samp>  <span class="t-dim">───</span> <span class="t-bold">Configured Redirects</span> <span class="t-dim">────────────────────────────</span>

  <span class="t-dim">ℹ No redirects configured.</span></samp></pre>

#### `redirects:add`

Add a new redirect from the command line.

```bash
./ava redirects:add <from> <to> [code]
```

**Arguments:**

| Argument | Description |
|----------|-------------|
| `from` | Source path (e.g., `/old-page`) |
| `to` | Destination URL (e.g., `/new-page` or `https://...`) |
| `code` | HTTP status code (default: `301`) |

**Supported Status Codes:**

| Code | Type | Description |
|------|------|-------------|
| `301` | Redirect | Moved Permanently (SEO-friendly) |
| `302` | Redirect | Found (Temporary) |
| `307` | Redirect | Temporary Redirect (preserves method) |
| `308` | Redirect | Permanent Redirect (preserves method) |
| `410` | Status | Gone (content deleted) |
| `418` | Status | I'm a Teapot ☕ |
| `451` | Status | Unavailable For Legal Reasons |
| `503` | Status | Service Unavailable (maintenance) |

**Examples:**

```bash
# Permanent redirect (301)
./ava redirects:add /old-page /new-page

# Temporary redirect (302)
./ava redirects:add /temp-redirect /target 302

# Mark page as permanently gone (410)
./ava redirects:add /deleted-page "" 410

# External redirect
./ava redirects:add /external https://example.com/page
```

#### `redirects:remove`

Remove a redirect.

```bash
./ava redirects:remove <from>
```

**Example:**

```bash
./ava redirects:remove /old-page
```

<pre><samp>  <span class="t-green">✓</span> Removed redirect: <span class="t-cyan">/old-page</span></samp></pre>

### Storage Format

Redirects are stored in `storage/redirects.json`:

```json
[
  {
    "from": "/old-page",
    "to": "/new-page",
    "code": 301,
    "created": "2025-01-20 14:30:00"
  }
]
```

## Enabling All Bundled Plugins

```php
// app/config/ava.php
return [
    // ...
    
    'plugins' => [
        'sitemap',
        'feed',
        'redirects',
    ],
];
```

After enabling, rebuild the content index:

```bash
./ava rebuild
```

Then access the plugin admin pages at:
- `/ava-admin/sitemap`
- `/ava-admin/feeds`
- `/ava-admin/redirects`

## Community Plugins

Looking for more plugins? Check out the [Community Plugins](/plugins) page for plugins shared by other Ava CMS users.

Built a plugin you'd like to share? [Submit it to the community gallery!](/plugins#content-submit-your-plugin)
