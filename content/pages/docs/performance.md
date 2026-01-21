---
title: Performance
slug: performance
status: published
meta_title: Performance | Flat-file PHP CMS | Ava CMS
meta_description: Ava CMS performance guide. Learn about content indexing, webpage caching, backend options (Array vs SQLite), and optimization for sites with thousands of pages.
excerpt: "Ava CMS is designed to be fast by default with a two-layer strategy: content indexing for metadata lookups and webpage caching for instant HTML responses."
---

Ava CMS is designed to be fast by default. To achieve this, it uses a two-layer performance strategy:

1. **Content Indexing:** A pre-built index of your content metadata to avoid parsing Markdown files on every request.
2. **Webpage Caching:** A static HTML cache that serves fully rendered webpages instantly.

Together, these systems mean most visitors get pre-rendered HTML served directly from disk with minimal overhead. 

With webpage caching enabled (the default), cached pages serve in ~0.02ms. Uncached pages render in ~5ms. The content index ensures even uncached pages are fast.

## Quick Guide

**For most users:**
- The defaults work great (Array backend with `igbinary` installed and webpage caching enabled)
- Run `./ava rebuild` after content changes in production
- Check `./ava status` to see your cache status
- Prefer servers with high I/O performance (SSD highly recommended)

**Upgrading from another CMS or have lots of content?**
- < 5,000 posts: Use default settings
- 5,000-10,000 posts: Test both Array and SQLite with `./ava benchmark --compare`
- 10,000+ posts: Switch to SQLite backend (`'backend' => 'sqlite'` in `app/config/ava.php`)

**Having performance issues?**
- Run `./ava status` to check index freshness
- Run `./ava cache:stats` to check webpage cache
- Run `./ava benchmark --compare` to test different backends on your server

## Content Indexing

The Content Index is the foundation of Ava CMS's performance. Instead of reading and parsing Markdown files on every request, Ava CMS builds a binary index of your content metadata (titles, dates, slugs, custom fields, taxonomies).

**Think of it like a library catalog:** Rather than opening every book to find what you need, you look it up in the catalog first.

### How It Works

