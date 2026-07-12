<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingVendorResource\Pages;
use App\Models\ShippingVendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShippingVendorResource extends Resource
{
    protected static ?string $model = ShippingVendor::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return trans('filament-ecommerce::messages.group');
    }

    public static function getNavigationLabel(): string
    {
        return trans('filament-ecommerce::messages.shipping.title');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('filament-ecommerce::messages.shipping.title');
    }

    public static function getLabel(): ?string
    {
        return trans('filament-ecommerce::messages.shipping.single');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('is_activated')
                    ->label(trans('filament-ecommerce::messages.shipping.columns.is_activated')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(trans('filament-ecommerce::messages.shipping.columns.name'))
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_activated')
                    ->label(trans('filament-ecommerce::messages.shipping.columns.is_activated')),
            ])
            ->actions([])
            ->bulkActions([])
            ->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageShippingVendors::route('/'),
        ];
    }
}
