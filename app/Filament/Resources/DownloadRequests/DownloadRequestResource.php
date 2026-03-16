<?php

namespace App\Filament\Resources\DownloadRequests;

use App\Filament\Resources\DownloadRequests\Pages\ListDownloadRequests;
use App\Filament\Resources\DownloadRequests\Pages\ViewDownloadRequest;
use App\Filament\Resources\DownloadRequests\Schemas\DownloadRequestInfolist;
use App\Filament\Resources\DownloadRequests\Tables\DownloadRequestsTable;
use App\Models\DownloadRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DownloadRequestResource extends Resource
{
    protected static ?string $model = DownloadRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static ?string $navigationLabel = 'Download Requests';

    protected static ?string $modelLabel = 'Download Request';

    protected static ?string $pluralModelLabel = 'Download Requests';

    protected static ?string $recordTitleAttribute = 'url';

    public static function infolist(Schema $schema): Schema
    {
        return DownloadRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DownloadRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDownloadRequests::route('/'),
            'view' => ViewDownloadRequest::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
