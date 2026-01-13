// Docs theme frontend interactions (search overlay, sidebar, toc, theme toggle)

// Theme toggle functionality
(function () {
    const themeToggle = document.querySelector('.theme-toggle');
    if (!themeToggle) return;

    themeToggle.addEventListener('click', function () {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    });
})();

// Mobile sidebar toggle
(function () {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar:not(.mobile-nav-only)') || document.querySelector('.sidebar');
    const backdrop = document.querySelector('.sidebar-backdrop');

    if (!sidebarToggle || !sidebar) return;

    function toggleSidebar(e) {
        e.preventDefault();
        e.stopPropagation();
        sidebar.classList.toggle('open');
        backdrop?.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
    }

    sidebarToggle.addEventListener('click', toggleSidebar);
    sidebarToggle.addEventListener('touchend', toggleSidebar);

    // Close sidebar when clicking backdrop
    backdrop?.addEventListener('click', function () {
        sidebar.classList.remove('open');
        backdrop.classList.remove('active');
        document.body.style.overflow = '';
    });

    // Close sidebar when clicking a link (mobile)
    document.querySelectorAll('.sidebar-nav a, .sidebar-mobile-nav a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 768) {
                sidebar.classList.remove('open');
                backdrop?.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    // Close sidebar on escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
            backdrop?.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
})();

// Build Table of Contents from headings (only runs if TOC exists)
(function () {
    const tocList = document.getElementById('toc-list');
    const article = document.querySelector('.markdown-section');

    if (!tocList || !article) return;

    // Only include h2 headings with IDs
    const headings = article.querySelectorAll('h2[id]');

    if (headings.length < 2) {
        const tocSidebar = document.getElementById('toc-sidebar');
        if (tocSidebar) tocSidebar.style.display = 'none';
        return;
    }

    headings.forEach(heading => {
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = '#' + heading.id;
        a.textContent = heading.textContent;
        a.dataset.level = '2';
        li.appendChild(a);
        tocList.appendChild(li);
    });

    const tocLinks = tocList.querySelectorAll('a');

    function updateActiveLink() {
        const scrollPos = window.scrollY + 100;
        let current = null;

        headings.forEach(heading => {
            if (heading.offsetTop <= scrollPos) {
                current = heading;
            }
        });

        tocLinks.forEach(link => {
            link.classList.remove('active');
            if (current && link.getAttribute('href') === '#' + current.id) {
                link.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', updateActiveLink, { passive: true });
    updateActiveLink();
})();

// Global Search Overlay with auto-search
(function () {
    const overlay = document.getElementById('search-overlay');
    const input = document.getElementById('search-overlay-input');
    const results = document.getElementById('search-overlay-results');
    const searchButton = document.getElementById('search-button');

    if (!overlay || !input || !results) return;

    let searchTimeout;
    let selectedIndex = -1;
    let isInitialized = false;

    if (searchButton) {
        searchButton.addEventListener('click', (e) => {
            e.preventDefault();
            openSearch();
        });
    }

    document.addEventListener('keydown', (e) => {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            openSearch();
        }
        if (e.key === 'Escape' && overlay.classList.contains('active')) {
            closeSearch();
        }
    });

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeSearch();
    });

    function focusAndSelect() {
        // In some cases, focusing immediately after toggling visibility can be ignored.
        input.focus({ preventScroll: true });
        input.select();
    }

    function openSearch() {
        if (!isInitialized) {
            isInitialized = true;
            overlay.classList.add('initialized');
        }

        overlay.classList.add('active');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        // Be aggressive in Chrome/Safari: focus now, next frame, and shortly after.
        focusAndSelect();
        requestAnimationFrame(focusAndSelect);
        setTimeout(focusAndSelect, 50);
    }

    function closeSearch() {
        overlay.classList.remove('active');
        overlay.setAttribute('aria-hidden', 'true');
        input.value = '';
        results.innerHTML = '<div class="search-hint">Start typing to search...</div>';
        selectedIndex = -1;
        document.body.style.overflow = '';
    }

    input.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        const query = input.value.trim();

        if (query.length < 3) {
            results.innerHTML = '<div class="search-hint">Type at least 3 characters to search...</div>';
            return;
        }

        results.innerHTML = '<div class="search-hint">Searching...</div>';
        searchTimeout = setTimeout(performSearch, 300);
    });

    function performSearch() {
        const query = input.value.trim();

        if (query.length < 3) {
            results.innerHTML = '<div class="search-hint">Type at least 3 characters to search...</div>';
            return;
        }

        fetch(`/search.json?q=${encodeURIComponent(query)}`)
            .then(r => r.json())
            .then(displayResults)
            .catch(() => {
                results.innerHTML = '<div class="search-hint">Search unavailable</div>';
            });
    }

    function displayResults(data) {
        selectedIndex = -1;
        const query = input.value.trim();

        if (!data.items || data.items.length === 0) {
            results.innerHTML = '<div class="search-hint">No results found</div>';
            return;
        }

        const html = data.items.map((item, i) => {
            const urlWithFragment = withTextFragment(item.url, query);
            return `
                <a href="${escapeHtml(urlWithFragment)}" class="search-result" data-index="${i}">
                    <div class="search-result-title">${escapeHtml(item.title)}</div>
                    <div class="search-result-meta">${escapeHtml(item.type)}</div>
                    ${item.excerpt ? `<div class="search-result-excerpt">${escapeHtml(item.excerpt)}</div>` : ''}
                </a>
            `;
        }).join('');

        results.innerHTML = html;
    }

    function withTextFragment(url, text) {
        const normalizedUrl = String(url || '');
        const normalizedText = String(text || '').trim();

        if (!normalizedUrl || !normalizedText) return normalizedUrl;
        if (normalizedUrl.includes(':~:text=')) return normalizedUrl;

        const encodedText = encodeURIComponent(normalizedText);
        if (normalizedUrl.includes('#')) {
            return `${normalizedUrl}:~:text=${encodedText}`;
        }
        return `${normalizedUrl}#:~:text=${encodedText}`;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    input.addEventListener('keydown', (e) => {
        const items = results.querySelectorAll('.search-result');

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateSelection(items);
        } else if (e.key === 'Enter' && selectedIndex >= 0 && items[selectedIndex]) {
            e.preventDefault();
            items[selectedIndex].click();
        }
    });

    function updateSelection(items) {
        items.forEach((item, i) => {
            item.classList.toggle('selected', i === selectedIndex);
        });
        if (selectedIndex >= 0 && items[selectedIndex]) {
            items[selectedIndex].scrollIntoView({ block: 'nearest' });
        }
    }

    // Text-fragment fallback for browsers without support.
    (function applyTextFragmentFallback() {
        const hash = String(window.location.hash || '');
        if (!hash.includes(':~:text=')) return;
        if ('fragmentDirective' in document) return;

        const textPart = hash.split(':~:text=')[1] || '';
        const encoded = textPart.split('&')[0];
        const term = decodeURIComponent(encoded || '').trim();
        if (!term) return;

        const root = document.querySelector('.markdown-section') || document.body;
        const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
            acceptNode(node) {
                if (!node.nodeValue || !node.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
                const parent = node.parentElement;
                if (!parent) return NodeFilter.FILTER_REJECT;
                const tag = parent.tagName;
                if (tag === 'SCRIPT' || tag === 'STYLE' || tag === 'NOSCRIPT') return NodeFilter.FILTER_REJECT;
                return NodeFilter.FILTER_ACCEPT;
            }
        });

        const termLower = term.toLowerCase();
        let textNode;
        while ((textNode = walker.nextNode())) {
            const haystack = textNode.nodeValue;
            const idx = haystack.toLowerCase().indexOf(termLower);
            if (idx === -1) continue;

            const range = document.createRange();
            range.setStart(textNode, idx);
            range.setEnd(textNode, idx + term.length);
            const mark = document.createElement('mark');
            try {
                range.surroundContents(mark);
                mark.scrollIntoView({ block: 'center' });
            } catch {
                (textNode.parentElement || root).scrollIntoView({ block: 'center' });
            }
            break;
        }
    })();
})();
