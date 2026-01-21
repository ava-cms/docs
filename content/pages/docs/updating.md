---
title: Updating
slug: updating
status: published
meta_title: Updating | Ava CMS
meta_description: Keep Ava CMS up to date with the built-in CLI updater. Learn update commands, requirements, and backup strategies for safe upgrades.
excerpt: Keeping Ava CMS up to date is easy. Use the CLI to check for updates and apply them, with backup reminders and safe file handling.
redirect_from:
  - /docs/updates
---

Keeping Ava CMS up to date is easy. We release updates regularly with new features and bug fixes.

<div class="callout-warning">
<strong>Always ensure you have a good backup before attempting to update.</strong> Ava CMS is in fairly early development and while we hope the updater will continue to seamlessly carry you through future versions, breaking changes may occur. See <a href="#backup-strategies">Backup Strategies</a> below.
</div>

<div class="callout-info">Anybody on an early dated release (v25.12.X) should manually update to v1+ by following the <a href="#content-manual-updates">manual update instructions</a>.
</div>

## How to Update

The easiest way is using the [CLI](/docs/cli):

```bash
# 1. Check for updates
./ava update:check

# 2. Apply the update
./ava update:apply

# 3. Detect stale files left from older releases
./ava update:stale
```

By default, the CLI will ask you to confirm you have a backup before proceeding.

Under the hood, Ava CMS downloads an update ZIP from GitHub, extracts it into `storage/tmp/`, then copies a curated set of files into your install.

<div class="callout-info">
<strong>Requirements:</strong> Updates require the PHP <code>ZipArchive</code> extension. If it is missing, <code>update:apply</code> will fail.
</div>

### Update Options

```bash
# Force a fresh update check (bypass the 1-hour cache)
./ava update:check --force

# Apply update without interactive prompts
./ava update:apply --yes

# Apply update from the latest commit on the main branch (unstable)
./ava update:apply --dev
```

<div class="callout-warning">
<strong>Important:</strong> While the updater is designed to preserve your files, things can go wrong—especially during early development. Always have a backup before updating. If an update fails midway, you can restore from backup and try again, or do a <a href="#manual-updates">manual update</a>.
</div>

## Manual Updates

If you prefer not to use the built-in updater:

