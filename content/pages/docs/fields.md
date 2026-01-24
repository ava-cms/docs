---
title: Fields
status: published
meta_title: Fields | Flat-file PHP CMS | Ava CMS
meta_description: Define typed fields in content_types.php for admin UI rendering, linter validation, and real-time feedback. Learn all built-in field types and options.
excerpt: Ava CMS includes a powerful field validation system. Define typed fields in your content type configuration to get admin UI rendering, CLI linting, and real-time validation.
---

Ava CMS includes a field system that validates content and renders appropriate admin UI inputs. You define fields in your [content type configuration](/docs/configuration#content-types-content_typesphp), and Ava CMS handles the rest.

**What fields give you:**

- **Admin UI inputs** — Each field type renders an appropriate input (text, date picker, image selector, etc.)
- **CLI validation** — Run `./ava lint` to check all content against field constraints
- **Real-time feedback** — JavaScript validation in the admin editor catches errors as you type
- **Type conversion** — Values are automatically converted for storage and retrieval

## Quick Start

Define fields in `app/config/content_types.php`:

```php
return [
    'post' => [
        'label' => 'Posts',
        'content_dir' => 'posts',
        'fields' => [
            'author' => [
                'type' => 'text',
                'label' => 'Author Name',
                'required' => true,
            ],
            'featured' => [
                'type' => 'checkbox',
                'label' => 'Featured Post',
            ],
            'publish_date' => [
                'type' => 'date',
                'label' => 'Publish Date',
                'includeTime' => true,
            ],
        ],
    ],
];
```

For complete content type configuration, see [Configuration: Content Types](/docs/configuration#content-content-types-content_typesphp).

## How Fields Are Stored

Field values are saved in your content file's [YAML frontmatter](/docs/content#the-basics). Most fields store simple values:

```yaml
---
title: My Blog Post
author: Jane Smith
featured: true
publish_date: 2025-01-15
accent_color: "#3b82f6"
---
```

Some field types store more complex structures:

```yaml
---
title: Recipe: Sourdough Bread

# Simple array (list)
ingredients:
  - "500g bread flour"
  - "350g water"
  - "100g starter"
  - "10g salt"

# Associative array (key-value pairs)
nutrition:
  calories: 250
  protein: "8g"
  carbs: "50g"

# Gallery (multiple images)
photos:
  - "@media‎:recipes/bread-1.jpg"
  - "@media‎:recipes/bread-2.jpg"

# Multiple taxonomy terms
category:
  - baking
  - bread

# Multiple content references
related_posts:
  - pizza-dough-recipe
  - starter-guide
---
```

The <code>@media<span></span>:</code> prefix is a [path alias](/docs/configuration#path-aliases) that references files in your `/media` folder.

## Common Options

All field types support these options:

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `type` | string | required | Field type identifier (see below) |
| `label` | string | from field name | Human-readable label shown in the admin |
| `description` | string | — | Help text shown below the field |
| `required` | bool | `false` | Whether a value is required |
| `default` | mixed | varies by type | Default value for new content |
| `placeholder` | string | — | Placeholder text (for applicable fields) |
| `group` | string | — | Group name for organising fields (see [Field Groups](#content-field-groups)) |

## Field Types

| Type | Description |
|------|-------------|
| [text](#content-text) | Single-line text input |
| [textarea](#content-textarea) | Multi-line text input |
| [number](#content-number) | Integer or decimal input |
| [checkbox](#content-checkbox) | Boolean toggle |
| [select](#content-select) | Dropdown selection |
| [date](#content-date) | Date or datetime picker |
| [color](#content-color) | Colour picker |
| [image](#content-image) | Image picker with preview |
| [file](#content-file) | General file picker |
| [gallery](#content-gallery) | Multiple image picker |
| [array](#content-array) | Dynamic list or key-value pairs |
| [content](#content-content) | Reference to other content |
| [taxonomy](#content-taxonomy) | Taxonomy term selector |
| [status](#content-status) | Content status toggle |
| [template](#content-template) | Theme template selector |

### text

Single-line text input.

```php
'title' => [
    'type' => 'text',
    'label' => 'Title',
    'required' => true,
    'minLength' => 5,
    'maxLength' => 200,
    'pattern' => '^[A-Z].*',
    'placeholder' => 'Enter title...',
],
```

| Option | Type | Description |
|--------|------|-------------|
| `minLength` | int | Minimum character count |
| `maxLength` | int | Maximum character count |
| `pattern` | string | Regex pattern for validation (without delimiters) |
| `patternMessage` | string | Custom error message when pattern fails |

### textarea

Multi-line text input.

```php
'excerpt' => [
    'type' => 'textarea',
    'label' => 'Excerpt',
    'minLength' => 50,
    'maxLength' => 500,
    'rows' => 6,
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `minLength` | int | — | Minimum character count |
| `maxLength` | int | — | Maximum character count (shows counter) |
| `rows` | int | `4` | Number of visible text rows |

### number

Numeric input for integers or decimals.

```php
'price' => [
    'type' => 'number',
    'label' => 'Price',
    'min' => 0,
    'max' => 10000,
    'step' => 0.01,
    'numberType' => 'float',
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `numberType` | string | `'int'` | `'int'` or `'float'` |
| `min` | number | — | Minimum allowed value |
| `max` | number | — | Maximum allowed value |
| `step` | number | `1` or `'any'` | Step increment (defaults to `'any'` for floats) |

### checkbox

Boolean toggle.

```php
'featured' => [
    'type' => 'checkbox',
    'label' => 'Featured',
    'description' => 'Show on homepage',
    'default' => false,
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `checkboxLabel` | string | same as `label` | Text shown next to the checkbox |

**Stored as:** `true` or `false` in YAML frontmatter.

### select

Dropdown selection.

```php
'difficulty' => [
    'type' => 'select',
    'label' => 'Difficulty',
    'required' => true,
    'options' => [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
    ],
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `options` | array | required | Key-value pairs or simple values |
| `multiple` | bool | `false` | Allow multiple selections |
| `emptyOption` | string | `'— Select —'` | Text for the empty/placeholder option |

**Options format:** Use `['value' => 'Display Label']` or simple `['Option 1', 'Option 2']`.

**Stored as:** Single value, or array if `multiple: true`.

### date

Date or datetime picker.

```php
'publish_date' => [
    'type' => 'date',
    'label' => 'Publish Date',
    'includeTime' => true,
    'min' => '2020-01-01',
    'max' => '2030-12-31',
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `includeTime` | bool | `false` | Include time picker |
| `min` | string | — | Earliest date (`YYYY-MM-DD`) |
| `max` | string | — | Latest date (`YYYY-MM-DD`) |

**Stored as:** `YYYY-MM-DD` or `YYYY-MM-DD HH:MM:SS` (with time).

### color

Colour picker.

```php
'accent_color' => [
    'type' => 'color',
    'label' => 'Accent Colour',
    'default' => '#3b82f6',
    'format' => 'hex',
    'alpha' => false,
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `format` | string | `'hex'` | Output format: `'hex'`, `'rgb'`, `'rgba'`, or `'hsl'` |
| `alpha` | bool | `false` | Allow transparency values |

**Stored as:** Colour string in the specified format (e.g., `#3b82f6`, `rgba(59, 130, 246, 0.5)`).

### image

Image picker with preview.

```php
'featured_image' => [
    'type' => 'image',
    'label' => 'Featured Image',
    'description' => '1200×630 recommended',
    'required' => true,
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `allowedTypes` | array | `['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg', '.avif']` | Allowed extensions |
| `basePath` | string | `'/media'` | Base path for file browser |
| `showPreview` | bool | `true` | Show image preview in editor |

**Stored as:** Path string with <code>@media<span></span>:</code> alias (e.g., <code>@media<span></span>:images/hero.jpg</code>). See [Path Aliases](/docs/configuration#path-aliases).

### file

General file picker.

```php
'download' => [
    'type' => 'file',
    'label' => 'Download File',
    'accept' => '.pdf,.zip,.docx',
    'required' => true,
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `accept` | string | `'*/*'` | Comma-separated extensions or MIME types |
| `basePath` | string | `'/media'` | Base path for file browser |

**Stored as:** Path string with <code>@media<span></span>:</code> alias (e.g., <code>@media<span></span>:documents/guide.pdf</code>).

### gallery

Multiple image picker with drag-and-drop reordering.

```php
'photos' => [
    'type' => 'gallery',
    'label' => 'Photo Gallery',
    'minItems' => 3,
    'maxItems' => 20,
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `allowedTypes` | array | `['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg', '.avif']` | Allowed extensions |
| `basePath` | string | `'/media'` | Base path for file browser |
| `minItems` | int | — | Minimum number of images |
| `maxItems` | int | — | Maximum number of images |

**Stored as:** Array of paths:

```yaml
photos:
  - "@media<span></span>:gallery/photo1.jpg"
  - "@media<span></span>:gallery/photo2.jpg"
```

### array

Dynamic list of values or key-value pairs.

```php
# Simple list
'ingredients' => [
    'type' => 'array',
    'label' => 'Ingredients',
    'minItems' => 1,
    'maxItems' => 50,
],

# Key-value pairs
'metadata' => [
    'type' => 'array',
    'label' => 'Metadata',
    'associative' => true,
    'keyPlaceholder' => 'Property',
    'valuePlaceholder' => 'Value',
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `associative` | bool | `false` | Store as key-value pairs instead of simple list |
| `minItems` | int | — | Minimum number of items |
| `maxItems` | int | — | Maximum number of items |
| `keyPlaceholder` | string | `'Key'` | Placeholder for key input (associative only) |
| `valuePlaceholder` | string | `'Value'` | Placeholder for value input (associative only) |
| `allowEmptyValues` | bool | `true` | Allow empty values (associative only) |

**Stored as (simple list):**
```yaml
ingredients:
  - "2 cups flour"
  - "1 tsp salt"
```

**Stored as (associative):**
```yaml
metadata:
  author: "John Doe"
  version: "1.0"
```

### content

Reference picker for linking to other content items.

```php
'related_posts' => [
    'type' => 'content',
    'label' => 'Related Posts',
    'contentType' => 'post',
    'multiple' => true,
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `contentType` | string | required | Content type to select from |
| `multiple` | bool | `false` | Allow multiple selections |
| `displayField` | string | `'title'` | Field to show in dropdown |
| `valueField` | string | `'slug'` | Field to store |

**Stored as:** Slug string, or array of slugs if `multiple: true`.

### taxonomy

Taxonomy term selector. Use this to assign categories, tags, or other taxonomy terms.

```php
'category' => [
    'type' => 'taxonomy',
    'taxonomy' => 'category',
    'label' => 'Categories',
    'required' => true,
    'multiple' => true,
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `taxonomy` | string | required | Taxonomy name (from [taxonomies.php](/docs/configuration#taxonomies-taxonomiesphp)) |
| `multiple` | bool | `true` | Allow multiple terms |
| `allowNew` | bool | `true` | Allow entering new terms |

**Stored as:** Term slug, or array of slugs if `multiple: true`.

**See:** [Taxonomies](/docs/taxonomies) for full documentation on defining taxonomies, term storage, and template helpers.

### status

Content status selector (`draft`, `published`, `unlisted`).

```php
'status' => [
    'type' => 'status',
    'label' => 'Status',
    'default' => 'draft',
],
```

Renders a visual toggle between the three states. Most content types don't need to define this explicitly—it's handled automatically by the core `status` frontmatter field.

**Valid values:** `draft`, `published`, `unlisted`

### template

Template file selector based on available theme templates.

```php
'template' => [
    'type' => 'template',
    'label' => 'Page Template',
    'defaultTemplate' => 'page.php',
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `defaultTemplate` | string | — | Template to mark as default |

Populates with `.php` files from your theme directory. See [Theming](/docs/theming) for template details.

## Field Groups

Organise related fields into collapsible panels in the admin editor:

```php
'fields' => [
    'author' => [
        'type' => 'text',
        'label' => 'Author',
        'group' => 'Meta',
    ],
    'publish_date' => [
        'type' => 'date',
        'label' => 'Publish Date',
        'group' => 'Meta',
    ],
    'featured_image' => [
        'type' => 'image',
        'label' => 'Featured Image',
        'group' => 'Media',
    ],
],
```

Fields with the same `group` value appear together. Groups render in the order the first field of each group appears.

## Validation

### CLI Linting

Run `./ava lint` to validate all content against your field definitions:

```bash
./ava lint
```

Example output:

```
Linting content files...

  ✗ content/posts/2024-01-15-new-post.md
    Line 5: Field 'author' is required but missing.
    Line 8: Field 'price' must be at least 0.

Found 2 errors in 1 file.
```

For more CLI commands, see [CLI Reference](/docs/cli).

### Admin Validation

The [admin dashboard](/docs/admin) provides real-time validation as you edit. Each field type has JavaScript validation that matches the server-side rules.

## Accessing Fields in Templates

Field values are stored in frontmatter and accessible via the Item API:

```php
// Get a field value
$author = $content->get('author');

// Check if a field has a value
if ($content->has('featured_image')) {
    echo '<img src="' . $content->get('featured_image') . '">';
}

// Get taxonomy terms
$categories = $content->terms('category');
```

For more on working with content in templates, see [Theming: Template Variables](/docs/theming#template-variables).

## Custom Field Types

Register custom field types for specialised inputs. Implement the `FieldType` interface:

```php
// In theme.php or a plugin
use Ava\Fields\FieldRegistry;
use Ava\Fields\FieldType;
use Ava\Fields\ValidationResult;

$registry = $ava->container()->get(FieldRegistry::class);

$registry->register(new class implements FieldType {
    public function name(): string
    {
        return 'rating';
    }
    
    public function label(): string
    {
        return 'Rating';
    }
    
    public function schema(): array
    {
        return [
            'min' => ['type' => 'int', 'default' => 1],
            'max' => ['type' => 'int', 'default' => 5],
        ];
    }
    
    public function validate(mixed $value, array $config): ValidationResult
    {
        if (!is_numeric($value)) {
            return ValidationResult::error('Rating must be a number.');
        }
        
        $min = $config['min'] ?? 1;
        $max = $config['max'] ?? 5;
        
        if ($value < $min || $value > $max) {
            return ValidationResult::error("Rating must be between {$min} and {$max}.");
        }
        
        return ValidationResult::success();
    }
    
    public function toStorage(mixed $value, array $config): mixed
    {
        return (int) $value;
    }
    
    public function fromStorage(mixed $value, array $config): mixed
    {
        return (int) $value;
    }
    
    public function defaultValue(array $config): mixed
    {
        return $config['default'] ?? null;
    }
    
    public function render(string $name, mixed $value, array $config, array $context = []): string
    {
        $min = $config['min'] ?? 1;
        $max = $config['max'] ?? 5;
        $id = 'field-' . htmlspecialchars($name);
        
        return '<input type="range" id="' . $id . '" name="fields[' . $name . ']" '
             . 'min="' . $min . '" max="' . $max . '" '
             . 'value="' . ($value ?? $min) . '">';
    }
    
    public function javascript(): string
    {
        return ''; // Optional: add client-side validation
    }
});
```

Then use your custom type:

```php
'rating' => [
    'type' => 'rating',
    'label' => 'Recipe Rating',
    'min' => 1,
    'max' => 10,
],
```

## Best Practices

1. **Choose specific types** — Use `date` for dates, `number` for numbers. This gives you proper inputs and validation.

2. **Always set labels** — Clear labels and descriptions help content editors understand each field.

3. **Use validation constraints** — Set `min`, `max`, `minLength`, `maxLength` to prevent bad data.

4. **Group related fields** — Use the `group` option to organise complex content types.

5. **Be thoughtful with `required`** — Only mark fields required if truly necessary.

6. **Provide sensible defaults** — Set `default` values to streamline content creation.

<div class="related-docs">
<h2>Related Documentation</h2>
<ul>
<li><a href="/docs/configuration#content-content-types-content_typesphp">Configuration: Content Types</a> — Defining fields in content types</li>
<li><a href="/docs/content#content-frontmatter-reference">Content: Frontmatter Reference</a> — How fields are stored</li>
<li><a href="/docs/taxonomies">Taxonomies</a> — The taxonomy field type in depth</li>
<li><a href="/docs/cli#content-lint">CLI: Lint</a> — Validating field values</li>
</ul>
</div>
