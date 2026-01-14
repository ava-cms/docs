<?php
/**
 * Page Template - Docs Theme
 * 
 * This template renders documentation pages with sidebar navigation.
 * 
 * Available variables:
 *   $content  - The page content item (Ava\Content\Item)
 *   $request  - The HTTP request object
 *   $route    - The matched route
 *   $ava      - Template helper
 *   $site     - Site configuration array
 * 
 * @see https://ava.addy.zone/docs/themes#templates
 */

// Only show sidebar on /docs pages
$showSidebar = str_starts_with($request->path(), '/docs');
$isHomepage = $request->path() === '/' || $request->path() === '';
$showToc = !$isHomepage;
$bodyClass = $isHomepage ? 'home-landing' : '';
?>
<?= $ava->partial('header', [
    'request' => $request,
    'item' => $content,
    'pageTitle' => $content->title() . ' | Ava CMS',
    'showSidebar' => $showSidebar,
    'bodyClass' => $bodyClass,
]) ?>

            <div class="docs-content-wrapper">
                <div class="docs-content">
                    <?php if (!$isHomepage): ?>
                    <h1 class="page-title"><?= $ava->e($content->title()) ?></h1>
                    <?php endif; ?>
                    
                    <article class="markdown-section">
                        <?= $ava->body($content) ?>
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
