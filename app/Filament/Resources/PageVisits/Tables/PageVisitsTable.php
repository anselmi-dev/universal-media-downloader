<?php

namespace App\Filament\Resources\PageVisits\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PageVisitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('path')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('site_host')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('locale')
                    ->badge()
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('referer')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('site_host')
                    ->options(fn () => \App\Models\PageVisit::query()->distinct()->pluck('site_host', 'site_host')->filter()->toArray()),
                SelectFilter::make('path')
                    ->options(fn () => \App\Models\PageVisit::query()->distinct()->pluck('path', 'path')->filter()->toArray()),
            ]);
    }
}
