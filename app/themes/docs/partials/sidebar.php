<?php
/**
 * Sidebar Navigation Partial
 * 
 * Renders the documentation sidebar with navigation links.
 * 
 * Available variables:
 *   $request - The current HTTP request object
 *   $ava     - Template helper
 * 
 * @see https://ava.addy.zone/docs/themes#partials
 */

$currentPath = $request->path();
?>
<aside class="sidebar">

    <!-- Mobile-only: Top navigation links -->
    <nav class="sidebar-mobile-nav">
        <a href="/docs"<?= str_starts_with($currentPath, '/docs') ? ' class="active"' : '' ?>>Docs</a>
        <a href="/themes"<?= $currentPath === '/themes' ? ' class="active"' : '' ?>>Themes</a>
        <a href="/plugins"<?= $currentPath === '/plugins' ? ' class="active"' : '' ?>>Plugins</a>
        <a href="/showcase"<?= $currentPath === '/showcase' ? ' class="active"' : '' ?>>Showcase</a>
    </nav>

    <nav class="sidebar-nav">
        <ul class="nav-section">
            <li><a href="/docs"<?= $currentPath === '/docs' || $currentPath === '/docs/index' ? ' class="active"' : '' ?>>Introduction</a></li>
            <li><a href="/docs/configuration"<?= str_contains($currentPath, 'configuration') ? ' class="active"' : '' ?>>Configuration</a></li>
            <li><a href="/docs/content"<?= $currentPath === '/docs/content' ? ' class="active"' : '' ?>>Content</a></li>
            <li><a href="/docs/cli"<?= str_contains($currentPath, 'cli') ? ' class="active"' : '' ?>>CLI</a></li>
            <li><a href="/docs/admin"<?= str_contains($currentPath, 'admin') ? ' class="active"' : '' ?>>Admin</a></li>
            <li><a href="/docs/theming"<?= str_contains($currentPath, 'theming') ? ' class="active"' : '' ?>>Theming</a></li>
            <li><a href="/docs/routing"<?= str_contains($currentPath, 'routing') ? ' class="active"' : '' ?>>Routing</a></li>
            <li><a href="/docs/shortcodes"<?= str_contains($currentPath, 'shortcodes') ? ' class="active"' : '' ?>>Shortcodes</a></li>
            <li><a href="/docs/creating-plugins"<?= str_contains($currentPath, 'creating-plugins') ? ' class="active"' : '' ?>>Extending</a></li>
            <li><a href="/docs/bundled-plugins"<?= str_contains($currentPath, 'bundled-plugins') ? ' class="active"' : '' ?>>Bundled Plugins</a></li>
            <li><a href="/docs/performance"<?= str_contains($currentPath, 'performance') ? ' class="active"' : '' ?>>Performance</a></li>
            <li><a href="/docs/hosting"<?= str_contains($currentPath, 'hosting') ? ' class="active"' : '' ?>>Hosting</a></li>
            <li><a href="/docs/updates"<?= str_contains($currentPath, 'updates') ? ' class="active"' : '' ?>>Updates</a></li>
            <li><a href="/docs/api"<?= $currentPath === '/docs/api' ? ' class="active"' : '' ?>>API</a></li>
            <li><a href="/docs/ai-reference"<?= str_contains($currentPath, 'ai-reference') ? ' class="active"' : '' ?>>AI Reference</a></li>
        </ul>
        
        <div class="nav-heading">Maintainers</div>
        <ul class="nav-section">
            <li><a href="/docs/testing"<?= str_contains($currentPath, 'testing') ? ' class="active"' : '' ?>>Testing</a></li>
            <li><a href="/docs/releasing"<?= str_contains($currentPath, 'releasing') ? ' class="active"' : '' ?>>Releasing</a></li>
        </ul>
    </nav>

</aside>
