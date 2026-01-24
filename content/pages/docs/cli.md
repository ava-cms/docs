---
title: CLI Reference
slug: cli
status: published
meta_title: CLI Reference | Flat-file PHP CMS | Ava CMS
meta_description: Complete CLI reference for Ava CMS. Manage content, users, rebuild indexes, run tests, and automate your flat-file CMS workflow from the command line.
excerpt: Ava CMS includes a friendly command-line interface for managing your site. Create content, manage users, rebuild indexes, and run tests—all from your terminal.
---

Ava CMS includes a command-line interface for managing your site. Run commands from your project root (the folder with `ava` in it):

```bash
./ava <command> [options]
```

<div class="callout-info">
<strong>Windows users:</strong> Use <code>php ava</code> instead of <code>./ava</code> for all commands throughout this documentation. The commands work identically—Windows just needs the explicit <code>php</code> prefix.
</div>

The CLI has been thoughtfully designed for a simple and delightful experience. Most output includes helpful tips and next steps.

<details class="beginner-box">
<summary>Beginner’s Guide to the Terminal</summary>
<div class="beginner-box-content">

“CLI” just means *typing commands* instead of clicking buttons. It’s a superpower for servers and automation, but you only need a tiny slice of it to be productive with Ava CMS.

### What is “the project root”?
It’s the folder that contains your Ava CMS project — where you can see `composer.json`, `content/`, `app/`, and the `ava` script.

<div class="callout-info">
<strong>Tip:</strong> If you type <code>./ava status</code> (or <code>php ava status</code> on Windows) and it works, you're in the right folder.
</div>

### A tiny CLI cheat-sheet (you’ll use these a lot)

| Command | What it does |
| :--- | :--- |
| `ls` | List files in the current folder. Works everywhere (including PowerShell). |
| `cd folder-name` | Move into a folder. Works everywhere. |
| `cd ..` | Go up one folder. Works everywhere. |
| `php -v` | Show your PHP version. Works everywhere. |
| `php ava status` | Check if Ava CMS is working. Use this on Windows instead of `./ava`. |

</div>
</details>

### Running Commands on a Server

To run Ava CMS commands on your live site, you'll need to connect via SSH. See the [Hosting Guide](/docs/hosting#ssh-running-commands-on-your-server) for a complete walkthrough of SSH setup, clients, and connecting to your server.

### Troubleshooting: Permission Denied

If you see a "permission denied" error when trying to run `./ava`, the script doesn't have execute permissions. This is common after uploading files via SFTP or extracting from a ZIP.

**Fix it once:**

```bash
chmod +x ava
```

This grants execute permissions to the `ava` script. You only need to run this once per installation.

**What does chmod do?**

`chmod` (change mode) modifies file permissions on Unix-like systems. The `+x` flag adds execute permission, allowing you to run the file as a program. Without it, your system treats `ava` as a text file rather than an executable script.

**Alternative:** If you can't or don't want to change permissions, you can always run commands through PHP directly:

```bash
php ava status
php ava rebuild
```

This works identically to `./ava` but doesn't require execute permissions.



## Getting Help

Run `./ava` or `./ava --help` to see all available commands:

```bash
./ava --help
```

**Shortcuts:** Several commands have convenient aliases:
- `./ava cache` → `cache:stats`
- `./ava logs` → `logs:stats`
- `./ava user` → `user:list`
- `./ava update` → `update:check`
- `./ava stress:benchmark` → `benchmark`

