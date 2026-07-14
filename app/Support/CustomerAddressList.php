<?php

namespace App\Support;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CustomerAddressList
{
    public const SESSION_KEY = 'customer_addresses';

    /**
     * @return list<array{
     *     id: string,
     *     first_name: string,
     *     last_name: string,
     *     company: string,
     *     phone: string,
     *     address1: string,
     *     address2: string,
     *     city: string,
     *     zip: string,
     *     country: string,
     *     province: string,
     *     default: bool
     * }>
     */
    public static function items(): array
    {
        $items = collect(Session::get(self::SESSION_KEY, []))
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['id'] ?? null))
            ->map(fn (array $item): array => self::normalize($item))
            ->values()
            ->all();

        if ($items === []) {
            $items = [self::normalize([
                'id' => 'default-indonesia',
                'country' => 'Indonesia',
                'default' => true,
            ])];
            Session::put(self::SESSION_KEY, $items);
        }

        return $items;
    }

    /**
     * @return array{
     *     id: string,
     *     first_name: string,
     *     last_name: string,
     *     company: string,
     *     phone: string,
     *     address1: string,
     *     address2: string,
     *     city: string,
     *     zip: string,
     *     country: string,
     *     province: string,
     *     default: bool
     * }|null
     */
    public static function default(): ?array
    {
        $items = self::items();

        return collect($items)->firstWhere('default', true) ?? ($items[0] ?? null);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{
     *     id: string,
     *     first_name: string,
     *     last_name: string,
     *     company: string,
     *     phone: string,
     *     address1: string,
     *     address2: string,
     *     city: string,
     *     zip: string,
     *     country: string,
     *     province: string,
     *     default: bool
     * }
     */
    public static function store(array $data, ?string $id = null): array
    {
        $items = self::items();
        $isDefault = (bool) ($data['default'] ?? false);
        $address = self::normalize([
            ...$data,
            'id' => $id ?: (string) Str::uuid(),
            'default' => $isDefault,
        ]);

        if ($isDefault) {
            $items = array_map(function (array $item) use ($address): array {
                $item['default'] = $item['id'] === $address['id'];

                return $item;
            }, $items);
        }

        $updated = false;
        foreach ($items as $index => $item) {
            if ($item['id'] === $address['id']) {
                $items[$index] = $address;
                $updated = true;
                break;
            }
        }

        if (! $updated) {
            if ($items === [] || $isDefault) {
                $address['default'] = true;
                $items = array_map(function (array $item) use ($address): array {
                    $item['default'] = $item['id'] === $address['id'];

                    return $item;
                }, $items);
            }
            $items[] = $address;
        }

        if (! collect($items)->contains(fn (array $item): bool => $item['default'])) {
            $items[0]['default'] = true;
        }

        Session::put(self::SESSION_KEY, array_values($items));

        return $address;
    }

    public static function remove(string $id): void
    {
        $items = array_values(array_filter(
            self::items(),
            fn (array $item): bool => $item['id'] !== $id
        ));

        if ($items !== [] && ! collect($items)->contains(fn (array $item): bool => $item['default'])) {
            $items[0]['default'] = true;
        }

        Session::put(self::SESSION_KEY, $items);
    }

    /**
     * @return list<array{0: string, 1: string}>
     */
    public static function provincesFor(string $country): array
    {
        return match ($country) {
            'Indonesia' => [
                ['Aceh', 'Aceh'],
                ['Bali', 'Bali'],
                ['Banten', 'Banten'],
                ['Bengkulu', 'Bengkulu'],
                ['Jakarta', 'Jakarta'],
                ['Jambi', 'Jambi'],
                ['Jawa Barat', 'West Java'],
                ['Jawa Tengah', 'Central Java'],
                ['Jawa Timur', 'East Java'],
                ['Kalimantan Barat', 'West Kalimantan'],
                ['Kalimantan Selatan', 'South Kalimantan'],
                ['Kalimantan Tengah', 'Central Kalimantan'],
                ['Kalimantan Timur', 'East Kalimantan'],
                ['Kalimantan Utara', 'North Kalimantan'],
                ['Kepulauan Riau', 'Riau Islands'],
                ['Lampung', 'Lampung'],
                ['Maluku', 'Maluku'],
                ['Maluku Utara', 'North Maluku'],
                ['North Sumatra', 'North Sumatra'],
                ['Nusa Tenggara Barat', 'West Nusa Tenggara'],
                ['Nusa Tenggara Timur', 'East Nusa Tenggara'],
                ['Papua', 'Papua'],
                ['Papua Barat', 'West Papua'],
                ['Riau', 'Riau'],
                ['South Sumatra', 'South Sumatra'],
                ['Sulawesi Barat', 'West Sulawesi'],
                ['Sulawesi Selatan', 'South Sulawesi'],
                ['Sulawesi Tengah', 'Central Sulawesi'],
                ['Sulawesi Tenggara', 'Southeast Sulawesi'],
                ['Sulawesi Utara', 'North Sulawesi'],
                ['West Sumatra', 'West Sumatra'],
                ['Yogyakarta', 'Yogyakarta'],
                ['Bangka Belitung', 'Bangka–Belitung Islands'],
                ['Gorontalo', 'Gorontalo'],
            ],
            default => [],
        };
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{
     *     id: string,
     *     first_name: string,
     *     last_name: string,
     *     company: string,
     *     phone: string,
     *     address1: string,
     *     address2: string,
     *     city: string,
     *     zip: string,
     *     country: string,
     *     province: string,
     *     default: bool
     * }
     */
    private static function normalize(array $item): array
    {
        return [
            'id' => (string) ($item['id'] ?? Str::uuid()),
            'first_name' => (string) ($item['first_name'] ?? ''),
            'last_name' => (string) ($item['last_name'] ?? ''),
            'company' => (string) ($item['company'] ?? ''),
            'phone' => (string) ($item['phone'] ?? ''),
            'address1' => (string) ($item['address1'] ?? ''),
            'address2' => (string) ($item['address2'] ?? ''),
            'city' => (string) ($item['city'] ?? ''),
            'zip' => (string) ($item['zip'] ?? ''),
            'country' => (string) ($item['country'] ?? 'Indonesia'),
            'province' => (string) ($item['province'] ?? ''),
            'default' => (bool) ($item['default'] ?? false),
        ];
    }
}
