<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\CreditController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Client routes
    Route::prefix('clients')->group(function () {
        Route::post('/', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/{id}', [ClientController::class, 'show'])->name('api.clients.show');
        Route::get('/{clientId}/applications', [CreditController::class, 'getClientApplications'])
            ->name('api.clients.applications');
    });

    // Credit routes
    Route::prefix('credits')->group(function () {
        Route::get('/', [CreditController::class, 'index'])->name('api.credits.index');
        Route::post('/check-eligibility', [CreditController::class, 'checkEligibility'])
            ->name('credits.check-eligibility');
        Route::post('/apply', [CreditController::class, 'processApplication'])
            ->name('credits.apply');
        Route::get('/applications/{applicationId}', [CreditController::class, 'showApplication'])
            ->name('api.credits.applications.show');
    });
});
