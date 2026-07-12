<?php

namespace App\Services\ShipStation;

class RatesResult
{
    /**
     * @param  array<string, mixed>  $raw
     * @param  list<array<string, mixed>>  $rates
     */
    public function __construct(
        public array $raw,
        public array $rates,
    ) {}
}
