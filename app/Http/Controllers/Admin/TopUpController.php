<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TopUpController as UserTopUpController;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopUpController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user')
            ->whereNull('order_id')
            ->when($request->filled('status'), fn($q) => $q->where('payment_status', $request->status))
            ->when($request->filled('method'), fn($q) => $q->where('payment_method', $request->method))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%'));
            })
            ->latest();

        $transactions = $query->paginate(20)->withQueryString();

        $pending = Transaction::whereNull('order_id')
            ->where('payment_status', 'waiting_review')
            ->count();

        return view('admin.topup.index', [
            'transactions' => $transactions,
            'pending'      => $pending,
            'methods'      => UserTopUpController::$methods,
        ]);
    }

    public function approve(Transaction $transaction)
    {
        if ($transaction->payment_status !== 'waiting_review') {
            return back()->with('error', 'Transaksi ini sudah diproses.');
        }

        DB::transaction(function () use ($transaction) {
            $transaction->user->increment('balance', $transaction->amount);

            $transaction->update([
                'status'         => 'success',
                'payment_status' => 'approved',
                'reviewed_by'    => auth()->id(),
                'reviewed_at'    => now(),
            ]);
        });

        return back()->with('success', 'Top up Rp ' . number_format($transaction->amount, 0, ',', '.') . ' berhasil disetujui. Saldo user telah ditambahkan.');
    }

    public function reject(Request $request, Transaction $transaction)
    {
        if ($transaction->payment_status !== 'waiting_review') {
            return back()->with('error', 'Transaksi ini sudah diproses.');
        }

        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        $transaction->update([
            'status'         => 'failed',
            'payment_status' => 'rejected',
            'admin_note'     => $request->admin_note ?? 'Ditolak oleh admin.',
            'reviewed_by'    => auth()->id(),
            'reviewed_at'    => now(),
        ]);

        return back()->with('info', 'Top up telah ditolak.');
    }
}
