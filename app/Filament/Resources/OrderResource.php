<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use Filament\Tables\Table;
use TomatoPHP\FilamentEcommerce\Filament\Resources\OrderResource as BaseOrderResource;
use TomatoPHP\FilamentEcommerce\FilamentEcommercePlugin;

class OrderResource extends BaseOrderResource
{
    public static function table(Table $table): Table
    {
        $built = parent::table($table);

        $headerActions = collect($built->getHeaderActions())
            ->reject(function ($action): bool {
                return ($action->getName() === 'import' && ! FilamentEcommercePlugin::$allowOrderImport)
                    || ($action->getName() === 'export' && ! FilamentEcommercePlugin::$allowOrderExport);
            })
            ->values()
            ->all();

        return $built->headerActions($headerActions);
    }

    public static function getPages(): array
    {
        $pages = parent::getPages();

        $pages['index'] = Pages\ListOrders::route('/');

        return $pages;
    }
}
