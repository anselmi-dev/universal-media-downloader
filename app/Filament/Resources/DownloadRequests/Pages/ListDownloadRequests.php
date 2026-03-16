<?php

namespace App\Filament\Resources\DownloadRequests\Pages;

use App\Filament\Resources\DownloadRequests\DownloadRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDownloadRequests extends ListRecords
{
    protected static string $resource = DownloadRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
