<?php

namespace App\Filament\Resources\PackageResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Tables\Columns\ContentPreview;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ContentsRelationManager extends RelationManager
{
    protected static string $relationship = 'contents';
    protected static ?string $label = 'Conteúdo';
    protected static ?string $pluralLabel = 'Conteúdos';
    protected static ?string $title = 'Conteúdo';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('file')
                    ->label('Arquivo')
                    ->disk('public')
                    ->directory('contents')
                    ->visibility('public')
                    ->required()
                    ->acceptedFileTypes(['image/*', 'video/*', 'application/pdf'])
                    ->getUploadedFileNameForStorageUsing(
                        fn(TemporaryUploadedFile $file, $record): string =>
                        now()->format('d-m-Y_H-i-s') . '_pkg-' .
                            ($record?->package_id ?? 'pkg') . '_cntt-' .
                            ($record?->id ?? 'new') . '.' .
                            $file->getClientOriginalExtension()
                    )
                    ->downloadable()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('author_id')
                    ->label('Autor')
                    ->placeholder('Selecione um autor')
                    ->relationship('author', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('ownership_rights')
                    ->label('Direitos de Propriedade')
                    ->default('VAT Tecnologia da Informação Ltda.')
                    ->maxLength(255)
                    ->required(),

                Forms\Components\TextInput::make('source_credit')
                    ->label('Fonte/Crédito')
                    ->placeholder('Opcional, mas recomendado para dar crédito ao autor original.')
                    ->helperText('A fonte deve ser preenchida no formato ABNT. Exemplo: "SILVA, João. Título do Livro. Editora, Ano."')
                    ->maxLength(255)
                    ->required(),

                Forms\Components\TextInput::make('license_type')
                    ->label('Tipo de Licença')
                    ->default('Copyright (todos os direitos reservados)')
                    ->maxLength(255)
                    ->required(),

                Forms\Components\TagsInput::make('tags')
                    ->label('Tags')
                    ->separator(',')
                    ->splitKeys(['Tab', 'Enter', ' '])
                    ->placeholder('Adicione tags para categorizar o conteúdo')
                    ->helperText('Use a tecla TAB, ENTER ou ESPAÇO para adicionar tags.'),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'disponivel' => 'Disponível',
                        'pendente' => 'Pendente',
                        'em_fila' => 'Em Fila',
                        'falhou' => 'Falhou',
                        'processando' => 'Processando',
                        'temporariamente_indisponivel' => 'Temporariamente Indisponível',
                        'aguardando_revisao' => 'Aguardando Revisão',
                        'descarte' => 'Descarte',
                    ])
                    ->default('pendente')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ContentPreview::make('file')
                    ->label('Prévia'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('author.name')
                    ->label('Autor')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ownership_rights')
                    ->label('Direitos de Propriedade')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('source_credit')
                    ->label('Fonte/Crédito')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('license_type')
                    ->label('Tipo de Licença')
                    ->badge()
                    ->color('secondary')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tags')
                    ->label('Tags')
                    ->badge()
                    ->separator(', ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'disponivel' => 'success',
                        'pendente' => 'warning',
                        'em_fila' => 'info',
                        'falhou' => 'danger',
                        'processando' => 'primary',
                        'temporariamente_indisponivel' => 'secondary',
                        'aguardando_revisao' => 'warning',
                        'descarte' => 'dark',
                        default => null,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Adicionar Conteúdo')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->icon('heroicon-o-pencil')
                    ->color('primary'),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
