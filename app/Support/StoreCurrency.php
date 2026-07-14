<?php

namespace App\Support;

use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use RuntimeException;
use Throwable;

class StoreCurrency
{
    public function __construct(
        private readonly ExchangeRate $exchangeRates,
    ) {}

    public function convert(float $amount, string $fromCurrency): float
    {
        $from = strtoupper(trim($fromCurrency));
        $target = $this->code();

        if ($from === $target) {
            return round($amount, 2);
        }

        try {
            $multiplier = $this->multiplier($from, $target);
        } catch (Throwable $exception) {
            throw new RuntimeException('Unable to convert shipping rates right now.', 0, $exception);
        }

        return round($amount * $multiplier, 2);
    }

    public function code(): string
    {
        return strtoupper((string) config('shipstation.preferred_currency', 'USD'));
    }

    /**
     * exchangeratesapi.io free tier only allows EUR as base; cross via EUR for any pair.
     */
    private function multiplier(string $from, string $target): float
    {
        if ($from === 'EUR') {
            return (float) $this->exchangeRates->exchangeRate('EUR', $target);
        }

        if ($target === 'EUR') {
            $rate = (float) $this->exchangeRates->exchangeRate('EUR', $from);

            return 1 / $rate;
        }

        /** @var array<string, float|int> $rates */
        $rates = $this->exchangeRates->exchangeRate('EUR', [$from, $target]);

        return (float) $rates[$target] / (float) $rates[$from];
    }
}
