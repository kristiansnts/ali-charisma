<div class="compare-modal" id="compare-modal" aria-hidden="true">
    <div class="compare-modal__backdrop" data-compare-close></div>
    <div class="compare-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="compare-modal-title">
        <button type="button" class="compare-modal__close" data-compare-close aria-label="Close compare">&times;</button>
        <div class="compare-modal__body" id="compare-modal-body">
            @include('malefashion.partials.compare-table', [
                'products' => $compareProducts ?? [],
            ])
        </div>
    </div>
</div>
