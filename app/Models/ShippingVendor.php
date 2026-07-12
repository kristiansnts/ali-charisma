<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use TomatoPHP\FilamentEcommerce\Models\ShippingVendor as TomatoShippingVendor;

class ShippingVendor extends TomatoShippingVendor
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'team_id',
        'code',
        'carrier_id',
        'service_codes',
        'price',
        'name',
        'delivery_estimation',
        'contact_person',
        'phone',
        'address',
        'is_activated',
        'integration',
        'created_at',
        'updated_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_activated' => 'boolean',
            'service_codes' => 'array',
            'integration' => 'array',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
