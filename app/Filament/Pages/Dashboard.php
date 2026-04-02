<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use BackedEnum;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Selamat Datang di Fairystuff Admin Panel';
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
}