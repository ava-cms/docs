---
title: Community Plugins
status: published
meta_title: Community Plugins | Ava CMS
meta_description: Explore plugins created by the Ava CMS community. Extend Ava CMS with extra features by dropping plugins into your plugins folder and enabling them in your config.
excerpt: Community-made plugins for Ava CMS. Install by adding the plugin folder to your plugins directory and enabling it in app/config/ava.php.
---

Plugins created by the Ava CMS community. These extend Ava CMS with additional functionality. You can use these by dropping them into your `app/plugins/` folder and enabling them in your config.

<div class="callout-warning"><strong>Security warning:</strong> Community plugins are third-party code and are <strong>not vetted or audited</strong> by the Ava CMS project. A plugin can execute PHP with the same permissions as your site (and may change routing, templates, headers, file access, etc). Only install plugins from authors you trust, review the source before deploying, and test in a staging environment.</div>

## Available Plugins

| Plugin | Description | Author |
|--------|-------------|--------|
| [Sitemap](/docs/bundled-plugins#sitemap) (bundled) | Automatic XML sitemap generation for search engines | [Ava CMS](https://github.com/avacms) |
| [Feed](/docs/bundled-plugins#rss-feed) (bundled) | RSS/Atom feeds for blog subscriptions | [Ava CMS](https://github.com/avacms) |
| [Redirects](/docs/bundled-plugins#redirects) (bundled) | Manage URL redirects from the admin panel | [Ava CMS](https://github.com/avacms) |
| [Ava CMS for Cloudflare®](https://github.com/avacms/ava-for-cloudflare) | Automatically purge your Cloudflare cache when content is rebuilt. | [Adam Greenough](https://github.com/adamgreenough) |

## Submit Your Plugin

Built a plugin you'd like to share? We'd love to list it here!

<ul class="feature-list">
    <li>Plugin name and a short description of what it does</li>
    <li>Link to the GitHub repo (or other download source)</li>
    <li>Which Ava CMS version you tested it with</li>
    <li>A brief installation guide (or include it in your repo's README)</li>
</ul>

**How to submit:**
- [Open an issue on GitHub](https://github.com/avacms/ava/issues) with the label "community-plugin"
- Or share it in [Discord](https://discord.gg/fZwW4jBVh5)

## Plugin Development

Want to create your own plugin? See the [Creating Plugins](/docs/creating-plugins) guide for a complete walkthrough, or browse the [bundled plugins](/docs/bundled-plugins) source code for working examples.
