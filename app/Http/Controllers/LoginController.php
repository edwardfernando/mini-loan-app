<?php
   
namespace App\Http\Controllers;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;


class LoginController extends Controller
{
    public function login(Request $request)
    {
        if(auth()->attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = auth()->user(); 
            $success['token'] =  $user->createToken('mini-aspire-api')->plainTextToken; 
            $success['name'] =  $user->name;

            return response()->json([
                'status' => 'success',
                'message' => 'user login successfully',
                'data' => $success
            ]);
        } 
        else{ 
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorised',
            ], 422);
        } 
    }

    public function logout(Request $request)
    {
        auth()->logout();
        return response()->json(['message' => 'Logged out successfully']);
    }
}