1. Download the [latest release](https://github.com/avacms/ava/releases) from GitHub
2. Extract and copy the files listed in "[What Gets Updated](#content-what-gets-updated)" into your install
3. Run `./ava rebuild` to rebuild the content index
4. Run `composer install` if `composer.json` changed


## Backup Strategies

Because Ava CMS is a flat-file CMS, backing up is incredibly simple. You don't need to dump databases or export complex configurations. You just need to copy files.

<details class="beginner-box">
<summary>What Should I Back Up?</summary>
<div class="beginner-box-content">

## What Should I Back Up?

The most important folders to back up are:

- **`content/`** — Your markdown content, taxonomies and structure
- **`app/`** — Your sites custom code (config, themes, plugins, snippets)
- **`public/`** — Any custom files you added here (like media files, `robots.txt`)

Most other things (like `core/`, `vendor/`, `storage/cache/`) can be regenerated or re-downloaded.

### The 3-2-1 Rule

For important data, consider the [**3-2-1 Backup Rule**](https://www.backblaze.com/blog/the-3-2-1-backup-strategy/):

- **3 Copies:** Your live site plus at least two backups
- **2 Different Places:** Store them on different types of storage (e.g., cloud + local)
- **1 Off-Site:** Keep at least one copy somewhere other than your server

</div>
</details>

Here are some backup approaches, from simplest to most automated.

### 1. Download a Copy (Simple)

Just download your files and keep them safe somewhere.

**How:**
- Use SFTP to download your site folder
- Or use your host's file manager to create and download a ZIP
- Or via command line: `zip -r backup-$(date +%Y-%m-%d).zip .`

**Pros:** Quick, works anywhere, no setup required.  
**Cons:** Manual effort, easy to forget, stored on same server until you download it.

### 2. Git Repository

If you're already using Git, your remote repository (GitHub, GitLab, etc.) is a natural backup.

**How:** Commit your changes and push to a remote repository.

```bash
git add .
git commit -m "Backup before update"
git push origin main
```

**Pros:** Automatic history of every change, off-site storage, easy to roll back.  
**Cons:** Requires Git knowledge, you need to remember to commit and push.

### 3. Cloud Sync (Set and Forget)

For production sites, consider automated sync to cloud storage.

**Options:**
- Use tools like `rclone` or `rsync` to sync folders
- Many hosts offer automated backups (check your control panel)
- Cloud services like Dropbox, Google Drive, or S3 can sync automatically

**Example with rclone:**
```bash
rclone sync ./content remote:my-ava-backups/content
rclone sync ./app remote:my-ava-backups/app
```

**Pros:** Automatic, protects against server failure.
**Cons:** Requires initial setup, may have small storage costs.

### Which Should I Choose?

| Approach | Best For |
|----------|----------|
| **Download a copy** | Occasional backups, before updates |
| **Git repository** | Developers, version tracking, collaboration |
| **Cloud sync** | Production sites, automated protection |

Many people combine approaches—Git for development history, plus periodic manual downloads before big changes.

## What Gets Updated?

The updater only syncs a curated set of core/runtime files. It is intentionally conservative and leaves your content and site configuration untouched.

**Updated (copied from the release into your install):**
- `core/` — The Ava CMS engine
- `ava` — The CLI entrypoint script
- `bootstrap.php` — Bootstrap file (includes the `AVA_VERSION` constant)
- `composer.json` — Dependencies manifest
- `public/index.php` — Front controller / entry point

**Bundled plugins** are updated in `app/plugins/<name>/` (Ava looks for them in `app/plugins/` and falls back to `plugins/` in older releases), currently:
- `app/plugins/sitemap/`
- `app/plugins/feed/`
- `app/plugins/redirects/`

If a release adds a **new bundled plugin** and you do not already have a folder for it, the updater will copy it into `app/plugins/<new-plugin>/`. New plugins are not automatically enabled — you activate plugins via your config.

**Preserved (not targeted by the updater):**
- `content/` — Your pages, posts, and media
- `app/config/` — Your configuration
- `app/themes/` — Your themes
- `app/snippets/` — Your snippets
- `storage/` — Cache, logs, temp files
- `vendor/` — Installed PHP dependencies
- `.git/`, `.env`
- `public/robots.txt`

<div class="callout-info">
<strong>After updating:</strong> The updater copies files but does not delete old files that no longer exist in newer versions.
If a file was renamed or removed between versions, you may need to delete the old file manually (use <code>update:stale</code> to detect stale files).
</div>

## Version Numbers

Ava CMS attempts to use Semantic Versioning (SemVer): `MAJOR.MINOR.PATCH`, although we are still in early development and the versioning is not yet strict. Check release notes for details.

## Troubleshooting

### "Could not fetch release info from GitHub"

- Check your internet connection
- GitHub API may be rate-limited (60 requests/hour for unauthenticated)
- Try again in a few minutes

### Update fails mid-way

If an update fails partway through:

1. Restore from your backup (this is why backups are essential!)
2. Or try running the update again
3. Or do a [manual update](#manual-updates)

Your content and configuration are in separate directories from core files, so they're less likely to be affected—but with any file operations, there's always some risk.

### After updating, site shows errors

1. Run `composer install` to update dependencies
2. Run `./ava rebuild` to rebuild the content index
3. Check the changelog for breaking changes


### After updating, custom files are missing

1. If you modified core files (not recommended), your changes may have been overwritten. Restore from backup.
2. Custom themes, plugins and custom files in the public folder should be unaffected, but always double-check. Set-ups can be inconsistent across different sites and servers and the updater may not cover every edge case.
3. Restore from backup if necessary.


## Need Help?

Updates not working? Something broken? Join the [Discord community](https://discord.gg/fZwW4jBVh5)—we're happy to help troubleshoot and get you back on track.
