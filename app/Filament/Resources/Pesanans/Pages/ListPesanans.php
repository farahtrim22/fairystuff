<?php

namespace App\Filament\Resources\Pesanans\Pages;

use App\Filament\Resources\Pesanans\PesananResource;
use App\Models\Pesanan;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
 use Filament\Resources\Pages\ListRecords;

class ListPesanans extends ListRecords
{
    protected static string $resource = PesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('exportLaporanPenjualan')
                ->label('Export Laporan Penjualan')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->action(function () {
                    $data = Pesanan::query()->get(['nama_pelanggan','tanggal_pesan','total_harga','status_pesanan']);

                    if ($data->isEmpty()) {
                        Notification::make()
                            ->title('Tidak ada data untuk diexport')
                            ->warning()
                            ->send();
                        return;
                    }

                    $filename = 'laporan-penjualan-' . date('Ymd-His') . '.xlsx';
                    $path = Storage::disk('public')->path($filename);

                    $writer = new Writer();
                    $writer->openToFile($path);
                    $writer->addRow(Row::fromValues(['Nama Pelanggan','Tanggal Pesan','Total Harga','Status Pesanan']));

                    foreach ($data as $row) {
                        $writer->addRow(Row::fromValues([
                            $row->nama_pelanggan,
                            date('Y-m-d', strtotime($row->tanggal_pesan)),
                            (string) $row->total_harga,
                            ucfirst($row->status_pesanan),
                        ]));
                    }

                    $writer->close();

                    $this->js('window.open("' . asset('storage/' . $filename) . '", "_blank")');
                }),
            Action::make('cetakLaporanPenjualan')
                ->label('Cetak Laporan Penjualan')
                ->icon('heroicon-o-printer')
                ->color('secondary')
                ->action(function () {
                    $hasData = Pesanan::query()->exists();

                    if (! $hasData) {
                        Notification::make()
                            ->title('Tidak ada data untuk dicetak')
                            ->warning()
                            ->send();
                        return;
                    }

                    // Buka unduhan PDF di tab baru untuk menghindari ERR_ABORTED pada XHR
                    $this->js('window.open("' . route('penjualan.cetak.pdf') . '", "_blank")');
                }),
            Action::make('cetakLaporanPenjualanPeriode')
                ->label('Cetak Laporan Penjualan (Periode)')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->form([
                    DatePicker::make('tanggal_mulai')
                        ->label('Tanggal Mulai')
                        ->required()
                        ->native(false),
                    DatePicker::make('tanggal_akhir')
                        ->label('Tanggal Akhir')
                        ->required()
                        ->native(false)
                        ->after('tanggal_mulai'),
                ])
                ->action(function (array $data) {
                    if (empty($data['tanggal_mulai']) || empty($data['tanggal_akhir'])) {
                        Notification::make()
                            ->title('Silakan pilih periode tanggal terlebih dahulu.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Cek apakah ada data dalam periode tersebut
                    $hasData = Pesanan::whereBetween('tanggal_pesan', [$data['tanggal_mulai'], $data['tanggal_akhir']])
                        ->exists();

                    if (! $hasData) {
                        Notification::make()
                            ->title('Tidak ada data penjualan dalam periode tersebut')
                            ->warning()
                            ->send();
                        return;
                    }

                    // Format tanggal untuk nama file
                    $tanggalMulai = date('Y-m-d', strtotime($data['tanggal_mulai']));
                    $tanggalAkhir = date('Y-m-d', strtotime($data['tanggal_akhir']));

                    // Buka unduhan PDF di tab baru dengan parameter periode
                    $url = route('penjualan.cetak.pdf.periode', [
                        'tanggal_mulai' => $tanggalMulai,
                        'tanggal_akhir' => $tanggalAkhir
                    ]);
                    
                    $this->js('window.open("' . $url . '", "_blank")');
                }),
        ];
    }
}