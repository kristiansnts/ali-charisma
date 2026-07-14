<div id="cart-drawer" class="cart-drawer" aria-hidden="true">
    <div class="cart-drawer__backdrop" data-cart-close></div>
    <aside class="cart-drawer__panel" role="dialog" aria-modal="true" aria-labelledby="cart-drawer-title">
        <div class="cart-drawer__header">
            <h2 id="cart-drawer-title">Cart</h2>
            <button type="button" class="cart-drawer__close" data-cart-close aria-label="Close cart">&times;</button>
        </div>
        <div class="cart-drawer__body" id="cart-drawer-body">
            @include('malefashion.partials.cart-drawer-body', [
                'items' => $cartItems ?? [],
                'total' => $cartTotal ?? '$0.00',
            ])
        </div>
    </aside>
</div>
