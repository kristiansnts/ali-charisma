<?php

namespace App\Services\ShipStation;

class ShipmentRateRequest
{
    /**
     * Customer/frontend-supplied shipment + optional rate options.
     * carrier_ids / service_codes are never taken from the customer — the service
     * injects those from the tenant's activated shipping vendors.
     *
     * @param  array{name: string, phone?: string|null, company_name?: string|null, address_line1: string, address_line2?: string|null, city_locality: string, state_province: string, postal_code: string, country_code: string, address_residential_indicator?: string}  $shipTo
     * @param  array{name: string, phone?: string|null, company_name?: string|null, address_line1: string, address_line2?: string|null, city_locality: string, state_province: string, postal_code: string, country_code: string, address_residential_indicator?: string}  $shipFrom
     * @param  list<array{package_code?: string, weight: array{value: float|int, unit: string}, dimensions?: array{unit: string, length: float|int, width: float|int, height: float|int}, products?: list<array<string, mixed>>}>  $packages
     * @param  array{contents: string, non_delivery: string}|null  $customs
     * @param  list<string>|null  $packageTypes
     */
    public function __construct(
        public array $shipTo,
        public array $shipFrom,
        public array $packages,
        public string $validateAddress = 'no_validation',
        public ?string $preferredCurrency = null,
        public ?bool $calculateTaxAmount = null,
        public ?bool $isReturn = null,
        public ?array $customs = null,
        public ?array $packageTypes = null,
    ) {}
}
