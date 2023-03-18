<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::all();
        return response()->json([
            'status' => 'success',
            'data' => $loans
        ]);
    }

    public function show($id)
    {
        $loan = Loan::find($id);
        if(!$loan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loan not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $loan
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'term' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        $loan = new Loan();
        $loan->amount = $request->input('amount');
        $loan->term = $request->input('term');
        $loan->state = 'PENDING';
        $loan->save();
        
        return response()->json([
            'status' => 'success',
            'data' => $loan
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $loan = Loan::find($id);
        if(!$loan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loan not found'
            ], 404);
        }

        $loan->fill($request->only([
            'amount',
            'term',
            'state'
        ]));

        $loan->save();
        return response()->json([
            'status' => 'success',
            'data' => $loan
        ]);
    }

    public function destroy($id)
    {
        $loan = Loan::find($id);
        if (!$loan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loan not found'
            ], 404);
        }
        $loan->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Loan deleted successfully'
        ]);
    }
}
