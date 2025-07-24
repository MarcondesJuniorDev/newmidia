<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Package;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Count;
use App\Filament\Resources\PackageResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PackageResource\RelationManagers;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Filament\Resources\PackageResource\RelationManagers\PackageRelationManager;
use App\Filament\Resources\PackageResource\RelationManagers\ContentsRelationManager;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;
    protected static ?string $label = 'Pacote';
    protected static ?string $pluralLabel = 'Pacotes';
    protected static ?string $slug = 'pacotes';
    protected static ?string $navigationGroup = 'Conteúdo';
    protected static ?string $navigationIcon = 'heroicon-s-folder-open';
    protected static ?string $activeNavigationIcon = 'heroicon-o-folder-open';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationBadgeTooltip = 'Quantidade de pacotes';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    Section::make([
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'aula' => 'Aula',
                                'tipo2' => 'Tipo 2',
                                'tipo3' => 'Tipo 3',
                                'outro' => 'Outro',
                            ])
                            ->placeholder('Selecione o tipo')
                            ->required(),

                        Forms\Components\Select::make('project_id')
                            ->label('Projeto')
                            ->relationship('project', 'title')
                            ->placeholder('Selecione o projeto')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->placeholder('Digite o título')
                            ->required(),

                        Forms\Components\TextInput::make('code')
                            ->label('Código')
                            ->placeholder('Digite o código - ex: PKG-0001')
                            ->mask('AAA-9999')
                            ->unique(ignoreRecord: true)
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->placeholder('Digite a descrição do pacote')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('tags')
                            ->label('Tags')
                            ->placeholder('Insira palavras-chave separadas por vírgula para facilitar a busca')
                            ->columnSpanFull(),
                    ])->grow(),

                    Section::make([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'rascunho' => 'Rascunho',
                                'publicado' => 'Publicado',
                                'pendente' => 'Pendente',
                                'arquivado' => 'Arquivado',
                            ])
                            ->required(),
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagem')
                            ->disk('public')
                            ->directory('packages')
                            ->visibility('public')
                            ->placeholder('Selecione uma imagem')
                            ->image()
                            ->columnSpanFull()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string =>
                                now()->format('d-m-Y_H-i-s') . '_pkg-' . ($record?->id ?? 'new') . '.' . $file->getClientOriginalExtension()
                            )
                    ])->grow(false),
                ])->from('md'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem')
                    ->circular()
                    ->size(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state): string => match ($state) {
                        'rascunho' => 'gray',
                        'publicado' => 'success',
                        'pendente' => 'warning',
                        'arquivado' => 'danger',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn($state): string => match ($state) {
                        'aula' => 'primary',
                        'tipo2' => 'secondary',
                        'tipo3' => 'info',
                        'outro' => 'warning',
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('contents_count')
                    ->label('Anexos')
                    ->badge()
                    ->color('info')
                    ->counts('contents')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('project.title')
                    ->label('Projeto')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                //filtro de status
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'rascunho' => 'Rascunho',
                        'publicado' => 'Publicado',
                        'pendente' => 'Pendente',
                        'arquivado' => 'Arquivado',
                    ]),

                //filtro de tipo
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'aula' => 'Aula',
                        'tipo2' => 'Tipo 2',
                        'tipo3' => 'Tipo 3',
                        'outro' => 'Outro',
                    ]),

                //filtro de projeto
                Tables\Filters\SelectFilter::make('project_id')
                    ->label('Projeto')
                    ->relationship('project', 'title')
                    ->searchable()
                    ->placeholder('Todos os projetos')
                    ->default(null),

                //filtro de data
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Criado a partir de')
                            ->placeholder('Selecione uma data')
                            ->default(now()->subDays(30)->format('d-m-Y')),

                        DatePicker::make('created_until')
                            ->label('Criado até')
                            ->placeholder('Selecione uma data')
                            ->default(now()->format('d-m-Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip(fn($record) => 'Editar ' . ($record->title ?? ''))
                    ->icon('heroicon-o-pencil')
                    ->color('primary'),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->tooltip(fn($record) => 'Excluir ' . ($record->title ?? ''))
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
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
            ContentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}
