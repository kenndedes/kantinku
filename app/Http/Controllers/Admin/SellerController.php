<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use App\Models\Stand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerController extends Controller
{
    public function index(): View
    {
        $profiles = SellerProfile::query()
            ->with('user')
            ->orderByRaw("FIELD(status, 'pending','approved','rejected')")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.sellers.index', compact('profiles'));
    }

    public function update(Request $request, SellerProfile $seller): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
        ]);

        if ($validated['action'] === 'approve') {
            // Create a stand for this seller if they don't have one yet
            if (! $seller->stand_id) {
                $name = $seller->stand_name ?: ($seller->user->name . "'s Stand");
                $code = strtoupper(Str::slug($name, ''));
                $code = substr($code, 0, 10);

                // Ensure code is unique
                $base = $code;
                $i = 1;
                while (Stand::where('code', $code)->exists()) {
                    $code = $base . $i++;
                }

                $stand = Stand::create([
                    'code'      => $code,
                    'name'      => $name,
                    'location'  => null,
                    'is_active' => true,
                ]);

                $seller->update([
                    'status'      => 'approved',
                    'approved_at' => now(),
                    'stand_id'    => $stand->id,
                ]);
            } else {
                $seller->update([
                    'status'      => 'approved',
                    'approved_at' => now(),
                ]);
            }
        }

        if ($validated['action'] === 'reject') {
            $seller->update([
                'status'      => 'rejected',
                'rejected_at' => now(),
            ]);
        }

        return back()->with('status', 'Seller berhasil diperbarui.');
    }
}
