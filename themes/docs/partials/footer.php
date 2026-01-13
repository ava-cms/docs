<?php
/**
 * Footer Partial - Docs Theme
 * 
 * This partial closes the main content area and includes scripts.
 * 
 * @see https://ava.addy.zone/docs/themes#partials
 */

$showToc = $showToc ?? false;
$isHomepage = $isHomepage ?? false;
?>
            <footer class="docs-footer<?= $isHomepage ? ' home-footer' : '' ?>">
                <div class="footer-content">
                    <span>Made with 💖 & ☕ by <a href="https://addy.zone/" target="_blank" rel="noopener">Addy</a>. Powered by <a href="https://ava.addy.zone/" target="_blank" rel="noopener">Ava</a> (so meta).</span>
                    <div class="footer-links">
                        <a href="https://github.com/adamgreenough/ava" target="_blank" rel="noopener">GitHub</a>
                        <a href="https://github.com/adamgreenough/ava/blob/main/LICENSE" target="_blank" rel="noopener">License</a>
                        <a href="https://ko-fi.com/addycodes" target="_blank" rel="noopener">Ko-fi</a>
                        <a href="https://discord.gg/fZwW4jBVh5" target="_blank" rel="noopener">Discord</a>
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <!-- Search Overlay -->
    <div id="search-overlay" class="search-overlay" aria-hidden="true">
        <div class="search-overlay-content">
            <div class="search-overlay-header">
                <svg class="search-icon" viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <input 
                    type="text" 
                    id="search-overlay-input" 
                    placeholder="Search documentation..."
                    autocomplete="off"
                    spellcheck="false"
                >
                <kbd class="search-shortcut">ESC</kbd>
            </div>
            <div id="search-overlay-results" class="search-overlay-results">
                <div class="search-hint">Start typing to search...</div>
            </div>
            <div class="search-overlay-footer">
                <div class="search-hint">
                    <kbd>↑</kbd> <kbd>↓</kbd> to navigate
                    <kbd>↵</kbd> to select
                    <kbd>ESC</kbd> to close
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/prismjs@1/components/prism-core.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1/plugins/autoloader/prism-autoloader.min.js"></script>
    <script src="<?= $ava->asset('instantpage.js') ?>"></script>

    <script src="<?= $ava->asset('docs.js') ?>"></script>
</body>
</html>
