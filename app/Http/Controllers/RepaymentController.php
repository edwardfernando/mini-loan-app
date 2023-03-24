<?php

namespace App\Http\Controllers;

use App\Models\Repayment;
use App\Models\ScheduledRepayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RepaymentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'scheduled_repayment_id' => 'required|exists:scheduled_repayments,id',
            'amount' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        $scheduledRepayment = ScheduledRepayment::find($request->input('scheduled_repayment_id'));

        if($scheduledRepayment->loan->state == 'PENDING') {
            return response()->json([
                'status' => 'error',
                'message' => 'Can not make repayment. Your loan has not been approved.',
            ], 422);
        }
        
        if ($scheduledRepayment->state == 'PAID') {
            return response()->json([
                'status' => 'error',
                'message' => 'The scheduled repayment has been fully paid.',
            ], 422);
        }
        
        if ($scheduledRepayment->amount < $request->input('amount')) {
            return response()->json([
                'status' => 'error',
                'message' => 'The repayment amount cannot be greater than the scheduled repayment amount.',
            ], 422);
        }

        $repayment = new Repayment();
        $repayment->scheduled_repayment_id = $request->input('scheduled_repayment_id');
        $repayment->amount = $request->input('amount');
        $repayment->save();
        
        // Update the scheduled repayment state based on the total amount of repayments made
        $totalRepaymentsAmount = $scheduledRepayment->repayments()->sum('amount');
        if ($totalRepaymentsAmount === $scheduledRepayment->amount) {
            $scheduledRepayment->state = 'PAID';
        } else {
            $scheduledRepayment->state = 'PARTIAL';
        }
        $scheduledRepayment->save();

        return response()->json([
            'status' => 'success',
            'data' => $repayment,
        ], 201);
    }
}
