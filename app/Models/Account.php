<?php

namespace App\Models;

use App\Enums\AccountType;
use Database\Factories\AccountFactory;
use Filament\Facades\Filament;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property int|null $team_id
 * @property int|null $user_id
 * @property AccountType|string|null $type
 * @property string $name
 * @property string $username
 * @property string|null $email
 * @property string|null $password
 */
class Account extends Authenticatable implements HasAvatar, HasMedia
{
    /** @use HasFactory<AccountFactory> */
    use HasFactory;

    use InteractsWithMedia;
    use Notifiable;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'team_id',
        'user_id',
        'email',
        'phone',
        'parent_id',
        'type',
        'name',
        'username',
        'loginBy',
        'address',
        'password',
        'otp_code',
        'otp_activated_at',
        'last_login',
        'agent',
        'host',
        'is_login',
        'is_active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
        'otp_activated_at',
        'host',
        'agent',
    ];

    protected static function booted(): void
    {
        // TomatoPHP CreateAction uses Model::create() and skips Filament tenant association.
        static::creating(function (Account $account): void {
            if ($account->type === null) {
                $account->type = AccountType::Customer;
            }

            if ($account->team_id !== null) {
                return;
            }

            $tenant = Filament::getTenant();

            if ($tenant instanceof Team) {
                $account->team_id = $tenant->getKey();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => AccountType::class,
            'password' => 'hashed',
            'is_login' => 'boolean',
            'is_active' => 'boolean',
            'otp_activated_at' => 'datetime',
            'last_login' => 'datetime',
        ];
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('avatar') ?: null;
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isAdmin(): bool
    {
        return $this->type === AccountType::Admin || $this->type === AccountType::SuperAdmin;
    }

    public function isSuperAdmin(): bool
    {
        return $this->type === AccountType::SuperAdmin;
    }
}
