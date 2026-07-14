<?php

namespace App\Support;

use App\Models\Account;

class CheckoutCustomerData
{
    /**
     * @return array{
     *     email: string,
     *     first_name: string,
     *     last_name: string,
     *     country: string,
     *     address: string,
     *     apartment: string,
     *     city: string,
     *     province: string,
     *     postal: string,
     *     phone: string
     * }
     */
    public static function for(?Account $account): array
    {
        if ($account === null) {
            return self::empty();
        }

        $address = CustomerAddressList::default() ?? [];
        [$firstName, $lastName] = self::resolveName($address, $account->name);

        return [
            'email' => (string) ($account->email ?? ''),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'country' => self::countryCode((string) ($address['country'] ?? 'Indonesia')),
            'address' => (string) ($address['address1'] ?? ''),
            'apartment' => (string) ($address['address2'] ?? ''),
            'city' => (string) ($address['city'] ?? ''),
            'province' => (string) ($address['province'] ?? ''),
            'postal' => (string) ($address['zip'] ?? ''),
            'phone' => (string) (($address['phone'] ?? '') ?: ($account->phone ?? '')),
        ];
    }

    /**
     * @return array{
     *     email: string,
     *     first_name: string,
     *     last_name: string,
     *     country: string,
     *     address: string,
     *     apartment: string,
     *     city: string,
     *     province: string,
     *     postal: string,
     *     phone: string
     * }
     */
    private static function empty(): array
    {
        return [
            'email' => '',
            'first_name' => '',
            'last_name' => '',
            'country' => 'ID',
            'address' => '',
            'apartment' => '',
            'city' => '',
            'province' => '',
            'postal' => '',
            'phone' => '',
        ];
    }

    /**
     * @param  array<string, mixed>  $address
     * @return array{0: string, 1: string}
     */
    private static function resolveName(array $address, ?string $accountName): array
    {
        $firstName = trim((string) ($address['first_name'] ?? ''));
        $lastName = trim((string) ($address['last_name'] ?? ''));

        if ($firstName !== '' || $lastName !== '') {
            return [$firstName, $lastName];
        }

        $accountName = trim((string) $accountName);

        if ($accountName === '') {
            return ['', ''];
        }

        $parts = preg_split('/\s+/', $accountName, 2) ?: [];

        return [
            (string) ($parts[0] ?? ''),
            (string) ($parts[1] ?? ''),
        ];
    }

    private static function countryCode(string $country): string
    {
        return match (strtolower(trim($country))) {
            'indonesia', 'id' => 'ID',
            'singapore', 'sg' => 'SG',
            'malaysia', 'my' => 'MY',
            'united states', 'us', 'usa' => 'US',
            'australia', 'au' => 'AU',
            default => 'ID',
        };
    }
}
