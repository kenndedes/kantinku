<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $standId = optional(Auth::user()->sellerProfile)->stand_id;

        if (! $standId) {
            return view('seller.pending');
        }

        $todayOrders = Order::query()
            ->where('stand_id', $standId)
            ->whereDate('created_at', today())
            ->count();

        $todayRevenue = Order::query()
            ->where('stand_id', $standId)
            ->whereDate('created_at', today())
            ->whereIn('payment_status', ['paid'])
            ->sum('total_price');

        $pending = Order::query()
            ->where('stand_id', $standId)
            ->whereIn('order_status', ['menunggu', 'diproses'])
            ->count();

        return view('seller.dashboard', [
            'todayOrders' => $todayOrders,
            'todayRevenue' => $todayRevenue,
            'pending' => $pending,
        ]);
    }
}
