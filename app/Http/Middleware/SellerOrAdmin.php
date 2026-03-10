<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SellerOrAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if ($user->role === 'admin') {
            return $next($request);
        }

        if ($user->role === 'seller') {
            $profile = $user->sellerProfile;

            if ($profile && $profile->status === 'approved') {
                return $next($request);
            }

            return redirect()->route('seller.pending');
        }

        abort(403);
    }
}
