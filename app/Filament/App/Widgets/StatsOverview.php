<?php

namespace App\Filament\App\Widgets;

use App\Models\User;
use App\Models\Content;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Anexos
            Stat::make('Total Attachments', User::class)
                ->label('Anexos')
                ->url('/anexos')
                ->description("Total de anexos cadastrados no sistema")
                ->descriptionIcon('heroicon-o-paper-clip', IconPosition::Before)
                ->chart(
                    array_map(
                        fn($x) => pow($x, 2),
                        range(1, 6)
                    )
                )
                ->value(fn() => Content::count())
                ->color('primary'),
        ];
    }
}
