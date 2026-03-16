<?php

namespace App\Filament\Resources\PageVisits\Pages;

use App\Filament\Resources\PageVisits\PageVisitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPageVisits extends ListRecords
{
    protected static string $resource = PageVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
