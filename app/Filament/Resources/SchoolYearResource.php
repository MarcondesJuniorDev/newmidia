<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Educational;
use App\Filament\Resources\SchoolYearResource\Pages;
use App\Filament\Resources\SchoolYearResource\RelationManagers;
use App\Models\SchoolYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SchoolYearResource extends Resource
{
    protected static ?string $model = SchoolYear::class;
    protected static ?string $label = 'Ano Letivo';
    protected static ?string $pluralLabel = 'Anos Letivos';
    protected static ?string $slug = 'anos-letivos';
    protected static ?string $navigationIcon = 'heroicon-s-calendar';
    protected static ?string $activeNavigationIcon = 'heroicon-o-calendar';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationBadgeTooltip = 'Quantidade de Anos Letivos';
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
                Forms\Components\Select::make('year')
                    ->label('Ano Letivo')
                    ->helperText('Selecione o ano letivo. Anos já utilizados não estarão disponíveis.')
                    ->searchable()
                    ->placeholder('Selecione o ano')
                    ->options(
                        collect(range(2000, date('Y')))
                            ->diff(
                                SchoolYear::pluck('year')->toArray()
                            )
                            ->mapWithKeys(fn($year) => [$year => (string)$year])
                            ->toArray()
                    )
                    ->required(),
                Forms\Components\Toggle::make('current')
                    ->label('Ano Atual')
                    ->helperText('Marque se este for o ano letivo atual.')
                    ->default(false)
                    ->reactive()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->label('Ano Letivo')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('current')
                    ->label('Ano Atual')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            'index' => Pages\ManageSchoolYears::route('/'),
        ];
    }
}
