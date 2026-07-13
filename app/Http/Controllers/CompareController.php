<?php

namespace App\Http\Controllers;

use App\Support\ProductCompareAttributes;
use App\Support\ProductCompareList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use TomatoPHP\FilamentEcommerce\Models\Product;

class CompareController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $products = ProductCompareList::products()
            ->map(fn (Product $product): array => ProductCompareAttributes::from($product))
            ->all();

        if ($request->wantsJson()) {
            return response()->json([
                'count' => count($products),
                'products' => $products,
                'html' => view('malefashion.partials.compare-table', [
                    'products' => $products,
                ])->render(),
            ]);
        }

        return view('malefashion.partials.compare-table', [
            'products' => $products,
        ]);
    }

    public function store(Product $product): JsonResponse
    {
        $added = ProductCompareList::add((int) $product->id);

        return response()->json([
            'ok' => $added,
            'count' => ProductCompareList::count(),
            'message' => $added
                ? 'Product added to compare.'
                : 'Compare list is full (max '.ProductCompareList::MAX_ITEMS.').',
        ], $added ? 200 : 422);
    }

    public function destroy(Product $product): JsonResponse
    {
        ProductCompareList::remove((int) $product->id);

        return response()->json([
            'ok' => true,
            'count' => ProductCompareList::count(),
        ]);
    }

    public function clear(): JsonResponse
    {
        ProductCompareList::clear();

        return response()->json([
            'ok' => true,
            'count' => 0,
        ]);
    }
}
