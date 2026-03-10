<?php

namespace App\Http\Middleware;

use App\Models\SellerProfile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SellerOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'seller') {
            abort(403, 'Akses ditolak. Hanya seller yang dapat mengakses halaman ini.');
        }

        $profile = $user->sellerProfile;

        if (! $profile || $profile->status === 'pending') {
            return redirect()->route('seller.pending');
        }

        if ($profile->status === 'rejected') {
            return redirect()->route('seller.rejected');
        }

        return $next($request);
    }
}
