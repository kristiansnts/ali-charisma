<?php

namespace App\Http\Controllers;

use App\Support\CheckoutCustomerData;
use App\Support\ProductCartList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StorefrontCartController extends Controller
{
    public function index(): View
    {
        return view('malefashion.pages.cart', [
            'items' => ProductCartList::items(),
            'subtotal' => ProductCartList::subtotal(),
            'total' => ProductCartList::subtotal(),
        ]);
    }

    public function drawer(): JsonResponse
    {
        return response()->json($this->drawerPayload());
    }

    public function checkout(): View|RedirectResponse
    {
        $items = ProductCartList::items();

        if ($items === []) {
            return redirect()
                ->route('malefashion.cart')
                ->with('status', 'Your cart is empty. Add a product before checkout.');
        }

        $subtotal = ProductCartList::subtotal();
        $shipping = 0.0;
        $account = Auth::guard('account')->user();

        return view('malefashion.pages.checkout', [
            'lineItems' => array_map(fn (array $item): array => [
                'key' => $item['key'],
                'name' => $item['name'],
                'variant' => '',
                'price' => $item['price'],
                'qty' => $item['qty'],
                'image' => $item['image'],
            ], $items),
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
            'account' => $account,
            'customer' => CheckoutCustomerData::for($account),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['nullable'],
            'price_label' => ['nullable', 'string', 'max:40'],
            'image' => ['nullable', 'string', 'max:500'],
            'product_id' => ['nullable', 'integer'],
        ]);

        $added = ProductCartList::add($validated);

        return response()->json([
            'ok' => true,
            'count' => ProductCartList::count(),
            'total' => ProductCartList::formattedSubtotal(),
            'item' => $added,
            'drawer_html' => $this->drawerHtml(),
            'html' => view('malefashion.partials.cart-upsell-dialog', [
                'item' => $added,
                'count' => ProductCartList::count(),
                'total' => ProductCartList::formattedSubtotal(),
                'upsells' => $this->upsells($added['key']),
            ])->render(),
        ]);
    }

    public function sync(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'qty' => ['required', 'array'],
            'qty.*' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        ProductCartList::syncQuantities($validated['qty']);

        if ($request->wantsJson()) {
            return response()->json($this->drawerPayload());
        }

        return redirect()
            ->route('malefashion.cart')
            ->with('status', 'Cart updated.');
    }

    public function destroy(Request $request, string $key): JsonResponse|RedirectResponse
    {
        ProductCartList::remove($key);

        if ($request->wantsJson()) {
            return response()->json($this->drawerPayload());
        }

        return redirect()
            ->route('malefashion.cart')
            ->with('status', 'Item removed from cart.');
    }

    /**
     * @return array{ok: true, count: int, total: string, html: string}
     */
    private function drawerPayload(): array
    {
        return [
            'ok' => true,
            'count' => ProductCartList::count(),
            'total' => ProductCartList::formattedSubtotal(),
            'html' => $this->drawerHtml(),
        ];
    }

    private function drawerHtml(): string
    {
        return view('malefashion.partials.cart-drawer-body', [
            'items' => ProductCartList::items(),
            'total' => ProductCartList::formattedSubtotal(),
        ])->render();
    }

    /**
     * @return list<array{key: string, name: string, price: string, sale_price: string, image: string, url: string}>
     */
    private function upsells(string $excludeKey): array
    {
        $catalog = [
            [
                'key' => 'evie-sweater-black',
                'name' => 'Evie Sweater Black',
                'price' => '$319.00',
                'sale_price' => '$271.15',
                'image' => asset('malefashion/img/product/product-2.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'key' => 'daliya-cardigan',
                'name' => 'Daliya Cardigan Dark Brown',
                'price' => '$299.00',
                'sale_price' => '$254.15',
                'image' => asset('malefashion/img/product/product-3.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'key' => 'eudora-top-ivory',
                'name' => 'Eudora Top Ivory',
                'price' => '$329.00',
                'sale_price' => '$279.65',
                'image' => asset('malefashion/img/product/product-5.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
        ];

        return array_values(array_filter(
            $catalog,
            fn (array $item): bool => $item['key'] !== $excludeKey
        ));
    }
}
