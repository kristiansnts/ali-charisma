@extends('layouts.malefashion')

@section('title', 'Addresses — Ali Charisma')

@section('content')
<section class="account-page">
    <div class="container">
        <a href="{{ route('malefashion.account') }}" class="account-page__back">&lt; Back to account</a>
        <h1 class="account-page__title">Addresses</h1>

        @if (session('status'))
            <div class="account-page__status" role="status">{{ session('status') }}</div>
        @endif

        <button type="button" class="account-page__btn account-page__btn--block" data-address-modal-open>
            Add address
        </button>

        <div class="account-addresses">
            @foreach ($addresses as $address)
                <article class="account-addresses__item">
                    @if ($address['default'])
                        <p class="account-addresses__label">Default address</p>
                    @endif
                    <div class="account-addresses__body">
                        @include('malefashion.partials.account-address-lines', ['address' => $address])
                    </div>
                    <div class="account-addresses__actions">
                        <button
                            type="button"
                            class="account-addresses__link"
                            data-address-modal-open
                            data-address="{{ e(json_encode($address)) }}"
                        >Edit</button>
                        <form action="{{ route('malefashion.account.addresses.destroy', $address['id']) }}" method="post" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="account-addresses__link">Delete</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

@include('malefashion.partials.account-address-modal')
@endsection

@push('scripts')
<script>
(function ($) {
    var $modal = $('#customer-address-new');
    var provincesByCountry = @json([
        'Indonesia' => \App\Support\CustomerAddressList::provincesFor('Indonesia'),
    ]);

    function fillProvinces(country, selected) {
        var $province = $('#address-province');
        var $wrap = $province.closest('.account-address-form__field');
        var list = provincesByCountry[country] || [];
        $province.empty().append($('<option>', { value: '', text: '---' }));
        list.forEach(function (pair) {
            $province.append($('<option>', {
                value: pair[0],
                text: pair[1],
                selected: selected === pair[0]
            }));
        });
        $wrap.prop('hidden', list.length === 0);
    }

    function openModal(address) {
        var data = address || {};
        $('#address-id').val(data.id || '');
        $('#address-first-name').val(data.first_name || '');
        $('#address-last-name').val(data.last_name || '');
        $('#address-company').val(data.company || '');
        $('#address-phone').val(data.phone || '');
        $('#address-address1').val(data.address1 || '');
        $('#address-address2').val(data.address2 || '');
        $('#address-city').val(data.city || '');
        $('#address-zip').val(data.zip || '');
        $('#address-country').val(data.country || 'Indonesia');
        $('#address-default').prop('checked', !!data.default || !data.id);
        fillProvinces($('#address-country').val(), data.province || '');
        $modal.addClass('is-open').attr('aria-hidden', 'false');
        $('body').addClass('account-modal-open');
        $('#address-modal-title').text(data.id ? 'Edit address' : 'Add address');
    }

    function closeModal() {
        $modal.removeClass('is-open').attr('aria-hidden', 'true');
        $('body').removeClass('account-modal-open');
    }

    $(document).on('click', '[data-address-modal-open]', function () {
        var raw = $(this).attr('data-address');
        var address = null;
        if (raw) {
            try {
                address = JSON.parse(raw);
            } catch (e) {
                address = null;
            }
        }
        openModal(address);
    });

    $(document).on('click', '[data-address-modal-close]', closeModal);

    $(document).on('change', '#address-country', function () {
        fillProvinces($(this).val(), '');
    });

    fillProvinces($('#address-country').val() || 'Indonesia', '');
})(jQuery);
</script>
@endpush
