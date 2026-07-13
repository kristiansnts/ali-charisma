<?php

namespace App\Http\Controllers;

use App\Support\ProductWishlistList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        return view('malefashion.pages.wishlist', [
            'items' => ProductWishlistList::items(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['nullable', 'string', 'max:40'],
            'image' => ['nullable', 'string', 'max:500'],
            'product_id' => ['nullable', 'integer'],
        ]);

        $added = ProductWishlistList::add($validated);

        return response()->json([
            'ok' => $added,
            'count' => ProductWishlistList::count(),
            'in_wishlist' => ProductWishlistList::has($validated['key']),
            'message' => $added
                ? 'Product added to wishlist.'
                : 'Wishlist is full (max '.ProductWishlistList::MAX_ITEMS.').',
        ], $added ? 200 : 422);
    }

    public function destroy(string $key): JsonResponse
    {
        ProductWishlistList::remove($key);

        return response()->json([
            'ok' => true,
            'count' => ProductWishlistList::count(),
            'in_wishlist' => false,
        ]);
    }
}
