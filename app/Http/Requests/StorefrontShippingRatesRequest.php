<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorefrontShippingRatesRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'apartment' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'province' => ['required', 'string', 'max:120'],
            'postal' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'size:2'],
        ];
    }

    /**
     * @return array{name: string, phone: string, address_line1: string, address_line2: string|null, city_locality: string, state_province: string, postal_code: string, country_code: string, address_residential_indicator: string}
     */
    public function shipTo(): array
    {
        $validated = $this->validated();

        return [
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
            'phone' => $validated['phone'],
            'address_line1' => $validated['address'],
            'address_line2' => $validated['apartment'] ?? null,
            'city_locality' => $validated['city'],
            'state_province' => $validated['province'],
            'postal_code' => $validated['postal'],
            'country_code' => strtoupper($validated['country']),
            'address_residential_indicator' => 'yes',
        ];
    }
}
