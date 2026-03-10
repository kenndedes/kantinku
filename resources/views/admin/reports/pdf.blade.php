<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        * { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 0; box-sizing: border-box; }
        body { margin: 20px; color: #333; }
        h1 { font-size: 18px; color: #7c3aed; margin-bottom: 4px; }
        .subtitle { color: #666; font-size: 10px; margin-bottom: 16px; }
        h2 { font-size: 13px; color: #7c3aed; border-bottom: 2px solid #ede9fe; padding-bottom: 4px; margin: 14px 0 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th { background: #ede9fe; color: #5b21b6; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: .05em; }
        td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; }
        tr:nth-child(even) td { background: #fafafa; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-green  { background: #d1fae5; color: #065f46; }
        .badge-blue   { background: #dbeafe; color: #1e40af; }
        .badge-purple { background: #ede9fe; color: #5b21b6; }
        .badge-red    { background: #fee2e2; color: #991b1b; }
        .badge-yellow { background: #fef3c7; color: #92400e; }
        .footer { margin-top: 20px; text-align: center; color: #9ca3af; font-size: 9px; border-top: 1px solid #e5e7eb; padding-top: 8px; }
        .summary-row td { background: #f5f3ff !important; font-weight: bold; border: 1px solid #ddd6fe; padding: 10px 12px; }
        .summary-label { font-size: 9px; color: #6b7280; display: block; margin-bottom: 2px; text-transform: uppercase; }
        .summary-value { font-size: 16px; color: #5b21b6; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .mono { font-family: monospace; font-size: 9px; }
    </style>
</head>
<body>
    <h1>E-Canteen &mdash; Laporan Penjualan</h1>
    <p class="subtitle">
        Dicetak: {{ now()->format('d M Y H:i') }}
        @if(request('date_from') || request('date_to'))
            &nbsp;|&nbsp; Periode: {{ request('date_from', '-') }} s/d {{ request('date_to', '-') }}
        @endif
        @if(request('stand_id'))
            &nbsp;|&nbsp; Stand: {{ $stands->firstWhere('id', request('stand_id'))?->name ?? '-' }}
        @endif
    </p>

    {{-- Summary --}}
    <table>
        <tr class="summary-row">
            <td style="width:25%">
                <span class="summary-label">Total Pesanan</span>
                <span class="summary-value">{{ number_format($summary->total_orders ?? 0) }}</span>
            </td>
            <td style="width:25%">
                <span class="summary-label">Total Pendapatan</span>
                <span class="summary-value" style="font-size:13px; color:#065f46;">Rp {{ number_format($summary->total_revenue ?? 0, 0, ',', '.') }}</span>
            </td>
            <td style="width:25%">
                <span class="summary-label">Pesanan Selesai</span>
                <span class="summary-value" style="color:#1e40af;">{{ number_format($summary->total_selesai ?? 0) }}</span>
            </td>
            <td style="width:25%">
                <span class="summary-label">Pesanan Batal</span>
                <span class="summary-value" style="color:#991b1b;">{{ number_format($summary->total_batal ?? 0) }}</span>
            </td>
        </tr>
    </table>

    {{-- Per-stand & Top items side by side --}}
    <table style="border:none; margin-bottom:0;">
        <tr>
            <td style="width:50%; vertical-align:top; padding:0 8px 0 0; border:none;">
                @if($perStand->isNotEmpty())
                <h2>Ringkasan per Stand</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Stand</th>
                            <th class="text-right">Pesanan</th>
                            <th class="text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($perStand as $row)
                        <tr>
                            <td>{{ $row['stand'] }}</td>
                            <td class="text-right">{{ number_format($row['total_orders']) }}</td>
                            <td class="text-right">Rp {{ number_format($row['total_revenue'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </td>
            <td style="width:50%; vertical-align:top; padding:0 0 0 8px; border:none;">
                @if($topItems->isNotEmpty())
                <h2>10 Menu Terlaris</h2>
                <table>
                    <thead>
                        <tr>
                            <th style="width:24px">#</th>
                            <th>Menu</th>
                            <th class="text-right">Terjual</th>
                            <th class="text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topItems as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td class="text-right">{{ number_format($item->total_qty) }}</td>
                            <td class="text-right">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </td>
        </tr>
    </table>

    {{-- Detail Pesanan --}}
    <h2>Detail Pesanan ({{ count($orders) }} transaksi)</h2>
    <table>
        <thead>
            <tr>
                <th>No. Pesanan</th>
                <th>Pembeli</th>
                <th>Stand</th>
                <th>Tanggal</th>
                <th>Pickup</th>
                <th class="text-right">Total</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            @php
                $badgeClass = match($order->order_status) {
                    'selesai'      => 'badge-green',
                    'diproses'     => 'badge-blue',
                    'siap_diambil' => 'badge-purple',
                    'batal'        => 'badge-red',
                    default        => 'badge-yellow',
                };
                $statusLabel = match($order->order_status) {
                    'menunggu'     => 'Menunggu',
                    'diproses'     => 'Diproses',
                    'siap_diambil' => 'Siap Diambil',
                    'selesai'      => 'Selesai',
                    'batal'        => 'Batal',
                    default        => $order->order_status,
                };
            @endphp
            <tr>
                <td class="mono">{{ $order->order_number }}</td>
                <td>{{ $order->user?->name ?? '-' }}</td>
                <td>{{ $order->stand?->name ?? '-' }}</td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                <td>{{ $order->pickup_time === '09:30' ? 'Istirahat 1' : 'Istirahat 2' }}</td>
                <td class="text-right" style="font-weight:bold;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                <td class="text-center"><span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">E-Canteen &mdash; Laporan dicetak otomatis oleh sistem pada {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
