<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $label = 'Usuário';
    protected static ?string $pluralLabel = 'Usuários';
    protected static ?string $slug = 'usuarios';
    protected static ?string $navigationGroup = 'Gerenciamento de Acesso';
    protected static ?string $navigationIcon = 'heroicon-s-user-group';
    protected static ?string $activeNavigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->placeholder('Digite o nome do usuário completo')
                    ->minLength(3)
                    ->maxLength(255)
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->placeholder('Digite o e-mail do usuário')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),

                Forms\Components\TextInput::make('password')
                    ->label('Senha')
                    ->placeholder('Digite uma senha de pelo menos 8 caracteres')
                    ->minLength(8)
                    ->maxLength(255)
                    ->password()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create')
                    ->columns(1),

                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Confirmação de Senha')
                    ->placeholder('Confirme a senha digitada')
                    ->password()
                    ->minLength(8)
                    ->maxLength(255)
                    ->same('password')
                    ->required(fn(string $context): bool => $context === 'create')
                    ->columns(1),

                Forms\Components\Select::make('roles')
                    ->label('Regras')
                    ->placeholder('Selecione as regras para o usuário')
                    ->relationship(
                        'roles',
                        'name',
                        fn(Builder $query) =>
                        auth()->user()->hasRole('admin') ? $query : $query->where('name', '!=', 'admin')
                    )
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->columns(1),
            ])->columns([
                'sm' => 1,
                'md' => 2,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Usuários')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Regras')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return auth()->user()->hasRole('admin')
            ? parent::getEloquentQuery()
            : parent::getEloquentQuery()->whereHas('roles', fn($query) => $query->where('name', '!=', 'admin'));
    }
}
