<div
    id="customer-address-new"
    class="account-address-modal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="address-modal-title"
    aria-hidden="true"
>
    <div class="account-address-modal__backdrop" data-address-modal-close></div>
    <div class="account-address-modal__dialog">
        <button type="button" class="account-address-modal__close" data-address-modal-close aria-label="Close">&times;</button>
        <h2 id="address-modal-title" class="account-address-modal__title">Add address</h2>

        <form method="post" action="{{ route('malefashion.account.addresses.store') }}" id="address_form_new" class="account-address-form">
            @csrf
            <input type="hidden" name="id" id="address-id" value="">

            <div class="account-address-form__grid">
                <div class="account-address-form__field">
                    <input id="address-first-name" class="account-address-form__input" type="text" name="first_name" placeholder=" " autocomplete="given-name">
                    <label for="address-first-name" class="account-address-form__label">First name</label>
                </div>
                <div class="account-address-form__field">
                    <input id="address-last-name" class="account-address-form__input" type="text" name="last_name" placeholder=" " autocomplete="family-name">
                    <label for="address-last-name" class="account-address-form__label">Last name</label>
                </div>
                <div class="account-address-form__field">
                    <input id="address-company" class="account-address-form__input" type="text" name="company" placeholder=" " autocomplete="organization">
                    <label for="address-company" class="account-address-form__label">Company</label>
                </div>
                <div class="account-address-form__field">
                    <input id="address-phone" class="account-address-form__input" type="tel" name="phone" placeholder=" " autocomplete="tel">
                    <label for="address-phone" class="account-address-form__label">Phone number</label>
                </div>
                <div class="account-address-form__field">
                    <input id="address-address1" class="account-address-form__input" type="text" name="address1" placeholder=" " autocomplete="address-line1">
                    <label for="address-address1" class="account-address-form__label">Address 1</label>
                </div>
                <div class="account-address-form__field">
                    <input id="address-address2" class="account-address-form__input" type="text" name="address2" placeholder=" " autocomplete="address-line2">
                    <label for="address-address2" class="account-address-form__label">Address 2</label>
                </div>
                <div class="account-address-form__row">
                    <div class="account-address-form__field">
                        <input id="address-city" class="account-address-form__input" type="text" name="city" placeholder=" " autocomplete="address-level2">
                        <label for="address-city" class="account-address-form__label">City</label>
                    </div>
                    <div class="account-address-form__field">
                        <input id="address-zip" class="account-address-form__input" type="text" name="zip" placeholder=" " autocomplete="postal-code">
                        <label for="address-zip" class="account-address-form__label">Zip code</label>
                    </div>
                </div>
                <div class="account-address-form__field">
                    <select id="address-country" class="account-address-form__select js-native-select" name="country" autocomplete="country">
                        <option value="Indonesia" selected>Indonesia</option>
                        <option value="Singapore">Singapore</option>
                        <option value="Malaysia">Malaysia</option>
                        <option value="Australia">Australia</option>
                        <option value="United States">United States</option>
                        <option value="United Kingdom">United Kingdom</option>
                    </select>
                    <label for="address-country" class="account-address-form__label">Country</label>
                </div>
                <div class="account-address-form__field">
                    <select id="address-province" class="account-address-form__select js-native-select" name="province" autocomplete="address-level1">
                        <option value="">---</option>
                    </select>
                    <label for="address-province" class="account-address-form__label">Province</label>
                </div>
                <div class="account-address-form__checkbox">
                    <input id="address-default" class="account-address-form__checkbox-input" type="checkbox" name="default" value="1">
                    <label for="address-default">Set as default address</label>
                </div>
            </div>

            <button type="submit" class="account-page__btn account-page__btn--block">Save address</button>
        </form>
    </div>
</div>