<pre><samp><span class="t-cyan">   ▄▄▄  ▄▄ ▄▄  ▄▄▄     ▄▄▄▄ ▄▄   ▄▄  ▄▄▄▄
  ██▀██ ██▄██ ██▀██   ██▀▀▀ ██▀▄▀██ ███▄▄
  ██▀██  ▀█▀  ██▀██   ▀████ ██   ██ ▄▄██▀</span>   <span class="t-dim">v1.1.0</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Usage</span> <span class="t-dim">─────────────────────────────────────────────</span>

    <span class="t-cyan">./ava</span> <span class="t-white">&lt;command&gt;</span> <span class="t-dim">[options]</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Site Management</span> <span class="t-dim">───────────────────────────────────</span>

    <span class="t-white">status</span>                        <span class="t-dim">Show site health and overview</span>
    <span class="t-white">rebuild [--keep-webcache]</span>     <span class="t-dim">Rebuild the content index</span>
    <span class="t-white">lint</span>                          <span class="t-dim">Validate all content files</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Content</span> <span class="t-dim">───────────────────────────────────────────</span>

    <span class="t-white">make &lt;type&gt; "Title"</span>           <span class="t-dim">Create new content</span>
    <span class="t-white">prefix &lt;add|remove&gt; [type]</span>    <span class="t-dim">Toggle date prefixes</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Webpage Cache</span> <span class="t-dim">─────────────────────────────────────</span>

    <span class="t-white">cache:stats (or cache)</span>        <span class="t-dim">View cache statistics</span>
    <span class="t-white">cache:clear [pattern]</span>         <span class="t-dim">Clear cached webpages</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Logs</span> <span class="t-dim">──────────────────────────────────────────────</span>

    <span class="t-white">logs:stats (or logs)</span>          <span class="t-dim">View log file statistics</span>
    <span class="t-white">logs:tail [name] [-n N]</span>       <span class="t-dim">Show last N lines of a log</span>
    <span class="t-white">logs:clear [name]</span>             <span class="t-dim">Clear log files</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Users</span> <span class="t-dim">─────────────────────────────────────────────</span>

    <span class="t-white">user:add &lt;email&gt; &lt;pass&gt;</span>       <span class="t-dim">Create admin user</span>
    <span class="t-white">user:password &lt;email&gt; &lt;pass&gt;</span>  <span class="t-dim">Update password</span>
    <span class="t-white">user:remove &lt;email&gt;</span>           <span class="t-dim">Remove user</span>
    <span class="t-white">user:list (or user)</span>           <span class="t-dim">List all users</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Updates</span> <span class="t-dim">───────────────────────────────────────────</span>

    <span class="t-white">update:check (or update)</span>      <span class="t-dim">Check for updates</span>
    <span class="t-white">update:apply</span>                  <span class="t-dim">Apply available update</span>
    <span class="t-white">update:stale</span>                  <span class="t-dim">Detect stale files from older releases</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Testing</span> <span class="t-dim">───────────────────────────────────────────</span>

    <span class="t-white">test [filter]</span>                 <span class="t-dim">Run the test suite</span>
    <span class="t-white">stress:generate &lt;type&gt; &lt;n&gt;</span>    <span class="t-dim">Generate test content</span>
    <span class="t-white">stress:clean &lt;type&gt;</span>           <span class="t-dim">Remove test content</span>
    <span class="t-white">stress:benchmark</span>              <span class="t-dim">Benchmark index backends</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Plugins</span> <span class="t-dim">───────────────────────────────────────────</span>

    <span class="t-white">sitemap:stats</span>                 <span class="t-dim">Show sitemap statistics</span>
    <span class="t-white">feed:stats</span>                    <span class="t-dim">Show RSS feed statistics</span>
    <span class="t-white">redirects:list</span>                <span class="t-dim">List all redirects</span>
    <span class="t-white">redirects:add</span>                 <span class="t-dim">Add a redirect</span>
    <span class="t-white">redirects:remove</span>              <span class="t-dim">Remove a redirect</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Examples</span> <span class="t-dim">──────────────────────────────────────────</span>

    <span class="t-white">./ava status</span>
    <span class="t-white">./ava make post "Hello World"</span>
    <span class="t-white">./ava lint</span></samp></pre>

### version

Show the current Ava CMS version:

```bash
./ava --version
# or
./ava -v
# or
./ava version
```

<pre><samp><span class="t-cyan">   ▄▄▄  ▄▄ ▄▄  ▄▄▄     ▄▄▄▄ ▄▄   ▄▄  ▄▄▄▄
  ██▀██ ██▄██ ██▀██   ██▀▀▀ ██▀▄▀██ ███▄▄
  ██▀██  ▀█▀  ██▀██   ▀████ ██   ██ ▄▄██▀</span>   <span class="t-dim">v1.1.0</span></samp></pre>

<details class="beginner-box">
<summary>Why do commands start with <code>./</code>?</summary>
<div class="beginner-box-content">

### Why do commands start with <code>./</code>?

`./ava` means “run the ava script in this folder.” The `./` tells your computer to look for the command right here, not somewhere else on your system. This is common for project tools in PHP, Node, Python, and more. If you just type `ava`, it only works if you’ve installed it globally (which is not recommended for project scripts).

</div>
</details>

## Site Management

### status

Shows a quick overview of your site's health:

```bash
./ava status
```

<pre><samp><span class="t-cyan">   ▄▄▄  ▄▄ ▄▄  ▄▄▄     ▄▄▄▄ ▄▄   ▄▄  ▄▄▄▄
  ██▀██ ██▄██ ██▀██   ██▀▀▀ ██▀▄▀██ ███▄▄
  ██▀██  ▀█▀  ██▀██   ▀████ ██   ██ ▄▄██▀</span>   <span class="t-dim">v1.1.0</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Site</span> <span class="t-dim">──────────────────────────────────────────────</span>

  <span class="t-dim">Name:</span>       <span class="t-white">My Site</span>
  <span class="t-dim">URL:</span>        <span class="t-cyan">https://example.com</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Environment</span> <span class="t-dim">───────────────────────────────────────</span>

  <span class="t-dim">PHP:</span>        <span class="t-white">8.3.29</span>
  <span class="t-dim">Extensions:</span> <span class="t-green">igbinary</span>, <span class="t-green">opcache</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Content Index</span> <span class="t-dim">─────────────────────────────────────</span>

  <span class="t-dim">Status:</span>     <span class="t-green">● Fresh</span>
  <span class="t-dim">Mode:</span>       <span class="t-white">auto</span>
  <span class="t-dim">Backend:</span>    <span class="t-cyan">Sqlite</span> <span class="t-dim">(auto-detected)</span>
  <span class="t-dim">Cache:</span>      <span class="t-dim">SQLite</span> 892 KB
  <span class="t-dim">Built:</span>      <span class="t-dim">2024-12-28 14:30:00</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Content</span> <span class="t-dim">───────────────────────────────────────────</span>

  <span class="t-dim">◆ Page:</span> <span class="t-white">5 published</span>
  <span class="t-dim">◆ Post:</span> <span class="t-white">38 published</span> <span class="t-yellow">(4 drafts)</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Taxonomies</span> <span class="t-dim">────────────────────────────────────────</span>

  <span class="t-dim">◆ Category:</span> <span class="t-white">8 terms</span>
  <span class="t-dim">◆ Tag:</span> <span class="t-white">23 terms</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Webpage Cache</span> <span class="t-dim">────────────────────────────────────────</span>

  <span class="t-dim">Status:</span>     <span class="t-green">● Enabled</span>
  <span class="t-dim">TTL:</span>        <span class="t-white">Forever (until cleared)</span>
  <span class="t-dim">Cached:</span>     <span class="t-white">42 webpages</span>
  <span class="t-dim">Size:</span>       <span class="t-white">1.2 MB</span></samp></pre>

