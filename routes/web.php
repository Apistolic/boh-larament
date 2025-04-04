<?php

use App\Services\EmailTrackingService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Email tracking routes
Route::get('/email/track/open/{pixelId}', function (string $pixelId, EmailTrackingService $trackingService) {
    $trackingService->trackOpen($pixelId, request());
    
    // Return 1x1 transparent GIF
    return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'))
        ->header('Content-Type', 'image/gif');
})->name('email.track.open');

Route::get('/email/track/click/{emailSendId}/{url}', function (string $emailSendId, string $url, EmailTrackingService $trackingService) {
    $trackingService->trackClick($emailSendId, urldecode($url), request());
    return redirect(urldecode($url));
})->name('email.track.click');
