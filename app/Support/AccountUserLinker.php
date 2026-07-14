<?php

namespace App\Support;

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountUserLinker
{
    public function syncAllUsers(): int
    {
        $synced = 0;

        User::query()->orderBy('id')->each(function (User $user) use (&$synced): void {
            $this->syncUser($user);
            $synced++;
        });

        return $synced;
    }

    public function syncUser(User $user): Account
    {
        $type = $this->resolveTypeForUser($user);
        $teamId = $user->teams()->orderBy('teams.id')->value('teams.id');

        $account = Account::query()
            ->where(function ($query) use ($user): void {
                $query->where('user_id', $user->id)
                    ->orWhere(function ($query) use ($user): void {
                        $query->whereNull('user_id')->where('email', $user->email);
                    });
            })
            ->first();

        $passwordHash = $user->getAttributes()['password'] ?? $user->password;

        $payload = [
            'user_id' => $user->id,
            'team_id' => $teamId,
            'type' => $type,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $this->uniqueUsername($user, $account?->id),
            'loginBy' => 'email',
            'password' => $passwordHash,
            'is_login' => true,
            'is_active' => true,
        ];

        if ($account === null) {
            return Account::query()->create($payload);
        }

        $account->forceFill(collect($payload)->except('password')->all());
        $account->password = $passwordHash;
        $account->save();

        return $account->refresh();
    }

    public function registerCustomer(string $name, string $email, string $password, ?Team $team = null): Account
    {
        $team ??= Team::query()->where('slug', 'ali-charisma')->first()
            ?? Team::query()->orderBy('id')->first();

        return Account::query()->create([
            'team_id' => $team?->id,
            'user_id' => null,
            'type' => AccountType::Customer,
            'name' => $name,
            'email' => $email,
            'username' => $this->uniqueUsernameFromEmail($email),
            'loginBy' => 'email',
            'password' => $password,
            'is_login' => true,
            'is_active' => true,
        ]);
    }

    public function resolveTypeForUser(User $user): AccountType
    {
        $roleNames = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', $user->getMorphClass())
            ->where('model_has_roles.model_id', $user->id)
            ->pluck('roles.name')
            ->unique()
            ->values()
            ->all();

        $isSuperAdmin = in_array(config('filament-shield.super_admin.name', 'super_admin'), $roleNames, true);
        $isAdmin = in_array('admin', $roleNames, true);

        return AccountType::fromUserRoles($isSuperAdmin, $isAdmin);
    }

    private function uniqueUsername(User $user, ?int $ignoreAccountId = null): string
    {
        $base = Str::slug($user->name) ?: Str::before($user->email, '@');

        return $this->uniqueUsernameFromBase($base !== '' ? $base : 'user-'.$user->id, $ignoreAccountId);
    }

    private function uniqueUsernameFromEmail(string $email): string
    {
        $base = Str::slug(Str::before($email, '@')) ?: 'customer';

        return $this->uniqueUsernameFromBase($base);
    }

    private function uniqueUsernameFromBase(string $base, ?int $ignoreAccountId = null): string
    {
        $username = $base;
        $i = 1;

        while (
            Account::withTrashed()
                ->when($ignoreAccountId, fn ($q) => $q->where('id', '!=', $ignoreAccountId))
                ->where('username', $username)
                ->exists()
        ) {
            $username = $base.'-'.$i;
            $i++;
        }

        return $username;
    }
}
