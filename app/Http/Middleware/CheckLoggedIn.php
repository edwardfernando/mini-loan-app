<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ScheduledRepayment;
use Illuminate\Support\Facades\Auth;

class CheckLoggedIn
{
    public function handle(Request $request, Closure $next)
    {    
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
