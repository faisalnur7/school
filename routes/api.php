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
        Route::get('mobile-configuration', [StudentApiController::class, 'mobileConfiguration']);
        Route::get('dashboard', [StudentApiController::class, 'dashboard'])->middleware('mobile.feature:dashboard');
        Route::get('attendance', [StudentApiController::class, 'attendance'])->middleware('mobile.feature:attendance');
        Route::get('routine', [StudentApiController::class, 'routine'])->middleware('mobile.feature:class_routine');
        Route::get('routine/pdf', [StudentApiController::class, 'routinePdf'])->middleware('mobile.feature:class_routine');
        Route::get('exam-routine', [StudentApiController::class, 'examRoutine'])->middleware('mobile.feature:exam_routine');
        Route::get('holidays', [StudentApiController::class, 'holidays'])->middleware('mobile.feature:holidays');
        Route::get('results/options', [StudentApiController::class, 'resultOptions'])->middleware('mobile.feature:results');
        Route::get('results', [StudentApiController::class, 'results'])->middleware('mobile.feature:results');
        Route::get('results/{result}', [StudentApiController::class, 'result'])->whereNumber('result')->middleware('mobile.feature:results');
        Route::get('fees', [StudentApiController::class, 'fees'])->middleware('mobile.feature:fees');
        Route::get('payments', [StudentApiController::class, 'payments'])->middleware('mobile.feature:fees');
        Route::get('payments/{payment}/receipt', [StudentApiController::class, 'receipt'])->whereNumber('payment')->middleware('mobile.feature:fees');
        Route::get('payment-requests', [StudentApiController::class, 'paymentRequests'])->middleware('mobile.feature:fees');
        Route::post('payment-requests', [StudentApiController::class, 'storePaymentRequest'])->middleware('mobile.feature:fees');
        Route::get('payment-requests/{paymentRequest}', [StudentApiController::class, 'paymentRequest'])->whereNumber('paymentRequest')->middleware('mobile.feature:fees');
        Route::delete('payment-requests/{paymentRequest}', [StudentApiController::class, 'cancelPaymentRequest'])->whereNumber('paymentRequest')->middleware('mobile.feature:fees');
        Route::get('notices', [StudentApiController::class, 'notices'])->middleware('mobile.feature:notices');
        Route::get('homework', [StudentApiController::class, 'homework'])->middleware('mobile.feature:homework');
        Route::get('notifications', [StudentApiController::class, 'notifications'])->middleware('mobile.feature:notifications');
        Route::post('devices', [StudentApiController::class, 'registerDevice']);
        Route::delete('devices/{device}', [StudentApiController::class, 'removeDevice']);
        Route::patch('notifications/{notification}/read', [StudentApiController::class, 'markNotificationRead']);
    });
});
