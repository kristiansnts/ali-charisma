@php
    /** @var list<array{id: int, slug: string, name: string, image: string, price_label: string, description: string, meta_tags: list<string>, category: string|null, availability: string, sku: string, colors: list<string>, sizes: list<string>}> $products */
    $products = $products ?? [];
@endphp

<div class="compare_head">
    <h2 id="compare-modal-title">Compare</h2>
    @if (count($products) > 0)
        <button type="button" class="cws_compare_remove_all" data-compare-clear>REMOVE ALL</button>
    @endif
</div>

@if (count($products) === 0)
    <p class="compare-empty">No products to compare yet. Use Compare on a product card to add one.</p>
@else
    <div class="cws_compare_table_wrap">
        <table class="cws_compare_table">
            <tbody>
                <tr class="compare_products">
                    <td><h4>Products</h4></td>
                    <td class="spacing"></td>
                    @foreach ($products as $product)
                        <td>
                            <button type="button" class="cws_remove_compare_product" data-compare-remove="{{ $product['id'] }}">Remove</button>
                            <div class="compare_image_wrapper">
                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}">
                            </div>
                            <h3 class="product_title entry-title">{{ $product['name'] }}</h3>
                            <p class="price">{{ $product['price_label'] }}</p>
                            <a href="{{ route('malefashion.shop-details') }}" class="compare-view-btn">View product</a>
                        </td>
                    @endforeach
                </tr>
                <tr class="compare_description">
                    <td><h4>Description</h4></td>
                    <td class="spacing"></td>
                    @foreach ($products as $product)
                        <td>
                            <div class="woocommerce-product-details__short-description">
                                <p>{{ \Illuminate\Support\Str::limit($product['description'], 220) }}</p>
                            </div>
                        </td>
                    @endforeach
                </tr>
                <tr class="compare_meta">
                    <td><h4>Meta</h4></td>
                    <td class="spacing"></td>
                    @foreach ($products as $product)
                        <td>
                            @if ($product['category'])
                                <div class="categories">{{ $product['category'] }}</div>
                            @endif
                            @if ($product['meta_tags'] !== [])
                                <div class="tags">{{ implode(', ', $product['meta_tags']) }}</div>
                            @endif
                        </td>
                    @endforeach
                </tr>
                <tr class="compare_availability">
                    <td><h4>Availability</h4></td>
                    <td class="spacing"></td>
                    @foreach ($products as $product)
                        <td>{{ $product['availability'] }}</td>
                    @endforeach
                </tr>
                <tr class="compare_sku">
                    <td><h4>Sku</h4></td>
                    <td class="spacing"></td>
                    @foreach ($products as $product)
                        <td>{{ $product['sku'] }}</td>
                    @endforeach
                </tr>
                <tr class="compare_attribute color">
                    <td><h4>color</h4></td>
                    <td class="spacing"></td>
                    @foreach ($products as $product)
                        <td>{{ $product['colors'] !== [] ? implode(', ', $product['colors']) : '—' }}</td>
                    @endforeach
                </tr>
                <tr class="compare_attribute size">
                    <td><h4>size</h4></td>
                    <td class="spacing"></td>
                    @foreach ($products as $product)
                        <td>{{ $product['sizes'] !== [] ? implode(', ', $product['sizes']) : '—' }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
@endif
