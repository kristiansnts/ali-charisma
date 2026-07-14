<?php

namespace App\Models;

use Database\Factories\AccountAddressFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $account_id
 * @property string $first_name
 * @property string $last_name
 * @property string $company
 * @property string $phone
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property string $zip
 * @property string $country
 * @property string $province
 * @property bool $is_default
 */
class AccountAddress extends Model
{
    /** @use HasFactory<AccountAddressFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'first_name',
        'last_name',
        'company',
        'phone',
        'address1',
        'address2',
        'city',
        'zip',
        'country',
        'province',
        'is_default',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'first_name' => '',
        'last_name' => '',
        'company' => '',
        'phone' => '',
        'address1' => '',
        'address2' => '',
        'city' => '',
        'zip' => '',
        'country' => 'Indonesia',
        'province' => '',
        'is_default' => false,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
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
     * }
     */
    public function toListItem(): array
    {
        return [
            'id' => (string) $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company' => $this->company,
            'phone' => $this->phone,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'city' => $this->city,
            'zip' => $this->zip,
            'country' => $this->country,
            'province' => $this->province,
            'default' => $this->is_default,
        ];
    }
}
