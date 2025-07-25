<?php

namespace App\Filament\Resources;

use Log;
use Filament\Forms;
use Filament\Tables;
use App\Models\Content;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\ContentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ContentResource\RelationManagers;

class ContentResource extends Resource
{
    protected static ?string $model = Content::class;
    protected static ?string $label = 'Anexo';
    protected static ?string $pluralLabel = 'Anexos';
    protected static ?string $slug = 'anexos';
    protected static ?string $navigationGroup = 'Conteúdo';
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
                            ->square()
                            ->label('Image')
                            ->extraAttributes(['class' => 'flex flex-col items-center justify-center w-full']),

                        Tables\Columns\TextColumn::make('title')
                            ->searchable()
                            ->extraAttributes(['class' => 'flex flex-col align-center justify-center font-bold']),

                        Tables\Columns\TextColumn::make('file')
                            ->label('Extensão')
                            ->badge()
                            ->formatStateUsing(fn($state) => pathinfo($state, PATHINFO_EXTENSION))
                            ->searchable()
                            ->extraAttributes(['class' => 'flex flex-col align-center justify-center']),

                        Tables\Columns\TextColumn::make('file')
                            ->label('Tamanho')
                            ->badge()
                            ->color('warning')
                            ->formatStateUsing(function ($state) {
                                $path = storage_path('app/public/' . $state);
                                if (!file_exists($path)) {
                                    return 'N/A';
                                }
                                $size = filesize($path);
                                if ($size >= 1073741824) {
                                    return number_format($size / 1073741824, 2) . ' GB';
                                } elseif ($size >= 1048576) {
                                    return number_format($size / 1048576, 2) . ' MB';
                                } elseif ($size >= 1024) {
                                    return number_format($size / 1024, 2) . ' KB';
                                }
                                return $size . ' B';
                            })
                            ->extraAttributes(['class' => 'flex flex-col align-center justify-center']),
                    ])
                    ->columnSpan(1),
            ])
            ->contentGrid([
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
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->extraAttributes(['class' => 'mx-auto flex justify-center items-center']),
                Tables\Actions\Action::make('download')
                    ->label('')
                    ->icon('heroicon-o-folder-arrow-down')
                    ->action(
                        function (Content $record) {
                            $path = storage_path('app/public/' . $record->file);
                            if (!file_exists($path)) {
                                return response()->json(['error' => 'File not found'], 404);
                            }
                            return response()->download(storage_path('app/public/' . $record->file), $record->title . '.' . pathinfo($record->file, PATHINFO_EXTENSION));
                        }
                    )
                    ->extraAttributes(['class' => 'mx-auto flex justify-center items-center']),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('downloadSelected')
                        ->label('Download Selecionados')
                        ->icon('heroicon-o-folder-arrow-down')
                        ->action(
                            function (Collection $records) {
                                $zip = new \ZipArchive();
                                $zipFileName = 'anexos.zip';

                                if ($zip->open(storage_path('app/public/' . $zipFileName), \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
                                    foreach ($records as $record) {
                                        $filePath = Storage::disk('public')->path($record->file);

                                        if (Storage::disk('public')->exists($record->file)) {
                                            $extension = pathinfo($filePath, PATHINFO_EXTENSION);

                                            $safeTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $record->title);

                                            $zipEntryName = $safeTitle . ($extension ? ".{$extension}" : '');

                                            $zip->addFile($filePath, $zipEntryName);
                                        } else {
                                            Log::error("Arquivo não encontrado: {$filePath}");
                                        }
                                    }
                                    $zip->close();
                                }

                                return response()->download(storage_path('app/public/' . $zipFileName))->deleteFileAfterSend(true);
                            }
                        ),
                ]),
            ])
            ->recordUrl(null);
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
