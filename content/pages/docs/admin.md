---
title: Admin Dashboard
status: published
meta_title: Admin Dashboard | Flat-file PHP CMS | Ava CMS
meta_description: Admin dashboard for Ava CMS. Full content editor with custom field support, media management, taxonomy terms, and system monitoring.
---
Ava CMS includes an admin dashboard — a complete content management interface that makes working with your site a breeze. Create and edit content with full custom field support, manage media, organize taxonomies, and monitor your site's health.

<div class="screenshot-window">
<a href="@media:admin-dashboard.webp" target="_blank" rel="noopener">
    <img src="@media:admin-dashboard.webp" alt="Ava CMS admin dashboard showing content stats, cache status, and recent content" />
</a>
</div>

The admin is especially powerful when working with [custom fields](/docs/fields) — if you have configured advanced custom field arrangements that could be complex to write as raw YAML, you can use simple form inputs with validation, dropdowns, and media pickers.

<div class="callout-info">
<strong>Your Markdown files remain the source of truth.</strong> The admin reads and writes the same files you'd edit by hand — it's just a friendlier way to do it.
</div>

## Getting Started

### 1. Enable the Dashboard

The admin is disabled by default. Enable it in `app/config/ava.php`:

```php
'admin' => [
    'enabled' => true,
    'path' => '/ava-admin',
],
```

You can change `path` to any URL (e.g., `/dashboard` or `/manage`).

See [Configuration](/docs/configuration) for all admin settings.*

### 2. Create a User

Users are stored in a config file (no database). Create one with the CLI:

```bash
./ava user:add admin@example.com yourpassword "Your Name"
```

This creates `app/config/users.php` with a securely hashed password. The file is gitignored by default.

