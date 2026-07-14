<?php

namespace App\Support;

use App\Models\Account;
use App\Models\AccountAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $account = self::account();

        if ($account !== null) {
            return self::itemsForAccount($account);
        }

        return self::sessionItems();
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
        $account = self::account();

        if ($account !== null) {
            return self::storeForAccount($account, $data, $id);
        }

        return self::storeInSession($data, $id);
    }

    public static function remove(string $id): void
    {
        $account = self::account();

        if ($account !== null) {
            self::removeForAccount($account, $id);

            return;
        }

        self::removeFromSession($id);
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

    private static function account(): ?Account
    {
        $account = Auth::guard('account')->user();

        return $account instanceof Account ? $account : null;
    }

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
    private static function itemsForAccount(Account $account): array
    {
        $items = $account->addresses()
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->get()
            ->map(fn (AccountAddress $address): array => $address->toListItem())
            ->values()
            ->all();

        if ($items === []) {
            return [self::normalize([
                'id' => 'default-indonesia',
                'country' => 'Indonesia',
                'default' => true,
            ])];
        }

        return $items;
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
    private static function storeForAccount(Account $account, array $data, ?string $id): array
    {
        $isDefault = (bool) ($data['default'] ?? false);

        return DB::transaction(function () use ($account, $data, $id, $isDefault): array {
            $address = null;

            if (filled($id) && ctype_digit($id)) {
                $address = $account->addresses()->whereKey($id)->first();
            }

            $payload = [
                'first_name' => (string) ($data['first_name'] ?? ''),
                'last_name' => (string) ($data['last_name'] ?? ''),
                'company' => (string) ($data['company'] ?? ''),
                'phone' => (string) ($data['phone'] ?? ''),
                'address1' => (string) ($data['address1'] ?? ''),
                'address2' => (string) ($data['address2'] ?? ''),
                'city' => (string) ($data['city'] ?? ''),
                'zip' => (string) ($data['zip'] ?? ''),
                'country' => (string) ($data['country'] ?? 'Indonesia'),
                'province' => (string) ($data['province'] ?? ''),
                'is_default' => $isDefault,
            ];

            if ($address === null) {
                $payload['is_default'] = $isDefault || ! $account->addresses()->exists();
                $address = $account->addresses()->create($payload);
            } else {
                if (! $isDefault && $address->is_default && $account->addresses()->count() === 1) {
                    $payload['is_default'] = true;
                }
                $address->update($payload);
            }

            if ($address->is_default) {
                $account->addresses()
                    ->whereKeyNot($address->id)
                    ->update(['is_default' => false]);
            } elseif (! $account->addresses()->where('is_default', true)->exists()) {
                $address->update(['is_default' => true]);
            }

            return $address->refresh()->toListItem();
        });
    }

    private static function removeForAccount(Account $account, string $id): void
    {
        if (! ctype_digit($id)) {
            return;
        }

        DB::transaction(function () use ($account, $id): void {
            $address = $account->addresses()->whereKey($id)->first();

            if ($address === null) {
                return;
            }

            $wasDefault = $address->is_default;
            $address->delete();

            if ($wasDefault) {
                $account->addresses()
                    ->orderBy('id')
                    ->first()
                    ?->update(['is_default' => true]);
            }
        });
    }

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
    private static function sessionItems(): array
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
    private static function storeInSession(array $data, ?string $id = null): array
    {
        $items = self::sessionItems();
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

    private static function removeFromSession(string $id): void
    {
        $items = array_values(array_filter(
            self::sessionItems(),
            fn (array $item): bool => $item['id'] !== $id
        ));

        if ($items !== [] && ! collect($items)->contains(fn (array $item): bool => $item['default'])) {
            $items[0]['default'] = true;
        }

        Session::put(self::SESSION_KEY, $items);
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
