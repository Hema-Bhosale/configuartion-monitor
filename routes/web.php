<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Events\ConfigurationChanged;

Route::get('/test-event', function () {

    ConfigurationChanged::dispatch(
        'storage/app/monitored/app-config.yml',
        [
            'application.debug' => [
                'old' => true,
                'new' => false,
            ],
        ]
    );

    return response()->json([
        'message' => 'Event dispatched successfully',
    ]);
});
Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-webhook-config', function () {
    return config('services.syncworks.webhook_url');
});
