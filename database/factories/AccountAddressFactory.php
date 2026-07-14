<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\AccountAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccountAddress>
 */
class AccountAddressFactory extends Factory
{
    protected $model = AccountAddress::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'company' => fake()->optional()->company() ?? '',
            'phone' => fake()->numerify('08##########'),
            'address1' => fake()->streetAddress(),
            'address2' => fake()->optional()->secondaryAddress() ?? '',
            'city' => fake()->city(),
            'zip' => fake()->postcode(),
            'country' => 'Indonesia',
            'province' => fake()->randomElement(['Jawa Timur', 'Jawa Barat', 'Jakarta', 'Bali']),
            'is_default' => false,
        ];
    }

    public function default(): static
    {
        return $this->state(fn (): array => [
            'is_default' => true,
        ]);
    }
}
