<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetShippingRatesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'ship_to' => ['required', 'array'],
            'ship_to.name' => ['required', 'string', 'max:255'],
            'ship_to.phone' => ['nullable', 'string', 'max:50'],
            'ship_to.company_name' => ['nullable', 'string', 'max:255'],
            'ship_to.address_line1' => ['required', 'string', 'max:255'],
            'ship_to.address_line2' => ['nullable', 'string', 'max:255'],
            'ship_to.city_locality' => ['required', 'string', 'max:255'],
            'ship_to.state_province' => ['required', 'string', 'max:50'],
            'ship_to.postal_code' => ['required', 'string', 'max:20'],
            'ship_to.country_code' => ['required', 'string', 'size:2'],
            'ship_to.address_residential_indicator' => ['nullable', 'string', Rule::in(['yes', 'no', 'unknown'])],

            'ship_from' => ['required', 'array'],
            'ship_from.name' => ['required', 'string', 'max:255'],
            'ship_from.phone' => ['nullable', 'string', 'max:50'],
            'ship_from.company_name' => ['nullable', 'string', 'max:255'],
            'ship_from.address_line1' => ['required', 'string', 'max:255'],
            'ship_from.address_line2' => ['nullable', 'string', 'max:255'],
            'ship_from.city_locality' => ['required', 'string', 'max:255'],
            'ship_from.state_province' => ['required', 'string', 'max:50'],
            'ship_from.postal_code' => ['required', 'string', 'max:20'],
            'ship_from.country_code' => ['required', 'string', 'size:2'],
            'ship_from.address_residential_indicator' => ['nullable', 'string', Rule::in(['yes', 'no', 'unknown'])],

            'packages' => ['required', 'array', 'min:1'],
            'packages.*.package_code' => ['nullable', 'string', 'max:50'],
            'packages.*.weight' => ['required', 'array'],
            'packages.*.weight.value' => ['required', 'numeric', 'min:0'],
            'packages.*.weight.unit' => ['required', 'string', Rule::in(['ounce', 'pound', 'gram', 'kilogram'])],
            'packages.*.dimensions' => ['nullable', 'array'],
            'packages.*.dimensions.unit' => ['required_with:packages.*.dimensions', 'string', Rule::in(['inch', 'centimeter'])],
            'packages.*.dimensions.length' => ['required_with:packages.*.dimensions', 'numeric', 'min:0'],
            'packages.*.dimensions.width' => ['required_with:packages.*.dimensions', 'numeric', 'min:0'],
            'packages.*.dimensions.height' => ['required_with:packages.*.dimensions', 'numeric', 'min:0'],

            'validate_address' => ['nullable', 'string', Rule::in(['no_validation', 'validate_only', 'validate_and_clean'])],
            'preferred_currency' => ['nullable', 'string', 'size:3'],
            'calculate_tax_amount' => ['nullable', 'boolean'],
            'is_return' => ['nullable', 'boolean'],
            'package_types' => ['nullable', 'array'],
            'package_types.*' => ['string', 'max:50'],
        ];
    }
}
