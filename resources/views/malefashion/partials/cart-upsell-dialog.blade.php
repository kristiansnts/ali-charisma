<div class="tdf_dialog_content cart-upsell" role="dialog" aria-modal="true" aria-label="Added to cart">
    <button type="button" class="cart-upsell__close" data-cart-upsell-close aria-label="Close">&times;</button>

    <div class="tdf_dialog_header">
        <div class="tdf_dialog_header_body">
            <div class="tdf_target_left">
                <div class="tdf_image">
                    <div class="tdf_detail_image">
                        <div class="tdf_image_target">
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" title="{{ $item['name'] }}">
                        </div>
                    </div>
                    <div class="tdf_target_name">
                        <span class="tdf_header_message">You added</span>
                        <strong title="{{ $item['name'] }}">{{ $item['name'] }}</strong>
                    </div>
                </div>
            </div>
            <div class="tdf_target_right">
                <div class="tdf_detail">
                    <div class="tdf_cart_detail">
                        <a href="{{ route('malefashion.cart') }}" class="tdf_link_view_cart">Cart total:</a>
                        <span class="tdf_cart_items">{{ $count }} {{ \Illuminate\Support\Str::plural('item', $count) }}</span>
                        <span class="tdf_cart_amount"> ({{ $total }})</span>
                    </div>
                    <div class="tdf_action">
                        <a href="{{ route('malefashion.checkout') }}" class="tdf_cta_btn">Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tdf_bs_offer">
        <div class="tdf_offer_message">
            <h3>Customers who bought this item also bought</h3>
        </div>
        <div class="tdf_bs_offer_products">
            @foreach ($upsells as $upsell)
                <div class="tdf_bs_offer_product">
                    <div class="tdf_horizontal_product">
                        <a href="{{ $upsell['url'] }}" class="tdf_horizontal_img_left">
                            <div class="tdf_img" style="background-image:url('{{ $upsell['image'] }}')"></div>
                        </a>
                        <div class="tdf_horizontal_content_right">
                            <a href="{{ $upsell['url'] }}" class="tdf_block" title="{{ $upsell['name'] }}">
                                <div class="tdf_horizontal_name">
                                    <h4 class="tdf_horizontal_title" title="{{ $upsell['name'] }}">{{ $upsell['name'] }}</h4>
                                </div>
                                <div class="tdf_horizontal_price">
                                    <span class="tdf_price_original">{{ $upsell['price'] }}</span>
                                    <span class="tdf_price_sales">{{ $upsell['sale_price'] }}</span>
                                </div>
                            </a>
                            <div>
                                <button
                                    type="button"
                                    class="tdf_normal_btn"
                                    data-add-to-cart
                                    data-cart-key="{{ $upsell['key'] }}"
                                    data-cart-name="{{ $upsell['name'] }}"
                                    data-cart-price="{{ preg_replace('/[^0-9.]/', '', $upsell['sale_price']) }}"
                                    data-cart-price-label="{{ $upsell['sale_price'] }}"
                                    data-cart-image="{{ $upsell['image'] }}"
                                >Add to cart</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
