<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ScheduledRepayment;
use Illuminate\Support\Facades\Auth;

class OnlyAdmin
{
    public function handle(Request $request, Closure $next)
    {    
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if($request->user()->role !== 'admin') {
          return response()->json(['error' => 'Only user with admin role can perform this action'], 401);
        }

        return $next($request);
    }
}
