<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Subject;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Clusters\Educational;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SubjectResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubjectResource\RelationManagers;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;
    protected static ?string $label = 'Componente Curricular';
    protected static ?string $pluralLabel = 'Componentes Curriculares';
    protected static ?string $slug = 'componentes-curriculares';
    protected static ?string $navigationIcon = 'heroicon-s-puzzle-piece';
    protected static ?string $activeNavigationIcon = 'heroicon-o-puzzle-piece';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationBadgeTooltip = 'Quantidade de Componentes Curriculares';
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
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->placeholder('Digite o título do componente curricular')
                    ->minLength(3)
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('code')
                    ->label('Código')
                    ->placeholder('Digite o código do componente curricular')
                    ->minLength(2)
                    ->maxLength(100)
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->placeholder('Digite a descrição do componente curricular')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->placeholder('Selecione o status do componente curricular')
                    ->options([
                        'pendente' => 'Pendente',
                        'publicado' => 'Publicado',
                        'cancelado' => 'Cancelado',
                        'removido' => 'Removido',
                    ])
                    ->required(),
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
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pendente' => 'warning',
                        'publicado' => 'success',
                        'cancelado' => 'danger',
                        'removido' => 'gray',
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pendente' => 'Pendente',
                        'publicado' => 'Publicado',
                        'cancelado' => 'Cancelado',
                        'removido' => 'Removido',
                    ]),
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
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