When you run [`./ava rebuild`](/docs/cli#rebuild) (or when changes are auto-detected in `'auto'` mode), Ava CMS:

1. **Scans** all your Markdown files recursively
2. **Parses** frontmatter and extracts metadata
3. **Validates** content for common issues (YAML syntax, required fields, duplicate slugs/IDs)
4. **Builds** optimized indexes for fast lookups
5. **Stores** the index in your chosen backend format

#### Index Files

Ava CMS generates several files in `storage/cache/` to optimise different types of queries:

| File | Contents | Purpose |
|------|----------|---------|
| `recent_cache.bin` | Top 200 items per type, pre-sorted | **Instant Archives:** Homepage, RSS, first ~20 pages (at the default 10 items/page). |
| `slug_lookup.bin` | Slug → File Path map with minimal metadata | **Fast Single Posts:** Find one item without loading the full index. |
| `content_index.bin` | Full content metadata | **Deep Queries:** Search, filtering, deep pagination (page 21+). |
| `tax_index.bin` | Taxonomy terms with item counts | **Taxonomies:** Category/tag lists, term pages. |
| `routes.bin` | URL → Content map | **Routing:** Maps incoming URLs to content and redirects. |
| `html_cache.bin` | Pre-rendered Markdown → HTML (published only) | **Faster Uncached Renders:** Skips Markdown conversion work when enabled. |
| `fingerprint.json` | Hash of content and config files | **Change Detection:** Determines when to rebuild in `auto` mode. |

#### Tiered Caching Strategy

Ava CMS uses a "tiered" approach to ensure common requests are ultra-fast, even on huge sites:

| Tier | Cache Used | Operations | Typical Response |
|------|-----------|-----------|------------------|
| **Tier 1** | Recent Cache | Homepage, RSS, archive pages 1-20 | **~0.2ms** |
| **Tier 2** | Slug Lookup | Viewing a single post or page | **~1-15ms** |
| **Tier 3** | Full Index | Search, complex filtering, deep pagination (page 21+) | **~15-300ms** |

**Why this matters:** ~90% of real-world traffic hits Tier 1 or Tier 2 operations. The full index is only loaded for things like search results or browsing beyond the first ~20 archive pages.

### Backend Options

Ava CMS supports two index storage backends, plus a compression option. The best choice depends on your content size and server resources.

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

Ava CMS automatically uses igbinary if installed and enabled. Most quality hosts include it by default.

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

We tested all backends with realistic content. You can run these tests on your own server using [`./ava benchmark --compare`](/docs/cli#content-benchmarking) and `./ava stress:generate post <count>` to create test posts.

**Key metrics explained:**
- **Homepage (Recent):** How fast your homepage and recent posts load (uses Tier 1 cache)
- **Get by slug:** How fast a single post/page loads (Tier 2)
- **Deep Archive (Page 50):** Complex queries beyond the recent cache (Tier 3)
- **Memory per query:** RAM used for each uncached request
- **Cache Size:** Disk space used by the index

**Note:** We're comparing Array with igbinary (the recommended default if available) vs SQLite. Plain `serialize()` is much slower and larger—use igbinary if possible.

#### 1,000 Posts

| Operation | Array + igbinary | SQLite | Winner |
|-----------|------------------|--------|--------|
| **Homepage** (Recent) | 0.3ms | 1.0ms | Array ✓ |
| **Get by slug** | 0.8ms | 0.7ms | Tied |
| **Deep Archive** (Page 50) | 18ms | 18ms | Tied |
| **Memory per query** | 7 MB | minimal | SQLite ✓ |
| **Cache Size** | ~4 MB | ~1 MB | SQLite ✓ |

**Verdict:** Array (default) is perfect at this scale. Both are very fast.

#### 10,000 Posts

| Operation | Array + igbinary | SQLite | Winner |
|-----------|------------------|--------|--------|
| **Homepage** (Recent) | 0.1ms | 6ms | Array ✓ |
| **Get by slug** | 14ms | 0.8ms | SQLite ✓ |
| **Deep Archive** (Page 50) | 233ms | 281ms | Array ✓ |
| **Memory per query** | 69 MB | minimal | SQLite ✓ |
| **Cache Size** | ~42 MB | ~10 MB | SQLite ✓ |

**Verdict:** Either works well. Array is faster for common queries, but SQLite uses far less memory. Test both with `./ava benchmark --compare` to see what matters for your use case.

<details>
<summary><strong>25,000 Posts (Click to expand—most sites won't reach this scale)</strong></summary>

| Operation | Array + igbinary | SQLite | Winner |
|-----------|------------------|--------|--------|
| **Homepage** (Recent) | 0.16ms | 15.8ms | Array ✓ |
| **Get by slug** | 30ms | 0.7ms | SQLite ✓ |
| **Deep Archive** (Page 50) | 577ms | 855ms | Array ✓ |
| **Memory per query** | **160 MB** | **2.6 KB** | SQLite ✓✓ |
| **Cache Size** | 96.5 MB | 25.2 MB | SQLite ✓ |

**Verdict:** SQLite is the clear winner at this scale due to memory constraints. Array needs 160 MB per concurrent uncached request, which will overwhelm most servers.

</details>

<details>
<summary><strong>Benchmark Environment & Methodology</strong></summary>

**Environment:**
- **Ava CMS:** v1.0.0
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

#### Understanding the Results

**Why Array is faster for homepages:** It uses a pre-sorted Recent Cache that loads instantly. Perfect for blog homepages and RSS feeds.

**Why SQLite wins for single posts at scale:** Database indexes let it jump directly to the exact row without loading the entire index.

**Why memory matters:** Array loads the index into RAM per concurrent uncached request. At 10k posts, that's 69 MB per request. With 10 concurrent users hitting uncached pages, that's 690 MB. SQLite uses just a few kilobytes.

**Real-world impact:** With webpage caching enabled (the default), 95%+ of requests are served from cache and don't touch the index at all. The benchmarks above matter mainly for:
- Search results (always uncached)
- First visit to any page (until cached)
- Admin/preview pages (bypass cache)
- Deep archive pagination beyond page 20

### Choosing a Backend

| Situation | Recommended Backend |
|-----------|---------------------|
| **< 5,000 posts** | Array + igbinary (default) |
| **5,000-10,000 posts** | Either works; test both with `./ava benchmark --compare` |
| **> 10,000 posts** | SQLite |
| **Memory-limited server** | SQLite |
| **No pdo_sqlite extension** | Array |

**How to decide:**
1. Start with the default (Array + igbinary)
2. Run `./ava benchmark --compare` on your actual server
3. Monitor memory with `./ava status` or your host's tools
4. Switch if needed—it's one line in `app/config/ava.php`

### Configuration Reference

All content index settings live in `app/config/ava.php`:

```php
'content_index' => [
    // When to rebuild the index
    'mode' => 'auto',           // 'auto' | 'never' | 'always'
    
    // Storage backend
    'backend' => 'array',       // 'array' | 'sqlite'
    
    // Compression (array backend only)
    'use_igbinary' => true,     // Uses igbinary if available, otherwise serialize()

    // Optional: pre-render Markdown → HTML during rebuild (experimental)
    'prerender_html' => false,  // Stores rendered HTML for published items
],
```

| Option | Values | Recommendation |
|--------|--------|----------------|
| `mode` | `'auto'` (rebuild on changes)<br>`'never'` (manual only)<br>`'always'` (every request) | `'auto'` for development<br>`'never'` for production |
| `backend` | `'array'` or `'sqlite'` | `'array'` < 10k posts<br>`'sqlite'` for 10k+ |
| `use_igbinary` | `true` or `false` | Keep `true` (auto-detects) |
| `prerender_html` | `true` or `false` | `false` unless you need it |

**About prerender_html:** This is an experimental feature that pre-renders Markdown → HTML during rebuild and stores it in `html_cache.bin`. It can speed up uncached page renders, but most sites won't need it since webpage caching already makes pages instant.



## Webpage Caching

Webpage caching is where the real performance magic happens. After the first visit to any page, Ava CMS saves the complete HTML to disk. Subsequent visitors receive the cached file directly—**up to 250× faster** than rendering.

**Performance:**
- First visit: ~5ms (renders template, processes Markdown)
- Cached visit: ~0.02ms (serves static HTML file)
- Handles thousands of requests/second on modest hardware

**How it works:**
1. Visitor requests `/blog/my-post`
2. Check if `storage/cache/pages/blog_my-post_[hash].html` exists
3. **HIT:** Return cached HTML (response headers: `X-Page-Cache: HIT`, `X-Cache-Age: <seconds>`)
4. **MISS:** Render page, save to cache, return HTML (header: `X-Page-Cache: MISS`)

**Fast path optimization:** On cache HITs for `GET` requests from non-admin users without query strings, Ava CMS can serve the HTML **before the application even boots**. No plugin loading, no index checks—just pure static file serving.

### Configuration

Webpage caching is configured in `app/config/ava.php`:

```php
'webpage_cache' => [
    'enabled' => true,          // Enable/disable caching
    'ttl' => null,              // null = forever, or seconds (3600 = 1 hour)
    'exclude' => [              // URL patterns to never cache
        '/api/*',
        '/search',
    ],
],
```

**TTL (Time-To-Live):**
- `null` (default): Cache until you run `./ava rebuild` or `./ava cache:clear`
- `3600`: Cache for 1 hour
- `86400`: Cache for 24 hours

**Exclude patterns:** Use glob-style patterns to prevent caching specific URLs.

**Per-page control:** Add `cache: false` to any page's frontmatter to bypass caching:

```yaml
---
title: Live Dashboard
cache: false    # This page always renders fresh
---
```

### What Gets Cached (and What Doesn't)

**✅ Cached:**
- Regular pages and posts
- Archive/list pages
- Taxonomy pages (categories, tags)
- Homepage and pagination

**❌ Never cached:**
- Admin area (`/ava-admin/*`)
- Requests from logged-in admins
- URLs with query parameters (except UTM marketing params)
- Pages with `cache: false` in frontmatter
- POST/PUT/DELETE requests
- URLs matching `exclude` patterns

**UTM parameters** (`utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`) are automatically ignored, so marketing campaigns don't pollute your cache.

### Cache Management

**Automatic clearing:** The cache clears automatically when you run `./ava rebuild` or when content changes are detected in `'auto'` mode.

**Manual clearing:**

```bash
./ava cache:stats            # View cache statistics
./ava cache:clear            # Clear all cached webpages
./ava cache:clear /blog/*    # Clear specific paths
```



## Tools & Troubleshooting

### Check Your Site's Performance

Use `./ava status` to see everything at a glance:

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

### Common Issues & Solutions

#### Content changes not appearing

**Cause:** Index mode is set to `'never'` or index hasn't rebuilt.

**Fix:**
```bash
./ava rebuild                # Rebuild index and clear webpage cache
```

If that doesn't work:
```bash
rm storage/cache/fingerprint.json  # Force rebuild on next request
```

#### Webpages not caching

**Check these in order:**

1. **Is caching enabled?** Run `./ava status` or check `'webpage_cache.enabled'` in config
2. **Are you logged into admin?** Log out—admins always bypass cache
3. **Query parameters?** URLs with `?params` (except UTM) won't cache
4. **Exclude pattern match?** Check `'webpage_cache.exclude'` in config
5. **Page has `cache: false`?** Check the page's frontmatter

#### High memory usage

**Symptoms:** Server running out of memory, PHP fatal errors

**Solutions:**
1. Check your content size: `./ava status`
2. Test memory usage: `./ava benchmark --compare`
3. Switch to SQLite if > 10k posts: `'backend' => 'sqlite'` in config
4. Check for memory leaks in custom plugins/themes

#### SQLite backend errors

**Error:** "could not find driver" or "pdo_sqlite not installed"

**Fix:**
```bash
php -m | grep -i sqlite      # Check if installed
```

If not installed:
- Contact your host to enable `pdo_sqlite`
- Or use `'backend' => 'array'` instead

#### Slow performance despite caching

**Troubleshooting steps:**

1. **Check cache hit rate:** Look for `X-Page-Cache: HIT` in response headers
2. **Test uncached speed:** `./ava benchmark`
3. **Check server resources:** CPU, RAM, disk I/O
4. **Profile specific pages:** Add `?XDEBUG_PROFILE=1` if Xdebug installed

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

**Development settings** (see changes immediately):
```php
'content_index' => ['mode' => 'auto'],
'webpage_cache' => ['enabled' => false],
```

**Production settings** (maximum performance):
```php
'content_index' => [
    'mode' => 'never',          // Manual rebuilds only
    'backend' => 'array',       // Or 'sqlite' for 10k+ posts
],
'webpage_cache' => [
    'enabled' => true,
    'ttl' => null,              // Cache until rebuild
],
```

**Deployment workflow:**
```bash
git pull                        # Pull latest changes
./ava rebuild                   # Rebuild index and clear cache
```

## CDN Integration

Ava CMS's webpage cache is already very fast, but adding a CDN provides:

- **Global edge caching:** Serve from locations worldwide
- **DDoS protection:** Absorb attack traffic
- **SSL termination:** Offload HTTPS processing

Popular options: Cloudflare (free tier), BunnyCDN, Fastly. See the [Ava for Cloudflare®](https://github.com/avacms/ava-for-cloudflare) plugin for handy Cloudflare utilities such as automatic cache purging.

## Quick Reference

**Check status:**
```bash
./ava status                 # View cache status and configuration
./ava cache:stats            # Webpage cache statistics
```

**Rebuild after changes:**
```bash
./ava rebuild                # Rebuild index and clear webpage cache
./ava cache:clear            # Clear webpage cache only
```

**Test performance:**
```bash
./ava benchmark              # Test current backend
./ava benchmark --compare    # Compare all backends
```

**Generate test content:**
```bash
./ava stress:generate post 5000   # Create 5,000 test posts
./ava stress:clean post            # Remove test posts
```

**Key files:**
- `app/config/ava.php` - Configure caching and backend
- `storage/cache/` - All cache files
- `storage/cache/fingerprint.json` - Delete to force rebuild
