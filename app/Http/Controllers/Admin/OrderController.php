<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->input('q', ''));
        $status = $request->input('status', '');

        $orders = Order::with(['user', 'stand'])
            ->when($search !== '', fn($q) => $q->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', '%' . $search . '%'))
                  ->orWhere('id', is_numeric($search) ? $search : 0);
            }))
            ->when($status !== '', fn($q) => $q->where('order_status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.menuItem', 'stand']);

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'order_status' => ['required', 'in:menunggu,diproses,siap_diambil,selesai,batal'],
        ]);

        $previousStatus = $order->order_status;
        $newStatus = $validated['order_status'];

        $order->order_status = $newStatus;
        $order->status = $newStatus;

        if ($newStatus === 'siap_diambil') {
            $order->ready_at = \Illuminate\Support\Carbon::now();
        }

        if ($newStatus === 'selesai') {
            $order->picked_up_at = \Illuminate\Support\Carbon::now();
            $order->verified_by = $request->user()->id;
            $order->payment_status = 'paid';
        }

        if ($newStatus === 'batal' && $previousStatus !== 'batal') {
            $order->restockItems();
        }

        $order->save();
        $order->logStatusChange($previousStatus, $newStatus, $request->user()->id);

        return redirect()->route('admin.orders.show', $order)->with('status', 'Status pesanan berhasil diperbarui.');
    }
}
