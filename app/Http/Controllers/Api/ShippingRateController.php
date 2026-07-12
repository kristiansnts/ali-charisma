<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetShippingRatesRequest;
use App\Models\Team;
use App\Services\ShipStation\ShipmentRateRequest;
use App\Services\ShipStation\ShipStationRatesService;
use Illuminate\Http\JsonResponse;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class ShippingRateController extends Controller
{
    /**
     * Customer-facing rates: frontend supplies shipment + optional rate_options;
     * carrier_ids / service_codes come only from the tenant's activated vendors.
     */
    public function store(
        GetShippingRatesRequest $request,
        Team $team,
        ShipStationRatesService $rates,
    ): JsonResponse {
        $validated = $request->validated();

        try {
            $result = $rates->getRatesForTeam($team, new ShipmentRateRequest(
                shipTo: $validated['ship_to'],
                shipFrom: $validated['ship_from'],
                packages: $validated['packages'],
                validateAddress: $validated['validate_address'] ?? 'no_validation',
                preferredCurrency: isset($validated['preferred_currency'])
                    ? strtoupper($validated['preferred_currency'])
                    : null,
                calculateTaxAmount: $validated['calculate_tax_amount'] ?? null,
                isReturn: $validated['is_return'] ?? null,
                packageTypes: $validated['package_types'] ?? null,
            ));
        } catch (RuntimeException $exception) {
            $status = str_contains($exception->getMessage(), 'No activated shipping carriers')
                ? Response::HTTP_UNPROCESSABLE_ENTITY
                : Response::HTTP_BAD_GATEWAY;

            return response()->json([
                'message' => $exception->getMessage(),
            ], $status);
        }

        return response()->json([
            'rates' => $result->rates,
            'raw' => $result->raw,
        ]);
    }
}
