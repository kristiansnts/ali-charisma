@php
    $hasResults = $suggestions !== [] || $products !== [];
@endphp

@if ($query === '')
    <p class="predictive-search__hint">Start typing to search products.</p>
@elseif (! $hasResults)
    <p class="predictive-search__hint">No results for “{{ $query }}”.</p>
@else
    <div class="predictive-search__results predictive-search__results--with-suggestions">
        @if ($suggestions !== [])
            <div class="predictive-search__resource-item">
                <p class="predictive-search__category">Suggestions</p>
                <div class="predictive-search__suggestions">
                    @foreach ($suggestions as $suggestion)
                        <div>
                            <a href="{{ $suggestion['url'] }}" class="predictive-search__suggestion-link">
                                {!! \App\Support\StorefrontSearchCatalog::highlight($suggestion['label'], $query) !!}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="predictive-search__resource-item">
            <p class="predictive-search__category">Products</p>
            @if ($products === [])
                <p class="predictive-search__hint">No products found.</p>
            @else
                <div class="predictive-search__products">
                    @foreach ($products as $product)
                        <a href="{{ $product['url'] }}" class="predictive-search__product-card">
                            <div class="predictive-search__product-media">
                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}">
                            </div>
                            <div class="predictive-search__product-info">
                                <span class="predictive-search__product-title">{{ $product['name'] }}</span>
                                <span class="predictive-search__product-price">{{ $product['price'] }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="predictive-search__view-all">
                    <button type="submit" class="predictive-search__view-all-btn" form="predictive-search-form">View all results</button>
                </div>
            @endif
        </div>
    </div>
@endif
