<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Stand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Get cart from session
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return back()->withErrors([
                'cart' => 'Keranjang belanja kosong. Silakan tambahkan menu terlebih dahulu.',
            ]);
        }

        $validated = $request->validate([
            'order_date'     => ['required', 'date', 'after_or_equal:today'],
            'pickup_time'    => ['required', 'in:09:30,12:00'],
            'payment_method' => ['required', 'in:saldo,cash'],
            'stand_id'       => ['nullable', 'exists:stands,id'],
        ]);

        // Extract cart items and build line items
        $cartItemIds = array_keys($cart);
        $menuItems = MenuItem::query()
            ->whereIn('id', $cartItemIds)
            ->where('is_available', true)
            ->get()
            ->keyBy('id');

        if ($menuItems->count() !== count($cartItemIds)) {
            return back()->withErrors([
                'cart' => 'Beberapa menu tidak tersedia. Silakan periksa kembali keranjang belanja Anda.',
            ]);
        }

        $lineItems = [];
        $totalPrice = 0;
        $standId = $validated['stand_id'] ?? null;
        $standIdsInCart = [];

        foreach ($cart as $menuId => $cartItem) {
            $quantity = (int) $cartItem['quantity'];

            if ($quantity < 1) {
                continue;
            }

            $menu = $menuItems->get($menuId);
            $subtotal = $menu->price * $quantity;
            $totalPrice += $subtotal;

            $standIdsInCart[] = $menu->stand_id;

            $lineItems[] = [
                'menu_item_id' => $menu->id,
                'stand_id' => $menu->stand_id,
                'quantity' => $quantity,
                'price' => $menu->price,
                'subtotal' => $subtotal,
                'item_name_snapshot' => $menu->name,
                'price_snapshot' => $menu->price,
                'image_snapshot' => $menu->photo,
            ];
        }

        if (count($lineItems) === 0) {
            return back()->withErrors([
                'cart' => 'Minimal ada satu menu dengan jumlah yang valid dalam keranjang.',
            ]);
        }

        $standIdsInCart = array_filter(array_unique($standIdsInCart));

        if (count($standIdsInCart) === 0 && ! $standId) {
            return back()->withErrors([
                'stand_id' => 'Stand harus dipilih sebelum checkout.',
            ]);
        }

        if (count($standIdsInCart) > 1) {
            return back()->withErrors([
                'stand_id' => 'Satu pesanan hanya boleh dari satu stand. Mohon pisahkan pesanan per stand.',
            ]);
        }

        $derivedStandId = $standId ?? $standIdsInCart[0];

        $stand = Stand::find($derivedStandId);

        if (! $stand || ! $stand->is_active) {
            return back()->withErrors([
                'stand_id' => 'Stand tidak tersedia atau sedang nonaktif.',
            ]);
        }

        foreach ($lineItems as $index => $item) {
            $lineItems[$index]['stand_id'] = $stand->id;
        }

        $user = Auth::user();
        $paymentMethod = $validated['payment_method'];

        // Validate balance if paying with saldo
        if ($paymentMethod === 'saldo' && $user->balance < $totalPrice) {
            return back()->withErrors([
                'payment_method' => 'Saldo tidak mencukupi. Saldo Anda: Rp ' . number_format($user->balance, 0, ',', '.') . ', Total pesanan: Rp ' . number_format((float) $totalPrice, 0, ',', '.'),
            ])->withInput();
        }

        $orderNumber = null;
        $pickupCode = null;

        try {
            DB::transaction(function () use ($user, $validated, $lineItems, $totalPrice, $paymentMethod, $stand, &$orderNumber, &$pickupCode): void {
                $freshUser = $user->newQuery()->lockForUpdate()->findOrFail($user->id);

                // Lock menu items for stock check
                $menuIds = collect($lineItems)->pluck('menu_item_id');
                $menus = MenuItem::query()->whereIn('id', $menuIds)->lockForUpdate()->get()->keyBy('id');

                foreach ($lineItems as $item) {
                    $menu = $menus->get($item['menu_item_id']);

                    if (! $menu) {
                        throw new \RuntimeException('Menu tidak ditemukan saat checkout.');
                    }

                    if ($menu->stock < $item['quantity']) {
                        throw new \RuntimeException('Stok untuk ' . $menu->name . ' tidak mencukupi.');
                    }
                }

                // Double-check balance for saldo payment
                if ($paymentMethod === 'saldo' && $freshUser->balance < $totalPrice) {
                    throw new \RuntimeException('Saldo tidak mencukupi untuk menyelesaikan pesanan.');
                }

                $orderNumber = $this->generateOrderNumber();
                $pickupCode = $this->generatePickupCode();

                $paymentStatus = $paymentMethod === 'saldo' ? 'paid' : 'unpaid';

                $order = Order::create([
                    'user_id' => $freshUser->id,
                    'stand_id' => $stand->id,
                    'order_number' => $orderNumber,
                    'order_date' => $validated['order_date'],
                    'pickup_time' => $validated['pickup_time'],
                    'order_status' => 'menunggu',
                    'status' => 'menunggu',
                    'pickup_code' => $pickupCode,
                    'pickup_qr_payload' => null,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentMethod,
                    'total_price' => $totalPrice,
                ]);

                $order->items()->createMany($lineItems);

                // Decrement stock per menu
                foreach ($lineItems as $item) {
                    $menu = $menus->get($item['menu_item_id']);
                    $menu->decrement('stock', $item['quantity']);
                }

                // Deduct balance only if paying with saldo
                if ($paymentMethod === 'saldo') {
                    $freshUser->decrement('balance', $totalPrice);
                }
            });
        } catch (\RuntimeException $exception) {
            return back()->withErrors([
                'order' => $exception->getMessage(),
            ])->withInput();
        }

        // Clear cart session after successful order
        session()->forget('cart');

        $pickupInfo = isset($orderNumber, $pickupCode)
            ? " • Nomor: {$orderNumber} • Kode pickup: {$pickupCode}"
            : '';

        $message = $paymentMethod === 'saldo'
            ? 'Pesanan berhasil dibuat dan saldo terpotong.' . $pickupInfo
            : 'Pesanan berhasil dibuat. Tunjukkan kode pickup setelah pembayaran tunai.' . $pickupInfo;

        return redirect()
            ->route('orders.history')
            ->with('status', $message);
    }

    public function history(): View
    {
        $orders = Order::query()
            ->with(['items.menuItem', 'stand'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('orders.history', [
            'orders' => $orders,
        ]);
    }

    private function isBreakTime(string $pickupTime): bool
    {
        return $pickupTime >= '09:00' && $pickupTime <= '15:00';
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'ORD-' . now()->format('Ymd') . '-';

        $lastOrderNumber = Order::query()
            ->whereDate('created_at', now()->toDateString())
            ->whereNotNull('order_number')
            ->orderByDesc('order_number')
            ->value('order_number');

        $sequence = 1;

        if ($lastOrderNumber && preg_match('/-(\d{4})$/', $lastOrderNumber, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    private function generatePickupCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (Order::where('pickup_code', $code)->exists());

        return $code;
    }
}
