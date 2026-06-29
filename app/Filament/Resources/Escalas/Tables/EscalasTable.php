<?php

namespace App\Filament\Resources\Escalas\Tables;

use App\Support\WhatsApp;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EscalasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('culto.data')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('culto.tipo.nome')
                    ->label('Culto'),

                TextColumn::make('funcao.nome')
                    ->label('Função')
                    ->badge(),

                TextColumn::make('user.name')
                    ->label('Membro')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmado' => 'success',
                        'recusado'   => 'danger',
                        default      => 'warning',
                    }),

                TextColumn::make('confirmado_em')
                    ->label('Confirmado em')
                    ->dateTime('d/m H:i')
                    ->sortable(),
            ])
            ->defaultSort('culto.data', 'asc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pendente'   => 'Pendente',
                        'confirmado' => 'Confirmado',
                        'recusado'   => 'Recusado',
                    ]),
            ])
            ->recordActions([
                Action::make('link_confirmacao')
                    ->label('Link')
                    ->icon('heroicon-o-link')
                    ->color('gray')
                    ->modalHeading('Link de Confirmação')
                    ->modalContent(fn ($record) => view('filament.escala-link-modal', [
                        'url' => route('escala.confirmacao', $record->token_confirmacao),
                        'nome' => $record->user?->name,
                        'funcao' => $record->funcao?->nome,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fechar'),

                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn ($record) => WhatsApp::link(
                        $record->user?->phone_whatsapp,
                        "Olá {$record->user?->name}! Você foi escalado como "
                        . ($record->funcao?->nome ?? 'voluntário')
                        . ' no culto de ' . ($record->culto?->data?->format('d/m/Y') ?? '')
                        . '. Confirme sua presença: '
                        . route('escala.confirmacao', $record->token_confirmacao)
                    ))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => filled($record->user?->phone_whatsapp)),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
