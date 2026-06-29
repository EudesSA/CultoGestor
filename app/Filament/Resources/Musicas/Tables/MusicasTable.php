<?php

namespace App\Filament\Resources\Musicas\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MusicasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('culto.data')
                    ->label('Culto')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('cantor.nome')
                    ->label('Cantor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nome')
                    ->label('Música')
                    ->searchable()
                    ->description(fn ($record) => $record->artista),

                TextColumn::make('tom')
                    ->label('Tom')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendente'  => 'warning',
                        'enviado'   => 'info',
                        'revisado'  => 'primary',
                        'aprovado'  => 'success',
                        default     => 'gray',
                    }),

                TextColumn::make('arquivos_count')
                    ->label('Arquivos')
                    ->counts('arquivos')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('enviado_em')
                    ->label('Enviado em')
                    ->dateTime('d/m H:i')
                    ->sortable(),
            ])
            ->defaultSort('culto.data', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pendente'  => 'Pendente',
                        'enviado'   => 'Enviado',
                        'revisado'  => 'Revisado',
                        'aprovado'  => 'Aprovado',
                    ]),
            ])
            ->recordActions([
                Action::make('portal')
                    ->label('QR / Link')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->modalHeading('Portal do Cantor')
                    ->modalContent(fn ($record) => view('filament.musica-portal-modal', [
                        'url' => route('cantor.portal', $record->token_acesso),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fechar'),

                Action::make('aprovar')
                    ->label('Aprovar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status !== 'aprovado')
                    ->action(fn ($record) => $record->update([
                        'status'      => 'aprovado',
                        'aprovado_em' => now(),
                    ])),
                ...\App\Filament\Support\ApresentarActions::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
