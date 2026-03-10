<?php

namespace App\Http\Controllers\Seller;

use App\Exports\SellerReportExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stand;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    private function standId(): ?int
    {
        return optional(Auth::user()->sellerProfile)->stand_id;
    }

    private function baseQuery(Request $request, int $standId)
    {
        return Order::with(['user', 'items'])
            ->where('stand_id', $standId)
            ->where('payment_status', 'paid')
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->filled('status'),    fn($q) => $q->where('order_status', $request->status));
    }

    private function summaryData(Request $request, int $standId): object
    {
        return Order::where('stand_id', $standId)
            ->where('payment_status', 'paid')
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->selectRaw('COUNT(*) as total_orders, SUM(total_price) as total_revenue,
                SUM(CASE WHEN order_status = "selesai" THEN 1 ELSE 0 END) as total_selesai,
                SUM(CASE WHEN order_status = "batal"   THEN 1 ELSE 0 END) as total_batal')
            ->first();
    }

    private function topItemsData(Request $request, int $standId): \Illuminate\Support\Collection
    {
        return OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.stand_id', $standId)
            ->where('orders.payment_status', 'paid')
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
        $standId = $this->standId();
        if (! $standId) {
            return view('seller.pending');
        }

        $orders   = $this->baseQuery($request, $standId)->latest()->paginate(25)->withQueryString();
        $summary  = $this->summaryData($request, $standId);
        $topItems = $this->topItemsData($request, $standId);

        return view('seller.reports.index', compact('orders', 'summary', 'topItems'));
    }

    public function exportExcel(Request $request)
    {
        $standId = $this->standId();
        if (! $standId) {
            return redirect()->route('seller.dashboard');
        }

        $orders = $this->baseQuery($request, $standId)->latest()->get();

        return Excel::download(
            new SellerReportExport($orders),
            'laporan-seller-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $standId = $this->standId();
        if (! $standId) {
            return redirect()->route('seller.dashboard');
        }

        $stand    = Stand::find($standId);
        $standName = $stand?->name ?? 'Stand';
        $orders   = $this->baseQuery($request, $standId)->latest()->get();
        $summary  = $this->summaryData($request, $standId);
        $topItems = $this->topItemsData($request, $standId);

        $pdf = Pdf::loadView('seller.reports.pdf', compact('orders', 'summary', 'topItems', 'standName'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-seller-' . now()->format('Y-m-d') . '.pdf');
    }
}
