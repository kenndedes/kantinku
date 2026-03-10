<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(protected Collection $orders) {}

    public function collection(): Collection
    {
        return $this->orders->map(fn($o) => [
            $o->order_number,
            $o->user?->name ?? '-',
            $o->stand?->name ?? '-',
            $o->created_at->format('d/m/Y'),
            $o->pickup_time === '09:30' ? 'Istirahat 1 (09:30)' : 'Istirahat 2 (12:00)',
            $this->statusLabel($o->order_status),
            (float) $o->total_price,
        ]);
    }

    public function headings(): array
    {
        return ['No. Pesanan', 'Pembeli', 'Stand', 'Tanggal', 'Jam Pickup', 'Status', 'Total (Rp)'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true, 'size' => 11]]];
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'menunggu'     => 'Menunggu',
            'diproses'     => 'Diproses',
            'siap_diambil' => 'Siap Diambil',
            'selesai'      => 'Selesai',
            'batal'        => 'Batal',
            default        => $status,
        };
    }
}
