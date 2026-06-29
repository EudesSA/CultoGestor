<?php

namespace App\Filament\Resources\Musicas\RelationManagers;

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

class ArquivosRelationManager extends RelationManager
{
    protected static string $relationship = 'arquivos';

    protected static ?string $title = 'Arquivos da Música';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipo')
                    ->label('Tipo de arquivo')
                    ->options([
                        'mp3'       => 'MP3 (áudio da música)',
                        'playback'  => 'Playback (instrumental)',
                        'letra'     => 'Letra (PDF/DOC)',
                        'cifra'     => 'Cifra (PDF/DOC)',
                        'partitura' => 'Partitura (PDF)',
                        'outro'     => 'Outro',
                    ])
                    ->required(),

                FileUpload::make('path_local')
                    ->label('Arquivo')
                    ->disk('cultos')
                    ->directory(function (callable $get) {
                        // Fonte única de árvore de pastas (mesma do Portal do Cantor).
                        $musica = $this->getOwnerRecord();
                        return $musica->diretorioArquivo($get('tipo') ?: 'mp3');
                    })
                    ->acceptedFileTypes([
                        'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg',
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    ->maxSize(102400)
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('nome_original')
                    ->label('Nome do arquivo')
                    ->maxLength(255)
                    ->helperText('Deixe em branco para usar o nome do arquivo enviado.'),
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
                        'mp3'       => 'success',
                        'playback'  => 'info',
                        'letra'     => 'warning',
                        'cifra'     => 'warning',
                        'partitura' => 'primary',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'mp3'       => 'MP3',
                        'playback'  => 'Playback',
                        'letra'     => 'Letra',
                        'cifra'     => 'Cifra',
                        'partitura' => 'Partitura',
                        default     => 'Outro',
                    }),

                TextColumn::make('nome_original')
                    ->label('Arquivo')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('tamanho_formatado')
                    ->label('Tamanho'),

                TextColumn::make('created_at')
                    ->label('Enviado em')
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
                    ->url(fn ($record) => route('arquivo.musica', [$record->id, 'inline']))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => (bool) $record->path_local),

                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn ($record) => route('arquivo.musica', [$record->id, 'download']))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => (bool) $record->path_local),

                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
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
