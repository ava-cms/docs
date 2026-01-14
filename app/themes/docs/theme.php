<?php

declare(strict_types=1);

/**
 * Ava CMS Documentation Theme
 * 
 * A clean, minimal documentation theme for Ava CMS.
 * 
 * Theme files:
 *   templates/   - Main page templates (page.php, index.php, etc.)
 *   partials/    - Reusable fragments (header.php, footer.php, sidebar.php)
 *   assets/      - CSS and images
 * 
 * @see https://ava.addy.zone/docs/themes
 */

use Ava\Application;
use Ava\Http\Request;
use Ava\Http\Response;
use Ava\Plugins\Hooks;

/**
 * Theme bootstrap function.
 * 
 * This function is called once when Ava loads the theme.
 * Add any custom routes, hooks, or filters here.
 */
return function (Application $app): void {
    
    /**
     * Search Route
     * 
     * Registers the /search route to handle search queries.
     * This uses Ava's built-in search system with proper weighting.
     */
    Hooks::addFilter('router.before_match', function ($match, Request $request) use ($app) {
        // Handle /search route
        if ($request->path() !== '/search') {
            return $match;
        }
        
        $searchQuery = trim($request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));
        
        // Build query with Ava's search
        $query = $app->query()
            ->published()
            ->orderBy('date', 'desc')
            ->perPage(10)
            ->page($page);
        
        // Apply search if query exists
        if ($searchQuery !== '') {
            $query->search($searchQuery);
        }
        
        // Render search template
        return Response::html(
            $app->render('search', [
                'query' => $query,
                'searchQuery' => $searchQuery,
                'request' => $request
            ])
        );
    });
    
    /**
     * JSON Search API Endpoint
     * 
     * Intercepts /search.json requests and returns search results as JSON.
     * Uses Ava's full-text search with proper content weighting.
     */
    Hooks::addFilter('router.before_match', function ($match, Request $request) use ($app) {
        // Only handle /search.json requests
        if ($request->path() !== '/search.json') {
            return $match;
        }
        
        $searchQuery = trim($request->query('q', ''));
        
        // Return empty results for short queries
        if (strlen($searchQuery) < 3) {
            return Response::json([
                'query' => $searchQuery,
                'items' => [],
                'count' => 0
            ]);
        }
        
        // Use Ava's built-in search with proper weighting
        $results = $app->query()
            ->published()
            ->search($searchQuery)
            ->perPage(8)
            ->get();
        
        // Format results for JSON
        $router = $app->router();
        $items = [];
        foreach ($results as $item) {
            $url = $router->urlFor($item->type(), $item->slug());
            if ($url) {
                $items[] = [
                    'title' => $item->title(),
                    'url' => $url,
                    'type' => ucfirst($item->type()),
                    'excerpt' => $item->excerpt() ?: substr(strip_tags($item->rawContent()), 0, 150)
                ];
            }
        }
        
        return Response::json([
            'query' => $searchQuery,
            'items' => $items,
            'count' => count($items)
        ]);
    });

};

