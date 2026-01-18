<?php
/**
 * Home Template - Hero landing page
 * 
 * Uses shared header/footer partials with homepage-specific body class
 * for consistent navigation while allowing unique hero styling.
 */

// Homepage configuration
$showSidebar = false;
$showToc = false;
$bodyClass = 'home-hero-page';
?>
<?= $ava->partial('header', [
    'request' => $request,
    'item' => $content,
    'showSidebar' => $showSidebar,
    'bodyClass' => $bodyClass,
]) ?>

            <?= $ava->body($content) ?>

<?= $ava->partial('footer', ['showToc' => $showToc, 'isHomepage' => true]) ?>
