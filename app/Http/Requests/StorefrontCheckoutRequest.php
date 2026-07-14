<?php

namespace App\Http\Requests;

use App\Support\CheckoutShippingQuote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use RuntimeException;

class StorefrontCheckoutRequest extends FormRequest
{
    /**
     * @var array{rate_id: string, service_code: string, service_type: string, carrier_friendly_name: string, amount: float, currency: string, delivery_days: int|null, meta: string}|null
     */
    private ?array $selectedShippingRate = null;

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
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'apartment' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'province' => ['required', 'string', 'max:120'],
            'postal' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'size:2'],
            'shipping_service_code' => ['required', 'string', 'max:120'],
            'shipping_rate_id' => ['nullable', 'string', 'max:120'],
            'shipping_amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            try {
                $quote = app(CheckoutShippingQuote::class)->forCart($this->shipTo());
            } catch (RuntimeException $exception) {
                $validator->errors()->add('shipping_service_code', $exception->getMessage());

                return;
            }

            $rate = app(CheckoutShippingQuote::class)->matchSelectedRate(
                $quote,
                (string) $this->input('shipping_service_code'),
                (float) $this->input('shipping_amount'),
            );

            if ($rate === null) {
                $validator->errors()->add('shipping_service_code', 'The selected shipping rate is invalid.');

                return;
            }

            $this->selectedShippingRate = $rate;
        });
    }

    /**
     * @return array{rate_id: string, service_code: string, service_type: string, carrier_friendly_name: string, amount: float, currency: string, delivery_days: int|null, meta: string}
     */
    public function selectedShippingRate(): array
    {
        return $this->selectedShippingRate ?? throw new RuntimeException('Shipping rate has not been validated.');
    }

    /**
     * @return array<string, mixed>
     */
    public function checkoutPayload(): array
    {
        $validated = $this->validated();
        $rate = $this->selectedShippingRate();

        $validated['shipping_rate_id'] = $rate['rate_id'];
        $validated['shipping_amount'] = $rate['amount'];
        $validated['shipping_service_code'] = $rate['service_code'];

        return $validated;
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
