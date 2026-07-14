@php
    $parts = array_filter([
        trim(($address['first_name'] ?? '').' '.($address['last_name'] ?? '')),
        $address['company'] ?? null,
        $address['address1'] ?? null,
        $address['address2'] ?? null,
        trim(($address['city'] ?? '').(($address['zip'] ?? '') !== '' ? ' '.$address['zip'] : '')),
        $address['province'] ?? null,
        $address['country'] ?? null,
        $address['phone'] ?? null,
    ], fn ($value) => filled($value));
@endphp

@forelse ($parts as $line)
    <p>{{ $line }}</p>
@empty
    <p>Indonesia</p>
@endforelse
