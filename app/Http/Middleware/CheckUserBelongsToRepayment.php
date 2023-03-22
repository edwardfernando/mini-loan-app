<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ScheduledRepayment;
use Illuminate\Support\Facades\Auth;

class CheckUserBelongsToRepayment
{
    public function handle(Request $request, Closure $next)
    {    
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $scheduledRepayment = ScheduledRepayment::findOrFail($request->scheduled_repayment_id);
        if($request->user()->id !== $scheduledRepayment->loan->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to perform this action'
            ], 401);
        }

        return $next($request);
    }
}
