---
title: Taxonomies
slug: taxonomies
status: published
meta_title: Taxonomies | Organize Content | Ava CMS
meta_description: Learn how to classify and organize content using taxonomies in Ava CMS. Categories, tags, authors, and custom groupings with flat-file YAML storage.
excerpt: Taxonomies in Ava CMS organize content into meaningful groups—categories, tags, authors, or any custom classification. Define them in config, store terms as YAML, and use them in templates.
---

Taxonomies let you classify and organize content. Common examples include categories, tags, and authors, but you can create any grouping system your site needs. Unlike traditional CMS platforms that require database tables, Ava stores taxonomy terms in simple YAML files.

## Quick Start

1. **Define a taxonomy** in `app/config/taxonomies.php`
2. **Assign terms** to content in frontmatter
3. **Display terms** in your templates using helper methods

```yaml
---
title: Getting Started with PHP
category: tutorials
tag:
  - php
  - beginner
---

Your content here...
```

That's it. When you reference a term, Ava creates it automatically (unless you've disabled `allow_unknown_terms`).

## Concepts

### Taxonomies vs Terms

- A **taxonomy** is a classification system (e.g., "category", "tag", "author")
- A **term** is a specific value within that taxonomy (e.g., "tutorials", "php", "jane-doe")

### How Terms Are Created

Terms can be created in three ways:

1. **Auto-creation** — Reference a term in content frontmatter and it's created automatically
2. **Registry files** — Pre-define terms with metadata in YAML files
3. **Admin UI** — Create and edit terms through the dashboard

## Configuration

Taxonomies are defined in `app/config/taxonomies.php`:

```php
return [
    'category' => [
        'label'        => 'Categories',
        'hierarchical' => true,
        'public'       => true,
        'rewrite'      => ['base' => '/category'],
    ],
    'tag' => [
        'label'        => 'Tags',
        'hierarchical' => false,
        'public'       => true,
        'rewrite'      => ['base' => '/tag'],
    ],
];
```

