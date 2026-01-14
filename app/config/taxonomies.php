<?php

declare(strict_types=1);

/**
 * ══════════════════════════════════════════════════════════════════════════════
 * AVA CMS — TAXONOMIES
 * ══════════════════════════════════════════════════════════════════════════════
 *
 * Taxonomies organise content into groups (Categories, Tags, Authors, etc.).
 * Assign taxonomies to content types in content_types.php.
 *
 * Docs: https://ava.addy.zone/docs/configuration#taxonomies-taxonomiesphp
 */

return [
    
    /*
    |═══════════════════════════════════════════════════════════════════════════
    | ADD YOUR TAXONOMIES BELOW
    |═══════════════════════════════════════════════════════════════════════════
    | Copy the structure above to create new taxonomies.
    |
    | Example — Authors:
    |
    |   'author' => [
    |       'label'        => 'Authors',
    |       'hierarchical' => false,
    |       'public'       => true,
    |       'rewrite'      => ['base' => '/author'],
    |       'behaviour'    => ['allow_unknown_terms' => true],
    |       'ui'           => ['show_counts' => true, 'sort_terms' => 'name_asc'],
    |   ],
    |
    | Remember to add new taxonomies to content types in content_types.php:
    |   'taxonomies' => ['category', 'tag', 'author'],
    */

];
