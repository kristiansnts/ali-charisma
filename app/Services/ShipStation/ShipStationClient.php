<?php

namespace App\Services\ShipStation;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ShipStationClient
{
    public function __construct(
        private readonly ?string $apiKey = null,
        private readonly ?string $baseUrl = null,
    ) {}

    public function post(string $path, array $payload): array
    {
        $response = $this->request()->post($this->url($path), $payload);

        try {
            $response->throw();
        } catch (RequestException $exception) {
            throw new RuntimeException(
                'ShipStation API request failed: '.$exception->response?->body(),
                previous: $exception,
            );
        }

        return $response->json() ?? [];
    }

    private function request(): PendingRequest
    {
        $apiKey = $this->apiKey ?? config('shipstation.api_key');

        if (blank($apiKey)) {
            throw new RuntimeException('SHIPSTATION_API_KEY is not configured.');
        }

        return Http::baseUrl(rtrim($this->baseUrl ?? (string) config('shipstation.base_url'), '/'))
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'API-Key' => $apiKey,
            ])
            ->timeout(30);
    }

    private function url(string $path): string
    {
        return '/'.ltrim($path, '/');
    }
}
