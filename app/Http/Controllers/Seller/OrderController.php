<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    private function standId(): ?int
    {
        return optional(Auth::user()->sellerProfile)->stand_id;
    }

    public function index(): View|RedirectResponse
    {
        $standId = $this->standId();

        if (! $standId) {
            return redirect()->route('seller.pending');
        }

        $stand = \App\Models\Stand::find($standId);

        $search = trim(request()->input('q', ''));
        $status = request()->input('status', '');

        $orders = Order::query()
            ->with(['user', 'items.menuItem'])
            ->where('stand_id', $standId)
            ->when($search !== '', fn($q) => $q->whereHas('user', fn($u) => $u->where('name', 'like', '%' . $search . '%')))
            ->when($status !== '', fn($q) => $q->where('order_status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('seller.orders.index', compact('orders', 'stand', 'search', 'status'));
    }

    public function show(Order $order): View
    {
        $this->authorizeOrder($order);
        $order->load(['user', 'items.menuItem', 'stand', 'statusLogs.changedBy']);

        return view('seller.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        $validated = $request->validate([
            'order_status' => ['required', 'in:menunggu,diproses,siap_diambil,selesai,batal'],
        ]);

        $previousStatus = $order->order_status;
        $newStatus = $validated['order_status'];

        $order->order_status = $newStatus;
        $order->status = $newStatus;

        if ($newStatus === 'siap_diambil') {
            $order->ready_at = new \Illuminate\Support\Carbon();
        }

        if ($newStatus === 'selesai') {
            $order->picked_up_at = new \Illuminate\Support\Carbon();
            $order->verified_by = $request->user()->id;
        }

        if ($newStatus === 'batal' && $previousStatus !== 'batal') {
            $order->restockItems();
        }

        $order->save();
        $order->logStatusChange($previousStatus, $newStatus, $request->user()->id);

        return back()->with('status', 'Status pesanan diperbarui.');
    }

    private function authorizeOrder(Order $order): void
    {
        if ($order->stand_id !== $this->standId()) {
            abort(403);
        }
    }
}
