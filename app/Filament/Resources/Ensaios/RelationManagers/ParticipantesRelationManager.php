<?php

namespace App\Filament\Resources\Ensaios\RelationManagers;

use App\Support\WhatsApp;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ParticipantesRelationManager extends RelationManager
{
    protected static string $relationship = 'participantes';

    protected static ?string $title = 'Convocados';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('Membro')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->required(),

            Select::make('status')
                ->label('Status')
                ->options([
                    'pendente'   => 'Pendente',
                    'confirmado' => 'Confirmado',
                    'recusado'   => 'Recusado',
                ])
                ->default('pendente')
                ->required(),

            TextInput::make('observacao')
                ->label('Observação')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Membro')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'confirmado' => 'success',
                        'recusado'   => 'danger',
                        default      => 'warning',
                    }),

                TextColumn::make('confirmado_em')
                    ->label('Confirmado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—'),

                TextColumn::make('observacao')
                    ->label('Observação')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Convocar membro'),
            ])
            ->recordActions([
                Action::make('link')
                    ->label('Link / QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->modalHeading('Link de confirmação do ensaio')
                    ->modalContent(fn ($record) => new HtmlString(
                        view('filament.ensaio-link-modal', [
                            'url' => route('ensaio.confirmacao', $record->token_confirmacao),
                        ])->render()
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fechar'),

                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(function ($record) {
                        $ensaio = $record->ensaio;
                        $quando = $ensaio?->data_hora?->format('d/m/Y \à\s H:i') ?? '';

                        return WhatsApp::link(
                            $record->user?->phone_whatsapp,
                            "Olá {$record->user?->name}! Convocação para ensaio em {$quando}"
                            . ($ensaio?->local ? " no {$ensaio->local}" : '')
                            . '. Confirme: ' . route('ensaio.confirmacao', $record->token_confirmacao)
                        );
                    })
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => filled($record->user?->phone_whatsapp)),

                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
