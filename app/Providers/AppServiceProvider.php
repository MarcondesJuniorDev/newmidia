<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn(): string => <<<'HTML'
                <div class="flex justify-center gap-1 text-sm">
                    <span>Precisa de ajuda?</span>
                    <a href="/admin/contato" class="text-primary-500 hover:underline">Entre em contato conosco</a>
                </div>
            HTML
        );
    }
}