For complete configuration options (`behaviour`, `ui`, `rewrite` settings), see [Configuration: Taxonomies](/docs/configuration#content-taxonomies-taxonomiesphp).

<div class="callout-info">
After defining a taxonomy, add it to your content types in <code>content_types.php</code> under the <code>taxonomies</code> array. See <a href="/docs/configuration#content-content-types-content_typesphp">Configuration: Content Types</a>.
</div>

## Term Storage

### Registry Files

Terms can be pre-defined with metadata in YAML files at `content/_taxonomies/{taxonomy}.yml`:

```yaml
# content/_taxonomies/category.yml
- slug: tutorials
  name: Tutorials
  description: Step-by-step guides and how-tos
  icon: book
  color: '#3b82f6'

- slug: news
  name: News
  description: Latest updates and announcements
  icon: newspaper
```

### Standard Fields

| Field | Required | Description |
|-------|----------|-------------|
| `slug` | Yes | URL-safe identifier (lowercase, hyphens) |
| `name` | Yes | Display name for the term |
| `description` | No | Description shown on archive pages |

### Custom Fields

Add any additional fields you need—`icon`, `color`, `image`, `featured`, etc. These are available in templates:

```php
$terms = $ava->terms('category');
$tutorials = $terms['tutorials'];

echo $tutorials['icon'];   // book
echo $tutorials['color'];  // #3b82f6
```

The admin term editor supports adding custom fields as key-value pairs, and suggests field names used by other terms in the same taxonomy.

### How Registry Merging Works

- Terms used in published content get their `count` and `items` from indexing, plus any extra fields from the registry
- Terms that exist only in the registry appear with `count: 0`
- Auto-generated term names (from slugs like `php-tutorials`) can be overridden by registry entries

## Assigning Terms to Content

In content frontmatter, assign terms using the taxonomy name as the key:

### Single Term

```yaml
---
title: My Post
category: tutorials
---
```

### Multiple Terms

```yaml
---
title: My Post
category:
  - tutorials
  - php
tag:
  - beginner
  - installation
---
```

### Alternative: Grouped Format

If you prefer to group all taxonomies under a single key:

```yaml
---
title: My Post
tax:
  category: tutorials
  tag: [beginner, installation]
---
```

Both formats work identically—use whichever keeps your frontmatter tidy.

### Indexing Behaviour

- Only taxonomies defined in `taxonomies.php` are indexed and routed
- Only **published** content is included in taxonomy indexes (not drafts or unlisted)
- Terms do not need to be pre-created — referencing a term creates it automatically
- Term slugs should be lowercase alphanumeric with hyphens

## Hierarchical Taxonomies

When `hierarchical: true`, terms can have parent/child relationships:

```yaml
# content/_taxonomies/category.yml
- slug: programming
  name: Programming

- slug: php
  name: PHP
  parent: programming

- slug: javascript
  name: JavaScript
  parent: programming
```

### Hierarchy Rollup

With `hierarchy_rollup: true` (the default), the parent term archive includes content from all child terms. For example, visiting `/category/programming` would show content tagged with "php" and "javascript" as well.

Set `hierarchy_rollup: false` if you want parent archives to only show directly-tagged content.

### Displaying Hierarchical Terms

```php
<?php $terms = $ava->terms('category'); ?>
<ul>
    <?php foreach ($terms as $slug => $term): ?>
        <li class="<?= isset($term['parent']) ? 'child' : 'parent' ?>">
            <?= str_repeat('— ', substr_count($term['parent'] ?? '', '/')) ?>
            <a href="<?= $ava->termUrl('category', $slug) ?>">
                <?= $ava->e($term['name']) ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
```

## Routing

When `public: true`, Ava automatically creates routes for each taxonomy:

| URL | Description | Template |
|-----|-------------|----------|
| `/category` | Taxonomy index (all terms) | `taxonomy-index.php` |
| `/category/tutorials` | Term archive (content with this term) | `taxonomy.php` |

The URL prefix comes from `rewrite.base` in your taxonomy config.

### Template Resolution

**For term archive pages:**

1. `taxonomy-{taxonomy}-{term}.php` (e.g., `taxonomy-category-tutorials.php`)
2. `taxonomy-{taxonomy}.php` (e.g., `taxonomy-category.php`)
3. `taxonomy.php`
4. `archive.php`
5. `index.php`

**For taxonomy index pages:**

1. `taxonomy-index-{taxonomy}.php` (e.g., `taxonomy-index-category.php`)
2. `taxonomy-index.php`
3. `archive.php`
4. `index.php`

For detailed template examples, see [Theming: Taxonomy Templates](/docs/theming#content-taxonomy-templates).

## Template Helpers

### Getting Terms for a Taxonomy

```php
// All terms in a taxonomy (site-wide)
$categories = $ava->terms('category');

foreach ($categories as $slug => $term) {
    echo $term['name'];         // Display name
    echo $term['description'];  // Description (if set)
    echo $term['icon'];         // Custom field (if set)
    echo $term['count'];        // Number of content items
}
```

### Getting Terms for Content

```php
// Terms assigned to a specific content item
<?php foreach ($content->terms('category') as $term): ?>
    <a href="<?= $ava->termUrl('category', $term) ?>">
        <?= $ava->termName('category', $term) ?>
    </a>
<?php endforeach; ?>
```

### Generating Term URLs

```php
$ava->termUrl('category', 'tutorials')  // /category/tutorials
$ava->termUrl('tag', 'php')             // /tag/php
```

### Getting Term Names

```php
$ava->termName('category', 'tutorials')  // "Tutorials"
```

Returns the display name from the registry, or auto-generates one from the slug.

### Querying Content by Taxonomy

```php
$tutorials = $ava->query()
    ->type('post')
    ->published()
    ->whereTax('category', 'tutorials')
    ->orderBy('date', 'desc')
    ->get();
```

See [Theming: Querying Content](/docs/theming#content-querying-content) for all query methods.

## The `$tax` Variable

The `$tax` array is available in taxonomy templates with context about the current page:

### In Term Archive Templates (`taxonomy.php`)

```php
$tax = [
    'name'   => 'category',           // Taxonomy slug
    'term'   => [                     // Current term data
        'slug'        => 'tutorials',
        'name'        => 'Tutorials',
        'description' => 'Step-by-step guides',
        'icon'        => 'book',      // Custom fields from registry
        'color'       => '#3b82f6',
        'count'       => 12,          // Number of items
    ],
];
```

### In Taxonomy Index Templates (`taxonomy-index.php`)

```php
$tax = [
    'name'  => 'category',            // Taxonomy slug
    'terms' => [                      // All terms (keyed by slug)
        'tutorials' => ['name' => 'Tutorials', 'count' => 12, ...],
        'news'      => ['name' => 'News', 'count' => 5, ...],
    ],
];
```

## Example Templates

### Term Archive (`taxonomy.php`)

```php
<?= $ava->partial('header', ['title' => $tax['term']['name']]) ?>

<header class="archive-header">
    <?php if (!empty($tax['term']['icon'])): ?>
        <span class="term-icon"><?= $ava->e($tax['term']['icon']) ?></span>
    <?php endif; ?>
    
    <h1><?= $ava->e($tax['term']['name']) ?></h1>
    
    <?php if (!empty($tax['term']['description'])): ?>
        <p class="term-description"><?= $ava->e($tax['term']['description']) ?></p>
    <?php endif; ?>
    
    <p class="term-count"><?= $tax['term']['count'] ?> items</p>
</header>

<div class="posts-grid">
    <?php foreach ($query->get() as $item): ?>
        <article>
            <h2>
                <a href="<?= $ava->url($item->type(), $item->slug()) ?>">
                    <?= $ava->e($item->title()) ?>
                </a>
            </h2>
            <?php if ($item->excerpt()): ?>
                <p><?= $ava->e($item->excerpt()) ?></p>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</div>

<?= $ava->pagination($query, $request->path()) ?>

<?= $ava->partial('footer') ?>
```

### Taxonomy Index (`taxonomy-index.php`)

```php
<?= $ava->partial('header', ['title' => ucfirst($tax['name'])]) ?>

<h1>All <?= ucfirst($tax['name']) ?>s</h1>

<ul class="term-list">
    <?php foreach ($tax['terms'] as $slug => $term): ?>
        <li>
            <a href="<?= $ava->termUrl($tax['name'], $slug) ?>">
                <?= $ava->e($term['name']) ?>
            </a>
            <span class="count">(<?= $term['count'] ?>)</span>
            <?php if (!empty($term['description'])): ?>
                <p><?= $ava->e($term['description']) ?></p>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>

<?= $ava->partial('footer') ?>
```

## Admin Management

Terms can be managed via the admin dashboard:

1. Navigate to the taxonomy in the sidebar
2. View all terms with their content counts
3. Create new terms with name, slug, description, and custom fields
4. Edit existing terms
5. Delete unused terms (terms with content show a warning first)

The admin term editor supports:
- Name, slug, and description fields
- Custom fields as key-value pairs
- Field name suggestions based on existing terms

<div class="callout-info">
Deleting a term removes it from the registry file but doesn't modify your content files. Content that references the deleted term will auto-create it again on next index.
</div>

See [Admin Dashboard: Taxonomy Management](/docs/admin#content-taxonomy-management) for more details.

## The Taxonomy Field Type

When defining fields for content types, use the `taxonomy` field type to create a term selector in the admin editor:

```php
'category' => [
    'type'     => 'taxonomy',
    'taxonomy' => 'category',
    'label'    => 'Categories',
    'required' => true,
    'multiple' => true,
],
```

| Option | Default | Description |
|--------|---------|-------------|
| `taxonomy` | Required | Taxonomy name from `taxonomies.php` |
| `multiple` | `true` | Allow multiple term selection |
| `allowNew` | `true` | Allow creating new terms inline |

See [Fields: taxonomy](/docs/fields#content-taxonomy) for full documentation.

<div class="related-docs">
<h2>Related Documentation</h2>
<ul>
<li><a href="/docs/configuration#content-taxonomies-taxonomiesphp">Configuration: Taxonomies</a> — Full config reference</li>
<li><a href="/docs/fields#content-taxonomy">Fields: taxonomy</a> — The taxonomy field type</li>
<li><a href="/docs/theming#content-taxonomy-templates">Theming: Taxonomy Templates</a> — Template examples</li>
<li><a href="/docs/routing#content-taxonomy-routes">Routing: Taxonomy Routes</a> — URL generation</li>
<li><a href="/docs/admin#content-taxonomy-management">Admin: Taxonomy Management</a> — Dashboard features</li>
</ul>
</div>
