<?php

namespace App\Filament\App\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Content;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\App\Resources\ContentResource\Pages;
use App\Filament\App\Resources\ContentResource\RelationManagers;

class ContentResource extends Resource
{
    protected static ?string $model = Content::class;
    protected static ?string $label = 'Anexo';
    protected static ?string $pluralLabel = 'Anexos';
    protected static ?string $slug = 'anexos';
    protected static ?string $navigationIcon = 'heroicon-s-paper-clip';
    protected static ?string $activeNavigationIcon = 'heroicon-o-paper-clip';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationBadgeTooltip = 'Quantidade de anexos';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('file')
                    ->label('Arquivo')
                    ->columnSpanFull()
                    ->required(),

                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required(),
                    
                Forms\Components\Select::make('author_id')
                    ->label('Autor')
                    ->relationship('author', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('package_id')
                    ->label('Pacote')
                    ->relationship('package', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('ownership_rights')
                    ->label('Direitos de Propriedade')
                    ->required(),

                Forms\Components\TextInput::make('source_credit')
                    ->label('Crédito da Fonte')
                    ->nullable(),

                Forms\Components\TextInput::make('license_type')
                    ->label('Tipo de Licença')
                    ->nullable(),

                Forms\Components\TagsInput::make('tags')
                    ->label('Tags')
                    ->separator(', ')
                    ->helperText('Informe palavras-chave para facilitar a busca'),

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
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Grid::make()
                    ->columns(1)
                    ->schema([
                        ImageColumn::make('file')
                            ->disk('public')
                            ->width(100)
                            ->height(100)
                            ->circular()
                            ->label('Image'),
                        Tables\Columns\TextColumn::make('title')
                            ->searchable(),
                    ])
            ])->contentGrid([
                'md' => 4,
                'xl' => 6,
            ])
            ->paginationPageOptions([12, 24, 36])
            ->defaultSort('id', 'asc')
            ->filters([
                //filtra por autor
                Tables\Filters\SelectFilter::make('author_id')
                    ->label('Autor')
                    ->relationship('author', 'name')
                    ->placeholder('Selecione um ou mais autores')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                //filtro por status
                Tables\Filters\SelectFilter::make('status')
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
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContents::route('/'),
            'create' => Pages\CreateContent::route('/create'),
            'edit' => Pages\EditContent::route('/{record}/edit'),
        ];
    }
}
