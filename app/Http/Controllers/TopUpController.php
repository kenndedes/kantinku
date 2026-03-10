<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TopUpController extends Controller
{
    /** Payment method info shown to users. */
    public static array $methods = [
        'dana'     => ['label' => 'Dana',          'account' => '08XXXXXXXXXX',   'icon' => '🟦'],
        'gopay'    => ['label' => 'GoPay',          'account' => '08XXXXXXXXXX',   'icon' => '🟢'],
        'ovo'      => ['label' => 'OVO',            'account' => '08XXXXXXXXXX',   'icon' => '🟣'],
        'bca'      => ['label' => 'Transfer BCA',   'account' => '1234567890 a.n. Kantin', 'icon' => '🏦'],
        'bni'      => ['label' => 'Transfer BNI',   'account' => '9876543210 a.n. Kantin', 'icon' => '🏦'],
        'mandiri'  => ['label' => 'Transfer Mandiri','account' => '1357924680 a.n. Kantin', 'icon' => '🏦'],
    ];

    public function index()
    {
        $history = Transaction::where('user_id', auth()->id())
            ->whereNull('order_id')
            ->latest()
            ->take(5)
            ->get();

        return view('topup.index', [
            'methods' => self::$methods,
            'history' => $history,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount'         => 'required|numeric|min:10000|max:5000000',
            'payment_method' => 'required|in:' . implode(',', array_keys(self::$methods)),
            'proof_image'    => 'required|image|max:4096',
        ], [
            'amount.min'            => 'Minimal top up Rp 10.000.',
            'amount.max'            => 'Maksimal top up Rp 5.000.000.',
            'payment_method.in'     => 'Metode pembayaran tidak valid.',
            'proof_image.required'  => 'Bukti pembayaran wajib diunggah.',
            'proof_image.image'     => 'File harus berupa gambar (jpg, png, dll).',
            'proof_image.max'       => 'Ukuran gambar maksimal 4MB.',
        ]);

        $path = $request->file('proof_image')->store('topup-proofs', 'public');

        $transaction = Transaction::create([
            'user_id'        => auth()->id(),
            'ref_id'         => 'topup_' . auth()->id() . '_' . time(),
            'payment_method' => $validated['payment_method'],
            'channel_code'   => strtoupper($validated['payment_method']),
            'amount'         => (int) $validated['amount'],
            'proof_image'    => $path,
            'status'         => 'pending',
            'payment_status' => 'waiting_review',
        ]);

        return redirect()->route('topup.show', $transaction)
            ->with('success', 'Permintaan top up berhasil dikirim. Tunggu konfirmasi admin.');
    }

    public function show(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        return view('topup.show', [
            'transaction' => $transaction,
            'methods'     => self::$methods,
        ]);
    }

    public function history()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->whereNull('order_id')
            ->latest()
            ->paginate(15);

        return view('topup.history', compact('transactions'));
    }
}
