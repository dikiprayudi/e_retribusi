<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Penerimaan; // <-- Import model Anda
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        // Ambil tanggal hari ini dan bulan ini
        $today = Carbon::today();
        $month = Carbon::now();

        // 1. Hitung Pendapatan Hari Ini
        $pendapatanHariIni = Penerimaan::whereDate('tanggal', $today)->sum('total_tarif');

        // 2. Hitung Jumlah Pasien Hari Ini
        $pasienHariIni = Penerimaan::whereDate('tanggal', $today)->count();

        // 3. Hitung Pendapatan Bulan Ini
        $pendapatanBulanIni = Penerimaan::whereMonth('tanggal', $month->month)
                                        ->whereYear('tanggal', $month->year)
                                        ->sum('total_tarif');
        $pendapatanTahunan = Penerimaan::whereYear('tanggal', $month->year)
                                        ->sum('total_tarif');

        return [
            Stat::make('Total Pendapatan (Hari Ini)', 'Rp ' . number_format($pendapatanHariIni, 0, ',', '.'))
                ->description('Total penerimaan hari ini')
                ->descriptionIcon('heroicon-s-banknotes')
                ->color('success'),

            Stat::make('Total Pasien (Hari Ini)', $pasienHariIni)
                ->description('Jumlah pasien hari ini')
                ->descriptionIcon('heroicon-s-user-group')
                ->color('warning'),

            Stat::make('Total Pendapatan (Bulan Ini)', 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'))
                ->description('Total penerimaan bulan ini')
                ->descriptionIcon('heroicon-s-calendar')
                ->color('info'),
            Stat::make('Total Pendapatan SeTahun', 'Rp ' . number_format($pendapatanTahunan, 0, ',', '.'))
                ->description('Total penerimaan dalam setahun')
                ->descriptionIcon('heroicon-s-calendar')
                ->color('danger'),
        ];
    }
}