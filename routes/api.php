<?php

use App\Http\Controllers\Frontend\QuestWebhookController;
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

Route::prefix('quest')
    ->middleware(['throttle:60,1', 'quest.webhook'])
    ->group(function () {
        Route::post('/order-status', [QuestWebhookController::class, 'receive'])
            ->name('quest.order-status.webhook');
    });

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
