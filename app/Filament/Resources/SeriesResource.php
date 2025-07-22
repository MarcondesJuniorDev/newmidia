<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeriesResource\Pages;
use App\Filament\Resources\SeriesResource\RelationManagers;
use App\Models\Series;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SeriesResource extends Resource
{
    protected static ?string $model = Series::class;
    protected static ?string $label = 'Série';
    protected static ?string $pluralLabel = 'Séries';
    protected static ?string $slug = 'series';
    protected static ?string $navigationGroup = 'Gerenciamento de Conteúdo';
    protected static ?string $navigationIcon = 'heroicon-s-academic-cap';
    protected static ?string $activeNavigationIcon = 'heroicon-o-academic-cap';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationBadgeTooltip = 'Quantidade de Séries';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('course_id')
                    ->label('Curso')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->relationship('course', 'title'),

                Forms\Components\Select::make('subjects')
                    ->label('Componentes')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->relationship('subjects', 'title'),

                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->columnSpanFull(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'publicado' => 'Publicado',
                        'rascunho' => 'Rascunho',
                        'arquivado' => 'Arquivado',
                        'pendente' => 'Pendente',
                    ])
                    ->default('rascunho'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('course.title')
                    ->label('Curso')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('subjects.title')
                    ->label('Componentes')
                    ->badge()
                    ->color('primary')
                    ->limitList(3)
                    ->separator(', ')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->icon(fn(string $state): string => match ($state) {
                        'rascunho' => 'heroicon-s-pencil',
                        'pendente' => 'heroicon-s-clock',
                        'publicado' => 'heroicon-s-check-circle',
                        'arquivado' => 'heroicon-s-archive-box',
                        default => 'heroicon-s-question-mark-circle',
                    })->color(fn(string $state): string => match ($state) {
                        'rascunho' => 'warning',
                        'pendente' => 'info',
                        'publicado' => 'success',
                        'arquivado' => 'secondary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSeries::route('/'),
            'create' => Pages\CreateSeries::route('/create'),
            'edit' => Pages\EditSeries::route('/{record}/edit'),
        ];
    }
}
