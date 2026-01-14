<?php
/**
 * Index/Fallback Template - Docs Theme
 * 
 * This is the default template for the documentation site.
 * 
 * Available variables:
 *   $content  - Content item (for single pages)
 *   $query    - Query object (for archives)
 *   $request  - The HTTP request object
 *   $ava      - Template helper
 *   $site     - Site configuration array
 * 
 * @see https://ava.addy.zone/docs/themes#template-hierarchy
 */

// Homepage doesn't need sidebar
$isHomepage = $request->path() === '/' || $request->path() === '';
$showSidebar = !$isHomepage && str_starts_with($request->path(), '/docs');
$showToc = !$isHomepage;
$bodyClass = $isHomepage ? 'home-landing' : '';
?>
<?= $ava->partial('header', [
    'request' => $request,
    'pageTitle' => 'Ava CMS | Flat-file PHP/Markdown CMS Documentation',
    'showSidebar' => $showSidebar,
    'bodyClass' => $bodyClass,
]) ?>

            <div class="docs-content-wrapper">
                <div class="docs-content">
                    <article class="markdown-section">
                        <?php if (isset($content)): ?>
                            <?= $ava->body($content) ?>
                        <?php else: ?>
                            <div class="hero hero-cover home-hero">
                                <div class="hero-meta">Stable Release · Ready to ship</div>
                                <h1>Build intentional web experiences with Ava</h1>
                                <p class="hero-tagline">A friendly, flexible, flat-file PHP CMS for bespoke personal sites, blogs, and docs with zero database and zero build step.</p>
                                <div class="hero-buttons">
                                    <a href="/docs" class="btn-primary">Get Started →</a>
                                    <a href="https://github.com/avacms/ava" class="btn-secondary" target="_blank" rel="noopener">View on GitHub</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </article>
                </div>

                <?php if ($showToc): ?>
                <aside class="toc-sidebar" id="toc-sidebar">
                    <div class="toc-title">On This Page</div>
                    <ul class="toc-list" id="toc-list">
                        <!-- Populated by JavaScript -->
                    </ul>
                </aside>
                <?php endif; ?>
            </div>

<?= $ava->partial('footer', ['showToc' => $showToc]) ?>
