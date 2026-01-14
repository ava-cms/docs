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

            <div class="home-hero-content">
                <?= $ava->partial('hero-animation') ?>
                <!-- Decorative Blueprint Corners -->
                <div class="hero-corner top-left">+</div> 
                <div class="hero-corner top-right">+</div>
                <div class="hero-corner bottom-left">+</div>
                <div class="hero-corner bottom-right">+</div>

                <div class="home-container">
                    <?= $ava->body($content) ?>
                </div>
            </div>

<?= $ava->partial('footer', ['showToc' => $showToc, 'isHomepage' => true]) ?>
