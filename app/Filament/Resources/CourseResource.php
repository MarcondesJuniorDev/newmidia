<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;
    protected static ?string $label = 'Curso';
    protected static ?string $pluralLabel = 'Cursos';
    protected static ?string $slug = 'cursos';
    protected static ?string $navigationGroup = 'Gerenciamento de Conteúdo';
    protected static ?string $navigationIcon = 'heroicon-s-bookmark-square';
    protected static ?string $activeNavigationIcon = 'heroicon-o-bookmark-square';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationBadgeTooltip = 'Quantidade de Cursos';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Imagem')
                    ->image()
                    ->imageEditor()
                    ->imageEditorMode(2)
                    ->columnSpanFull(),

                Forms\Components\Select::make('project_id')
                    ->label('Projetos')
                    ->placeholder('Selecione um projeto')
                    ->required()
                    ->relationship('project', 'title'),

                Forms\Components\Select::make('category_id')
                    ->label('Categorias')
                    ->placeholder('Selecione uma categoria')
                    ->required()
                    ->relationship('category', 'title'),

                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->minLength(5)
                    ->maxLength(255)
                    ->placeholder('Digite o título do curso')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'rascunho' => 'Rascunho',
                        'publicado' => 'Publicado',
                        'pendente' => 'Pendente',
                        'arquivado' => 'Arquivado',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('summary')
                    ->label('Resumo')
                    ->maxLength(500)
                    ->placeholder('Digite um resumo do curso')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->placeholder('Digite a descrição do curso')
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_featured')
                    ->label('Destacado')
                    ->default(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem')
                    ->square()
                    ->size(50)
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'rascunho' => 'gray',
                        'publicado' => 'success',
                        'pendente' => 'warning',
                        default => 'info',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('project.title')
                    ->label('Projetos')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.title')
                    ->label('Categorias')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
