---
title: Performance
slug: performance
status: published
meta_title: Performance | Flat-file PHP CMS | Ava CMS
meta_description: Ava CMS performance guide. Learn about content indexing, webpage caching, backend options (Array vs SQLite), and optimization for sites with thousands of pages.
excerpt: "Ava is designed to be fast by default with a two-layer strategy: content indexing for metadata lookups and webpage caching for instant HTML responses."
---

Ava is designed to be fast by default. To achieve this, it uses a two-layer performance strategy:

1. **Content Indexing:** A pre-built index of your content metadata to avoid parsing Markdown files on every request.
2. **Webpage Caching:** A static HTML cache that serves fully rendered webpages instantly.

Together, these systems mean most visitors get pre-rendered HTML served directly from disk with minimal overhead.

## Content Indexing

The Content Index is the foundation of Ava's performance. Instead of reading and parsing Markdown files every time a user visits your site, Ava builds a binary index of all your content's metadata (titles, dates, slugs, custom fields, taxonomies).

### How It Works

When you run [`./ava rebuild`](/docs/cli#rebuild) (or when Ava auto-detects changes in `auto` mode), it:

1. **Scans** all your Markdown files recursively
2. **Parses** frontmatter and extracts metadata
3. **Validates** content for common issues (YAML syntax, required fields, duplicate slugs/IDs)
4. **Builds** optimised indexes for fast lookups
5. **Stores** the index in your chosen backend format

#### Index Files

Ava generates several files in `storage/cache/` to optimise different types of queries:

| File | Contents | Purpose |
|------|----------|---------|
| `recent_cache.bin` | Top 200 items per type, pre-sorted | **Instant Archives:** Homepage, RSS, first ~20 pages (at the default 10 items/page). |
| `slug_lookup.bin` | Slug → File Path map with minimal metadata | **Fast Single Posts:** Find one item without loading the full index. |
| `content_index.bin` | Full content metadata | **Deep Queries:** Search, filtering, deep pagination (page 21+). |
| `tax_index.bin` | Taxonomy terms with item counts | **Taxonomies:** Category/tag lists, term pages. |
| `routes.bin` | URL → Content map | **Routing:** Maps incoming URLs to content and redirects. |
| `fingerprint.json` | Hash of content and config files | **Change Detection:** Determines when to rebuild in `auto` mode. |

#### Tiered Caching Strategy

Ava uses a "tiered" approach to ensure common requests are ultra-fast, even on huge sites:

| Tier | Cache Used | Operations | Typical Response |
|------|-----------|-----------|------------------|
| **Tier 1** | Recent Cache | Homepage, RSS, archive pages 1-20 | **~0.2ms** |
| **Tier 2** | Slug Lookup | Viewing a single post or page | **~1-15ms** |
| **Tier 3** | Full Index | Search, complex filtering, deep pagination (page 21+) | **~15-300ms** |

**Why this matters:** ~90% of real-world traffic hits Tier 1 or Tier 2 operations. The full index is only loaded for things like search results or browsing beyond the first ~20 archive pages.

### Backend Options

Ava supports two index storage backends, plus a compression option. The best choice depends on your content size and server resources.

#### Array Backend (Default)

Stores the index as serialized PHP arrays in `.bin` files. On each request, the relevant cache file is loaded into memory and queried.

```php
// app/config/ava.php
'content_index' => [
    'backend' => 'array',         // Default
    'use_igbinary' => true,       // Recommended if available
],
```

**Compression Options:**

| Option | Extension Required | Benefits |
|--------|-------------------|----------|
| **igbinary** (recommended) | `igbinary` | ~2× faster reads, much smaller cache files |
| **serialize** (fallback) | None | Works everywhere, slower and larger files |

Ava automatically uses igbinary if installed and enabled. Most quality hosts include it by default.

**Pros:**
- Fastest for most operations (everything happens in RAM)
- Zero external dependencies
- Simple to understand and debug

**Cons:**
- Memory usage scales with content size
- Each concurrent request loads the index into memory

#### SQLite Backend

Stores the index in a single SQLite database file (`storage/cache/content_index.sqlite`). Queries are executed directly against the database without loading everything into memory.

```php
// app/config/ava.php
'content_index' => [
    'backend' => 'sqlite',
],
```

**Pros:**
- Minimal per-query memory overhead (doesn't load full index into RAM)
- Near-instant counts (uses database indexes)
- Best for very large sites (10k+ items)

**Cons:**
- Slightly slower for complex queries (database I/O vs RAM)
- Requires `pdo_sqlite` PHP extension
- Slower for "Recent" queries that Array handles instantly

### Benchmark Comparison

We tested all backends with realistic content. You can run these tests on your own server using [`./ava benchmark --compare`](/docs/cli#content-benchmarking).

#### 1,000 Posts

| Operation | Array + igbinary | Array + serialize | SQLite |
|-----------|------------------|-------------------|--------|
| **Build index** | 229ms | 327ms | 264ms |
| **Count all posts** | 11ms | 57ms | 0.5ms |
| **Get by slug** | 0.8ms | 0.7ms | 0.7ms |
| **Homepage** (Recent) | 0.3ms | 0.2ms | 1.0ms |
| **Deep Archive** (Page 50, beyond recent cache) | 18ms | 59ms | 18ms |
| **Sort by date** | 16ms | 60ms | 19ms |
| **Sort by title** | 17ms | 61ms | 19ms |
| **Search** | 17ms | 60ms | 14ms |
| **Memory per query** | 7 MB | 31 MB | minimal |
| **Cache Size** | ~4 MB | ~18 MB | ~1 MB |

#### 10,000 Posts

| Operation | Array + igbinary | Array + serialize | SQLite |
|-----------|------------------|-------------------|--------|
| **Build index** | 2.4s | 3.3s | 2.7s |
| **Count all posts** | 141ms | 531ms | 1.2ms |
| **Get by slug** | 14ms | 12ms | 0.8ms |
| **Homepage** (Recent) | 0.1ms | 0.2ms | 6ms |
| **Deep Archive** (Page 50, beyond recent cache) | 233ms | 621ms | 281ms |
| **Sort by date** | 191ms | 695ms | 278ms |
| **Sort by title** | 215ms | 757ms | 285ms |
| **Search** | 192ms | 650ms | 237ms |
| **Memory per query** | 69 MB | 306 MB | minimal |
| **Cache Size** | ~42 MB | ~180 MB | ~10 MB |

#### 25,000 Posts

These results show what happens once the dataset is large enough that “load a big blob into RAM and scan it” starts to cost noticeable time.

| Operation | Array + igbinary | Array + serialize | SQLite |
|-----------|------------------|-------------------|--------|
| **Build index** | 4.6s | 7.8s | 6.1s |
| **Count all posts** | 327ms | 1.3s | 2.7ms |
| **Get by slug** | 30ms | 36ms | 0.7ms |
| **Homepage** (Recent) | 0.16ms | 0.15ms | 15.8ms |
| **Deep Archive** (Page 50, beyond recent cache) | 577ms | 1.7s | 855ms |
| **Memory per query** | 160 MB | 676 MB | 2.6 KB |
| **Cache Size** | 96.5 MB | 438.4 MB | 25.2 MB |

<details>
<summary><strong>Benchmark Environment & Methodology</strong></summary>

**Environment:**
- **Ava:** v1.0.0
- **OS:** Linux x86_64 (Ubuntu)
- **PHP:** 8.5.1 (CLI)
- **Hardware:** Budget Hetzner Cloud VPS (CX22: 2 vCPU, 4GB RAM)

**Methodology:**
1. Content generated via `./ava stress:generate post <count>`
2. Benchmarks run via `./ava benchmark --compare --iterations=5`
3. Each test iterated 5 times, average result shown
4. Memory cache cleared between iterations for accurate measurements
5. Index rebuilt fresh for each backend during comparison

**Note:** OPcache is disabled for CLI by default. This doesn't affect these results since OPcache only caches compiled PHP bytecode, not data operations. The benchmarks measure I/O, unserialization, and query performance.
</details>

#### Analysis

**Build Index:** All backends take similar time since the cost is dominated by parsing Markdown files. This is a one-time cost when content changes.

**Homepage/Recent:** Array backends are instant because they use the pre-sorted Recent Cache. SQLite must query the database, adding a few milliseconds.

**Counts:** SQLite is dramatically faster (~1ms vs 100s of ms) because it uses a database index. If your templates frequently call `{{ count('post') }}`, SQLite may be beneficial.

**Single Item Lookup (Get by slug):** SQLite wins at scale because it uses a database index to jump directly to the row. Array backends must load and unserialize the lookup table first.

**Deep Archives, Sort, Search:** Array + igbinary is typically fastest for in-memory scanning, but at larger scales (like 25k posts) SQLite can be competitive—especially when you care about filtering/counts and keeping memory low. Both are acceptable for human-facing response times when combined with webpage caching.

**Memory:** SQLite has minimal per-query overhead since it queries the database directly rather than loading the full index into RAM. This matters most for concurrent uncached requests.

#### The Memory Tradeoff

The Array backend consumes memory **per concurrent uncached request**. "Concurrent" here means requests being actively processed at the same instant—typically a fraction of a second per request, not how many users are browsing your site.

| Posts | Index Size | 1 Concurrent | 10 Concurrent | 50 Concurrent |
|-------|-----------|--------|----------|----------|
| 1,000 | ~4 MB | ~4 MB | ~40 MB | ~200 MB |
| 10,000 | ~42 MB | ~42 MB | ~420 MB | ~2.1 GB |

**SQLite** has minimal per-query overhead regardless of content size or concurrency. It won't be quite as fast for a single user, but it handles concurrent load more gracefully.

<div class="callout-info">
<strong>In practice:</strong> With webpage caching enabled, the vast majority of visitors get pre-rendered HTML directly from disk, consuming almost no memory. The memory table above applies to <em>uncached</em> requests only—things like search results, deep archive pagination, or the first visit after a cache clear. For a typical site with caching enabled, you'll rarely see more than a handful of concurrent uncached requests.
</div>

### Choosing a Backend

#### Quick Decision Guide

| Situation | Recommended Backend |
|-----------|---------------------|
| **< 5,000 posts** | Array + igbinary (default) |
| **5,000-10,000 posts** | Either works; test both |
| **> 10,000 posts** | SQLite |
| **Memory-limited server** | SQLite |
| **Heavy count() usage** | SQLite |
| **No pdo_sqlite extension** | Array |
| **Maximum speed, ample RAM** | Array + igbinary |

#### How to Decide for Your Site

1. **Start with the default** (Array + igbinary) — it's optimised for typical sites
2. **Run your own benchmarks:** `./ava benchmark --compare`
3. **Monitor memory usage** with your host's tools or `./ava status`
4. **Switch if needed** — it's just one config change

<div class="callout-info">
<strong>Future backends:</strong> We're exploring additional backend options to ensure Ava scales gracefully for all kinds of sites while staying true to its flat-file, portable approach. If you have specific needs or ideas, let us know in the <a href="https://discord.gg/fZwW4jBVh5">Discord community</a>.
</div>

### Configuration Reference

All content index settings live in `app/config/ava.php`:

```php
'content_index' => [
    // When to rebuild the index
    'mode' => 'auto',           // 'auto', 'never', or 'always'
    
    // Storage backend
    'backend' => 'array',       // 'array' or 'sqlite'
    
    // Compression (array backend only)
    'use_igbinary' => true,     // true or false
],
```

#### Mode Options

| Mode | Behaviour | Best For |
|------|-----------|----------|
| `auto` | Rebuild when files change (detected via fingerprint) | Development, small-medium sites |
| `never` | Only rebuild via `./ava rebuild` | Production, CI/CD workflows, large sites |
| `always` | Rebuild every request | Debugging only (**very slow**) |

**Recommendation:** Use `auto` during development, switch to `never` in production with scheduled/triggered rebuilds.

#### Backend Options

| Backend | Requires | Best For |
|---------|----------|----------|
| `array` | Nothing (or `igbinary` for speed) | Most sites, < 10k items |
| `sqlite` | `pdo_sqlite` extension | Large sites, memory constraints |

#### igbinary Option

| Setting | Effect |
|---------|--------|
| `true` (default) | Use igbinary if installed (faster reads, smaller files) |
| `false` | Always use standard `serialize()` |

If igbinary is enabled but not installed, Ava automatically falls back to serialize.



## Webpage Caching

For ultimate performance, Ava includes a full webpage cache. After the first visit to any page, Ava saves the complete HTML to disk. Subsequent visitors receive the cached file directly.

- **First visit:** ~5-50ms (renders template, processes Markdown)
- **Cached visit:** ~0.02-0.1ms (serves static file)

This bypasses the content index entirely for most traffic.

### How It Works

1. A visitor requests `/blog/my-post`
2. Ava checks if a cached HTML file exists for this path
3. **Cache HIT:** Return the cached file with `X-Page-Cache: HIT` header
4. **Cache MISS:** Render the page, save to cache, return with `X-Page-Cache: MISS` header

### Cache File Location

Cached pages are stored in `storage/cache/pages/` as `.html` files:

```
storage/cache/pages/
├── index_a1b2c3d4.html          ← Homepage
├── about_e5f6g7h8.html          ← /about
├── blog_my-post_i9j0k1l2.html   ← /blog/my-post
└── ...
```

Filenames include a hash to handle special characters and long paths safely.

### Webpage Rendering Benchmarks

These measure the full rendering pipeline (load content, parse Markdown, render template):

| Operation | Time |
|-----------|------|
| **Render post (uncached)** | ~5ms |
| **Write to cache** | ~0.1ms |
| **Read from cache (HIT)** | ~0.02ms |

**Key insight:** Uncached rendering is already fast (~5ms), but cached pages are **250× faster**. For high-traffic sites, this is the difference between handling 200 requests/second and 50,000 requests/second.

### Configuration

Enable and configure webpage caching in `app/config/ava.php`:

```php
'webpage_cache' => [
    'enabled' => true,          // Enable/disable caching
    'ttl' => null,              // Time-to-live in seconds, or null for forever
    'exclude' => [              // URL patterns to never cache
        '/api/*',
        '/preview/*',
    ],
],
```

### Options Explained

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `enabled` | bool | `false` | Enable webpage caching |
| `ttl` | int\|null | `null` | Cache lifetime in seconds. `null` = cache until rebuild |
| `exclude` | array | `[]` | Glob patterns for URLs to never cache |

### TTL Examples

```php
'ttl' => null,              // Cache forever (cleared on ./ava rebuild)
'ttl' => 3600,              // Cache for 1 hour
'ttl' => 86400,             // Cache for 24 hours
'ttl' => 60 * 60 * 24 * 7,  // Cache for 1 week
```

### Exclude Patterns

The `exclude` array supports glob-style patterns:

```php
'exclude' => [
    '/api/*',           // Don't cache API routes
    '/preview/*',       // Don't cache preview pages
    '/search',          // Don't cache search (dynamic)
    '/user/*',          // Don't cache user-specific pages
],
```

### Per-Page Cache Control

Override the global setting for individual pages using frontmatter:

```yaml
---
title: My Dynamic Page
cache: false
---
```

| Setting | Effect |
|---------|--------|
| `cache: false` | Never cache this page, even if global caching is enabled |
| `cache: true` | Force caching for this page (if global caching is enabled) |
| (not set) | Use global settings |

**Use case:** A page that displays random content, current time, or user-specific information should set `cache: false`.

### What Gets Cached

| Content Type | Cached? |
|--------------|---------|
| ✅ Single pages (posts, pages, custom types) | Yes |
| ✅ Archive pages (lists, paginated archives) | Yes |
| ✅ Taxonomy pages (categories, tags) | Yes |
| ❌ Admin pages | Never |
| ❌ URLs with query parameters (except UTM) | Never |
| ❌ Requests from logged-in admin users | Never |
| ❌ POST/PUT/DELETE requests | Never |

### UTM Parameters

Marketing UTM parameters are automatically stripped and don't create separate cache entries:

- `?utm_source=twitter` → Uses cached version of base URL
- `?utm_medium=social` → Uses cached version of base URL

This prevents cache pollution from marketing campaigns.

### Query Parameters

Any other query parameter bypasses the cache:

- `/search?q=hello` → Not cached (dynamic)
- `/page?preview=1` → Not cached (preview)
- `/api/data?format=json` → Not cached (API)

This prevents cache poisoning attacks.

### Cache Invalidation

The webpage cache is automatically cleared when:

1. You run `./ava rebuild`
2. Content changes are detected (if `content_index.mode` is `'auto'`)
3. You click "Rebuild" or "Flush Webpages" in the Admin Dashboard

### Manual Cache Management

Clear the cache via CLI:

```bash
./ava cache:stats            # View cache statistics
./ava cache:clear            # Clear all cached webpages
./ava cache:clear /blog/*    # Clear only matching paths
```

Example output:

<pre><samp>  <span class="t-dim">───</span> <span class="t-cyan t-bold">Webpage Cache</span> <span class="t-dim">─────────────────────────────────────</span>

  <span class="t-dim">Status:</span>     <span class="t-green">● Enabled</span>
  <span class="t-dim">TTL:</span>        <span class="t-white">Forever (until cleared)</span>
  <span class="t-dim">Cached:</span>     <span class="t-white">42 webpages</span>
  <span class="t-dim">Size:</span>       <span class="t-white">1.2 MB</span></samp></pre>

### Security Considerations

The webpage cache is designed to be secure by default:

| Protection | How It Works |
|------------|--------------|
| **No admin caching** | Admin pages are never cached |
| **Logged-in user detection** | Admin users always get fresh pages |
| **No query string caching** | Prevents cache poisoning |
| **Hashed filenames** | Prevents path traversal attacks |
| **Exclude patterns** | Sensitive routes can be explicitly excluded |

**Cookie handling:** Ava checks for the `ava_admin` session cookie. If present, caching is bypassed to ensure admins always see fresh content.



## Tools & Troubleshooting

### Checking Site Status

The `./ava status` command shows comprehensive performance information:

<pre><samp><span class="t-cyan">   ▄▄▄  ▄▄ ▄▄  ▄▄▄     ▄▄▄▄ ▄▄   ▄▄  ▄▄▄▄
  ██▀██ ██▄██ ██▀██   ██▀▀▀ ██▀▄▀██ ███▄▄
  ██▀██  ▀█▀  ██▀██   ▀████ ██   ██ ▄▄██▀</span>   <span class="t-dim">v1.0.0</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Content Index</span> <span class="t-dim">─────────────────────────────────────</span>

  <span class="t-dim">Status:</span>     <span class="t-green">● Fresh</span>
  <span class="t-dim">Mode:</span>       <span class="t-white">auto</span>
  <span class="t-dim">Backend:</span>    <span class="t-cyan">Array</span> <span class="t-dim">(igbinary)</span>
  <span class="t-dim">Cache:</span>      <span class="t-dim">Full index</span> 4.2 MB, <span class="t-dim">Slug lookup</span> 412 KB
  <span class="t-dim">Built:</span>      <span class="t-dim">2026-01-12 14:30:00</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Webpage Cache</span> <span class="t-dim">─────────────────────────────────────</span>

  <span class="t-dim">Status:</span>     <span class="t-green">● Enabled</span>
  <span class="t-dim">TTL:</span>        <span class="t-white">Forever (until cleared)</span>
  <span class="t-dim">Cached:</span>     <span class="t-white">42</span> webpages
  <span class="t-dim">Size:</span>       <span class="t-white">1.2 MB</span></samp></pre>

### Common Issues

### Content changes not appearing

1. Check your index mode: if set to `never`, run `./ava rebuild`
2. Delete `storage/cache/fingerprint.json` to force a rebuild
3. Run `./ava rebuild` to reset everything

### Webpages not being cached

1. Check if enabled: `./ava status` or `./ava cache:stats`
2. Log out of admin (admin users bypass cache)
3. Check exclude patterns in `app/config/ava.php`
4. Check for query parameters in the URL
5. Check if the page has `cache: false` in frontmatter

### High memory usage

1. Check your content count vs available RAM
2. Consider switching to `backend: 'sqlite'`
3. Run `./ava benchmark --compare` to see memory usage

### SQLite backend not working

1. Check if `pdo_sqlite` is installed: `php -m | grep -i sqlite`
2. If not available, ask your host or install it
3. Or keep using `backend: 'array'`

### Cache files not being created

1. Ensure `storage/cache/` is writable by the web server
2. Check file permissions: `chmod -R 755 storage/`
3. Check for errors in `storage/logs/ava.log`

### igbinary not being used

1. Check if installed: `php -m | grep igbinary`
2. Check if enabled in config: `'use_igbinary' => true`
3. Install via your host's PHP settings or `pecl install igbinary`

### Running Your Own Benchmarks

Test performance on your own server:

```bash
# 1. Generate test content
./ava stress:generate post 5000

# 2. Run benchmarks (current backend)
./ava benchmark

# 3. Compare all backends
./ava benchmark --compare

# 4. More iterations for accuracy
./ava benchmark --compare --iterations=10

# 5. Clean up when done
./ava stress:clean post
```

### Benchmark Options

| Option | Description |
|--------|-------------|
| `--compare` | Test all available backends side-by-side |
| `--iterations=N` | Number of test iterations (default: 5) |
| `--help` | Show benchmark help |

### Performance Best Practices

### For Development

```php
'content_index' => [
    'mode' => 'auto',           // Auto-rebuild on changes
    'backend' => 'array',
],
'webpage_cache' => [
    'enabled' => false,         // See changes immediately
],
```

### For Production

```php
'content_index' => [
    'mode' => 'never',          // Only rebuild via CLI/webhook
    'backend' => 'array',       // Or 'sqlite' for 10k+ items
    'use_igbinary' => true,
],
'webpage_cache' => [
    'enabled' => true,          // Maximum performance
    'ttl' => null,              // Cache until rebuild
],
```

### Deployment Workflow

When deploying content updates:

```bash
# After uploading new content files
./ava rebuild                    # Rebuilds index AND clears webpage cache
```

For automated deployments, add this to your CI/CD pipeline or webhook.

### CDN Integration

Ava's webpage cache is already very fast, but you can add a CDN for additional benefits:

- **Geographic distribution:** Serve from edge locations worldwide
- **DDoS protection:** Let the CDN absorb attack traffic
- **SSL termination:** Offload HTTPS to the CDN

Popular options: Cloudflare (free tier), BunnyCDN, Fastly.

**Tip:** Set appropriate `Cache-Control` headers in your theme for static assets. Ava automatically serves theme assets with 1-year cache headers.
