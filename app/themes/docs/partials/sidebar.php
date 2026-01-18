<?php
/**
 * Sidebar Navigation Partial
 *
 * Available variables:
 *   $request - The current HTTP request object
 *   $ava     - Template helper
 */

$currentPath = rtrim($request->path(), '/');
$currentPath = $currentPath === '' ? '/' : $currentPath;

$is_active = function (string $href, bool $prefix = false) use ($currentPath): bool {
    $href = rtrim($href, '/');
    if ($href === '') $href = '/';

    if ($prefix) {
        return $href === '/'
            ? $currentPath === '/'
            : ($currentPath === $href || str_starts_with($currentPath, $href . '/'));
    }

    return $currentPath === $href;
};

$active_attr = fn(bool $on) => $on ? ' class="active"' : '';
?>
<aside class="sidebar">
    <!-- Mobile-only: Top navigation links -->
    <nav class="sidebar-mobile-nav">
        <a href="/docs"<?= $active_attr($is_active('/docs', true)) ?>>Docs</a>
        <a href="/themes"<?= $active_attr($is_active('/themes', true)) ?>>Themes</a>
        <a href="/plugins"<?= $active_attr($is_active('/plugins', true)) ?>>Plugins</a>
        <a href="/showcase"<?= $active_attr($is_active('/showcase', true)) ?>>Showcase</a>

        <a href="https://github.com/AvaCMS/ava/releases"
           target="_blank" rel="noopener"
           class="external-link">
            Download
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left:4px; opacity:0.7;">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                <polyline points="15 3 21 3 21 9"></polyline>
                <line x1="10" y1="14" x2="21" y2="3"></line>
            </svg>
        </a>
    </nav>

    <nav class="sidebar-nav">
        <div class="nav-heading">Getting Started</div>
        <ul class="nav-section">
            <li><a href="/docs"<?= $active_attr($is_active('/docs') || $is_active('/docs/index')) ?>>Introduction</a></li>
            <li><a href="/docs/hosting"<?= $active_attr($is_active('/docs/hosting', true)) ?>>Hosting</a></li>
            <li><a href="/docs/configuration"<?= $active_attr($is_active('/docs/configuration', true)) ?>>Configuration</a></li>
            <li><a href="/docs/updating"<?= $active_attr($is_active('/docs/updating', true)) ?>>Updating</a></li>
            <li><a href="/docs/admin"<?= $active_attr($is_active('/docs/admin', true)) ?>>Admin Dashboard</a></li>
        </ul>

        <div class="nav-heading">Using Ava</div>
        <ul class="nav-section">
            <li><a href="/docs/content"<?= $active_attr($is_active('/docs/content', true)) ?>>Content</a></li>
            <li><a href="/docs/fields"<?= $active_attr($is_active('/docs/fields', true)) ?>>Fields</a></li>
            <li><a href="/docs/routing"<?= $active_attr($is_active('/docs/routing', true)) ?>>Routing</a></li>
            <li><a href="/docs/shortcodes"<?= $active_attr($is_active('/docs/shortcodes', true)) ?>>Shortcodes</a></li>
        </ul>

        <div class="nav-heading">Build & Extend</div>
        <ul class="nav-section">
            <li><a href="/docs/theming"<?= $active_attr($is_active('/docs/theming', true)) ?>>Theming</a></li>
            <li><a href="/docs/cli"<?= $active_attr($is_active('/docs/cli', true)) ?>>CLI</a></li>
            <li><a href="/docs/creating-plugins"<?= $active_attr($is_active('/docs/creating-plugins', true)) ?>>Extending</a></li>
        </ul>

        <div class="nav-heading">Reference</div>
        <ul class="nav-section">
            <li><a href="/docs/api"<?= $active_attr($is_active('/docs/api', true)) ?>>API</a></li>
            <li><a href="/docs/performance"<?= $active_attr($is_active('/docs/performance', true)) ?>>Performance</a></li>
            <li><a href="/docs/bundled-plugins"<?= $active_attr($is_active('/docs/bundled-plugins', true)) ?>>Bundled Plugins</a></li>
            <li><a href="/docs/markdown-reference"<?= $active_attr($is_active('/docs/markdown-reference', true)) ?>>Markdown Reference</a></li>
            <li><a href="/docs/ai-reference"<?= $active_attr($is_active('/docs/ai-reference', true)) ?>>AI Reference</a></li>
        </ul>

        <div class="nav-heading">Maintainers</div>
        <ul class="nav-section">
            <li><a href="/docs/testing"<?= $active_attr($is_active('/docs/testing', true)) ?>>Testing</a></li>
            <li><a href="/docs/releasing"<?= $active_attr($is_active('/docs/releasing', true)) ?>>Releasing</a></li>
        </ul>

    </nav>

</aside>