### rebuild

Rebuild the content index:

```bash
./ava rebuild

# Preserve existing webpage cache while rebuilding the index
./ava rebuild --keep-webpage-cache   # alias: --keep-webcache
```

<pre><samp>  <span class="t-green">✓</span> Rebuilding content index <span class="t-dim">(23ms)</span>

  <span class="t-green">✓ Content index rebuilt!</span></samp></pre>

Use this after deploying new content in production, or if something looks stuck.

What it does:

- Loads plugins first so rebuild hooks can run
- Rebuilds the content index (`$app->indexer()->rebuild()`)
- If `content_index.prerender_html` is enabled: writes `storage/cache/html_cache.bin` (otherwise deletes it)
- Clears the webpage cache at the end (so pages regenerate on the next request), unless the `--keep-webpage-cache` / `--keep-webcache` flag is supplied to preserve cached pages during the rebuild

Alternative invocation (if the `./ava` script isn’t executable): `php ava rebuild`.

### lint

Validate all content files for common problems:

```bash
./ava lint
```

<pre><samp>  🔍 Validating content files...

  <span class="t-green">╭────────────────────────────────╮
  │  All content files are valid!  │
  │  No issues found.              │
  ╰────────────────────────────────╯</span></samp></pre>

If there are issues, you'll see them listed with links to documentation:

<pre><samp>  🔍 Validating content files...

  <span class="t-red">✗ Found 2 issue(s):</span>

    <span class="t-red">•</span> <span class="t-white">posts/my-post.md:</span> Invalid status "archived" <span class="t-dim">— see https://ava.addy.zone/docs/content#status</span>
    <span class="t-red">•</span> <span class="t-white">pages/about.md:</span> Missing required field "slug" <span class="t-dim">— see https://ava.addy.zone/docs/content#frontmatter</span>

  <span class="t-yellow">💡 Tip:</span> Fix the issues above and run lint again</samp></pre>

Checks for:

| Check | What it means |
|-------|---------------|
| YAML syntax | Frontmatter must parse correctly |
| Required fields | `title`, `slug`, `status` are present |
| Status values | Must be `draft`, `published`, or `unlisted` |
| Slug format | Lowercase, alphanumeric, hyphens only |
| Duplicate slugs | Within the same content type |
| Duplicate IDs | Across all content |

## Content Creation

### make

Create new content with proper scaffolding:

```bash
./ava make <type> "Title"
```

Examples:

```bash
./ava make page "About Us"
./ava make post "Hello World"
```

<pre><samp>  <span class="t-green">╭───────────────────────────╮
  │  Created new post!        │
  ╰───────────────────────────╯</span>

  <span class="t-dim">File:</span>       <span class="t-white">content/posts/hello-world.md</span>
  <span class="t-dim">ID:</span>         <span class="t-white">01JGHK8M3Q4R5S6T7U8V9WXYZ</span>
  <span class="t-dim">Slug:</span>       <span class="t-cyan">hello-world</span>
  <span class="t-dim">Status:</span>     <span class="t-yellow">draft</span>

  <span class="t-yellow">💡 Tip:</span> Edit your content, then set status: published when ready</samp></pre>

Run without arguments to see available types:

```bash
./ava make
```

<pre><samp>  <span class="t-red">✗</span> Usage: ./ava make &lt;type&gt; "Title"

  <span class="t-bold">Available types:</span>

    <span class="t-cyan">▸ page</span> <span class="t-dim">— Pages</span>
    <span class="t-cyan">▸ post</span> <span class="t-dim">— Posts</span>

  <span class="t-bold">Example:</span>
    <span class="t-dim">./ava make post "My New Post"</span></samp></pre>

### prefix

Toggle date prefixes on content filenames:

```bash
./ava prefix <add|remove> [type]
```

Examples:

```bash
./ava prefix add post
```

<pre><samp>  Adding date prefixes...

    <span class="t-dim">→</span> <span class="t-white">hello-world.md</span> <span class="t-dim">→</span> <span class="t-cyan">2024-12-28-hello-world.md</span>
    <span class="t-dim">→</span> <span class="t-white">another-post.md</span> <span class="t-dim">→</span> <span class="t-cyan">2024-11-15-another-post.md</span>

  <span class="t-green">✓</span> Renamed 2 file(s)

  <span class="t-blue">→</span> <span class="t-cyan">./ava rebuild</span> <span class="t-dim">— Update the content index</span></samp></pre>

This reads the `date` field from frontmatter.

## User Management

