<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Store;
use Symfony\Component\HttpFoundation\Response;

class RefreshStoreSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('store')->check()) {
            $freshStore = Store::find(Auth::guard('store')->id());
            if ($freshStore) {
                Auth::guard('store')->setUser($freshStore);
            }
        }
        return $next($request);
    }
}