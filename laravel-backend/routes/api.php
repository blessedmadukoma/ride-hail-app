<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [LoginController::class, 'login']);
Route::post('/login/verify', [LoginController::class, 'verify']);

Route::group(['middleware' => ['auth:sanctum']], function () {
 Route::get('/user', function (Request $request) {
  return $request->user();
 });

 Route::get('/driver', [DriverController::class, "getDriver"]);
 Route::post('/driver', [DriverController::class, "updateDriver"]);

 Route::post('/trip', [DriverController::class, "store"]);
 Route::post('/trip/{trip}', [DriverController::class, "getTrip"]);
});
