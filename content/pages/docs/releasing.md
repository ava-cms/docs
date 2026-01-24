---
title: Releasing
slug: releasing
status: published
meta_title: Releasing | Flat-file PHP CMS | Ava CMS
meta_description: Guide for Ava CMS maintainers releasing new versions. Covers semantic versioning, release tests, version checks, and the complete release process.
excerpt: Release guide for Ava CMS maintainers. Covers semantic versioning, release tests, version checks, and the steps to publish a new version to GitHub.
---

This guide is for maintainers who are releasing a new version of Ava CMS.

## Versioning

Ava CMS uses Semantic Versioning: `MAJOR.MINOR.PATCH`.

Important: Ava CMS’s **release checks currently require a strict numeric SemVer** of the form:

- `^\d+\.\d+\.\d+$`

So versions like `1.0.0-beta.1` will fail release tests today.

Examples:

- `1.0.0` = Initial stable release
- `1.0.1` = Patch release with bug fixes
- `1.1.0` = Minor release with new features
- `2.0.0` = Major release with breaking changes

## How to Release

1. **Update Version:** Change `AVA_VERSION` in `bootstrap.php`.
2. **Install dependencies (if needed):** Ensure `vendor/` exists and `vendor/autoload.php` is present.
3. **Lint:** Run `./ava lint`.
4. **Test:** Run `./ava test`.
5. **Release Tests:** Run `./ava test --release`.
6. **Tag:** Create a git tag (recommended format: `v1.0.0`).
7. **Push:** Push the tag to GitHub.
8. **Release:** Create a GitHub Release from that tag.

That’s it. Ava CMS’s updater checks GitHub Releases and compares the installed version (from `AVA_VERSION`) to the latest release tag.

## Release Tests

The `--release` flag runs additional tests that verify the project is ready for public release:

```bash
./ava test --release
```

**What it checks:**

These checks are implemented in `core/tests/Release/ReleaseChecksTest.php`.

Note: these checks are intentionally opinionated. They validate the defaults of the **Ava CMS distribution / starter install** (safe config defaults, placeholder URLs/tokens, empty media directory, etc.). If you run them inside a real customised site (for example, a production docs site), you should expect them to fail unless you reset that site back to the starter defaults.

### Security & Git

- `.gitignore` should include `app/config/users.php` (or a `users.php` entry)
- `.gitignore` should include `.env`
- `.gitignore` should include `storage/cache` (or `/storage/cache/`)

### Default Configuration Expectations

The release suite expects a “fresh install / safe defaults” configuration in `app/config/ava.php`:

- `debug.enabled` is `false`
- `theme` is `default`
- `admin.enabled` is `false`
- `admin.path` is `/ava-admin`
- `admin.theme` is `cyan`
- `cli.theme` is `cyan`

And default site identity values:

- `site.name` is `My Ava CMS Site`
- `site.base_url` contains `localhost`
- `site.timezone` is `UTC`
- `site.locale` is `en_GB`

Security placeholder expectation:

- `security.preview_token` should be a placeholder value containing `your-preview-token` (or be empty)

### Version Checks

- `AVA_VERSION` must match strict `MAJOR.MINOR.PATCH`
- `AVA_VERSION` should be higher than the latest GitHub release

The “higher than GitHub” check requires the `curl` extension and network access. If `curl` is missing (or GitHub cannot be reached), the test is skipped.

### Project Structure

- Default theme directory exists: `app/themes/default/`
- Default theme has bootstrap file: `app/themes/default/theme.php`
- Example content exists: `content/pages/index.md`
- `app/config/users.php` should ideally be absent; if it exists, it must be gitignored
- `public/media/` should be empty (except `.gitkeep`)

### Documentation

- `README.md` must exist
- `LICENSE` must exist

### Dependencies

- `composer.json` must exist and be valid JSON (and contain a `name` field)
- `vendor/` must exist
- `vendor/autoload.php` must exist

## Changelog Format

Include a changelog in the release notes following this format:

```markdown
## What's New

- ✨ New feature description
- 🔧 Improvement description
- 🐛 Fixed issue description

## Breaking Changes

- ⚠️ Description of breaking change and migration steps

## New Bundled Plugins

- `plugin-name` — Brief description (not activated by default)
```

## What's Included in Releases

GitHub’s release zipball includes the repository contents.

When a user runs `./ava update:apply`, Ava CMS should **not** blindly overwrite their entire site. Instead, it downloads the zipball and synchronises a specific set of paths (see `core/Updater.php`).

[See Updates →](/docs/updates/) 

## Testing the Update Flow

Before releasing, test the update mechanism:

1. Create a test installation
2. Set it to an older version in `bootstrap.php`
3. Create a test release on GitHub
4. Run `./ava update:check --force`
5. Run `./ava update:apply`
6. Verify files were updated correctly
7. Verify user files were preserved

## Hotfix Releases

For urgent bug fixes:

1. Increment PATCH: `1.0.0` → `1.0.1`
2. Follow the normal release process
3. Note in changelog that it's a hotfix

## Pre-release / Beta

Pre-release versions are not supported by the current **release tests** (they require strict `MAJOR.MINOR.PATCH`).

The updater itself uses PHP’s `version_compare()` for comparisons, but if you want release tests to pass, stick to strict numeric SemVer.

## Repository Settings

Ensure the GitHub repository has:

- **Releases** enabled
- **Public** visibility (for API access without auth)
- Tags following the `v{VERSION}` format

## Troubleshooting

### Users can't fetch updates

- Ensure releases are published (not draft)
- Ensure repository is public
- Check GitHub API status

### Version comparison issues

The updater uses PHP’s `version_compare()` and strips a leading `v` from Git tags.

Numeric SemVer examples that compare as expected:
- `1.0.0` < `1.0.1` ✓
- `1.0.9` < `1.0.10` ✓
- `1.9.9` < `2.0.0` ✓

### Release tests failing locally

- If `./ava test --release` fails because `LICENSE` is missing, add a `LICENSE` file (the release suite requires it).
- If the “higher than GitHub” check is skipped, install/enable the `curl` extension so the suite can verify you’re not re-releasing an old version.

<div class="related-docs">
<h2>Related Documentation</h2>
<ul>
<li><a href="/docs/testing">Testing</a> — Running the test suite</li>
<li><a href="/docs/cli">CLI Reference</a> — Release-related commands</li>
<li><a href="/docs/updating">Updating</a> — How users receive updates</li>
</ul>
</div>