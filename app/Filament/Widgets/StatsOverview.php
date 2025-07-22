<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Usuarios
            Stat::make('Total Users', User::class)
                ->label('Usuários')
                ->url('/admin/usuarios')
                ->description("Total de usuários cadastrados no sistema")
                ->descriptionIcon('heroicon-o-users', IconPosition::Before)
                ->chart([1, 3, 5, 10, 20, 40])
                ->value(fn() => User::count())
                ->color('success'),
        ];
    }
}
