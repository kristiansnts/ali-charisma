<?php

use App\Support\StorefrontSearchCatalog;

it('returns predictive search html with suggestions and products only', function () {
    $response = $this->getJson(route('malefashion.search.predictive', ['q' => 'be']))
        ->assertSuccessful()
        ->assertJsonPath('query', 'be');

    expect($response->json('counts.suggestions'))->toBeGreaterThan(0);
    expect($response->json('counts.products'))->toBeGreaterThan(0);
    expect($response->json('html'))
        ->toContain('Suggestions')
        ->toContain('<mark>be</mark>')
        ->toContain('Products')
        ->toContain('View all results')
        ->not->toContain('Collections')
        ->not->toContain('>Pages</');
});

it('highlights the matched query inside suggestion labels', function () {
    expect(StorefrontSearchCatalog::highlight('belmont', 'be'))
        ->toBe('<mark>be</mark><span>lmont</span>');
});

it('exposes predictive search markup on the storefront layout', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertSee('header-predictive-search', false)
        ->assertSee('data-predictive-search-input', false)
        ->assertSee('malefashionSearch', false)
        ->assertSee('predictiveUrl', false)
        ->assertSee('type="text"', false);
});

it('returns an empty state for queries with no matches', function () {
    $response = $this->getJson(route('malefashion.search.predictive', ['q' => 'zzzz-no-match']))
        ->assertSuccessful();

    expect($response->json('html'))->toContain('No results for');
    expect($response->json('counts.suggestions'))->toBe(0);
    expect($response->json('counts.products'))->toBe(0);
});
