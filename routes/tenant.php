<?php

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
/*\Illuminate\Support\Facades\Log::info('tenant.php routes');
if(config('app.app_install') == true) {
    \Illuminate\Support\Facades\Log::info('installed');
Route::get('/', 'WebsiteController@getLandingPage')->name('home');

Auth::routes(['verify' => true]);

} else {
    \Illuminate\Support\Facades\Log::info('requesting install');

Route::namespace('Install')->group(function () {

    Route::get('/', function() {

        return redirect('/installation');

    });

    Route::get('install/database', 'InstallController@database');
Route::post('install/process_install', 'InstallController@process_install');
Route::get('install/create_user', 'InstallController@create_user');
Route::post('install/store_user', 'InstallController@store_user');
Route::get('install/system_settings', 'InstallController@system_settings');
Route::post('install/finish', 'InstallController@final_touch');

});

}*/

use App\Http\Controllers\Install\InstallController;
use App\Http\Controllers\WebsiteController;

////Log::info('tenant.php routes');
//if(config('app.app_install') == true) {
////    Log::info('installed');
//    Route::get('/', [WebsiteController::class, 'getLandingPage'])->name('home');
//
//    Auth::routes(['verify' => true]);
//
//} else {
////    Log::info('requesting install');
//
//    Route::group(function () {
//
//        Route::get('/', function() {
//            return redirect('/installation');
//        });
//
//        Route::get('install/database', [InstallController::class, 'database']);
//        Route::post('install/process_install', [InstallController::class, 'process_install']);
//        Route::get('install/create_user', [InstallController::class, 'create_user']);
//        Route::post('install/store_user', [InstallController::class, 'store_user']);
//        Route::get('install/system_settings', [InstallController::class, 'system_settings']);
//        Route::post('install/finish', [InstallController::class, 'final_touch']);
//
//    });
//
//}

Log::info('tenant.php routes');
if(config('app.app_install') == true) {
    Log::info('installed');
    Route::get('/', [WebsiteController::class, 'getLandingPage'])->name('home');

    Auth::routes(['verify' => true]);

} else {
    Log::info('requesting install');

    Route::group([], function () {

        Route::get('/', function() {
            return redirect('/installation');
        });

        Route::get('install/database', [InstallController::class, 'database']);
        Route::post('install/process_install', [InstallController::class, 'process_install']);
        Route::get('install/create_user', [InstallController::class, 'create_user']);
        Route::post('install/store_user', [InstallController::class, 'store_user']);
        Route::get('install/system_settings', [InstallController::class, 'system_settings']);
        Route::post('install/finish', [InstallController::class, 'final_touch']);

    });

}
