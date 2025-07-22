<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;
    protected static ?string $label = 'Permissão';
    protected static ?string $pluralLabel = 'Permissões';
    protected static ?string $slug = 'permissoes';
    protected static ?string $navigationGroup = 'Gerenciamento de Acesso';
    protected static ?string $navigationIcon = 'heroicon-s-key';
    protected static ?string $activeNavigationIcon = 'heroicon-o-key';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationBadgeTooltip = 'Quantidade de permissões';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome da Permissão')
                    ->required()
                    ->dehydrateStateUsing(
                        fn($state) =>
                        strtolower(
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
                    ),
                Forms\Components\Select::make('roles')
                    ->label('Regras')
                    ->placeholder('Selecione as regras desta permissão')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Permissões')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Regras')
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
            'index' => Pages\ManagePermissions::route('/'),
        ];
    }
}
