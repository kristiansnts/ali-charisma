<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\PermissionRegistrar;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 2;

    protected static bool $isScopedToTenant = false;

    public static function getNavigationGroup(): ?string
    {
        return __('Users');
    }

    public static function getNavigationLabel(): string
    {
        return __('Admin Users');
    }

    public static function getModelLabel(): string
    {
        return __('Admin User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Admin Users');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->rule(Password::defaults())
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->helperText(fn (string $operation): ?string => $operation === 'edit'
                        ? __('Leave blank to keep the current password.')
                        : null),
                Forms\Components\Select::make('roles')
                    ->label(__('Roles'))
                    ->multiple()
                    ->preload()
                    ->options(fn (): array => static::roleOptions())
                    ->afterStateHydrated(function (Forms\Components\Select $component, ?User $record): void {
                        if (! $record) {
                            return;
                        }

                        $tenant = Filament::getTenant();

                        if (! $tenant) {
                            return;
                        }

                        setPermissionsTeamId($tenant->getKey());

                        $component->state(
                            $record->roles()
                                ->where(
                                    $record->roles()->getModel()->getTable().'.'.config('permission.column_names.team_foreign_key'),
                                    $tenant->getKey(),
                                )
                                ->pluck('roles.id')
                                ->all()
                        );
                    })
                    ->dehydrated()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('Roles'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str($state)->headline()->toString()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(fn (array $data): array => static::withoutPasswordIfBlank($data))
                    ->using(fn (User $record, array $data): User => static::saveUser($record, $data)),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (User $record): bool => $record->is(auth()->user())),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records): void {
                            $records
                                ->reject(fn (User $record): bool => $record->is(auth()->user()))
                                ->each->delete();
                        }),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $tenant = Filament::getTenant();

        return parent::getEloquentQuery()
            ->when(
                $tenant,
                fn (Builder $query): Builder => $query->whereHas(
                    'teams',
                    fn (Builder $teams): Builder => $teams->whereKey($tenant->getKey()),
                ),
            )
            ->with(['roles' => function ($query) use ($tenant): void {
                if ($tenant) {
                    $query->where(
                        $query->getModel()->getTable().'.'.config('permission.column_names.team_foreign_key'),
                        $tenant->getKey(),
                    );
                }
            }]);
    }

    /**
     * @return array<int|string, string>
     */
    public static function roleOptions(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return [];
        }

        return Role::query()
            ->where(config('permission.column_names.team_foreign_key'), $tenant->getKey())
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Role $role): array => [
                $role->id => str($role->name)->headline()->toString(),
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function saveUser(User $user, array $data): User
    {
        $roleIds = $data['roles'] ?? [];
        unset($data['roles']);

        $user->fill($data);
        $user->save();

        $tenant = Filament::getTenant();

        if ($tenant) {
            $user->teams()->syncWithoutDetaching([$tenant->getKey()]);

            setPermissionsTeamId($tenant->getKey());
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            $roles = Role::query()
                ->where(config('permission.column_names.team_foreign_key'), $tenant->getKey())
                ->whereIn('id', $roleIds)
                ->get();

            $user->syncRoles($roles);
        }

        return $user;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function withoutPasswordIfBlank(array $data): array
    {
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
