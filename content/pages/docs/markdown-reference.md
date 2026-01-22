---
title: Markdown Reference
slug: markdown-reference
status: published
meta_title: Markdown Reference | Flat-file PHP CMS | Ava CMS
meta_description: The complete Markdown reference for Ava CMS covering all CommonMark and GitHub Flavored Markdown syntax with examples.
excerpt: The complete Markdown reference for Ava CMS. Every feature, every syntax, with clear examples.
raw_html: true
---

<style>
.md-section { margin: 3rem 0; padding-bottom: 3rem; border-bottom: 1px solid var(--border); }
.md-section:last-child { border-bottom: none; }
.md-section h2 { margin-top: 0; }
.md-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin: 1.5rem 0; }
@media (max-width: 768px) { .md-grid { grid-template-columns: 1fr; } }
.md-box { padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border); }
.md-box-code { background: var(--bg-code); font-family: var(--font-mono); font-size: 0.85rem; white-space: pre-wrap; }
.md-box-render { background: var(--bg-card); }
.md-box-render > *:first-child { margin-top: 0; }
.md-box-render > *:last-child { margin-bottom: 0; }
.md-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-tertiary); margin-bottom: 0.5rem; letter-spacing: 0.05em; }
.md-mini-table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 0.9rem; }
.md-mini-table th, .md-mini-table td { padding: 0.5rem 0.75rem; border: 1px solid var(--border); text-align: left; }
.md-mini-table th { background: var(--bg-subtle); font-weight: 600; }
.md-mini-table code { background: var(--bg-code); padding: 0.1rem 0.3rem; border-radius: 3px; font-size: 0.85em; }
.md-tag { display: inline-block; font-size: 0.7rem; padding: 0.15rem 0.4rem; border-radius: 3px; font-weight: 600; margin-left: 0.5rem; margin-right: 0.5rem; vertical-align: middle; }
.md-tag-gfm { background: #dbeafe; color: #1e40af; }
.md-tag-ava { background: #f3e8ff; color: #6b21a8; }
[data-theme="dark"] .md-tag-gfm { background: #1e3a5f; color: #93c5fd; }
[data-theme="dark"] .md-tag-ava { background: #3b1d5c; color: #d8b4fe; }
</style>

<p class="lead">This is the complete Markdown reference for Ava CMS. Ava uses <strong>CommonMark</strong> as the base parser with <strong>GitHub Flavored Markdown (GFM)</strong> extensions enabled by default.</p>

<div class="callout-info">
<strong>What's what?</strong> Features marked <span class="md-tag md-tag-gfm">GFM</span> come from the GitHub Flavored Markdown extension. Features marked <span class="md-tag md-tag-ava">Ava</span> are specific to Ava CMS.
</div>

<nav style="background: var(--bg-subtle); padding: 1.25rem; border-radius: var(--radius-md); margin: 2rem 0;">
<strong style="display: block; margin-bottom: 0.75rem;">Contents</strong>
<div style="column-count: 2; column-gap: 2rem; font-size: 0.9rem;">
<a href="#paragraphs">Paragraphs</a><br>
<a href="#line-breaks">Line Breaks</a><br>
<a href="#headings">Headings</a><br>
<a href="#emphasis">Emphasis (Bold & Italic)</a><br>
<a href="#strikethrough">Strikethrough</a><br>
<a href="#lists">Lists</a><br>
<a href="#task-lists">Task Lists</a><br>
<a href="#links">Links</a><br>
<a href="#images">Images</a><br>
<a href="#code">Code</a><br>
<a href="#blockquotes">Blockquotes</a><br>
<a href="#horizontal-rules">Horizontal Rules</a><br>
<a href="#tables">Tables</a><br>
<a href="#escaping">Escaping Characters</a><br>
<a href="#html">Raw HTML</a><br>
<a href="#ava-features">Ava-Specific Features</a><br>
</div>
</nav>

<!-- ============================================================ -->
<div class="md-section" id="paragraphs">
<h2>Paragraphs</h2>
<p>Paragraphs are separated by one or more blank lines. A single line break within text does <strong>not</strong> create a new paragraph—it's treated as a space.</p>

<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">This is the first paragraph.

This is the second paragraph.</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<p>This is the first paragraph.</p>
<p>This is the second paragraph.</p>
</div>
</div>
</div>
</div>

<!-- ============================================================ -->
<div class="md-section" id="line-breaks">
<h2>Line Breaks</h2>
<p>To create a line break (<code>&lt;br&gt;</code>) without starting a new paragraph, end a line with <strong>two or more spaces</strong>, or use a backslash (<code>\</code>).</p>

<div class="md-grid">
<div>
<div class="md-label">Markdown (two trailing spaces)</div>
<div class="md-box md-box-code">Roses are red··
Violets are blue</div>
<p style="font-size: 0.8rem; color: var(--text-tertiary); margin-top: 0.5rem;">The <code>··</code> represents two spaces.</p>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
Roses are red<br>
Violets are blue
</div>
</div>
</div>

<div class="md-grid">
<div>
<div class="md-label">Markdown (backslash)</div>
<div class="md-box md-box-code">Roses are red\
Violets are blue</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
Roses are red<br>
Violets are blue
</div>
</div>
</div>
</div>

<!-- ============================================================ -->
<div class="md-section" id="headings">
<h2>Headings</h2>
<p>Create headings using <code>#</code> symbols (ATX style) or underlines (Setext style). There are six levels of headings.</p>

<h3>ATX Style (Recommended)</h3>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code"># Heading 1
## Heading 2
### Heading 3
#### Heading 4
##### Heading 5
###### Heading 6</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<h1 style="font-size:1.5rem; margin:0 0 0.25rem">Heading 1</h1>
<h2 style="font-size:1.3rem; margin:0 0 0.25rem; border:none; padding:0">Heading 2</h2>
<h3 style="font-size:1.15rem; margin:0 0 0.25rem">Heading 3</h3>
<h4 style="font-size:1rem; margin:0 0 0.25rem">Heading 4</h4>
<h5 style="font-size:0.9rem; margin:0 0 0.25rem">Heading 5</h5>
<h6 style="font-size:0.85rem; margin:0">Heading 6</h6>
</div>
</div>
</div>

<h3>Setext Style (H1 and H2 only)</h3>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">Heading 1
=========

Heading 2
---------</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<h1 style="font-size:1.5rem; margin:0 0 0.5rem">Heading 1</h1>
<h2 style="font-size:1.3rem; margin:0; border:none; padding:0">Heading 2</h2>
</div>
</div>
</div>
</div>

<!-- ============================================================ -->
<div class="md-section" id="emphasis">
<h2>Emphasis (Bold &amp; Italic)</h2>
<p>Use asterisks (<code>*</code>) or underscores (<code>_</code>) for emphasis. One for italic, two for bold, three for both.</p>

<table class="md-mini-table">
<thead>
<tr><th>Style</th><th>Markdown</th><th>Result</th></tr>
</thead>
<tbody>
<tr><td>Italic</td><td><code>*italic*</code> or <code>_italic_</code></td><td><em>italic</em></td></tr>
<tr><td>Bold</td><td><code>**bold**</code> or <code>__bold__</code></td><td><strong>bold</strong></td></tr>
<tr><td>Bold + Italic</td><td><code>***both***</code> or <code>___both___</code></td><td><strong><em>both</em></strong></td></tr>
<tr><td>Mixed</td><td><code>**bold and _nested italic_**</code></td><td><strong>bold and <em>nested italic</em></strong></td></tr>
</tbody>
</table>
</div>

<!-- ============================================================ -->
<div class="md-section" id="strikethrough">
<h2>Strikethrough <span class="md-tag md-tag-gfm">GFM</span></h2>
<p>Wrap text in double tildes to strike it through.</p>

<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">~~This text is crossed out~~</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render"><del>This text is crossed out</del></div>
</div>
</div>
</div>

<!-- ============================================================ -->
<div class="md-section" id="lists">
<h2>Lists</h2>

<h3>Unordered Lists</h3>
<p>Use <code>-</code>, <code>*</code>, or <code>+</code> followed by a space.</p>

<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">- First item
- Second item
- Third item</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<ul>
<li>First item</li>
<li>Second item</li>
<li>Third item</li>
</ul>
</div>
</div>
</div>

<h3>Ordered Lists</h3>
<p>Use numbers followed by a period. The actual numbers don't matter—Markdown will number them sequentially.</p>

<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">1. First item
2. Second item
3. Third item</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<ol>
<li>First item</li>
<li>Second item</li>
<li>Third item</li>
</ol>
</div>
</div>
</div>

<h3>Nested Lists</h3>
<p>Indent items with 2–4 spaces to create nested lists.</p>

<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">- Parent item
  - Child item
  - Child item
    - Grandchild
- Another parent</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<ul>
<li>Parent item
  <ul>
  <li>Child item</li>
  <li>Child item
    <ul><li>Grandchild</li></ul>
  </li>
  </ul>
</li>
<li>Another parent</li>
</ul>
</div>
</div>
</div>

<h3>Mixed Lists</h3>
<p>You can mix ordered and unordered lists by nesting.</p>

<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">1. First step
   - Sub-point A
   - Sub-point B
2. Second step</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<ol>
<li>First step
  <ul>
  <li>Sub-point A</li>
  <li>Sub-point B</li>
  </ul>
</li>
<li>Second step</li>
</ol>
</div>
</div>
</div>
</div>

<!-- ============================================================ -->
<div class="md-section" id="task-lists">
<h2>Task Lists <span class="md-tag md-tag-gfm">GFM</span></h2>
<p>Create checkboxes with <code>[ ]</code> (unchecked) or <code>[x]</code> (checked).</p>

<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">- [x] Write documentation
- [x] Test examples
- [ ] Publish</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<ul style="list-style:none; padding-left:0;">
<li><input type="checkbox" checked disabled> Write documentation</li>
<li><input type="checkbox" checked disabled> Test examples</li>
<li><input type="checkbox" disabled> Publish</li>
</ul>
</div>
</div>
</div>
</div>

<!-- ============================================================ -->
<div class="md-section" id="links">
<h2>Links</h2>

<h3>Inline Links</h3>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">[Link text](https://example.com)</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render"><a href="https://example.com">Link text</a></div>
</div>
</div>

<h3>Links with Titles</h3>
<p>Add a title in quotes—it appears on hover.</p>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">[Ava CMS](https://ava.addy.zone "Visit Ava CMS")</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render"><a href="https://ava.addy.zone" title="Visit Ava CMS">Ava CMS</a></div>
</div>
</div>

<h3>Reference Links</h3>
<p>Define links separately for cleaner content. The reference can go anywhere in the document.</p>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">Check out [Ava CMS][ava] for more.

[ava]: https://ava.addy.zone "Ava CMS"</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render"><p>Check out <a href="https://ava.addy.zone" title="Ava CMS">Ava CMS</a> for more.</p></div>
</div>
</div>

<h3>Autolinks</h3>
<p>Wrap URLs or emails in angle brackets to make them clickable.</p>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">&lt;https://ava.addy.zone&gt;
&lt;hello@example.com&gt;</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<a href="https://ava.addy.zone">https://ava.addy.zone</a><br>
<a href="mailto:hello@example.com">hello@example.com</a>
</div>
</div>
</div>

<h3>Automatic URL Linking <span class="md-tag md-tag-gfm">GFM</span></h3>
<p>Plain URLs are automatically converted to links.</p>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">Visit https://ava.addy.zone for details.</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">Visit <a href="https://ava.addy.zone">https://ava.addy.zone</a> for details.</div>
</div>
</div>
</div>

<!-- ============================================================ -->
<div class="md-section" id="images">
<h2>Images</h2>
<p>Same syntax as links, but with a leading <code>!</code>. The text in brackets becomes the alt text.</p>

<h3>Basic Image</h3>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">![A cute cat](/media/cat.jpg)</div>
</div>
<div>
<div class="md-label">HTML Output</div>
<div class="md-box md-box-code">&lt;img src="/media/cat.jpg" alt="A cute cat"&gt;</div>
</div>
</div>

<h3>Image with Title</h3>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">![Cat](/media/cat.jpg "My cat Whiskers")</div>
</div>
<div>
<div class="md-label">HTML Output</div>
<div class="md-box md-box-code">&lt;img src="/media/cat.jpg" alt="Cat" title="My cat Whiskers"&gt;</div>
</div>
</div>

<h3>Linked Image</h3>
<p>Wrap an image in link syntax to make it clickable.</p>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">[![Alt text](/media/thumb.jpg)](/media/full.jpg)</div>
</div>
<div>
<div class="md-label">HTML Output</div>
<div class="md-box md-box-code">&lt;a href="/media/full.jpg"&gt;
  &lt;img src="/media/thumb.jpg" alt="Alt text"&gt;
&lt;/a&gt;</div>
</div>
</div>
</div>

<!-- ============================================================ -->
<div class="md-section" id="code">
<h2>Code</h2>

<h3>Inline Code</h3>
<p>Wrap text in single backticks for inline code.</p>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">Use the `echo` command.</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">Use the <code>echo</code> command.</div>
</div>
</div>

<h3>Inline Code with Backticks</h3>
<p>To include a literal backtick, use double backticks.</p>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">``Use `backticks` in code``</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render"><code>Use `backticks` in code</code></div>
</div>
</div>

<h3>Fenced Code Blocks</h3>
<p>Use triple backticks or tildes. Add a language identifier for syntax highlighting.</p>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">```php
&lt;?php
echo "Hello, World!";
```</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<pre style="margin:0; background:var(--bg-code-block); padding:1rem; border-radius:var(--radius-sm);"><code style="color:#e5e5e5;">&lt;?php
echo "Hello, World!";</code></pre>
</div>
</div>
</div>

<h3>Indented Code Blocks</h3>
<p>Indent lines by 4 spaces or 1 tab. No syntax highlighting available.</p>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">    function hello() {
        return "Hi!";
    }</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<pre style="margin:0;"><code>function hello() {
    return "Hi!";
}</code></pre>
</div>
</div>
</div>

<h3>Common Language Identifiers</h3>
<table class="md-mini-table">
<thead><tr><th>Language</th><th>Identifier</th></tr></thead>
<tbody>
<tr><td>PHP</td><td><code>php</code></td></tr>
<tr><td>JavaScript</td><td><code>js</code> or <code>javascript</code></td></tr>
<tr><td>HTML</td><td><code>html</code></td></tr>
<tr><td>CSS</td><td><code>css</code></td></tr>
<tr><td>JSON</td><td><code>json</code></td></tr>
<tr><td>YAML</td><td><code>yaml</code> or <code>yml</code></td></tr>
<tr><td>Bash / Shell</td><td><code>bash</code> or <code>sh</code></td></tr>
<tr><td>Plain text</td><td><code>text</code> or <code>plaintext</code></td></tr>
</tbody>
</table>
</div>

<!-- ============================================================ -->
<div class="md-section" id="blockquotes">
<h2>Blockquotes</h2>
<p>Prefix lines with <code>&gt;</code> to create a blockquote.</p>

<h3>Basic Blockquote</h3>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">&gt; This is a blockquote.
&gt; It can span multiple lines.</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<blockquote style="margin:0; border-left:4px solid var(--accent); padding-left:1rem; color:var(--text-secondary);">
This is a blockquote. It can span multiple lines.
</blockquote>
</div>
</div>
</div>

<h3>Nested Blockquotes</h3>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">&gt; Outer quote
&gt;&gt; Nested quote
&gt;&gt;&gt; Deeply nested</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<blockquote style="margin:0; border-left:4px solid var(--accent); padding-left:1rem;">
Outer quote
<blockquote style="margin:0.5rem 0 0; border-left:4px solid var(--border); padding-left:1rem;">
Nested quote
<blockquote style="margin:0.5rem 0 0; border-left:4px solid var(--border); padding-left:1rem;">
Deeply nested
</blockquote>
</blockquote>
</blockquote>
</div>
</div>
</div>

<h3>Blockquotes with Other Elements</h3>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">&gt; ### Heading in a quote
&gt;
&gt; - List item one
&gt; - List item two
&gt;
&gt; **Bold** works too.</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<blockquote style="margin:0; border-left:4px solid var(--accent); padding-left:1rem;">
<h3 style="margin:0 0 0.5rem; font-size:1rem;">Heading in a quote</h3>
<ul style="margin:0.5rem 0;"><li>List item one</li><li>List item two</li></ul>
<p style="margin:0;"><strong>Bold</strong> works too.</p>
</blockquote>
</div>
</div>
</div>
</div>

<!-- ============================================================ -->
<div class="md-section" id="horizontal-rules">
<h2>Horizontal Rules</h2>
<p>Create a horizontal line with three or more hyphens, asterisks, or underscores on their own line.</p>

<table class="md-mini-table">
<thead><tr><th>Markdown</th><th>Result</th></tr></thead>
<tbody>
<tr><td><code>---</code></td><td><hr style="margin:0.5rem 0;"></td></tr>
<tr><td><code>***</code></td><td><hr style="margin:0.5rem 0;"></td></tr>
<tr><td><code>___</code></td><td><hr style="margin:0.5rem 0;"></td></tr>
</tbody>
</table>

<p>You can also use spaces between: <code>- - -</code> or <code>* * *</code></p>
</div>

<!-- ============================================================ -->
<div class="md-section" id="tables">
<h2>Tables <span class="md-tag md-tag-gfm">GFM</span></h2>
<p>Create tables using pipes (<code>|</code>) and hyphens (<code>-</code>).</p>

<h3>Basic Table</h3>
<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">| Name    | Role      |
|---------|-----------|
| Ada     | Developer |
| Grace   | Manager   |</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<table style="width:100%; border-collapse:collapse;">
<thead><tr style="border-bottom:2px solid var(--border);"><th style="text-align:left; padding:0.5rem;">Name</th><th style="text-align:left; padding:0.5rem;">Role</th></tr></thead>
<tbody>
<tr style="border-bottom:1px solid var(--border);"><td style="padding:0.5rem;">Ada</td><td style="padding:0.5rem;">Developer</td></tr>
<tr><td style="padding:0.5rem;">Grace</td><td style="padding:0.5rem;">Manager</td></tr>
</tbody>
</table>
</div>
</div>
</div>

<h3>Column Alignment</h3>
<p>Use colons in the separator row to control alignment.</p>

<table class="md-mini-table">
<thead><tr><th>Syntax</th><th>Alignment</th></tr></thead>
<tbody>
<tr><td><code>:---</code></td><td>Left (default)</td></tr>
<tr><td><code>:---:</code></td><td>Center</td></tr>
<tr><td><code>---:</code></td><td>Right</td></tr>
</tbody>
</table>

<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">| Item   | Qty | Price  |
|:-------|:---:|-------:|
| Apples |  5  |  $1.20 |
| Bread  |  2  | $3.50  |</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<table style="width:100%; border-collapse:collapse;">
<thead><tr style="border-bottom:2px solid var(--border);"><th style="text-align:left; padding:0.5rem;">Item</th><th style="text-align:center; padding:0.5rem;">Qty</th><th style="text-align:right; padding:0.5rem;">Price</th></tr></thead>
<tbody>
<tr style="border-bottom:1px solid var(--border);"><td style="text-align:left; padding:0.5rem;">Apples</td><td style="text-align:center; padding:0.5rem;">5</td><td style="text-align:right; padding:0.5rem;">$1.20</td></tr>
<tr><td style="text-align:left; padding:0.5rem;">Bread</td><td style="text-align:center; padding:0.5rem;">2</td><td style="text-align:right; padding:0.5rem;">$3.50</td></tr>
</tbody>
</table>
</div>
</div>
</div>
</div>

<!-- ============================================================ -->
<div class="md-section" id="escaping">
<h2>Escaping Characters</h2>
<p>Prefix special characters with a backslash (<code>\</code>) to display them literally.</p>

<h3>Characters You Can Escape</h3>
<table class="md-mini-table">
<thead><tr><th>Character</th><th>Name</th><th>Escaped</th></tr></thead>
<tbody>
<tr><td><code>\</code></td><td>Backslash</td><td><code>\\</code></td></tr>
<tr><td><code>`</code></td><td>Backtick</td><td><code>\`</code></td></tr>
<tr><td><code>*</code></td><td>Asterisk</td><td><code>\*</code></td></tr>
<tr><td><code>_</code></td><td>Underscore</td><td><code>\_</code></td></tr>
<tr><td><code>{}</code></td><td>Curly braces</td><td><code>\{ \}</code></td></tr>
<tr><td><code>[]</code></td><td>Square brackets</td><td><code>\[ \]</code></td></tr>
<tr><td><code>()</code></td><td>Parentheses</td><td><code>\( \)</code></td></tr>
<tr><td><code>#</code></td><td>Hash</td><td><code>\#</code></td></tr>
<tr><td><code>+</code></td><td>Plus</td><td><code>\+</code></td></tr>
<tr><td><code>-</code></td><td>Hyphen</td><td><code>\-</code></td></tr>
<tr><td><code>.</code></td><td>Period</td><td><code>\.</code></td></tr>
<tr><td><code>!</code></td><td>Exclamation</td><td><code>\!</code></td></tr>
<tr><td><code>|</code></td><td>Pipe</td><td><code>\|</code></td></tr>
</tbody>
</table>

<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">\*This is not italic\*

1\. Not a list item</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<p>*This is not italic*</p>
<p>1. Not a list item</p>
</div>
</div>
</div>
</div>

<!-- ============================================================ -->
<div class="md-section" id="html">
<h2>Raw HTML</h2>
<p>By default, Ava allows raw HTML in your Markdown. This is controlled by the <code>content.markdown.allow_html</code> setting.</p>

<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">This has &lt;mark&gt;highlighted&lt;/mark&gt; text.

&lt;details&gt;
&lt;summary&gt;Click to expand&lt;/summary&gt;
Hidden content here.
&lt;/details&gt;</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render">
<p>This has <mark>highlighted</mark> text.</p>
<details>
<summary>Click to expand</summary>
Hidden content here.
</details>
</div>
</div>
</div>

<h3>HTML Entities</h3>
<p>Standard HTML entities work as expected.</p>

<table class="md-mini-table">
<thead><tr><th>Entity</th><th>Result</th><th>Description</th></tr></thead>
<tbody>
<tr><td><code>&amp;amp;</code></td><td>&amp;</td><td>Ampersand</td></tr>
<tr><td><code>&amp;lt;</code></td><td>&lt;</td><td>Less than</td></tr>
<tr><td><code>&amp;gt;</code></td><td>&gt;</td><td>Greater than</td></tr>
<tr><td><code>&amp;copy;</code></td><td>&copy;</td><td>Copyright</td></tr>
<tr><td><code>&amp;mdash;</code></td><td>&mdash;</td><td>Em dash</td></tr>
<tr><td><code>&amp;nbsp;</code></td><td>(non-breaking space)</td><td>Non-breaking space</td></tr>
</tbody>
</table>

<h3>Disallowed Tags</h3>
<p>You can block specific HTML tags for security using <code>content.markdown.disallowed_tags</code> in your config, even when HTML is allowed.</p>
</div>

<!-- ============================================================ -->
<div class="md-section" id="ava-features">
<h2>Ava-Specific Features <span class="md-tag md-tag-ava">Ava</span></h2>

<h3>Automatic Heading IDs</h3>
<p>Ava automatically adds <code>id</code> attributes to headings for anchor links. This is enabled by default via <code>content.markdown.heading_ids</code>.</p>

<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">## Getting Started</div>
</div>
<div>
<div class="md-label">HTML Output</div>
<div class="md-box md-box-code">&lt;h2 id="getting-started"&gt;Getting Started&lt;/h2&gt;</div>
</div>
</div>

<p>This lets you link directly to sections: <code>/docs/page#getting-started</code></p>

<h3>Path Aliases</h3>
<p>Use configured aliases (like <code>@media:</code>) in image and link paths. Ava expands them during rendering.</p>

<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">![Photo](@media:vacation.jpg)</div>
</div>
<div>
<div class="md-label">HTML Output</div>
<div class="md-box md-box-code">&lt;img src="/media/vacation.jpg" alt="Photo"&gt;</div>
</div>
</div>

<p>Configure aliases in <code>app/config/ava.php</code> under <code>paths.aliases</code>.</p>

<h3>Shortcodes</h3>
<p>Ava processes shortcodes in your content after Markdown rendering. See the <a href="/docs/shortcodes">Shortcodes documentation</a> for details.</p>

<div class="md-grid">
<div>
<div class="md-label">Markdown</div>
<div class="md-box md-box-code">[youtube id="dQw4w9WgXcQ"]

[snippet name="newsletter-signup"]</div>
</div>
<div>
<div class="md-label">Result</div>
<div class="md-box md-box-render" style="color:var(--text-tertiary); font-style:italic;">
(Rendered shortcode output)
</div>
</div>
</div>
</div>

<!-- ============================================================ -->
<div class="md-section" id="not-supported">
<h2>Features Not Supported by Default</h2>
<p>These features are <strong>not enabled</strong> in Ava's default Markdown configuration. They can be added via plugins using the <code>markdown.configure</code> hook.</p>

<ul>
<li>Footnotes (<code>[^1]</code>)</li>
<li>Definition lists</li>
<li>Abbreviations</li>
<li>Table row/column spans</li>
<li>Math/LaTeX (<code>$x^2$</code>)</li>
<li>Smart typography (curly quotes, em-dashes)</li>
<li>Wiki-style links (<code>[[Page]]</code>)</li>
<li>Mermaid diagrams</li>
<li>Table of contents generation</li>
</ul>

<p>See <a href="/docs/creating-plugins#markdown-configure">Creating Plugins</a> to learn how to extend the Markdown parser.</p>
</div>
