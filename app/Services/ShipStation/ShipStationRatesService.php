<?php

namespace App\Services\ShipStation;

use App\Models\ShippingVendor;
use App\Models\Team;
use Illuminate\Support\Collection;
use RuntimeException;

class ShipStationRatesService
{
    public function __construct(
        private readonly ShipStationClient $client,
    ) {}

    /**
     * Get rates for a tenant using only activated vendors' carrier_ids / service_codes.
     * All shipment fields and optional rate_options (preferred_currency, etc.) come from the customer request.
     */
    public function getRatesForTeam(Team $team, ShipmentRateRequest $request): RatesResult
    {
        $vendors = $this->activatedVendorsForTeam($team);

        if ($vendors->isEmpty()) {
            throw new RuntimeException('No activated shipping carriers for this store.');
        }

        $carrierIds = $vendors
            ->pluck('carrier_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $serviceCodes = $vendors
            ->pluck('service_codes')
            ->filter()
            ->flatten()
            ->unique()
            ->values()
            ->all();

        $rateOptions = array_filter([
            'carrier_ids' => array_values($carrierIds),
            'service_codes' => $serviceCodes !== [] ? array_values($serviceCodes) : null,
            'preferred_currency' => $request->preferredCurrency,
            'calculate_tax_amount' => $request->calculateTaxAmount,
            'is_return' => $request->isReturn,
            'package_types' => $request->packageTypes !== null && $request->packageTypes !== []
                ? array_values($request->packageTypes)
                : null,
        ], fn (mixed $value): bool => $value !== null);

        $shipment = [
            'validate_address' => $request->validateAddress,
            'ship_to' => $request->shipTo,
            'ship_from' => $request->shipFrom,
            'packages' => $request->packages,
        ];

        if ($request->customs !== null) {
            $shipment['customs'] = $request->customs;
        }

        $payload = [
            'rate_options' => $rateOptions,
            'shipment' => $shipment,
        ];

        $raw = $this->client->post('/v2/rates', $payload);

        /** @var list<array<string, mixed>> $rates */
        $rates = $raw['rate_response']['rates'] ?? $raw['rates'] ?? [];

        return new RatesResult($raw, $rates);
    }

    /**
     * @return Collection<int, ShippingVendor>
     */
    public function activatedVendorsForTeam(Team $team): Collection
    {
        return ShippingVendor::query()
            ->where('team_id', $team->id)
            ->where('is_activated', true)
            ->whereNotNull('carrier_id')
            ->get();
    }
}
