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
                        <a href="https://github.com/avacms/ava" target="_blank" rel="noopener">GitHub</a>
                        <a href="https://github.com/avacms/ava/blob/main/LICENSE" target="_blank" rel="noopener">License</a>
                        <a href="https://ko-fi.com/addycodes" target="_blank" rel="noopener">Ko-fi</a>
                        <a href="https://discord.gg/fZwW4jBVh5" target="_blank" rel="noopener">Discord</a>
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <!-- GitHub Star Toast -->
    <div id="github-toast" class="github-toast" role="dialog" aria-label="Star on GitHub">
        <button class="github-toast-close" aria-label="Close" onclick="closeGithubToast()">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
        <div class="github-toast-icon">
            <svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
        </div>
        <div class="github-toast-title">Interested in Ava?</div>
        <div class="github-toast-text">A GitHub star helps others discover Ava, keeps updates in your feed, and makes my day. 😄</div>
        <div class="github-toast-actions">
            <a href="https://github.com/avacms/ava" target="_blank" rel="noopener" class="github-star-btn" onclick="starredGithub()">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/></svg>
                Star on GitHub
            </a>
            <button class="github-toast-dismiss" onclick="dismissGithubToast()">Maybe later</button>
        </div>
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

    <script src="https://cdn.jsdelivr.net/npm/prismjs@1/components/prism-core.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1/plugins/autoloader/prism-autoloader.min.js" defer></script>
    <script src="<?= $ava->asset('instantpage.js') ?>" defer></script>
    <script src="<?= $ava->asset('docs.js') ?>" defer></script>
</body>
</html>
