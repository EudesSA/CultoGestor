<?php

namespace App\Filament\Resources\Anuncios\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MidiasRelationManager extends RelationManager
{
    protected static string $relationship = 'midias';

    protected static ?string $title = 'Arquivos de Mídia';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'video'  => 'Vídeo (MP4, MOV)',
                        'imagem' => 'Imagem (JPG, PNG)',
                        'slide'  => 'Apresentação (PDF, PPT, PPTX)',
                        'outro'  => 'Outro',
                    ])
                    ->required()
                    ->live(),

                FileUpload::make('path_local')
                    ->label('Arquivo')
                    ->disk('cultos')
                    ->directory('Anuncios/' . now()->format('Y/m'))
                    ->acceptedFileTypes([
                        'video/mp4', 'video/quicktime', 'video/avi',
                        'image/jpeg', 'image/png', 'image/webp', 'image/gif',
                        'application/pdf',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    ])
                    ->maxSize(512000)
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('nome_original')
                    ->label('Nome do arquivo')
                    ->maxLength(255)
                    ->helperText('Deixe em branco para usar o nome do arquivo enviado.'),

                TextInput::make('ordem')
                    ->label('Ordem de exibição')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'video'  => 'danger',
                        'imagem' => 'success',
                        'slide'  => 'warning',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'video'  => 'Vídeo',
                        'imagem' => 'Imagem',
                        'slide'  => 'Slide/PDF',
                        default  => 'Outro',
                    }),

                TextColumn::make('nome_original')
                    ->label('Arquivo')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('tamanho_formatado')
                    ->label('Tamanho'),

                TextColumn::make('ordem')
                    ->label('Ordem')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Enviado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Adicionar arquivo'),
            ])
            ->actions([
                Action::make('visualizar')
                    ->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => route('arquivo.midia', [$record->id, 'inline']))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => (bool) $record->path_local),

                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn ($record) => route('arquivo.midia', [$record->id, 'download']))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => (bool) $record->path_local),

                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('ordem');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['nome_original']) && ! empty($data['path_local'])) {
            $data['nome_original'] = basename($data['path_local']);
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['nome_original']) && ! empty($data['path_local'])) {
            $data['nome_original'] = basename($data['path_local']);
        }
        return $data;
    }
}
