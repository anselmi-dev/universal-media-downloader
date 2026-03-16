<?php

namespace App\Filament\Resources\PageVisits;

use App\Filament\Resources\PageVisits\Pages\ListPageVisits;
use App\Filament\Resources\PageVisits\Tables\PageVisitsTable;
use App\Models\PageVisit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PageVisitResource extends Resource
{
    protected static ?string $model = PageVisit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEye;

    protected static ?string $navigationLabel = 'Page Visits';

    protected static ?string $modelLabel = 'Page Visit';

    protected static ?string $pluralModelLabel = 'Page Visits';

    protected static ?string $recordTitleAttribute = 'path';

    public static function table(Table $table): Table
    {
        return PageVisitsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPageVisits::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