Manage admin dashboard users. Users are stored in [`app/config/users.php`](/docs/configuration#users).

### user:add

Create a new admin user:

```bash
./ava user:add admin@example.com secretpass "Admin User"
```

<pre><samp>  <span class="t-green">╭─────────────────────────────────╮
  │  User created successfully!     │
  ╰─────────────────────────────────╯</span>

  <span class="t-dim">Email:</span>      <span class="t-cyan">admin@example.com</span>
  <span class="t-dim">Name:</span>       <span class="t-white">Admin User</span>

  <span class="t-blue">→</span> <span class="t-cyan">/ava-admin</span> <span class="t-dim">— Login at your admin dashboard</span></samp></pre>

**Password Security:** Your password is hashed using [bcrypt](https://en.wikipedia.org/wiki/Bcrypt) before being stored in `app/config/users.php`. This means:
- Your actual password is never saved—only an irreversible hash
- Even if someone accesses the users file, they can't recover your password
- Each password has a unique salt, so identical passwords have different hashes

### user:password

Update an existing user's password:

```bash
./ava user:password admin@example.com newpassword
```

<pre><samp>  <span class="t-green">✓</span> Password updated for: <span class="t-cyan">admin@example.com</span></samp></pre>

### user:remove

Remove a user:

```bash
./ava user:remove admin@example.com
```

<pre><samp>  <span class="t-green">✓</span> User removed: <span class="t-cyan">admin@example.com</span></samp></pre>

### user:list

List all configured users:

```bash
./ava user:list
```

<pre><samp>  <span class="t-dim">───</span> <span class="t-cyan t-bold">Users</span> <span class="t-dim">─────────────────────────────────────────────</span>

    <span class="t-cyan">◆ admin@example.com</span>
      <span class="t-dim">Name:</span> <span class="t-white">Admin User</span>
      <span class="t-dim">Created:</span> <span class="t-white">2024-12-28</span>

    <span class="t-cyan">◆ editor@example.com</span>
      <span class="t-dim">Name:</span> <span class="t-white">Editor</span>
      <span class="t-dim">Created:</span> <span class="t-white">2024-12-15</span></samp></pre>

## Updates

### update

Check for available updates (alias for `update:check`):

```bash
./ava update
```

Results are cached for 1 hour. Force a fresh check:

```bash
./ava update --force
```

<pre><samp>  🔍 Checking for updates...

  <span class="t-dim">Current:</span>    <span class="t-white">1.1.0</span>
  <span class="t-dim">Latest:</span>     <span class="t-green">1.1.1</span>

  <span class="t-green">╭───────────────────────╮
  │  Update available!    │
  ╰───────────────────────╯</span>

  <span class="t-dim">Release:</span>    <span class="t-white">v1.1.1</span>
  <span class="t-dim">Published:</span>  <span class="t-white">2024-12-28</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Changelog</span> <span class="t-dim">──────────────────────────────────────────</span>

  <span class="t-dim">-</span> Fixed page cache invalidation
  <span class="t-dim">-</span> Improved CLI output formatting
  <span class="t-dim">-</span> Added progress bars for bulk operations

  <span class="t-blue">→</span> <span class="t-cyan">./ava update:apply</span> <span class="t-dim">— Download and apply the update</span></samp></pre>

### update:apply

Download and apply the latest update:

```bash
./ava update:apply
```

<pre><samp>  <span class="t-dim">───</span> <span class="t-cyan t-bold">Update Available</span> <span class="t-dim">───────────────────────────────────</span>

  <span class="t-dim">From:</span>       <span class="t-white">1.1.0</span>
  <span class="t-dim">To:</span>         <span class="t-green">1.1.1</span>

  <span class="t-bold">Will be updated:</span>
    <span class="t-cyan">▸</span> Core files <span class="t-dim">(core/, bin/, bootstrap.php)</span>
    <span class="t-cyan">▸</span> Default theme <span class="t-dim">(app/themes/default/)</span>
    <span class="t-cyan">▸</span> Bundled plugins <span class="t-dim">(sitemap, feed, redirects)</span>
    <span class="t-cyan">▸</span> Documentation <span class="t-dim">(docs/)</span>

  <span class="t-bold">Will NOT be modified:</span>
    <span class="t-green">•</span> Your content <span class="t-dim">(content/)</span>
    <span class="t-green">•</span> Your configuration <span class="t-dim">(app/)</span>
    <span class="t-green">•</span> Custom themes and plugins
    <span class="t-green">•</span> Storage and cache files

  <span class="t-yellow">⚠️  Have you backed up your site and have a secure copy saved off-site?</span>
  <span class="t-dim">[y/N]:</span> <span class="t-green">y</span>

  Continue with update? <span class="t-dim">[y/N]:</span> <span class="t-green">y</span>

  <span class="t-green">✓</span> Downloading update <span class="t-dim">(342ms)</span>

  <span class="t-green">✓ Update applied successfully!</span>

  <span class="t-green">✓</span> Rebuilding content index <span class="t-dim">(18ms)</span>
  <span class="t-green">✓ Done!</span></samp></pre>

Skip confirmation with `-y` or `--yes`:

```bash
./ava update:apply -y
```

**Developer Mode (Not Recommended):** Update from the latest commit on the main branch instead of a release. This is only for testing unreleased features—never use `--dev` for production sites or fresh installs:

```bash
./ava update --dev
# or
./ava update:apply --dev
```

<pre><samp>  <span class="t-dim">───</span> <span class="t-cyan t-bold">Dev Update</span> <span class="t-dim">──────────────────────────────────────────</span>

  <span class="t-yellow">⚠️  Forcing update from latest commit on main branch</span>
      <span class="t-dim">This may include unstable or untested changes.</span>
      <span class="t-dim">Version checks are bypassed in dev mode.</span>

  <span class="t-dim">From:</span>       <span class="t-white">1.0.0</span>
  <span class="t-dim">To:</span>         <span class="t-yellow t-bold">main (latest commit)</span></samp></pre>

<div class="callout-warning">
<strong>Caution:</strong> Dev mode updates may contain untested or breaking changes. Only use this for testing or if you need a specific fix before the next release.
</div>

See [Updates](/docs/updates) for details on what gets updated and preserved.

### update:stale

Detect leftover files from older Ava CMS releases that are no longer needed:

```bash
./ava update:stale
```

<pre><samp>  <span class="t-green">✓</span> Scanning for stale files <span class="t-dim">(1.2s)</span>

  <span class="t-dim">Compared to:</span> <span class="t-white">v1.1.0</span>

  <span class="t-green">╭──────────────────────────╮
  │  No stale files found    │
  ╰──────────────────────────╯</span></samp></pre>

If stale files are detected:

<pre><samp>  <span class="t-green">✓</span> Scanning for stale files <span class="t-dim">(1.2s)</span>

  <span class="t-dim">Compared to:</span> <span class="t-white">v1.1.0</span>

  <span class="t-yellow">╭──────────────────────────────────╮
  │  Found 3 stale file(s)          │
  ╰──────────────────────────────────╯</span>

  <span class="t-cyan">•</span> core/OldClass.php
  <span class="t-cyan">•</span> core/Deprecated/Helper.php
  <span class="t-cyan">•</span> app/plugins/feed/old-template.php

  <span class="t-yellow">💡 Tip:</span> Review before deleting any files</samp></pre>

This command compares your local files against the latest release to identify files that were removed in newer versions. Use `--dev` to compare against the latest main branch commit instead of a release.

<div class="callout-info">
<strong>Note:</strong> This command cannot run if you use custom paths (non-default <code>app</code>, <code>content</code>, or <code>storage</code> locations). Review stale files carefully before deleting—some may be intentional customizations.
</div>

## Webpage Cache

Commands for managing the on-demand HTML webpage cache. This cache stores rendered webpages for all URLs on your site—not just the "Page" content type—including posts, archives, taxonomy pages, and custom content types.

### cache:stats

View webpage cache statistics:

```bash
./ava cache:stats
```

<pre><samp>  <span class="t-dim">───</span> <span class="t-cyan t-bold">Webpage Cache</span> <span class="t-dim">─────────────────────────────────────</span>

  <span class="t-dim">Status:</span>     <span class="t-green">● Enabled</span>
  <span class="t-dim">TTL:</span>        <span class="t-white">Forever (until cleared)</span>

  <span class="t-dim">Cached:</span>     <span class="t-white">42 webpages</span>
  <span class="t-dim">Size:</span>       <span class="t-white">1.2 MB</span>
  <span class="t-dim">Oldest:</span>     <span class="t-white">2024-12-28 10:00:00</span>
  <span class="t-dim">Newest:</span>     <span class="t-white">2024-12-28 14:30:00</span></samp></pre>

### cache:clear

Clear cached webpages:

```bash
# Clear all cached webpages (with confirmation)
./ava cache:clear
```

<pre><samp>  Found <span class="t-white">42</span> cached webpage(s).

  Clear all cached webpages? <span class="t-dim">[y/N]:</span> <span class="t-green">y</span>

  <span class="t-green">✓</span> Cleared <span class="t-white">42</span> cached webpage(s)</samp></pre>

```bash
# Clear webpages matching a URL pattern
./ava cache:clear /blog/*
```

<pre><samp>  <span class="t-green">✓</span> Cleared <span class="t-white">15</span> webpage(s) matching: <span class="t-cyan">/blog/*</span></samp></pre>

The webpage cache is also automatically cleared when:
- You run `./ava rebuild`
- Content changes (in `content_index.mode = 'auto'`)

See [Performance](/docs/performance#page-cache-details) for details.

## Logs

Commands for managing log files in `storage/logs/`. Ava CMS automatically rotates log files when they exceed the configured size limit to prevent disk space issues.

### logs:stats

View log file statistics:

```bash
./ava logs:stats
```

<pre><samp>  <span class="t-dim">───</span> <span class="t-cyan t-bold">Logs</span> <span class="t-dim">───────────────────────────────────────────────</span>

  <span class="t-dim">indexer.log:</span>  <span class="t-white">245.3 KB</span> <span class="t-dim">(2 files) · 1,847 lines</span>
  <span class="t-dim">admin.log:</span>    <span class="t-white">12.1 KB</span> <span class="t-dim">· 89 lines</span>

  <span class="t-dim">Total:</span>        <span class="t-white">257.4 KB (3 files)</span>

  <span class="t-dim">Max Size:</span>     <span class="t-white">10 MB per log</span>
  <span class="t-dim">Max Files:</span>    <span class="t-white">3 rotated copies</span></samp></pre>

### logs:tail

Show the last lines of a log file:

```bash
# Show last 20 lines of indexer.log (default)
./ava logs:tail

# Show last 20 lines of a specific log
./ava logs:tail indexer.log

# Show last 50 lines
./ava logs:tail indexer -n 50

# Can also use -nN format
./ava logs:tail admin -n10
```

<pre><samp>  <span class="t-dim">───</span> <span class="t-cyan t-bold">indexer.log (last 20 lines)</span> <span class="t-dim">─────────────────────</span>

  <span class="t-dim">[2024-12-28T14:30:00+00:00]</span> Indexer errors:
    - Missing required field "slug" in posts/draft-post.md
    - Invalid date format in posts/old-post.md

  <span class="t-dim">[2024-12-28T15:45:00+00:00]</span> Indexer errors:
    - Duplicate ID found: posts/copy-of-post.md</samp></pre>

**Available logs:**

| Log File | Purpose |
|----------|---------|
| `indexer.log` | Content indexer errors and warnings |
| `admin.log` | Admin dashboard activity (logins, edits) |
| `error.log` | PHP errors and exceptions |

### logs:clear

Clear log files:

```bash
# Clear all logs (with confirmation)
./ava logs:clear
```

<pre><samp>  Found <span class="t-white">3</span> log file(s) <span class="t-dim">(257.4 KB)</span>.

  Clear all log files? <span class="t-dim">[y/N]:</span> <span class="t-green">y</span>

  <span class="t-green">✓</span> Cleared <span class="t-white">3</span> log file(s) <span class="t-dim">(257.4 KB)</span></samp></pre>

```bash
# Clear a specific log (and its rotated copies)
./ava logs:clear indexer.log
```

<pre><samp>  <span class="t-green">✓</span> Cleared <span class="t-white">2</span> log file(s) <span class="t-dim">(245.3 KB)</span></samp></pre>

### Log Rotation

Ava CMS automatically rotates log files to prevent them from growing too large. Configure rotation in `app/config/ava.php`:

```php
'logs' => [
    'max_size' => 10 * 1024 * 1024,  // 10 MB (default)
    'max_files' => 3,                 // Keep 3 rotated copies
],
```

When a log exceeds `max_size`, it's rotated:
- `indexer.log` → `indexer.log.1`
- `indexer.log.1` → `indexer.log.2` (etc.)
- Oldest files beyond `max_files` are deleted

See [Configuration - Logs](/docs/configuration#logs) for details.

## Testing

### test

Run the automated test suite:

```bash
./ava test
```

<pre><samp>  <span class="t-bold">Ava CMS Test Suite</span>
  <span class="t-dim">──────────────────────────────────────────────────</span>

  <span class="t-cyan">StrTest</span>

    <span class="t-green">✓</span> slug converts to lowercase
    <span class="t-green">✓</span> slug replaces spaces with separator
    <span class="t-green">✓</span> starts with returns true for match
    <span class="t-dim">...</span>

  <span class="t-dim">──────────────────────────────────────────────────</span>
  <span class="t-bold">Tests:</span> <span class="t-green">456 passed</span> <span class="t-dim">(95ms)</span></samp></pre>

**Options:**

| Option | Description |
|--------|-------------|
| `[filter]` | Filter tests by class name (e.g., `Str`, `Parser`) |
| `-q`, `--quiet` | Minimal output (useful for CI/CD) |
| `-v`, `--verbose` | Verbose output with more details |
| `--release` | Run release readiness checks |

**Examples:**

```bash
# Run all tests
./ava test

# Filter tests by class name
./ava test Str           # Run StrTest only
./ava test Parser        # Run ParserTest only
./ava test Request       # Run RequestTest only

# Quiet mode (useful for CI/CD)
./ava test --quiet
./ava test -q

# Release checks
./ava test --release
```

**Quiet mode output:**

<pre><samp>  <span class="t-bold">Ava CMS Test Suite</span>
  <span class="t-dim">──────────────────────────────────────────────────</span>
  <span class="t-bold">Tests:</span> <span class="t-green">456 passed</span> <span class="t-dim">(95ms)</span></samp></pre>

**Failed test output:**

<pre><samp>  <span class="t-bold">Ava CMS Test Suite</span>
  <span class="t-dim">──────────────────────────────────────────────────</span>
  <span class="t-bold">Tests:</span> <span class="t-green">455 passed</span>, <span class="t-red">1 failed</span> <span class="t-dim">(100ms)</span>

  <span class="t-red t-bold">Failures:</span>

  <span class="t-red">1) Ava\Tests\Core\ExampleTest::testSomething</span>
     Expected true, got false
     <span class="t-dim">at /path/to/core/Testing/TestCase.php:371</span></samp></pre>

**Release mode** (`--release`) runs all standard tests plus additional checks that verify the project is ready for release — including configuration defaults, gitignore rules, version numbers, and documentation. See [Releasing](/docs/releasing) for details.

See [Testing](/docs/testing) for details on writing tests and available assertions.

## Benchmarking

### benchmark

Test the performance of your content index:

```bash
./ava benchmark
# or
./ava stress:benchmark
```

<pre><samp><span class="t-cyan">   ▄▄▄  ▄▄ ▄▄  ▄▄▄     ▄▄▄▄ ▄▄   ▄▄  ▄▄▄▄
  ██▀██ ██▄██ ██▀██   ██▀▀▀ ██▀▄▀██ ███▄▄
  ██▀██  ▀█▀  ██▀██   ▀████ ██   ██ ▄▄██▀</span>   <span class="t-dim">v1.1.0</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Performance Benchmark</span> <span class="t-dim">───────────────────────────────</span>

  <span class="t-dim">Content:</span>    <span class="t-cyan">1,003</span> items
              page: 2
              post: 1001

  <span class="t-dim">Backend:</span>    <span class="t-cyan">array + igbinary</span>
  <span class="t-dim">igbinary:</span>   <span class="t-green">enabled</span>
  <span class="t-dim">Iterations:</span> 5

  Testing array + igbinary...

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Results</span> <span class="t-dim">──────────────────────────────────────────────</span>

  <span class="t-bold">Test                array + igbinary</span>
  <span class="t-dim">──────────────────────────────────────</span>
  Count               2.2ms
  Get by slug         3.5ms
  Recent (page 1)     0.14ms
  Archive (page 50, beyond recent cache)   7.4ms
  Sort by date        9.7ms
  Sort by title       10.5ms
  Search              7.3ms
  <span class="t-dim">──────────────────────────────────────</span>
  Build index         45ms
  Memory              124 KB
  Cache size          592.2 KB

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Webpage Rendering</span> <span class="t-dim">─────────────────────────────────</span>

  <span class="t-bold">Operation                     Time</span>
  <span class="t-dim">─────────────────────────────────────────────</span>
  Render post (uncached)        4.9ms
  Cache write                   0.12ms
  Cache read (HIT)              0.02ms

  <span class="t-yellow">💡 Tip:</span> Run with <span class="t-cyan">--compare</span> to test all backends.
  <span class="t-blue">📚 Docs:</span> https://ava.addy.zone/docs/performance</samp></pre>

**Options:**

| Option | Description |
|--------|-------------|
| `--compare` | Compare all available backends side-by-side |
| `--iterations=N` | Number of test iterations (default: 5) |

**What it tests:**

*Content Index:*
- **Build index** — Time to rebuild the content index
- **Count** — Counting all posts
- **Get by slug** — Fetching a single post by URL
- **Recent (page 1)** — Homepage/recent posts (uses fast cache)
- **Archive (page 50, beyond recent cache)** — Deep pagination (loads full index)
- **Sort by date** — Sorting all posts by date
- **Sort by title** — Sorting all posts by title
- **Search** — Full-text search across content

*Webpage Rendering:*
- **Render post (uncached)** — Full render pipeline (load item, markdown, template)
- **Cache write** — Time to write rendered HTML to disk
- **Cache read (HIT)** — Time to serve a cached page

**Typical workflow:**

```bash
# Generate test content at your target scale
./ava stress:generate post 10000

# Run benchmark on current backend
./ava benchmark

# Compare all backends (rebuilds for each)
./ava benchmark --compare

# Clean up when done
./ava stress:clean post
```

See [Performance](/docs/performance) for detailed benchmark results and backend recommendations.

## Stress Testing

Commands for testing performance with large amounts of content.

### stress:generate

Generate dummy content for stress testing:

```bash
./ava stress:generate post 100
```

<pre><samp>  🧪 Generating <span class="t-white">100</span> dummy post(s)...

  <span class="t-green">[████████████████████████████████]</span> <span class="t-white">100%</span> Creating posts...

  <span class="t-green">✓</span> Generated <span class="t-white">100</span> files in <span class="t-dim">245ms</span>

  <span class="t-green">✓</span> Rebuilding content index <span class="t-dim">(89ms)</span>

  <span class="t-blue">→</span> <span class="t-cyan">./ava stress:clean post</span> <span class="t-dim">— Remove generated content when done</span></samp></pre>

Generated content includes:
- Random lorem ipsum titles and content
- Random dates (within last 2 years for dated types)
- Random taxonomy terms from configured taxonomies
- 80% published, 20% draft status
- Files prefixed with `_dummy-` for easy identification

### stress:clean

Remove all generated test content:

```bash
./ava stress:clean post
```

<pre><samp>  Found <span class="t-white">100</span> dummy content file(s).

  Delete all? <span class="t-dim">[y/N]:</span> <span class="t-green">y</span>

  <span class="t-green">[████████████████████████████████]</span> <span class="t-white">100%</span> Deleting files...

  <span class="t-green">✓</span> Deleted <span class="t-white">100</span> file(s)

  <span class="t-green">✓</span> Rebuilding content index <span class="t-dim">(12ms)</span>
  <span class="t-green">✓ Done!</span></samp></pre>

## Plugin Commands

Enabled plugins can register their own CLI commands. When you run `./ava --help`, plugin commands appear in the **Plugins** section.

The bundled plugins provide these commands:

| Command | Plugin | Description |
|---------|--------|-------------|
| `sitemap:stats` | sitemap | Show sitemap statistics |
| `feed:stats` | feed | Show RSS feed statistics |
| `redirects:list` | redirects | List all configured redirects |
| `redirects:add <from> <to> [code]` | redirects | Add a redirect |
| `redirects:remove <from>` | redirects | Remove a redirect |

For detailed documentation and examples of each plugin command, see the [Bundled Plugins](/docs/bundled-plugins) reference.

### Creating Plugin Commands

Plugins can register CLI commands by including a `commands` key in their `plugin.php`. See [Creating Plugins](/docs/creating-plugins#cli-commands) for details.

```php
return [
    'name' => 'My Plugin',
    'commands' => [
        [
            'name' => 'myplugin:status',
            'description' => 'Show plugin status',
            'handler' => function ($args, $cli, $app) {
                $cli->info("Plugin is running!");
                return 0; // Exit code
            },
        ],
    ],
];
```

## Exit Codes

| Code | Meaning |
|------|---------|
| 0 | Success |
| 1 | Error (invalid arguments, validation failures, etc.) |

## Quick Reference

### Core Commands

| Command | Description |
|---------|-------------|
| `--help` (or `-h`, `help`) | Show all available commands |
| `--version` (or `-v`, `version`) | Show Ava CMS version |
| `status` | Show site overview and health |
| `rebuild` | Rebuild the [content index](/docs/performance#content-indexing) |
| `lint` | Validate [content files](/docs/content) |

### Content Commands

| Command | Description |
|---------|-------------|
| `make <type> "Title"` | Create new content |
| `prefix <add\|remove> [type]` | Toggle date prefixes on filenames |

### User Management

| Command | Description |
|---------|-------------|
| `user:add <email> <pass> [name]` | Create admin user |
| `user:password <email> <pass>` | Update user password |
| `user:remove <email>` | Remove admin user |
| `user:list` (or `user`) | List all users |

### Updates

| Command | Description |
|---------|-------------|
| `update:check [--force]` (or `update`) | Check for updates |
| `update:apply [-y] [--dev]` | Apply available update |
| `update:stale [--dev]` | Detect stale files from older releases |

### Webpage Cache

| Command | Description |
|---------|-------------|
| `cache:stats` (or `cache`) | Webpage cache statistics |
| `cache:clear [pattern]` | Clear webpage cache |

### Logs

| Command | Description |
|---------|-------------|
| `logs:stats` (or `logs`) | Log file statistics |
| `logs:tail [name] [-n N]` | Show last lines of a log |
| `logs:clear [name]` | Clear log files |

### Testing & Benchmarking

| Command | Description |
|---------|-------------|
| `test [filter] [-q] [--release]` | Run the [test suite](/docs/testing) |
| `benchmark [--compare] [--iterations=N]` | Test content index [performance](/docs/performance#benchmark-comparison) |
| `stress:generate <type> <count>` | Generate test content |
| `stress:clean <type>` | Remove test content |
| `stress:benchmark` | Alias for `benchmark` |

### Plugin Commands

Enabled plugins can add their own CLI commands. See [Bundled Plugins](/docs/bundled-plugins) for full documentation.

| Command | Plugin | Description |
|---------|--------|-------------|
| `sitemap:stats` | sitemap | Show sitemap statistics |
| `feed:stats` | feed | Show RSS feed statistics |
| `redirects:list` | redirects | List all redirects |
| `redirects:add <from> <to> [code]` | redirects | Add a redirect |
| `redirects:remove <from>` | redirects | Remove a redirect |


## Colour Theme

The CLI uses a configurable colour theme for the banner, section headers, and highlights. Set your preferred theme in `app/config/ava.php`:

```php
'cli' => [
    'theme' => 'cyan',  // cyan, pink, purple, green, blue, amber, disabled
],
```

Use `disabled` for CI/CD pipelines or terminals that don't support ANSI colours.

## Common Workflows

### Initial Setup

```bash
# Create your first admin user
./ava user:add admin@example.com secretpassword "Admin"

# Check site status
./ava status

# Validate all content
./ava lint
```

### Development

```bash
# Start dev server
php -S localhost:8000 -t public

# Content index rebuilds automatically when files change
# (when content_index.mode is 'auto')

# Create new content
./ava make page "About Us"
./ava make post "Hello World"

# Check for issues
./ava lint
```

### Production Deploy

```bash
# In production, set content_index.mode to 'never' in config
# Then rebuild after deploying new content:
./ava rebuild

# Clear webpage cache if needed
./ava cache:clear
```

### Content Validation

```bash
# Before committing content changes:
./ava lint

# Check site health
./ava status

# If errors found, fix and re-run
```

### Performance Testing

```bash
# Generate test content at your target scale
./ava stress:generate post 1000

# Run benchmark
./ava benchmark

# Compare all backends
./ava benchmark --compare

# Clean up when done
./ava stress:clean post
```

### User Management

```bash
# Add a new user
./ava user:add editor@example.com password123 "Editor"

# Change a password
./ava user:password editor@example.com newpassword

# List all users
./ava user

# Remove a user
./ava user:remove editor@example.com
```

### Updating Ava CMS

```bash
# Check for updates
./ava update

# Force a fresh check (bypass cache)
./ava update --force

# Apply update (with confirmation)
./ava update:apply

# Apply update without prompts (for scripts)
./ava update:apply -y
```

### Troubleshooting

```bash
# Check site health
./ava status

# View recent errors
./ava logs:tail indexer -n 50

# View all log stats
./ava logs

# Clear caches and rebuild
./ava cache:clear
./ava rebuild

# After updating, check for stale files
./ava update:stale
```

<div class="related-docs">
<h2>Related Documentation</h2>
<ul>
<li><a href="/docs/performance">Performance</a> — Caching and optimization</li>
<li><a href="/docs/content">Content</a> — Writing and organizing content</li>
<li><a href="/docs/testing">Testing</a> — Running the test suite</li>
<li><a href="/docs/updating">Updating</a> — Keeping Ava CMS current</li>
</ul>
</div>
