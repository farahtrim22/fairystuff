<?php

namespace App\Providers\Filament;

use App\Filament\Resources\BucketResource;
use App\Filament\Resources\Bungas\BungaResource;
use App\Filament\Resources\Pesanans\PesananResource as PesanansResource;
use App\Filament\Resources\Pembayarans\PembayaranResource as PembayaransResource;
use App\Filament\Resources\RekomendasiStoks\RekomendasiStokResource as RekomendasiStoksResource;
use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
// use Filament\Pages\Dashboard; // using custom Dashboard page
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use App\Filament\Widgets\FairystuffStats;
use App\Filament\Widgets\MonthlySalesChart;
use App\Filament\Widgets\TopSellingFlowers;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->resources([
                BucketResource::class,
                BungaResource::class,
                PesanansResource::class,
                PembayaransResource::class,
                RekomendasiStoksResource::class,
            ])
            ->login()
            ->brandName('Fairystuff Dashboard')
            ->brandLogo(null)
            
            ->favicon(asset('images/favicon-fairystuff.svg'))

            ->font('Poppins')
            ->colors([
                'primary' => Color::hex('#f9A8D4'),
                'secondary' => Color::hex('#E9D5FF'),
                'gray' => Color::hex('#374151'),
                'success' => Color::hex('#bbf7d0'),
                'warning' => Color::hex('#FDE68A'),
                'danger' => Color::hex('#FCA5A5'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                FairystuffStats::class,
                MonthlySalesChart::class,
                TopSellingFlowers::class,
                AccountWidget::class,
                // FilamentInfoWidget::class, // removed to hide Filament info card
            ])
            // Inject brand di kiri atas topbar
            ->renderHook('panels::topbar.start', fn () => view('filament.custom.topbar-brand'))
            ->renderHook('panels::topbar.end', fn () => view('filament.custom.topbar-user'))
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
