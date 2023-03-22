<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\RepaymentController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/loans', [LoanController::class, 'index']);
Route::get('/loans/{id}', [LoanController::class, 'show']);
Route::post('/loans', [LoanController::class, 'store']);
Route::put('/loans/{id}', [LoanController::class, 'update']);
Route::delete('/loans/{id}', [LoanController::class, 'destroy']);

Route::post('/repayments', [RepaymentController::class, 'store'])->middleware('check_user_belongs_to_repayment');



Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);