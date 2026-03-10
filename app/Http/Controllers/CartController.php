<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Stand;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request, MenuItem $menuItem)
    {
        $quantity = (int) $request->input('quantity', 1);
        $quantity = max(1, min($quantity, 10)); // Ensure quantity is between 1 and 10
        
        $cart = session()->get('cart', []);
        
        $existingStandId = collect($cart)->pluck('stand_id')->filter()->first();

        if ($existingStandId && $existingStandId !== $menuItem->stand_id) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang hanya boleh dari satu stand. Selesaikan pesanan atau kosongkan keranjang terlebih dahulu.',
            ], 422);
        }

        if (isset($cart[$menuItem->id])) {
            $cart[$menuItem->id]['quantity'] += $quantity;
        } else {
            $cart[$menuItem->id] = [
                'id' => $menuItem->id,
                'name' => $menuItem->name,
                'price' => $menuItem->price,
                'type' => $menuItem->type,
                'photo' => $menuItem->photo,
                'stand_id' => $menuItem->stand_id,
                'quantity' => $quantity,
            ];
        }
        
        session()->put('cart', $cart);
        
        return response()->json([
            'success' => true,
            'message' => 'Menu ditambahkan ke keranjang',
            'cart_count' => count($cart),
            'cart_total' => $this->getCartTotal($cart),
        ]);
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('menu.index');
        }

        return view('checkout.index');
    }

    public function view()
    {
        $cart = session()->get('cart', []);
        $total = $this->getCartTotal($cart);

        $stands = Stand::query()->where('is_active', true)->orderBy('name')->get();
        $standId = collect($cart)->pluck('stand_id')->filter()->first();
        
        return view('cart.index', [
            'cart' => $cart,
            'total' => $total,
            'stands' => $stands,
            'selectedStandId' => $standId,
        ]);
    }

    public function updateQuantity(Request $request, $itemId)
    {
        $quantity = (int) $request->input('quantity');
        $cart = session()->get('cart', []);
        
        if (isset($cart[$itemId])) {
            if ($quantity <= 0) {
                unset($cart[$itemId]);
            } else {
                $cart[$itemId]['quantity'] = $quantity;
            }
            session()->put('cart', $cart);
        }
        
        $total = $this->getCartTotal($cart);
        
        return response()->json([
            'success' => true,
            'cart_total' => $total,
            'cart_count' => count($cart),
        ]);
    }

    public function removeItem(Request $request, $itemId)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            session()->put('cart', $cart);
        }
        
        return response()->json([
            'success' => true,
            'cart_count' => count($cart),
            'cart_total' => $this->getCartTotal($cart),
        ]);
    }

    public function clear()
    {
        session()->forget('cart');
        
        return redirect()->route('menu.index')->with('status', 'Keranjang berhasil dikosongkan');
    }

    private function getCartTotal($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
}
