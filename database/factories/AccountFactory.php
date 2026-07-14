<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $email = fake()->unique()->safeEmail();

        return [
            'type' => AccountType::Customer,
            'name' => fake()->name(),
            'username' => Str::slug(Str::before($email, '@')).'-'.fake()->unique()->numerify('###'),
            'email' => $email,
            'loginBy' => 'email',
            'password' => static::$password ??= Hash::make('password'),
            'is_login' => true,
            'is_active' => true,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (): array => [
            'type' => AccountType::Admin,
        ]);
    }

    public function superAdmin(): static
    {
        return $this->state(fn (): array => [
            'type' => AccountType::SuperAdmin,
        ]);
    }
}
