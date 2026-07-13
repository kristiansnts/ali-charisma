<div class="search-model predictive-search-model" id="header-predictive-search" aria-hidden="true">
    <div class="predictive-search">
        <button type="button" class="search-close-switch" aria-label="Close search">&times;</button>

        <form id="predictive-search-form" class="predictive-search__form" action="{{ route('malefashion.shop') }}" method="get" role="search">
            <label class="sr-only" for="search-input">Search</label>
            <input
                type="text"
                id="search-input"
                name="q"
                placeholder="Search"
                autocomplete="off"
                data-predictive-search-input
            >
        </form>

        <div class="predictive-search__content" data-predictive-search-results>
            <p class="predictive-search__hint">Start typing to search products.</p>
        </div>
    </div>
</div>
