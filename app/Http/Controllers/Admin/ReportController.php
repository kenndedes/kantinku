<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AdminReportExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stand;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    private function baseQuery(Request $request)
    {
        return Order::with(['stand', 'user', 'items'])
            ->where('payment_status', 'paid')
            ->when($request->filled('stand_id'), fn($q) => $q->where('stand_id', $request->stand_id))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),   fn($q) => $q->whereDate('created_at', '<=', $request->date_to));
    }

    private function summaryData(Request $request): object
    {
        return Order::where('payment_status', 'paid')
            ->when($request->filled('stand_id'), fn($q) => $q->where('stand_id', $request->stand_id))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->selectRaw('COUNT(*) as total_orders, SUM(total_price) as total_revenue,
                SUM(CASE WHEN order_status = "selesai" THEN 1 ELSE 0 END) as total_selesai,
                SUM(CASE WHEN order_status = "batal"   THEN 1 ELSE 0 END) as total_batal')
            ->first();
    }

    private function perStandData(Request $request): \Illuminate\Support\Collection
    {
        return Order::with('stand')
            ->where('payment_status', 'paid')
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->selectRaw('stand_id, COUNT(*) as total_orders, SUM(total_price) as total_revenue')
            ->groupBy('stand_id')
            ->get()
            ->map(fn($r) => [
                'stand'         => $r->stand?->name ?? 'Tanpa Stand',
                'total_orders'  => $r->total_orders,
                'total_revenue' => $r->total_revenue,
            ]);
    }

    private function topItemsData(Request $request): \Illuminate\Support\Collection
    {
        return OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->when($request->filled('stand_id'), fn($q) => $q->where('orders.stand_id', $request->stand_id))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('orders.created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),   fn($q) => $q->whereDate('orders.created_at', '<=', $request->date_to))
            ->selectRaw('item_name_snapshot as name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('item_name_snapshot')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();
    }

    public function index(Request $request)
    {
        $stands   = Stand::orderBy('name')->get();
        $orders   = $this->baseQuery($request)->latest()->paginate(25)->withQueryString();
        $summary  = $this->summaryData($request);
        $perStand = $this->perStandData($request);
        $topItems = $this->topItemsData($request);

        return view('admin.reports.index', compact('stands', 'orders', 'summary', 'perStand', 'topItems'));
    }

    public function exportExcel(Request $request)
    {
        $orders = $this->baseQuery($request)->latest()->get();

        return Excel::download(
            new AdminReportExport($orders),
            'laporan-admin-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $stands   = Stand::orderBy('name')->get();
        $orders   = $this->baseQuery($request)->latest()->get();
        $summary  = $this->summaryData($request);
        $perStand = $this->perStandData($request);
        $topItems = $this->topItemsData($request);

        $pdf = Pdf::loadView('admin.reports.pdf', compact('stands', 'orders', 'summary', 'perStand', 'topItems'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-admin-' . now()->format('Y-m-d') . '.pdf');
    }
}
