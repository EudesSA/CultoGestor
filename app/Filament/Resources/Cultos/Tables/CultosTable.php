<?php

namespace App\Filament\Resources\Cultos\Tables;

use App\Jobs\SincronizarGoogleCalendarJob;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CultosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('data')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('tipo.nome')
                    ->label('Tipo')
                    ->badge()
                    ->sortable(),

                TextColumn::make('hora_inicio')
                    ->label('Início')
                    ->time('H:i'),

                TextColumn::make('tema')
                    ->label('Tema')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'agendado'  => 'info',
                        'realizado' => 'success',
                        'cancelado' => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('escalas_count')
                    ->label('Escalados')
                    ->counts('escalas')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('data', 'desc')
            ->filters([
                SelectFilter::make('culto_tipo_id')
                    ->label('Tipo')
                    ->relationship('tipo', 'nome'),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'agendado'  => 'Agendado',
                        'realizado' => 'Realizado',
                        'cancelado' => 'Cancelado',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('sincronizar_calendar')
                    ->label('Sync Calendar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->tooltip('Sincronizar com Google Calendar')
                    ->action(fn ($record) => SincronizarGoogleCalendarJob::dispatch($record->id, 'upsert')->onQueue('default'))
                    ->successNotificationTitle('Sincronização agendada!'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
