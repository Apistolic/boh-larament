<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\N8nWebhookController;
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

/*
|--------------------------------------------------------------------------
| n8n Webhook Routes
|--------------------------------------------------------------------------
*/
Route::post('/webhooks/n8n', [N8nWebhookController::class, 'handle'])
    ->name('webhooks.n8n');

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Add your other API routes here
});
