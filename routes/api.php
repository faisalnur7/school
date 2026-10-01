<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentApiController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::get('/', function (Request $request) {
    return "API Call";
});

Route::prefix('v1/student')->group(function () {
    Route::post('login', [StudentApiController::class, 'login'])->middleware('throttle:student-login');
    Route::post('logout', [StudentApiController::class, 'logout']);
    Route::middleware(['auth:sanctum', 'student.api'])->group(function () {
        Route::get('me', [StudentApiController::class, 'me']);
        Route::get('dashboard', [StudentApiController::class, 'dashboard']);
        Route::get('attendance', [StudentApiController::class, 'attendance']);
        Route::get('routine', [StudentApiController::class, 'routine']);
        Route::get('holidays', [StudentApiController::class, 'holidays']);
        Route::get('results/options', [StudentApiController::class, 'resultOptions']);
        Route::get('results', [StudentApiController::class, 'results']);
        Route::get('results/{result}', [StudentApiController::class, 'result'])->whereNumber('result');
        Route::get('fees', [StudentApiController::class, 'fees']);
        Route::get('payments', [StudentApiController::class, 'payments']);
        Route::get('payments/{payment}/receipt', [StudentApiController::class, 'receipt'])->whereNumber('payment');
        Route::get('notices', [StudentApiController::class, 'notices']);
        Route::get('homework', [StudentApiController::class, 'homework']);
        Route::get('notifications', [StudentApiController::class, 'notifications']);
        Route::post('devices', [StudentApiController::class, 'registerDevice']);
        Route::delete('devices/{device}', [StudentApiController::class, 'removeDevice']);
        Route::patch('notifications/{notification}/read', [StudentApiController::class, 'markNotificationRead']);
    });
});
