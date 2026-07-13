<?php

namespace App\Http\Controllers;

use App\Support\StorefrontSearchCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PredictiveSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        $results = StorefrontSearchCatalog::search($query);

        return response()->json([
            'query' => $results['query'],
            'html' => view('malefashion.partials.predictive-search-results', $results)->render(),
            'counts' => [
                'suggestions' => count($results['suggestions']),
                'products' => count($results['products']),
                'collections' => count($results['collections']),
                'pages' => count($results['pages']),
            ],
        ]);
    }
}
