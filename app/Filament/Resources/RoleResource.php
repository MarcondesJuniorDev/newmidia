<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $label = 'Regra';
    protected static ?string $pluralLabel = 'Regras';
    protected static ?string $slug = 'regras';
    protected static ?string $navigationGroup = 'Gerenciamento de Acesso';
    protected static ?string $navigationIcon = 'heroicon-s-lock-closed';
    protected static ?string $activeNavigationIcon = 'heroicon-o-lock-closed';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationBadgeTooltip = 'Quantidade de regras';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome da Regra')
                    ->placeholder('Digite o nome da regra')
                    ->minLength(3)
                    ->maxLength(255)
                    ->dehydrateStateUsing(
                        fn($state) =>
                        str_replace(
                            ' ',
                            '_',
                            preg_replace(
                                '/[áàâãäéèêëíìîïóòôõöúùûüçñ]/i',
                                '',
                                iconv(
                                    'UTF-8',
                                    'ASCII//TRANSLIT',
                                    $state
                                )
                            )
                        )
                    )
                    ->unique(ignoreRecord: true)
                    ->required(),

                Forms\Components\Select::make('permissions')
                    ->label('Permissões')
                    ->relationship(
                        'permissions',
                        'name',
                        fn(Builder $query) =>
                        auth()->user()->hasRole('admin') ? $query : $query->where('name', '!=', 'admin')
                    )
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Regras')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('permissions.name')
                    ->label('Permissões')
                    ->sortable()
                    ->searchable()
                    ->badge(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRoles::route('/'),
        ];
    }
}
