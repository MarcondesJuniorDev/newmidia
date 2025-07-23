<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CourseCategory;
use Filament\Resources\Resource;
use App\Filament\Clusters\Educational;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CourseCategoryResource\Pages;
use App\Filament\Resources\CourseCategoryResource\RelationManagers;

class CourseCategoryResource extends Resource
{
    protected static ?string $model = CourseCategory::class;
    protected static ?string $label = 'Categoria';
    protected static ?string $pluralLabel = 'Categorias';
    protected static ?string $slug = 'categorias';
    protected static ?string $navigationIcon = 'heroicon-s-inbox-stack';
    protected static ?string $activeNavigationIcon = 'heroicon-o-inbox-stack';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationBadgeTooltip = 'Quantidade de categorias';
    protected static ?string $cluster = Educational::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->label('Categoria Pai')
                    ->helperText('ID da categoria pai, se houver')
                    ->placeholder('Selecione uma categoria pai')
                    ->options(fn() => CourseCategory::all()->pluck('title', 'id')->toArray())
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->placeholder('Título da categoria')
                    ->required(),
                Forms\Components\TextInput::make('code')
                    ->label('Código')
                    ->placeholder('Código da categoria')
                    ->unique(ignoreRecord: true)
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->placeholder('Descrição da categoria')
                    ->columnSpanFull(),
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

                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('parent.title')
                    ->label('Categoria Pai')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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
            'index' => Pages\ListCourseCategories::route('/'),
            'create' => Pages\CreateCourseCategory::route('/create'),
            'edit' => Pages\EditCourseCategory::route('/{record}/edit'),
        ];
    }
}
