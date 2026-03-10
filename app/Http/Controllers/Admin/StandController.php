<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StandController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $stands = Stand::query()
            ->with(['sellerProfile.user'])
            ->withCount(['menuItems', 'orders'])
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get();

        return view('admin.stands.index', compact('stands', 'search'));
    }

    public function create(): View
    {
        return view('admin.stands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'location'  => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $lastCode = Stand::orderByDesc('id')->value('code');
        $nextNum  = $lastCode ? ((int) preg_replace('/\D/', '', $lastCode) + 1) : 1;
        $code     = 'STD-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        Stand::create([
            'code'      => $code,
            'name'      => $validated['name'],
            'location'  => $validated['location'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.stands.index')->with('status', 'Stand berhasil dibuat.');
    }

    public function edit(Stand $stand): View
    {
        $sellers = User::query()
            ->where('role', 'seller')
            ->with('sellerProfile')
            ->orderBy('name')
            ->get();

        return view('admin.stands.edit', compact('stand', 'sellers'));
    }

    public function update(Request $request, Stand $stand): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:stands,code,' . $stand->id],
            'name' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'seller_id' => ['nullable', 'exists:users,id'],
        ]);

        $stand->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'location' => $validated['location'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (array_key_exists('seller_id', $validated)) {
            $sellerId = $validated['seller_id'];

            // Clear previous seller assignment
            if ($stand->sellerProfile) {
                $stand->sellerProfile->update(['stand_id' => null]);
            }

            if ($sellerId) {
                $seller = User::find($sellerId);
                if ($seller && $seller->role === 'seller' && $seller->sellerProfile) {
                    $seller->sellerProfile->update(['stand_id' => $stand->id]);
                }
            }
        }

        return redirect()->route('admin.stands.index')->with('status', 'Stand berhasil diperbarui.');
    }

    public function destroy(Stand $stand): RedirectResponse
    {
        if ($stand->sellerProfile) {
            $stand->sellerProfile->update(['stand_id' => null]);
        }

        $stand->menuItems()->update(['is_available' => false]);
        $stand->delete();

        return redirect()->route('admin.stands.index')->with('status', 'Stand dihapus dan menu dinonaktifkan.');
    }
}
