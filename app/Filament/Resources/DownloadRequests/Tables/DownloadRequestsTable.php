<?php

namespace App\Filament\Resources\DownloadRequests\Tables;

use App\Models\DownloadRequest;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DownloadRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('url')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->url),
                TextColumn::make('platform')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        DownloadRequest::STATUS_SUCCESS => 'success',
                        DownloadRequest::STATUS_NO_MEDIA => 'warning',
                        DownloadRequest::STATUS_ERROR => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('items_count')
                    ->numeric()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('site_host')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ip_address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        DownloadRequest::STATUS_SUCCESS => 'Success',
                        DownloadRequest::STATUS_NO_MEDIA => 'No media',
                        DownloadRequest::STATUS_ERROR => 'Error',
                    ]),
                SelectFilter::make('platform')
                    ->options(fn () => DownloadRequest::query()->distinct()->pluck('platform', 'platform')->filter()->toArray()),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
