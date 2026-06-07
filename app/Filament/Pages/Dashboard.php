<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use BackedEnum;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    // Mengubah teks yang muncul di menu samping (Sidebar)
    protected static ?string $navigationLabel = "Dashboard";

    // Mengubah judul utama di dalam halaman (Header)
    protected static ?string $title = "Dashboard";

    // Mengubah icon menu (opsional, ganti jika ingin)
    protected static BackedEnum|string|null $navigationIcon = "heroicon-o-home";

    public function getHeading(): string
    {
        $username = Auth::user()->nama; // Mengambil nama user yang login

        return "Selamat datang, {$username} 👋";
    }

    public function getSubheading(): ?string
    {
        return "Berikut adalah ringkasan data inventaris saat ini.";
    }
}