See [CLI Reference](/docs/cli#content-user-management) for all user commands (`user:list`, `user:password`, `user:delete`).*

<details class="beginner-box">
<summary>No SSH access? Create users manually</summary>
<div class="beginner-box-content">

If your host doesn't provide SSH, you have two options:

**Option A: Run CLI locally, then upload**  
Clone your site locally, run `./ava user:add ...`, then upload `app/config/users.php` to your server.

**Option B: Create the file manually**

Create `app/config/users.php`:

```php
<?php

declare(strict_types=1);

return [
    'admin@example.com' => [
        'password' => '$2y$12$REPLACE_WITH_BCRYPT_HASH',
        'name' => 'Admin',
        'created' => '2026-01-12',
    ],
];
```

Generate the password hash on any machine with PHP:

```bash
php -r 'echo password_hash("your-password", PASSWORD_BCRYPT, ["cost" => 12]), PHP_EOL;'
```

Copy the output (starts with `$2y$12$...`) into the `password` field.

<div class="callout-warning">
<strong>Password requirements:</strong> At least 8 characters. After first login, Ava adds a <code>last_login</code> field automatically.
</div>

</div>
</details>

### 3. Log In

Visit `/ava-admin` (or your custom path) and log in with your email and password.

## Install as an app (PWA)

The admin dashboard supports installation as a basic **PWA (Progressive Web App)** on mobile and desktop browsers.

That means you can add it to your home screen and launch it like an app:
- Opens in a standalone window (less browser chrome)
- Keeps the admin “pinned” for quick access
- Can cache some admin assets for faster loads

<div class="callout-info">
<strong>Scope:</strong> The PWA is scoped to the admin area (e.g. <code>/ava-admin</code>). Your public site remains a normal website.
</div>

#### iPhone / iPad (Safari)

1. Open your admin URL in Safari (e.g. `https://example.com/ava-admin`).
2. Tap the <strong>Share</strong> button.
3. Tap <strong>Add to Home Screen</strong>.
4. Launch “Ava Admin” from your home screen.

#### Android (Chrome)

1. Open your admin URL in Chrome.
2. Tap the menu (<strong>⋮</strong>).
3. Tap <strong>Install app</strong> (or <strong>Add to Home screen</strong>).

#### Desktop (Chrome / Edge)

Open the admin dashboard, then use the browser’s <strong>Install</strong> option (often an install icon in the address bar, or <strong>⋮ → Install</strong>).

<div class="callout-warning">
<strong>Good to know:</strong> This is still your live admin site. You’ll generally need an internet connection to browse and save content. If you don’t see an install option, make sure you’re using <code>https://</code> and a supported browser (Safari/Chrome).
</div>

## Features

### Content Editor

A full-featured content editor with custom field support, syntax highlighting, and live validation.

<div class="screenshot-window">
<a href="@media:admin-content.webp" target="_blank" rel="noopener">
    <img src="@media:admin-content.webp?2" alt="Content list — pages with status badges and dates" />
</a>
</div>

**Browse content** by type using the sidebar. Each list shows:
- Title and slug
- Status badge (published/draft)
- Date (for date-sorted content types)
- File path on disk

**Create new content** with the "+ New" button. The editor generates proper frontmatter and sets up all your custom fields automatically.

<div class="screenshot-window">
<a href="@media:admin-editor.webp" target="_blank" rel="noopener">
    <img src="@media:admin-editor.webp" alt="Content editor with custom fields and Markdown body" />
</a>
</div>

**Edit with custom fields** — This is where the admin really shines. Instead of writing raw YAML, you get:
- **Text inputs** for strings with character counts
- **Date/time pickers** for dates
- **Dropdowns** for select fields and taxonomy terms
- **Checkboxes** for boolean values
- **Image pickers** that browse your media library
- **Repeaters** for arrays and lists
- **Real-time validation** that catches errors as you type

**Syntax-highlighted editor** for the Markdown body with frontmatter support.

**Preview drafts** instantly. Drafts use a [preview token](/docs/configuration#content-security) so only authorized users can see unpublished content.

See [Fields](/docs/fields) to learn about defining custom fields for your content types.*

<div class="callout-warning">
<strong>Content safety:</strong> The admin blocks potentially dangerous HTML like <code>&lt;script&gt;</code>, <code>&lt;iframe&gt;</code>, and JavaScript event handlers. If you need advanced HTML, edit the file directly on disk.
</div>

<div class="callout-info">
For <strong>hierarchical</strong> content types (like docs), the URL comes from the file path, not the <code>slug:</code> field. See <a href="/docs/content#content-hierarchical-content">Hierarchical Content</a>.
</div>

### Media Library

Upload and manage images without leaving your browser.

<div class="screenshot-window">
<a href="@media:admin-media.webp" target="_blank" rel="noopener">
    <img src="@media:admin-media.webp" alt="Media library with grid view and upload dropzone" />
</a>
</div>

**Features:**
- **Drag & drop** images or click to browse
- **Folder organization** — create subfolders or use automatic date-based organization
- **Copy shortlinks** — click any image to copy its `@media:filename.jpg` path
- **Grid or list view** — toggle between visual and detailed views

**Supported formats:** JPG, PNG, GIF, WebP, SVG, AVIF

The upload size limit depends on your PHP configuration — the media page shows your current limits.

<div class="callout-info">
<strong>Requires PHP image extension:</strong> Media uploads need either the <code>imagick</code> or <code>gd</code> PHP extension. Most hosts include these by default.
</div>

### Taxonomy Management

Create and manage taxonomy terms (categories, tags, topics, etc.) without editing files.

- **View terms** with content counts per term
- **Create new terms** with name, slug, and description
- **Delete unused terms** (terms with content show a warning first)

Terms are stored in `content/_taxonomies/{taxonomy}.yml`:

```yaml
- slug: tutorials
  name: Tutorials
  description: Step-by-step guides

- slug: php
  name: PHP
```

<div class="callout-info">
Deleting a term removes it from the registry but doesn't modify your content files.
</div>

See [Taxonomies](/docs/configuration#content-taxonomies-taxonomiesphp) for configuration options.*

### Content Linting

Check all your content for errors in one click.

The linter checks for:
- **YAML syntax** — Valid frontmatter structure
- **Required fields** — Title, slug, status
- **Custom fields** — Fields defined in your content type config
- **Slug format** — URL-safe lowercase alphanumeric
- **Unique IDs** — No duplicate content IDs across files

### Theme Info

See what's in your active theme at a glance.

<div class="screenshot-window">
<a href="@media:admin-theme.webp" target="_blank" rel="noopener">
    <img src="@media:admin-theme.webp" alt="Theme info page showing templates and assets" />
</a>
</div>

- **Templates** — All PHP template files with line counts
- **Assets** — CSS, JS, images, and fonts with file sizes
- **Shortcodes** — Available shortcodes and snippets with copy buttons

See [Theming](/docs/theming) to learn about creating themes.*

### System Info

Monitor your server and debug issues.

**Server stats:**
- Disk space usage
- Memory usage (current and peak)
- CPU load average
- Server uptime

**PHP info:**
- PHP version
- Key extensions (imagick, gd, opcache)
- Memory and upload limits

**Debugging:**
- Recent PHP errors (with one-click clear)
- Directory permission status
- Cache file details

### Admin Logs

Track activity in your admin panel.

- **Admin users** — List of all users with last login times
- **Recent activity** — Login/logout events, content changes, system actions
- **Security info** — IP addresses and browser info for audit trails

## Customization

### Admin Theme

Choose a color accent for the admin interface in `app/config/ava.php`:

```php
'admin' => [
    'enabled' => true,
    'path' => '/ava-admin',
    'theme' => 'cyan',  // cyan, blue, green, purple, orange, pink
],
```

The admin automatically respects your system's light/dark mode preference and can be toggled in the top bar.

---

## Extending the Admin

Plugins can extend the admin dashboard with custom pages, sidebar items, and functionality.

<div class="screenshot-window">
<a href="@media:plugin-redirects.webp" target="_blank" rel="noopener">
    <img src="@media:plugin-redirects.webp" alt="Plugin admin page: Redirects" />
</a>
</div>

**What plugins can add:**
- **Custom pages** — Full admin pages with your own UI
- **Sidebar items** — Links in the admin navigation
- **Dashboard widgets** — Cards on the main dashboard
- **Custom routes** — API endpoints for AJAX functionality

**Example: The Redirects plugin** adds a complete redirect management interface where you can create, edit, and delete URL redirections — all within the admin.

Building an admin page is straightforward:

```php
use Ava\Plugins\Hooks;

Hooks::addFilter('admin.register_pages', function(array $pages) {
    $pages['my-plugin'] = [
        'label' => 'My Plugin',
        'icon' => 'extension',
        'handler' => function($request, $app, $controller) {
            return $controller->renderPluginPage(
                ['title' => 'My Plugin'],
                '<div class="card"><div class="card-body">Your content</div></div>'
            );
        },
    ];
    return $pages;
});
```

See [Creating Plugins: Admin Pages](/docs/creating-plugins#content-admin-pages) for the complete guide.

## Security Information

<div class="callout-warning">
Ava CMS is provided as <a href="https://github.com/avacms/ava/blob/main/LICENSE">free, open-source software without warranty</a>. It is under active development and may contain bugs or security issues. You are responsible for reviewing, testing, and securing any deployment.
</div>

The admin dashboard includes a number of security-related measures intended to <strong>reduce common risks</strong>, but it should not be considered hardened, independently audited, or production-grade software. The sections below describe current behaviour and design choices, not guarantees of security.

### Password Storage

When you create a user with <code>./ava user:add</code>, Ava CMS currently handles passwords roughly as follows:

1. <strong>Password hashing</strong> — Passwords are processed using PHP’s <code>password_hash()</code> with bcrypt and a cost factor of 12.
2. <strong>Hash-only storage</strong> — The plaintext password is not written to disk; only the resulting hash is stored in <code>app/config/users.php</code>.
3. <strong>Explicit algorithm choice</strong> — <code>PASSWORD_BCRYPT</code> is used to provide predictable behaviour across supported PHP versions.

<strong>What this means in practice:</strong> If an attacker gains access to <code>users.php</code>, they obtain a <strong>password hash</strong>, not the original password. However, hashes can still be attacked using password-guessing techniques, especially if weak or reused passwords are used.

<div class="callout-info">
<strong>Use strong, unique passwords.</strong> Bcrypt is designed to make large-scale guessing slower, but it does not make weak passwords safe.
</div>

<strong>Treat <code>app/config/users.php</code> as sensitive data:</strong>
<ul>
  <li>Restrict access via file permissions, backups, and hosting control panels</li>
  <li>Do not commit the file to version control</li>
  <li>If the file is exposed, assume credentials may be compromised and rotate them promptly</li>
</ul>

Example stored value (shown for illustration only):

<pre><code class="language-php">
'password' => '$2a$12$erDlkVmb.CvQbJeQoAkwoej1FANMw2QTzf3h2/VI5acJYHcpPagJa'
</code></pre>

### HTTPS and Transport Security

Hashing protects stored passwords, but it does not protect credentials while they are being transmitted. Without HTTPS, login requests may travel over the network unencrypted and could be intercepted by third parties.

<div class="callout-info">
<strong>HTTPS is strongly recommended for production use.</strong> By default, the admin dashboard is configured to attempt to block HTTP access from non-localhost addresses and return a 403 response directing users to HTTPS. This behaviour is intended to reduce the risk of credentials and session cookies being transmitted unencrypted, but it can be affected by configuration and should be verified in your environment.
</div>

### Login & Session Handling

The admin includes several mechanisms intended to reduce common authentication risks, but these should be viewed as defensive measures rather than guarantees.

### CSRF Protection

Forms in the admin include a CSRF token intended to help distinguish legitimate requests from cross-site submissions.

---

<div class="callout-warning">
<strong>Project status:</strong> Ava CMS is an evolving project. Security-related behaviour may change between versions, and no part of the system should be assumed to be complete, audited, or suitable for high-risk or sensitive environments without independent review.
</div>
